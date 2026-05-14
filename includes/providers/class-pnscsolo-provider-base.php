<?php
/**
 * Base Social Provider Class
 */

if (!defined('ABSPATH')) {
    exit;
}

abstract class PNSCSOLO_Provider_Base {
    
    protected $provider_name;
    protected $settings;
    
    /**
     * Get provider name
     */
    abstract public function get_name();
    
    /**
     * Get provider display name
     */
    abstract public function get_display_name();
    
    /**
     * Get authorization URL
     */
    abstract public function get_auth_url();
    
    /**
     * Get token URL
     */
    abstract public function get_token_url();
    
    /**
     * Get user info URL
     */
    abstract public function get_user_info_url();
    
    /**
     * Get required scopes
     */
    abstract public function get_scopes();
    
    /**
     * Parse user data from provider response
     */
    abstract public function parse_user_data($response);
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->provider_name = $this->get_name();
        $this->settings = PNSCSOLO_Settings::get_provider_settings($this->provider_name);
    }
    
    /**
     * Get callback URL
     */
    public function get_callback_url() {
        return add_query_arg(array(
            'PNSCSOLO_provider' => $this->provider_name,
            'PNSCSOLO_action' => 'callback',
        ), home_url('/'));
    }
    
    /**
     * Get login URL
     */
    public function get_login_url() {
        $client_id = $this->get_client_id();
        
        if (empty($client_id)) {
            return '#';
        }
        
        $params = array(
            'client_id' => $client_id,
            'redirect_uri' => $this->get_callback_url(),
            'response_type' => 'code',
            'scope' => implode(' ', $this->get_scopes()),
            'state' => wp_create_nonce('PNSCSOLO_oauth_' . $this->provider_name),
        );
        
        return add_query_arg($params, $this->get_auth_url());
    }
    
    /**
     * Handle OAuth callback
     */
    public function handle_callback() {
        // Verify state
        if (!isset($_GET['state']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['state'])), 'PNSCSOLO_oauth_' . $this->provider_name)) {
            return new WP_Error('invalid_state', __('Invalid state parameter', 'pnscode-social-login-and-register'));
        }
        
        // Check for error
        if (isset($_GET['error'])) {
            return new WP_Error('oauth_error', sanitize_text_field(wp_unslash($_GET['error_description'] ?? $_GET['error'])));
        }
        
        // Get authorization code
        if (!isset($_GET['code'])) {
            return new WP_Error('no_code', __('Authorization code not received', 'pnscode-social-login-and-register'));
        }
        
        $code = sanitize_text_field(wp_unslash($_GET['code']));
        
        // Exchange code for access token
        $token = $this->get_access_token($code);
        
        if (is_wp_error($token)) {
            return $token;
        }
        
        return $token;
    }
    
    /**
     * Get access token
     */
    protected function get_access_token($code) {
        $params = array(
            'client_id' => $this->get_client_id(),
            'client_secret' => $this->get_client_secret(),
            'code' => $code,
            'redirect_uri' => $this->get_callback_url(),
            'grant_type' => 'authorization_code',
        );
        
        $response = wp_remote_post($this->get_token_url(), array(
            'body' => $params,
            'headers' => array(
                'Accept' => 'application/json',
            ),
        ));
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (isset($data['error'])) {
            return new WP_Error('token_error', $data['error_description'] ?? $data['error']);
        }
        
        if (!isset($data['access_token'])) {
            return new WP_Error('no_token', __('Access token not received', 'pnscode-social-login-and-register'));
        }
        
        return $data['access_token'];
    }
    
    /**
     * Get user data
     */
    public function get_user_data($access_token) {
        $response = wp_remote_get($this->get_user_info_url(), array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $access_token,
                'Accept' => 'application/json',
            ),
        ));
        
        if (is_wp_error($response)) {
            return $response;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (isset($data['error'])) {
            return new WP_Error('user_data_error', $data['error_description'] ?? $data['error']);
        }
        
        return $this->parse_user_data($data);
    }
    
    /**
     * Get client ID
     */
    protected function get_client_id() {
        return $this->settings['client_id'] ?? '';
    }
    
    /**
     * Get client secret
     */
    protected function get_client_secret() {
        return $this->settings['client_secret'] ?? '';
    }
    
    /**
     * Get button text
     */
    public function get_button_text() {
        $custom_text = $this->settings['button_text'] ?? '';
        
        if (!empty($custom_text)) {
            return $custom_text;
        }
        
        /* translators: %s: Provider display name */
        return sprintf(__('Continue with %s', 'pnscode-social-login-and-register'), $this->get_display_name());
    }
    
    /**
     * Get icon SVG
     */
    abstract public function get_icon();
}
