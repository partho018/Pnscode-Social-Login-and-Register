<?php
/**
 * Social Providers Settings View
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!current_user_can('manage_options')) {
    wp_die(esc_html__('Access denied', 'pnscode-social-login-and-register'));
}

$pnscsolo_auth              = PNSCSOLO_Social_Auth::get_instance();
$pnscsolo_providers         = $pnscsolo_auth->get_all_providers();
$pnscsolo_allowed_providers = array_keys($pnscsolo_providers);
$pnscsolo_active_provider    = isset($_GET['provider']) ? sanitize_text_field(wp_unslash($_GET['provider'])) : 'google'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

if (!in_array($pnscsolo_active_provider, $pnscsolo_allowed_providers, true)) {
    $pnscsolo_active_provider = 'google';
}

$pnscsolo_message = isset($_GET['message']) && 'saved' === $_GET['message']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
?>

<div class="wrap pnscsolo-admin-wrap pnscsolo-providers-page">
    <div class="pnscsolo-admin-header">
        <h1><?php esc_html_e('Pnscode Providers Configuration', 'pnscode-social-login-and-register'); ?></h1>
        <p class="pnscsolo-admin-subtitle"><?php esc_html_e('Manage your social authentication platforms and API credentials.', 'pnscode-social-login-and-register'); ?></p>
    </div>
    
    <?php if ($pnscsolo_message): ?>
        <div class="notice notice-success is-dismissible pnscsolo-notice">
            <p><?php esc_html_e('Provider settings saved successfully!', 'pnscode-social-login-and-register'); ?></p>
        </div>
    <?php endif; ?>
    
    <div class="pnscsolo-admin-grid">
        <div class="pnscsolo-admin-main-content">
            <div class="pnscsolo-providers-main">
                <div class="pnscsolo-providers-tabs">
                    <?php foreach ($pnscsolo_providers as $pnscsolo_name => $pnscsolo_provider): ?>
                        <a href="<?php echo esc_url(admin_url('admin.php?page=pnscsolo-social-login-providers&provider=' . $pnscsolo_name)); ?>" 
                           class="pnscsolo-provider-tab <?php echo $pnscsolo_active_provider === $pnscsolo_name ? 'active' : ''; ?>">
                            <span class="pnscsolo-provider-icon"><?php echo wp_kses_post($pnscsolo_provider->get_icon()); ?></span>
                            <span class="pnscsolo-provider-name"><?php echo esc_html($pnscsolo_provider->get_display_name()); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
                
                <div class="pnscsolo-provider-settings">
                    <?php
                    $pnscsolo_provider     = $pnscsolo_providers[$pnscsolo_active_provider];
                    $pnscsolo_settings     = PNSCSOLO_Settings::get_provider_settings($pnscsolo_active_provider);
                    $pnscsolo_callback_url = $pnscsolo_provider->get_callback_url();
                    ?>
                    
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="pnscsolo-settings-form">
                        <?php wp_nonce_field('PNSCSOLO_save_provider_settings'); ?>
                        <input type="hidden" name="action" value="PNSCSOLO_save_provider_settings">
                        <input type="hidden" name="provider" value="<?php echo esc_attr($pnscsolo_active_provider); ?>">
                        
                        <div class="pnscsolo-card">
                            <div class="pnscsolo-card-header">
                                <span class="pnscsolo-provider-icon-large"><?php echo wp_kses_post($pnscsolo_provider->get_icon()); ?></span>
                                <h2>
                                    <?php
                                    /* translators: %s: Provider display name */
                                    echo esc_html(sprintf(__('%s Authentication Settings', 'pnscode-social-login-and-register'), $pnscsolo_provider->get_display_name()));
                                    ?>
                                </h2>
                            </div>
                            
                            <div class="pnscsolo-card-body">
                                <div class="pnscsolo-setting-row">
                                    <div class="pnscsolo-setting-info">
                                        <label for="enabled"><?php esc_html_e('Enable Login', 'pnscode-social-login-and-register'); ?></label>
                                        <p class="pnscsolo-description">
                                            <?php
                                            /* translators: %s: Provider display name */
                                            echo esc_html(sprintf(__('Allow users to login with %s', 'pnscode-social-login-and-register'), $pnscsolo_provider->get_display_name()));
                                            ?>
                                        </p>
                                    </div>
                                    <div class="pnscsolo-setting-control">
                                        <label class="pnscsolo-switch">
                                            <input type="checkbox" id="enabled" name="enabled" value="1" <?php checked($pnscsolo_settings['enabled'] ?? false); ?>>
                                            <span class="pnscsolo-slider"></span>
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="pnscsolo-setting-row vertical">
                                    <div class="pnscsolo-setting-info">
                                        <label for="client_id"><?php esc_html_e('Application Client ID', 'pnscode-social-login-and-register'); ?></label>
                                        <p class="pnscsolo-description">
                                            <?php
                                            /* translators: %s: Provider display name */
                                            echo esc_html(sprintf(__('Obtained from your %s developer console', 'pnscode-social-login-and-register'), $pnscsolo_provider->get_display_name()));
                                            ?>
                                        </p>
                                    </div>
                                    <div class="pnscsolo-setting-control">
                                        <input type="text" id="client_id" name="client_id" value="<?php echo esc_attr($pnscsolo_settings['client_id'] ?? ''); ?>" class="pnscsolo-input-full" placeholder="<?php esc_html_e('Enter Client ID...', 'pnscode-social-login-and-register'); ?>">
                                    </div>
                                </div>
                                
                                <div class="pnscsolo-setting-row vertical">
                                    <div class="pnscsolo-setting-info">
                                        <label for="client_secret"><?php esc_html_e('Application Client Secret', 'pnscode-social-login-and-register'); ?></label>
                                        <p class="pnscsolo-description">
                                            <?php
                                            /* translators: %s: Provider display name */
                                            echo esc_html(sprintf(__('Your %s app secret key', 'pnscode-social-login-and-register'), $pnscsolo_provider->get_display_name()));
                                            ?>
                                        </p>
                                    </div>
                                    <div class="pnscsolo-setting-control">
                                        <div class="pnscsolo-password-wrapper">
                                            <input type="password" id="client_secret" name="client_secret" value="<?php echo esc_attr($pnscsolo_settings['client_secret'] ?? ''); ?>" class="pnscsolo-input-full" placeholder="<?php esc_html_e('Enter Client Secret...', 'pnscode-social-login-and-register'); ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="pnscsolo-setting-row vertical">
                                    <div class="pnscsolo-setting-info">
                                        <label><?php esc_html_e('Authorized Redirect URI (Callback URL)', 'pnscode-social-login-and-register'); ?></label>
                                        <p class="pnscsolo-description"><?php esc_html_e('Copy this URL to your provider\'s application settings', 'pnscode-social-login-and-register'); ?></p>
                                    </div>
                                    <div class="pnscsolo-setting-control">
                                        <div class="pnscsolo-callback-box">
                                            <code id="pnscsolo-callback-url"><?php echo esc_html($pnscsolo_callback_url); ?></code>
                                            <button type="button" class="pnscsolo-btn pnscsolo-btn-secondary pnscsolo-copy-btn" data-clipboard-text="<?php echo esc_attr($pnscsolo_callback_url); ?>">
                                                <span class="dashicons dashicons-clipboard"></span>
                                                <?php esc_html_e('Copy URL', 'pnscode-social-login-and-register'); ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="pnscsolo-setting-row">
                                    <div class="pnscsolo-setting-info">
                                        <label for="button_text"><?php esc_html_e('Custom Button Label', 'pnscode-social-login-and-register'); ?></label>
                                        <p class="pnscsolo-description"><?php esc_html_e('Override the default label for the social button', 'pnscode-social-login-and-register'); ?></p>
                                    </div>
                                    <div class="pnscsolo-setting-control">
                                        <div style="display: flex; align-items: center; gap: 20px; width: 100%;">
                                            <input type="text" id="button_text" name="button_text" value="<?php echo esc_attr($pnscsolo_settings['button_text'] ?? ''); ?>" class="pnscsolo-input-full" style="max-width: 300px;" placeholder="<?php echo esc_attr($pnscsolo_provider->get_button_text()); ?>">
                                            <div class="pnscsolo-btn-preview-mini">
                                                <div class="pnscsolo-btn pnscsolo-btn-secondary" style="margin:0; pointer-events:none;">
                                                    <span class="pnscsolo-btn-icon" style="margin-right:8px; display:flex; align-items:center;"><?php echo wp_kses_post($pnscsolo_provider->get_icon()); ?></span>
                                                    <span class="pnscsolo-preview-text"><?php echo esc_html(!empty($pnscsolo_settings['button_text']) ? $pnscsolo_settings['button_text'] : $pnscsolo_provider->get_button_text()); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="pnscsolo-form-footer">
                            <button type="submit" class="pnscsolo-btn pnscsolo-btn-primary pnscsolo-btn-large">
                                <span class="dashicons dashicons-saved"></span>
                                <?php esc_html_e('Update Configuration', 'pnscode-social-login-and-register'); ?>
                            </button>
                            
                            <a href="<?php echo esc_url(admin_url('admin.php?page=pnscsolo-social-login-documentation&provider=' . $pnscsolo_active_provider)); ?>" class="pnscsolo-btn pnscsolo-btn-secondary pnscsolo-btn-large">
                                <span class="dashicons dashicons-book"></span>
                                <?php esc_html_e('Setup Instructions', 'pnscode-social-login-and-register'); ?>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="pnscsolo-admin-sidebar">
            <div class="pnscsolo-card pnscsolo-sidebar-card">
                <div class="pnscsolo-card-header small">
                    <span class="dashicons dashicons-chart-pie"></span>
                    <h3><?php esc_html_e('Provider Insights', 'pnscode-social-login-and-register'); ?></h3>
                </div>
                <div class="pnscsolo-card-body">
                    <div class="pnscsolo-status-list">
                        <div class="pnscsolo-status-item">
                            <span class="pnscsolo-status-label"><?php esc_html_e('Login Status:', 'pnscode-social-login-and-register'); ?></span>
                            <span class="pnscsolo-status-badge <?php echo ($pnscsolo_settings['enabled'] ?? false) ? 'active' : 'inactive'; ?>">
                                <?php echo ($pnscsolo_settings['enabled'] ?? false) ? esc_html__('Enabled', 'pnscode-social-login-and-register') : esc_html__('Disabled', 'pnscode-social-login-and-register'); ?>
                            </span>
                        </div>
                        <div class="pnscsolo-status-item">
                            <span class="pnscsolo-status-label"><?php esc_html_e('Auth Setup:', 'pnscode-social-login-and-register'); ?></span>
                            <span class="pnscsolo-status-badge <?php echo (!empty($pnscsolo_settings['client_id']) && !empty($pnscsolo_settings['client_secret'])) ? 'active' : 'inactive'; ?>">
                                <?php echo (!empty($pnscsolo_settings['client_id']) && !empty($pnscsolo_settings['client_secret'])) ? esc_html__('Configured', 'pnscode-social-login-and-register') : esc_html__('Needed', 'pnscode-social-login-and-register'); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="pnscsolo-card pnscsolo-sidebar-card">
                <div class="pnscsolo-card-header small">
                    <span class="dashicons dashicons-awards"></span>
                    <h3><?php esc_html_e('Quick Checklist', 'pnscode-social-login-and-register'); ?></h3>
                </div>
                <div class="pnscsolo-card-body">
                    <ul class="pnscsolo-sidebar-links checklist">
                        <li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Register Developer App', 'pnscode-social-login-and-register'); ?></li>
                        <li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Copy API Credentials', 'pnscode-social-login-and-register'); ?></li>
                        <li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Set Redirect URI', 'pnscode-social-login-and-register'); ?></li>
                        <li><span class="dashicons dashicons-yes-alt"></span> <?php esc_html_e('Enable & Save Settings', 'pnscode-social-login-and-register'); ?></li>
                    </ul>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=pnscsolo-social-login-documentation&provider=' . $pnscsolo_active_provider)); ?>" class="pnscsolo-btn pnscsolo-btn-secondary" style="width: 100%; margin-top: 15px; justify-content: center;">
                        <?php esc_html_e('Detailed Help →', 'pnscode-social-login-and-register'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
