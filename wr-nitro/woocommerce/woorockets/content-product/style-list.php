<?php
/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $post;

$wr_nitro_options = WR_Nitro::get_options();
$wr_nitro_shortcode_attrs = class_exists( 'Nitro_Toolkit_Shortcode' ) ? Nitro_Toolkit_Shortcode::get_attrs() : null;

// Ensure visibility
if ( ! $product || ! $product->is_visible() )
	return;

$wr_product_class = $wr_hover_class = '';

// Extra post classes
$wr_classes = array();

array_push( $wr_classes, $wr_product_class );

// Hover style
$wr_hover_style = $wr_nitro_shortcode_attrs ? $wr_nitro_shortcode_attrs['hover_style'] : $wr_nitro_options['wc_archive_item_hover_style'];
if ( 'default' == $wr_hover_style ) {
	$wr_hover_class = '';
} elseif ( $wr_hover_style == 'flip-back' ) {
	// Flip back effect
	$wr_flip_effect = $wr_nitro_shortcode_attrs ? $wr_nitro_shortcode_attrs['transition_effects'] : $wr_nitro_options['wc_archive_item_transition'];

	// Hover class
	$wr_hover_class .= $wr_hover_style . ' ' . $wr_flip_effect;
} else {
	$wr_hover_class = $wr_hover_style;
}

// Show rating
$wr_show_rating = $wr_nitro_options['wc_general_rating'];

// Show Compare
$wr_show_compare = $wr_nitro_options['wc_general_compare'];

// Show Wishlist
$wr_show_wishlist = $wr_nitro_options['wc_general_wishlist'];

// Icon Set
$wr_icons = $wr_nitro_options['wc_icon_set'];
?>

<div class="product__image fl oh pr <?php echo esc_attr( $wr_hover_class ); ?>">
	<?php

		if ( $wr_hover_style == 'mask' )
			echo '<span class="mask-inner"></span>';

		if ( ! $product->get_price() ) {
			echo '<span class="product__badge free">' . esc_html__( 'Бесплатно', 'wr-nitro' ) . '</span>';
		}
		if ( $wr_hover_style == 'flip-back' ) {
			wc_get_template( 'woorockets/content-product/product-image-double.php' );
		} else {
			/**
			 * woocommerce_before_shop_loop_item_title hook
			 *
			 * @hooked woocommerce_show_product_loop_sale_flash - 10
			 * @hooked woocommerce_template_loop_product_thumbnail - 10
			 */
			echo '<a class="db" href="' . esc_url( get_permalink() ) . '">';
				do_action( 'woocommerce_before_shop_loop_item_title' ) ;
			echo '</a>';
		}
	?>
</div><!-- .product__image -->

<div class="product__list fl pdl30">
	<div class="product__title">
		<h5 class="mg0"><a class="hover-primary" href="<?php esc_url( the_permalink() ); ?>" title="<?php esc_attr( the_title() ); ?>"><?php the_title(); ?></a></h5>
	</div><!-- .product__title -->

	<div class="product__price mgt10 fc aic jcsb">
		<?php
			wc_get_template( 'loop/price.php' );

			if ( $wr_show_rating == '1' ) {
				wc_get_template( 'loop/rating.php' );
			}
		?>
	</div><!-- .product__price -->

	<div class="product__description mgt20">
		<?php
			$excerpt = apply_filters( 'woocommerce_short_description', $post->post_excerpt );
			echo substr( $excerpt, 0, 300 );
		?>
	</div><!-- .product__description -->

	<?php if ( ! $wr_nitro_options['wc_archive_catalog_mode'] ) : ?>
		<div class="product__action fl mgt20">
			<?php
				// Add to cart button
				if ( ( $wr_nitro_options['wc_buynow_btn'] && ! $wr_nitro_options['wc_disable_btn_atc'] ) || ! $wr_nitro_options['wc_buynow_btn'] || ( $wr_nitro_options['wc_buynow_btn'] && $wr_nitro_options['wc_disable_btn_atc'] && $product->product_type != 'simple' ) ) {
					wc_get_template( 'loop/add-to-cart.php' );
				}

				// Quick buy button
				if ( $wr_nitro_options['wc_buynow_btn'] && $product->product_type == 'simple' ) {
					echo '<a class="product__btn btr-50 button btn-buynow" href="#" data-product-id="' . get_the_ID() . '"><i class="mgr10 nitro-icon-' . esc_attr( $wr_icons ) . '-quickbuy"></i>' . esc_html__( 'Buy Now', 'wr-nitro' ) . '</a>';
				}

				// Add to wishlist button
				if ( class_exists( 'YITH_WCWL' ) && $wr_show_wishlist== '1' ) :
					echo do_shortcode( '[yith_wcwl_add_to_wishlist]' );
				endif;

				// Add compare button
				if ( class_exists( 'YITH_WOOCOMPARE' ) && $wr_show_compare == '1' ) {
					echo '
						<div class="product__compare icon_color">
							<a class="product__btn bts-50 btb nitro-line dib pr" href="#"><i class="nitro-icon-' . esc_attr( $wr_icons ) . '-compare"></i><span class="tooltip ab">' . esc_html__( 'Compare', 'wr-nitro' ) . '</span></a>
							<div class="hidden">' . do_shortcode( '[yith_compare_button container="no"]' ) . '</div>
						</div>
					';
				}
			?>
		</div><!-- .p-action -->
	<?php endif; ?>
</div><!-- .p-info -->
