<?php
/**
 * Blueprint Viewer
 * Displays HTML blueprints in the browser with proper headers
 */

add_action('rest_api_init', function () {
    register_rest_route('mgrnz/v1', '/view-blueprint/(?P<filename>[a-zA-Z0-9_\-\.]+)', [
        'methods'  => 'GET',
        'permission_callback' => '__return_true',
        'callback' => 'mgrnz_view_blueprint',
        'args' => [
            'filename' => [
                'required' => true,
                'type' => 'string',
                'sanitize_callback' => 'sanitize_file_name'
            ]
        ]
    ]);
});

function mgrnz_view_blueprint($request) {
    $filename = $request->get_param('filename');
    
    // Security: only allow specific pattern
    if (!preg_match('/^blueprint-[a-zA-Z0-9_\-]+\.html$/', $filename)) {
        return new WP_Error('invalid_filename', 'Invalid blueprint filename', ['status' => 400]);
    }
    
    $upload_dir = wp_upload_dir();
    $file_path = $upload_dir['basedir'] . '/blueprints/' . $filename;
    
    if (!file_exists($file_path)) {
        return new WP_Error('not_found', 'Blueprint not found', ['status' => 404]);
    }
    
    // Read the HTML content
    $html = file_get_contents($file_path);
    
    // Set proper headers to display in browser
    header('Content-Type: text/html; charset=utf-8');
    header('Cache-Control: no-cache, must-revalidate');
    
    // Output the HTML
    echo $html;
    exit;
}
