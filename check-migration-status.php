<?php
/**
 * Check Migration Status
 * 
 * Visit this file in your browser to check if the migration has run
 * URL: http://mgrnz.local/check-migration-status.php
 */

// Load WordPress
require_once __DIR__ . '/wp-load.php';

// Security check
if (!current_user_can('manage_options')) {
    wp_die('You must be logged in as an administrator to view this page.');
}

global $wpdb;
$table_name = $wpdb->prefix . 'mgrnz_blueprint_subscriptions';

// Check if table exists
$table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") === $table_name;

// Check if column exists
$column_exists = false;
$column_info = null;
if ($table_exists) {
    $column_info = $wpdb->get_results(
        $wpdb->prepare("SHOW COLUMNS FROM `$table_name` LIKE %s", 'ai_submission_id')
    );
    $column_exists = !empty($column_info);
}

// Check migration option
$migration_completed = get_option('mgrnz_ai_submission_id_migration_completed', false);

// Get table structure
$table_structure = [];
if ($table_exists) {
    $table_structure = $wpdb->get_results("SHOW COLUMNS FROM `$table_name`");
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Migration Status Check</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            max-width: 900px;
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
        h1 {
            color: #333;
            margin-top: 0;
        }
        .status {
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
            font-weight: 600;
        }
        .success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
        }
        .error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f8f9fa;
            font-weight: 600;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
        .action-button {
            display: inline-block;
            padding: 10px 20px;
            background: #0073aa;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 10px 5px;
        }
        .action-button:hover {
            background: #005a87;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Migration Status Check</h1>
        
        <h2>Overall Status</h2>
        <?php if ($table_exists && $column_exists): ?>
            <div class="status success">
                ✅ Migration Complete! The ai_submission_id column exists and is ready to use.
            </div>
        <?php elseif (!$table_exists): ?>
            <div class="status warning">
                ⚠️ Table doesn't exist yet. It will be created when the first subscription is made.
            </div>
        <?php else: ?>
            <div class="status error">
                ❌ Migration needed! The ai_submission_id column is missing.
            </div>
        <?php endif; ?>
        
        <h2>Detailed Status</h2>
        <table>
            <tr>
                <th>Check</th>
                <th>Status</th>
                <th>Details</th>
            </tr>
            <tr>
                <td>Table Exists</td>
                <td><?php echo $table_exists ? '✅ Yes' : '❌ No'; ?></td>
                <td><code><?php echo esc_html($table_name); ?></code></td>
            </tr>
            <tr>
                <td>Column Exists</td>
                <td><?php echo $column_exists ? '✅ Yes' : '❌ No'; ?></td>
                <td><code>ai_submission_id</code></td>
            </tr>
            <tr>
                <td>Migration Flag</td>
                <td><?php echo $migration_completed ? '✅ Set' : '○ Not Set'; ?></td>
                <td><code>mgrnz_ai_submission_id_migration_completed</code></td>
            </tr>
        </table>
        
        <?php if ($table_exists): ?>
            <h2>Table Structure</h2>
            <table>
                <tr>
                    <th>Column Name</th>
                    <th>Type</th>
                    <th>Null</th>
                    <th>Key</th>
                </tr>
                <?php foreach ($table_structure as $column): ?>
                    <tr style="<?php echo $column->Field === 'ai_submission_id' ? 'background: #d4edda;' : ''; ?>">
                        <td><code><?php echo esc_html($column->Field); ?></code></td>
                        <td><?php echo esc_html($column->Type); ?></td>
                        <td><?php echo esc_html($column->Null); ?></td>
                        <td><?php echo esc_html($column->Key); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
        
        <h2>Actions</h2>
        <a href="<?php echo admin_url('tools.php?page=mgrnz-migrations'); ?>" class="action-button">
            Go to Migration Admin Page
        </a>
        <a href="<?php echo admin_url(); ?>" class="action-button">
            Go to WordPress Admin
        </a>
        
        <?php if (!$column_exists && $table_exists): ?>
            <h3 style="margin-top: 30px;">Quick Fix</h3>
            <p>Run this SQL in phpMyAdmin or your database tool:</p>
            <pre style="background: #f4f4f4; padding: 15px; border-radius: 4px; overflow-x: auto;">ALTER TABLE `<?php echo esc_html($table_name); ?>` 
ADD COLUMN `ai_submission_id` VARCHAR(255) NULL 
AFTER `blueprint_id`;

CREATE INDEX `idx_ai_submission_id` 
ON `<?php echo esc_html($table_name); ?>` (`ai_submission_id`);</pre>
        <?php endif; ?>
    </div>
</body>
</html>
