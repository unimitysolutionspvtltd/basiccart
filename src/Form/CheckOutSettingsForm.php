<?php
/**
 * @file
 * Contains \Drupal\basiccart\Form\CartSettingsForm
 */
namespace Drupal\basiccart\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\basiccart\Utility;

/**
 * Configure checkout settings for this site.
 */
class CheckOutSettingsForm extends ConfigFormBase {
  /** 
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'basiccart_admin_checkout_settings';
  }

  /** 
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'basiccart.settings',
    ];
  }

  /** 
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('basiccart.settings');

    $form['email_messages'] = array(
    '#title' => t('Email messages'),
    '#type' => 'fieldset',
    '#description' => t('Here you can customize the mails sent to the site administrator and customer, after an order is placed.'),
    );

    $form['email_messages']['basiccart_administrator_emails'] = array(
    '#title' => t('Administrator emails'),
    '#type' => 'textarea',
    '#description' => t('After each placed order, an email with the order details will be sent to all the addresses from the list above. Please add one email address per line.'),
    //'#default_value' => $config->get('content_type'),
    );


    $form['email_messages']['basiccart_subject_admin'] = array(
    '#title' => t('Subject'),
    '#type' => 'textfield',
    '#description' => t("Subject field for the administrator's email."),
   // '#default_value' => $config->get('currency'),
    );

    $form['email_messages']['basiccart_administer_message'] = array(
    '#title' => t('Admin email'),
    '#type' => 'textarea',
    '#description' => t('This email will be sent to the site administrator just after an order is placed. Please see all available tokens below. For listing the products, please use: [basiccart_order:products]'),
    //'#default_value' => $config->get('content_type'),
    );
    
    $form['email_messages']['basiccart_sendemailto_user'] = array(
    '#type' => 'checkbox',
    '#title' => $this->t('Send an email to the customer after an order is placed'),
    //'#default_value' => $config->get('price_status'),
    //'#description' => t('Send an email to the customer after an order is placed'),      
    );

    $form['email_messages']['basiccart_subject_user'] = array(
    '#title' => t('Subject'),
    '#type' => 'textfield',
    '#description' => t("Subject field for the user's email."),
   // '#default_value' => $config->get('currency'),
    );

    $form['email_messages']['basiccart_user_message'] = array(
    '#title' => t('User email'),
    '#type' => 'textarea',
    '#description' => t('This email will be sent to the user just after an order is placed. Please see all available tokens below. For listing the products, please use: [basic_cart_order:products]'),
    //'#default_value' => $config->get('content_type'),
    );

     $form['thankyou'] = array(
    '#title' => t('Thank you page'),
    '#type' => 'fieldset',
    '#description' => t('Thank you page customization.'),
    );


    $form['thankyou']['basiccart_thankyou_page_title'] = array(
    '#title' => t('Title'),
    '#type' => 'textfield',
    '#description' => t("Thank you page title."),
   // '#default_value' => $config->get('currency'),
    );

    $form['thankyou']['basiccart_thankyou_page_text'] = array(
    '#title' => t('Text'),
    '#type' => 'textarea',
    '#description' => t('Thank you page text.'),
    //'#default_value' => $config->get('content_type'),
    );

    return parent::buildForm($form, $form_state);
  }

  /** 
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    /*$content_types = $this->config('basiccart.settings')->get('content_type');
    $this->config('basiccart.settings')
      ->set('cart_page_title', $form_state->getValue('basiccart_cart_page_title'))
      ->set('empty_cart',$form_state->getValue('basiccart_empty_cart'))
      ->set('cart_block_title',$form_state->getValue('basiccart_cart_block_title'))
      ->set('view_cart_button',$form_state->getValue('basiccart_view_cart_button'))
      ->set('cart_update_button',$form_state->getValue('basiccart_cart_update_button'))
      ->set('cart_updated_message',$form_state->getValue('basiccart_cart_updated_message'))
      ->set('quantity_status',$form_state->getValue('basiccart_quantity_status'))
      ->set('quantity_label',$form_state->getValue('basiccart_quantity_label'))
      ->set('price_status',$form_state->getValue('basiccart_price_status'))
      ->set('price_label',$form_state->getValue('basiccart_price_label'))
      ->set('price_format',$form_state->getValue('basiccart_price_format'))      
      ->set('total_price_status',$form_state->getValue('basiccart_total_price_status'))
      ->set('total_price_label',$form_state->getValue('basiccart_total_price_label'))
      ->set('currency_status',$form_state->getValue('basiccart_currency_status'))
      ->set('currency',$form_state->getValue('basiccart_currency'))
      ->set('vat_state',$form_state->getValue('basiccart_vat_state'))
      ->set('vat_value',$form_state->getValue('basiccart_vat_value'))
      ->set('add_to_cart_button',$form_state->getValue('basiccart_add_to_cart_button'))
      ->set('add_to_cart_redirect',$form_state->getValue('basiccart_add_to_cart_redirect'))            
      ->set('content_type',$form_state->getValue('basiccart_content_types'))
      ->set('order_status',$form_state->getValue('basiccart_order_status'))
      ->save();
    Utility::create_fields();
    //Utility::order_connect_fields();
    // To save enabled content types not from settings 
    foreach($form_state->getValue('basiccart_content_types') as $key => $value){
     $content_types[$key] = $value ? $value : $content_types[$key];
    }
    $this->config('basiccart.settings')->set('content_type',$content_types)->save(); */   
    parent::submitForm($form, $form_state);
  }
}

