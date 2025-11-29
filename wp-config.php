<?php
/**
 * Local WordPress Configuration
 * 
 * This file contains environment-specific configuration for local development.
 * It should be loaded by wp-config.php when running in a local environment.
 * 
 * IMPORTANT: This file should never be committed to version control.
 * Add it to .gitignore to prevent accidental commits.
 * 
 * @package MGRNZ
 */

// Disable emoji scripts globally (they cause JavaScript syntax errors)
define('DISABLE_WP_EMOJIS', true);

// Load environment variables from .env.local
if (file_exists(__DIR__ . '/.env.local')) {
    // Using vlucas/phpdotenv (install via: composer require vlucas/phpdotenv)
    // If not using Composer, you can manually parse the .env.local file
    if (class_exists('Dotenv\Dotenv')) {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__, '.env.local');
        $dotenv->load();
    } else {
        // Fallback: Manual parsing of .env.local
        $envFile = __DIR__ . '/.env.local';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                // Skip comments
                if (strpos(trim($line), '#') === 0) {
                    continue;
                }
                // Parse KEY=VALUE
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value);
                    if (!empty($key) && !array_key_exists($key, $_ENV)) {
                        putenv("$key=$value");
                        $_ENV[$key] = $value;
                        $_SERVER[$key] = $value;
                    }
                }
            }
        }
    }
}

// Helper function to get environment variables with fallback
function env($key, $default = null) {
    $value = getenv($key);
    if ($value === false) {
        return $default;
    }
    // Convert string booleans to actual booleans
    if (strtolower($value) === 'true') return true;
    if (strtolower($value) === 'false') return false;
    return $value;
}

// ============================================
// Database Configuration
// ============================================
define('DB_NAME', env('DB_NAME', 'mgrnz_local'));
define('DB_USER', env('DB_USER', 'root'));
define('DB_PASSWORD', env('DB_PASSWORD', 'root'));
define('DB_HOST', env('DB_HOST', 'localhost'));
define('DB_CHARSET', env('DB_CHARSET', 'utf8mb4'));
define('DB_COLLATE', env('DB_COLLATE', ''));

// ============================================
// WordPress URLs
// ============================================
define('WP_HOME', env('WP_HOME', 'http://mgrnz.local'));
define('WP_SITEURL', env('WP_SITEURL', 'http://mgrnz.local'));

// ============================================
// Debug Settings
// ============================================
define('WP_DEBUG', env('WP_DEBUG', true));
define('WP_DEBUG_LOG', env('WP_DEBUG_LOG', true));
define('WP_DEBUG_DISPLAY', env('WP_DEBUG_DISPLAY', true));
define('SCRIPT_DEBUG', env('SCRIPT_DEBUG', true));

// Log errors to wp-content/debug.log
if (WP_DEBUG) {
    @ini_set('log_errors', 'On');
    @ini_set('display_errors', 'On');
    @ini_set('error_log', __DIR__ . '/wp-content/debug.log');
}

// ============================================
// Security Keys and Salts
// ============================================
// Load from environment or use defaults for local development
define('AUTH_KEY',         env('AUTH_KEY', 'put-your-unique-phrase-here'));
define('SECURE_AUTH_KEY',  env('SECURE_AUTH_KEY', 'put-your-unique-phrase-here'));
define('LOGGED_IN_KEY',    env('LOGGED_IN_KEY', 'put-your-unique-phrase-here'));
define('NONCE_KEY',        env('NONCE_KEY', 'put-your-unique-phrase-here'));
define('AUTH_SALT',        env('AUTH_SALT', 'put-your-unique-phrase-here'));
define('SECURE_AUTH_SALT', env('SECURE_AUTH_SALT', 'put-your-unique-phrase-here'));
define('LOGGED_IN_SALT',   env('LOGGED_IN_SALT', 'put-your-unique-phrase-here'));
define('NONCE_SALT',       env('NONCE_SALT', 'put-your-unique-phrase-here'));

// ============================================
// WordPress Database Table Prefix
// ============================================
$table_prefix = 'wpx7_';

// ============================================
// Local Development Settings
// ============================================
// Set timezone to match server (prevents nonce/session issues)
date_default_timezone_set('UTC');

// Increase nonce lifetime to handle time differences (default is 1 day)
define('NONCE_LIFE', 172800); // 2 days (2 * 86400 seconds)

// Disable caching
define('WP_CACHE', env('WP_CACHE', false));

// Disable automatic updates
define('AUTOMATIC_UPDATER_DISABLED', env('AUTOMATIC_UPDATER_DISABLED', true));

// Increase memory limits
define('WP_MEMORY_LIMIT', env('WP_MEMORY_LIMIT', '256M'));
define('WP_MAX_MEMORY_LIMIT', env('WP_MAX_MEMORY_LIMIT', '512M'));

// Enable post revisions (limit to 5 for local development)
define('WP_POST_REVISIONS', 5);

// Set autosave interval (in seconds)
define('AUTOSAVE_INTERVAL', 160);

// ============================================
// Supabase Configuration
// ============================================
// These constants can be used by custom plugins/themes
// After running 'supabase start', update .env.local with the keys from the output

// Supabase API URL (local or staging)
define('SUPABASE_URL', env('SUPABASE_URL', 'http://localhost:54321'));

// Supabase anonymous key (for client-side requests)
define('SUPABASE_ANON_KEY', env('SUPABASE_ANON_KEY', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZS1kZW1vIiwicm9sZSI6ImFub24iLCJleHAiOjE5ODM4MTI5OTZ9.CRXP1A7WOeoJeXxjNni43kdQwgnWNReilDMblYTn_I0'));

// Supabase service role key (for server-side requests with elevated privileges)
define('SUPABASE_SERVICE_ROLE_KEY', env('SUPABASE_SERVICE_ROLE_KEY', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZS1kZW1vIiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImV4cCI6MTk4MzgxMjk5Nn0.EGIM96RAZx35lJzdJsyH-qQwv8Hdp7fsn3W0YpN81IU'));

// Webhook configuration for WordPress -> Supabase communication
define('MGRNZ_WEBHOOK_URL', env('MGRNZ_WEBHOOK_URL', 'http://localhost:54321/functions/v1/wp-sync'));
define('MGRNZ_WEBHOOK_SECRET', env('MGRNZ_WEBHOOK_SECRET', 'local-test-secret'));

// CORS allowed origins (used by mgrnz-core.php mu-plugin)
define('MGRNZ_ALLOWED_ORIGINS', env('MGRNZ_ALLOWED_ORIGINS', 'http://mgrnz.local,http://localhost:8000,http://localhost:3000'));

// ============================================
// Third-Party Integration Settings
// ============================================
define('MAILERLITE_API_KEY', env('MAILERLITE_API_KEY', 'test-key-local'));
define('ML_INTAKE_GROUP_ID', env('ML_INTAKE_GROUP_ID', 'test-group'));
define('GITHUB_TOKEN', env('GITHUB_TOKEN', ''));
define('GITHUB_OWNER', env('GITHUB_OWNER', ''));
define('GITHUB_REPO', env('GITHUB_REPO', ''));

// ============================================
// Environment Type
// ============================================
define('WP_ENVIRONMENT_TYPE', 'local');

// ============================================
// Custom Content Directory (Optional)
// ============================================
// Uncomment if you want to use a custom content directory
// define('WP_CONTENT_DIR', __DIR__ . '/wp-content');
// define('WP_CONTENT_URL', WP_HOME . '/wp-content');

// ============================================
// SSL Settings
// ============================================
// Force SSL for admin (disable for local development)
define('FORCE_SSL_ADMIN', false);

// ============================================
// File Permissions
// ============================================
// Set file permissions for local development
define('FS_CHMOD_DIR', (0755 & ~umask()));
define('FS_CHMOD_FILE', (0644 & ~umask()));

// ============================================
// Disable File Editing in Admin
// ============================================
// Uncomment to disable theme/plugin editor in admin
// define('DISALLOW_FILE_EDIT', true);

// ============================================
// That's all, stop editing! Happy blogging.
// ============================================

/** Absolute path to the WordPress directory. */
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
