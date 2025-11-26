import psycopg2

def get_db_connection():
    return psycopg2.connect(
        host="localhost",
        user="postgres",
        password="123",
        dbname="badminton-shop"
    )
