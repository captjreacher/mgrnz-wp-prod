<?php
/**
 * Blueprint Authentication Bypass
 * Allows public access to blueprint endpoints without authentication
 */

add_filter('rest_authentication_errors', function($result) {
    // If already authenticated or has error, return as is
    if (true === $result || is_wp_error($result)) {
        return $result;
    }
    
    // Get the current request
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    
    // Allow public access to blueprint endpoints
    $public_endpoints = [
        '/wp-json/mgrnz/v1/subscribe-blueprint',
        '/wp-json/mgrnz/v1/download-blueprint',
        '/wp-json/mgrnz/v1/view-blueprint',
        '/wp-json/mgrnz/v1/test-pdf'
    ];
    
    foreach ($public_endpoints as $endpoint) {
        if (strpos($request_uri, $endpoint) !== false) {
            // Allow access without authentication
            return true;
        }
    }
    
    return $result;
});
