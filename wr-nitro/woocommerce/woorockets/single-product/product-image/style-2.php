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

// Get thumbnail position
$thumb_position = $wr_nitro_options['wc_single_thumb_position'];

$zoom = '';
// Get preview image zoom settings
if ( ! wp_is_mobile() ) {
	$zoom = $wr_nitro_options['wc_single_image_zoom'];
}
$type       = $wr_nitro_options['wc_single_image_zoom_type'];
$mousewheel = $wr_nitro_options['wc_single_image_mousewheel'];
$easing     = $wr_nitro_options['wc_single_image_easing'];
$width      = $wr_nitro_options['wc_single_image_zoom_width'];
$height     = $wr_nitro_options['wc_single_image_zoom_height'];

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
<div class="p-gallery<?php echo ( $zoom ? ' magnifier' : '' ); ?>">
	<div class="product-preview pr">
		<?php if ( 'yes' == $countdown && $end && date( 'd-m-y', $start ) <= $now ) : ?>
			<div class="product__countdown pa bgw">
				<div class="wr-nitro-countdown fc jcsb tc aic" data-time='{"day": "<?php echo date( 'd', $end ); ?>", "month": "<?php echo date( 'm', $end ); ?>", "year": "<?php echo date( 'Y', $end ); ?>"}'></div>
			</div><!-- .product__countdown -->
		<?php endif; ?>
		<div id="p-large" class="wr-nitro-carousel exclude-carousel images <?php if ( $thumb_position == 'left' ) echo 'fr'; ?> ">
			<?php
				$gallery = $lightbox = '';
				if ( is_customize_preview() ) {
					$lightbox = 'class="lightbox-disable"';
				}

				if ( has_post_thumbnail() ) {
					$image_title 	= esc_attr( get_the_title( get_post_thumbnail_id() ) );
					$image_caption 	= get_post( get_post_thumbnail_id() )->post_excerpt;
					$image_link  	= wp_get_attachment_url( get_post_thumbnail_id() );
					$args = array(
						'title'	          => $image_title,
						'alt'	          => $image_title,
					);
					// Set attr for image zoom
					if ( $zoom ) {
						$args['data-zoom-image'] = $image_link;
						$args['class']           = 'magnifier-zoom';
					}

					$image       	= get_the_post_thumbnail(
						$post->ID,
						apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ),
						$args
					);

					$attachment_count = count( $product->get_gallery_attachment_ids() );

					if ( $attachment_count > 0 ) {
						$gallery = 'data-lightbox-gallery="' . esc_attr( $post->ID ) . '"';
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
						$args = array(
							'title'	          => $image_title,
							'alt'	          => $image_title,
						);
						// Set attr for image zoom
						if ( $zoom ) {
							$args['data-zoom-image'] = $image_link;
							$args['class']           = 'magnifier-zoom';
						}

						$image_title = esc_attr( get_the_title( $attachment_id ) );
						$image       = wp_get_attachment_image(
							$attachment_id,
							apply_filters( 'single_product_small_thumbnail_size', 'shop_single' ), false,
							$args
						);

						echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<a ' . $lightbox . ' href="%s" itemprop="image" title="%s" data-lightbox="nivo" ' . $gallery . '>%s</a>', $image_link, $image_caption, $image ), $post->ID );
					}
				}
			?>
		</div><!-- #p-large -->
	</div><!-- .product-preview -->
	<div id="p-thumb" class="<?php if ( 'bottom' == $thumb_position ) echo 'wr-nitro-carousel exclude-carousel'; ?>">
		<?php

			if ( 'bottom' != $thumb_position ) {
				echo '<div class="prev-item pa"><i class="fa fa-angle-up"></i></div><div class="next-item pa"><i class="fa fa-angle-down"></i></div>';
				echo '<div class="nav-wrap oh pr">';
				echo '<div class="nav-wrap-animation pr fc fcc ts-03">';
			}

			if ( has_post_thumbnail() ) {
				$image_title 	= esc_attr( get_the_title( get_post_thumbnail_id() ) );
				$image_caption 	= get_post( get_post_thumbnail_id() )->post_excerpt;
				$image_link  	= wp_get_attachment_url( get_post_thumbnail_id() );
				$image       	= get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'shop_thumbnail' ), array(
					'title'	=> $image_title,
					'alt'	=> $image_title
					) );

				echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<div class="nav-item">%s</div>', $image ), $post->ID );
			} else {
				echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<div class="nav-item"><img src="%s" alt="%s" /></div>', wc_placeholder_img_src(), esc_html__( 'Placeholder', 'wr-nitro' ) ), $post->ID );
			}

			$attachment_ids = $product->get_gallery_attachment_ids();
			if ( $attachment_ids ) {
				foreach ( $attachment_ids as $attachment_id ) {

					$props = wc_get_product_attachment_props( $attachment_id, $post );

					if ( ! $props['url'] ) {
						continue;
					}

					$image = wp_get_attachment_image( $attachment_id, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ), 0, $props );

					echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', sprintf( '<div class="nav-item">%s</div>', $image ), $attachment_id, $post->ID );
				}
			}

			if ( 'bottom' != $thumb_position ) {
				echo '</div>';
				echo '</div>';
			}
		?>
	</div><!-- #p-thumb -->
</div><!-- .p-gallery -->
<?php echo '<scr' . 'ipt>'; ?>
	(function($) {
		"use strict";

		$(document).ready(function() {
			var sync1    = $( '#p-large' ),
				sync2    = $( '#p-thumb' ),
				flag     = false,
				duration = 500;

			function image_owlCarousel() {
				// Check if OwlCarousel plugin for jQuery is loaded before setting up carousel.
				if ( typeof $.fn.owlCarousel == 'undefined' ) {
					return setTimeout( image_owlCarousel, 100 );
				}

				sync1.owlCarousel({
					items: 1,
					nav: true,
					loop: true,
					dots: false,
					navText: [
						"<i class=\'fa fa-angle-left\'></i>",
						"<i class=\'fa fa-angle-right\'></i>"
					],
					<?php if ( $zoom ) : ?>
					onTranslated: function(e, ui) {
						$('.zoomContainer').remove();
						image_elevateZoom();
					},
					<?php endif; ?>
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

				<?php if ( $thumb_position == 'bottom' ) : ?>
				sync2.owlCarousel({
					items: 5,
					nav: false,
					dots: true,
					mouseDrag: false,
				}).on( 'click', '.owl-item', function () {
					sync1.trigger('to.owl.carousel', [$(this).index(), duration, true]);
				});
				<?php else: ?>
				$( '#p-thumb .nav-item:first-child' ).addClass( 'active' );
				$( '#p-thumb .nav-item' ).click( function( e ) {
					e.preventDefault();

					// Trigger to carousel
					sync1.trigger('to.owl.carousel', [$(this).index(), duration, true]);
					var _this = $( this );
					_this.closest( '#p-thumb' ).find('.active').removeClass( 'active' );
					_this.addClass( 'active' );
				});

				// Set max height
				var max_height = $( '.p-gallery' ).height();
				var items  = $( '#p-thumb .nav-item' );

			    var number = parseInt( max_height / 70 ),
					last   = items.last().index() + 1,
					height = ( number ) * 70,
					step   = 0;

				// Handle next/prev item
				$( '#p-thumb .next-item' ).click( function() {
					var list_thumb = $( '#p-thumb' );
					var list_thumb_info = list_thumb[0].getBoundingClientRect();
					var height_list_thumb = $( '#p-thumb' ).height();
					var item_current = list_thumb.find( '.nav-item.active' );
					var item_current_next = item_current.next();

					if( ! item_current_next.length ) {
						return;
					}

					var item_current_next_info = item_current_next[0].getBoundingClientRect();

					if( item_current_next_info.bottom > list_thumb_info.bottom ) {
						var nav_wrap_animation = $( '#p-thumb .nav-wrap-animation' );
						var top_animation = parseInt( nav_wrap_animation.css( 'top' ) );
						var set_top_animation = top_animation - item_current_next_info.height - 5;

						nav_wrap_animation.css( 'top', set_top_animation );
					}

					sync1.trigger('next.owl.carousel');
				});

				$( '#p-thumb .prev-item' ).click( function() {
					var list_thumb = $( '#p-thumb' );
					var list_thumb_info = list_thumb[0].getBoundingClientRect();
					var height_list_thumb = $( '#p-thumb' ).height();
					var item_current = list_thumb.find( '.nav-item.active' );
					var item_current_prev = item_current.prev();

					if( ! item_current_prev.length ) {
						return;
					}

					var item_current_prev_info = item_current_prev[0].getBoundingClientRect();

					if( item_current_prev_info.top < list_thumb_info.top ) {
						var nav_wrap_animation = $( '#p-thumb .nav-wrap-animation' );
						var top_animation = parseInt( nav_wrap_animation.css( 'top' ) );
						var set_top_animation = top_animation + item_current_prev_info.height + 5;

						nav_wrap_animation.css( 'top', set_top_animation );
					}

					sync1.trigger('prev.owl.carousel', [300]);

				});
				<?php endif; ?>
			}
			image_owlCarousel();

			<?php if ( $zoom ) : ?>
			function image_elevateZoom() {
				// Check if Elevate Zoom plugin for jQuery is loaded before setting up zooming.
				if (typeof $.fn.elevateZoom == "undefined") {
					return setTimeout(image_elevateZoom, 100);
				}

				$('.owl-item.active .magnifier-zoom').elevateZoom({
					responsive: true,
					zoomType: "<?php echo esc_js( $type ); ?>",
					<?php if ( 'window' == $type ) : ?>
					zoomWindowWidth: <?php echo esc_js( $width ); ?>,
					zoomWindowHeight: <?php echo esc_js( $height ); ?>,
					<?php endif; ?>
					<?php echo ( $easing ? "easing : true," : '' ); ?>
					<?php echo ( $mousewheel ? "scrollZoom : true," : '' ); ?>
				});
			}
			image_elevateZoom();
			<?php endif; ?>
		});
	})(jQuery);
<?php echo '</scr' . 'ipt>'; ?>
