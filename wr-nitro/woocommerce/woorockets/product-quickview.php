<?php
/**
 * The template for displaying content of quick view product
 *
 */

global $post, $woocommerce, $product;

$wr_nitro_options = WR_Nitro::get_options();

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<div class="quickview-modal">
	<div class="row">
		<div class="cm-6">
			<div class="p-lightbox-img pr">
				<?php
					if ( $product->is_on_sale() ) {
						wc_get_template( 'loop/sale-flash.php' );
					} else if ( ! $product->get_price() ) {
						echo '<span class="product__badge free">' . esc_html__( 'Бесплатно', 'wr-nitro' ) . '</span>';
					}
				?>
				<div id="p-preview" class="wr-nitro-carousel" data-owl-options='{"items": "1", "nav": "true"<?php echo ( $wr_nitro_options['rtl'] ? ',"rtl": "true"' : '' ); ?>}'>
					<?php
						// Get post thumbnail
						if ( has_post_thumbnail() ) {
							echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '%s', get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) ) ) );
						}

						// Get attachment file
						$loop = 0;

						$attachment_ids = $product->get_gallery_attachment_ids();

						if ( $attachment_ids ) {
							foreach ( $attachment_ids as $attachment_id ) {
								echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', sprintf( '%s', wp_get_attachment_image( $attachment_id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ) ), wp_get_attachment_url( $attachment_id ) ) );
								$loop++;
							}
						}
					?>
				</div><!-- #p-preview -->
			</div><!-- .p-lightbox-img -->
		</div><!-- .cm-6 -->

		<div class="cm-6 info">
			<h1 itemprop="name" class="product_title entry-title mgb10"><a href="<?php esc_url( the_permalink() ); ?>" title="<?php esc_attr( the_title() ); ?>"><?php the_title(); ?></a></h1>
			<div class="fc jcsb aic mgb20">
				<?php
					echo woocommerce_template_single_price();
					echo woocommerce_template_single_rating();
				?>
			</div>
			<div itemprop="description">
				<?php echo apply_filters( 'woocommerce_short_description', $post->post_excerpt ) ?>

				<h3 class="view_detail"><a href="<?php esc_url( the_permalink() ); ?>" title="<?php esc_attr( the_title() ); ?>"><?php esc_html_e( 'View Full Details', 'wr-nitro' ); ?></a></h3>
			</div>
			<?php if ( ! $wr_nitro_options['wc_archive_catalog_mode'] && $product->is_in_stock() ) : ?>
				<div class="quickview-button clear mgt20 mgb20 pdt20">
					<?php woocommerce_template_single_add_to_cart(); ?>
				</div>
			<?php endif; ?>
			<div class="p-meta pdt20">
				<?php
					$cat_count = sizeof( get_the_terms( $post->ID, 'product_cat' ) );
					$tag_count = sizeof( get_the_terms( $post->ID, 'product_tag' ) );
				?>

				<?php echo wp_kses_post( $product->get_categories( ', ', '<span class="posted_in db mgb10">' . _n( '<span class="dib">Category</span>:  ', '<span class="dib">Categories</span>:  ', $cat_count, 'wr-nitro' ) . ' ', '</span>' ) ); ?>

				<?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>

					<span class="sku_wrapper db mgb10"><span class="dib"><?php esc_html_e( 'SKU', 'wr-nitro' ); ?> </span>: <span class="sku" itemprop="sku"><?php echo ( $sku = $product->get_sku() ) ? $sku : esc_html__( 'N/A', 'wr-nitro' ); ?></span></span>

				<?php endif; ?>

				<span class="availability mgb10">
					<?php $availability = $product->get_availability(); ?>
					<span class="dib"><?php esc_html_e( 'Availability:', 'wr-nitro' ); ?></span>:
					<span class="stock <?php echo esc_attr( $availability['class'] ); ?>">
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

				<?php echo wp_kses_post( $product->get_tags( ', ', '<span class="tagged_as">' . _n( '<span class="dib db">Tag</span>:  ', '<span class="dib">Tags</span>:  ', $tag_count, 'wr-nitro' ) . ' ', '</span>' ) ); ?>
			</div><!-- .p-meta -->
		</div><!-- .cm-6 -->
	</div><!-- .row -->
</div><!-- .quickview-modal -->
<?php do_action( 'woocommerce_after_quickview_modal' ); ?>
