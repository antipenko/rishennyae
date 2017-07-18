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
 * Pluggable initialization class.
 *
 * @package  WR_Theme
 * @since    1.0
 */
class WR_Nitro_Widgets {
	/**
	 * Variable to hold the initialization state.
	 *
	 * @var  boolean
	 */
	protected static $initialized = false;

	/**
	 * Initialize widgets functions.
	 *
	 * @return  void
	 */
	public static function initialize() {
		register_widget( 'WR_Nitro_Widgets_Instagram' );
		register_widget( 'WR_Nitro_Widgets_Recent_Posts' );
		register_widget( 'WR_Nitro_Widgets_Recent_Comments' );
	}
}
