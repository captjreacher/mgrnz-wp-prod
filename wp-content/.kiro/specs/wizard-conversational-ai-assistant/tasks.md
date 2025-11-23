# Implementation Plan

- [x] 1. Set up conversation management backend infrastructure








  - Create `mu-plugins/includes/class-conversation-manager.php` with session tracking, state management, and conversation path logic
  - Create `mu-plugins/includes/class-conversation-session.php` data model for storing session data, message history, and state
  - Create `mu-plugins/includes/class-chat-message.php` data model for individual messages
  - Add database tables for conversation sessions and messages using WordPress dbDelta
  - _Requirements: 4.1, 4.2, 5.1, 5.2_

- [x] 2. Implement REST API endpoints for chat functionality





  - [x] 2.1 Create chat message endpoint in `mu-plugins/mgrnz-ai-workflow-endpoint.php`


    - Add `POST /wp-json/mgrnz/v1/chat-message` endpoint to receive user messages
    - Integrate with Conversation_Manager to process messages and generate AI responses
    - Return assistant response with conversation state and next action
    - _Requirements: 3.5, 4.3, 4.4_
  - [x] 2.2 Create estimate generation endpoint

    - Add `POST /wp-json/mgrnz/v1/generate-estimate` endpoint
    - Use AI service to generate indicative cost estimates based on blueprint complexity
    - Return formatted estimate with setup cost, monthly cost, timeline, and disclaimer
    - _Requirements: 7.3_
  - [x] 2.3 Create quote request endpoint

    - Add `POST /wp-json/mgrnz/v1/request-quote` endpoint
    - Collect user contact details and additional notes
    - Store quote request in database and trigger email notification
    - _Requirements: 7.4_
  - [x] 2.4 Create blueprint subscription endpoint

    - Add `POST /wp-json/mgrnz/v1/subscribe-blueprint` endpoint
    - Validate and store user subscription details
    - Generate and return secure download URL for blueprint PDF
    - _Requirements: 8.4, 8.5_

- [x] 3. Build progress animation component






  - [x] 3.1 Create HTML structure and CSS for progress animation

    - Add progress container with message display area and progress bar to wizard page
    - Style with smooth animations, icons, and responsive design
    - Implement hide/show transitions
    - _Requirements: 2.2, 2.3_

  - [x] 3.2 Implement ProgressAnimation JavaScript class

    - Create class in `themes/mgrnz-theme/assets/js/progress-animation.js` with show(), hide(), addMessage(), updateProgress(), and complete() methods
    - Implement sequential message display with 2-second intervals
    - Add progress bar animation from 0% to 100%
    - Trigger callback when animation completes
    - _Requirements: 2.3, 2.4, 2.5_

- [x] 4. Build chat interface component





  - [x] 4.1 Create HTML structure and CSS for chat interface


    - Add chat container with header, message area, and input section to wizard page
    - Style assistant messages (left-aligned, gray) and user messages (right-aligned, blue)
    - Implement responsive design for mobile, tablet, and desktop
    - Add typing indicator animation
    - _Requirements: 3.1, 3.2, 3.4_
  - [x] 4.2 Implement ChatInterface JavaScript class


    - Create class in `themes/mgrnz-theme/assets/js/chat-interface.js` with show(), hide(), addMessage(), sendMessage(), and event handling methods
    - Implement auto-scroll to latest message
    - Add message timestamp display
    - Handle Enter key to send messages
    - _Requirements: 3.2, 3.3, 3.4, 3.5_
  - [x] 4.3 Integrate chat with REST API


    - Connect sendMessage() to `/wp-json/mgrnz/v1/chat-message` endpoint
    - Display typing indicator while waiting for AI response
    - Handle API responses and display assistant messages
    - Implement error handling with retry logic
    - _Requirements: 4.3, 4.4_

- [x] 5. Implement wizard submission flow with progress and chat transition





  - [x] 5.1 Update wizard controller to trigger progress animation


    - Modify `themes/mgrnz-theme/assets/js/wizard-controller.js` to hide wizard form on submission
    - Initialize ProgressAnimation with generated assistant name
    - Display sequential progress messages
    - _Requirements: 2.1, 2.2, 2.3, 2.4_
  - [x] 5.2 Transition from progress to chat interface


    - After progress animation completes, hide progress component
    - Show chat interface with assistant name in header
    - Load initial AI clarifying questions from backend
    - Display first assistant message automatically
    - _Requirements: 2.5, 3.1, 4.2_

- [x] 6. Implement conversation flow and state management



  - [x] 6.1 Add clarification question generation


    - Update `mu-plugins/includes/class-ai-service.php` to generate 2-5 clarifying questions based on wizard data
    - Use AI prompt template to create contextual, specific questions
    - Return questions to conversation manager
    - _Requirements: 4.2, 5.2_
  - [x] 6.2 Implement timeout handling for user inactivity


    - Add 60-second timeout timer in chat interface
    - Reset timer on user input
    - Trigger predetermined conversation path when timeout occurs
    - Display continue/wait quick action buttons
    - _Requirements: 5.1_
  - [x] 6.3 Implement conversation state transitions


    - Add state machine logic in Conversation_Manager: CLARIFICATION → UPSELL → BLUEPRINT_GENERATION → BLUEPRINT_PRESENTATION → COMPLETE
    - Trigger appropriate actions and messages for each state transition
    - Track conversation progress in session data
    - _Requirements: 5.2, 5.3, 5.5_

- [x] 7. Implement upsell conversation features






  - [x] 7.1 Add consultation booking integration

    - Display consultation offer message at appropriate conversation point
    - Add "Book Consultation" button that opens Calendly link in new tab
    - Track booking clicks in conversation metadata
    - Continue conversation after booking action
    - _Requirements: 7.1, 7.2, 7.5_
  - [x] 7.2 Implement cost estimate generation


    - Display estimate offer message during upsell phase
    - Call `/wp-json/mgrnz/v1/generate-estimate` endpoint when user accepts
    - Format and display AI-generated estimate in chat
    - Provide option to request formal quote
    - _Requirements: 7.1, 7.3, 7.5_

  - [x] 7.3 Implement formal quote request flow

    - Display quote offer message with 24-hour timeline
    - Show inline form to collect name, email, phone, and notes
    - Submit to `/wp-json/mgrnz/v1/request-quote` endpoint
    - Display confirmation message
    - _Requirements: 7.1, 7.4, 7.5_
  - [x] 7.4 Add additional workflow option


    - Offer to create second workflow during conversation
    - Provide button to start new wizard session
    - Preserve current session for later reference
    - _Requirements: 7.5_

- [x] 8. Implement blueprint diagram generation




  - [x] 8.1 Create diagram generator class


    - Create `mu-plugins/includes/class-diagram-generator.php` with methods to parse blueprint text and generate Mermaid diagram code
    - Implement workflow step extraction from blueprint text
    - Generate Mermaid flowchart syntax with nodes, edges, and decision points
    - _Requirements: 9.1, 9.2_
  - [x] 8.2 Integrate diagram rendering


    - Add Mermaid.js library to frontend assets
    - Render Mermaid code to SVG in blueprint display
    - Style diagram with clear boxes, arrows, and labels
    - Ensure mobile responsiveness with horizontal scroll if needed
    - _Requirements: 9.3, 9.4_

- [x] 9. Build blueprint presentation flow





  - [x] 9.1 Implement blueprint completion sequence


    - Display sequential completion messages in chat: "Agent reports Mission Complete", "Assistant Finalising Blueprint", "Assistant completing blueprint"
    - Add 2-second delays between messages
    - Transition to blueprint presentation after sequence
    - _Requirements: 6.1_
  - [x] 9.2 Create blueprint display component


    - Add HTML structure for blueprint with diagram section and text content section
    - Style with clear typography, section headings, and readable formatting
    - Implement CSS to prevent text selection and copying
    - Add "Download Blueprint" and "Request Changes" buttons
    - _Requirements: 6.2, 6.3_
  - [x] 9.3 Display blueprint in chat interface


    - Render blueprint as special message type in chat
    - Show both diagram and formatted text content
    - Offer refinement options via assistant message
    - Handle change requests by sending to AI for regeneration
    - _Requirements: 6.2, 6.3, 6.4_

- [x] 10. Implement blueprint subscription and download






  - [x] 10.1 Create subscription modal component

    - Add modal HTML structure with overlay, form, and close button
    - Style modal with centered layout and responsive design
    - Implement form validation for name and email fields
    - _Requirements: 8.2, 8.3_

  - [x] 10.2 Integrate subscription flow

    - Trigger modal when user clicks "Download Blueprint" button
    - Submit form data to `/wp-json/mgrnz/v1/subscribe-blueprint` endpoint
    - Store subscription details in database
    - Generate PDF with both diagram and text content
    - Provide download link after successful subscription
    - _Requirements: 8.1, 8.4, 8.5, 9.5_

  - [x] 10.3 Implement PDF generation

    - Create PDF generator using WordPress libraries or external service
    - Include blueprint diagram as image in PDF
    - Format text content with proper styling
    - Add branding and contact information
    - _Requirements: 9.5_

- [x] 11. Add error handling and edge cases





  - [x] 11.1 Implement API error handling


    - Add try-catch blocks for all API calls in chat interface
    - Display user-friendly error messages in chat
    - Implement exponential backoff retry logic
    - Log errors to WordPress error log
    - _Requirements: 4.4_
  - [x] 11.2 Handle blueprint generation failures


    - Catch AI service exceptions during blueprint generation
    - Display fallback message offering manual review
    - Send notification to admin email
    - Store failed request for manual processing
    - _Requirements: 4.5_
  - [x] 11.3 Implement input validation


    - Validate empty messages before sending
    - Add visual feedback (shake animation) for invalid input
    - Sanitize all user input on backend
    - Prevent XSS attacks with proper escaping
    - _Requirements: 3.3, 3.4_

- [x] 12. Implement security and rate limiting





  - [x] 12.1 Add rate limiting for chat messages


    - Implement 10 messages per minute per session limit
    - Add 50 messages per session total limit
    - Return rate limit error with retry-after header
    - Display rate limit message in chat interface
    - _Requirements: 3.5_
  - [x] 12.2 Implement input sanitization


    - Sanitize all user messages with `sanitize_textarea_field()` and `wp_kses()`
    - Escape output when displaying messages
    - Validate session IDs and prevent session hijacking
    - _Requirements: 8.5_
  - [x] 12.3 Add data privacy controls


    - Implement 30-day data retention policy for conversation sessions
    - Create cleanup cron job to delete old sessions
    - Add data deletion endpoint for GDPR compliance
    - _Requirements: 8.5_

- [x] 13. Optimize mobile responsiveness






  - [x] 13.1 Adapt chat interface for mobile

    - Implement responsive breakpoints (mobile < 768px, tablet 768-1024px, desktop > 1024px)
    - Make messages full-width on mobile
    - Increase touch target sizes to minimum 44px
    - Fix input at bottom of screen on mobile
    - _Requirements: 3.1, 3.2, 3.3, 3.4_
  - [x] 13.2 Optimize blueprint display for mobile


    - Stack diagram and text vertically on mobile
    - Enable horizontal scroll for wide diagrams
    - Implement collapsible sections for long content
    - Increase font sizes for readability
    - _Requirements: 6.2, 9.3, 9.4_
  - [x] 13.3 Make subscription modal mobile-friendly


    - Display modal full-screen on mobile devices
    - Increase form input sizes
    - Simplify layout for small screens
    - _Requirements: 8.2, 8.3_

- [x] 14. Add monitoring and analytics






  - Track wizard completion rate, chat engagement, upsell conversions, and blueprint downloads
  - Log key metrics to database for reporting
  - Create admin dashboard to view conversation analytics
  - _Requirements: All_

- [x] 15. Write comprehensive documentation






  - Document conversation flow logic and predetermined paths
  - Create admin guide for monitoring conversations and quotes
  - Write developer documentation for extending conversation features
  - Document API endpoints with request/response examples
  - _Requirements: All_
