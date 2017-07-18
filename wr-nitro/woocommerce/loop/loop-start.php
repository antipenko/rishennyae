<?php
/**
 * Product Loop Start
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

// Get theme options.
$wr_nitro_options = WR_Nitro::get_options();
$wr_nitro_shortcode_attrs = class_exists( 'Nitro_Toolkit_Shortcode' ) ? Nitro_Toolkit_Shortcode::get_attrs() : null;

// Style of list product
$wr_style      = $wr_nitro_shortcode_attrs ? $wr_nitro_shortcode_attrs['list_style'] : $wr_nitro_options['wc_archive_style'];
$wr_item_style = $wr_nitro_shortcode_attrs ? $wr_nitro_shortcode_attrs['style'] : $wr_nitro_options['wc_archive_item_layout'];
$wr_sizer      = '';
$wr_attr       = array();
$wr_classes    = array();

// List style
if ( 'masonry' == $wr_style ) {
	$wr_attr[]    = 'data-masonry=\'{"selector":".product", "columnWidth":".grid-sizer"}\'';
	$wr_classes[] = 'wr-nitro-masonry masonry-layout';
} else {
	$wr_classes[] = $wr_style . ' ' . esc_attr( $wr_style ) . '-layout';
}

// Enable border
if ( $wr_nitro_options['wc_archive_border_wrap'] ) {
	$wr_classes[] = 'boxed';
} else {
	$wr_classes[] = 'un-boxed';
}

// Pagination style
if ( $wr_nitro_options['wc_archive_pagination_type'] == 'number' ) {
	$wr_classes[] = 'pag-number';
}

// Item style
$wr_classes[] = 'item-style-' . absint( $wr_item_style );

// Catalog mode class
if ( $wr_nitro_options['wc_archive_catalog_mode'] ) {
	$wr_classes[] = 'catalog';
}

// Column layout
if ( 'list' != $wr_style ) {
	$wr_classes[] = 'columns-' . ( $wr_nitro_shortcode_attrs ? $wr_nitro_shortcode_attrs['columns'] : $wr_nitro_options['wc_archive_layout_column'] );
	$wr_sizer  = 'cs-6 cm-' . (int) ( 12 / ( $wr_nitro_shortcode_attrs ? $wr_nitro_shortcode_attrs['columns'] : $wr_nitro_options['wc_archive_layout_column'] ) );
}

// Slider setting for shortcode products
if ( $wr_nitro_shortcode_attrs['slider'] && 'sc-products' == $wr_nitro_shortcode_attrs['shortcode'] ) {
	if ( ! empty( $wr_nitro_shortcode_attrs['columns'] ) ) {
		$wr_attr_slider[] = '"items": "' . ( int ) $wr_nitro_shortcode_attrs['columns'] . '"';
	}
	if ( ! empty( $wr_nitro_shortcode_attrs['auto_play'] ) ) {
		$wr_attr_slider[] = '"autoplay": "true"';
	}
	if ( ! empty( $wr_nitro_shortcode_attrs['navigation'] ) ) {
		$wr_attr_slider[] = '"nav": "true"';
	}
	if ( ! empty( $wr_nitro_shortcode_attrs['pagination'] ) ) {
		$wr_attr_slider[] = '"dots": "true"';
	}

	if ( ! empty( $wr_attr_slider ) ) {
		$wr_attr[] = 'data-owl-options=\'{' . esc_attr( implode( ', ', $wr_attr_slider ) ) . ',"tablet":"3","mobile":"1"' . ( $wr_nitro_options['rtl'] ? ',"rtl": "true"' : '' ) . '}\'';
	}
	$wr_classes[] = 'wr-nitro-carousel';
}
?>

<div <?php echo implode( ' ', $wr_attr ); ?> class="products <?php echo esc_attr( $wr_nitro_shortcode_attrs['shortcode'] ) . ' ' . implode( ' ', $wr_classes ) . ( ( is_customize_preview() && ! $wr_nitro_shortcode_attrs ) ? ' customizable customize-section-product_list' : '' );  ?>">

<?php if ( $wr_style == 'masonry' ) {
	echo '<div class="grid-sizer ' . esc_attr( $wr_sizer ) . '"></div>';
} ?>

