<?php

namespace Drupal\curemint_checkout\Plugin\Commerce\CheckoutPane;

use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneInterface;
use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\checkout_pane\Plugin\Commerce\CheckoutPane\User;

/**
 * @CommerceCheckoutPane(
 *  id = "curemint_user_address",
 *  label = @Translation("Curemint User Address"),
 *  admin_label = @Translation("Curemint User Address"),
 * )
 */
class CuremintUserShippingAddress extends CheckoutPaneBase implements CheckoutPaneInterface {

  /**
   * {@inheritdoc}
   */
  public function buildPaneForm(array $pane_form, FormStateInterface $form_state, array &$complete_form) {
    $user = \Drupal\user\Entity\User::load(\Drupal::currentUser()->id());
    //$user_address = '';
    //dpr($user->field_address->address_line1 . ',' . $user->field_address->address_line2 . ',' . $user->field_address->administrative_area . ',' . $user->field_address->postal_code );
    $address_line1 = $user->field_address->address_line1;
    if ($user->field_address->address_line2) {
      $address_line2 = ' ' . $user->field_address->address_line2;
    }
    $city = $user->field_address->locality;
    $state = $user->field_address->administrative_area;
    $zip = $user->field_address->postal_code;
    $user_address = $address_line1 . $address_line2 . ' ' . $city . ' ' . $state . ' ' . $zip;
    $number = $user->field_number->value;
    $pane_form['message'] = [
      '#markup' => '<div>' . $user_address . '</div>',
    ];
    return $pane_form;
  }

}
