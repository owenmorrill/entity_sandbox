<?php

namespace Drupal\dnd_pb\Form;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\dnd_pb\Entity\DndLayoutEntityInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for reverting a Dnd layout entity revision.
 *
 * @ingroup dnd_pb
 */
class DndLayoutEntityRevisionRevertForm extends ConfirmFormBase {


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
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected $dateFormatter;

  /**
   * Constructs a new DndLayoutEntityRevisionRevertForm.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $entity_storage
   *   The Dnd layout entity storage.
   * @param \Drupal\Core\Datetime\DateFormatterInterface $date_formatter
   *   The date formatter service.
   */
  public function __construct(EntityStorageInterface $entity_storage, DateFormatterInterface $date_formatter) {
    $this->DndLayoutEntityStorage = $entity_storage;
    $this->dateFormatter = $date_formatter;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager')->getStorage('dnd_layout_entity'),
      $container->get('date.formatter')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dnd_layout_entity_revision_revert_confirm';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return t('Are you sure you want to revert to the revision from %revision-date?', ['%revision-date' => $this->dateFormatter->format($this->revision->getRevisionCreationTime())]);
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
    return t('Revert');
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return '';
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
    // The revision timestamp will be updated when the revision is saved. Keep
    // the original one for the confirmation message.
    $original_revision_timestamp = $this->revision->getRevisionCreationTime();

    $this->revision = $this->prepareRevertedRevision($this->revision, $form_state);
    $this->revision->revision_log = t('Copy of the revision from %date.', ['%date' => $this->dateFormatter->format($original_revision_timestamp)]);
    $this->revision->save();

    $this->logger('content')->notice('Dnd layout entity: reverted %title revision %revision.', ['%title' => $this->revision->label(), '%revision' => $this->revision->getRevisionId()]);
    drupal_set_message(t('Dnd layout entity %title has been reverted to the revision from %revision-date.', ['%title' => $this->revision->label(), '%revision-date' => $this->dateFormatter->format($original_revision_timestamp)]));
    $form_state->setRedirect(
      'entity.dnd_layout_entity.version_history',
      array('dnd_layout_entity' => $this->revision->id())
    );
  }

  /**
   * Prepares a revision to be reverted.
   *
   * @param \Drupal\dnd_pb\Entity\DndLayoutEntityInterface $revision
   *   The revision to be reverted.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return \Drupal\dnd_pb\Entity\DndLayoutEntityInterface
   *   The prepared revision ready to be stored.
   */
  protected function prepareRevertedRevision(DndLayoutEntityInterface $revision, FormStateInterface $form_state) {
    $revision->setNewRevision();
    $revision->isDefaultRevision(TRUE);
    $revision->setRevisionCreationTime(REQUEST_TIME);

    return $revision;
  }

}
