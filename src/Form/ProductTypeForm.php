<?php

namespace Drupal\products\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityMalformedException;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\products\Entity\ProductTypeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form handler for creating/editing ProductType entities.
 */
class ProductTypeForm extends EntityForm {

  /**
   * ProductTypeForm constructor.
   *
   * @param MessengerInterface $messenger
   *   The messenger.
   */
  public function __construct(MessengerInterface $messenger) {
    $this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): ProductTypeForm|static {
    return new static(
      $container->get('messenger')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) : array{
    $form = parent::form($form, $form_state);

    /** @var ProductTypeInterface $product_type */
    $product_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $product_type->label(),
      '#description' => $this->t('Label for the Product type.'),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $product_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\products\Entity\ProductType::load',
      ],
      '#disabled' => !$product_type->isNew(),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   * @throws EntityStorageException|EntityMalformedException
   */
  public function save(array $form, FormStateInterface $form_state) : void {
    $product_type = $this->entity;
    $status = $product_type->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger->addMessage($this->t('Created the %label Product type.', [
          '%label' => $product_type->label(),
        ]));
        break;

      default:
        $this->messenger->addMessage($this->t('Saved the %label Product type.', [
          '%label' => $product_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($product_type->toUrl('collection'));
  }
}
