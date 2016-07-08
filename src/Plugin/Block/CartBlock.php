<?php

namespace Drupal\basiccart\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\basiccart\Utility;
use Drupal\Core\Url;
use Drupal\Core\Link;

/**
 * Provides a 'Basic Cart' block.
 *
 * @Block(
 *   id = "basiccart_cartblock",
 *   admin_label = @Translation("Basic Cart Block")
 * )
 */
class CartBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
  	$config = Utility::cart_settings();
     return array(
      //'#theme' => 'basiccart_cart_template',
      //'#basiccart' => array(),	
      '#type' => 'markup',
      '#title' => $config->get('cart_block_title'),
      '#markup' => self::get_cart_content(),
      '#cache' => array('max-age' => 0),
    );
  }

  public function get_cart_content() {
  	$Utility  = new Utility();
  	$config = $Utility::cart_settings();
    $cart = $Utility::get_cart();
    $quantity_enabled = $config->get('quantity_status');
    $total_price = $Utility::get_total_price();
    //print_r($total_price); die;
    $cart_cart = $cart['cart'];
    $output = '';
 if (empty($cart_cart)){
  $output .= '<div class="basiccart-grid basic-cart-block">'.$this->t($config->get('empty_cart')).'</div>';
  } 
else {
	$output .= '<div class="basiccart-grid basic-cart-block">';
	if(is_array($cart_cart) && count($cart_cart) >= 1){
		foreach($cart_cart as $nid => $node){
	  $langcode = $node->language()->getId();
      $price_value = $node->getTranslation($langcode)->get('add_to_cart_price')->getValue();
      $title = $node->getTranslation($langcode)->get('title')->getValue();
      $url = new Url('entity.node.canonical',array("node"=>$nid));
      $link = new Link($title[0]['value'],$url);
         $output .= '<div class="basiccart-cart-contents row">
          <div class="basiccart-cart-node-title cell">'.$link->toString().'</div>';
         if($quantity_enabled) {
          $output .= '<div class="basiccart-cart-quantity cell">'.$cart['cart_quantity'][$nid].'</div>';
          $output .= '<div class="basiccart-cart-x cell">x</div>';
         }
         
         $output .='<div class="basiccart-cart-unit-price cell">';
       $output .= isset($price_value[0]) ? '<strong>'.$Utility::price_format($price_value[0]['value']).'</strong>' : '';
       $output .='</div>
        </div>';
		}

       $output .=  '<div class="basiccart-cart-total-price-contents row">
        <div class="basiccart-total-price cell">
            '.$this->t($config->get('total_price_label')).':<strong>'.$Utility::price_format($total_price->total).'</strong>
        </div>
      </div>';

        if (!empty ($config->get('vat_state'))) {
       $output .='<div class="basiccart-block-total-vat-contents row">
          <div class="basiccart-total-vat cell">'.$this->t('Total VAT').': <strong>'.$Utility::price_format($total_price->vat).'</strong></div>
        </div>';
        }
      $url = new Url('basiccart.cart');
      $link = new Link($this->t($config->get('view_cart_button')),$url);
        $output .='<div class="basiccart-cart-checkout-button basiccart-cart-checkout-button-block row">
        '.$link->toString().'
      </div>';
	}
	$output .= '</div>';
}
  return $output;
}

}
