<?php
/**
 * Security & Login History View
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!current_user_can('manage_options')) {
    wp_die(esc_html__('Access denied', 'pnscode-social-login-and-register'));
}

// Handle unlock action if any
if (isset($_GET['action']) && sanitize_text_field(wp_unslash($_GET['action'])) === 'unlock' && isset($_GET['user_id'])) {
    check_admin_referer('PNSCSOLO_unlock_user');
    $pnscsolo_user_to_unlock = intval($_GET['user_id']);
    PNSCSOLO_Security::unlock_account($pnscsolo_user_to_unlock);
    echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('User account unlocked successfully.', 'pnscode-social-login-and-register') . '</p></div>';
}

$pnscsolo_message      = isset($_GET['message']) && $_GET['message'] === 'saved';
$pnscsolo_settings     = PNSCSOLO_Settings::get_general_settings();
$pnscsolo_max_attempts = $pnscsolo_settings['max_login_attempts'] ?? 5;

// Get users with locked accounts
$pnscsolo_locked_users = get_users(array(
    'meta_key'   => 'PNSCSOLO_account_locked', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
    'meta_value' => '1',                       // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
));

// Get recent login activities across site (this is a bit heavy, but for small/medium sites it works)
$pnscsolo_recent_logins     = array();
$pnscsolo_users_with_history = get_users(array(
    'meta_key' => 'PNSCSOLO_last_login', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
    'orderby' => 'meta_value',
    'order' => 'DESC',
    'number' => 10
));

foreach ($pnscsolo_users_with_history as $pnscsolo_u) {
    $pnscsolo_history = PNSCSOLO_Security::get_login_history($pnscsolo_u->ID, 1);
    if (!empty($pnscsolo_history)) {
        $pnscsolo_recent_logins[] = array_merge($pnscsolo_history[0], array('user_login' => $pnscsolo_u->user_login, 'user_id' => $pnscsolo_u->ID));
    }
}
?>

<div class="wrap pnscsolo-admin-wrap pnscsolo-security-page">
    <div class="pnscsolo-admin-header">
        <h1><?php esc_html_e('Security & Login Logs', 'pnscode-social-login-and-register'); ?></h1>
        <p class="pnscsolo-admin-subtitle"><?php esc_html_e('Monitor login activities and manage account protection settings.', 'pnscode-social-login-and-register'); ?></p>
    </div>

    <?php if ($pnscsolo_message): ?>
        <div class="notice notice-success is-dismissible pnscsolo-notice">
            <p><?php esc_html_e('Security settings updated successfully!', 'pnscode-social-login-and-register'); ?></p>
        </div>
    <?php endif; ?>

    <div class="pnscsolo-admin-grid">
        <div class="pnscsolo-admin-main-content">
            <!-- Security Configuration -->
            <div class="pnscsolo-card">
                <div class="pnscsolo-card-header">
                    <span class="dashicons dashicons-shield"></span>
                    <h2><?php esc_html_e('Brute Force Protection', 'pnscode-social-login-and-register'); ?></h2>
                </div>
                <div class="pnscsolo-card-body">
                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <?php wp_nonce_field('PNSCSOLO_save_general_settings'); ?>
                        <input type="hidden" name="action" value="PNSCSOLO_save_general_settings">
                        
                        <div class="pnscsolo-setting-row">
                            <div class="pnscsolo-setting-info">
                                <label for="max_login_attempts"><?php esc_html_e('Max Login Attempts', 'pnscode-social-login-and-register'); ?></label>
                                <p class="pnscsolo-description"><?php esc_html_e('Number of failed attempts allowed before account lockout', 'pnscode-social-login-and-register'); ?></p>
                            </div>
                            <div class="pnscsolo-setting-control">
                                <input type="number" id="max_login_attempts" name="max_login_attempts" value="<?php echo esc_attr($pnscsolo_max_attempts); ?>" class="small-text" min="1" max="10">
                            </div>
                        </div>

                        <div class="pnscsolo-form-footer">
                            <button type="submit" class="pnscsolo-btn pnscsolo-btn-primary">
                                <span class="dashicons dashicons-saved"></span>
                                <?php esc_html_e('Save Security Settings', 'pnscode-social-login-and-register'); ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Recent Activity Table -->
            <div class="pnscsolo-card">
                <div class="pnscsolo-card-header">
                    <span class="dashicons dashicons-list-view"></span>
                    <h2><?php esc_html_e('Recent Login Activities', 'pnscode-social-login-and-register'); ?></h2>
                </div>
                <div class="pnscsolo-card-body" style="padding:0;">
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('User', 'pnscode-social-login-and-register'); ?></th>
                                <th><?php esc_html_e('IP Address', 'pnscode-social-login-and-register'); ?></th>
                                <th><?php esc_html_e('Time', 'pnscode-social-login-and-register'); ?></th>
                                <th><?php esc_html_e('Status', 'pnscode-social-login-and-register'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pnscsolo_recent_logins)): ?>
                                <tr>
                                    <td colspan="4" style="padding: 20px; text-align: center; color: #64748b;">
                                        <?php esc_html_e('No login activities recorded yet.', 'pnscode-social-login-and-register'); ?>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($pnscsolo_recent_logins as $pnscsolo_log): ?>
                                    <tr>
                                         <td><strong><?php echo esc_html($pnscsolo_log['user_login']); ?></strong></td>
                                        <td><code><?php echo esc_html($pnscsolo_log['ip']); ?></code></td>
                                        <td><?php echo esc_html($pnscsolo_log['time']); ?></td>
                                        <td>
                                            <span class="pnscsolo-status-badge <?php echo $pnscsolo_log['status'] === 'success' ? 'active' : 'inactive'; ?>">
                                                <?php echo esc_html(ucfirst($pnscsolo_log['status'])); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="pnscsolo-admin-sidebar">
            <!-- Locked Accounts -->
            <div class="pnscsolo-card pnscsolo-sidebar-card">
                <div class="pnscsolo-card-header small">
                    <span class="dashicons dashicons-lock"></span>
                    <h3><?php esc_html_e('Locked Accounts', 'pnscode-social-login-and-register'); ?></h3>
                </div>
                <div class="pnscsolo-card-body">
                    <?php if (empty($pnscsolo_locked_users)): ?>
                        <div class="pnscsolo-no-locked">
                            <p style="color: #64748b; font-size: 13px; margin: 0;"><?php esc_html_e('No accounts are currently locked.', 'pnscode-social-login-and-register'); ?></p>
                        </div>
                    <?php else: ?>
                        <ul class="pnscsolo-sidebar-links">
                            <?php foreach ($pnscsolo_locked_users as $pnscsolo_user): ?>
                                <li style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px; margin-bottom: 10px;">
                                    <div>
                                        <span style="display: block; font-weight: 600; font-size: 13px;"><?php echo esc_html($pnscsolo_user->user_login); ?></span>
                                        <span style="font-size: 11px; color: #94a3b8;"><?php esc_html_e('Locked due to multiple fails', 'pnscode-social-login-and-register'); ?></span>
                                    </div>
                                    <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=pnscsolo-social-login-security&action=unlock&user_id=' . $pnscsolo_user->ID), 'PNSCSOLO_unlock_user')); ?>" class="pnscsolo-btn pnscsolo-btn-secondary" style="padding: 4px 8px; font-size: 11px;">
                                        <?php esc_html_e('Unlock', 'pnscode-social-login-and-register'); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Security Tip -->
            <div class="pnscsolo-card pnscsolo-sidebar-card pnscsolo-card-indigo">
                <div class="pnscsolo-card-header small">
                    <span class="dashicons dashicons-lightbulb"></span>
                    <h3><?php esc_html_e('Security Tip', 'pnscode-social-login-and-register'); ?></h3>
                </div>
                <div class="pnscsolo-card-body">
                    <p style="font-size: 13px; margin: 0; line-height: 1.5;">
                        <?php esc_html_e('Using a lower number of max login attempts increases security but might lock out genuine users who forget their passwords frequently.', 'pnscode-social-login-and-register'); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
