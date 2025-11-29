<?php
/**
 * Time Synchronization Check
 * Diagnoses time differences between server, WordPress, and client
 */

require_once __DIR__ . '/wp-load.php';

echo "<h1>Time Synchronization Check</h1>\n";

// Server time
$server_time = time();
$server_date = date('Y-m-d H:i:s T', $server_time);

// WordPress time
$wp_time = current_time('timestamp');
$wp_date = current_time('Y-m-d H:i:s');
$wp_timezone = get_option('timezone_string') ?: 'UTC' . get_option('gmt_offset');

// PHP timezone
$php_timezone = date_default_timezone_get();

echo "<h2>Server & WordPress Time:</h2>\n";
echo "<table border='1' cellpadding='10'>\n";
echo "<tr><th>Source</th><th>Timestamp</th><th>Date/Time</th><th>Timezone</th></tr>\n";
echo "<tr><td>PHP Server</td><td>{$server_time}</td><td>{$server_date}</td><td>{$php_timezone}</td></tr>\n";
echo "<tr><td>WordPress</td><td>{$wp_time}</td><td>{$wp_date}</td><td>{$wp_timezone}</td></tr>\n";
echo "</table>\n";

// Time difference
$time_diff = $wp_time - $server_time;
echo "<h2>Time Difference:</h2>\n";
if ($time_diff == 0) {
    echo "<p style='color: green;'>✓ Server and WordPress times are synchronized</p>\n";
} else {
    $hours = abs($time_diff) / 3600;
    echo "<p style='color: orange;'>⚠ Time difference: " . abs($time_diff) . " seconds (" . round($hours, 2) . " hours)</p>\n";
    if ($time_diff > 0) {
        echo "<p>WordPress time is ahead of server time</p>\n";
    } else {
        echo "<p>Server time is ahead of WordPress time</p>\n";
    }
}

// Nonce check
echo "<h2>Nonce Configuration:</h2>\n";
$nonce_life = defined('NONCE_LIFE') ? NONCE_LIFE : (DAY_IN_SECONDS);
echo "<p>Nonce lifetime: " . ($nonce_life / 3600) . " hours</p>\n";

// Test nonce generation
$test_nonce = wp_create_nonce('test_action');
echo "<p>Test nonce: {$test_nonce}</p>\n";
$nonce_valid = wp_verify_nonce($test_nonce, 'test_action');
echo "<p>Nonce verification: " . ($nonce_valid ? '✓ Valid' : '✗ Invalid') . "</p>\n";

// Client time check (JavaScript)
echo "<h2>Client Time Check:</h2>\n";
echo "<p id='client-time'>Loading...</p>\n";
echo "<p id='client-diff'>Calculating...</p>\n";

echo "<script>
const serverTime = {$server_time};
const clientTime = Math.floor(Date.now() / 1000);
const diff = clientTime - serverTime;

document.getElementById('client-time').innerHTML = 'Client time: ' + new Date().toString();
document.getElementById('client-diff').innerHTML = 'Client vs Server difference: ' + Math.abs(diff) + ' seconds (' + (Math.abs(diff) / 3600).toFixed(2) + ' hours)';

if (Math.abs(diff) > 300) {
    document.getElementById('client-diff').style.color = 'red';
    document.getElementById('client-diff').innerHTML += '<br><strong>⚠ WARNING: Time difference exceeds 5 minutes!</strong>';
}
</script>\n";

echo "<h2>Recommendations:</h2>\n";
echo "<ul>\n";
echo "<li>Ensure server time is synchronized with NTP</li>\n";
echo "<li>Set WordPress timezone in Settings > General</li>\n";
echo "<li>Use UTC for consistency across systems</li>\n";
echo "<li>Increase NONCE_LIFE if time sync issues persist</li>\n";
echo "</ul>\n";
