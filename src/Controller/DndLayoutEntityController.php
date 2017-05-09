<?php

namespace Drupal\dnd_pb\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\dnd_pb\Entity\DndLayoutEntityInterface;

/**
 * Class DndLayoutEntityController.
 *
 *  Returns responses for Dnd layout entity routes.
 *
 * @package Drupal\dnd_pb\Controller
 */
class DndLayoutEntityController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Dnd layout entity  revision.
   *
   * @param int $dnd_layout_entity_revision
   *   The Dnd layout entity  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($dnd_layout_entity_revision) {
    $dnd_layout_entity = $this->entityManager()->getStorage('dnd_layout_entity')->loadRevision($dnd_layout_entity_revision);
    $view_builder = $this->entityManager()->getViewBuilder('dnd_layout_entity');

    return $view_builder->view($dnd_layout_entity);
  }

  /**
   * Page title callback for a Dnd layout entity  revision.
   *
   * @param int $dnd_layout_entity_revision
   *   The Dnd layout entity  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($dnd_layout_entity_revision) {
    $dnd_layout_entity = $this->entityManager()->getStorage('dnd_layout_entity')->loadRevision($dnd_layout_entity_revision);
    return $this->t('Revision of %title from %date', array('%title' => $dnd_layout_entity->label(), '%date' => format_date($dnd_layout_entity->getRevisionCreationTime())));
  }

  /**
   * Generates an overview table of older revisions of a Dnd layout entity .
   *
   * @param \Drupal\dnd_pb\Entity\DndLayoutEntityInterface $dnd_layout_entity
   *   A Dnd layout entity  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(DndLayoutEntityInterface $dnd_layout_entity) {
    $account = $this->currentUser();
    $langcode = $dnd_layout_entity->language()->getId();
    $langname = $dnd_layout_entity->language()->getName();
    $languages = $dnd_layout_entity->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $dnd_layout_entity_storage = $this->entityManager()->getStorage('dnd_layout_entity');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $dnd_layout_entity->label()]) : $this->t('Revisions for %title', ['%title' => $dnd_layout_entity->label()]);
    $header = array($this->t('Revision'), $this->t('Operations'));

    $revert_permission = (($account->hasPermission("revert all dnd layout entity revisions") || $account->hasPermission('administer dnd layout entity entities')));
    $delete_permission = (($account->hasPermission("delete all dnd layout entity revisions") || $account->hasPermission('administer dnd layout entity entities')));

    $rows = array();

    $vids = $dnd_layout_entity_storage->revisionIds($dnd_layout_entity);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\dnd_pb\DndLayoutEntityInterface $revision */
      $revision = $dnd_layout_entity_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->revision_timestamp->value, 'short');
        if ($vid != $dnd_layout_entity->getRevisionId()) {
          $link = $this->l($date, new Url('entity.dnd_layout_entity.revision', ['dnd_layout_entity' => $dnd_layout_entity->id(), 'dnd_layout_entity_revision' => $vid]));
        }
        else {
          $link = $dnd_layout_entity->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => \Drupal::service('renderer')->renderPlain($username),
              'message' => ['#markup' => $revision->revision_log_message->value, '#allowed_tags' => Xss::getHtmlTagList()],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('entity.dnd_layout_entity.translation_revert', ['dnd_layout_entity' => $dnd_layout_entity->id(), 'dnd_layout_entity_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.dnd_layout_entity.revision_revert', ['dnd_layout_entity' => $dnd_layout_entity->id(), 'dnd_layout_entity_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.dnd_layout_entity.revision_delete', ['dnd_layout_entity' => $dnd_layout_entity->id(), 'dnd_layout_entity_revision' => $vid]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['dnd_layout_entity_revisions_table'] = array(
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    );

    return $build;
  }

}
