import os
import pymysql

DB_CONFIG = {
    "host": os.getenv("MYSQL_HOST", "localhost"),
    "user": os.getenv("MYSQL_USER", "it11"),
    "password": os.getenv("MYSQL_PASSWORD", "faxjgeninc"),
    "database": os.getenv("MYSQL_DATABASE", "isecuredb"),
    "cursorclass": pymysql.cursors.DictCursor
}

def get_db_connection():
    try:
        return pymysql.connect(**DB_CONFIG)
    except pymysql.MySQLError as e:
        print(f"Database connection failed: {e}")
        raise

