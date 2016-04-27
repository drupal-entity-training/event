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
    return $this->get('date')->date ?: new \DateTime();
  }

  /**
   * {@inheritdoc}
   */
  public function setDate(\DateTimeInterface $date) {
    return $this->set('date', $date->format('c'));
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
      ->setRequired(TRUE);

    $fields['date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Date'))
      ->setSetting('datetime_type', DateTimeItem::DATETIME_TYPE_DATE)
      ->setRequired(TRUE);

    $fields['description'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Description'));

    return $fields;
  }

}
