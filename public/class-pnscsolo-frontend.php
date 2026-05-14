<?php
/**
 * Frontend Handler
 */

if (!defined('ABSPATH')) {
    exit;
}

class PNSCSOLO_Frontend {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Add social login buttons to default WordPress login form
        add_action('login_form', array($this, 'add_social_to_login_form'));
        add_action('register_form', array($this, 'add_social_to_register_form'));
    }
    
    /**
     * Add social login buttons to WordPress login form
     */
    public function add_social_to_login_form() {
        if (!PNSCSOLO_Settings::is_social_login_enabled()) {
            return;
        }
        
        echo '<div class="pnscsolo-login-divider"><span>' . esc_html__('OR', 'pnscode-social-login-and-register') . '</span></div>';
        echo wp_kses_post(PNSCSOLO_Social_Auth::render_social_buttons(array(
            'show_title' => false,
            'button_style' => 'default',
        )));
    }
    
    /**
     * Add social login buttons to WordPress registration form
     */
    public function add_social_to_register_form() {
        if (!PNSCSOLO_Settings::is_social_login_enabled()) {
            return;
        }
        
        echo '<div class="pnscsolo-login-divider"><span>' . esc_html__('OR', 'pnscode-social-login-and-register') . '</span></div>';
        echo wp_kses_post(PNSCSOLO_Social_Auth::render_social_buttons(array(
            'show_title' => false,
            'button_style' => 'default',
        )));
    }
}
