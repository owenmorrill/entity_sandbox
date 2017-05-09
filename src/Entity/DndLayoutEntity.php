<?php

namespace Drupal\dnd_pb\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\RevisionableContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Dnd layout entity entity.
 *
 * @ingroup dnd_pb
 *
 * @ContentEntityType(
 *   id = "dnd_layout_entity",
 *   label = @Translation("Dnd layout entity"),
 *   handlers = {
 *     "storage" = "Drupal\dnd_pb\DndLayoutEntityStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\dnd_pb\DndLayoutEntityListBuilder",
 *     "views_data" = "Drupal\dnd_pb\Entity\DndLayoutEntityViewsData",
 *     "translation" = "Drupal\dnd_pb\DndLayoutEntityTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\dnd_pb\Form\DndLayoutEntityForm",
 *       "add" = "Drupal\dnd_pb\Form\DndLayoutEntityForm",
 *       "edit" = "Drupal\dnd_pb\Form\DndLayoutEntityForm",
 *       "delete" = "Drupal\dnd_pb\Form\DndLayoutEntityDeleteForm",
 *     },
 *     "access" = "Drupal\dnd_pb\DndLayoutEntityAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\dnd_pb\DndLayoutEntityHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "dnd_layout_entity",
 *   data_table = "dnd_layout_entity_field_data",
 *   revision_table = "dnd_layout_entity_revision",
 *   revision_data_table = "dnd_layout_entity_field_revision",
 *   translatable = TRUE,
 *   admin_permission = "administer dnd layout entity entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/dnd_layout_entity/{dnd_layout_entity}",
 *     "add-form" = "/admin/structure/dnd_layout_entity/add",
 *     "edit-form" = "/admin/structure/dnd_layout_entity/{dnd_layout_entity}/edit",
 *     "delete-form" = "/admin/structure/dnd_layout_entity/{dnd_layout_entity}/delete",
 *     "version-history" = "/admin/structure/dnd_layout_entity/{dnd_layout_entity}/revisions",
 *     "revision" = "/admin/structure/dnd_layout_entity/{dnd_layout_entity}/revisions/{dnd_layout_entity_revision}/view",
 *     "revision_revert" = "/admin/structure/dnd_layout_entity/{dnd_layout_entity}/revisions/{dnd_layout_entity_revision}/revert",
 *     "translation_revert" = "/admin/structure/dnd_layout_entity/{dnd_layout_entity}/revisions/{dnd_layout_entity_revision}/revert/{langcode}",
 *     "revision_delete" = "/admin/structure/dnd_layout_entity/{dnd_layout_entity}/revisions/{dnd_layout_entity_revision}/delete",
 *     "collection" = "/admin/structure/dnd_layout_entity",
 *   },
 *   field_ui_base_route = "dnd_layout_entity.settings"
 * )
 */
class DndLayoutEntity extends RevisionableContentEntityBase implements DndLayoutEntityInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += array(
      'user_id' => \Drupal::currentUser()->id(),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    foreach (array_keys($this->getTranslationLanguages()) as $langcode) {
      $translation = $this->getTranslation($langcode);

      // If no owner has been set explicitly, make the anonymous user the owner.
      if (!$translation->getOwner()) {
        $translation->setOwnerId(0);
      }
    }

    // If no revision author has been set explicitly, make the dnd_layout_entity owner the
    // revision author.
    if (!$this->getRevisionUser()) {
      $this->setRevisionUserId($this->getOwnerId());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isPublished() {
    return (bool) $this->getEntityKey('status');
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published) {
    $this->set('status', $published ? TRUE : FALSE);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRevisionCreationTime() {
    return $this->get('revision_timestamp')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setRevisionCreationTime($timestamp) {
    $this->set('revision_timestamp', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRevisionUser() {
    return $this->get('revision_uid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function setRevisionUserId($uid) {
    $this->set('revision_uid', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Dnd layout entity entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ),
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Dnd layout entity entity.'))
      ->setRevisionable(TRUE)
      ->setSettings(array(
        'max_length' => 50,
        'text_processing' => 0,
      ))
      ->setDefaultValue('')
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'weight' => -4,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Dnd layout entity is published.'))
      ->setRevisionable(TRUE)
      ->setDefaultValue(TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    $fields['revision_timestamp'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Revision timestamp'))
      ->setDescription(t('The time that the current revision was created.'))
      ->setQueryable(FALSE)
      ->setRevisionable(TRUE);

    $fields['revision_uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Revision user ID'))
      ->setDescription(t('The user ID of the author of the current revision.'))
      ->setSetting('target_type', 'user')
      ->setQueryable(FALSE)
      ->setRevisionable(TRUE);

    $fields['revision_translation_affected'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Revision translation affected'))
      ->setDescription(t('Indicates if the last edit of a translation belongs to current revision.'))
      ->setReadOnly(TRUE)
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE);

    return $fields;
  }

}
