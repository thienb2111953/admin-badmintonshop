from fastapi import FastAPI
from pydantic import BaseModel
from groq import Groq
import json
import unicodedata
import re
from pathlib import Path
from typing import Dict, Any, Optional, List

import numpy as np
from sentence_transformers import SentenceTransformer
from fastapi.middleware.cors import CORSMiddleware

from danh_muc import CATEGORY_MAPPING, THUONG_HIEU_MAPPING
from conn import API_KEY_GROQ

# =====================================
# CONFIG
# =====================================

BASE_DIR = Path(__file__).resolve().parent
DIR_DANH_MUC = BASE_DIR / "danh-muc"
VECTOR_DIR = BASE_DIR / "vector-cache"
VECTOR_DIR.mkdir(exist_ok=True)

PAGE_SIZE = 5
EMBEDDING_MODEL_NAME = "intfloat/multilingual-e5-base"

app = FastAPI()

app.add_middleware(
    CORSMiddleware,
    allow_origins=["http://localhost:3000"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# =====================================
# GROQ CLIENT (LAZY)
# =====================================

_client: Optional[Groq] = None


def get_groq_client() -> Groq:
    global _client
    if _client is None:
        _client = Groq(api_key=API_KEY_GROQ)
    return _client


# =====================================
# EMBEDDING MODEL (LAZY)
# =====================================

_embedding_model: Optional[SentenceTransformer] = None


def get_embedding_model() -> SentenceTransformer:
    global _embedding_model
    if _embedding_model is None:
        _embedding_model = SentenceTransformer(EMBEDDING_MODEL_NAME)
    return _embedding_model


# =====================================
# GLOBAL STATE
# =====================================

SESSION: Dict[str, Dict[str, Any]] = {}
PRODUCT_CACHE: Dict[str, List[Dict[str, Any]]] = {}
VECTOR_CACHE: Dict[str, np.ndarray] = {}

CATEGORY_LABEL = {
    "vot-cau-long": "vợt cầu lông",
    "giay-cau-long": "giày cầu lông",
    "ao-cau-long": "áo cầu lông",
    "quan-cau-long": "quần cầu lông",
    "balo-cau-long": "balo cầu lông",
    "tui-vot-cau-long": "túi vợt cầu lông",
    "vay-cau-long": "váy cầu lông",
}

# =====================================
# INPUT
# =====================================


class ChatRequest(BaseModel):
    session_id: str
    message: str


# =====================================
# UTIL
# =====================================

def normalize(text: str) -> str:
    text = text.lower()
    text = unicodedata.normalize("NFD", text)
    return "".join(c for c in text if unicodedata.category(c) != "Mn")


def is_load_more(msg: str) -> bool:
    msg = normalize(msg)
    return any(k in msg for k in ["xem them", "them nua", "tiep di", "tiep tuc"])


def is_relax_price(msg: str) -> bool:
    msg = normalize(msg)
    return any(k in msg for k in [
        "khong quan tam gia",
        "bo gia",
        "gia nao cung duoc"
    ])


# =====================================
# CATEGORY FIXED
# =====================================

def detect_category(msg: str) -> Optional[str]:
    """
    FIX: Match theo TỪ NGUYÊN VẸN bằng regex \bword\b
    Không còn match substring (vd: 'cao' != 'ao')
    """
    msg_norm = normalize(msg)

    for raw_key, full_label in CATEGORY_MAPPING.items():
        key_norm = normalize(raw_key)
        pattern = r"\b{}\b".format(re.escape(key_norm))
        if re.search(pattern, msg_norm):
            slug = normalize(full_label).replace(" ", "-")
            return slug

    return None


# =====================================
# BRAND + PRICE (FIXED)
# =====================================

def parse_price(val: str, unit: Optional[str]) -> int:
    v = float(val.replace(",", "."))
    if unit in ("tr", "trieu"):
        return int(v * 1_000_000)
    if unit == "k":
        return int(v * 1_000)
    return int(v)


def extract_filters(msg: str):
    raw = msg
    msg = normalize(msg)

    # BRAND
    brand = None
    for k, v in THUONG_HIEU_MAPPING.items():
        if k in msg:
            brand = v
            break

    # CAO HƠN / TRÊN / LỚN HƠN / >
    m = re.search(
        r"(cao hon|tren|lon hon|>)\s*(\d+(?:[\.,]\d+)?)\s*(trieu|tr|k)?",
        msg
    )
    if m:
        unit = m.group(3) or "vnd"
        return brand, parse_price(m.group(2), unit), None

    # DƯỚI / NHỎ HƠN / <
    m = re.search(
        r"(duoi|nho hon|<)\s*(\d+(?:[\.,]\d+)?)\s*(trieu|tr|k)?",
        msg
    )
    if m:
        unit = m.group(3) or "vnd"
        return brand, None, parse_price(m.group(2), unit)

    # RANGE: 1tr - 2tr
    m = re.search(
        r"(\d+(?:[\.,]\d+)?)\s*(trieu|tr|k)\s*(den|-)\s*(\d+(?:[\.,]\d+)?)\s*(trieu|tr|k)",
        msg
    )
    if m:
        p1 = parse_price(m.group(1), m.group(2))
        p2 = parse_price(m.group(4), m.group(5))
        return brand, min(p1, p2), max(p1, p2)

    # GIÁ ĐƠN (1tr → ±300k)
    m = re.search(r"(\d+(?:[\.,]\d+)?)\s*(trieu|tr|k)", msg)
    if m:
        center = parse_price(m.group(1), m.group(2))
        return brand, center - 300_000, center + 300_000

    # Số thuần (>=6 chữ số → coi là VND)
    digits = re.findall(r"\d{6,9}", raw.replace(" ", ""))
    if digits:
        center = int(digits[0])
        return brand, center - 300_000, center + 300_000

    return brand, None, None


# =====================================
# DATA + VECTOR
# =====================================

def load_products(slug: str) -> List[Dict[str, Any]]:
    if slug in PRODUCT_CACHE:
        return PRODUCT_CACHE[slug]

    path = DIR_DANH_MUC / f"{slug}.json"
    if not path.exists():
        PRODUCT_CACHE[slug] = []
        return []

    with open(path, "r", encoding="utf-8") as f:
        PRODUCT_CACHE[slug] = json.load(f)

    return PRODUCT_CACHE[slug]


def build_product_text(p: Dict[str, Any]) -> str:
    parts = [
        str(p.get("ten_san_pham", "")),
        str(p.get("ten_thuong_hieu", "")),
        str(p.get("ten_danh_muc", "")),
        str(p.get("mo_ta_ngan", "")),
        str(p.get("mo_ta", "")),
    ]
    return " ".join([x for x in parts if x])


def load_vectors(slug: str) -> np.ndarray:
    if slug in VECTOR_CACHE:
        return VECTOR_CACHE[slug]

    vec_path = VECTOR_DIR / f"{slug}.npy"
    products = load_products(slug)
    model = get_embedding_model()

    if vec_path.exists():
        vectors = np.load(vec_path)
        if len(vectors) != len(products):
            texts = [f"passage: {build_product_text(p)}" for p in products]
            vectors = model.encode(texts, normalize_embeddings=True)
            np.save(vec_path, vectors)
    else:
        texts = [f"passage: {build_product_text(p)}" for p in products]
        vectors = model.encode(texts, normalize_embeddings=True)
        np.save(vec_path, vectors)

    VECTOR_CACHE[slug] = vectors
    return vectors


def semantic_search(
    slug: str,
    query: str,
    brand: Optional[str],
    pmin: Optional[int],
    pmax: Optional[int],
    top_k: int = 100,
) -> List[Dict[str, Any]]:
    products = load_products(slug)
    if not products:
        return []

    vectors = load_vectors(slug)
    model = get_embedding_model()

    q_emb = model.encode([f"query: {query}"], normalize_embeddings=True)[0]
    scores = vectors @ q_emb
    idx = np.argsort(-scores)

    results = []
    for i in idx:
        p = products[int(i)]

        if brand and p.get("ten_thuong_hieu") != brand:
            continue

        price = p.get("gia_ban", 0) or 0
        if pmin is not None and price < pmin:
            continue
        if pmax is not None and price > pmax:
            continue

        results.append(p)
        if len(results) >= top_k:
            break

    return results


# =====================================
# GROQ (SAFE)
# =====================================

def call_groq(category_label: str, brand: Optional[str]) -> str:
    client = get_groq_client()
    brand_text = brand or "không giới hạn"

    return client.chat.completions.create(
        model="llama-3.3-70b-versatile",
        messages=[
            {
                "role": "system",
                "content": (
                    "Bạn là trợ lý tư vấn bán dụng cụ cầu lông.\n"
                    "Luôn tư vấn ĐÚNG loại sản phẩm mà khách đang tìm.\n"
                    "KHÔNG nêu tên sản phẩm cụ thể.\n"
                    "Chỉ tư vấn tiêu chí chọn, ưu/nhược điểm.\n"
                    "Cuối cùng gợi ý 'xem thêm'."
                ),
            },
            {
                "role": "user",
                "content": (
                    f"Khách đang tìm: {category_label}. "
                    f"Thương hiệu ưu tiên: {brand_text}. "
                    "Hãy tư vấn cách chọn."
                ),
            },
        ],
    ).choices[0].message.content


# =====================================
# SESSION
# =====================================

def default_session():
    return {
        "search_state": {
            "category": None,
            "brand": None,
            "price_min": None,
            "price_max": None,
        },
        "results": [],
        "offset": 0,
    }


def get_category_label(slug: str) -> str:
    for k, v in CATEGORY_MAPPING.items():
        if normalize(v).replace(" ", "-") == slug:
            return v
    return "Sản phẩm cầu lông"


# =====================================
# CHAT
# =====================================

@app.post("/chat")
def chat(req: ChatRequest):
    uid = req.session_id
    msg = req.message

    s = SESSION.get(uid) or default_session()
    st = s["search_state"]

    # relax price
    if is_relax_price(msg) and st["category"]:
        st["price_min"] = None
        st["price_max"] = None

    # load more
    if is_load_more(msg) and s["results"]:
        offset = s["offset"]
        batch = s["results"][offset: offset + PAGE_SIZE]
        s["offset"] += PAGE_SIZE
        SESSION[uid] = s
        return {
            "answer": "Mình gửi thêm cho bạn vài mẫu nữa nhé.",
            "products": batch,
        }

    # detect category
    cat = detect_category(msg)
    if cat:
        if st["category"] != cat:
            st["brand"] = None
            st["price_min"] = None
            st["price_max"] = None
        st["category"] = cat

    # brand + price
    brand, pmin, pmax = extract_filters(msg)
    if brand is not None:
        st["brand"] = brand
    if pmin is not None:
        st["price_min"] = pmin
    if pmax is not None:
        st["price_max"] = pmax

    # no category yet
    if not st["category"]:
        return {
            "answer": "Bạn muốn tìm vợt, giày, áo hay balo cầu lông vậy?",
            "products": [],
        }

    slug = st["category"]

    # search
    filtered = semantic_search(
        slug=slug,
        query=msg,
        brand=st["brand"],
        pmin=st["price_min"],
        pmax=st["price_max"],
        top_k=100,
    )

    if not filtered:
        return {
            "answer": "Không có sản phẩm phù hợp, bạn thử nới lỏng bộ lọc nhé.",
            "products": [],
        }

    # save
    s["results"] = filtered
    s["offset"] = PAGE_SIZE
    SESSION[uid] = s

    # groq
    answer = call_groq(get_category_label(slug), st["brand"])

    return {
        "answer": answer,
        "products": filtered[:PAGE_SIZE],
    }
