<?php
/**
 * @file
 * Hooks provided by the field_multiple_extended module.
 */

/**
 * Provides a way to alter this module's settings before any form altering.
 *
 * The field_multiple_extended module needs to fire last in
 * hook_field_attach_form() in order to modify the "max_delta" potentially
 * altered by other modules - ex. field_collection, paragraphs.
 * This limits the ability of other modules to modify the field_attach_form
 * base on this modules settings.
 *
 * @param array $settings
 *   An array instance settings used by field_multiple_extended.
 * @param array $context
 *   An array with the following:
 *   - "instance": The field instance information array.
 *   - "field": The field information array.
 *   Optional keys provided when entity is available:
 *   - "entity": The entity object.
 *   - "entity_type": The entity type.
 *   - "items": Array of default values stored on the entity for this field.
 *   - "langcode": The language associated with $items.
 */
function hook_field_multiple_extended_field_instance_settings_alter(&$settings, array $context) {
  // Limit trial users to only 1 item.
  $trial_limit = 1;
  if (isset($settings['max_allowed']) && $settings['max_allowed'] > $trial_limit && user_access('trial access')) {
    $settings['max_allowed'] = $trial_limit;
    if (isset($settings['min_required']) && $settings['min_required'] > $trial_limit) {
      $settings['min_required'] = $trial_limit;
    }
  }
}

/**
 * Provides a way to alter whether a field instance is supported.
 *
 * @param bool $supported
 *   TRUE if this field instance is supported by field_multiple_extended.
 * @param array $instance
 *   The field instance information array.
 */
function hook_field_multiple_extended_field_instance_is_supported_alter(&$supported, array $instance) {
  if ($instance['widget']['type'] == 'my_custom_widget') {
    $supported = TRUE;
  }
}
