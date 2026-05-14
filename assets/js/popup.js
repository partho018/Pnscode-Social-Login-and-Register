/**
 * Popup Modal JavaScript
 * Handles login/register popup functionality
 */

(function ($) {
    'use strict';

    $(document).ready(function () {

        // Open popup automatically if on My Account page and not logged in
        if ($('body').hasClass('pnscsolo-force-popup') || ($('body').hasClass('woocommerce-account') && !$('body').hasClass('logged-in'))) {
            // Check if there is a login/register form on page
            if ($('.woocommerce-form-login').length || $('.woocommerce-form-register').length) {
                // Hide default WC forms to avoid clutter
                $('.woocommerce-MyAccount-content, .woocommerce-form-login, .woocommerce-form-register').css('opacity', '0.1');
                openLoginPopup();
            }
        }

        // Open popup when clicking Account links (if not logged in)
        $(document).on('click', 'a[href*="my-account"], a[href*="myaccount"], .pnscsolo-login-popup-trigger', function (e) {
            // Check if user is already logged in
            if ($('body').hasClass('logged-in')) {
                return true;
            }

            // Prevent default navigation
            e.preventDefault();
            openLoginPopup();
        });

        // Tab switching
        $('.pnscsolo-popup-tab').on('click', function () {
            var tab = $(this).data('tab');

            // Update tabs
            $('.pnscsolo-popup-tab').removeClass('active');
            $(this).addClass('active');

            // Update content
            $('.pnscsolo-popup-tab-content').removeClass('active');
            $('#pnscsolo-popup-' + tab).addClass('active');
        });

        // Close popup
        $('.pnscsolo-popup-close').on('click', function () {
            closeLoginPopup();
        });

        // Close on overlay click
        $('.pnscsolo-popup-overlay').on('click', function (e) {
            if ($(e.target).hasClass('pnscsolo-popup-overlay')) {
                closeLoginPopup();
            }
        });

        // Close on ESC key
        $(document).on('keydown', function (e) {
            if (e.key === 'Escape' && $('#pnscsolo-login-popup').is(':visible')) {
                closeLoginPopup();
            }
        });

        // Handle popup login form submission
        $('#pnscsolo-popup-loginform').on('submit', function (e) {
            e.preventDefault();

            var $form = $(this);
            var $button = $form.find('.pnscsolo-submit-btn');
            var $messages = $form.find('.pnscsolo-form-messages');

            // Disable button
            $button.prop('disabled', true);
            $button.find('.pnscsolo-btn-text').hide();
            $button.find('.pnscsolo-btn-loader').show();

            // Get form data
            var formData = {
                action: 'PNSCSOLO_popup_login',
                log: $form.find('[name="log"]').val(),
                pwd: $form.find('[name="pwd"]').val(),
                rememberme: $form.find('[name="rememberme"]').is(':checked') ? 'forever' : '',
                redirect_to: $form.find('[name="redirect_to"]').val(),
                nonce: pnscsoloPopup.nonce
            };

            // Submit via AJAX
            $.ajax({
                url: pnscsoloPopup.ajaxurl,
                type: 'POST',
                data: formData,
                dataType: 'text', // Handle response as text first for robustness
                success: function (textResponse) {
                    var response;
                    try {
                        response = JSON.parse(textResponse.trim());
                    } catch (e) {
                        console.error('JSON Parse Error:', e, textResponse);
                        $messages.removeClass('success').addClass('error').html('Invalid response. Check console.').show();
                        $button.prop('disabled', false); $button.find('.pnscsolo-btn-text').show(); $button.find('.pnscsolo-btn-loader').hide();
                        return;
                    }

                    if (response && response.success) {
                        // Show success message
                        $messages.removeClass('error').addClass('success').html(response.data.message).show();

                        // Redirect after 1 second
                        setTimeout(function () {
                            if (response.data.redirect) {
                                window.location.href = response.data.redirect;
                            } else {
                                window.location.reload();
                            }
                        }, 1000);
                    } else {
                        // Show error from response
                        var errorMsg = (response && response.data && response.data.message) ? response.data.message : 'Login failed. Please try again.';
                        $messages.removeClass('success').addClass('error').html(errorMsg).show();

                        // Re-enable button
                        $button.prop('disabled', false);
                        $button.find('.pnscsolo-btn-text').show();
                        $button.find('.pnscsolo-btn-loader').hide();
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', status, error, xhr.responseText);
                    $messages.removeClass('success').addClass('error').html('An error occurred. Check console.').show();

                    // Re-enable button
                    $button.prop('disabled', false);
                    $button.find('.pnscsolo-btn-text').show();
                    $button.find('.pnscsolo-btn-loader').hide();
                }
            });
        });
        // Toggle inline forgot password form in popup
        $(document).on('click', '.pnscsolo-toggle-forgot', function (e) {
            e.preventDefault();
            $(this).closest('form').find('.pnscsolo-forgot-form-container').slideToggle();
        });

        // Handle popup forgot password submission
        $(document).on('click', '.pnscsolo-reset-password-popup-btn', function (e) {
            e.preventDefault();

            var $btn = $(this);
            var $container = $btn.closest('.pnscsolo-forgot-form-container');
            var $emailInput = $container.find('.pnscsolo-forgot-email');
            var $messages = $container.find('.pnscsolo-forgot-messages');
            var user_login = $emailInput.val();

            if (!user_login) {
                $messages.removeClass('success').addClass('error').text('Please enter your email or username.').show();
                return;
            }

            // UI feedback
            $btn.prop('disabled', true);
            $btn.find('.pnscsolo-btn-text').hide();
            $btn.find('.pnscsolo-btn-loader').show();
            $messages.hide();

            $.ajax({
                url: pnscsoloPopup.ajaxurl,
                type: 'POST',
                data: {
                    action: 'PNSCSOLO_forgot_password',
                    user_login: user_login,
                    nonce: pnscsoloPopup.nonce
                },
                success: function (response) {
                    if (response.success) {
                        $messages.removeClass('error').addClass('success').html(response.data.message).show();
                        $emailInput.val('');
                    } else {
                        $messages.removeClass('success').addClass('error').html(response.data.message).show();
                    }
                },
                complete: function () {
                    $btn.prop('disabled', false);
                    $btn.find('.pnscsolo-btn-text').show();
                    $btn.find('.pnscsolo-btn-loader').hide();
                }
            });
        });

    });

    // Open popup function
    function openLoginPopup() {
        $('#pnscsolo-login-popup').fadeIn(300);
        $('body').css('overflow', 'hidden');
    }

    // Close popup function
    function closeLoginPopup() {
        $('#pnscsolo-login-popup').fadeOut(300);
        $('body').css('overflow', '');
    }

    // Make functions globally available
    window.slrOpenLoginPopup = openLoginPopup;
    window.slrCloseLoginPopup = closeLoginPopup;

})(jQuery);
