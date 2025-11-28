<?php
/**
 * PDF Generator Class
 * 
 * Generates PDF documents for blueprint downloads
 * Uses TCPDF library for PDF generation
 */

class MGRNZ_PDF_Generator {
    
    /**
     * Generate PDF from blueprint data
     * 
     * @param array $blueprint_data Blueprint content and diagram
     * @param array $user_data User information (name, email)
     * @param string $session_id Session ID
     * @return string|WP_Error Path to generated PDF or error
     */
    public function generate_blueprint_pdf($blueprint_data, $user_data, $session_id) {
        error_log('[PDF Generator] Starting PDF generation for session: ' . $session_id);
        
        try {
            // Try to find the autoloader in multiple possible locations
            $possible_paths = [
                __DIR__ . '/../vendor/autoload.php',
                WP_CONTENT_DIR . '/mu-plugins/vendor/autoload.php',
                ABSPATH . 'wp-content/mu-plugins/vendor/autoload.php'
            ];
            
            $autoload_loaded = false;
            
            foreach ($possible_paths as $path) {
                if (file_exists($path)) {
                    require_once $path;
                    error_log('[PDF Generator] Autoloader found and loaded at: ' . $path);
                    $autoload_loaded = true;
                    break;
                }
            }
            
            if (!$autoload_loaded) {
                error_log('[PDF Generator] WARNING: Could not find autoloader in any expected location.');
            }
            
            // Check if TCPDF is available
            if (!class_exists('TCPDF')) {
                error_log('[PDF Generator] TCPDF class not found. Using HTML fallback.');
                return $this->generate_simple_pdf($blueprint_data, $user_data, $session_id);
            }
            
            error_log('[PDF Generator] TCPDF available. Generating PDF...');
            
            // Create new PDF document
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            
            // Set document information
            $pdf->SetCreator('MGRNZ AI Workflow Wizard');
            $pdf->SetAuthor('MGRNZ');
            $pdf->SetTitle('AI Workflow Blueprint');
            $pdf->SetSubject('Workflow Automation Blueprint');
            
            // Remove default header/footer
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            
            // Set margins
            $pdf->SetMargins(15, 15, 15);
            $pdf->SetAutoPageBreak(true, 15);
            
            // Add a page
            $pdf->AddPage();
            
            // Set font
            $pdf->SetFont('helvetica', '', 11);
            
            // Add header with branding
            $this->add_pdf_header($pdf);
            
            // Add blueprint content
            $this->add_blueprint_content($pdf, $blueprint_data);
            
            // Add diagram if available
            if (isset($blueprint_data['diagram']) && !empty($blueprint_data['diagram'])) {
                $this->add_diagram_to_pdf($pdf, $blueprint_data['diagram']);
            }
            
            // Add footer with contact info
            $this->add_pdf_footer($pdf);
            
            // Generate filename
            $filename = $this->generate_filename($session_id, 'pdf');
            $upload_dir = wp_upload_dir();
            $pdf_dir = $upload_dir['basedir'] . '/blueprints';
            
            // Create directory if it doesn't exist
            if (!file_exists($pdf_dir)) {
                wp_mkdir_p($pdf_dir);
            }
            
            $pdf_path = $pdf_dir . '/' . $filename;
            
            // Output PDF to file
            $pdf->Output($pdf_path, 'F');
            
            return $pdf_path;
            
        } catch (Exception $e) {
            error_log('[PDF Generator] Error: ' . $e->getMessage());
            // Fallback to HTML if PDF generation fails
            return $this->generate_simple_pdf($blueprint_data, $user_data, $session_id);
        }
    }
    
    /**
     * Generate simple HTML-based fallback (opens print dialog for PDF save)
     */
    private function generate_simple_pdf($blueprint_data, $user_data, $session_id) {
        try {
            // Generate HTML version
            $html = $this->generate_blueprint_html($blueprint_data, $user_data);
            
            // Save as HTML file
            $filename = $this->generate_filename($session_id, 'html');
            $upload_dir = wp_upload_dir();
            $pdf_dir = $upload_dir['basedir'] . '/blueprints';
            
            // Create directory if it doesn't exist
            if (!file_exists($pdf_dir)) {
                wp_mkdir_p($pdf_dir);
            }
            
            $file_path = $pdf_dir . '/' . $filename;
            
            // Write HTML to file
            file_put_contents($file_path, $html);
            
            error_log('[PDF Generator] HTML file created: ' . $file_path);
            
            // Return the file path (not URL) - the handler will convert it
            return $file_path;
            
        } catch (Exception $e) {
            error_log('[PDF Generator] Fallback error: ' . $e->getMessage());
            return new WP_Error('pdf_generation_failed', 'Failed to generate document: ' . $e->getMessage());
        }
    }
    
    /**
     * Generate filename for blueprint
     * 
     * @param string $session_id Session ID
     * @param string $extension File extension (default: pdf)
     * @return string Filename
     */
    private function generate_filename($session_id, $extension = 'pdf') {
        $safe_session = preg_replace('/[^a-zA-Z0-9_-]/', '', $session_id);
        return 'blueprint-' . $safe_session . '-' . time() . '.' . $extension;
    }
    
    /**
     * Get download URL for generated file
     * 
     * @param string $file_path Full file path
     * @return string Download URL
     */
    public function get_download_url($file_path) {
        // If it's already a URL (from generate_simple_pdf), return as-is
        if (strpos($file_path, 'http') === 0 || strpos($file_path, '/wp-json/') === 0) {
            return $file_path;
        }
        
        // For HTML files, use the viewer endpoint
        if (strpos($file_path, '.html') !== false) {
            $filename = basename($file_path);
            return rest_url('mgrnz/v1/view-blueprint/' . $filename);
        }
        
        // For PDF files, return direct URL
        $upload_dir = wp_upload_dir();
        $file_url = str_replace($upload_dir['basedir'], $upload_dir['baseurl'], $file_path);
        return $file_url;
    }
    
    /**
     * Format content for HTML display
     * 
     * @param string $content Markdown or plain text content
     * @return string Formatted HTML
     */
    private function format_content_for_html($content) {
        if (empty($content)) {
            return '';
        }
        
        // Basic markdown-like formatting
        $html = $content;
        
        // Headers
        $html = preg_replace('/^### (.+)$/m', '<h3>$1</h3>', $html);
        $html = preg_replace('/^## (.+)$/m', '<h2>$1</h2>', $html);
        $html = preg_replace('/^# (.+)$/m', '<h1>$1</h1>', $html);
        
        // Bold
        $html = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $html);
        
        // Lists
        $html = preg_replace('/^- (.+)$/m', '<li>$1</li>', $html);
        $html = preg_replace('/(<li>.*<\/li>)/s', '<ul>$1</ul>', $html);
        
        // Line breaks
        $html = nl2br($html);
        
        return $html;
    }
    
    /**
     * Add PDF header
     */
    private function add_pdf_header($pdf) {
        $pdf->SetFont('helvetica', 'B', 20);
        $pdf->Cell(0, 10, 'AI Workflow Blueprint', 0, 1, 'C');
        $pdf->Ln(5);
    }
    
    /**
     * Add blueprint content to PDF
     */
    private function add_blueprint_content($pdf, $blueprint_data) {
        $content = $blueprint_data['content'] ?? '';
        $pdf->SetFont('helvetica', '', 11);
        $pdf->MultiCell(0, 5, strip_tags($content), 0, 'L');
    }
    
    /**
     * Add diagram to PDF
     */
    private function add_diagram_to_pdf($pdf, $diagram_data) {
        // Placeholder for diagram rendering
        $pdf->Ln(10);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, 'Workflow Diagram', 0, 1, 'L');
    }
    
    /**
     * Add PDF footer
     */
    private function add_pdf_footer($pdf) {
        $pdf->Ln(10);
        $pdf->SetFont('helvetica', 'I', 9);
        $pdf->Cell(0, 10, 'MGRNZ - AI Workflow Automation | www.mgrnz.com', 0, 1, 'C');
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
    
    /**
     * Generate blueprint HTML
     */
    private function generate_blueprint_html($blueprint_data, $user_data) {
        $content = $blueprint_data['content'] ?? 'No content available';
        
        // Build complete HTML document with print-to-PDF functionality
        $html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your AI Automation Blueprint</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #1e293b;
            background: #ffffff;
            padding: 40px;
            max-width: 900px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 3px solid #ff4f00;
        }
        .header h1 {
            color: #0f172a;
            font-size: 32px;
            margin-bottom: 10px;
        }
        .header p {
            color: #64748b;
            font-size: 16px;
        }
        .meta {
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-left: 4px solid #ff4f00;
        }
        .meta p {
            margin: 5px 0;
            color: #475569;
        }
        .content {
            margin: 30px 0;
        }
        .content h2 {
            color: #0f172a;
            font-size: 24px;
            margin: 30px 0 15px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #e2e8f0;
        }
        .content h3 {
            color: #1e293b;
            font-size: 20px;
            margin: 25px 0 12px 0;
        }
        .content p {
            margin: 12px 0;
            color: #334155;
        }
        .content ul, .content ol {
            margin: 15px 0 15px 30px;
        }
        .content li {
            margin: 8px 0;
            color: #475569;
        }
        .content strong {
            color: #0f172a;
            font-weight: 600;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 2px solid #e2e8f0;
            text-align: center;
            color: #64748b;
            font-size: 14px;
        }
        .footer p {
            margin: 8px 0;
        }
        .no-print {
            background: #f0f9ff;
            border: 2px solid #0284c7;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
            text-align: center;
        }
        .no-print h2 {
            color: #0369a1;
            font-size: 20px;
            margin-bottom: 10px;
        }
        .no-print p {
            color: #0c4a6e;
            margin: 8px 0;
        }
        .no-print button {
            background: #0284c7;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            margin-top: 15px;
            transition: background 0.2s;
        }
        .no-print button:hover {
            background: #0369a1;
        }
        @media print {
            .no-print { display: none !important; }
            body { padding: 20px; }
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        }
    </style>
    <script>
        // Automatically open print dialog when loaded
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</head>
<body>
    <div class="no-print">
        <h2>📄 Ready to Save as PDF!</h2>
        <p>Your blueprint is ready. The print dialog should open automatically.</p>
        <p><strong>Choose "Save as PDF"</strong> as your printer destination to save this document.</p>
        <button onclick="window.print()">🖨️ Print / Save as PDF</button>
    </div>
    
    <div class="header">
        <h1>AI Workflow Blueprint</h1>
        <p>Your Personalized Automation Strategy</p>
    </div>
    
    <div class="meta">
        <p><strong>Prepared for:</strong> ' . esc_html($user_data['name'] ?? 'Valued Client') . '</p>
        <p><strong>Email:</strong> ' . esc_html($user_data['email'] ?? '') . '</p>
        <p><strong>Date:</strong> ' . date('F j, Y g:i A') . '</p>
    </div>
    
    <div class="content">
        ' . $this->format_content_for_html($content) . '
    </div>
    
    <div class="footer">
        <p><strong>MGRNZ - AI Workflow Automation</strong></p>
        <p>www.mgrnz.com | info@mgrnz.com</p>
        <p style="margin-top: 15px; font-size: 11px;">
            <em>Disclaimer: This blueprint was generated by AI based on the information provided.
            While we strive for accuracy, please verify all technical details and costs before implementation.</em>
        </p>
        <p style="margin-top: 10px;">&copy; ' . date('Y') . ' MGRNZ. All rights reserved.</p>
    </div>
</body>
</html>';
        
        error_log('[PDF Generator] HTML generated successfully, length: ' . strlen($html));
        
        return $html;
    }
}

// Schedule cleanup cron job
add_action('init', function() {
    if (!wp_next_scheduled('mgrnz_cleanup_old_pdfs')) {
        wp_schedule_event(time(), 'daily', 'mgrnz_cleanup_old_pdfs');
    }
});

add_action('mgrnz_cleanup_old_pdfs', ['MGRNZ_PDF_Generator', 'cleanup_old_pdfs']);
