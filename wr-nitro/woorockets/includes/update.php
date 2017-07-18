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

/**
 * Hook into WordPress's automatic update.
 *
 * @package  WR_Theme
 * @since    1.0
 */
class WR_Nitro_Update {
	/**
	 * Define server to authorize Envato app.
	 *
	 * @var  string
	 */
	const ENVATO_APP_SERVER = 'http://www.woorockets.com/envato_api_authorization/';

	/**
	 * Define Envato App OAuth Client ID.
	 *
	 * @var  string
	 */
	const ENVATO_APP_CLIENT_ID = 'themeforest-purchase-verification-3g4ulwhy';

	/**
	 * Define Envato API server.
	 *
	 * @var  string
	 */
	const ENVATO_API_SERVER = 'https://api.envato.com/v3/market/';

	/**
	 * Define Nitro's item ID at ThemeForest.
	 *
	 * TODO: Replace Flatsome's item ID with the real Nitro's item ID.
	 *
	 * @var  int
	 */
	const NITRO_ITEM_ID = 15761106;

	/**
	 * Define Nitro's item name at ThemeForest.
	 *
	 * TODO: Replace Flatsome's item name with the real Nitro's item name.
	 *
	 * @var  string
	 */
	const NITRO_ITEM_NAME = 'Nitro - Universal WooCommerce Theme from ecommerce experts';

	/**
	 * Variable to hold the initialization state.
	 *
	 * @var  boolean
	 */
	protected static $initialized = false;

	/**
	 * Initialize pluggable functions.
	 *
	 * @return  void
	 */
	public static function initialize() {
		// Do nothing if pluggable functions already initialized.
		if ( self::$initialized ) {
			return;
		}

		// Register necessary actions / filters to initialize automatic update.
		add_action( 'init', array( __CLASS__, 'init' ) );
		add_action( 'admin_init', array( __CLASS__, 'admin_init' ) );

		if ( get_option( 'nitro_customer' ) ) {
			add_filter( 'pre_set_site_transient_update_themes', array( __CLASS__, 'check_update' ) );
		}

		// State that initialization completed.
		self::$initialized = true;
	}

	/**
	 * Handle data returned from Envato API Authorization.
	 *
	 * @return  void
	 */
	public static function init() {
		// Check if this a redirect back from Envato API Authorization?
		if (
			false !== strpos( $_SERVER['REQUEST_URI'], '?envato_api=1&' )
			||
			false !== strpos( $_SERVER['QUERY_STRING'], '?envato_api=1&' )
		) {
			// Store access token.
			if ( isset( $_GET['access_token'] ) ) {
				$expire = isset( $_GET['expires_in'] ) ? $_GET['expires_in'] : 3600;

				set_transient( 'envato_access_token', $_GET['access_token'], $expire );
			}

			// Store refresh token.
			if ( isset( $_GET['refresh_token'] ) ) {
				set_transient( 'envato_refresh_token', $_GET['refresh_token'] );
			}

			// Continue verifying purchase code.
			self::validate();

			exit;
		}
	}

	/**
	 * Add meta boxes to configure automatic update.
	 *
	 * @return  void
	 */
	public static function admin_init() {
		// Migrate old option key to new one.
		if ( ( $purchase_code = get_option( 'nitro_purchase_code' ) ) ) {
			update_option( 'nitro_customer', array(
				'purchase_code' => $purchase_code,
			) );

			delete_option( 'nitro_purchase_code' );
		}

		// Register setting to store ThemeForest purchase code.
		register_setting( 'nitro_automatic_update', 'nitro_customer', array( __CLASS__, 'validate' ) );
	}

	/**
	 * Validate purchase code.
	 *
	 * @param   array  $input  Input array.
	 *
	 * @return  array
	 */
	public static function validate( $input = null ) {
		// Prepare purchase code.
		$input = empty( $input ) ? get_transient( 'nitro_customer' ) : $input;

		if ( empty( $input ) || ! isset( $input['email'] ) || ! isset( $input['purchase_code'] ) ) {
			wp_redirect( add_query_arg(
				'error',
				urlencode( __( 'Please input both email and purchase code.', 'wr-nitro' ) ),
				admin_url( 'admin.php?page=wr-intro' )
			) . '#registration' );

			exit;
		}

		// Get access token.
		if ( $access_token = self::get_access_token() ) {
			// If purchase code is already validated, skip validation.
			$customer = get_option( 'nitro_customer' );

			if ( $customer && $input['purchase_code'] == $customer['purchase_code'] ) {
				if ( json_encode( $customer ) != json_encode( $input ) ) {
					remove_filter( 'sanitize_option_nitro_customer', array( __CLASS__, 'validate' ) );

					update_option( 'nitro_customer', $input );
				}

				wp_redirect( add_query_arg(
					'message',
					urlencode( __( 'Thank you for choosing Nitro!', 'wr-nitro' ) ),
					admin_url( 'admin.php?page=wr-intro' )
				) . '#registration' );

				exit;
			}

			// Look up purchase by code.
			$r = wp_remote_get(
				add_query_arg( 'code', $input['purchase_code'], self::ENVATO_API_SERVER . 'buyer/purchase' ),
				array(
					'headers' => "Authorization: Bearer {$access_token}",
				)
			);

			if ( is_wp_error( $r ) ) {
				wp_redirect( add_query_arg(
					'error',
					urlencode( $r->get_error_message() ),
					admin_url( 'admin.php?page=wr-intro' )
				) . '#registration' );

				exit;
			}

			if ( ! ( $r = json_decode( $r['body'], true ) ) ) {
				wp_redirect( add_query_arg(
					'error',
					urlencode( __( 'Failed to get response from Envato server.', 'wr-nitro' ) ),
					admin_url( 'admin.php?page=wr-intro' )
				) . '#registration' );

				exit;
			}

			if ( isset( $r['error'] ) ) {
				wp_redirect( add_query_arg(
					'error',
					urlencode( $r['description'] ),
					admin_url( 'admin.php?page=wr-intro' )
				) . '#registration' );

				exit;
			}

			// Check if this is a purchase for Nitro.
			$valid = ( self::NITRO_ITEM_ID && self::NITRO_ITEM_ID == $r['item']['id'] );

			if ( ! $valid ) {
				$valid = ( self::NITRO_ITEM_NAME && self::NITRO_ITEM_NAME == $r['item']['name'] );
			}

			if ( ! $valid ) {
				wp_redirect( add_query_arg(
					'error',
					urlencode( __( 'The purchase code you inputted is not related to Nitro.', 'wr-nitro' ) ),
					admin_url( 'admin.php?page=wr-intro' )
				) . '#registration' );

				exit;
			}

			// Save purchase code to options table.
			delete_transient( 'nitro_customer' );

			remove_filter( 'sanitize_option_nitro_customer', array( __CLASS__, 'validate' ) );

			update_option( 'nitro_customer', $input );

			wp_redirect( add_query_arg(
				'message',
				urlencode( __( 'Thank you for choosing Nitro!', 'wr-nitro' ) ),
				admin_url( 'admin.php?page=wr-intro' )
			) . '#registration' );

			exit;
		}

		// Request user to authorize our Envato app.
		else {
			// Store the purchase code to transient to verify later.
			set_transient( 'nitro_customer', $input );

			// Redirect user to Envato to authorize our app.
			wp_redirect( add_query_arg( $input, add_query_arg(
				'return',
				urlencode( site_url( 'index.php?envato_api=1' ) ),
				self::ENVATO_APP_SERVER
			) ) );

			exit;
		}
	}

	/**
	 * Method to check for new version of WR Nitro.
	 *
	 * @param   array  $value  The value of update_themes site transient.
	 *
	 * @return  array
	 */
	public static function check_update( $value ) {
		// Get access token to request Envato API.
		$access_token = self::get_access_token();

		// Get the latest version of Nitro from transient first.
		$latest = get_transient( 'nitro_latest_version' );
		$slug   = current( array_slice( explode( '/', str_replace( '\\', '/', get_template_directory() ) ), -1 ) );

		if ( ! $latest && $access_token ) {
			// Request Envato API for item details.
			$r = wp_remote_get(
				add_query_arg( 'id', self::NITRO_ITEM_ID, self::ENVATO_API_SERVER . 'catalog/item' ),
				array(
					'headers' => "Authorization: Bearer {$access_token}",
				)
			);

			if ( $r && ! is_wp_error( $r ) && $r = json_decode( $r['body'], true ) ) {
				$latest = $r['wordpress_theme_metadata']['version'];

				// Store the latest version of Nitro to transient.
				set_transient( 'nitro_latest_version', $latest, DAY_IN_SECONDS );
			}
		}

		// Check if update is available?
		if ( $latest ) {
			$theme = wp_get_theme();

			if ( version_compare( $theme['Version'], $latest, '<' ) ) {
				// Check if update data is defined.
				$def = ( isset( $value->response ) && isset( $value->response[ $slug ] ) );
				$ver = $def ? $value->response[ $slug ]['new_version'] : '0.0.0';

				if ( ( ! $def || version_compare( $ver, $latest, '<' ) ) && $access_token ) {
					// Get Nitro customer.
					$customer = get_option( 'nitro_customer' );

					if ( $customer && isset( $customer['purchase_code'] ) ) {
						// Request Envato API for download URL.
						$r = wp_remote_get(
							add_query_arg(
								array(
									'purchase_code' => $customer['purchase_code'],
									'shorten_url'   => 'false',
								),
								self::ENVATO_API_SERVER . 'buyer/download'
							),
							array(
								'headers' => "Authorization: Bearer {$access_token}",
							)
						);

						if ( $r && ! is_wp_error( $r ) && $r = json_decode( $r['body'], true ) ) {
							if ( isset( $r['wordpress_theme'] ) ) {
								// Get theme URL.
								if ( $theme->get( 'ThemeURI' ) ) {
									$url = $theme->get( 'ThemeURI' );
								} else {
									$url = $theme->get( 'AuthorURI' );
								}

								// Set update data.
								$value->response[ $slug ] = array(
									'theme'       => $slug,
									'new_version' => $latest,
									'url'         => $url,
									'package'     => $r['wordpress_theme'],
								);
							}
						}
					}
				}
			}
		}

		return $value;
	}

	/**
	 * Method to get access token to request Envato API.
	 *
	 * @return  string
	 */
	protected static function get_access_token() {
		// Get necessary tokens.
		$access_token  = get_transient( 'envato_access_token'  );
		$refresh_token = get_transient( 'envato_refresh_token' );

		if ( ! $access_token && $refresh_token ) {
			// Request new access token.
			$r = wp_remote_post(
				self::ENVATO_APP_SERVER,
				array(
					'body' => array( 'refresh_token' => $refresh_token ),
				)
			);

			if ( $r && ! is_wp_error( $r ) && $r = json_decode( $r['body'], true ) && isset( $r['access_token'] ) ) {
				// Update access token.
				$access_token = $r['access_token'];
				$expire       = isset( $r['expires_in'] ) ? $r['expires_in'] : 3600;

				set_transient( 'envato_access_token', $access_token, $expire );
			}
		}

		return $access_token;
	}
}
