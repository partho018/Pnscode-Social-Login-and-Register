<?php
/**
 * Admin Menu Handler
 */

if (!defined('ABSPATH')) {
    exit;
}

class PNSCSOLO_Admin_Menu {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('Social Login and Register', 'pnscode-social-login-and-register'),
            __('Social Login', 'pnscode-social-login-and-register'),
            'manage_options',
            'pnscsolo-social-login',
            array($this, 'render_main_page'),
            'dashicons-admin-users',
            30
        );
        
        add_submenu_page(
            'pnscsolo-social-login',
            __('General Settings', 'pnscode-social-login-and-register'),
            __('General Settings', 'pnscode-social-login-and-register'),
            'manage_options',
            'pnscsolo-social-login',
            array($this, 'render_main_page')
        );
        
        add_submenu_page(
            'pnscsolo-social-login',
            __('Form Builder', 'pnscode-social-login-and-register'),
            __('Form Builder', 'pnscode-social-login-and-register'),
            'manage_options',
            'pnscsolo-social-login-form-builder',
            array($this, 'render_form_builder_page')
        );
        
        add_submenu_page(
            'pnscsolo-social-login',
            __('Social Providers', 'pnscode-social-login-and-register'),
            __('Social Providers', 'pnscode-social-login-and-register'),
            'manage_options',
            'pnscsolo-social-login-providers',
            array($this, 'render_providers_page')
        );
        
        add_submenu_page(
            'pnscsolo-social-login',
            __('Documentation', 'pnscode-social-login-and-register'),
            __('Documentation', 'pnscode-social-login-and-register'),
            'manage_options',
            'pnscsolo-social-login-documentation',
            array($this, 'render_documentation_page')
        );
    }
    
    /**
     * Render main settings page
     */
    public function render_main_page() {
        require_once PNSCSOLO_PLUGIN_DIR . 'admin/views/general-settings.php';
    }
    
    /**
     * Render form builder page
     */
    public function render_form_builder_page() {
        require_once PNSCSOLO_PLUGIN_DIR . 'admin/views/form-builder.php';
    }
    
    /**
     * Render providers page
     */
    public function render_providers_page() {
        require_once PNSCSOLO_PLUGIN_DIR . 'admin/views/providers.php';
    }
    
    /**
     * Render documentation page
     */
    public function render_documentation_page() {
        require_once PNSCSOLO_PLUGIN_DIR . 'admin/views/documentation.php';
    }
}
