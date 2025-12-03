<?php

namespace Drupal\products\Commands;

use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\products\Plugin\ImporterPluginInterface;
use Drush\Commands\DrushCommands;
use Symfony\Component\Console\Input\InputOption;
use Drupal\products\Plugin\ImporterManager;

/**
 * Drush commands for products.
 */
class ProductCommands extends DrushCommands {

  /**
   * The importer manager.
   *
   * @var ImporterManager
   */
  protected $importerManager;

  /**
   * ProductCommands constructor.
   *
   * @param ImporterManager $importerManager
   *   The importer manager.
   */
  public function __construct(ImporterManager $importerManager) {
    $this->importerManager = $importerManager;
  }

  /**
   * Imports the Products.
   *
   * @param array $options
   *   The command options.
   *
   * @option importer
   *   The importer config ID to use.
   *
   * @command products-import-run
   * @aliases pir
   * @throws PluginException
   */
  public function import(array $options = ['importer' => InputOption::VALUE_OPTIONAL]): void
  {
    $importer = $options['importer'];

    if (!is_null($importer)) {
      $plugin = $this->importerManager->createInstanceFromConfig($importer);
      if (is_null($plugin)) {
        $this->logger()->error(t('The specified importer does not exist.'));
        return;
      }

      $this->runPluginImport($plugin);
      return;
    }

    $plugins = $this->importerManager->createInstanceFromAllConfigs();
    if (!$plugins) {
      $this->logger()->error(t('There are no importers to run.'));
      return;
    }

    foreach ($plugins as $plugin) {
      $this->runPluginImport($plugin);
    }
  }

  /**
   * Runs the import function of a given plugin.
   *
   * @param ImporterPluginInterface $plugin
   *   The importer plugin.
   */
  protected function runPluginImport(ImporterPluginInterface $plugin) : void {
    $result = $plugin->import();
    $message_values = ['@importer' => $plugin->getConfig()->label()];
    if ($result) {
      $this->logger()->notice(t('The "@importer" importer has been run.', $message_values));
      return;
    }

    $this->logger()->error(t('There was a problem running the "@importer" importer.', $message_values));
  }
}
