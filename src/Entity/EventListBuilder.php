<?php

namespace Drupal\event\Entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * Provides a list builder for events.
 */
class EventListBuilder extends EntityListBuilder {

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
    $row['title'] = $event->get('title')->value;
    $row['date'] = $event->get('date')->value;
    return $row + parent::buildRow($event);
  }

}
