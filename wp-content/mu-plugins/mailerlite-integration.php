<?php
/**
 * MailerLite Integration
 * 
 * Syncs blueprint subscriptions to MailerLite with AI submission ID
 * 
 * @package MGRNZ_AI_Workflow
 */

if (!defined('ABSPATH')) {
    exit;
}

class MGRNZ_MailerLite_Integration {
    
    /**
     * MailerLite API key
     * @var string
     */
    private $api_key;
    
    /**
     * MailerLite API endpoint
     * @var string
     */
    private $api_endpoint = 'https://connect.mailerlite.com/api';
    
    /**
     * Constructor
     */
    public function __construct() {
        // Get API key from WordPress options
        $this->api_key = get_option('mgrnz_mailerlite_api_key', '');
        
        // Add settings page
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('admin_init', [$this, 'register_settings']);
    }
    
    /**
     * Add settings page to WordPress admin
     */
    public function add_settings_page() {
        add_options_page(
            'MailerLite Integration',
            'MailerLite',
            'manage_options',
            'mgrnz-mailerlite',
            [$this, 'render_settings_page']
        );
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('mgrnz_mailerlite', 'mgrnz_mailerlite_api_key');
        register_setting('mgrnz_mailerlite', 'mgrnz_mailerlite_group_id');
        register_setting('mgrnz_mailerlite', 'mgrnz_mailerlite_enabled');
    }
    
    /**
     * Render settings page
     */
    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1>MailerLite Integration Settings</h1>
            
            <form method="post" action="options.php">
                <?php settings_fields('mgrnz_mailerlite'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="mgrnz_mailerlite_enabled">Enable Integration</label>
                        </th>
                        <td>
                            <input type="checkbox" 
                                   id="mgrnz_mailerlite_enabled" 
                                   name="mgrnz_mailerlite_enabled" 
                                   value="1" 
                                   <?php checked(get_option('mgrnz_mailerlite_enabled'), '1'); ?>>
                            <p class="description">Enable automatic sync to MailerLite</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="mgrnz_mailerlite_api_key">API Key</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="mgrnz_mailerlite_api_key" 
                                   name="mgrnz_mailerlite_api_key" 
                                   value="<?php echo esc_attr(get_option('mgrnz_mailerlite_api_key')); ?>" 
                                   class="regular-text">
                            <p class="description">
                                Get your API key from 
                                <a href="https://dashboard.mailerlite.com/integrations/api" target="_blank">MailerLite Dashboard</a>
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="mgrnz_mailerlite_group_id">Group ID (Optional)</label>
                        </th>
                        <td>
                            <input type="text" 
                                   id="mgrnz_mailerlite_group_id" 
                                   name="mgrnz_mailerlite_group_id" 
                                   value="<?php echo esc_attr(get_option('mgrnz_mailerlite_group_id')); ?>" 
                                   class="regular-text">
                            <p class="description">Add subscribers to a specific group (leave empty for no group)</p>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(); ?>
            </form>
            
            <hr>
            
            <h2>Custom Field Setup</h2>
            <p>To track AI submission IDs in MailerLite, create a custom field:</p>
            <ol>
                <li>Go to <a href="https://dashboard.mailerlite.com/subscribers/fields" target="_blank">MailerLite → Subscribers → Fields</a></li>
                <li>Click "Create Field"</li>
                <li>Field name: <code>submission_ref</code></li>
                <li>Field type: Text</li>
                <li>Save the field</li>
            </ol>
            
            <hr>
            
            <h2>Webhook Setup (Sync Form Submissions to WordPress)</h2>
            <p>To save MailerLite form submissions to your WordPress database:</p>
            <ol>
                <li>Copy this webhook URL:
                    <br>
                    <input type="text" 
                           value="<?php echo esc_attr(rest_url('mgrnz/v1/mailerlite-webhook')); ?>" 
                           readonly 
                           style="width: 100%; max-width: 600px; padding: 8px; margin: 10px 0; font-family: monospace; background: #f0f0f0;"
                           onclick="this.select()">
                    <button type="button" 
                            class="button button-secondary" 
                            onclick="navigator.clipboard.writeText('<?php echo esc_js(rest_url('mgrnz/v1/mailerlite-webhook')); ?>'); alert('Webhook URL copied!');">
                        Copy URL
                    </button>
                </li>
                <li>Go to <a href="https://dashboard.mailerlite.com/integrations/webhooks" target="_blank">MailerLite → Integrations → Webhooks</a></li>
                <li>Click "Add Webhook"</li>
                <li>Paste the URL above</li>
                <li>Select event: <strong>Subscriber created</strong></li>
                <li>Save the webhook</li>
            </ol>
            <p><strong>Note:</strong> This webhook will automatically save form submissions (including the submission_ref) to your WordPress database.</p>
            
            <h2>Test Integration</h2>
            <p>
                <a href="<?php echo admin_url('admin.php?page=mgrnz-mailerlite&test=1'); ?>" 
                   class="button button-secondary">
                    Test Connection
                </a>
            </p>
            
            <?php
            if (isset($_GET['test']) && $_GET['test'] == '1') {
                $this->test_connection();
            }
            ?>
        </div>
        <?php
    }
    
    /**
     * Test MailerLite connection
     */
    private function test_connection() {
        if (empty($this->api_key)) {
            echo '<div class="notice notice-error"><p>Please enter your API key first.</p></div>';
            return;
        }
        
        $response = wp_remote_get($this->api_endpoint . '/subscribers', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->api_key,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ],
            'timeout' => 15
        ]);
        
        if (is_wp_error($response)) {
            echo '<div class="notice notice-error"><p>Connection failed: ' . esc_html($response->get_error_message()) . '</p></div>';
            return;
        }
        
        $status_code = wp_remote_retrieve_response_code($response);
        
        if ($status_code === 200) {
            echo '<div class="notice notice-success"><p>✅ Connection successful! MailerLite API is working.</p></div>';
        } else {
            $body = wp_remote_retrieve_body($response);
            echo '<div class="notice notice-error"><p>Connection failed (Status: ' . $status_code . '): ' . esc_html($body) . '</p></div>';
        }
    }
    
    /**
     * Sync subscriber to MailerLite
     * 
     * @param string $email Email address
     * @param string $name Subscriber name
     * @param string $ai_submission_id AI submission ID
     * @return bool|WP_Error True on success, WP_Error on failure
     */
    public function sync_subscriber($email, $name, $ai_submission_id) {
        // Check if integration is enabled
        if (get_option('mgrnz_mailerlite_enabled') !== '1') {
            return new WP_Error('disabled', 'MailerLite integration is disabled');
        }
        
        // Check if API key is set
        if (empty($this->api_key)) {
            return new WP_Error('no_api_key', 'MailerLite API key not configured');
        }
        
        // Prepare subscriber data
        $data = [
            'email' => $email,
            'fields' => [
                'name' => $name,
                'ai_submission_id' => $ai_submission_id
            ]
        ];
        
        // Add to group if specified
        $group_id = get_option('mgrnz_mailerlite_group_id');
        if (!empty($group_id)) {
            $data['groups'] = [$group_id];
        }
        
        // Send to MailerLite API
        $response = wp_remote_post($this->api_endpoint . '/subscribers', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->api_key,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ],
            'body' => json_encode($data),
            'timeout' => 15
        ]);
        
        if (is_wp_error($response)) {
            error_log('[MailerLite] Sync failed: ' . $response->get_error_message());
            return $response;
        }
        
        $status_code = wp_remote_retrieve_response_code($response);
        $body = json_decode(wp_remote_retrieve_body($response), true);
        
        if ($status_code === 200 || $status_code === 201) {
            error_log('[MailerLite] Subscriber synced: ' . $email . ' (AI ID: ' . $ai_submission_id . ')');
            return true;
        } else {
            $error_message = isset($body['message']) ? $body['message'] : 'Unknown error';
            error_log('[MailerLite] Sync failed (Status: ' . $status_code . '): ' . $error_message);
            return new WP_Error('sync_failed', $error_message, ['status' => $status_code]);
        }
    }
}

// Initialize the integration
$mgrnz_mailerlite = new MGRNZ_MailerLite_Integration();

/**
 * Helper function to sync subscriber to MailerLite
 * 
 * @param string $email Email address
 * @param string $name Subscriber name
 * @param string $ai_submission_id AI submission ID
 * @return bool|WP_Error
 */
function mgrnz_sync_to_mailerlite($email, $name, $ai_submission_id) {
    global $mgrnz_mailerlite;
    return $mgrnz_mailerlite->sync_subscriber($email, $name, $ai_submission_id);
}
