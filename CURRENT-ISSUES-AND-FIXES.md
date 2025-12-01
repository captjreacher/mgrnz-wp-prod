# Current Issues and Fixes

## Status Summary

### ✅ Issue 1: Back Button on Quote Page - FIXED
**Problem:** Users couldn't navigate back from quote page to blueprint  
**Solution:** Added back button script that appears above MailerLite form  
**Files Changed:**
- `wp-content/themes/saaslauncher/templates/add-back-button-to-quote-page.js`
- `wp-content/mu-plugins/enqueue-mailerlite-populate-script.php`

**Status:** Committed and ready to deploy

---

### ⚠️ Issue 2: Webhook Not Firing - NEEDS CONFIGURATION
**Problem:** MailerLite form submissions not triggering webhook  
**Root Cause:** Webhook not configured in MailerLite dashboard  

**Solution Steps:**

1. **Test the webhook endpoint first:**
   ```
   Visit: https://mgrnz.com/test-webhook-endpoint.php
   ```
   This will verify the endpoint is working.

2. **Configure MailerLite:**
   - Go to: https://dashboard.mailerlite.com/integrations/webhooks
   - Click "Add Webhook"
   - URL: `https://mgrnz.com/wp-json/mgrnz/v1/mailerlite-webhook`
   - Event: **Subscriber created** or **Form submitted**
   - Save

3. **Test the webhook:**
   - Submit the quote form
   - Check WordPress debug log for webhook entries
   - Check AI Submissions for "Quote Requested" column

**Files to Deploy:**
- `test-webhook-endpoint.php` (new test file)

---

### 🔍 Issue 3: Rogue '>' Character in submission_ref - INVESTIGATING
**Problem:** submission_ref field contains a '>' character causing syntax errors  
**Investigation:**
- Not found in our JavaScript code ✓
- Not found in PHP webhook handler ✓
- Not found in form population scripts ✓

**Likely Causes:**
1. MailerLite form embed code contains HTML entities
2. MailerLite is encoding the field value
3. Browser auto-fill or extension interference

**Next Steps:**
1. Check MailerLite form builder for HTML in field configuration
2. Add sanitization to webhook handler
3. Test with different browsers
4. Check browser console for actual value being sent

**Temporary Fix - Add Sanitization:**
Add this to `wp-content/mu-plugins/mailerlite-webhook-handler.php` after line 54:

```php
// Sanitize submission_ref to remove any HTML entities or special chars
$submission_ref = preg_replace('/[^A-Z0-9\-_]/i', '', $submission_ref);
```

---

## Testing Checklist

### Before Deploying:
- [ ] Commit all changes
- [ ] Push to GitHub
- [ ] Run deployment script

### After Deploying:
- [ ] Visit test-webhook-endpoint.php to verify endpoint works
- [ ] Configure MailerLite webhook in dashboard
- [ ] Test quote form submission
- [ ] Check debug log for webhook entries
- [ ] Verify "Quote Requested" column populates
- [ ] Test back button on quote page

---

## Quick Commands

### Deploy Changes:
```powershell
.\deploy-wizard-to-production.ps1
```

### Check Debug Log:
```powershell
ssh mgrnz@mgrnz.com "tail -50 /home/mgrnz/public_html/wp-content/debug.log | grep -i mailerlite"
```

### Test Webhook Endpoint:
```
https://mgrnz.com/test-webhook-endpoint.php
```

---

## Files Modified This Session

1. `wp-content/themes/saaslauncher/templates/add-back-button-to-quote-page.js` - NEW
2. `wp-content/mu-plugins/enqueue-mailerlite-populate-script.php` - MODIFIED
3. `test-webhook-endpoint.php` - NEW
4. `CURRENT-ISSUES-AND-FIXES.md` - NEW (this file)

---

## Next Actions

1. **Deploy the back button fix:**
   ```powershell
   .\deploy-wizard-to-production.ps1
   ```

2. **Test webhook endpoint:**
   Visit https://mgrnz.com/test-webhook-endpoint.php

3. **Configure MailerLite webhook:**
   Follow instructions in WEBHOOK-SETUP-AND-TESTING.md

4. **Add sanitization for '>' character:**
   If issue persists, add the sanitization code to webhook handler

5. **Test end-to-end:**
   - Complete wizard
   - View blueprint
   - Click "Request Quote"
   - Fill form
   - Submit
   - Verify webhook fired
   - Check AI Submissions table
