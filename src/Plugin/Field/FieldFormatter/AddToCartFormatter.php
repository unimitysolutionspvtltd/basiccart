<?php

namespace Drupal\basiccart\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Plugin implementation of the 'addtocart' formatter.
 *
 * @FieldFormatter(
 *   id = "addtocart",
 *   module = "basiccart",
 *   label = @Translation("Add to cart"),
 *   field_types = {
 *     "addtocart"
 *   }
 * )
 */
class AddtoCartFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
  	$entity = $items->getEntity();
  	
    $form = \Drupal::formBuilder()->getForm('\Drupal\basiccart\Form\AddToCart',$entity->id(),$entity->getEntityTypeId(),$langcode);
    $elements = $form;


    return $elements;
  }

}
