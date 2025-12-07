from fastapi import FastAPI
from pydantic import BaseModel
import json
from pathlib import Path
import google.generativeai as genai
from typing import List, Dict, Any
import traceback
from rapidfuzz import process, fuzz
from fastapi.middleware.cors import CORSMiddleware

# =========================
# 1. CẤU HÌNH (CONFIGURATION)
# =========================
try:
    from conn import API_KEY_GEMINI
    API_KEY = API_KEY_GEMINI
except ImportError:
    API_KEY = "YOUR_API_KEY_HERE"

genai.configure(api_key=API_KEY)

# Đường dẫn đến thư mục chứa file JSON
BASE_DIR = Path(__file__).resolve().parent
CATEGORY_DIR = BASE_DIR / "danh-muc"

# Định nghĩa danh mục và từ khóa fallback
CATEGORY_MAP = {
    "vot-cau-long.json": ["vợt", "racket", "cây", "vợt cầu lông"],
    "giay-cau-long.json": ["giày", "shoes", "dép", "giày cầu lông"],
    "ao-cau-long.json": ["áo", "shirt", "áo cầu lông"],
    "quan-cau-long.json": ["quần", "short", "quần cầu lông"],
    "balo-cau-long.json": ["balo", "ba lô", "bag", "balo cầu lông"],
    "tui-vot-cau-long.json": ["túi", "bao vợt", "túi vợt cầu long", "túi vợt"],
    "vay-cau-long.json": ["váy", "skirt", "váy cầu lông"]
}

SESSIONS: Dict[str, Dict[str, Any]] = {}

# =========================
# 2. XỬ LÝ DỮ LIỆU ĐỘNG
# =========================

def load_products_from_file(filename: str):
    """Tải sản phẩm từ một file danh mục cụ thể."""
    file_path = CATEGORY_DIR / filename
    if not file_path.exists():
        print(f"❌ File not found: {file_path}")
        return []

    try:
        with open(file_path, "r", encoding="utf-8") as f:
            data = json.load(f)
            # Tiền xử lý text tìm kiếm
            for p in data:
                base_str = f"{p.get('ten_san_pham', '')} {p.get('ten_thuong_hieu', '')}"
                attrs = p.get('thuoc_tinh', {})
                attr_str = " ".join([str(v) for v in attrs.values()]) if isinstance(attrs, dict) else ""

                details = p.get('san_pham_chi_tiet', [])
                price_list = []
                if details:
                    for d in details:
                        gia = d.get('gia_ban')
                        if gia:
                            price_list.append(str(gia))
                price_str = " ".join(set(price_list))

                p['search_text'] = f"{base_str} {attr_str} {price_str}".lower()
            return data
    except Exception as e:
        print(f"❌ Error loading {filename}: {e}")
        return []

def search_products_in_list(query: str, product_list: list, limit=100):
    """Tìm kiếm trong danh sách sản phẩm đã tải."""
    if not product_list: return []

    # Tạo index tìm kiếm tạm thời
    search_index = {p['slug']: p['search_text'] for p in product_list}

    results = process.extract(
        query.lower(),
        search_index,
        limit=limit,
        scorer=fuzz.token_set_ratio
    )
    # Lấy các sản phẩm có độ khớp > 25
    slugs = [res[2] for res in results if res[1] > 25]
    return [p for p in product_list if p['slug'] in slugs]

def build_product_context(products):
    if not products: return "Không tìm thấy sản phẩm thô phù hợp."
    lines = []
    for p in products:
        details = p.get("san_pham_chi_tiet", [])
        price_str = "0"
        if details:
            prices = [d.get("gia_ban", 0) for d in details]
            if prices:
                min_p = min(prices)
                price_str = f"{min_p}"
        lines.append(f"- ID: {p['slug']} | Tên: {p['ten_san_pham']} | Hãng: {p['ten_thuong_hieu']} | Giá tham khảo: {price_str}")
    return "\n".join(lines)

# =========================
# 3. ĐIỀU HƯỚNG THÔNG MINH (ROUTER)
# =========================

def detect_category_and_rewrite(history: List[dict], current_msg: str):
    """
    Sử dụng AI để xác định:
    1. File JSON nào cần dùng.
    2. Viết lại câu query để search (bỏ các từ chỉ giá, tính chất phức tạp).
    """
    history_text = ""
    if history:
        recent = history[-4:]
        history_text = "\n".join([f"{h['role']}: {h['text']}" for h in recent])

    files_list = ", ".join(CATEGORY_MAP.keys())

    # PROMPT TIẾNG VIỆT CHO ROUTER
    prompt = f"""
    Bạn là trợ lý điều hướng cho một Cửa Hàng Cầu Lông.

    Danh sách file dữ liệu hiện có: [{files_list}]

    Lịch sử chat:
    {history_text}
    Tin nhắn hiện tại: "{current_msg}"

    NHIỆM VỤ:
    1. Xác định 01 file JSON phù hợp nhất với nhu cầu khách hàng. Nếu không liên quan đến sản phẩm, trả về "None".
    2. Viết lại câu tìm kiếm (query):
       - Bỏ các từ chỉ giá cả (ví dụ: "dưới 1 triệu", "rẻ", "đắt").
       - Bỏ các tính từ quá phức tạp.
       - Chỉ giữ lại: Tên thương hiệu, Tên loại sản phẩm, Mã sản phẩm (nếu có).

    ĐỊNH DẠNG OUTPUT (CHỈ JSON):
    {{
        "file": "ten_file.json",
        "query": "từ khóa đã lọc"
    }}

    Ví dụ:
    - Input: "Tìm vợt Yonex giá rẻ dưới 1 củ" -> {{"file": "vot-cau-long.json", "query": "vợt Yonex"}}
    - Input: "Có giày cầu lông nào êm không" -> {{"file": "giay-cau-long.json", "query": "giày cầu lông"}}
    """

    try:
        # Lưu ý: Đảm bảo model name đúng với cấu hình của bạn (gemini-1.5-flash hoặc gemini-2.5-flash)
        model = genai.GenerativeModel('gemini-2.5-flash')
        res = model.generate_content(prompt, generation_config={"response_mime_type": "application/json"})
        return json.loads(res.text)
    except Exception as e:
        print(f"Router Error: {e}")
        # Fallback thủ công
        msg_lower = current_msg.lower()
        for fname, keywords in CATEGORY_MAP.items():
            if any(k in msg_lower for k in keywords):
                return {"file": fname, "query": current_msg}
        return {"file": "vot-cau-long.json", "query": current_msg}

# =========================
# 4. API ENDPOINT
# =========================
app = FastAPI()

origins = [
    "http://localhost:3000",
    "http://127.0.0.1:3000",
]

# 3. Thêm Middleware vào ứng dụng
app.add_middleware(
    CORSMiddleware,
    allow_origins=origins,       # Cho phép port 3000 gọi vào
    allow_credentials=True,      # Cho phép gửi cookie/token (nếu có)
    allow_methods=["*"],         # Cho phép tất cả các method: POST, GET, PUT, DELETE...
    allow_headers=["*"],         # Cho phép tất cả các header
)

class ChatRequest(BaseModel):
    session_id: str = "default"
    message: str

@app.post("/chat")
async def chat_api(req: ChatRequest):
    try:
        sid = req.session_id
        user_msg = req.message.strip()

        if sid not in SESSIONS:
            SESSIONS[sid] = {"chat_history": []}
        session = SESSIONS[sid]

        # BƯỚC 1: Điều hướng & Lọc từ khóa
        router_result = detect_category_and_rewrite(session["chat_history"], user_msg)
        target_file = router_result.get("file")
        refined_query = router_result.get("query")

        print(f"📂 Target File: {target_file} | 🔍 Query: {refined_query}")

        final_products = []
        answer = ""

        # BƯỚC 2: Load dữ liệu & Tìm kiếm sơ bộ
        if target_file and target_file != "None":
            category_data = load_products_from_file(target_file)
            found_products = search_products_in_list(refined_query, category_data, limit=50)
            product_context = build_product_context(found_products)

            chat_history_txt = "\n".join([f"{h['role']}: {h['text']}" for h in session["chat_history"][-4:]])

            # BƯỚC 3: Tạo câu trả lời cuối cùng (Context Tiếng Việt)
            system_prompt = f"""
            Bạn là Nhân viên tư vấn bán hàng Cầu Lông chuyên nghiệp, thân thiện.

            Ngữ cảnh:
            - Khách hàng hỏi: "{user_msg}"
            - Hệ thống đang tìm trong file: "{target_file}"

            Lịch sử trò chuyện:
            {chat_history_txt}

            Dữ liệu sản phẩm tìm thấy (Raw Data):
            ---
            {product_context}
            ---

            NHIỆM VỤ CỦA BẠN:
            1. Phân tích yêu cầu cụ thể của khách (về giá, màu sắc, trình độ chơi...) để chọn ra các ID sản phẩm phù hợp nhất từ danh sách trên.
            2. Nếu danh sách trống hoặc không có gì phù hợp, hãy xin lỗi và gợi ý khách tìm từ khóa khác.
            3. Trả lời bằng tiếng Việt tự nhiên, ngắn gọn.

            QUY TẮC QUAN TRỌNG VỀ OUTPUT (JSON):
            - Field "products": Chứa danh sách các `slug` (ID) của sản phẩm phù hợp.
            - Field "answer":
                + Chỉ đưa ra lời dẫn, nhận xét chung hoặc lời khuyên.
                + VÍ DỤ ĐÚNG: "Dạ, bên em có một số mẫu vợt Yonex phù hợp với yêu cầu của anh ạ, mời anh xem bên dưới."
                + VÍ DỤ ĐÚNG: "Với tầm giá đó thì anh có thể tham khảo các mẫu giày này, đi rất êm chân."
                + TUYỆT ĐỐI KHÔNG liệt kê tên sản phẩm, KHÔNG gạch đầu dòng danh sách trong phần text này (vì giao diện website sẽ hiển thị thẻ sản phẩm riêng dựa trên list "products").

            OUTPUT JSON FORMAT: {{ "answer": "...", "products": ["slug1", "slug2"] }}
            """

            model = genai.GenerativeModel('gemini-2.5-flash')
            response = model.generate_content(system_prompt, generation_config={"response_mime_type": "application/json"})

            data = json.loads(response.text)

            # Map slugs ngược lại thành object sản phẩm đầy đủ
            slugs = data.get("products", [])
            final_products = [p for p in category_data if p['slug'] in slugs]
            answer = data.get("answer")

        else:
            answer = "Xin lỗi, mình chưa hiểu rõ bạn muốn tìm sản phẩm nào (vợt, giày, quần áo...). Bạn có thể nói rõ hơn chút được không ạ?"

        # Cập nhật lịch sử
        session["chat_history"].append({"role": "user", "text": user_msg})
        session["chat_history"].append({"role": "model", "text": answer})

        return {
            "answer": answer,
            "products": final_products
        }

    except Exception as e:
        print(f"❌ ERROR: {traceback.format_exc()}")
        return {"answer": "Hệ thống đang gặp chút sự cố, bạn thử lại sau nhé!", "products": []}
