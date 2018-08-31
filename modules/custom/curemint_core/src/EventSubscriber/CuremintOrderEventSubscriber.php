<?php

/**
 * @file
 * Contains \Drupal\curemint_core\EventSubscriber\CuremintOrderEventSubscriber.
 */

namespace Drupal\curemint_core\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\state_machine\Event\WorkflowTransitionEvent;
use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Event Subscriber for Orders.
 */
class CuremintOrderEventSubscriber implements EventSubscriberInterface {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = [
      'commerce_order.place.pre_transition' => 'onPlaceTransition',
    ];
    return $events;
  }
  
  /**
   * {@inheritdoc}
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }
  
  /**
   * Sets the order's placed status.
   *
   * @param \Drupal\state_machine\Event\WorkflowTransitionEvent $event
   *   The transition event.
   */
  public function onPlaceTransition(WorkflowTransitionEvent $event) {
    $orderTypeState = $this->configFactory->get('curemint_core.settings')->get('order_type_state');
    /** @var \Drupal\commerce_order\Entity\OrderInterface $order */
    $order = $event->getEntity();
    $order_state = $order->getState();
    if (!$orderTypeState && $event->getToState()->getId() == 'pending') {
      $order_state_transitions = $order_state->getTransitions();
      $order_state->applyTransition($order_state_transitions['approve']);
    }
  }

}
