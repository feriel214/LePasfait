/* eslint-env jquery */
(function ($) {
	'use strict';
	// Uploading files
	let file_frame, attachment;
	_wpPluploadSettings.defaults.multipart_params.admin_page = 'import';
	$('.cp-import-style').on('click', function (event) {
		event.preventDefault();

		// If the media frame already exists, reopen it.
		if (file_frame) {
			file_frame.open();
			return;
		}

		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media({
			title: jQuery(this).data('uploader_title'),
			button: {
				text: jQuery(this).data('uploader_button_text'),
			},
			library: {
				type: 'application/zip',
			},
			multiple: false, // Set to true to allow multiple files to be selected
		});

		// When the file is selected, run a callback.
		const module = jQuery(this).data('module');
		file_frame.on('select', function () {
			// We set multiple to false so only get one file from the uploader
			attachment = file_frame.state().get('selection').first().toJSON();
			const file = attachment;
			const loader = jQuery('.cp-loader.spinner');
			loader.css('visibility', 'visible');
			const data = {
				action: 'cp_import_' + module,
				file,
				module,
				security_nonce: cplus_var_nonce.cp_import_nonce,
			};
			jQuery.ajax({
				url: ajaxurl,
				data,
				type: 'POST',
				dataType: 'JSON',
				success(result) {
					loader.css('visibility', 'hidden');
					const status = result.status;
					const desc = result.description;
					if (status === 'error') {
						swal('Error!', desc, 'error');
					} else {
						swal('Imported!', desc, 'success');
					}
					setTimeout(function () {
						window.location = window.location;
					}, 1000);
				},
			});
		});

		// Finally, open the modal
		file_frame.open();
	});
})(jQuery);
