<?php

namespace Drupal\cart_icon_popup\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'header' block.
 *
 * @Block(
 *   id = "custom_header",
 *   admin_label = @Translation("Custom Header Block"),
 *
 * )
 */
class CustomHeader extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function getItemCount(){
    //$cart_manager = \Drupal::service('Drupal\commerce_product\Entity\ProductVariation');
   $store_id = 1;
   //$current_store = \Drupal::service('commerce_store.current_store');
   $order_type = 'default';
   $cart_manager = \Drupal::service('commerce_cart.cart_manager');
   $cart_provider = \Drupal::service('commerce_cart.cart_provider');
   $entity_manager = \Drupal::entityManager();
   $store = $entity_manager->getStorage('commerce_store')->load($store_id);
   //$store = $current_store->getStore();
   $cart = $cart_provider->getCart($order_type, $store);
   $items_count = count($cart-> getItems());

   return $items_count;
  }
  public function build() {
    $store_id = 1;
    $items_count = $savings = 0;
    //$current_store = \Drupal::service('commerce_store.current_store');
    $order_type = 'default';
    $cart_manager = \Drupal::service('commerce_cart.cart_manager');
    $cart_provider = \Drupal::service('commerce_cart.cart_provider');
    $entity_manager = \Drupal::entityManager();
    $store = $entity_manager->getStorage('commerce_store')->load($store_id);
    //$store = $current_store->getStore();
    $cart = $cart_provider->getCart($order_type, $store);
    if ($cart){
      $items = $cart-> getItems();
      $items_count = count($items);
      $total_formulary_price = ($cart->total_price->number);
      $total_msrp_price = NULL;
      foreach($items as $item){
        $quantity = $item->getQuantity();
        $msrp_price = ($item->getPurchasedEntity()->field_msrp_price->number);
        if ($quantity && $msrp_price) {
          $total_msrp_price += $quantity * $msrp_price;
        }
      }
      $savings = ($total_msrp_price - $total_formulary_price);
    }
    return array(
      '#title' => 'Custom Header',
      '#theme' => 'cart_icon_popup',
      '#items_count' => $items_count,
      '#savings' => $savings
    );
  }
}
