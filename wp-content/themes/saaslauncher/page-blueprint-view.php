<?php
/**
 * Template Name: Blueprint Viewer
 * Description: Displays blueprint content inline
 */

// Get blueprint filename from query parameter
$filename = isset($_GET['file']) ? sanitize_file_name($_GET['file']) : '';

if (empty($filename) || !preg_match('/^blueprint-[a-zA-Z0-9_\-]+\.html$/', $filename)) {
    wp_die('Invalid blueprint file');
}

$upload_dir = wp_upload_dir();
$file_path = $upload_dir['basedir'] . '/blueprints/' . $filename;

if (!file_exists($file_path)) {
    wp_die('Blueprint not found');
}

// Read and output the HTML content directly
$html = file_get_contents($file_path);
echo $html;
exit;
