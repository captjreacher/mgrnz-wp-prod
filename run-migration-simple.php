<?php
/**
 * Simple Migration Runner - No WordPress Dependencies
 * 
 * This script connects directly to the database and runs the migration
 * Visit: http://mgrnz.local/run-migration-simple.php
 */

// Database configuration - update these if needed
$db_host = 'localhost';
$db_name = 'local';
$db_user = 'root';
$db_pass = 'root';
$table_prefix = 'wp_';

// Connect to database
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

$table_name = $table_prefix . 'mgrnz_blueprint_subscriptions';

// Check if table exists
$result = $mysqli->query("SHOW TABLES LIKE '$table_name'");
$table_exists = $result && $result->num_rows > 0;

if (!$table_exists) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Migration Status</title>
        <style>
            body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
            .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 4px; color: #856404; }
        </style>
    </head>
    <body>
        <h1>⚠️ Table Not Found</h1>
        <div class="warning">
            <p>The table <code><?php echo htmlspecialchars($table_name); ?></code> doesn't exist yet.</p>
            <p>It will be created automatically when the first subscription is made.</p>
            <p>The migration will run automatically after that.</p>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Check if column exists
$result = $mysqli->query("SHOW COLUMNS FROM `$table_name` LIKE 'ai_submission_id'");
$column_exists = $result && $result->num_rows > 0;

if ($column_exists) {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Migration Complete</title>
        <style>
            body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
            .success { background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 4px; color: #155724; }
        </style>
    </head>
    <body>
        <h1>✅ Migration Already Complete</h1>
        <div class="success">
            <p>The <code>ai_submission_id</code> column already exists in the table.</p>
            <p>No action needed!</p>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Run the migration
$sql = "ALTER TABLE `$table_name` ADD COLUMN `ai_submission_id` VARCHAR(255) NULL AFTER `blueprint_id`";
$success = $mysqli->query($sql);

if ($success) {
    // Add index
    $mysqli->query("CREATE INDEX `idx_ai_submission_id` ON `$table_name` (`ai_submission_id`)");
    
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Migration Success</title>
        <style>
            body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
            .success { background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 4px; color: #155724; }
            code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
        </style>
    </head>
    <body>
        <h1>✅ Migration Successful!</h1>
        <div class="success">
            <p><strong>The ai_submission_id column has been added successfully!</strong></p>
            <p>Table: <code><?php echo htmlspecialchars($table_name); ?></code></p>
            <p>Column: <code>ai_submission_id VARCHAR(255)</code></p>
            <p>Index: <code>idx_ai_submission_id</code> created</p>
        </div>
        <p style="margin-top: 30px;">
            <a href="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/wp-admin/'; ?>" style="padding: 10px 20px; background: #0073aa; color: white; text-decoration: none; border-radius: 4px;">
                Go to WordPress Admin
            </a>
        </p>
    </body>
    </html>
    <?php
} else {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Migration Error</title>
        <style>
            body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
            .error { background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 4px; color: #721c24; }
        </style>
    </head>
    <body>
        <h1>❌ Migration Failed</h1>
        <div class="error">
            <p><strong>Error:</strong> <?php echo htmlspecialchars($mysqli->error); ?></p>
        </div>
    </body>
    </html>
    <?php
}

$mysqli->close();
