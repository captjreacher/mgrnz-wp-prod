<?php
/**
 * Simple Quote Request Handler
 * 
 * Handles quote requests directly without needing MailerLite webhooks.
 * When the MailerLite form is submitted, it redirects to a thank you page
 * with the submission_ref in the URL, and this script marks it as "quote requested".
 * 
 * @package MGRNZ_AI_Workflow
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register REST endpoint for marking quote as requested
 */
add_action('rest_api_init', function() {
    register_rest_route('mgrnz/v1', '/mark-quote-requested', [
        'methods'  => 'POST',
        'permission_callback' => '__return_true',
        'callback' => 'mgrnz_mark_quote_requested',
    ]);
});

/**
 * Mark a submission as "quote requested"
 * 
 * @param WP_REST_Request $request
 * @return WP_REST_Response
 */
function mgrnz_mark_quote_requested($request) {
    $data = $request->get_json_params();
    
    // Get submission_ref from request
    $submission_ref = sanitize_text_field($data['submission_ref'] ?? '');
    
    if (empty($submission_ref)) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'No submission_ref provided'
        ], 400);
    }
    
    // Find the AI submission by submission_ref
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
    
    if (!$query->have_posts()) {
        error_log('[Quote Request] No submission found for ref: ' . $submission_ref);
        return new WP_REST_Response([
            'success' => false,
            'message' => 'Submission not found'
        ], 404);
    }
    
    $post_id = $query->posts[0]->ID;
    
    // Mark as quote requested
    update_post_meta($post_id, '_mgrnz_quote_requested', true);
    update_post_meta($post_id, '_mgrnz_quote_requested_at', current_time('mysql'));
    
    error_log('[Quote Request] ✅ Marked submission ' . $post_id . ' (' . $submission_ref . ') as quote requested');
    
    return new WP_REST_Response([
        'success' => true,
        'message' => 'Quote request recorded',
        'submission_id' => $post_id,
        'submission_ref' => $submission_ref
    ], 200);
}

/**
 * Alternative: Handle via URL parameter on thank you page
 * Add this to your thank you page template
 */
add_action('template_redirect', function() {
    // Check if we're on the thank you page with a submission_ref
    if (is_page('thank-you-quote') || is_page('quote-thank-you')) {
        $submission_ref = sanitize_text_field($_GET['ref'] ?? $_GET['submission_ref'] ?? '');
        
        if (!empty($submission_ref)) {
            // Find and mark the submission
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
                
                // Only mark if not already marked (prevent duplicate marking on page refresh)
                if (!get_post_meta($post_id, '_mgrnz_quote_requested', true)) {
                    update_post_meta($post_id, '_mgrnz_quote_requested', true);
                    update_post_meta($post_id, '_mgrnz_quote_requested_at', current_time('mysql'));
                    
                    error_log('[Quote Request] ✅ Marked submission ' . $post_id . ' (' . $submission_ref . ') as quote requested via thank you page');
                }
            }
        }
    }
});
