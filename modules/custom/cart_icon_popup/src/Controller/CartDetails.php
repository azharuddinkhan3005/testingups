<?php
/**
 * @file
 * @author Karan Sen
 * Contains \Drupal\cart_icon_popup\Controller\CartDetails.
 */
namespace Drupal\cart_icon_popup\Controller;

use Drupal\commerce_product\Entity\ProductInterface;
use Drupal\commerce_product\Entity\ProductVariation;
use Drupal\Core\Entity\ContentEntityBase;


/**
 * Provides route responses for the Cart popup module.
 */
class CartDetails {
  /**
   * Returns a page having the Cart popup details.
   *
   * @return array
   *   A simple renderable array.
   */

   /* public function __construct(ProductVariation $product_variation, ProductInterface $ProductInterface){
     $this->product_variations = $product_variation;
   } */
  public function getCartDetails() {
    $cart = NULL;
    return $cart;
  }
}
?>
