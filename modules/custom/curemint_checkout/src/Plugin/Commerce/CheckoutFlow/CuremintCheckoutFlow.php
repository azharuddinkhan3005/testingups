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
     // Note that previous_label and next_label are not the labels
     // shown on the step itself. Instead, they are the labels shown
     // when going back to the step, or proceeding to the step.
     return [
       'shipping_address' => [
         'label' => $this->t('Shipping Address'),
         'has_sidebar' => TRUE,
         'previous_label' => $this->t('Go back'),
       ],
       'review' => [
         'label' => $this->t('Review'),
         'next_label' => $this->t('Continue to review'),
         'previous_label' => $this->t('Go back'),
         'has_sidebar' => TRUE,
       ],
     ] + parent::getSteps();
   }

}
