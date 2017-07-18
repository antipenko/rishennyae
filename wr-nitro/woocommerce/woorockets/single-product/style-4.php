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

global $post, $woocommerce, $product;

$wr_nitro_options = WR_Nitro::get_options();
$floating_cart  = $wr_nitro_options['wc_single_floating_button'];

// Get sale price dates
$countdown = get_post_meta( get_the_ID(), '_show_countdown', true );
$start     = get_post_meta( get_the_ID(), '_sale_price_dates_from', true );
$end       = get_post_meta( get_the_ID(), '_sale_price_dates_to', true );
$now       = date( 'd-m-y' );

$is_bookings = ( class_exists( 'WC_Bookings' ) && $product->product_type == 'booking' );

// Show rating
$show_rating = $wr_nitro_options['wc_general_rating'];
?>
<div itemscope itemtype="<?php echo woocommerce_get_product_schema(); ?>" id="product-<?php the_ID(); ?>" <?php post_class( 'style-4' . ( is_customize_preview() ? ' customizable customize-section-product_single' : '' ) ); ?>>
	<div class="p-single-top oh pr">
		<div class="cm-6 w800-12">
			<div class="p-single-info pa ">
				<h1 class="product-title mgt30 mgb10" itemprop="name"><?php the_title(); ?></h1>
				<div class="mgb20">
					<?php woocommerce_breadcrumb(); ?>
				</div>
				<div class="fc jcsb aic mgb20 pdr20">
					<?php
						woocommerce_template_single_price();

						if ( $show_rating ) {
							woocommerce_template_single_rating();
						}
					?>
				</div>

				<div class="desc mgb20 pdr20" itemprop="description">
					<?php echo apply_filters( 'woocommerce_short_description', $post->post_excerpt ) ?>
				</div>

				<div class="p-meta mgb20">
					<?php
						$cat_count = sizeof( get_the_terms( $post->ID, 'product_cat' ) );
						$tag_count = sizeof( get_the_terms( $post->ID, 'product_tag' ) );

						do_action( 'woocommerce_product_meta_start' );
					?>

					<?php echo wp_kses_post( $product->get_categories( ', ', '<span class="posted_in db mgb10">' . _n( '<span class="fwb dib">Category</span>:  ', '<span class="fwb dib">Categories</span>:  ', $cat_count, 'wr-nitro' ) . ' ', '</span>' ) ); ?>

					<?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>

						<span class="sku_wrapper db mgb10"><span class="fwb dib"><?php esc_html_e( 'SKU', 'wr-nitro' ); ?> </span>: <span class="sku" itemprop="sku"><?php echo ( $sku = $product->get_sku() ) ? $sku : esc_html__( 'N/A', 'wr-nitro' ); ?></span></span>

					<?php endif; ?>

					<span class="availability mgb10">
						<?php $availability = $product->get_availability(); ?>

						<span class="fwb dib"><?php esc_html_e( 'Availability', 'wr-nitro' ); ?></span>:
						<span class="stock <?php echo ( $product->is_in_stock() ? 'in-stock' : 'out-stock' ); ?>">
							<?php
								// Check product stock
								if ( $product->manage_stock == 'yes' && !empty($availability['availability']) ) :
									echo esc_html( $availability['availability'] );
								elseif ( $product->manage_stock == 'no' && $product->is_in_stock() ) :
									esc_html_e( 'In stock', 'wr-nitro' );
								else :
									esc_html_e( 'Out of stock', 'wr-nitro' );
								endif;
							?>
						</span>
					</span><!-- .availability -->

					<?php echo wp_kses_post( $product->get_tags( ', ', '<span class="tagged_as">' . _n( '<span class="fwb dib db">Tag</span>:  ', '<span class="fwb dib">Tags</span>:  ', $tag_count, 'wr-nitro' ) . ' ', '</span>' ) ); ?>

					<?php do_action( 'woocommerce_product_meta_end' ); ?>

				</div><!-- .p-meta -->

				<?php if ( ! ( $wr_nitro_options['wc_archive_catalog_mode'] || $is_bookings ) ) : ?>
					<div class="p-single-action btn-inline fc aic mgt20">
						<?php woocommerce_template_single_add_to_cart(); ?>
					</div>
				<?php endif; ?>
				<div class="sharing-product mgt20">
					<?php
						do_action( 'woocommerce_share' );
						echo WR_Nitro_Pluggable_WooCommerce::woocommerce_share();
					?>
				</div><!-- .product-share -->
			</div><!-- .p-single-info -->
		</div>
		<div class="cm-6 w800-12">
			<div class="p-single-images pa">
				<?php
					/**
					 * woocommerce_before_single_product_summary hook
					 *
					 * @hooked woocommerce_show_product_sale_flash - 10
					 * @hooked woocommerce_show_product_images - 20
					 */
					do_action( 'woocommerce_before_single_product_summary' );
				?>
			</div><!-- .p-single-images -->
			<?php if ( 'yes' == $countdown && $end && date( 'd-m-y', $start ) <= $now ) : ?>
				<div class="product__countdown pa bgw">
					<div class="wr-nitro-countdown fc jcsb tc aic" data-time='{"day": "<?php echo date( 'd', $end ); ?>", "month": "<?php echo date( 'm', $end ); ?>", "year": "<?php echo date( 'Y', $end ); ?>"}'></div>
				</div><!-- .product__countdown -->
			<?php endif; ?>
		</div><!-- .cm-6 -->
	</div><!-- .p-single-top -->

	<?php if ( $is_bookings ) : ?>
		<div class="p-single-booking pdt50 pdb50 overlay_bg">
			<div class="container">
				<?php woocommerce_template_single_add_to_cart(); ?>
			</div>
		</div><!-- .p-single-booking -->
	<?php endif; ?>

	<div class="p-single-bot">
		<?php woocommerce_output_product_data_tabs(); ?>
	</div><!-- p-single-bot -->

	<?php
		// Enable VC page builder for single product
		$builder = get_post_meta( get_the_ID(), 'enable_builder', true );
		if ( $builder ) {
			echo '<div class="row">';
				echo '<div class="p-single-builder mgb50">';
					the_content();
				echo '</div><!-- .p-single-builder -->';
			echo '</div><!-- .row -->';
		}
	?>

	<?php if ( $floating_cart && $product->is_in_stock() ) : ?>
	<div class="actions-fixed pf floating-add-to-cart">
		<?php if ( $wr_nitro_options['wc_buynow_btn'] ) echo '<div class="fc">'; ?>
			<?php
				// Add to cart button
				if ( ( $wr_nitro_options['wc_buynow_btn'] && ! $wr_nitro_options['wc_disable_btn_atc'] ) || ! $wr_nitro_options['wc_buynow_btn'] ) {
					echo WR_Nitro_Pluggable_WooCommerce::floating_add_to_cart( $product->id );
				}

				// Quick buy button
				if ( $wr_nitro_options['wc_buynow_btn'] ) {
					echo '<button type="submit" class="single_buy_now wr_add_to_cart_button button alt btr-50 db pdl20 pdr20 fl mgl10 br-3"><i class="fa fa-cart-arrow-down mgr10"></i>' . esc_html__( 'Buy now', 'wr-nitro' ) . '</button>';
				}
			?>
		<?php if ( $wr_nitro_options['wc_buynow_btn'] ) echo '</div>'; ?>
	</div><!-- .actions-fixed -->
	<?php endif; ?>

</div><!-- #product-<?php the_ID(); ?> -->
