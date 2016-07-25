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
    $url = new Url('basiccart.cartadd',array("nid"=>$entity->id()),array('query' => array('entitytype' => $entity->getEntityTypeId()),
       'absolute' => TRUE,));
  $link_options = array(
    'attributes' => array(
      'class' => array(
        'use-ajax',
        'button',
      ),
    ),
  );
  $url->setOptions($link_options);



// Link::createFromRoute(
//           $this->t('Modal Example'),
//           'fapi_example.modal_form',
//            [],
//            [
//              'attributes' => [
//                'class' => ['use-ajax'],
//                'data-dialog-type' => 'modal',
//              ],
//            ]
//         )->toString();

// array(
//         '#type' => 'html_tag',
//         '#tag' => 'a',
//         '#value' => t($config->get('add_to_cart_button')),
//         '#attributes' => array(
//           'href' => $url->toString()."?entitytype=".$entity->getEntityTypeId(),
//           'class' => 'button use-ajax',
//         ),


$link = new Link($this->t($config->get('add_to_cart_button')),$url);
    foreach ($items as $delta => $item) {
      $elements[$delta] = array('#type' => 'container',
      '#attributes' => array('id' => 'ajax-addtocart-message-'.$entity->id()),
      '#prefix' =>'<div class="addtocart-wrapper-container"><div class="addtocart-link-class">'.$link->toString()."</div>",
      '#suffix' =>'</div>',
      );
    }
   
     $elements['#attached']['library'][] = 'core/drupal.ajax';
   // print_r($elements); die;
    return $elements;

    //return $elements;
  }

}
