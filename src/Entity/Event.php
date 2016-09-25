<?php

namespace Drupal\event\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;

/**
 * @ContentEntityType(
 *   id = "event",
 *   label = @Translation("Event"),
 *   label_singular = @Translation("event"),
 *   label_plural = @Translation("events"),
 *   label_count = @PluralTranslation(
 *     singular = "@count event",
 *     plural = "@count events"
 *   ),
 *   base_table = "event",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "label" = "title",
 *   },
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *     },
 *     "form" = {
 *       "add" = "Drupal\event\Form\MessageRedirectContentEntityForm",
 *       "edit" = "Drupal\event\Form\MessageRedirectContentEntityForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "list_builder" = "Drupal\event\Entity\EventListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "access" = "Drupal\event\Access\EventAccessControlHandler",
 *   },
 *   links = {
 *     "canonical" = "/event/{event}",
 *     "add-form" = "/admin/content/events/add",
 *     "edit-form" = "/admin/content/events/manage/{event}",
 *     "delete-form" = "/admin/content/events/manage/{event}/delete",
 *     "collection" = "/admin/content/events",
 *   },
 *   admin_permission = "administer events",
 * )
 */

class Event extends ContentEntityBase implements EventInterface {

  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setRequired(TRUE)
      ->setDisplayOptions('form', ['weight' => 0]);

    $fields['date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Date'))
      ->setRequired(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'settings' => [
          'format_type' => 'html_date',
        ],
        'weight' => 0,
      ])
      ->setDisplayOptions('form', ['weight' => 10]);

    $fields['description'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Description'))
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'weight' => 10,
      ])
      ->setDisplayOptions('form', ['weight' => 20]);

    $fields['published'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Published'))
      ->setDefaultValue(FALSE)
      ->setDisplayOptions('form', [
        'settings' => [
          'display_label' => TRUE,
        ],
        'weight' => 30,
      ]);

    $fields['path'] = BaseFieldDefinition::create('path')
      ->setLabel(t('Path'))
      ->setDisplayOptions('form', ['weight' => 5]);

    $fields['attendees'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Attendees'))
      ->setSetting('target_type', 'user')
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setDisplayOptions('form', ['weight' => 25])
      ->setDisplayOptions('view', ['weight' => 20]);

    $fields['owner'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Owner'))
      ->setSetting('target_type', 'user')
      ->setDefaultValueCallback(static::class . '::getCurrentUser');

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'));

    return $fields;
  }

  public function getTitle() {
    return $this->get('title')->value;
  }

  public function setTitle($title) {
    return $this->set('title', $title);
  }

  public function getDate() {
    return $this->get('date')->date;
  }

  public function setDate(\DateTimeInterface $date) {
    return $this->set('date', $date->format(DATETIME_DATETIME_STORAGE_FORMAT));
  }

  public function getDescription() {
    return $this->get('description')->processed;
  }

  public function setDescription($description, $format) {
    return $this->set('description', [
      'value' => $description,
      'format' => $format,
    ]);
  }

  public function isPublished() {
    return (bool) $this->get('published')->value;
  }

  public function publish() {
    return $this->set('published', TRUE);
  }

  public function unpublish() {
    return $this->set('published', FALSE);
  }

  public static function getCurrentUser() {
    return \Drupal::currentUser()->id();
  }

}