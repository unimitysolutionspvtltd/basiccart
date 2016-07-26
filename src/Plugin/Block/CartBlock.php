<?php

namespace Drupal\basiccart\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\basiccart\Utility;
use Drupal\Core\Url;
use Drupal\Core\Link;

/**
 * Provides a 'Basic Cart' block.
 *
 * @Block(
 *   id = "basiccart_cartblock",
 *   admin_label = @Translation("Basic Cart Block")
 * )
 */
class CartBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $utility = new Utility();
  	$config = $utility::cart_settings();
     return array(
      //'#theme' => 'basiccart_cart_template',
      //'#basiccart' => array(),	
      '#type' => 'markup',
      '#title' => $config->get('cart_block_title'),
      '#markup' => $utility->get_cart_content(),
      '#cache' => array('max-age' => 0),
    );
  }
}
