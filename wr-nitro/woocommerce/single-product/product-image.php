<?php
/**
 * Single Product Image
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.6.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $woocommerce, $product;

$wr_nitro_options = WR_Nitro::get_options();
$count = count( $product->get_gallery_attachment_ids() );

// Single product style
$style = $wr_nitro_options['wc_single_style'];

wc_get_template( 'woorockets/single-product/product-image/style-' . $wr_nitro_options['wc_single_style'] . '.php' );
?>
