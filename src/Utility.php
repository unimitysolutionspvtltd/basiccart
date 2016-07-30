<?php
/**
 * @file
 * Contains \Drupal\basiccart\Utility
 */
namespace Drupal\basiccart;


use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\basiccart\Settings;
use Drupal\basiccart\CartStorageSelect;

class Utility extends Settings {

  private $storage;

  const FIELD_ADDTOCART    = 'addtocart';
  const FIELD_ORDERCONNECT = 'orderconnect';
  const BASICCART_ORDER    = 'basiccart_order';

  private static function get_storage() {
     $storage = new CartStorageSelect('E');
     return $storage;
  }


  public static function is_basiccart_order($bundle) {
    if($bundle == self::BASICCART_ORDER) {
      return TRUE;
    }
    return FALSE;
  }

/**
 * Function for shopping cart retrieval.
 *
 * @param int $nid
 *   We are using the node id to store the node in the shopping cart
 *
 * @return mixed
 *   Returning the shopping cart contents.
 *   An empty array if there is nothing in the cart
 */
public static function get_cart($nid = NULL) {
   $storage = static::get_storage();
   return $storage->get_cart($nid);
}

/**
 * Returns the final price for the shopping cart.
 *
 * @return mixed $total_price
 *   The total price for the shopping cart. 
 */
public static function get_total_price() {

  $config = self::cart_settings();
  $vat = $config->get('vat_state');
  // Building the return array.
  $return = array(
    'price' => 0,
    'vat' => 0,
    'total' => 0,
  );
  $cart = self::get_cart();

  if (empty($cart)) {
    return (object) $return;
  }

  $total_price = 0;
  foreach ($cart['cart'] as $nid => $node) {
     $langcode = $node->language()->getId();

     $value = $node->getTranslation($langcode)->get('add_to_cart_price')->getValue();
    if (isset($cart['cart_quantity'][$nid]) && isset($value[0]['value'])) {
      $total_price += $cart['cart_quantity'][$nid] * $value[0]['value'];
    }
   $value = 0;
  }
  
  $return['price'] = $total_price;
  
  // Checking whether to apply the VAT or not.
  $vat_is_enabled = (int) $config->get('vat_state');
  if (!empty ($vat_is_enabled) && $vat_is_enabled) {
    $vat_value = (float) $config->get('vat_value');
    $vat_value = ($total_price * $vat_value) / 100;
    $total_price += $vat_value;
    // Adding VAT and total price to the return array.
    $return['vat'] = $vat_value;
  }
  
  $return['total'] = $total_price;
  return (object) $return;
}

  /**
   * Callback function for cart/remove/.
   *
   * @param int $nid
   *   We are using the node id to remove the node in the shopping cart
   */
  public static function remove_from_cart($nid = NULL) {
    $nid = (int) $nid;
    $storage = static::get_storage();
    $storage->remove_from_cart($nid);
  }

/**
 * Shopping cart reset.
 */
  public static function empty_cart() {
    $storage = static::get_storage();
    $storage->empty_cart();
  }

  public static function add_to_cart($id, $params = array()) {
  $storage = static::get_storage();
  $storage->add_to_cart($id, $params);  
  }


  public function get_cart_content() {
    $Utility  = $this;
    $config = $Utility->cart_settings();
    $cart = $Utility->get_cart();
    $quantity_enabled = $config->get('quantity_status');
    $total_price = $Utility->get_total_price();
    $cart_cart = isset($cart['cart']) ? $cart['cart'] : array();
    $output = '';
 if (empty($cart_cart)){
  $output .= '<div class="basiccart-grid basic-cart-block">'.t($config->get('empty_cart')).'</div>';
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
       $output .= isset($price_value[0]) ? '<strong>'.$Utility->price_format($price_value[0]['value']).'</strong>' : '';
       $output .='</div>
        </div>';
    }

       $output .=  '<div class="basiccart-cart-total-price-contents row">
        <div class="basiccart-total-price cell">
            '.t($config->get('total_price_label')).':<strong>'.$Utility->price_format($total_price->total).'</strong>
        </div>
      </div>';
        if (!empty ($config->get('vat_state'))) {
       $output .='<div class="basiccart-block-total-vat-contents row">
          <div class="basiccart-total-vat cell">'.t('Total VAT').': <strong>'.$Utility->price_format($total_price->vat).'</strong></div>
        </div>';
        }
      $url = new Url('basiccart.cart');
      //$link = new Link($this->t($config->get('view_cart_button')),$url);
      $link = "<a href='".$url->toString()."' class='button'>".t($config->get('view_cart_button'))."</a>";
        $output .='<div class="basiccart-cart-checkout-button basiccart-cart-checkout-button-block row">
        '.$link.'
      </div>';
  }
  $output .= '</div>';
}


  return $output;
}
}

 