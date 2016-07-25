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
    $param['entitytype'] = $query->get('entitytype') ?  $query->get('entitytype') : "node";
    $param['quantity'] = $query->get('quantity') ? $query->get('quantity') : 1;
    Utility::add_to_cart($nid, $param);
    //return new RedirectResponse(Url::fromUri($_SERVER['HTTP_REFERER'])->toString());  
    drupal_get_messages();
    $message = t('Added to cart');
      $content = [
        '#type' => 'html_tag',
        '#tag' => 'p',
        '#value' => $message,
      ];
    $response = new AjaxResponse();
    $response->addCommand(new HtmlCommand('#ajax-addtocart-message-'.$nid, $content));
     
     return $response;

  }

    public function checkout() {
      $utility = new Utility();
      $cart = $utility::get_cart();
      //print_r($cart); die;
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
  
