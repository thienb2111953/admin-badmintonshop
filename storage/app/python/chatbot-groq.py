from fastapi import FastAPI
from pydantic import BaseModel
from groq import Groq
import json
import unicodedata
import re
from pathlib import Path
from typing import Dict, Any, Optional, List, Tuple

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
        if not API_KEY_GROQ:
            raise RuntimeError("Missing API_KEY_GROQ")
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
    text = (text or "").lower()
    text = unicodedata.normalize("NFD", text)
    return "".join(c for c in text if unicodedata.category(c) != "Mn")


def is_load_more(msg: str) -> bool:
    msg = normalize(msg)
    return any(k in msg for k in ["xem them", "them nua", "tiep di", "tiep tuc", "cho them", "xem tiep"])


def is_relax_price(msg: str) -> bool:
    msg = normalize(msg)
    return any(k in msg for k in ["khong quan tam gia", "bo gia", "gia nao cung duoc", "khong can gia"])


def is_combo_intent(msg: str) -> bool:
    msg = normalize(msg)
    if is_next_combo(msg):
        return False
    return any(k in msg for k in [
        "combo",
        "bo dung cu",
        "day du",
        "bao gom",
        "set do",
        "tron bo",
        "liet ke",
    ])

def is_next_combo(msg: str) -> bool:
    msg = normalize(msg)
    return any(k in msg for k in [
        "combo khac",
        "cho combo khac",
        "combo nua",
        "cho combo nua",
        "set khac",
        "doi combo",
        "doi set",
    ])


def is_many_combo(msg: str) -> bool:
    msg = normalize(msg)
    return any(k in msg for k in [
        "cho nhieu combo",
        "nhieu combo",
        "liet ke nhieu combo",
        "cho vai combo",
        "combo nhieu hon",
    ])


def has_category_in_message(msg: str) -> bool:
    msg_norm = normalize(msg)
    for raw_key in CATEGORY_MAPPING.keys():
        key_norm = normalize(raw_key)
        pattern = r"\b{}\b".format(re.escape(key_norm))
        if re.search(pattern, msg_norm):
            return True
    return False


# =====================================
# CATEGORY
# =====================================

def detect_category(msg: str) -> Optional[str]:
    """
    Match theo TỪ NGUYÊN VẸN bằng regex \bword\b
    Không match substring (vd: 'cao' != 'ao')
    """
    msg_norm = normalize(msg)

    for raw_key, full_label in CATEGORY_MAPPING.items():
        key_norm = normalize(raw_key)
        pattern = r"\b{}\b".format(re.escape(key_norm))
        if re.search(pattern, msg_norm):
            return normalize(full_label).replace(" ", "-")

    return None


def detect_multi_categories(msg: str) -> List[str]:
    """
    Dò nhiều category trong 1 câu để làm combo.
    """
    msg_norm = normalize(msg)
    slugs: List[str] = []

    for raw_key, full_label in CATEGORY_MAPPING.items():
        key_norm = normalize(raw_key)
        pattern = r"\b{}\b".format(re.escape(key_norm))
        if re.search(pattern, msg_norm):
            slugs.append(normalize(full_label).replace(" ", "-"))

    # unique + giữ thứ tự tương đối
    seen = set()
    out = []
    for x in slugs:
        if x not in seen:
            seen.add(x)
            out.append(x)
    return out


# =====================================
# BRAND + PRICE
# =====================================

def parse_price(val: str, unit: Optional[str]) -> int:
    v = float(val.replace(",", "."))
    if unit in ("tr", "trieu"):
        return int(v * 1_000_000)
    if unit == "k":
        return int(v * 1_000)
    return int(v)


def extract_filters(msg: str) -> Tuple[Optional[str], Optional[int], Optional[int]]:
    raw = msg or ""
    msg_norm = normalize(msg)

    # BRAND: match key đã normalize để chắc chắn
    brand = None
    for k, v in THUONG_HIEU_MAPPING.items():
        k_norm = normalize(k)
        if k_norm and k_norm in msg_norm:
            brand = v
            break

    # CAO HƠN / TRÊN / LỚN HƠN / >
    m = re.search(r"(cao hon|tren|lon hon|>)\s*(\d+(?:[\.,]\d+)?)\s*(trieu|tr|k)?", msg_norm)
    if m:
        unit = m.group(3) or "vnd"
        return brand, parse_price(m.group(2), unit), None

    # DƯỚI / NHỎ HƠN / <
    m = re.search(r"(duoi|nho hon|<)\s*(\d+(?:[\.,]\d+)?)\s*(trieu|tr|k)?", msg_norm)
    if m:
        unit = m.group(3) or "vnd"
        return brand, None, parse_price(m.group(2), unit)

    # RANGE: 1tr - 2tr
    m = re.search(
        r"(\d+(?:[\.,]\d+)?)\s*(trieu|tr|k)\s*(den|-)\s*(\d+(?:[\.,]\d+)?)\s*(trieu|tr|k)",
        msg_norm
    )
    if m:
        p1 = parse_price(m.group(1), m.group(2))
        p2 = parse_price(m.group(4), m.group(5))
        return brand, min(p1, p2), max(p1, p2)

    # GIÁ ĐƠN (1tr → ±300k)
    m = re.search(r"(\d+(?:[\.,]\d+)?)\s*(trieu|tr|k)", msg_norm)
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
# GROQ (ANSWER SHORT)
# =====================================

def call_groq(category_label: str, brand: Optional[str]) -> str:
    client = get_groq_client()
    brand_text = brand or "không giới hạn"

    return client.chat.completions.create(
        model="llama-3.1-8b-instant",
        messages=[
            {
                "role": "system",
                "content": (
                    "Bạn là trợ lý tư vấn bán dụng cụ cầu lông của Badminton Shop.\n"
                    "CHỈ trả lời TỐI ĐA 2 dòng, mỗi dòng 1 câu.\n"
                    "KHÔNG bullet, KHÔNG liệt kê dài, KHÔNG nêu tên sản phẩm cụ thể.\n"
                    "Dòng 1: tiêu chí chọn chính, đúng loại sản phẩm.\n"
                    "Dòng 2: kết thúc bằng gợi ý gõ 'xem thêm'."
                ),
            },
            {
                "role": "user",
                "content": (
                    f"Khách đang tìm: {category_label}. "
                    f"Thương hiệu ưu tiên: {brand_text}. "
                    "Tư vấn ngắn gọn."
                ),
            },
        ],
        max_tokens=80,
        temperature=0.5,
    ).choices[0].message.content.strip()


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
    for _, v in CATEGORY_MAPPING.items():
        if normalize(v).replace(" ", "-") == slug:
            return v
    return "Sản phẩm cầu lông"


# =====================================
# COMBO
# =====================================
COMBO_PER_CATEGORY = 6
COMBO_MAX_RETURN = 12


def default_combo_state():
    return {
        "slugs": [],
        "brand": None,
        "price_min": None,
        "price_max": None,
        "candidates": {},   # slug -> list sản phẩm
        "combo_index": 0,
        "last_query": "",
    }


def prepare_combo_candidates(
    slugs: List[str],
    query: str,
    brand: Optional[str],
    pmin: Optional[int],
    pmax: Optional[int],
    per_category: int = COMBO_PER_CATEGORY,
) -> Dict[str, List[Dict[str, Any]]]:
    data: Dict[str, List[Dict[str, Any]]] = {}
    for slug in slugs:
        data[slug] = semantic_search(
            slug=slug,
            query=query,
            brand=brand,
            pmin=pmin,
            pmax=pmax,
            top_k=per_category,
        )
    return data


def build_combo_by_index(
    candidates: Dict[str, List[Dict[str, Any]]],
    index: int,
) -> List[Dict[str, Any]]:
    combo: List[Dict[str, Any]] = []
    for slug, items in candidates.items():
        if not items:
            continue
        combo.append(items[index % len(items)])
    return combo


def combo_can_generate(candidates: Dict[str, List[Dict[str, Any]]]) -> bool:
    non_empty = sum(1 for items in candidates.values() if items)
    return non_empty >= 2


def calc_possible_combo_count(candidates: Dict[str, List[Dict[str, Any]]]) -> int:
    lens = [len(items) for items in candidates.values() if items]
    return max(lens) if lens else 0

def build_combo(
    slugs: List[str],
    query: str,
    brand: Optional[str],
    pmin: Optional[int],
    pmax: Optional[int],
) -> List[Dict[str, Any]]:
    """
    Chọn 1 sản phẩm/top cho mỗi danh mục trong combo.
    (Có thể mở rộng: nhiều lựa chọn mỗi danh mục)
    """
    combo: List[Dict[str, Any]] = []
    for slug in slugs:
        results = semantic_search(
            slug=slug,
            query=query,
            brand=brand,
            pmin=pmin,
            pmax=pmax,
            top_k=1,
        )
        if results:
            combo.append(results[0])
    return combo

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
        "combo_state": None,   # 👈 thêm
    }

def is_warranty_policy(msg: str) -> bool:
    msg = normalize(msg)
    keywords = [
        "chinh sach bao hanh",
        "bao hanh",
        "doi tra",
        "doi moi",
        "bao hanh nhu the nao",
        "chinh sach doi tra",
    ]
    return any(k in msg for k in keywords)

WARRANTY_TEXT = (
    "Nếu sản phẩm xảy ra lỗi như những tình trạng trên, quý khách vui lòng thực hiện các bước sau để cửa hàng hỗ trợ bảo hành:\n\n"
    "(Bước 1) Khi phát hiện lỗi sản phẩm, quý khách vui lòng giữ nguyên hiện trạng và liên hệ ngay với Shop Badminton "
    "qua LIÊN HỆ VỚI CHÚNG TÔI để yêu cầu bảo hành.\n\n"
    "(Bước 2) Quý khách vui lòng điền đầy đủ thông tin bao gồm (thông tin liên hệ), "
    "(thông tin sản phẩm) và (mô tả chi tiết lỗi gặp phải).\n\n"
    "(Bước 3) Sau khi admin tiếp nhận yêu cầu bảo hành, chúng tôi sẽ phản hồi lại qua (EMAIL) "
    "mà quý khách đã cung cấp. Vui lòng theo dõi thông báo từ email.\n\n"
    "(Bước 4) Trong trường hợp sản phẩm bị lỗi do nhà sản xuất, quý khách sẽ được (đổi sản phẩm mới) "
    "theo chính sách bảo hành."
)




# =====================================
# CHAT
# =====================================

@app.post("/chat")
def chat(req: ChatRequest):
    uid = req.session_id
    msg = req.message or ""

    if is_warranty_policy(msg):
        return {"answer": WARRANTY_TEXT, "products": []}

    s = SESSION.get(uid) or default_session()
    st = s["search_state"]

    # relax price
    if is_relax_price(msg) and (st.get("category") or st.get("brand")):
        st["price_min"] = None
        st["price_max"] = None

    # load more (chỉ áp dụng flow 1-category cũ)
    if is_load_more(msg) and s["results"]:
        offset = s["offset"]
        batch = s["results"][offset: offset + PAGE_SIZE]
        s["offset"] += PAGE_SIZE
        SESSION[uid] = s
        return {
            "answer": "Mình gửi thêm vài mẫu nữa.\nGõ 'xem thêm' nếu muốn tiếp.",
            "products": batch,
        }

    # =====================================
    # COMBO FLOW (return sớm)
    # =====================================
    if is_next_combo(msg):
        cs = s.get("combo_state")
        if not cs:
            return {
                "answer": "Bạn chưa chọn combo nào trước đó.\nHãy nói 'combo vợt giày balo' nhé.",
                "products": [],
            }

        cs["combo_index"] += 1
        idx = cs["combo_index"]

        max_combo = calc_possible_combo_count(cs["candidates"])
        if idx >= max_combo:
            return {
                "answer": "Mình đã gợi ý hết các combo phù hợp.\nBạn muốn đổi yêu cầu không?",
                "products": [],
            }

        combo = build_combo_by_index(cs["candidates"], idx)
        SESSION[uid] = s

        return {
            "answer": "Đây là combo khác để bạn so sánh.\nGõ 'combo khác' nếu muốn xem tiếp.",
            "products": combo,
        }

    if is_combo_intent(msg):
        brand, pmin, pmax = extract_filters(msg)
        slugs = detect_multi_categories(msg)

        if not slugs:
            return {
                "answer": "Bạn muốn combo gồm vợt, giày, balo hay quần áo?\nBạn hãy liệt kê giúp mình.",
                "products": [],
            }

        candidates = prepare_combo_candidates(
            slugs=slugs,
            query=msg,
            brand=brand,
            pmin=pmin,
            pmax=pmax,
        )

        if not combo_can_generate(candidates):
            return {
                "answer": "Không đủ sản phẩm để tạo combo phù hợp.\nBạn thử đổi yêu cầu nhé.",
                "products": [],
            }

        combo = build_combo_by_index(candidates, 0)

        s["combo_state"] = {
            "slugs": slugs,
            "brand": brand,
            "price_min": pmin,
            "price_max": pmax,
            "candidates": candidates,
            "combo_index": 0,
            "last_query": msg,
        }

        SESSION[uid] = s

        return {
            "answer": "Mình gợi ý 1 combo phù hợp để bắt đầu.\nGõ 'combo khác' để xem bộ khác.",
            "products": combo,
        }


    # =====================================
    # FLOW CŨ: 1 CATEGORY
    # =====================================

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
            "answer": "Bạn muốn tìm vợt, giày, áo, quần hay balo?\nHãy nói rõ danh mục giúp mình.",
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
            "answer": "Không có sản phẩm phù hợp theo bộ lọc hiện tại.\nBạn thử nới lỏng giá hoặc đổi thương hiệu.",
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
