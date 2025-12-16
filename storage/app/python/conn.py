import psycopg2
import os
from pathlib import Path

def get_db_connection():
    conn = psycopg2.connect(
            host=os.getenv('DB_HOST', '172.22.166.22'),
            database=os.getenv('DB_DATABASE', 'badminton_shop'),
            user=os.getenv('DB_USERNAME', 'postgres'),
            password=os.getenv('DB_PASSWORD', '123456'),
            port=os.getenv('DB_PORT', '5432')
        )
    return conn


PATH_PROJECT_STORAGE = Path(r"D:\Project\badminton-shop\admin-badmintonshop\storage\app")

APP_URL_PATH = "http://127.0.0.1:8000/storage"
API_KEY_GROQ = "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"

