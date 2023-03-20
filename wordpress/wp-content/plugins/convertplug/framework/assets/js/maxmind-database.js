/* eslint-env jquery */
jQuery(document).ready(function () {
	const btn = jQuery('.button-update-settings');
	btn.on('click', function () {
		const security = jQuery('#cplus_maxmind_nonce').val();
		const ser =
			jQuery('[name]').serialize() + '&security_nonce=' + security;
		const data = ser;
		const action = 'cplus_maxmind';
		jQuery.ajax({
			url: ajaxurl,
			action,
			data,
			dataType: 'JSON',
			type: 'POST',
			success(result) {
				if (result.message === 'Settings Updated!') {
					swal({
						title: 'Updated!',
						type: 'success',
						text: result.message,
					});
					setTimeout(function () {
						window.location = window.location;
					}, 500);
				} else {
					swal({
						title: 'Error!',
						type: 'error',
						text: result.message,
					});
				}
			},
		});
	});
});
