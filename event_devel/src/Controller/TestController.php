<?php

namespace Drupal\event_devel\Controller;

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
   * If Drush is available you can also run arbitrary code in the context of a
   * bootstrapped Drupal site with the "drush php-eval", "drush php-script" or
   * "drush core-cli" commands.
   *
   * @return array
   *   A renderable array that contains instruction text for this controller.
   *
   * @see event_devel.routing.yml
   */
  public function evaluateTestCode() {

    // This creates a new event and saves it to the database:
    /** $event = Event::create([
      'title' => 'DrupalCon New Orleans',
      'date' => REQUEST_TIME,
      'description' => [
        'value' => '<p>The North American DrupalCon in 2016 is happening in New Orleans and it is <strong>awesome</strong>!</p>',
        'format' => 'basic_html',
      ]
    ]); */
    // $event->save();
    // drupal_set_message('A new event with the ID ' . $event->id() . ' has been saved.');

    // This loads an event by its ID.
    // $id = 1;
    // $event = Event::load($id);

    // This tests the various methods of the interface.
    // $title = $event->getTitle();
    // drupal_set_message('The title of the event with the ID ' . $id . ' is ' . $title . '.');
    // $title = $event->setTitle('Drupal Developer Days Milano')->getTitle();
    // drupal_set_message('The title of the event with the ID ' . $id . ' is now ' . $title . '.');

    // $date = $event->getDate()->format(DATETIME_DATE_STORAGE_FORMAT);
    // drupal_set_message('The date of the event with the ID ' . $id . ' is ' . $date . '.');
    // $date = $event->setDate(new \DateTime('tomorrow'))->getDate()->format(DATETIME_DATE_STORAGE_FORMAT);
    // drupal_set_message('The date of the event with the ID ' . $id . ' is now ' . $date . '.');

    // $description = $event->getDescription();
    // drupal_set_message('The description of the event with the ID ' . $id . ' is ' . $description . '.');
    /** $description = $event
      ->setDescription('This is a youtube video: <iframe width="560" height="315" src="https://www.youtube.com/embed/Ch_hoYPPeGc" frameborder="0" allowfullscreen></iframe>', 'full_html')
      ->getDescription(); */
    // drupal_set_message('The description of the event with the ID ' . $id . ' is now ' . $description . '.');

    return ['#markup' => 'Any code placed in \\' . __METHOD__ . '() is executed on this page.'];
  }

  /**
   * Provides a test controller to update entity/field definitions.
   *
   * This is exposed at the '/update-entity-field-definitions' path on your
   * site.
   *
   * If Drush is available, this can be achieved by running
   * "drush entity-updates" (or "drush entup") instead.
   *
   * @return array
   *   A renderable array that contains a summary of the applied entity/field
   *   definitions.
   *
   * @see \Drupal\Core\Entity\EntityDefinitionUpdateManagerInterface::applyUpdates()
   */
  public function updateEntityFieldDefinitions() {
    $build = [];

    // This code mimics the code that displays the list of needed entity/field
    // definition updates on the status report at /admin/reports/status.
    /** @see system_requirements() */
    if ($change_summary = $this->entityDefinitionUpdateManager->getChangeSummary()) {
      foreach ($change_summary as $entity_type_id => $changes) {
        $build[] = [
          '#theme' => 'item_list',
          '#title' => $this->entityTypeManager->getDefinition($entity_type_id)->getLabel(),
          '#items' => $changes,
        ];
      }

      // This line of code is the only one that is not related to the output of
      // this controller. It proves that the functionality to update the
      // entity/field definitions is given by Drupal core itself although no UI
      // exists for it at this point.
      $this->entityDefinitionUpdateManager->applyUpdates();

      drupal_set_message('The entity/field definition updates listed below have been applied successfully.');
    }
    else {
      $build[] = ['#markup' => 'No outstanding entity/field definition updates.'];
    }

    return $build;
  }

}
