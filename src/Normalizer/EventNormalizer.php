<?php

namespace Drupal\event\Normalizer;

use Drupal\event\Entity\EventInterface;
use Drupal\serialization\Normalizer\ContentEntityNormalizer;

/**
 * Provides a normalizer for event entities.
 */
class EventNormalizer extends ContentEntityNormalizer {

  /**
   * {@inheritdoc}
   */
  protected $supportedInterfaceOrClass = EventInterface::class;

  /**
   * {@inheritdoc}
   */
  public function normalize($event, $format = NULL, array $context = []) {
    /** @var \Drupal\event\Entity\EventInterface $event */
    return [
      'id' => $event->id(),
      'type' => $event->bundle(),
      'title' => $event->getTitle(),
      'date' => $event->getDate()->format('Y-m-d'),
      'description' => (string) $event->getDescription(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function denormalize($data, $class, $format = NULL, array $context = []) {
    /** @var \Drupal\event\Entity\EventInterface $event */
    $event = parent::denormalize($data, $class, $format, $context);
    // We do not allow specifying the text format explicitly in the data, but
    // instead always assume the input is basic HTML. If this were omitted, the
    // text would be saved without a text format.
    if (isset($data['description'])) {
      $event->setDescription($data['description'], 'basic_html');
    }
    return $event;
  }

}
