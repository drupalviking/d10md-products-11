<?php

namespace Drupal\products\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\Attribute\ConfigEntityType;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;

/**
 * Defines the Importer entity.
 */
#[ConfigEntityType(
  id: "importer",
  label: new TranslatableMarkup("Importer"),
  config_prefix: "importer",
  entity_keys: [
    "id" => "id",
    "label" => "label",
    "uuid" => "uuid",
  ],
  handlers: [
    "list_builder" => "Drupal\products\ImporterListBuilder",
    "form" => [
      "add" => "Drupal\products\Form\ImporterForm",
      "edit" => "Drupal\products\Form\ImporterForm",
      "delete" => "Drupal\products\Form\ImporterDeleteForm",
    ],
    "route_provider" => [
      "html" => "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
    ],
  ],
  links: [
    "add-form" => "/admin/structure/importer/add",
    "edit-form" => "/admin/structure/importer/{importer}/edit",
    "delete-form" => "/admin/structure/importer/{importer}/delete",
    "collection" => "/admin/structure/importer",
  ],
  admin_permission: "administer site configuration",
  config_export: [
    "id",
    "label",
    "url",
    "plugin",
    "update_existing",
    "source",
    "bundle",
  ],
)]
class Importer extends ConfigEntityBase implements ImporterInterface {
  /**
   * The Importer ID.
   *gi
   * @var string
   */
  protected string $id;

  /**
   * The Importer label.
   *
   * @var string
   */
  protected string $label;

  /**
   * The URL from where the import file can be retrieved.
   *
   * @var string
   */
  protected string $url;

  /**
   * The plugin ID of the plugin to be used for processing this import.
   *
   * @var string
   */
  protected string $plugin;

  /**
   * Whether to update existing products if they have already been imported.
   *
   * @var bool
   */
  protected bool $update_existing = TRUE;

  /**
   * The source of the products.
   *
   * @var string
   */
  protected string $source;

  /**
   * The product bundle.
   *
   * @var string
   */
  protected string $bundle;

  /**
   * {@inheritdoc}
   */
  public function getUrl() : ?Url {
    return !empty($this->url) ? Url::fromUri($this->url) : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getPluginId(): ?string {
    return (!empty($this->plugin)) ? $this->plugin : null;
  }

  /**
   * {@inheritdoc}
   */
  public function updateExisting() : bool {
    return $this->update_existing;
  }

  /**
   * {@inheritdoc}
   */
  public function getSource() : ?string {
    return !empty($this->source) ? $this->source : null;
  }

  /**
   * {@inheritdoc}
   */
  public function getBundle(): ?string {
    return !empty($this->bundle) ? $this->bundle : null;
  }
}
