<?php

/**
 * @file
 * Database related functions for node_field module.
 */

/**
 * Add new fields in database.
 *
 * @param array $field
 *   Associative field array.
 *
 * @return int|bool
 *   If the record insert failed, returns FALSE. If it succeeded, returns
 *   SAVED_NEW.
 */
function node_field_db_field_insert(array &$field) {
  return drupal_write_record('node_field', $field);
}

/**
 * Update fields in database.
 *
 * @param array $field
 *   Associative field array.
 *
 * @return int|bool
 *   If the record update failed, returns FALSE. If it succeeded, returns
 *   SAVED_UPDATED.
 */
function node_field_db_field_update(array &$field) {
  return drupal_write_record('node_field', $field, 'id');
}

/**
 * Delete fields from database.
 *
 * @param array $values
 *   Node fields matching these values will be deleted from database.
 *
 * @return int
 *   Count of deleted records.
 */
function node_field_db_field_delete(array $values) {
  $query = db_delete('node_field');
  foreach ($values as $key => $value) {
    $query->condition('node_field.' . $key, $value);
  }
  $output = $query->execute();
  return $output;
}

/**
 * Select node node_fields.
 *
 * @param object $node
 *   Node to get node fields for.
 *
 * @return array
 *   Array of node fields for node.
 */
function node_field_db_node_fields_select($node) {
  $values = [
    'nid' => $node->nid,
  ];
  return node_field_db_field_select($values);
}

/**
 * Get fields from database.
 *
 * @param array $values
 *   Node fields matching these values will be selected from database.
 *
 * @return array
 *   Array of node fields.
 */
function node_field_db_field_select(array $values = array()) {
  $query = db_select('node_field');
  $query->fields('node_field');
  foreach ($values as $key => $value) {
    $query->condition('node_field.' . $key, $value);
  }
  $query->orderBy('weight', 'ASC');
  $result = $query->execute();
  $result_fields = $result->fetchAll(PDO::FETCH_ASSOC);
  $node_fields = [];
  foreach ($result_fields as $key => $node_field) {
    if (!empty($node_field['settings'])) {
      $result_fields[$key]['settings'] = unserialize($node_field['settings']);
    }
    else {
      $result_fields[$key]['settings'] = [];
    }
    $node_fields[$node_field['id']] = $result_fields[$key];
  }
  return $node_fields;
}

/**
 * Get nodes of the given content type.
 *
 * @param string $content_type
 *   Content type machine name.
 *
 * @return array
 *   Array of nodes of given content type
 */
function node_field_node_reference_db_get_nodes($content_type) {
  $query = db_select('node', 'n');
  $query->fields('n', ['nid', 'title']);
  $query->condition('n.type', $content_type, '=');
  $result = $query->execute();
  $items = $result->fetchAllKeyed(0, 1);
  return $items;
}
