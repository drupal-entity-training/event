<?php

namespace Drupal\event\Entity;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EventListBuilder extends EntityListBuilder {

  /**
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, DateFormatterInterface $date_formatter) {
    parent::__construct($entity_type, $storage);
    $this->dateFormatter = $date_formatter;
  }

  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity.manager')->getStorage($entity_type->id()),
      $container->get('date.formatter')
    );
  }

  public function buildHeader() {
    $header = [];
    $header['title'] = $this->t('Title');
    $header['date'] = $this->t('Date');
    $header['published'] = $this->t('Published');
    return $header + parent::buildHeader();
  }

  public function buildRow(EntityInterface $event) {
    /** @var \Drupal\event\Entity\EventInterface $event */
    $row = [];
    $row['title'] = $event->toLink();
    $row['date'] = $this->dateFormatter->format($event->getDate()->getTimestamp(), 'medium');
    $row['published'] = $event->isPublished() ? $this->t('Yes') : $this->t('No');
    return $row + parent::buildRow($event);
  }

}