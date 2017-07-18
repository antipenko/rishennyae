<?php
/**
 * Thankyou page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.woothemes.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// Get theme options
$wr_nitro_options = WR_Nitro::get_options();

if ( $order ) : ?>
<h1 class="title-account"><?php the_title(); ?></h1>
<div class="form-container">
	<?php if ( $order->has_status( 'failed' ) ) : ?>

		<p class="woocommerce-thankyou-order-failed"><?php esc_html_e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'wr-nitro' ); ?></p>

		<p class="woocommerce-thankyou-order-failed-actions">
			<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php esc_html_e( 'Pay', 'wr-nitro' ) ?></a>
			<?php if ( is_user_logged_in() ) : ?>
				<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button pay"><?php esc_html_e( 'My Account', 'wr-nitro' ); ?></a>
			<?php endif; ?>
		</p>

	<?php else : ?>

		<p class="woocommerce-thankyou-order-received"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Thank you. Your order has been received.', 'wr-nitro' ), $order ); ?></p>

		<ul class="woocommerce-thankyou-order-details order_details">
			<li class="order">
				<?php esc_html_e( 'Order Number:', 'wr-nitro' ); ?>
				<strong><?php echo esc_html( $order->get_order_number() ); ?></strong>
			</li>
			<li class="date">
				<?php esc_html_e( 'Date:', 'wr-nitro' ); ?>
				<strong><?php echo date_i18n( get_option( 'date_format' ), strtotime( $order->order_date ) ); ?></strong>
			</li>
			<li class="total">
				<?php esc_html_e( 'Total:', 'wr-nitro' ); ?>
				<strong><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></strong>
			</li>
			<?php if ( $order->payment_method_title ) : ?>
			<li class="method">
				<?php esc_html_e( 'Payment Method:', 'wr-nitro' ); ?>
				<strong><?php echo esc_html( $order->payment_method_title ); ?></strong>
			</li>
			<?php endif; ?>
		</ul>
		<div class="clear"></div>

	<?php endif; ?>

	<?php do_action( 'woocommerce_thankyou_' . $order->payment_method, $order->id ); ?>
	<?php do_action( 'woocommerce_thankyou', $order->id ); ?>

<?php else : ?>

	<p class="woocommerce-thankyou-order-received"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Thank you. Your order has been received.', 'wr-nitro' ), null ); ?></p>

<?php endif; ?>
</div>

<?php echo do_shortcode( wp_kses_post( $wr_nitro_options['wc_thankyou_content'] ) ); ?>
