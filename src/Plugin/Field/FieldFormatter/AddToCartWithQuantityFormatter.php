<?php

namespace Drupal\basiccart\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;
/**
 * Plugin implementation of the 'addtocartwithquantity' formatter.
 *
 * @FieldFormatter(
 *   id = "addtocartwithquantity",
 *   module = "basiccart",
 *   label = @Translation("Add to cart with quantity"),
 *   field_types = {
 *     "addtocart"
 *   }
 * )
 */
class AddtoCartWithQuantityFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $config = \Drupal::config('basiccart.settings');
   // $id			= $config->get('quantity_status') ? '\Drupal\basiccart\Form\AddToCartWithQuantity' : '\Drupal\basiccart\Form\AddToCart';
   // $id     = '\Drupal\basiccart\Form\AddToCartWithQuantity';
    $entity = $items->getEntity();
    $config = \Drupal::config('basiccart.settings');
    $elements = array();

     $option = [
    'query' => ['entitytype' => $entity->getEntityTypeId(),'quantity' => ''],
    'absolute' => TRUE
    ];
    $url = Url::fromRoute('basiccart.cartadd',["nid"=>$entity->id()],$option);
   // print_r($url->toString()); die;
    $link = '<a id="forquantitydynamictext_'.$entity->id().'" class="basiccart-get-quantity button use-basiccart-ajax" href="'.$url->toString().'">'.$this->t($config->get('add_to_cart_button')).'</a>';
  $link_options = [
    'attributes' => [
      'class' => [
        'basiccart-get-quantity',
        'use-basiccart-ajax',
        'button',
      ],
    ],
  ];
  $url->setOptions($link_options);

$quantity_content = $config->get('quantity_status') ? '<div id="quantity-wrapper_'.$entity->id().'" class="addtocart-quantity-wrapper-container"></div>' : '';
//$link = new Link($this->t($config->get('add_to_cart_button')),$url);
    foreach ($items as $delta => $item) {
      $elements[$delta] = ['#type' => 'container',
      '#attributes' => ['class' => 'ajax-addtocart-wrapper' ,'id' => 'ajax-addtocart-message-'.$entity->id()],
      '#prefix' =>'<div class="addtocart-wrapper-container">'.$quantity_content.'<div class="addtocart-link-class">'.$link."</div>",
      '#suffix' =>'</div>',
      ];
    }
   
     $elements['#attached']['library'][] = 'core/drupal.ajax';
   // print_r($elements); die;
    return $elements;
    /*$unit_price = $entity->getTranslation($langcode)->get('add_to_cart_price')->getValue();
    $unit_price = $unit_price ? $unit_price[0]['value'] : 0;
    if(empty($unit_price)) {
      $form = array();
      drupal_set_message(t('No price configured for this product'),'warning');
    }else{
       $form = \Drupal::formBuilder()->getForm($id,$entity->id(),$entity->getEntityTypeId(),$langcode);
    }
   
    return $form; */
  }

}
