<?php
/**
 * Temporary script to clear rate limit
 * Delete this file after use
 */

// Load WordPress
require_once(dirname(__DIR__, 2) . '/wp-load.php');

// Clear rate limit for localhost
$ip = '127.0.0.1';
$transient_key = 'ai_workflow_' . md5($ip);
delete_transient($transient_key);

echo "Rate limit cleared for IP: $ip\n";
echo "Transient key: $transient_key\n";
echo "You can now test the wizard again.\n";
