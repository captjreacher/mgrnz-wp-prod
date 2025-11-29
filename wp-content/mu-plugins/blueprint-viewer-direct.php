<?php
/**
 * Direct Blueprint Viewer
 * Serves HTML blueprints directly without REST API interference
 * Access via: /wp-content/mu-plugins/blueprint-viewer-direct.php?file=blueprint-xxx.html
 */

// Load WordPress
require_once dirname(dirname(dirname(__FILE__))) . '/wp-load.php';

// Get and validate filename
$filename = isset($_GET['file']) ? sanitize_file_name($_GET['file']) : '';

// Security: only allow specific pattern
if (!preg_match('/^blueprint-[a-zA-Z0-9_\-]+\.html$/', $filename)) {
    http_response_code(400);
    die('Invalid filename');
}

// Get file path
$upload_dir = wp_upload_dir();
$file_path = $upload_dir['basedir'] . '/blueprints/' . $filename;

// Check if file exists
if (!file_exists($file_path)) {
    http_response_code(404);
    die('Blueprint not found');
}

// Read the HTML content
$html = file_get_contents($file_path);

// Clear any previous output
if (ob_get_level()) {
    ob_end_clean();
}

// Set proper headers - MUST be before any output
header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');
header('X-Content-Type-Options: nosniff');

// Output the HTML
echo $html;
exit;
