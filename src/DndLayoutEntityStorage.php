<?php

namespace Drupal\dnd_pb;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\dnd_pb\Entity\DndLayoutEntityInterface;

/**
 * Defines the storage handler class for Dnd layout entity entities.
 *
 * This extends the base storage class, adding required special handling for
 * Dnd layout entity entities.
 *
 * @ingroup dnd_pb
 */
class DndLayoutEntityStorage extends SqlContentEntityStorage implements DndLayoutEntityStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(DndLayoutEntityInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {dnd_layout_entity_revision} WHERE id=:id ORDER BY vid',
      array(':id' => $entity->id())
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {dnd_layout_entity_field_revision} WHERE uid = :uid ORDER BY vid',
      array(':uid' => $account->id())
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(DndLayoutEntityInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {dnd_layout_entity_field_revision} WHERE id = :id AND default_langcode = 1', array(':id' => $entity->id()))
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('dnd_layout_entity_revision')
      ->fields(array('langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED))
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
