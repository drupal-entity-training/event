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
   * If Drush is available you can also run arbitrary code in the context of a
   * bootstrapped Drupal site with the "drupal php-eval" (or "drush ev")
   * command.
   *
   * @return array
   *   A renderable array that contains instruction text for this controller.
   *
   * @see event.routing.yml
   */
  public function evaluateTestCode() {

    // This creates a new event and saves it to the database:
    /** $event = Event::create([
      'title' => 'DrupalCon New Orleans',
      'date' => REQUEST_TIME,
      'description' => [
        'value' => 'The North American DrupalCon in 2016 is happening in New Orleans and it is <strong>awesome</strong>!',
        'format' => 'basic_html',
      ]
    ]); */
    // $event->save();
    // drupal_set_message('A new event with the ID ' . $event->id() . ' has been saved.');

    // This loads an event by its ID.
    // $id = 1;
    // $event = Event::load($id);

    // This displays the title of the event loaded above in a message.
    // There are multiple ways to retrieve a field value in Drupal 8:
    // 1. This is the format that is most similar to Drupal 7.
    // $title = $event->title->value;
    // 2. This is arguably the most common format. It is used below for the
    //    other fields.
    // $title = $event->get('title')->value;
    // 3. This is the most verbose method, but it best reveals the internal
    //    object structure of entities.
    // $title = $event->get('title')->get(0)->get('value')->getValue();
    // Below is the same code again but with each step explained:
    // The $event variable is the entity object itself.
    // Content entities have a get() method to retrieve a field's values given
    // the field name. Because some fields can have multiple values, for
    // consistency this method returns a field item list instead of a field
    // item.
    /** @see \Drupal\Core\Entity\FieldableEntityInterface::get() */
    // $field_item_list = $event->get('title');
    // Retrieve a given field item from the field item list by its delta. For
    // single-value fields the delta is always 0.
    /** @see \Drupal\Core\TypedData\ListInterface::get() */
    /** @var \Drupal\Core\Field\FieldItemInterface $field_item */
    // $field_item = $field_item_list->get(0);
    // Because some field types can have multiple properties (see the event
    // description below for one example) we need to specify the property we
    // want to fetch. For many field types that only have a single property such
    // as strings (like in this case) or integers the name of the property is
    // 'value' but this cannot be relied upon.
    /** @see \Drupal\Core\TypedData\ComplexDataInterface::get() */
    // $string = $field_item->get('value');
    // Properties itself are not the raw values but objects themselves, so in
    // order to get the actual string raw value we need to call the getValue()
    // method.
    /** @see \Drupal\Core\TypedData\TypedDataInterface::getValue() */
    // $title = $string->getValue();
    // drupal_set_message('The title of the event with the ID ' . $id . ' is ' . $title);

    // This displays the date of the event loaded above in a message.
    // $date = $event->get('date')->value;
    // drupal_set_message('The date of the event with the ID ' . $id . ' is ' . $date);

    // This displays the description of the event loaded above in a message.
    // Text fields store two property values in the database:
    // - value: The raw, unformatted text value
    // - format: The ID of the text format used to format the text.
    // Additionally, they expose a so-called "computed" property called
    // 'processed' which contains the processed text after application of the
    // text format.
    // drupal_set_message('The description for event with the ID ' . $id . ' is:');
    // drupal_set_message() hides duplicate messages by default but in this case
    // it even hides messages which are not strictly equal as is proven by the
    // output.
    // $description = $event->get('description')->value;
    // drupal_set_message($description, 'status', TRUE);
    // $description = $event->get('description')->processed;
    // drupal_set_message($description, 'status', TRUE);

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
