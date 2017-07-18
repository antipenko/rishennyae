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
 * Mega menu.
 *
 * @package  WR_Theme
 * @since    1.0
 */
class WR_Nitro_Megamenu {
	/**
	 * Variable to hold the initialization state.
	 *
	 * @var  boolean
	 */
	protected static $initialized = false;

	/**
	 * Variable to hold the status of megamenu.
	 *
	 * @var  boolean
	 */
	public static $active = false;

	/**
	 * Variable to hold the status of Woocommerce.
	 *
	 * @var  boolean
	 */
	public static $active_woocommerce = false;

	/**
	 * Current Megamenu data.
	 *
	 * @var  array
	 */
	protected static $data = array();

	/**
	 * Method to get megamenu data.
	 *
	 * @return  array
	 */
	public static function data() {
		return self::$data;
	}

	/**
	 * Plug into WordPress.
	 *
	 * @return  void
	 */
	public static function initialize() {
		// Do nothing if pluggable functions already initialized.
		if ( self::$initialized ) {
			return;
		}

		if ( file_exists( ABSPATH . 'wp-content/plugins/woocommerce/woocommerce.php' ) && in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			self::$active_woocommerce = true;
		}

		// Check if admin or front-end page is requested.
		if ( is_admin() ) {

			// Save data megamenu
			add_action( 'wp_ajax_wr_save_megamenu', array( __CLASS__, 'ajax_save_megamenu' ) );

			// Search product
			add_action( 'wp_ajax_wrmm_products', array( __CLASS__, 'wrmm_products' ) );

			// Get list product
			add_action( 'wp_ajax_wrmm_get_products', array( __CLASS__, 'wrmm_get_products' ) );

			// Check if menu management page is requested.
			global $pagenow;

			if ( $pagenow == 'nav-menus.php' ) {

				// Unset menu in database
				add_action( 'admin_footer', array( __CLASS__, 'reset_data' ) );

				add_action( 'admin_footer', array( __CLASS__, 'template_megamenu' ) );

				add_action( 'admin_footer', array( __CLASS__, 'enqueue_admin_assets' ) );

			}
		} else {

			// Permission menu item
			add_filter( 'wp_get_nav_menu_items', array( __CLASS__, 'permission_menu_item' ), 10, 3 );

			self::frontend();
		}
		// State that initialization completed.
		self::$initialized = true;
	}

	/**
	 * Permission menu item
	 *
	 * @param array  $items An array of menu item post objects.
	 * @param object $menu  The menu object.
	 * @param array  $args  An array of arguments used to retrieve menu item objects.
	 *
	 * @return  void
	 */
	public static function permission_menu_item( $items, $menu, $args ) {

		if( ! $items ) return;

		$menu_id = $menu->term_id;

		$parent_hide = array();

		$menu_items_new = array();

		foreach( $items as $key => $item ) {
			$menu_items_new[ $item->ID ] = $item->menu_item_parent;

			// Level 1
			if( $item->menu_item_parent == 0 ) {
				$level         = 0;

			// Level 2
			} elseif ( isset( $menu_items_new[ $item->menu_item_parent ] ) && $menu_items_new[ $item->menu_item_parent ] == 0 ) {
				$level = 1;

			// Level > 2
			} else {
				$level = 2;
			}

			$data_menu_item          = isset( self::$data[ $menu->term_id ][ $item->ID ] ) ? self::$data[ $menu->term_id ][ $item->ID ] : array();
			$data_menu_item['level'] = $level;
			$data_menu_item          = WR_Nitro_Megamenu::merge_data_menu_item( $data_menu_item );

			$permission_show = true;
			if( isset( $data_menu_item['permission_show'] ) ) {
				switch ( $data_menu_item['permission_show'] ) {
					case 'everyone':
						$permission_show = true;

						break;
					case 'log-out':

						// Logged in
						if( is_user_logged_in() ) {
							$permission_show = false;

						// Logged out
						} else {
							$permission_show = true;
						}

						break;
					case 'log-in':

						global $current_user;

						// Logged in
						if( is_user_logged_in() && isset( $data_menu_item['permission_user'] ) && is_array( $data_menu_item['permission_user'] ) && ( array_intersect( $data_menu_item['permission_user'], $current_user->roles ) || in_array( 'all', $data_menu_item['permission_user'] ) ) ) {
							$permission_show = true;

						// Logged out
						} else {
							$permission_show = false;
						}

						break;
				}
			}

			if( in_array( $item->menu_item_parent, $parent_hide ) ){
				$permission_show = false;
				$parent_hide[] = $item->ID;
			}

			if ( ! $permission_show ) {
				$parent_hide[] = $item->ID;
				unset( $items[$key] ) ;
			}
		}

		return $items;
	}
	/**
	 * Set level product category by recursive
	 *
	 * @param object $term_item
	 * @param array $list_category_children_along
	 * @param object $all_terms
	 *
	 * @return  void
	 */
	public static function set_level_category_recursive( $term_item, &$list_category_children_along, $all_terms ) {
		foreach ( $all_terms as $key => $val ) {
			if ( $val->parent == $term_item->term_id ) {
				$val->level = $term_item->level + 1;
				$list_category_children_along[] = $val;

				// Call recursive.
				self::set_level_category_recursive( $val, $list_category_children_along, $all_terms );
			}
		}
	}

	/**
	 * Show list product category
	 *
	 * @param   int  $category_id Product category id
	 *
	 * @param   string|array  $size
	 *
	 * @return  array
	 */
	public static function get_image_term_product_category( $category_id, $size ) {
		/* Get thumnai term */
		$thumbnail_id = get_woocommerce_term_meta( $category_id, 'thumbnail_id', true );
		if ( $thumbnail_id ) {
			$image = wp_get_attachment_image_src( $thumbnail_id, $size );
			if( isset( $image[0] ) ) {
				$image = $image[0];
			} else {
				$image = wc_placeholder_img_src();
			}
		} else {
			$image = wc_placeholder_img_src();
		}

		// Prevent esc_url from breaking spaces in urls for image embeds
		// Ref: http://core.trac.wordpress.org/ticket/23605
		$image = str_replace( ' ', '%20', $image );

		return $image;
	}

	/**
	 * Show list product category
	 *
	 * @return  array
	 */
	public static function get_term_product_category() {
		$list_category                = get_terms( 'product_cat' );
		$list_category_children_along = array();
		$product_category             = array();

		if ( $list_category ) {
			foreach ( $list_category as $key => $val ) {
				if ( $val->parent == 0 ) {
					$val->level = 0;
					$list_category_children_along[] = $val;

					self::set_level_category_recursive( $val, $list_category_children_along, $list_category );
				}
			}
		}

		if( $list_category_children_along ) {
			foreach( $list_category_children_along as $val ) {
				$image = self::get_image_term_product_category( $val->term_id, array( 100, 100 ) );

				$product_category[$val->term_id] = array(
					'name'  => $val->name,
					'count' => $val->count,
					'image' => esc_url( $image ),
					'level' => $val->level
				);
			}
		}

		return $product_category;
	}

	/**
	 * Print template for setting meganu
	 *
	 * @return string
	*/
	public static function template_megamenu() {
		global $wp_roles;
?>

	<?php echo '<scr' . 'ipt type="text/html" id="wrmm-modal-html">'; ?>
		<div class="wrmm-modal">
			<div class="wrmm-theme-overlay"></div>
			<div class="wrmm-dialog"></div>
		</div>
	<?php echo '</scr' . 'ipt>'; ?>

	<?php echo '<scr' . 'ipt type="text/html" id="wrmm-all-element">'; ?>
		<div class="dialog-title"><span class="title"><?php esc_html_e( 'Select element', 'wr-nitro' ); ?></span><span class="close dashicons dashicons-no-alt"></span></div>
		<div class="wrmm-list-element">
			<div data-value="text" class="text-el item"> <i class="dashicons dashicons-editor-paste-word"></i> <strong><?php esc_html_e( 'Text', 'wr-nitro' ); ?></strong> </div>
			<div data-value="widget" class="widget-el item"> <i class="dashicons dashicons-wordpress"></i> <strong><?php esc_html_e( 'Sidebar', 'wr-nitro' ); ?></strong> </div>

			<?php if( self::$active_woocommerce ) { ?>
				<div data-value="products" class="products-el item"> <i class="dashicons dashicons-cart"></i> <strong><?php esc_html_e( 'Product', 'wr-nitro' ); ?></strong> </div>
				<div data-value="categories" class="categories-el item"> <i class="dashicons dashicons-cart"></i> <strong><?php esc_html_e( 'Product categories', 'wr-nitro' ); ?></strong> </div>
			<?php } ?>

		</div>
	<?php echo '</scr' . 'ipt>'; ?>
	<?php echo '<scr' . 'ipt type="text/html" id="wrmm-select-icon">'; ?>
		<div class="dialog-title"><span class="title"><?php esc_html_e( 'Select icon', 'wr-nitro' ); ?></span><span class="close dashicons dashicons-no-alt"></span></div>
		<div class="wrmm-list-icon">
			<input type="search" class="search" placeholder="<?php esc_html_e( 'Search icon', 'wr-nitro' ); ?>..." />

			<div class="list-icon">
				<ul>
					<%
						for( var key in list_icon ) {
						   print( '<li ' + ( ( icon_active == key ) ? 'class="active"' : '' ) + ' data-value="' + list_icon[key] + '"><i class="' + key + '"></i></li>' )
						}
					%>
				</ul>
			</div>
		</div>
	<?php echo '</scr' . 'ipt>'; ?>

	<?php echo '<scr' . 'ipt type="text/html" id="wrmm-text-element">'; ?>
		<div class="wrmm-text-element">
			<div class="editor-wrapper">
				<?php
					echo wp_editor( '_WR_CONTENT_', 'wrmm-editor', array(
							'editor_class'  => 'wrmm-editor',
							'editor_height' => 200,
							'tinymce'       => array(
								'setup' => "function( editor ) {
									editor.on('change', function(e) {
										var content    = editor.getContent();
										var input_hide = jQuery( editor.targetElm ).closest( '.editor-wrapper' ).find( '.wrmm-editor-hidden' );
										input_hide.val( content ).trigger('change');
									} );
								}"
							),
						)
					);
				 ?>
				 <input type="hidden" class="wrmm-editor-hidden" value="">
			</div>
		</div>
	<?php echo '</scr' . 'ipt>'; ?>

	<?php echo '<scr' . 'ipt type="text/html" id="wrmm-widget-element">'; ?>
		<div class="wrmm-widget-element wr-row">
			<select class="wrmm-list-widget col-2">
				<% $.each( sidebars_area , function ( value_widget, key_widget ) { %>
					<option <% if( value == value_widget ) print("selected=\'selected\'") %> value="<%= value_widget %>"><%= key_widget %></option>
				<% } ) %>
			</select>
		</div>
	<?php echo '</scr' . 'ipt>'; ?>

	<?php if( self::$active_woocommerce ) { ?>
		<?php echo '<scr' . 'ipt type="text/html" id="wrmm-products-element">'; ?>
			<div class="wrmm-products-element wr-row">
				<div class="col-2-3">
					<div class="search-product row-text mgt-10">
						<div class="search-ajax"><input placeholder="<?php esc_html_e( 'Search product', 'wr-nitro' ); ?>" onkeypress="return event.keyCode != 13;" class="product-ajax" type="text"></div>
					</div>
					<div class="products-added">
						<div class="list-products">
							<% $.each( list_product , function ( key, val ) { %>
								<div class="item" data-id="<%= val["id"] %>">
									<div class="img"><%= val["image"] %></div>
									<div class="title-price"><div class="name-product"><%= val["title"] %></div><div class="price"><%= val["price"] %></div></div>
									<i class="del-product dashicons dashicons-no-alt"></i>
								</div>
							<% } ) %>
						</div>
					</div>
				</div>
			</div>
		<?php echo '</scr' . 'ipt>'; ?>

		<?php echo '<scr' . 'ipt type="text/html" id="wrmm-category-element">'; ?>
			<div class="wrmm-category-element wr-row">
				<div class="col-2-3">
					<div class="search-categories row-text mgt-10">
						<div class="search-ajax">
							<input placeholder="<?php esc_html_e( 'Поиск категорий услуг', 'wr-nitro' ); ?>" onkeypress="return event.keyCode != 13;" class="categories-ajax" type="text">
							<div class="list-categories">
								<?php
									$list_category = self::get_term_product_category();

									if( $list_category ) {
										foreach( $list_category as $key => $val ) {
											echo '
												<div class="item-categories" data-search="' . esc_attr( $val['name'] ) . '" data-id="' . $key . '">
													' . ( $val['level'] > 0 ? '<div class="level-symbol">' . str_repeat( '&#8211;&#124', $val['level'] ) . '</div>' : NULL ) . '
													<img class="img" src="' . $val['image'] . '" height="40" width="40" />
													<div class="info-categories">
														<div class="name">' . esc_attr( $val['name'] ) . '</div>
														<div class="count">' . (int) $val['count'] . ' ' . ( $val['count'] == 1 ? esc_html__( 'item', 'wr-nitro' ) : esc_html__( 'items', 'wr-nitro' ) )  . '</div>
													</div>
												</div>
											';
										}
									}
								?>
							</div>
						</div>
					</div>
					<div class="category-added">
						<%
							if( list_categories ) {
								list_categories = list_categories.split( ',' );

								$.each( all_categories , function ( key, val ) {
									if( list_categories.indexOf( key ) != -1 ) {
						%>
										<div class="item-categories" data-search="<%= val["name"] %>" data-id="<%= key %>">
											<img class="img" src="<%= val["image"] %>" height="40" width="40" />
											<div class="info-categories">
												<div class="name"><%= val["name"] %></div>
												<div class="count"><%= val["count"] %> <% ( val["count"] > 1 ) ? print( 'items' ) : print( 'item' ); %></div>
											</div>
											<i class="del-category dashicons dashicons-no-alt"></i>
										</div>
						<%
									}
								})
							}
					 	%>
					</div>
				</div>
			</div>
		<?php echo '</scr' . 'ipt>'; ?>
	<?php } ?>

	<?php echo '<scr' . 'ipt type="text/html" id="wrmm-template">'; ?>
		<%
			var hide_general_tab = ( level > 1 || ( level == 1 && active_parent != 1 ) ) ? true : false;
		%>
		<div class="dialog-title"><span class="title"><% print( title_modal ); %></span><span class="close dashicons dashicons-no-alt"></span></div>
		<div class="wrmm-wrapper" data-id="<% print( id ); %>">
			<ul class="nav-settings">
				<li <% if( hide_general_tab ) print( "style=\'display: none;\'" ); %> data-nav="general" class="active"><?php esc_html_e( 'General', 'wr-nitro' ) ?></li>
				<li data-nav="icon" <% if( hide_general_tab ) print( "class=\'active\'" ) %>><?php esc_html_e( 'Icon', 'wr-nitro' ) ?></li>
				<li data-nav="permission" <% if( ( level != 0 && permission_parent['permission_show'] == 'log-out' ) || ( permission_parent['permission_show'] == 'log-in' && permission_parent['permission_user'].length == 0 ) ) print( "style=\'display:none\'" ); %>><?php esc_html_e( 'Permission', 'wr-nitro' ) ?></li>
			</ul>
			<div class="option-settings">
				<div <% if( hide_general_tab ) print( "style=\'display: none;\'" ); %> data-option="general" class="item-option active">
					<div class="mm-option mgt-10">
						<% if( level < 2 ){ %>

							<% if ( level == 0 ) { %>
								<div class="on-off-mm <% if( ! has_children ) print( 'dis-enable' ); %>">
									<% if( ! has_children ) { %>
										<div class="des-dis"><?php esc_html_e( 'This parameter is disabled because this menu item has no children.', 'wr-nitro' ) ?></div>
									<% } %>
									<label class="check-style">
										<input <% if( active_parent == 1 ) print("checked=\'checked\'"); %> class="chb-of chb-enable" type="checkbox" />
										<span class="label"><?php esc_html_e( 'Enable MegaMenu', 'wr-nitro' ); ?></span>
									</label>
								</div>
							<% } %>

							<div class="wrapper-option mgt-10" <% if( active_parent != 1 ) print( "style=\'display:none;\'" ); %>>
								<% if ( level == 0 ) { %>
									<div class="width mgb-20">
										<span class="title"><?php esc_html_e( 'Width', 'wr-nitro' ); ?></span>
										<div class="width-inner wr-row">
											<select class="select-width col-2">
												<option <% if( data_item.width_type == "full" ) print("selected=\'selected\'"); %> value="full"><?php esc_html_e( 'Full container', 'wr-nitro' ); ?></option>
												<option <% if( data_item.width_type == "full-width" ) print("selected=\'selected\'"); %> value="full-width"><?php esc_html_e( 'Full page', 'wr-nitro' ); ?></option>
												<option <% if( data_item.width_type == "fixed" ) print("selected=\'selected\'"); %> value="fixed"><?php esc_html_e( 'Custom', 'wr-nitro' ); ?></option>
											</select>
											<div class="number-width-box  col-2" <% if( data_item.width_type != "fixed" ) print("style=\'display: none;\'"); %> >
												<input type="number" value="<%= data_item.width %>" class="number-width" />
												<span class="value-width">px</span>
											</div>
										</div>
									</div>
									<div class="wr-row mgb-20">
										<% if ( item_lv_2 <= 6 ) { %>
											<div class="row-layout col-2">
												<span class="title"><?php esc_html_e( 'Layout', 'wr-nitro' ); ?></span>
												<ul class="list-layout <%= "column-show-" + item_lv_2 %>">
													<%
														$.each( list_row, function( key, val ) {
															var class_name   = val.replace( /\//g, '' ).replace( / \+ /g, '-' );
															var class_column = (val.match(/\+/g) || []).length + 1;
													%>
															<li class="column-<%= class_column %> layout-<%= class_name %> <% if( data_item.row_layout == val ) print( "active" ) %>" title="<%= val %>"></li>
													<%
														} )
													%>
												</ul>
											</div>
										<% } %>
										<div class="row-text col-2">
											<span class="title"><?php esc_html_e( 'Custom format (x/z + y/z + ...)', 'wr-nitro' ); ?></span>
											<input class="row-txt" class="" value="<%= data_item.row_layout %>" type="text" />
										</div>
									</div>
									<div class="background wr-row">
										<div class="col-2-3 background-image">
											<span class="title"><?php esc_html_e( 'Background image', 'wr-nitro' ); ?></span>
											<div class="wr-uploader-media">
												<input class="wr-image-link" value="<%= data_item['background_image'] %>" type="text" />
												<span class="wr-image-button" >...</span> <i class="wr-image-remove dashicons dashicons-no-alt"></i>
											</div>
										</div>
										<div class="col-3 background-color">
											<span class="title"><?php esc_html_e( 'Background color', 'wr-nitro' ); ?></span>
											<div class="select-color">
												<input type="text" class="txt-colorpicker txt-select-color" value="<%= data_item.background_color %>" />
												<span class="show-color"><%= data_item.background_color %></span>
											</div>
										</div>
									</div>
									<div class="background-option wr-row mgt-10" <% if( data_item['background_image'] == undefined || data_item['background_image'] == '' ) print( "style=\"display: none\"" ); %>>
										<div class="col-3">
											<span class="title"><?php esc_html_e( 'Background size', 'wr-nitro' ); ?></span>
											<select class="background-size">
												<option <% if( typeof data_item.background_size != 'undefined' && data_item.background_size == "inherit" ) print("selected=\'selected\'"); %> value="inherit"><?php esc_html_e( 'Inherit', 'wr-nitro' ); ?></option>
												<option <% if( typeof data_item.background_size != 'undefined' && data_item.background_size == "contain" ) print("selected=\'selected\'"); %>  value="contain"><?php esc_html_e( 'Contain', 'wr-nitro' ); ?></option>
												<option <% if( typeof data_item.background_size != 'undefined' && data_item.background_size == "cover" ) print("selected=\'selected\'"); %>  value="cover"><?php esc_html_e( 'Cover', 'wr-nitro' ); ?></option>
											</select>
										</div>
										<div class="col-3">
											<span class="title"><?php esc_html_e( 'Background position', 'wr-nitro' ); ?></span>
											<select class="background-position">
												<option <% if( typeof data_item.background_position != 'undefined' && data_item.background_position == "left top" ) print("selected=\'selected\'"); %> value="left top"><?php esc_html_e( 'Left - Top', 'wr-nitro' ); ?></option>
												<option <% if( typeof data_item.background_position != 'undefined' && data_item.background_position == "left center" ) print("selected=\'selected\'"); %> value="left center"><?php esc_html_e( 'Left - Center', 'wr-nitro' ); ?></option>
												<option <% if( typeof data_item.background_position != 'undefined' && data_item.background_position == "left bottom" ) print("selected=\'selected\'"); %> value="left bottom"><?php esc_html_e( 'Left - Bottom', 'wr-nitro' ); ?></option>
												<option <% if( typeof data_item.background_position != 'undefined' && data_item.background_position == "right top" ) print("selected=\'selected\'"); %> value="right top"><?php esc_html_e( 'Right - Top', 'wr-nitro' ); ?></option>
												<option <% if( typeof data_item.background_position != 'undefined' && data_item.background_position == "right center" ) print("selected=\'selected\'"); %> value="right center"><?php esc_html_e( 'Right - Center', 'wr-nitro' ); ?></option>
												<option <% if( typeof data_item.background_position != 'undefined' && data_item.background_position == "right bottom" ) print("selected=\'selected\'"); %> value="right bottom"><?php esc_html_e( 'Right - Bottom', 'wr-nitro' ); ?></option>
												<option <% if( typeof data_item.background_position != 'undefined' && data_item.background_position == "center top" ) print("selected=\'selected\'"); %> value="center top"><?php esc_html_e( 'Center - Top', 'wr-nitro' ); ?></option>
												<option <% if( typeof data_item.background_position != 'undefined' && data_item.background_position == "center center" ) print("selected=\'selected\'"); %> value="center center"><?php esc_html_e( 'Center - Center', 'wr-nitro' ); ?></option>
												<option <% if( typeof data_item.background_position != 'undefined' && data_item.background_position == "center bottom" ) print("selected=\'selected\'"); %> value="center bottom"><?php esc_html_e( 'Center - Bottom', 'wr-nitro' ); ?></option>
											</select>
										</div>
										<div class="col-3">
											<span class="title"><?php esc_html_e( 'Background repeat', 'wr-nitro' ); ?></span>
											<select class="background-repeat">
												<option <% if( typeof data_item.background_repeat != 'undefined' && data_item.background_repeat == "no-repeat" ) print("selected=\'selected\'"); %> value="no-repeat"><?php esc_html_e( 'No-repeat', 'wr-nitro' ); ?></option>
												<option <% if( typeof data_item.background_repeat != 'undefined' &&  data_item.background_repeat == "repeat" ) print("selected=\'selected\'"); %> value="repeat"><?php esc_html_e( 'Repeat', 'wr-nitro' ); ?></option>
												<option <% if( typeof data_item.background_repeat != 'undefined' &&  data_item.background_repeat == "repeat-x" ) print("selected=\'selected\'"); %> value="repeat-x"><?php esc_html_e( 'Repeat X', 'wr-nitro' ); ?></option>
												<option <% if( typeof data_item.background_repeat != 'undefined' &&  data_item.background_repeat == "repeat-y" ) print("selected=\'selected\'"); %> value="repeat-y"><?php esc_html_e( 'Repeat Y', 'wr-nitro' ); ?></option>
											</select>
										</div>
									</div>
								<% } %>
								<% if ( level == 1 ) { %>
									<div class="on-off-mm show-hr">
										<label class="check-style">
											<input <% if( parseInt( data_item.disable_title ) == 1 ) print( "checked=\'checked\'" ); %> class="chb-of checkbox-column" type="checkbox" />
											<span class="label"><?php esc_html_e( 'Disable column title', 'wr-nitro' ); ?></span>
										</label>
									</div>

									<div class="select-element">
										<div class="action <% if( data_item.element_type == '' ) print( 'not-element' ); %>">
											<div class="add-element button button-primary">
												<span><?php esc_html_e( 'Add Element', 'wr-nitro' ) ?></span>
											</div>

											<div class="added-element">
												<span><%
													switch( data_item.element_type ){
														case 'element-text':

															print( "<?php esc_html_e( 'Text element', 'wr-nitro' ) ?>" );

															break;

													<?php if( self::$active_woocommerce ) { ?>
														case 'element-products':

															print( "<?php esc_html_e( 'Products element', 'wr-nitro' ) ?>" );

															break;
														case 'element-categories':

															print( "<?php esc_html_e( 'Product categories element', 'wr-nitro' ) ?>" );

															break;
													<?php } ?>

														case 'element-widget':

															print( "<?php esc_html_e( 'Widget element', 'wr-nitro' ) ?>" );

															break;
													}
												 %></span>
												<i class="delete dashicons dashicons-no"></i>
											</div>
										</div>
										<div class="content-element"></div>
									</div>

								<% } %>
							</div>
						<% } %>
					</div>
				</div>
				<div data-option="icon" class="item-option <% if( hide_general_tab  ) print( 'active' ) %>">
					<div class="menu-icon">
						<div class="delete-position wr-row <% if( ! data_item.icon ) print('data-empty'); %>">
							<div class="item get-delete col-3">
								<div class="title-icon"><?php esc_html_e( 'Selected', 'wr-nitro' ) ?></div>
								<div class="item-settings">

									<div class="add-icon button button-primary">
										<span><?php esc_html_e( 'Select icon', 'wr-nitro' ) ?></span>
									</div>

									<div class="added-icon">
										<div class="get"><% if( data_item.icon ) { print( '<i class="' + data_item.icon + '"></i>' ); } %></div>
										<i class="delete dashicons dashicons-no"></i>
									</div>

								</div>
							</div>
							<% if ( level == 0 ) { %>
								<div class="item position col-2-3">
									<div class="title-icon"><?php esc_html_e( 'Position', 'wr-nitro' ); ?></div>
									<div class="item-settings">
										<ul>
											<li title="<?php esc_attr_e( 'Left', 'wr-nitro' ); ?>" data-value="left" class="left <% if( data_item.icon_position == "left" || data_item.icon_position == '' || data_item.icon_position == undefined ) print("active"); %>"></li>
											<li title="<?php esc_attr_e( 'Right', 'wr-nitro' ); ?>" data-value="right" class="right <% if( data_item.icon_position == "right" ) print("active"); %>"></li>
											<li title="<?php esc_attr_e( 'Top - Left', 'wr-nitro' ); ?>" data-value="top-left" class="top-left <% if( data_item.icon_position == "top-left" ) print("active"); %>"></li>
											<li title="<?php esc_attr_e( 'Top - Center', 'wr-nitro' ); ?>" data-value="top-center" class="top-center <% if( data_item.icon_position == "top-center" ) print("active"); %>"></li>
											<li title="<?php esc_attr_e( 'Top - Right', 'wr-nitro' ); ?>" data-value="top-right" class="top-right <% if( data_item.icon_position == "top-right" ) print("active"); %>"></li>
											<li title="<?php esc_attr_e( 'Bottom - Left', 'wr-nitro' ); ?>" data-value="bottom-left" class="bottom-left <% if( data_item.icon_position == "bottom-left" ) print("active"); %>"></li>
											<li title="<?php esc_attr_e( 'Bottom - Center', 'wr-nitro' ); ?>" data-value="bottom-center" class="bottom-center <% if( data_item.icon_position == "bottom-center" ) print("active"); %>"></li>
											<li title="<?php esc_attr_e( 'Bottom - Right', 'wr-nitro' ); ?>" data-value="bottom-right" class="bottom-right <% if( data_item.icon_position == "bottom-right" ) print("active"); %>"></li>
										</ul>
									</div>
								</div>
							<% } %>
						</div>


					</div>
				</div>
				<div data-option="permission" class="item-option" <% if( ( level != 0 && permission_parent['permission_show'] == 'log-out' ) || ( permission_parent['permission_show'] == 'log-in' && permission_parent['permission_user'].length == 0 ) ) print( "style=\'display:none\'" ); %>>
					<div class="permission">
						<div class="row-layout" <% if( ! ( level == 0 || ( level != 0 && permission_parent['permission_show'] != 'log-out' ) ) ) print( "style=\'display:none\'" ); %>>
							<span class="title"><?php esc_html_e( 'Show to:', 'wr-nitro' ); ?></span>
							<div class="list-show list-check">
								<label class="check-style everyone-pm" <% if( ! ( level == 0 || permission_parent['permission_show'] == undefined || ( level != 0 && ( permission_parent['permission_show'] == 'everyone' ) ) ) ) print( "style=\'display:none\'" ); %>>
									<input <% if( data_item['permission_show'] == 'everyone' ) print( "checked=\'checked\'" ); %> class="chb-of" type="radio" name="wrmm-pms" value="everyone" />
									<span class="label"><?php esc_html_e( 'Everyone', 'wr-nitro' ); ?></span>
								</label>
								<label class="check-style log-out-pm" <% if( ! ( level == 0 || permission_parent['permission_show'] == undefined || ( level != 0 && ( permission_parent['permission_show'] == 'everyone' || permission_parent['permission_show'] == 'log-out' ) ) ) ) print( "style=\'display:none\'" ); %>>
									<input <% if( data_item['permission_show'] == 'log-out' ) print( "checked=\'checked\'" ); %> class="chb-of" type="radio" name="wrmm-pms" value="log-out" />
									<span class="label"><?php esc_html_e( 'Logged out users', 'wr-nitro' ); ?></span>
								</label>
								<label class="check-style log-in-pm" <% if( ! ( level == 0 || permission_parent['permission_show'] == undefined || ( level != 0 && ( permission_parent['permission_show'] == 'everyone' || permission_parent['permission_show'] == 'log-in' ) ) ) ) print( "style=\'display:none\'" ); %>>
									<input <% if( data_item['permission_show'] == 'log-in' ) print( "checked=\'checked\'" ); %> class="chb-of" type="radio" name="wrmm-pms" value="log-in" />
									<span class="label"><?php esc_html_e( 'Logged in users', 'wr-nitro' ); ?></span>
								</label>
							</div>
						</div>
					<% if( level == 0 || permission_parent['permission_show'] == undefined || ( level != 0 && ( permission_parent['permission_show'] == 'everyone' || permission_parent['permission_show'] == 'log-in' ) ) ) { %>
						<div class="row-layout type-member-row" <% if( data_item['permission_show'] != 'log-in' ) print( "style=\'display:none\'" ); %>>
							<span class="title"><?php esc_html_e( 'Choose type of member that can see:', 'wr-nitro' ); ?></span>
							<div class="type-member list-check">

								<label class="check-style" <% var parent_all_user = false; if( level != 0 && typeof permission_parent['permission_user'] == 'object' && permission_parent['permission_user'].indexOf( "all" ) == -1 ) { print( "style=\'display:none\'" ) } else if( typeof permission_parent['permission_user'] == 'object' && permission_parent['permission_user'].indexOf( "all" ) != -1 ) { parent_all_user = true; }; %>>
									<input <% var all_user = false; if( typeof data_item['permission_user'] == 'object' && data_item['permission_user'].indexOf( "all" ) != -1 ) { print( "checked=\'checked\'" ); var all_user = true; }; %> class="chb-of" type="checkbox" value="all" />
									<span class="label"><?php esc_html_e( 'All type', 'wr-nitro' ) ?></span>
								</label>

								<?php
									if( isset( $wp_roles->role_names ) ) {
										foreach( $wp_roles->role_names as $key => $val ) {
								?>
											<label class="check-style" <% if( level != 0 && typeof permission_parent['permission_user'] == 'object' && permission_parent['permission_user'].indexOf( "<?php echo esc_attr( $key ); ?>" ) == -1 && ! parent_all_user && permission_parent['permission_show'] != 'everyone' ) { print( "style=\'display:none\'" ) }; %>>
												<input <% if( ( typeof data_item['permission_user'] == 'object' && data_item['permission_user'].indexOf( "<?php echo esc_attr( $key ); ?>" ) != -1 ) || all_user ) print( "checked=\'checked\'" ); %> class="chb-of" type="checkbox" value="<?php echo esc_attr( $key ); ?>" />
												<span class="label"><?php echo esc_attr( $val ); ?></span>
											</label>
								<?php
										}
									}
								?>
							</div>
						</div>
					<% } %>
					</div>
				</div>
			</div>
		</div>
	<?php echo '</scr' . 'ipt>'; ?>

<?php
	}

	/**
	 * Search products
	 *
	 * @return json
	*/
	public static function wrmm_get_products() {
		// Check nonce
		if ( !isset( $_POST['_nonce'] ) || !wp_verify_nonce( $_POST['_nonce'], 'wr_theme_megamenu_nonce_check' ) ){
			exit( json_encode( array( 'message' => 'The nonce check wrong.' ) ) );
		}

		// Check isset data
		if( ! isset( $_POST['list_id'] ) ){
			exit( json_encode( array( 'message' => __( 'Data not isset.', 'wr-nitro' ) ) ) );
		}

		$list_id = explode( ',' ,  $_POST['list_id'] );

		$list_product = new WP_Query( array(
			'post_type' => 'product',
			'post__in'  => $list_id,
			'orderby'   => 'title',
			'order'     => 'DESC',
		));

		if( $list_product->post_count ) {
			foreach( $list_product->posts as $val ){
				$product = wc_get_product( $val->ID );
				$data_return[] = array(
					'id' 	=> $val->ID,
					'title' => $product->get_title(),
					'image' => $product->get_image( array( 50, 50) ),
					'price' => $product->get_price_html(),
				);
			}
			exit( json_encode( $data_return ) );
		}

		exit( json_encode( array( 'message' => __( 'Product empty.', 'wr-nitro' ) ) ) );

		die;
	}

	/**
	 * Search products
	 *
	 * @return json
	*/
	public static function wrmm_products() {
		// Check nonce
		if ( !isset( $_POST['_nonce'] ) || !wp_verify_nonce( $_POST['_nonce'], 'wr_theme_megamenu_nonce_check' ) ){
			exit( json_encode( array( 'message' => 'The nonce check wrong.' ) ) );
		}

		// Check isset data
		if( !isset( $_POST['keyword'] ) ){
			exit( json_encode( array( 'message' => __( 'Data not isset.', 'wr-nitro' ) ) ) );
		}

		$keyword = $_POST['keyword'];

		$products = new WP_Query( array(
			'post_type' => 'product',
			'post_status' => 'publish',
			's' => $keyword,
			'orderby' => 'post_title',
			'order' => 'DESC',
			'posts_per_page' => 5,
		) );

		$data_return = array();

		if ( $products->have_posts() ) {

			foreach( $products->posts as $product ) {
				$product = wc_get_product( $product );

				$data_return['list_product'][] = array(
					'id' 	=> $product->id,
					'title' => $product->get_title(),
					'image' => $product->get_image( array( 50, 50) ),
					'price' => $product->get_price_html(),
				);
			}

			exit( json_encode( $data_return ) );
		}

		exit( json_encode( array( 'message' => __( 'No results.', 'wr-nitro' ) ) ) );

		die;
	}

	/**
	 * Enqueue required admin assets for mega menu.
	 *
	 * @return  void
	 */
	public static function enqueue_admin_assets() {
		// Enqueue WordPress's media library.
		wp_enqueue_media();

		// Enqueue jQuery UI.
		wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_style( 'wp-jquery-ui-dialog' );

		// Enqueue font Awesome.
		wp_enqueue_style( 'font-awesome', get_template_directory_uri() . '/assets/3rd-party/font-awesome/css/font-awesome.min.css' );

		// Enqueue style for Mega Menu.
		wp_enqueue_style( 'wr-mega-menu', get_template_directory_uri() . '/assets/woorockets/css/admin/megamenu.css' );

		// Enqueue spectrum color.
		wp_enqueue_style( 'spectrum-css' , get_template_directory_uri() . '/assets/3rd-party/spectrum/spectrum.css');
		wp_enqueue_script( 'spectrum-color' , get_template_directory_uri() . '/assets/3rd-party/spectrum/spectrum.js', array(), false, true );

		// Enqueue script for Mega Menu.
		wp_enqueue_script( 'wr-mega-menu', get_template_directory_uri() . '/assets/woorockets/js/admin/megamenu/megamenu.js', array(), false, true );

		// Embed inline script.
		wp_localize_script( 'wr-mega-menu', 'wr_theme_megamenu', self::localize_script() );

		// Embed data for all menus.
		wp_localize_script( 'wr-mega-menu', 'wr_theme_data_megamenu', self::localize_menus() );

		// Embed data for all sidebars.
		wp_localize_script( 'wr-mega-menu', 'wrmm_sidebars_area', self::localize_sidebars() );

		if( self::$active_woocommerce ) {
			// Embed data for all category.
			wp_localize_script( 'wr-mega-menu', 'wrmm_category', self::get_term_product_category() );
		}

		// Embed data for menu item setting default.
		wp_localize_script( 'wr-mega-menu', 'wrmm_data_default', self::localize_menu_item_settings_default() );
	}

	/**
	 * Save mega menu data by ajax.
	 *
	 * @return  json
	 */
	public static function ajax_save_megamenu() {

		// Check nonce
		if ( ! isset( $_POST['_nonce'] ) || ! wp_verify_nonce( $_POST['_nonce'], 'wr_theme_megamenu_nonce_check' ) ) {
			exit( json_encode( array( 'status' => 'false', 'message' => __( 'The nonce check wrong.', 'wr-nitro' ) ) ) );
		}

		// Check is menu
		if ( ! ( isset( $_POST['menu_id'] ) && ( $_POST['menu_id'] == 0 || is_nav_menu( $_POST['menu_id'] ) ) ) ) {
			exit( json_encode( array( 'status' => 'false', 'message' => __( 'Menu ID is empty.', 'wr-nitro' ) ) ) );
		}

		// Get current data.
		$cur_data = get_option( 'wr_nitro_theme_data_megamenu', '' );
		$cur_data = is_string( $cur_data ) ? json_decode( $cur_data , true ) : $cur_data;

		$data_post = isset( $_POST['data'] ) ? wp_unslash ( $_POST['data'] ) : NULL;

		$data_menu_update = array();

		if( $data_post ) {
			if ( isset( $_POST['data_last_update'] ) && $_POST['data_last_update'] == 'ok' ) {
				foreach ( $data_post as $key => $val ) {
					$data_menu_update[$key] = $val;
				}
			} else {
				array_pop( $data_post );

				$list_id_updated = array();

				foreach ( $data_post as $key => $val ) {
					$data_menu_update[$key] = $val;
					$list_id_updated[] = $key;
				}

				exit( json_encode( array( 'status' => 'updating', 'list_id_updated' => $list_id_updated ) ) );
			};
		}

		if ( $data_menu_update ) {
			$cur_data[ $_POST['menu_id'] ] = $data_menu_update;
		} else {
			unset( $cur_data[ $_POST['menu_id'] ] );
		}

		update_option( 'wr_nitro_theme_data_megamenu', $cur_data );

		exit( json_encode( array( 'status' => 'true' ) ) ) ;
	}

	/**
	 * Reset data megamenu.
	 *
	 * @return  void
	 */
	public static function reset_data() {

		// Get all menu
		$nav_menus = wp_get_nav_menus( array( 'fields' => 'ids' ) );

		$data_mega = '';
		if ( $nav_menus ) {

			// Get current data.
			$data_mega = get_option( 'wr_nitro_theme_data_megamenu', '' );
			$data_mega = is_string( $data_mega ) ? json_decode( $data_mega, true ) : $data_mega;

			if ( $data_mega ) {

				global $wpdb;

				foreach ( $data_mega as $key => $val ) {

					// Unset menu
					if( $key == 0 ) {
						$data_mega[ $nav_menus[0] ] = $data_mega[ $key ];
						unset( $data_mega[ $key ] );
						$val = NULL;
					} elseif( ! in_array( $key, $nav_menus ) ){
						unset( $data_mega[$key] );
					}

					if( $val ) {

						// Get list menu item
						$list_menu_item = $wpdb->get_results(
							$wpdb->prepare(
								"SELECT object_id FROM $wpdb->term_relationships WHERE term_taxonomy_id = %d ",
								$key
							)
						);
						$list_item_check = array();
						if( $list_menu_item ) {
							foreach( $list_menu_item as $val_item ) {
								$list_item_check[] = $val_item->object_id;
							}
						}

						foreach ( $val as $key_item => $val_item ) {
							// Unset menu item
							if ( ! in_array( $key_item, $list_item_check ) ) {
								unset( $data_mega[$key][$key_item] );
							}
						}
					}
				}
			}
		}

		// Update data reset
		update_option( 'wr_nitro_theme_data_megamenu', $data_mega );
	}

	/**
	 * Embed inline script.
	 *
	 * @return  array
	 */
	public static function localize_script() {

		// Get current menu.
		if ( isset( $_GET['menu'] ) && (int) $_GET['menu'] && is_nav_menu( $_GET['menu'] ) ) {
			$menu = ( int ) $_GET['menu'];
		} else {
			$menu = ( int ) get_user_option( 'nav_menu_recently_edited' );

			if( ! is_nav_menu( $menu ) ) {
				$menu = 0;
			}
		}

		return array(
			'ajaxurl'   => admin_url( 'admin-ajax.php' ),
			'adminroot' => admin_url(),
			'rooturl'   => admin_url( 'index.php' ),
			'_nonce'    => wp_create_nonce( 'wr_theme_megamenu_nonce_check' ),
			'menu_id' 	=> $menu,
			'active_wc' => self::$active_woocommerce,
		);
	}

	/**
	 * Embed data for all menus.
	 *
	 * @return  array
	 */
	public static function localize_menus() {
		// Get current menu.
		if ( isset( $_GET['menu'] ) && (int) $_GET['menu'] && is_nav_menu( $_GET['menu'] ) ) {
			$menu = ( int ) $_GET['menu'];
		} else {
			$menu = ( int ) get_user_option( 'nav_menu_recently_edited' );
		}

		// Get menu data.
		$data = get_option( 'wr_nitro_theme_data_megamenu', '' );
		$data = is_string( $data ) ? json_decode( $data, true ) : $data;

		if ( $data && isset( $data[$menu] ) && $data[$menu] ) {

			// Set data product
			foreach( $data[$menu] as $key => $val ) {

				if( ! ( isset( $val['element_type'] ) && isset( $val['element_data'] ) && $val['element_data'] ) ) continue;

				$list_id = explode( ',', $val['element_data'] );
				$list_id = array_reverse( $list_id );

				if( $list_id ) {
					if( $val['element_type'] == 'element-products' ) {
						foreach( $list_id as $key_item => $val_item ) {
							$val_item = (int) $val_item;
							$product  = wc_get_product( $val_item );

							if( $val_item > 0 && $product ) {
								if( $product->post->post_status == 'publish' ) {
									$data[$menu][$key]['element_data_product'][] = array(
										'id' 	=> $val_item,
										'title' => $product->get_title(),
										'image' => $product->get_image( array( 50, 50) ),
										'price' => $product->get_price_html(),
									);
								}
							} else {
								// Delete product data
								unset( $list_id[$key_item] );
							}
						}
					} else if ( $val['element_type'] == 'element-categories' )  {
						foreach( $list_id as $key_item => $val_item ) {
							$val_item   = (int) $val_item;
							$categories = get_term( $val_item, 'product_cat', ARRAY_A );

							if( $val_item > 0 && $categories ) {
								$image = self::get_image_term_product_category( $val_item, array( 100, 100 ) );

								$data[$menu][$key]['element_data_categories'][] = array(
									'id' 	=> $val_item,
									'name'  => $categories['name'],
									'count' => $categories['count'],
									'image' => $image
								);
							} else {
								// Delete product data
								unset( $list_id[$key_item] );
							}
						}
					}
				}

				$data[$menu][$key]['element_data'] = implode( ',', $list_id );
			}

			return $data[$menu];
		}

		return array();
	}

	/**
	 * Embed data for all sidebars.
	 *
	 * @return  array
	 */
	public static function localize_sidebars() {
		// Get all sidebars.
		global $wp_registered_sidebars;

		$data = array();

		if ( $wp_registered_sidebars ) {
			foreach ( $wp_registered_sidebars as $key => $value ) {
				$data[$value['id']] = $value['name'];
			}
		}

		return $data;
	}

	/**
	 * Data menu item settings default.
	 *
	 * @return  array
	 */
	public static function localize_menu_item_settings_default() {
		$data = array(
			'lv_1' => array(
				'icon'                => '',
				'icon_position'       => 'left',
				'active'              => '0',
				'width_type'          => 'full',
				'width'               => '1000',
				'row_layout'          => '1/1',
				'background_image'    => '',
				'background_color'    => '',
				'background_size'     => 'inherit',
				'background_position' => 'left top',
				'background_repeat'   => 'no-repeat',
				'permission_show'     => 'everyone',
				'permission_user'     => array( 'all' ),
			),
			'lv_2' => array(
				'icon'            => '',
				'disable_title'   => '0',
				'element_type'    => '',
				'element_data'    => '',
				'permission_show' => 'everyone',
				'permission_user' => array( 'all' )
			),
			'lv_3' => array(
				'icon'            => '',
				'permission_show' => 'everyone',
				'permission_user' => array( 'all' ),
			)
		);

		return $data;
	}

	/**
	 * Merge data menu item width data default
	 *
	 * @param   array  $data
	 *
	 * @return  array
	 */
	public static function merge_data_menu_item( $data ) {
		$level = ( absint( $data['level'] ) >= 2 ) ? 2 : absint( $data['level'] );
		$level++;

		$data_default = self::localize_menu_item_settings_default();
		$data_default = $data_default[ 'lv_' . $level ];

		return WR_Nitro_Helper::array_replace_recursive( $data_default , $data );
	}

	/**
	 * Plug into WordPress's front-end.
	 *
	 * @return  void
	 */
	public static function frontend() {
		self::$data = get_option( 'wr_nitro_theme_data_megamenu', '' );
		self::$data = is_string( self::$data ) ? json_decode( self::$data, true ) : self::$data;

		// Add filter to process nav menu arguments.
		add_filter( 'header_builder_nav_menu_args', array( __CLASS__, 'nav_menu_args' ) );
	}

	/**
	 * Process nav menu arguments.
	 *
	 * @param   array  $args  Nav menu arguments.
	 *
	 * @return  array
	 */
	public static function nav_menu_args( $args ) {
		if ( ( isset( $args['menu'] ) && $args['menu'] ) || ( isset( $args['theme_location'] ) && $args['theme_location'] ) ) {
			if ( isset( $args['menu']->term_id ) ) {
				$id_menu = $args['menu']->term_id;
			} elseif ( $args['menu'] ) {
				$id_menu = $args['menu'];
			} elseif ( $args['theme_location'] ) {
				// Get location menu current
				$locations = get_nav_menu_locations();
				$id_menu   = $locations[$args['theme_location']];
			}

			if ( isset( $id_menu ) && is_nav_menu( $id_menu ) ) {

				// Define default arguments.
				$defaults = array(
					'container'  => false,
					'menu_class' => 'site-navigator',
					'items_wrap' => '<ul class="%2$s">%3$s</ul>',
					'walker'     => new WR_Nitro_Megamenu_Walker,
				);

				return array_merge( $args, $defaults );

			} else {
				return $args;
			}
		} else {
			// Define default arguments.
			$defaults = array(
				'echo' => false,
			);

			return array_merge( $args, $defaults );
		}
	}
}
