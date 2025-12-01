<?php
/**
 * Force Update Endpoint File
 * 
 * This will download the latest version from GitHub and update the local file
 */

add_action('admin_menu', function() {
    add_submenu_page(
        'tools.php',
        'Force Update Endpoint',
        'Force Update Endpoint',
        'manage_options',
        'force-update-endpoint',
        'force_update_endpoint_page'
    );
});

function force_update_endpoint_page() {
    ?>
    <div class="wrap">
        <h1>Force Update Endpoint File</h1>
        
        <?php
        if (isset($_POST['do_update']) && check_admin_referer('force_update_endpoint')) {
            echo "<div class='notice notice-info'><p>Downloading latest version from GitHub...</p></div>";
            
            // GitHub raw URL for the file
            $github_url = 'https://raw.githubusercontent.com/captjreacher/mgrnz-wp-prod/main/wp-content/mu-plugins/mgrnz-ai-workflow-endpoint.php';
            
            // Download the file
            $response = wp_remote_get($github_url, ['timeout' => 30]);
            
            if (is_wp_error($response)) {
                echo "<div class='notice notice-error'><p>❌ Error downloading: " . $response->get_error_message() . "</p></div>";
            } else {
                $new_content = wp_remote_retrieve_body($response);
                
                // Check if it has the fix
                if (strpos($new_content, "'REF-' . strtoupper(substr(md5(uniqid()), 0, 8))") !== false) {
                    echo "<div class='notice notice-success'><p>✅ Downloaded file contains the fix!</p></div>";
                    
                    // Backup current file
                    $file_path = __DIR__ . '/mgrnz-ai-workflow-endpoint.php';
                    $backup_path = __DIR__ . '/mgrnz-ai-workflow-endpoint.php.backup.' . time();
                    
                    if (copy($file_path, $backup_path)) {
                        echo "<div class='notice notice-success'><p>✅ Backed up current file to: " . basename($backup_path) . "</p></div>";
                    }
                    
                    // Write new file
                    $result = file_put_contents($file_path, $new_content);
                    
                    if ($result !== false) {
                        echo "<div class='notice notice-success'><p>✅ <strong>File updated successfully!</strong></p></div>";
                        echo "<p>Wrote " . number_format($result) . " bytes</p>";
                        
                        // Clear OPcache
                        if (function_exists('opcache_invalidate')) {
                            opcache_invalidate($file_path, true);
                            echo "<div class='notice notice-success'><p>✅ Cleared OPcache for this file</p></div>";
                        }
                        
                        echo "<div class='notice notice-success'><p><strong>✅ UPDATE COMPLETE!</strong></p></div>";
                        echo "<p>Now try completing the wizard again. You should get REF-XXXXXXXX IDs.</p>";
                    } else {
                        echo "<div class='notice notice-error'><p>❌ Failed to write file. Check permissions.</p></div>";
                    }
                } else {
                    echo "<div class='notice notice-error'><p>❌ Downloaded file does NOT contain the fix!</p></div>";
                    echo "<p>The GitHub version might not be updated yet.</p>";
                }
            }
        }
        ?>
        
        <div class="card" style="max-width: 600px;">
            <h2>Current File Status</h2>
            <?php
            $file_path = __DIR__ . '/mgrnz-ai-workflow-endpoint.php';
            $file_contents = file_get_contents($file_path);
            
            if (strpos($file_contents, "'REF-' . strtoupper(substr(md5(uniqid()), 0, 8))") !== false) {
                echo "<p>✅ <strong>Current file HAS the fix</strong></p>";
            } else {
                echo "<p>❌ <strong>Current file does NOT have the fix</strong></p>";
            }
            
            $mod_time = filemtime($file_path);
            echo "<p><strong>Last modified:</strong> " . date('Y-m-d H:i:s', $mod_time) . "</p>";
            echo "<p><strong>Minutes ago:</strong> " . round((time() - $mod_time) / 60) . "</p>";
            ?>
        </div>
        
        <div class="card" style="max-width: 600px; margin-top: 20px;">
            <h2>Force Update from GitHub</h2>
            <p>This will download the latest version from GitHub and replace the current file.</p>
            <form method="post">
                <?php wp_nonce_field('force_update_endpoint'); ?>
                <input type="hidden" name="do_update" value="1">
                <button type="submit" class="button button-primary button-hero">
                    🔄 Download and Update Now
                </button>
            </form>
        </div>
        
        <div class="card" style="max-width: 600px; margin-top: 20px;">
            <h2>Manual Update Instructions</h2>
            <p>If the automatic update doesn't work, you can manually update via FTP:</p>
            <ol>
                <li>Download the file from GitHub: <a href="https://raw.githubusercontent.com/captjreacher/mgrnz-wp-prod/main/wp-content/mu-plugins/mgrnz-ai-workflow-endpoint.php" target="_blank">Download</a></li>
                <li>Connect to your server via FTP</li>
                <li>Navigate to: <code>/wp-content/mu-plugins/</code></li>
                <li>Upload the file (overwrite existing)</li>
                <li>Come back here and check the status</li>
            </ol>
        </div>
    </div>
    <?php
}
