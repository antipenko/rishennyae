<?php
/**
 * Pagination - Show numbered pagination for catalog pages.
 *
 * @author     WooThemes
 * @package    WooCommerce/Templates
 * @version    2.2.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wp_query;

$wr_nitro_options = WR_Nitro::get_options();

if ( $wp_query->max_num_pages <= 1 ) {
	return;
}

// Pagination type
$type = $wr_nitro_options['wc_archive_pagination_type'];

// Pagination style
$style = $wr_nitro_options['pagination_style'];

// Style of list product
$layout_style = $wr_nitro_options['wc_archive_style'];

if ( 'loadmore' == $type || 'infinite' == $type ) : ?>

	<div class="pagination wc-pagination" layout="<?php echo esc_attr( $type ); ?>" layout-style="<?php echo esc_attr( $layout_style ); ?>">
		<div class="page-ajax enable" data-page="<?php echo esc_attr( $wp_query->max_num_pages ); ?>">
			<?php echo next_posts_link( '...' ); ?>
		</div><!-- .page-ajax -->
	</div><!-- .pagination -->

<?php else : ?>

	<nav class="woocommerce-pagination nitro-line <?php echo esc_attr( $style ) . ' ' . ( is_customize_preview() ? 'customizable customize-section-pagination ' : '' ); ?>">
		<?php echo paginate_links(
			apply_filters(
				'woocommerce_pagination_args',
				array(
					'base'      => esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) ),
					'format'    => '',
					'add_args'  => '',
					'current'   => max( 1, get_query_var( 'paged' ) ),
					'total'     => $wp_query->max_num_pages,
					'prev_text' => '&larr;',
					'next_text' => '&rarr;',
					'type'      => 'list',
					'end_size'  => 3,
					'mid_size'  => 3
				)
			)
		);
		?>
	</nav>

<?php endif; ?>
