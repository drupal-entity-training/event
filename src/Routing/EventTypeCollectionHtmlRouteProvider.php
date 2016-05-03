<?php

namespace Drupal\event\Routing;

use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Provides a HTML collection route for event types.
 */
class EventTypeCollectionHtmlRouteProvider extends CollectionHtmlRouteProviderBase {

  /**
   * {@inheritdoc}
   */
  protected function getCollectionRoute(EntityTypeInterface $entity_type) {
    if (($route = parent::getCollectionRoute($entity_type)) && ($admin_permission = $entity_type->getAdminPermission())) {
      // There is currently no way for an entity type to specify its plural
      // label in an uppercase form (the 'plural_label' annotation is
      // intended for use in a sentence, so is lowercase). This is the only
      // thing that we cannot provide generically.
      $route->setDefault('_title', 'Event types');
      $route->setRequirement('_permission', $admin_permission);
      return $route;
    }
  }

}
