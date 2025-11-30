<?php
/**
 * Standalone PDF Generator Endpoint
 * Bypasses WordPress REST API to avoid rewrite rule issues
 */

// Load WordPress
require_once('wp-load.php');

// Set headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

try {
    // Get JSON input
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (empty($data['blueprint_html'])) {
        throw new Exception('No blueprint HTML provided');
    }
    
    // Load the generator class if not already loaded
    if (!class_exists('MGRNZ_PDF_Generator_V2')) {
        $class_file = WP_CONTENT_DIR . '/mu-plugins/includes/class-pdf-generator.php';
        if (file_exists($class_file)) {
            require_once $class_file;
        } else {
            throw new Exception('PDF Generator class file not found at: ' . $class_file);
        }
    }
    
    if (!class_exists('MGRNZ_PDF_Generator_V2')) {
        throw new Exception('PDF Generator class could not be loaded');
    }
    
    // Prepare data
    $blueprint_html = $data['blueprint_html'];
    $user_data = [
        'name' => $data['user_name'] ?? 'Valued Client',
        'email' => $data['user_email'] ?? ''
    ];
    $session_id = 'direct-' . uniqid();
    
    // Generate PDF
    $pdf_generator = new MGRNZ_PDF_Generator_V2();
    $blueprint_data = ['content' => $blueprint_html];
    
    $pdf_path = $pdf_generator->generate_blueprint_pdf($blueprint_data, $user_data, $session_id);
    
    if (is_wp_error($pdf_path)) {
        throw new Exception($pdf_path->get_error_message());
    }
    
    // Get URL
    $download_url = $pdf_generator->get_download_url($pdf_path);
    
    echo json_encode([
        'success' => true,
        'download_url' => $download_url
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
