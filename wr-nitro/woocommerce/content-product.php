<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woothemes.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.6.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

$wr_nitro_options = WR_Nitro::get_options();
$wr_nitro_shortcode_attrs = class_exists( 'Nitro_Toolkit_Shortcode' ) ? Nitro_Toolkit_Shortcode::get_attrs() : null;

// Ensure visibility
if ( ! $product || ! $product->is_visible() )
	return;

// Extra post classes
$wr_classes = array();

// Grid column
$wr_columns = $wr_nitro_shortcode_attrs ? $wr_nitro_shortcode_attrs['columns'] : $wr_nitro_options['wc_archive_layout_column'];
if ( $wr_nitro_options['wc_archive_layout_column'] ) {
	array_push( $wr_classes, 'cxs-12 cs-6 cm-' . (int) ( 12 / $wr_columns ) );
}

// Get masonry settings
$wr_masonry_image_size = get_post_meta( get_the_ID(), 'wc_masonry_product_size', true );
if ( 'wc-large-square' == $wr_masonry_image_size || 'wc-large-rectangle' == $wr_masonry_image_size ) {
	array_push( $wr_classes, 'large' );
}

$wr_layout = $wr_nitro_shortcode_attrs ? $wr_nitro_shortcode_attrs['style'] : $wr_nitro_options['wc_archive_item_layout'];

// Style of list product
$wr_style = $wr_nitro_shortcode_attrs ? $wr_nitro_shortcode_attrs['list_style'] : $wr_nitro_options['wc_archive_style'];
?>

<div <?php post_class( $wr_classes ); ?>>
	<?php
		if ( $wr_nitro_options['wc_archive_item_animation'] && ! ( $wr_nitro_shortcode_attrs['slider'] && 'sc-products' == $wr_nitro_shortcode_attrs['shortcode'] ) ) {
			echo '<div class="wr-item-animation">';
		}
			if ( 'list' == $wr_style ) :
				wc_get_template( 'woorockets/content-product/style-list.php' );
			else :
				wc_get_template( 'woorockets/content-product/style-' . esc_attr( $wr_layout ) . '.php' );
			endif;
		if ( $wr_nitro_options['wc_archive_item_animation'] && ! ( $wr_nitro_shortcode_attrs['slider'] && 'sc-products' == $wr_nitro_shortcode_attrs['shortcode'] ) ) {
			echo '</div>';
		}
	?>
</div>
