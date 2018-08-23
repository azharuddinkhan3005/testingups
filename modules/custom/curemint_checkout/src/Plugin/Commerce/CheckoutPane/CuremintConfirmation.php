<?php

namespace Drupal\curemint_checkout\Plugin\Commerce\CheckoutPane;

use Drupal\Core\Form\FormStateInterface;
use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneInterface;
use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneBase;
use Drupal\curemint_checkout\CuremintCheckoutHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\commerce_checkout\Plugin\Commerce\CheckoutFlow\CheckoutFlowInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\commerce_cart\CartProvider;
use Drupal\cart_icon_popup\CuremintTotalSummary;

/**
 * Provides the Curemint Confirmation pane.
 *
 * @CommerceCheckoutPane(
 *   id = "curemint_confirmation",
 *   label = @Translation("Curemint Confimation"),
 *   default_step = "confirmation",
 * )
 */
class CuremintConfirmation extends CheckoutPaneBase implements CheckoutPaneInterface {

  /**
   * The Curemint checkout helper service object.
   *
   * @var \Drupal\curemint_checkout\CuremintCheckoutHelper
   */
  protected $curemintCheckoutHelper;

  /**
   * The Commerce cart provider service object.
   *
   * @var \Drupal\commerce_cart\CartProvider
   */
  protected $commerceCartProvider;

  /**
   * The Curemint Total Summary service object.
   *
   * @var \Drupal\cart_icon_popup\CuremintTotalSummary
   */
  protected $curemintTotalSummary;

  /**
   * Constructs a new CheckoutPaneBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\commerce_checkout\Plugin\Commerce\CheckoutFlow\CheckoutFlowInterface $checkout_flow
   *   The parent checkout flow.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\curemint_checkout\curemintCheckoutHelper $curemint_checkout_helper
   *   The curemint checkout helper service object.
   * @param \Drupal\commerce_cart\CartProvider $commerce_cart_provider
   *   The commerce cart provider service object.
   * @param \Drupal\cart_icon_popup\CuremintTotalSummary $curemint_total_summary
   *   The total summary service object.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, CheckoutFlowInterface $checkout_flow, EntityTypeManagerInterface $entity_type_manager, CuremintCheckoutHelper $curemint_checkout_helper, CartProvider $commerce_cart_provider, CuremintTotalSummary $curemint_total_summary) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $checkout_flow, $entity_type_manager);

    $this->curemintCheckoutHelper = $curemint_checkout_helper;
    $this->commerceCartProvider = $commerce_cart_provider;
    $this->curemintTotalSummary = $curemint_total_summary;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition, CheckoutFlowInterface $checkout_flow = NULL) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $checkout_flow,
      $container->get('entity_type.manager'),
      $container->get('curemint_checkout.checkout_helper'),
      $container->get('commerce_cart.cart_provider'),
      $container->get('cart_icon_popup.order_total_summary')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildPaneForm(array $pane_form, FormStateInterface $form_state, array &$complete_form) {
    // Get user shipping info.
    $userAttributes = $this->curemintCheckoutHelper->getUserAttributes();
    $pane_form['shipping_address'] = [
      '#markup' => '<div class="title">' . $this->t('Shipping Address') . '</div><div class="shipping-address-confirm">' . implode('', $userAttributes) . '</div>',
    ];

    // Get order total.
    $totals = $this->curemintTotalSummary->buildTotals();
    $pane_form['order_total'] = [
      '#markup' => '<div class="order-total">' . $totals['subtotal'] . '</div>',
    ];

    // Get cart products.
    $carts = $this->commerceCartProvider->getCarts();
    /* @var \Drupal\commerce_order\Entity\Order $cart */
    foreach ($carts as $cart) {
      $pane_form['items']['#prefix'] = '<div class="cart-items">';
      $pane_form['items']['#suffix'] = '</div>';
      /* @var \Drupal\commerce_order\Entity\OrderItem $item */
      foreach ($cart->getItems() as $item) {
        $view_builder = $this->entityTypeManager->getViewBuilder('commerce_product_variation');
        $storage = $this->entityTypeManager->getStorage('commerce_product_variation');
        $pane_form['items'][] = $view_builder->view($storage->load($item->getPurchasedEntityId()), 'cart');
      }
    }
    return $pane_form;
  }

}
