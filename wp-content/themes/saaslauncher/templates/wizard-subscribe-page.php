<?php
/**
 * Template Name: Wizard Subscribe Page
 * Description: A page to handle email subscription before downloading the blueprint
 */

// Don't load theme header/footer to avoid interference
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Your Blueprint - <?php bloginfo('name'); ?></title>
    <!-- Don't load wp_head to avoid theme script conflicts -->
    <script>
        // Prevent jQuery errors
        window.jQuery = window.$ = undefined;
        // Suppress console errors
        window.addEventListener('error', function(e) {
            if (e.message && (e.message.includes('jQuery') || e.message.includes('$'))) {
                e.preventDefault();
                return true;
            }
        });
    </script>
    <style>
        /* Aggressive CSS to prevent theme interference */
        html, body {
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            height: 100% !important;
            overflow-x: hidden !important;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif !important;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
            min-height: 100vh !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 20px !important;
        }
        .wizard-subscribe-container {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            position: relative !important;
            z-index: 9999 !important;
        }
        .wizard-subscribe-container * {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        input, button {
            display: block !important;
        }
    </style>
</head>
<body><?php

<div class="wizard-subscribe-container" style="max-width: 600px; margin: 4rem auto; padding: 2rem; background: #131c32; border-radius: 16px; border: 1px solid rgba(255,255,255,0.1); color: #fff; text-align: center;">
    <h1 style="color: #fff; margin-bottom: 1rem;">Almost There!</h1>
    <p style="color: #cbd5e0; margin-bottom: 2rem;">Enter your name and email to unlock your personalized AI Workflow Blueprint.</p>

    <div id="wizard-subscribe-form" style="display: flex; flex-direction: column; gap: 1rem;">
        <input type="text" id="sub-name" placeholder="Your Name" required style="padding: 1rem; border-radius: 8px; border: 1px solid #2d3748; background: #0f172a; color: #fff;">
        <input type="email" id="sub-email" placeholder="Your Email Address" required style="padding: 1rem; border-radius: 8px; border: 1px solid #2d3748; background: #0f172a; color: #fff;">
        
        <button type="button" id="sub-btn" style="padding: 1rem; background: #ff4f00; color: #fff; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; transition: background 0.2s;">
            Unlock & Download Blueprint
        </button>
    </div>

    <div id="sub-message" style="margin-top: 1rem; display: none;"></div>
</div>

<script>
// Disable any html2pdf or client-side PDF generation
window.html2pdf = undefined;
window.html2canvas = undefined;

// Prevent any script from hiding the page
console.log('=== PAGE LOAD START ===');
console.log('Body display:', window.getComputedStyle(document.body).display);
console.log('Body visibility:', window.getComputedStyle(document.body).visibility);

// Aggressively prevent content from being hidden
setInterval(function() {
    const container = document.querySelector('.wizard-subscribe-container');
    if (container) {
        const computed = window.getComputedStyle(container);
        if (computed.display === 'none' || computed.visibility === 'hidden' || computed.opacity === '0') {
            console.warn('Container was hidden! Forcing visible...');
            container.style.display = 'block';
            container.style.visibility = 'visible';
            container.style.opacity = '1';
        }
    }
    
    // Also check body
    const bodyComputed = window.getComputedStyle(document.body);
    if (bodyComputed.display === 'none') {
        console.warn('Body was hidden! Forcing visible...');
        document.body.style.display = 'flex';
    }
}, 100); // Check every 100ms

// Watch for any changes that might hide content
const observer = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
        if (mutation.type === 'attributes' && (mutation.attributeName === 'style' || mutation.attributeName === 'class')) {
            console.log('Style/class changed on:', mutation.target.className || mutation.target.tagName);
            const computed = window.getComputedStyle(mutation.target);
            console.log('Display:', computed.display, 'Visibility:', computed.visibility, 'Opacity:', computed.opacity);
            
            // Force visibility if hidden
            if (mutation.target.classList.contains('wizard-subscribe-container')) {
                if (computed.display === 'none' || computed.visibility === 'hidden') {
                    console.warn('Forcing container visible!');
                    mutation.target.style.display = 'block';
                    mutation.target.style.visibility = 'visible';
                    mutation.target.style.opacity = '1';
                }
            }
        }
    });
});

observer.observe(document.body, {
    attributes: true,
    subtree: true,
    attributeFilter: ['style', 'class']
});

document.addEventListener('DOMContentLoaded', function() {
    console.log('=== DOM CONTENT LOADED ===');
    const formContainer = document.getElementById('wizard-subscribe-form');
    const btn = document.getElementById('sub-btn');
    const msg = document.getElementById('sub-message');
    
    console.log('=== SUBSCRIBE PAGE LOADED ===');
    console.log('Disabling html2pdf...');
    
    // Retrieve wizard data from localStorage
    const wizardData = JSON.parse(localStorage.getItem('mgrnz_wizard_data') || '{}');
    const blueprintHTML = localStorage.getItem('mgrnz_blueprint_download');

    console.log('Subscribe Page - Data Check:', {
        hasWizardData: !!wizardData,
        hasBlueprintHTML: !!blueprintHTML,
        blueprintLength: blueprintHTML ? blueprintHTML.length : 0,
        wizardDataKeys: Object.keys(wizardData)
    });

    // Add debug info display
    const debugInfo = document.createElement('div');
    debugInfo.style.cssText = 'background: #1e293b; padding: 15px; margin-bottom: 20px; border-radius: 8px; font-family: monospace; font-size: 12px; border: 1px solid #334155;';
    debugInfo.innerHTML = `
        <h3 style="color: #4ade80; margin-top: 0; font-size: 14px;">📊 Debug Info (for troubleshooting)</h3>
        <p style="margin: 5px 0;"><strong>Has wizard data:</strong> <span style="color: ${!!wizardData ? '#4ade80' : '#ff4f00'}">${!!wizardData ? 'YES' : 'NO'}</span></p>
        <p style="margin: 5px 0;"><strong>Has blueprint HTML:</strong> <span style="color: ${!!blueprintHTML ? '#4ade80' : '#ff4f00'}">${!!blueprintHTML ? 'YES' : 'NO'}</span></p>
        <p style="margin: 5px 0;"><strong>Blueprint length:</strong> ${blueprintHTML ? blueprintHTML.length : 0} characters</p>
        ${wizardData.submission_ref ? `<p style="margin: 5px 0;"><strong>Submission Ref:</strong> ${wizardData.submission_ref}</p>` : ''}
        <details style="margin-top: 10px;">
            <summary style="cursor: pointer; color: #cbd5e0;">Blueprint Preview (first 500 chars)</summary>
            <pre style="background: #000; padding: 10px; border-radius: 4px; overflow-x: auto; max-height: 200px; margin-top: 10px; font-size: 11px;">${blueprintHTML ? blueprintHTML.substring(0, 500) + '...' : 'EMPTY'}</pre>
        </details>
    `;
    const container = document.querySelector('.wizard-subscribe-container');
    if (container) {
        container.insertBefore(debugInfo, container.firstChild);
    }

    if (!blueprintHTML || blueprintHTML.trim().length === 0) {
        msg.style.display = 'block';
        msg.style.color = '#ff4f00';
        msg.textContent = 'Error: No blueprint data found. Please go back and generate your blueprint first.';
        btn.disabled = true;
        return;
    }

    btn.addEventListener('click', async function(e) {
        e.preventDefault();
        e.stopPropagation();
        e.stopImmediatePropagation();
        
        console.log('=== BUTTON CLICKED - OUR HANDLER ===');
        
        const name = document.getElementById('sub-name').value.trim();
        const email = document.getElementById('sub-email').value.trim();
        
        if (!name || !email) {
            alert('Please fill in all fields.');
            return;
        }

        btn.disabled = true;
        btn.textContent = 'Processing...';
        msg.style.display = 'none';

        const requestData = {
            session_id: wizardData.submission_ref || 'unknown',
            name: name,
            email: email,
            blueprint_data: {
                html: blueprintHTML,
                content: blueprintHTML // Send both for compatibility
            }
        };

        console.log('Sending subscription request:', {
            session_id: requestData.session_id,
            name: requestData.name,
            email: requestData.email,
            blueprint_length: blueprintHTML.length,
            blueprint_preview: blueprintHTML.substring(0, 200)
        });
        
        console.log('Full request data:', requestData);

        try {
            // 1. Subscribe User
            const subResponse = await fetch('/wp-json/mgrnz/v1/subscribe-blueprint', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-WP-Nonce': '<?php echo wp_create_nonce("wp_rest"); ?>'
                },
                body: JSON.stringify(requestData)
            });

            const subData = await subResponse.json();
            
            console.log('API Response:', subData);

            if (subData.success && subData.download_url) {
                // Success! Redirect to download
                msg.style.display = 'block';
                msg.style.color = '#4ade80';
                msg.textContent = 'Success! Downloading your blueprint...';
                
                console.log('=== DOWNLOAD URL ===');
                console.log('URL:', subData.download_url);
                console.log('Full response:', subData);
                
                // Open blueprint in new tab (will trigger print dialog for PDF save)
                console.log('Opening blueprint in new tab:', subData.download_url);
                window.open(subData.download_url, '_blank');
                
                // Optional: Clear storage after success?
                // localStorage.removeItem('mgrnz_blueprint_download');
            } else {
                console.error('API returned error:', subData);
                throw new Error(subData.message || 'Subscription failed');
            }

        } catch (error) {
            console.error('Subscription Error:', error);
            msg.style.display = 'block';
            msg.style.color = '#ff4f00';
            msg.textContent = 'Error: ' + error.message + ' (Check browser console for details)';
            btn.disabled = false;
            btn.textContent = 'Unlock & Download Blueprint';
        }
    });
});
</script>

<!-- Don't load wp_footer to avoid theme script conflicts -->
</body>
</html>
