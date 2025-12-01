<?php
/**
 * MailerLite Webhook Handler
 * 
 * Captures form submissions from MailerLite and saves them to WordPress database
 * including the submission_ref (AI submission ID)
 * 
 * @package MGRNZ_AI_Workflow
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register webhook endpoint
 */
add_action('rest_api_init', function() {
    register_rest_route('mgrnz/v1', '/mailerlite-webhook', [
        'methods'  => 'POST',
        'permission_callback' => '__return_true', // MailerLite webhooks don't use WP auth
        'callback' => 'mgrnz_handle_mailerlite_webhook',
    ]);
});

/**
 * Handle MailerLite webhook
 * 
 * @param WP_REST_Request $request
 * @return WP_REST_Response
 */
function mgrnz_handle_mailerlite_webhook($request) {
    $data = $request->get_json_params();
    
    error_log('[MailerLite Webhook] Received: ' . print_r($data, true));
    
    // MailerLite sends different event types
    $event_type = $data['type'] ?? $data['event'] ?? 'unknown';
    
    // We're interested in subscriber.created or form.submitted events
    if (!in_array($event_type, ['subscriber.created', 'subscriber.create', 'form.submitted', 'subscriber'])) {
        error_log('[MailerLite Webhook] Ignoring event type: ' . $event_type);
        return new WP_REST_Response(['status' => 'ignored', 'event' => $event_type], 200);
    }
    
    // Extract subscriber data
    $subscriber = $data['data'] ?? $data['subscriber'] ?? $data;
    
    $email = $subscriber['email'] ?? '';
    $name = $subscriber['name'] ?? ($subscriber['fields']['name'] ?? '');
    $last_name = $subscriber['last_name'] ?? ($subscriber['fields']['last_name'] ?? '');
    $company = $subscriber['fields']['company'] ?? '';
    $message = $subscriber['fields']['message'] ?? '';
    $submission_ref = $subscriber['fields']['submission_ref'] ?? '';
    
    if (empty($email)) {
        error_log('[MailerLite Webhook] No email found in webhook data');
        return new WP_REST_Response(['status' => 'error', 'message' => 'No email'], 400);
    }
    
    $full_name = trim($name . ' ' . $last_name);
    
    // Find the AI submission by submission_ref
    if (!empty($submission_ref)) {
        $args = [
            'post_type' => 'ai_workflow_sub',
            'meta_query' => [
                [
                    'key' => '_mgrnz_submission_ref',
                    'value' => $submission_ref,
                    'compare' => '='
                ]
            ],
            'posts_per_page' => 1
        ];
        
        $query = new WP_Query($args);
        
        if ($query->have_posts()) {
            $post_id = $query->posts[0]->ID;
            
            // Add meta data about the quote request
            update_post_meta($post_id, '_mgrnz_quote_requested', true);
            update_post_meta($post_id, '_mgrnz_quote_requested_at', current_time('mysql'));
            update_post_meta($post_id, '_mgrnz_quote_contact_name', $full_name);
            update_post_meta($post_id, '_mgrnz_quote_contact_email', $email);
            update_post_meta($post_id, '_mgrnz_quote_company', $company);
            update_post_meta($post_id, '_mgrnz_quote_message', $message);
            
            error_log('[MailerLite Webhook] ✅ Updated AI submission ' . $post_id . ' with quote request from: ' . $email);
            
            return new WP_REST_Response([
                'status' => 'success',
                'message' => 'AI submission updated with quote request',
                'submission_id' => $post_id,
                'submission_ref' => $submission_ref,
                'email' => $email
            ], 200);
        } else {
            error_log('[MailerLite Webhook] ⚠️ No AI submission found for submission_ref: ' . $submission_ref);
        }
    }
    
    // If no submission_ref or submission not found, just log it
    error_log('[MailerLite Webhook] Form submitted without valid submission_ref: ' . $email);
    
    return new WP_REST_Response([
        'status' => 'success',
        'email' => $email,
        'submission_ref' => $submission_ref
    ], 200);
}

/**
 * Add admin notice with webhook URL
 */
add_action('admin_notices', function() {
    $screen = get_current_screen();
    if ($screen && $screen->id === 'settings_page_mgrnz-mailerlite') {
        $webhook_url = rest_url('mgrnz/v1/mailerlite-webhook');
        ?>
        <div class="notice notice-info">
            <h3>📡 MailerLite Webhook Setup</h3>
            <p>To sync form submissions to your WordPress database, add this webhook URL in MailerLite:</p>
            <p>
                <input type="text" 
                       value="<?php echo esc_attr($webhook_url); ?>" 
                       readonly 
                       style="width: 100%; max-width: 600px; padding: 8px; font-family: monospace; background: #f0f0f0;"
                       onclick="this.select()">
                <button type="button" 
                        class="button button-secondary" 
                        onclick="navigator.clipboard.writeText('<?php echo esc_js($webhook_url); ?>'); alert('Copied!');">
                    Copy URL
                </button>
            </p>
            <p>
                <strong>Setup Instructions:</strong>
            </p>
            <ol>
                <li>Go to <a href="https://dashboard.mailerlite.com/integrations/webhooks" target="_blank">MailerLite → Integrations → Webhooks</a></li>
                <li>Click "Add Webhook"</li>
                <li>Paste the URL above</li>
                <li>Select event: <strong>Subscriber created</strong></li>
                <li>Save the webhook</li>
            </ol>
        </div>
        <?php
    }
});
