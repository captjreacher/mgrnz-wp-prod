<?php
/**
 * AI Service Integration Class
 * 
 * Handles communication with external AI APIs (OpenAI, Anthropic)
 * to generate workflow blueprints based on user input.
 *
 * @package MGRNZ_AI_Workflow
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/class-diagram-generator.php';

class MGRNZ_AI_Service {
    
    /**
     * AI provider API key
     * @var string
     */
    private $api_key;
    
    /**
     * AI provider (openai or anthropic)
     * @var string
     */
    private $provider;
    
    /**
     * AI model to use
     * @var string
     */
    private $model;
    
    /**
     * Maximum tokens for response
     * @var int
     */
    private $max_tokens;
    
    /**
     * Temperature for AI generation
     * @var float
     */
    private $temperature;
    
    /**
     * Request timeout in seconds
     * @var int
     */
    private $timeout;
    
    /**
     * Maximum retry attempts
     * @var int
     */
    private $max_retries;
    
    /**
     * Constructor - loads API credentials and configuration
     */
    public function __construct() {
        // Load from environment variables first, then WordPress options
        $this->provider = getenv('MGRNZ_AI_PROVIDER') ?: get_option('mgrnz_ai_provider', 'openai');
        $this->api_key = getenv('MGRNZ_AI_API_KEY') ?: get_option('mgrnz_ai_api_key', '');
        $this->model = getenv('MGRNZ_AI_MODEL') ?: get_option('mgrnz_ai_model', $this->get_default_model());
        $this->max_tokens = (int) (getenv('MGRNZ_AI_MAX_TOKENS') ?: get_option('mgrnz_ai_max_tokens', 2000));
        $this->temperature = (float) (getenv('MGRNZ_AI_TEMPERATURE') ?: get_option('mgrnz_ai_temperature', 0.7));
        $this->timeout = 60;
        $this->max_retries = 2;
    }
    
    /**
     * Get default model based on provider
     * 
     * @return string
     */
    private function get_default_model() {
        return $this->provider === 'anthropic' ? 'claude-3-5-sonnet-20241022' : 'gpt-4o-mini';
    }
    
    /**
     * Generate blueprint from workflow data
     * 
     * @param array $workflow_data User workflow information
     * @return array Blueprint data with summary and content
     * @throws Exception If generation fails
     */
    public function generate_blueprint($workflow_data) {
        // Validate API key
        if (empty($this->api_key)) {
            $this->handle_blueprint_failure('AI API key not configured', $workflow_data);
            throw new Exception('AI API key not configured');
        }
        
        // Build the prompt
        $prompt = $this->build_prompt($workflow_data);
        
        // Call appropriate AI service with retry logic
        $retries = 0;
        $last_error = null;
        
        while ($retries < $this->max_retries) {
            try {
                if ($this->provider === 'anthropic') {
                    $raw_response = $this->call_anthropic_api($prompt);
                } else {
                    $raw_response = $this->call_openai_api($prompt);
                }
                
                // Parse and return the response
                return $this->parse_response($raw_response);
                
            } catch (Exception $e) {
                $last_error = $e;
                $retries++;
                
                // Log each retry attempt
                error_log(sprintf(
                    '[AI Service] Blueprint generation attempt %d/%d failed: %s',
                    $retries,
                    $this->max_retries,
                    $e->getMessage()
                ));
                
                // Only retry on transient failures (timeout, 5xx errors)
                if (!$this->is_retryable_error($e)) {
                    break;
                }
                
                // Wait before retry (exponential backoff)
                if ($retries < $this->max_retries) {
                    sleep(pow(2, $retries));
                }
            }
        }
        
        // All retries failed - handle the failure
        $this->handle_blueprint_failure($last_error->getMessage(), $workflow_data, $last_error);
        
        // Throw the error to be caught by the endpoint
        throw $last_error;
    }
    
    /**
     * Handle blueprint generation failure
     * 
     * @param string $error_message Error message
     * @param array $workflow_data User workflow data
     * @param Exception|null $exception Optional exception object
     * @return void
     */
    private function handle_blueprint_failure($error_message, $workflow_data, $exception = null) {
        // Log detailed error information
        error_log(sprintf(
            '[AI Service] Blueprint generation failed | Provider: %s | Model: %s | Error: %s | Time: %s',
            $this->provider,
            $this->model,
            $error_message,
            current_time('mysql')
        ));
        
        // Store failed request for manual processing
        $failed_request_id = wp_insert_post([
            'post_type' => 'ai_workflow_failed',
            'post_title' => 'Failed Blueprint - ' . substr($workflow_data['goal'] ?? 'Unknown', 0, 50),
            'post_status' => 'publish',
            'post_content' => $error_message
        ]);
        
        if (!is_wp_error($failed_request_id)) {
            // Save workflow data for manual review
            update_post_meta($failed_request_id, '_mgrnz_failed_goal', $workflow_data['goal'] ?? '');
            update_post_meta($failed_request_id, '_mgrnz_failed_workflow', $workflow_data['workflow_description'] ?? '');
            update_post_meta($failed_request_id, '_mgrnz_failed_tools', $workflow_data['tools'] ?? '');
            update_post_meta($failed_request_id, '_mgrnz_failed_pain_points', $workflow_data['pain_points'] ?? '');
            update_post_meta($failed_request_id, '_mgrnz_failed_email', $workflow_data['email'] ?? '');
            update_post_meta($failed_request_id, '_mgrnz_failed_error', $error_message);
            update_post_meta($failed_request_id, '_mgrnz_failed_provider', $this->provider);
            update_post_meta($failed_request_id, '_mgrnz_failed_model', $this->model);
            update_post_meta($failed_request_id, '_mgrnz_failed_at', current_time('mysql'));
            
            if ($exception) {
                update_post_meta($failed_request_id, '_mgrnz_failed_trace', $exception->getTraceAsString());
            }
        }
        
        // Send notification to admin email
        $this->send_failure_notification($workflow_data, $error_message, $failed_request_id);
    }
    
    /**
     * Send failure notification to admin
     * 
     * @param array $workflow_data User workflow data
     * @param string $error_message Error message
     * @param int|WP_Error $failed_request_id Failed request post ID
     * @return void
     */
    private function send_failure_notification($workflow_data, $error_message, $failed_request_id) {
        $admin_email = get_option('admin_email');
        
        if (empty($admin_email)) {
            return;
        }
        
        $subject = '[AI Workflow] Blueprint Generation Failed';
        
        $message = "A blueprint generation request has failed and requires manual review.\n\n";
        $message .= "ERROR DETAILS:\n";
        $message .= "Error: {$error_message}\n";
        $message .= "Provider: {$this->provider}\n";
        $message .= "Model: {$this->model}\n";
        $message .= "Time: " . current_time('mysql') . "\n\n";
        
        $message .= "USER INFORMATION:\n";
        $message .= "Goal: " . ($workflow_data['goal'] ?? 'N/A') . "\n";
        $message .= "Email: " . ($workflow_data['email'] ?? 'N/A') . "\n\n";
        
        $message .= "WORKFLOW DETAILS:\n";
        $message .= "Workflow: " . substr($workflow_data['workflow_description'] ?? 'N/A', 0, 200) . "...\n";
        $message .= "Tools: " . ($workflow_data['tools'] ?? 'N/A') . "\n";
        $message .= "Pain Points: " . substr($workflow_data['pain_points'] ?? 'N/A', 0, 200) . "...\n\n";
        
        if (!is_wp_error($failed_request_id)) {
            $message .= "View in dashboard: " . admin_url('post.php?post=' . $failed_request_id . '&action=edit') . "\n";
        }
        
        $message .= "\nPlease review and process this request manually.";
        
        // Send email
        wp_mail($admin_email, $subject, $message);
    }

    
    /**
     * Build structured prompt from workflow data
     * 
     * @param array $workflow_data User workflow information
     * @return string Formatted prompt
     */
    private function build_prompt($workflow_data) {
        $goal = $workflow_data['goal'] ?? '';
        $workflow = $workflow_data['workflow_description'] ?? '';
        $tools = $workflow_data['tools'] ?? '';
        $pain_points = $workflow_data['pain_points'] ?? '';
        $conversation_history = $workflow_data['conversation_history'] ?? '';
        
        // Load template configuration
        $template_path = __DIR__ . '/blueprint-template.php';
        $template = file_exists($template_path) ? include $template_path : $this->get_default_template();
        
        // Load training documentation
        $training_path = __DIR__ . '/ai-training-docs.php';
        $training = file_exists($training_path) ? include $training_path : [];
        
        // Build prompt from template
        $prompt = $template['system_role'] . "\n\n";
        
        // Add company context if available
        if (!empty($training['company_context'])) {
            $prompt .= "COMPANY CONTEXT:\n";
            $prompt .= trim($training['company_context']) . "\n\n";
        }
        
        $prompt .= "USER CONTEXT:\n";
        $prompt .= "- Goal: {$goal}\n";
        $prompt .= "- Current Workflow: {$workflow}\n";
        $prompt .= "- Current Tools: {$tools}\n";
        $prompt .= "- Pain Points: {$pain_points}\n";
        
        // Include conversation history if available
        if (!empty($conversation_history)) {
            $prompt .= "\n{$conversation_history}\n";
            $prompt .= "IMPORTANT: Use the conversation history above to personalize this blueprint. The user has provided specific details about their workflow - incorporate these details throughout the blueprint instead of using generic examples.\n\n";
        } else {
            $prompt .= "\n";
        }
        
        // Add pricing context if available
        if (!empty($training['pricing_context'])) {
            $prompt .= "PRICING GUIDELINES:\n";
            $prompt .= trim($training['pricing_context']) . "\n\n";
        }
        
        // Add technical guidelines if available
        if (!empty($training['technical_guidelines'])) {
            $prompt .= "TECHNICAL BEST PRACTICES:\n";
            $prompt .= trim($training['technical_guidelines']) . "\n\n";
        }
        
        // Add relevant use case examples if available
        if (!empty($training['use_case_examples'])) {
            $prompt .= "REFERENCE EXAMPLES (for context, not to copy directly):\n";
            foreach (array_slice($training['use_case_examples'], 0, 2) as $example) {
                $prompt .= "- {$example['scenario']}: {$example['solution']} (Saved {$example['time_saved']}, ROI in {$example['roi_months']} months)\n";
            }
            $prompt .= "\n";
        }
        
        // Add technology stack
        $prompt .= "PREFERRED TECHNOLOGY STACK (Use these defaults unless user needs dictate otherwise):\n";
        foreach ($template['tech_stack'] as $category => $tool) {
            $prompt .= "- {$category}: {$tool}\n";
        }
        $prompt .= "\n";
        
        // Add blueprint structure
        $prompt .= "Please generate a professional Blueprint following the D.R.I.V.E.™ Framework:\n\n";
        
        foreach ($template['blueprint_structure'] as $section) {
            $prompt .= "{$section['number']}. {$section['title']}\n";
            foreach ($section['guidelines'] as $guideline) {
                $prompt .= "   - {$guideline}\n";
            }
            $prompt .= "\n";
        }
        
        // Add output formatting instructions
        $format = $template['output_format'];
        $prompt .= "Format the response in {$format['format']}. ";
        $prompt .= "Use a {$format['tone']} tone with {$format['style']} explanations.";
        
        // Add additional instructions
        if (!empty($template['additional_instructions'])) {
            $prompt .= "\n\nAdditional Guidelines:\n";
            foreach ($template['additional_instructions'] as $instruction) {
                $prompt .= "- {$instruction}\n";
            }
        }
        
        // Add common pitfalls to avoid
        if (!empty($training['pitfalls_to_mention'])) {
            $prompt .= "\n\nCommon Pitfalls to Address:\n";
            $count = 0;
            foreach ($training['pitfalls_to_mention'] as $pitfall => $explanation) {
                $prompt .= "- {$pitfall}: {$explanation}\n";
                if (++$count >= 3) break; // Limit to top 3 to keep prompt concise
            }
        }
        
        // Add additional notes from training
        if (!empty($training['additional_notes'])) {
            $prompt .= "\n\nIMPORTANT REMINDERS:\n";
            $prompt .= trim($training['additional_notes']) . "\n";
        }
        
        return $prompt;
    }
    
    /**
     * Get default template if template file doesn't exist
     * 
     * @return array Default template configuration
     */
    private function get_default_template() {
        return [
            'system_role' => "You are a Senior AI Workflow Architect at MGRNZ. Your goal is to design a professional, automated solution using the MGRNZ D.R.I.V.E.™ Consulting Framework.",
            'tech_stack' => [
                'AI Engine' => 'OpenAI / ChatGPT',
                'Orchestration' => 'Make.com (for all automation flows)',
                'Database' => 'Supabase (for structured data storage)',
                'Email/Marketing' => 'MailerLite',
                'Productivity' => 'Google Workspace or Outlook',
                'Version Control' => 'GitHub',
                'Platform' => 'WordPress',
            ],
            'blueprint_structure' => [
                ['number' => '1', 'title' => 'EXECUTIVE SUMMARY', 'guidelines' => ['Brief overview of the transformation.']],
                ['number' => '2', 'title' => 'DISCOVER (Analysis)', 'guidelines' => ['Identify the core need and opportunity.', 'Analyze the current state vs. future state.']],
                ['number' => '3', 'title' => 'READY (Preparation)', 'guidelines' => ['Detailed design requirements.', 'Data preparation and dependencies.']],
                ['number' => '4', 'title' => 'IMPLEMENT (Execution)', 'guidelines' => ['The Core Build: Explain how Make.com, OpenAI, and Supabase work together.', 'Integration steps.']],
                ['number' => '5', 'title' => 'VALIDATE (Quality Assurance)', 'guidelines' => ['User Acceptance Testing (UAT) criteria.', 'Documentation and training needs.']],
                ['number' => '6', 'title' => 'EVOLVE (Optimization)', 'guidelines' => ['The Level Up Engine: How to monitor and improve over time.', 'Future scaling opportunities.']],
            ],
            'output_format' => [
                'format' => 'professional Markdown with clear headers',
                'tone' => 'professional, consultative',
                'style' => 'detailed yet accessible',
            ],
            'additional_instructions' => [
                'Use specific details from the conversation history when available.',
                'Avoid generic examples - personalize based on user\'s actual workflow.',
            ],
        ];
    }
    
    /**
     * Call OpenAI API
     * 
     * @param string $prompt The prompt to send
     * @return array Raw API response
     * @throws Exception On API error
     */
    private function call_openai_api($prompt) {
        $url = 'https://api.openai.com/v1/chat/completions';
        
        $body = json_encode([
            'model' => $this->model,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'max_tokens' => $this->max_tokens,
            'temperature' => $this->temperature
        ]);
        
        $args = [
            'method' => 'POST',
            'timeout' => $this->timeout,
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->api_key
            ],
            'body' => $body
        ];
        
        $response = wp_remote_post($url, $args);
        
        if (is_wp_error($response)) {
            $this->handle_api_error($response->get_error_message(), 'openai');
        }
        
        $status_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if ($status_code !== 200) {
            $error_message = $data['error']['message'] ?? 'Unknown error';
            $this->handle_api_error($error_message, 'openai', $status_code);
        }
        
        return $data;
    }
    
    /**
     * Call Anthropic API
     * 
     * @param string $prompt The prompt to send
     * @return array Raw API response
     * @throws Exception On API error
     */
    private function call_anthropic_api($prompt) {
        $url = 'https://api.anthropic.com/v1/messages';
        
        $body = json_encode([
            'model' => $this->model,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'max_tokens' => $this->max_tokens,
            'temperature' => $this->temperature
        ]);
        
        $args = [
            'method' => 'POST',
            'timeout' => $this->timeout,
            'headers' => [
                'Content-Type' => 'application/json',
                'x-api-key' => $this->api_key,
                'anthropic-version' => '2023-06-01'
            ],
            'body' => $body
        ];
        
        $response = wp_remote_post($url, $args);
        
        if (is_wp_error($response)) {
            $this->handle_api_error($response->get_error_message(), 'anthropic');
        }
        
        $status_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if ($status_code !== 200) {
            $error_message = $data['error']['message'] ?? 'Unknown error';
            $this->handle_api_error($error_message, 'anthropic', $status_code);
        }
        
        return $data;
    }

    
    /**
     * Parse AI response and extract blueprint
     * 
     * @param array $raw_response Raw API response
     * @return array Formatted blueprint with summary and content
     */
    private function parse_response($raw_response) {
        $content = '';
        $tokens_used = 0;
        
        // Extract content based on provider
        if ($this->provider === 'anthropic') {
            if (isset($raw_response['content'][0]['text'])) {
                $content = $raw_response['content'][0]['text'];
            }
            $tokens_used = $raw_response['usage']['output_tokens'] ?? 0;
        } else {
            // OpenAI
            if (isset($raw_response['choices'][0]['message']['content'])) {
                $content = $raw_response['choices'][0]['message']['content'];
            }
            $tokens_used = $raw_response['usage']['completion_tokens'] ?? 0;
        }
        
        if (empty($content)) {
            throw new Exception('Empty response from AI service');
        }
        
        // Clean markdown code fences from AI response
        $content = $this->clean_markdown_fences($content);
        
        // Extract summary (first paragraph or first 200 chars)
        $summary = $this->extract_summary($content);
        
        // Generate diagram from blueprint content
        $diagram_generator = new MGRNZ_Diagram_Generator();
        $diagram_data = $diagram_generator->generate_from_blueprint($content);
        
        return [
            'summary' => $summary,
            'content' => $content,
            'generated_at' => current_time('mysql'),
            'ai_model' => $this->model,
            'tokens_used' => $tokens_used,
            'diagram' => $diagram_data
        ];
    }
    
    /**
     * Clean markdown code fences from AI response
     * 
     * @param string $content Content with potential markdown fences
     * @return string Cleaned content
     */
    private function clean_markdown_fences($content) {
        // Remove ```html or """html at the start (with or without backticks/quotes)
        $content = preg_replace('/^[`"]{3}html\s*/i', '', $content);
        $content = preg_replace('/^[`"]{2}html\s*/i', '', $content);
        
        // Remove ``` or """ at the end
        $content = preg_replace('/\s*[`"]{3}\s*$/m', '', $content);
        $content = preg_replace('/\s*[`"]{2}\s*$/m', '', $content);
        
        // Remove any remaining standalone ``` or """ lines
        $content = preg_replace('/^[`"]{3}\s*$/m', '', $content);
        $content = preg_replace('/^[`"]{2}\s*$/m', '', $content);
        
        // Remove any remaining ``` or """ markers
        $content = str_replace(['```', '"""', '``', '""'], '', $content);
        
        return trim($content);
    }
    
    /**
     * Extract summary from blueprint content
     * 
     * @param string $content Full blueprint content
     * @return string Summary text
     */
    private function extract_summary($content) {
        // Remove markdown headers
        $text = preg_replace('/^#+\s+/m', '', $content);
        
        // Get first paragraph
        $paragraphs = preg_split('/\n\n+/', trim($text));
        $first_paragraph = $paragraphs[0] ?? '';
        
        // Limit to 200 characters
        if (strlen($first_paragraph) > 200) {
            $first_paragraph = substr($first_paragraph, 0, 197) . '...';
        }
        
        return $first_paragraph;
    }
    
    /**
     * Handle API errors with logging and user-friendly messages
     * 
     * @param string $error_message Error message from API
     * @param string $provider AI provider name
     * @param int $status_code HTTP status code
     * @throws Exception Always throws with user-friendly message
     */
    private function handle_api_error($error_message, $provider, $status_code = 0) {
        // Log the detailed error
        error_log(sprintf(
            '[AI WORKFLOW ERROR] Provider: %s | Status: %d | Error: %s | Time: %s',
            $provider,
            $status_code,
            $error_message,
            current_time('mysql')
        ));
        
        // Determine user-friendly message based on error type
        $user_message = 'Unable to generate blueprint. Please try again.';
        
        if ($status_code === 401 || $status_code === 403) {
            $user_message = 'AI service authentication failed. Please contact support.';
        } elseif ($status_code === 429) {
            $user_message = 'AI service rate limit reached. Please try again in a few minutes.';
        } elseif ($status_code >= 500) {
            $user_message = 'AI service is temporarily unavailable. Please try again later.';
        } elseif (strpos($error_message, 'timeout') !== false || strpos($error_message, 'timed out') !== false) {
            $user_message = 'Request timed out. Please try again.';
        }
        
        throw new Exception($user_message, $status_code);
    }
    
    /**
     * Check if error is retryable
     * 
     * @param Exception $error The error to check
     * @return bool True if error is retryable
     */
    private function is_retryable_error($error) {
        $code = $error->getCode();
        $message = $error->getMessage();
        
        // Retry on timeout errors
        if (strpos($message, 'timeout') !== false || strpos($message, 'timed out') !== false) {
            return true;
        }
        
        // Retry on 5xx server errors
        if ($code >= 500 && $code < 600) {
            return true;
        }
        
        // Retry on 429 rate limit (with backoff)
        if ($code === 429) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Generate cost estimate based on workflow complexity
     * 
     * @param array $workflow_data User workflow information
     * @return array Estimate with setup cost, monthly cost, timeline, and disclaimer
     * @throws Exception If generation fails
     */
    public function generate_cost_estimate($workflow_data) {
        // Validate API key
        if (empty($this->api_key)) {
            throw new Exception('AI API key not configured');
        }
        
        // Build the estimate prompt
        $prompt = $this->build_estimate_prompt($workflow_data);
        
        try {
            // Call AI service
            if ($this->provider === 'anthropic') {
                $raw_response = $this->call_anthropic_api($prompt);
                $content = $raw_response['content'][0]['text'] ?? '';
            } else {
                $raw_response = $this->call_openai_api($prompt);
                $content = $raw_response['choices'][0]['message']['content'] ?? '';
            }
            
            if (empty($content)) {
                throw new Exception('Empty response from AI service');
            }
            
            // Parse the estimate from the response
            return $this->parse_estimate_response($content);
            
        } catch (Exception $e) {
            error_log('[AI Service] Cost estimate generation failed: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Build prompt for cost estimate generation
     * 
     * @param array $workflow_data User workflow information
     * @return string Formatted prompt
     */
    private function build_estimate_prompt($workflow_data) {
        $goal = $workflow_data['goal'] ?? '';
        $workflow = $workflow_data['workflow_description'] ?? '';
        $tools = $workflow_data['tools'] ?? '';
        $pain_points = $workflow_data['pain_points'] ?? '';
        
        $prompt = "You are an AI workflow automation cost estimator. Based on the following workflow information, provide an indicative cost estimate.\n\n";
        $prompt .= "WORKFLOW INFORMATION:\n";
        $prompt .= "- Goal: {$goal}\n";
        $prompt .= "- Current Workflow: {$workflow}\n";
        $prompt .= "- Tools: {$tools}\n";
        $prompt .= "- Pain Points: {$pain_points}\n\n";
        $prompt .= "Provide an estimate in the following format:\n\n";
        $prompt .= "SETUP_COST: [range like $2,500 - $4,000]\n";
        $prompt .= "MONTHLY_COST: [range like $150 - $300]\n";
        $prompt .= "TIMELINE: [like 2-3 weeks]\n";
        $prompt .= "COMPLEXITY: [Low/Medium/High]\n";
        $prompt .= "EXPLANATION: [2-3 sentences explaining the estimate]\n\n";
        $prompt .= "Base your estimate on:\n";
        $prompt .= "- Number of integration points\n";
        $prompt .= "- Complexity of automation logic\n";
        $prompt .= "- Custom development needs\n";
        $prompt .= "- Ongoing maintenance requirements\n\n";
        $prompt .= "Provide realistic ranges. Be conservative but fair.";
        
        return $prompt;
    }
    
    /**
     * Parse estimate response from AI
     * 
     * @param string $content AI response content
     * @return array Parsed estimate data
     */
    private function parse_estimate_response($content) {
        // Extract values using regex
        $setup_cost = '';
        $monthly_cost = '';
        $timeline = '';
        $complexity = 'Medium';
        $explanation = '';
        
        if (preg_match('/SETUP_COST:\s*(.+)/i', $content, $matches)) {
            $setup_cost = trim($matches[1]);
        }
        
        if (preg_match('/MONTHLY_COST:\s*(.+)/i', $content, $matches)) {
            $monthly_cost = trim($matches[1]);
        }
        
        if (preg_match('/TIMELINE:\s*(.+)/i', $content, $matches)) {
            $timeline = trim($matches[1]);
        }
        
        if (preg_match('/COMPLEXITY:\s*(.+)/i', $content, $matches)) {
            $complexity = trim($matches[1]);
        }
        
        if (preg_match('/EXPLANATION:\s*(.+)/is', $content, $matches)) {
            $explanation = trim($matches[1]);
        }
        
        // Fallback to defaults if parsing fails
        if (empty($setup_cost)) {
            $setup_cost = '$2,000 - $5,000';
        }
        if (empty($monthly_cost)) {
            $monthly_cost = '$100 - $500';
        }
        if (empty($timeline)) {
            $timeline = '2-4 weeks';
        }
        if (empty($explanation)) {
            $explanation = 'This estimate is based on the complexity of your workflow and typical automation project costs.';
        }
        
        return [
            'setup_cost' => $setup_cost,
            'monthly_cost' => $monthly_cost,
            'timeline' => $timeline,
            'complexity' => $complexity,
            'explanation' => $explanation,
            'disclaimer' => 'This is an indicative estimate only. Final pricing will be provided in a formal quote after detailed analysis.'
        ];
    }
    
    /**
     * Generate clarifying questions based on wizard data
     * 
     * @param array $wizard_data User workflow information
     * @return array Array of 2-5 clarifying questions
     * @throws Exception If generation fails
     */
    public function generate_clarifying_questions($wizard_data) {
        // Validate API key
        if (empty($this->api_key)) {
            throw new Exception('AI API key not configured');
        }
        
        // Build the prompt for clarifying questions
        $prompt = $this->build_clarification_questions_prompt($wizard_data);
        
        try {
            // Call AI service
            if ($this->provider === 'anthropic') {
                $raw_response = $this->call_anthropic_api($prompt);
                $content = $raw_response['content'][0]['text'] ?? '';
            } else {
                $raw_response = $this->call_openai_api($prompt);
                $content = $raw_response['choices'][0]['message']['content'] ?? '';
            }
            
            if (empty($content)) {
                throw new Exception('Empty response from AI service');
            }
            
            // Parse the questions from the response
            return $this->parse_clarifying_questions($content);
            
        } catch (Exception $e) {
            error_log('[AI Service] Clarifying questions generation failed: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Build prompt for clarifying questions generation
     * 
     * @param array $wizard_data User workflow information
     * @return string Formatted prompt
     */
    private function build_clarification_questions_prompt($wizard_data) {
        $goal = $wizard_data['goal'] ?? '';
        $workflow = $wizard_data['workflow_description'] ?? '';
        $tools = $wizard_data['tools'] ?? '';
        $pain_points = $wizard_data['pain_points'] ?? '';
        
        $prompt = "You are a technical analyst. You have a MAXIMUM of 5 questions total to gather workflow data.\n\n";
        
        $prompt .= "USER SUBMITTED:\n";
        $prompt .= "Goal: {$goal}\n";
        $prompt .= "Workflow: {$workflow}\n\n";
        
        $prompt .= "PRIORITY ORDER (ask in this order):\n";
        $prompt .= "1. Type of notifications/items (MOST IMPORTANT - ask this first)\n";
        $prompt .= "2. Volume/frequency\n";
        $prompt .= "3. Operating environment (platform/tools)\n";
        $prompt .= "4. Time spent\n";
        $prompt .= "5. Common actions\n\n";
        
        $prompt .= "RULES:\n";
        $prompt .= "- NEVER mention consultations, quotes, or services\n";
        $prompt .= "- Ask ONE question (under 20 words)\n";
        $prompt .= "- Start with priority #1: type of notifications/items\n\n";
        
        $prompt .= "GOOD FIRST QUESTIONS:\n";
        $prompt .= "\"What types of emails do you get most?\"\n";
        $prompt .= "\"What types of notifications are you managing?\"\n";
        $prompt .= "\"What kinds of requests come in most often?\"\n\n";
        
        $prompt .= "Generate your first question about notification/item types (under 20 words):";
        
        return $prompt;
    }
    
    /**
     * Parse clarifying questions from AI response
     * 
     * @param string $content AI response content
     * @return array Parsed questions data with message and question list
     */
    private function parse_clarifying_questions($content) {
        // Extract numbered questions using regex
        $questions = [];
        if (preg_match_all('/\d+\.\s*(.+?)(?=\n\d+\.|\n\n|$)/s', $content, $matches)) {
            $questions = array_map('trim', $matches[1]);
        }
        
        // If no numbered questions found, return the whole content as a single message
        if (empty($questions)) {
            return [
                'message' => trim($content),
                'questions' => [],
                'count' => 0
            ];
        }
        
        return [
            'message' => trim($content),
            'questions' => $questions,
            'count' => count($questions)
        ];
    }
    
    /**
     * Test connection to AI service
     * 
     * @return array Result with success status and message/error
     */
    public function test_connection() {
        if (empty($this->api_key)) {
            return [
                'success' => false,
                'error' => 'API key is not configured'
            ];
        }
        
        try {
            // Send a minimal test request
            $test_prompt = "Respond with 'OK' if you can read this message.";
            
            if ($this->provider === 'anthropic') {
                $url = 'https://api.anthropic.com/v1/messages';
                
                $body = json_encode([
                    'model' => $this->model,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $test_prompt
                        ]
                    ],
                    'max_tokens' => 10
                ]);
                
                $args = [
                    'method' => 'POST',
                    'timeout' => 10,
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'x-api-key' => $this->api_key,
                        'anthropic-version' => '2023-06-01'
                    ],
                    'body' => $body
                ];
            } else {
                // OpenAI
                $url = 'https://api.openai.com/v1/chat/completions';
                
                $body = json_encode([
                    'model' => $this->model,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $test_prompt
                        ]
                    ],
                    'max_tokens' => 10
                ]);
                
                $args = [
                    'method' => 'POST',
                    'timeout' => 10,
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $this->api_key
                    ],
                    'body' => $body
                ];
            }
            
            $response = wp_remote_post($url, $args);
            
            if (is_wp_error($response)) {
                return [
                    'success' => false,
                    'error' => $response->get_error_message()
                ];
            }
            
            $status_code = wp_remote_retrieve_response_code($response);
            
            if ($status_code === 200) {
                return [
                    'success' => true,
                    'message' => 'Connection successful'
                ];
            } else {
                $body = wp_remote_retrieve_body($response);
                $data = json_decode($body, true);
                $error_message = $data['error']['message'] ?? 'HTTP ' . $status_code;
                
                return [
                    'success' => false,
                    'error' => $error_message
                ];
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
