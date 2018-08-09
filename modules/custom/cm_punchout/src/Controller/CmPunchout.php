<?php

namespace Drupal\cm_punchout\Controller;

use Drupal\Core\Controller\ControllerBase;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Returns responses for various routes.
 */
class CmPunchout extends ControllerBase {

  /**
   * This method provides appropriate punchout error messages on homepage.
   *
   * @param string $type
   *   The type of error.
   *
   * @return object
   *   Returns a RedirectResponse object.
   */
  public function errorRedirect($type) {
    $cm_punchout_messenger = \Drupal::service('cm_punchout.messenger');
    $cm_punchout_messenger->showErrorMessage($type);

    return new RedirectResponse(\Drupal::url('<front>'));
  }

  /**
   * This method provides punchout order message.
   *
   * @param Symfony\Component\HttpFoundation\Request $request
   *   An object which gets us the response for various protocols.
   *
   * @return array
   *   An array depicting the POOM.
   */
  public function getPunchoutOrderMessage(Request $request) {
    $punchout_order_message_base64_encoded = $request->request->all();
    $punchout_order_message = json_decode(base64_decode($punchout_order_message_base64_encoded['params']), TRUE);
    return [
      '#type' => 'markup',
      '#markup' => new JsonResponse($punchout_order_message),
    ];
  }

}
