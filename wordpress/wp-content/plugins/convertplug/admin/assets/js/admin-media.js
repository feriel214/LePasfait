/* eslint-env jquery */
(function () {
	'use strict';
	jQuery.SmileTrigger = jQuery.SmileTrigger || {};
	jQuery.SmileTrigger.wp_media = jQuery.SmileTrigger.wp_media || [];
	jQuery.SmileTrigger.media_new = function () {
		const $body = jQuery('body');
		$body.on(
			'click',
			'.smile_upload_icon',
			jQuery.SmileTrigger.media_new_activate
		);
	};
	//intended for zip files only. if needed should be easy to expand in the future
	jQuery.SmileTrigger.media_new_activate = function (event) {
		event.preventDefault();
		const clicked = jQuery(this),
			options = clicked.data();
		options.input_target = jQuery('#' + options.target);
		// Create the media frame.
		const file_frame = wp.media({
			frame: options.frame,
			library: { type: options.type },
			button: { text: options.button },
			className: options.class,
		});
		file_frame.on('select update insert', function () {
			jQuery.SmileTrigger.media_new_insert(file_frame, options);
		});
		//open the media frame
		file_frame.open();
	};
	//insert the url of the zip file
	jQuery.SmileTrigger.media_new_insert = function (file_frame, options) {
		const state = file_frame.state(),
			selection = state.get('selection').first().toJSON();
		options.input_target.val(selection.id).trigger('change');
		jQuery('body').trigger(options.trigger, [selection, options]);
	};
	jQuery(document).ready(function () {
		jQuery.SmileTrigger.media_new();
		//Fonts Zip file upload
		jQuery('body').on('smile_insert_zip', jQuery.SmileTrigger.icon_insert);
		//font manager
		jQuery('body').on(
			'click',
			'.smile_del_icon',
			jQuery.SmileTrigger.icon_remove
		);
	});
	/**
	 * @param {string} selection
	 * @param {string} options
	 */
	jQuery.SmileTrigger.icon_insert = function (selection, options) {
		// clean the options field, we dont need to save a value
		options.input_target.val('');
		const msg = jQuery('#msg');
		if (selection.subtype !== 'zip') {
			jQuery('.spinner').hide();
			msg.html(
				"<div class='error'><p>Please upload a valid ZIP file.<br/>You can create the file on icomoon.io</p></div>"
			);
			msg.show();
			setTimeout(function () {
				msg.slideUp();
			}, 5000);
			return;
		}
		// send request to server to extract the zip file, re arrange the content and save a config file
		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {
				action: 'smile_ajax_add_zipped_font',
				values: selection,
			},
			beforeSend() {
				jQuery('.spinner')
					.css({
						opacity: 0,
						display: 'block',
						visibility: 'visible',
						position: 'absolute',
						top: '21px',
						left: '345px',
					})
					.animate({ opacity: 1 });
			},
			success(response) {
				jQuery('.spinner').hide();
				if (response.match(/smile_font_added/)) {
					msg.html(
						"<div class='updated'><p>Font icon added successfully! Reloading the page... </p></div>"
					);
					msg.show();
					setTimeout(function () {
						msg.slideUp();
						location.reload();
					}, 5000);
				} else {
					msg.html(
						"<div class='error'><p>Couldn't add the font.<br/>The script returned the following error: " +
							response +
							'</p></div>'
					);
					msg.show();
					setTimeout(function () {
						msg.slideUp();
					}, 5000);
				}
			},
		});
	};
	jQuery.SmileTrigger.icon_remove = function (event) {
		event.preventDefault();
		const button = jQuery(this),
			del_font = button.data('delete');
		const msg = jQuery('#msg');
		// send request to server to remove the folder and the database entry
		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: {
				action: 'smile_ajax_remove_zipped_font',
				del_font,
			},
			beforeSend() {
				jQuery('.spinner')
					.css({
						opacity: 0,
						display: 'block',
						visibility: 'visible',
						position: 'absolute',
						top: '21px',
						left: '345px',
					})
					.animate({ opacity: 1 });
			},
			error() {
				jQuery('.spinner').hide();
				msg.html(
					"<div class='error'><p>Couldn't remove the font because the server didn’t respond.<br/>Please reload the page, then try again</p></div>"
				);
				msg.show();
				setTimeout(function () {
					msg.slideUp();
				}, 5000);
			},
			success(response) {
				jQuery('.spinner').hide();
				if (response.match(/smile_font_removed/)) {
					msg.html(
						"<div class='updated'><p>Icon set deleted successfully! Reloading the page...</p></div>"
					);
					msg.show();
					setTimeout(function () {
						msg.slideUp();
						location.reload();
					}, 5000);
				} else {
					msg.html(
						"<p><div class='error'><p>Couldn't remove the font.<br/>Reloading the page...</p></div>"
					);
					msg.show();
					setTimeout(function () {
						msg.slideUp();
						location.reload();
					}, 5000);
				}
			},
			complete() {},
		});
	};
})(jQuery);
