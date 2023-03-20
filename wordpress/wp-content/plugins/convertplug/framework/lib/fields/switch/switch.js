/* eslint-env jquery */
jQuery(document).ready(function ($) {
	jQuery(document).on('click', '.smile-switch-btn', function () {
		if (jQuery('#mailchimp-list').val() !== undefined) {
			// Conflict with Connects MailChimp Switch for Group Selection.
			return;
		}
		const id = jQuery(this).data('id');
		const value = jQuery(this)
			.parents('.switch-wrapper')
			.find('#' + id)
			.val();

		if (value === 1 || value === '1') {
			jQuery(this)
				.parents('.switch-wrapper')
				.find('#' + id)
				.attr('value', '0');
		} else {
			jQuery(this)
				.parents('.switch-wrapper')
				.find('#' + id)
				.attr('value', '1');
		}
		jQuery(this)
			.parents('.switch-wrapper')
			.find('.smile-switch-input')
			.trigger('change');
		$(document).trigger('smile-switch-change', [id]);
	});
});
