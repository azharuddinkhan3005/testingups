<?php

namespace Drupal\cart_icon_popup;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\commerce_cart\CartProviderInterface;
use CommerceGuys\Intl\Formatter\CurrencyFormatterInterface;

class CuremintTotalSummary {

  /**
   * Store ID defaults to 1.
   */
  const STORE_ID = '1';

  /**
   * Order Type defaults to 'default'.
   */
  const ORDER_TYPE = 'default';

  /**
   * The store storage.
   *
   * @var \Drupal\Core\Entity\Sql\SqlContentEntityStorage
   */
  protected $storeStorage;

  /**
   * The cart provider service.
   *
   * @var \Drupal\commerce_cart\CartProviderInterface
   */
  protected $cartProvider;

  /**
   * The currency formatter.
   *
   * @var \CommerceGuys\Intl\Formatter\CurrencyFormatterInterface
   */
  protected $currencyFormatter;

  /**
   * Constructs a new CuremintTotalSummary object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\commerce_cart\CartProviderInterface $cart_provider
   *   The cart provider service.
   * @param \CommerceGuys\Intl\Formatter\CurrencyFormatterInterface $currency_formatter
   *   The currency formatter.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, CartProviderInterface $cart_provider, CurrencyFormatterInterface $currency_formatter) {
    $this->storeStorage = $entity_type_manager->getStorage('commerce_store');
    $this->cartProvider = $cart_provider;
    $this->currencyFormatter = $currency_formatter;
  }

  /**
   * Build totals from the current cart object in session.
   *
   * @param array/mixed $cart
   *   Object of cart or order to fetch totals from.
   *
   * @return array
   */
  public function buildTotals($cart = []) {
    if (empty($cart)) {
      $store = $this->storeStorage->load(self::STORE_ID);
      $cart = $this->cartProvider->getCart(self::ORDER_TYPE, $store);
    }

    if ($cart) {
      $items = $cart->getItems();
      $totalFormularyPrice = $cart->total_price->number;
      $totalCurrencyCode = $cart->total_price->currency_code;
      $totalMsrpPrice = $totalQuantity = NULL;
      foreach ($items as $item) {
        $quantity = $item->getQuantity();
        $totalQuantity += $quantity;
        $msrpPrice = $item->getPurchasedEntity()->field_msrp_price->number;
        if ($quantity && $msrpPrice) {
          $totalMsrpPrice += $quantity * $msrpPrice;
        }
      }
      $savings = ($totalMsrpPrice - $totalFormularyPrice);
    }

    if (!empty($savings) && !empty($totalFormularyPrice)) {
      return [
        'quantity' => $totalQuantity,
        'savings' => $this->currencyFormatter->format($savings, $totalCurrencyCode),
        'subtotal' => $this->currencyFormatter->format($totalFormularyPrice, $totalCurrencyCode),
        'user_id' => \Drupal::currentUser()->id(), //@todo: Inject service.
      ];
    }
    return [
      'quantity' => '',
      'savings' => '',
      'subtotal' => '',
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

}
