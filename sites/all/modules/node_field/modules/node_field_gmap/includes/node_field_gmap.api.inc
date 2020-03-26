<?php

/**
 * @file
 * Api and helpers function for node_field_gmap module.
 */

/**
 * Widget for gmap node field.
 *
 * @param array $node_field
 *   Node field data.
 *
 * @return array
 *   Form elements.
 */
function node_field_gmap_field_widget(array $node_field) {
  $value = unserialize($node_field['value']);
  $form = array(
    '#type' => 'fieldset',
    '#title' => $node_field['title'],
    'latitude' => array(
      '#type' => 'hidden',
      '#title' => t('Latitude'),
      '#default_value' => !empty($value['latitude']) ? $value['latitude'] : NULL,
      '#attributes' => array('id' => 'node-field-gmap-' . $node_field['id'] . '-latitude'),
    ),
    'longitude' => array(
      '#type' => 'hidden',
      '#title' => t('Longitude'),
      '#default_value' => !empty($value['longitude']) ? $value['longitude'] : NULL,
      '#attributes' => array('id' => 'node-field-gmap-' . $node_field['id'] . '-longitude'),
    ),
    'description' => array(
      '#type' => 'textarea',
      '#title' => t('Location description'),
      '#default_value' => !empty($value['description']) ? $value['description'] : NULL,
    ),
    'gmap' => array(
      '#type' => 'hidden',
      '#value' => 'gmap',
    ),
    'map_container' => array(
      '#type' => 'item',
      '#markup' => '<div style="width:100%;height:300px;" class="node-field-gmap" id="node-field-gmap-' . $node_field['id'] . '"></div>',
    ),
  );
  // Scripts and settings.
  $map_settings = array(
    'id' => 'node-field-gmap-' . $node_field['id'],
    'lat' => 40,
    'lon' => -100,
    'description' => NULL,
    'zoom' => 2,
    'marker' => FALSE,
    'latId' => '#node-field-gmap-' . $node_field['id'] . '-latitude',
    'lonId' => '#node-field-gmap-' . $node_field['id'] . '-longitude',
  );
  if (!empty($value['latitude']) && !empty($value['longitude'])) {
    $map_settings['lat'] = $value['latitude'];
    $map_settings['lon'] = $value['longitude'];
    $map_settings['description'] = $value['description'];
    $map_settings['zoom'] = 16;
    $map_settings['marker'] = TRUE;
  }
  $url_options = array(
    'external' => TRUE,
    'query' => array(
      'key' => variable_get('node_field_gmap_api_key', ''),
      'libraries' => 'places',
    ),
  );
  $url = url('https://maps.googleapis.com/maps/api/js', $url_options);
  drupal_add_js(array('nodeFieldGmapEdit' => array($map_settings)), 'setting');
  drupal_add_js($url, array('type' => 'external', 'group' => JS_LIBRARY));
  drupal_add_js(drupal_get_path('module', 'node_field_gmap') . '/includes/node_field_gmap.edit.js');

  return $form;
}

/**
 * Formatter for gmap node field.
 *
 * @param array $node_field
 *   Node field data.
 *
 * @return string
 *   Google map container.
 */
function node_field_gmap_field_formatter(array $node_field) {
  $value = unserialize($node_field['value']);
  $map_settings = array(
    'id' => 'node-field-gmap-' . $node_field['id'],
    'lat' => 40,
    'lon' => -100,
    'description' => NULL,
    'zoom' => 2,
    'marker' => FALSE,
  );
  if (!empty($value['latitude']) && !empty($value['longitude'])) {
    $map_settings['lat'] = $value['latitude'];
    $map_settings['lon'] = $value['longitude'];
    $map_settings['description'] = $value['description'];
    $map_settings['zoom'] = 16;
    $map_settings['marker'] = TRUE;
  }
  $url_options = array(
    'external' => TRUE,
    'query' => array(
      'key' => variable_get('node_field_gmap_api_key', ''),
      'libraries' => 'places',
    ),
  );
  $url = url('https://maps.googleapis.com/maps/api/js', $url_options);
  drupal_add_js(array('nodeFieldGmapView' => array($map_settings)), 'setting');
  drupal_add_js($url, array('type' => 'external', 'group' => JS_LIBRARY));
  drupal_add_js(drupal_get_path('module', 'node_field_gmap') . '/includes/node_field_gmap.view.js');

  $output = '<div style="width:100%;height:300px;" id="' . $map_settings['id'] . '"></div>';

  return $output;
}
