import sys, json, math
from openai import OpenAI

client = OpenAI(api_key="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")

# Đọc dữ liệu sản phẩm
with open("storage/app/python/products.json", "r", encoding="utf-8") as f:
    products = json.load(f)

# Câu hỏi người dùng
question = sys.argv[1] if len(sys.argv) > 1 else "Tôi muốn mua vợt nhẹ cho người mới chơi"

# 1️⃣ Tạo embedding cho câu hỏi
emb = client.embeddings.create(
    model="text-embedding-3-small",
    input=question
).data[0].embedding

# 2️⃣ Tạo embedding cho từng sản phẩm (cache sẵn nếu muốn)
def get_vector(text):
    return client.embeddings.create(
        model="text-embedding-3-small",
        input=text
    ).data[0].embedding

def cosine(a, b):
    dot = sum(x * y for x, y in zip(a, b))
    normA = math.sqrt(sum(x * x for x in a))
    normB = math.sqrt(sum(x * x for x in b))
    return dot / (normA * normB)

scored = []
for p in products:
    text = f"{p['ten']} - {p['loai']} - {p['phong_cach']} - {p['trinh_do']} - {p['muc_gia']}đ"
    vec = get_vector(text)
    sim = cosine(vec, emb)
    scored.append((sim, p))

scored.sort(reverse=True, key=lambda x: x[0])
top = [x[1] for x in scored[:3]]

# 3️⃣ Gửi danh sách phù hợp vào GPT để tạo câu trả lời
prompt = f"Người dùng hỏi: {question}\nDưới đây là 3 sản phẩm phù hợp:\n"
for p in top:
    prompt += f"- {p['ten']} ({p['loai']}, {p['phong_cach']}, {p['muc_gia']}đ)\n"
prompt += "\nHãy tư vấn thân thiện bằng tiếng Việt."

response = client.chat.completions.create(
    model="gpt-4o-mini",
    messages=[
        {"role": "system", "content": "Bạn là chatbot tư vấn bán hàng thông minh, nói tiếng Việt."},
        {"role": "user", "content": prompt}
    ]
)

print(response.choices[0].message.content)
