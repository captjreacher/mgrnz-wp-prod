# MailerLite Integration Setup Guide

## Overview

The AI submission ID is now automatically synced to MailerLite when users subscribe to download their blueprint.

## Setup Steps

### 1. Get Your MailerLite API Key

1. Log in to [MailerLite Dashboard](https://dashboard.mailerlite.com/)
2. Go to **Integrations → API**
3. Copy your API key

### 2. Create Custom Field in MailerLite

1. Go to [MailerLite → Subscribers → Fields](https://dashboard.mailerlite.com/subscribers/fields)
2. Click **"Create Field"**
3. Enter field details:
   - **Field name:** `ai_submission_id`
   - **Field type:** Text
4. Click **Save**

### 3. Configure WordPress Integration

1. Go to WordPress Admin: `http://mgrnz.local/wp-admin/`
2. Navigate to **Settings → MailerLite**
3. Enter your settings:
   - ✅ **Enable Integration:** Check this box
   - **API Key:** Paste your MailerLite API key
   - **Group ID:** (Optional) Enter a group ID if you want subscribers added to a specific group
4. Click **Save Changes**

### 4. Test the Integration

1. On the same settings page, click **"Test Connection"**
2. You should see: ✅ Connection successful!

### 5. Test with a Real Subscription

1. Complete the wizard at `http://mgrnz.local/start-using-ai/`
2. Go through the chat and generate a blueprint
3. Enter your name and email to download
4. Check MailerLite dashboard to verify:
   - New subscriber was added
   - The `ai_submission_id` field is populated

## What Gets Synced

When someone subscribes, the following data is sent to MailerLite:

```json
{
  "email": "user@example.com",
  "fields": {
    "name": "John Doe",
    "ai_submission_id": "sess_abc123xyz456..."
  }
}
```

## Viewing AI Submission IDs in MailerLite

1. Go to **Subscribers** in MailerLite
2. Click on any subscriber
3. Scroll to **Custom Fields**
4. You'll see the `ai_submission_id` field with the session ID

## Using AI Submission ID for Segmentation

You can create segments based on AI submission ID:

1. Go to **Subscribers → Segments**
2. Create a new segment
3. Add condition: `ai_submission_id` → `is not empty`
4. This gives you all subscribers who came through the AI wizard

## Troubleshooting

### Integration Not Working

1. **Check API Key:**
   - Make sure you copied the full API key
   - No extra spaces before/after

2. **Check Custom Field:**
   - Field name must be exactly: `ai_submission_id`
   - Field type must be: Text

3. **Check WordPress Logs:**
   ```bash
   # View error log
   tail -f wp-content/debug.log
   ```
   
   Look for lines containing `[MailerLite]`

### Subscribers Not Appearing

1. Check if integration is enabled in Settings → MailerLite
2. Test the connection on the settings page
3. Check WordPress error logs for sync failures

### Custom Field Not Populating

1. Verify the field name is exactly `ai_submission_id` (case-sensitive)
2. Check that the field type is "Text"
3. Try creating a test subscription and check the logs

## Advanced: Getting Group ID

If you want to add subscribers to a specific group:

1. Go to **Subscribers → Groups** in MailerLite
2. Click on the group you want to use
3. Look at the URL: `https://dashboard.mailerlite.com/subscribers/groups/12345678`
4. The number at the end (`12345678`) is your Group ID
5. Enter this in WordPress Settings → MailerLite → Group ID

## Disabling the Integration

To temporarily disable without removing your API key:

1. Go to **Settings → MailerLite**
2. Uncheck **"Enable Integration"**
3. Click **Save Changes**

Subscriptions will still be saved in WordPress, but won't sync to MailerLite.

## Data Flow

```
User Completes Wizard
        ↓
Generates Blueprint
        ↓
Enters Name & Email
        ↓
WordPress Saves to Database
   (with ai_submission_id)
        ↓
Syncs to MailerLite
   (includes ai_submission_id)
        ↓
Sends Email with Blueprint
```

## Support

If you encounter issues:

1. Check WordPress error logs
2. Test the connection on Settings → MailerLite
3. Verify your API key is valid
4. Ensure the custom field exists in MailerLite

## Files Modified

- `wp-content/mu-plugins/mailerlite-integration.php` - New MailerLite integration
- `wp-content/mu-plugins/mgrnz-ai-workflow-endpoint.php` - Added sync call after subscription
- Database table `wp_mgrnz_blueprint_subscriptions` - Includes `ai_submission_id` column
