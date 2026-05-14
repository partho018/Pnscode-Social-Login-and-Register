<?php
/**
 * Security & Login History Handler
 */

if (!defined('ABSPATH')) {
    exit;
}

class PNSCSOLO_Security {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('wp_login', array($this, 'log_successful_login'), 10, 2);
        add_action('wp_login_failed', array($this, 'log_failed_login'));
        add_action('admin_menu', array($this, 'add_security_menu'), 100);
    }
    
    /**
     * Log successful login
     */
    public function log_successful_login($user_login, $user) {
        $login_data = array(
            'time' => current_time('mysql'),
            'ip' => $this->get_user_ip(),
            'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'])) : '',
            'location' => $this->get_location_from_ip($this->get_user_ip()),
            'status' => 'success',
        );
        
        // Get existing login history
        $history = get_user_meta($user->ID, 'PNSCSOLO_login_history', true);
        if (!is_array($history)) {
            $history = array();
        }
        
        // Add new login
        array_unshift($history, $login_data);
        
        // Keep only last 20 logins
        $history = array_slice($history, 0, 20);
        
        // Update user meta
        update_user_meta($user->ID, 'PNSCSOLO_login_history', $history);
        update_user_meta($user->ID, 'PNSCSOLO_last_login', current_time('mysql'));
        update_user_meta($user->ID, 'PNSCSOLO_last_login_ip', $this->get_user_ip());
        
        // Clear failed login attempts
        delete_user_meta($user->ID, 'PNSCSOLO_failed_login_count');
    }
    
    /**
     * Log failed login
     */
    public function log_failed_login($username) {
        $user = get_user_by('login', $username);
        if (!$user) {
            $user = get_user_by('email', $username);
        }
        
        if (!$user) {
            return;
        }
        
        // Increment failed login count
        $failed_count = get_user_meta($user->ID, 'PNSCSOLO_failed_login_count', true);
        $failed_count = $failed_count ? intval($failed_count) + 1 : 1;
        update_user_meta($user->ID, 'PNSCSOLO_failed_login_count', $failed_count);
        update_user_meta($user->ID, 'PNSCSOLO_last_failed_login', current_time('mysql'));
        
        // Log failed attempt
        $login_data = array(
            'time' => current_time('mysql'),
            'ip' => $this->get_user_ip(),
            'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'])) : '',
            'location' => $this->get_location_from_ip($this->get_user_ip()),
            'status' => 'failed',
        );
        
        $history = get_user_meta($user->ID, 'PNSCSOLO_login_history', true);
        if (!is_array($history)) {
            $history = array();
        }
        
        array_unshift($history, $login_data);
        $history = array_slice($history, 0, 20);
        
        update_user_meta($user->ID, 'PNSCSOLO_login_history', $history);
        
        // Check if account should be locked
        $settings = PNSCSOLO_Settings::get_general_settings();
        $max_attempts = $settings['max_login_attempts'] ?? 5;
        
        if ($failed_count >= $max_attempts) {
            update_user_meta($user->ID, 'PNSCSOLO_account_locked', true);
            update_user_meta($user->ID, 'PNSCSOLO_account_locked_time', current_time('mysql'));
        }
    }
    
    /**
     * Get user IP address
     */
    private function get_user_ip() {
        $ip = '';
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = sanitize_text_field(wp_unslash($_SERVER['HTTP_CLIENT_IP']));
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = sanitize_text_field(wp_unslash($_SERVER['HTTP_X_FORWARDED_FOR']));
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR']));
        }
        return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '0.0.0.0';
    }
    
    /**
     * Get approximate location from IP
     */
    private function get_location_from_ip($ip) {
        // Simple implementation - you can integrate with IP geolocation API
        return __('Unknown', 'pnscode-social-login-and-register');
    }
    
    /**
     * Get login history for user
     */
    public static function get_login_history($user_id, $limit = 10) {
        $history = get_user_meta($user_id, 'PNSCSOLO_login_history', true);
        
        if (!is_array($history)) {
            return array();
        }
        
        return array_slice($history, 0, $limit);
    }
    
    /**
     * Check if account is locked
     */
    public static function is_account_locked($user_id) {
        $locked = get_user_meta($user_id, 'PNSCSOLO_account_locked', true);
        
        if (!$locked) {
            return false;
        }
        
        // Check if lock has expired (e.g., 30 minutes)
        $locked_time = get_user_meta($user_id, 'PNSCSOLO_account_locked_time', true);
        if ($locked_time) {
            $locked_timestamp = strtotime($locked_time);
            $unlock_time = $locked_timestamp + (30 * 60); // 30 minutes
            
            if (time() > $unlock_time) {
                // Unlock account
                delete_user_meta($user_id, 'PNSCSOLO_account_locked');
                delete_user_meta($user_id, 'PNSCSOLO_failed_login_count');
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Unlock account manually
     */
    public static function unlock_account($user_id) {
        delete_user_meta($user_id, 'PNSCSOLO_account_locked');
        delete_user_meta($user_id, 'PNSCSOLO_account_locked_time');
        delete_user_meta($user_id, 'PNSCSOLO_failed_login_count');
    }
    
    /**
     * Add security submenu
     */
    public function add_security_menu() {
        add_submenu_page(
            'pnscsolo-social-login',
            __('Security & Logs', 'pnscode-social-login-and-register'),
            __('Security', 'pnscode-social-login-and-register'),
            'manage_options',
            'pnscsolo-social-login-security',
            array($this, 'render_security_page')
        );
    }
    
    /**
     * Render security page
     */
    public function render_security_page() {
        require_once PNSCSOLO_PLUGIN_DIR . 'admin/views/security.php';
    }
}
