/**
 * Add Back Button to Quote Page
 * 
 * Adds a back button that returns to the blueprint view
 */

(function() {
    'use strict';
    
    console.log('[Back Button] Script loaded');
    
    function addBackButton() {
        // Check if we're on the quote page
        if (!window.location.pathname.includes('quote-my-workflow')) {
            return;
        }
        
        // Find the form container or main content area
        const formContainer = document.querySelector('.ml-form-embedContainer') || 
                            document.querySelector('[data-form-id]') ||
                            document.querySelector('form') ||
                            document.body;
        
        if (!formContainer) {
            console.warn('[Back Button] Could not find form container');
            return;
        }
        
        // Create back button
        const backButton = document.createElement('button');
        backButton.type = 'button';
        backButton.className = 'mgrnz-back-to-blueprint';
        backButton.innerHTML = '← Back to Blueprint';
        backButton.style.cssText = `
            display: inline-block;
            padding: 12px 24px;
            background: #1e293b;
            color: white;
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin: 20px 0;
            transition: all 0.2s;
        `;
        
        // Add hover effect
        backButton.addEventListener('mouseenter', function() {
            this.style.background = '#0f172a';
            this.style.borderColor = '#ff4f00';
        });
        
        backButton.addEventListener('mouseleave', function() {
            this.style.background = '#1e293b';
            this.style.borderColor = 'rgba(255,255,255,0.2)';
        });
        
        // Add click handler
        backButton.addEventListener('click', function() {
            // Try to go back to the blueprint
            if (document.referrer && document.referrer.includes('start-using-ai')) {
                window.history.back();
            } else {
                // Fallback: go to wizard page
                window.location.href = '/start-using-ai/';
            }
        });
        
        // Insert button before the form
        formContainer.parentNode.insertBefore(backButton, formContainer);
        
        console.log('[Back Button] ✅ Back button added');
    }
    
    // Try to add button after page loads
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(addBackButton, 500);
        });
    } else {
        addBackButton();
        setTimeout(addBackButton, 500);
        setTimeout(addBackButton, 1500);
    }
    
})();
