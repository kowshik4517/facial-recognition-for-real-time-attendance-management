import cv2
import numpy as np
import torch
import json
import os
from facenet_pytorch import InceptionResnetV1, MTCNN

# Initialize Face Detection & Recognition Models
mtcnn = MTCNN(keep_all=True)
resnet = InceptionResnetV1(pretrained="vggface2", classify=False).eval()

# JSON file to store student data
JSON_FILE = "front end and python/faces.json"

# Load existing data or create a new JSON file
if os.path.exists(JSON_FILE):
    try:
        with open(JSON_FILE, "r") as file:
            student_data = json.load(file)
    except (json.JSONDecodeError, FileNotFoundError):
        student_data = {"students": []}
else:
    student_data = {"students": []}

# Function to Detect and Encode Faces
def detect_and_encode(image_path):
    if not os.path.exists(image_path):
        print(f"Error: Image {image_path} not found.")
        return []
    
    image = cv2.imread(image_path)
    if image is None:
        print(f"Error: Unable to read {image_path}.")
        return []
    
    image_rgb = cv2.cvtColor(image, cv2.COLOR_BGR2RGB)
    with torch.no_grad():
        boxes, _ = mtcnn.detect(image_rgb)
        if boxes is None or len(boxes) == 0:
            print(f"No face detected in {image_path}!")
            return []
    
    encodings = []
    for box in boxes:
        x1, y1, x2, y2 = map(int, box)
        face = image_rgb[y1:y2, x1:x2]
        face = cv2.resize(face, (160, 160))
        face = np.transpose(face, (2, 0, 1)).astype(np.float32) / 255.0
        face_tensor = torch.tensor(face).unsqueeze(0)
        encoding = resnet(face_tensor).detach().numpy().flatten()
        encodings.append(encoding.tolist())
    return encodings

# Function to Store a New Student's Face Embeddings in JSON
def store_face(jntu_no, name, year, semester, branch, section, image_path):
    encodings = detect_and_encode(image_path)
    if not encodings:
        return f"No face detected in {image_path}. Please try again."
    
    student_exists = False
    for student in student_data["students"]:
        if student["jntu_no"] == jntu_no:
            student_exists = True
            student["face_encoding"].extend(encodings)
            break
    
    if not student_exists:
        student_data["students"].append({
            "jntu_no": jntu_no,
            "name": name,
            "year": year,
            "semester": semester,
            "branch": branch,
            "section": section,
            "face_encoding": encodings,
        })
    
    # Save updated JSON file
    try:
        with open(JSON_FILE, "w") as file:
            json.dump(student_data, file, indent=4)
        return f"Stored {len(encodings)} face(s) for {name} (JNTU No: {jntu_no}) successfully!"
    except Exception as e:
        return f"Error writing to JSON file: {e}"

# List of students with images
students_list = []

# Add 64 students: 22341A4501 to 22341A4564
for i in range(1, 65):
    jntu_no = f"22341a45{str(i).zfill(2)}"
    name = f"Student{i}"
    year = 3
    semester = 6
    branch = "AIDS"
    section = "A"
    image_path = f"front end and python/2022-AIDS/{jntu_no}.jpg"
    students_list.append([jntu_no, name, year, semester, branch, section, image_path])

# Add 8 students: 23345A4501 to 23345A4508
for i in range(1, 9):
    jntu_no = f"23345a45{str(i).zfill(2)}"
    name = f"Student{64 + i}"
    year = 3
    semester = 6
    branch = "AIDS"
    section = "A"
    image_path = f"front end and python/2022-AIDS/{jntu_no}.jpg"
    students_list.append([jntu_no, name, year, semester, branch, section, image_path])

# Debug: Print and store face
for student in students_list:
    jntu_no, name, year, semester, branch, section, image_path = student
    result = store_face(jntu_no, name, year, semester, branch, section, image_path)
    print(f"{jntu_no} - {name}: {result}")


