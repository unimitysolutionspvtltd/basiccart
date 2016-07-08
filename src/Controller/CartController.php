<?php

/**
 * @file
 * Contains \Drupal\basiccart\Controller\CartController.
 */

namespace Drupal\basiccart\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\basiccart\Utility;
/**
 * Contains the cart controller.
 */
class CartController extends ControllerBase
{
 
  public function getCartPageTitle() {
    $config = Utility::cart_settings();
    $message = $config->get('cart_page_title');
    return $this->t($message);
  }
 
  public function cart() {
    $utility = new Utility();
    $cart = $utility::get_cart();
    $config= $utility::cart_settings(); 
  //print_r($cart); die;  
  $request = \Drupal::request();
  if ($route = $request->attributes->get(\Symfony\Cmf\Component\Routing\RouteObjectInterface::ROUTE_OBJECT)) {
   $route->setDefault('_title', t($config->get('cart_page_title')));
  }
  return !empty($cart['cart']) ? \Drupal::formBuilder()->getForm('\Drupal\basiccart\Form\CartForm') : array('#type' => 'markup','#markup' => t($config->get('empty_cart')),);


  } 
  
  public function remove_from_cart($nid) {
     $cart = Utility::remove_from_cart($nid); 
     return $this->redirect('basiccart.cart'); 
  }
  
}
  
