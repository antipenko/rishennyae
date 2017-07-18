<?php
/**
 * Loop Add to Cart
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

$wr_nitro_options = WR_Nitro::get_options();
$wr_nitro_shortcode_attrs = class_exists( 'Nitro_Toolkit_Shortcode' ) ? Nitro_Toolkit_Shortcode::get_attrs() : null;

if ( $wr_nitro_options['wc_archive_catalog_mode'] ) return;

// Get product item style
$wr_item_style = $wr_nitro_shortcode_attrs ? $wr_nitro_shortcode_attrs['style'] : $wr_nitro_options['wc_archive_item_layout'];

// Change icon and button class in single product
$wr_btn_class = 'bts-40 color-dark bgw';

// Style of list product
$wr_style = $wr_nitro_shortcode_attrs ? $wr_nitro_shortcode_attrs['list_style'] : $wr_nitro_options['wc_archive_style'];

// Icon Set
$wr_icons = $wr_nitro_options['wc_icon_set'];

if ( 'list' == $wr_style && ! is_singular( 'product' ) ) {
	echo apply_filters( 'woocommerce_loop_add_to_cart_link',
		sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" data-quantity="%s" class="' . ( ( $product->is_type( 'simple' ) ) ? 'ajax_add_to_cart' : '' ) . ' product__btn_cart product__btn btr-50 button %s product_type_%s"><i class="mgr10 nitro-icon-' . esc_attr( $wr_icons ) . '-cart"></i>%s</a>',
			esc_url( $product->add_to_cart_url() ),
			esc_attr( $product->id ),
			esc_attr( $product->get_sku() ),
			esc_attr( isset( $quantity ) ? $quantity : 1 ),
			$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
			esc_attr( $product->product_type ),
			esc_html( $product->add_to_cart_text() )
		),
	$product );
} else {
	if ( '1' == $wr_item_style ) {
		echo apply_filters( 'woocommerce_loop_add_to_cart_link',
			sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" data-quantity="%s" class="' . ( ( $product->is_type( 'simple' ) ) ? 'ajax_add_to_cart' : '' ) . ' product__btn_cart product__btn db %s %s product_type_%s hover-primary"><i class="nitro-icon-' . esc_attr( $wr_icons ) . '-cart"></i><span class="tooltip ar">%s</span></a>',
				esc_url( $product->add_to_cart_url() ),
				esc_attr( $product->id ),
				esc_attr( $product->get_sku() ),
				esc_attr( isset( $quantity ) ? $quantity : 1 ),
				esc_attr( $wr_btn_class ),
				$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
				esc_attr( $product->product_type ),
				esc_html( $product->add_to_cart_text() )
			),
		$product );
	} elseif ( '2' == $wr_item_style ) {
		echo apply_filters( 'woocommerce_loop_add_to_cart_link',
			sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" data-quantity="%s" class="' . ( ( $product->is_type( 'simple' ) ) ? 'ajax_add_to_cart' : '' ) . ' product__btn_cart product__btn bgw btb btr-40 color-dark %s product_type_%s"><i class="nitro-icon-' . esc_attr( $wr_icons ) . '-cart mgr10"></i><span>%s</span></a>',
				esc_url( $product->add_to_cart_url() ),
				esc_attr( $product->id ),
				esc_attr( $product->get_sku() ),
				esc_attr( isset( $quantity ) ? $quantity : 1 ),
				$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
				esc_attr( $product->product_type ),
				esc_html( $product->add_to_cart_text() )
			),
		$product );
	} elseif ( '3' == $wr_item_style || '4' == $wr_item_style ) {
		echo apply_filters( 'woocommerce_loop_add_to_cart_link',
			sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" data-quantity="%s" class="' . ( ( $product->is_type( 'simple' ) ) ? 'ajax_add_to_cart' : '' ) . ' product__btn_cart product__btn pr color-dark hover-primary %s product_type_%s"><i class="nitro-icon-' . esc_attr( $wr_icons ) . '-cart"></i><span class="tooltip ab">%s</span></a>',
				esc_url( $product->add_to_cart_url() ),
				esc_attr( $product->id ),
				esc_attr( $product->get_sku() ),
				esc_attr( isset( $quantity ) ? $quantity : 1 ),
				$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
				esc_attr( $product->product_type ),
				esc_html( $product->add_to_cart_text() )
			),
		$product );
	} elseif ( '5' == $wr_item_style ) {
		echo apply_filters( 'woocommerce_loop_add_to_cart_link',
			sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" data-quantity="%s" class="' . ( ( $product->is_type( 'simple' ) ) ? 'ajax_add_to_cart' : '' ) . ' product__btn_cart product__btn button db textup aligncenter pr %s product_type_%s"><i class="nitro-icon-' . esc_attr( $wr_icons ) . '-cart mgr10"></i>%s</a>',
				esc_url( $product->add_to_cart_url() ),
				esc_attr( $product->id ),
				esc_attr( $product->get_sku() ),
				esc_attr( isset( $quantity ) ? $quantity : 1 ),
				$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
				esc_attr( $product->product_type ),
				esc_html( $product->add_to_cart_text() )
			),
		$product );
	}
}
