<?php
/**
 * Documentation View
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!current_user_can('manage_options')) {
    wp_die(esc_html__('Access denied', 'pnscode-social-login-and-register'));
}

$pnscsolo_allowed_docs = array('google', 'facebook', 'twitter', 'linkedin', 'github');
$pnscsolo_active_doc   = isset($_GET['provider']) ? sanitize_text_field(wp_unslash($_GET['provider'])) : 'google';

if (!in_array($pnscsolo_active_doc, $pnscsolo_allowed_docs, true)) {
    $pnscsolo_active_doc = 'google';
}
?>

<div class="wrap pnscsolo-admin-wrap pnscsolo-documentation-page">
    <div class="pnscsolo-admin-header">
        <h1><?php esc_html_e('Setup Documentation', 'pnscode-social-login-and-register'); ?></h1>
        <p class="pnscsolo-admin-subtitle"><?php esc_html_e('Follow these guides to correctly configure your social authentication applications.', 'pnscode-social-login-and-register'); ?></p>
    </div>
    
    <div class="pnscsolo-admin-grid">
        <div class="pnscsolo-admin-main-content">
            <div class="pnscsolo-documentation-main">
                <div class="pnscsolo-doc-tabs">
                    <a href="<?php echo esc_url(admin_url('admin.php?page=pnscsolo-social-login-documentation&provider=google')); ?>" class="pnscsolo-doc-tab <?php echo $pnscsolo_active_doc === 'google' ? 'active' : ''; ?>">
                        <span class="dashicons dashicons-google"></span> <?php esc_html_e('Google', 'pnscode-social-login-and-register'); ?>
                    </a>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=pnscsolo-social-login-documentation&provider=facebook')); ?>" class="pnscsolo-doc-tab <?php echo $pnscsolo_active_doc === 'facebook' ? 'active' : ''; ?>">
                        <span class="dashicons dashicons-facebook"></span> <?php esc_html_e('Facebook', 'pnscode-social-login-and-register'); ?>
                    </a>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=pnscsolo-social-login-documentation&provider=twitter')); ?>" class="pnscsolo-doc-tab <?php echo $pnscsolo_active_doc === 'twitter' ? 'active' : ''; ?>">
                        <span class="dashicons dashicons-twitter"></span> <?php esc_html_e('X (Twitter)', 'pnscode-social-login-and-register'); ?>
                    </a>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=pnscsolo-social-login-documentation&provider=linkedin')); ?>" class="pnscsolo-doc-tab <?php echo $pnscsolo_active_doc === 'linkedin' ? 'active' : ''; ?>">
                        <span class="dashicons dashicons-linkedin"></span> <?php esc_html_e('LinkedIn', 'pnscode-social-login-and-register'); ?>
                    </a>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=pnscsolo-social-login-documentation&provider=github')); ?>" class="pnscsolo-doc-tab <?php echo $pnscsolo_active_doc === 'github' ? 'active' : ''; ?>">
                        <span class="dashicons dashicons-networks"></span> <?php esc_html_e('GitHub', 'pnscode-social-login-and-register'); ?>
                    </a>
                </div>
                
                <div class="pnscsolo-doc-content-wrapper">
                    <div class="pnscsolo-card">
                        <div class="pnscsolo-card-body pnscsolo-doc-body">
                            <?php
                            $pnscsolo_doc_file = PNSCSOLO_PLUGIN_DIR . 'admin/views/docs/' . $pnscsolo_active_doc . '.php';
                            if (file_exists($pnscsolo_doc_file)) {
                                include $pnscsolo_doc_file;
                            } else {
                                echo '<div class="pnscsolo-no-doc"><span class="dashicons dashicons-warning"></span><p>' . esc_html__('Documentation guide is coming soon for this provider.', 'pnscode-social-login-and-register') . '</p></div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="pnscsolo-admin-sidebar">
            <div class="pnscsolo-card pnscsolo-sidebar-card">
                <div class="pnscsolo-card-header small">
                    <span class="dashicons dashicons-sos"></span>
                    <h3><?php esc_html_e('Need Assistance?', 'pnscode-social-login-and-register'); ?></h3>
                </div>
                <div class="pnscsolo-card-body">
                    <p class="pnscsolo-sidebar-info"><?php esc_html_e('Stuck with the setup? Verify these common requirements first:', 'pnscode-social-login-and-register'); ?></p>
                    <ul class="pnscsolo-sidebar-links checklist">
                        <li><span class="dashicons dashicons-yes"></span> <?php esc_html_e('Callback URL matches exactly', 'pnscode-social-login-and-register'); ?></li>
                        <li><span class="dashicons dashicons-yes"></span> <?php esc_html_e('API keys are correctly pasted', 'pnscode-social-login-and-register'); ?></li>
                        <li><span class="dashicons dashicons-yes"></span> <?php esc_html_e('Scopes/Permissions enabled', 'pnscode-social-login-and-register'); ?></li>
                        <li><span class="dashicons dashicons-yes"></span> <?php esc_html_e('Your site uses HTTPS (SSL)', 'pnscode-social-login-and-register'); ?></li>
                    </ul>
                </div>
            </div>
            
            <div class="pnscsolo-card pnscsolo-sidebar-card">
                <div class="pnscsolo-card-header small">
                    <span class="dashicons dashicons-warning"></span>
                    <h3><?php esc_html_e('Troubleshooting', 'pnscode-social-login-and-register'); ?></h3>
                </div>
                <div class="pnscsolo-card-body">
                    <div class="pnscsolo-error-guide">
                        <div class="pnscsolo-error-item-v2">
                            <strong><?php esc_html_e('URI Mismatch', 'pnscode-social-login-and-register'); ?></strong>
                            <p><?php esc_html_e('Ensure the "Authorized Redirect URI" in your provider panel is identical to the one in our settings.', 'pnscode-social-login-and-register'); ?></p>
                        </div>
                        <div class="pnscsolo-error-item-v2">
                            <strong><?php esc_html_e('Invalid Client', 'pnscode-social-login-and-register'); ?></strong>
                            <p><?php esc_html_e('This usually means the Client ID or Secret is incorrect or the app is not published.', 'pnscode-social-login-and-register'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
