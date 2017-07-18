<?php
/**
 * Mini-cart
 *
 * Contains the markup for the mini-cart, used by the cart widget
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version 2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<?php do_action( 'woocommerce_before_mini_cart' ); ?>

<div class="cart_list-outer">
	<ul class="cart_list product_list_widget <?php echo esc_attr( $args['list_class'] ); ?>">

		<?php if ( ! WC()->cart->is_empty() ) : ?>

			<?php
				foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
					$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
					$product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

					if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {

						$product_name  = apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key );
						$thumbnail     = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( apply_filters( 'single_product_small_thumbnail_size', '60x60' ) ), $cart_item, $cart_item_key );
						$product_price = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
						?>

						<li data-key="<?php echo esc_attr( $cart_item_key ); ?>" class="<?php echo esc_attr( apply_filters( 'woocommerce_mini_cart_item_class', 'mini_cart_item', $cart_item, $cart_item_key ) ); ?>">

							<div class="img-item-outer">
								<div class="img-item">
									<?php if ( ! $_product->is_visible() ) : ?>
										<?php echo str_replace( array( 'http:', 'https:' ), '', $thumbnail ); ?>
									<?php else : ?>
										<a href="<?php echo esc_url( $_product->get_permalink( $cart_item ) ); ?>">
											<?php echo str_replace( array( 'http:', 'https:' ), '', $thumbnail ); ?>
										</a>
									<?php endif; ?>
								</div>
							</div>

							<div class="info-item">
								<h5 class="title-item">
									<?php echo '<a href="' . get_permalink( $cart_item['product_id'] ) . '">' . esc_html( $product_name ) . '</a>'; ?>
								</h5>
								<div class="price-item">
									<?php echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf( '%s &times; %s', '<span class="count-item">' . $cart_item['quantity'] . '</span>' , $product_price ) . '</span>', $cart_item, $cart_item_key ); ?>
								</div>

								<?php echo WC()->cart->get_item_data( $cart_item ); ?>

								<div class="action-item">

									<div class="edit-form-outer">
										<div class="edit-form">
											<input type="number" value="<?php echo esc_attr( $cart_item['quantity'] ); ?>" class="edit-number extenal-bdcl" />
											<div class="edit-btn-action">
												<button type="submit" class="edit-btn"><i class="fa fa-check"></i></button>
											</div>
											<button type="reset" class="cancel-btn"><i class="fa fa-close"></i></button>
										</div>
									</div>

									<div class="remove-outer">
										<?php
											echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
												'<a href="%s" class="remove" title="%s" data-product_id="%s" data-product_sku="%s"><i class="fa fa-trash-o"></i></a>',
												esc_url( WC()->cart->get_remove_url( $cart_item_key ) ),
												esc_attr__( 'Remove this item', 'wr-nitro' ),
												esc_attr( $cart_item_key ),
												esc_attr( $_product->get_sku() )
											), $cart_item_key );
										?>
									</div>
									<div class="quickview-outer"><span class="edit-cart"><i class="fa fa-pencil"></i></span></div>
								</div>
							</div>
						</li>
						<?php
					}
				}
			?>

		<?php else : ?>

			<li class="empty"><?php esc_html_e( 'No products in the cart.', 'wr-nitro' ); ?></li>

		<?php endif; ?>

	</ul><!-- end product list -->
</div>

<?php if ( ! WC()->cart->is_empty() ) :
?>
	<div class="price-checkout">
		<p data-count="<?php echo esc_attr( WC()->cart->get_cart_contents_count() ); ?>" class="total"><strong><?php esc_html_e( 'Subtotal', 'wr-nitro' ); ?>:</strong> <span class="mini-price"><?php echo WC()->cart->get_cart_subtotal(); ?></span></p>

		<?php do_action( 'woocommerce_widget_shopping_cart_before_buttons' ); ?>

		<p class="buttons">
			<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="wr-btn wr-btn-outline wc-forward"><?php esc_html_e( 'View Cart', 'wr-nitro' ); ?></a>
			<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="button checkout"><?php esc_html_e( 'Checkout', 'wr-nitro' ); ?></a>
		</p>
	</div>

<?php endif; ?>

<?php do_action( 'woocommerce_after_mini_cart' ); ?>
