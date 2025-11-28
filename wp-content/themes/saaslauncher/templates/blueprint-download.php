<?php
/**
 * Template Name: Blueprint Download (No Theme)
 * Description: Standalone page for blueprint download without theme interference
 */

// Don't load theme header/footer
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Your Blueprint</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            max-width: 500px;
            width: 100%;
            background: #1e293b;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            border: 1px solid rgba(255,255,255,0.1);
        }
        h1 {
            color: #fff;
            font-size: 28px;
            margin-bottom: 10px;
            text-align: center;
        }
        p {
            color: #cbd5e0;
            text-align: center;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        input {
            width: 100%;
            padding: 14px;
            border-radius: 8px;
            border: 1px solid #334155;
            background: #0f172a;
            color: #fff;
            font-size: 16px;
        }
        input:focus {
            outline: none;
            border-color: #ff4f00;
        }
        button {
            width: 100%;
            padding: 16px;
            background: #ff4f00;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
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
            margin-top: 20px;
            padding: 12px;
            border-radius: 8px;
            text-align: center;
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
        <h1>Almost There!</h1>
        <p>Enter your details to download your personalized AI Workflow Blueprint.</p>
        
        <div class="form-group">
            <input type="text" id="name" placeholder="Your Name" required>
        </div>
        
        <div class="form-group">
            <input type="email" id="email" placeholder="Your Email Address" required>
        </div>
        
        <button id="download-btn">Download Blueprint</button>
        
        <div id="message" class="message"></div>
    </div>

    <script>
    (function() {
        'use strict';
        
        console.log('=== BLUEPRINT DOWNLOAD PAGE LOADED ===');
        
        const nameInput = document.getElementById('name');
        const emailInput = document.getElementById('email');
        const downloadBtn = document.getElementById('download-btn');
        const message = document.getElementById('message');
        
        // Get blueprint data from localStorage
        const wizardData = JSON.parse(localStorage.getItem('mgrnz_wizard_data') || '{}');
        const blueprintHTML = localStorage.getItem('mgrnz_blueprint_download');
        
        console.log('Data check:', {
            hasWizardData: !!wizardData,
            hasBlueprintHTML: !!blueprintHTML,
            blueprintLength: blueprintHTML ? blueprintHTML.length : 0
        });
        
        if (!blueprintHTML) {
            message.textContent = 'No blueprint data found. Please go back and generate your blueprint first.';
            message.className = 'message error';
            message.style.display = 'block';
            downloadBtn.disabled = true;
            return;
        }
        
        downloadBtn.addEventListener('click', async function() {
            const name = nameInput.value.trim();
            const email = emailInput.value.trim();
            
            if (!name || !email) {
                message.textContent = 'Please fill in all fields.';
                message.className = 'message error';
                message.style.display = 'block';
                return;
            }
            
            if (!email.includes('@')) {
                message.textContent = 'Please enter a valid email address.';
                message.className = 'message error';
                message.style.display = 'block';
                return;
            }
            
            downloadBtn.disabled = true;
            downloadBtn.textContent = 'Processing...';
            message.style.display = 'none';
            
            console.log('=== SUBMITTING TO API ===');
            
            try {
                const response = await fetch('/wp-json/mgrnz/v1/subscribe-blueprint', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-WP-Nonce': '<?php echo wp_create_nonce("wp_rest"); ?>'
                    },
                    body: JSON.stringify({
                        session_id: wizardData.submission_ref || 'direct_' + Date.now(),
                        name: name,
                        email: email,
                        blueprint_data: {
                            html: blueprintHTML,
                            content: blueprintHTML
                        }
                    })
                });
                
                const data = await response.json();
                console.log('API Response:', data);
                
                if (data.success && data.download_url) {
                    message.textContent = 'Success! Downloading your blueprint...';
                    message.className = 'message success';
                    message.style.display = 'block';
                    
                    console.log('Download URL:', data.download_url);
                    
                    // Open blueprint in new tab (will trigger print dialog for PDF save)
                    window.open(data.download_url, '_blank');
                    
                    console.log('Blueprint opened in new tab');
                } else {
                    throw new Error(data.message || 'Download failed');
                }
            } catch (error) {
                console.error('Error:', error);
                message.textContent = 'Error: ' + error.message;
                message.className = 'message error';
                message.style.display = 'block';
                downloadBtn.disabled = false;
                downloadBtn.textContent = 'Download Blueprint';
            }
        });
    })();
    </script>
</body>
</html>
