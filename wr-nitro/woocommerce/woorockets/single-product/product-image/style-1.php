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

// Get offset width
$offset = $wr_nitro_options['wr_layout_offset'];
?>
<div class="p-gallery wr-nitro-carousel images" data-owl-options='{"items": "1", "animateIn": "fadeIn", "animateOut": "fadeOut", "nav": "true"<?php echo ( $wr_nitro_options['rtl'] ? ',"rtl": "true"' : '' ); ?>}'>
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
				apply_filters( 'single_product_large_thumbnail_size', 'full' ),
				array(
					'title'	          => $image_title,
					'alt'	          => $image_title,
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
					apply_filters( 'single_product_small_thumbnail_size', 'full' ), false,
					array(
						'title'	          => $image_title,
						'alt'	          => $image_title,
					)
				);

				echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<a ' . $lightbox . ' href="%s" itemprop="image" title="%s" data-lightbox="nivo" ' . $gallery . '>%s</a>', $image_link, $image_caption, $image ), $post->ID );
			}
		}
	?>
</div><!-- .p-gallery -->
<?php echo '<scr' . 'ipt>'; ?>
	(function($) {
		"use strict";

		$( document ).ready( function() {
			var adminbarHeight = $( '#wpadminbar' ).length ? $( '#wpadminbar' ).height() : 0,
				offset   = <?php echo esc_js( $offset ); ?>,
				header   = $( '.header-outer' ),
				actions  = $( '.p-single-middle' ).outerHeight(),
				content  = $( '.p-single-info' ).outerHeight();

			setTimeout( function() {
				var height = ( $( window ).height() - header.height() - actions - adminbarHeight );
				if ( height >= content + 10 ) {
					$( '.p-single-top, .p-gallery img' ).css( 'height', ( height - offset * 2 ) );
				} else {
					$( '.p-single-top, .p-gallery img' ).css( 'height', ( content + 30 ) );
				}
			}, 100)

			$( window ).on('resize', function() {
				var height = ( $( window ).height() - header.height() - actions - adminbarHeight );
				if ( window.innerHeight >= 730 && height >= content + 10 ) {
					$( '.p-single-top, .p-gallery img' ).css( 'height', height - offset * 2 );
				} else {
					$( '.p-single-top, .p-gallery img' ).css( 'height', ( content + 30 ) );
				}
			});
		});

	})(jQuery);
<?php echo '</scr' . 'ipt>'; ?>
