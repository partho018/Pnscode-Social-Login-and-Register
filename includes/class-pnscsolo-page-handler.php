<?php
/**
 * Login/Register Page Handler
 * Automatically replaces WordPress default login/register pages
 */

if (!defined('ABSPATH')) {
    exit;
}

class PNSCSOLO_Page_Handler {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Replace default login page
        add_action('login_init', array($this, 'replace_login_page'));
        
        // Redirect to custom pages if they exist
        add_filter('login_url', array($this, 'custom_login_url'), 10, 3);
        add_filter('register_url', array($this, 'custom_register_url'));
        add_filter('lostpassword_url', array($this, 'custom_lostpassword_url'), 10, 2);
        
        // Create default pages on activation
        add_action('admin_init', array($this, 'maybe_create_default_pages'));
    }
    
    /**
     * Replace default WordPress login page
     */
    public function replace_login_page() {
        global $pagenow;
        
        // Only replace if on wp-login.php
        if ($pagenow !== 'wp-login.php') {
            return;
        }
        
        // Don't replace for logout, lost password, etc.
        $action = isset($_REQUEST['action']) ? sanitize_text_field(wp_unslash($_REQUEST['action'])) : 'login'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        
        if (in_array($action, array('logout', 'lostpassword', 'resetpass', 'rp', 'postpass'))) {
            return;
        }
        
        // Check if custom pages exist
        $login_page_id = get_option('PNSCSOLO_login_page_id');
        $register_page_id = get_option('PNSCSOLO_register_page_id');
        
        // Redirect to custom pages if they exist
        if ($action === 'register' && $register_page_id) {
            wp_safe_redirect(get_permalink($register_page_id));
            exit;
        } elseif ($action === 'login' && $login_page_id) {
            wp_safe_redirect(get_permalink($login_page_id));
            exit;
        }
        
        // Otherwise, show custom form on wp-login.php
        $this->render_custom_login_page($action);
    }
    
    /**
     * Render custom login/register page
     */
    private function render_custom_login_page($action) {
        // Enqueue plugin styles
        wp_enqueue_style('pnscsolo-frontend', PNSCSOLO_PLUGIN_URL . 'assets/css/frontend.css', array(), PNSCSOLO_VERSION);
        wp_enqueue_script('pnscsolo-frontend', PNSCSOLO_PLUGIN_URL . 'assets/js/frontend.js', array('jquery'), PNSCSOLO_VERSION, true);
        
        wp_localize_script('pnscsolo-frontend', 'pnscsoloAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('PNSCSOLO_nonce'),
        ));
        
        // Start output buffering
        ob_start();
        ?>
        <!DOCTYPE html>
        <html <?php language_attributes(); ?>>
        <head>
            <meta charset="<?php bloginfo('charset'); ?>">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title><?php echo $action === 'register' ? esc_html__('Register', 'pnscode-social-login-and-register') : esc_html__('Login', 'pnscode-social-login-and-register'); ?> &lsaquo; <?php bloginfo('name'); ?></title>
            <?php wp_head(); ?>
        </head>
        <body class="login pnscsolo-login-body">
            <div class="pnscsolo-login-container">
                <div class="pnscsolo-site-branding">
                    <h1><a href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a></h1>
                </div>
                
                <?php
                if ($action === 'register') {
                    echo wp_kses_post(PNSCSOLO_Form_Builder::render_registration_form(array(
                        'show_title' => true,
                        'title' => esc_html__('Create Account', 'pnscode-social-login-and-register'),
                        'submit_text' => esc_html__('Register', 'pnscode-social-login-and-register'),
                        'show_social' => true,
                        'show_login_link' => true,
                        'login_url' => esc_url(wp_login_url()),
                    )));
                } else {
                    // Get redirect URL
                    $redirect_to = isset($_REQUEST['redirect_to']) ? esc_url_raw(wp_unslash($_REQUEST['redirect_to'])) : admin_url(); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                    
                    ?>
                    <div class="pnscsolo-login-form-wrapper">
                        <h2 class="pnscsolo-form-title"><?php esc_html_e('Login', 'pnscode-social-login-and-register'); ?></h2>
                        
                        <?php
                        // Show any login errors
                        if (isset($_GET['login']) && sanitize_text_field(wp_unslash($_GET['login'])) === 'failed') { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                            echo '<div class="pnscsolo-form-messages error">' . esc_html__('Invalid username or password.', 'pnscode-social-login-and-register') . '</div>';
                        }
                        ?>
                        
                        <form name="loginform" id="loginform" action="<?php echo esc_url(site_url('wp-login.php', 'login_post')); ?>" method="post">
                            <div class="pnscsolo-form-field">
                                <label for="user_login" class="pnscsolo-field-label">
                                    <?php esc_html_e('Username or Email', 'pnscode-social-login-and-register'); ?>
                                </label>
                                <input type="text" name="log" id="user_login" class="pnscsolo-field-input" value="" size="20" autocapitalize="off" required />
                            </div>
                            
                            <div class="pnscsolo-form-field">
                                <label for="user_pass" class="pnscsolo-field-label">
                                    <?php esc_html_e('Password', 'pnscode-social-login-and-register'); ?>
                                </label>
                                <input type="password" name="pwd" id="user_pass" class="pnscsolo-field-input" value="" size="20" required />
                            </div>
                            
                            <div class="pnscsolo-form-field">
                                <label class="pnscsolo-checkbox-label">
                                    <input name="rememberme" type="checkbox" id="rememberme" value="forever" class="pnscsolo-checkbox" />
                                    <span><?php esc_html_e('Remember Me', 'pnscode-social-login-and-register'); ?></span>
                                </label>
                            </div>
                            
                            <div class="pnscsolo-forgot-password">
                                <a href="<?php echo esc_url(wp_lostpassword_url()); ?>">
                                    <?php esc_html_e('Forgot Password?', 'pnscode-social-login-and-register'); ?>
                                </a>
                            </div>
                            
                            <input type="hidden" name="redirect_to" value="<?php echo esc_attr($redirect_to); ?>" />
                            
                            <div class="pnscsolo-form-field pnscsolo-submit-field">
                                <button type="submit" name="wp-submit" id="wp-submit" class="pnscsolo-submit-btn">
                                    <span class="pnscsolo-btn-text"><?php esc_html_e('Log In', 'pnscode-social-login-and-register'); ?></span>
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
                        
                        <?php if (get_option('users_can_register')): ?>
                            <div class="pnscsolo-register-link">
                                <p>
                                    <?php esc_html_e("Don't have an account?", 'pnscode-social-login-and-register'); ?>
                                    <a href="<?php echo esc_url(wp_registration_url()); ?>">
                                        <?php esc_html_e('Register Now', 'pnscode-social-login-and-register'); ?>
                                    </a>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php
                }
                ?>
                
                <div class="pnscsolo-back-link">
                    <a href="<?php echo esc_url(home_url('/')); ?>">&larr; <?php esc_html_e('Back to', 'pnscode-social-login-and-register'); ?> <?php bloginfo('name'); ?></a>
                </div>
            </div>
            
            <?php wp_footer(); ?>
        </body>
        </html>
        <?php
        $output = ob_get_clean();
        echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        exit;
    }
    
    /**
     * Custom login URL
     */
    public function custom_login_url($login_url, $redirect, $force_reauth) {
        $login_page_id = get_option('PNSCSOLO_login_page_id');
        
        if ($login_page_id && get_post_status($login_page_id) === 'publish') {
            $login_url = get_permalink($login_page_id);
            
            if (!empty($redirect)) {
                $login_url = add_query_arg('redirect_to', urlencode($redirect), $login_url);
            }
        }
        
        return $login_url;
    }
    
    /**
     * Custom register URL
     */
    public function custom_register_url($register_url) {
        $register_page_id = get_option('PNSCSOLO_register_page_id');
        
        if ($register_page_id && get_post_status($register_page_id) === 'publish') {
            $register_url = get_permalink($register_page_id);
        }
        
        return $register_url;
    }
    
    /**
     * Custom lost password URL
     */
    public function custom_lostpassword_url($lostpassword_url, $redirect) {
        // Keep WordPress default for now
        return $lostpassword_url;
    }
    
    /**
     * Create default login/register pages
     */
    public function maybe_create_default_pages() {
        // Only run once
        if (get_option('PNSCSOLO_pages_created')) {
            return;
        }
        
        $settings = PNSCSOLO_Settings::get_general_settings();
        
        // Check if auto-create is enabled
        if (!isset($settings['auto_create_pages']) || !$settings['auto_create_pages']) {
            return;
        }
        
        // Create Login Page
        $login_page_id = get_option('PNSCSOLO_login_page_id');
        if (!$login_page_id || get_post_status($login_page_id) !== 'publish') {
            $login_page_id = wp_insert_post(array(
                'post_title' => __('Login', 'pnscode-social-login-and-register'),
                'post_content' => '[PNSCSOLO_login_form]',
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_author' => 1,
                'comment_status' => 'closed',
                'ping_status' => 'closed',
            ));
            
            if ($login_page_id) {
                update_option('PNSCSOLO_login_page_id', $login_page_id);
            }
        }
        
        // Create Register Page
        $register_page_id = get_option('PNSCSOLO_register_page_id');
        if (!$register_page_id || get_post_status($register_page_id) !== 'publish') {
            $register_page_id = wp_insert_post(array(
                'post_title' => __('Register', 'pnscode-social-login-and-register'),
                'post_content' => '[PNSCSOLO_registration_form]',
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_author' => 1,
                'comment_status' => 'closed',
                'ping_status' => 'closed',
            ));
            
            if ($register_page_id) {
                update_option('PNSCSOLO_register_page_id', $register_page_id);
            }
        }
        
        // Mark as created
        update_option('PNSCSOLO_pages_created', true);
    }
    
    /**
     * Get login page URL
     */
    public static function get_login_page_url() {
        $login_page_id = get_option('PNSCSOLO_login_page_id');
        
        if ($login_page_id && get_post_status($login_page_id) === 'publish') {
            return get_permalink($login_page_id);
        }
        
        return wp_login_url();
    }
    
    /**
     * Get register page URL
     */
    public static function get_register_page_url() {
        $register_page_id = get_option('PNSCSOLO_register_page_id');
        
        if ($register_page_id && get_post_status($register_page_id) === 'publish') {
            return get_permalink($register_page_id);
        }
        
        return wp_registration_url();
    }
}
