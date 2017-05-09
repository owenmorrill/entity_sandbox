<?php

namespace Drupal\dnd_pb;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface DndLayoutEntityStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Dnd layout entity revision IDs for a specific Dnd layout entity.
   *
   * @param \Drupal\dnd_pb\Entity\DndLayoutEntityInterface $entity
   *   The Dnd layout entity entity.
   *
   * @return int[]
   *   Dnd layout entity revision IDs (in ascending order).
   */
  public function revisionIds(DndLayoutEntityInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Dnd layout entity author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Dnd layout entity revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\dnd_pb\Entity\DndLayoutEntityInterface $entity
   *   The Dnd layout entity entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(DndLayoutEntityInterface $entity);

  /**
   * Unsets the language for all Dnd layout entity with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
