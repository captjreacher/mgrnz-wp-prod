/**
 * Mark Quote as Requested on Form Submit
 * 
 * This script intercepts the MailerLite form submission and marks
 * the AI submission as "quote requested" in WordPress.
 */

(function() {
    'use strict';
    
    console.log('[Quote Marker] Script loaded');
    
    /**
     * Get submission_ref from localStorage
     */
    function getSubmissionRef() {
        try {
            const wizardData = localStorage.getItem('mgrnz_wizard_data');
            if (wizardData) {
                const data = JSON.parse(wizardData);
                return data.submission_ref || null;
            }
        } catch (e) {
            console.error('[Quote Marker] Error reading localStorage:', e);
        }
        return null;
    }
    
    /**
     * Mark quote as requested in WordPress
     */
    function markQuoteRequested(submissionRef) {
        const apiUrl = '/wp-json/mgrnz/v1/mark-quote-requested';
        
        fetch(apiUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                submission_ref: submissionRef
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('[Quote Marker] ✅ Quote request recorded:', data);
            } else {
                console.warn('[Quote Marker] Failed to record quote request:', data);
            }
        })
        .catch(error => {
            console.error('[Quote Marker] Error recording quote request:', error);
        });
    }
    
    /**
     * Find and attach to MailerLite form
     */
    function attachToForm() {
        // Find the MailerLite form
        const forms = document.querySelectorAll('form');
        let mlForm = null;
        
        for (let form of forms) {
            // Check if it's the quote form (has email input and submission_ref field)
            const hasEmail = form.querySelector('input[type="email"]');
            const hasSubmissionRef = form.querySelector('input[name*="submission_ref"]');
            
            if (hasEmail && hasSubmissionRef) {
                mlForm = form;
                break;
            }
        }
        
        if (!mlForm) {
            console.log('[Quote Marker] Form not found yet, will retry...');
            return false;
        }
        
        console.log('[Quote Marker] Found MailerLite form');
        
        // Get submission_ref
        const submissionRef = getSubmissionRef();
        if (!submissionRef) {
            console.warn('[Quote Marker] No submission_ref found in localStorage');
            return true; // Form found, but no ref
        }
        
        // Attach submit handler
        mlForm.addEventListener('submit', function(e) {
            console.log('[Quote Marker] Form submitted, marking quote as requested...');
            markQuoteRequested(submissionRef);
            
            // Don't prevent form submission - let MailerLite handle it
            // The marking happens in parallel
        });
        
        console.log('[Quote Marker] ✅ Attached to form, ready to mark quote on submit');
        return true;
    }
    
    /**
     * Initialize with retry logic
     */
    function init() {
        // Try immediately
        if (attachToForm()) {
            return;
        }
        
        // Retry a few times for dynamically loaded forms
        let attempts = 0;
        const maxAttempts = 10;
        
        const interval = setInterval(function() {
            attempts++;
            
            if (attachToForm() || attempts >= maxAttempts) {
                clearInterval(interval);
                if (attempts >= maxAttempts) {
                    console.warn('[Quote Marker] Could not find form after ' + maxAttempts + ' attempts');
                }
            }
        }, 500);
    }
    
    // Start when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
})();
