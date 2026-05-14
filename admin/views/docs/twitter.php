<?php
/**
 * Twitter (X) Setup Documentation
 */

if (!defined('ABSPATH')) {
    exit;
}

$pnscsolo_provider = PNSCSOLO_Social_Auth::get_instance()->get_provider('twitter');
$pnscsolo_callback_url = $pnscsolo_provider ? $pnscsolo_provider->get_callback_url() : '';
?>

<div class="pnscsolo-doc-provider">
    <div class="pnscsolo-doc-header">
        <h2><?php esc_html_e('X (Twitter) OAuth Setup Guide', 'pnscode-social-login-and-register'); ?></h2>
        <p class="pnscsolo-doc-intro"><?php esc_html_e('Follow these steps to enable X (Twitter) login for your WordPress site.', 'pnscode-social-login-and-register'); ?></p>
    </div>
    
    <div class="pnscsolo-doc-section">
        <h3><?php esc_html_e('Step 1: Create a Twitter Developer Account', 'pnscode-social-login-and-register'); ?></h3>
        <ol>
            <li><?php esc_html_e('Go to', 'pnscode-social-login-and-register'); ?> <a href="https://developer.twitter.com/" target="_blank">Twitter Developer Portal</a></li>
            <li><?php esc_html_e('Sign in with your Twitter account', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('Apply for a developer account if you don\'t have one', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('Complete the application form and verify your email', 'pnscode-social-login-and-register'); ?></li>
        </ol>
    </div>
    
    <div class="pnscsolo-doc-section">
        <h3><?php esc_html_e('Step 2: Create a New App', 'pnscode-social-login-and-register'); ?></h3>
        <ol>
            <li><?php esc_html_e('Go to the', 'pnscode-social-login-and-register'); ?> <a href="https://developer.twitter.com/en/portal/projects-and-apps" target="_blank"><?php esc_html_e('Developer Portal Dashboard', 'pnscode-social-login-and-register'); ?></a></li>
            <li><?php esc_html_e('Click "Create Project" or "Create App"', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('Enter your app details:', 'pnscode-social-login-and-register'); ?>
                <ul>
                    <li><?php esc_html_e('App Name: Your site name', 'pnscode-social-login-and-register'); ?></li>
                    <li><?php esc_html_e('Description: Brief description of your site', 'pnscode-social-login-and-register'); ?></li>
                </ul>
            </li>
            <li><?php esc_html_e('Click "Complete" or "Create"', 'pnscode-social-login-and-register'); ?></li>
        </ol>
    </div>
    
    <div class="pnscsolo-doc-section">
        <h3><?php esc_html_e('Step 3: Configure App Settings', 'pnscode-social-login-and-register'); ?></h3>
        <ol>
            <li><?php esc_html_e('In your app settings, go to "User authentication settings"', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('Click "Set up" or "Edit"', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('Enable "OAuth 2.0"', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('Set Type of App to "Web App"', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('Add this Callback URL:', 'pnscode-social-login-and-register'); ?>
                <div class="pnscsolo-callback-url-box">
                    <code><?php echo esc_html($pnscsolo_callback_url); ?></code>
                    <button type="button" class="button button-small pnscsolo-copy-btn" data-clipboard-text="<?php echo esc_attr($pnscsolo_callback_url); ?>">
                        <?php esc_html_e('Copy', 'pnscode-social-login-and-register'); ?>
                    </button>
                </div>
            </li>
            <li><?php esc_html_e('Enter your Website URL', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('Click "Save"', 'pnscode-social-login-and-register'); ?></li>
        </ol>
    </div>
    
    <div class="pnscsolo-doc-section">
        <h3><?php esc_html_e('Step 4: Get Your Credentials', 'pnscode-social-login-and-register'); ?></h3>
        <ol>
            <li><?php esc_html_e('Go to "Keys and tokens" tab', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('Under "OAuth 2.0 Client ID and Client Secret":', 'pnscode-social-login-and-register'); ?>
                <ul>
                    <li><?php esc_html_e('Copy the "Client ID"', 'pnscode-social-login-and-register'); ?></li>
                    <li><?php esc_html_e('Copy the "Client Secret"', 'pnscode-social-login-and-register'); ?></li>
                </ul>
            </li>
            <li><?php esc_html_e('Go to', 'pnscode-social-login-and-register'); ?> <a href="<?php echo esc_url(admin_url('admin.php?page=pnscsolo-social-login-providers&provider=twitter')); ?>"><?php esc_html_e('Social Providers Settings', 'pnscode-social-login-and-register'); ?></a></li>
            <li><?php esc_html_e('Paste the credentials and enable the provider', 'pnscode-social-login-and-register'); ?></li>
        </ol>
    </div>
    
    <div class="pnscsolo-doc-section">
        <h3><?php esc_html_e('Important Notes', 'pnscode-social-login-and-register'); ?></h3>
        <div class="pnscsolo-doc-warning">
            <ul>
                <li><?php esc_html_e('Twitter OAuth 2.0 does not provide email by default. Users may need to provide email separately.', 'pnscode-social-login-and-register'); ?></li>
                <li><?php esc_html_e('Make sure your app has the necessary permissions (tweet.read, users.read)', 'pnscode-social-login-and-register'); ?></li>
                <li><?php esc_html_e('For production use, your app must be approved by Twitter', 'pnscode-social-login-and-register'); ?></li>
            </ul>
        </div>
    </div>
    
    <div class="pnscsolo-doc-section">
        <h3><?php esc_html_e('Common Issues & Solutions', 'pnscode-social-login-and-register'); ?></h3>
        
        <div class="pnscsolo-doc-issue">
            <h4><?php esc_html_e('Error: Callback URL not approved', 'pnscode-social-login-and-register'); ?></h4>
            <p><strong><?php esc_html_e('Solution:', 'pnscode-social-login-and-register'); ?></strong> <?php esc_html_e('Make sure the callback URL is added in User authentication settings', 'pnscode-social-login-and-register'); ?></p>
        </div>
        
        <div class="pnscsolo-doc-issue">
            <h4><?php esc_html_e('Error: Invalid client credentials', 'pnscode-social-login-and-register'); ?></h4>
            <p><strong><?php esc_html_e('Solution:', 'pnscode-social-login-and-register'); ?></strong> <?php esc_html_e('Regenerate your Client ID and Secret from the Keys and tokens tab', 'pnscode-social-login-and-register'); ?></p>
        </div>
    </div>
    
    <div class="pnscsolo-doc-footer">
        <a href="<?php echo esc_url(admin_url('admin.php?page=pnscsolo-social-login-providers&provider=twitter')); ?>" class="button button-primary button-large">
            <?php esc_html_e('Configure Twitter Settings', 'pnscode-social-login-and-register'); ?>
        </a>
        <a href="https://developer.twitter.com/en/portal/dashboard" target="_blank" class="button button-secondary button-large">
            <?php esc_html_e('Open Twitter Developer Portal', 'pnscode-social-login-and-register'); ?>
        </a>
    </div>
</div>
