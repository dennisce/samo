<?php

/**
 * @file
 * API and helpers functions for Node Field module.
 */

/**
 * Init $node->node_fields param.
 *
 * @param object $node
 *   Node to populate node fields for.
 */
function node_field_init_node_fields($node) {
  $node_fields = node_field_db_node_fields_select($node);
  drupal_alter('node_fields', $node, $node_fields);
  node_field_set_node_fields($node, $node_fields, FALSE);
}

/**
 * Set node_fields node param.
 *
 * @param object $node
 *   Node to set node fields for.
 * @param array $node_fields
 *   Array of node fields.
 * @param bool $set_changed_param
 *   Whether to set node_fields_changed flag in node or not.
 */
function node_field_set_node_fields($node, array $node_fields, $set_changed_param = TRUE) {
  $node->node_fields = $node_fields;
  if ($set_changed_param) {
    $node->node_fields_changed = TRUE;
  }
}

/**
 * Check whether node_fields of node were changed or not.
 *
 * @param object $node
 *   Node to check node_fields for.
 *
 * @return bool
 *   TRUE if node fields were changed. FALSE otherwise.
 */
function node_field_is_node_fields_changed($node) {
  if (!empty($node->node_fields_changed)) {
    return TRUE;
  }
  return FALSE;
}

/**
 * Get node fields for node.
 *
 * @param object $node
 *   Node to populate node_fields for.
 *
 * @return array
 *   Array of node fields.
 */
function node_field_get_node_fields($node) {
  // Init node node_fields param if it doesn't exist yet.
  if (!node_field_is_node_fields_init($node)) {
    node_field_init_node_fields($node);
  }
  // Return array of node fields if there are any.
  if (!empty($node->node_fields)) {
    return $node->node_fields;
  }
  // TODO: I think it's better to always store array in node_fields and return
  // empty array if there are no node_fields for node. This will allow us to
  // get rid of additional checks.
  return FALSE;
}

/**
 * Checks if node_fields attribute is set for node.
 *
 * @param object $node
 *   Node to check.
 *
 * @return bool
 *   Returns TRUE if node_field for node has been initialized already.
 */
function node_field_is_node_fields_init($node) {
  if (isset($node->node_fields)) {
    return TRUE;
  }
  return FALSE;
}

/**
 * Create a new node field.
 *
 * Field should contain all required attributes except field id.
 *
 * @param array $field
 *   Array of field attributes.
 *
 * @return bool
 *   Returns FALSE in case operation failed.
 */
function node_field_create_node_field(array &$field) {
  $result = node_field_db_field_insert($field);
  if ($result) {
    module_invoke_all('node_field_insert', $field);
  }
  return $result;
}

/**
 * Load node field.
 *
 * @param int $fid
 *   Id of field to load.
 *
 * @return array
 *   Returns array containing all field attributes or FALSE in case field not
 *   found.
 */
function node_field_load_node_field($fid) {
  $node_field = node_field_db_field_select(['id' => $fid]);
  if ($node_field) {
    module_invoke_all('node_field_load', $node_field);
    $node_field = array_pop($node_field);
    return $node_field;
  }
  return FALSE;
}

/**
 * Update existing node field. Input array must contain id of field.
 *
 * @param array $field
 *   Array of node field attributes.
 *
 * @return bool
 *   Returns FALSE in case operation failed.
 */
function node_field_update_node_field(array &$field) {
  $result = node_field_db_field_update($field);
  if ($result) {
    module_invoke_all('node_field_update', $field);
  }
  return $result;
}

/**
 * Delete node field. Input array must contain id of field.
 *
 * @param array $field
 *   Array of node field attributes.
 *
 * @return bool
 *   Returns TRUE on success, FALSE on failure.
 */
function node_field_delete_node_field(array $field) {
  module_invoke_all('node_field_delete', $field);
  $values = [
    'id' => $field['id'],
  ];
  $result = node_field_db_field_delete($values);
  return $result;
}

/**
 * Delete all node fields of node.
 *
 * @param object $node
 *   Node to delete node field of.
 *
 * @return bool
 *   Returns TRUE on success, FALSE on failure.
 */
function node_field_delete_node_fields($node) {
  $values = [
    'nid' => $node->nid,
  ];
  $result = node_field_db_field_delete($values);
  return $result;
}

/**
 * Get settings from node field.
 *
 * @param array $node_field
 *   Array of node field attributes.
 *
 * @return array
 *   Node field settings.
 */
function node_field_get_node_field_settings(array $node_field) {
  if (isset($node_field['settings'])) {
    return $node_field['settings'];
  }
  // TODO: I think this function should return empty array in case there is no
  // settings attribute.
  return FALSE;
}

/**
 * Rebuild array of node fields for node using machine names as keys of array.
 *
 * @param object $node
 *   Node to rebuild node_field for.
 *
 * @return array
 *   Rebuilt array of node fields.
 */
function node_field_get_node_fields_assoc($node) {
  $assoc_fields = [];
  $fields = node_field_get_node_fields($node);

  if ($fields) {
    foreach ($fields as $field) {
      $assoc_fields[$field['machine_name']] = $field;
    }
    return $assoc_fields;
  }
  // TODO: I think this function should return empty array in case there is no
  // node field in node.
  return FALSE;
}

/**
 * Get render array of node fields.
 *
 * @param array $node_fields
 *   Node fields to render.
 *
 * @return array
 *   Drupal render array containing formatted node fields.
 */
function node_field_node_content_add_node_fields(array $node_fields) {
  $content = [
    '#theme_wrappers' => ['node_field_fields'],
  ];
  foreach ($node_fields as $node_field) {
    $node_field = node_field_node_field_formatter($node_field);
    $field = [
      '#theme' => 'node_field_field',
      '#node_field' => $node_field,
    ];
    // Add classes to field (if there are any).
    if (!empty($node_field['settings']['node_field_class'])) {
      // Class(es) of the field.
      $classes = explode(' ', $node_field['settings']['node_field_class']);
      $classes = array_filter($classes);
      $classes = array_map('drupal_html_class', $classes);
      $prefix = '<div class="' . implode(' ', $classes) . '">';
      $suffix = '</div>';
      $field['#prefix'] = $prefix;
      $field['#suffix'] = $suffix;
    }
    $content[] = $field;
  }
  return $content;
}

/**
 * Copy node fields from one node to another.
 *
 * @param object $source_node
 *   Node to copy fields from.
 * @param object $node
 *   Node to copy fields to.
 */
function node_field_copy_node_fields($source_node, $node) {
  $source_node_fields = node_field_get_node_fields($source_node);
  if (!empty($source_node_fields)) {
    foreach ($source_node_fields as $field) {
      $field['id'] = NULL;
      $field['nid'] = $node->nid;
      node_field_create_node_field($field);
    }
  }
}
