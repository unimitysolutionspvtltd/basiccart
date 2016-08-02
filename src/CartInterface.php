<?php

/**
 * @file
 * Contains \Drupal\basiccart\CartInterface.
 */

namespace Drupal\basiccart;

/**
 * Cart interface definition for basiccart plugins.
 *
 */
interface CartInterface {
	public function get_cart($nid = NULL);
	public function remove_from_cart($nid);
	public function empty_cart();
	public function add_to_cart($id, $params = array());
}	