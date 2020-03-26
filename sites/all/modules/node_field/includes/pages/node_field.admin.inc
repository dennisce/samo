<?php

/**
 * @file
 * Admin interface for Node Field module.
 */

/**
 * Admin form.
 */
function node_field_admin_settings_form($form, &$form_state) {
  // Return default system settings form.
  $form = system_settings_form($form);
  return $form;
}
