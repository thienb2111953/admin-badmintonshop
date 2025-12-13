from fastapi import FastAPI
from pydantic import BaseModel
from groq import Groq
import json
import unicodedata
import re
from pathlib import Path
from typing import Dict, Any, Optional, List
from danh_muc import THUONG_HIEU_MAPPING

import numpy as np
from sentence_transformers import SentenceTransformer
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

client = Groq(api_key=API_KEY_GROQ)
app = FastAPI()

_embedding_model = None


def get_embedding_model():
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
    sender: str
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
    return any(k in msg for k in [
        "xem them", "them nua", "tiep di", "tiep tuc"
    ])


def is_relax_price(msg: str) -> bool:
    msg = normalize(msg)
    return any(k in msg for k in [
        "khong quan tam gia",
        "khong quan tam ve gia",
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
        "giay": "giay-cau-long",
        "ao": "ao-cau-long",
        "quan": "quan-cau-long",
        "balo": "balo-cau-long",
        "tui": "tui-vot-cau-long",
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
    v = float(val.replace(",", "."))
    if unit in ("tr", "trieu"):
        return int(v * 1_000_000)
    if unit == "k":
        return int(v * 1_000)
    return int(v * 1_000_000)


def extract_filters(msg: str):
    msg = normalize(msg)

    # ===== BRAND từ mapping =====
    brand = None
    for k, v in THUONG_HIEU_MAPPING.items():
        if k in msg:
            brand = v
            break

    # ===== PRICE =====
    m = re.search(r"(\d+(?:\.\d+)?)\s*(trieu|tr|k)", msg)
    if m:
        center = parse_price(m.group(1), m.group(2))
        return brand, center - 300_000, center + 300_000

    return brand, None, None


# =====================================
# DATA
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


def filter_products(products, brand, pmin, pmax):
    result = []
    for p in products:
        if brand and p.get("ten_thuong_hieu") != brand:
            continue

        price = p.get("gia_ban", 0)
        if pmin is not None and price < pmin:
            continue
        if pmax is not None and price > pmax:
            continue

        result.append(p)
    return result


# =====================================
# GROQ (SAFE)
# =====================================

def call_groq(category_label: str, brand: Optional[str]) -> str:
    return client.chat.completions.create(
        model="llama-3.3-70b-versatile",
        messages=[
            {
                "role": "system",
                "content": (
                    "Bạn là trợ lý tư vấn bán dụng cụ cầu lông.\n"
                    "CẤM TUYỆT ĐỐI:\n"
                    "- KHÔNG nêu tên sản phẩm\n"
                    "- KHÔNG nêu model\n"
                    "- KHÔNG đoán giá\n"
                    "CHỈ tư vấn tiêu chí chọn chung."
                ),
            },
            {
                "role": "user",
                "content": (
                    f"Khách đang tìm {category_label} "
                    f"thương hiệu {brand or 'không giới hạn'}. "
                    "Hãy tư vấn ngắn gọn và nhắc khách có thể gõ 'xem thêm'."
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
        "offset": 0
    }


# =====================================
# CHAT
# =====================================

@app.post("/chat")
def chat(req: ChatRequest):
    uid = req.sender
    msg = req.message

    s = SESSION.get(uid) or default_session()
    st = s["search_state"]

    # ===============================
    # 1. RELAX PRICE → XOÁ GIÁ + SEARCH LẠI
    # ===============================
    if is_relax_price(msg) and st["category"]:
        st["price_min"] = None
        st["price_max"] = None

        products = load_products(st["category"])
        filtered = filter_products(
            products,
            st["brand"],
            None,
            None
        )

        s["results"] = filtered
        s["offset"] = PAGE_SIZE
        SESSION[uid] = s

        if not filtered:
            return {
                "reply": "Hiện tại chưa có sản phẩm phù hợp.",
                "products": []
            }

        reply = call_groq(
            CATEGORY_LABEL[st["category"]],
            st["brand"]
        )

        return {
            "reply": reply,
            "products": filtered[:PAGE_SIZE]
        }

    # ===============================
    # 2. LOAD MORE
    # ===============================
    if is_load_more(msg) and s["results"]:
        offset = s["offset"]
        batch = s["results"][offset: offset + PAGE_SIZE]
        s["offset"] += PAGE_SIZE
        SESSION[uid] = s
        return {
            "reply": f"Mình gửi thêm {len(batch)} sản phẩm cho bạn.",
            "products": batch
        }

    # ===============================
    # 3. NEW / UPDATE SEARCH
    # ===============================
    cat = detect_category(msg)
    brand, pmin, pmax = extract_filters(msg)

    if cat:
        st["category"] = cat
        st["price_min"] = None
        st["price_max"] = None
        st["brand"] = None

    if brand is not None:
        st["brand"] = brand
    if pmin is not None:
        st["price_min"] = pmin
    if pmax is not None:
        st["price_max"] = pmax

    if not st["category"]:
        return {
            "reply": "Bạn muốn tìm vợt, giày hay balo cầu lông?",
            "products": []
        }

    # ===============================
    # 4. SEARCH
    # ===============================
    products = load_products(st["category"])
    filtered = filter_products(
        products,
        st["brand"],
        st["price_min"],
        st["price_max"]
    )

    if not filtered:
        return {
            "reply": "Hiện tại chưa tìm được sản phẩm phù hợp.",
            "products": []
        }

    s["results"] = filtered
    s["offset"] = PAGE_SIZE
    SESSION[uid] = s

    reply = call_groq(
        CATEGORY_LABEL[st["category"]],
        st["brand"]
    )

    return {
        "reply": reply,
        "products": filtered[:PAGE_SIZE]
    }
