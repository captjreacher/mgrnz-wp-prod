<?php
/**
 * Direct Blueprint Viewer
 * Serves HTML blueprints directly without REST API interference
 * Access via: /wp-content/mu-plugins/blueprint-viewer-direct.php?file=blueprint-xxx.html
 */

// Start output buffering to prevent any accidental output
ob_start();

// Get and validate filename BEFORE loading WordPress
$filename = isset($_GET['file']) ? $_GET['file'] : '';

// Basic sanitization without WordPress functions
$filename = basename($filename); // Remove any path traversal
$filename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '', $filename); // Only allow safe characters

// Security: only allow specific pattern
if (!preg_match('/^blueprint-[a-zA-Z0-9_\-]+\.html$/', $filename)) {
    ob_end_clean();
    http_response_code(400);
    header('Content-Type: text/plain');
    die('Invalid filename');
}

// Build file path manually (without WordPress)
$wp_content_dir = dirname(dirname(__FILE__));
$uploads_dir = dirname($wp_content_dir) . '/wp-content/uploads';
$file_path = $uploads_dir . '/blueprints/' . $filename;

// Check if file exists
if (!file_exists($file_path)) {
    ob_end_clean();
    http_response_code(404);
    header('Content-Type: text/plain');
    die('Blueprint not found');
}

// Read the HTML content
$html = file_get_contents($file_path);

// Clear output buffer
ob_end_clean();

// Set proper headers - MUST be before any output
header('Content-Type: text/html; charset=utf-8');
header('Content-Disposition: inline'); // Open in browser, not download
header('Cache-Control: no-cache, must-revalidate');
header('X-Content-Type-Options: nosniff');

// Output the HTML
echo $html;
exit;
