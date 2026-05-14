<?php
/**
 * Facebook OAuth Provider
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once PNSCSOLO_PLUGIN_DIR . 'includes/providers/class-pnscsolo-provider-base.php';

class PNSCSOLO_Provider_Facebook extends PNSCSOLO_Provider_Base {
    
    public function get_name() {
        return 'facebook';
    }
    
    public function get_display_name() {
        return 'Facebook';
    }
    
    public function get_auth_url() {
        return 'https://www.facebook.com/v18.0/dialog/oauth';
    }
    
    public function get_token_url() {
        return 'https://graph.facebook.com/v18.0/oauth/access_token';
    }
    
    public function get_user_info_url() {
        return 'https://graph.facebook.com/v18.0/me?fields=id,name,email,first_name,last_name,picture';
    }
    
    public function get_scopes() {
        return array('email', 'public_profile');
    }
    
    public function parse_user_data($response) {
        return array(
            'id' => $response['id'] ?? '',
            'email' => $response['email'] ?? '',
            'name' => $response['name'] ?? '',
            'first_name' => $response['first_name'] ?? '',
            'last_name' => $response['last_name'] ?? '',
            'picture' => $response['picture']['data']['url'] ?? '',
        );
    }
    
    public function get_icon() {
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"><path fill="#1877F2" d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>';
    }
}
