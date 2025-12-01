-- Migration: Add ai_submission_id column to mgrnz_blueprint_subscriptions table
-- Date: 2025-12-01
-- Description: Adds the AI submission ID field to track which AI session generated each subscription

-- Add the ai_submission_id column if it doesn't exist
ALTER TABLE wp_mgrnz_blueprint_subscriptions 
ADD COLUMN IF NOT EXISTS ai_submission_id VARCHAR(255) NULL 
AFTER blueprint_id;

-- Add an index for faster lookups
CREATE INDEX IF NOT EXISTS idx_ai_submission_id 
ON wp_mgrnz_blueprint_subscriptions(ai_submission_id);

-- Note: If your WordPress installation uses a different table prefix than 'wp_',
-- you'll need to update the table name accordingly.
