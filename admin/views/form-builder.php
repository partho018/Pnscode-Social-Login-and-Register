<?php
/**
 * Form Builder View
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!current_user_can('manage_options')) {
    wp_die(esc_html__('Access denied', 'pnscode-social-login-and-register'));
}

$pnscsolo_fields      = PNSCSOLO_Settings::get_registration_fields();
$pnscsolo_field_types = PNSCSOLO_Form_Builder::get_field_types();
?>

<div class="wrap pnscsolo-admin-wrap pnscsolo-form-builder-page">
    <div class="pnscsolo-admin-header">
        <h1><?php esc_html_e('Pnscode Form Builder', 'pnscode-social-login-and-register'); ?></h1>
        <p class="pnscsolo-admin-subtitle"><?php esc_html_e('Create and customize your custom registration fields with drag & drop.', 'pnscode-social-login-and-register'); ?></p>
    </div>
    
    <div class="pnscsolo-admin-grid">
        <div class="pnscsolo-admin-main-content">
            <div class="pnscsolo-card">
                <div class="pnscsolo-card-header">
                    <div class="pnscsolo-header-actions">
                        <button type="button" class="pnscsolo-btn pnscsolo-btn-secondary" id="pnscsolo-add-field">
                            <span class="dashicons dashicons-plus-alt"></span>
                            <?php esc_html_e('Add New Field', 'pnscode-social-login-and-register'); ?>
                        </button>
                    </div>
                    <div class="pnscsolo-header-actions">
                        <button type="button" class="pnscsolo-btn pnscsolo-btn-primary" id="pnscsolo-save-fields">
                            <span class="dashicons dashicons-saved"></span>
                            <?php esc_html_e('Save Form Configuration', 'pnscode-social-login-and-register'); ?>
                        </button>
                    </div>
                </div>
                
                <div class="pnscsolo-form-builder-notice" style="display:none; margin: 20px 25px 0;"></div>
                
                <div class="pnscsolo-form-fields-list" id="pnscsolo-form-fields-list">
                    <?php if (empty($pnscsolo_fields)): ?>
                        <div class="pnscsolo-no-fields">
                            <span class="dashicons dashicons-layout" style="font-size: 48px; width: 48px; height: 48px; color: #cbd5e1; margin-bottom: 15px;"></span>
                            <p><?php esc_html_e('No fields added yet. Click "Add New Field" to get started.', 'pnscode-social-login-and-register'); ?></p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($pnscsolo_fields as $pnscsolo_field): ?>
                            <?php include PNSCSOLO_PLUGIN_DIR . 'admin/views/partials/field-item.php'; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="pnscsolo-admin-sidebar">
            <div class="pnscsolo-card pnscsolo-sidebar-card">
                <div class="pnscsolo-card-header small">
                    <span class="dashicons dashicons-visibility"></span>
                    <h3><?php esc_html_e('Live Preview', 'pnscode-social-login-and-register'); ?></h3>
                    <span class="pnscsolo-live-dot"></span>
                </div>
                <div class="pnscsolo-card-body">
                    <div class="pnscsolo-form-preview-mini">
                        <div class="pnscsolo-preview-header">
                            <span class="pnscsolo-preview-dot"></span>
                            <span class="pnscsolo-preview-dot"></span>
                            <span class="pnscsolo-preview-dot"></span>
                        </div>
                        <div id="pnscsolo-preview-container">
                            <?php echo wp_kses_post(PNSCSOLO_Form_Builder::render_registration_form()); ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="pnscsolo-card pnscsolo-sidebar-card">
                <div class="pnscsolo-card-header small">
                    <span class="dashicons dashicons-info"></span>
                    <h3><?php esc_html_e('Quick Guide', 'pnscode-social-login-and-register'); ?></h3>
                </div>
                <div class="pnscsolo-card-body">
                    <ul class="pnscsolo-sidebar-links checklist">
                        <li><span class="dashicons dashicons-move"></span> <?php esc_html_e('Drag & drop to reorder', 'pnscode-social-login-and-register'); ?></li>
                        <li><span class="dashicons dashicons-edit"></span> <?php esc_html_e('Click edit to customize', 'pnscode-social-login-and-register'); ?></li>
                        <li><span class="dashicons dashicons-yes"></span> <?php esc_html_e('Enable/disable anytime', 'pnscode-social-login-and-register'); ?></li>
                        <li><span class="dashicons dashicons-trash"></span> <?php esc_html_e('Remove unused fields', 'pnscode-social-login-and-register'); ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Field Template -->
<script type="text/template" id="pnscsolo-field-template">
    <div class="pnscsolo-field-item" data-field-id="{{field_id}}">
        <div class="pnscsolo-field-header">
            <span class="pnscsolo-field-drag-handle dashicons dashicons-menu"></span>
            <span class="pnscsolo-field-title">{{field_label}}</span>
            <span class="pnscsolo-field-type-badge">{{field_type}}</span>
            <div class="pnscsolo-field-actions">
                <label class="pnscsolo-switch pnscsolo-field-toggle">
                    <input type="checkbox" class="pnscsolo-field-enabled" {{field_enabled}}>
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
                            <option value="<?php echo esc_attr($pnscsolo_type); ?>"><?php echo esc_html($pnscsolo_label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="pnscsolo-field-setting">
                    <label><?php esc_html_e('Field Label', 'pnscode-social-login-and-register'); ?></label>
                    <input type="text" class="pnscsolo-field-label" value="{{field_label}}">
                </div>
                
                <div class="pnscsolo-field-setting">
                    <label><?php esc_html_e('Placeholder', 'pnscode-social-login-and-register'); ?></label>
                    <input type="text" class="pnscsolo-field-placeholder" value="{{field_placeholder}}">
                </div>
                
                <div class="pnscsolo-field-setting">
                    <label>
                        <input type="checkbox" class="pnscsolo-field-required" {{field_required}}>
                        <?php esc_html_e('Required Field', 'pnscode-social-login-and-register'); ?>
                    </label>
                </div>
            </div>
        </div>
    </div>
</script>
