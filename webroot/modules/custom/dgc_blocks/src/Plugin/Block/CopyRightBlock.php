<?php

namespace Drupal\dgc_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'CopyRightBlock' block.
 *
 * @Block(
 *  id = "copyright_block",
 *  admin_label = @Translation("Copyright block"),
 * )
 */
class CopyRightBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'copyright_message' => "© 2019 All rights reserved.",
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['copyright_message'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Copyright Message'),
      '#format' => 'limited_html',
      '#default_value' => $this->configuration['copyright_message']['value'],
      '#maxlength' => 300,
      '#maxlength_js' => TRUE,
      '#weight' => 1,
      '#attributes' => [
        'maxlength' => 300,
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['copyright_message'] = $form_state->getValue('copyright_message');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['#markup'] = '<p>' . $this->configuration['copyright_message']['value'] . '</p>';

    return $build;
  }

}
