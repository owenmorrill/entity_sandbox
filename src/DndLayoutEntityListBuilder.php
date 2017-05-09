<?php

namespace Drupal\dnd_pb;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Routing\LinkGeneratorTrait;
use Drupal\Core\Url;

/**
 * Defines a class to build a listing of Dnd layout entity entities.
 *
 * @ingroup dnd_pb
 */
class DndLayoutEntityListBuilder extends EntityListBuilder {

  use LinkGeneratorTrait;

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Dnd layout entity ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\dnd_pb\Entity\DndLayoutEntity */
    $row['id'] = $entity->id();
    $row['name'] = $this->l(
      $entity->label(),
      new Url(
        'entity.dnd_layout_entity.edit_form', array(
          'dnd_layout_entity' => $entity->id(),
        )
      )
    );
    return $row + parent::buildRow($entity);
  }

}
