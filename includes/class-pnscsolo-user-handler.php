<?php
/**
 * User Handler - Manages user registration and login
 */

if (!defined('ABSPATH')) {
    exit;
}

class PNSCSOLO_User_Handler {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    
    private function __construct() {
        add_action('wp_ajax_nopriv_PNSCSOLO_register_user', array($this, 'ajax_register_user'));
        add_action('wp_ajax_PNSCSOLO_register_user', array($this, 'ajax_register_user'));
        add_action('wp_ajax_nopriv_PNSCSOLO_popup_login', array($this, 'ajax_popup_login'));
        add_action('wp_ajax_nopriv_PNSCSOLO_forgot_password', array($this, 'ajax_forgot_password'));
    }
    
    /**
     * Register new user
     */
    public static function register_user($data) {
        // Validate required fields
        if (empty($data['username'])) {
            return new WP_Error('empty_username', __('Username is required', 'pnscode-social-login-and-register'));
        }
        
        if (empty($data['email'])) {
            return new WP_Error('empty_email', __('Email is required', 'pnscode-social-login-and-register'));
        }
        
        if (empty($data['password'])) {
            return new WP_Error('empty_password', __('Password is required', 'pnscode-social-login-and-register'));
        }
        
        // Validate email
        if (!is_email($data['email'])) {
            return new WP_Error('invalid_email', __('Invalid email address', 'pnscode-social-login-and-register'));
        }
        
        // Check if username exists
        if (username_exists($data['username'])) {
            return new WP_Error('username_exists', __('Username already exists', 'pnscode-social-login-and-register'));
        }
        
        // Check if email exists
        if (email_exists($data['email'])) {
            return new WP_Error('email_exists', __('Email already exists', 'pnscode-social-login-and-register'));
        }
        
        // Validate password confirmation
        if (isset($data['confirm_password']) && $data['password'] !== $data['confirm_password']) {
            return new WP_Error('password_mismatch', __('Passwords do not match', 'pnscode-social-login-and-register'));
        }
        
        // Create user
        $user_id = wp_create_user(
            sanitize_user($data['username']),
            $data['password'],
            sanitize_email($data['email'])
        );
        
        if (is_wp_error($user_id)) {
            return $user_id;
        }
        
        // Set user role
        $user = new WP_User($user_id);
        $user->set_role(PNSCSOLO_Settings::get_default_user_role());
        
        // Save custom fields as user meta
        $fields = PNSCSOLO_Settings::get_registration_fields();
        $skip_fields = array('username', 'email', 'password', 'confirm_password');
        
        foreach ($fields as $field) {
            if (in_array($field['id'], $skip_fields)) {
                continue;
            }
            
            if (isset($data[$field['id']])) {
                update_user_meta($user_id, 'PNSCSOLO_' . $field['id'], sanitize_text_field($data[$field['id']]));
            }
        }
        
        // Send notification email (optional)
        try {
            wp_new_user_notification($user_id, null, 'user');
        } catch (Throwable $e) {
            // Log error but don't stop registration
        }
        
        return $user_id;
    }
    
    /**
     * Create or login user via social provider
     */
    public static function social_login($provider, $social_data) {
        if (empty($social_data['email'])) {
            return new WP_Error('no_email', __('Email not provided by social provider', 'pnscode-social-login-and-register'));
        }
        
        // Check if user exists
        $user = get_user_by('email', $social_data['email']);
        
        if ($user) {
            // User exists, log them in
            $user_id = $user->ID;
            
            // Update social meta
            update_user_meta($user_id, 'PNSCSOLO_social_provider', $provider);
            update_user_meta($user_id, 'PNSCSOLO_social_id', $social_data['id']);
        } else {
            // Create new user
            $username = self::generate_username($social_data['email'], $social_data['name'] ?? '');
            
            // Generate random password
            $password = wp_generate_password(12, true);
            
            $user_id = wp_create_user($username, $password, $social_data['email']);
            
            if (is_wp_error($user_id)) {
                return $user_id;
            }
            
            // Set user role
            $user = new WP_User($user_id);
            $user->set_role(PNSCSOLO_Settings::get_default_user_role());
            
            // Update user data
            if (!empty($social_data['name'])) {
                $name_parts = explode(' ', $social_data['name'], 2);
                wp_update_user(array(
                    'ID' => $user_id,
                    'first_name' => $name_parts[0],
                    'last_name' => $name_parts[1] ?? '',
                    'display_name' => $social_data['name'],
                ));
            }
            
            // Save social meta
            update_user_meta($user_id, 'PNSCSOLO_social_provider', $provider);
            update_user_meta($user_id, 'PNSCSOLO_social_id', $social_data['id']);
            
            if (!empty($social_data['picture'])) {
                update_user_meta($user_id, 'PNSCSOLO_social_picture', $social_data['picture']);
            }
            
            // Send welcome email
            wp_new_user_notification($user_id, null, 'user');
        }
        
        // Log user in
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id, true);
        
        return $user_id;
    }
    
    /**
     * Generate unique username
     */
    private static function generate_username($email, $name = '') {
        if (!empty($name)) {
            $username = sanitize_user(strtolower(str_replace(' ', '', $name)));
        } else {
            $username = sanitize_user(strtolower(explode('@', $email)[0]));
        }
        
        // Make sure username is unique
        $original_username = $username;
        $counter = 1;
        
        while (username_exists($username)) {
            $username = $original_username . $counter;
            $counter++;
        }
        
        return $username;
    }
    
    /**
     * AJAX: Register user
     */
    public function ajax_register_user() {
        if (!ob_get_level()) {
            ob_start();
        }
        try {
            if (!isset($_POST['PNSCSOLO_registration_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['PNSCSOLO_registration_nonce'])), 'PNSCSOLO_registration')) {
                if (ob_get_length()) ob_clean();
                wp_send_json_error(array('message' => __('Security check failed. Please refresh the page.', 'pnscode-social-login-and-register')));
            }
            
            if (!PNSCSOLO_Settings::is_registration_enabled()) {
                if (ob_get_length()) ob_clean();
                wp_send_json_error(array('message' => __('Registration is currently disabled', 'pnscode-social-login-and-register')));
            }
            
            $data = array();
            $fields = PNSCSOLO_Settings::get_registration_fields();
            foreach ($fields as $field) {
                if (isset($_POST[$field['id']])) {
                    $data[$field['id']] = sanitize_text_field(wp_unslash($_POST[$field['id']]));
                }
            }
            
            $result = self::register_user($data);
            
            if (is_wp_error($result)) {
                if (ob_get_length()) ob_clean();
                wp_send_json_error(array('message' => $result->get_error_message()));
            }
            
            $settings = PNSCSOLO_Settings::get_general_settings();
            if (isset($settings['email_verification_enabled']) && $settings['email_verification_enabled']) {
                try {
                    PNSCSOLO_Email_Verification::send_verification_email($result);
                    if (ob_get_length()) ob_clean();
                    wp_send_json_success(array(
                        'message' => __('Registration successful! Please check your email.', 'pnscode-social-login-and-register'),
                        'redirect' => ''
                    ));
                } catch (Throwable $e) {
                    // Fallback: If email fails but verification is on, we might want to auto-verify or show error
                    if (ob_get_length()) ob_clean();
                    wp_send_json_error(array('message' => __('Registration successful, but we couldn\'t send the verification email. Please contact admin.', 'pnscode-social-login-and-register')));
                }
            } else {
                wp_set_current_user($result);
                wp_set_auth_cookie($result, true);
                if (ob_get_length()) ob_clean();
                wp_send_json_success(array(
                    'message' => __('Registration successful!', 'pnscode-social-login-and-register'),
                    'redirect' => PNSCSOLO_Settings::get_registration_redirect()
                ));
            }
        } catch (Throwable $e) {
            if (ob_get_length()) ob_clean();
            wp_send_json_error(array('message' => 'System Error: ' . $e->getMessage()));
        }
        exit;
    }
    
    /**
     * AJAX: Popup login
     */
    public function ajax_popup_login() {
        if (!ob_get_level()) {
            ob_start();
        }
        try {
            if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'PNSCSOLO_nonce')) {
                if (ob_get_length()) ob_clean();
                wp_send_json_error(array('message' => __('Security check failed. Please refresh.', 'pnscode-social-login-and-register')));
            }
            
            $username = isset($_POST['log']) ? sanitize_user(wp_unslash($_POST['log'])) : '';
            $password = isset($_POST['pwd']) ? wp_unslash($_POST['pwd']) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
            $remember = isset($_POST['rememberme']) && $_POST['rememberme'] === 'forever';
            $redirect_to = isset($_POST['redirect_to']) ? esc_url_raw(wp_unslash($_POST['redirect_to'])) : '';
            
            if (empty($username) || empty($password)) {
                if (ob_get_length()) ob_clean();
                wp_send_json_error(array('message' => __('Please enter both username and password.', 'pnscode-social-login-and-register')));
            }
            
            $credentials = array('user_login' => $username, 'user_password' => $password, 'remember' => $remember);
            $user = wp_signon($credentials, is_ssl());
            
            if (is_wp_error($user)) {
                if (ob_get_length()) ob_clean();
                wp_send_json_error(array('message' => $user->get_error_message()));
            }
            
            $settings = PNSCSOLO_Settings::get_general_settings();
            if (isset($settings['email_verification_enabled']) && $settings['email_verification_enabled']) {
                $verified = get_user_meta($user->ID, 'PNSCSOLO_email_verified', true);
                if (!$verified) {
                    wp_logout();
                    if (ob_get_length()) ob_clean();
                    wp_send_json_error(array('message' => __('Please verify your email address.', 'pnscode-social-login-and-register')));
                }
            }
            
            if (ob_get_length()) ob_clean();
            wp_send_json_success(array(
                'message' => __('Login successful! Redirecting...', 'pnscode-social-login-and-register'),
                'redirect' => !empty($redirect_to) ? $redirect_to : home_url()
            ));
        } catch (Throwable $e) {
            if (ob_get_length()) ob_clean();
            wp_send_json_error(array('message' => 'Login Error: ' . $e->getMessage()));
        }
        exit;
    }

    /**
     * AJAX: Forgot password
     */
    public function ajax_forgot_password() {
        if (!ob_get_level()) {
            ob_start();
        }
        try {
            check_ajax_referer('PNSCSOLO_nonce', 'nonce');
            
            $user_login = isset($_POST['user_login']) ? sanitize_text_field(wp_unslash($_POST['user_login'])) : '';
            if (empty($user_login)) {
                if (ob_get_length()) ob_clean();
                wp_send_json_error(array('message' => __('Please enter a username or email.', 'pnscode-social-login-and-register')));
            }
            
            $user_data = get_user_by('login', $user_login);
            if (!$user_data && is_email($user_login)) {
                $user_data = get_user_by('email', $user_login);
            }
            
            if (!$user_data) {
                if (ob_get_length()) ob_clean();
                wp_send_json_error(array('message' => __('User not found.', 'pnscode-social-login-and-register')));
            }
            
            $errors = retrieve_password($user_data->user_login);
            
            if (ob_get_length()) ob_clean();
            if (is_wp_error($errors)) {
                wp_send_json_error(array('message' => $errors->get_error_message()));
            } else {
                wp_send_json_success(array('message' => __('Password reset link has been sent to your email.', 'pnscode-social-login-and-register')));
            }
        } catch (Throwable $e) {
            if (ob_get_length()) ob_clean();
            wp_send_json_error(array('message' => 'Reset Error: ' . $e->getMessage()));
        }
    }
}
