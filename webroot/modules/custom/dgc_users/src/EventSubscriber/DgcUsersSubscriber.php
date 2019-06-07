<?php

namespace Drupal\dgc_users\EventSubscriber;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\Core\Routing\TrustedRedirectResponse;

/**
 * Class FrontPageSubscriber.
 *
 * @package Drupal\dgc_users\EventSubscriber
 */
class DgcUsersSubscriber implements EventSubscriberInterface {

  /**
   * Manage the logic.
   *
   * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
   *   Managed event.
   */
  public function frontPageRedirect(GetResponseEvent $event) {
    global $base_path;

    // Make sure it does not when using cli (drush).
    // Make sure it does not run when installing Drupal either.
    if (PHP_SAPI === 'cli' || drupal_installation_attempted()) {
      return;
    }

    // Don't run when site is in maintenance mode.
    if (\Drupal::state()->get('system.maintenance_mode')) {
      return;
    }

    // Ignore non index.php requests (like cron).
    if (!empty($_SERVER['SCRIPT_FILENAME']) && realpath(DRUPAL_ROOT . '/index.php') != realpath($_SERVER['SCRIPT_FILENAME'])) {
      return;
    }

    if (\Drupal::service('path.matcher')->isFrontPage()) {
      $event->setResponse(new TrustedRedirectResponse('http://www.joinallofus.org/'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = [];
    if ((bool) getenv('REDIRECT_TO_JAU')) {
      $events[KernelEvents::REQUEST][] = ['frontPageRedirect'];
    }
    return $events;
  }

}
