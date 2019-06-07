<?php
/**
 * Created by PhpStorm.
 * User: mariana
 * Date: 11/02/19
 * Time: 13:17
 */

namespace Drupal\dgc_media\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\Core\TempStore\PrivateTempStoreFactory;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Get current gid from Group Route and store it in the TempStorage.
 */
class DgcMediaSubscriber implements EventSubscriberInterface {

  /**
   * Set Group id to 'dgc_media_filter' Private Storage.
   *
   * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
   *   The response event.
   */
  public function groupByRoute(GetResponseEvent $event) {
    if ($group = \Drupal::routeMatch()->getParameter('group')) {
      if (is_numeric($group)) {
        $group = \Drupal::entityTypeManager()->getStorage('group')->load($group);
      }
      // Get gid only for Org type groups.
      if ($group->getGroupType()->id() === 'org') {
        $group_id = $group->id();
      }
      if (!empty($group_id)) {
        \Drupal::service('tempstore.private')->get('dgc_media_filter')->set('gid', $group_id);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = ['groupByRoute'];
    return $events;
  }

}
