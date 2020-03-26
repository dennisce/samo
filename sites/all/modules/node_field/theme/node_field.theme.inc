<?php

/**
 * @file
 * Theme functions for node_field module.
 */

/**
 * Theme for node fields views widget.
 */
function theme_node_field_views_node_field($variables) {
  $output = '';
  $node_fields = $variables['node_fields'];
  foreach ($node_fields as $field) {
    $output .= '</br>' . $field['title'] . ': ' . $field['value'];
  }
  return $output;
}
