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
	$flip_effect = $wr_nitro_shortcode_attrs ? $wr_nitro_shortcode_attrs['transition_effects'] : $wr_nitro_options['wc_archive_item_transition'];

	// Hover class
	$wr_hover_class = $wr_hover_style . ' ' . $flip_effect;
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
<div class="product__wrap product-btn-right">
	<div class="product__image oh pr <?php echo esc_attr( $wr_hover_class ); ?>">
		<?php wc_get_template( 'woorockets/content-product/product-image.php' ); ?>

			<div class="product__action pa icon_color">
				<?php
					// Product quickview
					//echo '<a class="product__btn bts-40 btn-quickview color-dark bgw db hover-primary" href="#0" data-prod="' . esc_attr( $post->ID ) . '"><i class="nitro-icon-' . esc_attr( $wr_icons ) . '-quickview"></i><span class="tooltip ar">' . esc_html__( 'Quick View', 'wr-nitro' ) . '</span></a>';

					// Quick buy button
					// if ( $wr_nitro_options['wc_buynow_btn'] && $product->product_type == 'simple' && ! $wr_catalog_mode && $product->is_in_stock() ) {
					// 	echo '<a class="product__btn bts-40 color-dark bgw db btn-buynow hover-primary" href="#" data-product-id="' . get_the_ID() . '"><i class="nitro-icon-' . esc_attr( $wr_icons ) . '-quickbuy"></i><span class="tooltip ar">' . esc_html__( 'Buy Now', 'wr-nitro' ) . '</span></a>';
					// }

					// Add to cart button
					// if ( ( $wr_nitro_options['wc_buynow_btn'] && ! $wr_nitro_options['wc_disable_btn_atc'] ) || ! $wr_nitro_options['wc_buynow_btn'] || ( $wr_nitro_options['wc_buynow_btn'] && $wr_nitro_options['wc_disable_btn_atc'] && $product->product_type != 'simple' ) ) {
					// 	wc_get_template( 'loop/add-to-cart.php' );
					// }

					// Add compare button
					// if ( class_exists( 'YITH_WOOCOMPARE' ) && $wr_show_compare && ! $wr_catalog_mode ) {
					// 	echo '
					// 		<div class="product__compare">
					// 			<a class="product__btn bts-40 color-dark bgw mg0 db hover-primary" href="#"><i class="nitro-icon-' . esc_attr( $wr_icons ) . '-compare"></i><span class="tooltip ar">' . esc_html__( 'Compare', 'wr-nitro' ) . '</span></a>
					// 			<div class="hidden">' . do_shortcode( '[yith_compare_button container="no"]' ) . '</div>
					// 		</div>
					// 	';
					// }
				?>
			</div><!-- .product__action -->

		<?php if ( ! empty( $wr_nitro_shortcode_attrs['countdown'] ) && ( $end && date( 'd-m-y', $start ) <= $now ) ) : ?>
			<div class="product__countdown pa bgw">
				<div class="wr-nitro-countdown fc jcsb tc aic" data-time='{"day": "<?php echo date( 'd', $end ); ?>", "month": "<?php echo date( 'm', $end ); ?>", "year": "<?php echo date( 'Y', $end ); ?>"}'></div>
			</div>
		<?php endif; ?>
	</div><!-- .product__image -->

	<div class="product__title mgt10 fc aic jcsb">
		<h5 class="mg0"><a class="hover-primary" href="<?php esc_url( the_permalink() ); ?>" title="<?php esc_attr( the_title() ); ?>"><?php the_title(); ?></a></h5>
		<?php
			if ( class_exists( 'YITH_WCWL' ) && $wr_show_wishlist && ! $wr_catalog_mode ) :
				echo do_shortcode( '[yith_wcwl_add_to_wishlist]' );
			endif;
		?>
	</div><!-- .product__title -->

	<div class="product__price fc aic jcsb">
		<?php
			wc_get_template( 'loop/price.php' );

			if ( $wr_show_rating ) {
				wc_get_template( 'loop/rating.php' );
			}
		?>
	</div><!-- .product__price -->
</div><!-- .product-btn-right -->
