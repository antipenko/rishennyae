<?php
/**
 * Single Product Up-Sells
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

$wr_nitro_options = WR_Nitro::get_options();

if ( empty( $product ) || ! $product->exists() || ! $wr_nitro_options['wc_single_product_upsell'] ) {
	return;
}

$upsells = $product->get_upsells();

if ( sizeof( $upsells ) == 0 ) {
	return;
}

// Get number of product want to show
$limit = $wr_nitro_options['wc_single_product_show'];

// Get product item layout
$layout =  $wr_nitro_options['wc_archive_item_layout'];

$meta_query = WC()->query->get_meta_query();

$args = array(
	'post_type'           => 'product',
	'ignore_sticky_posts' => 1,
	'no_found_rows'       => 1,
	'posts_per_page'      => -1,
	'orderby'             => $orderby,
	'post__in'            => $upsells,
	'post__not_in'        => array( $product->id ),
	'meta_query'          => $meta_query
);

$products = new WP_Query( $args );

// Get number of post in loop
$count = $products->post_count;

// Set product class
if ( '5' == $limit ) {
	$class = 'columns-5 fl cs-6 cxs-12';
} else {
	$class = 'cm-' . 12 / $limit . ' cs-6 cxs-12';
}


if ( $products->have_posts() ) : ?>
	<div class="p-upsell mgt60 mgb30">
		<h3 class="wc-heading tc tu"><?php esc_html_e( 'You may also like', 'wr-nitro' ); ?></h3>
		<div class="products grid <?php if ( $count > $limit ) echo 'wr-nitro-carousel' ;?>" data-owl-options='{"items": "<?php echo ( int ) $limit; ?>","tablet":"2","mobile":"1"<?php echo ( $wr_nitro_options['rtl'] ? ',"rtl": "true"' : '' ); ?>}'>
			<?php while ( $products->have_posts() ) : $products->the_post(); ?>
				<div <?php post_class( $class ); ?>>
					<?php wc_get_template( 'woorockets/content-product/style-' . esc_attr( $layout ) . '.php' ); ?>
				</div>
			<?php endwhile; // end of the loop. ?>
		</div>
	</div><!-- .p-upsell -->
<?php endif; ?>

<?php wp_reset_postdata(); ?>
