<?php
/**
 * Simple Blueprint PDF Viewer
 * Place this file in: public_html/wp/blueprint-pdf-viewer.php
 */

$file = $_GET['f'] ?? '';

// Security: Only allow blueprint HTML files
if (!preg_match('/^blueprint-[a-zA-Z0-9\-]+\.html$/', $file)) {
    http_response_code(403);
    die('Invalid filename');
}

// Get file path
$path = __DIR__ . '/wp-content/uploads/blueprints/' . $file;

// Check if file exists
if (!file_exists($path)) {
    http_response_code(404);
    die('Blueprint not found');
}

// Serve with correct headers
header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: no-cache');

readfile($path);
exit;