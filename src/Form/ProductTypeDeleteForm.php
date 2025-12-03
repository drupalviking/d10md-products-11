<?php

namespace Drupal\products\Form;

use Drupal\Core\Entity\EntityConfirmFormBase;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form handler for deleting ProductType entities.
 */
class ProductTypeDeleteForm extends EntityConfirmFormBase {

  /**
   * ProductTypeDeleteForm constructor.
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
  public static function create(ContainerInterface $container): ProductTypeDeleteForm|static {
    return new static(
      $container->get('messenger')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion(): TranslatableMarkup {
    return $this->t('Are you sure you want to delete %name?', ['%name' => $this->entity->label()]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() : Url {
    return new Url('entity.product_type.collection');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() : TranslatableMarkup {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   * @throws EntityStorageException
   */
  public function submitForm(array &$form, FormStateInterface $form_state) : void{
    $this->entity->delete();
    $this->messenger->addMessage($this->t('Deleted @entity product type.', ['@entity' => $this->entity->label()]));
    $form_state->setRedirectUrl($this->getCancelUrl());
  }
}
