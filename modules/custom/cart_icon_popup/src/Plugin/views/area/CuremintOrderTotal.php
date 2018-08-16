<?php

namespace Drupal\cart_icon_popup\Plugin\views\area;

use Drupal\cart_icon_popup\CuremintTotalSummary;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\area\AreaPluginBase;
use Drupal\views\Plugin\views\argument\NumericArgument;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines an order total area handler for Curemint.
 *
 * @ingroup views_area_handlers
 *
 * @ViewsArea("curemint_order_total")
 */
class CuremintOrderTotal extends AreaPluginBase {

  /**
   * The order storage.
   *
   * @var \Drupal\Core\Entity\Sql\SqlContentEntityStorage
   */
  protected $orderStorage;

  /**
   * The Curemint Total Summary service object.
   *
   * @var \Drupal\cart_icon_popup\CuremintTotalSummary
   */
  protected $curemintTotalSummary;

  /**
   * Constructs a new CuremintOrderTotal instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\cart_icon_popup\CuremintTotalSummary $curemint_total_summary
   *   The total summary service object.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, CuremintTotalSummary $curemint_total_summary) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->orderStorage = $entity_type_manager->getStorage('commerce_order');
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
      $container->get('entity_type.manager'),
      $container->get('cart_icon_popup.order_total_summary')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    $form['empty']['#description'] = $this->t("Even if selected, this area handler will never render if a valid order cannot be found in the View's arguments.");
  }

  /**
   * {@inheritdoc}
   */
  public function render($empty = FALSE) {
    if (!$empty || !empty($this->options['empty'])) {
      foreach ($this->view->argument as $name => $argument) {
        // First look for an order_id argument.
        if (!$argument instanceof NumericArgument) {
          continue;
        }
        if (!in_array($argument->getField(), ['commerce_order.order_id', 'commerce_order_item.order_id'])) {
          continue;
        }
        if ($order = $this->orderStorage->load($argument->getValue())) {
          $totals = $this->curemintTotalSummary->buildTotals();
          return [
            '#title' => 'Custom Header',
            '#theme' => 'curemint_order_total_summary',
            '#savings' => $totals['savings'],
            '#subtotal' => $totals['subtotal'],
          ];
        }
      }
    }

    return [];
  }

}
