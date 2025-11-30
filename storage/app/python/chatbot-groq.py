from fastapi import FastAPI
from pydantic import BaseModel
import psycopg2
from psycopg2.extras import RealDictCursor
from groq import Groq
import json
import os

# =====================================================
# 1. CONFIG
# =====================================================
GROQ_API_KEY = os.getenv("GROQ_API_KEY", "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")
client = Groq(api_key=GROQ_API_KEY)

def db():
    return psycopg2.connect(
        host="localhost",
        user="postgres",
        password="123",
        dbname="badminton_shop",
        cursor_factory=RealDictCursor
    )

app = FastAPI()

# ===============================================
# AI INTENT PARSER → JSON
# ===============================================

INTENT_PROMPT = """
Bạn là hệ thống phân tích nhu cầu mua sắm.
Hãy đọc câu của khách và trả về JSON duy nhất.

JSON format:
{
  "category": "vợt" | "giày" | "quần áo" | null,
  "brand": "Yonex" | "Lining" | "Victor" | null,
  "min_price": number | null,
  "max_price": number | null,
  "attributes": [
    {"name": "...", "value": "..."}
  ]
}

Quy tắc:
- "dưới X triệu" → max_price = X * 1.000.000
- "từ X đến Y triệu" → min_price = X * 1.000.000
- "X triệu" → max_price = X * 1.000.000
- Thương hiệu: Yonex, Lining, Victor, Apacs, Mizuno, ProKennex...
- Trả về duy nhất JSON hợp lệ.
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

    # Extract JSON
    try:
        start = raw.index("{")
        end = raw.rindex("}") + 1
        return json.loads(raw[start:end])
    except:
        return {
            "category": None,
            "brand": None,
            "min_price": None,
            "max_price": None,
            "attributes": []
        }

# ===============================================
# SQL HELPER – MAP THUỘC TÍNH
# ===============================================

def map_attr_value(name, value):
    conn = db()
    cur = conn.cursor()

    cur.execute("""
        SELECT id_thuoc_tinh FROM thuoc_tinh
        WHERE LOWER(ten_thuoc_tinh) = LOWER(%s)
    """, (name,))
    tt = cur.fetchone()

    if not tt:
        return None

    cur.execute("""
        SELECT id_thuoc_tinh_chi_tiet
        FROM thuoc_tinh_chi_tiet
        WHERE id_thuoc_tinh = %s AND LOWER(ten_thuoc_tinh_chi_tiet) = LOWER(%s)
    """, (tt["id_thuoc_tinh"], value))

    row = cur.fetchone()

    cur.close()
    conn.close()
    return row["id_thuoc_tinh_chi_tiet"] if row else None

# ===============================================
# QUERY SẢN PHẨM
# ===============================================

def search_products(filters):
    conn = db()
    cur = conn.cursor()

    sql = """
        SELECT DISTINCT ON (sp.id_san_pham)
            sp.id_san_pham,
            sp.ten_san_pham,
            sp.slug,
            sp.ma_san_pham,
            spct.gia_ban,
            spct.gia_niem_yet,
            (
                SELECT asp.anh_url
                FROM anh_san_pham asp
                WHERE asp.id_san_pham_chi_tiet = spct.id_san_pham_chi_tiet
                ORDER BY asp.thu_tu ASC, asp.id_anh_san_pham ASC
                LIMIT 1
            ) AS anh
        FROM san_pham sp
        LEFT JOIN san_pham_chi_tiet spct
            ON spct.id_san_pham = sp.id_san_pham
        LEFT JOIN danh_muc_thuong_hieu dmth
            ON dmth.id_danh_muc_thuong_hieu = sp.id_danh_muc_thuong_hieu
        LEFT JOIN thuong_hieu th
            ON th.id_thuong_hieu = dmth.id_thuong_hieu
        WHERE sp.trang_thai = 'Đang sản xuất'
    """

    params = []

    # Category
    if filters.get("category"):
        cur.execute("""
            SELECT id_danh_muc FROM danh_muc
            WHERE LOWER(ten_danh_muc) LIKE %s
        """, (f"%{filters['category'].lower()}%",))
        cat = cur.fetchone()

        if cat:
            sql += " AND dmth.id_danh_muc = %s"
            params.append(cat["id_danh_muc"])

    # Brand
    if filters.get("brand"):
        sql += " AND LOWER(th.ten_thuong_hieu) = LOWER(%s)"
        params.append(filters["brand"])

    # Price
    if filters.get("min_price"):
        sql += " AND spct.gia_ban >= %s"
        params.append(filters["min_price"])

    if filters.get("max_price"):
        sql += " AND spct.gia_ban <= %s"
        params.append(filters["max_price"])

    # Attributes
    for attr in filters.get("attributes", []):
        attr_id = map_attr_value(attr["name"], attr["value"])
        if not attr_id:
            continue

        sql += """
            AND EXISTS (
                SELECT 1 FROM san_pham_thuoc_tinh spt
                WHERE spt.id_san_pham = sp.id_san_pham
                AND spt.id_thuoc_tinh_chi_tiet = %s
            )
        """
        params.append(attr_id)

    sql += " ORDER BY sp.id_san_pham, spct.gia_ban ASC LIMIT 12"

    cur.execute(sql, params)
    rows = cur.fetchall()

    cur.close()
    conn.close()
    return rows

# ===============================================
# FASTAPI REQUEST MODEL
# ===============================================

class ChatRequest(BaseModel):
    message: str

# ===============================================
# API ROUTE
# ===============================================

@app.post("/api/chatbot/search")
def api_search(req: ChatRequest):
    print(req.message)
    filters = ai_intent(req.message)
    products = search_products(filters)

    return {
        "filters": filters,
        "products": products
    }

