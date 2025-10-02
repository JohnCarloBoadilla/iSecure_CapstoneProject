import threading
import cv2

class Camera:
    def __init__(self, camera_type="webcam", source=0):
        self.source = source
        self.cap = None
        self.running = False
        self.frame = None
        self.lock = threading.Lock()
        self.thread = None

    def start(self):
        if not self.running:
            self.cap = cv2.VideoCapture(self.source)
            self.running = True
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
        if not self.running:
            self.start()
        with self.lock:
            return self.frame.copy() if self.frame is not None else None

    def stop(self):
        if self.running:
            self.running = False
            if self.cap:
                self.cap.release()
            if self.thread:
                self.thread.join(timeout=1.0)
