/* global ajaxurl */
( function( jQuery ) {

	'use strict';

	jQuery( document ).ready( function( ) {
		var formModified = false,
			colorPickerOptions;

		// Convert color-field input to color picker.
		colorPickerOptions = {
			change: function( event, ui ) { // eslint-disable-line no-unused-vars
				setTimeout( function() {
					if ( event.originalEvent ) {
						jQuery( event.target ).trigger( 'change' );
					}
				}, 1 );
			}
		};

		jQuery( '.color-field' ).wpColorPicker( colorPickerOptions );

		// Handle the radio button.
		jQuery( '.ui-buttonset .ui-button' ).on( 'click', function( e ) {
			e.preventDefault();

			jQuery( this ).parent().find( '.button-set-value' ).val( jQuery( this ).data( 'value' ) ).trigger( 'change' );
			jQuery( this ).parent().find( '.ui-button' ).removeClass( 'ui-state-active' );
			jQuery( this ).addClass( 'ui-state-active' );
		} );

		// Handle the image upload.
		jQuery( '.button-upload-image' ).click( function( e ) {
			var imageUploader = '',
				title         = jQuery( this ).data( 'title' ),
				buttonTitle   = jQuery( this ).data( 'button-title' ),
				imageID       = jQuery( this ).data( 'image-id' ),
				removeButton  = jQuery( this ).next( '.button-remove-image' ),
				attachment;

			e.preventDefault();

			imageUploader = wp.media( {
				title: title,
				button: {
					text: buttonTitle
				},
				multiple: false  // Set this to true to allow multiple files to be selected.
			} )
			.on( 'select', function() {
				attachment = imageUploader.state().get( 'selection' ).first().toJSON();
				jQuery( '.' + imageID + '_preview' ).html( '<img src="' + attachment.url + '">' );
				jQuery( '#' + imageID ).val( attachment.url ).trigger( 'change' );
				removeButton.show();
			} )
			.open();
		} );

		// Handle the settings import.
		jQuery( '.button-import-setting' ).click( function( e ) {
			var importFile = document.getElementById( 'import_settings' ).files,
				fileReader,
				jsonData,
				security,
				spinner = jQuery( '.fusion-white-label-import-settings .avada-db-loader' );

			e.preventDefault();

			if ( 0 >= importFile.length ) {
				jQuery( '#import_settings' ).css( 'border-color', 'red' );
				return false;
			}

			fileReader = new FileReader();

			fileReader.onload = function( loadEvent ) {
				jsonData = loadEvent.target.result;
				security = jQuery( '#import-nonce' ).val();
				spinner.show();
				jQuery( '#import_settings' ).val( '' );

				jQuery.ajax( {
					url: ajaxurl,
					type: 'POST',
					data: { action: 'import_white_label_settings', json_data: jsonData, security: security },
					dataType: 'json',
					success: function() { // eslint-disable-line no-empty-function
					},
					complete: function() {
						spinner.removeAttr( 'style' );
						jQuery( '.import-success-icon' ).removeClass( 'hidden' );

						setTimeout( function() {
							jQuery( '.import-success-icon' ).addClass( 'hidden' );
						}, 750 );
					}
				} );
			};

			fileReader.readAsText( importFile.item( 0 ) );
		} );

		// Handle unsaved changes warning.
		jQuery( '.fusion-white-label-branding-settings form *' ).change( function() {
			formModified = true;
		} );

		// Set flag to false if form is being submitted.
		jQuery( '.fusion-white-label-branding-settings form' ).submit( function() {
			formModified = false;
		} );

		window.onbeforeunload = confirmExit;
		function confirmExit() {
			if ( formModified ) {
				return '';
			}
		}

	} );
}( jQuery ) );
