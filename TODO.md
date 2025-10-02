# Integrate Face Recognition in Visitor Modal

## Pending Tasks
- [x] Add compare_faces function in app/services/face_recog/face_service.py to compare two images (live frame and selfie).
- [x] Add /compare/faces API endpoint in app/main.py to accept two images and return face recognition result.
- [x] Update php/routes/visitors.php to include camera video element, selfie display, and result container in facial tab.
- [x] Update scripts/visitors.js to:
  - Access camera and display feed in facial tab.
  - Capture current frame on "next" button click.
  - Send frame and selfie to backend API.
  - Display recognition result (match/no match).
- [ ] Test the integration to ensure camera access, API calls, and recognition work correctly.
