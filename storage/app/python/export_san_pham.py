import psycopg2
from psycopg2.extras import RealDictCursor
from conn import get_db_connection, PATH_PROJECT_STORAGE, APP_URL_PATH
import json
from decimal import Decimal
from pathlib import Path
import re


# ============================
# Utils
# ============================

def slugify_filename(s):
    s = str(s).strip()
    return re.sub(r'[\\/*?:"<>|]', "", s)


# ============================
# Main Export Function
# ============================

def export_products_by_category():
    conn = get_db_connection()
    cur = conn.cursor(cursor_factory=RealDictCursor)

    print("⏳ Đang lấy dữ liệu từ database...")

    # 1) Lấy thông tin sản phẩm
    cur.execute("""
        SELECT
            sp.id_san_pham,
            sp.ten_san_pham,
            sp.slug,
            sp.created_at,
            dm.ten_danh_muc,
            dm.slug AS slug_danh_muc,
            th.ten_thuong_hieu
        FROM san_pham sp
        LEFT JOIN danh_muc_thuong_hieu dmth
            ON dmth.id_danh_muc_thuong_hieu = sp.id_danh_muc_thuong_hieu
        LEFT JOIN danh_muc dm
            ON dm.id_danh_muc = dmth.id_danh_muc
        LEFT JOIN thuong_hieu th
            ON th.id_thuong_hieu = dmth.id_thuong_hieu
        WHERE sp.trang_thai = 'Đang sản xuất'
        ORDER BY sp.id_san_pham
    """)

    san_pham_list = cur.fetchall()
    total_products = len(san_pham_list)
    print(f"📦 Tìm thấy {total_products} sản phẩm. Bắt đầu xử lý...")

    grouped_results = {}

    # ============================
    # Xử lý từng sản phẩm
    # ============================
    for idx, sp in enumerate(san_pham_list):
        sp_id = sp["id_san_pham"]

        slug_danh_muc = sp["slug_danh_muc"] or "khac"
        ten_danh_muc = sp["ten_danh_muc"] or "Khác"

        # ===========================================
        # 2) Lấy biến thể nhưng CHỈ dùng để gom màu, size
        # ===========================================
        cur.execute("""
            SELECT
                spct.gia_ban,
                m.ten_mau,
                kt.ten_kich_thuoc,
                asp.anh_url
            FROM san_pham_chi_tiet spct
            LEFT JOIN anh_san_pham asp
                ON asp.id_san_pham_chi_tiet = spct.id_san_pham_chi_tiet AND asp.thu_tu = 1
            LEFT JOIN mau m ON m.id_mau = spct.id_mau
            LEFT JOIN kich_thuoc kt ON kt.id_kich_thuoc = spct.id_kich_thuoc
            WHERE spct.id_san_pham = %s
            ORDER BY spct.id_san_pham_chi_tiet
        """, (sp_id,))
        ct_rows = cur.fetchall()

        ds_mau = []
        ds_kich_thuoc = []
        anh_dai_dien = None
        gia_ban_min = None

        for ct in ct_rows:
            # Giá bán nhỏ nhất
            g = int(ct["gia_ban"]) if isinstance(ct["gia_ban"], Decimal) else ct["gia_ban"]
            if gia_ban_min is None or g < gia_ban_min:
                gia_ban_min = g

            # Màu
            if ct["ten_mau"] and ct["ten_mau"] not in ds_mau:
                ds_mau.append(ct["ten_mau"])

            # Kích thước
            if ct["ten_kich_thuoc"] and ct["ten_kich_thuoc"] not in ds_kich_thuoc:
                ds_kich_thuoc.append(ct["ten_kich_thuoc"])

            # Ảnh đại diện
            if not anh_dai_dien and ct.get("anh_url"):
                anh_dai_dien = f"{APP_URL_PATH}/{ct['anh_url']}"

        # Nếu không có giá thì để 0
        gia_ban_min = gia_ban_min or 0

        # ===========================================
        # 3) Thuộc tính sản phẩm
        # ===========================================
        cur.execute("""
            SELECT
                tt.ten_thuoc_tinh,
                ttct.ten_thuoc_tinh_chi_tiet
            FROM san_pham_thuoc_tinh spt
            LEFT JOIN thuoc_tinh_chi_tiet ttct
                ON ttct.id_thuoc_tinh_chi_tiet = spt.id_thuoc_tinh_chi_tiet
            LEFT JOIN thuoc_tinh tt
                ON tt.id_thuoc_tinh = ttct.id_thuoc_tinh
            WHERE spt.id_san_pham = %s
        """, (sp_id,))
        attrs = cur.fetchall()

        thuoc_tinh = {
            a["ten_thuoc_tinh"]: a["ten_thuoc_tinh_chi_tiet"]
            for a in attrs
        }

        # ===========================================
        # 4) Build object
        # ===========================================
        product_obj = {
            "id_san_pham": sp["id_san_pham"],
            "ten_san_pham": sp["ten_san_pham"],
            "slug": sp["slug"],
            "ten_danh_muc": ten_danh_muc,
            "ten_thuong_hieu": sp["ten_thuong_hieu"],
            "ngay_tao": sp["created_at"].strftime("%Y-%m-%d") if sp["created_at"] else None,
            "anh_dai_dien": anh_dai_dien,
            "gia_ban": gia_ban_min,
            "ds_mau": ds_mau,
            "ds_kich_thuoc": ds_kich_thuoc,
            "thuoc_tinh": thuoc_tinh
        }

        # Nhóm theo slug danh mục
        grouped_results.setdefault(slug_danh_muc, []).append(product_obj)

        if (idx + 1) % 50 == 0:
            print(f"   ... Đã xử lý {idx + 1}/{total_products} sản phẩm")

    cur.close()
    conn.close()

    # ============================
    # Ghi file JSON
    # ============================

    output_dir = PATH_PROJECT_STORAGE / "python" / "danh-muc"
    output_dir.mkdir(parents=True, exist_ok=True)

    print("\n💾 Đang ghi file...")

    for slug_dm, products in grouped_results.items():
        safe_name = slugify_filename(slug_dm)
        file_path = output_dir / f"{safe_name}.json"

        try:
            with open(file_path, "w", encoding="utf-8") as f:
                json.dump(products, f, indent=4, ensure_ascii=False)
            print(f"   ✅ {safe_name}.json: {len(products)} sản phẩm")
        except Exception as e:
            print(f"   ❌ Lỗi ghi file {safe_name}.json: {e}")

    print("\n🎉 Hoàn tất export!")


# ============================
# Run Script
# ============================

if __name__ == "__main__":
    export_products_by_category()
