<?php

/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */

use Robo\Tasks;

/**
 * Robo Tasks.
 */
class RoboFile extends Tasks {

  /**
   * Local Site update.
   */
  public function localUpdate() {
    $this->say("dgc Local site update started");
    $collection = $this->collectionBuilder();
    $collection->taskExec('drush state:set system.maintenance_mode 1 -y')
      ->taskFilesystemStack()->mkdir('config-group-menus')
      ->taskExec('drush cr -y')
      ->taskExec('drush updatedb -y')
      ->taskExec('drush csex menus -y')
      ->taskExec('drush csex group_menu_link_field -y')
      ->taskExec('drush cim -y')
      ->taskExec('drush cim -y')
      ->taskExec('drush cr -y')
      ->taskExec('drush user:role:add developer admin -y')
      ->taskExec('drush user:role:add tfadisabled admin -y')
      ->taskExec('drush php-eval "node_access_rebuild();" -y')
      ->taskExec('drush state:set system.maintenance_mode 0 -y')
      ->addCode([$this, 'themeUpdate']);
    return $collection;
  }

  /**
   * Local Site install.
   */
  public function localInstall() {
    $this->say("dgc Local site installation started");
    $collection = $this->collectionBuilder();
    $collection->taskFilesystemStack()->mkdir('webroot/sf')
      ->taskExec('grep "key.pem" mtwplatform-app/templates/secret-sf-key.yaml | cut -d " " -f 4 | base64 -d > webroot/sf/key.pem')
      ->taskExec('drush si --account-name=admin --account-pass=admin --existing-config -y')
      ->taskFilesystemStack()->mkdir('config-group-menus')
      ->taskExec('drush cim -y')
      ->taskExec('drush cim -y')
      ->taskExec('drush cr -y')
      // ->taskExec('drush vbap jwt_govcloud -y')
      ->taskExec('drush user:role:add developer admin -y')
      ->taskExec('drush user:role:add tfadisabled admin -y')
      ->taskExec('drush config-set system.logging error_level hide -y')
      ->taskExec('drush php-eval "node_access_rebuild();" -y')
      ->addCode([$this, 'themeUpdate']);
    return $collection;
  }

  /**
   * Site update.
   */
  public function siteUpdate() {
    $this->say("dgc site Update started");
    $collection = $this->collectionBuilder();
    $collection->taskExec('drush state:set system.maintenance_mode 1 -y')
      ->taskFilesystemStack()->mkdir('config-group-menus')
      ->taskExec('drush cr -y')
      ->taskExec('drush updatedb -y')
      ->taskExec('drush csex menus -y')
      ->taskExec('drush csex group_menu_link_field -y')
      ->taskExec('drush cim -y')
      ->taskExec('drush cim -y')
      ->taskExec('drush cr -y')
      ->taskExec('drush user:role:add developer admin -y')
      ->taskExec('drush user:role:add tfadisabled admin -y')
      ->taskExec('drush config-set system.logging error_level hide -y')
      ->taskExec('drush php-eval "node_access_rebuild();" -y')
      ->taskExec('drush state:set system.maintenance_mode 0 -y');
    return $collection;
  }

  /**
   * Site install.
   */
  public function siteInstall() {
    $this->say("dgc site installation started");
    $collection = $this->collectionBuilder();
    $collection->taskExec('drush si --account-name=admin --account-pass=admin --existing-config -y')
      ->taskFilesystemStack()->mkdir('config-group-menus')
      ->taskExec('drush cim -y')
      ->taskExec('drush cim -y')
      ->taskExec('drush cr -y')
      ->taskExec('drush user:role:add developer admin -y')
      ->taskExec('drush user:role:add tfadisabled admin -y')
      ->taskExec('drush config-set system.logging error_level hide -y')
      ->taskExec('drush php-eval "node_access_rebuild();" -y');
    return $collection;
  }

  /**
   * Theme update.
   */
  public function themeUpdate() {
    $this->taskNpmInstall()->dir('/app/webroot/themes/custom/dgc')->run();
    $this->taskGulpRun('sass')->dir('/app/webroot/themes/custom/dgc')->run();
    $this->taskGulpRun('javascript')->dir('/app/webroot/themes/custom/dgc')->run();
    $this->taskExec('drush cc css-js')->run();
  }

}
