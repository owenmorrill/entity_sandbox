<?php

namespace Drupal\dnd_pb\Form;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for deleting a Dnd layout entity revision.
 *
 * @ingroup dnd_pb
 */
class DndLayoutEntityRevisionDeleteForm extends ConfirmFormBase {


  /**
   * The Dnd layout entity revision.
   *
   * @var \Drupal\dnd_pb\Entity\DndLayoutEntityInterface
   */
  protected $revision;

  /**
   * The Dnd layout entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $DndLayoutEntityStorage;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * Constructs a new DndLayoutEntityRevisionDeleteForm.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $entity_storage
   *   The entity storage.
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection.
   */
  public function __construct(EntityStorageInterface $entity_storage, Connection $connection) {
    $this->DndLayoutEntityStorage = $entity_storage;
    $this->connection = $connection;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $entity_manager = $container->get('entity.manager');
    return new static(
      $entity_manager->getStorage('dnd_layout_entity'),
      $container->get('database')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dnd_layout_entity_revision_delete_confirm';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return t('Are you sure you want to delete the revision from %revision-date?', array('%revision-date' => format_date($this->revision->getRevisionCreationTime())));
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('entity.dnd_layout_entity.version_history', array('dnd_layout_entity' => $this->revision->id()));
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $dnd_layout_entity_revision = NULL) {
    $this->revision = $this->DndLayoutEntityStorage->loadRevision($dnd_layout_entity_revision);
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->DndLayoutEntityStorage->deleteRevision($this->revision->getRevisionId());

    $this->logger('content')->notice('Dnd layout entity: deleted %title revision %revision.', array('%title' => $this->revision->label(), '%revision' => $this->revision->getRevisionId()));
    drupal_set_message(t('Revision from %revision-date of Dnd layout entity %title has been deleted.', array('%revision-date' => format_date($this->revision->getRevisionCreationTime()), '%title' => $this->revision->label())));
    $form_state->setRedirect(
      'entity.dnd_layout_entity.canonical',
       array('dnd_layout_entity' => $this->revision->id())
    );
    if ($this->connection->query('SELECT COUNT(DISTINCT vid) FROM {dnd_layout_entity_field_revision} WHERE id = :id', array(':id' => $this->revision->id()))->fetchField() > 1) {
      $form_state->setRedirect(
        'entity.dnd_layout_entity.version_history',
         array('dnd_layout_entity' => $this->revision->id())
      );
    }
  }

}
