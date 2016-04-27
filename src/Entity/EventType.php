<?php

namespace Drupal\event\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the event type entity.
 *
 * @ConfigEntityType(
 *   id = "event_type",
 *   label = @Translation("Event type"),
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *   },
 *   config_prefix = "type",
 *   config_export = {
 *     "id",
 *     "label",
 *   },
 *   bundle_of = "event",
 *   handlers = {
 *     "list_builder" = "Drupal\event\Entity\EventTypeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\event\Form\EventTypeAddForm",
 *       "edit" = "Drupal\event\Form\EventTypeEditForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html_default" = "Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider",
 *       "html_collection" = "Drupal\event\Routing\CollectionHtmlRouteProvider",
 *     },
 *   },
 *   links = {
 *     "add-form" = "/admin/structure/event-types/add",
 *     "edit-form" = "/admin/structure/event-types/manage/{event_type}/edit",
 *     "delete-form" = "/admin/structure/event-types/manage/{event_type}/delete",
 *     "collection" = "/admin/structure/event-types",
 *   },
 *   admin_permission = "administer event types",
 * )
 */
class EventType extends ConfigEntityBase {

  /**
   * The machine-readable ID of the event type.
   *
   * @var string
   */
  protected $id;

  /**
   * The human-readable label of the event type.
   *
   * @var string
   */
  protected $label;

}

