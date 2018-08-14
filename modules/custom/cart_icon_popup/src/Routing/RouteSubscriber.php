<?php
/**
 * @file
 * Contains \Drupal\cart_icon_popup\Routing\RouteSubscriber.
 */

namespace Drupal\cart_icon_popup\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {
  /**
   * {@inheritdoc}
   */
  public function alterRoutes(RouteCollection $collection) {
    if ($route = $collection->get('commerce_cart.page')) {
      $route->setDefault('_title', 'Your shopping cart');
    }
  }
}
