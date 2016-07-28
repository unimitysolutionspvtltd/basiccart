<?php

/**
 * @file
 * Contains \Drupal\basiccart\Controller\CartController.
 */

namespace Drupal\basiccart\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\basiccart\Utility;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Symfony\Component\HttpFoundation\JsonResponse;


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

    \Drupal::service('page_cache_kill_switch')->trigger();
    $utility = new Utility();
    $cart = $utility::get_cart();
    $config= $utility::cart_settings(); 
    $request = \Drupal::request();

    if ($route = $request->attributes->get(\Symfony\Cmf\Component\Routing\RouteObjectInterface::ROUTE_OBJECT)) {
      $route->setDefault('_title', t($config->get('cart_page_title')));
    }

    return !empty($cart['cart']) ? \Drupal::formBuilder()->getForm('\Drupal\basiccart\Form\CartForm') : array('#type' => 'markup','#markup' => t($config->get('empty_cart')),);

  } 
  
  public function remove_from_cart($nid) {
    \Drupal::service('page_cache_kill_switch')->trigger();
    $cart = Utility::remove_from_cart($nid); 
    return new RedirectResponse(Url::fromUri($_SERVER['HTTP_REFERER'])->toString());  
  }

  public function add_to_cart($nid) {
    \Drupal::service('page_cache_kill_switch')->trigger();
    $query = \Drupal::request()->query;
    $utility = new Utility();
    $config = $utility::cart_settings();
    $param['entitytype'] = $query->get('entitytype') ?  $query->get('entitytype') : "node";
    $param['quantity'] = $query->get('quantity') ? (is_numeric($query->get('quantity')) ? (int) $query->get('quantity') : 1) : 1;
    $utility::add_to_cart($nid, $param);
    drupal_get_messages();
    $response = new \stdClass();
    $response->status = TRUE;
    $response->text = '<p class="messages messages--status">'.t($config->get('added_to_cart_message')).'</p>';
    $response->id = 'ajax-addtocart-message-'.$nid;
    $response->block = $utility->get_cart_content();
    return new JsonResponse($response);
  }

    public function checkout() {
      $utility = new Utility();
      $cart = $utility::get_cart();
       if(isset($cart['cart']) && !empty($cart['cart'])) {
          $type = node_type_load("basiccart_order"); 
          $node = $this->entityManager()->getStorage('node')->create(array(
          'type' => $type->id(),
          ));

          $node_create_form = $this->entityFormBuilder()->getForm($node);  

          return array(
          '#type' => 'markup',
          '#markup' => render($node_create_form),
          );
       }else{
 
         $url = new Url('basiccart.cart');    
         return new RedirectResponse($url->toString()); 
       } 
   }    
      public function order_create() {
        $type = node_type_load("basiccart_order"); 
        $node = $this->entityManager()->getStorage('node')->create(array(
        'type' => $type->id(),
        ));

        $node_create_form = $this->entityFormBuilder()->getForm($node);  

        return array(
        '#type' => 'markup',
        '#markup' => render($node_create_form),
        );
     }
}
  
