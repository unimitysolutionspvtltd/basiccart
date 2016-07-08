<?php

namespace Drupal\basiccart\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;

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
    $id			= $config->get('quantity_status') ? '\Drupal\basiccart\Form\AddToCartWithQuantity' : '\Drupal\basiccart\Form\AddToCart';
   // $id     = '\Drupal\basiccart\Form\AddToCartWithQuantity';
    $entity = $items->getEntity();
    $unit_price = $entity->getTranslation($langcode)->get('add_to_cart_price')->getValue();
    $unit_price = $unit_price ? $unit_price[0]['value'] : 0;
    if(empty($unit_price)) {
      $form = array();
      drupal_set_message(t('No price configured for this product'),'warning');
    }else{
       $form = \Drupal::formBuilder()->getForm($id,$entity->id(),$entity->getEntityTypeId(),$langcode);
    }
   
    return $form;
  }

}
