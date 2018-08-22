<?php

namespace Drupal\curemint_checkout\Plugin\Commerce\CheckoutFlow;

use Drupal\commerce_checkout\Plugin\Commerce\CheckoutFlow\CheckoutFlowWithPanesBase;
use Drupal\commerce_checkout\Plugin\Commerce\CheckoutFlow\MultistepDefault;
use Drupal\Core\Form\FormStateInterface;

/**
 * @CommerceCheckoutFlow(
 *  id = "curemint_checkout_flow",
 *  label = @Translation("Shipping Address Checkout"),
 * )
 */
class CuremintCheckoutFlow extends CheckoutFlowWithPanesBase {

  /**
   * {@inheritdoc}
   */
   public function getSteps() {
     return [
       'shipping_address' => [
         'label' => $this->t('1. Shipping Address'),
         'has_sidebar' => TRUE,
       ],
       'confirmation' => [
         'label' => $this->t('2. Confirmation'),
         'next_label' => $this->t('Continue'),
         'has_sidebar' => TRUE,
       ],
       'complete' => [
         'label' => $this->t('3. Complete'),
         'next_label' => $this->t('Continue'),
         'has_sidebar' => FALSE,
       ],
     ];
   }

}
