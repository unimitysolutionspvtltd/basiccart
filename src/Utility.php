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
     $user = \Drupal::currentUser();
     $config = self::cart_settings();
     $storage = new CartStorageSelect($user, $config->get('use_cart_table'));
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
   
  public function loggedinactioncart() {
	  $storage = static::get_storage();
    return $storage->loggedinactioncart();   
  } 
}

 
