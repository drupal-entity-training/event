<?php

namespace Drupal\event\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;

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
 *     "label" = "title",
 *   },
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
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
 *     "add-form" = "/admin/content/events/add",
 *     "edit-form" = "/admin/content/events/manage/{event}/edit",
 *     "delete-form" = "/admin/content/events/manage/{event}/delete",
 *     "collection" = "/admin/content/events",
 *   },
 *   admin_permission = "administer events",
 * )
 */
class Event extends ContentEntityBase implements EventInterface {

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
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setRequired(TRUE)
      ->setDisplayOptions('form', [
        'weight' => 0,
      ]);

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
      ]);

    $fields['description'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Description'))
      ->setDisplayOptions('form', [
        'weight' => 20,
      ])
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'weight' => 10,
      ]);

    return $fields;
  }

}
