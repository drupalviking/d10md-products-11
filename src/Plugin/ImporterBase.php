<?php

namespace Drupal\products\Plugin;

use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\products\Entity\ImporterInterface;
use GuzzleHttp\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base class for Importer plugins.
 */
abstract class ImporterBase extends PluginBase implements ImporterPluginInterface, ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var EntityTypeManager
   */
  protected EntityTypeManager $entityTypeManager;

  /**
   * The HTTP client.
   *
   * @var Client
   */
  protected Client $httpClient;

  /**
   * {@inheritdoc}
   * @throws PluginException
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManager $entityTypeManager, Client $httpClient) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entityTypeManager;
    $this->httpClient = $httpClient;

    if (!isset($configuration['config'])) {
      throw new PluginException('Missing Importer configuration.');
    }

    if (!$configuration['config'] instanceof ImporterInterface) {
      throw new PluginException('Wrong Importer configuration.');
    }
  }

  /**
   * {@inheritdoc}
   * @throws PluginException
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition):
    ImporterBase|ContainerFactoryPluginInterface|static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('http_client')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(): ImporterInterface {
    return $this->configuration['config'];
  }
}
