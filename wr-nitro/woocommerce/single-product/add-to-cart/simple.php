<?php
/**
 * Simple product add to cart
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

if ( ! $product->is_purchasable() ) {
	return;
}

// Get theme option
$wr_nitro_options = WR_Nitro::get_options();

// Show compare
$show_compare = $wr_nitro_options['wc_general_compare'];

// Show wishlist
$show_wishlist = $wr_nitro_options['wc_general_wishlist'];

// Icon Set
$icons = $wr_nitro_options['wc_icon_set'];
?>

<?php if ( $product->is_in_stock() ) : ?>

	<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

	<form class="cart" method="post" enctype='multipart/form-data'>
		<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

		<?php
			if ( ! $product->is_sold_individually() )
				woocommerce_quantity_input( array(
					'min_value' => apply_filters( 'woocommerce_quantity_input_min', 1, $product ),
					'max_value' => apply_filters( 'woocommerce_quantity_input_max', $product->backorders_allowed() ? '' : $product->get_stock_quantity(), $product )
				)
			);
		?>

		<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->id ); ?>" />

		<?php
			$wr_nitro_options   = WR_Nitro::get_options();
			$add_to_cart_button = '<button type="submit" class="wr_single_add_to_cart_ajax single_add_to_cart_button wr_add_to_cart_button button alt btr-50 db pdl20 pdr20 bgd fl mgl10 br-3"><i class="nitro-icon-' . esc_attr( $icons ) . '-cart mgr10"></i>' . $product->single_add_to_cart_text() . '</button>';

			// Add to cart button
			if ( ( $wr_nitro_options['wc_buynow_btn'] && ! $wr_nitro_options['wc_disable_btn_atc'] ) || ! $wr_nitro_options['wc_buynow_btn'] ) {
				echo wp_kses_post( $add_to_cart_button );
			}

			// Quick buy button
			if ( $wr_nitro_options['wc_buynow_btn'] ) {
				echo '<button type="submit" class="single_buy_now wr_add_to_cart_button button alt btr-50 db pdl20 pdr20 bgd fl mgl10 br-3"><i class="nitro-icon-' . esc_attr( $icons ) . '-quickbuy mgr10"></i>' . esc_html__( 'Buy now', 'wr-nitro' ) . '</button>';
			}
		?>

		<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
	</form>

	<?php
		// Add Wishlist button
		if ( class_exists( 'YITH_WCWL' ) && $show_wishlist ) {
			echo do_shortcode( '[yith_wcwl_add_to_wishlist]' );
		}

		// Add compare button
		if ( class_exists( 'YITH_WOOCOMPARE' ) && $show_compare ) {
			echo '
				<div class="product__compare icon_color">
					<a class="product__btn bts-50 mg0 db nitro-line btb pr" href="#"><i class="nitro-icon-' . esc_attr( $icons ) . '-compare"></i><span class="tooltip ab">' . esc_html__( 'Compare', 'wr-nitro' ) . '</span></a>
					<div class="hidden">' . do_shortcode( '[yith_compare_button container="no"]' ) . '</div>
				</div>
			';
		}
	?>

	<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>

<?php endif; ?>
