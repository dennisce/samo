<?php
/**
 * @file
 * Field Multiple Extended hooks.
 */

/**
 * Implements hook_field_multiple_extended_field_instance_is_supported_alter().
 */
function field_multiple_extended_field_multiple_extended_field_instance_is_supported_alter(&$supported, array $instance) {
  if (!$supported) {
    $field = field_info_field($instance['field_name']);
    if ($field['type'] == 'paragraphs' && $instance['widget']['type'] != 'paragraphs_hidden') {
      // Paragraphs use a custom multiple value form but are supported.
      $supported = TRUE;
    }
  }
}

/**
 * Implements hook_field_multiple_extended_field_instance_settings_alter().
 */
function field_multiple_extended_field_multiple_extended_field_instance_settings_alter(&$settings, array $context) {
  if (!empty($settings['exclude_extra_item'])) {
    switch ($context['field']['type']) {
      case 'paragraphs':
        // Paragraphs widgets are assumed to handle their own exta item.
        // Example: paragraphs_embed widget.
        $settings['exclude_extra_item'] = FALSE;
        break;

      case 'field_collections':
        // Field collections have their own setting to hide blanks.
        if (field_collection_hide_blank_items($context['field'])) {
          $settings['exclude_extra_item'] = FALSE;
        }
        break;
    }
  }
}
