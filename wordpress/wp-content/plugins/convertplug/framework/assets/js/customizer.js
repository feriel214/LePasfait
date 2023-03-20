/**
 * ConvertPlug
 *
 * Triggers & Functions
 *
 * 1. 	Trigger - smile_data_received
 * 2. 	Trigger - smile_data_on_load
 * 3. 	Trigger - smile_customizer_field_change
 */

/**
 * Global All FORM DATA
 */
let smile_global_data = '';

/**
 * Add this div for preview CSS
 */
/* eslint-env jquery */

jQuery('body').append('<div id="cp-preview-css"></div>');
jQuery('body').append('<div id="cp-form-css"></div>');

/**
 * 1. 	Trigger - smile_data_received
 *
 * Triggered after any customizer input change.
 *
 * @param {Object} event
 */
window.onload = function (event) {
	function receiveMessage(e) {
		const origin = e.origin;

		// If request is from our domain then only process data
		if (origin.indexOf(window.location.host) >= 0) {
			const e_data = e.data.replace(/\"/g, '');
			const pairs = e_data.split('&');
			const result = {};
			pairs.forEach(function (pair) {
				pair = pair.split('=');
				if (result[pair[0]]) {
					result[pair[0]] =
						result[pair[0]] + ',' + decodeURIComponent(pair[1]);
				} else {
					result[pair[0]] = decodeURIComponent(pair[1]);
				}
			});
			smile_global_data = result;

			jQuery(document).trigger('smile_data_continue_received', [
				smile_global_data,
			]);
		}
	}
	event.currentTarget.addEventListener('message', receiveMessage);
};

/**
 * 2. 	Trigger - smile_data_on_load
 *
 * It works only once when page is loaded.
 */
jQuery(window).on('load', function () {
	smile_global_data = get_customizer_form_data();

	smile_global_data.modal_size =
		typeof smile_global_data.modal_size === 'undefined'
			? 'cp-modal-custom-size'
			: smile_global_data.modal_size;

	/**
	 *	1.	Add Selected Google Fonts
	 */
	const cp_google_fonts = smile_global_data.cp_google_fonts || '';

	if ('' !== cp_google_fonts && 'undefined' !== cp_google_fonts) {
		cp_get_gfonts(cp_google_fonts);
	}

	cp_ckeditors_setup(smile_global_data);

	/**
	 * 	3.	Individual style CSS
	 *
	 * 	Add CSS file of this style
	 */
	const style = smile_global_data.style || null;
	const modules = smile_global_data.option;
	const module_name = cp.demo_dir;

	switch (modules) {
		case 'smile_info_bar_styles':
		case 'info_bar_variant_tests':
			jQuery('html').addClass('cp-customizer-info_bar');
			break;
		case 'smile_modal_styles':
		case 'modal_variant_tests':
			jQuery('html').addClass('cp-customizer-modal');
			break;
		case 'smile_slide_in_styles':
		case 'slide_in_variant_tests':
			jQuery('html').addClass('cp-customizer-slide_in');
			break;
	}

	if (cp_isValid(style)) {
		const css_file =
			'/' + style.toLowerCase() + '/' + style.toLowerCase() + '.min.css';

		jQuery('head').append(
			'<link rel="stylesheet" href="' +
				module_name +
				css_file +
				'" type="text/css" />'
		);
	}

	/**
	 * 	4.	Blinking cursor
	 *
	 * @param  string style Module Style Name
	 */
	const modal_title_color = smile_global_data.modal_title_color || null;
	if (cp_isValid(modal_title_color)) {
		switch (style) {
			case 'blank':
			case 'social_media':
				cp_blinking_cursor('#short_desc_editor', modal_title_color);
				break;
			case 'countdown':
			case 'direct_download':
			case 'every_design':
			case 'first_order':
			case 'first_order_2':
			case 'flat_discount':
			case 'free_ebook':
			case 'instant_coupon':
			case 'locked_content':
			case 'optin_to_win':
			case 'special_offer':
			case 'webinar':
				cp_blinking_cursor('.cp-title', modal_title_color);
				break;
		}
	}

	jQuery(document).trigger('smile_data_on_load', [smile_global_data]);
	jQuery(document).trigger('smile_data_received', [smile_global_data]);
});

/**
 *  Initially APPLY CSS
 *
 *	1. Apply INLINE
 *	2. Apply after CSS Generation
 */
function get_customizer_form_data() {
	const data = jQuery('.cp-cust-form', window.parent.document).serialize();
	let e_data = data.replace(/\"/g, '');
	e_data = e_data.replace(/\+/g, ' ');
	const pairs = e_data.split('&');
	const result = {};
	pairs.forEach(function (pair) {
		pair = pair.split('=');
		if (result[pair[0]]) {
			result[pair[0]] =
				result[pair[0]] + ',' + decodeURIComponent(pair[1]);
		} else {
			result[pair[0]] = decodeURIComponent(pair[1]);
		}
	});
	return result;
}

/**
 * 3. 	Trigger - smile_customizer_field_change
 *
 * Trigger on form '.cp-cust-form' for customizer fields events:
 *
 * 	Input 			-	Change
 * 	Select 			- 	Change
 * 	MultiField 		-	Drag Drop
 * 	Slider			- 	Slide
 * 	ColorPicker		- 	Drag Drop
 */
jQuery(window).on('load', function () {
	jQuery('.cp-cust-form .smile-input', window.parent.document).on(
		'change',
		function () {
			let self = jQuery(this);
			let elm_id = '';
			//	FIELD - SWITCH
			if (self.hasClass('smile-switch')) {
				elm_id = self.siblings('input[type="text"]').attr('id');
				self = self.siblings('input[type="text"]');
			}

			const elm_data = self.val();
			elm_id = self.attr('id');
			elm_id = elm_id.split('smile_').pop();

			const single_data = {};
			single_data[elm_id] = decodeURIComponent(elm_data);

			//	Update single instance from global variable 'smile_global_data'
			smile_global_data[elm_id] = decodeURIComponent(elm_data);

			jQuery(document).trigger('smile_customizer_field_change', [
				single_data,
			]);

			//	Toggle Form - Show either CP (Default) Form or Custom form via ShortCode.
			if (elm_id === 'mailer') {
				dual__toggle_cp_form(smile_global_data);
			}

			input_shadow_change(smile_global_data);
		}
	);
});

/** TRIGGER - SINGLE */
jQuery(document).on('smile_data_received', function (e, data) {
	dual__toggle_cp_form(data);
	input_shadow_change(smile_global_data);
});

/** TRIGGER - CONTINUE */
jQuery(document).on('smile_data_continue_received', function (e, data) {
	const custom_html_form = data.custom_html_form || '';

	if ('' !== custom_html_form) {
		single__live_custom_form_data(custom_html_form);
	}
});

//	Live Custom Form Data
function single__live_custom_form_data(custom_html_form) {
	jQuery('.custom-html-form').html(custom_html_form);
}

//	Toggle Form - Show either CP (Default) Form or Custom form via ShortCode.
function dual__toggle_cp_form(data) {
	const mailer = data.mailer,
		default_form = jQuery('.default-form'),
		custom_form = jQuery('.custom-html-form');
	if (mailer === 'custom-form') {
		/* For InfoBar we use the display: flex !important */
		default_form.attr('style', 'display: none !important');
		custom_form.show();
	} else {
		default_form.attr('style', 'display: block');
		custom_form.css('display', 'none');
	}
}

function input_shadow_change(data) {
	if (data.input_shadow !== '' && data.input_shadow === '1') {
		jQuery('.default-form').addClass('enable_input_shadow');
	} else {
		jQuery('.default-form').removeClass('enable_input_shadow');
	}
}
