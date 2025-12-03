<?php

namespace Drupal\products\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Represents a Product entity.
 */
interface ProductInterface extends ContentEntityInterface, EntityChangedInterface {

  /**
   * Gets the Product name.
   *
   * @return string
   *   The name.
   */
  public function getName() : string;

  /**
   * Sets the Product name.
   *
   * @param string $name
   *   The name.
   *
   * @return ProductInterface
   *   The called Product entity.
   */
  public function setName(string $name) : self;

  /**
   * Gets the Product number.
   *
   * @return int
   *   The product number.
   */
  public function getProductNumber() : int;

  /**
   * Sets the Product number.
   *
   * @param int $number
   *   The product number.
   *
   * @return ProductInterface
   *   The called Product entity.
   */
  public function setProductNumber(int $number) : self;

  /**
   * Gets the Product remote ID.
   *
   * @return string
   *   The product remote ID.
   */
  public function getRemoteId() : string;

  /**
   * Sets the Product remote ID.
   *
   * @param string $id
   *   The product remote ID.
   *
   * @return ProductInterface
   *   The called Product entity.
   */
  public function setRemoteId(string $id) : self;

  /**
   * Gets the Product source.
   *
   * @return string
   *   The product source.
   */
  public function getSource() : string;

  /**
   * Sets the Product source.
   *
   * @param string $source
   *   The product source.
   *
   * @return ProductInterface
   *   The called Product entity.
   */
  public function setSource(string $source) : self;

  /**
   * Gets the Product creation timestamp.
   *
   * @return int
   *   The created timestamp.
   */
  public function getCreatedTime() : int;

  /**
   * Sets the Product creation timestamp.
   *
   * @param int $timestamp
   *   The created timestamp.
   *
   * @return ProductInterface
   *   The called Product entity.
   */
  public function setCreatedTime(int $timestamp) : self;

}
