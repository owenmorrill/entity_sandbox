<?php

namespace Drupal\dnd_pb\Entity;

use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Url;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Dnd layout entity entities.
 *
 * @ingroup dnd_pb
 */
interface DndLayoutEntityInterface extends RevisionableInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Dnd layout entity name.
   *
   * @return string
   *   Name of the Dnd layout entity.
   */
  public function getName();

  /**
   * Sets the Dnd layout entity name.
   *
   * @param string $name
   *   The Dnd layout entity name.
   *
   * @return \Drupal\dnd_pb\Entity\DndLayoutEntityInterface
   *   The called Dnd layout entity entity.
   */
  public function setName($name);

  /**
   * Gets the Dnd layout entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Dnd layout entity.
   */
  public function getCreatedTime();

  /**
   * Sets the Dnd layout entity creation timestamp.
   *
   * @param int $timestamp
   *   The Dnd layout entity creation timestamp.
   *
   * @return \Drupal\dnd_pb\Entity\DndLayoutEntityInterface
   *   The called Dnd layout entity entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Dnd layout entity published status indicator.
   *
   * Unpublished Dnd layout entity are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Dnd layout entity is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Dnd layout entity.
   *
   * @param bool $published
   *   TRUE to set this Dnd layout entity to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\dnd_pb\Entity\DndLayoutEntityInterface
   *   The called Dnd layout entity entity.
   */
  public function setPublished($published);

  /**
   * Gets the Dnd layout entity revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Dnd layout entity revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\dnd_pb\Entity\DndLayoutEntityInterface
   *   The called Dnd layout entity entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Dnd layout entity revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Dnd layout entity revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\dnd_pb\Entity\DndLayoutEntityInterface
   *   The called Dnd layout entity entity.
   */
  public function setRevisionUserId($uid);

}
