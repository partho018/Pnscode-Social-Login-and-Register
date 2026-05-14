<?php
/**
 * Field Item Partial
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="pnscsolo-field-item" data-field-id="<?php echo esc_attr($pnscsolo_field['id']); ?>">
    <div class="pnscsolo-field-header">
        <span class="pnscsolo-field-drag-handle dashicons dashicons-menu"></span>
        <span class="pnscsolo-field-title"><?php echo esc_html($pnscsolo_field['label']); ?></span>
        <span class="pnscsolo-field-type-badge"><?php echo esc_html($pnscsolo_field_types[$pnscsolo_field['type']] ?? $pnscsolo_field['type']); ?></span>
        <div class="pnscsolo-field-actions">
            <label class="pnscsolo-switch pnscsolo-field-toggle">
                <input type="checkbox" class="pnscsolo-field-enabled" <?php checked($pnscsolo_field['enabled'] ?? false); ?>>
                <span class="pnscsolo-slider"></span>
            </label>
            <button type="button" class="pnscsolo-field-edit" title="<?php esc_html_e('Edit', 'pnscode-social-login-and-register'); ?>">
                <span class="dashicons dashicons-edit"></span>
            </button>
            <button type="button" class="pnscsolo-field-delete" title="<?php esc_html_e('Delete', 'pnscode-social-login-and-register'); ?>">
                <span class="dashicons dashicons-trash"></span>
            </button>
        </div>
    </div>
    <div class="pnscsolo-field-body" style="display:none;">
        <div class="pnscsolo-field-settings">
            <div class="pnscsolo-field-setting">
                <label><?php esc_html_e('Field Type', 'pnscode-social-login-and-register'); ?></label>
                <select class="pnscsolo-field-type">
                    <?php foreach ($pnscsolo_field_types as $pnscsolo_type => $pnscsolo_label): ?>
                        <option value="<?php echo esc_attr($pnscsolo_type); ?>" <?php selected($pnscsolo_field['type'], $pnscsolo_type); ?>><?php echo esc_html($pnscsolo_label); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="pnscsolo-field-setting">
                <label><?php esc_html_e('Field Label', 'pnscode-social-login-and-register'); ?></label>
                <input type="text" class="pnscsolo-field-label" value="<?php echo esc_attr($pnscsolo_field['label']); ?>">
            </div>
            
            <div class="pnscsolo-field-setting">
                <label><?php esc_html_e('Placeholder', 'pnscode-social-login-and-register'); ?></label>
                <input type="text" class="pnscsolo-field-placeholder" value="<?php echo esc_attr($pnscsolo_field['placeholder'] ?? ''); ?>">
            </div>
            
            <div class="pnscsolo-field-setting">
                <label>
                    <input type="checkbox" class="pnscsolo-field-required" <?php checked($pnscsolo_field['required'] ?? false); ?>>
                    <?php esc_html_e('Required Field', 'pnscode-social-login-and-register'); ?>
                </label>
            </div>
        </div>
    </div>
</div>
