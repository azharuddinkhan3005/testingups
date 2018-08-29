<?php

namespace Drupal\cart_icon_popup\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\cart_icon_popup\CuremintTotalSummary;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\block\Entity\Block;

/**
 * Provides a 'header' block.
 *
 * @Block(
 *   id = "custom_header",
 *   admin_label = @Translation("Custom Header Block"),
 * )
 */
class CustomHeader extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The Curemint Total Summary service object.
   *
   * @var \Drupal\cart_icon_popup\CuremintTotalSummary
   */
  protected $curemintTotalSummary;

  /**
   * Constructor of the class.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\cart_icon_popup\CuremintTotalSummary $curemint_total_summary
   *   The curemint total summary service object.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, CuremintTotalSummary $curemint_total_summary) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->curemintTotalSummary = $curemint_total_summary;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('cart_icon_popup.order_total_summary')
    );
  }

  public function build() {
    $totals = $this->curemintTotalSummary->buildTotals();

    $params = \Drupal::request()->query->all();

    return [
      '#title' => 'Custom Header',
      '#theme' => 'cart_icon_popup',
      '#items_count' => $totals['quantity'],
      '#savings' => $totals['savings'],
      '#keyword' => !empty($params['keyword']) ? $params['keyword'] : '',
    ];
  }

}
