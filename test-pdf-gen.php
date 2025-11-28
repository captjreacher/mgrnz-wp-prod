<?php
require_once('wp-load.php');
require_once('wp-content/mu-plugins/includes/class-pdf-generator.php');

$pdf_generator = new MGRNZ_PDF_Generator();

$blueprint_data = [
    'content' => "# Test Blueprint\n\nThis is a test paragraph.\n\n- Item 1\n- Item 2"
];

$user_data = [
    'name' => 'Test User',
    'email' => 'test@example.com'
];

$session_id = 'test_debug_' . time();

echo "Generating PDF...\n";
$pdf_path = $pdf_generator->generate_blueprint_pdf($blueprint_data, $user_data, $session_id);

if (is_wp_error($pdf_path)) {
    echo "Error: " . $pdf_path->get_error_message() . "\n";
} else {
    echo "Success: " . $pdf_path . "\n";
    echo "URL: " . $pdf_generator->get_download_url($pdf_path) . "\n";
    
    // Read the file content to verify it's not blank
    $content = file_get_contents($pdf_path);
    echo "File size: " . strlen($content) . " bytes\n";
    echo "Preview:\n" . substr($content, 0, 500) . "\n";
}
