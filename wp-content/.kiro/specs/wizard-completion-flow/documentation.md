# Wizard Completion Flow Documentation

## 1. JavaScript Classes

### WizardController (`wizard-controller.js`)
Manages the overall wizard flow, including step navigation, validation, and coordination between components.
- **Key Methods:**
  - `setStep(step)`: Navigates to a specific step.
  - `validateCurrentStep()`: Validates inputs for the current step.
  - `collapseWizard()`: Animates the wizard form collapsing.
  - `initializeProgressAnimation(assistantName)`: Starts the progress animation.
  - `transitionToCompletionScreen(progressAnimation)`: Transitions to the completion screen.
  - `reloadWithData()`: Reloads the wizard with stored data (for "Edit Workflow").

### CompletionScreen (`completion-screen.js`)
Displays the final screen with action buttons after blueprint generation.
- **Key Methods:**
  - `show()`: Displays the screen with animation.
  - `handleEditWorkflow()`: Triggers wizard reload.
  - `handleDownloadBlueprint()`: Initiates the download flow (subscription -> download).
  - `handleGetQuote()`: Opens the quote request form.
  - `handleGoBack()`: Opens the blog subscription popup.
  - `trackEvent(eventType, metadata)`: Sends analytics events to the backend.

### ProgressAnimation (`progress-animation.js`)
Handles the loading animation with sequential messages.
- **Key Methods:**
  - `addMessage(text, icon)`: Queues a message to be displayed.
  - `show()`: Starts the animation sequence.
  - `complete(callback)`: Calls the callback when animation finishes.

### SubscriptionModal (`subscription-modal.js`)
Manages the newsletter/blueprint subscription modal.
- **Key Methods:**
  - `show(title, subtitle)`: Displays the modal.
  - `onSubmit(callback)`: Registers a callback for successful subscription.

### QuoteForm (`quote-form.js`)
Manages the quote request form modal.
- **Key Methods:**
  - `show()`: Displays the form.

### BlogPopup (`blog-popup.js`)
Manages the blog subscription popup (exit intent/go back).
- **Key Methods:**
  - `show()`: Displays the popup.

## 2. REST API Endpoints

### `POST /wp-json/mgrnz/v1/ai-workflow`
Submits the wizard data and generates the blueprint.
- **Input:** JSON with `goal`, `workflow`, `tools`, `pain_points`, `email`.
- **Output:** JSON with `success`, `session_id`, `assistant_name`, `blueprint` (HTML).

### `POST /wp-json/mgrnz/v1/subscribe-blueprint`
Subscribes a user to the blueprint (creates a record in `wp_mgrnz_blueprint_subscriptions`).
- **Input:** JSON with `session_id`, `name`, `email`, `subscription_type`.
- **Output:** JSON with `success`.

### `POST /wp-json/mgrnz/v1/download-blueprint`
Generates the PDF and returns the download URL.
- **Input:** JSON with `session_id`, `name`, `email`.
- **Output:** JSON with `success`, `download_url`.

### `POST /wp-json/mgrnz/v1/request-quote`
Submits a quote request (creates a record in `wp_mgrnz_quote_requests`).
- **Input:** JSON with `session_id`, `name`, `email`, `phone`, `notes`.
- **Output:** JSON with `success`.

### `POST /wp-json/mgrnz/v1/track-event`
Tracks arbitrary analytics events.
- **Input:** JSON with `session_id`, `event_type`, `metadata`.
- **Output:** JSON with `success`.

## 3. Database Schema

### `wp_mgrnz_blueprint_subscriptions`
Stores blueprint subscription records.
- `id` (bigint, PK, AUTO_INCREMENT)
- `name` (varchar(100))
- `email` (varchar(100))
- `subscription_type` (varchar(50))
- `blueprint_id` (bigint UNSIGNED) - Links to `ai_workflow_sub` post ID.
- `subscribed_at` (datetime)
- `download_count` (int)
- `last_download_at` (datetime)

### `wp_mgrnz_quote_requests`
Stores quote requests.
- `id` (bigint, PK, AUTO_INCREMENT)
- `blueprint_id` (bigint UNSIGNED) - Links to `ai_workflow_sub` post ID.
- `name` (varchar(100))
- `email` (varchar(100))
- `phone` (varchar(50))
- `notes` (text)
- `requested_at` (datetime)
- `status` (varchar(50)) - e.g., 'new', 'pending'.

## 4. Admin Guide

### Viewing Data
- **Subscriptions:** Currently stored in the custom table. Can be accessed via database or future admin UI.
- **Quote Requests:** Stored in custom table. Admin receives email notifications.
- **Analytics:** Events are stored in `wp_mgrnz_conversation_analytics`.

### Configuration
- **PDF Generation:** Uses `MGRNZ_PDF_Generator` class. Templates can be adjusted in the class.
- **Email Settings:** Uses `MGRNZ_Email_Service`. Ensure WP Mail is configured.
