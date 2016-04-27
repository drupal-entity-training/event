<?php

namespace Drupal\event\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a form for adding event types.
 */
class EventTypeAddForm extends EventTypeFormBase {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $status = parent::save($form, $form_state);

    $event_type = $this->getEntity();
    $this->logger('event')->info("The event type '{id}' has been added.", [
      'id' => $event_type->id(),
    ]);
    drupal_set_message($this->t('The event type %title has been added.', [
      '%title' => $event_type->label(),
    ]));

    return $status;
  }

}
