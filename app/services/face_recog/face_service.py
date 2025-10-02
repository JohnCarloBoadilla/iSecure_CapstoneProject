import face_recognition
import numpy as np
import json
from fastapi import UploadFile
from app.db import get_db_connection
import cv2

def get_all_visitors_encodings():
    conn = get_db_connection()
    cursor = conn.cursor()
    cursor.execute("SELECT id, visitor_name as name, face_encoding FROM visitors WHERE face_encoding IS NOT NULL")
    visitors = cursor.fetchall()
    conn.close()
    return [{"id": v["id"], "name": v["name"], "face_encoding": json.loads(v["face_encoding"])} for v in visitors]

def get_all_personnels_encodings():
    conn = get_db_connection()
    cursor = conn.cursor()
    cursor.execute("SELECT id, name, face_encoding FROM personnels WHERE face_encoding IS NOT NULL")
    personnels = cursor.fetchall()
    conn.close()
    return [{"id": p["id"], "name": p["name"], "face_encoding": json.loads(p["face_encoding"])} for p in personnels]

def update_visitor_time_in(visitor_id):
    conn = get_db_connection()
    cursor = conn.cursor()
    cursor.execute("UPDATE visitors SET time_in = NOW() WHERE id = %s", (visitor_id,))
    conn.commit()
    conn.close()

def update_personnel_time_in(personnel_id):
    conn = get_db_connection()
    cursor = conn.cursor()
    cursor.execute("UPDATE personnels SET time_in = NOW() WHERE id = %s", (personnel_id,))
    conn.commit()
    conn.close()

def recognize_face_from_frame(frame):
    rgb_frame = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)
    uploaded_encodings = face_recognition.face_encodings(rgb_frame)
    
    if not uploaded_encodings:
        return {"recognized": False}
    
    uploaded_encoding = uploaded_encodings[0]
    visitors = get_all_visitors_encodings()
    personnels = get_all_personnels_encodings()

    for visitor in visitors:
        known_encoding = np.array(visitor["face_encoding"])
        match = face_recognition.compare_faces([known_encoding], uploaded_encoding, tolerance=0.5)
        if match[0]:
            update_visitor_time_in(visitor["id"])
            return {"recognized": True, "type": "visitor", "id": visitor["id"], "name": visitor["name"]}

    for personnel in personnels:
        known_encoding = np.array(personnel["face_encoding"])
        match = face_recognition.compare_faces([known_encoding], uploaded_encoding, tolerance=0.5)
        if match[0]:
            update_personnel_time_in(personnel["id"])
            return {"recognized": True, "type": "personnel", "id": personnel["id"], "name": personnel["name"]}

    return {"recognized": False}

def recognize_face(file: UploadFile):
    image = face_recognition.load_image_file(file.file)
    uploaded_encodings = face_recognition.face_encodings(image)

    if not uploaded_encodings:
        return {"recognized": False}

    uploaded_encoding = uploaded_encodings[0]
    visitors = get_all_visitors_encodings()
    personnels = get_all_personnels_encodings()

    for visitor in visitors:
        known_encoding = np.array(visitor["face_encoding"])
        match = face_recognition.compare_faces([known_encoding], uploaded_encoding, tolerance=0.5)
        if match[0]:
            update_visitor_time_in(visitor["id"])
            return {"recognized": True, "type": "visitor", "id": visitor["id"], "name": visitor["name"]}

    for personnel in personnels:
        known_encoding = np.array(personnel["face_encoding"])
        match = face_recognition.compare_faces([known_encoding], uploaded_encoding, tolerance=0.5)
        if match[0]:
            update_personnel_time_in(personnel["id"])
            return {"recognized": True, "type": "personnel", "id": personnel["id"], "name": personnel["name"]}

    return {"recognized": False}

def compare_faces(file1: UploadFile, file2: UploadFile):
    image1 = face_recognition.load_image_file(file1.file)
    image2 = face_recognition.load_image_file(file2.file)

    encodings1 = face_recognition.face_encodings(image1)
    encodings2 = face_recognition.face_encodings(image2)

    if not encodings1 or not encodings2:
        return {"match": False, "message": "No face detected in one or both images"}

    encoding1 = encodings1[0]
    encoding2 = encodings2[0]

    match = face_recognition.compare_faces([encoding1], encoding2, tolerance=0.5)
    return {"match": bool(match[0]), "message": "Faces match" if match[0] else "Faces do not match"}
