import psycopg2
from psycopg2.extras import RealDictCursor
from conn import get_db_connection, PATH_PROJECT_STORAGE
import json
from decimal import Decimal

def export_products():
    conn = get_db_connection()
    cur = conn.cursor(cursor_factory=RealDictCursor)

    # 1) Query toàn bộ sản phẩm
    cur.execute("""
        SELECT
            sp.id_san_pham,
            sp.ten_san_pham,
            sp.slug,
            sp.created_at,
            dm.ten_danh_muc,
            th.ten_thuong_hieu
        FROM san_pham sp
        LEFT JOIN danh_muc_thuong_hieu dmth ON dmth.id_danh_muc_thuong_hieu = sp.id_danh_muc_thuong_hieu
        LEFT JOIN danh_muc dm ON dm.id_danh_muc = dmth.id_danh_muc
        LEFT JOIN thuong_hieu th ON th.id_thuong_hieu = dmth.id_thuong_hieu
        WHERE sp.trang_thai = 'Đang sản xuất'
        ORDER BY sp.id_san_pham
    """)

    san_pham_list = cur.fetchall()
    results = []

    for sp in san_pham_list:
        sp_id = sp["id_san_pham"]

        # 2) Lấy danh sách biến thể sản phẩm
        cur.execute("""
            SELECT
                spct.id_san_pham_chi_tiet,
                spct.gia_ban,
                m.ten_mau,
                kt.ten_kich_thuoc,
                spct.so_luong_ton
            FROM san_pham_chi_tiet spct
            LEFT JOIN mau m ON m.id_mau = spct.id_mau
            LEFT JOIN kich_thuoc kt ON kt.id_kich_thuoc = spct.id_kich_thuoc
            WHERE spct.id_san_pham = %s
            ORDER BY spct.id_san_pham_chi_tiet
        """, (sp_id,))
        chi_tiet_rows = cur.fetchall()

        chi_tiet = []
        for ct in chi_tiet_rows:
            chi_tiet.append({
                "id_san_pham_chi_tiet": ct["id_san_pham_chi_tiet"],
                "gia_ban": int(ct["gia_ban"]) if isinstance(ct["gia_ban"], Decimal) else ct["gia_ban"],
                "ten_mau": ct["ten_mau"],
                "ten_kich_thuoc": ct["ten_kich_thuoc"],
                "so_luong_ton": int(ct["so_luong_ton"])
            })

        # 3) Lấy thuộc tính sản phẩm
        cur.execute("""
            SELECT
                tt.ten_thuoc_tinh,
                ttct.ten_thuoc_tinh_chi_tiet
            FROM san_pham_thuoc_tinh spt
            LEFT JOIN thuoc_tinh_chi_tiet ttct ON ttct.id_thuoc_tinh_chi_tiet = spt.id_thuoc_tinh_chi_tiet
            LEFT JOIN thuoc_tinh tt ON tt.id_thuoc_tinh = ttct.id_thuoc_tinh
            WHERE spt.id_san_pham = %s
        """, (sp_id,))
        attrs = cur.fetchall()
        thuoc_tinh_map = {a["ten_thuoc_tinh"]: a["ten_thuoc_tinh_chi_tiet"] for a in attrs}

        # 4) Thêm vào kết quả
        results.append({
            "id_san_pham": sp["id_san_pham"],
            "ten_san_pham": sp["ten_san_pham"],
            "slug": sp["slug"],
            "ten_danh_muc": sp["ten_danh_muc"],
            "ten_thuong_hieu": sp["ten_thuong_hieu"],
            "ngay_tao": sp["created_at"].strftime("%Y-%m-%d"),
            "san_pham_chi_tiet": chi_tiet,
            "thuoc_tinh": thuoc_tinh_map
        })

    cur.close()
    conn.close()

    OUTPUT_PATH = PATH_PROJECT_STORAGE / "python" / "san_pham.json"

    with open(OUTPUT_PATH, "w", encoding="utf-8") as f:
        json.dump(results, f, indent=4, ensure_ascii=False)

    print(f"✅ Đã export thành công → {OUTPUT_PATH}")


if __name__ == "__main__":
    export_products()
