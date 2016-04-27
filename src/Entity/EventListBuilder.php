<?php

namespace Drupal\event\Entity;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a list builder for events.
 */
class EventListBuilder extends EntityListBuilder {

  /**
   * The date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * Constructs an EventListBuilder object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   * @param \Drupal\Core\Entity\EntityStorageInterface $entity_storage
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $entity_storage, DateFormatterInterface $date_formatter) {
    parent::__construct($entity_type, $entity_storage);
    $this->dateFormatter = $date_formatter;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity.manager')->getStorage($entity_type->id()),
      $container->get('date.formatter')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['title'] = $this->t('Title');
    $header['date'] = $this->t('Date');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $event) {
    /** @var \Drupal\event\Entity\EventInterface $event */
    $row['title'] = $event->toLink();
    $row['date'] = $this->dateFormatter->format($event->getDate()->getTimestamp());
    return $row + parent::buildRow($event);
  }

}
