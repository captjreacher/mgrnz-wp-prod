# Implementation Plan

- [x] 1. Create frontend JavaScript wizard controller





  - Create `themes/mgrnz-theme/assets/js/wizard-controller.js` with WizardController class that handles step navigation, form validation, and API communication
  - Implement step navigation methods (nextStep, previousStep, goToStep) with proper state management
  - Implement validation logic for each step including email format validation and required field checks
  - Implement form submission with fetch API call to REST endpoint including loading states and error handling
  - Implement blueprint rendering that converts markdown to HTML and displays in the blueprint section
  - Add event listeners for all wizard buttons (Next, Back, Submit, Subscribe, Consult)
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5, 6.1, 6.2, 6.3, 6.4_

- [x] 2. Enhance WordPress script enqueuing





  - Add `mgrnz_enqueue_wizard_scripts()` function to `themes/mgrnz-theme/functions.php` that conditionally loads wizard JavaScript only on the start-using-ai page
  - Use `wp_localize_script()` to pass REST API URL and nonce to JavaScript
  - Ensure proper script dependencies and loading order
  - _Requirements: 7.2, 7.3_

- [x] 3. Create AI service integration class





  - Create `mu-plugins/includes/class-ai-service.php` with MGRNZ_AI_Service class
  - Implement constructor that loads API credentials from WordPress options or environment variables
  - Implement `generate_blueprint()` method that orchestrates the AI call and response parsing
  - Implement `build_prompt()` method that creates structured prompts from user workflow data
  - Implement `call_openai_api()` method with proper authentication headers and error handling
  - Implement `call_anthropic_api()` method as alternative provider option
  - Implement `parse_response()` method that extracts and formats the blueprint from AI response
  - Implement `handle_api_error()` method with proper logging and user-friendly error messages
  - Add timeout handling (30 second limit) and retry logic for transient failures
  - _Requirements: 2.1, 2.2, 2.4, 5.2, 5.3_

- [x] 4. Create custom post type for submissions





  - Create `mu-plugins/includes/class-submission-cpt.php` with submission post type registration
  - Register `ai_workflow_sub` custom post type with appropriate labels and capabilities
  - Register custom meta fields for goal, workflow_description, tools, pain_points, email, blueprint data
  - Create custom admin columns showing submission date, email, goal preview, and email sent status
  - Implement meta box for viewing full submission details in WordPress admin
  - Add search and filter functionality for submissions in admin list view
  - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5_

- [x] 5. Create email service class





  - Create `mu-plugins/includes/class-email-service.php` with MGRNZ_Email_Service class
  - Implement `send_blueprint_email()` method that sends formatted blueprint to user
  - Create HTML email template with inline styles for email client compatibility
  - Implement template variable replacement for dynamic content (blueprint, user name, links)
  - Implement `send_subscription_confirmation()` method for newsletter signups
  - Add email delivery error handling and logging
  - Integrate with WordPress wp_mail() function with proper headers
  - _Requirements: 4.1, 4.2, 4.3, 4.4_

- [x] 6. Enhance REST API endpoint





  - Update `mu-plugins/mgrnz-ai-workflow-endpoint.php` to include comprehensive input validation
  - Implement `validate_submission_data()` function with sanitization for all input fields
  - Implement `save_submission()` function that creates custom post with all metadata
  - Integrate AI service class to generate blueprint from validated data
  - Integrate email service to send blueprint if email provided
  - Implement proper error responses with specific error codes and messages
  - Add rate limiting using WordPress transients (3 submissions per hour per IP)
  - Return structured JSON response with submission_id, blueprint, and email_sent status
  - _Requirements: 2.1, 2.2, 2.3, 3.1, 3.2, 4.1, 5.1, 5.5, 6.4_

- [x] 7. Create configuration and settings





  - Create `mu-plugins/config/ai-workflow-config.php` for environment-based configuration
  - Add WordPress admin settings page for AI provider selection and API key management
  - Implement settings fields for AI provider (OpenAI/Anthropic), API key, model selection, max tokens, temperature
  - Add settings validation and sanitization for API credentials
  - Store API keys securely using WordPress options with encryption or environment variables
  - Add settings UI with clear instructions and test connection button
  - _Requirements: 5.1, 5.4_

- [x] 8. Implement security measures





  - Add rate limiting check function in endpoint using IP-based transients
  - Implement nonce verification for REST API requests
  - Add input length validation (goal: 500 chars, workflow: 2000 chars, tools: 500 chars, pain_points: 1000 chars)
  - Sanitize all user inputs before storing or sending to AI service
  - Add CORS headers to restrict endpoint access
  - Implement API key validation before making external calls
  - _Requirements: 5.3, 5.5_

- [x] 9. Add blueprint caching system





  - Implement `get_cached_blueprint()` function that checks for existing blueprints based on input hash
  - Implement `cache_blueprint()` function that stores generated blueprints in WordPress transients
  - Create hash function for submission data to use as cache key
  - Set cache expiration to 7 days
  - Add cache bypass option for testing and admin users
  - _Requirements: 2.3_

- [x] 10. Implement async email processing





  - Create WordPress cron action `mgrnz_send_blueprint_email` for async email delivery
  - Update email sending to use `wp_schedule_single_event()` instead of blocking execution
  - Add cron handler function that processes queued emails
  - Implement email queue status tracking in submission metadata
  - _Requirements: 4.1_

- [x] 11. Add post-submission action handlers






  - Implement subscribe button handler in JavaScript that calls newsletter API or redirects to signup
  - Implement consult button handler that navigates to booking page with submission_id parameter
  - Add URL parameter handling on consultation booking page to pre-fill context
  - Update REST endpoint to handle subscription requests from blueprint view
  - _Requirements: 4.5, 6.5_

- [x] 12. Create main plugin initialization file





  - Create `mu-plugins/mgrnz-ai-workflow-wizard.php` as main plugin file
  - Include all class files (AI service, email service, submission CPT)
  - Initialize all components with proper WordPress hooks
  - Add plugin activation hook to create necessary database tables or options
  - Add plugin deactivation hook for cleanup
  - Register REST API routes and custom post types
  - _Requirements: All_

- [x] 13. Add error logging and monitoring






  - Implement comprehensive error logging for all failure points
  - Add success logging with metrics (submission_id, tokens_used, processing_time)
  - Create log viewing interface in WordPress admin
  - Add email notification for critical errors (API failures, rate limit exceeded)
  - _Requirements: 2.4, 5.3_
- [x] 14. Create admin dashboard for submissions









- [ ] 14. Create admin dashboard for submissions


  - Add admin menu page for viewing submission analytics
  - Display submission count by date range
  - Show common pain points and tools mentioned
  - Display AI API usage statistics (tokens, costs)
  - Add export functionality for submissions (CSV)
  - _Requirements: 3.4, 3.5_

- [x] 15. Write documentation






  - Create README.md with setup instructions for AI API keys
  - Document configuration options and environment variables
  - Add inline code comments explaining complex logic
  - Create user guide for WordPress admin features
  - Document troubleshooting steps for common issues
  - _Requirements: 7.4_
