<?php
/**
 * LinkedIn Setup Documentation
 */

if (!defined('ABSPATH')) {
    exit;
}

$pnscsolo_provider = PNSCSOLO_Social_Auth::get_instance()->get_provider('linkedin');
$pnscsolo_callback_url = $pnscsolo_provider ? $pnscsolo_provider->get_callback_url() : '';
?>

<div class="pnscsolo-doc-provider">
    <div class="pnscsolo-doc-header">
        <h2><?php esc_html_e('LinkedIn OAuth Setup Guide', 'pnscode-social-login-and-register'); ?></h2>
        <p class="pnscsolo-doc-intro"><?php esc_html_e('Follow these steps to enable LinkedIn login for your WordPress site.', 'pnscode-social-login-and-register'); ?></p>
    </div>
    
    <div class="pnscsolo-doc-section">
        <h3><?php esc_html_e('Step 1: Create a LinkedIn App', 'pnscode-social-login-and-register'); ?></h3>
        <ol>
            <li><?php esc_html_e('Go to', 'pnscode-social-login-and-register'); ?> <a href="https://www.linkedin.com/developers/apps" target="_blank">LinkedIn Developers</a></li>
            <li><?php esc_html_e('Click "Create app"', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('Fill in the required information:', 'pnscode-social-login-and-register'); ?>
                <ul>
                    <li><?php esc_html_e('App name: Your site name', 'pnscode-social-login-and-register'); ?></li>
                    <li><?php esc_html_e('LinkedIn Page: Select or create a LinkedIn page', 'pnscode-social-login-and-register'); ?></li>
                    <li><?php esc_html_e('App logo: Upload your logo', 'pnscode-social-login-and-register'); ?></li>
                </ul>
            </li>
            <li><?php esc_html_e('Accept the API Terms of Use', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('Click "Create app"', 'pnscode-social-login-and-register'); ?></li>
        </ol>
    </div>
    
    <div class="pnscsolo-doc-section">
        <h3><?php esc_html_e('Step 2: Configure OAuth Settings', 'pnscode-social-login-and-register'); ?></h3>
        <ol>
            <li><?php esc_html_e('In your app, go to the "Auth" tab', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('Under "OAuth 2.0 settings", find "Redirect URLs"', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('Click "Add redirect URL" and paste this callback URL:', 'pnscode-social-login-and-register'); ?>
                <div class="pnscsolo-callback-url-box">
                    <code><?php echo esc_html($pnscsolo_callback_url); ?></code>
                    <button type="button" class="button button-small pnscsolo-copy-btn" data-clipboard-text="<?php echo esc_attr($pnscsolo_callback_url); ?>">
                        <?php esc_html_e('Copy', 'pnscode-social-login-and-register'); ?>
                    </button>
                </div>
            </li>
            <li><?php esc_html_e('Click "Update"', 'pnscode-social-login-and-register'); ?></li>
        </ol>
    </div>
    
    <div class="pnscsolo-doc-section">
        <h3><?php esc_html_e('Step 3: Request Access to Sign In with LinkedIn', 'pnscode-social-login-and-register'); ?></h3>
        <ol>
            <li><?php esc_html_e('Go to the "Products" tab', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('Find "Sign In with LinkedIn using OpenID Connect"', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('Click "Request access"', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('Wait for approval (usually instant for basic use)', 'pnscode-social-login-and-register'); ?></li>
        </ol>
    </div>
    
    <div class="pnscsolo-doc-section">
        <h3><?php esc_html_e('Step 4: Get Your Credentials', 'pnscode-social-login-and-register'); ?></h3>
        <ol>
            <li><?php esc_html_e('Go back to the "Auth" tab', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('Under "Application credentials":', 'pnscode-social-login-and-register'); ?>
                <ul>
                    <li><?php esc_html_e('Copy the "Client ID"', 'pnscode-social-login-and-register'); ?></li>
                    <li><?php esc_html_e('Copy the "Client Secret"', 'pnscode-social-login-and-register'); ?></li>
                </ul>
            </li>
            <li><?php esc_html_e('Go to', 'pnscode-social-login-and-register'); ?> <a href="<?php echo esc_url(admin_url('admin.php?page=pnscsolo-social-login-providers&provider=linkedin')); ?>"><?php esc_html_e('Social Providers Settings', 'pnscode-social-login-and-register'); ?></a></li>
            <li><?php esc_html_e('Paste the credentials and enable the provider', 'pnscode-social-login-and-register'); ?></li>
        </ol>
    </div>
    
    <div class="pnscsolo-doc-section">
        <h3><?php esc_html_e('Common Issues & Solutions', 'pnscode-social-login-and-register'); ?></h3>
        
        <div class="pnscsolo-doc-issue">
            <h4><?php esc_html_e('Error: Redirect URI does not match', 'pnscode-social-login-and-register'); ?></h4>
            <p><strong><?php esc_html_e('Solution:', 'pnscode-social-login-and-register'); ?></strong> <?php esc_html_e('Ensure the callback URL is exactly as shown, including protocol (https)', 'pnscode-social-login-and-register'); ?></p>
        </div>
        
        <div class="pnscsolo-doc-issue">
            <h4><?php esc_html_e('Error: Unauthorized scope', 'pnscode-social-login-and-register'); ?></h4>
            <p><strong><?php esc_html_e('Solution:', 'pnscode-social-login-and-register'); ?></strong> <?php esc_html_e('Make sure "Sign In with LinkedIn using OpenID Connect" product is approved', 'pnscode-social-login-and-register'); ?></p>
        </div>
    </div>
    
    <div class="pnscsolo-doc-footer">
        <a href="<?php echo esc_url(admin_url('admin.php?page=pnscsolo-social-login-providers&provider=linkedin')); ?>" class="button button-primary button-large">
            <?php esc_html_e('Configure LinkedIn Settings', 'pnscode-social-login-and-register'); ?>
        </a>
        <a href="https://www.linkedin.com/developers/apps" target="_blank" class="button button-secondary button-large">
            <?php esc_html_e('Open LinkedIn Developers', 'pnscode-social-login-and-register'); ?>
        </a>
    </div>
</div>
