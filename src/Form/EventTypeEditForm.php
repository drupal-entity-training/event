<?php

namespace Drupal\event\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a form for editing event types.
 */
class EventTypeEditForm extends EventTypeFormBase {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $status = parent::save($form, $form_state);

    $event_type = $this->getEntity();
    $this->logger('event')->info("The event type '{id}' has been updated.", [
      'id' => $event_type->id(),
    ]);
    drupal_set_message($this->t('The event type %title has been updated.', [
      '%title' => $event_type->label(),
    ]));

    return $status;
  }

}
