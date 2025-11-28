# PDF Generation Testing Instructions

## Quick Test (Already Done ✅)
The automated test shows PDF generation IS working:
- Test endpoint created a 9.7KB PDF file
- PDF contains actual content (not blank)
- File structure is valid

## Test the Full Wizard Flow

### Option 1: Test with Real Wizard
1. Go to your wizard page (wherever it's hosted on your site)
2. Fill out steps 1-3 and complete the chat
3. Click "Generate Blueprint"
4. When the blueprint appears, click "Download My Blueprint"
5. Enter name and email
6. Check the browser console (F12) for any errors
7. Check `wp-content/debug.log` for PDF generation logs

### Option 2: Test API Directly
Run this in your browser console on any page of your site:

```javascript
// Test the subscribe-blueprint endpoint directly
fetch('/wp-json/mgrnz/v1/subscribe-blueprint', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'X-WP-Nonce': wpApiSettings.nonce
  },
  body: JSON.stringify({
    session_id: 'test_' + Date.now(),
    name: 'Test User',
    email: 'test@example.com',
    blueprint_data: {
      content: '<h2>Test Blueprint</h2><p>This is test content.</p><ul><li>Item 1</li><li>Item 2</li></ul>'
    }
  })
})
.then(r => r.json())
.then(data => {
  console.log('Response:', data);
  if (data.download_url) {
    window.open(data.download_url, '_blank');
  }
});
```

### Option 3: Use Test Endpoint
Simply visit: `http://mgrnz.local/wp-json/mgrnz/v1/test-pdf`

This will generate a test PDF and return the download URL.

## Check the Logs

After testing, check the logs:
```bash
Get-Content wp-content\debug.log -Tail 50 | Select-String "PDF Generator"
```

## What to Look For

### If PDF is Blank:
1. Check if `blueprint_data['content']` is empty in logs
2. Check if TCPDF is loading (should see "Using TCPDF" in logs)
3. Check if content is being cleaned too aggressively
4. Check file size - if < 1KB, likely blank

### If Download Fails:
1. Check browser console for JavaScript errors
2. Check Network tab for failed API calls
3. Verify `wp-content/uploads/blueprints/` directory exists and is writable
4. Check for CORS or nonce errors

## Current Status
✅ PDF generation works (test endpoint successful)
✅ TCPDF is installed and functional
✅ File writing works
❓ Need to test with actual wizard flow to see if blueprint content is being passed correctly
