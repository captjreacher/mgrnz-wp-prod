# AI Submission ID Cross-Population - Complete ✅

## What This Does

When a user completes the AI wizard and then requests a quote via the MailerLite form, the system now cross-populates the data so you can trace which AI submission the quote is for.

## Data Flow

```
1. User completes AI wizard
   ↓
2. Blueprint generated → AI Submission created
   - Stored in: ai_workflow_sub custom post type
   - Has: submission_ref (e.g., sess_abc123...)
   ↓
3. User visits /quote-my-workflow/
   ↓
4. JavaScript auto-fills submission_ref field
   - Reads from localStorage
   - Populates MailerLite form field
   - Makes field readonly
   ↓
5. User fills out quote form and submits
   ↓
6. MailerLite receives form with submission_ref
   ↓
7. MailerLite webhook fires → WordPress
   ↓
8. WordPress finds AI submission by submission_ref
   ↓
9. WordPress updates AI submission with quote details
   - Quote requested: ✓
   - Contact name
   - Contact email
   - Company
   - Message
```

## What You'll See

### In AI Submissions Admin

1. Go to **AI Submissions** in WordPress admin
2. New column: **Quote Requested**
   - Shows ✓ Yes if quote was requested
   - Shows — if no quote requested
3. Click on a submission to see full details
4. New section: **📋 Quote Request Details**
   - Requested At
   - Contact Name
   - Contact Email
   - Company
   - Message

### In MailerLite

1. Subscriber has `submission_ref` field populated
2. You can see which AI session they came from

## Setup Required

### 1. Configure MailerLite Webhook

1. Go to [MailerLite → Integrations → Webhooks](https://dashboard.mailerlite.com/integrations/webhooks)
2. Click "Add Webhook"
3. Paste this URL:
   ```
   https://your-site.com/wp-json/mgrnz/v1/mailerlite-webhook
   ```
4. Select event: **Subscriber created**
5. Save

### 2. Test the Flow

1. **Complete wizard:**
   ```
   http://mgrnz.local/start-using-ai/
   ```

2. **Generate blueprint** through chat

3. **Visit quote page:**
   ```
   http://mgrnz.local/quote-my-workflow/
   ```

4. **Check submission_ref field** - should be auto-filled

5. **Fill out and submit form**

6. **Check AI Submissions in WordPress:**
   - Find the submission by Ref ID
   - Should show "Quote Requested: ✓ Yes"
   - Click to view full quote details

## Files Modified/Created

### Created:
- `wp-content/mu-plugins/mailerlite-webhook-handler.php` - Webhook endpoint
- `wp-content/mu-plugins/enqueue-mailerlite-populate-script.php` - Auto-fill script loader
- `wp-content/themes/saaslauncher/templates/populate-mailerlite-submission-ref.js` - Auto-fill logic

### Modified:
- `wp-content/mu-plugins/includes/class-submission-cpt.php` - Added quote request column and details
- `wp-content/mu-plugins/mailerlite-integration.php` - Added webhook setup instructions

## Database Fields Added

### AI Submission Meta Fields:
- `_mgrnz_quote_requested` (boolean) - Whether quote was requested
- `_mgrnz_quote_requested_at` (datetime) - When quote was requested
- `_mgrnz_quote_contact_name` (string) - Contact name from form
- `_mgrnz_quote_contact_email` (string) - Contact email from form
- `_mgrnz_quote_company` (string) - Company from form
- `_mgrnz_quote_message` (text) - Message from form

## Troubleshooting

### Webhook Not Firing

**Check webhook URL:**
```
https://your-site.com/wp-json/mgrnz/v1/mailerlite-webhook
```

**Test manually:**
```bash
curl -X POST https://your-site.com/wp-json/mgrnz/v1/mailerlite-webhook \
  -H "Content-Type: application/json" \
  -d '{
    "type": "subscriber.created",
    "data": {
      "email": "test@example.com",
      "name": "Test User",
      "fields": {
        "submission_ref": "sess_test123"
      }
    }
  }'
```

**Check WordPress error log:**
```
tail -f wp-content/debug.log | grep "MailerLite Webhook"
```

### submission_ref Not Populating

**Check browser console:**
```javascript
// Should see:
[ML Populate] Script loaded
[ML Populate] Found submission_ref in localStorage: sess_...
[ML Populate] ✅ Successfully populated submission_ref field
```

**Check localStorage:**
```javascript
JSON.parse(localStorage.getItem('mgrnz_wizard_data'))
// Should show: { submission_ref: "sess_..." }
```

### Quote Details Not Showing

1. Check that webhook is configured in MailerLite
2. Verify webhook URL is correct
3. Check WordPress error logs
4. Test webhook manually (see above)

## Benefits

✅ **Complete Traceability** - Know exactly which AI conversation led to each quote request

✅ **Better Follow-up** - See the original workflow details when responding to quotes

✅ **Improved Analytics** - Track conversion from AI chat to quote request

✅ **Unified Data** - All information in one place in WordPress admin

✅ **No Manual Work** - Everything happens automatically

## Example Use Case

1. User completes wizard, gets blueprint for "automating daily reports"
2. User requests quote via MailerLite form
3. You receive quote request
4. You open AI Submissions in WordPress
5. You see "Quote Requested: ✓ Yes"
6. You click to view details
7. You see:
   - Original goal: "automate daily reporting"
   - Current workflow description
   - Tools they use
   - Pain points
   - Quote request details (name, email, company, message)
8. You have full context to provide accurate quote

---

**Status:** ✅ Complete and Ready to Use
**Requires:** MailerLite webhook configuration
**Testing:** Recommended before production use
