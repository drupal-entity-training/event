<?php

namespace Drupal\event\Routing;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Routing\EntityRouteProviderInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Provides a HTML collection route for events.
 */
class EventCollectionHtmlRouteProvider implements EntityRouteProviderInterface {

  /**
   * {@inheritdoc}
   */
  public function getRoutes(EntityTypeInterface $entity_type) {
    $collection = new RouteCollection();

    $entity_type_id = $entity_type->id();

    if ($collection_route = $this->getCollectionRoute($entity_type)) {
      $collection->add("entity.{$entity_type_id}.collection", $collection_route);
    }

    return $collection;
  }

  /**
   * Gets the collection route.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type.
   *
   * @return \Symfony\Component\Routing\Route|null
   *   The generated route, if available.
   */
  protected function getCollectionRoute(EntityTypeInterface $entity_type) {
    if ($entity_type->hasLinkTemplate('collection') && $entity_type->hasListBuilderClass()) {
      $route = new Route($entity_type->getLinkTemplate('collection'));
      $route
        ->addDefaults([
          '_entity_list' => $entity_type->id(),
          // There is currently no way for an entity type to specify its plural
          // label in an uppercase form (the 'plural_label' annotation is
          // intended for use in a sentence, so is lowercase). This is the only
          // thing that we cannot provide generically.
          '_title' => 'Events',
        ]);

      $permissions = ['administer events', 'create events', 'edit events', 'delete events'];
      $route
        ->setRequirement('_permission', implode('+', $permissions));

      return $route;
    }
  }

}
