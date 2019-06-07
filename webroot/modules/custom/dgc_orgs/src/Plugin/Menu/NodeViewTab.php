<?php

namespace Drupal\dgc_orgs\Plugin\Menu;

use Drupal\Core\Menu\LocalTaskDefault;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\group\Entity\GroupContent;

/**
 * Alters the node view link task.
 */
class NodeViewTab extends LocalTaskDefault {

  /**
   * {@inheritdoc}
   */
  public function getRouteName() {
    if ($node = \Drupal::routeMatch()->getParameter('node')) {
      if (GroupContent::loadByEntity($node)) {
        return 'entity.group_content.canonical';
      }
    }
    return $this->pluginDefinition['route_name'];
  }

  /**
   * {@inheritdoc}
   */
  public function getRouteParameters(RouteMatchInterface $route_match) {
    if ($node = $route_match->getParameter('node')) {
      if ($group_content = GroupContent::loadByEntity($node)) {
        $group_content = reset($group_content);
        return [
          'group' => $group_content->getGroup()->id(),
          'group_content' => $group_content->id(),
        ];
      }
    }
    return parent::getRouteParameters($route_match);
  }

}
