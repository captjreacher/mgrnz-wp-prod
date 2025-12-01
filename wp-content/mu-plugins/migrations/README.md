# Database Migrations

This directory contains database migration scripts for the MGRNZ AI Workflow system.

## Current Migrations

### Add AI Submission ID to Subscriptions (2025-12-01)

**Purpose:** Adds the `ai_submission_id` field to the `mgrnz_blueprint_subscriptions` table to track which AI session generated each subscription.

**Files:**
- `add-ai-submission-id-to-subscriptions.sql` - Raw SQL migration
- `run-migration.php` - PHP migration runner

**How to Run:**

#### Option 1: Via Browser
1. Navigate to: `/wp-content/mu-plugins/migrations/run-migration.php`
2. The migration will run automatically and show results

#### Option 2: Via WP-CLI
```bash
wp eval-file wp-content/mu-plugins/migrations/run-migration.php
```

#### Option 3: Via MySQL/phpMyAdmin
1. Open your database management tool
2. Run the SQL from `add-ai-submission-id-to-subscriptions.sql`
3. Update the table prefix if needed (default is `wp_`)

**What Changed:**
- Added `ai_submission_id` VARCHAR(255) column to `mgrnz_blueprint_subscriptions` table
- Added index on `ai_submission_id` for better query performance
- Updated `mgrnz_handle_subscribe_blueprint()` function to store the session ID

**Benefits:**
- Track which AI conversation led to each subscription
- Better analytics and reporting
- Can sync this data to MailerLite or other email marketing platforms
- Enables follow-up based on specific AI interactions

## Rollback

If you need to remove the column:

```sql
ALTER TABLE wp_mgrnz_blueprint_subscriptions DROP COLUMN ai_submission_id;
DROP INDEX idx_ai_submission_id ON wp_mgrnz_blueprint_subscriptions;
```

## Notes

- The migration is safe to run multiple times (it checks if the column exists)
- Existing records will have `NULL` for `ai_submission_id`
- New subscriptions will automatically include the AI submission ID
