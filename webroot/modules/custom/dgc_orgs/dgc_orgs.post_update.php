<?php

/**
 * Implements hook_post_update_NAME().
 */
function dgc_orgs_post_update_remove_left_align(&$sandbox) {
  $query = \Drupal::entityQuery('paragraph')
    ->condition('type', 'banner_item')
    ->condition('field_card_alignment', 'right');
  $ids = $query->execute();
  if ($ids) {
    foreach ($ids as $id) {
      $entity = \Drupal::entityTypeManager()->getStorage('paragraph')->load($id);
      $entity->field_card_alignment->value = 'left';
      $entity->save();
    }
    return t('Module dgc_orgs Post Update #remove_left_align was executed successfully.');
  }
}
