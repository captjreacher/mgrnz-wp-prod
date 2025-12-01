<?php
/**
 * Database Migration Runner
 * 
 * Run this file to add the ai_submission_id column to the subscriptions table.
 * 
 * Usage:
 * 1. Via browser: Navigate to /wp-content/mu-plugins/migrations/run-migration.php
 * 2. Via WP-CLI: wp eval-file wp-content/mu-plugins/migrations/run-migration.php
 * 3. Via admin: Add this to functions.php temporarily and visit any admin page
 * 
 * @package MGRNZ_AI_Workflow
 */

// Load WordPress if not already loaded
if (!defined('ABSPATH')) {
    // Try to find wp-load.php
    $wp_load_paths = [
        dirname(__FILE__) . '/../../../../wp-load.php',
        dirname(__FILE__) . '/../../../wp-load.php',
        dirname(dirname(dirname(dirname(__FILE__)))) . '/wp-load.php',
    ];
    
    $loaded = false;
    foreach ($wp_load_paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            $loaded = true;
            break;
        }
    }
    
    if (!$loaded) {
        die('Error: Could not locate wp-load.php. Please run this script from WordPress admin or use the auto-run-migration.php file instead.');
    }
}

// Security check - only allow admins
if (!current_user_can('manage_options') && !defined('WP_CLI')) {
    wp_die('Unauthorized access');
}

/**
 * Run the migration to add ai_submission_id column
 */
function mgrnz_run_subscription_migration() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'mgrnz_blueprint_subscriptions';
    
    // Check if table exists
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") !== $table_name) {
        return [
            'success' => false,
            'message' => "Table $table_name does not exist. Please create the table first."
        ];
    }
    
    // Check if column already exists
    $column_exists = $wpdb->get_results(
        $wpdb->prepare(
            "SHOW COLUMNS FROM `$table_name` LIKE %s",
            'ai_submission_id'
        )
    );
    
    if (!empty($column_exists)) {
        return [
            'success' => true,
            'message' => 'Column ai_submission_id already exists. No migration needed.',
            'already_exists' => true
        ];
    }
    
    // Add the column
    $sql = "ALTER TABLE `$table_name` 
            ADD COLUMN `ai_submission_id` VARCHAR(255) NULL 
            AFTER `blueprint_id`";
    
    $result = $wpdb->query($sql);
    
    if ($result === false) {
        return [
            'success' => false,
            'message' => 'Failed to add column: ' . $wpdb->last_error
        ];
    }
    
    // Add index for better performance
    $index_sql = "CREATE INDEX `idx_ai_submission_id` 
                  ON `$table_name` (`ai_submission_id`)";
    
    $wpdb->query($index_sql); // Don't fail if index creation fails
    
    return [
        'success' => true,
        'message' => 'Successfully added ai_submission_id column to subscriptions table.',
        'table' => $table_name
    ];
}

// Run the migration
$result = mgrnz_run_subscription_migration();

// Output result
if (defined('WP_CLI')) {
    // WP-CLI output
    if ($result['success']) {
        WP_CLI::success($result['message']);
    } else {
        WP_CLI::error($result['message']);
    }
} else {
    // Browser output
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>MGRNZ Migration Runner</title>
        <style>
            body {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                max-width: 800px;
                margin: 50px auto;
                padding: 20px;
                background: #f5f5f5;
            }
            .container {
                background: white;
                padding: 30px;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            .success {
                padding: 15px;
                background: #d4edda;
                border: 1px solid #c3e6cb;
                border-radius: 4px;
                color: #155724;
                margin: 20px 0;
            }
            .error {
                padding: 15px;
                background: #f8d7da;
                border: 1px solid #f5c6cb;
                border-radius: 4px;
                color: #721c24;
                margin: 20px 0;
            }
            .info {
                padding: 15px;
                background: #d1ecf1;
                border: 1px solid #bee5eb;
                border-radius: 4px;
                color: #0c5460;
                margin: 20px 0;
            }
            h1 {
                color: #333;
                margin-top: 0;
            }
            code {
                background: #f4f4f4;
                padding: 2px 6px;
                border-radius: 3px;
                font-family: monospace;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>🔧 MGRNZ Database Migration</h1>
            <p>Migration: Add <code>ai_submission_id</code> to subscriptions table</p>
            
            <?php if ($result['success']): ?>
                <div class="<?php echo isset($result['already_exists']) ? 'info' : 'success'; ?>">
                    <strong><?php echo isset($result['already_exists']) ? 'ℹ️ Info:' : '✅ Success:'; ?></strong>
                    <?php echo esc_html($result['message']); ?>
                    <?php if (isset($result['table'])): ?>
                        <br><small>Table: <code><?php echo esc_html($result['table']); ?></code></small>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="error">
                    <strong>❌ Error:</strong>
                    <?php echo esc_html($result['message']); ?>
                </div>
            <?php endif; ?>
            
            <p><a href="<?php echo admin_url(); ?>">← Back to WordPress Admin</a></p>
        </div>
    </body>
    </html>
    <?php
}
