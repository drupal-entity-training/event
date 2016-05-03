<?php

namespace Drupal\event\Routing;

use Drupal\Core\Entity\Controller\EntityViewController;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Routing\EntityRouteProviderInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Provides a HTML revision route for events.
 */
class EventRevisionHtmlRouteProvider implements EntityRouteProviderInterface {

  /**
   * {@inheritdoc}
   */
  public function getRoutes(EntityTypeInterface $entity_type) {
    $collection = new RouteCollection();

    $entity_type_id = $entity_type->id();

    if ($revision_route = $this->getRevisionRoute($entity_type)) {
      $collection->add("entity.{$entity_type_id}.revision", $revision_route);
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
  protected function getRevisionRoute(EntityTypeInterface $entity_type) {
    if ($entity_type->hasLinkTemplate('revision') && $entity_type->getKey('revision')) {
      $entity_type_id = $entity_type->id();

      $route = new Route($entity_type->getLinkTemplate('revision'));
      $route->addDefaults([
        '_title_callback' => EntityViewController::class . '::buildTitle',
        '_controller' => EntityViewController::class . '::viewRevision',
      ]);
      $route->addRequirements([
        '_entity_access' => "$entity_type_id.view",
        '_permission' => 'view event revisions',
      ]);
      $route->setOption('parameters', [
        $entity_type_id => ['type' => "entity:$entity_type_id"],
        $entity_type_id . '_revision' => ['type' => "entity_revision:$entity_type_id"],
      ]);
      return $route;
    }
  }

}
