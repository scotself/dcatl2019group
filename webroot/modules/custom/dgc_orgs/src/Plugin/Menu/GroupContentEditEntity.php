<?php

namespace Drupal\dgc_orgs\Plugin\Menu;

use Drupal\Core\Menu\LocalTaskDefault;
use Drupal\Core\Routing\RouteMatchInterface;

/**
 * Class GroupContentNodeEdit.
 */
class GroupContentEditEntity extends LocalTaskDefault {

  /**
   * {@inheritdoc}
   */
  public function getRouteParameters(RouteMatchInterface $route_match) {
    if ($group_content = $route_match->getParameter('group_content')) {
      return [
        'node' => $group_content->getEntity()->id(),
      ];
    }
    return [];
  }

}
