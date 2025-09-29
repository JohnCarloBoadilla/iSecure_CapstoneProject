from app.db import get_db_connection
from datetime import datetime, timedelta
import smtplib
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart

# SMTP configuration
SMTP_SERVER = 'smtp.example.com'
SMTP_PORT = 587
SMTP_USERNAME = 'your_email@example.com'
SMTP_PASSWORD = 'your_password'
EMAIL_FROM = 'your_email@example.com'
EMAIL_SUBJECT = 'Visit Time Limit Notification'

def send_email(to_email, visitor_name):
    msg = MIMEMultipart()
    msg['From'] = EMAIL_FROM
    msg['To'] = to_email
    msg['Subject'] = EMAIL_SUBJECT

    body = f"Dear {visitor_name},\n\nYour visit time of 2 hours is about to expire. Please prepare to leave.\n\nThank you."
    msg.attach(MIMEText(body, 'plain'))

    try:
        server = smtplib.SMTP(SMTP_SERVER, SMTP_PORT)
        server.starttls()
        server.login(SMTP_USERNAME, SMTP_PASSWORD)
        server.send_message(msg)
        server.quit()
        print(f"Notification email sent to {to_email}")
    except Exception as e:
        print(f"Failed to send email to {to_email}: {e}")

def check_visitors_time_limit():
    conn = get_db_connection()
    cursor = conn.cursor()
    two_hours_ago = datetime.now() - timedelta(hours=2)

    cursor.execute("""
        SELECT id, visitor_name, email, time_in FROM visitors
        WHERE time_in IS NOT NULL AND time_out IS NULL AND time_in <= %s
    """, (two_hours_ago.strftime('%Y-%m-%d %H:%M:%S'),))

    visitors_to_notify = cursor.fetchall()

    for visitor in visitors_to_notify:
        visitor_id, visitor_name, email, time_in = visitor.values()
        if email and email.strip():
            send_email(email.strip(), visitor_name)
        # Optionally mark notification_sent in DB

    conn.close()