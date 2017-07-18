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

$wr_output = '';

// Get content quote
$wr_content = get_post_meta( get_the_ID(), 'format_quote_content', true );
$wr_author  = get_post_meta( get_the_ID(), 'format_quote_author', true );

if ( ! empty( $wr_content ) ) {
	$wr_output .= '<h4 class="entry-title"><a href="' . esc_url( get_permalink() ) . '">' . get_the_title() . '</a></h4>';
	$wr_output .= '<div class="quote-content mgt10 pr">"' . esc_html( $wr_content ) . '"</div>';
	$wr_output .= '<div class="quote-author mgt10 fwb"><span class="mgr10">-</span>' . esc_html( $wr_author ) . '</div>';

	echo wp_kses_post( $wr_output );
} else {
	WR_Nitro_Render::get_template( 'blog/content/standard' );
}
