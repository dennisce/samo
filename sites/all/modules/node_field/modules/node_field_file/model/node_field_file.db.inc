<?php

/**
 * @file
 * Database related functions for node_field_file module.
 */

/**
 * Change status of file for newly created node field.
 */
function node_field_file_db_on_insert($fid, $field_id) {
  // TODO: node_field_update_node_field shouldn't be here.
  $field = node_field_load_node_field($field_id);
  $field['value'] = $fid;
  node_field_update_node_field($field);
  $query = db_update('file_managed');
  $query->fields(array('status' => 1));
  $query->condition('fid', $fid);
  $query->execute();
}
