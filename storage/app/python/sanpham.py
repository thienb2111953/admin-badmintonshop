import psycopg2
import requests
import os
from bs4 import BeautifulSoup
from conn import get_db_connection, PATH_PROJECT_STORAGE
from func import to_slug
from func import parse_price
from func import get_product_info_from_shopvnb
from func import get_variant_options_from_shopvnb
from func import random_date_2025
from func import natural_sort_key

from slugify import slugify
import random
from datetime import date, timedelta
import re
import uuid
import datetime
import shutil
import random


def getDanhMuc(cursor):
    url = "https://shopvnb.com/"
    headers = {"User-Agent": "Mozilla/5.0"}
    response = requests.get(url, headers=headers)
    soup = BeautifulSoup(response.text, 'html.parser')

    # Xóa dữ liệu cũ
    cursor.execute("DELETE FROM danh_muc")

    titles = soup.find_all('a', class_='hmega')

    for title in titles:
        name = title.text.strip()

        # Kiểm tra chỉ lấy danh mục liên quan cầu lông
        keywords = ['cầu lông']  # có thể thêm các từ khác
        if any(keyword.lower() in name.lower() for keyword in keywords):
            slug = to_slug(name)
            print(f"Đang thêm: {name} → {slug}")
            cursor.execute("""
                           INSERT INTO danh_muc (ten_danh_muc, slug)
                           VALUES (%s, %s)
                           """, (name, slug))


def getThuongHieu(cursor, slug_category):
    url = f"https://shopvnb.com/{slug_category}.html"
    headers = {"User-Agent": "Mozilla/5.0"}
    response = requests.get(url, headers=headers)
    soup = BeautifulSoup(response.text, 'html.parser')

    labels = soup.select("ul.filter-vendor label")
    unique_label = set(label.text.strip() for label in labels)

    for label in unique_label:
        # Kiểm tra xem thương hiệu đã tồn tại chưa
        cursor.execute("""
                       SELECT 1 FROM thuong_hieu WHERE ten_thuong_hieu = %s
                       """, (label,))
        exists = cursor.fetchone()

        if not exists:
            print(f"Đang thêm: {label}")
            cursor.execute("""
                           INSERT INTO thuong_hieu(ten_thuong_hieu)
                           VALUES (%s)
                           """, (label,))
        else:
            print(f"Đã tồn tại: {label}")


def createDanhMucThuongHieu(cursor):
    headers = {"User-Agent": "Mozilla/5.0"}
    base_url = "https://shopvnb.com/vot-cau-long.html"

    # Gửi request đến trang chính
    response = requests.get(base_url, headers=headers)
    soup = BeautifulSoup(response.text, "html.parser")

    # Duyệt qua tất cả các thẻ ul.level1
    ul_list = soup.select("ul.level1")

    for ul in ul_list:
        # Lấy tất cả thẻ li.level2 > a
        li_tags = ul.select("li.level2 a")

        for a_tag in li_tags:
            ten_danh_muc_thuong_hieu = a_tag.text.strip()
            if not ten_danh_muc_thuong_hieu or "xem thêm" in ten_danh_muc_thuong_hieu.lower():
                continue  # Bỏ qua các mục không hợp lệ

            print(f"Đang xử lý: {ten_danh_muc_thuong_hieu}")

            # Tách tên: phần đầu là danh mục, phần cuối là thương hiệu
            parts = ten_danh_muc_thuong_hieu.split()
            if len(parts) < 3:
                print(f"⚠️ Không tách được: {ten_danh_muc_thuong_hieu}")
                continue

            # Xác định phần danh mục (tất cả trừ từ cuối)
            ten_danh_muc = " ".join(parts[:-1]).strip()
            ten_thuong_hieu = parts[-1].strip()

            # Truy vấn id_danh_muc từ DB
            cursor.execute(
                "SELECT id_danh_muc FROM danh_muc WHERE LOWER(ten_danh_muc) = %s",
                (ten_danh_muc.lower(),)
            )
            result_danh_muc = cursor.fetchone()
            id_danh_muc = result_danh_muc[0] if result_danh_muc else None

            # Truy vấn id_thuong_hieu từ DB
            cursor.execute(
                "SELECT id_thuong_hieu FROM thuong_hieu WHERE LOWER(ten_thuong_hieu) = %s",
                (ten_thuong_hieu.lower(),)
            )
            result_th = cursor.fetchone()
            id_thuong_hieu = result_th[0] if result_th else None

            if not id_danh_muc or not id_thuong_hieu:
                print(f"❌ Không tìm thấy ID cho '{ten_danh_muc_thuong_hieu}'")
                continue

            # Tạo slug
            slug = to_slug(ten_danh_muc_thuong_hieu)

            # Thêm vào bảng danh_muc_thuong_hieu
            try:
                cursor.execute("""
                    INSERT INTO danh_muc_thuong_hieu
                    (ten_danh_muc_thuong_hieu, slug, id_thuong_hieu, id_danh_muc)
                    VALUES (%s, %s, %s, %s)
                """, (
                    ten_danh_muc_thuong_hieu,
                    slug,
                    id_thuong_hieu,
                    id_danh_muc
                ))
                print(slug)
                print(id_thuong_hieu)
                print(id_danh_muc)
                print(f"✅ Đã thêm: {ten_danh_muc_thuong_hieu}")
            except Exception as e:
                print(f"⚠️ Lỗi khi thêm '{ten_danh_muc_thuong_hieu}': {e}")


def createSanPham(cursor):
    cursor.execute("TRUNCATE san_pham CASCADE")

    headers = {"User-Agent": "Mozilla/5.0"}

    # 1) Lấy toàn bộ danh_muc_thuong_hieu + slug danh mục & slug thương hiệu
    cursor.execute("""
        SELECT
            dmth.id_danh_muc_thuong_hieu,
            dm.id_danh_muc,
            th.id_thuong_hieu,
            dm.ten_danh_muc,
            th.ten_thuong_hieu,
            dmth.slug as dmth_slug
        FROM danh_muc_thuong_hieu dmth
        JOIN danh_muc dm ON dm.id_danh_muc = dmth.id_danh_muc
        JOIN thuong_hieu th ON th.id_thuong_hieu = dmth.id_thuong_hieu
        ORDER BY dm.ten_danh_muc, th.ten_thuong_hieu
    """)

    dmth_list = cursor.fetchall()

    print(f"🔍 Tìm thấy {len(dmth_list)} danh_muc_thuong_hieu cần crawl")

    # 2) Lặp từng dòng
    for row in dmth_list:
        id_dmth = row[0]
        id_danh_muc = row[1]
        id_thuong_hieu = row[2]
        ten_danh_muc = row[3]
        ten_thuong_hieu = row[4]
        dmth_slug = row[5]

        url = f"https://shopvnb.com/{dmth_slug}.html"
        print(f"\n🌐 Crawl URL: {url}")

        res = requests.get(url, headers=headers)
        soup = BeautifulSoup(res.text, "html.parser")

        # danh sách sản phẩm
        product_tags = soup.select("span.product-name a")
        if not product_tags:
            print("❌ Không tìm thấy sản phẩm nào.")
            continue

        print(f"👉 {len(product_tags)} sản phẩm")

        # 3) Lặp từng sản phẩm
        for index, a_tag in enumerate(product_tags, start=1):
            ten_san_pham = a_tag.text.strip()
            if not ten_san_pham:
                continue

            slug = slugify(ten_san_pham)

            # Tạo mã sản phẩm theo thương hiệu + danh mục + index
            ma_san_pham = f"BMS{id_thuong_hieu}{id_danh_muc}{str(index).zfill(3)}"

            try:
                cursor.execute("""
                    INSERT INTO san_pham (ma_san_pham, ten_san_pham, slug, id_danh_muc_thuong_hieu)
                    VALUES (%s, %s, %s, %s)
                """, (ma_san_pham, ten_san_pham, slug, id_dmth))

                print(f"   ✅ {ten_san_pham}")

            except Exception as e:
                print(f"   ⚠️ Lỗi khi thêm '{ten_san_pham}': {e}")


def createSanPhamChiTiet(cursor):
    cursor.execute("TRUNCATE san_pham_chi_tiet CASCADE")
    cursor.execute("TRUNCATE nhap_hang CASCADE")
    cursor.execute("TRUNCATE san_pham_thuoc_tinh CASCADE")

    # Lấy danh sách sản phẩm
    cursor.execute("SELECT id_san_pham, ten_san_pham, ma_san_pham, slug FROM san_pham")
    san_phams = cursor.fetchall()

    if not san_phams:
        print("❌ Không có sản phẩm!")
        return

    # Lấy danh sách màu DB
    cursor.execute("SELECT ten_mau FROM mau")
    all_db_colors = [row[0] for row in cursor.fetchall()]

    # Lấy danh sách size DB
    cursor.execute("SELECT ten_kich_thuoc FROM kich_thuoc")
    all_db_sizes = [row[0] for row in cursor.fetchall()]

    for sp in san_phams:
        id_san_pham, ten_san_pham, ma_san_pham, slug = sp

        # 1) Lấy giá + thuộc tính
        info = get_product_info_from_shopvnb(slug)
        gia_ban = info.get("gia_ban") or 0
        gia_niem_yet = info.get("gia_niem_yet") or 0
        attributes = info.get("attributes", [])

        # 2) Lấy màu + size thật từ web
        variants = get_variant_options_from_shopvnb(slug)
        color_names = variants["colors"]       # ['Black/Gray', 'Peacock Blue', ...]
        size_names = variants["sizes"]         # ['36', '37', '38', ...]


        # ============================================================
        #  🔥  THÊM VÀO ĐÚNG YÊU CẦU: Nếu không có thì random
        # ============================================================

        if not color_names:
            color_names = random.sample(all_db_colors, min(2, len(all_db_colors)))
            print(f"⚠️ Web không có màu → random: {color_names}")

        if not size_names:
            size_names = random.sample(all_db_sizes, min(2, len(all_db_sizes)))
            print(f"⚠️ Web không có size → random: {size_names}")


        # Nếu random xong mà vẫn rỗng thì skip
        if not color_names or not size_names:
            print(f"❌ Không có màu hoặc size (kể cả random) cho {ten_san_pham}")
            continue

        # 3) Fix giá nếu web không có
        if gia_ban <= 0:
            gia_ban = random.randint(700_000, 3_000_000)
        if gia_niem_yet <= 0 or gia_niem_yet < gia_ban:
            gia_niem_yet = int(gia_ban * random.uniform(1.05, 1.25))

        # 4) Tạo phiếu nhập
        ngay_nhap = random_date_2025()
        cursor.execute("""
            INSERT INTO nhap_hang (ma_nhap_hang, ngay_nhap)
            VALUES (%s, %s)
            RETURNING id_nhap_hang
        """, (ma_san_pham, ngay_nhap))
        id_nhap_hang = cursor.fetchone()[0]

        # ============================================================
        # 🔥 5) Mapping màu + size vào DB
        # ============================================================
        for color_name in color_names:

            # Tìm hoặc tạo màu
            cursor.execute("""
                SELECT id_mau FROM mau WHERE LOWER(ten_mau)=LOWER(%s)
            """, (color_name,))
            row_color = cursor.fetchone()

            if row_color:
                id_mau = row_color[0]
            else:
                cursor.execute("""
                    INSERT INTO mau (ten_mau)
                    VALUES (%s)
                    RETURNING id_mau
                """, (color_name,))
                id_mau = cursor.fetchone()[0]

            for size_name in size_names:

                # Tìm hoặc tạo kích thước
                cursor.execute("""
                    SELECT id_kich_thuoc FROM kich_thuoc WHERE LOWER(ten_kich_thuoc)=LOWER(%s)
                """, (size_name,))
                row_size = cursor.fetchone()

                if row_size:
                    id_kich_thuoc = row_size[0]
                else:
                    cursor.execute("""
                        INSERT INTO kich_thuoc (ten_kich_thuoc)
                        VALUES (%s)
                        RETURNING id_kich_thuoc
                    """, (size_name,))
                    id_kich_thuoc = cursor.fetchone()[0]

                # Tạo tên chi tiết
                ten_chi_tiet = f"{ten_san_pham} - {color_name} - {size_name}"

                # Tạo chi tiết sản phẩm
                try:
                    giam_phan_tram = random.uniform(0.10, 0.20)
                    don_gia = int(gia_ban * (1 - giam_phan_tram))
                    cursor.execute("""
                        INSERT INTO san_pham_chi_tiet
                        (id_san_pham, id_mau, id_kich_thuoc, so_luong_ton,
                         ten_san_pham_chi_tiet, gia_niem_yet, gia_ban)
                        VALUES (%s, %s, %s, %s, %s, %s, %s)
                        RETURNING id_san_pham_chi_tiet
                    """, (
                        id_san_pham, id_mau, id_kich_thuoc, 10,
                        ten_chi_tiet, gia_niem_yet, gia_ban
                    ))
                    id_spct = cursor.fetchone()[0]

                    print(f"✅ SPCT: {ten_chi_tiet} — Giá nhập: {don_gia:,} VNĐ")

                    # Thêm vào nhap_hang_chi_tiet
                    cursor.execute("""
                        INSERT INTO nhap_hang_chi_tiet
                        (id_nhap_hang, id_san_pham_chi_tiet, so_luong, don_gia)
                        VALUES (%s, %s, %s, %s)
                    """, (id_nhap_hang, id_spct, 10, don_gia))

                except Exception as e:
                    print(f"❌ Lỗi tạo chi tiết '{ten_chi_tiet}': {e}")

        # ============================================================
        # 6) Gán thuộc tính sản phẩm (giữ nguyên code của em)
        # ============================================================
        for attr_name, attr_value in attributes:

            cursor.execute("""
                SELECT id_thuoc_tinh FROM thuoc_tinh
                WHERE LOWER(ten_thuoc_tinh)=LOWER(%s)
            """, (attr_name,))
            row = cursor.fetchone()

            if row:
                id_thuoc_tinh = row[0]
            else:
                cursor.execute("""
                    INSERT INTO thuoc_tinh (ten_thuoc_tinh)
                    VALUES (%s)
                    RETURNING id_thuoc_tinh
                """, (attr_name,))
                id_thuoc_tinh = cursor.fetchone()[0]

            cursor.execute("""
                SELECT id_thuoc_tinh_chi_tiet
                FROM thuoc_tinh_chi_tiet
                WHERE LOWER(ten_thuoc_tinh_chi_tiet)=LOWER(%s)
                  AND id_thuoc_tinh=%s
            """, (attr_value, id_thuoc_tinh))
            row2 = cursor.fetchone()

            if row2:
                id_ttct = row2[0]
            else:
                cursor.execute("""
                    INSERT INTO thuoc_tinh_chi_tiet (id_thuoc_tinh, ten_thuoc_tinh_chi_tiet)
                    VALUES (%s, %s)
                    RETURNING id_thuoc_tinh_chi_tiet
                """, (id_thuoc_tinh, attr_value))
                id_ttct = cursor.fetchone()[0]

            cursor.execute("""
                INSERT INTO san_pham_thuoc_tinh (id_san_pham, id_thuoc_tinh_chi_tiet)
                VALUES (%s, %s)
                ON CONFLICT DO NOTHING
            """, (id_san_pham, id_ttct))

        print(f"📌 Gán xong {len(attributes)} thuộc tính cho {ten_san_pham}")

    print("🎉 DONE TẠO CHI TIẾT SẢN PHẨM!")


def createAnhSanPham(cursor, storage_folder=None):
    # Nếu không truyền vào → dùng default
    if not storage_folder:
        storage_folder = r"C:\Users\huyph\Downloads\badminton"

    # Tạo thư mục gốc nếu chưa có
    if not os.path.exists(storage_folder):
        os.makedirs(storage_folder, exist_ok=True)

    # Lấy danh sách sản phẩm: slug + mã sản phẩm để đặt tên folder
    cursor.execute("SELECT slug, ma_san_pham FROM san_pham")
    san_phams = cursor.fetchall()

    if not san_phams:
        print("❌ Không có sản phẩm nào để tải ảnh.")
        return

    headers = {"User-Agent": "Mozilla/5.0"}

    for slug, ma_san_pham in san_phams:
        product_url = f"https://shopvnb.com/{slug}.html"
        print(f"\n🔍 Đang xử lý: {product_url}")

        try:
            response = requests.get(product_url, headers=headers, timeout=10)
            soup = BeautifulSoup(response.text, "html.parser")

            slides = soup.select(".swiper-wrapper .swiper-slide img")

            if not slides:
                print(f"⚠️ Không tìm thấy ảnh cho sản phẩm {ma_san_pham}")
                continue

            # Thư mục theo từng mã sản phẩm
            product_folder = os.path.join(storage_folder, ma_san_pham)
            os.makedirs(product_folder, exist_ok=True)

            img_index = 1  # số thứ tự ảnh

            for img_tag in slides:
                img_url = img_tag.get("src") or img_tag.get("data-src")

                # BỎ QUA ảnh dạng Base64
                if not img_url or img_url.startswith("data:image"):
                    continue

                # Fix URL thiếu domain
                if img_url.startswith("//"):
                    img_url = "https:" + img_url
                elif img_url.startswith("/"):
                    img_url = "https://shopvnb.com" + img_url

                file_path = os.path.join(product_folder, f"image_{img_index}.jpg")

                try:
                    img_data = requests.get(img_url, headers=headers, timeout=10).content
                    with open(file_path, "wb") as f:
                        f.write(img_data)

                    print(f"   📥 Đã tải: image_{img_index}.jpg")

                    img_index += 1

                except Exception as e:
                    print(f"   ❌ Lỗi tải ảnh {img_url}: {e}")

        except Exception as e:
            print(f"❌ Lỗi truy cập trang {product_url}: {e}")

    print("\n🎉 Hoàn tất tải ảnh tất cả sản phẩm!")


def ganAnhSanPham(cursor, connection, storage_folder=None):
    print("🗑️ TRUNCATE bảng anh_san_pham…")
    cursor.execute("TRUNCATE anh_san_pham CASCADE")
    connection.commit()

    laravel_storage = PATH_PROJECT_STORAGE / "anh_san_phams"

    os.makedirs(laravel_storage, exist_ok=True)

    # XÓA file storage cũ
    print("🗑️ Xóa ảnh cũ trong storage…")
    for f in os.listdir(laravel_storage):
        fp = os.path.join(laravel_storage, f)
        if os.path.isfile(fp):
            os.remove(fp)
    print("✅ Đã dọn sạch!")

    # BẮT ĐẦU XỬ LÝ SẢN PHẨM
    for ma_san_pham in os.listdir(storage_folder):
        sp_folder = os.path.join(storage_folder, ma_san_pham)
        if not os.path.isdir(sp_folder):
            continue

        print(f"\n🔍 SP: {ma_san_pham}")

        # Lấy id_san_pham
        cursor.execute("""
            SELECT id_san_pham
            FROM san_pham
            WHERE ma_san_pham = %s
            LIMIT 1
        """, (ma_san_pham,))
        row = cursor.fetchone()
        if not row:
            print("⚠ Không tìm thấy SP trong DB.")
            continue

        id_san_pham = row[0]

        # Lấy toàn bộ id_san_pham_chi_tiet của sản phẩm này
        cursor.execute("""
            SELECT id_san_pham_chi_tiet, id_mau FROM san_pham_chi_tiet WHERE id_san_pham = %s ORDER BY id_mau, id_san_pham_chi_tiet;
        """, (id_san_pham,))
        list_ct = [r[0] for r in cursor.fetchall()]

        if not list_ct:
            print("⚠ Không có chi tiết SP.")
            continue

        # LẤY ẢNH TỪ FOLDER SẢN PHẨM
        image_files = [
            f for f in os.listdir(sp_folder)
            if f.lower().endswith((".jpg", ".jpeg", ".png"))
        ]

        if not image_files:
            print("⚠ Không có ảnh.")
            continue

        image_files = sorted(image_files, key=natural_sort_key)
        use_images = image_files[:max(1, len(image_files) // 2)]

        # LẶP ẢNH & GÁN CHO TỪNG CHI TIẾT SP
        for file_name in use_images:
            full_path = os.path.join(sp_folder, file_name)

            # lấy thứ tự từ tên file, ví dụ image_3.jpg → 3
            m = re.search(r"(\d+)(?=\.\w+$)", file_name)
            thu_tu = int(m.group(1)) if m else 1

            for id_ct in list_ct:
                ext = os.path.splitext(file_name)[1].replace(".", "")
                time_str = datetime.datetime.now().strftime("%Y%m%d_%H%M%S")
                uniq = uuid.uuid4().hex[:8]
                new_name = f"{ma_san_pham}_{time_str}_{uniq}.{ext}"

                dest = os.path.join(laravel_storage, new_name)
                shutil.copy2(full_path, dest)

                db_path = f"anh_san_phams/{new_name}"

                cursor.execute("""
                    INSERT INTO anh_san_pham (id_san_pham_chi_tiet, anh_url, thu_tu)
                    VALUES (%s, %s, %s)
                """, (id_ct, db_path, thu_tu))
                connection.commit()

                print(f"   📥 Insert: {db_path} (id_ct={id_ct}, thu_tu={thu_tu})")

    print("\n🎉 HOÀN TẤT GÁN ẢNH CHO TẤT CẢ SẢN PHẨM!")

def main():
    conn = get_db_connection()
    cursor = conn.cursor()

#     getDanhMuc(cursor)
#     conn.commit()

#     slug_category = "vot-cau-long"
#     getThuongHieu(cursor, slug_category)
#     conn.commit()

#     createDanhMucThuongHieu(cursor)
#     conn.commit()

#     createSanPham(cursor)
#     conn.commit()

    # thêm thuộc tính cho danh mục
    # tao het san pham roi hay chay
#     createSanPhamChiTiet(cursor)
#     conn.commit()
#
    createAnhSanPham(
        cursor,
        storage_folder=r"C:\Users\huyph\Downloads\badminton"
    )
    conn.commit()

    ganAnhSanPham(
            cursor,
            conn,
            storage_folder=r"C:\Users\huyph\Downloads\badminton"
        )
    conn.commit()

    cursor.close()
    conn.close()

if __name__ == '__main__':
    main()

