<?php

/**
 * Set default 'en' value for preferred_admin_langcode for uid 1.
 */
function dgc_users_post_update_admin_langcode_set(&$sandbox) {
  $users = \Drupal::entityTypeManager()->getStorage('user')->loadByProperties([
    'name' => 'admin',
    'uid' => '1',
  ]);
  if ($users) {
    foreach ($users as $user) {
      if ($user->get('preferred_admin_langcode')->isEmpty()) {
        $lang = $user->getPreferredAdminLangcode(TRUE);
        $user->set('preferred_admin_langcode', $lang);
        $user->save();

        return t('Module dgc_Users Post Update #admin_langcode_set was executed successfully.');
      }
    }
  }
}
