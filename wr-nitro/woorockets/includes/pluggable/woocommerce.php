<?php
/**
 * @version    1.0
 * @package    WR_Theme
 * @author     WooRockets Team <support@woorockets.com>
 * @copyright  Copyright (C) 2014 WooRockets.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.woorockets.com
 * Custom functions for WooCommerce.
 */

/**
 * Plug into WooCommerce.
 *
 * @package  WR_Theme
 * @since    1.0
 */
class WR_Nitro_Pluggable_WooCommerce {
	/**
	 * Variable to hold the initialization state.
	 *
	 * @var  boolean
	 */
	protected static $initialized = false;

	/**
	 * Initialize pluggable functions for WooCommerce.
	 *
	 * @return  voidz
	 */
	public static function initialize() {
		// Do nothing if pluggable functions already initialized.
		if ( self::$initialized ) {
			return;
		}

		// Remove some default action handlers.
		remove_action( 'woocommerce_after_shop_loop_item_title'  , 'woocommerce_template_loop_rating'   , 5  );
		remove_action( 'woocommerce_before_main_content'         , 'woocommerce_breadcrumb'             , 20 );
		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display'         , 15 );
		remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
		remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );

		// Change product rating position.
		add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 15 );

		// Add sidebar to shop page
		add_action( 'woocommerce_before_product_list', array( __CLASS__, 'update_before_archive_product' ) );
		add_action( 'woocommerce_after_shop_loop', array( __CLASS__, 'update_after_archive_product' ) );

		// Add sidebar to product details
		add_action( 'woocommerce_before_single_product', array( __CLASS__, 'update_before_single_product' ) );
		add_action( 'woocommerce_after_single_product', array( __CLASS__, 'update_after_single_product' ) );

		// Add sidebar to cart page
		add_action( 'woocommerce_before_cart_table', array( __CLASS__, 'update_before_cart_page' ) );
		add_action( 'woocommerce_after_cart', array( __CLASS__, 'update_after_cart_page' ) );

		// Add sidebar to checkout page
		add_action( 'woocommerce_after_checkout_form', array( __CLASS__, 'update_after_checkout_page' ) );

		// Register additional sidebar for WooCommerce.
		add_action( 'widgets_init',  array( __CLASS__, 'widgets_init' ) );

		// Customize product quick view.
		add_action( 'wp_ajax_wr_quickview', array( __CLASS__, 'wr_quickview' ) );
		add_action( 'wp_ajax_nopriv_wr_quickview', array( __CLASS__, 'wr_quickview' ) );
		add_action( 'wp_ajax_wr_quickbuy', array( __CLASS__, 'wr_quickbuy' ) );
		add_action( 'wp_ajax_nopriv_wr_quickbuy', array( __CLASS__, 'wr_quickbuy' ) );

		// Delete product in wishlish
		add_action( 'wp_ajax_wr_remove_product_wishlish', array( __CLASS__, 'remove_product_wishlish' ) );
		add_action( 'wp_ajax_nopriv_wr_remove_product_wishlish', array( __CLASS__, 'remove_product_wishlish' ) );

		// Delete product in cart
		add_action( 'wp_ajax_wr_product_remove', array( __CLASS__, 'product_remove' ) );
		add_action( 'wp_ajax_nopriv_wr_product_remove', array( __CLASS__, 'product_remove' ) );

		// Edit product in cart
		add_action( 'wp_ajax_wr_product_edit', array( __CLASS__, 'product_edit' ) );
		add_action( 'wp_ajax_nopriv_wr_product_edit', array( __CLASS__, 'product_edit' ) );

		// Edit product in cart
		add_action( 'wp_ajax_wr_product_add', array( __CLASS__, 'product_add' ) );
		add_action( 'wp_ajax_nopriv_wr_product_add', array( __CLASS__, 'product_add' ) );

		// Get add to cart message
		add_action( 'wp_ajax_wr_add_to_cart_message', array( __CLASS__, 'add_to_cart_message' ) );
		add_action( 'wp_ajax_nopriv_wr_add_to_cart_message', array( __CLASS__, 'add_to_cart_message' ) );

		// Change button class of variable product
		add_action( 'woocommerce_single_variation', array( __CLASS__, 'change_single_variation_add_to_cart_button' ), 20 );

		// Customize WooCommerce image dimensions.
		add_action( 'admin_init', array( __CLASS__, 'customize_image_dimensions' ), 1 );

		// Enqueue custom scripts and styles.
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );

		// Print Ajax URL for front-end.
		add_action( 'wp_head', array( __CLASS__, 'wp_head' ) );

		// Add custom fields to product options
		add_action( 'woocommerce_product_options_pricing', array( __CLASS__, 'add_custom_general_fields' ) );
		add_action( 'woocommerce_process_product_meta',    array( __CLASS__, 'add_custom_general_fields_save' ) );

		// Remove assets for buy now
		add_action( 'init', array( __CLASS__, 'remove_assets_buy_now' ) );

		add_action( 'template_redirect', array( __CLASS__, 'add_product_viewed' ) );

		// Remove WooCommerce default styles.
		add_filter( 'woocommerce_enqueue_styles', '__return_false' );

		// Customize search form.
		add_filter( 'get_product_search_form', array( __CLASS__, 'get_product_search_form' ) );

		// Customize product tabs.
		add_filter( 'woocommerce_product_tabs', array( __CLASS__, 'woocommerce_product_tabs' ), 100 );

		// Change number of products displayed per page
		add_filter( 'loop_shop_per_page', array( __CLASS__, 'change_product_per_page' ) );

		// Switch layout grid and list in product list.
		add_filter( 'wr_nitro_options', array( __CLASS__, 'switch_layout_products' ), 20 );

		// Add product title when added to wishlist
		add_filter( 'yith_wcwl_product_added_to_wishlist_message', array( __CLASS__, 'add_title_to_wishlist' ) );

		// State that initialization completed.
		self::$initialized = true;

	}

	/**
	 * Add product to list viewed
	 *
	 * @since  1.0
	 *
	 * @return  array
	 *
	 */
	public static function add_product_viewed( ) {

		if ( ! is_singular( 'product' ) ) {
			return;
		}

		global $post;

		if ( empty( $_COOKIE['woocommerce_recently_viewed'] ) )
			$viewed_products = array();
		else
			$viewed_products = (array) explode( '|', $_COOKIE['woocommerce_recently_viewed'] );

		if ( ! in_array( $post->ID, $viewed_products ) ) {
			$viewed_products[] = $post->ID;
		}

		if ( sizeof( $viewed_products ) > 15 ) {
			array_shift( $viewed_products );
		}

		// Store for session only
		wc_setcookie( 'woocommerce_recently_viewed', implode( '|', $viewed_products ) );
	}

	/**
	 * Switch layout gril and list in product list.
	 *
	 * @param array $wr_nitro_options
	 *
	 * @since  1.0
	 *
	 * @return  array
	 *
	 */
	public static function switch_layout_products( $wr_nitro_options ) {

		// Check if product list screen is toggled between list and grid view?
		if ( isset( $_GET['switch'] ) && in_array( $_GET['switch'] , array( 'grid', 'masonry', 'list' ) ) ) {
			$wr_nitro_options['wc_archive_style'] = esc_attr( $_GET['switch'] );
		}

		return $wr_nitro_options;
	}

	/**
	 * Remove wr page loader html.
	 *
	 * @return  NULL
	 */
	public static function empty_callback() {
		return NULL;
	}

	/**
	 * Remove wr page loader html.
	 *
	 * @return  link
	 */
	public static function checkout_order_received_url( $url ) {
		return $url . '&wr-buy-now=thankyou';
	}

	/**
	 * Remove custom styles.
	 *
	 * @return  string
	 */
	public static function wr_custom_styles() {
		$wr_nitro_options = WR_Nitro::get_options();
		$main_color = $wr_nitro_options['custom_color'];
		if ( empty( $main_color ) ) {
			$main_color = '#ff4064';
		}

		// Get button Settings
		$btn_solid = $btn_solid_hover = $btn_outline = $btn_outline_hover = '';
		$btn_font           = $wr_nitro_options['btn_font'];
		$btn_font_size      = $wr_nitro_options['btn_font_size'];
		$btn_line_height    = $wr_nitro_options['btn_line_height'];
		$btn_letter_spacing = $wr_nitro_options['btn_letter_spacing'];
		$btn_border_width   = $wr_nitro_options['btn_border_width'];
		$btn_border_radius  = $wr_nitro_options['btn_border_radius'];
		$btn_padding        = $wr_nitro_options['btn_padding'];
		$btn_primary_bg     = $wr_nitro_options['btn_primary_bg_color'];
		$btn_primary        = $wr_nitro_options['btn_primary_color'];

		$css = '
			#wpadminbar,
			.header-outer,
			.footer,
			.site-title {
				display:none;
			}
			.wrapper > .container {
				margin: 0;
				padding: 15px;
			}
			.woocommerce-checkout .main-content .woocommerce {
				padding: 0;
			}
			body.logged-in {
				margin-top: -32px;
			}
			a {
				color: ' . esc_attr( $main_color ) . ';
			}
			.button {
				font-size: ' . esc_attr( $btn_font_size ) . 'px;
				height: ' . esc_attr( $btn_line_height) . 'px;
				line-height: ' . ( esc_attr( $btn_line_height) - esc_attr( $btn_border_width ) * 2 ) . 'px;
				letter-spacing: ' . esc_attr( $btn_letter_spacing ) . 'px;
				border: ' . esc_attr( $btn_border_width ) . 'px solid ' . esc_attr( $btn_primary_bg['normal'] ) . ';
				border-radius: ' . esc_attr( $btn_border_radius ) . 'px;
				padding: 0;
				background-color: ' . esc_attr( $btn_primary_bg['normal'] ) . ';
				color: ' . esc_attr( $btn_primary['normal'] ) . ';
			}
			.button:hover {
				background-color: ' . esc_attr( $btn_primary_bg['hover'] ) . ';
				border-color: ' . esc_attr( $btn_primary_bg['hover'] ) . ';
				color: ' . esc_attr( $btn_primary['hover'] ) . ';
			}

		';

		return $css;
	}

	/**
	 * Remove assets Checkout page and Thank you page when show in buy now modal
	 *
	 * @since  1.0
	 *
	 * @return  void
	 *
	 */
	public static function remove_assets_buy_now() {
		if ( ! isset( $_GET[ 'wr-buy-now' ] ) )
			return;

		// Remove wr page loader html
		add_filter( 'wr_page_loader', array( __CLASS__, 'empty_callback' ) );

		// Remove custom styles
		add_filter( 'wr_custom_styles', array( __CLASS__, 'wr_custom_styles' ) );

		// Remove custom styles
		add_filter( 'wr_header_custom_css', array( __CLASS__, 'empty_callback' ) );

		// Add value buy now to url Thank you page
		add_filter( 'woocommerce_get_checkout_order_received_url', array( __CLASS__, 'checkout_order_received_url' ), 10, 1 );
	}

	/**
	 * Change number of products displayed per page.
	 *
	 * @since  1.0
	 *
	 * @return  number
	 *
	 */
	public static function change_product_per_page() {
		$wr_nitro_options = WR_Nitro::get_options();
		$number  = $wr_nitro_options['wc_archive_number_products'];

		return $number;
	}

	/**
	 * Add to cart message.
	 *
	 * @since  1.0
	 *
	 * @return  json
	 *
	 */
	public static function add_to_cart_message() {

		if( ! (isset( $_REQUEST['product_id'] ) && (int) $_REQUEST['product_id'] > 0 ) )
			return;

		$titles 	= array();
		$product_id = (int) $_REQUEST['product_id'];

		if ( is_array( $product_id ) ) {
			foreach ( $product_id as $id ) {
				$titles[] = get_the_title( $id );
			}
		} else {
			$titles[] = get_the_title( $product_id );
		}

		$titles     = array_filter( $titles );
		$added_text = sprintf( _n( '<div><b>%s</b> has been added to your cart.</div>', '%s have been added to your cart.', sizeof( $titles ), 'wr-nitro' ), '"' . wc_format_list_of_items( $titles ) . '"' );
		$message    = sprintf( '%s <a href="%s" class="wc-forward db">%s</a>', wp_kses_post( $added_text ), esc_url( wc_get_page_permalink( 'cart' ) ), esc_html__( 'View Cart', 'wr-nitro' ) );
		$data       =  array( 'message' => apply_filters( 'wc_add_to_cart_message', $message, $product_id ) );

		wp_send_json( $data );

		exit();
	}

	/**
	 * Add to cart message for product detail.
	 *
	 * @since  1.0
	 *
	 * @return  json
	 *
	 */
	public static function add_to_cart_message_product_detail( $product_id ) {
		$titles     = get_the_title( intval( $product_id ) );
		$added_text = sprintf( __( '%s has been added to your cart.', 'wr-nitro' ), '"' . $titles . '"' );
		$message    = sprintf( '<a href="%s" class="wc-forward">%s</a> %s', esc_url( wc_get_page_permalink( 'cart' ) ), esc_html__( 'View Cart', 'wr-nitro' ), esc_html( $added_text ) );
		$message    = apply_filters( 'wc_add_to_cart_message', $message, $product_id );

		return $message;
	}

	/**
	 * Delete product in cart by ajax.
	 *
	 * @since  1.0
	 *
	 * @return  json
	 *
	 */
	public static function product_remove() {
		$cart = WC()->instance()->cart;
		$cart_item_key = sanitize_title( $_POST['cart_item_key'] );

		if ( $cart_item_key ) {
			$cart->set_quantity( $cart_item_key,0 );
		}

		$print_r = array(
			'count_product' => WC()->cart->get_cart_contents_count(),
			'price_total' => WC()->cart->get_cart_subtotal()
		);

		// Show text empty if count = 0
		if ( $print_r['count_product'] == 0 ) {
			$print_r['empty'] = __( 'No products in the cart.', 'wr-nitro' );
		}

		echo json_encode( $print_r );

		exit();
	}

	/**
	 * Edit product in cart by ajax.
	 *
	 * @since  1.0
	 *
	 * @return  json
	 *
	 */
	public static function product_edit() {
		$cart = WC()->instance()->cart;
		$cart_item_key = sanitize_title( $_POST['cart_item_key'] );
		$cart_item_number = (int) $_POST['cart_item_number'];

		if ( $cart_item_key ) {
			$cart->set_quantity( $cart_item_key, $cart_item_number );
		}

		$print_r = array(
			'count_product' => WC()->cart->get_cart_contents_count(),
			'price_total' => WC()->cart->get_cart_subtotal()
		);

		// Get price total product item
		if ( isset( $cart->cart_contents[ $cart_item_key ] ) ) {
			$print_r['price'] = wc_price( $cart->cart_contents[ $cart_item_key ]['line_total'] );
		}

		// Show text empty if count = 0
		if ( $print_r['count_product'] == 0 ) {
			$print_r['empty'] = __( 'No products in the cart.', 'wr-nitro' );
		}

		echo json_encode( $print_r );

		exit();
	}

	/**
	 * Add product in cart by ajax.
	 *
	 * @since  1.0
	 *
	 * @return  json
	 *
	 */
	public static function product_add() {
		if ( ! ( isset ( $_POST['wr-custom-add-to-cart'] ) && (int) $_POST['wr-custom-add-to-cart'] > 0 && isset( $_POST['_nonce'] ) && wp_verify_nonce( $_POST['_nonce'], 'bb_wr_nitro' ) ) ) {
			wp_send_json ( array(
				'status' => 'false',
				'notice' => __( 'Not validate.', 'wr-nitro' )
			));
		}

		$product_id = (int) $_POST['wr-custom-add-to-cart'];

		// Add to cart
		$data = self::add_to_cart_action( $product_id );

		wp_send_json( $data );

		die();
	}

	/**
	 * Remove product in wishlish.
	 *
	 * @since  1.0
	 *
	 * @return  json
	 *
	 */
	public static function remove_product_wishlish() {
		if ( ! ( isset ( $_POST['product_id'] ) && isset( $_POST['_nonce'] ) && wp_verify_nonce( $_POST['_nonce'], 'bb_wr_nitro' ) ) ) {
			wp_send_json ( array(
				'status' => 'false',
				'notice' => __( 'Not validate.', 'wr-nitro' )
			));
		}

		$product_id = intval( $_POST['product_id'] );

		$user_id = get_current_user_id();

		if( $user_id ) {
			global $wpdb;
			$sql = "DELETE FROM {$wpdb->yith_wcwl_items} WHERE user_id = %d AND prod_id = %d";
            $sql_args = array(
                $user_id,
                $product_id
            );
            $wpdb->query( $wpdb->prepare( $sql, $sql_args ) );
		} else {
			$wishlist = yith_getcookie( 'yith_wcwl_products' );
	        foreach( $wishlist as $key => $item ){
	            if( $item['prod_id'] == $product_id ){
	                unset( $wishlist[ $key ] );
	            }
	        }
	        yith_setcookie( 'yith_wcwl_products', $wishlist );
	    }
		$data = array(
			'status' => 'true',
		);

		wp_send_json( $data );

		die();
	}

	/**
	 * Add product title to wishlist notice.
	 *
	 * @since  1.0
	 */
	public static function add_title_to_wishlist() {
		$product_id = isset( $_POST['add_to_wishlist'] ) ? intval( $_POST['add_to_wishlist'] ) : 0;

		if( ! $product_id ) return;

		$product_title = get_the_title( $product_id );

		return sprintf( __( '<b>%s</b> has been added to your Wishlist', 'wr-nitro' ), $product_title );
	}

	/**
	 * Customize quick buy button.
	 *
	 * @since  1.0
	 */
	public static function wr_quickbuy() {
		if ( ! ( isset ( $_POST['product_id'] ) && (int) $_POST['product_id'] > 0 && isset( $_POST['_nonce'] ) && wp_verify_nonce( $_POST['_nonce'], 'bb_wr_nitro' ) ) ) {
			wp_send_json ( array(
				'status' => 'false',
				'notice' => __( 'Not validate.', 'wr-nitro' )
			));
		}

		// Get theme options
		$wr_nitro_options    = WR_Nitro::get_options();

		// Check is shortcode
		if( isset( $_POST['shortcode_checkout'] ) && isset( $_POST['shortcode_payment'] ) ) {
			$wc_buynow_checkout     = absint( $_POST['shortcode_checkout'] );
			$wc_buynow_payment_info = absint( $_POST['shortcode_payment'] );
			$wc_buynow_btn          = 1;

		// Get setting in customizer
		} else {
			$wc_buynow_checkout     = $wr_nitro_options['wc_buynow_checkout'];
			$wc_buynow_payment_info = $wr_nitro_options['wc_buynow_payment_info'];
			$wc_buynow_btn          = $wr_nitro_options['wc_buynow_btn'];
		}

		// Check turn on buy now button
		if ( $wc_buynow_btn == 1 ) {
			global $woocommerce;

			// Checkout Current Product Only
			if ( $wc_buynow_checkout == 1 ) {
				// Delete all products in cart
				WC()->cart->empty_cart( true );
			}

			$product_id = (int) $_POST['product_id'];

			// Add to cart
			$data = self::add_to_cart_action( $product_id, false );

			// Show Modal Popup
			if ( $wc_buynow_payment_info == 1 ) {
				$data[ 'type' ] = 'modal';

			// Redirect To Checkout Page
			} else if ( $wc_buynow_payment_info == 2 ) {
				$data[ 'type' ] = 'redirect';
			}

			$data[ 'checkout_url' ] = $woocommerce->cart->get_checkout_url();

			wp_send_json ( $data );
		}

		wp_send_json ( array(
			'status' => 'false',
			'notice' => __( 'Not validate.', 'wr-nitro' )
		) );

		die();
	}

	/**
	 * Add to cart action.
	 *
	 * @param int $product_id
	 *
	 * @param bool $get_mini_cart
	 *
	 * @return array()
	 *
	 * @since  1.0
	 */
	private static function add_to_cart_action ( $product_id, $get_mini_cart = true ) {
		ob_start();

		$product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $product_id ) );
		$was_added_to_cart = false;
		$adding_to_cart    = wc_get_product( $product_id );

		if ( ! $adding_to_cart ) {
			return array(
				'status' => 'false',
				'notice' => __( 'Not validate.', 'wr-nitro' )
			);
		}

		$add_to_cart_handler = apply_filters( 'woocommerce_add_to_cart_handler', $adding_to_cart->product_type, $adding_to_cart );

		// Variable product handling
		if ( 'variable' === $add_to_cart_handler ) {
			$was_added_to_cart = self::add_to_cart_handler_variable ( $product_id );

		// Grouped Products
		} elseif ( 'grouped' === $add_to_cart_handler ) {
			$was_added_to_cart = self::add_to_cart_handler_grouped ( $product_id );

		// Simple Products
		} else {
			$was_added_to_cart = self::add_to_cart_handler_simple( $product_id );
		}

		// If we added the product to the cart we can now optionally do a redirect.
		if ( $was_added_to_cart && wc_notice_count( 'error' ) === 0 ) {

			// Fragments and mini cart are returned
			$notices_success = self::add_to_cart_message_product_detail( $product_id );

			$data = array(
				'status' 	=> 'true',
				'notice' 	=> $notices_success,
				'cart_hash' => apply_filters( 'woocommerce_add_to_cart_hash', WC()->cart->get_cart_for_session() ? md5( json_encode( WC()->cart->get_cart_for_session() ) ) : '', WC()->cart->get_cart_for_session() )
			);

			// Get mini cart
			if ( $get_mini_cart ) {
				ob_start();
				woocommerce_mini_cart();
				$mini_cart = ob_get_clean();

				$data[ 'fragments' ] = apply_filters( 'woocommerce_add_to_cart_fragments', array('div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>') );
			}

			wc_clear_notices();

			do_action( 'woocommerce_ajax_added_to_cart', $product_id );

			return $data;
		} else {
			$notices_error = wc_get_notices( 'error' );
			$data = array(
				'status' => 'false',
				'notice' => isset( $notices_error[0] ) ? $notices_error[0] : __( 'Not validate.', 'wr-nitro' ),
			);

			wc_clear_notices();

			return $data;
		}
	}

	/**
	 * Handle adding simple products to the cart.
	 *
	 * @param int $product_id
	 *
	 * @return bool
	 *
	 * @since  1.0
	 */
	private static function add_to_cart_handler_simple( $product_id ) {
		$quantity 			= empty( $_REQUEST['quantity'] ) ? 1 : wc_stock_amount( $_REQUEST['quantity'] );
		$passed_validation 	= apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );

		if ( $passed_validation && WC()->cart->add_to_cart( $product_id, $quantity ) !== false ) {
			wc_add_to_cart_message( $product_id );
			return true;
		}
		return false;
	}

	/**
	 * Handle adding variable products to the cart.
	 *
	 * @param int $product_id
	 *
	 * @return bool
	 *
	 * @since  1.0
	 */
	private static function add_to_cart_handler_variable( $product_id ) {
		$adding_to_cart     = wc_get_product( $product_id );
		$variation_id       = empty( $_REQUEST['variation_id'] ) ? '' : absint( $_REQUEST['variation_id'] );
		$quantity           = empty( $_REQUEST['quantity'] ) ? 1 : wc_stock_amount( $_REQUEST['quantity'] );
		$missing_attributes = array();
		$variations         = array();
		$attributes         = $adding_to_cart->get_attributes();
		$variation          = wc_get_product( $variation_id );

		// Verify all attributes
		foreach ( $attributes as $attribute ) {
			if ( ! $attribute['is_variation'] ) {
				continue;
			}

			$taxonomy = 'attribute_' . sanitize_title( $attribute['name'] );

			if ( isset( $_REQUEST[ $taxonomy ] ) ) {

				// Get value from post data
				if ( $attribute['is_taxonomy'] ) {
					// Don't use wc_clean as it destroys sanitized characters
					$value = sanitize_title( stripslashes( $_REQUEST[ $taxonomy ] ) );
				} else {
					$value = wc_clean( stripslashes( $_REQUEST[ $taxonomy ] ) );
				}

				// Get valid value from variation
				$valid_value = isset( $variation->variation_data[ $taxonomy ] ) ? $variation->variation_data[ $taxonomy ] : '';

				// Allow if valid
				if ( '' === $valid_value || $valid_value === $value ) {
					$variations[ $taxonomy ] = $value;
					continue;
				}

			} else {
				$missing_attributes[] = wc_attribute_label( $attribute['name'] );
			}
		}

		if ( $missing_attributes ) {
			wc_add_notice( sprintf( _n( '%s is a required field', '%s are required fields', sizeof( $missing_attributes ), 'wr-nitro' ), wc_format_list_of_items( $missing_attributes ) ), 'error' );
		} elseif ( empty( $variation_id ) ) {
			wc_add_notice( __( 'Please choose product options&hellip;', 'wr-nitro' ), 'error' );
		} else {
			// Add to cart validation
			$passed_validation 	= apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variations );

			if ( $passed_validation && WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variations ) !== false ) {
				wc_add_to_cart_message( $product_id );
				return true;
			}
		}
		return false;
	}

	/**
	 * Handle adding grouped products to the cart.
	 *
	 * @param int $product_id
	 *
	 * @return bool
	 *
	 * @since  1.0
	 */
	private static function add_to_cart_handler_grouped( $product_id ) {
		$was_added_to_cart = false;
		$added_to_cart     = array();

		if ( ! empty( $_REQUEST['quantity'] ) && is_array( $_REQUEST['quantity'] ) ) {
			$quantity_set = false;

			foreach ( $_REQUEST['quantity'] as $item => $quantity ) {
				if ( $quantity <= 0 ) {
					continue;
				}
				$quantity_set = true;

				// Add to cart validation
				$passed_validation 	= apply_filters( 'woocommerce_add_to_cart_validation', true, $item, $quantity );

				if ( $passed_validation && WC()->cart->add_to_cart( $item, $quantity ) !== false ) {
					$was_added_to_cart = true;
					$added_to_cart[]   = $item;
				}
			}

			if ( ! $was_added_to_cart && ! $quantity_set ) {
				wc_add_notice( __( 'Please choose the quantity of items you wish to add to your cart&hellip;', 'wr-nitro' ), 'error' );
			} elseif ( $was_added_to_cart ) {
				wc_add_to_cart_message( $added_to_cart );
				return true;
			}

		} elseif ( $product_id ) {
			/* Link on product archives */
			wc_add_notice( __( 'Please choose a product to add to your cart&hellip;', 'wr-nitro' ), 'error' );
		}
		return false;
	}

	/**
	 * Register additional sidebar for WooCommerce.
	 *
	 * @since  1.0
	 */
	public static function widgets_init() {
		register_sidebar(
			array(
				'name'          => __( 'WooCommerce Sidebar', 'wr-nitro' ),
				'id'            => 'wc-sidebar',
				'description'   => __( 'Widgets in this area will be shown on shop page.', 'wr-nitro' ),
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			)
		);
	}

	/**
	 * Customize product quick view.
	 *
	 * @since  1.0
	 */
	public static function wr_quickview() {
		// Get product from request.
		if ( isset( $_POST['product'] ) && (int) $_POST['product'] ) {
			global $post, $product, $woocommerce;

			$id      = ( int ) $_POST['product'];
			$post    = get_post( $id );
			$product = get_product( $id );

			if ( $product ) {
				// Get quickview template.
				wc_get_template( 'woorockets/product-quickview.php' );
			}
		}

		exit;
	}

	/**
	 * Customize product search form.
	 *
	 * @since  1.0
	 */
	public static function get_product_search_form( $form ) {
		$form = '
			<form class="widget-search" role="search" method="get" action="' . esc_url( home_url( '/'  ) ) . '">
				<input type="text" value="' . esc_attr( get_search_query() ) . '" name="s" placeholder="' . __( 'Поиск услуг...', 'wr-nitro' ) . '" />
				<button type="submit" class="search-submit"><i class="fa fa-search"></i></button>
				<input type="hidden" name="post_type" value="product" />
			</form>';

		return $form;
	}

//	add_shortcode( 'product_search', 'get_product_search_form' );
	/**
	 * Customize product tabs.
	 *
	 * @since  1.0
	 */
	public static function woocommerce_product_tabs( $tabs = array() ) {
		global $product, $post;

		$wr_nitro_options = WR_Nitro::get_options();

		// Get product setting
		$single_style    = $wr_nitro_options['wc_single_style'];
		$tab_description = $wr_nitro_options['wc_single_product_tab_description'];
		$tab_additional  = $wr_nitro_options['wc_single_product_tab_info'];
		$tab_review      = $wr_nitro_options['wc_single_product_tab_review'];

		// Show rating
		$show_rating = $wr_nitro_options['wc_general_rating'];

		// Description tab - shows product content
		if ( $post->post_content ) {
			$tabs['description'] = array(
				'title'    => __( 'Описание', 'wr-nitro' ),
				'priority' => 10,
				'callback' => 'woocommerce_product_description_tab',
			);
		}

		// Additional information tab - shows attributes
		if ( $product && ( $product->has_attributes() || ( $product->enable_dimensions_display() && ( $product->has_dimensions() || $product->has_weight() ) ) ) ) {
			$tabs['additional_information'] = array(
				'title'    => __( 'Additional Information', 'wr-nitro' ),
				'priority' => 20,
				'callback' => 'woocommerce_product_additional_information_tab',
			);
		}

		// Reviews tab - shows comments
		if ( comments_open() ) {
			$tabs['reviews'] = array(
				'title'    => sprintf( __( 'Отзывы (%d)', 'wr-nitro' ), $product->get_review_count() ),
				'priority' => 30,
				'callback' => 'comments_template',
			);
		}

		if ( '4' == $single_style ) {
			if ( $wr_nitro_options['wc_single_product_related'] ) {
				// Related products tab
				$tabs['related'] = array(
					'title'    => __( 'Related products', 'wr-nitro' ),
					'priority' => 40,
					'callback' => 'woocommerce_output_related_products',
				);
			}

			if ( $wr_nitro_options['wc_single_product_upsell'] ) {
				// Upsell products tab
				$tabs['upsell'] = array(
					'title'    => __( 'Upsell products', 'wr-nitro' ),
					'priority' => 50,
					'callback' => 'woocommerce_upsell_display',
				);
			}

			if ( $wr_nitro_options['wc_single_product_recent_viewed'] ) {
				// Recent viewed products tab
				$tabs['recent_viewed'] = array(
					'title'    => __( 'Recent viewed products', 'wr-nitro' ),
					'priority' => 60,
					'callback' => 'WR_Nitro_Pluggable_WooCommerce::woocommerce_recent_viewed_products',
				);
			}
		}

		// Enable VC page builder for single product
		$builder = get_post_meta( get_the_ID(), 'enable_builder', true );

		// Remove some default tabs
		if ( ! $tab_description || $builder ) {
			unset( $tabs['description'] );
		}
		if ( ! $tab_additional ) {
			unset( $tabs['additional_information'] );
		}
		if ( ! $tab_review || ! $show_rating ) {
			unset( $tabs['reviews'] );
		}

		return $tabs;
	}

	/**
	 * Add Recent viewed products
	 *
	 * @since  1.0
	 */
	public static function woocommerce_recent_viewed_products() {
		wc_get_template( 'single-product/recent-viewed.php' );
	}

	/**
	 * Customize social share in single product view.
	 *
	 * @since  1.0
	 */
	public static function woocommerce_share() {
		global $post;

		// Get theme options
		$wr_nitro_options = WR_Nitro::get_options();
		if ( ! $wr_nitro_options['wc_single_social_share'] ) return;

		// Get post thumbnail
		$src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ) );
		?>
		<div class="product-share">
			<span class="fwb dib mgb10"><?php esc_html_e( 'Поделиться этим', 'wr-nitro' ); ?></span>
			<ul class="social-share clear pd0">
				<li class="fl">
					<a class="db tc br-2 color-dark nitro-line" title="Facebook" href="http://www.facebook.com/sharer.php?u=<?php esc_url( the_permalink() ); ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
						<i class="fa fa-facebook"></i>
					</a>
				</li>
				<li class="fl">
					<a class="db tc br-2 color-dark nitro-line" title="Twitter" href="https://twitter.com/share?url=<?php esc_url( the_permalink() ); ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
						<i class="fa fa-twitter"></i>
					</a>
				</li>
				<li class="fl">
					<a class="db tc br-2 color-dark nitro-line" title="Googleplus" href="https://plus.google.com/share?url=<?php esc_url( the_permalink() ); ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
						<i class="fa fa-google-plus"></i>
					</a>
				</li>
				<li class="fl">
					<a class="db tc br-2 color-dark nitro-line" title="Pinterest" href="//pinterest.com/pin/create/button/?url=<?php esc_url( the_permalink() ); ?>&media=<?php echo esc_attr( $src[0] ); ?>&description=<?php the_title(); ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
						<i class="fa fa-pinterest"></i>
					</a>
				</li>
			</ul>
		</div>
	<?php
	}

	/**
	 * Custom button of variable product.
	 *
	 * @since  1.0
	 */
	public static function change_single_variation_add_to_cart_button() {
		global $product;
		?>
		<div class="variations_button">
		<?php
			woocommerce_quantity_input( array( 'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( $_POST['quantity'] ) : 1 ) );

			$wr_nitro_options   = WR_Nitro::get_options();

			// Icon Set
			$icons = $wr_nitro_options['wc_icon_set'];

			$add_to_cart_button = '<button type="submit" class="wr_single_add_to_cart_ajax variation single_add_to_cart_button wr_add_to_cart_button button alt btr-50 db pdl20 pdr20 fl mgl10 br-3"><i class="nitro-icon-' . esc_attr( $icons ) . '-cart mgr10"></i>' . esc_html( $product->single_add_to_cart_text() ) . '</button>';

			// Add to cart button
			if ( ( $wr_nitro_options['wc_buynow_btn'] && ! $wr_nitro_options['wc_disable_btn_atc'] ) || ! $wr_nitro_options['wc_buynow_btn'] ) {
				echo wp_kses_post( $add_to_cart_button );
			}

			// Quick buy button
			if ( $wr_nitro_options['wc_buynow_btn'] ) {
				echo '<button type="submit" class="variation single_buy_now wr_add_to_cart_button button alt btr-50 db pdl20 pdr20 bgd color-white fl mgl10 br-3"><i class="fa fa-cart-arrow-down mgr10"></i>' . __( 'Buy now', 'wr-nitro' ) . '</button>';
			}
		?>
			<input type="hidden" name="add-to-cart" value="<?php echo absint( $product->id ); ?>" />
			<input type="hidden" name="product_id" value="<?php echo absint( $product->id ); ?>" />
			<input type="hidden" name="variation_id" class="variation_id" value="" />
		</div>
		<?php
	}

	/**
	 * Customize WooCommerce image dimensions.
	 *
	 * @since  1.0
	 */
	public static function customize_image_dimensions() {
		global $pagenow;

		if ( $pagenow != 'themes.php' || ! isset( $_GET['activated'] ) ) {
			return;
		}

		// Update WooCommerce image dimensions.
		update_option(
			'shop_catalog_image_size',
			array( 'width' => '370', 'height' => '480', 'crop' => 1 )
		);

		update_option(
			'shop_single_image_size',
			array( 'width' => '420', 'height' => '521', 'crop' => 1 )
		);

		update_option(
			'shop_thumbnail_image_size',
			array( 'width' => '100', 'height' => '100', 'crop' => 1 )
		);
	}

	/**
	 * Add sidebar before archive product.
	 *
	 * @since  1.0
	 */
	public static function update_before_archive_product() {
		// Get theme options
		$wr_nitro_options = WR_Nitro::get_options();
		$sidebar = $wr_nitro_options['wc_archive_content_before'];

		if ( ! empty( $sidebar ) && is_active_sidebar( $sidebar ) ) {
			echo '<div class="widget-before-product-list">';
				dynamic_sidebar( $sidebar );
			echo '</div>';
		}
	}

	/**
	 * Add sidebar after archive product.
	 *
	 * @since  1.0
	 */
	public static function update_after_archive_product() {
		// Get theme options
		$wr_nitro_options = WR_Nitro::get_options();
		$sidebar = $wr_nitro_options['wc_archive_content_after'];

		if ( ! empty( $sidebar ) && is_active_sidebar( $sidebar ) ) {
			echo '<div class="widget-after-product-list">';
				dynamic_sidebar( $sidebar );
			echo '</div>';
		}
	}

	/**
	 * Add sidebar before single product.
	 *
	 * @since  1.0
	 */
	public static function update_before_single_product() {
		// Get theme options
		$wr_nitro_options = WR_Nitro::get_options();
		$sidebar = $wr_nitro_options['wc_single_content_before'];

		if ( ! empty( $sidebar ) && is_active_sidebar( $sidebar ) && $wr_nitro_options['wc_single_style'] != 2 ) {
			echo '<div class="widget-before-product-detail">';
				dynamic_sidebar( $sidebar );
			echo '</div>';
		}
	}

	/**
	 * Add sidebar after single product.
	 *
	 * @since  1.0
	 */
	public static function update_after_single_product() {
		// Get theme options
		$wr_nitro_options = WR_Nitro::get_options();
		$sidebar = $wr_nitro_options['wc_single_content_after'];

		if ( ! empty( $sidebar ) && is_active_sidebar( $sidebar ) ) {
			echo '<div class="widget-after-product-detail mgt30">';
				dynamic_sidebar( $sidebar );
			echo '</div>';
		}
	}

	/**
	 * Add sidebar before cart page.
	 *
	 * @since  1.0
	 */
	public static function update_before_cart_page() {
		// Get theme options
		$wr_nitro_options = WR_Nitro::get_options();
		$sidebar = $wr_nitro_options['wc_cart_content_before'];

		if ( ! empty( $sidebar ) && is_active_sidebar( $sidebar ) ) {
			echo '<div class="widget-before-product-list mgb30">';
				dynamic_sidebar( $sidebar );
			echo '</div>';
		}
	}

	/**
	 * Add sidebar after cart page.
	 *
	 * @since  1.0
	 */
	public static function update_after_cart_page() {
		// Get theme options
		$wr_nitro_options = WR_Nitro::get_options();
		$sidebar = $wr_nitro_options['wc_cart_content_after'];

		if ( ! empty( $sidebar ) && is_active_sidebar( $sidebar ) ) {
			echo '<div class="widget-after-product-list mgt30">';
				dynamic_sidebar( $sidebar );
			echo '</div>';
		}
	}

	/**
	 * Add sidebar after checkout page.
	 *
	 * @since  1.0
	 */
	public static function update_after_checkout_page() {
		// Get theme options
		$wr_nitro_options = WR_Nitro::get_options();
		$sidebar = $wr_nitro_options['wc_checkout_content_after'];

		if ( ! empty( $sidebar ) && is_active_sidebar( $sidebar ) ) {
			echo '<div class="widget-before-checkout mgt30">';
				dynamic_sidebar( $sidebar );
			echo '</div>';
		}
	}

	/**
	 * Floating add to cart button for single product.
	 * @since  1.0
	 * @param  String $product_id
	 * @return String
	 */
	public static function floating_add_to_cart( $product_id ) {
		global $product;

		$wr_nitro_options = WR_Nitro::get_options();

		// Icon Set
		$icons = $wr_nitro_options['wc_icon_set'];

		$add_to_cart = '<button type="submit" class="floating_button wr_add_to_cart_button button alt btr-50 db pdl20 pdr20 bgd color-white br-3"><i class="nitro-icon-' . esc_attr( $icons ) . '-cart mgr10"></i>' . $product->single_add_to_cart_text() .'</button>';

		return $add_to_cart;
	}

	/**
	 * Enqueue custom scripts and styles.
	 *
	 * @since  1.0
	 */
	public static function enqueue_scripts() {
		// Enqueue style for WooCommerce.
		wp_register_style( 'wr-nitro-woocommerce', get_template_directory_uri() . '/assets/woorockets/css/woocommerce.css' );

		// Remove unnecessary stylesheet.
		wp_dequeue_style( 'woocommerce_prettyPhoto_css' );
	}

	/**
	 * Customize check out template
	 *
	 * @since  1.0
	 */
	public static function customize_checkout_template() {
		ob_start();

		// Get checkout object
		$checkout = WC()->checkout();

		wc_get_template( 'checkout/checkout-simple.php', array( 'checkout' => $checkout ) );

	}

	/**
	 * Add custom fields to general settings
	 *
	 * @since  1.0
	 */
	public static function add_custom_general_fields() {

		global $woocommerce, $post;

		woocommerce_wp_checkbox(
			array(
				'id'            => '_show_countdown',
				'wrapper_class' => 'show_if_simple show_if_external show_if_sale_schedule',
				'label'         => __('Show Countdown Timer', 'wr-nitro' ),
			)
		);

	}

	/**
	 * Save custom fields to general settings
	 *
	 * @since  1.0
	 */
	public static function add_custom_general_fields_save( $post_id ){

		$show_countdown = isset( $_POST['_show_countdown'] ) ? 'yes' : 'no';
		update_post_meta( $post_id, '_show_countdown', $show_countdown );

	}

	/**
	 * Print Ajax URL for front-end.
	 *
	 * @since  1.0
	 */
	public static function wp_head() {
		echo '<scr' . 'ipt>'; ?>
			var WRAjaxURL 	    = '<?php echo esc_js( admin_url( 'admin-ajax.php' ) ); ?>';
			var WR_URL 	        = '<?php echo esc_js( site_url() ); ?>';
			var _nonce_wr_nitro = '<?php echo wp_create_nonce( 'bb_wr_nitro' ); ?>';
			var _WR_THEME_URL   = '<?php echo get_template_directory_uri(); ?>';
		<?php echo '</scr' . 'ipt>';
	}
}
