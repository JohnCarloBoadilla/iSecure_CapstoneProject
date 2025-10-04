# TODO: Fix Visitor ID Tab Image Display and OCR Function

## Steps to Complete

1. **Edit `scripts/visitors.js`** (COMPLETED):
   - Updated `showVisitorDetails` function to set ID photo `src` to use the backend API `php/routes/fetch_visitor_image.php?visitor_id=...` instead of direct paths.
   - Simplified the ID tab logic to always use the API for image fetching and OCR, removing incorrect fallback paths.

2. **Verify `php/routes/fetch_visitor_image.php`** (COMPLETED):
   - Confirmed the script correctly handles image retrieval from multiple directories (uploads, app/services/ocr, public, images, root) and base64 data.
   - Added public/ and images/ directories to possible paths for image retrieval.

3. **Test the Flow**:
   - Open visitor details modal and navigate to the ID tab.
   - Verify ID photo displays without 404 errors.
   - Confirm OCR data is fetched and displayed correctly in the ID tab.

## Dependent Files
- `scripts/visitors.js` (primary edit)
- `php/routes/fetch_visitor_image.php` (verify functionality)

## Follow-up Steps
- After edits, test the visitor ID tab to ensure images load and OCR works.
- If issues persist, investigate image storage locations or database paths.
