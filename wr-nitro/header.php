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

// Get theme options
$wr_nitro_options = WR_Nitro::get_options();

// Get header template
WR_Nitro_Render::get_template( 'common/header' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); echo esc_attr( $wr_nitro_options['rtl'] ? 'dir=rtl' : '' ); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php wp_head(); ?>
</head>


<body <?php body_class(); ?> <?php WR_Nitro_Render::body_animation(); ?>>
	<?php 
		// $pageName = basename( get_page_template() );
		// echo "<strong style='position:absolute; top:30px; left:30px;z-index:9999;'> $pageName </strong>";

	?>

	<?php
		// Render page loader
	echo wp_kses_post( WR_Nitro_Render::page_loader() );
	?>

	<div class="wrapper-outer">
		<div class="wrapper">
			<section class=" " id="wrapper-head">
			<?php
			if ( $wr_nitro_options['under_construction'] && ( is_customize_preview() || ! is_super_admin() ) ) return;
			echo apply_filters( 'wr_header', WR_Nitro_Header_Builder::prop( 'html' ) );
			?>

			<?php if ( function_exists('yoast_breadcrumb') && !is_front_page() && !is_home() ) { ?>
			<div class="row site-title site-title__p0">
				<div class="container " >
					<div class="breadcrumbs" id="breadcrumbs">
						<?php yoast_breadcrumb('',' '); ?>		
					</div>
				</div>



				<?php } ?>
			</div>
		</section>
