<?php
/**
 * Google Setup Documentation
 */

if (!defined('ABSPATH')) {
    exit;
}

$pnscsolo_provider     = PNSCSOLO_Social_Auth::get_instance()->get_provider('google');
$pnscsolo_callback_url = $pnscsolo_provider ? $pnscsolo_provider->get_callback_url() : '';
?>

<div class="pnscsolo-doc-provider">
    <div class="pnscsolo-doc-header">
        <h2><?php esc_html_e('Google OAuth Setup Guide', 'pnscode-social-login-and-register'); ?></h2>
        <p class="pnscsolo-doc-intro"><?php esc_html_e('Follow these steps to enable Google login for your WordPress site.', 'pnscode-social-login-and-register'); ?></p>
    </div>
    
    <div class="pnscsolo-doc-section">
        <h3><?php esc_html_e('Step 1: Create a Google Cloud Project', 'pnscode-social-login-and-register'); ?></h3>
        <ol>
            <li><?php esc_html_e('Go to', 'pnscode-social-login-and-register'); ?> <a href="https://console.cloud.google.com/" target="_blank">Google Cloud Console</a></li>
            <li><?php esc_html_e('Click on the project dropdown at the top and select "New Project"', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('Enter a project name (e.g., "My WordPress Site") and click "Create"', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('Wait for the project to be created and select it from the dropdown', 'pnscode-social-login-and-register'); ?></li>
        </ol>
        <div class="pnscsolo-doc-note">
            <strong><?php esc_html_e('Note:', 'pnscode-social-login-and-register'); ?></strong>
            <?php esc_html_e('If you already have a project, you can use it instead of creating a new one.', 'pnscode-social-login-and-register'); ?>
        </div>
    </div>
    
    <div class="pnscsolo-doc-section">
        <h3><?php esc_html_e('Step 2: Enable Google+ API', 'pnscode-social-login-and-register'); ?></h3>
        <ol>
            <li><?php esc_html_e('In the Google Cloud Console, go to "APIs & Services" > "Library"', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('Search for "Google+ API" or "People API"', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('Click on it and press "Enable"', 'pnscode-social-login-and-register'); ?></li>
        </ol>
    </div>
    
    <div class="pnscsolo-doc-section">
        <h3><?php esc_html_e('Step 3: Create OAuth 2.0 Credentials', 'pnscode-social-login-and-register'); ?></h3>
        <ol>
            <li><?php esc_html_e('Go to "APIs & Services" > "Credentials"', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('Click "Create Credentials" and select "OAuth client ID"', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('If prompted, configure the OAuth consent screen:', 'pnscode-social-login-and-register'); ?>
                <ul>
                    <li><?php esc_html_e('Choose "External" user type', 'pnscode-social-login-and-register'); ?></li>
                    <li><?php esc_html_e('Fill in the required fields (App name, User support email, Developer contact)', 'pnscode-social-login-and-register'); ?></li>
                    <li><?php esc_html_e('Click "Save and Continue"', 'pnscode-social-login-and-register'); ?></li>
                    <li><?php esc_html_e('Add scopes: userinfo.email and userinfo.profile', 'pnscode-social-login-and-register'); ?></li>
                    <li><?php esc_html_e('Complete the consent screen setup', 'pnscode-social-login-and-register'); ?></li>
                </ul>
            </li>
            <li><?php esc_html_e('Back in Credentials, click "Create Credentials" > "OAuth client ID" again', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('Select "Web application" as the application type', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('Enter a name (e.g., "WordPress Social Login")', 'pnscode-social-login-and-register'); ?></li>
        </ol>
    </div>
    
    <div class="pnscsolo-doc-section">
        <h3><?php esc_html_e('Step 4: Configure Authorized Redirect URIs', 'pnscode-social-login-and-register'); ?></h3>
        <ol>
            <li><?php esc_html_e('Under "Authorized redirect URIs", click "Add URI"', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('Copy and paste this callback URL:', 'pnscode-social-login-and-register'); ?>
                <div class="pnscsolo-callback-url-box">
                    <code id="google-callback-url"><?php echo esc_html($pnscsolo_callback_url); ?></code>
                    <button type="button" class="button button-small pnscsolo-copy-btn" data-clipboard-text="<?php echo esc_attr($pnscsolo_callback_url); ?>">
                        <?php esc_html_e('Copy', 'pnscode-social-login-and-register'); ?>
                    </button>
                </div>
            </li>
            <li><?php esc_html_e('Click "Create"', 'pnscode-social-login-and-register'); ?></li>
        </ol>
        <div class="pnscsolo-doc-warning">
            <strong><?php esc_html_e('Important:', 'pnscode-social-login-and-register'); ?></strong>
            <?php esc_html_e('The callback URL must match exactly, including the protocol (http/https). For production sites, always use HTTPS.', 'pnscode-social-login-and-register'); ?>
        </div>
    </div>
    
    <div class="pnscsolo-doc-section">
        <h3><?php esc_html_e('Step 5: Get Your Credentials', 'pnscode-social-login-and-register'); ?></h3>
        <ol>
            <li><?php esc_html_e('After creating the OAuth client, a dialog will appear with your credentials', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('Copy the "Client ID" and "Client Secret"', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('Go to', 'pnscode-social-login-and-register'); ?> <a href="<?php echo esc_url(admin_url('admin.php?page=pnscsolo-social-login-providers&provider=google')); ?>"><?php esc_html_e('Social Providers Settings', 'pnscode-social-login-and-register'); ?></a></li>
            <li><?php esc_html_e('Paste the Client ID and Client Secret in the Google settings', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('Enable the provider and save settings', 'pnscode-social-login-and-register'); ?></li>
        </ol>
    </div>
    
    <div class="pnscsolo-doc-section">
        <h3><?php esc_html_e('Step 6: Test the Integration', 'pnscode-social-login-and-register'); ?></h3>
        <ol>
            <li><?php esc_html_e('Open your site in an incognito/private window', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('Navigate to your login or registration page', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('Click the "Continue with Google" button', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('You should be redirected to Google for authentication', 'pnscode-social-login-and-register'); ?></li>
            <li><?php esc_html_e('After successful authentication, you should be logged into your WordPress site', 'pnscode-social-login-and-register'); ?></li>
        </ol>
    </div>
    
    <div class="pnscsolo-doc-section">
        <h3><?php esc_html_e('Common Issues & Solutions', 'pnscode-social-login-and-register'); ?></h3>
        
        <div class="pnscsolo-doc-issue">
            <h4><?php esc_html_e('Error: redirect_uri_mismatch', 'pnscode-social-login-and-register'); ?></h4>
            <p><strong><?php esc_html_e('Cause:', 'pnscode-social-login-and-register'); ?></strong> <?php esc_html_e('The callback URL in your Google app doesn\'t match the one being used.', 'pnscode-social-login-and-register'); ?></p>
            <p><strong><?php esc_html_e('Solution:', 'pnscode-social-login-and-register'); ?></strong></p>
            <ul>
                <li><?php esc_html_e('Make sure the callback URL in Google Console exactly matches:', 'pnscode-social-login-and-register'); ?> <code><?php echo esc_html($pnscsolo_callback_url); ?></code></li>
                <li><?php esc_html_e('Check for trailing slashes - they must match exactly', 'pnscode-social-login-and-register'); ?></li>
                <li><?php esc_html_e('Verify the protocol (http vs https)', 'pnscode-social-login-and-register'); ?></li>
            </ul>
        </div>
        
        <div class="pnscsolo-doc-issue">
            <h4><?php esc_html_e('Error: access_denied', 'pnscode-social-login-and-register'); ?></h4>
            <p><strong><?php esc_html_e('Cause:', 'pnscode-social-login-and-register'); ?></strong> <?php esc_html_e('User denied permission or app is not verified.', 'pnscode-social-login-and-register'); ?></p>
            <p><strong><?php esc_html_e('Solution:', 'pnscode-social-login-and-register'); ?></strong></p>
            <ul>
                <li><?php esc_html_e('Make sure the OAuth consent screen is properly configured', 'pnscode-social-login-and-register'); ?></li>
                <li><?php esc_html_e('For testing, add your email to the test users list', 'pnscode-social-login-and-register'); ?></li>
                <li><?php esc_html_e('For production, submit your app for verification', 'pnscode-social-login-and-register'); ?></li>
            </ul>
        </div>
        
        <div class="pnscsolo-doc-issue">
            <h4><?php esc_html_e('Error: invalid_client', 'pnscode-social-login-and-register'); ?></h4>
            <p><strong><?php esc_html_e('Cause:', 'pnscode-social-login-and-register'); ?></strong> <?php esc_html_e('Client ID or Client Secret is incorrect.', 'pnscode-social-login-and-register'); ?></p>
            <p><strong><?php esc_html_e('Solution:', 'pnscode-social-login-and-register'); ?></strong></p>
            <ul>
                <li><?php esc_html_e('Double-check your Client ID and Client Secret for typos', 'pnscode-social-login-and-register'); ?></li>
                <li><?php esc_html_e('Make sure there are no extra spaces before or after the credentials', 'pnscode-social-login-and-register'); ?></li>
                <li><?php esc_html_e('If needed, regenerate the credentials in Google Console', 'pnscode-social-login-and-register'); ?></li>
            </ul>
        </div>
    </div>
    
    <div class="pnscsolo-doc-footer">
        <a href="<?php echo esc_url(admin_url('admin.php?page=pnscsolo-social-login-providers&provider=google')); ?>" class="button button-primary button-large">
            <?php esc_html_e('Configure Google Settings', 'pnscode-social-login-and-register'); ?>
        </a>
        <a href="https://console.cloud.google.com/apis/credentials" target="_blank" class="button button-secondary button-large">
            <?php esc_html_e('Open Google Cloud Console', 'pnscode-social-login-and-register'); ?>
        </a>
    </div>
</div>
