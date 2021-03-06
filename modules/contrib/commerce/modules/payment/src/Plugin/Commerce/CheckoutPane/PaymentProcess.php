<?php

namespace Drupal\commerce_payment\Plugin\Commerce\CheckoutPane;

use Drupal\commerce_checkout\Plugin\Commerce\CheckoutFlow\CheckoutFlowInterface;
use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneBase;
use Drupal\commerce_payment\Exception\DeclineException;
use Drupal\commerce_payment\Exception\PaymentGatewayException;
use Drupal\commerce_payment\Plugin\Commerce\PaymentGateway\ManualPaymentGatewayInterface;
use Drupal\commerce_payment\Plugin\Commerce\PaymentGateway\OffsitePaymentGatewayInterface;
use Drupal\commerce_payment\Plugin\Commerce\PaymentGateway\OnsitePaymentGatewayInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the payment process pane.
 *
 * @CommerceCheckoutPane(
 *   id = "payment_process",
 *   label = @Translation("Payment process"),
 *   default_step = "payment",
 *   wrapper_element = "container",
 * )
 */
class PaymentProcess extends CheckoutPaneBase {

  /**
   * The messenger.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Constructs a new PaymentProcess object.
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
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, CheckoutFlowInterface $checkout_flow, EntityTypeManagerInterface $entity_type_manager, MessengerInterface $messenger) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $checkout_flow, $entity_type_manager);

    $this->messenger = $messenger;
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
      $container->get('messenger')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'capture' => TRUE,
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationSummary() {
    if (!empty($this->configuration['capture'])) {
      $summary = $this->t('Transaction mode: Authorize and capture');
    }
    else {
      $summary = $this->t('Transaction mode: Authorize only');
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);
    $form['capture'] = [
      '#type' => 'radios',
      '#title' => $this->t('Transaction mode'),
      '#description' => $this->t('This setting is only respected if the chosen payment gateway supports authorizations.'),
      '#options' => [
        TRUE => $this->t('Authorize and capture'),
        FALSE => $this->t('Authorize only (requires manual capture after checkout)'),
      ],
      '#default_value' => (int) $this->configuration['capture'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);

    if (!$form_state->getErrors()) {
      $values = $form_state->getValue($form['#parents']);
      $this->configuration['capture'] = !empty($values['capture']);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function isVisible() {
    if ($this->order->getTotalPrice()->isZero()) {
      // Hide the pane for free orders, since they don't need a payment.
      return FALSE;
    }
    $payment_info_pane = $this->checkoutFlow->getPane('payment_information');
    if (!$payment_info_pane->isVisible() || $payment_info_pane->getStepId() == '_disabled') {
      // Hide the pane if the PaymentInformation pane has been disabled.
      return FALSE;
    }

    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function buildPaneForm(array $pane_form, FormStateInterface $form_state, array &$complete_form) {
    // The payment gateway is currently always required to be set.
    if ($this->order->get('payment_gateway')->isEmpty()) {
      $this->messenger->addError($this->t('No payment gateway selected.'));
      $this->redirectToPreviousStep();
    }

    /** @var \Drupal\commerce_payment\Entity\PaymentGatewayInterface $payment_gateway */
    $payment_gateway = $this->order->payment_gateway->entity;
    $payment_gateway_plugin = $payment_gateway->getPlugin();

    $payment_storage = $this->entityTypeManager->getStorage('commerce_payment');
    /** @var \Drupal\commerce_payment\Entity\PaymentInterface $payment */
    $payment = $payment_storage->create([
      'state' => 'new',
      'amount' => $this->order->getTotalPrice(),
      'payment_gateway' => $payment_gateway->id(),
      'order_id' => $this->order->id(),
    ]);
    $next_step_id = $this->checkoutFlow->getNextStepId($this->getStepId());

    if ($payment_gateway_plugin instanceof OnsitePaymentGatewayInterface) {
      try {
        $payment->payment_method = $this->order->payment_method->entity;
        $payment_gateway_plugin->createPayment($payment, $this->configuration['capture']);
        $this->redirectToStep($next_step_id);
      }
      catch (DeclineException $e) {
        $message = $this->t('We encountered an error processing your payment method. Please verify your details and try again.');
        $this->messenger->addError($message);
        $this->redirectToPreviousStep();
      }
      catch (PaymentGatewayException $e) {
        \Drupal::logger('commerce_payment')->error($e->getMessage());
        $message = $this->t('We encountered an unexpected error processing your payment method. Please try again later.');
        $this->messenger->addError($message);
        $this->redirectToPreviousStep();
      }
    }
    elseif ($payment_gateway_plugin instanceof OffsitePaymentGatewayInterface) {
      $pane_form['offsite_payment'] = [
        '#type' => 'commerce_payment_gateway_form',
        '#operation' => 'offsite-payment',
        '#default_value' => $payment,
        '#return_url' => $this->buildReturnUrl()->toString(),
        '#cancel_url' => $this->buildCancelUrl()->toString(),
        '#exception_url' => $this->buildPaymentInformationStepUrl()->toString(),
        '#exception_message' => $this->t('We encountered an unexpected error processing your payment. Please try again later.'),
        '#capture' => $this->configuration['capture'],
      ];

      $complete_form['actions']['next']['#value'] = $this->t('Proceed to @gateway', [
        '@gateway' => $payment_gateway_plugin->getDisplayLabel(),
      ]);
      // Make sure that the payment gateway's onCancel() method is invoked,
      // by pointing the "Go back" link to the cancel URL.
      $complete_form['actions']['next']['#suffix'] = Link::fromTextAndUrl($this->t('Go back'), $this->buildCancelUrl())->toString();
      // Hide the actions by default, they are not needed by gateways that
      // embed iframes or redirect via GET. The offsite-payment form can
      // choose to show them when needed (redirect via POST).
      $complete_form['actions']['#access'] = FALSE;

      return $pane_form;
    }
    elseif ($payment_gateway_plugin instanceof ManualPaymentGatewayInterface) {
      try {
        $payment_gateway_plugin->createPayment($payment);
        $this->redirectToStep($next_step_id);
      }
      catch (PaymentGatewayException $e) {
        \Drupal::logger('commerce_payment')->error($e->getMessage());
        $message = $this->t('We encountered an unexpected error processing your payment. Please try again later.');
        $this->messenger->addError($message);
        $this->redirectToPreviousStep();
      }
    }
    else {
      $this->redirectToStep($next_step_id);
    }
  }

  /**
   * Builds the URL to the "return" page.
   *
   * @return \Drupal\Core\Url
   *   The "return" page URL.
   */
  protected function buildReturnUrl() {
    return Url::fromRoute('commerce_payment.checkout.return', [
      'commerce_order' => $this->order->id(),
      'step' => 'payment',
    ], ['absolute' => TRUE]);
  }

  /**
   * Builds the URL to the "cancel" page.
   *
   * @return \Drupal\Core\Url
   *   The "cancel" page URL.
   */
  protected function buildCancelUrl() {
    return Url::fromRoute('commerce_payment.checkout.cancel', [
      'commerce_order' => $this->order->id(),
      'step' => 'payment',
    ], ['absolute' => TRUE]);
  }

  /**
   * Builds the URL to the payment information checkout step.
   *
   * @return \Drupal\Core\Url
   *   The URL to the payment information checkout step.
   */
  protected function buildPaymentInformationStepUrl() {
    return Url::fromRoute('commerce_checkout.form', [
      'commerce_order' => $this->order->id(),
      'step' => $this->checkoutFlow->getPane('payment_information')->getStepId(),
    ], ['absolute' => TRUE]);
  }

  /**
   * Redirects to a specific checkout step.
   *
   * @param string $step_id
   *   The step ID to redirect to.
   *
   * @throws \Drupal\commerce\Response\NeedsRedirectException
   */
  protected function redirectToStep($step_id) {
    $this->checkoutFlow->redirectToStep($step_id);
  }

  /**
   * Redirects to a previous checkout step on error.
   *
   * @throws \Drupal\commerce\Response\NeedsRedirectException
   */
  protected function redirectToPreviousStep() {
    $step_id = $this->checkoutFlow->getPane('payment_information')->getStepId();
    return $this->redirectToStep($step_id);
  }

}
