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
        try {
            // Check if TCPDF is available
            if (!class_exists('TCPDF')) {
                // Try to load TCPDF from WordPress or use fallback
                return $this->generate_simple_pdf($blueprint_data, $user_data, $session_id);
            }
            
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
            return new WP_Error('pdf_generation_failed', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }
    
    /**
     * Generate simple text-based PDF fallback
     * 
     * @param array $blueprint_data Blueprint content
     * @param array $user_data User information
     * @param string $session_id Session ID
     * @return string|WP_Error Path to generated file or error
     */
    private function generate_simple_pdf($blueprint_data, $user_data, $session_id) {
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
    

    
    /**
     * Format markdown content for HTML
     * 
     * @param string $content Markdown content
     * @return string HTML content
     */
    private function format_content_for_html($content) {
        // Convert markdown-style formatting to HTML
        $formatted = $content;
        
        // Headers
        $formatted = preg_replace('/^### (.+)$/m', '<h4>$1</h4>', $formatted);
        $formatted = preg_replace('/^## (.+)$/m', '<h3>$1</h3>', $formatted);
        $formatted = preg_replace('/^# (.+)$/m', '<h2>$1</h2>', $formatted);
        
        // Bold
        $formatted = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $formatted);
        
        // Italic
        $formatted = preg_replace('/\*(.+?)\*/s', '<em>$1</em>', $formatted);
        
        // Lists
        $formatted = preg_replace('/^- (.+)$/m', '<li>$1</li>', $formatted);
        $formatted = preg_replace('/(<li>.*<\/li>)/s', '<ul>$1</ul>', $formatted);
        
        // Paragraphs
        $formatted = preg_replace('/\n\n/', '</p><p>', $formatted);
        $formatted = '<p>' . $formatted . '</p>';
        
        // Clean up empty paragraphs
        $formatted = preg_replace('/<p>\s*<\/p>/', '', $formatted);
        
        return $formatted;
    }
    
    /**
     * Add header to PDF
     * 
     * @param TCPDF $pdf PDF object
     */
    private function add_pdf_header($pdf) {
        // Add logo if available
        $logo_path = get_template_directory() . '/assets/images/logo.png';
        if (file_exists($logo_path)) {
            $pdf->Image($logo_path, 15, 15, 30, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        }
        
        // Add title
        $pdf->SetFont('helvetica', 'B', 20);
        $pdf->SetTextColor(33, 150, 243);
        $pdf->Cell(0, 15, 'Your AI Workflow Blueprint', 0, 1, 'C');
        
        $pdf->SetFont('helvetica', '', 11);
        $pdf->SetTextColor(102, 102, 102);
        $pdf->Cell(0, 5, 'Personalized automation plan generated by MGRNZ', 0, 1, 'C');
        
        $pdf->Ln(10);
    }
    
    /**
     * Add blueprint content to PDF
     * 
     * @param TCPDF $pdf PDF object
     * @param array $blueprint_data Blueprint data
     */
    private function add_blueprint_content($pdf, $blueprint_data) {
        $content = $blueprint_data['content'] ?? '';
        
        // Convert markdown to HTML for PDF
        $html = $this->format_content_for_html($content);
        
        // Write HTML content
        $pdf->writeHTML($html, true, false, true, false, '');
    }
    
    /**
     * Add diagram to PDF
     * 
     * @param TCPDF $pdf PDF object
     * @param array $diagram_data Diagram data
     */
    private function add_diagram_to_pdf($pdf, $diagram_data) {
        $pdf->AddPage();
        
        // Add section title
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->SetTextColor(33, 150, 243);
        $pdf->Cell(0, 10, 'Workflow Diagram', 0, 1, 'L');
        $pdf->Ln(5);
        
        // Add diagram
        if (isset($diagram_data['svg'])) {
            // Convert SVG to image if possible
            // For now, add a placeholder or the SVG code
            $pdf->SetFont('helvetica', '', 10);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->writeHTML($diagram_data['svg'], true, false, true, false, '');
        } elseif (isset($diagram_data['image_path'])) {
            // Add image if available
            $pdf->Image($diagram_data['image_path'], 15, $pdf->GetY(), 180, '', '', '', 'T', false, 300, '', false, false, 0, false, false, false);
        }
    }
    
    /**
     * Add footer to PDF
     * 
     * @param TCPDF $pdf PDF object
     */
    private function add_pdf_footer($pdf) {
        $pdf->AddPage();
        
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->SetTextColor(33, 150, 243);
        $pdf->Cell(0, 10, 'Next Steps', 0, 1, 'L');
        $pdf->Ln(5);
        
        $pdf->SetFont('helvetica', '', 11);
        $pdf->SetTextColor(0, 0, 0);
        
        $next_steps = '
        <p><strong>Ready to implement your AI workflow?</strong></p>
        <ul>
            <li>Book a free consultation to discuss implementation details</li>
            <li>Request a formal quote with exact pricing</li>
            <li>Explore our automation services at mgrnz.com</li>
        </ul>
        
        <p style="margin-top: 20px;"><strong>Contact Us:</strong></p>
        <p>
            Website: <a href="https://mgrnz.com">mgrnz.com</a><br>
            Email: info@mgrnz.com<br>
            Phone: +1 (555) 123-4567
        </p>
        
        <p style="margin-top: 20px; color: #666; font-size: 10px;">
            This blueprint was generated by the MGRNZ AI Workflow Wizard. 
            The recommendations are based on the information you provided and should be reviewed 
            by automation experts before implementation.
        </p>
        ';
        
        $pdf->writeHTML($next_steps, true, false, true, false, '');
    }
    
    /**
     * Generate unique filename for PDF
     * 
     * @param string $session_id Session ID
     * @param string $extension File extension (default: pdf)
     * @return string Filename
     */
    private function generate_filename($session_id, $extension = 'pdf') {
        $timestamp = time();
        $hash = substr(md5($session_id . $timestamp), 0, 8);
        return 'blueprint-' . $hash . '-' . $timestamp . '.' . $extension;
    }
    
    /**
     * Get download URL for generated PDF
     * 
     * @param string $pdf_path Path to PDF file
     * @return string Download URL
     */
    public function get_download_url($pdf_path) {
        $upload_dir = wp_upload_dir();
        $pdf_url = str_replace($upload_dir['basedir'], $upload_dir['baseurl'], $pdf_path);
        return $pdf_url;
    }
    
    /**
     * Clean up old PDF files (older than 7 days)
     */
    public static function cleanup_old_pdfs() {
        $upload_dir = wp_upload_dir();
        $pdf_dir = $upload_dir['basedir'] . '/blueprints';
        
        if (!file_exists($pdf_dir)) {
            return;
        }
        
        $files = glob($pdf_dir . '/blueprint-*');
        $now = time();
        $deleted = 0;
        
        foreach ($files as $file) {
            if (is_file($file)) {
                // Delete files older than 7 days
                if ($now - filemtime($file) >= 7 * 24 * 60 * 60) {
                    unlink($file);
                    $deleted++;
                }
            }
        }
        
        error_log("[PDF Generator] Cleaned up {$deleted} old blueprint files");
    }
    
    /**
     * Generate HTML version of blueprint
     * Creates a print-friendly HTML document
     * 
     * @param array $blueprint_data Blueprint content
     * @param array $user_data User information
     * @return string HTML content
     */
    private function generate_blueprint_html($blueprint_data, $user_data) {
        error_log('[PDF Generator] Generating HTML blueprint');
        error_log('[PDF Generator] Blueprint data keys: ' . json_encode(array_keys($blueprint_data ?? [])));
        
        $content = $blueprint_data['content'] ?? $blueprint_data['html'] ?? '';
        
        error_log('[PDF Generator] Content length: ' . strlen($content));
        error_log('[PDF Generator] Content preview: ' . substr($content, 0, 200));
        
        // Check if content is empty
        if (empty($content) || trim($content) === '') {
            error_log('[PDF Generator] WARNING: Empty content detected!');
            $content = '<h2>Error</h2><p>No blueprint content was provided. Please try generating your blueprint again.</p>';
        }
        
        // Clean any markdown code fences if present
        $content = preg_replace('/```html\s*/i', '', $content);
        $content = preg_replace('/```\s*$/m', '', $content);
        $content = preg_replace('/^```\s*/m', '', $content);
        $content = str_replace('```', '', $content);
        $content = trim($content);
        
        // Build complete HTML document
        $html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your AI Automation Blueprint</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #1a1a1a;
            max-width: 900px;
            margin: 0 auto;
            padding: 40px 20px;
            background: #fff;
        }
        h1 {
            color: #0f172a;
            font-size: 32px;
            border-bottom: 3px solid #ff4f00;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }
        h2 {
            color: #ff4f00;
            font-size: 24px;
            margin-top: 40px;
            margin-bottom: 15px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 8px;
        }
        h3 {
            color: #1e293b;
            font-size: 18px;
            margin-top: 25px;
            margin-bottom: 10px;
        }
        h4 {
            color: #334155;
            font-size: 16px;
            margin-top: 20px;
            margin-bottom: 8px;
        }
        p {
            margin-bottom: 15px;
            color: #374151;
        }
        ul, ol {
            margin: 15px 0;
            padding-left: 30px;
        }
        li {
            margin-bottom: 8px;
            color: #374151;
        }
        strong {
            font-weight: 600;
            color: #1a1a1a;
        }
        .header {
            text-align: center;
            margin-bottom: 50px;
            padding-bottom: 30px;
            border-bottom: 2px solid #e5e7eb;
        }
        .logo {
            max-width: 200px;
            margin-bottom: 20px;
        }
        .meta {
            background: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 40px;
            border-left: 4px solid #ff4f00;
        }
        .meta p {
            margin: 8px 0;
            font-size: 14px;
        }
        .content {
            margin: 30px 0;
        }
        .footer {
            margin-top: 60px;
            text-align: center;
            font-size: 12px;
            color: #6b7280;
            border-top: 2px solid #e5e7eb;
            padding-top: 30px;
        }
        .footer p {
            margin: 5px 0;
            color: #6b7280;
        }
        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #ff4f00;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            cursor: pointer;
            border: none;
            font-size: 14px;
            z-index: 1000;
        }
        .print-btn:hover {
            background: #e64500;
            box-shadow: 0 6px 8px rgba(0,0,0,0.15);
        }
        @media print {
            body {
                padding: 0;
                max-width: none;
            }
            .no-print {
                display: none !important;
            }
            .header {
                page-break-after: avoid;
            }
            h1, h2, h3, h4 {
                page-break-after: avoid;
            }
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-btn no-print">🖨️ Print / Save as PDF</button>
    
    <div class="header">
        <img src="' . home_url('/wp-content/uploads/2025/11/mgrnz-logo-full-300x146.png') . '" class="logo" alt="MGRNZ">
        <h1>Your 2-minute AI Automation Blueprint</h1>
    </div>
    
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
