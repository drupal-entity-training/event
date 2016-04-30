<?php

namespace Drupal\event\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a form for adding events.
 */
class EventAddForm extends EventFormBase {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $status = parent::save($form, $form_state);

    /** @var \Drupal\event\Entity\EventInterface $event */
    $event = $this->getEntity();
    $this->logger('event')->info('The event with ID {id} has been added.', [
      'id' => $event->id(),
      'link' => $event->toLink($this->t('View'))->toString(),
    ]);
    drupal_set_message($this->t('The event %title has been added.', [
      '%title' => $event->getTitle(),
    ]));

    return $status;
  }

}
