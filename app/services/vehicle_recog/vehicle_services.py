import cv2
import pytesseract
from fastapi import UploadFile
import numpy as np

def detect_vehicle_plate(file: UploadFile):
    img_array = np.frombuffer(file.file.read(), np.uint8)
    img = cv2.imdecode(img_array, cv2.IMREAD_COLOR)
    gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)
    plate_text = pytesseract.image_to_string(gray)
    file.file.seek(0)
    return plate_text.strip()

def detect_vehicle_color(file: UploadFile):
    img_array = np.frombuffer(file.file.read(), np.uint8)
    img = cv2.imdecode(img_array, cv2.IMREAD_COLOR)
    avg_color = tuple(int(c) for c in np.average(np.average(img, axis=0), axis=0))
    file.file.seek(0)
    return avg_color
