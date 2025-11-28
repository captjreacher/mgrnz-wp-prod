<?php
/**
 * Plugin Name: Load Quote Page Script
 * Description: Automatically injects the quote page logic script into the footer of the /quote-my-workflow page.
 */

add_action('wp_footer', function() {
    // Only run on the quote page
    if (is_page('quote-my-workflow') || is_page('quote')) {
        $script_path = WP_CONTENT_DIR . '/quote-page-blueprint-data-script.html';
        
        if (file_exists($script_path)) {
            echo "<!-- START AUTOMATED SCRIPT INJECTION -->\n";
            include $script_path;
            echo "\n<!-- END AUTOMATED SCRIPT INJECTION -->";
        } else {
            echo "<!-- Script file not found at: $script_path -->";
        }
    }
});
