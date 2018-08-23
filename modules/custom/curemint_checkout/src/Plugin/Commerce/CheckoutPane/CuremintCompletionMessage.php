<?php

namespace Drupal\curemint_checkout\Plugin\Commerce\CheckoutPane;

use Drupal\Core\Form\FormStateInterface;

/**
 * Provides the completion message pane.
 *
 * @CommerceCheckoutPane(
 *   id = "curemint_completion_message",
 *   label = @Translation("Curemint Completion message"),
 *   default_step = "complete",
 * )
 */
class CuremintCompletionMessage extends CuremintConfirmation {

  /**
   * {@inheritdoc}
   */
  public function buildPaneForm(array $pane_form, FormStateInterface $form_state, array &$complete_form) {
    // Prepare Order Markup.
    $orderMarkup = '<div class="title">';
    $orderMarkup .= $this->t('Order');
    $orderMarkup .= '</div>';
    $orderMarkup .= '<div>';
    $orderMarkup .= '<span class="order-number">';
    $orderMarkup .= $this->t('Order # ');
    $orderMarkup .= $this->order->getOrderNumber();
    $orderMarkup .= '</span>';
    $orderMarkup .= '<span class="order-placed">';
    $orderMarkup .= $this->t('Order Placed: ');
    $orderMarkup .= date('m/d/Y', $this->order->getPlacedTime());
    $orderMarkup .= '</span>';
    $orderMarkup .= '</div>';

    $pane_form['order_detail'] = [
      '#markup' => $orderMarkup,
    ];

    // Add all other markup from review page.
    $pane_form = parent::buildPaneForm($pane_form, $form_state, $complete_form);

    return $pane_form;
  }

}
