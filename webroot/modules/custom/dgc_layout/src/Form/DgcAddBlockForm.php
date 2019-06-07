<?php

namespace Drupal\dgc_layout\Form;

use Drupal\layout_builder\Form\AddBlockForm;
use Drupal\layout_builder\SectionStorageInterface;
use Drupal\layout_builder\SectionComponent;
use Drupal\layout_builder\Controller\LayoutRebuildTrait;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\CloseDialogCommand;
use Drupal\Core\Ajax\AjaxFormHelperTrait;

/**
 * Provides a form to add a block.
 *
 * @internal
 */
class DgcAddBlockForm extends AddBlockForm {

  use AjaxFormHelperTrait;
  use LayoutRebuildTrait;

  /**
   * {@inheritdoc}
   */
  protected function rebuildAndClose(SectionStorageInterface $section_storage) {
    $response = $this->rebuildLayout($section_storage);
    $response->addCommand(new CloseDialogCommand('.layout-buidler-inline-block .ui-dialog-content'));
    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public function doBuildForm(array $form, FormStateInterface $form_state, SectionStorageInterface $section_storage = NULL, $delta = NULL, SectionComponent $component = NULL) {
    $form = parent::doBuildForm($form, $form_state, $section_storage, $delta, $component);
    $form['#prefix'] = '<div id="layout-builder-modal-inline-block-form">';
    $form['#suffix'] = '</div>';
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit']['#ajax']['wrapper'] = 'layout-builder-modal-inline-block-form';
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function ajaxSubmit(array &$form, FormStateInterface $form_state) {
    if ($form_state->hasAnyErrors()) {
      return $form;
    }
    else {
      $response = $this->successfulAjaxSubmit($form, $form_state);
    }
    return $response;
  }

}
