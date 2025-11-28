<?php
/**
 * Test PDF Generation Endpoint
 * 
 * Access via: /wp-json/mgrnz/v1/test-pdf
 */

add_action('rest_api_init', function () {
    register_rest_route('mgrnz/v1', '/test-pdf', [
        'methods'  => 'GET',
        'permission_callback' => '__return_true',
        'callback' => 'mgrnz_test_pdf_generation',
    ]);
});

function mgrnz_test_pdf_generation($request) {
    // Load required class
    require_once __DIR__ . '/includes/class-pdf-generator.php';
    
    $test_blueprint_data = [
        'content' => '<h2>Test Blueprint</h2>
<p>This is a test blueprint to verify PDF generation is working.</p>
<h3>Section 1</h3>
<p>Some content here with <strong>bold text</strong> and <em>italic text</em>.</p>
<ul>
    <li>Item 1</li>
    <li>Item 2</li>
    <li>Item 3</li>
</ul>
<h3>Section 2</h3>
<p>More content to ensure the PDF has substance.</p>
<ol>
    <li>First step</li>
    <li>Second step</li>
    <li>Third step</li>
</ol>'
    ];
    
    $test_user_data = [
        'name' => 'Test User',
        'email' => 'test@example.com'
    ];
    
    $test_session_id = 'test_' . time();
    
    // Initialize PDF generator
    $pdf_generator = new MGRNZ_PDF_Generator();
    
    // Generate PDF
    $pdf_path = $pdf_generator->generate_blueprint_pdf(
        $test_blueprint_data,
        $test_user_data,
        $test_session_id
    );
    
    // Check result
    if (is_wp_error($pdf_path)) {
        return new WP_REST_Response([
            'success' => false,
            'error' => $pdf_path->get_error_message(),
            'error_code' => $pdf_path->get_error_code()
        ], 500);
    }
    
    // Get file info
    $file_exists = file_exists($pdf_path);
    $file_size = $file_exists ? filesize($pdf_path) : 0;
    $file_extension = pathinfo($pdf_path, PATHINFO_EXTENSION);
    $download_url = $pdf_generator->get_download_url($pdf_path);
    
    // For HTML files, use viewer endpoint
    if ($file_extension === 'html') {
        $filename = basename($pdf_path);
        $download_url = rest_url('mgrnz/v1/view-blueprint/' . $filename);
    }
    
    return new WP_REST_Response([
        'success' => true,
        'pdf_path' => $pdf_path,
        'file_exists' => $file_exists,
        'file_size' => $file_size,
        'file_extension' => $file_extension,
        'download_url' => $download_url,
        'is_blank' => $file_size < 1000,
        'session_id' => $test_session_id
    ], 200);
}
