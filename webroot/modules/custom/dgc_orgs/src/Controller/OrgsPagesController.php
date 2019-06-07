<?php

namespace Drupal\dgc_orgs\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\group\Entity\Group;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class OrgsPagesController.
 */
class OrgsPagesController extends ControllerBase {

  /**
   * Privacy.
   *
   * @return string
   *   Return Hello string.
   */
  public function privacy(Group $group) {
    return $this->getFieldPage($group, 'field_privacy');
  }

  /**
   * Terms.
   *
   * @return string
   *   Return Hello string.
   */
  public function terms(Group $group) {
    return $this->getFieldPage($group, 'field_terms_of_use');
  }

  /**
   * Returns the field content or page not found if empty.
   *
   * @param Drupal\group\Entity\Group $group
   *   The Group entity.
   * @param string $field_name
   *   The field.
   *
   * @return array
   *   Render array.
   */
  protected function getFieldPage(Group $group, $field_name) {
    if ($group->bundle() == 'org') {
      $field_value = $group->{$field_name}->value ?? NULL;
      if ($field_value) {
        return [
          '#type' => 'markup',
          '#markup' => $field_value,
        ];
      }
    }
    throw new NotFoundHttpException();
  }

}
