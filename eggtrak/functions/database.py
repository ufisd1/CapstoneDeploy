import os
import mysql.connector
from mysql.connector import pooling
from dotenv import load_dotenv

load_dotenv()

class Database:
    _instance = None
    
    def __new__(cls):
        if cls._instance is None:
            cls._instance = super(Database, cls).__new__(cls)
            cls._instance.config = {
                'host': os.getenv('DB_HOST'),
                'user': os.getenv('DB_USER'),
                'password': os.getenv('DB_PASSWORD'),
                'database': os.getenv('DB_NAME'),
                'pool_name': os.getenv('DB_POOL_NAME'),
                'pool_size': int(os.getenv('DB_POOL_SIZE', 5))
            }
            cls._instance.create_pool()
        return cls._instance
    
    def create_pool(self):
        try:
            self.connection_pool = pooling.MySQLConnectionPool(
                pool_name=self.config['pool_name'],
                pool_size=self.config['pool_size'],
                **{k: v for k, v in self.config.items() if k not in ['pool_name', 'pool_size']}
            )
            print("✅ Connection pool created successfully.")
        except Exception as e:
            # === FIX 1 HERE ===
            print("❌ Error creating connection pool: {}".format(e))
            raise

    def get_connection(self):
        try:
            return self.connection_pool.get_connection()
        except Exception as e:
            # === FIX 2 HERE ===
            print("❌ Error getting database connection: {}".format(e))
            raise

db_instance = Database()