<?php
/**
 * Product quantity inputs
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<div class="quantity pr fl nitro-line">
	<input type="number" class="nitro-line" step="<?php echo esc_attr( $step ); ?>" min="<?php echo esc_attr( $min_value ); ?>" max="<?php echo esc_attr( $max_value ); ?>" name="<?php echo esc_attr( $input_name ); ?>" value="<?php echo esc_attr( $input_value ); ?>" title="<?php echo esc_attr_x( 'Qty', 'Product quantity input tooltip', 'wr-nitro' ) ?>" size="4" />
	<div class="qty pa tc">
		<a class="plus db body_color hover-primary nitro-line" href="javascript:void(0);">
			<i class="fa fa-angle-up"></i>
		</a>
		<a class="minus db body_color hover-primary" href="javascript:void(0);">
			<i class="fa fa-angle-down"></i>
		</a>
	</div>
</div>
