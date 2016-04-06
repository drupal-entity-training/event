<?php

namespace Drupal\event\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityDefinitionUpdateManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\event\Entity\Event;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a test controller.
 */
class TestController implements ContainerInjectionInterface {

  /**
   * The entity definition update manager.
   *
   * @var \Drupal\Core\Entity\EntityDefinitionUpdateManagerInterface
   */
  protected $entityDefinitionUpdateManager;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a TestController object.
   *
   * @param \Drupal\Core\Entity\EntityDefinitionUpdateManagerInterface $entity_definition_update_manager
   *   The entity definition update manager.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityDefinitionUpdateManagerInterface $entity_definition_update_manager, EntityTypeManagerInterface $entity_type_manager) {
    $this->entityDefinitionUpdateManager = $entity_definition_update_manager;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.definition_update_manager'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * Provides an empty test controller to easily execute arbitrary code.
   *
   * This is exposed at the '/test' path on your site.
   *
   * @return array
   *   A renderable array that contains instruction text for this controller.
   *
   * @see event.routing.yml
   */
  public function test() {

    // This creates a new event and saves it to the database:
    // Event::create()->save();

    // This loads an event by its ID and displays its UUID in a message.
    // $id = 1;
    // $uuid = Event::load($id)->uuid();
    // drupal_set_message('The UUID for event with ID ' . $id . ' is ' . $uuid . '.');

    return ['#markup' => 'Any code placed in \\' . __METHOD__ . '() is executed on this page.'];
  }

  /**
   * Provides a test controller to updates entity/field definitions.
   *
   * @return array
   *   A renderable array that contains a summary of the applied entity/field
   *   definitions.
   */
  public function updateEntityFieldDefinitions() {
    $build = [];

    if ($change_summary = $this->entityDefinitionUpdateManager->getChangeSummary()) {
      foreach ($change_summary as $entity_type_id => $changes) {
        $build[] = [
          '#theme' => 'item_list',
          '#title' => $this->entityTypeManager->getDefinition($entity_type_id)->getLabel(),
          '#items' => $changes,
        ];
      }
      $this->entityDefinitionUpdateManager->applyUpdates();

      drupal_set_message('The entity/field definition updates listed below have been applied successfully.');
    }
    else {
      $build[] = ['#markup' => 'No outstanding entity/field definition updates.'];
    }

    return $build;
  }

}
