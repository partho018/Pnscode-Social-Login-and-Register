<?php
/**
 * Settings Handler
 */

if (!defined('ABSPATH')) {
    exit;
}

class PNSCSOLO_Settings {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Settings will be managed through admin panel
    }
    
    /**
     * Get general settings
     */
    public static function get_general_settings() {
        return get_option('PNSCSOLO_general_settings', array());
    }
    
    /**
     * Update general settings
     */
    public static function update_general_settings($settings) {
        return update_option('PNSCSOLO_general_settings', $settings);
    }
    
    /**
     * Get social provider settings
     */
    public static function get_provider_settings($provider) {
        return get_option('PNSCSOLO_provider_' . $provider, array());
    }
    
    /**
     * Update social provider settings
     */
    public static function update_provider_settings($provider, $settings) {
        return update_option('PNSCSOLO_provider_' . $provider, $settings);
    }
    
    /**
     * Get registration fields
     */
    public static function get_registration_fields() {
        $fields = get_option('PNSCSOLO_registration_fields', array());
        
        // Sort by order
        usort($fields, function($a, $b) {
            return ($a['order'] ?? 0) - ($b['order'] ?? 0);
        });
        
        return $fields;
    }
    
    /**
     * Update registration fields
     */
    public static function update_registration_fields($fields) {
        return update_option('PNSCSOLO_registration_fields', $fields);
    }
    
    /**
     * Check if plugin is enabled
     */
    public static function is_plugin_enabled() {
        $settings = self::get_general_settings();
        return isset($settings['plugin_enabled']) && $settings['plugin_enabled'];
    }
    
    /**
     * Check if registration is enabled
     */
    public static function is_registration_enabled() {
        $settings = self::get_general_settings();
        return isset($settings['registration_enabled']) && $settings['registration_enabled'];
    }
    
    /**
     * Check if social login is enabled
     */
    public static function is_social_login_enabled() {
        $settings = self::get_general_settings();
        return isset($settings['social_login_enabled']) && $settings['social_login_enabled'];
    }
    
    /**
     * Check if provider is enabled
     */
    public static function is_provider_enabled($provider) {
        $settings = self::get_provider_settings($provider);
        return isset($settings['enabled']) && $settings['enabled'];
    }
    
    /**
     * Get redirect URL after login
     */
    public static function get_login_redirect() {
        $settings = self::get_general_settings();
        return $settings['redirect_after_login'] ?? home_url();
    }
    
    /**
     * Get redirect URL after registration
     */
    public static function get_registration_redirect() {
        $settings = self::get_general_settings();
        return $settings['redirect_after_registration'] ?? home_url();
    }
    
    /**
     * Get default user role
     */
    public static function get_default_user_role() {
        $settings = self::get_general_settings();
        return $settings['default_user_role'] ?? 'subscriber';
    }
}
