<?php
/**
 * Plugin Name: Pnscode Social Login and Register
 * Plugin URI: https://wordpress.org/plugins/pnscode-social-login-and-register/
 * Description: Professional social login (Google, Facebook, X, LinkedIn, GitHub) and custom registration form builder for WordPress. Includes brute force protection, email verification, and WooCommerce integration.
 * Version: 1.0.0
 * Author: Partho Samadder
 * Author URI:https://profiles.wordpress.org/partho800/
 * Text Domain: pnscode-social-login-and-register
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('PNSCSOLO_VERSION', '1.0.0');
define('PNSCSOLO_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('PNSCSOLO_PLUGIN_URL', plugin_dir_url(__FILE__));
define('PNSCSOLO_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main Plugin Class
 */
class PNSCSOLO_Main {
    
    /**
     * Single instance of the class
     */
    private static $instance = null;
    
    /**
     * Get instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
    }
    
    /**
     * Load required files
     */
    private function load_dependencies() {
        // Core classes
        require_once PNSCSOLO_PLUGIN_DIR . 'includes/class-pnscsolo-settings.php';
        require_once PNSCSOLO_PLUGIN_DIR . 'includes/class-pnscsolo-form-builder.php';
        require_once PNSCSOLO_PLUGIN_DIR . 'includes/class-pnscsolo-social-auth.php';
        require_once PNSCSOLO_PLUGIN_DIR . 'includes/class-pnscsolo-user-handler.php';
        require_once PNSCSOLO_PLUGIN_DIR . 'includes/class-pnscsolo-page-handler.php';
        
        // Security & Verification
        require_once PNSCSOLO_PLUGIN_DIR . 'includes/class-pnscsolo-email-verification.php';
        require_once PNSCSOLO_PLUGIN_DIR . 'includes/class-pnscsolo-recaptcha.php';
        require_once PNSCSOLO_PLUGIN_DIR . 'includes/class-pnscsolo-security.php';
        
        // Integrations
        require_once PNSCSOLO_PLUGIN_DIR . 'includes/class-pnscsolo-woocommerce.php';
        
        // Social providers
        require_once PNSCSOLO_PLUGIN_DIR . 'includes/providers/class-pnscsolo-google.php';
        require_once PNSCSOLO_PLUGIN_DIR . 'includes/providers/class-pnscsolo-facebook.php';
        require_once PNSCSOLO_PLUGIN_DIR . 'includes/providers/class-pnscsolo-twitter.php';
        require_once PNSCSOLO_PLUGIN_DIR . 'includes/providers/class-pnscsolo-linkedin.php';
        require_once PNSCSOLO_PLUGIN_DIR . 'includes/providers/class-pnscsolo-github.php';
        
        // Admin
        if (is_admin()) {
            require_once PNSCSOLO_PLUGIN_DIR . 'admin/class-pnscsolo-admin.php';
            require_once PNSCSOLO_PLUGIN_DIR . 'admin/class-pnscsolo-admin-menu.php';
        }
        
        // Frontend
        require_once PNSCSOLO_PLUGIN_DIR . 'public/class-pnscsolo-shortcodes.php';
        require_once PNSCSOLO_PLUGIN_DIR . 'public/class-pnscsolo-frontend.php';
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Activation/Deactivation
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        
        // Initialize components
        add_action('plugins_loaded', array($this, 'init'));

        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }
    
    /**
     * Initialize plugin components
     */
    public function init() {
        // Initialize settings
        PNSCSOLO_Settings::get_instance();
        
        // Initialize form builder
        PNSCSOLO_Form_Builder::get_instance();
        
        // Initialize social auth
        PNSCSOLO_Social_Auth::get_instance();
        
        // Initialize user handler
        PNSCSOLO_User_Handler::get_instance();
        
        // Initialize page handler (auto-replace login/register pages)
        PNSCSOLO_Page_Handler::get_instance();
        
        // Initialize integrations
        PNSCSOLO_WooCommerce_Integration::get_instance();
        
        // Initialize security & verification
        PNSCSOLO_Email_Verification::get_instance();
        PNSCSOLO_Recaptcha::get_instance();
        PNSCSOLO_Security::get_instance();
        
        // Initialize admin
        if (is_admin()) {
            PNSCSOLO_Admin::get_instance();
            PNSCSOLO_Admin_Menu::get_instance();
        }
        
        // Initialize frontend
        PNSCSOLO_Shortcodes::get_instance();
        PNSCSOLO_Frontend::get_instance();
    }
    
    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        wp_enqueue_style('pnscsolo-google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap', array(), null);
        wp_enqueue_style('pnscsolo-frontend', PNSCSOLO_PLUGIN_URL . 'assets/css/frontend.css', array('pnscsolo-google-fonts'), PNSCSOLO_VERSION);
        wp_enqueue_script('pnscsolo-frontend', PNSCSOLO_PLUGIN_URL . 'assets/js/frontend.js', array('jquery'), PNSCSOLO_VERSION, true);
        
        // Localize script
        wp_localize_script('pnscsolo-frontend', 'pnscsoloAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('PNSCSOLO_nonce'),
        ));
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        $version = PNSCSOLO_VERSION . '.' . time();
        
        // Enqueue Admin Styles
        wp_enqueue_style('pnscsolo-modern-admin', PNSCSOLO_PLUGIN_URL . 'assets/css/admin.css', array('dashicons'), $version);
        
        // Enqueue Admin Scripts
        wp_enqueue_script('pnscsolo-modern-admin-js', PNSCSOLO_PLUGIN_URL . 'assets/js/admin.js', array('jquery', 'jquery-ui-sortable'), $version, true);
        
        // Localize script
        wp_localize_script('pnscsolo-modern-admin-js', 'pnscsoloAdmin', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('PNSCSOLO_admin_nonce'),
        ));
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Create default options
        $default_settings = array(
            'plugin_enabled' => true,
            'registration_enabled' => true,
            'social_login_enabled' => true,
            'redirect_after_login' => home_url(),
            'redirect_after_registration' => home_url(),
            'default_user_role' => 'subscriber',
            'auto_create_pages' => true,
        );
        
        add_option('PNSCSOLO_general_settings', $default_settings);
        
        // Create default registration fields
        $default_fields = array(
            array(
                'id' => 'username',
                'type' => 'text',
                'label' => 'Username',
                'placeholder' => 'Enter your username',
                'required' => true,
                'enabled' => true,
                'order' => 1,
            ),
            array(
                'id' => 'email',
                'type' => 'email',
                'label' => 'Email',
                'placeholder' => 'Enter your email',
                'required' => true,
                'enabled' => true,
                'order' => 2,
            ),
            array(
                'id' => 'password',
                'type' => 'password',
                'label' => 'Password',
                'placeholder' => 'Enter your password',
                'required' => true,
                'enabled' => true,
                'order' => 3,
            ),
            array(
                'id' => 'confirm_password',
                'type' => 'password',
                'label' => 'Confirm Password',
                'placeholder' => 'Confirm your password',
                'required' => true,
                'enabled' => true,
                'order' => 4,
            ),
        );
        
        add_option('PNSCSOLO_registration_fields', $default_fields);
        
        // Create default login and register pages
        $this->create_default_pages();
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
    
    /**
     * Create default login and register pages
     */
    private function create_default_pages() {
        // Create Login Page
        $login_page_id = wp_insert_post(array(
            'post_title' => __('Login', 'pnscode-social-login-and-register'),
            'post_content' => '[PNSCSOLO_login_form]',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_author' => 1,
            'comment_status' => 'closed',
            'ping_status' => 'closed',
        ));
        
        if ($login_page_id && !is_wp_error($login_page_id)) {
            update_option('PNSCSOLO_login_page_id', $login_page_id);
        }
        
        // Create Register Page
        $register_page_id = wp_insert_post(array(
            'post_title' => __('Register', 'pnscode-social-login-and-register'),
            'post_content' => '[PNSCSOLO_registration_form]',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_author' => 1,
            'comment_status' => 'closed',
            'ping_status' => 'closed',
        ));
        
        if ($register_page_id && !is_wp_error($register_page_id)) {
            update_option('PNSCSOLO_register_page_id', $register_page_id);
        }
        
        // Mark pages as created
        update_option('PNSCSOLO_pages_created', true);
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }
}

/**
 * Main instance of the plugin
 */
function PNSCSOLO() {
    return PNSCSOLO_Main::get_instance();
}

// Global instance
$GLOBALS['pnscsolo'] = PNSCSOLO();
