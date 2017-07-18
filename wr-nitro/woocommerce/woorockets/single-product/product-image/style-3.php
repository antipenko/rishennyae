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

// Right to left
$rtl = $wr_nitro_options['rtl'];

// Get offset width
$offset = $wr_nitro_options['wr_layout_offset'];

// Get sale price dates
$countdown = get_post_meta( get_the_ID(), '_show_countdown', true );
$start     = get_post_meta( get_the_ID(), '_sale_price_dates_from', true );
$end       = get_post_meta( get_the_ID(), '_sale_price_dates_to', true );
$now       = date( 'd-m-y' );

?>
<div class="p-gallery">
	<div id="p-large" class="wr-nitro-carousel images">
		<?php
			$gallery = $lightbox = '';
			if ( is_customize_preview() ) {
				$lightbox = 'class="lightbox-disable"';
			}
			if ( has_post_thumbnail() ) {
				$image_title 	= esc_attr( get_the_title( get_post_thumbnail_id() ) );
				$image_caption 	= get_post( get_post_thumbnail_id() )->post_excerpt;
				$image_link  	= wp_get_attachment_url( get_post_thumbnail_id() );
				$image       	= get_the_post_thumbnail(
					$post->ID,
					apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ),
					array(
						'title' => $image_title,
						'alt'   => $image_title,
					)
				);

				$attachment_count = count( $product->get_gallery_attachment_ids() );

				if ( $attachment_count > 0 ) {
					$gallery = 'data-lightbox-gallery="' . esc_attr( $post->ID ) . '"';
				} else {
					$gallery = '';
				}

				echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<a ' . $lightbox . ' href="%s" itemprop="image" title="%s" data-lightbox="nivo" ' . $gallery . '>%s</a>', $image_link, $image_caption, $image ), $post->ID );

			} else {
				echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<img src="%s" alt="%s" />', wc_placeholder_img_src(), esc_html__( 'Placeholder', 'wr-nitro' ) ), $post->ID );
			}

			$attachment_ids = $product->get_gallery_attachment_ids();
			if ( $attachment_ids ) {
				foreach ( $attachment_ids as $attachment_id ) {
					$image_link = wp_get_attachment_url( $attachment_id );
					if ( ! $image_link ) continue;

					$image_title = esc_attr( get_the_title( $attachment_id ) );
					$image       = wp_get_attachment_image(
						$attachment_id,
						apply_filters( 'single_product_small_thumbnail_size', 'shop_single' ), false,
						array(
							'title' => $image_title,
							'alt'   => $image_title,
						)
					);

					echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', sprintf( '<a ' . $lightbox . ' href="%s" title="%s" data-lightbox="nivo" data-lightbox-gallery="' . esc_attr( $post->ID ) . '">%s</a>', $image_link, $image_title, $image ), $attachment_id, $post->ID );
				}
			}
		?>
	</div><!-- #p-large -->
	<div class="p-thumb-nav pa">
		<div id="p-thumb" class="fc fcc fcw">
			<?php
				if ( has_post_thumbnail() ) {
					$image_title 	= esc_attr( get_the_title( get_post_thumbnail_id() ) );
					$image_caption 	= get_post( get_post_thumbnail_id() )->post_excerpt;
					$image_link  	= wp_get_attachment_url( get_post_thumbnail_id() );
					$image       	= get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', '60x60' ), array(
						'title'	=> $image_title,
						'alt'	=> $image_title
						) );

					echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<div class="nav-item">%s</div>', $image ), $post->ID );
				} else {
					echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<div class="nav-item"><img src="%s" alt="%s" /></div>', wc_placeholder_img_src(), esc_html__( 'Placeholder', 'wr-nitro' ) ), $post->ID );
				}

				$attachment_ids = $product->get_gallery_attachment_ids();
				if ( $attachment_ids ) {
					foreach ( $attachment_ids as $i => $attachment_id ) {
						$props = wc_get_product_attachment_props( $attachment_id, $post );

						if ( ! $props['url'] ) {
							continue;
						}

						$image = wp_get_attachment_image( $attachment_id, apply_filters( 'single_product_small_thumbnail_size', '60x60' ), 0, $props );

						echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', sprintf( '<div class="nav-item">%s</div>', $image ), $attachment_id, $post->ID );
					}
				}
			?>
		</div><!-- #p-thumb -->
	</div><!-- .p-thumb-nav -->
	<?php if ( 'yes' == $countdown && $end && date( 'd-m-y', $start ) <= $now ) : ?>
		<div class="product__countdown pa">
			<div class="wr-nitro-countdown fc jcsb tc aic" data-time='{"day": "<?php echo date( 'd', $end ); ?>", "month": "<?php echo date( 'm', $end ); ?>", "year": "<?php echo date( 'Y', $end ); ?>"}'></div>
		</div><!-- .product__countdown -->
	<?php endif; ?>
</div><!-- .p-gallery -->
<?php echo '<scr' . 'ipt>'; ?>
(function($) {
		"use strict";

		$(document).ready(function() {
			var sync1    = $( '#p-large' ),
				sync2    = $( '#p-thumb' ),
				flag     = false,
				duration = 600;

			sync1.owlCarousel({
				items: 1,
				nav: true,
				loop: true,
				dots: false,
				animateIn: 'fadeIn',
				animateOut: 'fadeOut',
				navText: [
					"<i class=\'fa fa-angle-left\'></i>",
					"<i class=\'fa fa-angle-right\'></i>"
				],
				<?php if ( $rtl ) : ?>
				rtl: true
				<?php endif; ?>
			});

			// Get current item to sync with navigation
			sync1.on('changed.owl.carousel', function( el ) {
				var count = el.item.count-1;
				var currentItem = parseInt( Math.round(el.item.index - (el.item.count/2) - .5) );
				sync2.find(".nav-item").removeClass("active") .eq(currentItem) .addClass("active");
			})

			$( '#p-thumb .nav-item:first-child' ).addClass( 'active' );
			$( '#p-thumb .nav-item' ).click( function( e ) {
				e.preventDefault();

				// Trigger to carousel
				sync1.trigger('to.owl.carousel', [$(this).index(), duration, true]);

				var _this = $( this );
				_this.closest( '#p-thumb' ).find('.active').removeClass( 'active' );
				_this.addClass( 'active' );
			});

			// Set navigation max height
			var max_height = $( '.p-gallery' ).height();
			$( '#p-thumb' ).height( max_height / 2 );
		});
	})(jQuery);
<?php echo '</scr' . 'ipt>'; ?>
