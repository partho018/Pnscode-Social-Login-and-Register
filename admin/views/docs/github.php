<?php
/**
 * GitHub Setup Documentation
 */

if (!defined('ABSPATH')) {
    exit;
}

$pnscsolo_provider = PNSCSOLO_Social_Auth::get_instance()->get_provider('github');
$pnscsolo_callback_url = $pnscsolo_provider ? $pnscsolo_provider->get_callback_url() : '';
?>

<div class="pnscsolo-doc-provider">
    <div class="pnscsolo-doc-header">
        <h2><?php esc_html_e('GitHub OAuth Setup Guide', 'pnscode-social-login-and-register'); ?></h2>
        <p class="pnscsolo-doc-intro"><?php esc_html_e('Follow these steps to enable GitHub login for your WordPress site.', 'pnscode-social-login-and-register'); ?></p>
    </div>
    
    <div class="pnscsolo-doc-section">
        <h3><?php esc_html_e('Step 1: Create a GitHub OAuth App', 'pnscode-social-login-and-register'); ?></h3>
        <ol>
            <li><?php esc_html_e('Go to', 'pnscode-social-login-and-register'); ?> <a href="https://github.com/settings/developers" target="_blank">GitHub Developer Settings</a></li>
            <li><?php esc_html_e('Click "OAuth Apps" in the left sidebar', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('Click "New OAuth App"', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('Fill in the application details:', 'pnscode-social-login-and-register'); ?>
                <ul>
                    <li><?php esc_html_e('Application name: Your site name', 'pnscode-social-login-and-register'); ?></li>
                    <li><?php esc_html_e('Homepage URL: Your site URL', 'pnscode-social-login-and-register'); ?></li>
                    <li><?php esc_html_e('Application description: Brief description (optional)', 'pnscode-social-login-and-register'); ?></li>
                </ul>
            </li>
        </ol>
    </div>
    
    <div class="pnscsolo-doc-section">
        <h3><?php esc_html_e('Step 2: Set Authorization Callback URL', 'pnscode-social-login-and-register'); ?></h3>
        <ol>
            <li><?php esc_html_e('In the "Authorization callback URL" field, paste:', 'pnscode-social-login-and-register'); ?>
                <div class="pnscsolo-callback-url-box">
                    <code><?php echo esc_html($pnscsolo_callback_url); ?></code>
                    <button type="button" class="button button-small pnscsolo-copy-btn" data-clipboard-text="<?php echo esc_attr($pnscsolo_callback_url); ?>">
                        <?php esc_html_e('Copy', 'pnscode-social-login-and-register'); ?>
                    </button>
                </div>
            </li>
            <li><?php esc_html_e('Click "Register application"', 'pnscode-social-login-and-register'); ?></li>
        </ol>
    </div>
    
    <div class="pnscsolo-doc-section">
        <h3><?php esc_html_e('Step 3: Get Your Credentials', 'pnscode-social-login-and-register'); ?></h3>
        <ol>
            <li><?php esc_html_e('After creating the app, you\'ll see your "Client ID"', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('Click "Generate a new client secret"', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('Copy both the Client ID and Client Secret immediately (you won\'t be able to see the secret again)', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('Go to', 'pnscode-social-login-and-register'); ?> <a href="<?php echo esc_url(admin_url('admin.php?page=pnscsolo-social-login-providers&provider=github')); ?>"><?php esc_html_e('Social Providers Settings', 'pnscode-social-login-and-register'); ?></a></li>
            <li><?php esc_html_e('Paste the credentials and enable the provider', 'pnscode-social-login-and-register'); ?></li>
        </ol>
    </div>
    
    <div class="pnscsolo-doc-section">
        <h3><?php esc_html_e('Important Notes', 'pnscode-social-login-and-register'); ?></h3>
        <div class="pnscsolo-doc-note">
            <ul>
                <li><?php esc_html_e('GitHub provides public email addresses by default', 'pnscode-social-login-and-register'); ?></li>
                <li><?php esc_html_e('Users with private emails may need to provide email separately', 'pnscode-social-login-and-register'); ?></li>
                <li><?php esc_html_e('The app works immediately - no approval process needed', 'pnscode-social-login-and-register'); ?></li>
            </ul>
        </div>
    </div>
    
    <div class="pnscsolo-doc-section">
        <h3><?php esc_html_e('Common Issues & Solutions', 'pnscode-social-login-and-register'); ?></h3>
        
        <div class="pnscsolo-doc-issue">
            <h4><?php esc_html_e('Error: Redirect URI mismatch', 'pnscode-social-login-and-register'); ?></h4>
            <p><strong><?php esc_html_e('Solution:', 'pnscode-social-login-and-register'); ?></strong> <?php esc_html_e('The callback URL must exactly match what\'s in your GitHub app settings', 'pnscode-social-login-and-register'); ?></p>
        </div>
        
        <div class="pnscsolo-doc-issue">
            <h4><?php esc_html_e('Error: Bad credentials', 'pnscode-social-login-and-register'); ?></h4>
            <p><strong><?php esc_html_e('Solution:', 'pnscode-social-login-and-register'); ?></strong> <?php esc_html_e('Regenerate your client secret and update it in the plugin settings', 'pnscode-social-login-and-register'); ?></p>
        </div>
        
        <div class="pnscsolo-doc-issue">
            <h4><?php esc_html_e('No email received', 'pnscode-social-login-and-register'); ?></h4>
            <p><strong><?php esc_html_e('Solution:', 'pnscode-social-login-and-register'); ?></strong> <?php esc_html_e('User has private email. You may need to request email scope or ask users to provide email separately', 'pnscode-social-login-and-register'); ?></p>
        </div>
    </div>
    
    <div class="pnscsolo-doc-footer">
        <a href="<?php echo esc_url(admin_url('admin.php?page=pnscsolo-social-login-providers&provider=github')); ?>" class="button button-primary button-large">
            <?php esc_html_e('Configure GitHub Settings', 'pnscode-social-login-and-register'); ?>
        </a>
        <a href="https://github.com/settings/developers" target="_blank" class="button button-secondary button-large">
            <?php esc_html_e('Open GitHub Developer Settings', 'pnscode-social-login-and-register'); ?>
        </a>
    </div>
</div>
