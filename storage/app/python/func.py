import re
from decimal import Decimal
import requests
from bs4 import BeautifulSoup
import random
from datetime import date, timedelta
import unicodedata

def to_slug(text: str) -> str:
    text = unicodedata.normalize('NFD', text)
    text = text.encode('ascii', 'ignore').decode('utf-8')
    text = re.sub(r'[^a-zA-Z0-9\s-]', '', text)
    text = re.sub(r'[\s\-]+', '-', text)
    return text.strip('-').lower()

def parse_price(text: str) -> int:
    if not text:
        return 0
    digits = re.sub(r'\D', '', text)
    return int(digits) if digits else 0


def get_product_info_from_shopvnb(slug: str):
    """
    Trả về:
    {
        "gia_ban": int,
        "gia_niem_yet": int,
        "attributes": [(ten_thuoc_tinh, ten_chi_tiet), ...]
    }
    """
    url = f"https://shopvnb.com/{slug}.html"
    print(f"🔍 Lấy dữ liệu từ: {url}")

    headers = {"User-Agent": "Mozilla/5.0"}

    # ------------------------------------
    # 1. REQUEST HTML
    # ------------------------------------
    try:
        resp = requests.get(url, headers=headers, timeout=10)
        resp.raise_for_status()
    except Exception as e:
        print(f"❌ Lỗi tải trang {url}: {e}")
        return {"gia_ban": 0, "gia_niem_yet": 0, "attributes": []}

    soup = BeautifulSoup(resp.text, "html.parser")

    # ------------------------------------
    # 2. LẤY GIÁ BÁN + GIÁ NIÊM YẾT
    # ------------------------------------
    price_el = soup.select_one(".price.product-price span") or soup.select_one(".price.product-price")
    old_price_el = soup.select_one(".price.product-price-old span") or soup.select_one(".price.product-price-old")

    gia_ban = parse_price(price_el.get_text(strip=True)) if price_el else 0
    gia_niem_yet = parse_price(old_price_el.get_text(strip=True)) if old_price_el else gia_ban

    # ------------------------------------
    # 3. LẤY THUỘC TÍNH
    # ------------------------------------
    table = soup.select_one("table.table.table-bordered tbody")
    attributes = []

    if table:
        for tr in table.select("tr"):
            tds = tr.select("td")
            if len(tds) < 2:
                continue

            raw_name = tds[0].get_text(strip=True).replace(":", "").strip()
            attr_name = raw_name.lower()

            attr_value = tds[1].get_text(strip=True).lower()

            attributes.append((attr_name, attr_value))
    else:
        print("⚠️ Không có bảng thuộc tính!")

    print(f"   💰 Giá bán: {gia_ban}, Giá niêm yết: {gia_niem_yet}")
    print(f"   📌 Thuộc tính: {len(attributes)} dòng")

    return {
        "gia_ban": gia_ban,
        "gia_niem_yet": gia_niem_yet,
        "attributes": attributes,
    }

def get_variant_options_from_shopvnb(slug: str):
    url = f"https://shopvnb.com/{slug}.html"
    print(f"🔍 Lấy dữ liệu từ: {url}")

    headers = {"User-Agent": "Mozilla/5.0"}

    try:
        resp = requests.get(url, headers=headers, timeout=10)
        resp.raise_for_status()
    except Exception as e:
        print(f"❌ Lỗi tải trang {url}: {e}")
        return {
            "colors": [],
            "sizes": []
        }

    soup = BeautifulSoup(resp.text, "html.parser")

    # ======================================================
    # 1) LẤY DANH SÁCH MÀU TỪ class="reprelst lesspro"
    # ======================================================
    colors = []
    color_block = soup.select_one(".reprelst.lesspro")

    if color_block:
        for bt in color_block.select("bt"):
            # Lấy tên màu từ label.rname
            name_el = bt.select_one(".rname")
            ten_mau = name_el.get_text(strip=True) if name_el else None

            if ten_mau:
                colors.append(ten_mau)
    else:
        print("⚠️ Không có danh sách màu")

    # ======================================================
    # 2) LẤY DANH SÁCH SIZE TỪ class="swatch clearfix"
    # ======================================================
    sizes = []
    size_block = soup.select_one(".swatch.clearfix")

    if size_block:
        for se in size_block.select(".swatch-element"):
            size_text = se.get("data-value") or None
            if size_text:
                sizes.append(size_text)
    else:
        print("⚠️ Không có danh sách size")

    print(f"🎨 Màu lấy được: {len(colors)} → {colors}")
    print(f"📏 Size lấy được: {len(sizes)} → {sizes}")

    return {
        "colors": colors,
        "sizes": sizes
    }


def random_date_2025():
    start = date(2025, 1, 1)
    end = date(2025, 12, 31)
    delta_days = (end - start).days
    return start + timedelta(days=random.randint(0, delta_days))

# Hàm natural sort
def natural_sort_key(s):
    return [int(text) if text.isdigit() else text.lower() for text in re.split(r'(\d+)', s)]
