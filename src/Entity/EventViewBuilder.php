<?php

namespace Drupal\event\Entity;

use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityViewBuilder;

/**
 * Provides a view builder for events.
 */
class EventViewBuilder extends EntityViewBuilder {

  /**
   * {@inheritdoc}
   */
  protected function alterBuild(array &$build, EntityInterface $entity, EntityViewDisplayInterface $display, $view_mode) {
    /** @var \Drupal\event\Entity\EventInterface $entity */
    if ($entity->id()) {
      $build['#contextual_links']['event'] = [
        'route_parameters' => ['event' => $entity->id()],
        'metadata' => ['changed' => $entity->getChangedTime()],
      ];
    }
  }

}
