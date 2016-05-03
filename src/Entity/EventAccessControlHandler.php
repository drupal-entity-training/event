<?php

namespace Drupal\event\Entity;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Controls access to events.
 */
class EventAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    $access_result = AccessResult::allowedIfHasPermission($account, 'create events');
    return $access_result->orIf(parent::checkCreateAccess($account, $context, $entity_bundle));
  }

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    switch ($operation) {
      case 'view':
      case 'view label':
        $access_result = AccessResult::allowedIfHasPermission($account, 'view events');
        break;

      case 'update':
        $access_result = AccessResult::allowedIfHasPermission($account, 'edit events');
        break;

      case 'delete':
        $access_result = AccessResult::allowedIfHasPermission($account, 'delete events');
        break;

      default:
        $access_result = AccessResult::neutral();
    }

    return $access_result->orIf(parent::checkAccess($entity, $operation, $account));
  }

  /**
   * {@inheritdoc}
   */
  protected function checkFieldAccess($operation, FieldDefinitionInterface $field_definition, AccountInterface $account, FieldItemListInterface $items = NULL) {
    switch ($field_definition->getName()) {
      case 'revision_log_message':
        if ($items) {
          $entity = $items->getEntity();
          $access_result = AccessResult::allowedIf(!$entity->isNew())
            ->addCacheableDependency($entity);
        }
        else {
          $access_result = AccessResult::allowed();
        }
        break;

      default:
        $access_result = AccessResult::allowed();

    }
    return $access_result;
  }

}
