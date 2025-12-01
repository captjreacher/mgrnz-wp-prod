/**
 * Populate MailerLite submission_ref field with AI Submission ID
 * 
 * This script automatically fills the submission_ref field in MailerLite forms
 * with the AI session ID from the wizard.
 */

(function() {
    'use strict';
    
    console.log('[ML Populate] Script loaded');
    
    /**
     * Get AI submission ID from various sources
     */
    function getAISubmissionID() {
        // Try to get from localStorage (wizard data)
        try {
            const wizardData = localStorage.getItem('mgrnz_wizard_data');
            if (wizardData) {
                const data = JSON.parse(wizardData);
                if (data.submission_ref) {
                    console.log('[ML Populate] Found submission_ref in localStorage:', data.submission_ref);
                    return data.submission_ref;
                }
            }
        } catch (e) {
            console.warn('[ML Populate] Error reading localStorage:', e);
        }
        
        // Try to get from URL parameter
        const urlParams = new URLSearchParams(window.location.search);
        const urlSubmissionRef = urlParams.get('submission_ref') || urlParams.get('session_id');
        if (urlSubmissionRef) {
            console.log('[ML Populate] Found submission_ref in URL:', urlSubmissionRef);
            return urlSubmissionRef;
        }
        
        // Try to get from session storage
        try {
            const sessionSubmissionRef = sessionStorage.getItem('ai_submission_id');
            if (sessionSubmissionRef) {
                console.log('[ML Populate] Found submission_ref in sessionStorage:', sessionSubmissionRef);
                return sessionSubmissionRef;
            }
        } catch (e) {
            console.warn('[ML Populate] Error reading sessionStorage:', e);
        }
        
        console.warn('[ML Populate] No submission_ref found');
        return null;
    }
    
    /**
     * Populate the submission_ref field
     */
    function populateSubmissionRefField() {
        const submissionID = getAISubmissionID();
        
        if (!submissionID) {
            console.log('[ML Populate] No AI submission ID available to populate');
            return false;
        }
        
        // Find the submission_ref input field
        // Try multiple selectors to find the field
        const selectors = [
            'input[name="fields[submission_ref]"]',
            'input[name="submission_ref"]',
            'input[placeholder*="submission_ref"]',
            'input[id*="submission_ref"]',
            '#submission_ref',
            '.submission_ref'
        ];
        
        let field = null;
        for (const selector of selectors) {
            field = document.querySelector(selector);
            if (field) {
                console.log('[ML Populate] Found field with selector:', selector);
                break;
            }
        }
        
        if (!field) {
            console.warn('[ML Populate] submission_ref field not found. Tried selectors:', selectors);
            return false;
        }
        
        // Populate the field
        field.value = submissionID;
        
        // Trigger change event in case MailerLite is listening
        const event = new Event('change', { bubbles: true });
        field.dispatchEvent(event);
        
        // Also trigger input event
        const inputEvent = new Event('input', { bubbles: true });
        field.dispatchEvent(inputEvent);
        
        // Make field readonly so user doesn't change it
        field.setAttribute('readonly', 'readonly');
        field.style.backgroundColor = '#f0f0f0';
        field.style.cursor = 'not-allowed';
        
        console.log('[ML Populate] ✅ Successfully populated submission_ref field with:', submissionID);
        return true;
    }
    
    /**
     * Initialize - try to populate immediately and on DOM ready
     */
    function init() {
        console.log('[ML Populate] Initializing...');
        
        // Try immediately
        if (populateSubmissionRefField()) {
            return;
        }
        
        // If not found, wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(populateSubmissionRefField, 500);
            });
        } else {
            // DOM is already ready, try again after a short delay
            setTimeout(populateSubmissionRefField, 500);
        }
        
        // Also try again after 1 second (in case MailerLite form loads slowly)
        setTimeout(populateSubmissionRefField, 1000);
        
        // And one more time after 2 seconds
        setTimeout(populateSubmissionRefField, 2000);
        
        // Watch for dynamically added forms (MailerLite might load async)
        if (typeof MutationObserver !== 'undefined') {
            const observer = new MutationObserver(function(mutations) {
                for (const mutation of mutations) {
                    if (mutation.addedNodes.length > 0) {
                        // Check if any added nodes contain the submission_ref field
                        for (const node of mutation.addedNodes) {
                            if (node.nodeType === 1) { // Element node
                                const field = node.querySelector ? node.querySelector('input[name*="submission_ref"]') : null;
                                if (field) {
                                    console.log('[ML Populate] Detected dynamically added submission_ref field');
                                    setTimeout(populateSubmissionRefField, 100);
                                    break;
                                }
                            }
                        }
                    }
                }
            });
            
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
            
            console.log('[ML Populate] MutationObserver watching for dynamic forms');
        }
    }
    
    // Start initialization
    init();
    
    // Expose function globally in case it needs to be called manually
    window.populateMailerLiteSubmissionRef = populateSubmissionRefField;
    
})();
