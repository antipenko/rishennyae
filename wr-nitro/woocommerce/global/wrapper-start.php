<?php
/**
 * Content wrappers
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
// Get theme options
$wr_nitro_options = WR_Nitro::get_options();

$style     = $wr_nitro_options['wc_single_style'];
$fullwidth = $wr_nitro_options['wc_archive_full_width'];

$html = '';

// Wrap container if not single product page
if ( is_archive() && $fullwidth == true ) {
	$html = '<div class="archive-full-width">';
} elseif ( ! is_product() ) {
	$html = '<div class="container">';
}

?>
	<?php echo wp_kses_post( $html ); ?>
		<div class="row">
			<div class="fc fcw">
				<main id="shop-main" class="main-content mgt30 <?php if ( is_archive() ) echo 'mgb30'; ?>" role="main">


