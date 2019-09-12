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
    $this->say("DC ATL Local site update started");
    $collection = $this->collectionBuilder();
    $collection->taskExec('drush state:set system.maintenance_mode 1 -y')
      ->taskExec('drush cr -y')
      ->taskExec('drush updatedb -y')
      ->taskExec('drush cim -y')
      ->taskExec('drush cim -y')
      ->taskExec('drush cr -y')
      ->taskExec('drush php-eval "node_access_rebuild();" -y')
      ->taskExec('drush state:set system.maintenance_mode 0 -y')
      ->addCode([$this, 'themeUpdate']);
    return $collection;
  }

  /**
   * Local Site install.
   */
  public function localInstall() {
    $this->say("DC ATL Local site installation started");
    $collection = $this->collectionBuilder();
    $collection->taskExec('drush si --account-name=admin --account-pass=admin --existing-config -y')
      ->taskExec('drush cim -y')
      ->taskExec('drush cim -y')
      ->taskExec('drush cr -y')
      ->taskExec('drush php-eval "node_access_rebuild();" -y')
      ->addCode([$this, 'themeUpdate']);
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
