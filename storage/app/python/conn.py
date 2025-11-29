import psycopg2

def get_db_connection():
    return psycopg2.connect(
        host="localhost",
        user="postgres",
<<<<<<< HEAD
        password="1",
        dbname="badminton_shop"
=======
        password="123",
        dbname="badminton-shop"
>>>>>>> 5162171fc3c31b7a8844af0f26b25ef2ba648494
    )
