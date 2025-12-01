# MailerLite Webhook Setup & Testing Guide

## Overview

This guide will help you configure the MailerLite webhook so that when users submit the quote form, the "Quote Requested" column in AI Submissions gets populated.

## Prerequisites

✅ Deployment completed (check DEPLOYMENT-TEST.txt)
✅ MailerLite form is working and has `submission_ref` field
✅ JavaScript is auto-filling the `submission_ref` field

## Step 1: Get Your Webhook URL

### Production Webhook URL:
```
https://mgrnz.com/wp-json/mgrnz/v1/mailerlite-webhook
```

### Test Webhook URL (for testing):
```
https://mgrnz.com/wp-json/mgrnz/v1/test-webhook
```

## Step 2: Configure MailerLite Webhook

1. **Login to MailerLite Dashboard**
   - Go to: https://dashboard.mailerlite.com/

2. **Navigate to Webhooks**
   - Click: **Integrations** → **Webhooks**
   - Or direct link: https://dashboard.mailerlite.com/integrations/webhooks

3. **Add New Webhook**
   - Click **"Add Webhook"** button

4. **Configure Webhook**
   - **URL:** `https://mgrnz.com/wp-json/mgrnz/v1/mailerlite-webhook`
   - **Events:** Select **"Subscriber created"** or **"Subscriber added"**
   - **Status:** Enabled

5. **Save Webhook**
   - Click **"Save"** or **"Create"**

## Step 3: Test the Webhook

### Method 1: Test from WordPress Admin

1. Go to production WordPress admin
2. Navigate to **Settings → MailerLite**
3. Click **"Test Webhook Now"** button
4. Should see: ✅ Success! Webhook is working

### Method 2: Test from MailerLite

1. In MailerLite dashboard, find your webhook
2. Click **"Test"** or **"Send Test"** button
3. Check WordPress error log for webhook receipt

### Method 3: Submit Actual Form

1. Complete the AI wizard on production
2. Visit `/quote-my-workflow/`
3. Verify `submission_ref` field is filled
4. Submit the form
5. Check AI Submissions in WordPress
6. Should see "Quote Requested: ✓ Yes"

## Step 4: Verify It's Working

### Check 1: WordPress Error Log

Look for these log entries:
```
[MailerLite Webhook] ===== WEBHOOK RECEIVED =====
[MailerLite Webhook] ✅ Updated AI submission X with quote request
```

### Check 2: AI Submissions Table

1. Go to **AI Submissions** in WordPress admin
2. Find the submission with matching Ref ID
3. **Quote Requested** column should show: ✓ Yes
4. Click on submission to see full quote details

### Check 3: Submission Details

When you click on a submission that has a quote request, you should see:

**📋 Quote Request Details:**
- Requested At: [timestamp]
- Contact Name: [name from form]
- Contact Email: [email from form]
- Company: [company from form]
- Message: [message from form]

## Troubleshooting

### Issue: Webhook Not Firing

**Symptoms:**
- Form submits successfully
- No log entries in WordPress
- Quote Requested column stays empty

**Solutions:**

1. **Verify webhook is enabled in MailerLite**
   - Check webhook status is "Active" or "Enabled"

2. **Check webhook URL is correct**
   ```
   https://mgrnz.com/wp-json/mgrnz/v1/mailerlite-webhook
   ```
   NOT:
   - `http://` (must be https)
   - `/wp/wp-json/` (wrong path)
   - Missing `/mailerlite-webhook`

3. **Test webhook endpoint directly**
   ```bash
   curl -X POST https://mgrnz.com/wp-json/mgrnz/v1/test-webhook \
     -H "Content-Type: application/json" \
     -d '{
       "type": "subscriber.created",
       "data": {
         "email": "test@example.com",
         "fields": {
           "submission_ref": "sess_test123"
         }
       }
     }'
   ```

4. **Check MailerLite webhook logs**
   - In MailerLite dashboard, view webhook delivery logs
   - Look for failed deliveries or errors

### Issue: Webhook Fires But Column Stays Empty

**Symptoms:**
- Webhook log shows receipt
- But Quote Requested column doesn't update

**Solutions:**

1. **Check submission_ref matches**
   - The `submission_ref` in the form must match a Ref ID in AI Submissions
   - Case-sensitive match required

2. **Check WordPress error log**
   ```
   [MailerLite Webhook] ⚠️ No AI submission found for submission_ref: sess_xxx
   ```

3. **Verify AI submission exists**
   - Go to AI Submissions
   - Search for the Ref ID
   - Make sure it exists before submitting quote form

4. **Check field name in MailerLite**
   - Field must be named exactly: `submission_ref`
   - Not `submissionRef` or `submission-ref`

### Issue: submission_ref Field Not Populating

**Symptoms:**
- Form loads but `submission_ref` field is empty
- JavaScript not working

**Solutions:**

1. **Check browser console**
   ```javascript
   // Should see:
   [ML Populate] Script loaded
   [ML Populate] Found submission_ref in localStorage
   [ML Populate] ✅ Successfully populated submission_ref field
   ```

2. **Check localStorage**
   ```javascript
   // In browser console:
   JSON.parse(localStorage.getItem('mgrnz_wizard_data'))
   // Should show: { submission_ref: "sess_..." }
   ```

3. **Verify script is loaded**
   - View page source
   - Search for `populate-mailerlite-submission-ref.js`
   - Should be in the HTML

4. **Clear browser cache**
   - Hard refresh: Ctrl+F5 or Cmd+Shift+R

## Testing Checklist

- [ ] Deployment verified (DEPLOYMENT-TEST.txt accessible)
- [ ] Webhook configured in MailerLite
- [ ] Webhook URL is correct (https://mgrnz.com/wp-json/...)
- [ ] Webhook event is "Subscriber created"
- [ ] Webhook is enabled/active
- [ ] Test webhook fires successfully
- [ ] AI wizard completed (creates submission)
- [ ] Quote form visited (/quote-my-workflow/)
- [ ] submission_ref field auto-fills
- [ ] Form submitted successfully
- [ ] Webhook received (check logs)
- [ ] Quote Requested column shows ✓ Yes
- [ ] Quote details visible in submission

## Expected Data Flow

```
1. User completes wizard
   ↓
2. AI submission created with Ref ID (sess_abc123)
   ↓
3. User visits /quote-my-workflow/
   ↓
4. JavaScript reads Ref ID from localStorage
   ↓
5. JavaScript fills submission_ref field
   ↓
6. User fills form and submits
   ↓
7. MailerLite receives form with submission_ref
   ↓
8. MailerLite fires webhook to WordPress
   ↓
9. WordPress receives webhook
   ↓
10. WordPress finds AI submission by Ref ID
   ↓
11. WordPress updates submission with quote details
   ↓
12. Quote Requested column shows ✓ Yes
```

## Webhook Payload Example

What MailerLite sends to your webhook:

```json
{
  "type": "subscriber.created",
  "data": {
    "email": "john@example.com",
    "name": "John",
    "fields": {
      "last_name": "Doe",
      "company": "Acme Inc",
      "message": "I need a quote for...",
      "submission_ref": "sess_abc123xyz456"
    }
  }
}
```

## Success Indicators

✅ **Webhook Working:**
- Log shows: `[MailerLite Webhook] ✅ Updated AI submission`
- Quote Requested column shows: ✓ Yes
- Quote details visible in submission

✅ **JavaScript Working:**
- submission_ref field auto-fills
- Field is readonly/grayed out
- Console shows: `[ML Populate] ✅ Successfully populated`

✅ **Full Integration Working:**
- Complete wizard → submission created
- Visit quote page → field auto-fills
- Submit form → webhook fires
- Check admin → Quote Requested: ✓ Yes

## Support

If you're still having issues:

1. **Check WordPress error log:**
   ```
   tail -f /path/to/wp-content/debug.log | grep "MailerLite"
   ```

2. **Enable WordPress debug mode:**
   ```php
   // In wp-config.php:
   define('WP_DEBUG', true);
   define('WP_DEBUG_LOG', true);
   ```

3. **Test webhook manually:**
   - Use the test endpoint first
   - Check if WordPress can receive webhooks at all
   - Then test the actual webhook

4. **Check MailerLite webhook logs:**
   - View delivery attempts
   - Check for error responses
   - Verify payload format

---

**Once webhook is working, you'll have complete traceability from AI chat → blueprint → quote request!** 🎉
