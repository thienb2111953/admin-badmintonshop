import psycopg2
from pathlib import Path

def get_db_connection():
    return psycopg2.connect(
        host="172.22.166.22",
        user="postgres",
        password="123456",
        dbname="badminton_shop"
    )


PATH_PROJECT_STORAGE = Path(r"D:\Project\badminton-shop\admin-badmintonshop\storage\app")

APP_URL_PATH = "http://127.0.0.1:8000/storage"

API_KEY_GROQ = "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"
API_KEY_GEMINI = "AIzaSyAvb4hg2xxPmHqdQaMhO9xhZYWuMbvXTWs"

