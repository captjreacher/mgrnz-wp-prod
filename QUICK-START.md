# Quick Start Guide - AI Submission ID

## ✅ Implementation Complete!

The AI submission ID is now integrated into your subscription system.

## What You Need to Do

### Option 1: Just Use It (No MailerLite)
If you don't need MailerLite integration, **you're done!** The AI submission ID is already being saved to your database automatically.

### Option 2: Enable MailerLite Integration

**5-Minute Setup:**

1. **Get MailerLite API Key**
   - Go to: https://dashboard.mailerlite.com/integrations/api
   - Copy your API key

2. **Create Custom Field in MailerLite**
   - Go to: https://dashboard.mailerlite.com/subscribers/fields
   - Click "Create Field"
   - Name: `ai_submission_id`
   - Type: Text
   - Save

3. **Configure WordPress**
   - Go to: `http://mgrnz.local/wp-admin/options-general.php?page=mgrnz-mailerlite`
   - Or: Settings → MailerLite
   - Check "Enable Integration"
   - Paste your API key
   - Click "Save Changes"
   - Click "Test Connection" to verify

4. **Test It**
   - Complete the wizard at: `http://mgrnz.local/start-using-ai/`
   - Subscribe with your email
   - Check MailerLite to see the AI submission ID

## That's It!

The system will now:
- ✅ Save AI submission ID in WordPress database
- ✅ Sync to MailerLite (if enabled)
- ✅ Track which AI conversations lead to subscriptions

## Need Help?

- **Full Setup Guide:** See `MAILERLITE-SETUP-GUIDE.md`
- **Technical Details:** See `AI-SUBMISSION-ID-IMPLEMENTATION.md`
- **Implementation Summary:** See `IMPLEMENTATION-SUMMARY.md`

## Quick Test

```bash
# Check if column exists in database
mysql -u root -proot local -e "DESCRIBE wp_mgrnz_blueprint_subscriptions;"

# View recent subscriptions with AI IDs
mysql -u root -proot local -e "SELECT id, email, ai_submission_id FROM wp_mgrnz_blueprint_subscriptions ORDER BY id DESC LIMIT 5;"
```

---

**Status:** ✅ Ready to Use
**MailerLite:** Optional (configure when ready)
