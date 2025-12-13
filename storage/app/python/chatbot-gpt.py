# -*- coding: utf-8 -*-
"""
FastAPI + OpenAI, hỗ trợ nhiều file theo slug danh mục (danh-muc/*.json)
- Mỗi file có cache embedding riêng: <name>.with_vec.json
- /chat: 1 call embed + 1 call chat
- /rebuild: rebuild 1 file hoặc toàn bộ
"""

import os, json, math, asyncio, re
from pathlib import Path
from typing import List, Dict, Any, Optional
from threading import Lock
from fastapi import FastAPI, HTTPException, Query
from pydantic import BaseModel
from openai import OpenAI

# =========================
# CONFIG
# =========================
BASE = Path(__file__).resolve().parent
DATA_DIR = BASE / "danh-muc"           # thư mục chứa các file danh mục
SUFFIX_VEC = ".with_vec.json"

MODEL_EMB  = os.getenv("EMBED_MODEL", "text-embedding-3-small")
MODEL_CHAT = os.getenv("CHAT_MODEL", "gpt-4o-mini")
BATCH_SIZE = int(os.getenv("EMBED_BATCH", "100"))
DEFAULT_TOP_K = int(os.getenv("TOP_K", "3"))

try:
    from conn import OPENAI_API_KEY
    API_KEY = OPENAI_API_KEY
except ImportError:
    API_KEY = "YOUR_API_KEY_HERE"

client = OpenAI(api_key=API_KEY)
_build_lock = Lock()

# =========================
# SCHEMA
# =========================
class ChatReq(BaseModel):
    query: str
    top_k: Optional[int] = None
    # ưu tiên nếu truyền file (ví dụ: "vot-cau-long.json")
    file: Optional[str] = None
    # ép rebuild cache cho file đã chọn
    rebuild: Optional[bool] = False

class ChatResp(BaseModel):
    answer: str
    products: List[Dict[str, Any]]
    file: str

class RebuildReq(BaseModel):
    file: Optional[str] = None  # rỗng = rebuild tất cả

# =========================
# FILE/DATA HELPERS
# =========================
def list_source_files() -> List[Path]:
    if not DATA_DIR.exists():
        raise FileNotFoundError(f"Không tìm thấy thư mục dữ liệu: {DATA_DIR}")
    return sorted(DATA_DIR.glob("*.json"))

def vec_path_for(src: Path) -> Path:
    return src.with_suffix(src.suffix + SUFFIX_VEC)  # file.json.with_vec.json

def load_raw_products(src: Path) -> List[Dict[str, Any]]:
    return json.loads(src.read_text(encoding="utf-8"))

def save_vec_data(out_path: Path, data: List[Dict[str, Any]]):
    out_path.write_text(json.dumps(data, ensure_ascii=False), encoding="utf-8")

def cosine(a: List[float], b: List[float]) -> float:
    if not a or not b:
        return 0.0
    sa = math.sqrt(sum(x*x for x in a))
    sb = math.sqrt(sum(x*x for x in b))
    if sa == 0 or sb == 0:
        return 0.0
    return sum(x*y for x, y in zip(a, b)) / (sa * sb)

def get_field(p: Dict[str, Any], names: List[str], default=""):
    for n in names:
        if n in p and p[n] not in (None, ""):
            return p[n]
    return default

def build_corpus_text(p: Dict[str, Any]) -> str:
    ten   = get_field(p, ["ten", "ten_san_pham", "name"])
    brand = get_field(p, ["ten_thuong_hieu", "thuong_hieu", "brand"])
    dm    = get_field(p, ["ten_danh_muc", "danh_muc", "category"])
    mo_ta = get_field(p, ["mo_ta", "moTa", "description"])
    gia   = str(get_field(p, ["muc_gia", "gia_min", "price_min", "gia"], ""))
    attrs = p.get("thuoc_tinh") or p.get("attributes") or {}
    if isinstance(attrs, dict):
        attr_str = " ".join(f"{k}:{v}" for k, v in attrs.items())
    elif isinstance(attrs, list):
        attr_str = " ".join(map(str, attrs))
    else:
        attr_str = ""
    return " ".join(map(str, [ten, brand, dm, mo_ta, gia, attr_str])).strip()

def precompute_embeddings_for(src: Path):
    raw = load_raw_products(src)
    texts = [build_corpus_text(p) for p in raw]
    embeddings: List[List[float]] = []

    # batch embed
    for i in range(0, len(texts), BATCH_SIZE):
        chunk = texts[i:i+BATCH_SIZE]
        rsp = client.embeddings.create(model=MODEL_EMB, input=chunk)
        embeddings.extend([it.embedding for it in rsp.data])

    # gắn vào dữ liệu
    for p, vec in zip(raw, embeddings):
        p["embedding"] = vec

    save_vec_data(vec_path_for(src), raw)

def ensure_vec_cache(src: Path, force_rebuild: bool = False):
    out = vec_path_for(src)
    if force_rebuild or (not out.exists()):
        with _build_lock:
            # double-check trong lock
            if force_rebuild or (not out.exists()):
                precompute_embeddings_for(src)

def pick_top_k_from_src(src: Path, question: str, k: int) -> List[Dict[str, Any]]:
    # đảm bảo có cache
    ensure_vec_cache(src, False)
    out_path = vec_path_for(src)

    q_emb = client.embeddings.create(model=MODEL_EMB, input=question).data[0].embedding
    data = json.loads(out_path.read_text(encoding="utf-8"))

    scored = []
    for p in data:
        sim = cosine(q_emb, p.get("embedding"))
        p_copy = dict(p)
        p_copy["_score"] = round(sim, 6)
        scored.append(p_copy)

    scored.sort(key=lambda x: x["_score"], reverse=True)
    return scored[:k]

# =========================
# TỰ ĐỘNG CHỌN FILE THEO TỪ KHÓA
# =========================
# map slug -> regex keyword
CATEGORY_HINTS = {
    "vot-cau-long.json":  r"\b(vợt|vot|racket|astrox|lining|victor|apacs)\b",
    "giay-cau-long.json": r"\b(giày|giay|shoes|power cushion|kumpoo)\b",
    "ao-cau-long.json":   r"\b(áo|ao|shirt|tee)\b",
    "quan-cau-long.json": r"\b(quần|quan|short|pants)\b",
    "vay-cau-long.json":  r"\b(váy|vay|skirt|dress)\b",
    "tui-vot-cau-long.json": r"\b(túi|tui|bag|backpack|bao vợt)\b",
    "balo-cau-long.json": r"\b(balo|ba lô|backpack)\b",
}

def auto_select_file(query: str, available_files: List[Path]) -> Optional[Path]:
    q = query.lower()
    # Ưu tiên khớp theo hints
    for fname, pattern in CATEGORY_HINTS.items():
        cand = next((p for p in available_files if p.name == fname), None)
        if cand and re.search(pattern, q):
            return cand
    # fallback: nếu câu chứa trực tiếp tên file (hiếm)
    for p in available_files:
        if p.stem in q:
            return p
    # fallback ưu tiên vợt nếu có
    for prefer in ["vot-cau-long.json", "giay-cau-long.json"]:
        cand = next((p for p in available_files if p.name == prefer), None)
        if cand:
            return cand
    # cuối cùng: lấy file đầu tiên
    return available_files[0] if available_files else None

def to_line(p: Dict[str, Any]) -> str:
    ten   = get_field(p, ["ten", "ten_san_pham", "name"])
    brand = get_field(p, ["ten_thuong_hieu", "thuong_hieu", "brand"])
    gia   = get_field(p, ["muc_gia", "gia_min", "price_min", "gia"])
    dm    = get_field(p, ["ten_danh_muc", "danh_muc", "category"])
    return f"- {ten} | {brand} | {dm} | Giá: {gia}"

def build_answer(question: str, top: List[Dict[str, Any]]) -> str:
    bullets = "\n".join(to_line(p) for p in top)
    user_content = (
        f"Người dùng hỏi: {question}\n"
        "Đây là vài gợi ý (tư vấn ngắn gọn, bullet, thực dụng):\n"
        f"{bullets}\n"
        "Kết thúc bằng 1 câu hỏi gợi ý tiếp theo."
    )
    rsp = client.chat.completions.create(
        model=MODEL_CHAT,
        messages=[
            {"role": "system", "content": "Bạn là tư vấn viên bán hàng cầu lông. Trả lời tiếng Việt, ngắn gọn, rõ ràng."},
            {"role": "user", "content": user_content},
        ],
        temperature=0.4,
    )
    return rsp.choices[0].message.content

# =========================
# FASTAPI
# =========================
app = FastAPI(title="Chatbot Badminton (multi-file)", version="1.1.0")

@app.on_event("startup")
def _startup():
    try:
        DATA_DIR.mkdir(parents=True, exist_ok=True)
        # không rebuild hàng loạt ở startup để tránh chậm khởi động
        list_source_files()  # kiểm tra tồn tại
    except Exception as e:
        print(f"[startup] warning: {e}")

@app.get("/health")
def health():
    files = [p.name for p in list_source_files()]
    return {"ok": True, "files": files}

@app.post("/rebuild")
async def rebuild(body: RebuildReq):
    files = list_source_files()

    targets: List[Path]
    if body.file:
        match = next((p for p in files if p.name == body.file), None)
        if not match:
            raise HTTPException(status_code=400, detail=f"File không tồn tại: {body.file}")
        targets = [match]
    else:
        targets = files

    def _job():
        try:
            for src in targets:
                ensure_vec_cache(src, True)
            print(f"✅ Rebuild xong: {', '.join(p.name for p in targets)}")
        except Exception as e:
            print(f"❌ Rebuild error: {e}")

    loop = asyncio.get_event_loop()
    await loop.run_in_executor(None, _job)
    return {"status": "rebuild-done", "files": [p.name for p in targets]}

@app.post("/chat", response_model=ChatResp)
async def chat(body: ChatReq):
    try:
        files = list_source_files()
        if not files:
            raise HTTPException(status_code=400, detail="Chưa có file JSON trong danh-muc/")

        # 1) Chọn file
        if body.file:
            src = next((p for p in files if p.name == body.file), None)
            if not src:
                raise HTTPException(status_code=400, detail=f"File không tồn tại: {body.file}")
        else:
            src = auto_select_file(body.query, files)
            if not src:
                raise HTTPException(status_code=400, detail="Không chọn được file phù hợp.")

        # 2) Bảo đảm cache
        ensure_vec_cache(src, bool(body.rebuild))

        # 3) Lấy top K từ file đã chọn
        k = body.top_k or DEFAULT_TOP_K
        top = pick_top_k_from_src(src, body.query, k)

        # 4) Gọi LLM để viết câu trả lời
        answer = build_answer(body.query, top)

        # 5) Trả về field gọn cho FE
        safe_fields = []
        for p in top:
            safe_fields.append({
                "ten": get_field(p, ["ten", "ten_san_pham", "name"]),
                "thuong_hieu": get_field(p, ["ten_thuong_hieu", "thuong_hieu", "brand"]),
                "danh_muc": get_field(p, ["ten_danh_muc", "danh_muc", "category"]),
                "gia": get_field(p, ["muc_gia", "gia_min", "price_min", "gia"]),
                "_score": p.get("_score", 0),
                "slug": p.get("slug", ""),
                "ma_san_pham": p.get("ma_san_pham") or p.get("sku", ""),
                "id": p.get("id") or p.get("id_san_pham"),
            })

        return ChatResp(answer=answer, products=safe_fields, file=src.name)

    except FileNotFoundError as e:
        raise HTTPException(status_code=400, detail=str(e))
    except Exception as e:
        raise HTTPException(status_code=500, detail=f"Server error: {e}")
