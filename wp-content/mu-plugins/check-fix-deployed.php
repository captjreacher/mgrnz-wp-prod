<?php
/**
 * Test if Fix is Deployed
 * 
 * Visit: https://mgrnz.com/wp-admin/admin.php?page=test-fix-deployed
 */

add_action('admin_menu', function() {
    add_submenu_page(
        null, // No parent menu
        'Test Fix Deployed',
        'Test Fix Deployed',
        'manage_options',
        'test-fix-deployed',
        'test_fix_deployed_page'
    );
});

function test_fix_deployed_page() {
    ?>
    <div class="wrap">
        <h1>Test if Fix is Deployed</h1>
        
        <?php
        $file_path = __DIR__ . '/mgrnz-ai-workflow-endpoint.php';
        $file_contents = file_get_contents($file_path);
        
        // Check for the fix signature
        if (strpos($file_contents, "'REF-' . strtoupper(substr(md5(uniqid()), 0, 8))") !== false) {
            echo "<div class='notice notice-success'><p>✅ <strong>Fix is present in the file!</strong></p></div>";
            echo "<p>The code generates proper REF- IDs</p>";
        } else {
            echo "<div class='notice notice-error'><p>❌ <strong>Fix is NOT in the file</strong></p></div>";
            echo "<p>The file still has the old code</p>";
        }
        
        // Check file modification time
        $mod_time = filemtime($file_path);
        echo "<p><strong>File last modified:</strong> " . date('Y-m-d H:i:s', $mod_time) . "</p>";
        echo "<p><strong>Current server time:</strong> " . date('Y-m-d H:i:s') . "</p>";
        
        $minutes_ago = round((time() - $mod_time) / 60);
        echo "<p><strong>Modified:</strong> $minutes_ago minutes ago</p>";
        
        if ($minutes_ago > 10) {
            echo "<div class='notice notice-warning'><p>⚠️ File hasn't been updated recently. Deployment may not have completed.</p></div>";
        } else {
            echo "<div class='notice notice-success'><p>✅ File was recently updated. Deployment likely completed.</p></div>";
        }
        
        // Show the actual line
        $lines = explode("\n", $file_contents);
        foreach ($lines as $num => $line) {
            if (strpos($line, "\$submission_ref = ") !== false && strpos($line, "REF-") !== false) {
                echo "<h3>Found at line " . ($num + 1) . ":</h3>";
                echo "<pre style='background:#f0f0f0; padding:10px;'>" . htmlspecialchars($line) . "</pre>";
                break;
            }
        }
        
        // Check OPcache
        echo "<hr>";
        echo "<h2>PHP Cache Status</h2>";
        if (function_exists('opcache_get_status')) {
            $status = opcache_get_status();
            if ($status && $status['opcache_enabled']) {
                echo "<p>⚠️ OPcache is enabled. You may need to clear it.</p>";
                echo "<form method='post'>";
                echo "<input type='hidden' name='clear_opcache' value='1'>";
                echo "<button type='submit' class='button button-primary'>Clear OPcache Now</button>";
                echo "</form>";
                
                if (isset($_POST['clear_opcache'])) {
                    opcache_reset();
                    echo "<div class='notice notice-success'><p>✅ OPcache cleared!</p></div>";
                    echo "<p>Now try completing the wizard again.</p>";
                }
            } else {
                echo "<p>✅ OPcache is not enabled</p>";
            }
        } else {
            echo "<p>ℹ️ OPcache functions not available</p>";
        }
        ?>
        
        <hr>
        <h2>Next Steps</h2>
        <ol>
            <li>If the fix is present, clear OPcache (button above)</li>
            <li>Complete a fresh wizard session</li>
            <li>Check browser console for "Blueprint generated:" log</li>
            <li>Look for <code>submission_ref: "REF-XXXXXXXX"</code> in the response</li>
        </ol>
    </div>
    <?php
}
