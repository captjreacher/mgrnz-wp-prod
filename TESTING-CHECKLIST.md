# Quote Tracking Testing Checklist

## Pre-Deployment Check

- [x] Fixed submission_ref mismatch (removed fake ID generation)
- [x] Created direct API endpoint for quote tracking
- [x] Added JavaScript to call API on form submit
- [x] Committed and pushed all changes
- [ ] Wait for GitHub Actions deployment (~5 minutes)

**Check deployment:** https://github.com/captjreacher/mgrnz-wp-prod/actions

---

## Test 1: Verify Files Deployed

### Check Endpoint
```
https://mgrnz.com/wp-json/mgrnz/v1/mark-quote-requested
```
**Expected:** JSON error (route exists but needs POST data)

### Check JavaScript File
```
https://mgrnz.com/wp-content/themes/saaslauncher/templates/mark-quote-on-submit.js
```
**Expected:** JavaScript code (not 404)

### Check Handler File
```
https://mgrnz.com/wp-content/mu-plugins/simple-quote-request-handler.php
```
**Expected:** Blank page or PHP error (not 404)

---

## Test 2: Complete Fresh Wizard

### Step 1: Clear Browser Data
```javascript
// In browser console (F12)
localStorage.clear();
sessionStorage.clear();
location.reload();
```

### Step 2: Complete Wizard
1. Go to: https://mgrnz.com/start-using-ai/
2. Fill in all fields:
   - Goal: "Automate customer onboarding"
   - Workflow: "New customers fill form, get welcome email, added to CRM"
   - Tools: "Gmail, Google Sheets, Zapier"
   - Pain points: "Manual data entry, missed follow-ups"
3. Complete chat (if applicable)
4. Wait for blueprint to generate

### Step 3: Note Reference ID
Look for the blue box at top of blueprint:
```
Reference ID: REF-XXXXXXXX
```
**Write it down:** `REF-________________`

### Step 4: Check localStorage
```javascript
// In browser console
JSON.parse(localStorage.getItem('mgrnz_wizard_data'))
```
**Expected:**
```json
{
  "submission_ref": "REF-XXXXXXXX"
}
```
**Verify:** submission_ref matches the Reference ID shown on blueprint

---

## Test 3: Quote Page

### Step 1: Click "Request Quote"
Should navigate to: https://mgrnz.com/quote-my-workflow/

### Step 2: Check Console Logs
Open browser console (F12), look for:
```
🚀 Quote page script initialized
📦 Wizard data from localStorage: {"submission_ref":"REF-XXXXXXXX"}
✅ Using submission ref: REF-XXXXXXXX
[Quote Marker] Script loaded
[Quote Marker] Found MailerLite form
[Quote Marker] ✅ Attached to form, ready to mark quote on submit
```

### Step 3: Check Form Field
Find the "Blueprint ID" or "submission_ref" field in the form.

**Expected value:** `REF-XXXXXXXX` (same as blueprint)

**If different:** 
- ❌ Check console for errors
- ❌ Check if localStorage was cleared
- ❌ Check if quote page script ran

### Step 4: Verify Field is Read-Only
Try to edit the submission_ref field.

**Expected:** Can't edit (field is read-only)

---

## Test 4: Submit Quote Form

### Step 1: Fill Out Form
- Name: Test User
- Email: test@example.com
- Company: Test Company
- Message: This is a test quote request

### Step 2: Watch Console
Keep browser console open (F12)

### Step 3: Submit Form
Click "Submit" or "Request Quote" button

### Step 4: Check Console Logs
Should see:
```
[Quote Marker] Form submitted, marking quote as requested...
[Quote Marker] ✅ Quote request recorded: {success: true, ...}
```

**If you see errors:**
- Check the error message
- Verify endpoint is accessible
- Check network tab for failed requests

---

## Test 5: Verify in WordPress

### Step 1: Go to WordPress Admin
https://mgrnz.com/wp-admin/

### Step 2: Navigate to AI Submissions
Look for "AI Submissions" in the admin menu

### Step 3: Find Your Submission
Search for the Reference ID: `REF-XXXXXXXX`

### Step 4: Check "Quote Requested" Column
**Expected:** ✅ Yes

**If No:**
- Check if API call succeeded (console logs)
- Check WordPress debug log
- Run test script: https://mgrnz.com/test-quote-tracking.php

### Step 5: Check Submission Details
Click on the submission to view details.

**Should show:**
- Quote Requested: Yes
- Quote Requested At: [timestamp]

---

## Test 6: Test with Different Scenarios

### Scenario A: Direct URL Access
1. Copy the quote page URL
2. Open in new incognito window
3. Paste URL and visit
4. **Expected:** No submission_ref (user didn't complete wizard)
5. **Expected:** Console warning about missing submission_ref

### Scenario B: Multiple Submissions
1. Complete wizard again (new submission)
2. Note new Reference ID
3. Request quote
4. Verify new ID is used (not old one)

### Scenario C: Page Refresh
1. Complete wizard
2. Request quote
3. Refresh quote page
4. **Expected:** submission_ref still populated
5. Submit form again
6. **Expected:** Still works (idempotent)

---

## Troubleshooting Guide

### Issue: submission_ref field is empty

**Check:**
1. Did you complete the wizard?
2. Is localStorage enabled in browser?
3. Check console for errors
4. Check: `localStorage.getItem('mgrnz_wizard_data')`

**Fix:**
- Complete wizard again
- Enable localStorage in browser settings
- Try different browser

---

### Issue: submission_ref doesn't match blueprint

**Check:**
1. Console logs for warnings
2. Check if quote page script is generating new ID
3. Verify localStorage value

**Fix:**
- Should be fixed with latest deployment
- If still happening, check `wp-content/quote-page-blueprint-data-script.html`

---

### Issue: API call fails

**Check:**
1. Network tab in browser console
2. Response status code
3. Response body

**Common errors:**
- **404:** Endpoint not registered (deployment issue)
- **500:** PHP error in handler (check debug log)
- **CORS:** Shouldn't happen (same domain)

**Fix:**
- Wait for deployment to complete
- Check: https://mgrnz.com/wp-json/mgrnz/v1/mark-quote-requested
- Run test script: https://mgrnz.com/test-quote-tracking.php

---

### Issue: "Quote Requested" not updating

**Check:**
1. Did API call succeed? (console logs)
2. Is submission_ref correct?
3. Does submission exist in database?

**Debug:**
```bash
# Check debug log
ssh mgrnz@mgrnz.com "tail -50 /home/mgrnz/public_html/wp-content/debug.log | grep -i quote"
```

**Fix:**
- Verify submission exists with that Reference ID
- Check if API is finding the submission
- Run test script to verify endpoint works

---

## Success Criteria

✅ **All tests pass if:**
1. Reference ID on blueprint matches form field
2. Form submission triggers API call
3. Console shows success message
4. "Quote Requested" column shows "Yes" in WordPress
5. No errors in console or debug log

---

## Quick Test Command

Run this in browser console after completing wizard:

```javascript
// Quick test
const data = JSON.parse(localStorage.getItem('mgrnz_wizard_data'));
console.log('Submission Ref:', data?.submission_ref);

fetch('/wp-json/mgrnz/v1/mark-quote-requested', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({submission_ref: data?.submission_ref})
})
.then(r => r.json())
.then(d => console.log('API Response:', d));
```

**Expected output:**
```
Submission Ref: REF-XXXXXXXX
API Response: {success: true, message: "Quote request recorded", ...}
```

---

## Deployment Timeline

- **Committed:** ✅ Yes
- **Pushed:** ✅ Yes  
- **GitHub Actions:** In progress
- **ETA:** ~5 minutes from last push
- **Verify:** Check actions page

Once deployment completes, run through all tests above.
