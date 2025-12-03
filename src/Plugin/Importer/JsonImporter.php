<?php

namespace Drupal\products\Plugin\Importer;

use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\products\Attribute\Importer;
use Drupal\products\Entity\ImporterInterface;
use Drupal\products\Entity\ProductInterface;
use Drupal\products\Plugin\ImporterBase;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Product importer from a JSON format.
 */
#[Importer(
  id: "json",
  label: new TranslatableMarkup("JSON Importer"),
)]
class JsonImporter extends ImporterBase {

  /**
   * {@inheritdoc}
   * @throws GuzzleException|EntityStorageException
   */
  public function import(): bool {
    $data = $this->getData();
    if (!$data) {
      return FALSE;
    }

    if (!isset($data->products)) {
      return FALSE;
    }

    $products = $data->products;
    foreach ($products as $product) {
      $this->persistProduct($product);
    }
    return TRUE;
  }

  /**
   * Loads the product data from the remote URL.
   *
   * @return object
   *   The data from the remote URL.
   * @throws GuzzleException
   */
  private function getData(): object
  {
    /** @var ImporterInterface $config */
    $config = $this->configuration['config'];
    $request = $this->httpClient->get($config->getUrl()->toString());
    $string = $request->getBody()->getContents();
    return json_decode($string);
  }

  /**
   * Saves a Product entity from the remote data.
   *
   * @param object $data
   *   The data to persist.
   * @throws EntityStorageException
   */
  private function persistProduct(object $data): void {
    $existing = NULL;
    /** @var ImporterInterface $config */
    $config = $this->configuration['config'];

    try {
      $existing = $this->entityTypeManager->getStorage('product')->loadByProperties([
        'remote_id' => $data->id,
        'source' => $config->getSource(),
      ]);
    } catch (InvalidPluginDefinitionException|PluginNotFoundException $e) {

    }
    if (!$existing) {
      $values = [
        'remote_id' => $data->id,
        'source' => $config->getSource(),
        'type' => $config->getBundle()
      ];
      /** @var ProductInterface $product */
      try {
        $product = $this->entityTypeManager->getStorage('product')->create($values);
      } catch (InvalidPluginDefinitionException|PluginNotFoundException $e) {

      }
      $product->setName($data->name);
      $product->setProductNumber($data->number);
      $product->save();
      return;
    }

    if (!$config->updateExisting()) {
      return;
    }

    /** @var ProductInterface $product */
    $product = reset($existing);
    $product->setName($data->name);
    $product->setProductNumber($data->number);
    $product->save();
  }
}
