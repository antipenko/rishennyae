<?php
/**
 * Product loop sale flash
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post, $product;

$sale = '';

if ( $product->is_type( 'simple' ) && $product->is_on_sale() ) {

	$product = wc_get_product( $product->id );
	$regular_price 	= $product->get_regular_price();
	$sale_price 	= $product->get_sale_price();

	$sale = ceil( ( ( $regular_price - $sale_price ) / $regular_price ) * 100 );

} elseif ( $product->is_type( 'variable' ) && $product->is_on_sale() ) {

	$available_variations = $product->get_available_variations();

	$sale = array();

	foreach ( $available_variations as $val ) {
		if( $val['display_price'] > 0 ) {
			$regular_price = $val['display_regular_price'];
			$sale_price = $val['display_price'];

			$sale[] = ceil( ( ( $regular_price - $sale_price ) / $regular_price ) * 100 );
		}
	}

	$sale = max( $sale );

}

if ( $product->is_on_sale() ) {

	$R = floor( ( 255 * $sale ) / 100 );
	$G = floor( ( 255 * ( 100 - $sale ) ) / 100 );
	$bg_style = 'background-color: rgb(' . $R . ',' . $G . ', 0);';

	echo apply_filters( 'woocommerce_sale_flash', '<span class="product__badge sale" style="'. esc_attr( $bg_style ) .'">-' . $sale . '%</span>', $post, $product );
}
