<?php

namespace Drupal\products\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityMalformedException;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Url;
use Drupal\products\Entity\Importer;
use Drupal\products\Entity\ImporterInterface;
use Drupal\products\Plugin\ImporterManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form for creating/editing Importer entities.
 */
class ImporterForm extends EntityForm {
  /**
   * The importer manager.
   *
   * @var ImporterManager
   */
  protected ImporterManager $importerManager;

  /**
   * ImporterForm constructor.
   *
   * @param ImporterManager $importer_manager
   * @param MessengerInterface $messenger
   */
  public function __construct(ImporterManager $importer_manager, MessengerInterface $messenger) {
    $this->importerManager = $importer_manager;
    $this->messenger = $messenger;
  }

  public static function create(ContainerInterface $container): ImporterForm|static
  {
    return new static(
      $container->get('products.importer_manager'),
      $container->get('messenger')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state): array {
    $form = parent::form($form, $form_state);

    /** @var ImporterInterface $importer */
    $importer = $this->entity;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#maxlength' => 255,
      '#default_value' => $importer->label(),
      '#description' => $this->t('Name of the Importer.'),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $importer->id(),
      '#machine_name' => [
        'exists' => '\Drupal\products\Entity\Importer::load',
      ],
      '#disabled' => !$importer->isNew(),
    ];

    $form['url'] = [
      '#type' => 'url',
      '#default_value' => $importer->getUrl() instanceof Url ? $importer->getUrl()->toString() : '',
      '#title' => $this->t('Url'),
      '#description' => $this->t('The URL to the import resource'),
      '#required' => TRUE,
    ];

    $definitions = $this->importerManager->getDefinitions();
    /**
    foreach ($definitions as $id => $definition) {
      $options[$id] = $definition['label'];
    }*/
    $options = array_map(function ($definition) {
      return $definition['label'];
    }, $definitions);

    $form['plugin'] = [
      '#type' => 'select',
      '#title' => $this->t('Plugin'),
      '#default_value' => $importer->getPluginId(),
      '#options' => $options,
      '#description' => $this->t('The plugin to be used with this importer.'),
      '#required' => TRUE,
    ];

    $form['update_existing'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Update existing'),
      '#description' => $this->t('Whether to update existing products if already imported.'),
      '#default_value' => $importer->updateExisting(),
    ];

    $form['source'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Source'),
      '#description' => $this->t('The source of the products.'),
      '#default_value' => $importer->getSource(),
    ];

    $form['bundle'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'product_type',
      '#title' => $this->t('Product type'),
      '#default_value' => $importer->getBundle() ? $this->entityTypeManager->getStorage('product_type')->load($importer->getBundle()) : NULL,
      '#description' => $this->t('The type of products that need to be created.'),
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   * @throws EntityStorageException|EntityMalformedException
   */
  public function save(array $form, FormStateInterface $form_state): void {
    /** @var Importer $importer */
    $importer = $this->entity;
    $status = $importer->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger->addMessage($this->t('Created the %label Importer.', [
          '%label' => $importer->label(),
        ]));
        break;

      default:
        $this->messenger->addMessage($this->t('Saved the %label Importer.', [
          '%label' => $importer->label(),
        ]));
    }

    $form_state->setRedirectUrl($importer->toUrl('collection'));
  }
}
