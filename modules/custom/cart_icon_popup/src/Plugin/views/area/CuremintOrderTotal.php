<?php

namespace Drupal\cart_icon_popup\Plugin\views\area;

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
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->orderStorage = $entity_type_manager->getStorage('commerce_order');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
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
            $store_id = 1;
            $order_type = 'default';
            $cart_provider = \Drupal::service('commerce_cart.cart_provider');
            $entity_manager = \Drupal::entityManager();
            $store = $entity_manager->getStorage('commerce_store')->load($store_id);
            $cart = $cart_provider->getCart($order_type, $store);
            if ($cart) {
              $items = $cart->getItems();
              $total_formulary_price = ($cart->total_price->number);
              $total_msrp_price = NULL;
              foreach ($items as $item) {
                $quantity = $item->getQuantity();
                $msrp_price = ($item->getPurchasedEntity()->field_msrp_price->number);
                if ($quantity && $msrp_price) {
                  $total_msrp_price += $quantity * $msrp_price;
                }
              }
              $savings = ($total_msrp_price - $total_formulary_price);
            }

            return [
              '#title' => 'Custom Header',
              '#theme' => 'curemint_order_total_summary',
              '#savings' => $savings,
              '#subtotal' => (float)$total_formulary_price,
            ];
        }
      }
    }

    return [];
  }

}
