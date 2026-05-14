<?php
/**
 * Twitter (X) OAuth Provider
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once PNSCSOLO_PLUGIN_DIR . 'includes/providers/class-pnscsolo-provider-base.php';

class PNSCSOLO_Provider_Twitter extends PNSCSOLO_Provider_Base {
    
    public function get_name() {
        return 'twitter';
    }
    
    public function get_display_name() {
        return 'X (Twitter)';
    }
    
    public function get_auth_url() {
        return 'https://twitter.com/i/oauth2/authorize';
    }
    
    public function get_token_url() {
        return 'https://api.twitter.com/2/oauth2/token';
    }
    
    public function get_user_info_url() {
        return 'https://api.twitter.com/2/users/me?user.fields=profile_image_url';
    }
    
    public function get_scopes() {
        return array('tweet.read', 'users.read');
    }
    
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
            'code_challenge' => 'challenge',
            'code_challenge_method' => 'plain',
        );
        
        return add_query_arg($params, $this->get_auth_url());
    }
    
    protected function get_access_token($code) {
        $params = array(
            'client_id' => $this->get_client_id(),
            'code' => $code,
            'redirect_uri' => $this->get_callback_url(),
            'grant_type' => 'authorization_code',
            'code_verifier' => 'challenge',
        );
        
        $auth = base64_encode($this->get_client_id() . ':' . $this->get_client_secret());
        
        $response = wp_remote_post($this->get_token_url(), array(
            'body' => $params,
            'headers' => array(
                'Authorization' => 'Basic ' . $auth,
                'Content-Type' => 'application/x-www-form-urlencoded',
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
    
    public function parse_user_data($response) {
        $data = $response['data'] ?? array();
        
        return array(
            'id' => $data['id'] ?? '',
            'email' => $data['username'] . '@twitter.temp', // Twitter doesn't provide email by default
            'name' => $data['name'] ?? '',
            'picture' => $data['profile_image_url'] ?? '',
        );
    }
    
    public function get_icon() {
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"><path fill="#000000" d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>';
    }
}
