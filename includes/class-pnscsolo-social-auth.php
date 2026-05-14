<?php
/**
 * Social Authentication Handler
 */

if (!defined('ABSPATH')) {
    exit;
}

class PNSCSOLO_Social_Auth {
    
    private static $instance = null;
    private $providers = array();
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->init_providers();
        $this->init_hooks();
    }
    
    /**
     * Initialize social providers
     */
    private function init_providers() {
        $this->providers = array(
            'google' => new PNSCSOLO_Provider_Google(),
            'facebook' => new PNSCSOLO_Provider_Facebook(),
            'twitter' => new PNSCSOLO_Provider_Twitter(),
            'linkedin' => new PNSCSOLO_Provider_LinkedIn(),
            'github' => new PNSCSOLO_Provider_GitHub(),
        );
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        add_action('init', array($this, 'handle_callback'));
    }
    
    /**
     * Get provider instance
     */
    public function get_provider($provider_name) {
        return isset($this->providers[$provider_name]) ? $this->providers[$provider_name] : null;
    }
    
    /**
     * Get all providers
     */
    public function get_all_providers() {
        return $this->providers;
    }
    
    /**
     * Get enabled providers
     */
    public function get_enabled_providers() {
        $enabled = array();
        
        foreach ($this->providers as $name => $provider) {
            if (PNSCSOLO_Settings::is_provider_enabled($name)) {
                $enabled[$name] = $provider;
            }
        }
        
        return $enabled;
    }
    
    /**
     * Handle OAuth callback
     */
    public function handle_callback() {
        if (!isset($_GET['PNSCSOLO_provider']) || !isset($_GET['PNSCSOLO_action'])) {
            return;
        }
        
        $provider_name = sanitize_text_field(wp_unslash($_GET['PNSCSOLO_provider']));
        $action = sanitize_text_field(wp_unslash($_GET['PNSCSOLO_action']));
        
        if ($action !== 'callback') {
            return;
        }
        
        $provider = $this->get_provider($provider_name);
        
        if (!$provider) {
            wp_die(esc_html__('Invalid social provider', 'pnscode-social-login-and-register'));
        }
        
        if (!PNSCSOLO_Settings::is_provider_enabled($provider_name)) {
            wp_die(esc_html__('This social login provider is disabled', 'pnscode-social-login-and-register'));
        }
        
        // Handle the callback
        $result = $provider->handle_callback();
        
        if (is_wp_error($result)) {
            wp_die(esc_html($result->get_error_message()));
        }
        
        // Get user data
        $user_data = $provider->get_user_data($result);
        
        if (is_wp_error($user_data)) {
            wp_die(esc_html($user_data->get_error_message()));
        }
        
        // Create or login user
        $user_id = PNSCSOLO_User_Handler::social_login($provider_name, $user_data);
        
        if (is_wp_error($user_id)) {
            wp_die(esc_html($user_id->get_error_message()));
        }
        
        // Redirect to appropriate page
        $redirect_url = PNSCSOLO_Settings::get_login_redirect();
        wp_safe_redirect($redirect_url);
        exit;
    }
    
    /**
     * Render social login buttons
     */
    public static function render_social_buttons($args = array()) {
        $defaults = array(
            'show_title' => true,
            'title' => __('Or login with', 'pnscode-social-login-and-register'),
            'button_style' => 'default', // default, icon-only, text-only
        );
        
        $args = wp_parse_args($args, $defaults);
        
        if (!PNSCSOLO_Settings::is_social_login_enabled()) {
            return '';
        }
        
        $auth = self::get_instance();
        $providers = $auth->get_enabled_providers();
        
        if (empty($providers)) {
            return '';
        }
        
        ob_start();
        ?>
        <div class="pnscsolo-social-login-wrapper">
            <?php if ($args['show_title']): ?>
                <div class="pnscsolo-social-title">
                    <span><?php echo esc_html($args['title']); ?></span>
                </div>
            <?php endif; ?>
            
            <div class="pnscsolo-social-buttons pnscsolo-button-style-<?php echo esc_attr($args['button_style']); ?>">
                <?php foreach ($providers as $name => $provider): ?>
                    <?php
                    $p_settings = PNSCSOLO_Settings::get_provider_settings($name);
                    $login_url = $provider->get_login_url();
                    $button_text = !empty($p_settings['button_text']) ? $p_settings['button_text'] : $provider->get_button_text();
                    $icon = $provider->get_icon();
                    ?>
                    <a href="<?php echo esc_url($login_url); ?>" 
                       class="pnscsolo-social-btn pnscsolo-social-btn-<?php echo esc_attr($name); ?>"
                       data-provider="<?php echo esc_attr($name); ?>">
                        <span class="pnscsolo-btn-icon">
                            <?php 
                            echo wp_kses($icon, array(
                                'svg'  => array(
                                    'class'           => true,
                                    'aria-hidden'     => true,
                                    'aria-labelledby' => true,
                                    'role'            => true,
                                    'viewbox'         => true,
                                    'xmlns'           => true,
                                    'width'           => true,
                                    'height'          => true,
                                ),
                                'path' => array(
                                    'd'    => true,
                                    'fill' => true,
                                ),
                                'circle' => array(
                                    'cx' => true,
                                    'cy' => true,
                                    'r'  => true,
                                    'fill' => true,
                                ),
                            )); 
                            ?>
                        </span>
                        <?php if ($args['button_style'] !== 'icon-only'): ?>
                            <span class="pnscsolo-btn-text"><?php echo esc_html($button_text); ?></span>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
