from fastapi import FastAPI
from pydantic import BaseModel
from groq import Groq
import json
import os
from pathlib import Path
from danh_muc import DANH_MUC_THUOC_TINH

# ==========================
# CONFIG + LOAD DATA
# ==========================

BASE_DIR = Path(__file__).resolve().parent
SAN_PHAM_PATH = BASE_DIR / "san_pham.json"

with open(SAN_PHAM_PATH, "r", encoding="utf-8") as f:
    SAN_PHAM_DATA = json.load(f)  # list[dict]

GROQ_API_KEY = os.getenv("GROQ_API_KEY", "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")
client = Groq(api_key=GROQ_API_KEY)

app = FastAPI()

# ==========================
# AI INTENT (giữ nguyên, nhưng thêm context)
# ==========================

INTENT_PROMPT = f"""
Bạn là hệ thống phân tích nhu cầu mua sắm.
Hãy đọc câu của khách và trả về JSON duy nhất.

Danh mục & thuộc tính hợp lệ (không được bịa thêm):

{json.dumps(DANH_MUC_THUOC_TINH, ensure_ascii=False, indent=2)}

KHI THIẾU THÔNG TIN:
Trả JSON:
{{
  "need_more_info": true,
  "question": "Câu hỏi cần hỏi thêm khách hàng"
}}

KHI ĐỦ THÔNG TIN:
Trả JSON dạng:
{{
  "need_more_info": false,
  "category": "vợt cầu lông" | "giày cầu lông" | null,
  "brand": string | null,
  "min_price": number | null,
  "max_price": number | null,
  "attributes": [
    {{"name": "...", "value": "..." }}
  ]
}}

YÊU CẦU:
- Không trả text ngoài JSON.
- Không giải thích.
- Không đoán bừa.
"""



def ai_intent(user_msg: str):
    rsp = client.chat.completions.create(
        model="llama-3.3-70b-versatile",
        messages=[
            {"role": "system", "content": INTENT_PROMPT},
            {"role": "user", "content": user_msg}
        ],
        temperature=0.2,
        max_tokens=400
    )

    raw = rsp.choices[0].message.content.strip()

    try:
        start = raw.index("{")
        end = raw.rindex("}") + 1
        return json.loads(raw[start:end])
    except Exception:
        return {
            "category": None,
            "brand": None,
            "min_price": None,
            "max_price": None,
            "attributes": []
        }

# ==========================
# SEARCH TỪ JSON
# ==========================

CATEGORY_MAPPING = {
    "vợt": "Vợt cầu lông",
    "vợt cầu lông": "Vợt cầu lông",
    "giày": "Giày cầu lông",
    "giày cầu lông": "Giày cầu lông",
}

def _norm(s: str | None) -> str:
    return s.lower().strip() if isinstance(s, str) else ""

def search_products_from_json(filters: dict):
    category = filters.get("category")
    brand = filters.get("brand")
    min_price = filters.get("min_price")
    max_price = filters.get("max_price")
    attributes = filters.get("attributes", [])

    # chuẩn hóa category
    target_category = None
    if category:
        target_category = CATEGORY_MAPPING.get(_norm(category))

    results = []

    for p in SAN_PHAM_DATA:
        # 1) Category
        if target_category:
            if _norm(p.get("ten_danh_muc")) != _norm(target_category):
                continue

        # 2) Brand
        if brand:
            if _norm(p.get("ten_thuong_hieu")) != _norm(brand):
                continue

        # 3) Price range – lấy giá_ban thấp nhất trong san_pham_chi_tiet
        chi_tiet = p.get("san_pham_chi_tiet") or []
        gia_list = [ct.get("gia_ban") for ct in chi_tiet if ct.get("gia_ban") is not None]
        if not gia_list:
            # không có giá → tạm bỏ
            continue

        min_gia_sp = min(gia_list)

        if min_price is not None and min_gia_sp < min_price:
            continue
        if max_price is not None and min_gia_sp > max_price:
            continue

        # 4) Attributes
        product_attrs = p.get("thuoc_tinh") or {}

        ok = True
        for attr in attributes:
            name = attr.get("name")
            value = attr.get("value")
            if not name or not value:
                continue

            prod_val = product_attrs.get(name)
            if not prod_val:
                ok = False
                break

            if _norm(prod_val) != _norm(value):
                ok = False
                break

        if not ok:
            continue

        # Nếu pass hết filter:
        results.append(p)

        if len(results) >= 12:
            break

    return results

# ==========================
# REQUEST MODEL + ROUTE
# ==========================

class ChatRequest(BaseModel):
    message: str

@app.post("/api/chatbot/search")
def api_search(req: ChatRequest):
    intent = ai_intent(req.message)

    # Nếu thiếu thông tin → hỏi thêm khách hàng
    if intent.get("need_more_info"):
        return {
            "type": "ask",
            "question": intent["question"]
        }

    # Ngược lại → tìm sản phẩm
    products = search_products_from_json(intent)

    return {
        "type": "products",
        "filters": intent,
        "products": products
    }
