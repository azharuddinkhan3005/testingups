<?php

namespace Drupal\cart_icon_popup\Plugin\Commerce\CheckoutPane;

use Drupal\Core\Form\FormStateInterface;
use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneBase;
use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneInterface;
use Drupal\cart_icon_popup\CuremintTotalSummary;
use Drupal\commerce_checkout\Plugin\Commerce\CheckoutFlow\CheckoutFlowInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the Curemint Order summary pane.
 *
 * @CommerceCheckoutPane(
 *   id = "curemint_order_summary",
 *   label = @Translation("Curemint Order summary"),
 *   default_step = "_sidebar",
 *   wrapper_element = "container",
 * )
 */
class CuremintOrderSummary extends CheckoutPaneBase implements CheckoutPaneInterface {

  /**
   * The Curemint Total Summary service object.
   *
   * @var \Drupal\cart_icon_popup\CuremintTotalSummary
   */
  protected $curemintTotalSummary;

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
   * @param \Drupal\cart_icon_popup\CuremintTotalSummary $curemint_total_summary
   *   The curemint total summary service object.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, CheckoutFlowInterface $checkout_flow, EntityTypeManagerInterface $entity_type_manager, CuremintTotalSummary $curemint_total_summary) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $checkout_flow, $entity_type_manager);

    $this->curemintTotalSummary = $curemint_total_summary;
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
      $container->get('cart_icon_popup.order_total_summary')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildPaneForm(array $pane_form, FormStateInterface $form_state, array &$complete_form) {
    $totals = $this->curemintTotalSummary->buildTotals();
    $total = [
      'savings' => [
        'label' => $this->t('Your Savings'),
        'value' => $totals['savings'],
      ],
      'quantity' => [
        'label' => $this->t('List item'),
        'value' => $totals['quantity'],
      ],
      'subtotal' => [
        'label' => $this->t('Estimated Total'),
        'value' => $totals['subtotal'],
      ],
    ];

    $pane_form['summary'] = [
      '#theme' => 'curemint_checkout_order_summary',
      '#totals' => $total,
    ];

    return $pane_form;
  }

}
