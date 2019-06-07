<?php

namespace Drupal\dgc_users;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;

// @note: You only need Reference, if you want to change service arguments.
use Symfony\Component\DependencyInjection\Reference;

/**
 * Modifies the language manager service.
 */
class DgcUsersServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    // Overrides content_translation.overview_access class to change access.
    // Adds entity_type.manager service as an additional argument.
    $definition = $container->getDefinition('content_translation.overview_access');
    $definition->setClass('Drupal\dgc_users\AccessChecks\DgcCustomAccessChecks')
      ->addArgument(new Reference('entity_type.manager'));
  }
}