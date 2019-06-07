<?php
/**
 * Created by PhpStorm.
 * User: mariana
 * Date: 02/02/19
 * Time: 22:41
 */

namespace Drupal\dgc_media\Plugin\views\filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\filter\FilterPluginBase;
use Drupal\views\Plugin\views\display\DisplayPluginBase;
use Drupal\Core\Language\LanguageInterface;
use Drupal\views\ViewExecutable;
use Drupal\views\Plugin\views\filter\InOperator;
use Drupal\views\Plugin\views\filter\BooleanOperator;
use Drupal\Core\Database\Query\Condition;
use Drupal\user\Entity\User;
use Drupal\group\Entity\GroupContent;
use Drupal\views\Views;
use Drupal\Core\Database\Driver\mysql\Schema;
use Symfony\Component\HttpFoundation\Request;

/**
 * Handler to filter Media by group.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("media_current_path_filter")
 */
class MediaGroupCurrentPathFilter extends BooleanOperator {

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
    $this->valueTitle = t('Media Group');
    $this->value_value = $this->t('Current page Group');
    $this->no_operator = TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    $this->ensureMyTable();
    $this->always_required = TRUE;

    // // Join Group Content Entity ID table.
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

    // Don't apply this filter if user has 'administer media' permissions.
    if (\Drupal::currentUser()->hasPermission('administer media')) {
      return;
    }

    // Add JOINs with aliases.
    $this->query->addRelationship('gcei', $first_join, 'media_field_data');
    $this->query->addRelationship('gcfd', $second_join, 'group_content__entity_id');

    // Get params from current page.
    $params = \Drupal::request()->query->all();

    if (!empty($this->value)) {
      if (!empty($params) && !empty($params['gid'])) {
        // Filter results per Current Page gid.
        $this->query->addWhere($this->options['group'], "gcfd.gid", $params['gid'], '=');
      }
    }
  }

  /**
   * Add selector to the value form.
   */
  protected function valueForm(&$form, FormStateInterface $form_state) {
    $user_input = $form_state->getUserInput();
    $default_value = 'checked';
    // Get params from current page.
    $params = \Drupal::request()->query->all();
    if (!empty($params) && !empty($params['gid'])) {
      $return_value = $params['gid'];
    }
    else {
      $return_value = $default_value;
    }
    $form['value'] = [
      '#type' => 'radio',
      '#title' => $this->t('Current page Group'),
      '#default_value' => $default_value,
      '#return_value' => $return_value,
    ];
    // If Filter is exposed, get params to set the value.
    $user_input = $form_state->getUserInput();
    if ($form_state->get('exposed') && !isset($user_input[$this->options['expose']['identifier']])) {
      $user_input[$this->options['expose']['identifier']] = $return_value;
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