from flask import Flask, request, jsonify
from datetime import datetime
import pytz
import hmac
import hashlib
import os
from dotenv import load_dotenv

from functions.database import db_instance  

load_dotenv()

SECRET_KEY = os.getenv("SECRET_KEY", "default_secret").encode()

app = Flask(__name__)
ph_timezone = pytz.timezone('Asia/Manila')


@app.route('/verify_otp', methods=['POST'])
def verify_otp():
    try:
        data = request.get_json()
        if not data or 'email' not in data or 'otp' not in data:
            return jsonify({'status': 'error', 'message': 'invalid_request'}), 400

        email = data['email']
        otp = data['otp']

        conn = db_instance.get_connection()
        cursor = conn.cursor(dictionary=True)

        cursor.execute(
            "SELECT * FROM otp_verifications WHERE email=%s ORDER BY created_at DESC LIMIT 1",
            (email,)
        )
        otp_data = cursor.fetchone()

        if not otp_data:
            return jsonify({'status': 'error', 'message': 'No OTP Found'}), 400

        expires_at = otp_data['expires_at'].replace(tzinfo=pytz.utc).astimezone(ph_timezone)
        current_time = datetime.now(ph_timezone)

        hashed_otp = hmac.new(SECRET_KEY, str(otp).encode(), hashlib.sha256).hexdigest()

        if not hmac.compare_digest(otp_data['otp'], hashed_otp):
            return jsonify({'status': 'error', 'message': 'Invalid OTP'}), 400

        if current_time > expires_at:
            return jsonify({'status': 'error', 'message': 'Expired OTP'}), 400

        cursor.execute("UPDATE otp_verifications SET verified=1 WHERE id=%s", (otp_data['id'],))
        conn.commit()

        return jsonify({'status': 'success'})

    except Exception as e:
        # === FIX 1 HERE ===
        print("Error during OTP verification: {}".format(e))
        return jsonify({'status': 'error', 'message': 'Server Error'}), 500

    finally:
        if cursor:
            cursor.close()
        if conn:
            conn.close()


# === ADMIN OTP VERIFICATION ===
@app.route('/admin_verify_otp', methods=['POST'])
def admin_verify_otp():
    try:
        data = request.get_json()
        if not data or 'email' not in data or 'otp' not in data:
            return jsonify({'status': 'error', 'message': 'invalid_request'}), 400

        email = data['email']
        otp = data['otp']

        conn = db_instance.get_connection()
        cursor = conn.cursor(dictionary=True)

        cursor.execute("""
            SELECT * FROM otp_verifications 
            WHERE email=%s AND verified=0 
            ORDER BY created_at DESC LIMIT 1
        """, (email,))
        otp_data = cursor.fetchone()

        if not otp_data:
            return jsonify({'status': 'error', 'message': 'No OTP found or already used'}), 400

        expires_at = otp_data['expires_at']
        if isinstance(expires_at, str):
            expires_at = datetime.strptime(expires_at, '%Y-%m-%d %H:%M:%S')
        expires_at = ph_timezone.localize(expires_at)
        current_time = datetime.now(ph_timezone)

        hashed_otp = hmac.new(SECRET_KEY, str(otp).encode(), hashlib.sha256).hexdigest()

        if not hmac.compare_digest(otp_data['otp'], hashed_otp):
            return jsonify({'status': 'error', 'message': 'Invalid OTP'}), 400

        if current_time > expires_at:
            return jsonify({'status': 'error', 'message': 'Expired OTP'}), 400

        cursor.execute("UPDATE otp_verifications SET verified=1 WHERE id=%s", (otp_data['id'],))
        conn.commit()

        return jsonify({'status': 'success'})

    except Exception as e:
        # === FIX 2 HERE ===
        print("Error during admin OTP verification: {}".format(e))
        return jsonify({'status': 'error', 'message': 'Server Error'}), 500

    finally:
        if cursor:
            cursor.close()
        if conn:
            conn.close()


if __name__ == '__main__':
    app.run(debug=True)