<?php

namespace Drupal\event\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

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
 *       "add" = "Drupal\Core\Entity\ContentEntityForm",
 *       "edit" = "Drupal\Core\Entity\ContentEntityForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html_default" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *       "html_collection" = "Drupal\event\Entity\CollectionHtmlRouteProvider",
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
class Event extends ContentEntityBase {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Title'))
      ->setDisplayOptions('form', [
        'weight' => 0,
      ]);

    $fields['date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Date'))
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
