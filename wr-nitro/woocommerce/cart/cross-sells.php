<?php
/**
 * Cross-sells
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

$crosssells = WC()->cart->get_cross_sells();

if ( sizeof( $crosssells ) == 0 ) return;

$meta_query = WC()->query->get_meta_query();

$args = array(
	'post_type'           => 'product',
	'ignore_sticky_posts' => 1,
	'no_found_rows'       => 1,
	'posts_per_page'      => apply_filters( 'woocommerce_cross_sells_total', $posts_per_page ),
	'orderby'             => $orderby,
	'post__in'            => $crosssells,
	'meta_query'          => $meta_query
);

$products = new WP_Query( $args );

if ( $products->have_posts() ) : ?>

	<div class="cross-sells">

		<h4><?php esc_html_e( 'You may be interested in&hellip;', 'wr-nitro' ) ?></h4>

		<table class="shop_table">
			<tbody>
				<?php while ( $products->have_posts() ) : $products->the_post(); ?>
					<tr>
						<td class="product-thumbnail">
							<?php echo woocommerce_get_product_thumbnail( 'thumbnail'); ?>
						</td>
						<td class="product-name heading-color">
							<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
							<?php wc_get_template( 'loop/rating.php' ); ?>
						</td>
						<td class="product-price">
							<?php wc_get_template( 'loop/price.php' ); ?>
						</td>
						<td class="product-subtotal">
							<?php wc_get_template( 'loop/add-to-cart.php' ); ?>
						</td>
					</tr>
				<?php endwhile; // end of the loop. ?>
			</tbody>
		</table>
	</div>

<?php endif;

wp_reset_query();
