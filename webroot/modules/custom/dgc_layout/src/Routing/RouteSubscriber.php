<?php

namespace Drupal\dgc_layout\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    // Change layout buidler add block form.
    if ($route = $collection->get('layout_builder.add_block')) {
      $defaults = ['_form' => '\Drupal\dgc_layout\Form\DgcAddBlockForm'];
      $route->setDefaults($defaults);
    }
    // Change layout buidler update block form.
    if ($route = $collection->get('layout_builder.update_block')) {
      $defaults = ['_form' => '\Drupal\dgc_layout\Form\DgcUpdateBlockForm'];
      $route->setDefaults($defaults);
    }
  }

}
