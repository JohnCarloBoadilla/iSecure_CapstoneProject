from fastapi import UploadFile
from PIL import Image
import pytesseract
import io
import re

def extract_id_info(file: UploadFile):
    img_bytes = file.file.read()
    file.file.seek(0)
    image = Image.open(io.BytesIO(img_bytes))
    text = pytesseract.image_to_string(image)
    
    id_number = re.search(r"ID[:\s]*([A-Z0-9]+)", text)
    dob = re.search(r"DOB[:\s]*([0-9/]+)", text)
    
    return {
        "id_number": id_number.group(1) if id_number else None,
        "dob": dob.group(1) if dob else None,
        "raw_text": text
    }
