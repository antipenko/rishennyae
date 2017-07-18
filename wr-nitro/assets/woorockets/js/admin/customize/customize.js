( function( $ ) {
	$( document ).ready( function() {
		// Clear 'src' attribute of all 'img' tag to lazy load later.
		$( '#customize-theme-controls img' ).each( function( i, e ) {
			$( e ).data( 'src', $( e ).attr( 'src' ) ).removeAttr( 'src' );
		} );

		function init_customize_controls() {
			// Make sure WP Theme Customize is finished loading.
			if ( !$( '#customize-theme-controls > ul' ).children( 'li' ).length ) {
				return setTimeout( init_customize_controls, 100 );
			}

			// Track the value of WooCommerce Catalog Mode.
			var wc_archive_catalog_mode = wp.customize.control( 'wc_archive_catalog_mode' ).setting;

			function toogle_woocommerce_catelog_mode() {
				if ( wc_archive_catalog_mode.get() === true ) {
					$( '#accordion-section-wc_cart, #accordion-section-wc_checkout, #accordion-section-wc_thankyou, #accordion-section-wc_buynow' ).each( function( i, e ) {
						if ( !$( e ).children( '.wr-mask' ).length ) {
							// Mask the panel title.
							var mask = $( '<div class="wr-mask wr-mask-big has-tip" />' ).append( $( '<span class="dashicons dashicons-warning"></span>' ).attr( 'title', wr_customize_messages.wc_catalog_mode_enabled_warning ) ).tooltip( {
								track: true
							} );

							$( e ).css( 'position', 'relative' ).append( mask );
						}
					} );
				} else {
					$( '#accordion-section-wc_cart, #accordion-section-wc_checkout, #accordion-section-wc_thankyou, #accordion-section-wc_buynow' ).find( '.wr-mask' ).remove();
				}
			}

			wc_archive_catalog_mode.bind( 'change', function( value ) {
				toogle_woocommerce_catelog_mode();
			} );

			toogle_woocommerce_catelog_mode();

			// Convert customize control descriptions to tooltips.
			function create_tooltip( e ) {
				$( e ).removeClass( 'description customize-control-description' ).addClass( 'fa fa-question-circle has-tip' ).attr( 'title', $( e ).text() ).css( {
				    'position': 'absolute',
				    'top': '0',
				    'right': '0'
				} ).empty().parent().addClass( 'clearfix' );

				$( e ).tooltip( {
					track: true
				} );
			}

			$( '.customize-control-description' ).each( function( i, e ) {
				create_tooltip( e );
			} );

			$( '.wr-radio-image' ).tooltip( {
				track: true
			} );

			// Track click on Media control buttons to re-create tooltip.
			function recreate_tooltip( control ) {
				if ( control.find( '.has-tip' ).length || !control.find( '.customize-control-description' ).length ) {
					control[ 0 ].wr_recreate_tooltip_timer = setTimeout( function() {
						recreate_tooltip( control );
					}, 100 );
				} else {
					create_tooltip( control.find( '.customize-control-description' ) );

					if ( control[ 0 ].wr_recreate_tooltip_timer ) {
						clearTimeout( control[ 0 ].wr_recreate_tooltip_timer );
					}
				}
			}

			$( '#customize-control-site_icon' ).on( 'click', '.actions button', function() {
				recreate_tooltip( $( '#customize-control-site_icon' ) );
			} );

			// Init controls dependencies.
			if ( window.WR_Nitro_Customize_Controls_Dependencies ) {
				function check_control_dependency( id, track_change ) {
					var control, visible, action, logical_operator, condition, dependency, values;

					// Get the control element.
					control = $( '#customize-control-' + id );

					// Get action to do if dependency checking fails.
					action = 'hide';

					if ( WR_Nitro_Customize_Controls_Dependencies[ id ].dependency_failed_action ) {
						action = WR_Nitro_Customize_Controls_Dependencies[ id ].dependency_failed_action;
					}

					// Get logical operator.
					logical_operator = 'AND';

					if ( WR_Nitro_Customize_Controls_Dependencies[ id ].logical_operator ) {
						switch ( WR_Nitro_Customize_Controls_Dependencies[ id ].logical_operator.toUpperCase() ) {
							case '|':
							case '||':
							case 'OR':
								logical_operator = 'OR';
							break;

							default:
								logical_operator = 'AND';
							break;
						}
					}

					// Get the other control element.
					visible = logical_operator == 'AND' ? true : false;

					for ( var i in WR_Nitro_Customize_Controls_Dependencies[ id ] ) {
						if ( i == 'logical_operator' || i == 'dependency_failed_action' ) {
							continue;
						}

						// Get dependency condition.
						condition = WR_Nitro_Customize_Controls_Dependencies[ id ][ i ].split( /\s+/ );

						if ( condition.length < 3 ) {
							continue;
						}

						// Look for dependency model.
						dependency = null;

						wp.customize.control.each( function( model, name ) {
							if ( !dependency && name == condition[ 0 ] ) {
								dependency = model;
							}
						} );

						if ( !dependency ) {
							continue;
						}

						// Get dependency value
						value = dependency.setting.get();

						if ( value === true ) {
							value = 1;
						} else if ( value === false ) {
							value = 0;
						}

						if ( typeof value == 'string' && value.match( /^\d+$/ ) ) {
							value = parseInt( value );
						}

						// Check the value of the other control element against required condition.
						if ( values = condition[ 2 ].match( /^['"](.*)['"]$/ ) ) {
							values = values[ 1 ].split( '|' );

							for ( var i in values ) {
								if ( typeof values[ i ] == 'string' && values[ i ].match( /^\d+$/ ) ) {
									values[ i ] = parseInt( values[ i ] );
								}
							}
						} else if ( condition[ 2 ].match( /^\d+$/ ) ) {
							values = [ parseInt( condition[ 2 ] ) ];
						} else {
							values = [ condition[ 2 ] ];
						}

						switch ( condition[ 1 ] ) {
							case '=':
							case '==':
								if ( logical_operator == 'AND' && $.inArray( value, values ) < 0 ) {
									visible = visible && false;
								} else if ( logical_operator == 'OR' && $.inArray( value, values ) > -1 ) {
									visible = visible || true;
								}
							break;

							case '!=':
								if ( logical_operator == 'AND' && $.inArray( value, values ) > -1 ) {
									visible = visible && false;
								} else if ( logical_operator == 'OR' && $.inArray( value, values ) < 0 ) {
									visible = visible || true;
								}
							break;
						}

						// If this is the first check, track the other control element for value change.
						if ( track_change ) {
							dependency.setting.bind( 'change', function( value ) {
								check_control_dependency( id );
							} );
						}

						// Otherwise, if the check returns expected result already, just break.
						else {
							// If logical operator is 'AND' and condition checking fails, break early.
							if ( logical_operator == 'AND' && !visible ) {
								break;
							}

							// If logical operator is 'OR' and condition checking succeeds, break early.
							if ( logical_operator == 'OR' && visible ) {
								break;
							}
						}
					}

					// If the required condition is match, show / enable the control element.
					if ( visible ) {
						// Remove mask if has to re-enable the customize control.
						if ( control.children( '.wr-mask' ).length ) {
							control.children( '.wr-mask' ).remove();
						}

						// If the customize control is belongs to the same section as the dependency, show it.
						if ( wp.customize.control( id ).section() == dependency.section() ) {
							control.removeClass( 'hidden' );
						}
					}

					// Otherwise, hide / disable it.
					else {
						// If the customize control is belongs to the same section as the dependency, hide it.
						if ( wp.customize.control( id ).section() == dependency.section() && action == 'hide' ) {
							control.addClass( 'hidden' );
						}

						// Otherwise, if the customize control is visible, create a mask to disable it.
						else if ( ! control.hasClass( 'hidden' ) && ! control.children( '.wr-mask' ).length ) {
							// Create the mask.
							var mask = $( '<div class="wr-mask wr-mask-small has-tip" />' ).attr( 'title', wr_customize_messages.disabled_because_of_dependency );

							control.append( mask );

							mask.tooltip( {
								track: true
							} );
						}
					}
				}

				// Loop thru all control elements that have dependency to check.
				for ( var id in WR_Nitro_Customize_Controls_Dependencies ) {
					check_control_dependency( id, true );
				}
			}

			// Setup live preview support for color profiles.
			var settings = [
				'custom_color',
				'content_body_color',
				'content_meta_color',
				'general_line_color',
				'general_overlay_color',
				'general_fields_bg',
				'wr_general_container_color',
			];

			for ( var i = 0, n = settings.length; i < n; i++ ) {
				wp.customize.control( settings[ i ] ).setting.bind( 'change', function() {
					setTimeout( function() {
						// Get the live preview element of the selected color profile.
						var control = document.querySelector( '#customize-control-color_profile .wr-image-selected .colors-preset' ),
							current = wp.customize.control( 'color_profile' ).setting.get(),
							profile = document.querySelector( '#customize-control-color_profile input[value="' + current + '"]' ),
							preview = profile.previousElementSibling;

						for ( var i = 0, n = settings.length; i < n; i++ ) {
							var color = settings[ i ] == 'content_body_color'
								? wp.customize.control( settings[ i ] ).setting.get().heading_text
								: wp.customize.control( settings[ i ] ).setting.get();

							control.children[ i ].style.backgroundColor = color ? color : 'transparent';
							preview.children[ i ].style.backgroundColor = color ? color : 'transparent';
						}
					}, 100 );
				} );
			}

			// Track ajaxSend event to prevent refreshing preview when changing value of ineffective option.
			$( document ).ajaxSend( function( event, xhr ) {
				// Prevent refreshing the preview iframe if changed option is Expert Mode.
				if ( event.currentTarget.activeElement.getAttribute( 'data-customize-setting-link' ) == 'expert_mode' ) {
					xhr.abort();

					// Check effective panels.
					if ( $( '#customize-preview iframe' )[0].contentWindow.jQuery.check_effective_panels ) {
						$( '#customize-preview iframe' )[0].contentWindow.jQuery.check_effective_panels();
					}
				}

				// Prevent refreshing the preview iframe if changing value of ineffective option.
				if ( $( event.currentTarget.activeElement ).closest( '[id^="accordion-panel-"]' ).data( 'disabled' ) ) {
					xhr.abort();
				}
			} );

			// Override default ThickBox handle.
			$.wr_override_thickbox = function() {
				$( 'a[target="thickbox"]' ).each( function( i, e ) {
					if ( !$( e ).prop( 'wr_override_thickbox' ) ) {
						$( e ).click( function( event ) {
							event.preventDefault();

							// Calculate width and height for ThickBox window.
							var width = $( this ).attr( 'data-width' ), height = $( this ).attr( 'data-height' );

							if ( width.substr( -1 ) == '%' ) {
								width = $( window ).width() * ( parseInt( width ) / 100 );
							}

							if ( height.substr( -1 ) == '%' ) {
								height = $( window ).height() * ( parseInt( height ) / 100 );
							}

							// Finalize the URL for opening ThickBox window.
							var url = $( this ).attr( 'href' ) + ( $( this ).attr( 'href' ).indexOf( '?' ) > -1 ? '&' : '?' ) + 'width=' + width + '&height=' + height;

							tb_show( $( this ).attr( 'title' ), url );

							// Handle window resize event to resize ThickBox window accordingly.
							var self = this,

							resize = function() {
								// Calculate new width and height for ThickBox window.
								var width = $( self ).attr( 'data-width' ), height = $( self ).attr( 'data-height' );

								if ( width.substr( -1 ) == '%' ) {
									width = $( window ).width() * ( parseInt( width ) / 100 );
								}

								if ( height.substr( -1 ) == '%' ) {
									height = $( window ).height() * ( parseInt( height ) / 100 );
								}

								// Update width and height for ThickBox window.
								TB_WIDTH = ( width * 1 ) + 30;
								TB_HEIGHT = ( height * 1 ) + 40;

								ajaxContentW = TB_WIDTH - 30;
								ajaxContentH = TB_HEIGHT - 45;

								$( '#TB_ajaxContent' ).css( {
								    width: ajaxContentW,
								    height: ajaxContentH,
								} );

								$( '#TB_iframeContent' ).css( {
								    width: ajaxContentW + 29,
								    height: ajaxContentH + 17,
								} );

								tb_position();
							}

							$( window ).on( 'resize', resize );

							$( '#TB_closeWindowButton, #TB_overlay' ).click( function() {
								$( window ).off( 'resize', resize );
							} );

							return false;
						} );

						$( e ).prop( 'wr_override_thickbox', true );
					}
				} );
			};

			$.wr_override_thickbox();

			// Define function to load all lazy loaded sources.
			var loaded_sources = {};

			$.wr_load_delayed_sources = function() {
				function load_source( type, src ) {
					var url = type == 'css' ? src : $( src ).data( 'src' ),

					update = function() {
						if ( type == 'css' ) {
							$( 'head' ).append( '<link rel="stylesheet" href="' + src + '" type="text/css" media="all" />' );
						} else {
							$( src ).attr( 'src', $( src ).data( 'src' ) ).removeData( 'src' );
						}
					};

					if ( !loaded_sources[ url ] ) {
						$.ajax( {
						    timeout: 500,
						    url: url,
						    complete: function( response ) {
							    if ( response.status == 200 ) {
								    update();
							    }

							    loaded_sources[ url ] = true;
						    },
						} );
					} else {
						update();
					}
				}

				if ( window.runtime_delayed_sources ) {
					for ( var i in runtime_delayed_sources ) {
						load_source( 'css', runtime_delayed_sources[ i ] );
					}

					delete window.runtime_delayed_sources;
				}

				if ( window.nitro_lazy_load_sources ) {
					for ( var i in nitro_lazy_load_sources ) {
						load_source( 'css', nitro_lazy_load_sources[ i ] );
					}

					delete window.nitro_lazy_load_sources;
				}

				$( '#customize-theme-controls img' ).each( function( i, e ) {
					if ( $( e ).data( 'src' ) ) {
						load_source( 'img', e );
					}
				} );
			}
		}

		// Move to another section.
		function move_to_section() {
			$( '.move-to-section' ).click( function( e ) {
				e.preventDefault();

				var section = $( this ).data( 'section' );

				wp.customize.section( section ).focus();

				$( '.accordion-section-content' ).css( 'margin-top', '0' );
			} );
		}

		// Move mobile device to customize bar
		function add_customize_bar() {
			var bar = $( '<div class="customize-bar"><div class="action"></div></div>' );

			$( '#customize-controls' ).after( bar );
			$( '.devices' ).detach().appendTo( '.customize-bar' );

			$( '.devices' ).children( 'button' ).siblings().removeClass( 'active' );
			$( '.preview-desktop' ).addClass( 'active' );

			$( '.devices' ).children( 'button' ).on( 'click', function() {
				$( this ).addClass( 'active' ).siblings().removeClass( 'active' );
			});
		}

		window.wr_click_outside = function( selector, parent_selector, callback ) {
			$( window ).on( 'mousedown', function clickHandler( e ) {
				var index_selector = $( parent_selector ).index( selector.closest( parent_selector ) );
				var index_current = $( parent_selector ).index( $( e.target ).closest( parent_selector ) );
				if ( index_selector != index_current ) {
					$( window ).off( 'mousedown' );
					callback.call( e );
				}
			} );
		}

		init_customize_controls();
		move_to_section();
		add_customize_bar();
	} );

} )( jQuery );
