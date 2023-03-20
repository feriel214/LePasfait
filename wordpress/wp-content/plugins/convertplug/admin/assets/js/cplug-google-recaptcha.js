/* eslint-env jquery */
(function () {
	'use strict';
	jQuery(document).ready(function () {
		const btn = jQuery('.button-update-settings');
		btn.on('click', function () {
			const ser = jQuery('[name]').serialize();
			const data = ser;
			jQuery.ajax({
				url: ajaxurl,
				data,
				dataType: 'JSON',
				type: 'POST',
				success(result) {
					if (result.message === 'Settings Updated!') {
						swal('Updated!', result.message, 'success');
						setTimeout(function () {
							window.location = window.location;
						}, 500);
					} else {
						swal('Error!', result.message, 'error');
					}
				},
			});
		});
	});
})();
