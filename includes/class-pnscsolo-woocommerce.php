<?php
/**
 * WooCommerce Integration
 * Adds popup login/register modal for WooCommerce
 */

if (!defined('ABSPATH')) {
    exit;
}

class PNSCSOLO_WooCommerce_Integration {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Check if WooCommerce is active
        if (!class_exists('WooCommerce')) {
            return;
        }
        
        // Add popup modal to footer
        add_action('wp_footer', array($this, 'add_login_popup'));
        
        // Enqueue popup scripts
        add_action('wp_enqueue_scripts', array($this, 'enqueue_popup_scripts'));
        
        // Intercept WooCommerce login redirect
        add_filter('woocommerce_login_redirect', array($this, 'prevent_redirect'), 10, 2);

        // Ensure we handle custom slugs
        add_action('wp', array($this, 'handle_account_page_detection'));
    }

    /**
     * Better detection for WooCommerce account page
     */
    public function handle_account_page_detection() {
        if (is_account_page() && !is_user_logged_in()) {
            // Add a class to body for easier JS targeting
            add_filter('body_class', function($classes) {
                $classes[] = 'pnscsolo-force-popup';
                return $classes;
            });
        }
    }
    
    /**
     * Enqueue popup scripts and styles
     */
    public function enqueue_popup_scripts() {
        wp_enqueue_style('pnscsolo-popup', PNSCSOLO_PLUGIN_URL . 'assets/css/popup.css', array(), PNSCSOLO_VERSION);
        wp_enqueue_script('pnscsolo-popup', PNSCSOLO_PLUGIN_URL . 'assets/js/popup.js', array('jquery'), PNSCSOLO_VERSION, true);
        
        wp_localize_script('pnscsolo-popup', 'pnscsoloPopup', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('PNSCSOLO_nonce'),
            'loginUrl' => wp_login_url(),
            'registerUrl' => wp_registration_url(),
        ));
    }
    
    /**
     * Add login popup modal to footer
     */
    public function add_login_popup() {
        // Only show for non-logged-in users
        if (is_user_logged_in()) {
            return;
        }
        ?>
        <div id="pnscsolo-login-popup" class="pnscsolo-popup-overlay" style="display: none;">
            <div class="pnscsolo-popup-container">
                <button class="pnscsolo-popup-close">&times;</button>
                
                <div class="pnscsolo-popup-tabs">
                    <button class="pnscsolo-popup-tab active" data-tab="login">
                        <?php esc_html_e('Login', 'pnscode-social-login-and-register'); ?>
                    </button>
                    <button class="pnscsolo-popup-tab" data-tab="register">
                        <?php esc_html_e('Register', 'pnscode-social-login-and-register'); ?>
                    </button>
                </div>
                
                <div class="pnscsolo-popup-content">
                    <!-- Login Tab -->
                    <div id="pnscsolo-popup-login" class="pnscsolo-popup-tab-content active">
                        <?php
                        // Get redirect URL
                        $redirect_to = is_account_page() ? wc_get_page_permalink('myaccount') : '';
                        ?>
                        <div class="pnscsolo-login-form-wrapper">
                            <form name="loginform" id="pnscsolo-popup-loginform" method="post">
                                <div class="pnscsolo-form-messages"></div>
                                
                                <div class="pnscsolo-form-field">
                                    <label for="PNSCSOLO_popup_user_login" class="pnscsolo-field-label">
                                        <?php esc_html_e('Username or Email', 'pnscode-social-login-and-register'); ?>
                                    </label>
                                    <input type="text" name="log" id="PNSCSOLO_popup_user_login" class="pnscsolo-field-input" required />
                                </div>
                                
                                <div class="pnscsolo-form-field">
                                    <label for="PNSCSOLO_popup_user_pass" class="pnscsolo-field-label">
                                        <?php esc_html_e('Password', 'pnscode-social-login-and-register'); ?>
                                    </label>
                                    <input type="password" name="pwd" id="PNSCSOLO_popup_user_pass" class="pnscsolo-field-input" required />
                                </div>
                                
                                <div class="pnscsolo-popup-options">
                                    <label class="pnscsolo-checkbox-label">
                                        <input name="rememberme" type="checkbox" value="forever">
                                        <span><?php esc_html_e('Remember Me', 'pnscode-social-login-and-register'); ?></span>
                                    </label>
                                    <a href="#" class="pnscsolo-toggle-forgot"><?php esc_html_e('Forgot Password?', 'pnscode-social-login-and-register'); ?></a>
                                </div>

                                <div class="pnscsolo-forgot-form-container" style="display: none; margin-bottom: 20px;">
                                    <div class="pnscsolo-forgot-password-inner">
                                        <div class="pnscsolo-form-field">
                                            <label class="pnscsolo-field-label"><?php esc_html_e('Enter your email or username', 'pnscode-social-login-and-register'); ?></label>
                                            <input type="text" class="pnscsolo-forgot-email pnscsolo-field-input" placeholder="<?php esc_html_e('Email or Username', 'pnscode-social-login-and-register'); ?>" />
                                        </div>
                                        <button type="button" class="pnscsolo-submit-btn pnscsolo-reset-password-popup-btn">
                                            <span class="pnscsolo-btn-text"><?php esc_html_e('Send Reset Link', 'pnscode-social-login-and-register'); ?></span>
                                            <span class="pnscsolo-btn-loader" style="display:none;"><span class="pnscsolo-spinner"></span></span>
                                        </button>
                                        <div class="pnscsolo-forgot-messages" style="margin-top: 10px;"></div>
                                    </div>
                                </div>
                                
                                <input type="hidden" name="redirect_to" value="<?php echo esc_attr($redirect_to); ?>" />
                                
                                <div class="pnscsolo-form-field pnscsolo-submit-field">
                                    <button type="submit" class="pnscsolo-submit-btn">
                                        <span class="pnscsolo-btn-text"><?php esc_html_e('Log In', 'pnscode-social-login-and-register'); ?></span>
                                        <span class="pnscsolo-btn-loader" style="display:none;">
                                            <span class="pnscsolo-spinner"></span>
                                        </span>
                                    </button>
                                </div>
                            </form>
                            
                            <?php if (PNSCSOLO_Settings::is_social_login_enabled()): ?>
                                <div class="pnscsolo-login-divider">
                                    <span><?php esc_html_e('OR', 'pnscode-social-login-and-register'); ?></span>
                                </div>
                                
                                <?php echo wp_kses_post(PNSCSOLO_Social_Auth::render_social_buttons(array(
                                    'show_title' => false,
                                    'button_style' => 'default',
                                ))); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Register Tab -->
                    <div id="pnscsolo-popup-register" class="pnscsolo-popup-tab-content">
                        <?php
                        echo wp_kses_post(PNSCSOLO_Form_Builder::render_registration_form(array(
                            'show_title' => false,
                            'submit_text' => esc_html__('Register', 'pnscode-social-login-and-register'),
                            'show_social' => true,
                            'show_login_link' => false,
                        )));
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Prevent WooCommerce redirect for AJAX login
     */
    public function prevent_redirect($redirect, $user) {
        if (wp_doing_ajax()) {
            return false;
        }
        return $redirect;
    }
}
