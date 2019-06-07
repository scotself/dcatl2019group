<?php

/**
 * @file
 * Post update function for saving field config.
 */

/**
 * Implements hook_post_update_NAME().
 */
function dgc_multilingual_post_update_faq_menu_link_trans(&$sandbox) {

  $entity_field_manager = \Drupal::service('entity_field.manager');
  $map = $entity_field_manager->getFieldMapByFieldType('menu_link');

  $fields_config = [];
  foreach ($map as $entity_type => $info) {
    foreach ($info as $name => $data) {
      foreach ($data['bundles'] as $bundle) {
        $fields = $entity_field_manager->getFieldDefinitions($entity_type, $bundle);
        $fields_config[$bundle] = $fields[$name]->getConfig($bundle);
      }
    }
  }

  foreach ($fields_config as $bundle => $fconfig) {
    // Define multilingual settings array.
    $definition = \Drupal::service('plugin.manager.field.field_type')
      ->getDefinition('menu_link');
    $column_groups = $definition['column_groups'];
    $trans_settings = [];
    foreach ($column_groups as $name => $property) {
      if (isset($property['translatable'])) {
        $trans_settings[$name] = $name;
      }
    }
    // Set multilingual settings.
    $fconfig->setThirdPartySetting('content_translation', 'translation_sync', $trans_settings);
    // Set field to be translatable.
    $fconfig->setTranslatable(TRUE);
    // Save config changes.
    $fconfig->save();

  }
  return t('Module dgc_multilingual Post Update #faq_menu_link_trans was executed successfully.');
}

