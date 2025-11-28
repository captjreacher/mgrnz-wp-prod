<?php
/**
 * Direct test of subscribe endpoint
 * Visit: http://mgrnz.local/test-subscribe-direct.php
 */

require_once __DIR__ . '/wp-load.php';

echo "<h1>Subscribe Blueprint Test</h1>";
echo "<hr>";

// Test data from localStorage
$test_data = [
    'session_id' => 'test_' . time(),
    'name' => 'Test User',
    'email' => 'test@example.com',
    'blueprint_data' => [
        'content' => '<h2>Test Blueprint</h2><p>This is test content.</p>'
    ]
];

echo "<h2>Sending request to subscribe-blueprint endpoint...</h2>";
echo "<p><strong>Data:</strong></p>";
echo "<pre>" . print_r($test_data, true) . "</pre>";

// Make the API call
$url = rest_url('mgrnz/v1/subscribe-blueprint');
echo "<p><strong>URL:</strong> " . $url . "</p>";

$response = wp_remote_post($url, [
    'headers' => [
        'Content-Type' => 'application/json',
    ],
    'body' => json_encode($test_data),
    'timeout' => 30
]);

echo "<h2>Response:</h2>";

if (is_wp_error($response)) {
    echo "<p style='color: red;'>ERROR: " . $response->get_error_message() . "</p>";
} else {
    $status_code = wp_remote_retrieve_response_code($response);
    $body = wp_remote_retrieve_body($response);
    
    echo "<p><strong>Status Code:</strong> " . $status_code . "</p>";
    echo "<p><strong>Response Body:</strong></p>";
    echo "<pre>" . $body . "</pre>";
    
    $data = json_decode($body, true);
    if ($data) {
        echo "<h3>Parsed Response:</h3>";
        echo "<pre>" . print_r($data, true) . "</pre>";
        
        if (isset($data['download_url'])) {
            echo "<p style='color: green;'>✓ Download URL: <a href='" . $data['download_url'] . "' target='_blank'>" . $data['download_url'] . "</a></p>";
        }
    }
}
