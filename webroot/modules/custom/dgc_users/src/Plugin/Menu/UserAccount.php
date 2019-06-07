<?php

namespace Drupal\dgc_users\Plugin\Menu;

use Drupal\Core\Menu\LocalTaskDefault;
use Drupal\Core\Routing\RouteMatchInterface;

/**
 * Class GroupContentNodeEdit.
 */
class UserAccount extends LocalTaskDefault {

  /**
   * Current user object.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * Gets the current active user.
   */
  protected function currentUser() {
    if (!$this->currentUser) {
      $this->currentUser = \Drupal::currentUser();
    }
    return $this->currentUser;
  }

  /**
   * {@inheritdoc}
   */
  public function getRouteParameters(RouteMatchInterface $route_match) {
    return [
      'user' => $this->currentUser()->id(),
    ];
  }

}
