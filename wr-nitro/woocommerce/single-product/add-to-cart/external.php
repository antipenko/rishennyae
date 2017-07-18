<?php
/**
 * External product add to cart
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Icon Set
$icons = $wr_nitro_options['wc_icon_set'];
?>

<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

<p class="cart mg0">
	<a href="<?php echo esc_url( $product_url ); ?>" rel="nofollow" class="single_add_to_cart_button button alt btr-50 db pdl20 pdr20 fl"><i class="nitro-icon-<?php echo esc_attr( $icons ); ?>'-cart mgr10"></i><?php echo esc_html( $button_text ); ?></a>
</p>

<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
