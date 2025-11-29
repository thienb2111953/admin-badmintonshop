import psycopg2

def get_db_connection():
    return psycopg2.connect(
        host="localhost",
        user="postgres",
        password="1",
        dbname="badminton_shop"
    )
