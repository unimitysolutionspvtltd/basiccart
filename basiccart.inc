<?php

use Drupal\basiccart\Utility;

function basiccart_get_cart() {
  $cart =  Utility::get_cart();
  return $cart;
}

function basiccart_empty_cart(){
  $cart =  Utility::empty_cart();
  return $cart;
}