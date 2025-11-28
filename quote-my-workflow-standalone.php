<?php
/**
 * Standalone Quote Page (bypasses WordPress template system)
 * Visit: http://mgrnz.local/quote-my-workflow-standalone.php
 */

// Load WordPress but don't use template
define('WP_USE_THEMES', false);
require_once __DIR__ . '/wp-load.php';

// Prevent any output buffering or headers from WordPress
while (ob_get_level()) {
    ob_end_clean();
}

header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quote My Workflow - MGRNZ</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            padding: 20px;
            color: #fff;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        h1 {
            text-align: center;
            margin-bottom: 2rem;
            font-size: 2.5rem;
        }
        .debug {
            background: #1e293b;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 12px;
            border: 1px solid #334155;
        }
        .form-container {
            background: #131c32;
            border-radius: 16px;
            padding: 2rem;
            border: 1px solid rgba(255,255,255,0.1);
            margin-top: 2rem;
        }
        .form-field {
            margin-bottom: 1.5rem;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #cbd5e0;
        }
        input, textarea {
            width: 100%;
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid #2d3748;
            background: #0f172a;
            color: #fff;
            font-size: 16px;
            font-family: inherit;
        }
        textarea {
            min-height: 150px;
            resize: vertical;
        }
        button {
            width: 100%;
            padding: 1rem;
            background: #ff4f00;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.2s;
        }
        button:hover {
            background: #e64500;
        }
        button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .message {
            margin-top: 1rem;
            padding: 1rem;
            border-radius: 8px;
            display: none;
        }
        .message.success {
            background: rgba(74, 222, 128, 0.1);
            color: #4ade80;
            border: 1px solid rgba(74, 222, 128, 0.2);
        }
        .message.error {
            background: rgba(255, 79, 0, 0.1);
            color: #ff4f00;
            border: 1px solid rgba(255, 79, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Quote My Workflow</h1>
        
        <div class="debug" id="debug-info"></div>
        
        <div class="form-container">
            <form id="quote-form">
                <div class="form-field">
                    <label for="blueprint-id">Blueprint ID (Auto-filled)</label>
                    <input type="text" id="blueprint-id" name="blueprint_id" readonly>
                </div>
                
                <div class="form-field">
                    <label for="name">Your Name *</label>
                    <input type="text" id="name" name="name" required>
                </div>
                
                <div class="form-field">
                    <label for="email">Your Email *</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-field">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone">
                </div>
                
                <div class="form-field">
                    <label for="company">Company</label>
                    <input type="text" id="company" name="company">
                </div>
                
                <div class="form-field">
                    <label for="message">Additional Details</label>
                    <textarea id="message" name="message" placeholder="Tell us more about your project..."></textarea>
                </div>
                
                <button type="submit" id="submit-btn">Request Quote</button>
                
                <div id="form-message" class="message"></div>
            </form>
        </div>
    </div>

    <script>
    console.log('=== STANDALONE QUOTE PAGE LOADED ===');
    
    // Get wizard data from localStorage
    const wizardData = JSON.parse(localStorage.getItem('mgrnz_wizard_data') || '{}');
    const blueprintHTML = localStorage.getItem('mgrnz_blueprint_download');
    
    console.log('Data check:', {
        hasWizardData: !!wizardData,
        hasBlueprintHTML: !!blueprintHTML,
        submissionRef: wizardData.submission_ref
    });
    
    // Show debug info
    const debugInfo = document.getElementById('debug-info');
    debugInfo.innerHTML = `
        <h3 style="color: #4ade80; margin-bottom: 10px;">📊 Debug Info</h3>
        <p><strong>Has wizard data:</strong> <span style="color: ${!!wizardData ? '#4ade80' : '#ff4f00'}">${!!wizardData ? 'YES' : 'NO'}</span></p>
        <p><strong>Submission Ref:</strong> ${wizardData.submission_ref || 'NONE'}</p>
        <p><strong>Has blueprint:</strong> <span style="color: ${!!blueprintHTML ? '#4ade80' : '#ff4f00'}">${!!blueprintHTML ? 'YES' : 'NO'}</span></p>
    `;
    
    // Pre-fill blueprint ID
    const blueprintIdField = document.getElementById('blueprint-id');
    if (wizardData.submission_ref) {
        blueprintIdField.value = wizardData.submission_ref;
        blueprintIdField.style.background = 'rgba(74, 222, 128, 0.1)';
        blueprintIdField.style.borderColor = '#4ade80';
        blueprintIdField.style.color = '#4ade80';
        blueprintIdField.style.fontWeight = 'bold';
        blueprintIdField.style.textAlign = 'center';
    } else {
        blueprintIdField.value = 'No blueprint ID found - please complete the wizard first';
        blueprintIdField.style.background = 'rgba(255, 79, 0, 0.1)';
        blueprintIdField.style.borderColor = '#ff4f00';
        blueprintIdField.style.color = '#ff4f00';
    }
    
    // Handle form submission
    document.getElementById('quote-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('submit-btn');
        const message = document.getElementById('form-message');
        
        const formData = {
            blueprint_id: wizardData.submission_ref || 'unknown',
            name: document.getElementById('name').value.trim(),
            email: document.getElementById('email').value.trim(),
            phone: document.getElementById('phone').value.trim(),
            company: document.getElementById('company').value.trim(),
            message: document.getElementById('message').value.trim()
        };
        
        console.log('Submitting quote request:', formData);
        
        submitBtn.disabled = true;
        submitBtn.textContent = 'Sending...';
        message.style.display = 'none';
        
        try {
            // Here you would send to your quote endpoint or email service
            // For now, just simulate success
            await new Promise(resolve => setTimeout(resolve, 1000));
            
            message.style.display = 'block';
            message.className = 'message success';
            message.textContent = 'Thank you! We\'ll be in touch soon with your quote.';
            
            // Reset form
            document.getElementById('quote-form').reset();
            blueprintIdField.value = wizardData.submission_ref;
            
            submitBtn.textContent = 'Request Quote';
            submitBtn.disabled = false;
            
        } catch (error) {
            console.error('Error:', error);
            message.style.display = 'block';
            message.className = 'message error';
            message.textContent = 'Error: ' + error.message;
            submitBtn.disabled = false;
            submitBtn.textContent = 'Request Quote';
        }
    });
    </script>
</body>
</html>
<?php
exit; // Prevent WordPress from adding anything
?>
