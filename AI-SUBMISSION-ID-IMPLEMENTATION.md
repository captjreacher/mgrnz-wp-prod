# AI Submission ID Implementation Guide

## Overview

The AI submission ID has been successfully added to the ML (MailerLite) form / subscription system. This allows you to track which AI conversation session led to each blueprint subscription.

## What Was Changed

### 1. Database Schema
- **New Column:** `ai_submission_id` added to `mgrnz_blueprint_subscriptions` table
- **Type:** VARCHAR(255)
- **Purpose:** Stores the session ID from the AI conversation

### 2. Backend Code Updates

#### File: `wp-content/mu-plugins/mgrnz-ai-workflow-endpoint.php`

**Function:** `mgrnz_handle_subscribe_blueprint()`

**Changes:**
- Added `ai_submission_id` to the database insert statement
- The session ID is now stored when a user subscribes to download their blueprint
- Added `ai_submission_id` to the API response

**Before:**
```php
$result = $wpdb->insert(
    $table_name,
    [
        'name' => $name,
        'email' => $email,
        'subscription_type' => 'blueprint_download',
        'blueprint_id' => $blueprint_id,
        'subscribed_at' => current_time('mysql'),
        'download_count' => 0
    ],
    ['%s', '%s', '%s', '%d', '%s', '%d']
);
```

**After:**
```php
$result = $wpdb->insert(
    $table_name,
    [
        'name' => $name,
        'email' => $email,
        'subscription_type' => 'blueprint_download',
        'blueprint_id' => $blueprint_id,
        'ai_submission_id' => $session_id, // NEW: AI submission ID
        'subscribed_at' => current_time('mysql'),
        'download_count' => 0
    ],
    ['%s', '%s', '%s', '%d', '%s', '%s', '%d']
);
```

### 3. API Response
The API now returns the `ai_submission_id` in the response:

```json
{
    "success": true,
    "download_url": "...",
    "subscription_id": 123,
    "ai_submission_id": "sess_abc123xyz"
}
```

## How to Deploy

### Step 1: Run the Database Migration

Choose one of these methods:

#### Option A: Via Browser (Easiest)
1. Navigate to: `https://your-site.com/wp-content/mu-plugins/migrations/run-migration.php`
2. You'll see a success message if the migration completes

#### Option B: Via WP-CLI
```bash
wp eval-file wp-content/mu-plugins/migrations/run-migration.php
```

#### Option C: Via MySQL/phpMyAdmin
```sql
ALTER TABLE wp_mgrnz_blueprint_subscriptions 
ADD COLUMN ai_submission_id VARCHAR(255) NULL 
AFTER blueprint_id;

CREATE INDEX idx_ai_submission_id 
ON wp_mgrnz_blueprint_subscriptions(ai_submission_id);
```

### Step 2: Deploy the Code
The updated PHP file is already in place:
- `wp-content/mu-plugins/mgrnz-ai-workflow-endpoint.php`

No additional deployment steps needed - the changes are live once the file is updated.

## Usage Examples

### 1. Query Subscriptions by AI Session
```php
global $wpdb;
$table_name = $wpdb->prefix . 'mgrnz_blueprint_subscriptions';

$subscriptions = $wpdb->get_results($wpdb->prepare(
    "SELECT * FROM $table_name WHERE ai_submission_id = %s",
    'sess_abc123xyz'
));
```

### 2. Export for MailerLite
```php
global $wpdb;
$table_name = $wpdb->prefix . 'mgrnz_blueprint_subscriptions';

$subscribers = $wpdb->get_results(
    "SELECT 
        email,
        name,
        ai_submission_id,
        subscribed_at
    FROM $table_name
    WHERE ai_submission_id IS NOT NULL
    ORDER BY subscribed_at DESC"
);

// Format for MailerLite import
foreach ($subscribers as $sub) {
    // Send to MailerLite API with custom field
    $mailerlite_data = [
        'email' => $sub->email,
        'name' => $sub->name,
        'fields' => [
            'ai_submission_id' => $sub->ai_submission_id
        ]
    ];
}
```

### 3. Analytics Query
```php
// Count subscriptions per AI session
global $wpdb;
$table_name = $wpdb->prefix . 'mgrnz_blueprint_subscriptions';

$stats = $wpdb->get_results(
    "SELECT 
        ai_submission_id,
        COUNT(*) as subscription_count,
        MIN(subscribed_at) as first_subscription,
        MAX(subscribed_at) as last_subscription
    FROM $table_name
    WHERE ai_submission_id IS NOT NULL
    GROUP BY ai_submission_id
    ORDER BY subscription_count DESC"
);
```

## Integration with MailerLite

To sync this data to MailerLite, you can:

1. **Create a Custom Field in MailerLite:**
   - Field name: `ai_submission_id`
   - Field type: Text

2. **Update Your MailerLite Integration:**
   ```php
   // Example MailerLite API integration
   function sync_to_mailerlite($email, $name, $ai_submission_id) {
       $api_key = get_option('mailerlite_api_key');
       
       $data = [
           'email' => $email,
           'name' => $name,
           'fields' => [
               'ai_submission_id' => $ai_submission_id
           ]
       ];
       
       wp_remote_post('https://api.mailerlite.com/api/v2/subscribers', [
           'headers' => [
               'X-MailerLite-ApiKey' => $api_key,
               'Content-Type' => 'application/json'
           ],
           'body' => json_encode($data)
       ]);
   }
   ```

3. **Add to Subscription Handler:**
   In `mgrnz_handle_subscribe_blueprint()`, after the database insert:
   ```php
   // Sync to MailerLite
   if ($subscription_id) {
       sync_to_mailerlite($email, $name, $session_id);
   }
   ```

## Testing

### Test the Implementation

1. **Complete the wizard:**
   - Go through the AI workflow wizard
   - Complete the chat conversation
   - Generate a blueprint

2. **Subscribe to download:**
   - Enter name and email
   - Click download

3. **Verify in database:**
   ```sql
   SELECT * FROM wp_mgrnz_blueprint_subscriptions 
   ORDER BY id DESC LIMIT 1;
   ```
   
   You should see the `ai_submission_id` populated with the session ID.

## Troubleshooting

### Column Already Exists Error
If you see "Column already exists", the migration has already been run. This is safe to ignore.

### Permission Denied
Make sure you're logged in as an admin when running the browser-based migration.

### MailerLite Sync Not Working
1. Check that your MailerLite API key is configured
2. Verify the custom field exists in MailerLite
3. Check WordPress error logs for API errors

## Benefits

✅ **Better Tracking:** Know which AI conversations lead to subscriptions
✅ **Improved Analytics:** Analyze conversion rates by conversation quality
✅ **Personalized Follow-up:** Send targeted emails based on AI interaction
✅ **MailerLite Integration:** Sync AI session data to your email marketing platform
✅ **Data-Driven Optimization:** Identify which AI responses drive the most engagement

## Support

If you encounter any issues:
1. Check the WordPress error log
2. Verify database permissions
3. Ensure the table exists before running migration
4. Contact your development team for assistance
