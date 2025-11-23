# Design Document

## Overview

This design document outlines the architecture and implementation approach for completing the AI Workflow Wizard system. The solution transforms the existing data collection form into a fully functional AI-powered workflow analysis tool by adding JavaScript functionality, AI integration, data persistence, and email delivery capabilities.

The design follows WordPress best practices, uses modern JavaScript (ES6+), integrates with external AI services, and maintains security and performance standards.

## Architecture

### System Components

```
┌─────────────────────────────────────────────────────────────┐
│                     Browser (Frontend)                       │
│  ┌────────────────────────────────────────────────────────┐ │
│  │  Wizard UI (HTML + CSS - already exists)              │ │
│  │  - 5-step form with progress indicator                │ │
│  │  - Blueprint display area                             │ │
│  └────────────────────────────────────────────────────────┘ │
│  ┌────────────────────────────────────────────────────────┐ │
│  │  wizard-controller.js (NEW)                           │ │
│  │  - Step navigation & validation                       │ │
│  │  - Form submission & API communication                │ │
│  │  - Blueprint rendering                                │ │
│  └────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
                            ↓ AJAX POST
┌─────────────────────────────────────────────────────────────┐
│              WordPress Backend (PHP)                         │
│  ┌────────────────────────────────────────────────────────┐ │
│  │  REST API Endpoint (ENHANCED)                         │ │
│  │  /wp-json/mgrnz/v1/ai-workflow                        │ │
│  │  - Input validation & sanitization                    │ │
│  │  - AI service orchestration                           │ │
│  │  - Data persistence                                   │ │
│  │  - Email triggering                                   │ │
│  └────────────────────────────────────────────────────────┘ │
│  ┌────────────────────────────────────────────────────────┐ │
│  │  AI Service Integration (NEW)                         │ │
│  │  - API client for OpenAI/Anthropic                    │ │
│  │  - Prompt engineering                                 │ │
│  │  - Response parsing                                   │ │
│  └────────────────────────────────────────────────────────┘ │
│  ┌────────────────────────────────────────────────────────┐ │
│  │  Custom Post Type: ai_workflow_submission (NEW)      │ │
│  │  - Stores all submission data                         │ │
│  │  - Admin UI for viewing submissions                   │ │
│  └────────────────────────────────────────────────────────┘ │
│  ┌────────────────────────────────────────────────────────┐ │
│  │  Email Service (NEW)                                  │ │
│  │  - HTML email templates                               │ │
│  │  - Blueprint delivery                                 │ │
│  │  - Newsletter integration                             │ │
│  └────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
                            ↓ API Call
┌─────────────────────────────────────────────────────────────┐
│              External AI Service                             │
│  (OpenAI GPT-4, Anthropic Claude, or configurable)         │
└─────────────────────────────────────────────────────────────┘
```

### Data Flow

1. **User Interaction**: User fills out wizard form, navigates steps
2. **Client Validation**: JavaScript validates each step before progression
3. **Form Submission**: JavaScript sends data to REST endpoint via fetch()
4. **Server Processing**: PHP endpoint validates, sanitizes, and processes data
5. **AI Generation**: Server calls AI service with structured prompt
6. **Data Persistence**: Server saves submission and blueprint to database
7. **Email Delivery**: Server sends blueprint email if address provided
8. **Response Display**: JavaScript receives blueprint and renders it to user

## Components and Interfaces

### 1. Frontend JavaScript Module

**File**: `themes/mgrnz-theme/assets/js/wizard-controller.js`

**Purpose**: Manages all client-side wizard interactions

**Key Classes/Functions**:

```javascript
class WizardController {
    constructor(formElement)
    
    // Navigation
    nextStep()
    previousStep()
    goToStep(stepNumber)
    updateProgress()
    
    // Validation
    validateCurrentStep()
    validateEmail(email)
    showError(message)
    clearErrors()
    
    // Submission
    async submitWizard()
    displayLoading()
    hideLoading()
    
    // Blueprint Display
    renderBlueprint(data)
    showBlueprintSection()
    hideFormSection()
    
    // Post-submission Actions
    handleSubscribe()
    handleConsult()
}
```

**Event Handlers**:
- Next button click → `validateCurrentStep()` → `nextStep()`
- Back button click → `previousStep()`
- Submit button click → `submitWizard()`
- Subscribe button click → `handleSubscribe()`
- Consult button click → `handleConsult()`

**API Communication**:
```javascript
async submitWizard() {
    const formData = {
        goal: document.getElementById('goal').value,
        workflow_description: document.getElementById('workflow').value,
        tools: document.getElementById('tools').value,
        pain_points: document.getElementById('pain_points').value,
        email: document.getElementById('email').value
    };
    
    const response = await fetch('/wp-json/mgrnz/v1/ai-workflow', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData)
    });
    
    return await response.json();
}
```

### 2. Enhanced REST API Endpoint

**File**: `mu-plugins/mgrnz-ai-workflow-endpoint.php` (enhanced)

**Endpoint Structure**:

```php
POST /wp-json/mgrnz/v1/ai-workflow

Request Body:
{
    "goal": "string",
    "workflow_description": "string",
    "tools": "string",
    "pain_points": "string",
    "email": "string (optional)"
}

Success Response (200):
{
    "status": "success",
    "submission_id": 123,
    "blueprint": {
        "summary": "string",
        "content": "markdown string"
    },
    "email_sent": true
}

Error Response (400/500):
{
    "status": "error",
    "message": "Error description",
    "code": "error_code"
}
```

**Processing Flow**:

```php
function handle_ai_workflow_submission($request) {
    // 1. Validate and sanitize input
    $data = validate_submission_data($request->get_json_params());
    
    // 2. Generate blueprint via AI
    $blueprint = generate_ai_blueprint($data);
    
    // 3. Save to database
    $submission_id = save_submission($data, $blueprint);
    
    // 4. Send email if provided
    $email_sent = false;
    if (!empty($data['email'])) {
        $email_sent = send_blueprint_email($data['email'], $blueprint);
    }
    
    // 5. Return response
    return new WP_REST_Response([
        'status' => 'success',
        'submission_id' => $submission_id,
        'blueprint' => $blueprint,
        'email_sent' => $email_sent
    ], 200);
}
```

### 3. AI Service Integration

**File**: `mu-plugins/includes/class-ai-service.php` (new)

**Purpose**: Handles communication with external AI APIs

**Class Structure**:

```php
class MGRNZ_AI_Service {
    private $api_key;
    private $provider; // 'openai' or 'anthropic'
    private $model;
    
    public function __construct()
    public function generate_blueprint($workflow_data)
    private function build_prompt($workflow_data)
    private function call_openai_api($prompt)
    private function call_anthropic_api($prompt)
    private function parse_response($raw_response)
    private function handle_api_error($error)
}
```

**Prompt Engineering**:

The AI prompt will be structured to generate consistent, actionable blueprints:

```
You are an AI workflow consultant. Based on the following information about a user's workflow, generate a detailed AI-enabled workflow blueprint.

USER INFORMATION:
- Goal: {goal}
- Current Workflow: {workflow_description}
- Tools: {tools}
- Pain Points: {pain_points}

Generate a blueprint with the following sections:

1. WORKFLOW ANALYSIS
   - Summary of current state
   - Key inefficiencies identified

2. AI-ENABLED SOLUTION
   - Specific AI tools and techniques to apply
   - How they address the pain points

3. IMPLEMENTATION ROADMAP
   - Step-by-step action plan
   - Quick wins (can implement immediately)
   - Long-term improvements

4. TOOL RECOMMENDATIONS
   - Specific AI tools to use
   - Integration suggestions with existing tools

Format the response in clean markdown with clear headings and bullet points.
```

**Configuration**:

Settings stored in WordPress options:
- `mgrnz_ai_provider` (openai/anthropic)
- `mgrnz_ai_api_key` (encrypted)
- `mgrnz_ai_model` (gpt-4, claude-3-opus, etc.)
- `mgrnz_ai_max_tokens` (default: 2000)
- `mgrnz_ai_temperature` (default: 0.7)

### 4. Custom Post Type for Submissions

**File**: `mu-plugins/includes/class-submission-cpt.php` (new)

**Post Type Registration**:

```php
register_post_type('ai_workflow_sub', [
    'labels' => [
        'name' => 'AI Workflow Submissions',
        'singular_name' => 'Submission'
    ],
    'public' => false,
    'show_ui' => true,
    'show_in_menu' => true,
    'capability_type' => 'post',
    'supports' => ['title', 'editor', 'custom-fields'],
    'menu_icon' => 'dashicons-analytics'
]);
```

**Meta Fields**:
- `_mgrnz_goal` (text)
- `_mgrnz_workflow_description` (text)
- `_mgrnz_tools` (text)
- `_mgrnz_pain_points` (text)
- `_mgrnz_email` (text)
- `_mgrnz_blueprint_summary` (text)
- `_mgrnz_blueprint_content` (longtext)
- `_mgrnz_submission_date` (datetime)
- `_mgrnz_email_sent` (boolean)

**Admin Columns**:
- Submission Date
- User Email
- Goal (truncated)
- Email Sent Status

### 5. Email Service

**File**: `mu-plugins/includes/class-email-service.php` (new)

**Purpose**: Handles all email communications

**Functions**:

```php
class MGRNZ_Email_Service {
    public function send_blueprint_email($to, $blueprint)
    public function send_subscription_confirmation($email)
    private function get_email_template($type)
    private function replace_template_variables($template, $data)
}
```

**Email Template Structure**:

```html
<!DOCTYPE html>
<html>
<head>
    <style>
        /* Inline styles for email compatibility */
        body { font-family: Arial, sans-serif; }
        .header { background: #0f172a; color: #fff; padding: 20px; }
        .content { padding: 30px; }
        .blueprint { background: #f5f5f5; padding: 20px; }
        .cta { background: #ff4f00; color: #fff; padding: 12px 24px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Your AI Workflow Blueprint</h1>
    </div>
    <div class="content">
        <p>Hi there,</p>
        <p>Thanks for using the AI Workflow Wizard. Here's your personalized blueprint:</p>
        <div class="blueprint">
            {{BLUEPRINT_CONTENT}}
        </div>
        <p><a href="{{CONSULT_LINK}}" class="cta">Book a Consultation</a></p>
    </div>
</body>
</html>
```

### 6. Script Enqueuing

**File**: `themes/mgrnz-theme/functions.php` (enhanced)

**New Function**:

```php
function mgrnz_enqueue_wizard_scripts() {
    // Only load on the wizard page
    if (is_page('start-using-ai')) {
        wp_enqueue_script(
            'mgrnz-wizard',
            get_template_directory_uri() . '/assets/js/wizard-controller.js',
            array(), // No dependencies
            '1.0.0',
            true // Load in footer
        );
        
        // Pass WordPress data to JavaScript
        wp_localize_script('mgrnz-wizard', 'mgrnzWizard', array(
            'ajaxUrl' => rest_url('mgrnz/v1/ai-workflow'),
            'nonce' => wp_create_nonce('wp_rest')
        ));
    }
}
add_action('wp_enqueue_scripts', 'mgrnz_enqueue_wizard_scripts');
```

## Data Models

### Submission Data Structure

```javascript
// Client-side (JavaScript)
{
    goal: string,              // Required, max 500 chars
    workflow_description: string, // Required, max 2000 chars
    tools: string,             // Required, max 500 chars
    pain_points: string,       // Required, max 1000 chars
    email: string              // Optional, valid email format
}
```

```php
// Server-side (PHP)
[
    'goal' => string,
    'workflow_description' => string,
    'tools' => string,
    'pain_points' => string,
    'email' => string|null,
    'submission_date' => datetime,
    'ip_address' => string,
    'user_agent' => string
]
```

### Blueprint Data Structure

```php
[
    'summary' => string,        // 1-2 sentence overview
    'content' => string,        // Full markdown blueprint
    'generated_at' => datetime,
    'ai_model' => string,       // Which AI model generated it
    'tokens_used' => int        // For cost tracking
]
```

## Error Handling

### Client-Side Errors

**Validation Errors**:
- Empty required fields → Display inline error message
- Invalid email format → Display email-specific error
- Network errors → Display "Connection failed, please try again"

**Error Display**:
```javascript
showError(message) {
    const statusDiv = document.getElementById('ai-wizard-status');
    statusDiv.textContent = message;
    statusDiv.classList.add('mgrnz-status-error');
    statusDiv.setAttribute('role', 'alert');
}
```

### Server-Side Errors

**Error Types and Responses**:

1. **Validation Error** (400):
```php
return new WP_REST_Response([
    'status' => 'error',
    'message' => 'Please fill in all required fields',
    'code' => 'validation_failed'
], 400);
```

2. **AI Service Error** (500):
```php
return new WP_REST_Response([
    'status' => 'error',
    'message' => 'Unable to generate blueprint. Please try again.',
    'code' => 'ai_service_error'
], 500);
```

3. **Rate Limit Error** (429):
```php
return new WP_REST_Response([
    'status' => 'error',
    'message' => 'Too many requests. Please try again in a few minutes.',
    'code' => 'rate_limit_exceeded'
], 429);
```

**Error Logging**:
```php
error_log(sprintf(
    '[AI WORKFLOW ERROR] %s | User: %s | Error: %s',
    date('Y-m-d H:i:s'),
    $user_email ?? 'anonymous',
    $error_message
));
```

## Testing Strategy

### Unit Testing

**JavaScript Tests** (using Jest):
- `WizardController.validateEmail()` - Test email validation logic
- `WizardController.validateCurrentStep()` - Test field validation
- `WizardController.updateProgress()` - Test progress calculation

**PHP Tests** (using PHPUnit):
- `validate_submission_data()` - Test input sanitization
- `MGRNZ_AI_Service::build_prompt()` - Test prompt generation
- `MGRNZ_Email_Service::send_blueprint_email()` - Test email formatting

### Integration Testing

**API Endpoint Tests**:
- Submit valid data → Expect 200 response with blueprint
- Submit invalid data → Expect 400 response with error
- Submit with missing fields → Expect 400 response
- Test AI service timeout handling
- Test email delivery success/failure

**End-to-End Tests**:
- Complete wizard flow from step 1 to blueprint display
- Test with and without email address
- Test error recovery (retry after failure)
- Test post-submission actions (subscribe, consult)

### Manual Testing Checklist

- [ ] Navigate through all 5 steps
- [ ] Test back button functionality
- [ ] Test validation on each step
- [ ] Submit form and verify blueprint generation
- [ ] Verify email delivery (if provided)
- [ ] Check submission appears in WordPress admin
- [ ] Test error scenarios (network failure, invalid input)
- [ ] Test on mobile devices
- [ ] Test with different AI providers
- [ ] Verify accessibility (keyboard navigation, screen readers)

## Security Considerations

### Input Validation

**Client-Side**:
- Validate field lengths before submission
- Sanitize HTML to prevent XSS
- Validate email format with regex

**Server-Side**:
```php
function validate_submission_data($data) {
    return [
        'goal' => sanitize_textarea_field($data['goal'] ?? ''),
        'workflow_description' => sanitize_textarea_field($data['workflow_description'] ?? ''),
        'tools' => sanitize_text_field($data['tools'] ?? ''),
        'pain_points' => sanitize_textarea_field($data['pain_points'] ?? ''),
        'email' => sanitize_email($data['email'] ?? '')
    ];
}
```

### API Security

**Rate Limiting**:
- Implement WordPress transients to limit submissions per IP
- Max 3 submissions per hour per IP address

```php
function check_rate_limit($ip_address) {
    $transient_key = 'ai_workflow_' . md5($ip_address);
    $submission_count = get_transient($transient_key) ?: 0;
    
    if ($submission_count >= 3) {
        return false;
    }
    
    set_transient($transient_key, $submission_count + 1, HOUR_IN_SECONDS);
    return true;
}
```

**API Key Protection**:
- Store AI API keys in wp-config.php or environment variables
- Never expose keys in client-side code
- Use WordPress options encryption for stored credentials

**CORS and Nonce**:
- Verify WordPress REST API nonce on requests
- Restrict endpoint access to same-origin requests

### Data Privacy

- Store minimal user data (no unnecessary tracking)
- Provide clear privacy notice on form
- Allow users to request data deletion (GDPR compliance)
- Don't send sensitive data to AI service (sanitize first)

## Performance Optimization

### Caching Strategy

**Blueprint Caching**:
- Cache generated blueprints for identical inputs (hash-based)
- Cache duration: 7 days
- Reduces AI API calls and costs

```php
function get_cached_blueprint($data_hash) {
    return get_transient('blueprint_' . $data_hash);
}

function cache_blueprint($data_hash, $blueprint) {
    set_transient('blueprint_' . $data_hash, $blueprint, 7 * DAY_IN_SECONDS);
}
```

### Async Processing

**Email Sending**:
- Use WordPress `wp_schedule_single_event()` for async email delivery
- Prevents blocking the API response

```php
wp_schedule_single_event(time(), 'mgrnz_send_blueprint_email', [
    $email,
    $blueprint
]);
```

### JavaScript Optimization

- Minify wizard-controller.js for production
- Use event delegation for button handlers
- Debounce validation on text inputs
- Lazy load blueprint rendering

## Deployment Considerations

### Environment Configuration

**Development**:
- Use test AI API keys
- Enable verbose error logging
- Disable email sending (log instead)

**Production**:
- Use production AI API keys
- Minimal error logging (security)
- Enable email sending
- Enable caching

### Configuration File

Create `mu-plugins/config/ai-workflow-config.php`:

```php
return [
    'ai_provider' => getenv('MGRNZ_AI_PROVIDER') ?: 'openai',
    'ai_api_key' => getenv('MGRNZ_AI_API_KEY'),
    'ai_model' => getenv('MGRNZ_AI_MODEL') ?: 'gpt-4',
    'enable_caching' => getenv('MGRNZ_ENABLE_CACHE') !== 'false',
    'enable_emails' => getenv('MGRNZ_ENABLE_EMAILS') !== 'false',
    'rate_limit' => (int) (getenv('MGRNZ_RATE_LIMIT') ?: 3),
];
```

### Monitoring

**Metrics to Track**:
- Submission count per day
- AI API response times
- AI API error rates
- Email delivery success rate
- Average tokens used per blueprint

**Logging**:
```php
// Log successful submissions
error_log(sprintf(
    '[AI WORKFLOW SUCCESS] Submission ID: %d | Tokens: %d | Time: %.2fs',
    $submission_id,
    $tokens_used,
    $processing_time
));
```

## Future Enhancements

### Phase 2 Possibilities

1. **Admin Dashboard**: Analytics view showing submission trends and common pain points
2. **Blueprint Refinement**: Allow users to request modifications to their blueprint
3. **Multi-language Support**: Generate blueprints in user's preferred language
4. **PDF Export**: Generate downloadable PDF version of blueprint
5. **Follow-up Automation**: Automated email sequence with implementation tips
6. **Integration Marketplace**: Connect directly to recommended tools
7. **Community Blueprints**: Share anonymized blueprints as case studies
8. **AI Model Selection**: Let users choose between different AI models
9. **Voice Input**: Allow users to speak their responses
10. **Blueprint Templates**: Pre-built templates for common workflow types
