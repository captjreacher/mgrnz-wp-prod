<?php
/**
 * PDF Generator Class
 * 
 * Generates PDF documents for blueprint downloads
 * Uses Api2Pdf (Cloud API) for high-quality rendering
 */

class MGRNZ_PDF_Generator_V2 {
    
    // Api2Pdf API Key
    private $api_key = '21f8e7ef-e58e-471c-b83f-a47b20514b80';
    
    /**
     * Generate PDF from blueprint data
     * 
     * @param array $blueprint_data Blueprint content and diagram
     * @param array $user_data User information (name, email)
     * @param string $session_id Session ID
     * @return string|WP_Error Path to generated PDF or error
     */
    public function generate_blueprint_pdf($blueprint_data, $user_data, $session_id) {
        error_log('[PDF Generator] Starting PDF generation via Api2Pdf for session: ' . $session_id);
        
        try {
            // 1. Generate HTML content (clean version for API)
            $html = $this->generate_blueprint_html($blueprint_data, $user_data, true);
            
            // 2. Call Api2Pdf
            $pdf_url = $this->call_api2pdf($html);
            
            if (is_wp_error($pdf_url)) {
                throw new Exception($pdf_url->get_error_message());
            }
            
            error_log('[PDF Generator] PDF generated successfully at URL: ' . $pdf_url);
            
            // 3. Download the PDF to local server
            $filename = $this->generate_filename($session_id, 'pdf');
            $upload_dir = wp_upload_dir();
            $pdf_dir = $upload_dir['basedir'] . '/blueprints';
            
            if (!file_exists($pdf_dir)) {
                wp_mkdir_p($pdf_dir);
            }
            
            $local_path = $pdf_dir . '/' . $filename;
            
            $file_content = file_get_contents($pdf_url);
            if ($file_content === false) {
                throw new Exception('Failed to download PDF from Api2Pdf');
            }
            
            file_put_contents($local_path, $file_content);
            error_log('[PDF Generator] PDF saved locally to: ' . $local_path);
            
            return $local_path;
            
        } catch (Exception $e) {
            error_log('[PDF Generator] Error: ' . $e->getMessage());
            // Fallback to HTML if API fails
            return $this->generate_simple_pdf($blueprint_data, $user_data, $session_id);
        }
    }
    
    /**
     * Call Api2Pdf API
     */
    private function call_api2pdf($html) {
        // DEBUG: Force custom implementation to rule out plugin issues
        /*
        // Try to use the "Save Page to PDF" plugin's library if available
        if (class_exists('Api2Pdf_Library')) {
            try {
                $options = get_option('savePageToPdf_options');
                $api_key = $options['savePageToPdf_apiKey'] ?? $this->api_key;
                
                if (empty($api_key)) {
                    error_log('[PDF Generator] No API key found in plugin settings, using hardcoded key');
                    $api_key = $this->api_key;
                }
                
                $client = new Api2Pdf_Library($api_key);
                
                // Use Chrome headless API from HTML
                $result = $client->api2pdf_headless_chrome_from_html(
                    $html,
                    false, // inline
                    'My-AI-Blueprint.pdf', // filename
                    [
                        'landscape' => false,
                        'printBackground' => true,
                        'marginTop' => 0, // Set to 0 for full bleed
                        'marginBottom' => 0,
                        'marginLeft' => 0,
                        'marginRight' => 0
                    ]
                );
                
                if (isset($result->pdf) && !empty($result->pdf)) {
                    error_log('[PDF Generator] Using Save Page to PDF plugin - PDF URL: ' . $result->pdf);
                    return $result->pdf;
                } else {
                    throw new Exception('No PDF URL in plugin response');
                }
                
            } catch (Exception $e) {
                error_log('[PDF Generator] Plugin library failed: ' . $e->getMessage() . ' - Falling back to custom implementation');
                // Fall through to custom implementation below
            }
        }
        */
        
        // Fallback: Use our custom implementation
        $endpoint = 'https://v2.api2pdf.com/chrome/html';
        
        $body = [
            'html' => $html,
            'options' => [
                'landscape' => false,
                'printBackground' => true,
                'marginTop' => 0, // Set to 0 for full bleed
                'marginBottom' => 0,
                'marginLeft' => 0,
                'marginRight' => 0,
                'scale' => 1
            ]
        ];
        
        $response = wp_remote_post($endpoint, [
            'headers' => [
                'Authorization' => $this->api_key,
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($body),
            'timeout' => 30 // Wait up to 30 seconds
        ]);
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if ($code !== 200) {
            error_log('[PDF Generator] Api2Pdf Error (' . $code . '): ' . $body);
            return new WP_Error('api_error', 'Api2Pdf failed: ' . ($data['error'] ?? 'Unknown error'));
        }
        
        if (empty($data['FileUrl'])) {
            return new WP_Error('api_error', 'No FileUrl in Api2Pdf response');
        }
        
        return $data['FileUrl'];
    }
    
    /**
     * Generate simple HTML-based fallback
     */
    private function generate_simple_pdf($blueprint_data, $user_data, $session_id) {
        error_log('[PDF Generator] Using HTML fallback');
        
        try {
            $html = $this->generate_blueprint_html($blueprint_data, $user_data, false); // false = include auto-print script
            
            $filename = $this->generate_filename($session_id, 'html');
            $upload_dir = wp_upload_dir();
            $pdf_dir = $upload_dir['basedir'] . '/blueprints';
            
            if (!file_exists($pdf_dir)) {
                wp_mkdir_p($pdf_dir);
            }
            
            $file_path = $pdf_dir . '/' . $filename;
            file_put_contents($file_path, $html);
            
            return $file_path;
            
        } catch (Exception $e) {
            return new WP_Error('pdf_generation_failed', 'Failed to generate document: ' . $e->getMessage());
        }
    }
    
    /**
     * Generate filename
     */
    private function generate_filename($session_id, $extension = 'pdf') {
        $safe_session = preg_replace('/[^a-zA-Z0-9_-]/', '', $session_id);
        return 'blueprint-' . $safe_session . '-' . time() . '.' . $extension;
    }
    
    /**
     * Get download URL
     */
    public function get_download_url($file_path) {
        if (strpos($file_path, 'http') === 0) return $file_path;
        
        $upload_dir = wp_upload_dir();
        $filename = basename($file_path);
        
        // For HTML files, use viewer
        if (strpos($filename, '.html') !== false) {
            return home_url('/blueprint-pdf-viewer.php?f=' . urlencode($filename));
        }
        
        // For PDF files, direct download
        return str_replace($upload_dir['basedir'], $upload_dir['baseurl'], $file_path);
    }
    
    /**
     * Format content for HTML
     */
    private function format_content_for_html($content) {
        if (empty($content)) return '';
        
        $html = $content;
        $html = preg_replace('/^### (.+)$/m', '<h3>$1</h3>', $html);
        $html = preg_replace('/^## (.+)$/m', '<h2>$1</h2>', $html);
        $html = preg_replace('/^# (.+)$/m', '<h1>$1</h1>', $html);
        $html = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $html);
        $html = preg_replace('/^- (.+)$/m', '<li>$1</li>', $html);
        $html = preg_replace('/(<li>.*<\/li>)/s', '<ul>$1</ul>', $html);
        $html = nl2br($html);
        return $html;
    }
    
    /**
     * Generate blueprint HTML
     * @param bool $for_api If true, removes auto-print script and buttons
     */
    private function generate_blueprint_html($blueprint_data, $user_data, $for_api = false) {
        $content = $blueprint_data['content'] ?? 'No content available';
        
        // FIX: Replace local URLs with production URLs so Api2Pdf can access images
        // Handle /wp/ subdirectory installation
        $content = str_replace('http://mgrnz.local', 'https://mgrnz.com/wp', $content);
        $content = str_replace('http://localhost', 'https://mgrnz.com/wp', $content);
        
        // Also handle cases where the URL might already have /wp/
        $content = str_replace('https://mgrnz.com/wp/wp/', 'https://mgrnz.com/wp/', $content);
        
        // AUTO-INJECT: Add DRIVE framework image to DISCOVER section if not already present
        if (stripos($content, 'DISCOVER') !== false && stripos($content, 'DRIVE_Public_14-07-2025.png') === false && stripos($content, 'data:image') === false) {
            // Find the DISCOVER section heading
            $discover_pattern = '/(<h[23][^>]*>.*?DISCOVER.*?<\/h[23]>)/i';
            if (preg_match($discover_pattern, $content, $matches)) {
                $discover_heading = $matches[1];
                
                // Fetch and encode the image as base64
                $image_url = 'https://mgrnz.com/wp/wp-content/uploads/2025/11/DRIVE_Public_14-07-2025.png';
                $image_data = @file_get_contents($image_url);
                
                if ($image_data !== false) {
                    $base64_image = base64_encode($image_data);
                    $image_html = '<div style="text-align: center; margin: 30px 0;"><img src="data:image/png;base64,' . $base64_image . '" alt="DRIVE Framework" style="max-width: 100%; height: auto; display: block; margin: 0 auto;" /></div>';
                } else {
                    // Fallback if image can't be fetched
                    error_log('[PDF Generator] Failed to fetch DRIVE framework image from: ' . $image_url);
                    $image_html = '<div style="text-align: center; margin: 30px 0;"><img src="' . $image_url . '" alt="DRIVE Framework" style="max-width: 100%; height: auto; display: block; margin: 0 auto;" /></div>';
                }
                
                // Insert image right after the DISCOVER heading
                $content = str_replace($discover_heading, $discover_heading . $image_html, $content);
            }
        }
        
        // Don't strip all tags - preserve images and structure
        // Only strip potentially dangerous tags
        $content = strip_tags($content, '<h1><h2><h3><h4><p><strong><em><ul><ol><li><br><img><div><span><table><tr><td>');
        
        // Styles - Using solid colors for better PDF compatibility
        $styles = '
            @import url("https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap");
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: "Inter", Helvetica, Arial, sans-serif;
                line-height: 1.6;
                color: #1f2937;
                background: #ffffff;
                padding: 0;
                margin: 0;
            }
            .header { 
                text-align: center; 
                padding: 40px 20px 30px;
                background-color: #1e293b;
                border-bottom: 5px solid #ff4f00;
                margin-bottom: 30px;
            }
            .header h1 { 
                color: #ffffff !important; 
                font-size: 36px; 
                margin: 0 0 10px 0; 
                font-weight: 800;
            }
            .header p { 
                color: #e2e8f0 !important; 
                font-size: 18px;
                font-weight: 500;
                margin: 0;
            }
            .meta { 
                background: #f9fafb; 
                padding: 20px; 
                margin: 0 40px 30px 40px;
                border-radius: 8px; 
                border-left: 5px solid #ff4f00;
            }
            .meta p { margin: 8px 0; color: #4b5563; font-size: 14px; }
            .meta strong { color: #111827; }
            .content {
                padding: 0 40px;
            }
            .content h1 {
                color: #111827; 
                font-size: 28px; 
                margin: 30px 0 15px 0; 
                padding-bottom: 10px; 
               border-bottom: 3px solid #ff4f00; 
                font-weight: 800;
            }
            .content h2 { 
                color: #111827 !important; 
                font-size: 24px; 
                margin: 30px 0 15px 0; 
                padding-bottom: 10px; 
                border-bottom: 2px solid #ff4f00 !important; 
                font-weight: 700; 
                page-break-after: avoid;
            }
            .content h3 { 
                color: #374151 !important; 
                font-size: 20px; 
                margin: 25px 0 12px 0; 
                font-weight: 600; 
                page-break-after: avoid;
            }
            .content p { 
                margin: 12px 0; 
                color: #374151; 
                text-align: justify;
                font-size: 14px;
                line-height: 1.8;
            }
            .content ul, .content ol { 
                margin: 15px 0 15px 30px;
            }
            .content li { 
                margin: 8px 0; 
                color: #4b5563;
                font-size: 14px;
                line-height: 1.6;
            }
            .content strong { 
                color: #111827; 
                font-weight: 700;
            }
            .content img {
                max-width: 100% !important;
                height: auto !important;
                display: block;
                margin: 20px auto;
                border: 2px solid #e5e7eb;
                border-radius: 8px;
            }
            .footer { 
                margin-top: 50px; 
                padding: 30px 20px;
                background-color: #1e293b;
                border-top: 5px solid #ff4f00;
                text-align: center;
            }
            .footer p {
                color: #e2e8f0 !important;
                font-size: 13px;
                margin: 5px 0;
            }
            .footer strong {
                color: #ffffff !important;
                font-size: 15px;
                font-weight: 700;
            }
            .no-print { 
                background: #eff6ff; 
                border: 1px solid #bfdbfe; 
                border-radius: 8px; 
                padding: 20px; 
                margin: 20px; 
                text-align: center;
            }
            .no-print button { 
                background: #2563eb; 
                color: white; 
                border: none; 
                padding: 10px 20px; 
                border-radius: 6px; 
                cursor: pointer; 
                font-weight: 600; 
                margin-top: 10px;
            }
            @media print { 
                .no-print { display: none !important; } 
            }
        ';
        
        $auto_print_section = '';
        if (!$for_api) {
            $auto_print_section = '
            <div class="no-print">
                <h2>📄 Ready to Save as PDF!</h2>
                <p>Choose "Save as PDF" in the print dialog.</p>
                <button onclick="window.print()">🖨️ Print / Save</button>
            </div>
            <script>window.onload = function() { setTimeout(function() { window.print(); }, 500); };</script>
            ';
        }
        
        return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AI Workflow Blueprint</title>
    <style>' . $styles . '</style>
</head>
<body>
    ' . $auto_print_section . '
    
    <table width="100%" cellpadding="0" cellspacing="0" style="margin:0; padding:0; -webkit-print-color-adjust: exact; color-adjust: exact;">
        <tr>
            <td style="background-color: #1e293b; padding: 40px 20px 30px; text-align: center; border-bottom: 5px solid #ff4f00; -webkit-print-color-adjust: exact; color-adjust: exact;">
                <h1 style="color: #ffffff; font-size: 36px; margin: 0 0 10px 0; font-weight: 800; font-family: Inter, Helvetica, Arial, sans-serif;">AI Workflow Blueprint</h1>
                <p style="color: #e2e8f0; font-size: 18px; font-weight: 500; margin: 0; font-family: Inter, Helvetica, Arial, sans-serif;">Your Personalized Automation Strategy</p>
                <p style="color: #64748b; font-size: 10px; margin-top: 10px;">Generated: ' . date('Y-m-d H:i:s') . '</p>
            </td>
        </tr>
    </table>
    
    <div style="background: #f9fafb; padding: 20px; margin: 30px 40px; border-radius: 8px; border-left: 5px solid #ff4f00;">
        <p style="margin: 8px 0; color: #4b5563; font-size: 14px;"><strong style="color: #111827;">Prepared for:</strong> ' . esc_html($user_data['name'] ?? 'Valued Client') . '</p>
        <p style="margin: 8px 0; color: #4b5563; font-size: 14px;"><strong style="color: #111827;">Email:</strong> ' . esc_html($user_data['email'] ?? '') . '</p>
        <p style="margin: 8px 0; color: #4b5563; font-size: 14px;"><strong style="color: #111827;">Date:</strong> ' . date('F j, Y g:i A') . '</p>
    </div>
    
    <div class="content">
        ' . $content . '
    </div>
    
    <table width="100%" cellpadding="0" cellspacing="0" style="margin:50px 0 0 0; padding:0;">
        <tr>
            <td style="background-color: #1e293b; padding: 30px 20px; text-align: center; border-top: 5px solid #ff4f00;">
                <p style="color: #ffffff; font-size: 15px; font-weight: 700; margin: 5px 0; font-family: Inter, Helvetica, Arial, sans-serif;">MGRNZ - AI Workflow Automation</p>
                <p style="color: #e2e8f0; font-size: 13px; margin: 5px 0; font-family: Inter, Helvetica, Arial, sans-serif;">www.mgrnz.com | info@mgrnz.com</p>
                <p style="color: #e2e8f0; font-size: 13px; margin: 5px 0; font-family: Inter, Helvetica, Arial, sans-serif;">&copy; ' . date('Y') . ' MGRNZ. All rights reserved.</p>
            </td>
        </tr>
    </table>
</body>
</html>';
    }

    /**
     * Cleanup old PDF files
     */
    public static function cleanup_old_pdfs() {
        $upload_dir = wp_upload_dir();
        $pdf_dir = $upload_dir['basedir'] . '/blueprints';
        
        if (!file_exists($pdf_dir)) {
            return;
        }
        
        $files = glob($pdf_dir . '/blueprint-*');
        $now = time();
        
        foreach ($files as $file) {
            if (is_file($file) && ($now - filemtime($file)) > (7 * DAY_IN_SECONDS)) {
                unlink($file);
            }
        }
    }
}

// Schedule cleanup
add_action('init', function() {
    if (!wp_next_scheduled('mgrnz_cleanup_old_pdfs')) {
        wp_schedule_event(time(), 'daily', 'mgrnz_cleanup_old_pdfs');
    }
});

add_action('mgrnz_cleanup_old_pdfs', ['MGRNZ_PDF_Generator_V2', 'cleanup_old_pdfs']);
