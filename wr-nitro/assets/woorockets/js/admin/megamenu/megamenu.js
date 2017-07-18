/**
 * @version    1.0
 * @package    Nitro
 * @author     WooRockets Team <support@woorockets.com>
 * @copyright  Copyright (C) 2014 WooRockets.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.woorockets.com
 */

(function ( $ ) {
	"use strict";

	$.WrThemeMegamenu                 = $.WrThemeMegamenu || {};
	$.WrThemeMegamenu['data_save']    = {};
	$.WrThemeMegamenu['allow_submit'] = false;

	// Add button show modal setting
	function button_show_modal( pending ){
		var class_pending = pending ? '.pending' : '';

		$( '#menu-to-edit > .menu-item' + class_pending ).each( function(){
			var _this = $(this);

			if( _this.find( '.item-title .wrmm-show-modal' ).length ) {
				return;
			}

			// Get id menu item
			var id = parseInt( _this.attr('id').split( 'menu-item-' )[1] );

			// Get level menu item
			var level = parseInt( _this.attr( 'class' ).split( 'menu-item-depth-' )[1].split( ' ' )[0] );

			// Render button
			_this.find( '.item-title' ).append( '<span class="wrmm-show-modal button-primary">Settings</span>' );

			// Creat data
			$.WrThemeMegamenu['data_save'][id] = data_menu_item( id, level );

			// Add active icon
			if( level == 0 && $.WrThemeMegamenu['data_save'][id]['active'] == 1 ) {
				_this.addClass( 'megamenu-active' );
			}
		});
	}

	function button_expand(){
		var list_menu_item = $( '#menu-to-edit > .menu-item' );
		var has_expand     = false;

		$.each( list_menu_item, function( key, val ){
			var _this      = $(this);
			var level      = parseInt( _this.attr( 'class' ).split( 'menu-item-depth-' )[1].split( ' ' )[0] );
			var el_next    = $( list_menu_item[ key + 1 ] );
			var level_next = level + 1;

			if( el_next.hasClass( 'menu-item-depth-' + level_next ) ) {
				if( ! _this.find( '.menu-item-bar .wr-expand' ).length ) {
					_this.find( '.menu-item-bar' ).append( '<span class="wr-expand"></span>' );
				}
			} else {
				_this.find( '.menu-item-bar .wr-expand' ).remove();
			}
		});
	}

	function add_active_megaemnu(){
		var list_menu_item = $( '#menu-to-edit > .menu-item-depth-0' );
		var has_expand     = false;

		$.each( list_menu_item, function( key, val ){
			var _this = $(this);
			var id    = parseInt( _this.attr('id').split( 'menu-item-' )[1] );

			$.WrThemeMegamenu['data_save'][id]

		});
	}

	// Add expand all and collapse all
	function button_expand_collapse_all(){

		var dom = '<ul class="expand-collapse"><li class="expand-all">Expand all</li><li class="collapse-all">Collapse all</li></ul>';
		$( dom ).insertBefore( '#menu-to-edit' );


		$( 'body' ).on( 'click', '.expand-collapse .expand-all', function(){
			$( '#menu-to-edit > .menu-item:not(".menu-item-depth-0")' ).show();
			$( '#menu-to-edit > .menu-item .wr-expand.collapse' ).removeClass( 'collapse' );
		} );

		$( 'body' ).on( 'click', '.expand-collapse .collapse-all', function(){
			$( '#menu-to-edit > .menu-item:not(".menu-item-depth-0")' ).hide();
			$( '#menu-to-edit > .menu-item .wr-expand' ).addClass( 'collapse' );
		} );

		/*===*===*===*===*===*===*===*===*===*     Expand     *===*===*===*===*===*===*===*===*===*/
		$( '#menu-to-edit' ).on( 'click', '.wr-expand', function() {
			var _this = $(this);
			var hide_flag  = true;

			if( _this.hasClass( 'collapse' ) ) {
				_this.removeClass( 'collapse' );
				hide_flag = false;
			} else {
				_this.addClass( 'collapse' );
			}
			
			var parent         = _this.closest( '.menu-item' );
			var level          = parseInt( parent.attr( 'class' ).split( 'menu-item-depth-' )[1].split(' ')[0] );
			var list_menu_item = $( '#menu-to-edit .menu-item' );
			var index_current  = list_menu_item.index( parent );

			$.each( list_menu_item, function( key, val ){
				if( key > index_current ) {
					var _this          = $(this);
					var level_children = parseInt( _this.attr( 'class' ).split( 'menu-item-depth-' )[1].split(' ')[0] );

					if( level_children <= level ) {
						return false;
					} else {
						if( hide_flag ) {
							_this.hide();
						} else {
							_this.show();
							
							_this.find( '.wr-expand.collapse' ).removeClass( 'collapse' );
						}
					}
				}

			} );
		});
	
		// Delete menu parent
		$( '#menu-to-edit' ).on( 'click', '.item-delete', function(){
			var _this          = $(this);
			var parent         = _this.closest( '.menu-item' );
			var level          = parseInt( parent.attr( 'class' ).split( 'menu-item-depth-' )[1].split(' ')[0] );
			var list_menu_item = $( '#menu-to-edit .menu-item' );
			var index_current  = list_menu_item.index( parent );

			$.each( list_menu_item, function( key, val ){
				if( key > index_current ) {
					var _this          = $(this);
					var level_children = parseInt( _this.attr( 'class' ).split( 'menu-item-depth-' )[1].split(' ')[0] );

					if( level_children <= level ) {
						return false;
					} else {
						_this.show();
					}
				}
			} );
		} );

	}

	var api;
	api = wpNavMenu;
	wpNavMenu.refreshAdvancedAccessibility = function() {

		// Hide all links by default
		$( '.menu-item-settings .field-move a' ).hide();

		// Mark all menu items as unprocessed
		$( 'a.item-edit' ).data( 'needs_accessibility_refresh', true );

		// All open items have to be refreshed or they will show no links
		$( '.menu-item-edit-active a.item-edit' ).each( function() {
			api.refreshAdvancedAccessibilityOfItem( this );
		} );


		// Add listen customs for megamenu
		button_expand();
	};

	function data_menu_item( id, level ){
		var level_data_default = ( level > 1 ) ? 2 : level;
		level_data_default++;

		var data = {};

		if( $.WrThemeMegamenu['data_save'][id] != undefined ) {
			data = $.extend( {}, wrmm_data_default[ 'lv_'+ level_data_default ], $.WrThemeMegamenu['data_save'][id] );
		} else if( wr_theme_data_megamenu[id] != undefined ){
			data = $.extend( {}, wrmm_data_default[ 'lv_'+ level_data_default ], wr_theme_data_megamenu[id] );
		} else {
			data = wrmm_data_default[ 'lv_'+ level_data_default ];
		}

		data['level'] = level;

		return data;
	}

	// Show modal setting
	function list_event(){

		/*===*===*===*===*===*===*===*===*===*     MODAL     *===*===*===*===*===*===*===*===*===*/
		// Show modal settings
		$( '#menu-to-edit' ).on( 'click', '.wrmm-show-modal', function(){
			var _this       = $(this);
			var item_parent = _this.closest( '.menu-item' );

			/* Action for data render */

			// Get id menu item
			var id = parseInt( item_parent.attr('id').split( 'menu-item-' )[1] );

			// Get level menu item
			var level = parseInt( item_parent.attr( 'class' ).split( 'menu-item-depth-' )[1].split( ' ' )[0] );

			$.WrThemeMegamenu['data_save'][id] = data_menu_item( id, level );

			var active_parent        = 0;
			var total_menu_item_lv_2 = 0;

			if( level == 0 ) {
				active_parent        = parseInt( $.WrThemeMegamenu['data_save'][id]['active'] );
				total_menu_item_lv_2 = count_menu_item_lv_2( item_parent );

				/* Refine row layout */
				var row_layout_filter = ( typeof $.WrThemeMegamenu['data_save'][id]['row_layout'] != 'undefined' && typeof $.WrThemeMegamenu['data_save'][id]['row_layout'] == 'string' ) ? $.WrThemeMegamenu['data_save'][id]['row_layout'] : '';
				row_layout_filter     = row_layout_filter.split( ' + ' );
				row_layout_filter     = remove_add_column( total_menu_item_lv_2, row_layout_filter ).join( ' + ' );
				$.WrThemeMegamenu['data_save'][id]['row_layout'] = filter_row( row_layout_filter ).join( ' + ' );
			} else if( level == 1 ) {
				var parent_lv_0 = item_parent.prevAll( '.menu-item-depth-0:first' );
				var parent_id   = parseInt( parent_lv_0.attr('id').split( 'menu-item-' )[1] );

				active_parent   = ( $.WrThemeMegamenu['data_save'][parent_id] != undefined ) ? $.WrThemeMegamenu['data_save'][parent_id]['active'] : 0;
			}

			var permission_parent = get_permission_parent( item_parent );

			/* Check level 0 has menu children */
			var has_children = false;
			if( level == 0 && item_parent.next( '.menu-item-depth-1' ).length != 0 ){
				has_children = true;
			}

			// Get html
			var template_show = _.template( $( "script#wrmm-template" ).html() )({
				data_item         : $.WrThemeMegamenu['data_save'][id],
				active_parent     : active_parent,
				permission_parent : permission_parent,
				title_modal       : item_parent.find( '.menu-item-title' ).text(),
				level             : level,
				id                : id, 
				sidebars_area     : wrmm_sidebars_area,
				"$"               : jQuery,
				item_lv_2         : total_menu_item_lv_2,
				list_row          : $.WrThemeMegamenu.list_row,
				list_icon         : $.WrThemeMegamenu.data_icon,
				has_children      : has_children
			});

			$( 'body' ).append( $( 'script#wrmm-modal-html' ).html() );
			$( '.wrmm-dialog' ).html( template_show );
			$( '.wrmm-modal' ).addClass( 'main-settings' );

			// Set color picker
			if( level == 0 ) {
				set_spectrum();
			}

			// Load content element for menu item level 2
			if( level == 1 ) {
				load_content_element( $.WrThemeMegamenu['data_save'][id]['element_type'], $.WrThemeMegamenu['data_save'][id]['element_data'] );
			}

			/* Action for modal */
			var modal         = $( '.wrmm-dialog' );
			var modal_info    = modal[0].getBoundingClientRect();
			var window_el     = $(window);
			var scroll_top    = window_el.scrollTop();
			var height_window = window_el.height();
			var top_position  = 0;

			if( modal_info.height < height_window ) {
				top_position = scroll_top + ( ( height_window - modal_info.height ) / 2 );
			} else {
				top_position = scroll_top + 10;
			}

			modal.css( 'top', top_position );

		})

		// Close modal main settings
		$('body').on('click', '.wrmm-modal.main-settings .dialog-title .close', function() {
			$(this).closest( '.wrmm-modal' ).remove();
		});

		// Close sub modal settings
		$('body').on('click', '.wrmm-modal.md-all-element .dialog-title .close, .wrmm-modal.md-select-icon .dialog-title .close', function() {
			$(this).closest( '.wrmm-modal' ).remove();

			$( '.wrmm-modal.main-settings.hidden' ).removeClass( 'hidden' );
		});

		// Switch tab in modal settings
		$('body').on('click', '.wrmm-wrapper .nav-settings li', function() {
			var _this    = $(this);
			var parent   = _this.closest( '.wrmm-wrapper' );
			var box_name = _this.attr( 'data-nav' );

			$( '.wrmm-dialog .nav-settings .active' ).removeClass( 'active' );
			$( '.wrmm-dialog .option-settings .item-option.active' ).removeClass( 'active' );

			_this.addClass( 'active' );
			$( '.wrmm-dialog .option-settings .item-option[data-option="' + box_name + '"]' ).addClass( 'active' );
		});

		// Show all element modal
		$('body').on('click', '.wrmm-wrapper .select-element .action .add-element', function() {
			var _this = $(this);

			// Hide main setting modal
			$( '.wrmm-modal.main-settings' ).addClass( 'hidden' );

			$( 'body' ).append( $( 'script#wrmm-modal-html' ).html() );

			$( '.wrmm-dialog:last' ).html( $( 'script#wrmm-all-element' ).html() );
			$( '.wrmm-modal:last' ).addClass( 'md-all-element' );

			/* Action for modal */

			var modal         = $( '.wrmm-dialog:last' );
			var modal_info    = modal[0].getBoundingClientRect();
			var window_el     = $(window);
			var scroll_top    = window_el.scrollTop();
			var height_window = window_el.height();
			var top_position  = 0;

			if( modal_info.height < height_window ) {
				top_position = scroll_top + ( ( height_window - modal_info.height ) / 2 );
			} else {
				top_position = scroll_top + 10;
			}

			modal.css( 'top', top_position );
		});

		/*===*===*===*===*===*===*===*===*===*     CHANGE DATA SETTINGS     *===*===*===*===*===*===*===*===*===*/
		// Show enable megamenu
		$('body').on('click', '.wrmm-wrapper .chb-enable', function() {
			var _this = $( this );

			//Get id current
			var parent_current 	= _this.closest( '.wrmm-wrapper' );

			var id_el = $( '#menu-item-' + parent_current.attr( 'data-id' ) ) ;
			
			var menu_item 		= $( '#menu-to-edit li.menu-item' );
			if($(this).is(":checked")) {
				var data_insert = '1';
				parent_current.find( '.wrapper-option' ).stop( true, false ).slideDown();

				// Add active
				id_el.addClass( 'megamenu-active' );  
			} else {
				var data_insert = '0';
				parent_current.find( '.wrapper-option' ).stop( true, false ).slideUp();

				// Remove active
				id_el.removeClass( 'megamenu-active' );  
			}

			//Load data in input
			update_option_data( _this, data_insert, 'active');
		});

		// Choose width type
		$('body').on('change', '.wrmm-wrapper .select-width', function() {
			//Load data in input
			var value = $(this).val();
			update_option_data( $( this ), value, 'width_type');

			if( value == 'fixed' ){
				$( this ).closest( '.width' ).find( '.number-width-box' ).removeAttr( 'style' );
			} else {
				$( this ).closest( '.width' ).find( '.number-width-box' ).hide();
			}
		});

		// Change width menu
		$('body').on('blur', '.wrmm-wrapper .number-width-box .number-width', function() {
			//Load data in input
			var data_insert = $( this ).val().replace(/[^0-9]/gi, '');
			update_option_data( $( this ), data_insert, 'width');
		});

		// Select media 
		$('body').on('click', '.wr-uploader-media .wr-image-button', function() {
			var _this     = $( this );
			var parent    = _this.closest( '.wrmm-wrapper' );
			var input_url = parent.find( '.wr-image-link' );
			var bg_option = parent.find( '.background-option' );

		    // Create a new media frame
		    if ( ! window.wr_media ) {
				window.wr_media = wp.media({
					frame    : 'post',
			        multiple : false,
			        button: {
				        text: 'Insert'
				    },
				});
			}

			window.wr_media.off( 'insert' ).on( 'insert', function( e ) {
				_this.closest( '.wrmm-modal' ).removeClass( 'hidden' );

				var attachmente = window.wr_media.state().get( 'selection' ).first().toJSON();
				var size        = $( '.attachment-display-settings .size' ).val();
				var url         = attachmente['sizes'][size]['url'];

				input_url.val( url );
				input_url.focus();
				bg_option.show();
			});

			window.wr_media.off( 'close' ).on( 'close', function( e ) {
				_this.closest( '.wrmm-modal' ).removeClass( 'hidden' );
			});

			// Finally, open the modal on click
			window.wr_media.open();

			// Hide modal
			_this.closest( '.wrmm-modal' ).addClass( 'hidden' );
		});

		// Remove url link image
		$('body').on('click', '.wr-uploader-media .wr-image-remove', function() {

			var _this     = $(this);
			var parent    = _this.closest( '.wrmm-wrapper' );
			var txt_image = parent.find('.wr-image-link');
			var bg_option = parent.find( '.background-option' );

			txt_image.val( '' );
			txt_image.focus();
			bg_option.hide();
		});

		// Choose image upload
		$('body').on('blur', '.wrmm-wrapper .background-image .wr-uploader-media .wr-image-link', function() {
			var _this       = $(this);
    		var data_insert = _this.val();
    		var parent      = _this.closest( '.wrmm-wrapper' );
			var bg_option   = parent.find( '.background-option' );

			update_option_data( _this, data_insert, 'background_image');

			if( data_insert == '' ) {
				bg_option.hide();
			} else {
				bg_option.show();
			}
		});

		$('body').on('change', '.wrmm-wrapper .background-size', function() {
			var _this = $(this);
			var data_insert = _this.val();
			update_option_data( _this, data_insert, 'background_size');
		});

		$('body').on('change', '.wrmm-wrapper .background-position', function() {
			var _this = $(this);
			var data_insert = _this.val();
			update_option_data( _this, data_insert, 'background_position');
		});

		$('body').on('change', '.wrmm-wrapper .background-repeat', function() {
			var _this = $(this);
			var data_insert = _this.val();
			update_option_data( _this, data_insert, 'background_repeat');
		});

		// Set color background
		$( 'body' ).on( 'change', '.wrmm-wrapper .txt-select-color', function( ) {
			var _this = $(this);
			var data_insert = _this.val();
			update_option_data( _this, data_insert, 'background_color');
		});

		// Show collum title
		$('body').on('click', '.wrmm-wrapper .checkbox-column', function() {
			if($(this).is(":checked")) {
				var data_insert = '1';
			} else {
				var data_insert = '0';
			}

			//Load data in input
			update_option_data( $( this ), data_insert, 'disable_title');
		});

		// Choose row layout 
		$('body').on('click', '.wrmm-wrapper .list-layout li', function() {
			var _this          = $(this);
			var _parent        = _this.closest( '.wrmm-wrapper' );
			var childrent      = _parent.find( '.list-layout li' );
			var txt_show_value = _parent.find( '.row-text .row-txt' );
			var value_select   = _this.attr( 'title' );

			update_option_data( $( this ), value_select, 'row_layout');
     
			childrent.removeClass( 'active' );
			_this.addClass( 'active' );

			txt_show_value.val( value_select );
			txt_show_value.focus();
		});

		$('body').on('keyup', '.wrmm-wrapper .row-text .row-txt', _.debounce( function(){
			var _this         = $(this);
			var parent        = _this.closest( '.wrmm-wrapper' );
			var list_layout   = parent.find( '.list-layout li' );
			var value         = _this.val();
			var result_array  = filter_row( value );
			var result_string = result_array.join( ' + ' );

			list_layout.removeClass( 'active' );
			parent.find( '.list-layout li[title="' + result_string + '"]' ).addClass( 'active' );
		} , 1000 ) );

		$('body').on( 'blur', '.wrmm-wrapper .row-text .row-txt', function(){
			var _this            = $(this);
			var value            = _this.val();
			var parent           = _this.closest( '.wrmm-wrapper' );
			var item_id          = parent.attr( 'data-id' );
			var result_array     = filter_row( value );
			var parent_menu_item = $( '#menu-item-' + item_id );
			var list_layout      = parent.find( '.list-layout li' );
			var count_item_lv_2  = count_menu_item_lv_2( parent_menu_item );
			var result_string    = remove_add_column( count_item_lv_2, result_array ).join( ' + ' );

			list_layout.removeClass( 'active' );
			parent.find( '.list-layout li[title="' + result_string + '"]' ).addClass( 'active' );

			_this.val( result_string );

			update_option_data( _this, result_string, 'row_layout');
		});

		// Permission show to
		$('body').on('click', '.wrmm-wrapper .permission .list-show .chb-of', function() {
			var _this = $(this);

			if( _this.is( ':checked' ) ) {
				var value = _this.val();

				update_option_data( _this, value, 'permission_show');

				if( value == 'log-in' ) {
					_this.closest( '.permission' ).find( '.type-member-row' ).show();
				} else {
					_this.closest( '.permission' ).find( '.type-member-row' ).hide();
				}
			}
		});

		// Permission user allow
		$('body').on('click', '.wrmm-wrapper .permission .type-member .chb-of', function() {
			var _this       = $(this);
			var parent      = _this.closest( '.type-member' );
			var list_user   = parent.find( '.chb-of' );
			var data_insert = [];

			if( list_user.length ) {

				var val = _this.val();

				if( val == 'all' ) {
					if( _this.is( ':checked' ) ) {
						list_user.prop( 'checked', true );
					} else {
						list_user.prop( 'checked', false );
					}
				} else {
					if( _this.is( ':checked' ) ) {
						var length_checked = parent.find( '.chb-of:checked' ).length;

						if( length_checked >= list_user.length - 1 ) {
							parent.find( '.chb-of[value="all"]' ).prop( 'checked', true );
						}
					} else {
						parent.find( '.chb-of[value="all"]' ).prop( 'checked', false );
					}
				}

				$.each( list_user, function( ) {
					if( $(this).is( ':checked' ) ) {
						data_insert.push( $(this).val() );
					}
				} );
			};

			update_option_data( _this, data_insert, 'permission_user');
		});

		// Search icon
		$('body').on('keyup', '.md-select-icon .wrmm-list-icon .search', function() {
			var _this     = $(this);
			var keyword   = _this.val().toLowerCase();
			var list_icon = _this.closest( '.wrmm-list-icon' ).find( '.list-icon ul li' );
			search_icon( keyword, list_icon );
		});

		// Choose icon
		$('body').on('click', '.md-select-icon .wrmm-list-icon li', function() {
			var _this       = $(this);
			var value       = _this.html();
			var parent      = $( '.wrmm-modal.main-settings .menu-icon' );
			var data_insert = _this.find( 'i' ).attr( 'class' );

			parent.find( '.delete-position.data-empty' ).removeClass( 'data-empty' );
			parent.find( '.delete-position .get-delete .get' ).html( value );

			update_option_data( $( '.wrmm-modal.main-settings .menu-icon' ), data_insert, 'icon');

			// Remove modal list icon
			$( '.wrmm-modal.md-select-icon' ).remove();

			// Show main setting modal
			$( '.wrmm-modal.main-settings.hidden' ).removeClass( 'hidden' );
		});

		// Remove icon
		$('body').on('click', '.wrmm-wrapper .menu-icon .get-delete .delete', function() {
			var _this  = $(this);

			var delete_confirm = confirm( 'Do you really want to delete this icon?' );

			if( ! delete_confirm ) return;

			var parent = _this.closest( '.menu-icon' );

			$( '.wrmm-wrapper .menu-icon .list-icon li.active' ).removeClass( 'active' );
			_this.closest( '.delete-position' ).addClass( 'data-empty' );

			update_option_data( _this, '', 'icon');
		});

		// Add or edit icon
		$('body').on('click', '.wrmm-wrapper .menu-icon .get-delete .add-icon, .wrmm-wrapper .menu-icon .get-delete .added-icon .get', function() {
			var _this = $(this);

			// Hide main setting modal
			$( '.wrmm-modal.main-settings' ).addClass( 'hidden' );

			$( 'body' ).append( $( 'script#wrmm-modal-html' ).html() );

			var data_active = '';
			if( ! _this.hasClass( 'add-icon' ) ) {
				var id = _this.closest( '.wrmm-wrapper' ).attr( 'data-id' );
				data_active = $.WrThemeMegamenu['data_save'][id]['icon'];
			}

			var template_list_icon = _.template( $( "script#wrmm-select-icon" ).html() )({
				list_icon   : $.WrThemeMegamenu.data_icon.font_awesome,
				icon_active : data_active
			});

			$( '.wrmm-dialog:last' ).html( template_list_icon );

			$( '.wrmm-modal:last' ).addClass( 'md-select-icon' );

			/* Action for modal */

			var modal         = $( '.wrmm-dialog:last' );
			var modal_info    = modal[0].getBoundingClientRect();
			var window_el     = $(window);
			var scroll_top    = window_el.scrollTop();
			var height_window = window_el.height();
			var top_position  = 0;

			if( modal_info.height < height_window ) {
				top_position = scroll_top + ( ( height_window - modal_info.height ) / 2 );
			} else {
				top_position = scroll_top + 10;
			}

			modal.css( 'top', top_position );
		});

		// Choose icon position
		$('body').on('click', '.wrmm-wrapper .menu-icon .delete-position .position li', function() {
			var _this = $(this);

			$( '.wrmm-wrapper .menu-icon .delete-position .position li.active' ).removeClass( 'active' );
			_this.addClass( 'active' );

			var data_insert = _this.attr( 'data-value' );
			update_option_data( _this, data_insert, 'icon_position');
		});

		// Choose element
		$('body').on('click', '.wrmm-list-element .item', function() {
			var _this         = $(this);
			var value         = 'element-' + _this.attr( 'data-value' );
			var main_setting  = $( '.wrmm-modal.main-settings' );
			var title_element = '';

			$( '.wrmm-modal.md-all-element' ).remove();

			// Show main setting modal
			$( '.wrmm-modal.main-settings.hidden' ).removeClass( 'hidden' );

			load_content_element( value, '' );

			switch( value ){
				case 'element-text':
					title_element = 'Text element';
					break;

				case 'element-products':
					title_element = 'Products element';
					break;

				case 'element-categories':
					title_element = 'Product categories element';
					break;

				case 'element-widget':
					title_element = 'Widget element';
					break;
			}

			$( '.wrmm-wrapper .select-element .action.not-element' ).removeClass( 'not-element' );
			$( '.wrmm-wrapper .select-element .action .added-element span' ).html( title_element );

			update_option_data( $( '.wrmm-modal.main-settings .select-element' ), value, 'element_type');
		});
	
		// Remove element
		$('body').on('click', '.wrmm-wrapper .select-element .action .added-element .delete', function() {
			var _this = $(this);

			var delete_confirm = confirm( 'Do you really want to delete this element?' );

			if( ! delete_confirm ) return;

			$( '.wrmm-wrapper .select-element .action' ).addClass( 'not-element' );

			$( '.wrmm-wrapper .select-element .content-element' ).html( '' );

			update_option_data( _this, '', 'element_type');
			update_option_data( _this, '', 'element_data');
		});

		// Event blur of editor text element
		$('body').on('blur', '.wrmm-wrapper .wrmm-text-element .wrmm-editor', function( e ) {
		 	var _this      = $( e.currentTarget );
            var content    = window.switchEditors._wp_Autop ( _this.val() );     // Changes double line-breaks in the text into HTML paragraphs (<p>...</p>).
            var input_hide = _this.closest( '.editor-wrapper' ).find( '.wrmm-editor-hidden' );

            input_hide.val( content ).trigger('change');
		});

		// Change value text element
		$('body').on('change', '.wrmm-wrapper .wrmm-text-element .wrmm-editor-hidden', function( e ) {
			var _this       = $(this);
			var data_insert = _this.val();

			update_option_data( _this, data_insert, 'element_data');
		});

		// Choose widget area
		$('body').on('change', '.wrmm-wrapper .wrmm-widget-element .wrmm-list-widget', function( e ) {
			var _this       = $(this);
			var data_insert = _this.val();

			update_option_data( _this, data_insert, 'element_data');
		});

		// Search product by ajax
		var timer_product, last_keyword_product = true;
		$('body').on('keyup', '.wrmm-wrapper .wrmm-products-element .search-product .search-ajax .product-ajax', function() {
			var _this         = $(this);
			var container 	  = _this.closest( '.search-ajax' );

			if ( timer_product ) {
				clearTimeout( timer_product );
			}

			timer_product = setTimeout( function() {

				// Get keyword.
				var keyword = _this.val();

				container.find( '.loading-search' ).remove();
				container.find( '.results-search' ).remove();

				if( last_keyword_product !== true && keyword == last_keyword_product && ! container.find( '.loading-search' ).length ) {
					return;
				}

				last_keyword_product = keyword;

				if ( keyword == '' || keyword.length <= parseInt( _this.attr( 'data-min-characters' ) ) ) {
					return;
				}

				// Show loading indicator.
				container.append( '<img class="loading-search" src="images/spinner.gif">' );

				// Custom for Nitro theme
				_this.closest( '.element-item' ).addClass( 'loading-wrls' );

				$.ajax( {
					type : "POST",
					url  : wr_theme_megamenu.ajaxurl,
					data : {
						action 	: 'wrmm_products',
						keyword : keyword,
						_nonce 	: wr_theme_megamenu._nonce,
					},
					success  : function( response ) {

						var response = ( response ) ? JSON.parse( response ) : '';

						container.find( '.loading-search' ).remove();
						container.find( '.results-search' ).remove();

						container.append( '<div class="results-search"></div>' );

						// Prepare response.
						if ( response.message ) {
							container.find( '.results-search' ).append( '<div class="wrmm-no-results">' + response.message + '</div>' );
						} else {
							container.find( '.results-search' ).append( '<div class="list-products"></div>' );

							// Show results.
							$.each( response.list_product, function( key, value ) {
								container.find( '.list-products' ).append( '<div class="item" data-id="' + value.id + '"><div class="img">' + value.image + '</div><div class="title-price"><div class="name-product">' + value.title + '</div><div class="price">' + value.price + '</div></div></div>' );
							} );
						}
					}
				} );
			}, 300 );
		});
		$('body').on('focus', '.wrmm-wrapper .wrmm-products-element .search-product .search-ajax .product-ajax', function() {
			var parent = $(this).closest('.search-ajax');

			parent.find('.loading-search').remove();
			parent.find('.results-search').show();
		});

		$('body').on('blur', '.wrmm-wrapper .wrmm-products-element .search-product .search-ajax .product-ajax', function() {
			var parent = $(this).closest('.search-ajax');

			parent.find('.loading-search').remove();
			parent.find('.results-search').hide();
		});
		
		// Choose product
		$('body').on('mousedown', '.wrmm-wrapper .search-ajax .results-search .list-products .item', function() {
			var _this           = $(this);
			var parent          = _this.closest( '.wrmm-wrapper' );
			var product_id      = _this.attr( 'data-id' );
			var item_id         = parent.attr( 'data-id' );
			var product_content = _this[ 0 ].outerHTML;

			// Move DOM to product added
			parent.find( '.wrmm-products-element .products-added .list-products' ).append( product_content );

			// Update data
			if( typeof $.WrThemeMegamenu.data_save[item_id]['element_data'] == 'undefined' ) {
				var data_insert = product_id;
			} else {
				var element_data = $.WrThemeMegamenu.data_save[item_id]['element_data'].split( ',' );
				element_data.push( product_id );
				var data_insert = element_data.join( ',' );
			}
			update_option_data( _this, data_insert, 'element_data');

			// Add buttom delete product
			parent.find( '.products-added .item:last' ).append( '<i class="del-product dashicons dashicons-no-alt"></i>' );

			parent.find( '.wrmm-products-element .search-ajax .results-search' ).hide();

			_this.remove();
		});

		// Delete product
		$('body').on( 'click', '.wrmm-wrapper .products-added .del-product', function() {
			var _this       = $(this);
			var wrapper     = _this.closest( '.wrmm-wrapper' );
			var parent      = _this.closest( '.item' );
			var product_id  = parent.attr( 'data-id' );
			var item_id     = wrapper.attr( 'data-id' );

			if( ( typeof $.WrThemeMegamenu.data_save[ item_id ] != 'undefined' ) && ( typeof $.WrThemeMegamenu.data_save[ item_id ]['element_data'] != 'undefined' ) ) {
				var data_insert = $.WrThemeMegamenu.data_save[ item_id ]['element_data'];
				data_insert = data_insert.split( ',' );
				
				// Unset product_id value in array
				data_insert = _.without( data_insert, product_id );
				
				data_insert = data_insert.join();

				update_option_data( _this, data_insert, 'element_data');
			}

			parent.remove();
		});

		/* Action for category element */
		$('body').on( 'focus', '.wrmm-wrapper .categories-ajax', function() {
			var _this = $(this);
			
			_this.trigger( 'keyup' );
		});

		$('body').on( 'keyup', '.wrmm-wrapper .categories-ajax', function() {
			var _this         = $(this);
	        var keyword       = _this.val();
	        var parent        = _this.parent();
	        var list_category = parent.find( '.item-categories' );

	        if( list_category.length ) {
	        	parent.find( '.list-categories' ).show();
	        }

	        if( keyword ) {
	            if( window.keyword_font_old == undefined || window.keyword_font_old != keyword ) {
	                list_category.hide();
	                list_category.each( function () {
	                	var _this_list = $(this);
	                    var textField = ( _this_list.attr( 'data-search' ) != undefined ) ? _this_list.attr( 'data-search' ).toLowerCase() : '' ;
	                    var keyword_lowercase = keyword.toLowerCase().trim();
	                    if( textField.indexOf( keyword_lowercase ) == -1 ) {
	                        _this_list.hide();
	                    } else {
	                        _this_list.show();
	                    }
	                } );

	                window.keyword_font_old = keyword; 
	            }
	        } else {
	            list_category.show();
	        }
		});

		$('body').on( 'blur', '.wrmm-wrapper .categories-ajax', function() {
			var _this = $(this);
			var parent = _this.parent();
			parent.find( '.list-categories' ).hide();
		});
		
		// Choose category
		$('body').on('mousedown', '.wrmm-wrapper .search-categories .item-categories', function() {
			var _this           = $(this);
			var parent          = _this.closest( '.wrmm-wrapper' );
			var category_id      = _this.attr( 'data-id' );
			var item_id         = parent.attr( 'data-id' );
			var category_content = _this[ 0 ].outerHTML;

			// Move DOM to product added
			parent.find( '.wrmm-category-element .category-added' ).append( category_content );

			// Update data
			if( typeof $.WrThemeMegamenu.data_save[item_id]['element_data'] == 'undefined' ) {
				var data_insert = category_id;
			} else {
				var element_data = $.WrThemeMegamenu.data_save[item_id]['element_data'].split( ',' );
				element_data.push( category_id );
				var data_insert = element_data.join( ',' );
			}
			update_option_data( _this, data_insert, 'element_data');

			// Add buttom delete product
			parent.find( '.category-added .item-categories:last' ).append( '<i class="del-category dashicons dashicons-no-alt"></i>' );

			parent.find( '.wrmm-category-element .search-ajax .list-categories' ).hide();
		});

		// Delete a category
		$('body').on( 'click', '.wrmm-category-element .category-added .del-category', function() {
			var _this       = $(this);
			var wrapper     = _this.closest( '.wrmm-wrapper' );
			var parent      = _this.closest( '.item-categories' );
			var category_id  = parent.attr( 'data-id' );
			var item_id     = wrapper.attr( 'data-id' );

			if( ( typeof $.WrThemeMegamenu.data_save[ item_id ] != 'undefined' ) && ( typeof $.WrThemeMegamenu.data_save[ item_id ]['element_data'] != 'undefined' ) ) {
				var data_insert = $.WrThemeMegamenu.data_save[ item_id ]['element_data'];
				data_insert = data_insert.split( ',' );
				
				// Unset category_id value in array
				data_insert = _.without( data_insert, category_id );
				
				data_insert = data_insert.join();

				update_option_data( _this, data_insert, 'element_data');
			}

			parent.remove();
		});

		/*===*===*===*===*===*===*===*===*===*     SAVE DATA     *===*===*===*===*===*===*===*===*===*/
		// Save data menu
		$( '.wp-admin #update-nav-menu' ).on( "submit", function( e ) {
			if( Object.keys( $.WrThemeMegamenu.data_save ).length ) {
				
				// Add loading
				if( !$( '.wrmm-loading' ).length ) {
					$( '.major-publishing-actions .publishing-action' ).prepend( '<img class="wrmm-loading" src="images/spinner.gif" />' );
				}
				
				// Remove error if has
				if( $( '.wrmm-error' ).length ) {
					$( '.wrmm-error' ).remove();
				}

				if( $.WrThemeMegamenu.allow_submit == false ) {
					e.preventDefault();
					
					// Save data
					save_ajax();
				}
			}
		});
	}

	function load_content_element( type, content ) {
		var html_render = '';

		switch( type ){
			case 'element-text':

				var wp_editor = $( 'script#wrmm-text-element' ).html();

				wp_editor = wp_editor.replace( '_WR_CONTENT_', content );

				$( '.wrmm-wrapper .select-element .content-element' ).html( wp_editor );

				var render_editor = function(){
					var intTimeout = 5000;
			        var intAmount  = 100;
			        var iframe_load_completed = true;

			        var ifLoadedInt = setInterval(function(){
			            if (iframe_load_completed || intAmount >= intTimeout) {

			                ( function() {
			                    var init, id, $wrap;

			                    // Render Visual Tab
			                    for ( id in tinyMCEPreInit.mceInit ) {
			                        if ( id != 'wrmm-editor' )
			                            continue;

			                        init  = tinyMCEPreInit.mceInit[id];
			                        $wrap = tinymce.$( '#wp-' + id + '-wrap' );

			                        tinymce.remove(tinymce.get('wrmm-editor'));
			                        tinymce.init( init );

			                        setTimeout( function(){
			                            $( '#wp-wrmm-editor-wrap' ).removeClass( 'html-active' );
			                            $( '#wp-wrmm-editor-wrap' ).addClass( 'tmce-active' );
			                        }, 10 );

			                        if ( ! window.wpActiveEditor )
			                                window.wpActiveEditor = id;

			                        break;
			                    }

			                    // Render Text tab
			                    for ( id in tinyMCEPreInit.qtInit ) {
			                        if ( id != 'wrmm-editor' )
			                            continue;

			                        quicktags( tinyMCEPreInit.qtInit[id] );

			                        // Re call inset quicktags button
			                        QTags._buttonsInit();

			                        if ( ! window.wpActiveEditor )
			                            window.wpActiveEditor = id;

			                        break;
			                    }
			                }());

			                iframe_load_completed = false;
			                window.clearInterval(ifLoadedInt);
			            }
			        },
			        intAmount
			        );
				};

				render_editor();

				break;
			case 'element-products':

				if( wr_theme_megamenu.active_wc != 1 ) { break; }

				if( content ) {
					var content_element = $( '.wrmm-wrapper .select-element .content-element' );

					content_element.html( '<img class="loading-search" src="images/spinner.gif" />' );

					$.ajax( {
						type : "POST",
						url  : wr_theme_megamenu.ajaxurl,
						data : {
							action 	: 'wrmm_get_products',
							list_id : content,
							_nonce 	: wr_theme_megamenu._nonce,
						},
						success  : function( response ) {
							var response = ( response ) ? JSON.parse( response ) : '';

							var template_show = _.template( $( "script#wrmm-products-element" ).html() )({
								"$"          : jQuery,
								list_product : response
							});
							content_element.html( template_show );
						}
					} );
				} else {
					var template_show = _.template( $( "script#wrmm-products-element" ).html() )({
						"$"          : jQuery,
						list_product : content
					});

					$( '.wrmm-wrapper .select-element .content-element' ).html( template_show );
				}

				break;
			case 'element-categories':

				if( wr_theme_megamenu.active_wc != 1 ) { break; }

				var template_show = _.template( $( "script#wrmm-category-element" ).html() )({
					"$"             : jQuery,
					list_categories : content,
					all_categories  : wrmm_category
				});

				$( '.wrmm-wrapper .select-element .content-element' ).html( template_show );

				break;
			case 'element-widget':

				var template_show = _.template( $( "script#wrmm-widget-element" ).html() )({
					"$"           : jQuery,
					sidebars_area : wrmm_sidebars_area,
					value         : content,
				});

				$( '.wrmm-wrapper .select-element .content-element' ).html( template_show );

				$( '.wrmm-wrapper .wrmm-widget-element .wrmm-list-widget' ).trigger( 'change' );

				break;
		}

		return html_render;
	}

	function search_icon( keyword, list_icon ) {
		if( keyword ) {
			list_icon.each( function(){
				var _this = $(this);
                var textField = $(this).attr("data-value").toLowerCase();
				if ( textField.indexOf( keyword ) == -1 ) {
                    _this.hide();
                } else {
                    _this.fadeIn(300);
                }
			});
		} else{
			list_icon.show();
		}
	};

	function save_ajax(){
		// Remove data null before udpate
		var data_save = {};
		
		$.each( $.WrThemeMegamenu.data_save, function( key, val ){
			data_save[key] = {};

			$.each( val, function( key_item, val_item ) {
				if( typeof val_item == 'string' )
					val_item = val_item.trim();

				if( val_item !== '' ) {
					switch( val['level'] ) {
					    case 0:
					    	if ( typeof wrmm_data_default['lv_1'][key_item] != 'undefined' && wrmm_data_default['lv_1'][key_item] != val_item )
					    		data_save[key][key_item] = val_item;

					        break;
					    case 1:
					    	if ( typeof wrmm_data_default['lv_2'][key_item] != 'undefined' && wrmm_data_default['lv_2'][key_item] != val_item )
					    		data_save[key][key_item] = val_item;

					        break;
					    default:
					    	if ( typeof wrmm_data_default['lv_3'][key_item] != 'undefined' && wrmm_data_default['lv_3'][key_item] != val_item )
					    		data_save[key][key_item] = val_item;
					}
				}

			});
		});

		$.ajax( {
			type   : "POST",
			url    : wr_theme_megamenu.ajaxurl,
			data   : {
				action           : 'wr_save_megamenu',
				_nonce           : wr_theme_megamenu._nonce,
				menu_id          : wr_theme_megamenu.menu_id,
				data             : data_save,
				data_last_update : 'ok',
			},
			success: function ( data_return ) {
				
				// Parse data
				var data_return = ( data_return ) ? JSON.parse( data_return ) : '';
				if( data_return.status == 'true' ) {
					if( $( '.wrmm-error' ).length ) {
						$( '.wrmm-error' ).remove();
					}
					
					// Submit form
					$.WrThemeMegamenu.allow_submit = true;
					$( '.wp-admin #update-nav-menu' ).submit();
				} else if( data_return.status == 'updating' ) {
					$.each( data_return.list_id_updated , function ( value, key ) {
						delete $.WrThemeMegamenu.data_save[ key ];
					});
					
					// Update next data
					$.WrThemeMegamenu.save_ajax();

				} else if( data_return.status == 'false' ) {
					if( $( '.wrmm-loading' ).length ) {
						$( '.wrmm-loading' ).remove();
					}
					
					// Show error
					$( '.major-publishing-actions .publishing-action' ).prepend( '<p class="wrmm-error">' + data_return.message + '</p>' );
				}
			}
		});
	}

	function count_menu_item_lv_2( element ){
		var item_next_lv_2  = element.next( '.menu-item-depth-1' );
		var count_item_lv_2 = 0;

		if( item_next_lv_2.length ) {
			var item_next_lv_0 = element.nextAll( '.menu-item-depth-0' );

			if( item_next_lv_0.length ) {
				var list_menu_item       = $( '#menu-to-edit > .menu-item' );
				var index_menu_item_next = list_menu_item.index( item_next_lv_0[0] );
				var index_menu_item      = list_menu_item.index( element );

				for( var i = index_menu_item + 2; i <= index_menu_item_next; i++ ) {
					if( $( '#menu-to-edit .menu-item:nth-child(' + i + ')' ).hasClass( 'menu-item-depth-1' ) ) {
						count_item_lv_2++;
					}
				}
			} else {
				var menu_item_next_lv_2   = element.nextAll( '.menu-item-depth-1' );
				count_item_lv_2 = menu_item_next_lv_2.length;
			}
		}

		return count_item_lv_2;
	}

	function get_permission_parent( _this ){
		var permission = { permission_show: null, permission_user: null };

		// Get id menu item
		var id = parseInt( _this.attr('id').split( 'menu-item-' )[1] );

		// Get level menu item
		var level = parseInt( _this.attr( 'class' ).split( 'menu-item-depth-' )[1].split( ' ' )[0] );

		if( level == 0 ){
			permission = {
				permission_show: $.WrThemeMegamenu['data_save'][id]['permission_show'],
				permission_user: $.WrThemeMegamenu['data_save'][id]['permission_user']
			};
		} else {
			var all_el_prev        = _this.prevAll();
			var length_all_el_prev = all_el_prev.length;
			var list_parent        = {};

			for( var i = length_all_el_prev - 1; i >= 0; i-- ){
				var prev_item = $( all_el_prev[i] );

				// Get id menu item
				var prev_id = parseInt( prev_item.attr('id').split( 'menu-item-' )[1] );

				// Get level menu item
				var prev_level = parseInt( prev_item.attr( 'class' ).split( 'menu-item-depth-' )[1].split( ' ' )[0] );

				if( prev_level < level ){
					list_parent[prev_level] = {
						permission_show: $.WrThemeMegamenu['data_save'][prev_id]['permission_show'],
						permission_user: $.WrThemeMegamenu['data_save'][prev_id]['permission_user']
					};
				}
			};

			$.each( list_parent, function( level, val ) {
				var flag_set_log_in = false;

				if( level == 0 ) {
					if( val['permission_show'] == 'log-in' ) {
						permission['permission_user'] = val['permission_user'];
					} else {
						permission['permission_user'] = 'all';
					}
				} else if( permission['permission_user'].length == 0 ) {
					permission['permission_user'] = '';
					flag_set_log_in = true;
				} else if( permission['permission_show'] != 'log-in' ){
					if( val['permission_show'] == 'log-in' ) {
						permission['permission_user'] = val['permission_user'];
					} else {
						permission['permission_user'] = 'all';	
					}
				} else {

					// Check permission user of parent
					if( permission['permission_user'].indexOf( 'all' ) != -1 ) {
						permission['permission_user'] = val['permission_user'];
					} else if( val['permission_user'].indexOf( 'all' ) != -1 ) {
						permission['permission_user'] = permission['permission_user'];
					} else {
						permission['permission_user'] = array_intersect( val['permission_user'], permission['permission_user'] );
					}
				}

				permission['permission_show'] = flag_set_log_in ? 'log-in' : val['permission_show'] ;
			});
		}

		return permission;
	}

	function array_intersect( array_1, array_2 ){
	    return $.grep( array_1, function(i) {
	        return $.inArray( i, array_2 ) > -1;
	    });
	}

	function update_option_data( element, val, key ){
		if( key ) {
			var id = element.closest( '.wrmm-wrapper' ).attr( 'data-id' );

			// Set value for option
    		$.WrThemeMegamenu.data_save[id][key] = val;
		}
	}

	function filter_row( value ){
		var list_column  = value.split( '+' );
		var result_array = [];

		var filter_column = function( value ){
			if( value.search( '/' ) != -1 ) {
				var val_all   = value.split( '/' );
				var val_first = Math.abs( parseInt( val_all[0] ) ) ? Math.abs( parseInt( val_all[0] ) ) : 1;
				var val_last  = Math.abs( parseInt( val_all[1] ) ) ? Math.abs( parseInt( val_all[1] ) ) : 1;

				return val_first + '/' + val_last;
			} else {
				var val_all = Math.abs( parseInt( value ) ) ? Math.abs( parseInt( value ) ) : 1;

				return val_all + '/1';
			}
		}

		if( list_column.length ) {
			$.each( list_column, function( key, val ) {
				var column = filter_column( val );

				result_array.push( column );
			} )
		} else {
			var column = filter_column( val );

			result_array.push( column );
		}

		return result_array;
	}

	function remove_add_column( count_item_lv_2, list_column ){
		if( count_item_lv_2 == 0 )
			return [];

		var column_surplus  = list_column.length - count_item_lv_2;

		if( column_surplus > 0 ) {
			for( var i = column_surplus; i > 0 ; i-- ) {
				list_column.splice( count_item_lv_2 + i - 1 , 1);
			}
		} else if ( column_surplus < 0 ) {
			for( var i = Math.abs( column_surplus ); i > 0 ; i-- ) {
				list_column.push( '1/1' );
			}
		}

		return list_column;
	}

	function set_spectrum(){
		$( '.wrmm-wrapper .txt-select-color' ).spectrum( {
			color: '',
		    showInput: true,
		    showInitial: true,
		    allowEmpty: true,
		    showAlpha: true,
		    clickoutFiresChange: true,
		    showButtons: true,
		    cancelText: 'Cancel',
		    chooseText: 'Choose',
		    preferredFormat: 'rgb',
		    show: function ( color ) {

		        $( '.sp-button-container .sp-default' ).remove();
		        $( '.sp-button-container' ).prepend( '<a class="sp-default" href="#">Default</a>' );
		        
		        var _this           = $(this);
		        var color_default   = color ? ( color.getAlpha() == 1 ? color.toHexString() : color.toRgbString() ) : '';
		        var container       = _this.parents('.wr-hb-colors-control');

		        $('.sp-default').off('click').on('click', function( event ) {;
		            event.preventDefault();
		            _this.spectrum( 'set', color_default );
		            _this.siblings('.show-color').text( color_default );
		            _this.val( color_default ).trigger('blur');
		            $('.sp-container:visible').find('.sp-input').val( color_default );
		        });

		        $('.sp-clear').off('click').on('click', function( event ) {;
		            event.preventDefault();
		            _this.spectrum( 'set', '' );
		            _this.siblings('.show-color').text( '' );
		            _this.val( '' ).trigger('blur');
		            $('.sp-container:visible').find('.sp-input').val( '' );
		        });

		        $('.sp-container:visible').find('.sp-input').val( color_default );
		    },
		    move: function ( color ) {
		    	if( ! color )
		    		return;

	            var val = color.getAlpha() == 1 ? color.toHexString() : color.toRgbString();
	            var _this = $(this);

	            _this.siblings('.show-color').text(val);
	            $('.sp-container:visible').find('.sp-input').val(val);
	            _this.val(val).trigger('change');
		    },
		    change: function( color ) {
		    	if( ! color )
		    		return;

	            var val = color.getAlpha() == 1 ? color.toHexString() : color.toRgbString();
	            var _this = $(this);

	            _this.siblings('.show-color').text(val);
	            _this.val(val).trigger('change');
	            $('.sp-container:visible').find('.sp-input').val(val);
		    }
		});
	}

	function add_button_ajax(){
		$( document ).ajaxComplete( function( event, xhr, settings ) {
			var url          = settings.url;
			var data_request = ( typeof settings.data != 'undefined' ) ? settings.data : '';

			if ( data_request.search( 'action=add-menu-item' ) != -1 ) {
				// Render button
				button_show_modal( true );
			}
		} );
	}

	$.WrThemeMegamenu['list_row'] = [ 
		'1/1',

		'1/2 + 1/2',
		'2/3 + 1/3',
		'1/3 + 2/3',
		'1/4 + 3/4',
		'3/4 + 1/4',
		'1/5 + 4/5',
		'4/5 + 1/5',
		'1/6 + 5/6',
		'5/6 + 1/6',

		'1/3 + 1/3 + 1/3',
		'1/4 + 1/4 + 2/4',
		'2/4 + 1/4 + 1/4',
		'1/5 + 1/5 + 3/5',
		'3/5 + 1/5 + 1/5',
		'1/6 + 1/6 + 4/6',
		'4/6 + 1/6 + 1/6',

		'1/4 + 1/4 + 1/4 + 1/4',
		'1/5 + 1/5 + 1/5 + 2/5',
		'2/5 + 1/5 + 1/5 + 1/5',
		'1/6 + 1/6 + 1/6 + 3/6',
		'3/6 + 1/6 + 1/6 + 1/6',

		'1/5 + 1/5 + 1/5 + 1/5 + 1/5',
		'1/6 + 1/6 + 1/6 + 1/6 + 2/6',
		'2/6 + 1/6 + 1/6 + 1/6 + 1/6',

		'1/6 + 1/6 + 1/6 + 1/6 + 1/6 + 1/6'
	];

	$.WrThemeMegamenu.data_icon = {
		'font_awesome' : { "fa fa-american-sign-language-interpreting":"american-sign-language-interpreting","fa fa-asl-interpreting":"asl-interpreting","fa fa-assistive-listening-systems":"assistive-listening-systems","fa fa-audio-description":"audio-description","fa fa-blind":"blind","fa fa-braille":"braille","fa fa-deaf":"deaf","fa fa-deafness":"deafness","fa fa-envira":"envira","fa fa-gitlab":"gitlab","fa fa-glide":"glide","fa fa-glide-g":"glide-g","fa fa-hard-of-hearing":"hard-of-hearing","fa fa-low-vision":"low-vision","fa fa-question-circle-o":"question-circle-o","fa fa-sign-language":"sign-language","fa fa-signing":"signing","fa fa-snapchat":"snapchat","fa fa-snapchat-ghost":"snapchat-ghost","fa fa-snapchat-square":"snapchat-square","fa fa-universal-access":"universal-access","fa fa-viadeo":"viadeo","fa fa-viadeo-square":"viadeo-square","fa fa-volume-control-phone":"volume-control-phone","fa fa-wheelchair-alt":"wheelchair-alt","fa fa-wpbeginner":"wpbeginner","fa fa-wpforms":"wpforms","fa fa-adjust":"adjust","fa fa-anchor":"anchor","fa fa-archive":"archive","fa fa-area-chart":"area-chart","fa fa-arrows":"arrows","fa fa-arrows-h":"arrows-h","fa fa-arrows-v":"arrows-v","fa fa-asterisk":"asterisk","fa fa-at":"at","fa fa-automobile":"automobile","fa fa-balance-scale":"balance-scale","fa fa-ban":"ban","fa fa-bank":"bank","fa fa-bar-chart":"bar-chart","fa fa-bar-chart-o":"bar-chart-o","fa fa-barcode":"barcode","fa fa-bars":"bars","fa fa-battery-0":"battery-0","fa fa-battery-1":"battery-1","fa fa-battery-2":"battery-2","fa fa-battery-3":"battery-3","fa fa-battery-4":"battery-4","fa fa-battery-empty":"battery-empty","fa fa-battery-full":"battery-full","fa fa-battery-half":"battery-half","fa fa-battery-quarter":"battery-quarter","fa fa-battery-three-quarters":"battery-three-quarters","fa fa-bed":"bed","fa fa-beer":"beer","fa fa-bell":"bell","fa fa-bell-o":"bell-o","fa fa-bell-slash":"bell-slash","fa fa-bell-slash-o":"bell-slash-o","fa fa-bicycle":"bicycle","fa fa-binoculars":"binoculars","fa fa-birthday-cake":"birthday-cake","fa fa-bluetooth":"bluetooth","fa fa-bluetooth-b":"bluetooth-b","fa fa-bolt":"bolt","fa fa-bomb":"bomb","fa fa-book":"book","fa fa-bookmark":"bookmark","fa fa-bookmark-o":"bookmark-o","fa fa-briefcase":"briefcase","fa fa-bug":"bug","fa fa-building":"building","fa fa-building-o":"building-o","fa fa-bullhorn":"bullhorn","fa fa-bullseye":"bullseye","fa fa-bus":"bus","fa fa-cab":"cab","fa fa-calculator":"calculator","fa fa-calendar":"calendar","fa fa-calendar-check-o":"calendar-check-o","fa fa-calendar-minus-o":"calendar-minus-o","fa fa-calendar-o":"calendar-o","fa fa-calendar-plus-o":"calendar-plus-o","fa fa-calendar-times-o":"calendar-times-o","fa fa-camera":"camera","fa fa-camera-retro":"camera-retro","fa fa-car":"car","fa fa-caret-square-o-down":"caret-square-o-down","fa fa-caret-square-o-left":"caret-square-o-left","fa fa-caret-square-o-right":"caret-square-o-right","fa fa-caret-square-o-up":"caret-square-o-up","fa fa-cart-arrow-down":"cart-arrow-down","fa fa-cart-plus":"cart-plus","fa fa-cc":"cc","fa fa-certificate":"certificate","fa fa-check":"check","fa fa-check-circle":"check-circle","fa fa-check-circle-o":"check-circle-o","fa fa-check-square":"check-square","fa fa-check-square-o":"check-square-o","fa fa-child":"child","fa fa-circle":"circle","fa fa-circle-o":"circle-o","fa fa-circle-o-notch":"circle-o-notch","fa fa-circle-thin":"circle-thin","fa fa-clock-o":"clock-o","fa fa-clone":"clone","fa fa-close":"close","fa fa-cloud":"cloud","fa fa-cloud-download":"cloud-download","fa fa-cloud-upload":"cloud-upload","fa fa-code":"code","fa fa-code-fork":"code-fork","fa fa-coffee":"coffee","fa fa-cog":"cog","fa fa-cogs":"cogs","fa fa-comment":"comment","fa fa-comment-o":"comment-o","fa fa-commenting":"commenting","fa fa-commenting-o":"commenting-o","fa fa-comments":"comments","fa fa-comments-o":"comments-o","fa fa-compass":"compass","fa fa-copyright":"copyright","fa fa-creative-commons":"creative-commons","fa fa-credit-card":"credit-card","fa fa-credit-card-alt":"credit-card-alt","fa fa-crop":"crop","fa fa-crosshairs":"crosshairs","fa fa-cube":"cube","fa fa-cubes":"cubes","fa fa-cutlery":"cutlery","fa fa-dashboard":"dashboard","fa fa-database":"database","fa fa-desktop":"desktop","fa fa-diamond":"diamond","fa fa-dot-circle-o":"dot-circle-o","fa fa-download":"download","fa fa-edit":"edit","fa fa-ellipsis-h":"ellipsis-h","fa fa-ellipsis-v":"ellipsis-v","fa fa-envelope":"envelope","fa fa-envelope-o":"envelope-o","fa fa-envelope-square":"envelope-square","fa fa-eraser":"eraser","fa fa-exchange":"exchange","fa fa-exclamation":"exclamation","fa fa-exclamation-circle":"exclamation-circle","fa fa-exclamation-triangle":"exclamation-triangle","fa fa-external-link":"external-link","fa fa-external-link-square":"external-link-square","fa fa-eye":"eye","fa fa-eye-slash":"eye-slash","fa fa-eyedropper":"eyedropper","fa fa-fax":"fax","fa fa-feed":"feed","fa fa-female":"female","fa fa-fighter-jet":"fighter-jet","fa fa-file-archive-o":"file-archive-o","fa fa-file-audio-o":"file-audio-o","fa fa-file-code-o":"file-code-o","fa fa-file-excel-o":"file-excel-o","fa fa-file-image-o":"file-image-o","fa fa-file-movie-o":"file-movie-o","fa fa-file-pdf-o":"file-pdf-o","fa fa-file-photo-o":"file-photo-o","fa fa-file-picture-o":"file-picture-o","fa fa-file-powerpoint-o":"file-powerpoint-o","fa fa-file-sound-o":"file-sound-o","fa fa-file-video-o":"file-video-o","fa fa-file-word-o":"file-word-o","fa fa-file-zip-o":"file-zip-o","fa fa-film":"film","fa fa-filter":"filter","fa fa-fire":"fire","fa fa-fire-extinguisher":"fire-extinguisher","fa fa-flag":"flag","fa fa-flag-checkered":"flag-checkered","fa fa-flag-o":"flag-o","fa fa-flash":"flash","fa fa-flask":"flask","fa fa-folder":"folder","fa fa-folder-o":"folder-o","fa fa-folder-open":"folder-open","fa fa-folder-open-o":"folder-open-o","fa fa-frown-o":"frown-o","fa fa-futbol-o":"futbol-o","fa fa-gamepad":"gamepad","fa fa-gavel":"gavel","fa fa-gear":"gear","fa fa-gears":"gears","fa fa-gift":"gift","fa fa-glass":"glass","fa fa-globe":"globe","fa fa-graduation-cap":"graduation-cap","fa fa-group":"group","fa fa-hand-grab-o":"hand-grab-o","fa fa-hand-lizard-o":"hand-lizard-o","fa fa-hand-paper-o":"hand-paper-o","fa fa-hand-peace-o":"hand-peace-o","fa fa-hand-pointer-o":"hand-pointer-o","fa fa-hand-rock-o":"hand-rock-o","fa fa-hand-scissors-o":"hand-scissors-o","fa fa-hand-spock-o":"hand-spock-o","fa fa-hand-stop-o":"hand-stop-o","fa fa-hashtag":"hashtag","fa fa-hdd-o":"hdd-o","fa fa-headphones":"headphones","fa fa-heart":"heart","fa fa-heart-o":"heart-o","fa fa-heartbeat":"heartbeat","fa fa-history":"history","fa fa-home":"home","fa fa-hotel":"hotel","fa fa-hourglass":"hourglass","fa fa-hourglass-1":"hourglass-1","fa fa-hourglass-2":"hourglass-2","fa fa-hourglass-3":"hourglass-3","fa fa-hourglass-end":"hourglass-end","fa fa-hourglass-half":"hourglass-half","fa fa-hourglass-o":"hourglass-o","fa fa-hourglass-start":"hourglass-start","fa fa-i-cursor":"i-cursor","fa fa-image":"image","fa fa-inbox":"inbox","fa fa-industry":"industry","fa fa-info":"info","fa fa-info-circle":"info-circle","fa fa-institution":"institution","fa fa-key":"key","fa fa-keyboard-o":"keyboard-o","fa fa-language":"language","fa fa-laptop":"laptop","fa fa-leaf":"leaf","fa fa-legal":"legal","fa fa-lemon-o":"lemon-o","fa fa-level-down":"level-down","fa fa-level-up":"level-up","fa fa-life-bouy":"life-bouy","fa fa-life-buoy":"life-buoy","fa fa-life-ring":"life-ring","fa fa-life-saver":"life-saver","fa fa-lightbulb-o":"lightbulb-o","fa fa-line-chart":"line-chart","fa fa-location-arrow":"location-arrow","fa fa-lock":"lock","fa fa-magic":"magic","fa fa-magnet":"magnet","fa fa-mail-forward":"mail-forward","fa fa-mail-reply":"mail-reply","fa fa-mail-reply-all":"mail-reply-all","fa fa-male":"male","fa fa-map":"map","fa fa-map-marker":"map-marker","fa fa-map-o":"map-o","fa fa-map-pin":"map-pin","fa fa-map-signs":"map-signs","fa fa-meh-o":"meh-o","fa fa-microphone":"microphone","fa fa-microphone-slash":"microphone-slash","fa fa-minus":"minus","fa fa-minus-circle":"minus-circle","fa fa-minus-square":"minus-square","fa fa-minus-square-o":"minus-square-o","fa fa-mobile":"mobile","fa fa-mobile-phone":"mobile-phone","fa fa-money":"money","fa fa-moon-o":"moon-o","fa fa-mortar-board":"mortar-board","fa fa-motorcycle":"motorcycle","fa fa-mouse-pointer":"mouse-pointer","fa fa-music":"music","fa fa-navicon":"navicon","fa fa-newspaper-o":"newspaper-o","fa fa-object-group":"object-group","fa fa-object-ungroup":"object-ungroup","fa fa-paint-brush":"paint-brush","fa fa-paper-plane":"paper-plane","fa fa-paper-plane-o":"paper-plane-o","fa fa-paw":"paw","fa fa-pencil":"pencil","fa fa-pencil-square":"pencil-square","fa fa-pencil-square-o":"pencil-square-o","fa fa-percent":"percent","fa fa-phone":"phone","fa fa-phone-square":"phone-square","fa fa-photo":"photo","fa fa-picture-o":"picture-o","fa fa-pie-chart":"pie-chart","fa fa-plane":"plane","fa fa-plug":"plug","fa fa-plus":"plus","fa fa-plus-circle":"plus-circle","fa fa-plus-square":"plus-square","fa fa-plus-square-o":"plus-square-o","fa fa-power-off":"power-off","fa fa-print":"print","fa fa-puzzle-piece":"puzzle-piece","fa fa-qrcode":"qrcode","fa fa-question":"question","fa fa-question-circle":"question-circle","fa fa-quote-left":"quote-left","fa fa-quote-right":"quote-right","fa fa-random":"random","fa fa-recycle":"recycle","fa fa-refresh":"refresh","fa fa-registered":"registered","fa fa-remove":"remove","fa fa-reorder":"reorder","fa fa-reply":"reply","fa fa-reply-all":"reply-all","fa fa-retweet":"retweet","fa fa-road":"road","fa fa-rocket":"rocket","fa fa-rss":"rss","fa fa-rss-square":"rss-square","fa fa-search":"search","fa fa-search-minus":"search-minus","fa fa-search-plus":"search-plus","fa fa-send":"send","fa fa-send-o":"send-o","fa fa-server":"server","fa fa-share":"share","fa fa-share-alt":"share-alt","fa fa-share-alt-square":"share-alt-square","fa fa-share-square":"share-square","fa fa-share-square-o":"share-square-o","fa fa-shield":"shield","fa fa-ship":"ship","fa fa-shopping-bag":"shopping-bag","fa fa-shopping-basket":"shopping-basket","fa fa-shopping-cart":"shopping-cart","fa fa-sign-in":"sign-in","fa fa-sign-out":"sign-out","fa fa-signal":"signal","fa fa-sitemap":"sitemap","fa fa-sliders":"sliders","fa fa-smile-o":"smile-o","fa fa-soccer-ball-o":"soccer-ball-o","fa fa-sort":"sort","fa fa-sort-alpha-asc":"sort-alpha-asc","fa fa-sort-alpha-desc":"sort-alpha-desc","fa fa-sort-amount-asc":"sort-amount-asc","fa fa-sort-amount-desc":"sort-amount-desc","fa fa-sort-asc":"sort-asc","fa fa-sort-desc":"sort-desc","fa fa-sort-down":"sort-down","fa fa-sort-numeric-asc":"sort-numeric-asc","fa fa-sort-numeric-desc":"sort-numeric-desc","fa fa-sort-up":"sort-up","fa fa-space-shuttle":"space-shuttle","fa fa-spinner":"spinner","fa fa-spoon":"spoon","fa fa-square":"square","fa fa-square-o":"square-o","fa fa-star":"star","fa fa-star-half":"star-half","fa fa-star-half-empty":"star-half-empty","fa fa-star-half-full":"star-half-full","fa fa-star-half-o":"star-half-o","fa fa-star-o":"star-o","fa fa-sticky-note":"sticky-note","fa fa-sticky-note-o":"sticky-note-o","fa fa-street-view":"street-view","fa fa-suitcase":"suitcase","fa fa-sun-o":"sun-o","fa fa-support":"support","fa fa-tablet":"tablet","fa fa-tachometer":"tachometer","fa fa-tag":"tag","fa fa-tags":"tags","fa fa-tasks":"tasks","fa fa-taxi":"taxi","fa fa-television":"television","fa fa-terminal":"terminal","fa fa-thumb-tack":"thumb-tack","fa fa-thumbs-down":"thumbs-down","fa fa-thumbs-o-down":"thumbs-o-down","fa fa-thumbs-o-up":"thumbs-o-up","fa fa-thumbs-up":"thumbs-up","fa fa-ticket":"ticket","fa fa-times":"times","fa fa-times-circle":"times-circle","fa fa-times-circle-o":"times-circle-o","fa fa-tint":"tint","fa fa-toggle-down":"toggle-down","fa fa-toggle-left":"toggle-left","fa fa-toggle-off":"toggle-off","fa fa-toggle-on":"toggle-on","fa fa-toggle-right":"toggle-right","fa fa-toggle-up":"toggle-up","fa fa-trademark":"trademark","fa fa-trash":"trash","fa fa-trash-o":"trash-o","fa fa-tree":"tree","fa fa-trophy":"trophy","fa fa-truck":"truck","fa fa-tty":"tty","fa fa-tv":"tv","fa fa-umbrella":"umbrella","fa fa-university":"university","fa fa-unlock":"unlock","fa fa-unlock-alt":"unlock-alt","fa fa-unsorted":"unsorted","fa fa-upload":"upload","fa fa-user":"user","fa fa-user-plus":"user-plus","fa fa-user-secret":"user-secret","fa fa-user-times":"user-times","fa fa-users":"users","fa fa-video-camera":"video-camera","fa fa-volume-down":"volume-down","fa fa-volume-off":"volume-off","fa fa-volume-up":"volume-up","fa fa-warning":"warning","fa fa-wheelchair":"wheelchair","fa fa-wifi":"wifi","fa fa-wrench":"wrench","fa fa-hand-o-down":"hand-o-down","fa fa-hand-o-left":"hand-o-left","fa fa-hand-o-right":"hand-o-right","fa fa-hand-o-up":"hand-o-up","fa fa-ambulance":"ambulance","fa fa-subway":"subway","fa fa-train":"train","fa fa-genderless":"genderless","fa fa-intersex":"intersex","fa fa-mars":"mars","fa fa-mars-double":"mars-double","fa fa-mars-stroke":"mars-stroke","fa fa-mars-stroke-h":"mars-stroke-h","fa fa-mars-stroke-v":"mars-stroke-v","fa fa-mercury":"mercury","fa fa-neuter":"neuter","fa fa-transgender":"transgender","fa fa-transgender-alt":"transgender-alt","fa fa-venus":"venus","fa fa-venus-double":"venus-double","fa fa-venus-mars":"venus-mars","fa fa-file":"file","fa fa-file-o":"file-o","fa fa-file-text":"file-text","fa fa-file-text-o":"file-text-o","fa fa-cc-amex":"cc-amex","fa fa-cc-diners-club":"cc-diners-club","fa fa-cc-discover":"cc-discover","fa fa-cc-jcb":"cc-jcb","fa fa-cc-mastercard":"cc-mastercard","fa fa-cc-paypal":"cc-paypal","fa fa-cc-stripe":"cc-stripe","fa fa-cc-visa":"cc-visa","fa fa-google-wallet":"google-wallet","fa fa-paypal":"paypal","fa fa-bitcoin":"bitcoin","fa fa-btc":"btc","fa fa-cny":"cny","fa fa-dollar":"dollar","fa fa-eur":"eur","fa fa-euro":"euro","fa fa-gbp":"gbp","fa fa-gg":"gg","fa fa-gg-circle":"gg-circle","fa fa-ils":"ils","fa fa-inr":"inr","fa fa-jpy":"jpy","fa fa-krw":"krw","fa fa-rmb":"rmb","fa fa-rouble":"rouble","fa fa-rub":"rub","fa fa-ruble":"ruble","fa fa-rupee":"rupee","fa fa-shekel":"shekel","fa fa-sheqel":"sheqel","fa fa-try":"try","fa fa-turkish-lira":"turkish-lira","fa fa-usd":"usd","fa fa-won":"won","fa fa-yen":"yen","fa fa-align-center":"align-center","fa fa-align-justify":"align-justify","fa fa-align-left":"align-left","fa fa-align-right":"align-right","fa fa-bold":"bold","fa fa-chain":"chain","fa fa-chain-broken":"chain-broken","fa fa-clipboard":"clipboard","fa fa-columns":"columns","fa fa-copy":"copy","fa fa-cut":"cut","fa fa-dedent":"dedent","fa fa-files-o":"files-o","fa fa-floppy-o":"floppy-o","fa fa-font":"font","fa fa-header":"header","fa fa-indent":"indent","fa fa-italic":"italic","fa fa-link":"link","fa fa-list":"list","fa fa-list-alt":"list-alt","fa fa-list-ol":"list-ol","fa fa-list-ul":"list-ul","fa fa-outdent":"outdent","fa fa-paperclip":"paperclip","fa fa-paragraph":"paragraph","fa fa-paste":"paste","fa fa-repeat":"repeat","fa fa-rotate-left":"rotate-left","fa fa-rotate-right":"rotate-right","fa fa-save":"save","fa fa-scissors":"scissors","fa fa-strikethrough":"strikethrough","fa fa-subscript":"subscript","fa fa-superscript":"superscript","fa fa-table":"table","fa fa-text-height":"text-height","fa fa-text-width":"text-width","fa fa-th":"th","fa fa-th-large":"th-large","fa fa-th-list":"th-list","fa fa-underline":"underline","fa fa-undo":"undo","fa fa-unlink":"unlink","fa fa-angle-double-down":"angle-double-down","fa fa-angle-double-left":"angle-double-left","fa fa-angle-double-right":"angle-double-right","fa fa-angle-double-up":"angle-double-up","fa fa-angle-down":"angle-down","fa fa-angle-left":"angle-left","fa fa-angle-right":"angle-right","fa fa-angle-up":"angle-up","fa fa-arrow-circle-down":"arrow-circle-down","fa fa-arrow-circle-left":"arrow-circle-left","fa fa-arrow-circle-o-down":"arrow-circle-o-down","fa fa-arrow-circle-o-left":"arrow-circle-o-left","fa fa-arrow-circle-o-right":"arrow-circle-o-right","fa fa-arrow-circle-o-up":"arrow-circle-o-up","fa fa-arrow-circle-right":"arrow-circle-right","fa fa-arrow-circle-up":"arrow-circle-up","fa fa-arrow-down":"arrow-down","fa fa-arrow-left":"arrow-left","fa fa-arrow-right":"arrow-right","fa fa-arrow-up":"arrow-up","fa fa-arrows-alt":"arrows-alt","fa fa-caret-down":"caret-down","fa fa-caret-left":"caret-left","fa fa-caret-right":"caret-right","fa fa-caret-up":"caret-up","fa fa-chevron-circle-down":"chevron-circle-down","fa fa-chevron-circle-left":"chevron-circle-left","fa fa-chevron-circle-right":"chevron-circle-right","fa fa-chevron-circle-up":"chevron-circle-up","fa fa-chevron-down":"chevron-down","fa fa-chevron-left":"chevron-left","fa fa-chevron-right":"chevron-right","fa fa-chevron-up":"chevron-up","fa fa-long-arrow-down":"long-arrow-down","fa fa-long-arrow-left":"long-arrow-left","fa fa-long-arrow-right":"long-arrow-right","fa fa-long-arrow-up":"long-arrow-up","fa fa-backward":"backward","fa fa-compress":"compress","fa fa-eject":"eject","fa fa-expand":"expand","fa fa-fast-backward":"fast-backward","fa fa-fast-forward":"fast-forward","fa fa-forward":"forward","fa fa-pause":"pause","fa fa-pause-circle":"pause-circle","fa fa-pause-circle-o":"pause-circle-o","fa fa-play":"play","fa fa-play-circle":"play-circle","fa fa-play-circle-o":"play-circle-o","fa fa-step-backward":"step-backward","fa fa-step-forward":"step-forward","fa fa-stop":"stop","fa fa-stop-circle":"stop-circle","fa fa-stop-circle-o":"stop-circle-o","fa fa-youtube-play":"youtube-play","fa fa-500px":"500px","fa fa-adn":"adn","fa fa-amazon":"amazon","fa fa-android":"android","fa fa-angellist":"angellist","fa fa-apple":"apple","fa fa-behance":"behance","fa fa-behance-square":"behance-square","fa fa-bitbucket":"bitbucket","fa fa-bitbucket-square":"bitbucket-square","fa fa-black-tie":"black-tie","fa fa-buysellads":"buysellads","fa fa-chrome":"chrome","fa fa-codepen":"codepen","fa fa-codiepie":"codiepie","fa fa-connectdevelop":"connectdevelop","fa fa-contao":"contao","fa fa-css3":"css3","fa fa-dashcube":"dashcube","fa fa-delicious":"delicious","fa fa-deviantart":"deviantart","fa fa-digg":"digg","fa fa-dribbble":"dribbble","fa fa-dropbox":"dropbox","fa fa-drupal":"drupal","fa fa-edge":"edge","fa fa-empire":"empire","fa fa-expeditedssl":"expeditedssl","fa fa-facebook":"facebook","fa fa-facebook-f":"facebook-f","fa fa-facebook-official":"facebook-official","fa fa-facebook-square":"facebook-square","fa fa-firefox":"firefox","fa fa-flickr":"flickr","fa fa-fonticons":"fonticons","fa fa-fort-awesome":"fort-awesome","fa fa-forumbee":"forumbee","fa fa-foursquare":"foursquare","fa fa-ge":"ge","fa fa-get-pocket":"get-pocket","fa fa-git":"git","fa fa-git-square":"git-square","fa fa-github":"github","fa fa-github-alt":"github-alt","fa fa-github-square":"github-square","fa fa-gittip":"gittip","fa fa-google":"google","fa fa-google-plus":"google-plus","fa fa-google-plus-square":"google-plus-square","fa fa-gratipay":"gratipay","fa fa-hacker-news":"hacker-news","fa fa-houzz":"houzz","fa fa-html5":"html5","fa fa-instagram":"instagram","fa fa-internet-explorer":"internet-explorer","fa fa-ioxhost":"ioxhost","fa fa-joomla":"joomla","fa fa-jsfiddle":"jsfiddle","fa fa-lastfm":"lastfm","fa fa-lastfm-square":"lastfm-square","fa fa-leanpub":"leanpub","fa fa-linkedin":"linkedin","fa fa-linkedin-square":"linkedin-square","fa fa-linux":"linux","fa fa-maxcdn":"maxcdn","fa fa-meanpath":"meanpath","fa fa-medium":"medium","fa fa-mixcloud":"mixcloud","fa fa-modx":"modx","fa fa-odnoklassniki":"odnoklassniki","fa fa-odnoklassniki-square":"odnoklassniki-square","fa fa-opencart":"opencart","fa fa-openid":"openid","fa fa-opera":"opera","fa fa-optin-monster":"optin-monster","fa fa-pagelines":"pagelines","fa fa-pied-piper":"pied-piper","fa fa-pied-piper-alt":"pied-piper-alt","fa fa-pinterest":"pinterest","fa fa-pinterest-p":"pinterest-p","fa fa-pinterest-square":"pinterest-square","fa fa-product-hunt":"product-hunt","fa fa-qq":"qq","fa fa-ra":"ra","fa fa-rebel":"rebel","fa fa-reddit":"reddit","fa fa-reddit-alien":"reddit-alien","fa fa-reddit-square":"reddit-square","fa fa-renren":"renren","fa fa-safari":"safari","fa fa-scribd":"scribd","fa fa-sellsy":"sellsy","fa fa-shirtsinbulk":"shirtsinbulk","fa fa-simplybuilt":"simplybuilt","fa fa-skyatlas":"skyatlas","fa fa-skype":"skype","fa fa-slack":"slack","fa fa-slideshare":"slideshare","fa fa-soundcloud":"soundcloud","fa fa-spotify":"spotify","fa fa-stack-exchange":"stack-exchange","fa fa-stack-overflow":"stack-overflow","fa fa-steam":"steam","fa fa-steam-square":"steam-square","fa fa-stumbleupon":"stumbleupon","fa fa-stumbleupon-circle":"stumbleupon-circle","fa fa-tencent-weibo":"tencent-weibo","fa fa-trello":"trello","fa fa-tripadvisor":"tripadvisor","fa fa-tumblr":"tumblr","fa fa-tumblr-square":"tumblr-square","fa fa-twitch":"twitch","fa fa-twitter":"twitter","fa fa-twitter-square":"twitter-square","fa fa-usb":"usb","fa fa-viacoin":"viacoin","fa fa-vimeo":"vimeo","fa fa-vimeo-square":"vimeo-square","fa fa-vine":"vine","fa fa-vk":"vk","fa fa-wechat":"wechat","fa fa-weibo":"weibo","fa fa-weixin":"weixin","fa fa-whatsapp":"whatsapp","fa fa-wikipedia-w":"wikipedia-w","fa fa-windows":"windows","fa fa-wordpress":"wordpress","fa fa-xing":"xing","fa fa-xing-square":"xing-square","fa fa-y-combinator":"y-combinator","fa fa-y-combinator-square":"y-combinator-square","fa fa-yahoo":"yahoo","fa fa-yc":"yc","fa fa-yc-square":"yc-square","fa fa-yelp":"yelp","fa fa-youtube":"youtube","fa fa-youtube-square":"youtube-square","fa fa-h-square":"h-square","fa fa-hospital-o":"hospital-o","fa fa-medkit":"medkit","fa fa-stethoscope":"stethoscope","fa fa-user-md":"user-md" },
		'font_icomoon' : {},
		'dashicons' : {}
	};

	// Call function
	button_show_modal( false );
	list_event();
	add_button_ajax();
	button_expand();
	button_expand_collapse_all();

})( jQuery );