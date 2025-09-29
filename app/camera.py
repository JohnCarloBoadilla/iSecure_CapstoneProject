import threading
import cv2

class Camera:
    def __init__(self, camera_type="webcam", source=0):
        self.source = source
        self.cap = cv2.VideoCapture(self.source)
        self.running = True
        self.frame = None
        self.lock = threading.Lock()
        # Start thread to continuously read frames
        self.thread = threading.Thread(target=self._update_frame, daemon=True)
        self.thread.start()

    def _update_frame(self):
        while self.running:
            ret, frame = self.cap.read()
            if ret:
                with self.lock:
                    self.frame = frame
            else:
                self.frame = None

    def read_frame(self):
        with self.lock:
            return self.frame.copy() if self.frame is not None else None

    def stop(self):
        self.running = False
        self.cap.release()