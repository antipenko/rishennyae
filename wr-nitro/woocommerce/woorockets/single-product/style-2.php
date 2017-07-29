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

// Get layout
$layout         = $wr_nitro_options['wc_single_layout'];
$tab_style      = $wr_nitro_options['wc_single_tab_style'];
$thumb_position = $wr_nitro_options['wc_single_thumb_position'];
$product_nav    = $wr_nitro_options['wc_single_single_nav'];
$page_title     = $wr_nitro_options['wc_single_title'];
$floating_cart  = $wr_nitro_options['wc_single_floating_button'];
$sticky         = $wr_nitro_options['wc_single_sidebar_sticky'];

// Show rating
$show_rating = $wr_nitro_options['wc_general_rating'];

// Get widget style
$w_style = $wr_nitro_options['w_style'];

// Get sidebar add to above product detail
$sidebar = $wr_nitro_options['wc_single_content_before'];
?>
<?php
	// Get page title
if ( $page_title ) {
	WR_Nitro_Render::get_template( 'common/page', 'title' );
}
if ( ! empty( $sidebar ) && is_active_sidebar( $sidebar ) ) {
	echo '<div class="widget-before-product-detail">';
	dynamic_sidebar( $sidebar );
	echo '</div>';
}
?>

<div itemscope itemtype="<?php echo woocommerce_get_product_schema(); ?>" id="product-<?php the_ID(); ?>" <?php post_class( 'style-2 social-circle ' . $layout . ( is_customize_preview() ? ' customizable customize-section-product_single' : '' ) ); ?>>
	<div class="oh pdt30 <?php if ( 'no-sidebar' != $layout ) echo 'container' ?>">
		<div class="fc fcw">
			<div id="shop-detail">
				<div class="container">
					<div class="row">
						<div class="w667-12 <?php echo ( $layout != 'no-sidebar' ? ' cm-5' : 'cm-5' ); ?>">
							<div class="p-single-images pr clear thumb-<?php echo esc_attr( $thumb_position ) ?>">
								<?php
									/**
									 * woocommerce_before_single_product_summary hook
									 *
									 * @hooked woocommerce_show_product_sale_flash - 10
									 * @hooked woocommerce_show_product_images - 20
									 */
									//do_action( 'woocommerce_before_single_product_summary' );
									 the_post_thumbnail(); 
									?>
								</div><!-- .p-single-image -->
								<div class="mgt20 fc fcc">
									<?php
									do_action( 'woocommerce_share' );
									echo WR_Nitro_Pluggable_WooCommerce::woocommerce_share();
									?>
								</div>
							</div><!-- .cm-5 -->

							<div class="w667-12 <?php echo ( $layout != 'no-sidebar' ? ' cm-7' : 'cm-7' ); ?>">
								<div class="p-single-info">
									<div class="p-meta ">
										<?php echo wp_kses_post( $product->get_categories( ', ', '<span class="posted_in db ">' . _n( '<span class="fwb dib">Категории</span>:  ', '<span class="fwb dib" id="meta-category">Категории</span>:  ', $cat_count, 'wr-nitro' ) . ' ', '</span>' ) ); ?>
									</div>

									<div class="desc mgb20" itemprop="description">
										<?php echo apply_filters( 'woocommerce_short_description', $post->post_excerpt ) ?>
									</div>

									<?php 
									$category_id = array(249);
								    // // получаем категории товаров
									$categories = get_the_terms( $product->id, 'product_cat' );
								    // проверяем принадлежность товара к вышеуказанным категориям по id
									$product_in_category = false;
									if ($categories) {
										foreach ($categories as $item) {
											if (in_array((int)$item->term_id, $category_id)) {
												$product_in_category = true;
												break;
											}  
										} 
										if ($product_in_category) {
											//echo 'Отзыв об адвокате';
											wc_show_advocat_button();
										} else{
											echo do_shortcode( '[contact-form-7 id="6527" title="consultation"]' );
										}
									}else{
										echo do_shortcode( '[contact-form-7 id="6527" title="consultation"]' );
									}
									
									?>
									<?php //echo do_shortcode( '[contact-form-7 id="6527" title="consultation"]' ); ?>
									<?php if ( ! $page_title ) : ?>
										<h1 class="product-title mgt30 mgb10" itemprop="name"><?php the_title(); ?></h1>
										<div class="mgb20">
											<?php woocommerce_breadcrumb(); ?>
										</div>
									<?php endif; ?>

									<div class="fc jcsb aic ">
										<?php
										woocommerce_template_single_price();

										if ( $show_rating ) {
											woocommerce_template_single_rating();
										}
										?>
									</div>

									

									<div class="p-meta ">
										<?php
										$cat_count = sizeof( get_the_terms( $post->ID, 'product_cat' ) );
										$tag_count = sizeof( get_the_terms( $post->ID, 'product_tag' ) );

										do_action( 'woocommerce_product_meta_start' );
										?>

										<?php //echo wp_kses_post( $product->get_categories( ', ', '<span class="posted_in db mgb10">' . _n( '<span class="fwb dib">Категории</span>:  ', '<span class="fwb dib">Категории</span>:  ', $cat_count, 'wr-nitro' ) . ' ', '</span>' ) ); ?>

										<?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>

											<span class="sku_wrapper db "><span class="fwb dib"><?php esc_html_e( 'SKU', 'wr-nitro' ); ?> </span>: <span class="sku" itemprop="sku"><?php echo ( $sku = $product->get_sku() ) ? $sku : esc_html__( 'N/A', 'wr-nitro' ); ?></span></span>

										<?php endif; ?>

										<span class="availability ">
											<?php $availability = $product->get_availability(); ?>
											<span class="fwb dib"><?php esc_html_e( 'Availability', 'wr-nitro' ); ?></span>:
											<span class="stock <?php echo ( $product->is_in_stock() ? 'in-stock' : 'out-stock' ); ?>">
												<?php
												// Check product stock
												if ( $product->manage_stock == 'yes' && !empty($availability['availability']) ) :
													echo esc_html( $availability['availability'] );
												elseif ( $product->manage_stock == 'no' && $product->is_in_stock() ) :
													esc_html_e( 'In Stock', 'wr-nitro' );
												else :
													esc_html_e( 'Out Of Stock', 'wr-nitro' );
												endif;
												?>
											</span>
										</span><!-- .availability -->

										<?php echo wp_kses_post( $product->get_tags( ', ', '<span class="tagged_as">' . _n( '<span class="fwb dib db">Tag</span>:  ', '<span class="fwb dib">Tags</span>:  ', $tag_count, 'wr-nitro' ) . ' ', '</span>' ) ); ?>

										<?php do_action( 'woocommerce_product_meta_end' ); ?>

									</div><!-- .p-meta -->

									<?php if ( ! $wr_nitro_options['wc_archive_catalog_mode'] ) : ?>
										<div class="p-single-action nitro-line btn-inline pdb20 <?php if ( ! $product->is_type( 'variable' ) ) echo 'fc aic'; ?>">
											<?php echo woocommerce_template_single_add_to_cart(); ?>
										</div><!-- .p-single-action -->
									<?php endif; ?>
									<div class="mgt20 fc fcc">
										<?php
										//do_action( 'woocommerce_share' );
										//echo WR_Nitro_Pluggable_WooCommerce::woocommerce_share();
										?>
									</div>
								</div><!-- .p-single-info -->
							</div><!-- .cm-7 -->

							<div class="product-tabs cm-12 <?php echo esc_attr( $tab_style ); ?>-tab">
								<?php
								if ( 'accordion' == $tab_style ) {
									wc_get_template( 'single-product/tabs/tabs-accordion.php' );
								} else {
									woocommerce_output_product_data_tabs();
								}
								?>
							</div><!-- .product-tabs -->
						</div><!-- .row -->
					</div><!-- .container -->
					<?php
					// Enable VC page builder for single product
					$builder = get_post_meta( get_the_ID(), 'enable_builder', true );
					if ( $builder ) {
						echo '<div class="row mgt50">';
						echo '<div class="p-single-builder">';
						the_content();
						echo '</div><!-- .p-single-builder -->';
						echo '</div><!-- .row -->';
					}
					?>
					<div class="addition-product test <?php if ( $wr_nitro_options['wc_single_product_related_full'] == 'boxed' ) echo 'container'; ?>">
						<?php woocommerce_upsell_display(); ?>

						<?php woocommerce_output_related_products(); ?>

						<?php wc_get_template( 'single-product/recent-viewed.php' ); ?>
					</div><!-- .addition-product -->
				</div><!-- #shop-detail -->

				<?php if ( $layout != 'no-sidebar' ) : ?>
					<div id="shop-sidebar" class="primary-sidebar <?php if ( $sticky == true ) echo 'primary-sidebar-sticky'; ?> widget-style-<?php echo esc_attr( $w_style ) . ' ' . ( is_customize_preview() ? 'customizable customize-section-widget_styles ' : '' ); ?> mgt30">
						<?php if ( $sticky == true ) echo '<div class="primary-sidebar-inner">'; ?>
						<?php dynamic_sidebar( $wr_nitro_options['wc_single_sidebar'] ); ?>
						<?php if ( $sticky == true ) echo '</div>'; ?>
					</div><!-- #shop-sidebar -->
				<?php endif; ?>
			</div><!-- .fc -->
		</div>

		<?php if ( $product_nav ) : ?>
			<div class="p-single-nav">
				<div class="left fc aic pf">
					<?php
					$prev_post = get_previous_post();
					if ( is_a( $prev_post , 'WP_Post' ) ) {
						$prev_product   = new WC_Product( $prev_post->ID );
						$prev_price     = $prev_product->get_price_html();

						echo get_the_post_thumbnail( $prev_post->ID, '60x60' );
						echo '<div class="ts-03 overlay_bg fc fcc jcc">';
						echo '<a class="fwb db color-dark" href="' . esc_url( get_permalink( $prev_post->ID ) ) . '">' . get_the_title( $prev_post->ID ) . '</a>';
						echo '<span class="price db">' . $prev_price . '</span>';
						echo '</div>';
					}
					?>
				</div><!-- .left -->
				<div class="right fc aic pf">
					<?php
					$next_post = get_next_post();
					if ( is_a( $next_post , 'WP_Post' ) ) {
						$next_product   = new WC_Product( $next_post->ID );
						$next_price     = $next_product->get_price_html();

						echo '<div class="ts-03 overlay_bg fc fcc jcc">';
						echo '<a class="fwb db color-dark" href="' . esc_url( get_permalink( $next_post->ID ) ) . '">' . get_the_title( $next_post->ID ) . '</a>';
						echo '<span class="price db">' . $next_price . '</span>';
						echo '</div>';
						echo get_the_post_thumbnail( $next_post->ID, '60x60' );
					}
					?>
				</div><!-- .right -->
			</div><!-- .p-single-nav -->
		<?php endif; ?>

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
