# Requirements Document

## Introduction

This document specifies the requirements for completing the AI Workflow Wizard system on the MGRNZ website. The wizard currently collects user workflow information through a 5-step form but lacks the backend processing, AI integration, and blueprint generation capabilities. This feature will transform the wizard from a data collection tool into a fully functional AI-powered workflow analysis and recommendation system.

## Glossary

- **Wizard System**: The complete multi-step form interface that collects user workflow information
- **Blueprint**: An AI-generated document that provides a customized workflow recommendation with specific steps and tool suggestions
- **REST Endpoint**: The WordPress REST API route at `/wp-json/mgrnz/v1/ai-workflow` that receives form submissions
- **AI Service**: An external AI API (such as OpenAI, Anthropic, or similar) that processes user input and generates workflow recommendations
- **Submission Record**: A WordPress custom post type that stores wizard submission data persistently
- **Email Service**: The system component that sends blueprint results and follow-up communications to users

## Requirements

### Requirement 1

**User Story:** As a website visitor, I want the wizard form to work smoothly with proper navigation and validation, so that I can easily provide my workflow information.

#### Acceptance Criteria

1. WHEN a user clicks the "Next" button, THE Wizard System SHALL validate the current step's required fields before advancing
2. WHEN a user clicks the "Back" button, THE Wizard System SHALL navigate to the previous step without losing entered data
3. WHEN a user reaches step 5, THE Wizard System SHALL display a summary of all previously entered information
4. WHEN a user submits incomplete required fields, THE Wizard System SHALL display an error message indicating which fields need completion
5. WHEN a user progresses through steps, THE Wizard System SHALL update the progress bar to reflect the current step percentage

### Requirement 2

**User Story:** As a website visitor, I want to receive an AI-generated workflow blueprint based on my inputs, so that I can understand how to implement AI in my specific situation.

#### Acceptance Criteria

1. WHEN a user submits the wizard form, THE REST Endpoint SHALL send the collected data to the AI Service within 2 seconds
2. WHEN the AI Service receives workflow data, THE REST Endpoint SHALL generate a structured blueprint containing goal analysis, workflow assessment, tool recommendations, and implementation steps
3. WHEN the AI Service returns a blueprint, THE Wizard System SHALL display the formatted blueprint to the user within 10 seconds of submission
4. IF the AI Service fails to respond within 30 seconds, THEN THE REST Endpoint SHALL return a timeout error message to the user
5. WHEN a blueprint is generated, THE Wizard System SHALL format the markdown content with proper headings, lists, and styling

### Requirement 3

**User Story:** As a website administrator, I want all wizard submissions to be stored in WordPress, so that I can review user needs and follow up with potential clients.

#### Acceptance Criteria

1. WHEN a user submits the wizard form, THE REST Endpoint SHALL create a Submission Record in the WordPress database
2. WHEN creating a Submission Record, THE REST Endpoint SHALL store the user's goal, workflow description, tools, pain points, email, submission timestamp, and generated blueprint
3. WHEN a Submission Record is created, THE REST Endpoint SHALL assign it a unique identifier for future reference
4. WHEN viewing Submission Records in WordPress admin, THE Wizard System SHALL display submissions in a searchable and filterable list
5. WHEN an administrator views a Submission Record, THE Wizard System SHALL display all collected data and the generated blueprint in a readable format

### Requirement 4

**User Story:** As a website visitor who provided my email, I want to receive the blueprint via email, so that I can reference it later and take action on the recommendations.

#### Acceptance Criteria

1. WHEN a user provides an email address and submits the wizard, THE Email Service SHALL send a copy of the blueprint to the provided email address within 60 seconds
2. WHEN sending a blueprint email, THE Email Service SHALL format the content with proper HTML styling consistent with the website's brand
3. WHEN a blueprint email is sent, THE Email Service SHALL include a clear subject line referencing the AI workflow blueprint
4. IF email delivery fails, THEN THE REST Endpoint SHALL log the error but still display the blueprint to the user on the website
5. WHEN a user clicks "Subscribe & get updates" after viewing their blueprint, THE Email Service SHALL add the email address to the newsletter subscription list

### Requirement 5

**User Story:** As a website administrator, I want the AI integration to be configurable and secure, so that I can manage API credentials and costs without exposing sensitive information.

#### Acceptance Criteria

1. WHEN configuring the AI Service, THE Wizard System SHALL store API credentials in WordPress options encrypted or as environment variables
2. WHEN the REST Endpoint calls the AI Service, THE Wizard System SHALL include proper authentication headers and error handling
3. WHEN API rate limits are exceeded, THE REST Endpoint SHALL return a user-friendly error message and log the incident
4. WHERE the AI Service is configured, THE Wizard System SHALL allow administrators to select between different AI providers through WordPress settings
5. WHEN processing user data, THE REST Endpoint SHALL sanitize and validate all inputs before sending to the AI Service

### Requirement 6

**User Story:** As a website visitor, I want clear feedback during the submission process, so that I know the system is working and when my blueprint is ready.

#### Acceptance Criteria

1. WHEN a user clicks "Build my AI workflow", THE Wizard System SHALL display a loading indicator with status text
2. WHILE the blueprint is being generated, THE Wizard System SHALL disable the submit button to prevent duplicate submissions
3. WHEN the blueprint generation is complete, THE Wizard System SHALL hide the form and display the blueprint section with a smooth transition
4. IF an error occurs during submission, THEN THE Wizard System SHALL display a specific error message and allow the user to retry
5. WHEN a user clicks "Book an AI consult" after viewing their blueprint, THE Wizard System SHALL navigate to the consultation booking page with the submission ID as a URL parameter

### Requirement 7

**User Story:** As a website administrator, I want the wizard JavaScript to be properly version controlled and maintainable, so that I can update and debug the code efficiently.

#### Acceptance Criteria

1. THE Wizard System SHALL store all JavaScript code in the theme's assets directory as separate files
2. WHEN the wizard page loads, THE Wizard System SHALL enqueue the JavaScript file with proper WordPress dependencies
3. WHEN updating wizard functionality, THE Wizard System SHALL allow modifications through the file system rather than database-stored snippets
4. THE Wizard System SHALL include inline code comments explaining key functionality and event handlers
5. WHEN JavaScript errors occur, THE Wizard System SHALL log errors to the browser console with descriptive messages
