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

from danh_muc import THUONG_HIEU_MAPPING
from danh_muc import CATEGORY_MAPPING

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
# CATEGORY
# =====================================

def detect_category(msg: str) -> Optional[str]:
    msg = normalize(msg)
    mapping = {
        "vot": "vot-cau-long",
        "vot cau long": "vot-cau-long",
        "giay": "giay-cau-long",
        "giay cau long": "giay-cau-long",
        "ao": "ao-cau-long",
        "ao cau long": "ao-cau-long",
        "quan": "quan-cau-long",
        "quan cau long": "quan-cau-long",
        "balo": "balo-cau-long",
        "tui": "tui-vot-cau-long",
        "tui vot": "tui-vot-cau-long",
        "vay": "vay-cau-long",
    }
    for k, v in mapping.items():
        if k in msg:
            return v
    return None


# =====================================
# BRAND + PRICE
# =====================================

def parse_price(val: str, unit: Optional[str]) -> int:
    # val đã là số dạng "1", "1.5", "1500", ...
    v = float(val.replace(",", "."))
    if unit in ("tr", "trieu"):
        return int(v * 1_000_000)
    if unit == "k":
        return int(v * 1_000)
    # nếu không có đơn vị, coi như v là VND
    return int(v)


def extract_filters(msg: str):
    """
    Trả về (brand, price_min, price_max)
    Hỗ trợ:
    - 'cao hon 2 trieu', 'tren 1.5tr', 'duoi 2tr', 'nho hon 800k'
    - '1tr den 2tr', '1.5 trieu - 2 trieu'
    - số thuần: 1500000, 1 500 000 (coi như giá đơn ±300k)
    """
    raw = msg
    msg = normalize(msg)

    # BRAND
    brand = None
    for k, v in THUONG_HIEU_MAPPING.items():
        if k in msg:
            brand = v
            break

    # CAO HƠN / TRÊN
    m = re.search(
        r"(cao hon|tren)\s*(\d+(?:[\.,]\d+)?)\s*(trieu|tr|k)",
        msg
    )
    if m:
        return brand, parse_price(m.group(2).replace(".", "").replace(",", "."), m.group(3)), None

    # DƯỚI / NHỎ HƠN
    m = re.search(
        r"(duoi|nho hon)\s*(\d+(?:[\.,]\d+)?)\s*(trieu|tr|k)",
        msg
    )
    if m:
        return brand, None, parse_price(m.group(2).replace(".", "").replace(",", "."), m.group(3))

    # KHOẢNG GIÁ: 1tr den 2tr, 1.5 trieu - 2 trieu
    m = re.search(
        r"(\d+(?:[\.,]\d+)?)\s*(trieu|tr|k)\s*(den|-)\s*(\d+(?:[\.,]\d+)?)\s*(trieu|tr|k)",
        msg
    )
    if m:
        p1 = parse_price(m.group(1).replace(".", "").replace(",", "."), m.group(2))
        p2 = parse_price(m.group(4).replace(".", "").replace(",", "."), m.group(5))
        return brand, min(p1, p2), max(p1, p2)

    # GIÁ ĐƠN có đơn vị → ±300k
    m = re.search(r"(\d+(?:[\.,]\d+)?)\s*(trieu|tr|k)", msg)
    if m:
        center = parse_price(m.group(1).replace(".", "").replace(",", "."), m.group(2))
        return brand, center - 300_000, center + 300_000

    # GIÁ ĐƠN LÀ SỐ THUẦN VND (>= 6 chữ số) → ±300k
    digits = re.findall(r"\d{6,9}", raw.replace(" ", ""))
    if digits:
        # lấy số đầu tiên
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
    """
    Text để embedding: tên + thương hiệu + danh mục + mô tả (nếu có)
    """
    parts = [
        str(p.get("ten_san_pham", "")),
        str(p.get("ten_thuong_hieu", "")),
        str(p.get("ten_danh_muc", "")),
        str(p.get("mo_ta_ngan", "")),
        str(p.get("mo_ta", "")),
    ]
    return " ".join([x for x in parts if x])


def load_vectors(slug: str) -> np.ndarray:
    """
    Mỗi slug tương ứng một file npy: vector-cache/<slug>.npy
    Nếu chưa có thì build mới.
    """
    if slug in VECTOR_CACHE:
        return VECTOR_CACHE[slug]

    vec_path = VECTOR_DIR / f"{slug}.npy"
    products = load_products(slug)
    model = get_embedding_model()

    if vec_path.exists():
        vectors = np.load(vec_path)
        # đảm bảo số vector khớp số sản phẩm, nếu lệch thì build lại
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
    scores = vectors @ q_emb  # cosine vì đã normalize
    idx = np.argsort(-scores)  # giảm dần

    results: List[Dict[str, Any]] = []
    for i in idx:
        p = products[int(i)]

        # lọc brand
        if brand and p.get("ten_thuong_hieu") != brand:
            continue

        # lọc giá
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
    """
    Tư vấn chung, KHÔNG đổi loại sản phẩm.
    """
    client = get_groq_client()
    brand_text = brand or "không giới hạn"

    return client.chat.completions.create(
        model="llama-3.3-70b-versatile",
        messages=[
            {
                "role": "system",
                "content": (
                    "Bạn là trợ lý tư vấn bán dụng cụ cầu lông.\n"
                    "Luôn tư vấn ĐÚNG loại sản phẩm mà khách đang tìm (ví dụ đang tìm vợt thì không được nói sang áo, giày...).\n"
                    "KHÔNG nêu tên sản phẩm cụ thể, KHÔNG đoán giá.\n"
                    "Chỉ đưa ra tiêu chí chọn mua, lưu ý quan trọng, ưu/nhược điểm.\n"
                    "Trả lời ngắn gọn, tiếng Việt, dễ hiểu.\n"
                    "Kết thúc bằng việc gợi ý khách có thể gõ 'xem thêm' nếu muốn xem thêm sản phẩm."
                ),
            },
            {
                "role": "user",
                "content": (
                    f"Khách đang tìm: {category_label}. "
                    f"Thương hiệu ưu tiên: {brand_text}. "
                    "Hãy tư vấn cách chọn phù hợp, không đổi sang loại sản phẩm khác."
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


# =====================================
# CHAT
# =====================================

@app.post("/chat")
def chat(req: ChatRequest):
    uid = req.session_id
    msg = req.message

    s = SESSION.get(uid) or default_session()
    st = s["search_state"]

    # 1. Bỏ lọc giá nếu user nói không quan tâm giá
    if is_relax_price(msg) and st["category"]:
        st["price_min"] = None
        st["price_max"] = None

    # 2. Xem thêm (load-more)
    if is_load_more(msg) and s["results"]:
        offset = s["offset"]
        batch = s["results"][offset: offset + PAGE_SIZE]
        s["offset"] += PAGE_SIZE
        SESSION[uid] = s
        return {
            "answer": "Mình gửi thêm cho bạn vài mẫu nữa nhé.",
            "products": batch,
        }

    # 3. Update category (nếu user nêu rõ)
    cat = detect_category(msg)
    if cat:
        # nếu đổi category thì reset filter
        if st["category"] != cat:
            st["brand"] = None
            st["price_min"] = None
            st["price_max"] = None
        st["category"] = cat

    # 4. Brand + Price filter
    brand, pmin, pmax = extract_filters(msg)

    if brand is not None:
        st["brand"] = brand
    if pmin is not None:
        st["price_min"] = pmin
    if pmax is not None:
        st["price_max"] = pmax

    # 5. Nếu chưa biết category thì hỏi lại
    if not st["category"]:
        return {
            "answer": "Bạn muốn tìm vợt, giày, áo hay balo cầu lông vậy?",
            "products": [],
        }

    slug = st["category"]

    # 6. Semantic search theo category + filter brand/giá
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
            "answer": "Hiện chưa có sản phẩm phù hợp với bộ lọc hiện tại, bạn thử nới lỏng giá hoặc bỏ lọc thương hiệu nhé.",
            "products": [],
        }

    # 7. Lưu state để lần sau 'xem thêm' dùng tiếp
    s["results"] = filtered
    s["offset"] = PAGE_SIZE
    SESSION[uid] = s

    # 8. Gọi Groq tư vấn chung, KHÔNG đổi category
    answer = call_groq(
        CATEGORY_LABEL.get(slug, "sản phẩm cầu lông"),
        st["brand"],
    )

    return {
        "answer": answer,
        "products": filtered[:PAGE_SIZE],
    }
