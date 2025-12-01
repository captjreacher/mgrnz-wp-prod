<?php
/**
 * Create Blueprint Subscriptions Table
 * 
 * This ensures the table is created with the ai_submission_id field included
 * 
 * @package MGRNZ_AI_Workflow
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Create the subscriptions table if it doesn't exist
 */
function mgrnz_create_subscriptions_table() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'mgrnz_blueprint_subscriptions';
    $charset_collate = $wpdb->get_charset_collate();
    
    // Check if table already exists
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name) {
        return; // Table already exists
    }
    
    $sql = "CREATE TABLE $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        email varchar(255) NOT NULL,
        subscription_type varchar(50) NOT NULL DEFAULT 'blueprint_download',
        blueprint_id bigint(20) DEFAULT NULL,
        ai_submission_id varchar(255) DEFAULT NULL,
        subscribed_at datetime NOT NULL,
        download_count int(11) DEFAULT 0,
        last_download_at datetime DEFAULT NULL,
        PRIMARY KEY  (id),
        KEY idx_email (email),
        KEY idx_blueprint_id (blueprint_id),
        KEY idx_ai_submission_id (ai_submission_id),
        KEY idx_subscribed_at (subscribed_at)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    
    // Log table creation
    error_log('[MGRNZ] Created blueprint_subscriptions table with ai_submission_id field');
}

// Create table on plugin load
add_action('plugins_loaded', 'mgrnz_create_subscriptions_table');

// Also create on admin_init as a backup
add_action('admin_init', 'mgrnz_create_subscriptions_table');
