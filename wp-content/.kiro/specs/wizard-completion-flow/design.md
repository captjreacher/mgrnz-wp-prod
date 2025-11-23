# Wizard Completion Flow - Design Document

## Overview

This design implements a streamlined post-submission wizard experience. The wizard collapses after submission, displays animated progress messages during blueprint generation, then presents four clear action buttons. The design focuses on simplicity: no chat interface, no conversational AI, just progress feedback and clear next-step options.

## Architecture

### High-Level Flow

```
User Submits Wizard
    ↓
Wizard Collapses (300ms animation)
    ↓
Progress Container Appears
    ↓
Show Message 1: "Your Assistant [NAME] has been created..." (2s)
    ↓
Show Message 2: "Your Assistant [NAME] has deployed..." (2s)
    ↓
Show Message 3: "Building Chat..." (2s)
    ↓
[Blueprint generates in background]
    ↓
Hide Progress, Show Completion Screen
    ↓
Display: "What do you want to do next?"
    ↓
Show 4 Buttons: Edit | Download | Quote | Go Back
```

### Component Structure

```
wizard-controller.js (existing - modify)
    ├── collapseWizard()
    ├── showProgress()
    └── showCompletion()

progress-animation.js (new)
    ├── show()
    ├── displayMessages()
    └── complete()

completion-screen.js (new)
    ├── show()
    ├── attachButtonHandlers()
    └── hide()

subscription-modal.js (new)
    ├── show(mandatory)
    ├── validate()
    └── submit()

blog-popup.js (new)
    ├── show()
    ├── onYes()
    └── onNo()

quote-form.js (new)
    ├── show()
    ├── validate()
    └── submit()
```

## Implementation Details

### 1. Wizard Collapse

**File:** `themes/mgrnz-theme/assets/js/wizard-controller.js`

**Modifications:**
```javascript
// Add to existing WizardController class
collapseWizard() {
  const wizardContainer = document.querySelector('.wizard-container');
  wizardContainer.style.transition = 'all 0.3s ease-out';
  wizardContainer.style.opacity = '0';
  wizardContainer.style.transform = 'translateY(-20px)';
  
  setTimeout(() => {
    wizardContainer.style.display = 'none';
  }, 300);
}
```

**CSS:** `themes/mgrnz-theme/assets/css/custom.css`
```css
.wizard-container {
  transition: all 0.3s ease-out;
}

.wizard-container.collapsed {
  opacity: 0;
  transform: translateY(-20px);
  display: none;
}
```

### 2. Progress Animation Component

**File:** `themes/mgrnz-theme/assets/js/progress-animation.js`

**HTML Structure:**
```html
<div id="progress-container" style="display:none;">
  <div class="progress-messages">
    <div class="progress-message" id="msg-1" style="display:none;">
      <span class="icon">⚙️</span>
      <span class="text"></span>
    </div>
    <div class="progress-message" id="msg-2" style="display:none;">
      <span class="icon">🤖</span>
      <span class="text"></span>
    </div>
    <div class="progress-message" id="msg-3" style="display:none;">
      <span class="icon">💬</span>
      <span class="text"></span>
    </div>
  </div>
  <div class="progress-bar">
    <div class="progress-fill"></div>
  </div>
</div>
```

**JavaScript Class:**
```javascript
class ProgressAnimation {
  constructor() {
    this.container = document.getElementById('progress-container');
    this.messages = [
      { id: 'msg-1', template: 'Your Assistant {name} has been created...' },
      { id: 'msg-2', template: 'Your Assistant {name} has deployed a business analysis agent...' },
      { id: 'msg-3', template: 'Building Chat...' }
    ];
  }

  show() {
    this.container.style.display = 'block';
    this.container.style.opacity = '0';
    setTimeout(() => {
      this.container.style.transition = 'opacity 0.3s';
      this.container.style.opacity = '1';
    }, 50);
  }

  start(assistantName, onComplete) {
    this.displayMessage(0, assistantName, () => {
      this.displayMessage(1, assistantName, () => {
        this.displayMessage(2, assistantName, () => {
          setTimeout(() => {
            this.hide();
            onComplete();
          }, 1000);
        });
      });
    });
  }

  displayMessage(index, assistantName, callback) {
    const msg = this.messages[index];
    const element = document.getElementById(msg.id);
    const text = msg.template.replace('{name}', assistantName);
    
    element.querySelector('.text').textContent = text;
    element.style.display = 'flex';
    element.style.opacity = '0';
    
    setTimeout(() => {
      element.style.transition = 'opacity 0.5s';
      element.style.opacity = '1';
    }, 50);

    setTimeout(callback, 2000);
  }

  hide() {
    this.container.style.opacity = '0';
    setTimeout(() => {
      this.container.style.display = 'none';
    }, 300);
  }
}
```

### 3. Completion Screen Component

**File:** `themes/mgrnz-theme/assets/js/completion-screen.js`

**HTML Structure:**
```html
<div id="completion-screen" style="display:none;">
  <div class="completion-message">
    <h2>What do you want to do next?</h2>
  </div>
  <div class="action-buttons">
    <button id="btn-edit" class="action-btn">
      <span class="icon">✏️</span>
      Edit my Workflow
    </button>
    <button id="btn-download" class="action-btn">
      <span class="icon">⬇️</span>
      Download My Blueprint
    </button>
    <button id="btn-quote" class="action-btn">
      <span class="icon">💰</span>
      Get a Quote for this Workflow
    </button>
    <button id="btn-back" class="action-btn">
      <span class="icon">↩️</span>
      Go Back
    </button>
  </div>
</div>
```

**JavaScript Class:**
```javascript
class CompletionScreen {
  constructor(wizardController) {
    this.container = document.getElementById('completion-screen');
    this.wizardController = wizardController;
    this.attachHandlers();
  }

  show() {
    this.container.style.display = 'block';
    this.container.style.opacity = '0';
    setTimeout(() => {
      this.container.style.transition = 'opacity 0.3s';
      this.container.style.opacity = '1';
    }, 50);
  }

  hide() {
    this.container.style.opacity = '0';
    setTimeout(() => {
      this.container.style.display = 'none';
    }, 300);
  }

  attachHandlers() {
    document.getElementById('btn-edit').addEventListener('click', () => {
      this.hide();
      this.wizardController.reloadWithData();
    });

    document.getElementById('btn-download').addEventListener('click', () => {
      this.showDownloadFlow();
    });

    document.getElementById('btn-quote').addEventListener('click', () => {
      this.showQuoteForm();
    });

    document.getElementById('btn-back').addEventListener('click', () => {
      this.showBlogPopup();
    });
  }

  showDownloadFlow() {
    const modal = new SubscriptionModal(true); // mandatory
    modal.show('Subscribe to Download', 'Enter your details to download your blueprint');
    modal.onSubmit((data) => {
      this.wizardController.downloadBlueprint(data);
    });
  }

  showQuoteForm() {
    const quoteForm = new QuoteForm();
    quoteForm.show();
  }

  showBlogPopup() {
    const blogPopup = new BlogPopup();
    blogPopup.show();
  }
}
```

### 4. Subscription Modal Component

**File:** `themes/mgrnz-theme/assets/js/subscription-modal.js`

**HTML Structure:**
```html
<div id="subscription-modal" class="modal-overlay" style="display:none;">
  <div class="modal-content">
    <button class="modal-close" id="modal-close" style="display:none;">×</button>
    <h3 id="modal-title"></h3>
    <p id="modal-desc"></p>
    <form id="sub-form">
      <input type="text" id="sub-name" placeholder="Name" required>
      <span class="error" id="name-error"></span>
      <input type="email" id="sub-email" placeholder="Email" required>
      <span class="error" id="email-error"></span>
      <button type="submit">Subscribe</button>
    </form>
  </div>
</div>
```

**JavaScript Class:**
```javascript
class SubscriptionModal {
  constructor(mandatory = false) {
    this.modal = document.getElementById('subscription-modal');
    this.form = document.getElementById('sub-form');
    this.closeBtn = document.getElementById('modal-close');
    this.mandatory = mandatory;
    this.setupCloseButton();
    this.setupFormSubmit();
  }

  show(title, description) {
    document.getElementById('modal-title').textContent = title;
    document.getElementById('modal-desc').textContent = description;
    
    if (this.mandatory) {
      this.closeBtn.style.display = 'none';
      this.modal.onclick = null; // Prevent closing by clicking outside
    } else {
      this.closeBtn.style.display = 'block';
      this.modal.onclick = (e) => {
        if (e.target === this.modal) this.hide();
      };
    }

    this.modal.style.display = 'flex';
  }

  hide() {
    this.modal.style.display = 'none';
    this.form.reset();
  }

  setupCloseButton() {
    this.closeBtn.addEventListener('click', () => this.hide());
  }

  setupFormSubmit() {
    this.form.addEventListener('submit', (e) => {
      e.preventDefault();
      if (this.validate()) {
        const data = {
          name: document.getElementById('sub-name').value,
          email: document.getElementById('sub-email').value
        };
        if (this.submitCallback) {
          this.submitCallback(data);
        }
      }
    });
  }

  validate() {
    const name = document.getElementById('sub-name').value.trim();
    const email = document.getElementById('sub-email').value.trim();
    let valid = true;

    if (!name) {
      document.getElementById('name-error').textContent = 'Name is required';
      valid = false;
    } else {
      document.getElementById('name-error').textContent = '';
    }

    if (!email || !this.isValidEmail(email)) {
      document.getElementById('email-error').textContent = 'Valid email is required';
      valid = false;
    } else {
      document.getElementById('email-error').textContent = '';
    }

    return valid;
  }

  isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  }

  onSubmit(callback) {
    this.submitCallback = callback;
  }
}
```

### 5. Blog Subscription Popup

**File:** `themes/mgrnz-theme/assets/js/blog-popup.js`

**HTML Structure:**
```html
<div id="blog-popup" class="popup-overlay" style="display:none;">
  <div class="popup-content">
    <h3>Would you like to subscribe to my Blog?</h3>
    <div class="popup-buttons">
      <button id="blog-yes">Yes</button>
      <button id="blog-no">No</button>
    </div>
  </div>
  <div id="thanks-message" style="display:none;">
    <p id="thanks-text"></p>
  </div>
</div>
```

**JavaScript Class:**
```javascript
class BlogPopup {
  constructor() {
    this.popup = document.getElementById('blog-popup');
    this.setupHandlers();
  }

  show() {
    this.popup.style.display = 'flex';
  }

  hide() {
    this.popup.style.display = 'none';
  }

  setupHandlers() {
    document.getElementById('blog-yes').addEventListener('click', () => {
      this.showThanksMessage('Thanks for trying AI');
      setTimeout(() => {
        const modal = new SubscriptionModal(false); // optional
        modal.show('Subscribe to Blog', 'Stay updated with our latest content');
        modal.onSubmit((data) => {
          this.submitBlogSubscription(data);
          modal.hide();
          this.hide();
        });
      }, 1500);
    });

    document.getElementById('blog-no').addEventListener('click', () => {
      this.showThanksMessage('Thanks for trying AI!');
      setTimeout(() => {
        this.hide();
      }, 2000);
    });
  }

  showThanksMessage(text) {
    document.getElementById('thanks-text').textContent = text;
    document.getElementById('thanks-message').style.display = 'block';
    document.querySelector('.popup-content').style.display = 'none';
  }

  async submitBlogSubscription(data) {
    await fetch('/wp-json/mgrnz/v1/subscribe', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        ...data,
        subscription_type: 'blog'
      })
    });
  }
}
```

### 6. Quote Request Form

**File:** `themes/mgrnz-theme/assets/js/quote-form.js`

**HTML Structure:**
```html
<div id="quote-form" class="modal-overlay" style="display:none;">
  <div class="modal-content">
    <button class="modal-close" id="quote-close">×</button>
    <h3>Request a Quote</h3>
    <p>We'll send you a detailed quote within 24 hours</p>
    <form id="quote-request-form">
      <input type="text" id="quote-name" placeholder="Name" required>
      <input type="email" id="quote-email" placeholder="Email" required>
      <input type="tel" id="quote-phone" placeholder="Phone (optional)">
      <textarea id="quote-notes" placeholder="Additional notes" rows="4"></textarea>
      <button type="submit">Request Quote</button>
    </form>
  </div>
</div>
```

**JavaScript Class:**
```javascript
class QuoteForm {
  constructor() {
    this.modal = document.getElementById('quote-form');
    this.form = document.getElementById('quote-request-form');
    this.setupHandlers();
  }

  show() {
    this.modal.style.display = 'flex';
  }

  hide() {
    this.modal.style.display = 'none';
    this.form.reset();
  }

  setupHandlers() {
    document.getElementById('quote-close').addEventListener('click', () => this.hide());
    
    this.form.addEventListener('submit', async (e) => {
      e.preventDefault();
      if (this.validate()) {
        await this.submit();
      }
    });
  }

  validate() {
    const name = document.getElementById('quote-name').value.trim();
    const email = document.getElementById('quote-email').value.trim();
    return name && email && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  }

  async submit() {
    const data = {
      blueprint_id: window.currentBlueprintId,
      name: document.getElementById('quote-name').value,
      email: document.getElementById('quote-email').value,
      phone: document.getElementById('quote-phone').value,
      notes: document.getElementById('quote-notes').value
    };

    const response = await fetch('/wp-json/mgrnz/v1/request-quote', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    });

    if (response.ok) {
      alert('Quote request received. We\'ll send a detailed quote within 24 hours.');
      this.hide();
    }
  }
}
```

## REST API Endpoints

### 1. Subscribe Endpoint

**File:** `mu-plugins/mgrnz-ai-workflow-endpoint.php`

```php
add_action('rest_api_init', function() {
  register_rest_route('mgrnz/v1', '/subscribe', [
    'methods' => 'POST',
    'callback' => 'mgrnz_handle_subscription',
    'permission_callback' => '__return_true'
  ]);
});

function mgrnz_handle_subscription($request) {
  $params = $request->get_json_params();
  
  global $wpdb;
  $table = $wpdb->prefix . 'blueprint_subscriptions';
  
  $wpdb->insert($table, [
    'name' => sanitize_text_field($params['name']),
    'email' => sanitize_email($params['email']),
    'subscription_type' => sanitize_text_field($params['subscription_type']),
    'blueprint_id' => sanitize_text_field($params['blueprint_id'] ?? ''),
    'subscribed_at' => current_time('mysql')
  ]);

  if ($params['subscription_type'] === 'blueprint_download') {
    $download_url = mgrnz_generate_blueprint_pdf($params['blueprint_id']);
    return ['success' => true, 'download_url' => $download_url];
  }

  return ['success' => true];
}
```

### 2. Request Quote Endpoint

```php
add_action('rest_api_init', function() {
  register_rest_route('mgrnz/v1', '/request-quote', [
    'methods' => 'POST',
    'callback' => 'mgrnz_handle_quote_request',
    'permission_callback' => '__return_true'
  ]);
});

function mgrnz_handle_quote_request($request) {
  $params = $request->get_json_params();
  
  global $wpdb;
  $table = $wpdb->prefix . 'quote_requests';
  
  $wpdb->insert($table, [
    'blueprint_id' => sanitize_text_field($params['blueprint_id']),
    'name' => sanitize_text_field($params['name']),
    'email' => sanitize_email($params['email']),
    'phone' => sanitize_text_field($params['phone'] ?? ''),
    'notes' => sanitize_textarea_field($params['notes'] ?? ''),
    'requested_at' => current_time('mysql'),
    'status' => 'pending'
  ]);

  // Send email notification
  wp_mail(
    get_option('admin_email'),
    'New Quote Request',
    "Name: {$params['name']}\nEmail: {$params['email']}\nBlueprint: {$params['blueprint_id']}"
  );

  return ['success' => true, 'message' => 'Quote request received'];
}
```

## Database Tables

### blueprint_subscriptions

```sql
CREATE TABLE wp_blueprint_subscriptions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  subscription_type VARCHAR(50) NOT NULL,
  blueprint_id VARCHAR(100),
  subscribed_at DATETIME NOT NULL,
  INDEX(email),
  INDEX(blueprint_id)
);
```

### quote_requests

```sql
CREATE TABLE wp_quote_requests (
  id INT AUTO_INCREMENT PRIMARY KEY,
  blueprint_id VARCHAR(100) NOT NULL,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  phone VARCHAR(50),
  notes TEXT,
  requested_at DATETIME NOT NULL,
  status VARCHAR(20) DEFAULT 'pending',
  INDEX(blueprint_id),
  INDEX(status)
);
```

## CSS Styling

**File:** `themes/mgrnz-theme/assets/css/wizard-completion.css`

```css
/* Progress Animation */
#progress-container {
  padding: 40px 20px;
  text-align: center;
}

.progress-message {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  font-size: 18px;
  margin: 20px 0;
  opacity: 0;
  transition: opacity 0.5s;
}

.progress-message .icon {
  font-size: 24px;
}

.progress-bar {
  width: 100%;
  height: 4px;
  background: #e0e0e0;
  border-radius: 2px;
  margin-top: 30px;
  overflow: hidden;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(90deg, #4CAF50, #8BC34A);
  width: 0%;
  transition: width 0.5s ease;
}

/* Completion Screen */
#completion-screen {
  padding: 40px 20px;
  text-align: center;
}

.completion-message h2 {
  font-size: 28px;
  margin-bottom: 30px;
  color: #333;
}

.action-buttons {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
  max-width: 800px;
  margin: 0 auto;
}

.action-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  padding: 16px 24px;
  font-size: 16px;
  border: 2px solid #4CAF50;
  background: white;
  color: #4CAF50;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s;
  min-height: 60px;
}

.action-btn:hover {
  background: #4CAF50;
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
}

.action-btn .icon {
  font-size: 20px;
}

/* Modal Overlay */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

.modal-content {
  background: white;
  padding: 40px;
  border-radius: 12px;
  max-width: 500px;
  width: 90%;
  position: relative;
}

.modal-close {
  position: absolute;
  top: 10px;
  right: 10px;
  background: none;
  border: none;
  font-size: 24px;
  cursor: pointer;
  color: #999;
}

.modal-content h3 {
  margin-bottom: 10px;
  font-size: 24px;
}

.modal-content p {
  margin-bottom: 20px;
  color: #666;
}

.modal-content form input,
.modal-content form textarea {
  width: 100%;
  padding: 12px;
  margin-bottom: 15px;
  border: 1px solid #ddd;
  border-radius: 6px;
  font-size: 16px;
}

.modal-content form button {
  width: 100%;
  padding: 14px;
  background: #4CAF50;
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 16px;
  cursor: pointer;
  transition: background 0.3s;
}

.modal-content form button:hover {
  background: #45a049;
}

.error {
  color: #f44336;
  font-size: 14px;
  display: block;
  margin-top: -10px;
  margin-bottom: 10px;
}

/* Mobile Responsive */
@media (max-width: 768px) {
  .action-buttons {
    grid-template-columns: 1fr;
  }
  
  .modal-content {
    padding: 30px 20px;
  }
  
  .completion-message h2 {
    font-size: 22px;
  }
}
```

## Testing Checklist

- [ ] Wizard collapses smoothly on submit
- [ ] Progress messages display sequentially with 2s delays
- [ ] Assistant name is generated and displayed
- [ ] Blueprint generates during progress animation
- [ ] Completion screen shows after progress
- [ ] All 4 buttons are visible and clickable
- [ ] Edit Workflow reloads wizard with pre-filled data
- [ ] Download shows mandatory subscription modal
- [ ] Subscription modal cannot be closed (mandatory mode)
- [ ] Download works after subscription
- [ ] Get Quote shows form and sends email
- [ ] Go Back shows blog subscription popup
- [ ] Blog popup Yes shows optional modal
- [ ] Blog popup No shows thanks message
- [ ] All flows work on mobile devices
- [ ] Error handling works for API failures

## Deployment Steps

1. Add HTML structures to `page-start-using-ai.php`
2. Create new JavaScript files
3. Create new CSS file
4. Update `wizard-controller.js`
5. Add REST API endpoints to `mgrnz-ai-workflow-endpoint.php`
6. Create database tables
7. Test each flow individually
8. Test on mobile devices
9. Deploy to production
