import psycopg2

def get_db_connection():
    return psycopg2.connect(
        host="172.22.166.22",
        user="postgres",
        password="123456",
        dbname="badminton_shop"
    )
