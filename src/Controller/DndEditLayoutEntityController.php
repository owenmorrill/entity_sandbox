<?php

namespace Drupal\dnd_pb\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\dnd_pb\Entity\DndLayoutEntity;

class DndEditLayoutEntityController extends ControllerBase {
  public function editLayout(DndLayoutEntity $dnd_layout_entity) {
    return $this->t('Hello everyone!');
  }
}