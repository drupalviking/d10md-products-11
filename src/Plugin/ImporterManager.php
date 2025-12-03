<?php
namespace Drupal\products\Plugin;

use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\products\Entity\ImporterInterface;

class ImporterManager extends DefaultPluginManager {

  /**
   * The entity type manager.
   *
   * @var EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  public function __construct(\Traversable           $namespaces,
                              CacheBackendInterface  $cache_backend,
                              ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/Importer', $namespaces, $module_handler,
      'Drupal\products\Plugin\ImporterPluginInterface',
      'Drupal\products\Attribute\Importer');
    $this->alterInfo('products_importer_info');
    $this->setCacheBackend($cache_backend, 'products_importer_plugins');
  }

  /**
   * Creates a new instance from a given config.
   *
   * @param string $id
   *   The ID of the config.
   *
   * @return ?object
   *   The plugin instance.
   * @throws PluginException
   */
  public function createInstanceFromConfig(string $id): ?object
  {
    $config = null;
    try {
      $config = $this->entityTypeManager->getStorage('importer')->load($id);
    } catch (InvalidPluginDefinitionException|PluginNotFoundException $e) {

    }
    if (!$config instanceof ImporterInterface) {
      return NULL;
    }

    return $this->createInstance($config->getPluginId(), ['config' => $config]);
  }

  /**
   * Creates instances from all the available configs.
   *
   * @return ImporterPluginInterface[]
   *   The plugin instances.
   * @throws PluginException
   */
  public function createInstanceFromAllConfigs(): array {
    $configs = null;
    try {
      $configs = $this->entityTypeManager->getStorage('importer')->loadMultiple();
    } catch (InvalidPluginDefinitionException|PluginNotFoundException $e) {

    }
    if (!$configs) {
      return [];
    }
    $plugins = [];
    foreach ($configs as $config) {
      $plugin = $this->createInstanceFromConfig($config->id());
      if (!$plugin) {
        continue;
      }

      $plugins[] = $plugin;
    }

    return $plugins;
  }
}
