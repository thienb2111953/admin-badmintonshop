from fastapi import FastAPI
from pydantic import BaseModel
from groq import Groq
import json
import os
from pathlib import Path
from danh_muc import DANH_MUC_THUOC_TINH
from conn import API_KEY_GROQ


# ==========================
# LOAD DATA
# ==========================

BASE_DIR = Path(__file__).resolve().parent
SAN_PHAM_PATH = BASE_DIR / "san_pham.json"

with open(SAN_PHAM_PATH, "r", encoding="utf-8") as f:
    SAN_PHAM_DATA = json.load(f)

client = Groq(api_key=os.getenv("GROQ_API_KEY", API_KEY_GROQ))
app = FastAPI()


# ==========================
# SESSION CONTEXT (GHI NHỚ)
# ==========================

SESSION_CONTEXT = {
    "category": None,
    "brand": None,
    "min_price": None,
    "max_price": None,
    "attributes": {}
}


SESSION_LAST_FILTERS = {}

def reset_context():
    SESSION_CONTEXT["category"] = None
    SESSION_CONTEXT["brand"] = None
    SESSION_CONTEXT["min_price"] = None
    SESSION_CONTEXT["max_price"] = None
    SESSION_CONTEXT["attributes"] = {}



# ==========================
# AI PROMPT — luôn trả JSON
# ==========================

INTENT_PROMPT = """
Bạn là hệ thống phân tích nhu cầu mua sắm thể thao.

Nhiệm vụ:
- Hiểu category: vợt, giày, balo…
- Hiểu thương hiệu: Yonex, Lining, Mizuno…
- Hiểu giá: dưới X, tầm X, khoảng X → tự tạo min/max price hợp lý (±30%)
- Hiểu trình độ chơi: người mới chơi, trung bình, khá tốt
    → map thành thuộc tính thật.

Output bắt buộc dạng JSON:
{
  "need_more_info": false,
  "category": string | null,
  "brand": string | null,
  "min_price": number | null,
  "max_price": number | null,
  "attributes": [
    {"name": "...", "value": "..."}
  ]
}

KHÔNG trả text ngoài JSON.
"""


def ai_intent(message: str):
    rsp = client.chat.completions.create(
        model="llama-3.3-70b-versatile",
        messages=[
            {"role": "system", "content": INTENT_PROMPT},
            {"role": "user", "content": message}
        ],
        temperature=0.2,
        max_tokens=300
    )

    raw = rsp.choices[0].message.content.strip()

    try:
        start = raw.index("{")
        end = raw.rindex("}") + 1
        return json.loads(raw[start:end])
    except:
        return {
            "need_more_info": False,
            "category": None,
            "brand": None,
            "min_price": None,
            "max_price": None,
            "attributes": []
        }


# ==========================
# CẬP NHẬT CONTEXT (ghi nhớ)
# ==========================

def update_context(intent):

    # Category
    if intent.get("category"):
        SESSION_CONTEXT["category"] = intent["category"]

    # Brand
    if intent.get("brand"):
        SESSION_CONTEXT["brand"] = intent["brand"]

    # Price
    if intent.get("min_price") is not None:
        SESSION_CONTEXT["min_price"] = intent["min_price"]

    if intent.get("max_price") is not None:
        SESSION_CONTEXT["max_price"] = intent["max_price"]

    # Attributes
    for attr in intent.get("attributes", []):
        name = attr.get("name")
        value = attr.get("value")
        if name and value:
            SESSION_CONTEXT["attributes"][name] = value


# ==========================
# CHECK USER ASK "MORE"
# ==========================

def is_more_request(message: str):
    msg = message.lower()
    return any(k in msg for k in [
        "thêm sản phẩm",
        "xem thêm",
        "cho thêm",
        "còn sản phẩm",
        "thêm nữa"
    ])


# ==========================
# PRODUCT FILTER
# ==========================

CATEGORY_MAPPING = {
    "vợt": "Vợt cầu lông",
    "vợt cầu lông": "Vợt cầu lông",
    "giày": "Giày cầu lông",
    "giày cầu lông": "Giày cầu lông",
}

def _norm(s):
    return s.lower().strip() if isinstance(s, str) else ""


def expand_level_attributes(ctx_attrs):

    # Nếu user đã chọn thuộc tính rõ ràng thì giữ nguyên
    if ctx_attrs:
        if any("Độ cứng" in k or "Điểm cân" in k for k in ctx_attrs):
            return ctx_attrs

    # Otherwise dùng "trình độ chơi"
    level = None
    for name, val in ctx_attrs.items():
        if _norm(name) == "trình độ chơi":
            level = _norm(val)

    if not level:
        return ctx_attrs

    # Mapping
    if level in ["mới chơi", "newbie"]:
        return {
            "Độ cứng đũa": "Dẻo",
            "Điểm cân bằng": "Nhẹ đầu"
        }

    if level == "trung bình":
        return {
            "Phong cách chơi": "Công thủ toàn diện"
        }

    if level in ["khá tốt", "đã chơi lâu"]:
        return {
            "Độ cứng đũa": "Cứng",
            "Điểm cân bằng": "Nặng đầu"
        }

    return ctx_attrs



def search_products(ctx, full=False):

    category = ctx["category"]
    brand = ctx["brand"]
    min_price = ctx["min_price"]
    max_price = ctx["max_price"]
    attributes = expand_level_attributes(ctx["attributes"])

    # mở range giá thêm +300k
    if min_price and max_price:
        mid = (min_price + max_price) / 2
        max_price = mid + 300000

    target_cat = CATEGORY_MAPPING.get(_norm(category)) if category else None

    results = []

    for p in SAN_PHAM_DATA:

        # category
        if target_cat and _norm(p["ten_danh_muc"]) != _norm(target_cat):
            continue

        # brand
        if brand and _norm(p["ten_thuong_hieu"]) != _norm(brand):
            continue

        # price
        variants = p["san_pham_chi_tiet"]
        prices = [v["gia_ban"] for v in variants]

        if not prices:
            continue

        min_price_sp = min(prices)

        if min_price and min_price_sp < min_price:
            continue
        if max_price and min_price_sp > max_price:
            continue

        # attributes
        p_attrs = p["thuoc_tinh"]
        ok = True
        for k, v in attributes.items():
            if _norm(p_attrs.get(k)) != _norm(v):
                ok = False
                break

        if not ok:
            continue

        results.append(p)

    results = sorted(results, key=lambda x: x["ngay_tao"], reverse=True)

    return results if full else results[:5]



# ==========================
# API ROUTE
# ==========================

class ChatRequest(BaseModel):
    message: str


@app.post("/api/chatbot/search")
def api_search(req: ChatRequest):

    # User muốn xem thêm
    if is_more_request(req.message):
        filters = SESSION_CONTEXT.copy()
        products = search_products(filters, full=True)

        return {
            "type": "products_more",
            "filters": filters,
            "products": products,
            "ask_more": "Bạn có muốn lọc thêm theo tiêu chí nào nữa không?"
        }

    # Intent
    intent = ai_intent(req.message)

    # Update context ghi nhớ
    update_context(intent)

    # Search sản phẩm
    products = search_products(SESSION_CONTEXT)

    # Tạo gợi ý tiếp theo theo category
    cat = SESSION_CONTEXT["category"]
    brand = SESSION_CONTEXT["brand"]

    if cat and _norm(cat) in ["vợt", "vợt cầu lông"]:
        follow_up = (
            "Bạn có muốn chọn thương hiệu (Yonex, Lining, Mizuno) "
            "hoặc trình độ chơi (Mới chơi, Trung bình, Khá tốt) không?"
        )
    else:
        follow_up = "Bạn muốn lọc theo thương hiệu hoặc thuộc tính nào nữa không?"

    return {
        "type": "products",
        "filters": SESSION_CONTEXT,
        "products": products,
        "ask_more": follow_up
    }
