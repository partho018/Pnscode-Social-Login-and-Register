<?php
/**
 * reCAPTCHA Handler
 */

if (!defined('ABSPATH')) {
    exit;
}

class PNSCSOLO_Recaptcha {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_recaptcha'));
    }
    
    /**
     * Enqueue reCAPTCHA script
     */
    public function enqueue_recaptcha() {
        $settings = PNSCSOLO_Settings::get_general_settings();
        
        if (!isset($settings['recaptcha_enabled']) || !$settings['recaptcha_enabled']) {
            return;
        }
        
        $site_key = $settings['recaptcha_site_key'] ?? '';
        
        if (empty($site_key)) {
            return;
        }
        
        wp_enqueue_script(
            'google-recaptcha',
            'https://www.google.com/recaptcha/api.js',
            array(),
            null,
            true
        );
    }
    
    /**
     * Render reCAPTCHA widget
     */
    public static function render_recaptcha() {
        $settings = PNSCSOLO_Settings::get_general_settings();
        
        if (!isset($settings['recaptcha_enabled']) || !$settings['recaptcha_enabled']) {
            return '';
        }
        
        $site_key = $settings['recaptcha_site_key'] ?? '';
        
        if (empty($site_key)) {
            return '';
        }
        
        return sprintf(
            '<div class="pnscsolo-recaptcha-wrapper"><div class="g-recaptcha" data-sitekey="%s"></div></div>',
            esc_attr($site_key)
        );
    }
    
    /**
     * Verify reCAPTCHA response
     */
    public static function verify_recaptcha($response) {
        $settings = PNSCSOLO_Settings::get_general_settings();
        
        if (!isset($settings['recaptcha_enabled']) || !$settings['recaptcha_enabled']) {
            return true;
        }
        
        $secret_key = $settings['recaptcha_secret_key'] ?? '';
        
        if (empty($secret_key)) {
            return true;
        }
        
        if (empty($response)) {
            return false;
        }
        
        $verify_url = 'https://www.google.com/recaptcha/api/siteverify';
        
        $response = wp_remote_post($verify_url, array(
            'body' => array(
                'secret' => $secret_key,
                'response' => $response,
                'remoteip' => isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '',
            ),
        ));
        
        if (is_wp_error($response)) {
            return false;
        }
        
        $body = wp_remote_retrieve_body($response);
        $result = json_decode($body, true);
        
        return isset($result['success']) && $result['success'];
    }
}
