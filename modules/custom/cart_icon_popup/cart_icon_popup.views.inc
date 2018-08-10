<?php

/**
 * @file
 * Provide views data for the Curemint cart icon popup module.
 */

/**
 * Implements hook_views_data().
 */
function cart_icon_popup_views_data() {
  $data['views']['curemint_order_total'] = [
    'title' => t('Curemint Order total'),
    'help' => t('Displays the curemint order total field, requires an Order ID argument.'),
    'area' => [
      'id' => 'curemint_order_total',
    ],
  ];
  return $data;
}
