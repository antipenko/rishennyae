<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * Override this template by copying it to yourtheme/woocommerce/content-single-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $post, $woocommerce, $product;

$wr_nitro_options = WR_Nitro::get_options();

if ( post_password_required() ) {
	echo get_the_password_form();
	return;
}

/**
 * woocommerce_before_single_product hook
 *
 * @hooked wc_print_notices - 10
 */
do_action( 'woocommerce_before_single_product' );

wc_get_template( 'woorockets/single-product/style-' . $wr_nitro_options['wc_single_style'] . '.php' );

do_action( 'woocommerce_after_single_product' );
