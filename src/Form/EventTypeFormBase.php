<?php

namespace Drupal\event\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\event\Entity\EventType;

/**
 * Provides a base form for entity types.
 */
class EventTypeFormBase extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\event\Entity\EventTypeInterface $event_type */
    $event_type = $this->getEntity();

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#default_value' => $event_type->get('label'),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $event_type->get('id'),
      '#machine_name' => [
        'exists' => [EventType::class, 'load'],
      ],
      '#required' => TRUE,
    ];

    return parent::form($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $status = parent::save($form, $form_state);
    $form_state->setRedirect('entity.event_type.collection');
    return $status;
  }

}
