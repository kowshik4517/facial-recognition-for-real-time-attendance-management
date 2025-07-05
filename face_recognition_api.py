import cv2
import numpy as np
import torch
import json
from fastapi import FastAPI
from fastapi.responses import JSONResponse
from facenet_pytorch import InceptionResnetV1, MTCNN
from fastapi.middleware.cors import CORSMiddleware

# ✅ Initialize FastAPI
app = FastAPI()

# ✅ Enable CORS to fix 403 Forbidden errors
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # Change to frontend URL if needed
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# ✅ Initialize Face Detection & Recognition Models
mtcnn = MTCNN(keep_all=True, min_face_size=20, thresholds=[0.5, 0.6, 0.7])

resnet = InceptionResnetV1(pretrained="vggface2").eval()

# ✅ JSON File for Storing Face Data
JSON_FILE = "front end and python/faces.json"


# ✅ Load Faces from JSON
def load_faces():
    try:
        with open(JSON_FILE, "r") as file:
            return json.load(file)["students"]
    except (FileNotFoundError, json.JSONDecodeError):
        return []


# ✅ Detect & Encode Faces
def detect_and_encode(image):
    with torch.no_grad():
        boxes, _ = mtcnn.detect(image)
        encodings = []
        if boxes is not None:
            for box in boxes:
                x1, y1, x2, y2 = map(int, box)
                face = image[y1:y2, x1:x2]
                if face.size == 0:
                    continue
                face = cv2.resize(face, (160, 160))
                face = np.transpose(face, (2, 0, 1)).astype(np.float32) / 255.0
                face_tensor = torch.tensor(face).unsqueeze(0)
                encoding = resnet(face_tensor).detach().numpy().flatten()
                encodings.append(encoding.tolist())
        return encodings
    return []


# ✅ Recognize Faces
def recognize_faces(encodings):
    stored_faces = load_faces()
    # print(encodings)
    # print(f"Loaded {len(stored_faces)} faces from JSON.")
    # print(stored_faces)
    recognized_faces = []

    for encoding in encodings:
        best_match = {
            "jntu_no": "Unknown",
            "name": "Unknown",
            "distance":1,  # Threshold
        }

        for face in stored_faces:
            stored_encoding = np.array(face["face_encoding"], dtype=np.float32)
            distance = np.linalg.norm(stored_encoding - encoding)
            print(distance,face['name'])
            if distance < best_match["distance"]:
                best_match = {
                    "jntu_no": face["jntu_no"],
                    "name": face["name"],
                    "distance": float(distance),
                }

        if best_match["jntu_no"] != "Unknown":
            recognized_faces.append(best_match)

    return recognized_faces


# ✅ Anti-Spoofing: Detect if Image is from a Screen
def is_live_face(frame, prev_frame):
    if prev_frame is None:
        return True  # First frame

    # ✅ Check movement (Real faces move, screen images don't)
    diff = cv2.absdiff(frame, prev_frame)
    non_zero_count = np.count_nonzero(diff)
    return non_zero_count > 5000  # Threshold for movement


STATIC_IMAGE_PATH = "Images\KOWSHIK2.jpg"  
import os
import cv2
import time
FACE_DIR='FACES_CAPTURED/'
# ✅ API: Process Live Video
@app.get("/recognize_faces/")
def process_live_video():
    cap = cv2.VideoCapture(0)
    prev_frame = None

    ret, frame = cap.read()
    cap.release()

    if not ret:
        return JSONResponse(content={"error": "Failed to capture image"}, status_code=500)


    # frame = cv2.imread(STATIC_IMAGE_PATH)

    if frame is None:
        return JSONResponse(content={"error": f"Failed to load image from {STATIC_IMAGE_PATH}"}, status_code=500)

    # ✅ Save the captured image
    timestamp = time.strftime("%Y%m%d_%H%M%S")  # Unique timestamp filename
    image_path = os.path.join(FACE_DIR, f"captured_{timestamp}.jpg")
    cv2.imwrite(image_path, frame)


    frame_rgb = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)

    # ✅ Check if it's a real face, not a phone screen
    # if not is_live_face(frame, prev_frame):
    #     return JSONResponse(content={"error": "Fake image detected! Show a real face."}, status_code=403)
     
    encodings = detect_and_encode(frame_rgb)

    if not encodings:
        return JSONResponse(content={"faces": [], "image_saved": image_path})

    recognized_faces = recognize_faces(encodings)
    return JSONResponse(content={"faces": recognized_faces, "image_saved": image_path})


# ✅ Verify Face Against a Specific Person
def verify_person_encoding(name, jntu_no, encodings):
    stored_faces = load_faces()

    # Find the specific person's encoding
    person_face = next((face for face in stored_faces if face["name"] == name and face["jntu_no"] == jntu_no), None)

    if not person_face:
        return {"error": "Person not found in database"}

    stored_encoding = np.array(person_face["face_encoding"], dtype=np.float32)

    for encoding in encodings:
        distance = np.linalg.norm(stored_encoding - encoding)
        print(f"Distance: {distance} for {name}")

        if distance < 1:  # Threshold for a match
            return {
                "jntu_no": jntu_no,
                "name": name,
                "match": True,
                "distance": float(distance),
            }
    return {
        "jntu_no": jntu_no,
        "name": name,
        "match": False,
        "distance": float(distance),
    }


# ✅ API: Verify a Specific Person
@app.get("/verify_person/")
def verify_person(name: str, jntu_no: str):
    cap = cv2.VideoCapture(0)
    ret, frame = cap.read()
    cap.release()

    if not ret:
        return JSONResponse(content={"error": "Failed to capture image"}, status_code=500)

    frame_rgb = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)

    encodings = detect_and_encode(frame_rgb)

    if not encodings:
        return JSONResponse(content={"error": "No face detected"}, status_code=400)

    verification_result = verify_person_encoding(name, jntu_no, encodings)

    return JSONResponse(content={"verification": verification_result})


import uvicorn 
# ✅ Run FastAPI Server
if __name__ == "__main__":
    uvicorn.run("face_recognition_api:app", host="0.0.0.0", port=8020, reload=True)  # Change 8006 to 8010 or any free port



