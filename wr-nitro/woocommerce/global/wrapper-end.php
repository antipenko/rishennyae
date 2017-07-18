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

$cols = $html = '';

// Wrap container if not single product page
if ( ! is_product() ) :
	$html = '</div>';
endif;

// Get settings
$layout = $wr_nitro_options['wc_archive_layout'];
$sticky = $wr_nitro_options['wc_archive_sidebar_sticky'];
$style  = $wr_nitro_options['w_style'];

?>
			</main><!-- .shop-main -->

			<?php if ( ! is_product() && 'no-sidebar' != $layout ) : ?>

				<div id="shop-sidebar" class="primary-sidebar <?php if ( $sticky == true ) echo 'primary-sidebar-sticky'; ?> widget-style-<?php echo esc_attr( $style ) . ' ' . ( is_customize_preview() ? 'customizable customize-section-widget_styles ' : '' ); ?> mgt30">
					<?php if ( $sticky == true ) echo '<div class="primary-sidebar-inner">'; ?>
						<?php dynamic_sidebar( 'wc-sidebar' ); ?>
					<?php if ( $sticky == true ) echo '</div>'; ?>
				</div>

			<?php endif; ?>
		</div><!-- .fc -->
	</div><!-- .row -->
<?php echo wp_kses_post( $html ); ?>
