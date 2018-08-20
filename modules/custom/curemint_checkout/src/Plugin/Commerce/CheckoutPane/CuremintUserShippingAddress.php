<?php

namespace Drupal\curemint_checkout\Plugin\Commerce\CheckoutPane;

use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneInterface;
use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Url;
use Drupal\Core\Messenger\MessengerInterface;

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
    $user = User::load(\Drupal::currentUser()->id());

    $userName = $user->field_name->value;
    $userAddress = render($user->field_address->view(['label' => 'hidden']));
    $userPhone = $user->field_number->value;

    // If $userAddress is empty, send the user back to cart page.
    if (empty($userAddress)) {
      $messenger = \Drupal::messenger();
      $messenger->addMessage($this->t('Please add your address to continue.'), 'error', FALSE);
      $response = new RedirectResponse(Url::fromRoute('commerce_cart.page', [], ['absolute' => TRUE])->toString());
      $response->send();
    }

    $pane_form['message'] = [
      '#markup' => '<div>' . $userName . $userAddress . $userPhone . '</div>',
    ];
    return $pane_form;
  }

}
