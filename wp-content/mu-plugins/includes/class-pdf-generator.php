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
            $filename = $this->generate_filename($session_id);
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
     * Generate simple text-based PDF fallback
     * ...
     */
    private function generate_simple_pdf($blueprint_data, $user_data, $session_id) {
        // ... existing code ...
        try {
            // Generate HTML version that can be converted to PDF
            $html = $this->generate_blueprint_html($blueprint_data, $user_data);
            
            // Save as HTML file (can be converted to PDF by browser)
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
            
            return $file_path;
            
        } catch (Exception $e) {
            error_log('[PDF Generator] Fallback error: ' . $e->getMessage());
            return new WP_Error('pdf_generation_failed', 'Failed to generate document: ' . $e->getMessage());
        }
    }

    // ... (skip to generate_blueprint_html) ...

    private function generate_blueprint_html($blueprint_data, $user_data) {
        // ... (existing code) ...
        
        // Build complete HTML document
        $html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your AI Automation Blueprint</title>
    <style>
        /* ... existing styles ... */
        @media print {
            .no-print { display: none !important; }
            /* Ensure background colors print */
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
    <div class="no-print" style="text-align: center; padding: 20px; background: #f0f9ff; border-bottom: 1px solid #bae6fd; margin-bottom: 30px;">
        <p style="font-size: 18px; color: #0369a1; margin-bottom: 10px;"><strong>📄 Ready to Save!</strong></p>
        <p style="color: #0c4a6e;">Your blueprint is ready. The print dialog should open automatically.</p>
        <p style="color: #0c4a6e;">Choose <strong>"Save as PDF"</strong> as your printer destination.</p>
        <button onclick="window.print()" style="background: #0284c7; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-size: 16px; margin-top: 10px;">🖨️ Print / Save as PDF</button>
    </div>
    
    <div class="header">
        <!-- ... rest of content ... -->

    
    <div class="meta">
        <p><strong>Prepared for:</strong> ' . esc_html($user_data['name'] ?? 'Valued Client') . '</p>
        <p><strong>Email:</strong> ' . esc_html($user_data['email'] ?? '') . '</p>
        <p><strong>Date:</strong> ' . date('F j, Y g:i A') . '</p>
    </div>
    
    <div class="content">
        ' . ($this->format_content_for_html($content) ?: nl2br(htmlspecialchars($content))) . '
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
