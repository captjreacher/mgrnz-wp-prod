<?php
/**
 * Enqueue MailerLite Submission Ref Population Script
 * 
 * This script automatically populates the submission_ref field in MailerLite forms
 * with the AI session ID from the wizard.
 * 
 * @package MGRNZ_AI_Workflow
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue the populate script on pages with MailerLite forms
 */
function mgrnz_enqueue_mailerlite_populate_script() {
    // Enqueue on all pages (the script will only act if it finds the field)
    wp_enqueue_script(
        'mgrnz-mailerlite-populate',
        get_template_directory_uri() . '/templates/populate-mailerlite-submission-ref.js',
        [],
        filemtime(get_template_directory() . '/templates/populate-mailerlite-submission-ref.js'),
        true // Load in footer
    );
}
add_action('wp_enqueue_scripts', 'mgrnz_enqueue_mailerlite_populate_script');

/**
 * Alternative: Add inline script directly to footer
 * This ensures it runs even if the file enqueue doesn't work
 */
function mgrnz_add_mailerlite_populate_inline_script() {
    ?>
    <script>
    // Inline version of MailerLite submission_ref populator
    (function() {
        function getAISubmissionID() {
            try {
                const wizardData = localStorage.getItem('mgrnz_wizard_data');
                if (wizardData) {
                    const data = JSON.parse(wizardData);
                    if (data.submission_ref) return data.submission_ref;
                }
            } catch (e) {}
            
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get('submission_ref') || urlParams.get('session_id') || null;
        }
        
        function populateField() {
            const submissionID = getAISubmissionID();
            if (!submissionID) return false;
            
            const selectors = [
                'input[name="fields[submission_ref]"]',
                'input[name="submission_ref"]',
                'input[placeholder*="submission_ref"]',
                'input[id*="submission_ref"]'
            ];
            
            for (const selector of selectors) {
                const field = document.querySelector(selector);
                if (field) {
                    field.value = submissionID;
                    field.setAttribute('readonly', 'readonly');
                    field.style.backgroundColor = '#f0f0f0';
                    console.log('[ML] Populated submission_ref:', submissionID);
                    return true;
                }
            }
            return false;
        }
        
        // Try multiple times to catch dynamically loaded forms
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(populateField, 500);
                setTimeout(populateField, 1000);
                setTimeout(populateField, 2000);
            });
        } else {
            populateField();
            setTimeout(populateField, 500);
            setTimeout(populateField, 1000);
            setTimeout(populateField, 2000);
        }
        
        // Watch for dynamic forms
        if (typeof MutationObserver !== 'undefined') {
            const observer = new MutationObserver(function() {
                populateField();
            });
            observer.observe(document.body, { childList: true, subtree: true });
        }
    })();
    </script>
    <?php
}
add_action('wp_footer', 'mgrnz_add_mailerlite_populate_inline_script', 999);
