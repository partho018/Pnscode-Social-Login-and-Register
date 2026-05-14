=== Pnscode Social Login and Register ===
Contributors: partho800
Tags: social login, registration, google login, facebook login, form builder
Requires at least: 6.0
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Social login (Google, Facebook, X, LinkedIn, GitHub) and registration form builder with brute force protection, email verification & WooCommerce support.

== Description ==

Social Login & Custom Registration is the ultimate solution for simplifying user registration and login on your WordPress site. With a powerful drag-and-drop form builder and support for major social providers, you can create a seamless user experience that increases conversion rates.

= Key Features =

* **Social Login & Registration**: Users can log in or register with Google, Facebook, X (Twitter), LinkedIn, and GitHub.
* **Custom Registration Form Builder**: Build professional registration forms with custom fields (text, email, password, select, etc.).
* **Security & Protection**: Built-in brute force protection and Google reCAPTCHA v3 support.
* **Email Verification**: Ensure user authenticity with mandatory email verification before account activation.
* **WooCommerce Integration**: Seamlessly adds social login buttons to WooCommerce login, registration, and checkout pages.
* **Modern UI**: Stylish, indigo-themed admin interface and responsive frontend layouts.

== External Services ==

This plugin connects to third-party services to provide social login functionality and security features. Below is a detailed disclosure of all external services used:

= Google OAuth 2.0 =
* **Purpose**: Enables users to log in or register using their Google account
* **Data Sent**: User's Google profile information (email, name, profile picture) when user authorizes the connection
* **When**: Only when a user clicks "Login with Google" and authorizes access
* **Service Provider**: Google LLC
* **Terms of Service**: https://policies.google.com/terms
* **Privacy Policy**: https://policies.google.com/privacy

= Facebook Graph API =
* **Purpose**: Enables users to log in or register using their Facebook account
* **Data Sent**: User's Facebook profile information (email, name, profile picture) when user authorizes the connection
* **When**: Only when a user clicks "Login with Facebook" and authorizes access
* **Service Provider**: Meta Platforms, Inc.
* **Terms of Service**: https://www.facebook.com/terms.php
* **Privacy Policy**: https://www.facebook.com/privacy/explanation

= Google reCAPTCHA v3 =
* **Purpose**: Protects forms from spam and abuse
* **Data Sent**: User interactions, browser information, and IP address for bot detection
* **When**: When reCAPTCHA is enabled and users interact with login/registration forms
* **Service Provider**: Google LLC
* **Terms of Service**: https://policies.google.com/terms
* **Privacy Policy**: https://policies.google.com/privacy

= Twitter/X OAuth =
* **Purpose**: Enables login/registration via Twitter/X
* **Data Sent**: Public profile details and email address (if authorized)
* **Service Provider**: X Corp.
* **Privacy Policy**: https://twitter.com/en/privacy

= LinkedIn OAuth =
* **Purpose**: Enables login/registration via LinkedIn
* **Data Sent**: Full name, profile photo, and primary email address
* **Service Provider**: LinkedIn Corporation
* **Privacy Policy**: https://www.linkedin.com/legal/privacy-policy

= GitHub OAuth =
* **Purpose**: Enables login/registration via GitHub
* **Data Sent**: GitHub profile information and public email
* **Service Provider**: GitHub, Inc.
* **Privacy Policy**: https://docs.github.com/en/site-policy/privacy-policies/github-privacy-statement

== Installation ==

1. Upload the `pnscode-social-login-and-register` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to 'Social Login' menu in the admin dashboard to configure settings.

== Frequently Asked Questions ==

= Do I need API keys for social login? =
Yes, you will need to create developer applications for each social provider (Google, Facebook, etc.) to obtain Client IDs and Secrets. Detailed guides are provided within the plugin.

= Is it compatible with WooCommerce? =
Absolutely! The plugin automatically integrates with WooCommerce pages if WooCommerce is active.

== Screenshots ==

1. **Dashboard**: The clean, modern admin dashboard.
2. **Form Builder**: Drag-and-drop registration form builder.
3. **Social Providers**: Easy configuration for each social platform.
4. **Security Logs**: Monitor login activities and managed locked accounts.

== Changelog ==

= 1.0.0 =
* Initial release.
* Added 5 social providers.
* Integrated custom form builder.
* Added security monitoring and brute force protection.
