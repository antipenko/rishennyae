( function( $ ) {
	$( window ).load( function() {
		// Loop thru all control containers to init.
		$( 'div[id^="wr-' + wr_nitro_customize_backup_restore.type + '-"]' ).each( function() {
			var self = $( this );

			// Get the control ID.
			self.id = self.attr( 'id' ).replace( 'wr-' + wr_nitro_customize_backup_restore.type + '-', '' );

			// Method to display a message box.
			function message( message, type ) {
				// Prepare parameters.
				message = typeof message != 'undefined' ? message : '';
				type    = typeof type    != 'undefined' ? type    : 'success';

				// Remove all previous messages.
				self.find( '.notice' ).remove();

				// Create message box.
				var class_name  = 'notice notice-' + type + ' is-dismissible';
				var message_box = $('<div class="' + class_name + '">').append( '<p>' + message + '</p>' ).append(
					$( '<button type="button" class="notice-dismiss">' ).append(
						'<span class="screen-reader-text">' + wr_nitro_customize_backup_restore.dismiss + '</span>'
					).click( function() {
						$( this ).closest( '.notice' ).fadeOut();
					} )
				);

				// Show message box.
				self.append( message_box );

				return message_box;
			}

			// Setup click event handle for restore button.
			self.find( '.nitro-restore-settings' ).click( function() {
				self.find( '.nitro-restore-settings-form' ).toggleClass( 'hidden' );
			} );

			// Initialize Ajax File Upload.
			self.NitroUploadBackup = self.NitroUploadBackup || self.find( '.nitro-upload-backup').uploadFile( {
				url: wr_nitro_customize_backup_restore.restore_url,
				multiple: false,
				autoSubmit: false,
				showFileCounter: false,
				allowedTypes: 'json',
				fileName: 'file',
				onSelect: function( files ) {
					// Remove previously selected file.
					self.find( '.ajax-file-upload-statusbar' ).remove();

					// Show start upload button.
					setTimeout( function() {
						self.find( '.start-upload' ).clone( true ).removeClass( 'hidden' ).appendTo(
							self.find( '.ajax-file-upload-statusbar' )
						);
					}, 500 );
				},
				onSubmit: function( files ) {
					// Hide all buttons.
					self.find( '.ajax-file-upload-statusbar div[class^="ajax-file-upload-"]' ).addClass( 'hidden' );

					// Set current root directory.
					this.url += '&nonce=' + wr_nitro_customize_backup_restore.restore_nonce;
				},
				onSuccess: function( files, response, xhr ) {
					// Hide start upload button.
					self.find( '.start-upload' ).addClass( 'hidden' ).appendTo( self.find( '.nitro-restore-settings-form' ) );

					// Hide the form to upload file.
					self.find( '.ajax-file-upload-statusbar' ).remove();
					self.find( '.nitro-restore-settings-form' ).addClass( 'hidden' );

					if ( response.success ) {
						message( wr_nitro_customize_backup_restore.restore_success );
					} else {
						message( response.data, 'error' );
					}
				},
			});

			self.find( '.start-upload').click( function() {
				self.NitroUploadBackup.startUpload();
			} );
		} );
	} );
} )( jQuery );
