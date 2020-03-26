<?php

/**
 * @file
 * Forms for node_field module.
 */

/**
 * Add node fields to node edit form.
 *
 * Changed node fields will be saved on hook_node_submit.
 */
function node_field_node_form_edit(&$form, &$form_state) {
  $node = $form_state['node'];
  $node_fields = node_field_get_node_fields($node);
  if (empty($node_fields)) {
    return;
  }
  $form_state['node_fields'] = $node_fields;
  $form['node_fields'] = node_field_value_fields_form($node, $node_fields, $form_state);
}

/**
 * Build node fields form for node.
 *
 * @param object $node
 *   Node to build form for.
 * @param array $node_fields
 *   Node fields which will be on the form.
 *
 * @return array
 *   Returned node fields form.
 */
function node_field_value_fields_form($node, array $node_fields, &$form_state) {
  $form = [
    '#type' => 'fieldset',
    '#title' => t('Node fields'),
    '#tree' => TRUE,
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
  ];
  foreach ($node_fields as $field) {
    $widget = node_field_node_field_widget_form($field, $form_state);
    drupal_alter('node_field_value_form', $field, $widget, $node);
    $form[$field['id']] = $widget;
  }
  return $form;
}
