import psycopg2
import requests
from bs4 import BeautifulSoup
from conn import get_db_connection
from func import to_slug
from func import parse_price
from slugify import slugify
import random

def getDanhMuc(cursor):
    url = "https://shopvnb.com/"
    headers = {"User-Agent": "Mozilla/5.0"}
    response = requests.get(url, headers=headers)
    soup = BeautifulSoup(response.text, 'html.parser')

    titles = soup.find_all('a', class_='hmega')

    for title in titles:
        name = title.text.strip()
        slug = to_slug(name)
        print(f"Đang thêm: {name} → {slug}")
        cursor.execute("""
                       INSERT INTO danh_muc (ten_danh_muc, slug)
                       VALUES (%s, %s)
                       """, (name, slug))

def getAllSlugDanhMuc(cursor):
    cursor.execute("SELECT slug FROM danh_muc;")
    rows = cursor.fetchall()
    return [row[0] for row in rows]

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


def createSanPham(cursor, ten_thuong_hieu_input, ten_danh_muc_input):
    headers = {"User-Agent": "Mozilla/5.0"}
    base_url = "https://shopvnb.com/vot-cau-long-yonex.html"

    response = requests.get(base_url, headers=headers)
    soup = BeautifulSoup(response.text, "html.parser")

    product_tags = soup.select("span.product-name a")
    if not product_tags:
        print("❌ Không tìm thấy sản phẩm nào.")
        return

    # Lấy id_thuong_hieu theo tên nhập
    cursor.execute(
        "SELECT id_thuong_hieu FROM thuong_hieu WHERE LOWER(ten_thuong_hieu) = %s",
        (ten_thuong_hieu_input.lower(),)
    )
    th = cursor.fetchone()
    id_thuong_hieu = th[0] if th else None

    # Lấy id_danh_muc theo tên nhập
    cursor.execute(
        "SELECT id_danh_muc FROM danh_muc WHERE LOWER(ten_danh_muc) = %s",
        (ten_danh_muc_input.lower(),)
    )
    dm = cursor.fetchone()
    id_danh_muc = dm[0] if dm else None

    if not id_thuong_hieu or not id_danh_muc:
        print(f"❌ Không tìm thấy ID cho thương hiệu '{ten_thuong_hieu_input}' hoặc danh mục '{ten_danh_muc_input}'")
        return

    # Lấy id_danh_muc_thuong_hieu
    cursor.execute("""
        SELECT id_danh_muc_thuong_hieu
        FROM danh_muc_thuong_hieu
        WHERE id_thuong_hieu = %s AND id_danh_muc = %s
    """, (id_thuong_hieu, id_danh_muc))
    dmth = cursor.fetchone()
    id_danh_muc_thuong_hieu = dmth[0] if dmth else None

    if not id_danh_muc_thuong_hieu:
        print(f"⚠️ Không tìm thấy danh_muc_thuong_hieu cho {ten_thuong_hieu_input}-{ten_danh_muc_input}")
        return

    # Duyệt qua từng sản phẩm
    for index, a_tag in enumerate(product_tags, start=1):
        ten_san_pham = a_tag.text.strip()
        if not ten_san_pham:
            continue

        slug = slugify(ten_san_pham)
        ma_san_pham = f"BMS{id_thuong_hieu}{id_danh_muc}{str(index).zfill(3)}"

        try:
            cursor.execute("""
                INSERT INTO san_pham (ma_san_pham, ten_san_pham, slug, id_danh_muc_thuong_hieu)
                VALUES (%s, %s, %s, %s)
            """, (ma_san_pham, ten_san_pham, slug, id_danh_muc_thuong_hieu))
            print(f"✅ Đã thêm: {ten_san_pham} ({ma_san_pham})")

        except Exception as e:
            print(f"⚠️ Lỗi khi thêm sản phẩm '{ten_san_pham}': {e}")

def createSanPhamChiTiet(cursor):
    # Lấy toàn bộ dữ liệu san_pham, mau, kich_thuoc
    cursor.execute("SELECT id_san_pham, ten_san_pham FROM san_pham")
    san_phams = cursor.fetchall()

    cursor.execute("SELECT id_mau, ten_mau FROM mau")
    maus = cursor.fetchall()

    cursor.execute("SELECT id_kich_thuoc, ten_kich_thuoc FROM kich_thuoc")
    kich_thuocs = cursor.fetchall()

    if not san_phams or not maus or not kich_thuocs:
        print("❌ Thiếu dữ liệu san_pham / mau / kich_thuoc để tạo chi tiết!")
        return

    for sp in san_phams:
        id_san_pham, ten_san_pham = sp

        # Random 2 màu khác nhau
        selected_maus = random.sample(maus, 2)

        # Random 2 kích thước khác nhau
        selected_sizes = random.sample(kich_thuocs, 2)

        for mau in selected_maus:
            id_mau, ten_mau = mau

            for kt in selected_sizes:
                id_kich_thuoc, ten_kich_thuoc = kt

                ten_chi_tiet = f"{ten_san_pham} - {ten_mau} - {ten_kich_thuoc}"
                gia_niem_yet = random.randint(300000, 5000000)
                gia_ban = gia_niem_yet - random.randint(0, 300000)  # giảm nhẹ random

                try:
                    cursor.execute("""
                        INSERT INTO san_pham_chi_tiet
                        (id_san_pham, id_mau, id_kich_thuoc, so_luong_ton, ten_san_pham_chi_tiet, gia_niem_yet, gia_ban)
                        VALUES (%s, %s, %s, %s, %s, %s, %s)
                    """, (
                        id_san_pham,
                        id_mau,
                        id_kich_thuoc,
                        10,
                        ten_chi_tiet,
                        gia_niem_yet,
                        gia_ban
                    ))

                    print(f"✅ Tạo chi tiết: {ten_chi_tiet}")

                except Exception as e:
                    print(f"❌ Lỗi khi thêm chi tiết sản phẩm '{ten_chi_tiet}': {e}")

    print("🎉 Hoàn tất tạo dữ liệu san_pham_chi_tiet!")



def main():
    conn = get_db_connection()
    cursor = conn.cursor()

    # getDanhMuc(cursor)
    # conn.commit()

    # slugs = getAllSlugDanhMuc(cursor)
    # for slug in slugs:
    #     getThuongHieu(cursor, slug)
    #     conn.commit()

    # createDanhMucThuongHieu(cursor)
    # conn.commit()

#     createSanPham(cursor, "Lining", "Vợt cầu lông")
#     conn.commit()

    createSanPhamChiTiet(cursor)
    conn.commit()

    cursor.close()
    conn.close()

if __name__ == '__main__':
    main()

