<?php
/**
 * Related Products
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $related_product;

$related_product = true;

$wr_nitro_options = WR_Nitro::get_options();

if ( empty( $product ) || ! $product->exists() || ! $wr_nitro_options['wc_single_product_related'] ) {
	return;
}

$related = $product->get_related( 20 );

// Get product item layout
$layout = $wr_nitro_options['wc_archive_item_layout'];

// Get number of product want to show
$limit = $wr_nitro_options['wc_single_product_show'];

if ( sizeof( $related ) == 0 ) return;

$args = apply_filters( 'woocommerce_related_products_args', array(
	'post_type'            => 'product',
	'ignore_sticky_posts'  => 1,
	'no_found_rows'        => 1,
	'posts_per_page'       => 20,
	'orderby'              => $orderby,
	'post__in'             => $related,
	'post__not_in'         => array( $product->id )
) );

$products = new WP_Query( $args );

// Get number of post in loop
$count = $products->post_count;

// Set product class
if ( '5' == $limit ) {
	$class = 'columns-5 fl cs-6 cxs-12';
} else {
	$class = 'cm-' . 12 / $limit . ' cs-6 cxs-12';
}
?>

<div class="section-form">
	<?php echo do_shortcode( '[contact-form-7 id="6527" title="consultation"]' ); ?>
</div>

<?php

if ( $products->have_posts() ) : ?>
	<div class="p-related mgt60 mgb30">
		<h3 class="wc-heading tc tu"><?php esc_html_e( 'РЕКОМЕНДАЦИИ', 'wr-nitro' ); ?></h3>
		<div class="products grid <?php if ( $count > $limit ) echo 'wr-nitro-carousel' ;?>" data-owl-options='{"items": "<?php echo ( int ) $limit; ?>","tablet":"3","mobile":"2"<?php echo ( $wr_nitro_options['rtl'] ? ',"rtl": "true"' : '' ); ?>}'>
			<?php while ( $products->have_posts() ) : $products->the_post(); ?>
				<div <?php post_class( $class ); ?>>
					<?php wc_get_template( 'woorockets/content-product/style-' . esc_attr( $layout ) . '.php' ); ?>
				</div>
			<?php endwhile; // end of the loop. ?>
		</div>
	</div><!-- .p-related -->
<?php endif; ?>

<?php wp_reset_postdata(); ?>
