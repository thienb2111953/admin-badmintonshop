import sys, json, math, os
from groq import Groq

# ==============================
# 1️⃣ Cấu hình API Key
# ==============================
GROQ_API_KEY = os.getenv("GROQ_API_KEY", "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")
if not GROQ_API_KEY or GROQ_API_KEY.startswith("THAY_KEY"):
    print("❌ Bạn chưa cấu hình GROQ_API_KEY.")
    print("Hướng dẫn:")
    print("  Windows: set GROQ_API_KEY=KEY_CUA_BAN")
    print("  Linux/Mac: export GROQ_API_KEY=KEY_CUA_BAN")
    sys.exit(1)

client = Groq(api_key=GROQ_API_KEY)

# ==============================
# 2️⃣ Đọc dữ liệu sản phẩm
# ==============================
PRODUCT_PATH = os.path.join(os.path.dirname(__file__), "products.json")

try:
    with open(PRODUCT_PATH, "r", encoding="utf-8") as f:
        products = json.load(f)
except Exception as e:
    print(f"❌ Lỗi đọc dữ liệu sản phẩm: {e}")
    sys.exit(1)

# ==============================
# 3️⃣ Nhận câu hỏi người dùng
# ==============================
question = sys.argv[1] if len(sys.argv) > 1 else "Tôi muốn mua vợt nhẹ cho người mới chơi"

# ==============================
# 4️⃣ Tính độ tương đồng đơn giản bằng từ khóa
# ==============================
def score_product(q, p):
    q = q.lower()
    score = 0
    if p["loai"].lower() in q: score += 2
    if p["phong_cach"].lower() in q: score += 2
    if p["trinh_do"].lower() in q: score += 1
    if "vợt" in q and "vợt" in p["loai"].lower(): score += 1
    if "giày" in q and "giày" in p["loai"].lower(): score += 1
    return score

scored = [(score_product(question, p), p) for p in products]
scored.sort(reverse=True, key=lambda x: x[0])
top = [x[1] for x in scored[:3]]

# ==============================
# 5️⃣ Gọi model Groq để sinh câu trả lời
# ==============================
prompt = f"Người dùng hỏi: {question}\n\nDưới đây là các sản phẩm phù hợp:\n"
for p in top:
    prompt += f"- {p['ten']} (Loại: {p['loai']}, Phong cách: {p['phong_cach']}, Trình độ: {p['trinh_do']}, Giá: {p['muc_gia']}đ)\n"

prompt += "\nHãy đóng vai là chuyên gia tư vấn cầu lông, tư vấn ngắn gọn, tự nhiên và thân thiện bằng tiếng Việt."

try:
    response = client.chat.completions.create(
        model="llama-3.3-70b-versatile",
        messages=[
            {"role": "system", "content": "Bạn là chatbot bán hàng thông minh, nói tiếng Việt thân thiện."},
            {"role": "user", "content": prompt}
        ]
    )
    print("\n--- Tư vấn từ Groq ---")
    print(response.choices[0].message.content.strip())

except Exception as e:
    print(f"❌ Lỗi khi gọi API Groq: {e}")
    sys.exit(1)
