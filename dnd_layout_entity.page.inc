<?php

/**
 * @file
 * Contains dnd_layout_entity.page.inc.
 *
 * Page callback for Dnd layout entity entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Dnd layout entity templates.
 *
 * Default template: dnd_layout_entity.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_dnd_layout_entity(array &$variables) {
  // Fetch DndLayoutEntity Entity Object.
  $dnd_layout_entity = $variables['elements']['#dnd_layout_entity'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
