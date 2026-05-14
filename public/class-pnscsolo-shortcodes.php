<?php
/**
 * Shortcodes Handler
 */

if (!defined('ABSPATH')) {
    exit;
}

class PNSCSOLO_Shortcodes {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_shortcode('PNSCSOLO_registration_form', array($this, 'registration_form_shortcode'));
        add_shortcode('PNSCSOLO_social_login', array($this, 'social_login_shortcode'));
        add_shortcode('PNSCSOLO_login_form', array($this, 'login_form_shortcode'));
    }
    
    /**
     * Registration form shortcode
     * Usage: [PNSCSOLO_registration_form title="Register Now" submit_text="Sign Up" show_social="yes"]
     */
    public function registration_form_shortcode($atts) {
        $atts = shortcode_atts(array(
            'show_title' => 'yes',
            'title' => __('Register', 'pnscode-social-login-and-register'),
            'submit_text' => __('Register', 'pnscode-social-login-and-register'),
            'show_social' => 'yes',
            'show_login_link' => 'yes',
            'login_url' => '',
        ), $atts);
        
        $args = array(
            'show_title' => $atts['show_title'] === 'yes',
            'title' => $atts['title'],
            'submit_text' => $atts['submit_text'],
            'show_social' => $atts['show_social'] === 'yes',
            'show_login_link' => $atts['show_login_link'] === 'yes',
            'login_url' => $atts['login_url'],
        );
        
        return PNSCSOLO_Form_Builder::render_registration_form($args);
    }
    
    /**
     * Social login buttons shortcode
     * Usage: [PNSCSOLO_social_login title="Or login with" style="default"]
     */
    public function social_login_shortcode($atts) {
        $atts = shortcode_atts(array(
            'show_title' => 'yes',
            'title' => __('Or login with', 'pnscode-social-login-and-register'),
            'style' => 'default', // default, icon-only, text-only
        ), $atts);
        
        $args = array(
            'show_title' => $atts['show_title'] === 'yes',
            'title' => $atts['title'],
            'button_style' => $atts['style'],
        );
        
        return PNSCSOLO_Social_Auth::render_social_buttons($args);
    }
    
    /**
     * Login form shortcode (WordPress default + social)
     * Usage: [PNSCSOLO_login_form]
     */
    public function login_form_shortcode($atts) {
        $atts = shortcode_atts(array(
            'redirect' => '',
            'show_social' => 'yes',
            'show_register_link' => 'yes',
            'show_forgot_password' => 'yes',
            'register_url' => '',
            'show_title' => 'yes',
            'title' => __('Login', 'pnscode-social-login-and-register'),
        ), $atts);
        
        // Check if user is already logged in
        if (is_user_logged_in()) {
            return '<div class="pnscsolo-already-logged-in"><p>' . 
                   sprintf(
                       /* translators: %s: Dashboard URL */
                       __('You are already logged in. <a href="%s">Go to Dashboard</a>', 'pnscode-social-login-and-register'),
                       esc_url(admin_url())
                   ) . 
                   '</p></div>';
        }
        
        ob_start();
        ?>
        <div class="pnscsolo-login-form-wrapper">
            <?php if ($atts['show_title'] === 'yes'): ?>
                <h2 class="pnscsolo-form-title"><?php echo esc_html($atts['title']); ?></h2>
            <?php endif; ?>
            
            <form name="loginform" id="pnscsolo-custom-login-form" action="<?php echo esc_url(site_url('wp-login.php', 'login_post')); ?>" method="post">
                <?php wp_nonce_field('PNSCSOLO_nonce', 'pnscsolo_login_nonce'); ?>
                
                <div class="pnscsolo-form-messages"></div>

                <div class="pnscsolo-form-field">
                    <label for="user_login" class="pnscsolo-field-label"><?php esc_html_e('Username or Email', 'pnscode-social-login-and-register'); ?></label>
                    <input type="text" name="log" id="user_login" class="pnscsolo-field-input" value="" size="20" autocapitalize="off" required />
                </div>
                
                <div class="pnscsolo-form-field">
                    <label for="user_pass" class="pnscsolo-field-label"><?php esc_html_e('Password', 'pnscode-social-login-and-register'); ?></label>
                    <input type="password" name="pwd" id="user_pass" class="pnscsolo-field-input" value="" size="20" required />
                </div>
                
                <div class="pnscsolo-form-options">
                    <div class="pnscsolo-remember-field">
                        <label class="pnscsolo-checkbox-label">
                            <input name="rememberme" type="checkbox" id="rememberme" value="forever" class="pnscsolo-checkbox" />
                            <span><?php esc_html_e('Remember Me', 'pnscode-social-login-and-register'); ?></span>
                        </label>
                    </div>
                    
                    <?php if ($atts['show_forgot_password'] === 'yes'): ?>
                        <div class="pnscsolo-forgot-password">
                            <a href="#" id="pnscsolo-toggle-forgot">
                                <?php esc_html_e('Forgot Password?', 'pnscode-social-login-and-register'); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <div id="pnscsolo-forgot-form-container" style="display: none; margin-bottom: 20px;">
                    <div class="pnscsolo-forgot-password-inner">
                        <div class="pnscsolo-form-field">
                            <label for="PNSCSOLO_user_email" class="pnscsolo-field-label"><?php esc_html_e('Enter your email or username', 'pnscode-social-login-and-register'); ?></label>
                            <input type="text" id="PNSCSOLO_user_email" class="pnscsolo-field-input" placeholder="<?php esc_html_e('Email or Username', 'pnscode-social-login-and-register'); ?>" />
                        </div>
                        <button type="button" id="pnscsolo-reset-password-btn" class="pnscsolo-submit-btn pnscsolo-reset-btn">
                            <span class="pnscsolo-btn-text"><?php esc_html_e('Send Reset Link', 'pnscode-social-login-and-register'); ?></span>
                            <span class="pnscsolo-btn-loader" style="display:none;"><span class="pnscsolo-spinner"></span></span>
                        </button>
                        <div class="pnscsolo-forgot-messages" style="margin-top: 10px;"></div>
                    </div>
                </div>
                
                <input type="hidden" name="redirect_to" value="<?php echo !empty($atts['redirect']) ? esc_url($atts['redirect']) : esc_url(PNSCSOLO_Settings::get_login_redirect()); ?>" />
                
                <div class="pnscsolo-form-field pnscsolo-submit-field">
                    <button type="submit" name="wp-submit" id="pnscsolo-login-submit" class="pnscsolo-submit-btn">
                        <span class="pnscsolo-btn-text"><?php esc_html_e('LOG IN', 'pnscode-social-login-and-register'); ?></span>
                        <span class="pnscsolo-btn-loader" style="display:none;"><span class="pnscsolo-spinner"></span></span>
                    </button>
                </div>
            </form>
            
            <?php if ($atts['show_social'] === 'yes' && PNSCSOLO_Settings::is_social_login_enabled()): ?>
                <div class="pnscsolo-login-divider">
                    <span><?php esc_html_e('OR', 'pnscode-social-login-and-register'); ?></span>
                </div>
                
                <?php echo wp_kses_post(PNSCSOLO_Social_Auth::render_social_buttons(array(
                    'show_title' => false,
                    'button_style' => 'default',
                ))); ?>
            <?php endif; ?>
            
            <?php if ($atts['show_register_link'] === 'yes'): ?>
                <div class="pnscsolo-register-link">
                    <p>
                        <?php esc_html_e("Don't have an account?", 'pnscode-social-login-and-register'); ?>
                        <a href="<?php echo !empty($atts['register_url']) ? esc_url($atts['register_url']) : esc_url(wp_registration_url()); ?>">
                            <?php esc_html_e('Register Now', 'pnscode-social-login-and-register'); ?>
                        </a>
                    </p>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
