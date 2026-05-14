/**
 * Frontend JavaScript
 * Social Login & Registration Plugin
 */

(function ($) {
    'use strict';

    $(document).ready(function () {

        /**
         * Handle registration form submission
         */
        $('#pnscsolo-registration-form').on('submit', function (e) {
            e.preventDefault();

            var $form = $(this);
            var $submitBtn = $form.find('.pnscsolo-submit-btn');
            var $btnText = $submitBtn.find('.pnscsolo-btn-text');
            var $btnLoader = $submitBtn.find('.pnscsolo-btn-loader');
            var $messages = $form.find('.pnscsolo-form-messages');

            // Disable submit button
            $submitBtn.prop('disabled', true);
            $btnText.hide();
            $btnLoader.show();

            // Clear previous messages
            $messages.removeClass('success error').hide().text('');

            // Prepare form data
            var formData = $form.serialize();

            // Send AJAX request
            $.ajax({
                url: pnscsoloAjax.ajaxurl,
                type: 'POST',
                data: formData + '&action=PNSCSOLO_register_user',
                dataType: 'text', // Get response as text first to handle potential whitespace/BOM
                success: function (textResponse) {
                    var response;
                    try {
                        // Trim the response to remove any leading/trailing whitespace or junk
                        response = JSON.parse(textResponse.trim());
                    } catch (e) {
                        console.error('JSON Parse Error:', e, textResponse);
                        $messages.addClass('error').text('Invalid server response. Please check console.').show();
                        $submitBtn.prop('disabled', false); $btnText.show(); $btnLoader.hide();
                        return;
                    }

                    if (response && response.success) {
                        $messages.addClass('success').text(response.data.message).show();
                        $form[0].reset();

                        if (response.data.redirect) {
                            setTimeout(function () {
                                window.location.href = response.data.redirect;
                            }, 1000);
                        }
                    } else {
                        var errorMsg = (response && response.data && response.data.message) ? response.data.message : 'Registration failed. Please try again.';
                        $messages.addClass('error').text(errorMsg).show();
                        $submitBtn.prop('disabled', false);
                        $btnText.show();
                        $btnLoader.hide();
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error:', status, error, xhr.responseText);
                    $messages.addClass('error').text('An error occurred on the server. Please check console.').show();
                    $submitBtn.prop('disabled', false);
                    $btnText.show();
                    $btnLoader.hide();
                }
            });
        });

        /**
         * Toggle inline forgot password form
         */
        $(document).on('click', '#pnscsolo-toggle-forgot', function (e) {
            e.preventDefault();
            $('#pnscsolo-forgot-form-container').slideToggle();
        });

        /**
         * Handle inline forgot password submission
         */
        $(document).on('click', '#pnscsolo-reset-password-btn', function (e) {
            e.preventDefault();

            var $btn = $(this);
            var $container = $('#pnscsolo-forgot-form-container');
            var $emailInput = $('#PNSCSOLO_user_email');
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
                url: pnscsoloAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'PNSCSOLO_forgot_password',
                    user_login: user_login,
                    nonce: pnscsoloAjax.nonce
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

        /**
         * Social login button click tracking (optional analytics)
         */
        $('.pnscsolo-social-btn').on('click', function () {
            var provider = $(this).data('provider');
            // You can add analytics tracking here
            console.log('Social login clicked:', provider);
        });

        /**
         * Handle standalone login form submission
         */
        $('#pnscsolo-custom-login-form').on('submit', function (e) {
            e.preventDefault();

            var $form = $(this);
            var $submitBtn = $form.find('#pnscsolo-login-submit');
            var $messages = $form.find('.pnscsolo-form-messages');
            var formData = $form.serialize();

            // UI feedback
            $submitBtn.prop('disabled', true);
            $submitBtn.find('.pnscsolo-btn-text').hide();
            $submitBtn.find('.pnscsolo-btn-loader').show();
            $messages.removeClass('success error').hide().text('');

            $.ajax({
                url: pnscsoloAjax.ajaxurl,
                type: 'POST',
                data: formData + '&action=PNSCSOLO_popup_login&nonce=' + $form.find('#pnscsolo_login_nonce').val(),
                dataType: 'text',
                success: function (textResponse) {
                    var response;
                    try {
                        response = JSON.parse(textResponse.trim());
                    } catch (e) {
                        console.error('Login JSON Parse Error:', e, textResponse);
                        $messages.addClass('error').text('Invalid server response. Check console.').show();
                        return;
                    }

                    if (response.success) {
                        $messages.addClass('success').text(response.data.message).show();
                        if (response.data.redirect) {
                            window.location.href = response.data.redirect;
                        }
                    } else {
                        $messages.addClass('error').text(response.data.message).show();
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Login AJAX Error:', status, error, xhr.responseText);
                    $messages.addClass('error').text('An error occurred. Please try again.').show();
                },
                complete: function () {
                    if (!$messages.hasClass('success')) {
                        $submitBtn.prop('disabled', false);
                        $submitBtn.find('.pnscsolo-btn-text').show();
                        $submitBtn.find('.pnscsolo-btn-loader').hide();
                    }
                }
            });
        });

    });

})(jQuery);
