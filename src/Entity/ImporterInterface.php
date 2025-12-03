<?php

namespace Drupal\products\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Url;

/**
 * Importer configuration entity.
 */
interface ImporterInterface extends ConfigEntityInterface {

  /**
   * Returns the Url where the import can get the data from.
   *
   * @return ?Url
   *   The URL.
   */
  public function getUrl() : ?Url;

  /**
   * Returns the Importer plugin ID to be used by this importer.
   *
   * @return ?string
   *   The plugin ID.
   */
  public function getPluginId() : ?string;

  /**
   * Whether to update existing products if they have already been imported.
   *
   * @return bool
   *   Whether to update existing products.
   */
  public function updateExisting() : bool;

  /**
   * Returns the source of the products.
   *
   * @return ?string
   *   The source.
   */
  public function getSource() : ?string;

  /**
   * Returns the Product type that needs to be created.
   *
   * @return ?string
   *   The product bundle to create.
   */
  public function getBundle() : ?string;
}
