import psycopg2

def get_db_connection():
    return psycopg2.connect(
        host="localhost",
        user="postgres",
        password="123456",
        dbname="badminton_shop"
    )
