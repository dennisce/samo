<?php

/**
 * @file
 * Node fields forms.
 */

/**
 * Node field form.
 */
function node_field_node_field_form($form, &$form_state, $node) {
  $form_state['wrapper_id'] = 'node-field-node-field-form';
  $form_state['node'] = $node;
  $node_fields = node_field_get_node_fields($node);
  $form_state['node_fields'] = $node_fields;
  $node_edit_url = url('node/' . $node->nid . '/edit');
  $form['description'] = [
    '#type' => 'markup',
    '#markup' => t('Configure Node Fields here. Then set values on <a href="@url">node edit page</a>.', ['@url' => $node_edit_url]),
  ];
  // Building node fields form.
  $form['node_fields'] = [
    '#type' => 'container',
    '#attributes' => [
      'id' => $form_state['wrapper_id'],
    ],
    '#tree' => TRUE,
    'fields' => [
      '#tree' => TRUE,
    ],
  ];
  $form['node_fields']['#theme'] = 'node_field_node_field_page_form_fields';
  // Add existing fields form elements.
  _node_field_node_field_page_fields_form($form, $form_state, $node_fields);
  // Add new field form elements.
  _node_field_node_field_page_new_field_form($form, $form_state);
  $form['actions'] = [
    '#type' => 'actions',
  ];
  $form['actions']['submit'] = [
    '#type' => 'submit',
    '#value' => t('Save'),
  ];
  return $form;
}

/**
 * Submit handler for "Save" button.
 */
function node_field_node_field_form_submit($form, &$form_state) {
  // Update existing fields.
  if (isset($form_state['values']['node_fields']['fields'])) {
    foreach ($form_state['values']['node_fields']['fields'] as $field) {
      $node_field = node_field_load_node_field($field['id']);
      $node_field = array_merge($node_field, $field);
      node_field_update_node_field($node_field);
    }
  }
  // Create new field.
  $new_node_field = $form_state['values']['node_fields']['new'];
  if (!empty($new_node_field['title']) && !empty($new_node_field['machine_name'])) {
    // Add default values.
    $new_node_field += [
      'nid' => $form_state['node']->nid,
      'show_title' => TRUE,
      'hidden' => FALSE,
    ];
    if (node_field_create_node_field($new_node_field)) {
      drupal_set_message(t('Field %name successfully created.', ['%name' => $new_node_field['title']]));
    }
    else {
      drupal_set_message(t("Field %name wasn't created.", ['%name' => $new_node_field['title']]), 'error');
    }
  }
}

/**
 * Fields form.
 */
function _node_field_node_field_page_fields_form(&$form, &$form_state, $node_fields) {
  if (empty($node_fields)) {
    return;
  }
  $delta_max = ceil(count($node_fields) / 2);
  foreach ($node_fields as $node_field) {
    $delta_max = max($delta_max, abs($node_field['weight']));
  }
  $form_state['delta_max'] = $delta_max;
  foreach ($node_fields as $key => $node_field) {
    $field_form = _node_field_node_field_page_field_form($form_state, $node_field);
    drupal_alter('node_field_field_form', $field_form, $form_state);
    $form['node_fields']['fields'][$node_field['id']] = $field_form;
  }
}

/**
 * Form elements for single node field.
 */
function _node_field_node_field_page_field_form(&$form_state, $node_field) {
  // Building node field form.
  $form = [
    '#type' => 'fieldset',
    '#title' => filter_xss($node_field['title']),
  ];
  $form['id'] = [
    '#type' => 'value',
    '#value' => $node_field['id'],
  ];
  $form['nid'] = [
    '#type' => 'value',
    '#value' => $node_field['nid'],
    '#access' => FALSE,
  ];
  $form['title'] = [
    '#markup' => filter_xss_admin($node_field['title']),
    '#title' => t('Title'),
    '#title_display' => 'invisible',
  ];
  $form['weight'] = [
    '#type' => 'weight',
    '#title' => t('Weight'),
    '#title_display' => 'invisible',
    '#default_value' => $node_field['weight'],
    '#delta' => $form_state['delta_max'],
  ];
  $form['type'] = [
    '#markup' => filter_xss_admin($node_field['type']),
    '#title' => t('Type'),
    '#title_display' => 'invisible',
  ];
  $form['operations'] = [
    '#type' => 'container',
    '#title' => 'operations',
  ];
  $destination = drupal_get_destination();
  $link_attributes = [
    'query' => [
      'destination' => $destination['destination'],
    ],
  ];
  $operations = [];
  $attributes = ['class' => ['edit']];
  $link_attributes['attributes'] = $attributes;
  $operations[] = l(t('edit'), 'node/' . $form['nid']['#value'] . '/node-field/' . $node_field['id'] . '/edit', $link_attributes);
  $attributes = ['class' => ['delete']];
  $link_attributes['attributes'] = $attributes;
  $operations[] = l(t('delete'), 'node/' . $form['nid']['#value'] . '/node-field/' . $node_field['id'] . '/delete', $link_attributes);
  $form['operations']['op'] = [
    '#markup' => theme('item_list', ['items' => $operations]),
  ];
  return $form;
}

/**
 * Form element for new node field.
 */
function _node_field_node_field_page_new_field_form(&$form, $form_state) {
  // Building new field form.
  $form['node_fields']['new'] = [
    '#tree' => TRUE,
  ];
  $form['node_fields']['new']['title'] = [
    '#type' => 'textfield',
    '#title' => t('Add new field'),
    '#description' => t('Label'),
  ];

  $delta_max = ceil(count($form_state['node_fields']) / 2);
  if (!empty($form_state['node_fields'])) {
    foreach ($form_state['node_fields'] as $node_field) {
      $delta_max = max($delta_max, abs($node_field['weight']));
    }
  }

  $form['node_fields']['new']['weight'] = [
    '#type' => 'weight',
    '#title' => t('Weight'),
    '#title_display' => 'invisible',
    '#default_value' => $delta_max,
    '#delta' => $delta_max,
  ];
  $form['node_fields']['new']['type'] = [
    '#type' => 'select',
    '#title' => t('Type'),
    '#title_display' => 'invisible',
    '#description' => t('Field type'),
    '#options' => node_field_node_field_types_options(),
  ];
  $form['node_fields']['new']['machine_name'] = [
    '#type' => 'machine_name',
    '#title' => t('Machine name'),
    '#description' => t('It must only contain lowercase letters, numbers and hyphens.'),
    '#maxlength' => 256,
    '#required' => FALSE,
    '#machine_name' => [
      'exists' => 'node_field_machine_name_check',
      'source' => ['node_fields', 'new', 'title'],
    ],
  ];
}

/**
 * Get node field types options.
 *
 * @return array
 *   Associative array of options.
 */
function node_field_node_field_types_options() {
  $options = [];
  // Populate node field types options.
  $node_field_types = node_field_types_info();
  foreach ($node_field_types as $node_field_type) {
    $options[$node_field_type['type']] = $node_field_type['label'];
  }
  return $options;
}

/**
 * Delete node_field.
 *
 * @param object $node
 *   Node where node_field belongs.
 * @param array $node_field
 *   Node field to delete.
 *
 * @return array
 *   Returned the form.
 */
function node_field_node_field_delete_form($form, &$form_state, $node, array $node_field) {
  $destination = 'node/' . $node->nid . '/node-field';
  $question = t('Are you sure you want to delete node field %title', ['%title' => $node_field['title']]);
  $form = confirm_form($form, $question, $destination);
  $form_state['node_field'] = $node_field;
  return $form;
}

/**
 * Submit callback for node_field_node_field_delete_form().
 */
function node_field_node_field_delete_form_submit($form, &$form_state) {
  if ($form_state['values']['confirm']) {
    $node_field = $form_state['node_field'];
    $is_deleted = node_field_delete_node_field($node_field);
    if ($is_deleted) {
      drupal_set_message(t('Node field %title successfully removed.', ['%title' => $node_field['title']]));
    }
    else {
      drupal_set_message(t('Node field %title can not be removed.', ['%title' => $node_field['title']]), 'error');
    }
  }
}

/**
 * Settings form for single node field.
 *
 * @param object $node
 *   Node where node_field belongs.
 * @param array $node_field
 *   Node field to edit.
 *
 * @return array
 *   Returned the form.
 */
function node_field_node_field_edit_form($form, &$form_state, $node, array $node_field) {
  $form_state['node_fields'][] = $node_field;
  $form_state['node'] = $node;
  // Building node field form.
  $form['id'] = [
    '#type' => 'value',
    '#value' => $node_field['id'],
  ];
  $form['nid'] = [
    '#type' => 'value',
    '#value' => $node_field['nid'],
    '#access' => FALSE,
  ];
  $form['title'] = [
    '#type' => 'textfield',
    '#title' => t('Label'),
    '#default_value' => $node_field['title'],
    '#length' => 256,
  ];
  $form['machine_name'] = [
    '#type' => 'machine_name',
    '#title' => t('Machine name'),
    '#default_value' => $node_field['machine_name'],
    '#description' => t('It must only contain lowercase letters, numbers and hyphens.'),
    '#maxlength' => 256,
    '#machine_name' => [
      'exists' => 'node_field_machine_name_check',
      'source' => ['title'],
    ],
  ];
  $form['show_title'] = [
    '#type' => 'checkbox',
    '#title' => t('Show title'),
    '#title_display' => 'before',
    '#field_suffix' => t('Yes'),
    '#default_value' => $node_field['show_title'],
  ];
  $form['hidden'] = [
    '#type' => 'checkbox',
    '#title' => t('Hidden'),
    '#title_display' => 'before',
    '#field_suffix' => t('Yes'),
    '#default_value' => $node_field['hidden'],
  ];
  $form['settings'] = node_field_node_field_settings_form($node_field);
  $form['settings']['#tree'] = TRUE;
  $form['actions'] = [
    '#type' => 'actions',
  ];
  $form['actions']['submit'] = [
    '#type' => 'submit',
    '#value' => t('Save'),
  ];
  return $form;
}

/**
 * Submit callback for node_field_node_field_edit_form().
 */
function node_field_node_field_edit_form_submit($form, &$form_state) {
  // Update field.
  $field = $form_state['values'];
  $node_field = node_field_load_node_field($field['id']);
  $node_field = array_merge($node_field, $field);
  node_field_update_node_field($node_field);
}

/**
 * Callback function for machine_name field.
 */
function node_field_machine_name_check($value) {
  $nid = arg(1);
  // TODO: Move to node_field.db.inc.
  $name_exists = db_query_range("SELECT 1 FROM {node_field} WHERE machine_name = :name AND nid = :nid", 0, 1, [
    ':name' => $value,
    ':nid' => $nid,
  ])->fetchField();
  return $name_exists;
}
