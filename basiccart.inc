<?php

use Drupal\basiccart\Utility;

function basiccart_get_cart() {
  $cart =  Utility::get_cart();
  return $cart;
}