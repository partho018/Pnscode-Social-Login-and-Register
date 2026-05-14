<?php
/**
 * Form Builder Handler
 */

if (!defined('ABSPATH')) {
    exit;
}

class PNSCSOLO_Form_Builder {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        add_action('wp_ajax_PNSCSOLO_save_form_fields', array($this, 'ajax_save_form_fields'));
        add_action('wp_ajax_PNSCSOLO_add_form_field', array($this, 'ajax_add_form_field'));
        add_action('wp_ajax_PNSCSOLO_delete_form_field', array($this, 'ajax_delete_form_field'));
    }
    
    /**
     * Get available field types
     */
    public static function get_field_types() {
        return array(
            'text' => __('Text', 'pnscode-social-login-and-register'),
            'email' => __('Email', 'pnscode-social-login-and-register'),
            'password' => __('Password', 'pnscode-social-login-and-register'),
            'tel' => __('Phone', 'pnscode-social-login-and-register'),
            'select' => __('Select/Dropdown', 'pnscode-social-login-and-register'),
            'checkbox' => __('Checkbox', 'pnscode-social-login-and-register'),
            'textarea' => __('Textarea', 'pnscode-social-login-and-register'),
            'number' => __('Number', 'pnscode-social-login-and-register'),
            'url' => __('URL', 'pnscode-social-login-and-register'),
            'date' => __('Date', 'pnscode-social-login-and-register'),
        );
    }
    
    /**
     * Render registration form
     */
    public static function render_registration_form($args = array()) {
        $defaults = array(
            'show_title' => true,
            'title' => __('Register', 'pnscode-social-login-and-register'),
            'submit_text' => __('Register', 'pnscode-social-login-and-register'),
            'show_login_link' => true,
            'show_social' => true,
            'login_url' => '',
        );
        
        $args = wp_parse_args($args, $defaults);
        
        if (!PNSCSOLO_Settings::is_registration_enabled()) {
            return '<p>' . esc_html__('Registration is currently disabled.', 'pnscode-social-login-and-register') . '</p>';
        }
        
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
        
        $fields = PNSCSOLO_Settings::get_registration_fields();
        $enabled_fields = array_filter($fields, function($field) {
            return isset($field['enabled']) && $field['enabled'];
        });
        
        ob_start();
        ?>
        <div class="pnscsolo-registration-form-wrapper">
            <?php if ($args['show_title']): ?>
                <h2 class="pnscsolo-form-title"><?php echo esc_html($args['title']); ?></h2>
            <?php endif; ?>
            
            <form class="pnscsolo-registration-form" method="post" id="pnscsolo-registration-form">
                <?php wp_nonce_field('PNSCSOLO_registration', 'PNSCSOLO_registration_nonce'); ?>
                
                <div class="pnscsolo-form-messages"></div>
                
                <?php foreach ($enabled_fields as $field): ?>
                    <div class="pnscsolo-form-field pnscsolo-field-<?php echo esc_attr($field['type']); ?>">
                        <?php self::render_field($field); ?>
                    </div>
                <?php endforeach; ?>
                
                <div class="pnscsolo-form-field pnscsolo-submit-field">
                    <button type="submit" class="pnscsolo-submit-btn">
                        <span class="pnscsolo-btn-text"><?php echo esc_html($args['submit_text']); ?></span>
                        <span class="pnscsolo-btn-loader" style="display:none;">
                            <span class="pnscsolo-spinner"></span>
                        </span>
                    </button>
                </div>
            </form>
            
            <?php if ($args['show_social'] && PNSCSOLO_Settings::is_social_login_enabled()): ?>
                <div class="pnscsolo-login-divider">
                    <span><?php esc_html_e('OR', 'pnscode-social-login-and-register'); ?></span>
                </div>
                
                <?php echo wp_kses_post(PNSCSOLO_Social_Auth::render_social_buttons(array(
                    'show_title' => false,
                    'button_style' => 'default',
                ))); ?>
            <?php endif; ?>
            
            <?php if ($args['show_login_link']): ?>
                <div class="pnscsolo-login-link">
                    <p>
                        <?php esc_html_e('Already have an account?', 'pnscode-social-login-and-register'); ?>
                        <a href="<?php echo !empty($args['login_url']) ? esc_url($args['login_url']) : esc_url(wp_login_url()); ?>">
                            <?php esc_html_e('Login', 'pnscode-social-login-and-register'); ?>
                        </a>
                    </p>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    /**
     * Render individual field
     */
    private static function render_field($field) {
        $field_id = 'PNSCSOLO_field_' . sanitize_key($field['id']);
        $required = isset($field['required']) && $field['required'];
        $required_attr = $required ? 'required' : '';
        $required_mark = $required ? '<span class="pnscsolo-required">*</span>' : '';
        
        ?>
        <label for="<?php echo esc_attr($field_id); ?>" class="pnscsolo-field-label">
            <?php echo esc_html($field['label']); ?>
            <?php echo wp_kses_post($required_mark); ?>
        </label>
        
        <?php
        switch ($field['type']) {
            case 'textarea':
                ?>
                <textarea 
                    id="<?php echo esc_attr($field_id); ?>" 
                    name="<?php echo esc_attr($field['id']); ?>" 
                    class="pnscsolo-field-input pnscsolo-textarea"
                    placeholder="<?php echo esc_attr($field['placeholder'] ?? ''); ?>"
                    <?php echo esc_attr($required_attr); ?>
                ></textarea>
                <?php
                break;
                
            case 'select':
                ?>
                <select 
                    id="<?php echo esc_attr($field_id); ?>" 
                    name="<?php echo esc_attr($field['id']); ?>" 
                    class="pnscsolo-field-input pnscsolo-select"
                    <?php echo esc_attr($required_attr); ?>
                >
                    <option value=""><?php echo esc_html($field['placeholder'] ?? __('Select...', 'pnscode-social-login-and-register')); ?></option>
                    <?php if (isset($field['options']) && is_array($field['options'])): ?>
                        <?php foreach ($field['options'] as $value => $label): ?>
                            <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <?php
                break;
                
            case 'checkbox':
                ?>
                <label class="pnscsolo-checkbox-label">
                    <input 
                        type="checkbox" 
                        id="<?php echo esc_attr($field_id); ?>" 
                        name="<?php echo esc_attr($field['id']); ?>" 
                        class="pnscsolo-field-input pnscsolo-checkbox"
                        value="1"
                        <?php echo esc_attr($required_attr); ?>
                    />
                    <span><?php echo esc_html($field['placeholder'] ?? ''); ?></span>
                </label>
                <?php
                break;
                
            default:
                ?>
                <input 
                    type="<?php echo esc_attr($field['type']); ?>" 
                    id="<?php echo esc_attr($field_id); ?>" 
                    name="<?php echo esc_attr($field['id']); ?>" 
                    class="pnscsolo-field-input"
                    placeholder="<?php echo esc_attr($field['placeholder'] ?? ''); ?>"
                    <?php echo esc_attr($required_attr); ?>
                />
                <?php
                break;
        }
    }
    
    /**
     * AJAX: Save form fields
     */
    public function ajax_save_form_fields() {
        check_ajax_referer('PNSCSOLO_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'pnscode-social-login-and-register')));
        }
        
        $fields_raw = isset($_POST['fields']) ? json_decode(sanitize_textarea_field(wp_unslash($_POST['fields'])), true) : array();
        $fields = array();
        
        if (is_array($fields_raw)) {
            foreach ($fields_raw as $field) {
                $sanitized = array();
                $sanitized['id'] = sanitize_key($field['id'] ?? '');
                $sanitized['type'] = sanitize_text_field($field['type'] ?? '');
                $sanitized['label'] = sanitize_text_field($field['label'] ?? '');
                $sanitized['placeholder'] = sanitize_text_field($field['placeholder'] ?? '');
                $sanitized['required'] = isset($field['required']) ? (bool)$field['required'] : false;
                $sanitized['enabled'] = isset($field['enabled']) ? (bool)$field['enabled'] : false;
                $sanitized['order'] = intval($field['order'] ?? 0);
                
                if (isset($field['options']) && is_array($field['options'])) {
                    $sanitized['options'] = array_map('sanitize_text_field', $field['options']);
                }
                
                $fields[] = $sanitized;
            }
        }
        
        if (PNSCSOLO_Settings::update_registration_fields($fields)) {
            wp_send_json_success(array('message' => __('Fields saved successfully', 'pnscode-social-login-and-register')));
        } else {
            wp_send_json_error(array('message' => __('Failed to save fields', 'pnscode-social-login-and-register')));
        }
    }
    
    /**
     * AJAX: Add new field
     */
    public function ajax_add_form_field() {
        check_ajax_referer('PNSCSOLO_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'pnscode-social-login-and-register')));
        }
        
        $fields = PNSCSOLO_Settings::get_registration_fields();
        $max_order = 0;
        
        foreach ($fields as $field) {
            if (isset($field['order']) && $field['order'] > $max_order) {
                $max_order = $field['order'];
            }
        }
        
        $new_field = array(
            'id' => 'field_' . uniqid(),
            'type' => 'text',
            'label' => __('New Field', 'pnscode-social-login-and-register'),
            'placeholder' => '',
            'required' => false,
            'enabled' => true,
            'order' => $max_order + 1,
        );
        
        $fields[] = $new_field;
        
        if (PNSCSOLO_Settings::update_registration_fields($fields)) {
            wp_send_json_success(array(
                'message' => __('Field added successfully', 'pnscode-social-login-and-register'),
                'field' => $new_field
            ));
        } else {
            wp_send_json_error(array('message' => __('Failed to add field', 'pnscode-social-login-and-register')));
        }
    }
    
    /**
     * AJAX: Delete field
     */
    public function ajax_delete_form_field() {
        check_ajax_referer('PNSCSOLO_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => __('Permission denied', 'pnscode-social-login-and-register')));
        }
        
        $field_id = isset($_POST['field_id']) ? sanitize_text_field(wp_unslash($_POST['field_id'])) : '';
        
        if (empty($field_id)) {
            wp_send_json_error(array('message' => __('Invalid field ID', 'pnscode-social-login-and-register')));
        }
        
        $fields = PNSCSOLO_Settings::get_registration_fields();
        $fields = array_filter($fields, function($field) use ($field_id) {
            return $field['id'] !== $field_id;
        });
        
        if (PNSCSOLO_Settings::update_registration_fields(array_values($fields))) {
            wp_send_json_success(array('message' => __('Field deleted successfully', 'pnscode-social-login-and-register')));
        } else {
            wp_send_json_error(array('message' => __('Failed to delete field', 'pnscode-social-login-and-register')));
        }
    }
}
