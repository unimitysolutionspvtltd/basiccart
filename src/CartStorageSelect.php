<?php
/**
 * @file
 * Contains \Drupal\basiccart\Utility
 */
namespace Drupal\basiccart;

use Drupal\basiccart\CartSession;
use Drupal\basiccart\CartTable;


class CartStorageSelect {

    private $cart = NULL; 

    public function __construct($strategy_ind_id) {
        switch ($strategy_ind_id) {
            case "C": 
                $this->cart = new CartSession();
            break;
            case "E": 
                $this->cart = new CartTable();
            break;
        }
    }

    public  function get_cart($nid = NULL) {
        return $this->cart->get_cart($nid);
    }

    public  function remove_from_cart($nid) {
        return $this->cart->remove_from_cart($nid);
    }
    public  function empty_cart() {
        return $this->cart->empty_cart();
    }
    public  function add_to_cart($id, $params = array()) {
        return $this->cart->add_to_cart($id, $params);
    }
}
