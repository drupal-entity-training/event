<?php

namespace Drupal\event\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a base event form.
 */
abstract class EventFormBase extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    /** @var \Drupal\event\Entity\EventInterface $event */
    $event = $this->getEntity();
    $event->setNewRevision();
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $status = parent::save($form, $form_state);
    $form_state->setRedirect('entity.event.collection');
    return $status;
  }

}
