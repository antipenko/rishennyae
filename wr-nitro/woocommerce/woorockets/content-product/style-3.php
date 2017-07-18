<?php
/**
 * @version    1.0
 * @package    WR_Theme
 * @author     WooRockets Team <support@woorockets.com>
 * @copyright  Copyright (C) 2014 WooRockets.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.woorockets.com
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $post;

$wr_nitro_options = WR_Nitro::get_options();
$wr_nitro_shortcode_attrs = class_exists( 'Nitro_Toolkit_Shortcode' ) ? Nitro_Toolkit_Shortcode::get_attrs() : null;

// Get product list style
$wr_style = $wr_nitro_shortcode_attrs ? $wr_nitro_shortcode_attrs['list_style'] : $wr_nitro_options['wc_archive_style'];

// Hover style
$wr_hover_style = $wr_nitro_shortcode_attrs ? $wr_nitro_shortcode_attrs['hover_style'] : $wr_nitro_options['wc_archive_item_hover_style'];
if ( 'default' == $wr_hover_style ) {
	$wr_hover_class = '';
} elseif ( $wr_hover_style == 'flip-back' ) {
	// Flip back effect
	$wr_flip_effect = $wr_nitro_shortcode_attrs ? $wr_nitro_shortcode_attrs['transition_effects'] : $wr_nitro_options['wc_archive_item_transition'];

	// Hover class
	$wr_hover_class = $wr_hover_style . ' ' . $wr_flip_effect;
} else {
	$wr_hover_class = $wr_hover_style;
}

// Show rating
$wr_show_rating = $wr_nitro_options['wc_general_rating'];

// Show compare
$wr_show_compare = $wr_nitro_options['wc_general_compare'];

// Show wishlist
$wr_show_wishlist = $wr_nitro_options['wc_general_wishlist'];

// Catalog mode
$wr_catalog_mode = $wr_nitro_options['wc_archive_catalog_mode'];

// Icon Set
$wr_icons = $wr_nitro_options['wc_icon_set'];

// Countdown for sale product
$start = get_post_meta( get_the_ID(), '_sale_price_dates_from', true );
$end   = get_post_meta( get_the_ID(), '_sale_price_dates_to', true );
$now   = date( 'd-m-y' );
?>
<div class="product__wrap product-btn-left pr oh <?php if ( $product->get_rating_html() ) echo 'has-rating' ?>">
	<div class="product__image oh pr ts-03 <?php echo esc_attr( $wr_hover_class ); ?>">
		<?php if ( ! empty( $wr_nitro_shortcode_attrs['countdown'] ) && ( $end && date( 'd-m-y', $start ) <= $now ) ) : ?>
			<div class="product__countdown pa bgw">
				<div class="wr-nitro-countdown fc jcsb tc aic fcc" data-time='{"day": "<?php echo date( 'd', $end ); ?>", "month": "<?php echo date( 'm', $end ); ?>", "year": "<?php echo date( 'Y', $end ); ?>"}'></div>
			</div>
		<?php endif; ?>

		<?php wc_get_template( 'woorockets/content-product/product-image.php' ); ?>
		<?php if ( ! ( ! $product->get_price() && $wr_catalog_mode ) ) : ?>
			<div class="product__action pa body_bg">
				<div class="product__action-inner icon_color fl">
					<?php
						// Add to cart button
						// if ( ( $wr_nitro_options['wc_buynow_btn'] && ! $wr_nitro_options['wc_disable_btn_atc'] ) || ! $wr_nitro_options['wc_buynow_btn'] || ( $wr_nitro_options['wc_buynow_btn'] && $wr_nitro_options['wc_disable_btn_atc'] && $product->product_type != 'simple' ) ) {
						// 	wc_get_template( 'loop/add-to-cart.php' );
						// }

						// Quick buy button
						// if ( $wr_nitro_options['wc_buynow_btn'] && $product->product_type == 'simple' && ! $wr_catalog_mode && $product->is_in_stock() ) {
						// 	echo '<a class="product__btn color-dark btn-buynow hover-primary" href="#" data-product-id="' . get_the_ID() . '"><i class="nitro-icon-' . esc_attr( $wr_icons ) . '-quickbuy"></i><span class="tooltip ab">' . esc_html__( 'Buy Now', 'wr-nitro' ) . '</span></a>';
						// }

						// Product quickview
						//echo '<a class="product__btn btn-quickview pr color-dark hover-primary" href="#0" data-prod="' . esc_attr( $post->ID ) . '"><i class="nitro-icon-' . esc_attr( $wr_icons ) . '-quickview"></i><span class="tooltip ab">' . esc_html__( 'Quick View', 'wr-nitro' ) . '</span></a>';

						// Add compare button
						// if ( class_exists( 'YITH_WOOCOMPARE' ) && $wr_show_compare && ! $wr_catalog_mode ) {
						// 	echo '
						// 		<div class="product__compare">
						// 			<a class="product__btn pr color-dark hover-primary fl" href="#"><i class="nitro-icon-' . esc_attr( $wr_icons ) . '-compare"></i><span class="tooltip ab">' . esc_html__( 'Compare', 'wr-nitro' ) . '</span></a>
						// 			<div class="hidden">' . do_shortcode( '[yith_compare_button container="no"]' ) . '</div>
						// 		</div>
						// 	';
						// }

						// Add to wishlist button
						// if ( class_exists( 'YITH_WCWL' ) && $wr_show_wishlist && ! $wr_catalog_mode ) {
						// 	echo do_shortcode( '[yith_wcwl_add_to_wishlist]' );
						// }
					?>
				</div><!-- .product__action-inner -->
				<div class="product__price">
					<?php
						if ( ! $product->get_price() ) {
							echo esc_html__( 'Бесплатно', 'wr-nitro' );
						} else {
							wc_get_template( 'loop/price.php' );
						}
					?>
				</div><!-- .product__price -->
			</div><!-- .product__action -->
		<?php endif; ?>
	</div><!-- .product__image -->

	<div class="product__title pa ts-05 tc">
		<h5 class="mg0"><a class="hover-primary" href="<?php esc_url( the_permalink() ); ?>" title="<?php esc_attr( the_title() ); ?>"><?php the_title(); ?></a></h5>

		<?php if ( $wr_show_rating == '1' ): ?>
			<?php wc_get_template( 'loop/rating.php' ); ?>
		<?php endif; ?>
	</div><!-- .product__title -->
</div><!-- .product-btn-left -->