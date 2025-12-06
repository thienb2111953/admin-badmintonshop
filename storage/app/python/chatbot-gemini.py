from fastapi import FastAPI
from pydantic import BaseModel
import json
from pathlib import Path
from google.genai import Client, types
from typing import List, Optional, Any

# =========================
# CONFIG & IMPORTS
# =========================

try:
    from conn import API_KEY_GEMINI
except ImportError:
    API_KEY_GEMINI = "YOUR_API_KEY_HERE"

BASE_DIR = Path(__file__).resolve().parent
PRODUCT_FILE = BASE_DIR / "san_pham.json"

# =========================
# 1. HÀM XỬ LÝ DỮ LIỆU
# =========================

def load_products():
    if not PRODUCT_FILE.exists():
        return []
    try:
        with open(PRODUCT_FILE, "r", encoding="utf-8") as f:
            return json.load(f)
    except Exception:
        return []

PRODUCT_DATA_RAW = load_products()

def build_product_context(products):
    lines = []
    for p in products:
        gia_ban = p["san_pham_chi_tiet"][0]["gia_ban"] if p["san_pham_chi_tiet"] else "N/A"

        lines.append(
f"""
SẢN PHẨM:
  - id: {p['id_san_pham']}
  - tên: {p['ten_san_pham']}
  - slug: {p['slug']}
  - thương hiệu: {p['ten_thuong_hieu']}
  - danh mục: {p['ten_danh_muc']}
  - giá: {gia_ban}
  - thuộc tính:
      {json.dumps(p['thuoc_tinh'], ensure_ascii=False)}
"""
        )
    return "\n".join(lines)


# --- HÀM MỚI: Tra cứu thông tin chi tiết từ Slug ---
def get_product_details_by_slugs(slugs: List[str], all_products: List[dict]) -> List[dict]:
    results = []
    for slug in slugs:
        # Tìm sản phẩm trong danh sách gốc khớp với slug
        product = next((p for p in all_products if p.get("slug") == slug), None)

        if product:
            gia_ban = None
            chi_tiet_list = product.get("san_pham_chi_tiet", [])
            if isinstance(chi_tiet_list, list) and chi_tiet_list:
                gia_ban = chi_tiet_list[0].get("gia_ban")

            # Map sang format bạn mong muốn
            item = {
                "id_san_pham": product.get("id") or product.get("id_san_pham"),
                "ten_san_pham": product.get("ten_san_pham"),
                "slug": product.get("slug"),
                "ten_danh_muc": product.get("ten_danh_muc"),
                "ten_thuong_hieu": product.get("ten_thuong_hieu"),
                "ngay_tao": product.get("created_at") or product.get("ngay_tao"),
                "anh_dai_dien": product.get("anh_dai_dien"),
                "gia_ban": gia_ban,
            }
            results.append(item)
    return results

# =========================
# 2. CẤU HÌNH CONTEXT
# =========================

CONTEXT_DATA = f"""
Bạn là nhân viên tư vấn Badminton Shop.
Dữ liệu:
{build_product_context(PRODUCT_DATA_RAW)}

QUY TẮC:
1. Trả về JSON: {{"answer": "...", "products": ["slug1", "slug2"]}}
2. Chỉ lấy slug của sản phẩm phù hợp.
3. KHÔNG dùng Markdown.
4. Không liệt kê tên sản phẩm trong answer.
"""

client = Client(api_key=API_KEY_GEMINI)
chat_history = []

# =========================
# 3. FASTAPI APP
# =========================

app = FastAPI()

class ChatRequest(BaseModel):
    message: str

# Cập nhật Model Response: products là danh sách Object (dict), không phải string nữa
class ChatResponse(BaseModel):
    answer: str
    products: List[dict]

@app.post("/chat", response_model=ChatResponse)
async def chat_api(req: ChatRequest):
    try:
        chat_history.append({"role": "user", "content": req.message})

        formatted_contents = []
        for msg in chat_history:
            role = "user" if msg["role"] == "user" else "model"
            formatted_contents.append(types.Content(role=role, parts=[types.Part(text=msg["content"])]))

        conf = types.GenerateContentConfig(
            system_instruction=CONTEXT_DATA,
            response_mime_type="application/json",
            temperature=0.7
        )

        # Dùng model 1.5 Flash cho ổn định
        response = client.models.generate_content(
            model="gemini-2.5-flash",
            contents=formatted_contents,
            config=conf
        )

        # Parse kết quả từ AI
        try:
            data = json.loads(response.text)
        except:
            data = {"answer": response.text, "products": []}

        # --- BƯỚC QUAN TRỌNG: MAPPING LẠI DỮ LIỆU ---
        # AI trả về list slug -> Python đổi thành list chi tiết
        slug_list = data.get("products", [])
        detailed_products = get_product_details_by_slugs(slug_list, PRODUCT_DATA_RAW)

        chat_history.append({"role": "assistant", "content": data.get("answer", "")})

        return ChatResponse(
            answer=data.get("answer", ""),
            products=detailed_products # Trả về full info
        )

    except Exception as e:
        print(f"❌ Error: {e}")
        return ChatResponse(answer="Hệ thống bận.", products=[])
