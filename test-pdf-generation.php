<?php
/**
 * Test PDF Generation
 * 
 * Run this in browser: http://mgrnz.local/test-pdf-generation.php
 */

// Load WordPress
require_once __DIR__ . '/wp-load.php';

// Test data
$test_blueprint_data = [
    'content' => '<h2>Test Blueprint</h2><p>This is a test blueprint with some content.</p><ul><li>Item 1</li><li>Item 2</li></ul>'
];

$test_user_data = [
    'name' => 'Test User',
    'email' => 'test@example.com'
];

$test_session_id = 'test_' . time();

echo "<h1>PDF Generation Test</h1>";
echo "<hr>";

// Check if PDF Generator class exists
if (!class_exists('MGRNZ_PDF_Generator')) {
    echo "<p style='color: red;'>ERROR: MGRNZ_PDF_Generator class not found!</p>";
    echo "<p>Trying to load it...</p>";
    require_once __DIR__ . '/wp-content/mu-plugins/includes/class-pdf-generator.php';
}

echo "<p style='color: green;'>✓ MGRNZ_PDF_Generator class loaded</p>";

// Check if TCPDF is available
if (class_exists('TCPDF')) {
    echo "<p style='color: green;'>✓ TCPDF is available</p>";
} else {
    echo "<p style='color: orange;'>⚠ TCPDF not available - will use HTML fallback</p>";
}

// Test PDF generation
echo "<h2>Generating PDF...</h2>";

try {
    $pdf_generator = new MGRNZ_PDF_Generator();
    $result = $pdf_generator->generate_blueprint_pdf($test_blueprint_data, $test_user_data, $test_session_id);
    
    if (is_wp_error($result)) {
        echo "<p style='color: red;'>ERROR: " . $result->get_error_message() . "</p>";
    } else {
        echo "<p style='color: green;'>✓ PDF generated successfully!</p>";
        echo "<p><strong>File path:</strong> " . $result . "</p>";
        
        // Check if file exists
        if (file_exists($result)) {
            echo "<p style='color: green;'>✓ File exists on disk</p>";
            echo "<p><strong>File size:</strong> " . filesize($result) . " bytes</p>";
            
            // Get download URL
            $download_url = $pdf_generator->get_download_url($result);
            echo "<p><strong>Download URL:</strong> <a href='" . $download_url . "' target='_blank'>" . $download_url . "</a></p>";
        } else {
            echo "<p style='color: red;'>ERROR: File does not exist on disk!</p>";
        }
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>EXCEPTION: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";
echo "<h2>Debug Info</h2>";
echo "<p><strong>Upload directory:</strong> " . wp_upload_dir()['basedir'] . "/blueprints</p>";
echo "<p><strong>Upload URL:</strong> " . wp_upload_dir()['baseurl'] . "/blueprints</p>";

// Check if blueprints directory exists
$blueprints_dir = wp_upload_dir()['basedir'] . '/blueprints';
if (file_exists($blueprints_dir)) {
    echo "<p style='color: green;'>✓ Blueprints directory exists</p>";
    
    // List files
    $files = scandir($blueprints_dir);
    echo "<p><strong>Files in directory:</strong></p>";
    echo "<ul>";
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            echo "<li>" . $file . " (" . filesize($blueprints_dir . '/' . $file) . " bytes)</li>";
        }
    }
    echo "</ul>";
} else {
    echo "<p style='color: orange;'>⚠ Blueprints directory does not exist</p>";
}
