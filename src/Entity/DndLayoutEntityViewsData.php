<?php

namespace Drupal\dnd_pb\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Dnd layout entity entities.
 */
class DndLayoutEntityViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.

    return $data;
  }

}
