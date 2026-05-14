<?php
/**
 * Email Verification Handler
 */

if (!defined('ABSPATH')) {
    exit;
}

class PNSCSOLO_Email_Verification {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('init', array($this, 'handle_verification'));
        add_filter('authenticate', array($this, 'check_verification'), 30, 3);
    }
    
    /**
     * Send verification email
     */
    public static function send_verification_email($user_id) {
        $settings = PNSCSOLO_Settings::get_general_settings();
        
        // Check if email verification is enabled
        if (!isset($settings['email_verification_enabled']) || !$settings['email_verification_enabled']) {
            return true;
        }
        
        $user = get_user_by('id', $user_id);
        if (!$user) {
            return false;
        }
        
        // Generate verification token
        $token = wp_generate_password(32, false);
        update_user_meta($user_id, 'PNSCSOLO_verification_token', $token);
        update_user_meta($user_id, 'PNSCSOLO_email_verified', false);
        
        // Create verification URL
        $verification_url = add_query_arg(array(
            'PNSCSOLO_action' => 'verify_email',
            'user_id' => $user_id,
            'token' => $token,
        ), home_url('/'));
        
        // Email subject and message
        /* translators: %s: Site name */
        $subject = sprintf(__('Verify your email address - %s', 'pnscode-social-login-and-register'), get_bloginfo('name'));
        
        $message = sprintf(
            /* translators: 1: User display name, 2: Site name, 3: Verification URL, 4: Site name */
            __('Hello %s,', 'pnscode-social-login-and-register') . "\n\n" . esc_html__('Thank you for registering at %s.', 'pnscode-social-login-and-register') . "\n\n" . esc_html__('Please click the link below to verify your email address:', 'pnscode-social-login-and-register') . "\n\n" .
            '%s' . "\n\n" . esc_html__('This link will expire in 24 hours.', 'pnscode-social-login-and-register') . "\n\n" . esc_html__('If you did not create an account, please ignore this email.', 'pnscode-social-login-and-register') . "\n\n" . esc_html__('Best regards,', 'pnscode-social-login-and-register') . "\n" .
            '%s',
            $user->display_name,
            get_bloginfo('name'),
            $verification_url,
            get_bloginfo('name')
        );
        
        // Send email
        $sent = wp_mail($user->user_email, $subject, $message);
        
        if ($sent) {
            update_user_meta($user_id, 'PNSCSOLO_verification_email_sent', time());
        }
        
        return $sent;
    }
    
    /**
     * Handle email verification
     */
    public function handle_verification() {
        if (!isset($_GET['PNSCSOLO_action']) || sanitize_text_field(wp_unslash($_GET['PNSCSOLO_action'])) !== 'verify_email') {
            return;
        }
        
        $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;
        $token = isset($_GET['token']) ? sanitize_text_field(wp_unslash($_GET['token'])) : '';
        
        if (!$user_id || !$token) {
            wp_die(esc_html__('Invalid verification link.', 'pnscode-social-login-and-register'));
        }
        
        $stored_token = get_user_meta($user_id, 'PNSCSOLO_verification_token', true);
        
        if ($token !== $stored_token) {
            wp_die(esc_html__('Invalid or expired verification token.', 'pnscode-social-login-and-register'));
        }
        
        // Check if token is expired (24 hours)
        $sent_time = get_user_meta($user_id, 'PNSCSOLO_verification_email_sent', true);
        if ($sent_time && (time() - $sent_time) > 86400) {
            wp_die(esc_html__('Verification link has expired. Please request a new one.', 'pnscode-social-login-and-register'));
        }
        
        // Verify email
        update_user_meta($user_id, 'PNSCSOLO_email_verified', true);
        delete_user_meta($user_id, 'PNSCSOLO_verification_token');
        
        // Log user in
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id, true);
        
        // Redirect
        $redirect = PNSCSOLO_Settings::get_registration_redirect();
        wp_safe_redirect(add_query_arg('verified', '1', $redirect));
        exit;
    }
    
    /**
     * Check if email is verified before login
     */
    public function check_verification($user, $username, $password) {
        if (is_wp_error($user)) {
            return $user;
        }
        
        if (!$user) {
            return $user;
        }
        
        $settings = PNSCSOLO_Settings::get_general_settings();
        
        // Check if email verification is enabled
        if (!isset($settings['email_verification_enabled']) || !$settings['email_verification_enabled']) {
            return $user;
        }
        
        // Check if email is verified
        $verified = get_user_meta($user->ID, 'PNSCSOLO_email_verified', true);
        
        if (!$verified) {
            return new WP_Error(
                'email_not_verified',
                __('Please verify your email address before logging in. Check your inbox for the verification link.', 'pnscode-social-login-and-register')
            );
        }
        
        return $user;
    }
    
    /**
     * Resend verification email
     */
    public static function resend_verification($user_id) {
        // Delete old token
        delete_user_meta($user_id, 'PNSCSOLO_verification_token');
        delete_user_meta($user_id, 'PNSCSOLO_verification_email_sent');
        
        // Send new verification email
        return self::send_verification_email($user_id);
    }
}
