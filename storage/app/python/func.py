import re
from decimal import Decimal

import unicodedata

def to_slug(text: str) -> str:
    text = unicodedata.normalize('NFD', text)
    text = text.encode('ascii', 'ignore').decode('utf-8')
    text = re.sub(r'[^a-zA-Z0-9\s-]', '', text)
    text = re.sub(r'[\s\-]+', '-', text)
    return text.strip('-').lower()


def parse_price(price_str: str) -> Decimal:
    cleaned = price_str.replace("₫", "").replace(".", "").replace(",", ".").strip()
    return Decimal(cleaned)
