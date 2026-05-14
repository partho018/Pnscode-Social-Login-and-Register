<?php
/**
 * General Settings View
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!current_user_can('manage_options')) {
    wp_die(esc_html__('Access denied', 'pnscode-social-login-and-register'));
}

$pnscsolo_settings = PNSCSOLO_Settings::get_general_settings();
$pnscsolo_message  = isset($_GET['message']) && $_GET['message'] === 'saved';
?>

<div class="wrap pnscsolo-admin-wrap pnscsolo-general-page">
    <div class="pnscsolo-admin-header">
        <h1><?php esc_html_e('Pnscode General Settings', 'pnscode-social-login-and-register'); ?></h1>
        <p class="pnscsolo-admin-subtitle"><?php esc_html_e('Configure your global settings and redirection rules.', 'pnscode-social-login-and-register'); ?></p>
    </div>
    
    <?php if ($pnscsolo_message): ?>
        <div class="notice notice-success is-dismissible pnscsolo-notice">
            <p><?php esc_html_e('Settings saved successfully!', 'pnscode-social-login-and-register'); ?></p>
        </div>
    <?php endif; ?>
    
    <div class="pnscsolo-admin-grid">
        <div class="pnscsolo-admin-main-content">
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="pnscsolo-settings-form">
                <?php wp_nonce_field('PNSCSOLO_save_general_settings'); ?>
                <input type="hidden" name="action" value="PNSCSOLO_save_general_settings">
                
                <div class="pnscsolo-card">
                    <div class="pnscsolo-card-header">
                        <span class="dashicons dashicons-admin-settings"></span>
                        <h2><?php esc_html_e('Plugin Status', 'pnscode-social-login-and-register'); ?></h2>
                    </div>
                    
                    <div class="pnscsolo-card-body">
                        <div class="pnscsolo-setting-row">
                            <div class="pnscsolo-setting-info">
                                <label for="plugin_enabled"><?php esc_html_e('Enable Plugin', 'pnscode-social-login-and-register'); ?></label>
                                <p class="pnscsolo-description"><?php esc_html_e('Enable or disable the entire plugin functionality', 'pnscode-social-login-and-register'); ?></p>
                            </div>
                            <div class="pnscsolo-setting-control">
                                <label class="pnscsolo-switch">
                                    <input type="checkbox" id="plugin_enabled" name="plugin_enabled" value="1" <?php checked($pnscsolo_settings['plugin_enabled'] ?? false); ?>>
                                    <span class="pnscsolo-slider"></span>
                                </label>
                            </div>
                        </div>
                        
                        <div class="pnscsolo-setting-row">
                            <div class="pnscsolo-setting-info">
                                <label for="registration_enabled"><?php esc_html_e('Enable Registration', 'pnscode-social-login-and-register'); ?></label>
                                <p class="pnscsolo-description"><?php esc_html_e('Allow users to register using the custom registration form', 'pnscode-social-login-and-register'); ?></p>
                            </div>
                            <div class="pnscsolo-setting-control">
                                <label class="pnscsolo-switch">
                                    <input type="checkbox" id="registration_enabled" name="registration_enabled" value="1" <?php checked($pnscsolo_settings['registration_enabled'] ?? false); ?>>
                                    <span class="pnscsolo-slider"></span>
                                </label>
                            </div>
                        </div>
                        
                        <div class="pnscsolo-setting-row">
                            <div class="pnscsolo-setting-info">
                                <label for="social_login_enabled"><?php esc_html_e('Enable Social Login', 'pnscode-social-login-and-register'); ?></label>
                                <p class="pnscsolo-description"><?php esc_html_e('Allow users to login/register using social providers', 'pnscode-social-login-and-register'); ?></p>
                            </div>
                            <div class="pnscsolo-setting-control">
                                <label class="pnscsolo-switch">
                                    <input type="checkbox" id="social_login_enabled" name="social_login_enabled" value="1" <?php checked($pnscsolo_settings['social_login_enabled'] ?? false); ?>>
                                    <span class="pnscsolo-slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="pnscsolo-card">
                    <div class="pnscsolo-card-header">
                        <span class="dashicons dashicons-randomize"></span>
                        <h2><?php esc_html_e('Redirect Settings', 'pnscode-social-login-and-register'); ?></h2>
                    </div>
                    
                    <div class="pnscsolo-card-body">
                        <div class="pnscsolo-setting-row vertical">
                            <div class="pnscsolo-setting-info">
                                <label for="redirect_after_login"><?php esc_html_e('Redirect After Login', 'pnscode-social-login-and-register'); ?></label>
                                <p class="pnscsolo-description"><?php esc_html_e('URL to redirect users after successful login', 'pnscode-social-login-and-register'); ?></p>
                            </div>
                            <div class="pnscsolo-setting-control">
                                <input type="url" id="redirect_after_login" name="redirect_after_login" value="<?php echo esc_attr($pnscsolo_settings['redirect_after_login'] ?? home_url()); ?>" class="pnscsolo-input-full">
                            </div>
                        </div>
                        
                        <div class="pnscsolo-setting-row vertical">
                            <div class="pnscsolo-setting-info">
                                <label for="redirect_after_registration"><?php esc_html_e('Redirect After Registration', 'pnscode-social-login-and-register'); ?></label>
                                <p class="pnscsolo-description"><?php esc_html_e('URL to redirect users after successful registration', 'pnscode-social-login-and-register'); ?></p>
                            </div>
                            <div class="pnscsolo-setting-control">
                                <input type="url" id="redirect_after_registration" name="redirect_after_registration" value="<?php echo esc_attr($pnscsolo_settings['redirect_after_registration'] ?? home_url()); ?>" class="pnscsolo-input-full">
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="pnscsolo-card">
                    <div class="pnscsolo-card-header">
                        <span class="dashicons dashicons-admin-users"></span>
                        <h2><?php esc_html_e('User Settings', 'pnscode-social-login-and-register'); ?></h2>
                    </div>
                    
                    <div class="pnscsolo-card-body">
                        <div class="pnscsolo-setting-row">
                            <div class="pnscsolo-setting-info">
                                <label for="default_user_role"><?php esc_html_e('Default User Role', 'pnscode-social-login-and-register'); ?></label>
                                <p class="pnscsolo-description"><?php esc_html_e('Default role assigned to new users', 'pnscode-social-login-and-register'); ?></p>
                            </div>
                            <div class="pnscsolo-setting-control">
                                <select id="default_user_role" name="default_user_role" class="pnscsolo-select">
                                    <?php wp_dropdown_roles($pnscsolo_settings['default_user_role'] ?? 'subscriber'); ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="pnscsolo-form-footer">
                    <button type="submit" class="pnscsolo-btn pnscsolo-btn-primary pnscsolo-btn-large">
                        <span class="dashicons dashicons-saved"></span>
                        <?php esc_html_e('Save General Settings', 'pnscode-social-login-and-register'); ?>
                    </button>
                </div>
            </form>
        </div>
        
        <div class="pnscsolo-admin-sidebar">
            <div class="pnscsolo-card pnscsolo-sidebar-card">
                <div class="pnscsolo-card-header small">
                    <h3><?php esc_html_e('Quick Links', 'pnscode-social-login-and-register'); ?></h3>
                </div>
                <div class="pnscsolo-card-body">
                    <ul class="pnscsolo-sidebar-links">
                        <li><a href="<?php echo esc_url(admin_url('admin.php?page=pnscsolo-social-login-form-builder')); ?>"><span class="dashicons dashicons-layout"></span> <?php esc_html_e('Customize Form', 'pnscode-social-login-and-register'); ?></a></li>
                        <li><a href="<?php echo esc_url(admin_url('admin.php?page=pnscsolo-social-login-providers')); ?>"><span class="dashicons dashicons-networking"></span> <?php esc_html_e('Social Providers', 'pnscode-social-login-and-register'); ?></a></li>
                        <li><a href="<?php echo esc_url(admin_url('admin.php?page=pnscsolo-social-login-documentation')); ?>"><span class="dashicons dashicons-editor-help"></span> <?php esc_html_e('Documentation', 'pnscode-social-login-and-register'); ?></a></li>
                    </ul>
                </div>
            </div>
            
            <div class="pnscsolo-card pnscsolo-sidebar-card pnscsolo-card-indigo">
                <div class="pnscsolo-card-header small">
                    <h3><?php esc_html_e('Shortcodes', 'pnscode-social-login-and-register'); ?></h3>
                </div>
                <div class="pnscsolo-card-body">
                    <div class="pnscsolo-shortcode-item">
                        <p><?php esc_html_e('Registration Form:', 'pnscode-social-login-and-register'); ?></p>
                        <code>[PNSCSOLO_registration_form]</code>
                    </div>
                    <div class="pnscsolo-shortcode-item">
                        <p><?php esc_html_e('Social Login Buttons:', 'pnscode-social-login-and-register'); ?></p>
                        <code>[PNSCSOLO_social_login]</code>
                    </div>
                    <div class="pnscsolo-shortcode-item">
                        <p><?php esc_html_e('Login Form:', 'pnscode-social-login-and-register'); ?></p>
                        <code>[PNSCSOLO_login_form]</code>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
