<?php

namespace Drupal\cm_punchout;

use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Url;

/**
 * Defines re-usable services and functions to display message for punchout.
 */
class CmPunchoutMessenger {

  protected $messenger;

  /**
   * Constructs a new MessengerInterface object.
   *
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The entity type definition.
   */
  public function __construct(MessengerInterface $messenger) {
    $this->messenger = $messenger;
  }

  /**
   * Function to add the message to the queue based on type.
   *
   * @param string $type
   *   Code defining type of punchout error.
   */
  public function showErrorMessage($type) {
    switch ($type) {
      case 'ERR_ANON':
        $login_path = Url::fromRoute('user.login')->toString();
        $message = t('You are currently not logged in. Click here to <a href="@link">login</a>.', ['@link' => $login_path]);
        break;

      case 'ERR_NOADDR':
        $message = 'Please add a shipping address.';
    }
    $this->messenger->addMessage($message);
  }

}
