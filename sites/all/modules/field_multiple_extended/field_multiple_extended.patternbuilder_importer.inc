<?php
/**
 * @file
 * Pattern Builder importer hooks.
 */

/**
 * Implements hook_patternbuilder_importer_field_import_alter().
 */
function field_multiple_extended_patternbuilder_importer_field_import_alter(&$field_import, array $context) {
  $settings = variable_get('field_multiple_extended_pb_importer', field_multiple_extended_patternbuilder_importer_default_settings());
  $cardinality = isset($field_import['base']['cardinality']) ? $field_import['base']['cardinality'] : FIELD_CARDINALITY_UNLIMITED;

  if (!empty($settings['enabled']) && ($cardinality == FIELD_CARDINALITY_UNLIMITED || $cardinality > 1)) {
    $custom_settings = array(
      'min_required' => NULL,
      'max_allowed' => NULL,
    ) + $settings;

    // Set min required.
    $min_required = NULL;
    $min_items = pbi_utility::property_min_items($context['property']);
    if (isset($min_items)) {
      $min_required = $min_items;
    }

    // Determine max allowed.
    $max_allowed = NULL;
    if ($cardinality == FIELD_CARDINALITY_UNLIMITED) {
      // If set to unlimited, check if there is a max.
      $max_items = pbi_utility::property_max_items($context['property']);
      if (isset($max_items) && $max_items != FIELD_CARDINALITY_UNLIMITED && $max_items > 1) {
        $max_allowed = $max_items;
      }
    }
    else {
      // Set max allowed to field cardinality.
      // Patternbuilder importer sets cardinality = property maxItems.
      $max_allowed = $cardinality;
    }

    // Ensure min_required <= max_allowed.
    if ($max_allowed && $min_required && $min_required > $max_allowed) {
      $max_allowed = $min_required;
    }

    // Update settings.
    $custom_settings['min_required'] = $min_required;
    $custom_settings['max_allowed'] = $max_allowed;

    // Add settings to the instance.
    $field_import['instance']['settings']['field_multiple_extended'] = $custom_settings;

    // Ensure field cardinality is set to unlimited.
    $field_import['base']['cardinality'] = FIELD_CARDINALITY_UNLIMITED;
  }
  else {
    // Disabled due to settings or cardinality = 1.
    $settings['enabled'] = FALSE;
    $field_import['instance']['settings']['field_multiple_extended'] = $settings;
  }
}
