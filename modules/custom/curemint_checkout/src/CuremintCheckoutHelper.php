<?php

namespace Drupal\curemint_checkout;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Class CuremintCheckoutHelper
 */
class CuremintCheckoutHelper {

  /**
   * The user storage.
   *
   * @var \Drupal\user\UserStorageInterface
   */
  protected $userStorage;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $account;

  /**
   * Constructs an authentication provider object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The current user account.
   */
  public function __construct(EntityTypeManagerInterface $entity_manager, AccountInterface $account) {
    $this->userStorage = $entity_manager->getStorage('user');
    $this->account = $account;
  }

  /**
   * Returns the current user address.
   *
   * @return array
   */
  public function getUserAttributes() {
    $user = $this->userStorage->load($this->account->id());

    $userAttributes['name'] = $user->field_name->value;
    $userAddress = $user->field_address->view(['label' => 'hidden']);
    $userAttributes['address'] = render($userAddress);
    $userAttributes['userPhone'] = $user->field_number->value;

    return $userAttributes;
  }
}
