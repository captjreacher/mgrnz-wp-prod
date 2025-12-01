<?php
/**
 * Migration Admin Page
 * 
 * Provides a simple admin interface to run database migrations
 * 
 * @package MGRNZ_AI_Workflow
 */

if (!defined('ABSPATH')) {
    exit;
}

// Add admin menu
add_action('admin_menu', function() {
    add_management_page(
        'MGRNZ Migrations',
        'MGRNZ Migrations',
        'manage_options',
        'mgrnz-migrations',
        'mgrnz_render_migration_page'
    );
});

/**
 * Render the migration admin page
 */
function mgrnz_render_migration_page() {
    // Handle form submission
    if (isset($_POST['run_migration']) && check_admin_referer('mgrnz_run_migration')) {
        $result = mgrnz_run_ai_submission_migration();
        
        if ($result['success']) {
            echo '<div class="notice notice-success"><p>' . esc_html($result['message']) . '</p></div>';
        } else {
            echo '<div class="notice notice-error"><p>' . esc_html($result['message']) . '</p></div>';
        }
    }
    
    // Check current status
    global $wpdb;
    $table_name = $wpdb->prefix . 'mgrnz_blueprint_subscriptions';
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;
    
    $column_exists = false;
    if ($table_exists) {
        $column_exists = !empty($wpdb->get_results(
            $wpdb->prepare("SHOW COLUMNS FROM `$table_name` LIKE %s", 'ai_submission_id')
        ));
    }
    
    ?>
    <div class="wrap">
        <h1>🔧 MGRNZ Database Migrations</h1>
        
        <div class="card" style="max-width: 800px;">
            <h2>Add AI Submission ID to Subscriptions</h2>
            
            <table class="widefat" style="margin: 20px 0;">
                <tr>
                    <th style="width: 200px;">Table Status:</th>
                    <td>
                        <?php if ($table_exists): ?>
                            <span style="color: #00a32a;">✓ Table exists</span>
                        <?php else: ?>
                            <span style="color: #d63638;">✗ Table not found</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th>Column Status:</th>
                    <td>
                        <?php if ($column_exists): ?>
                            <span style="color: #00a32a;">✓ Column exists (Migration complete)</span>
                        <?php else: ?>
                            <span style="color: #dba617;">○ Column not found (Migration needed)</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th>Table Name:</th>
                    <td><code><?php echo esc_html($table_name); ?></code></td>
                </tr>
            </table>
            
            <?php if ($table_exists && !$column_exists): ?>
                <form method="post" style="margin: 20px 0;">
                    <?php wp_nonce_field('mgrnz_run_migration'); ?>
                    <p>
                        <button type="submit" name="run_migration" class="button button-primary button-large">
                            Run Migration Now
                        </button>
                    </p>
                    <p class="description">
                        This will add the <code>ai_submission_id</code> column to the subscriptions table.
                        The migration is safe to run multiple times.
                    </p>
                </form>
            <?php elseif (!$table_exists): ?>
                <div class="notice notice-warning inline">
                    <p>
                        <strong>Note:</strong> The subscriptions table doesn't exist yet. 
                        It will be created automatically when the first subscription is made.
                        The migration will run automatically after that.
                    </p>
                </div>
            <?php else: ?>
                <div class="notice notice-success inline">
                    <p>
                        <strong>✓ Migration Complete!</strong> The AI submission ID field is ready to use.
                    </p>
                </div>
            <?php endif; ?>
            
            <hr style="margin: 30px 0;">
            
            <h3>What This Migration Does</h3>
            <ul>
                <li>Adds <code>ai_submission_id</code> column to track which AI session generated each subscription</li>
                <li>Creates an index for better query performance</li>
                <li>Enables integration with MailerLite and other email marketing platforms</li>
            </ul>
            
            <h3>Documentation</h3>
            <p>
                <a href="<?php echo esc_url(content_url('mu-plugins/migrations/README.md')); ?>" target="_blank">
                    View Migration README
                </a>
                |
                <a href="<?php echo esc_url(content_url('../AI-SUBMISSION-ID-IMPLEMENTATION.md')); ?>" target="_blank">
                    View Implementation Guide
                </a>
            </p>
        </div>
    </div>
    <?php
}

/**
 * Run the AI submission ID migration
 */
function mgrnz_run_ai_submission_migration() {
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
    
    $wpdb->query($index_sql);
    
    // Mark as completed
    update_option('mgrnz_ai_submission_id_migration_completed', true);
    
    return [
        'success' => true,
        'message' => 'Successfully added ai_submission_id column to subscriptions table!',
        'table' => $table_name
    ];
}
