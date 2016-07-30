<?php

namespace Drupal\basiccart;

use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\Core\Url;
use Drupal\Core\Link;

abstract class Settings
{
  protected $checkout_settings;
  protected $cart_settings;
  
  const FIELD_ADDTOCART    = 'addtocart';
  const FIELD_ORDERCONNECT = 'orderconnect';
  const BASICCART_ORDER    = 'basiccart_order';

    // Force Extending class to define this method
  abstract public static function is_basiccart_order($bundle);

  public  function  checkout_settings() {
    $return = \Drupal::config('basiccart.checkout');
    return $return;
  }

  public static function  cart_settings() {
    $return = \Drupal::config('basiccart.settings');
    return $return;
  }

  public static  function cart_updated_message() {
    $config = static::cart_settings();
    drupal_set_message(t($config->get('cart_updated_message')));
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
   * Returns the fields we need to create.
   * 
   * @return mixed
   *   Key / Value pair of field name => field type. 
   */
  public static function  get_fields_config($type = null) {

    $config = self::cart_settings();
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
                              ), 'search_result' =>  'default', 'teaser' => 'default') 
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
                                'type' => 'addtocart',
                              ), 'teaser' => array(
                                'label' => 'hidden',
                                'weight' => 11,
                                'type' => 'addtocart',
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
                  //$formatter['type'] = ($formatter['type'] == "addcartsearch") ? "addtocart"  : $formatter['type'];
                   entity_get_display($config['entity_type'], $bundle, $view)
                  ->setComponent($field_name, !is_array($formatter) ? $config['formatter']['default'] : $config['formatter']['default'])
                  ->save();
                }  
             } 
          } 
        } 
      }
    }
  } 



  public static function order_connect_fields() {
    self::create_fields(self::FIELD_ORDERCONNECT);
  }

    
} 