<?php

namespace Drupal\event\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

class EventAccessControlHandler extends EntityAccessControlHandler {

  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    $access_result = AccessResult::allowedIfHasPermission($account, 'create events');
    return $access_result->orIf(parent::checkCreateAccess($account, $context, $entity_bundle));
  }

  protected function checkAccess(EntityInterface $event, $operation, AccountInterface $account) {
    /** @var \Drupal\event\Entity\EventInterface $event */
    // The parent class grants access based on the administrative permission.
    $access_result = parent::checkAccess($event, $operation, $account);
    switch ($operation) {
      case "view":
        // Only allow administrators to view unpublished events.
        if ($event->isPublished()) {
          $permission = 'view events';
        }
        else {
          $permission = 'administer events';
        }
        $access_result->addCacheableDependency($event);
        break;

      case "update":
        $permission = 'edit events';
        break;

      case "delete":
        $permission = 'delete events';
        break;

    }
    return $access_result->orIf(AccessResult::allowedIfHasPermission($account, $permission));
  }

}