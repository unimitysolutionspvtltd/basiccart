<?php

namespace Drupal\basiccart\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;

/**
 * Plugin implementation of the 'addtocartsearch' formatter.
 *
 * @FieldFormatter(
 *   id = "addtocartsearch",
 *   module = "basiccart",
 *   label = @Translation("Add to cart Search Result"),
 *   field_types = {
 *     "addtocart"
 *   }
 * )
 */
class AddtoCartSearchFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    //print_r($items); die;
  	$entity = $items->getEntity();
  	$config = \Drupal::config('basiccart.settings');
    $elements = array();
    $url = new Url('basiccart.cartadd',array("nid"=>$entity->id()));
    foreach ($items as $delta => $item) {
      $elements[$delta] = array(
        '#type' => 'html_tag',
        '#tag' => 'a',
        '#value' => t($config->get('add_to_cart_button')),
        '#attributes' => array(
          'href' => $url->toString()."?entitytype=".$entity->getEntityTypeId(),
          'class' => 'button',
        ),
      );
    }
   // print_r($elements); die;
    return $elements;

    //return $elements;
  }

}
