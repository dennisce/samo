<?php

/**
 * @file
 * Theme for node fields page.
 */

/**
 * Building theme.
 *
 * TODO: Should be moved to node_field.theme.inc.
 */
function theme_node_field_node_field_page_form_fields($node_field) {
  $fields = $node_field['container']['fields'];
  $rows = [];
  $weight_class = 'node_field_weight';
  $header = [];
  foreach (element_children($fields) as $fid) {
    $fields[$fid]['weight']['#attributes']['class'][] = $weight_class;
    $row = [
      'data' => [],
      'class' => ['draggable'],
    ];
    foreach (element_children($fields[$fid]) as $key) {
      if (isset($fields[$fid][$key]['#title'])) {
        $item = drupal_render($fields[$fid][$key]);
        if ($item) {
          $header[$key] = $fields[$fid][$key]['#title'];
          $row['data'][] = $item;
        }
      }
    }
    if ($row['data']) {
      $rows[] = $row;
    }
  }

  $node_field['container']['new']['weight']['#attributes']['class'][] = $weight_class;
  $data = [];
  foreach (element_children($node_field['container']['new']) as $fid) {
    if (isset($node_field['container']['new'][$fid]['#title'])) {
      $item = drupal_render($node_field['container']['new'][$fid]);
      if ($item) {
        $data[] = $item;
      }
    }
  }
  if (!empty($data)) {
    $row = [
      'data' => $data,
      'class' => ['draggable'],
    ];
    for ($i = count($row['data']); $i < count($header); $i++) {
      $row['data'][] = '';
    }
    $rows[] = $row;
  }

  $table_id = 'node_field_table';
  $table_attributes = [
    'header' => $header,
    'rows' => $rows,
    'sticky' => FALSE,
    'attributes' => [
      'id' => $table_id,
    ],
    'empty' => t('No fields yet.'),
  ];
  $output = theme('table', $table_attributes);
  drupal_add_tabledrag($table_id, 'order', 'sibling', $weight_class);
  return $output;
}
