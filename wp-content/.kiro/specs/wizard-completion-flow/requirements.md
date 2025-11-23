# Wizard Completion Flow - Requirements

## Introduction

This specification defines requirements for a streamlined wizard completion experience. After the 5-step wizard is submitted, it collapses and displays progress animations while the blueprint generates. Upon completion, users see four clear action buttons for their next steps: editing the workflow, downloading the blueprint (with mandatory subscription), requesting a quote, or going back (with optional blog subscription offer).

## Glossary

- **Wizard System**: The 5-step form interface collecting goal, workflow, tools, pain points, and email
- **Blueprint**: The AI-generated workflow automation plan document containing both text and diagram
- **Progress Animation**: Visual feedback showing assistant creation and blueprint generation status
- **Completion Screen**: The interface displaying four action buttons after blueprint generation
- **Subscription Modal**: A popup overlay requesting user contact details
- **Mandatory Subscription**: Required subscription before blueprint download (cannot close modal)
- **Optional Subscription**: Blog subscription offer that can be declined (can close modal)
- **Action Buttons**: Four buttons: Edit Workflow, Download Blueprint, Get Quote, Go Back
- **AI Backend**: The existing mu-plugins OpenAI/Anthropic integration service

## Current State

- 5-step wizard collecting: goal, workflow, tools, pain points, email
- Real blueprint generation via OpenAI/Anthropic API
- Blueprint displays immediately after generation
- Working in WPCode on saaslauncher theme

## Desired User Journey

### Phase 1: Wizard Submission
User completes 5-step wizard → Clicks "Build my AI workflow"

### Phase 2: Progress Animation
- Wizard collapses (slides up or fades out)
- Progress animation appears in its place
- Sequential messages display:
  1. "Your Assistant [NAME] has been created..."
  2. "Your Assistant [NAME] has deployed a business analysis agent..."
  3. "Building Chat..."
- Blueprint generates in background during animation

### Phase 3: Completion Screen
- Progress animation completes
- Display message: "What do you want to do next?"
- Show 4 action buttons:
  - **Button 1:** Edit my Workflow
  - **Button 2:** Download My Blueprint
  - **Button 3:** Get a Quote for this Workflow
  - **Button 4:** Go Back

### Phase 4: Button Actions

**Button 1 - Edit my Workflow:**
- Reload wizard form
- Pre-fill all fields with user's previous inputs
- Allow user to modify and resubmit

**Button 2 - Download My Blueprint:**
- Display message: "Please subscribe to download"
- Show mandatory subscription modal
- User must enter name and email
- Cannot close modal without submitting
- After successful subscription, enable download
- Download PDF with blueprint text and diagram

**Button 3 - Get a Quote for this Workflow:**
- Show quote request form
- Collect: name, email, phone (optional), notes
- Submit to backend
- Send email notification to admin
- Display confirmation: "Quote request received. We'll send a detailed quote within 24 hours."

**Button 4 - Go Back:**
- Show popup: "Would you like to subscribe to my Blog?"
- Two options: Yes or No
- **If Yes:**
  - Display message: "Thanks for trying AI"
  - Show optional subscription modal (can close without submitting)
  - If user subscribes, store data
  - End flow
- **If No:**
  - Display message: "Thanks for trying AI!"
  - End flow

## Requirements

### REQ-1: Wizard Submission and Collapse
**User Story:** As a user, I want the wizard to collapse smoothly after submission so I can see progress feedback.

#### Acceptance Criteria
1.1. WHEN the user clicks the submit button, THE Wizard System SHALL validate all required fields
1.2. WHEN validation passes, THE Wizard System SHALL collapse the wizard form within 300 milliseconds
1.3. WHEN the wizard collapses, THE Wizard System SHALL use smooth animation (slide up or fade out)
1.4. WHEN the wizard is collapsed, THE Wizard System SHALL hide all wizard form elements
1.5. WHEN the wizard collapses, THE Wizard System SHALL show the Progress Animation component in the same location

### REQ-2: Progress Animation Display
**User Story:** As a user, I want to see progress messages so I understand what's happening while my blueprint generates.

#### Acceptance Criteria
2.1. WHEN the wizard collapses, THE Wizard System SHALL display the Progress Animation component within 200 milliseconds
2.2. WHEN the Progress Animation displays, THE Wizard System SHALL generate a unique assistant name
2.3. WHEN displaying progress, THE Wizard System SHALL show three sequential messages with 2-second intervals
2.4. WHEN showing each message, THE Wizard System SHALL include an icon and the assistant name
2.5. WHEN all messages complete, THE Wizard System SHALL transition to the Completion Screen within 500 milliseconds

### REQ-3: Background Blueprint Generation
**User Story:** As a system, I need to generate the blueprint while progress animation plays so it's ready when animation completes.

#### Acceptance Criteria
3.1. WHEN the wizard submits, THE Wizard System SHALL send wizard data to the AI Backend within 1 second
3.2. WHEN the AI Backend receives data, THE AI Backend SHALL generate the blueprint within 60 seconds
3.3. WHEN generating the blueprint, THE AI Backend SHALL create both text content and workflow diagram
3.4. WHEN blueprint generation completes, THE Wizard System SHALL store the blueprint data
3.5. WHEN blueprint generation fails, THE Wizard System SHALL display error message with retry option

### REQ-4: Completion Screen Display
**User Story:** As a user, I want clear action options after my blueprint is ready so I know what I can do next.

#### Acceptance Criteria
4.1. WHEN blueprint generation completes, THE Wizard System SHALL display the completion message "What do you want to do next?"
4.2. WHEN the completion message displays, THE Wizard System SHALL show four action buttons below the message
4.3. WHEN displaying buttons, THE Wizard System SHALL label them clearly: "Edit my Workflow", "Download My Blueprint", "Get a Quote for this Workflow", "Go Back"
4.4. WHEN buttons are displayed, THE Wizard System SHALL ensure minimum 44px touch targets for mobile
4.5. WHEN a button is clicked, THE Wizard System SHALL execute the corresponding action within 300 milliseconds

### REQ-5: Edit Workflow Action
**User Story:** As a user, I want to edit my workflow inputs so I can refine my blueprint.

#### Acceptance Criteria
5.1. WHEN the user clicks "Edit my Workflow", THE Wizard System SHALL hide the Completion Screen
5.2. WHEN the Completion Screen hides, THE Wizard System SHALL show the wizard form within 300 milliseconds
5.3. WHEN the wizard form displays, THE Wizard System SHALL pre-fill all fields with the user's previous inputs
5.4. WHEN fields are pre-filled, THE Wizard System SHALL allow the user to modify any field
5.5. WHEN the user resubmits, THE Wizard System SHALL repeat the entire flow with updated data

### REQ-6: Download Blueprint with Mandatory Subscription
**User Story:** As a user, I want to download my blueprint after subscribing so I can save it for reference.

#### Acceptance Criteria
6.1. WHEN the user clicks "Download My Blueprint", THE Wizard System SHALL display message "Please subscribe to download" within 200 milliseconds
6.2. WHEN the message displays, THE Wizard System SHALL show the Subscription Modal as a popup overlay
6.3. WHEN the Subscription Modal displays, THE Wizard System SHALL hide the close button (mandatory mode)
6.4. WHEN the modal is in mandatory mode, THE Wizard System SHALL prevent closing by clicking outside or pressing ESC
6.5. WHEN the user submits valid subscription details, THE Wizard System SHALL enable blueprint download and provide download link

### REQ-7: Subscription Modal Functionality
**User Story:** As a business, I want to capture user details through subscription so I can build my lead database.

#### Acceptance Criteria
7.1. WHEN the Subscription Modal displays, THE Wizard System SHALL show form fields for name and email
7.2. WHEN the user submits the form, THE Wizard System SHALL validate name (not empty) and email (valid format)
7.3. WHEN validation fails, THE Wizard System SHALL display field-specific error messages
7.4. WHEN validation passes, THE Wizard System SHALL store subscription data in WordPress database within 1 second
7.5. WHEN subscription succeeds, THE Wizard System SHALL close the modal and proceed with the requested action

### REQ-8: Get Quote Action
**User Story:** As a user, I want to request a formal quote so I can understand implementation costs.

#### Acceptance Criteria
8.1. WHEN the user clicks "Get a Quote for this Workflow", THE Wizard System SHALL display a quote request form within 300 milliseconds
8.2. WHEN the quote form displays, THE Wizard System SHALL show fields for name, email, phone (optional), and notes
8.3. WHEN the user submits the form, THE Wizard System SHALL validate required fields (name, email)
8.4. WHEN validation passes, THE Wizard System SHALL store the quote request in WordPress database
8.5. WHEN the quote is stored, THE Wizard System SHALL send email notification to admin and display confirmation message

### REQ-9: Go Back with Blog Subscription Offer
**User Story:** As a user, I want to exit gracefully and optionally subscribe to the blog.

#### Acceptance Criteria
9.1. WHEN the user clicks "Go Back", THE Wizard System SHALL display popup "Would you like to subscribe to my Blog?" within 200 milliseconds
9.2. WHEN the popup displays, THE Wizard System SHALL show two buttons: "Yes" and "No"
9.3. WHEN the user clicks "Yes", THE Wizard System SHALL display message "Thanks for trying AI" and show optional Subscription Modal
9.4. WHEN the user clicks "No", THE Wizard System SHALL display message "Thanks for trying AI!" and end the flow
9.5. WHEN the optional Subscription Modal displays, THE Wizard System SHALL show close button and allow dismissal

### REQ-10: Blueprint PDF Generation
**User Story:** As a user, I want to download a complete PDF with my blueprint so I can reference it offline.

#### Acceptance Criteria
10.1. WHEN generating the download file, THE Wizard System SHALL create a PDF containing blueprint text and diagram
10.2. WHEN creating the PDF, THE Wizard System SHALL format content with clear headings and readable typography
10.3. WHEN formatting the PDF, THE Wizard System SHALL include branding, user name, and generation date
10.4. WHEN the PDF is ready, THE Wizard System SHALL provide a download link with descriptive filename
10.5. WHEN the user clicks download, THE Wizard System SHALL serve the PDF file with appropriate headers

## Technical Constraints

- Must work with existing WPCode setup in saaslauncher theme
- Must use existing mu-plugins AI backend (OpenAI/Anthropic integration)
- Must maintain current wizard styling and UX
- Must be mobile responsive (< 768px, 768-1024px, > 1024px)
- Must handle API failures gracefully with retry options
- Must provide loading states for all async operations
- Must work in modern browsers (Chrome, Firefox, Safari, Edge)

## Success Criteria

1. Wizard collapses smoothly on submission (< 300ms)
2. Progress animation displays with personalized assistant name
3. Three progress messages display sequentially with 2s intervals
4. Blueprint generates successfully during animation
5. Completion screen displays with 4 clear, clickable buttons
6. Edit Workflow reloads wizard with all fields pre-filled
7. Download Blueprint requires mandatory subscription before download
8. Get Quote captures request and sends admin notification
9. Go Back offers optional blog subscription with Yes/No choice
10. All flows work correctly on mobile devices
11. Error handling provides clear messages and retry options
12. System handles concurrent users without conflicts

## Out of Scope

- Multi-language support
- Voice interface
- Video integration
- Payment processing (quotes are offline)
- CRM integration beyond email storage
- Advanced analytics dashboard
- Real-time chat with AI assistant
- Blueprint editing/refinement through conversation
- Multiple blueprint versions/history
- Social media sharing
- User accounts/authentication

## Assumptions

- Users have JavaScript enabled
- Users have modern browsers (ES6+ support)
- Email service is configured and working
- PDF generation library is available
- WordPress database is accessible
- AI backend (OpenAI/Anthropic) is configured with valid API keys
- Rate limiting is handled by existing infrastructure
