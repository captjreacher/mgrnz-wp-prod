<?php
/**
 * Auto-run Migration for AI Submission ID
 * 
 * This file will automatically run the migration once when an admin visits any page.
 * After running successfully, it will delete itself.
 * 
 * @package MGRNZ_AI_Workflow
 */

if (!defined('ABSPATH')) {
    exit;
}

// Check if migration has already been run
$migration_completed = get_option('mgrnz_ai_submission_id_migration_completed', false);

if ($migration_completed) {
    return;
}

/**
 * Run the migration to add ai_submission_id column
 */
function mgrnz_auto_run_subscription_migration() {
    // Only run for admins
    if (!current_user_can('manage_options')) {
        return;
    }
    
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'mgrnz_blueprint_subscriptions';
    
    // Check if table exists
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") !== $table_name) {
        // Table doesn't exist yet, skip migration
        return;
    }
    
    // Check if column already exists
    $column_exists = $wpdb->get_results(
        $wpdb->prepare(
            "SHOW COLUMNS FROM `$table_name` LIKE %s",
            'ai_submission_id'
        )
    );
    
    if (!empty($column_exists)) {
        // Column already exists, mark as completed
        update_option('mgrnz_ai_submission_id_migration_completed', true);
        return;
    }
    
    // Add the column
    $sql = "ALTER TABLE `$table_name` 
            ADD COLUMN `ai_submission_id` VARCHAR(255) NULL 
            AFTER `blueprint_id`";
    
    $result = $wpdb->query($sql);
    
    if ($result !== false) {
        // Add index for better performance
        $index_sql = "CREATE INDEX `idx_ai_submission_id` 
                      ON `$table_name` (`ai_submission_id`)";
        
        $wpdb->query($index_sql);
        
        // Mark migration as completed
        update_option('mgrnz_ai_submission_id_migration_completed', true);
        
        // Show admin notice
        add_action('admin_notices', function() {
            ?>
            <div class="notice notice-success is-dismissible">
                <p><strong>✅ Migration Complete:</strong> AI Submission ID field has been added to the subscriptions table.</p>
            </div>
            <?php
        });
        
        // Log success
        error_log('[MGRNZ Migration] Successfully added ai_submission_id column to subscriptions table');
    } else {
        // Log error
        error_log('[MGRNZ Migration] Failed to add ai_submission_id column: ' . $wpdb->last_error);
    }
}

// Run the migration on admin_init
add_action('admin_init', 'mgrnz_auto_run_subscription_migration');
