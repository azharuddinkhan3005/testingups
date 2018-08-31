<?php

/**
 * @file
 * Contains \Drupal\curemint_core\EventSubscriber\CuremintOrderEventSubscriber.
 */

namespace Drupal\curemint_core\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\state_machine\Event\WorkflowTransitionEvent;
use Drupal\Core\Entity\EntityInterface;

/**
 * Event Subscriber MyEventSubscriber.
 */
class CuremintOrderEventSubscriber implements EventSubscriberInterface {
  
  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = [
      'commerce_order.place.post_transition' => 'onPlaceTransition',
    ];
    return $events;
  }
  
  /**
   * {@inheritdoc}
   */
  public function __construct($config) {
    $this->orderTypeStatus = $config->get('curemint_config.settings')->get('order_type_state');
  }
  
  /**
   * Sets the order's placed status.
   *
   * @param \Drupal\state_machine\Event\WorkflowTransitionEvent $event
   *   The transition event.
   */
  public function onPlaceTransition(WorkflowTransitionEvent $event) {
    /** @var \Drupal\commerce_order\Entity\OrderInterface $order */
    $order = $event->getEntity();
    $order_state = $order->getState();
    //dpr($order->state->value);
    if (!$this->orderTypeStatus && $order_state->getString()== 'pending') {
      $order_state_transitions = $order_state->getTransitions();
      $order_state->applyTransition($order_state_transitions['approve']);
      $order->save();
    }
    
  }


}
