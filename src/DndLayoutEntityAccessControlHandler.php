<?php

namespace Drupal\dnd_pb;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Dnd layout entity entity.
 *
 * @see \Drupal\dnd_pb\Entity\DndLayoutEntity.
 */
class DndLayoutEntityAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\dnd_pb\Entity\DndLayoutEntityInterface $entity */
    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished dnd layout entity entities');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published dnd layout entity entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit dnd layout entity entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete dnd layout entity entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add dnd layout entity entities');
  }

}
