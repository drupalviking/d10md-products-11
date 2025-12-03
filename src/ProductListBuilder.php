<?php
namespace Drupal\products;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityMalformedException;

/**
 * EntityListBuilderInterface implementation for the Product entities
 */
class ProductListBuilder extends EntityListBuilder {

  /**
   * @inheritDoc
   */
  public function buildHeader(): array {
    $header['id'] = t('Product ID');
    $header['name'] = t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * @inheritDoc
   * @throws EntityMalformedException
   */
  public function buildRow(EntityInterface $entity): array {
    $row['id'] = $entity->id();
    $row['name'] = $entity->toLink();
    return $row + parent::buildRow($entity);
  }
}
