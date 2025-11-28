<?php
/**
 * Standalone Subscribe Page (bypasses WordPress template system)
 * Visit: http://mgrnz.local/wizard-subscribe-standalone.php
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
    <title>Download Your Blueprint</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
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
            max-width: 600px;
            width: 100%;
            background: #131c32;
            border-radius: 16px;
            border: 1px solid rgba(255,255,255,0.1);
            padding: 2rem;
            color: #fff;
            text-align: center;
        }
        h1 { color: #fff; margin-bottom: 1rem; }
        p { color: #cbd5e0; margin-bottom: 2rem; }
        .form { display: flex; flex-direction: column; gap: 1rem; }
        input {
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid #2d3748;
            background: #0f172a;
            color: #fff;
            font-size: 16px;
        }
        button {
            padding: 1rem;
            background: #ff4f00;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.2s;
        }
        button:hover { background: #e64500; }
        button:disabled { background: #666; cursor: not-allowed; }
        .message {
            margin-top: 1rem;
            padding: 1rem;
            border-radius: 8px;
            display: none;
        }
        .message.success { background: #10b981; display: block; }
        .message.error { background: #ef4444; display: block; }
        .debug {
            background: #1e293b;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 12px;
            border: 1px solid #334155;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="debug" id="debug"></div>
        
        <h1>Almost There!</h1>
        <p>Enter your name and email to unlock your personalized AI Workflow Blueprint.</p>

        <div class="form">
            <input type="text" id="name" placeholder="Your Name" required>
            <input type="email" id="email" placeholder="Your Email Address" required>
            <button id="submit-btn">Unlock & Download Blueprint</button>
        </div>

        <div class="message" id="message"></div>
    </div>

    <script>
        console.log('=== STANDALONE SUBSCRIBE PAGE ===');
        
        // Get blueprint data from localStorage
        const blueprintHTML = localStorage.getItem('mgrnz_blueprint_download');
        const wizardData = JSON.parse(localStorage.getItem('mgrnz_wizard_data') || '{}');
        
        // Show debug info
        const debugEl = document.getElementById('debug');
        debugEl.innerHTML = `
            <strong>Debug Info:</strong><br>
            Has blueprint: ${!!blueprintHTML ? 'YES' : 'NO'}<br>
            Blueprint length: ${blueprintHTML ? blueprintHTML.length : 0} chars<br>
            Session ID: ${wizardData.submission_ref || 'none'}<br>
        `;
        
        console.log('Blueprint data:', {
            hasBlueprint: !!blueprintHTML,
            length: blueprintHTML ? blueprintHTML.length : 0,
            preview: blueprintHTML ? blueprintHTML.substring(0, 100) : 'NONE'
        });
        
        if (!blueprintHTML) {
            document.getElementById('message').className = 'message error';
            document.getElementById('message').textContent = 'No blueprint data found. Please generate your blueprint first.';
            document.getElementById('submit-btn').disabled = true;
        }
        
        // Handle form submission
        document.getElementById('submit-btn').addEventListener('click', async function() {
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const btn = this;
            const msg = document.getElementById('message');
            
            if (!name || !email) {
                msg.className = 'message error';
                msg.textContent = 'Please fill in all fields.';
                return;
            }
            
            btn.disabled = true;
            btn.textContent = 'Processing...';
            msg.style.display = 'none';
            
            try {
                console.log('Sending request...');
                
                const response = await fetch('/wp-json/mgrnz/v1/subscribe-blueprint', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        session_id: wizardData.submission_ref || 'standalone_' + Date.now(),
                        name: name,
                        email: email,
                        blueprint_data: {
                            content: blueprintHTML
                        }
                    })
                });
                
                const data = await response.json();
                console.log('Response:', data);
                
                if (data.success && data.download_url) {
                    msg.className = 'message success';
                    msg.textContent = 'Success! Opening your blueprint...';
                    
                    console.log('Download URL:', data.download_url);
                    
                    // Open in new tab
                    setTimeout(() => {
                        window.open(data.download_url, '_blank');
                    }, 500);
                } else {
                    throw new Error(data.message || 'Subscription failed');
                }
                
            } catch (error) {
                console.error('Error:', error);
                msg.className = 'message error';
                msg.textContent = 'Error: ' + error.message;
                btn.disabled = false;
                btn.textContent = 'Unlock & Download Blueprint';
            }
        });
    </script>
</body>
</html>
<?php
exit; // Prevent WordPress from adding anything
?>
