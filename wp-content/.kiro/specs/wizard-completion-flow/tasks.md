# Implementation Plan

- [x] 1. Add HTML structures to wizard page





  - Add progress animation container to `themes/mgrnz-theme/page-start-using-ai.php`
  - Add completion screen container with 4 action buttons
  - Add subscription modal HTML
  - Add blog popup HTML
  - Add quote form HTML
  - _Requirements: 2.1, 4.1_

- [x] 2. Create CSS for new components





  - Create `themes/mgrnz-theme/assets/css/wizard-completion.css` with styles for progress animation, completion screen, modals, and buttons
  - Add responsive styles for mobile (< 768px)
  - Add smooth transitions and animations
  - Enqueue CSS file in theme functions
  - _Requirements: 2.3, 4.4_

- [x] 3. Implement wizard collapse functionality






  - [x] 3.1 Modify wizard-controller.js to add collapse method

    - Add `collapseWizard()` method with 300ms animation
    - Update submit handler to call collapse before showing progress
    - _Requirements: 1.2, 1.3, 1.4, 1.5_

- [x] 4. Create progress animation component






  - [x] 4.1 Create progress-animation.js file

    - Implement `ProgressAnimation` class with show(), start(), displayMessage(), and hide() methods
    - Add sequential message display with 2-second delays
    - Generate unique assistant name
    - _Requirements: 2.2, 2.3, 2.4, 2.5_

- [x] 5. Create completion screen component






  - [x] 5.1 Create completion-screen.js file

    - Implement `CompletionScreen` class with show(), hide(), and button handlers
    - Attach click handlers for all 4 buttons
    - _Requirements: 4.1, 4.2, 4.3, 4.4_

- [x] 6. Implement Edit Workflow functionality






  - [x] 6.1 Add wizard reload with pre-filled data

    - Add `reloadWithData()` method to wizard-controller.js
    - Store wizard data in session/localStorage
    - Pre-fill all form fields when reloading
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5_

- [x] 7. Create subscription modal component




  - [x] 7.1 Create subscription-modal.js file


    - Implement `SubscriptionModal` class with show(), hide(), validate(), and submit() methods
    - Add mandatory mode (no close button, can't click outside)
    - Add optional mode (close button visible, can dismiss)
    - Implement form validation for name and email
    - _Requirements: 6.3, 6.4, 7.1, 7.2, 7.3, 7.4, 7.5_

- [x] 8. Implement download blueprint flow






  - [x] 8.1 Add download button handler

    - Show "Please subscribe to download" message
    - Display mandatory subscription modal
    - Call subscribe API endpoint
    - Generate and download PDF after successful subscription
    - _Requirements: 6.1, 6.2, 6.5_

- [x] 9. Create blog subscription popup






  - [x] 9.1 Create blog-popup.js file

    - Implement `BlogPopup` class with show(), hide(), onYes(), and onNo() methods
    - Handle Yes: show "Thanks for trying AI" → optional subscription modal
    - Handle No: show "Thanks for trying AI!" → end flow
    - _Requirements: 9.1, 9.2, 9.3, 9.4, 9.5_

- [x] 10. Create quote request form






  - [x] 10.1 Create quote-form.js file

    - Implement `QuoteForm` class with show(), hide(), validate(), and submit() methods
    - Add form fields: name, email, phone (optional), notes
    - Implement validation
    - Call quote request API endpoint
    - _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5_

- [x] 11. Create REST API endpoints






  - [x] 11.1 Add subscribe endpoint

    - Add `/wp-json/mgrnz/v1/subscribe` POST endpoint in `mu-plugins/mgrnz-ai-workflow-endpoint.php`
    - Validate and sanitize input data
    - Store subscription in database
    - Return download URL for blueprint subscriptions
    - _Requirements: 7.4, 7.5_

  - [x] 11.2 Add request quote endpoint


    - Add `/wp-json/mgrnz/v1/request-quote` POST endpoint
    - Validate and sanitize input data
    - Store quote request in database
    - Send email notification to admin
    - Return success confirmation
    - _Requirements: 8.4, 8.5_

- [ ] 12. Create database tables
  - [ ] 12.1 Add blueprint_subscriptions table
    - Create migration script in `mu-plugins/mgrnz-ai-workflow-wizard.php`
    - Add table with columns: id, name, email, subscription_type, blueprint_id, subscribed_at
    - Add indexes on email and blueprint_id
    - _Requirements: 7.5_
  - [ ] 12.2 Add quote_requests table
    - Create table with columns: id, blueprint_id, name, email, phone, notes, requested_at, status
    - Add indexes on blueprint_id and status
    - _Requirements: 8.4_

- [ ] 13. Implement PDF generation for blueprint download
  - [ ] 13.1 Create PDF generator function
    - Add `mgrnz_generate_blueprint_pdf()` function in mu-plugins
    - Include blueprint text and diagram in PDF
    - Add branding, user name, and generation date
    - Test completion screen button layout (stacked vertically)
    - Test modals (full-screen on mobile)
    - Test touch targets (minimum 44px)
    - _Requirements: 4.4_

- [ ]* 17. Add analytics tracking
  - Track button clicks (edit, download, quote, go back)
  - Track subscription submissions
  - Track quote requests
  - Store metrics in database for reporting
  - _Requirements: All_

- [ ]* 18. Write documentation
  - Document new JavaScript classes and their usage
  - Document REST API endpoints with examples
  - Document database schema
  - Create admin guide for viewing subscriptions and quotes
  - _Requirements: All_
