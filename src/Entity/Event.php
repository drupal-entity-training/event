<?php

namespace Drupal\event\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;
use Drupal\user\UserInterface;

/**
 * Defines the event entity.
 *
 * @ContentEntityType(
 *   id = "event",
 *   label = @Translation("Event"),
 *   base_table = "event",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "bundle" = "type",
 *     "label" = "title",
 *   },
 *   bundle_entity_type = "event_type",
 *   bundle_label = @Translation("Type"),
 *   handlers = {
 *     "view_builder" = "Drupal\event\Entity\EventViewBuilder",
 *     "list_builder" = "Drupal\event\Entity\EventListBuilder",
 *     "form" = {
 *       "add" = "Drupal\event\Form\EventAddForm",
 *       "edit" = "Drupal\event\Form\EventEditForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html_default" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *       "html_collection" = "Drupal\event\Routing\CollectionHtmlRouteProvider",
 *     },
 *   },
 *   links = {
 *     "canonical" = "/events/{event}",
 *     "add-form" = "/admin/content/events/add/{event_type}",
 *     "edit-form" = "/admin/content/events/manage/{event}/edit",
 *     "delete-form" = "/admin/content/events/manage/{event}/delete",
 *     "add-page" = "/admin/content/events/add",
 *     "collection" = "/admin/content/events",
 *   },
 *   field_ui_base_route = "entity.event_type.edit_form",
 *   admin_permission = "administer events",
 * )
 */
class Event extends ContentEntityBase implements EventInterface {

  /**
   * {@inheritdoc}
   */
  public function getType() {
    return $this->get('type')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function setType(EventTypeInterface $type) {
    return $this->set('type', $type);
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return $this->get('title')->value ?: '';
  }

  /**
   * {@inheritdoc}
   */
  public function setTitle($title) {
    return $this->set('title', $title);
  }

  /**
   * {@inheritdoc}
   */
  public function getDate() {
    // The interface dictates that we return a datetime object in all cases, so
    // in case one is not yet available we return one corresponding to the
    // beginning of the Unix epoch so that it is probably clear to the consumer
    // that something has gone wrong.
    return $this->get('date')->date ?: new \DateTime('@0');
  }

  /**
   * {@inheritdoc}
   */
  public function setDate(\DateTimeInterface $date) {
    // We need to set the date in the specific format that is expected by Drupal
    // date fields without time information.
    $this->set('date', $date->format(DATETIME_DATE_STORAGE_FORMAT));
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->get('description')->processed ?: '';
  }

  /**
   * {@inheritdoc}
   */
  public function setDescription($text, $format) {
    return $this->set('description', [
      'value' => $text,
      'format' => $format,
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getAttendees() {
    /** @var \Drupal\Core\Field\EntityReferenceFieldItemListInterface $attendees_item_list */
    $attendees_item_list = $this->get('attendees');
    return $attendees_item_list->referencedEntities();
  }

  /**
   * {@inheritdoc}
   */
  public function addAttendee(UserInterface $attendee) {
    // Check if the attendee is already registered.
    $attendees_item_list = $this->get('attendees');
    foreach ($attendees_item_list as $attendee_item) {
      if ($attendee_item->target_id === $attendee->id()) {
        return $this;
      }
    }

    $attendees_item_list->appendItem($attendee);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function removeAttendee(UserInterface $attendee) {
    $attendees_item_list = $this->get('attendees');
    foreach ($attendees_item_list as $delta => $attendee_item) {
      if ($attendee_item->target_id === $attendee->id()) {
        $attendees_item_list->removeItem($delta);
      }
    }

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setRequired(TRUE)
      ->setDisplayOptions('form', [
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Date'))
      ->setSetting('datetime_type', DateTimeItem::DATETIME_TYPE_DATE)
      ->setRequired(TRUE)
      ->setDisplayOptions('form', [
        'weight' => 10,
      ])
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['description'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Description'))
      ->setDisplayOptions('form', [
        'weight' => 20,
      ])
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'weight' => 10,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['attendees'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Attendees'))
      ->setSetting('target_type', 'user')
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete_tags',
        'weight' => 30,
      ])
      ->setDisplayOptions('view', [
        'weight' => 20,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['path'] = BaseFieldDefinition::create('path')
      ->setLabel(t('Path'))
      ->setDisplayOptions('form', [
        'weight' => 40,
      ])
      ->setDisplayConfigurable('form', TRUE);

    return $fields;
  }

}
