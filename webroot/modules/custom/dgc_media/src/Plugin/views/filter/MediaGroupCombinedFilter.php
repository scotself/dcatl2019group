<?php
/**
 * Created by PhpStorm.
 * User: mariana
 * Date: 02/02/19
 * Time: 22:41
 */

namespace Drupal\dgc_media\Plugin\views\filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\display\DisplayPluginBase;
use Drupal\views\ViewExecutable;
use Drupal\views\Plugin\views\filter\BooleanOperator;
use Drupal\Core\Database\Query\Condition;
use Drupal\views\Views;

/**
 * Handler to filter Media by Path Group and Global Assets field on media.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("media_group_combined_filter")
 */
class MediaGroupCombinedFilter extends BooleanOperator {

  /**
   * {@inheritdoc}
   *
   * @todo: this is not working as expected, find a way to hide operators in the view add filter form.
   */
  public $no_operator = TRUE;

  /**
   * {@inheritdoc}
   */
  public function init(ViewExecutable $view, DisplayPluginBase $display, array &$options = NULL) {
    parent::init($view, $display, $options);
    $this->valueTitle = t('Media per group access');
    $this->value_value = $this->t('Include Global Assets?');
    $this->no_operator = TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    // Don't apply this filter if user has 'bypass assets filter' permissions.
    if (\Drupal::currentUser()->hasPermission('bypass assets filter')) {
      return;
    }

    $this->ensureMyTable();
    $this->always_required = TRUE;

    // Join Group Content Entity ID table.
    $first = [
      'table' => 'group_content__entity_id',
      'field' => 'entity_id_target_id',
      'left_table' => 'media_field_data',
      'left_field' => 'mid',
      'type' => 'LEFT',
      'operator' => '=',
      'extra' => 'gcei.bundle LIKE \'%group_media%\'',
    ];
    $first_join = Views::pluginManager('join')
      ->createInstance('standard', $first);

    // Add gcei table aliased to the query.
    $this->query->addTable($first['table'], $this->relationship, $first_join, 'gcei');

    // Join Group Content field_data table.
    // Second, relate the gcfd table to the gcei specified using
    // the id on the gcei table and the entity_id field.
    $second = [
      'table' => 'group_content_field_data',
      'field' => 'id',
      'left_table' => 'group_content__entity_id',
      'left_field' => 'entity_id',
      'type' => 'LEFT',
      'operator' => '=',
      'extra' => 'gcfd.type LIKE \'%group_media%\'',
    ];
    $second_join = Views::pluginManager('join')
      ->createInstance('standard', $second);

    // Third, relate the "Global Asset" field table to the media entity
    // using the entity_id on the field table and the entity's mid field.
    $third = [
      'table' => 'media__field_global_asset',
      'field' => 'entity_id',
      'left_table' => 'media_field_data',
      'left_field' => 'mid',
      'type' => 'LEFT',
      'operator' => '=',
    ];
    $third_join = Views::pluginManager('join')
      ->createInstance('standard', $third);

    // Add JOINs with aliases.
    // media_field_data.mid = gcei.entity_id_target_id.
    $this->query->addRelationship('gcei', $first_join, 'media_field_data');
    // gcfd.id = gcei.entity_id.
    $this->query->addRelationship('gcfd', $second_join, 'group_content__entity_id');
    // media_field_ga.entity_id = media_field_data.id.
    $this->query->addRelationship('media_field_ga', $third_join, 'media_field_data');

    // Get tempStorage.
    $temp_storage = \Drupal::service('tempstore.private');

    // Get params from current page.
    $params = \Drupal::request()->query->all();

    // Init $gid var.
    $gid = '';
    // If gid comes from params..
    if (!empty($params['gid'])) {
      $gid = $params['gid'];
    }
    // If no gid in params get gid from tempStorage.
    else {
      $gid = $temp_storage->get('dgc_media_filter')->get('gid');
    }
    // Program id from temp storage.
    $pid = $temp_storage->get('dgc_media_filter')->get('pid');
    // If gid is empty.
    if (empty($gid)) {
      // If no GA, nothing else to do.
      if (empty($this->value)) {
        $this->view->executed = TRUE;
      }
      // If GA set to 1. Only list global assets.
      else {
        // When creating new organizations only platform wide
        // and global assets should appear.
        $or = new Condition('OR');
        $this->query->addWhere(0,
          $or
            ->condition('gcfd.gid', $pid, '=')
            ->condition("media_field_ga.field_global_asset_value", 1, '=')
        );
      }
    }
    // If we get gid from params or tempStorage.
    else {
      // If Global Asset is not checked, filter results per Current Page gid.
      if (empty($this->value)) {
        $this->query->addWhere($this->options['group'], "gcfd.gid", $gid, '=');
      }
      // If Global asset, add as OR to the WHERE clause.
      else {
        // Getting program id when it doesn't come from url.
        $group = \Drupal::entityTypeManager()->getStorage('group')->load($gid);
        if (!$group->field_study->isEmpty() && empty($pid)) {
          $pid = $group->field_study->first()->getValue()['target_id'];
        }
        // Conditions.
        $or = new Condition('OR');
        $this->query->addWhere(0,
          $or
            ->condition('gcfd.gid', $gid, '=')
            ->condition('gcfd.gid', $pid, '=')
            ->condition("media_field_ga.field_global_asset_value", 1, '=')
        );
        // Adding distinct flag to avoid duplicates.
        $this->query->distinct = TRUE;
      }
    }
  }

  /**
   * Add GA selector to the value form.
   */
  protected function valueForm(&$form, FormStateInterface $form_state) {
    $user_input = $form_state->getUserInput();
    $return_value = $this->value;
    $default_value = '1';
    $form['value'] = [
      '#type' => 'radios',
      '#title' => $this->t('Include Global Assets'),
      '#options' => [
        '0' => $this->t('No'),
        '1' => $this->t('Yes'),
      ],
      '#default_value' => $default_value,
      '#return_value' => $return_value,
    ];
    $user_input = $form_state->getUserInput();
    if ($form_state->get('exposed') && !isset($user_input[$this->options['expose']['identifier']])) {
      $user_input[$this->options['expose']['identifier']] = $this->value;
      $form_state->setUserInput($user_input);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    $contexts = parent::getCacheContexts();
    // This filter depends on the current user.
    $contexts[] = 'user';
    return $contexts;
  }
}
