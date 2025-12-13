from fastapi import FastAPI
from pydantic import BaseModel
from groq import Groq
import json
import unicodedata
import re
from pathlib import Path
from conn import API_KEY_GROQ

# =====================================
# CONFIG
# =====================================

BASE_DIR = Path(__file__).resolve().parent
DIR_DANH_MUC = BASE_DIR / "danh-muc"

SESSION_LAST_FILTERS = {}

client = Groq(api_key=API_KEY_GROQ)
app = FastAPI()


# =====================================
# INPUT MODEL
# =====================================

class ChatRequest(BaseModel):
    sender: str
    message: str


# =====================================
# UTIL: NORMALIZE TEXT
# =====================================

def normalize(text: str) -> str:
    text = text.lower()
    text = unicodedata.normalize("NFD", text)
    return "".join(ch for ch in text if unicodedata.category(ch) != "Mn")


# =====================================
# 1. DETECT CATEGORY
# =====================================

def detect_category(msg: str):
    msg = normalize(msg)

    mapping = {
        "vot": "vot-cau-long",
        "vot cau long": "vot-cau-long",
        "giay": "giay-cau-long",
        "giay cau long": "giay-cau-long",
        "ao": "ao-cau-long",
        "quan": "quan-cau-long",
        "balo": "balo-cau-long",
        "tui": "tui-cau-long",
        "vay": "vay-cau-long",
    }

    for k, v in mapping.items():
        if k in msg:
            return v

    return None


# =====================================
# 2. EXTRACT FILTERS
# =====================================

def extract_filters(msg: str):
    msg = normalize(msg)

    brand = None
    price_min = None
    price_max = None
    is_explicit_range = False

    # ----- BRAND -----
    brands = ["yonex", "lining", "apacs", "mizuno", "kumpoo", "victor"]
    for b in brands:
        if b in msg:
            brand = b.capitalize()

    # ----- PRICE: "từ X đến Y" -----
    patterns = [
        r"tu\s*(\d+)\s*trieu\s*den\s*(\d+)\s*trieu",
        r"(\d+)\s*tr\s*-\s*(\d+)\s*tr",
        r"(\d+)\s*trieu\s*-\s*(\d+)\s*trieu",
    ]

    for p in patterns:
        m = re.search(p, msg)
        if m:
            price_min = int(m.group(1)) * 1_000_000
            price_max = int(m.group(2)) * 1_000_000
            is_explicit_range = True
            return brand, price_min, price_max, is_explicit_range

    # ----- PRICE: "tầm / khoảng" -----
    approx_prices = {
        "1 trieu": 1_000_000,
        "1tr": 1_000_000,
        "2 trieu": 2_000_000,
        "2tr": 2_000_000,
        "900k": 900_000,
        "800k": 800_000,
        "700k": 700_000,
    }

    for k, v in approx_prices.items():
        if k in msg:
            price_min = v - 300_000
            price_max = v + 300_000
            return brand, price_min, price_max, is_explicit_range

    return brand, price_min, price_max, is_explicit_range


# =====================================
# 3. LOAD PRODUCTS
# =====================================

def load_products_by_category(slug: str):
    path = DIR_DANH_MUC / f"{slug}.json"
    if not path.exists():
        return []

    with open(path, "r", encoding="utf-8") as f:
        return json.load(f)


# =====================================
# 4. FILTER PRODUCTS
# =====================================

def filter_products(products, brand=None, price_min=None, price_max=None):
    result = []

    for p in products:
        if brand and p.get("ten_thuong_hieu", "").lower() != brand.lower():
            continue

        prices = [ct["gia_ban"] for ct in p.get("san_pham_chi_tiet", [])]
        if not prices:
            continue

        min_price = min(prices)

        if price_min is not None and min_price < price_min:
            continue
        if price_max is not None and min_price > price_max:
            continue

        result.append(p)

    return result


# =====================================
# 5. AI RESPONSE (NO HALLUCINATION)
# =====================================

def call_groq(prompt: str):
    completion = client.chat.completions.create(
        model="llama-3.3-70b-versatile",
        messages=[
            {
                "role": "system",
                "content": (
                    "Bạn là trợ lý tìm kiếm sản phẩm cầu lông. "
                    "Chỉ mô tả chung, KHÔNG liệt kê tên sản phẩm, "
                    "KHÔNG bịa thương hiệu, KHÔNG tạo danh sách."
                )
            },
            {"role": "user", "content": prompt}
        ]
    )
    return completion.choices[0].message.content


# =====================================
# 6. CHAT ENDPOINT (RESET SESSION LOGIC)
# =====================================

@app.post("/chat")
def chat(req: ChatRequest):
    uid = req.sender
    msg = req.message

    # detect intent
    cat_new = detect_category(msg)
    brand_new, pmin_new, pmax_new, is_explicit = extract_filters(msg)

    # ============================
    # 🔥 RESET SESSION NẾU LÀ TRUY VẤN ĐẦY ĐỦ
    # ============================
    if is_explicit and cat_new and brand_new:
        session = {
            "category": cat_new,
            "brand": brand_new,
            "price_min": pmin_new,
            "price_max": pmax_new
        }
    else:
        session = SESSION_LAST_FILTERS.get(uid, {
            "category": None,
            "brand": None,
            "price_min": None,
            "price_max": None
        })

        if cat_new:
            session["category"] = cat_new
        if brand_new:
            session["brand"] = brand_new

        if is_explicit:
            session["price_min"] = pmin_new
            session["price_max"] = pmax_new
        else:
            if pmin_new is not None:
                session["price_min"] = pmin_new
            if pmax_new is not None:
                session["price_max"] = pmax_new

    SESSION_LAST_FILTERS[uid] = session

    # chưa có danh mục
    if not session["category"]:
        return {
            "reply": call_groq(
                f"Khách nói: {msg}. Hãy hỏi lại khách họ muốn tìm loại sản phẩm nào."
            ),
            "products": []
        }

    products = load_products_by_category(session["category"])
    filtered = filter_products(
        products,
        brand=session["brand"],
        price_min=session["price_min"],
        price_max=session["price_max"]
    )

    reply = call_groq(
        f"Khách đang tìm {session['category']} "
        f"thương hiệu {session['brand']} "
        f"trong khoảng giá từ {session['price_min']} đến {session['price_max']}. "
        f"Hãy tư vấn ngắn gọn."
    )

    return {
        "reply": reply,
        "products": filtered
    }
