/**
 * Admin JavaScript
 * Social Login & Registration Plugin
 */

(function ($) {
    'use strict';

    $(document).ready(function () {

        /**
         * Form Builder - Sortable fields
         */
        if ($('#pnscsolo-form-fields-list').length) {
            $('#pnscsolo-form-fields-list').sortable({
                handle: '.pnscsolo-field-drag-handle',
                placeholder: 'pnscsolo-sortable-placeholder',
                update: function () {
                    // Field order changed
                }
            });
        }

        /**
         * Form Builder - Toggle field edit
         */
        $(document).on('click', '.pnscsolo-field-edit', function (e) {
            e.preventDefault();
            var $fieldItem = $(this).closest('.pnscsolo-field-item');
            var $fieldBody = $fieldItem.find('.pnscsolo-field-body');

            $fieldBody.slideToggle(300);
        });

        /**
         * Form Builder - Delete field
         */
        $(document).on('click', '.pnscsolo-field-delete', function (e) {
            e.preventDefault();

            if (!confirm('Are you sure you want to delete this field?')) {
                return;
            }

            var $fieldItem = $(this).closest('.pnscsolo-field-item');
            var fieldId = $fieldItem.data('field-id');

            // Send AJAX request to delete
            $.ajax({
                url: pnscsoloAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'PNSCSOLO_delete_form_field',
                    nonce: pnscsoloAdmin.nonce,
                    field_id: fieldId
                },
                success: function (response) {
                    if (response.success) {
                        $fieldItem.slideUp(300, function () {
                            $(this).remove();
                            updatePreview();
                        });
                        showNotice('success', response.data.message);
                    } else {
                        showNotice('error', response.data.message);
                    }
                },
                error: function () {
                    showNotice('error', 'An error occurred. Please try again.');
                }
            });
        });

        /**
         * Form Builder - Add new field
         */
        $('#pnscsolo-add-field').on('click', function (e) {
            e.preventDefault();

            $.ajax({
                url: pnscsoloAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'PNSCSOLO_add_form_field',
                    nonce: pnscsoloAdmin.nonce
                },
                success: function (response) {
                    if (response.success) {
                        var template = $('#pnscsolo-field-template').html();
                        var field = response.data.field;

                        var html = template
                            .replace(/{{field_id}}/g, field.id)
                            .replace(/{{field_label}}/g, field.label)
                            .replace(/{{field_placeholder}}/g, field.placeholder || '')
                            .replace(/{{field_type}}/g, field.type.charAt(0).toUpperCase() + field.type.slice(1))
                            .replace(/{{field_enabled}}/g, field.enabled ? 'checked' : '')
                            .replace(/{{field_required}}/g, field.required ? 'checked' : '');

                        $('.pnscsolo-no-fields').remove();
                        $('#pnscsolo-form-fields-list').append(html);

                        showNotice('success', response.data.message);
                        updatePreview();
                    } else {
                        showNotice('error', response.data.message);
                    }
                },
                error: function () {
                    showNotice('error', 'An error occurred. Please try again.');
                }
            });
        });

        /**
         * Form Builder - Save all fields
         */
        $('#pnscsolo-save-fields').on('click', function (e) {
            e.preventDefault();

            var fields = [];
            var order = 1;

            $('#pnscsolo-form-fields-list .pnscsolo-field-item').each(function () {
                var $item = $(this);

                var field = {
                    id: $item.data('field-id'),
                    type: $item.find('.pnscsolo-field-type').val(),
                    label: $item.find('.pnscsolo-field-label').val(),
                    placeholder: $item.find('.pnscsolo-field-placeholder').val(),
                    required: $item.find('.pnscsolo-field-required').is(':checked'),
                    enabled: $item.find('.pnscsolo-field-enabled').is(':checked'),
                    order: order++
                };

                fields.push(field);
            });

            $.ajax({
                url: pnscsoloAdmin.ajaxurl,
                type: 'POST',
                data: {
                    action: 'PNSCSOLO_save_form_fields',
                    nonce: pnscsoloAdmin.nonce,
                    fields: JSON.stringify(fields)
                },
                success: function (response) {
                    if (response.success) {
                        showNotice('success', response.data.message);
                        updatePreview();
                    } else {
                        showNotice('error', response.data.message);
                    }
                },
                error: function () {
                    showNotice('error', 'An error occurred. Please try again.');
                }
            });
        });

        /**
         * Form Builder - Update field title on label change
         */
        $(document).on('input', '.pnscsolo-field-label', function () {
            var $fieldItem = $(this).closest('.pnscsolo-field-item');
            var newLabel = $(this).val();
            $fieldItem.find('.pnscsolo-field-title').text(newLabel);
        });

        /**
         * Copy to clipboard functionality
         */
        $('.pnscsolo-copy-btn').on('click', function (e) {
            e.preventDefault();

            var text = $(this).data('clipboard-text');
            var $btn = $(this);
            var originalText = $btn.text();

            // Create temporary input
            var $temp = $('<input>');
            $('body').append($temp);
            $temp.val(text).select();
            document.execCommand('copy');
            $temp.remove();

            // Show feedback
            $btn.text('Copied!');
            setTimeout(function () {
                $btn.text(originalText);
            }, 2000);
        });

        /**
         * Show admin notice
         */
        function showNotice(type, message) {
            var $notice = $('.pnscsolo-form-builder-notice');

            $notice
                .removeClass('success error')
                .addClass(type)
                .text(message)
                .show();

            setTimeout(function () {
                $notice.fadeOut();
            }, 5000);
        }

        /**
         * Update form preview
         */
        function updatePreview() {
            // You can implement live preview update here
            // For now, we'll just reload the preview section
            // In a full implementation, you'd regenerate the form HTML
        }

        /**
         * Social Providers - Live Button Text Preview
         */
        $(document).on('input', '#button_text', function () {
            var val = $(this).val() || $(this).attr('placeholder');
            $('.pnscsolo-preview-text').text(val);
        });

    });

})(jQuery);
