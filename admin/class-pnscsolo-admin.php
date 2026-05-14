<?php
/**
 * Admin Handler
 */

if (!defined('ABSPATH')) {
    exit;
}

class PNSCSOLO_Admin {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_post_PNSCSOLO_save_general_settings', array($this, 'save_general_settings'));
        add_action('admin_post_PNSCSOLO_save_provider_settings', array($this, 'save_provider_settings'));
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        register_setting('PNSCSOLO_general_settings', 'PNSCSOLO_general_settings', array(
            'sanitize_callback' => array($this, 'sanitize_general_settings')
        ));
        register_setting('PNSCSOLO_registration_fields', 'PNSCSOLO_registration_fields', array(
            'sanitize_callback' => array($this, 'sanitize_registration_fields')
        ));
    }
    
    /**
     * Save general settings
     */
    public function save_general_settings() {
        check_admin_referer('PNSCSOLO_save_general_settings');
        
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('Permission denied', 'pnscode-social-login-and-register'));
        }
        
        $settings = array(
            'plugin_enabled' => isset($_POST['plugin_enabled']),
            'registration_enabled' => isset($_POST['registration_enabled']),
            'social_login_enabled' => isset($_POST['social_login_enabled']),
            'redirect_after_login' => sanitize_text_field(wp_unslash($_POST['redirect_after_login'] ?? '')),
            'redirect_after_registration' => sanitize_text_field(wp_unslash($_POST['redirect_after_registration'] ?? '')),
            'default_user_role' => sanitize_text_field(wp_unslash($_POST['default_user_role'] ?? 'subscriber')),
            'max_login_attempts' => intval(wp_unslash($_POST['max_login_attempts'] ?? 5)),
        );
        
        PNSCSOLO_Settings::update_general_settings($settings);
        
        $redirect_url = admin_url('admin.php?page=pnscsolo-social-login&message=saved');
        $pnscsolo_referer = isset($_POST['_wp_http_referer']) ? sanitize_text_field(wp_unslash($_POST['_wp_http_referer'])) : '';
        if (strpos($pnscsolo_referer, 'pnscsolo-social-login-security') !== false) {
            $redirect_url = admin_url('admin.php?page=pnscsolo-social-login-security&message=saved');
        }
        
        wp_safe_redirect($redirect_url);
        exit;
    }
    
    /**
     * Save provider settings
     */
    public function save_provider_settings() {
        check_admin_referer('PNSCSOLO_save_provider_settings');
        
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('Permission denied', 'pnscode-social-login-and-register'));
        }
        
        $provider = isset($_POST['provider']) ? sanitize_text_field(wp_unslash($_POST['provider'])) : '';
        
        if (empty($provider)) {
            wp_die(esc_html__('Invalid provider', 'pnscode-social-login-and-register'));
        }
        
        $settings = array(
            'enabled' => isset($_POST['enabled']),
            'client_id' => sanitize_text_field(wp_unslash($_POST['client_id'] ?? '')),
            'client_secret' => sanitize_text_field(wp_unslash($_POST['client_secret'] ?? '')),
            'button_text' => sanitize_text_field(wp_unslash($_POST['button_text'] ?? '')),
        );
        
        PNSCSOLO_Settings::update_provider_settings($provider, $settings);
        
        wp_safe_redirect(add_query_arg(array(
            'page' => 'pnscsolo-social-login-providers',
            'provider' => $provider,
            'message' => 'saved'
        ), admin_url('admin.php')));
        exit;
    }

    /**
     * Sanitize general settings
     */
    public function sanitize_general_settings($input) {
        $sanitized = array();
        if (is_array($input)) {
            $sanitized['plugin_enabled'] = isset($input['plugin_enabled']) ? true : false;
            $sanitized['registration_enabled'] = isset($input['registration_enabled']) ? true : false;
            $sanitized['social_login_enabled'] = isset($input['social_login_enabled']) ? true : false;
            $sanitized['redirect_after_login'] = sanitize_text_field($input['redirect_after_login'] ?? '');
            $sanitized['redirect_after_registration'] = sanitize_text_field($input['redirect_after_registration'] ?? '');
            $sanitized['default_user_role'] = sanitize_text_field($input['default_user_role'] ?? 'subscriber');
            $sanitized['max_login_attempts'] = intval($input['max_login_attempts'] ?? 5);
        }
        return $sanitized;
    }

    /**
     * Sanitize registration fields
     */
    public function sanitize_registration_fields($input) {
        if (!is_array($input)) {
            return array();
        }
        
        $sanitized = array();
        foreach ($input as $field) {
            $inner = array();
            $inner['id'] = sanitize_key($field['id'] ?? '');
            $inner['type'] = sanitize_text_field($field['type'] ?? '');
            $inner['label'] = sanitize_text_field($field['label'] ?? '');
            $inner['placeholder'] = sanitize_text_field($field['placeholder'] ?? '');
            $inner['required'] = isset($field['required']) ? true : false;
            $inner['enabled'] = isset($field['enabled']) ? true : false;
            $inner['order'] = intval($field['order'] ?? 0);
            
            if (isset($field['options']) && is_array($field['options'])) {
                $inner['options'] = array_map('sanitize_text_field', $field['options']);
            }
            
            $sanitized[] = $inner;
        }
        return $sanitized;
    }
}
