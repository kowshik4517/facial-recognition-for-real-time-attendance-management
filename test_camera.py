import cv2

cap = cv2.VideoCapture(0)
if cap.isOpened():
    print(f"✅ Camera found at index")
    cap.release()
else:
    print(f"❌ No camera at index")
