<?php
/**
 * Facebook Setup Documentation
 */

if (!defined('ABSPATH')) {
    exit;
}

$pnscsolo_provider     = PNSCSOLO_Social_Auth::get_instance()->get_provider('facebook');
$pnscsolo_callback_url = $pnscsolo_provider ? $pnscsolo_provider->get_callback_url() : '';
?>

<div class="pnscsolo-doc-provider">
    <div class="pnscsolo-doc-header">
        <h2><?php esc_html_e('Facebook OAuth Setup Guide', 'pnscode-social-login-and-register'); ?></h2>
        <p class="pnscsolo-doc-intro"><?php esc_html_e('Follow these steps to enable Facebook login for your WordPress site.', 'pnscode-social-login-and-register'); ?></p>
    </div>
    
    <div class="pnscsolo-doc-section">
        <h3><?php esc_html_e('Step 1: Create a Facebook App', 'pnscode-social-login-and-register'); ?></h3>
        <ol>
            <li><?php esc_html_e('Go to', 'pnscode-social-login-and-register'); ?> <a href="https://developers.facebook.com/apps/" target="_blank">Facebook Developers</a></li>
            <li><?php esc_html_e('Click "Create App"', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('Select "Consumer" as the app type', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('Fill in the app details:', 'pnscode-social-login-and-register'); ?>
                <ul>
                    <li><?php esc_html_e('App Name: Your site name', 'pnscode-social-login-and-register'); ?></li>
                    <li><?php esc_html_e('App Contact Email: Your email', 'pnscode-social-login-and-register'); ?></li>
                </ul>
            </li>
            <li><?php esc_html_e('Click "Create App"', 'pnscode-social-login-and-register'); ?></li>
        </ol>
    </div>
    
    <div class="pnscsolo-doc-section">
        <h3><?php esc_html_e('Step 2: Add Facebook Login Product', 'pnscode-social-login-and-register'); ?></h3>
        <ol>
            <li><?php esc_html_e('In your app dashboard, find "Facebook Login" and click "Set Up"', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('Select "Web" as the platform', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('Enter your site URL and click "Save"', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('Click "Continue" through the quick start guide', 'pnscode-social-login-and-register'); ?></li>
        </ol>
    </div>
    
    <div class="pnscsolo-doc-section">
        <h3><?php esc_html_e('Step 3: Configure OAuth Redirect URIs', 'pnscode-social-login-and-register'); ?></h3>
        <ol>
            <li><?php esc_html_e('Go to "Facebook Login" > "Settings" in the left sidebar', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('In "Valid OAuth Redirect URIs", add this callback URL:', 'pnscode-social-login-and-register'); ?>
                <div class="pnscsolo-callback-url-box">
                    <code><?php echo esc_html($pnscsolo_callback_url); ?></code>
                    <button type="button" class="button button-small pnscsolo-copy-btn" data-clipboard-text="<?php echo esc_attr($pnscsolo_callback_url); ?>">
                        <?php esc_html_e('Copy', 'pnscode-social-login-and-register'); ?>
                    </button>
                </div>
            </li>
            <li><?php esc_html_e('Click "Save Changes"', 'pnscode-social-login-and-register'); ?></li>
        </ol>
    </div>
    
    <div class="pnscsolo-doc-section">
        <h3><?php esc_html_e('Step 4: Get Your App Credentials', 'pnscode-social-login-and-register'); ?></h3>
        <ol>
            <li><?php esc_html_e('Go to "Settings" > "Basic" in the left sidebar', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('Copy the "App ID" (this is your Client ID)', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('Click "Show" next to "App Secret" and copy it (this is your Client Secret)', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('Go to', 'pnscode-social-login-and-register'); ?> <a href="<?php echo esc_url(admin_url('admin.php?page=pnscsolo-social-login-providers&provider=facebook')); ?>"><?php esc_html_e('Social Providers Settings', 'pnscode-social-login-and-register'); ?></a></li>
            <li><?php esc_html_e('Paste the App ID as Client ID and App Secret as Client Secret', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('Enable the provider and save settings', 'pnscode-social-login-and-register'); ?></li>
        </ol>
    </div>
    
    <div class="pnscsolo-doc-section">
        <h3><?php esc_html_e('Step 5: Make Your App Live', 'pnscode-social-login-and-register'); ?></h3>
        <ol>
            <li><?php esc_html_e('Go to "Settings" > "Basic"', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('Fill in all required fields (Privacy Policy URL, Category, etc.)', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('At the top of the page, toggle the app from "Development" to "Live"', 'pnscode-social-login-and-register'); ?></li>
        </ol>
        <div class="pnscsolo-doc-note">
            <strong><?php esc_html_e('Note:', 'pnscode-social-login-and-register'); ?></strong>
            <?php esc_html_e('You can test with development mode, but only you and other app developers can use it. Switch to Live mode for public use.', 'pnscode-social-login-and-register'); ?>
        </div>
    </div>
    
    <div class="pnscsolo-doc-section">
        <h3><?php esc_html_e('Common Issues & Solutions', 'pnscode-social-login-and-register'); ?></h3>
        
        <div class="pnscsolo-doc-issue">
            <h4><?php esc_html_e('Error: URL Blocked', 'pnscode-social-login-and-register'); ?></h4>
            <p><strong><?php esc_html_e('Solution:', 'pnscode-social-login-and-register'); ?></strong> <?php esc_html_e('Add your site URL to "App Domains" in Settings > Basic', 'pnscode-social-login-and-register'); ?></p>
        </div>
        
        <div class="pnscsolo-doc-issue">
            <h4><?php esc_html_e('Error: Can\'t Load URL', 'pnscode-social-login-and-register'); ?></h4>
            <p><strong><?php esc_html_e('Solution:', 'pnscode-social-login-and-register'); ?></strong> <?php esc_html_e('Make sure the callback URL is added to Valid OAuth Redirect URIs', 'pnscode-social-login-and-register'); ?></p>
        </div>
        
        <div class="pnscsolo-doc-issue">
            <h4><?php esc_html_e('App is in Development Mode', 'pnscode-social-login-and-register'); ?></h4>
            <p><strong><?php esc_html_e('Solution:', 'pnscode-social-login-and-register'); ?></strong> <?php esc_html_e('Complete all required fields and switch to Live mode, or add test users in Roles > Test Users', 'pnscode-social-login-and-register'); ?></p>
        </div>
    </div>
    
    <div class="pnscsolo-doc-footer">
        <a href="<?php echo esc_url(admin_url('admin.php?page=pnscsolo-social-login-providers&provider=facebook')); ?>" class="button button-primary button-large">
            <?php esc_html_e('Configure Facebook Settings', 'pnscode-social-login-and-register'); ?>
        </a>
        <a href="https://developers.facebook.com/apps/" target="_blank" class="button button-secondary button-large">
            <?php esc_html_e('Open Facebook Developers', 'pnscode-social-login-and-register'); ?>
        </a>
    </div>
</div>
