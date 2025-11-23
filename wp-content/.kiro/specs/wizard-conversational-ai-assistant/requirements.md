# AI Workflow Wizard Enhancement - Requirements

## Introduction

This specification defines requirements for enhancing the existing 5-step AI workflow wizard with a streamlined post-submission experience. After wizard submission, the form collapses and displays progress animations, followed by blueprint presentation with clear action buttons for downloading, editing, getting quotes, and optional blog subscription.

## Glossary

- **Wizard System**: The 5-step form interface collecting goal, workflow, tools, pain points, and email
- **Blueprint**: The AI-generated workflow automation plan document containing both text and diagram
- **Progress Animation**: Visual feedback showing assistant creation and blueprint generation status
- **AI Backend**: The existing mu-plugins OpenAI/Anthropic integration service
- **Completion Screen**: The interface displaying action buttons after blueprint generation completes
- **Subscription Modal**: A popup overlay requesting user contact details before Blueprint download (mandatory for download)
- **Blog Subscription Modal**: An optional popup asking if user wants to subscribe to the blog (optional)
- **Action Buttons**: Four buttons presented after completion: Edit Workflow, Download Blueprint, Get Quote, Go Back
- **Workflow Diagram**: A visual flowchart representation showing high-level automation steps and connections
- **Downloadable File**: A PDF or document format containing both text and diagram Blueprint content

## Current State
- 5-step wizard collecting: goal, workflow, tools, pain points, email
- Real blueprint generation via OpenAI/Anthropic API
- Basic subscribe/consult buttons
- Working in WPCode on saaslauncher theme

## Desired User Journey

### Phase 1: Wizard Data Collection
User completes 5-step wizard → Clicks "Build my AI workflow"

### Phase 2: Progress Animation
Wizard collapses → Progress animation begins with sequential messages:
1. "Your Assistant [NAME] has been created..."
2. "Your Assistant [NAME] has deployed a business analysis agent..."
3. "Building Chat..."
4. Blueprint generation occurs in background

### Phase 3: Completion Screen with Action Buttons
Blueprint generation completes → Display completion message: "What do you want to do next?"

Four action buttons appear:
1. **Button 1: Edit my Workflow** - Reloads wizard with current data
2. **Button 2: Download My Blueprint** - Triggers mandatory subscription modal, then enables download
3. **Button 3: Get a Quote for this Workflow** - Opens quote request flow
4. **Button 4: Go Back** - Triggers optional blog subscription offer

### Phase 4: Button Actions

**If Button 2 (Download) chosen:**
- Display message: "Please subscribe to download"
- Show subscription modal (mandatory)
- User must submit name and email
- Download enabled after successful subscription

**If Button 4 (Go Back) chosen:**
- Show popup question: "Would you like to subscribe to my Blog?"
- Two options: Yes or No
- **If Yes:** Display "Thanks for trying AI" → Show subscription modal (optional, can close)
- **If No:** Display "Thanks for trying AI!" → End flow

## Requirements

### REQ-1: Wizard Functionality
**User Story:** As a user, I want the wizard to work smoothly so I can complete all 5 steps without issues.

#### Acceptance Criteria
1.1. WHEN the user clicks the Next button, THE Wizard System SHALL advance to the next step within 200 milliseconds
1.2. WHEN the user clicks the Back button, THE Wizard System SHALL return to the previous step within 200 milliseconds
1.3. WHEN the user submits a form with incomplete required fields, THE Wizard System SHALL display field-specific validation error messages
1.4. WHEN the user completes step 5 data entry, THE Wizard System SHALL display the submit button
1.5. WHEN the user clicks the submit button, THE Wizard System SHALL initiate the AI Assistant creation flow

### REQ-2: Progress Animation
**User Story:** As a user, I want to see progress feedback so I understand the blueprint is being generated.

#### Acceptance Criteria
2.1. WHEN the Wizard System receives submit confirmation, THE Wizard System SHALL collapse the wizard form within 300 milliseconds
2.2. WHEN the wizard form collapses, THE Wizard System SHALL display the Progress Animation component in its place
2.3. WHEN the Progress Animation displays, THE Wizard System SHALL show sequential status messages with 2-second intervals between messages
2.4. WHEN displaying progress messages, THE Wizard System SHALL assign a unique assistant name and display it in messages
2.5. WHEN blueprint generation completes, THE Wizard System SHALL transition to the Completion Screen within 500 milliseconds

### REQ-3: Completion Screen with Action Buttons
**User Story:** As a user, I want clear action options after my blueprint is generated so I can decide what to do next.

#### Acceptance Criteria
3.1. WHEN the blueprint generation completes, THE Wizard System SHALL display the completion message "What do you want to do next?" within 200 milliseconds
3.2. WHEN the completion message displays, THE Wizard System SHALL show four action buttons with clear labels
3.3. WHEN displaying action buttons, THE Wizard System SHALL style them consistently with minimum 44px touch targets
3.4. WHEN the user clicks any action button, THE Wizard System SHALL execute the corresponding action within 300 milliseconds
3.5. WHEN action buttons are displayed, THE Wizard System SHALL ensure they are visible and accessible on all device sizes

### REQ-4: Blueprint Generation
**User Story:** As a system, I need to generate the blueprint during the progress animation so it's ready when animation completes.

#### Acceptance Criteria
4.1. WHEN the Wizard System submits, THE Wizard System SHALL send wizard data to the AI Backend within 1 second
4.2. WHEN the AI Backend receives wizard data, THE AI Backend SHALL generate the Blueprint within 60 seconds
4.3. WHEN the AI Backend generates the Blueprint, THE AI Backend SHALL include both text content and workflow diagram
4.4. WHEN blueprint generation fails, THE Wizard System SHALL display an error message and offer to retry
4.5. WHEN blueprint generation completes successfully, THE Wizard System SHALL store the blueprint and transition to Completion Screen

### REQ-5: Edit Workflow Action (Button 1)
**User Story:** As a user, I want to edit my workflow inputs so I can refine my blueprint.

#### Acceptance Criteria
5.1. WHEN the user clicks "Edit my Workflow" button, THE Wizard System SHALL reload the wizard form within 500 milliseconds
5.2. WHEN the wizard reloads, THE Wizard System SHALL pre-fill all form fields with the user's previous inputs
5.3. WHEN the wizard is pre-filled, THE Wizard System SHALL allow the user to modify any field
5.4. WHEN the user submits the edited wizard, THE Wizard System SHALL repeat the progress animation and blueprint generation flow
5.5. WHEN the new blueprint generates, THE Wizard System SHALL replace the previous blueprint with the updated version

### REQ-6: Download Blueprint Action (Button 2)
**User Story:** As a user, I want to download my blueprint after subscribing so I can save it for later.

#### Acceptance Criteria
6.1. WHEN the user clicks "Download My Blueprint" button, THE Wizard System SHALL display the message "Please subscribe to download" within 200 milliseconds
6.2. WHEN the subscribe message displays, THE Wizard System SHALL show the Subscription Modal as a popup overlay
6.3. WHEN the Subscription Modal displays, THE Wizard System SHALL request user name and email address with validation
6.4. WHEN the user submits valid subscription details, THE Wizard System SHALL store the details in WordPress database within 1 second
6.5. WHEN subscription is successful, THE Wizard System SHALL enable blueprint download and provide download link within 1 second

### REQ-7: Get Quote Action (Button 3)
**User Story:** As a user, I want to request a formal quote so I can understand implementation costs.

#### Acceptance Criteria
7.1. WHEN the user clicks "Get a Quote for this Workflow" button, THE Wizard System SHALL open a quote request form within 300 milliseconds
7.2. WHEN the quote form displays, THE Wizard System SHALL request user contact details and any additional notes
7.3. WHEN the user submits the quote request, THE Wizard System SHALL store the request in WordPress database
7.4. WHEN the quote request is stored, THE Wizard System SHALL send email notification to admin within 2 seconds
7.5. WHEN the quote submission succeeds, THE Wizard System SHALL display confirmation message with 24-hour timeline

### REQ-8: Go Back Action with Optional Blog Subscription (Button 4)
**User Story:** As a user, I want to exit the wizard flow and optionally subscribe to the blog.

#### Acceptance Criteria
8.1. WHEN the user clicks "Go Back" button, THE Wizard System SHALL display a popup question "Would you like to subscribe to my Blog?" within 200 milliseconds
8.2. WHEN the blog subscription popup displays, THE Wizard System SHALL show two options: "Yes" and "No"
8.3. WHEN the user clicks "Yes", THE Wizard System SHALL display message "Thanks for trying AI" and show the Subscription Modal as optional (can be closed)
8.4. WHEN the user clicks "No", THE Wizard System SHALL display message "Thanks for trying AI!" and end the flow
8.5. WHEN the user closes the optional subscription modal, THE Wizard System SHALL end the flow gracefully

### REQ-9: Subscription Modal Management
**User Story:** As a business, I want to capture user contact details through subscription modals so I can build my lead database.

#### Acceptance Criteria
9.1. WHEN displaying a mandatory subscription modal, THE Wizard System SHALL prevent closing until form is submitted
9.2. WHEN displaying an optional subscription modal, THE Wizard System SHALL allow closing via X button or clicking outside
9.3. WHEN the subscription form is submitted, THE Wizard System SHALL validate name and email fields
9.4. WHEN validation fails, THE Wizard System SHALL display field-specific error messages
9.5. WHEN subscription succeeds, THE Wizard System SHALL store user details with subscription type (blueprint download or blog) in WordPress database

### REQ-10: Blueprint File Generation
**User Story:** As a user, I want to download a complete blueprint file with diagram and text so I can reference it offline.

#### Acceptance Criteria
10.1. WHEN the AI Backend generates the Blueprint, THE AI Backend SHALL create a high-level workflow diagram in addition to text content
10.2. WHEN creating the diagram, THE AI Backend SHALL generate a flowchart showing workflow steps similar to make.com or n8n visual flows
10.3. WHEN generating the download file, THE Wizard System SHALL create a PDF containing both diagram and formatted text
10.4. WHEN the PDF is generated, THE Wizard System SHALL include branding, user name, and generation date
10.5. WHEN the user downloads the Blueprint, THE Wizard System SHALL serve the PDF file with appropriate filename

## Technical Constraints

- Must work with existing WPCode setup in saaslauncher theme
- Must use existing mu-plugins AI backend (OpenAI/Anthropic integration)
- Must maintain current wizard styling and UX
- Must be mobile responsive
- Must handle API failures gracefully
- Must provide loading states for all async operations

## Success Criteria

1. Wizard completes all 5 steps without errors
2. Wizard collapses smoothly on submission
3. Progress animation displays with personalized messages
4. Blueprint generates successfully during animation
5. Completion screen displays with 4 clear action buttons
6. Download button requires mandatory subscription
7. Go Back button offers optional blog subscription
8. Edit Workflow reloads wizard with pre-filled data
9. Get Quote captures request and sends notification
10. System handles errors gracefully with retry options

## Out of Scope

- Multi-language support
- Voice interface
- Video integration
- Payment processing (quotes are offline)
- CRM integration
- Analytics dashboard
