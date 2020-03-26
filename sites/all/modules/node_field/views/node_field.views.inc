<?php

/**
 * @file
 * Views hooks for node_field module.
 */

/**
 * Implements hook_views_data().
 */
function node_field_views_data() {
  $data = [];
  $data['node']['node_fields'] = [
    'title' => t('Node fields'),
    'real field' => 'nid',
    'help' => t('All node fields of the node.'),
    'field' => [
      'handler' => 'node_field_handler_field_node_field',
    ],
  ];
  return $data;
}
