<?php

namespace Drupal\dgc_media\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\media_library\Plugin\Field\FieldWidget\MediaLibraryWidget;

/**
 * Plugin implementation of the 'dgc_media_library_widget' widget.
 *
 * @FieldWidget(
 *   id = "dgc_media_library_widget",
 *   label = @Translation("Media library for Vibrant"),
 *   description = @Translation("Allows you to select items from the media library."),
 *   field_types = {
 *     "entity_reference"
 *   },
 *   multiple_values = TRUE,
 * )
 *
 * @internal
 */
class DgcMediaLibraryWidget extends MediaLibraryWidget {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    // Init var.
    $gid = '';
    // Get Temp storage.
    if ($temp_storage = \Drupal::service('tempstore.private')) {
      // If we get a Group from tempstore, set var with group id().
      if (!empty($temp_storage->get('dgc_media_filter')->get('gid'))) {
        $gid = $temp_storage->get('dgc_media_filter')->get('gid');
      }
    }
    // Set array to append to the query.
    $group_param['gid'] = $gid;

    // Get element array from parent class.
    $element = parent::formElement($items, $delta, $element, $form, $form_state);
    // Get uri.
    $url = $element['media_library_open_button']['#url'];
    // Get url options.
    $getUrlOptions = $url->getOptions();
    // Get query Option.
    $query = $getUrlOptions['query'];
    // Append gid param to query array.
    $query += $group_param;

    // Set the new query array to Uri options.
    $url->setOption('query', $query);

    return $element;
  }

}
