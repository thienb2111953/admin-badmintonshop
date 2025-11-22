import sys
import json
import math
import os
import google.generativeai as genai

# ===========================
# 1️⃣ Cấu hình API Key Gemini
# ===========================
try:
    # ⚠️ KHÔNG nên hardcode key — đây chỉ để test nhanh.
    GOOGLE_API_KEY = os.getenv("GOOGLE_API_KEY", "AIzaSyBVmKfeHadaZh8MhZT9sjw6ctX3-6D9gOY")
    if not GOOGLE_API_KEY or GOOGLE_API_KEY == "YOUR_GOOGLE_API_KEY":
        print("❌ Lỗi: Bạn chưa cấu hình GOOGLE_API_KEY.")
        print("Hướng dẫn:")
        print("  Windows: set GOOGLE_API_KEY=KEY_CUA_BAN")
        print("  Linux/Mac: export GOOGLE_API_KEY=KEY_CUA_BAN")
        sys.exit(1)
    genai.configure(api_key=GOOGLE_API_KEY)
except Exception as e:
    print(f"❌ Lỗi cấu hình API Key: {e}")
    sys.exit(1)

# ===========================
# 2️⃣ Đọc dữ liệu sản phẩm
# ===========================
PRODUCT_PATH = os.path.join(os.path.dirname(__file__), "products.json")

try:
    with open(PRODUCT_PATH, "r", encoding="utf-8") as f:
        products = json.load(f)
except FileNotFoundError:
    print(f"❌ Không tìm thấy file: {PRODUCT_PATH}")
    sys.exit(1)
except json.JSONDecodeError:
    print("❌ File 'products.json' không hợp lệ (không phải JSON).")
    sys.exit(1)

# ===========================
# 3️⃣ Nhận câu hỏi từ Laravel
# ===========================
question = sys.argv[1] if len(sys.argv) > 1 else "Tôi muốn mua vợt nhẹ cho người mới chơi"
print(f"🔍 Đang tìm kiếm cho câu hỏi: \"{question}\"")

# ===========================
# 4️⃣ Tạo embedding cho câu hỏi
# ===========================
print("➡️ 1. Đang tạo embedding cho câu hỏi...")
try:
    emb_result = genai.embed_content(
        model="models/embedding-001",
        content=question,
        task_type="RETRIEVAL_QUERY"
    )
    emb = emb_result["embedding"]
except Exception as e:
    print(f"❌ Lỗi khi tạo embedding cho câu hỏi: {e}")
    sys.exit(1)

# ===========================
# 5️⃣ Tạo embedding cho sản phẩm
# ===========================
print(f"➡️ 2. Đang tạo embedding cho {len(products)} sản phẩm...")
product_texts = [f"{p['ten']} - {p['loai']} - {p['phong_cach']} - {p['trinh_do']} - {p['muc_gia']}đ" for p in products]

try:
    product_embeds_result = genai.embed_content(
        model="models/embedding-001",
        content=product_texts,
        task_type="RETRIEVAL_DOCUMENT"
    )
    # API trả về dictionary, có thể là list hoặc dict -> xử lý an toàn
    if isinstance(product_embeds_result, list):
        product_vectors = [p["embedding"] for p in product_embeds_result]
    else:
        product_vectors = product_embeds_result.get("embedding", [])
except Exception as e:
    print(f"❌ Lỗi khi tạo embedding cho sản phẩm: {e}")
    sys.exit(1)

# ===========================
# 6️⃣ Hàm tính cosine similarity
# ===========================
def cosine(a, b):
    dot = sum(x * y for x, y in zip(a, b))
    normA = math.sqrt(sum(x * x for x in a))
    normB = math.sqrt(sum(x * x for x in b))
    if normA == 0 or normB == 0:
        return 0.0
    return dot / (normA * normB)

# ===========================
# 7️⃣ Tính độ tương đồng & chọn top sản phẩm
# ===========================
print("➡️ 3. Đang tính toán độ tương đồng...")
scored = []
for p, vec in zip(products, product_vectors):
    sim = cosine(vec, emb)
    scored.append((sim, p))

scored.sort(reverse=True, key=lambda x: x[0])
top = [x[1] for x in scored[:3]]

# ===========================
# 8️⃣ Gọi Gemini tạo câu trả lời
# ===========================
print("➡️ 4. Đang tạo câu trả lời tư vấn...\n")

prompt = f"Người dùng hỏi: {question}\n\nDưới đây là 3 sản phẩm phù hợp nhất tôi tìm thấy:\n"
for p in top:
    prompt += f"- {p['ten']} (Loại: {p['loai']}, Phong cách: {p['phong_cach']}, Trình độ: {p['trinh_do']}, Giá: {p['muc_gia']}đ)\n"

prompt += "\nHãy đóng vai là chuyên gia tư vấn cầu lông. Hãy giải thích ngắn gọn và thân thiện vì sao sản phẩm phù hợp với nhu cầu người dùng, dùng tiếng Việt tự nhiên."

try:
    model = genai.GenerativeModel(
        model_name="gemini-2.0-flash-exp",  # nhanh & tiết kiệm hơn gemini-1.5
        system_instruction="Bạn là chatbot tư vấn bán hàng chuyên nghiệp, nói tiếng Việt tự nhiên, thân thiện."
    )
    response = model.generate_content(prompt)

    print("--- Tư vấn từ Gemini ---")
    print(response.text.strip())
except Exception as e:
    print(f"❌ Lỗi khi tạo câu trả lời: {e}")
    sys.exit(1)
