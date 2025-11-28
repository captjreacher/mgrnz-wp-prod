<?php
/**
 * Test Wizard Files
 * Upload this to production root and access it to check if wizard files exist
 * URL: https://your-domain.com/test-wizard-files.php
 */

echo "<h1>Wizard Files Test</h1>";
echo "<style>body { font-family: monospace; padding: 20px; } .ok { color: green; } .error { color: red; }</style>";

$files_to_check = [
    'Page Template' => 'wp-content/themes/saaslauncher/page-wizard-clean.php',
    'Wizard Main' => 'wp-content/themes/saaslauncher/templates/wizard/wizard-main.php',
    'Wizard Step 1' => 'wp-content/themes/saaslauncher/templates/wizard/wizard-step-1.php',
    'Wizard Step 2' => 'wp-content/themes/saaslauncher/templates/wizard/wizard-step-2.php',
    'Wizard Step 3' => 'wp-content/themes/saaslauncher/templates/wizard/wizard-step-3-chat.php',
    'Wizard Completion' => 'wp-content/themes/saaslauncher/templates/wizard/wizard-completion.php',
    'Wizard Styles' => 'wp-content/themes/saaslauncher/templates/wizard/wizard-styles.css',
    'Wizard Scripts' => 'wp-content/themes/saaslauncher/templates/wizard/wizard-scripts.js',
];

echo "<h2>File Check:</h2>";
echo "<ul>";

$all_ok = true;
foreach ($files_to_check as $name => $path) {
    $exists = file_exists($path);
    $class = $exists ? 'ok' : 'error';
    $status = $exists ? '✓ EXISTS' : '✗ MISSING';
    
    echo "<li class='$class'><strong>$name:</strong> $status<br><small>$path</small></li>";
    
    if (!$exists) {
        $all_ok = false;
    }
}

echo "</ul>";

if ($all_ok) {
    echo "<h2 class='ok'>✓ All files present!</h2>";
    echo "<p>The blank page issue is likely a PHP error. Check error logs or enable WP_DEBUG.</p>";
} else {
    echo "<h2 class='error'>✗ Some files are missing!</h2>";
    echo "<p>Upload the missing files via FTP/SFTP.</p>";
}

echo "<hr>";
echo "<h2>WordPress Info:</h2>";
echo "<ul>";
echo "<li><strong>WordPress Root:</strong> " . ABSPATH . "</li>";
echo "<li><strong>Theme Directory:</strong> " . get_template_directory() . "</li>";
echo "<li><strong>Theme URI:</strong> " . get_template_directory_uri() . "</li>";
echo "</ul>";

echo "<hr>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ol>";
echo "<li>If files are missing, upload them</li>";
echo "<li>If all files exist, enable WP_DEBUG in wp-config.php</li>";
echo "<li>Check browser console (F12) for JavaScript errors</li>";
echo "<li>Check server error logs</li>";
echo "</ol>";

echo "<hr>";
echo "<p><small>Delete this file after testing: test-wizard-files.php</small></p>";
?>
