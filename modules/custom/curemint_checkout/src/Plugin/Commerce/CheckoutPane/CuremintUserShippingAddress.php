<?php

namespace Drupal\curemint_checkout\Plugin\Commerce\CheckoutPane;

use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneInterface;
use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Url;
use Drupal\curemint_checkout\CuremintCheckoutHelper;
use Drupal\commerce_checkout\Plugin\Commerce\CheckoutFlow\CheckoutFlowInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @CommerceCheckoutPane(
 *  id = "curemint_user_address",
 *  label = @Translation("Curemint User Address"),
 *  admin_label = @Translation("Curemint User Address"),
 * )
 */
class CuremintUserShippingAddress extends CheckoutPaneBase implements CheckoutPaneInterface {

  /**
   * The Curemint checkout helper service object.
   *
   * @var \Drupal\curemint_checkout\CuremintCheckoutHelper
   */
  protected $curemintCheckoutHelper;

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
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, CheckoutFlowInterface $checkout_flow, EntityTypeManagerInterface $entity_type_manager, CuremintCheckoutHelper $curemint_checkout_helper) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $checkout_flow, $entity_type_manager);

    $this->curemintCheckoutHelper = $curemint_checkout_helper;
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
      $container->get('curemint_checkout.checkout_helper')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildPaneForm(array $pane_form, FormStateInterface $form_state, array &$complete_form) {
    $userAttributes = $this->curemintCheckoutHelper->getUserAttributes();

    // If users address is empty, send the user back to cart page.
    if (empty($userAttributes['address'])) {
      $messenger = \Drupal::messenger();
      $messenger->addMessage($this->t('Please add your address to continue.'), 'error', FALSE);
      $response = new RedirectResponse(Url::fromRoute('commerce_cart.page', [], ['absolute' => TRUE])->toString());
      $response->send();
    }

    $pane_form['message'] = [
      '#markup' => '<div class="shipping-address selected">' . implode('', $userAttributes) . '</div>',
    ];
    return $pane_form;
  }

}
