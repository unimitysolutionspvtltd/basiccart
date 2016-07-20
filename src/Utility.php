<?php
/**
 * @file
 * Contains \Drupal\basiccart\Utility
 */
namespace Drupal\basiccart;

use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;

class Utility {

  const FIELD_ADDTOCART    = 'addtocart';
  const FIELD_ORDERCONNECT = 'orderconnect';
  const BASICCART_ORDER    = 'basiccart_order';


/**
 * Returns the available price formats.
 *
 * @return $formats
 *   A list with the available price formats.
 */
public static function _price_format() {
  $config = self::cart_settings();
  $currency = $config->get('currency');
  return array(
    0 => t('1 234,00 @currency', array('@currency' => $currency)),
    1 => t('1 234.00 @currency', array('@currency' => $currency)),
    2 => t('1,234.00 @currency', array('@currency' => $currency)),
    3 => t('1.234,00 @currency', array('@currency' => $currency)),
    
    4 => t('@currency 1 234,00', array('@currency' => $currency)),
    5 => t('@currency 1 234.00', array('@currency' => $currency)),
    6 => t('@currency 1,234.00', array('@currency' => $currency)),
    7 => t('@currency 1.234,00', array('@currency' => $currency)),
  );
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

  if (isset($nid)) {
    return array("cart" => $_SESSION['basiccart']['cart'][$nid], "cart_quantity" => $_SESSION['basiccart']['cart_quantity'][$nid]);
  }
  if (isset($_SESSION['basiccart']['cart'])) {
    return array("cart" => $_SESSION['basiccart']['cart'], "cart_quantity" => $_SESSION['basiccart']['cart_quantity']);
  }
  // Empty cart.
  return array();
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
  if ($nid > 0) {
    unset($_SESSION['basiccart']['cart'][$nid]);
    unset($_SESSION['basiccart']['cart_quantity'][$nid]);
  }
  self::cart_updated_message();
  //drupal_goto('cart');
}

/**
 * Shopping cart reset.
 */
public static function empty_cart() {
  unset($_SESSION['basiccart']['cart']);
  unset($_SESSION['basiccart']['cart_quantity']);
}

/**
 * Formats the input $price in the desired format.
 *
 * @param float $price
 *   The price in the raw format.
 * @return $price
 *   The price in the custom format.
 */
public static function price_format($price) {
  $config = self::cart_settings();
  $format = $config->get('price_format');
  $currency = $config->get('currency');

  $price = (float) $price;
  switch ($format) {
    case 0:
      $price = number_format($price, 2, ',', ' ') . ' ' . $currency;
      break;
    
    case 1:
      $price = number_format($price, 2, '.', ' ') . ' ' . $currency;
      break;
    
    case 2:
      $price = number_format($price, 2, '.', ',') . ' ' . $currency;
      break;
    
    case 3:
      $price = number_format($price, 2, ',', '.') . ' ' . $currency;
      break;
    
    case 4:
      $price = $currency . ' ' . number_format($price, 2, ',', ' ');
      break;
    
    case 5:
      $price = $currency . ' ' . number_format($price, 2, '.', ' ');
      break;
    
    case 6:
      $price = $currency . ' ' . number_format($price, 2, '.', ',');
      break;
    
    case 7:
      $price = $currency . ' ' . number_format($price, 2, ',', '.');
      break;
    
    default:
      $price = number_format($price, 2, ',', ' ') . ' ' . $currency;
      break;
  }
  return $price;
}
  


  public static function  cart_settings() {
    $return = \Drupal::config('basiccart.settings');
    return $return;
  }

  public static function add_to_cart($id, $params = array()) {
    $config = self::cart_settings();
    if(!empty($params)) {
      $quantity = $params['quantity'];
      $entitytype = $params['entitytype'];
      $quantity = $params['quantity'];

      if ($id > 0 && $quantity > 0) {
            // If a node is added more times, just update the quantity.
            $cart = self::get_cart();
            if ($config->get('quantity_status') && !empty($cart['cart']) && in_array($id, array_keys($cart['cart']))) {
              // Clicked 2 times on add to cart button. Increment quantity.
              $_SESSION['basiccart']['cart_quantity'][$id] += $quantity;
            }
            else {
               $entity = \Drupal::entityTypeManager()->getStorage($entitytype)->load($id);
               $_SESSION['basiccart']['cart'][$id] = $entity;
               $_SESSION['basiccart']['cart_quantity'][$id] = $quantity;
            }
      }
      self::cart_updated_message();
    }  
  }

  /**
   * Returns the fields we need to create.
   * 
   * @return mixed
   *   Key / Value pair of field name => field type. 
   */
  public static function  get_fields_config($type = null) {

    $config = Utility::cart_settings();
    $fields['bundle_types'] = $config->get('content_type');
      foreach ($config->get('content_type') as $key => $value) {
        if($value){
          $bundles[$key] = $key;
        }
      }
    $fields['bundle_types'] = $bundles;
    if($type == self::FIELD_ORDERCONNECT) {

     $fields['bundle_types'] = array('basiccart_connect' =>  'basiccart_connect'); 
     $fields['fields'] =  array(
                      'basiccart_contentoconnect' => array(
                        'type' => 'entity_reference',
                        'entity_type' => 'node',
                        'bundle' => 'basiccart_connect',
                        'title' => t('Basic Cart Content Connect'),
                        'label' => t('Basic Cart Content Connect'),
                        'required' => FALSE,
                        'description' => t('Basic Cart content connect'),
                        'settings' => array('handler' => 'default:node',
                                            'handler_settings'=> array(
                                                  "target_bundles" =>  $bundles,
                                              ) 
                                            )

                          ),);
    }
    else {
     $fields['fields'] =  array(
                      'add_to_cart_price' => array(
                        'type' => 'decimal',
                        'entity_type' => 'node',
                        'title' => t($config->get('price_label')),
                        'label' => t($config->get('price_label')),
                        'required' => FALSE,
                        'description' => t('Please enter this item\'s price.'),
                        'widget' => array('type' => 'number'),
                        'formatter' => array('default'=> array(
                                'label' => 'inline',
                                'type' => 'number_decimal',
                                'weight' => 11,
                              ), /*'search_result' =>  'default',*/ 'teaser' => 'default') 
                      ),
                      'add_to_cart' => array(
                        'type' => 'addtocart',
                        'entity_type' => 'node',
                        'title' => t($config->get('add_to_cart_button')),
                        'label' => t($config->get('add_to_cart_button')),
                        'required' => FALSE,
                        'description' => '',
                        'widget' => array('type' => 'addtocart'),
                        'formatter' => array('default'=> array(
                                'label' => 'hidden',
                                'weight' => 11,
                                'type' => $config->get('quantity_status') ? 'addtocartwithquantity' : 'addtocart',
                              ), 'search_result' =>  array(
                                'label' => 'hidden',
                                'weight' => 11,
                                'type' => 'addtocartsearch',
                              ), 'teaser' => array(
                                'label' => 'hidden',
                                'weight' => 11,
                                'type' => 'addtocartsearch',
                              ),) 

                      ), 
                      );
                 
    }
    return (object) $fields;
  }

  public static function create_fields($type = null) {

    $fields = ($type == self::FIELD_ORDERCONNECT) ? self::get_fields_config(self::FIELD_ORDERCONNECT) : self::get_fields_config();
    $view_modes = \Drupal::entityManager()->getViewModes('node');
    foreach($fields->fields as $field_name => $config) {
     $field_storage = FieldStorageConfig::loadByName($config['entity_type'], $field_name);
     if(empty($field_storage)) {
        FieldStorageConfig::create(array(
            'field_name' => $field_name,
            'entity_type' => $config['entity_type'],
            'type' => $config['type'],
          ))->save();
     }
    }
    foreach($fields->bundle_types as  $bundle) {
      foreach ($fields->fields as $field_name => $config) {
        $config_array = array(
                'field_name' =>  $field_name,
                'entity_type' => $config['entity_type'],
                'bundle' => $bundle,
                'label' => $config['label'],
                'required' => $config['required'],
                
              );

        if(isset($config['settings'])) {
          $config_array['settings'] = $config['settings'];
        }
        $field = FieldConfig::loadByName($config['entity_type'], $bundle, $field_name);
        if(empty($field) && $bundle !== "" && !empty($bundle)) {
                FieldConfig::create($config_array)->save();
        }

        if($bundle !== "" && !empty($bundle)) {
          if(!empty($field)) {
             $field->setLabel($config['label'])->save();
             $field->setRequired($config['required'])->save();
          }
           if($config['widget']) {
              entity_get_form_display($config['entity_type'], $bundle, 'default')
              ->setComponent($field_name, $config['widget'])
              ->save();
           }
           if($config['formatter']) {  
             foreach ($config['formatter'] as $view => $formatter) {
                if (isset($view_modes[$view]) || $view == "default") { 
                   entity_get_display($config['entity_type'], $bundle, $view)
                  ->setComponent($field_name, !is_array($formatter) ? $config['formatter']['default'] : $formatter)
                  ->save();
                }  
             } 
          } 
        } 
      }
    }
  } 

  public static function cart_updated_message() {
    $config = Utility::cart_settings();
    drupal_set_message(t($config->get('cart_updated_message')));
  }

  public static function order_connect_fields() {
    self::create_fields(self::FIELD_ORDERCONNECT);
  }

  public static function is_basiccart_order($bundle) {
    if($bundle == self::BASICCART_ORDER) {
      return TRUE;
    }
    return FALSE;
  }

  public static function  checkout_settings() {
    $return = \Drupal::config('checkout.settings');
    return $return;
  }
}

 