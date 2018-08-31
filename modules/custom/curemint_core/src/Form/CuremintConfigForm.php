<?php

namespace Drupal\curemint_core\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure Curemint order type workflow settings for this site.
 */
class CuremintConfigForm extends ConfigFormBase {
  /** 
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'curemint_order_workflow_settings';
  }

  /** 
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'curemint_config.settings',
    ];
  }

  /** 
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('curemint_config.settings');
    
    $form['order_type_state_fiesldset'] = [
      '#type' => 'fieldset',
      '#title' => t('Enable / Disable'),
      '#collapsible' => TRUE, // Added
      '#collapsed' => FALSE,  // Added
    ];

    $form['order_type_state_fiesldset']['order_type_state'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Order Approval Mechanism'),
      '#description' => $this->t('If Enabled, All the orders placed will be moved to "Pending" State. If Disabled, All the orders placed will be moved to "Approved" State.'),
      '#default_value' => $config->get('order_type_state'),
    ];  

    return parent::buildForm($form, $form_state);
  }

  /** 
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
      // Retrieve the configuration
       $this->configFactory->getEditable('curemint_config.settings')
      // Set the submitted configuration setting
      ->set('order_type_state', $form_state->getValue('order_type_state'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}

