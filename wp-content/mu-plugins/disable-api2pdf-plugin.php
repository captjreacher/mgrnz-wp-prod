<?php
/**
 * Disable API2PDF Plugin on Wizard Pages
 * 
 * The "Save Page to PDF" plugin interferes with our custom blueprint download
 * This file dequeues its scripts on wizard pages
 */

add_action('wp_enqueue_scripts', function() {
    // Check if we're on a wizard page
    $wizard_pages = [
        'wizard-subscribe-page',
        'start-using-ai',
        'quote-my-workflow',
        'blueprint-download'
    ];
    
    $current_url = $_SERVER['REQUEST_URI'] ?? '';
    $is_wizard_page = false;
    
    foreach ($wizard_pages as $page) {
        if (strpos($current_url, $page) !== false) {
            $is_wizard_page = true;
            break;
        }
    }
    
    // Dequeue the API2PDF plugin script on wizard pages
    if ($is_wizard_page) {
        wp_dequeue_script('api2pdf');
        wp_deregister_script('api2pdf');
        
        // Also remove the AJAX URL that the plugin sets
        add_action('wp_footer', function() {
            echo '<script>window.Api2PdfWpAjaxUrl = undefined;</script>';
        }, 1);
    }
}, 100); // High priority to run after the plugin enqueues its scripts
