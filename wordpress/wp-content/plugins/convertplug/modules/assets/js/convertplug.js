/* eslint-env jquery */
(function () {
	'use strict';

	/**
	 * Helper Functions
	 *
	 * 1. Render - HTML - DropDown - Extract all dropdown options
	 * 2. Build ConvertPlug Form
	 */

	/**
	 * 3. Only for Placeholder
	 *
	 */
	function only_for_placeholder() {
		//  CSS - Placeholders
		let style = '::-webkit-input-placeholder { /* WebKit, Blink, Edge */';
		style += '    font-family: inherit;';
		style += '}';
		style += ':-moz-placeholder { /* Mozilla Firefox 4 to 18 */';
		style += '   font-family: inherit;';
		style += '}';
		style += '::-moz-placeholder { /* Mozilla Firefox 19+ */';
		style += '   font-family: inherit;';
		style += '}';
		style += ':-ms-input-placeholder { /* Internet Explorer 10-11 */';
		style += '   font-family: inherit;';
		style += '}';
		style +=
			':placeholder-shown { /* Standard (https://drafts.csswg.org/selectors-4/#placeholder) */';
		style += '  font-family: inherit;';
		style += '}';

		//  Append CSS code
		jQuery('#cp-customizer-form-css').html('<style>' + style + '</style>');
	}

	/**
	 * Form Button - Styles
	 *
	 * @param {Object} data
	 */
	function form_submit_button(data) {
		const btn_bg_color = data.button_bg_color;
		let btn_style = data.btn_style;
		const btn_shadow = data.btn_shadow;
		const btn_border_radius = data.btn_border_radius;
		const button_txt_hover_color = data.button_txt_hover_color;
		const form_input_align = smile_global_data.form_input_align;
		const cp_submit = jQuery('.cp-submit');
		const input_shadow = data.input_shadow;
		const input_shadow_color = data.input_shadow_color;
		const form_layout = data.form_layout;
		const btn_attached_email = data.btn_attached_email;
		const form_fields = data.form_fields;

		//  Remove all classes

		cp_submit.removeClass(
			'cp-btn-flat cp-btn-3d cp-btn-outline cp-btn-gradient'
		);

		//check if only on einput field is present in form
		const data_value = [];
		let last_order = '';
		//  Extract ALL - field
		const all = form_fields.split(';');
		const lg = all.length - 1;
		if (lg !== 0) {
			jQuery.each(all, function (index, val) {
				//  Extract SINGLE - all
				const single = val.split('|');
				if (single.length !== 0) {
					if (single[1].indexOf('hidden') <= 0) {
						data_value.push(single[0]);
					}
				}
			});
			last_order = data_value[data_value.length - 1];
			last_order = last_order.split('->');
			last_order = parseInt(last_order[1]);
		}
		if (typeof last_order === 'undefined' || last_order === '') {
			last_order = 0;
		}

		if (
			form_layout === 'cp-form-layout-3' &&
			parseInt(btn_attached_email) === 1 &&
			parseInt(last_order) === 0
		) {
			if (btn_style === 'cp-btn-3d' || btn_style === 'cp-btn-outline') {
				btn_style = 'cp-btn-flat';
			}
		}

		cp_submit.addClass(btn_style);

		const c_normal = btn_bg_color;
		const c_hover = darkerColor(c_normal, 0.05);
		const light = lighterColor(c_normal, 0.3);

		cp_submit.css('background', c_normal);
		//  Apply box shadow to submit button - If its set & equals to - 1
		let shadow = '';
		let radius = '';
		if (parseInt(btn_shadow) === 1) {
			shadow += 'box-shadow: 1px 1px 2px 0px rgba(66, 66, 66, 0.6);';
		}
		if (btn_border_radius !== '') {
			radius += 'border-radius: ' + btn_border_radius + 'px;';
		}
		jQuery('head').append('<div id="cp-temporary-inline-css"></div>');
		switch (btn_style) {
			case 'cp-btn-flat':
				jQuery('#cp-temporary-inline-css').html(
					'<style>' +
						'.' +
						btn_style +
						'.cp-submit{ background: ' +
						c_normal +
						'!important;' +
						shadow +
						radius +
						'; } ' +
						'.' +
						btn_style +
						'.cp-submit:hover { background: ' +
						c_hover +
						'!important; } ' +
						'</style>'
				);
				break;
			case 'cp-btn-3d':
				jQuery('#cp-temporary-inline-css').html(
					'<style>' +
						'.' +
						btn_style +
						'.cp-submit {background: ' +
						c_normal +
						'!important; ' +
						radius +
						' position: relative ; box-shadow: 0 6px ' +
						c_hover +
						';} ' +
						'.' +
						btn_style +
						'.cp-submit:hover {background: ' +
						c_normal +
						'!important;top: 2px; box-shadow: 0 4px ' +
						c_hover +
						';} ' +
						'.' +
						btn_style +
						'.cp-submit:active {background: ' +
						c_normal +
						'!important;top: 6px; box-shadow: 0 0px ' +
						c_hover +
						';} ' +
						'</style>'
				);
				break;
			case 'cp-btn-outline':
				jQuery('#cp-temporary-inline-css').html(
					'<style>' +
						'.' +
						btn_style +
						'.cp-submit { background: transparent!important;border: 2px solid ' +
						c_normal +
						';color: inherit ;' +
						shadow +
						radius +
						'}' +
						'.' +
						btn_style +
						'.cp-submit:hover { background: ' +
						c_hover +
						'!important;border: 2px solid ' +
						c_hover +
						';color: ' +
						button_txt_hover_color +
						' ;' +
						'}' +
						'.' +
						btn_style +
						'.cp-submit:hover span { color: inherit !important ; } ' +
						'</style>'
				);
				break;
			case 'cp-btn-gradient': //  Apply box shadow to submit button - If its set & equals to - 1
				jQuery('#cp-temporary-inline-css').html(
					'<style>' +
						'.' +
						btn_style +
						'.cp-submit {' +
						'     border: none ;' +
						shadow +
						radius +
						'     background: -webkit-linear-gradient(' +
						light +
						', ' +
						c_normal +
						') !important;' +
						'     background: -o-linear-gradient(' +
						light +
						', ' +
						c_normal +
						') !important;' +
						'     background: -moz-linear-gradient(' +
						light +
						', ' +
						c_normal +
						') !important;' +
						'     background: linear-gradient(' +
						light +
						', ' +
						c_normal +
						') !important;' +
						'}' +
						'.' +
						btn_style +
						'.cp-submit:hover {' +
						'     background: ' +
						c_normal +
						' !important;' +
						'}' +
						'</style>'
				);
				break;
		}

		//for dropdown option
		let text_align = 'center';
		if (form_input_align === 'right') {
			text_align = 'rtl';
		} else if (form_input_align === 'left') {
			text_align = 'ltr';
		}

		jQuery('#cp-temporary-inline-css-for-select').remove();
		jQuery('head').append(
			'<div id="cp-temporary-inline-css-for-select"></div>'
		);
		jQuery('#cp-temporary-inline-css-for-select').html(
			'<style>' +
				'.cp-form-container .cp-form-field select { ' +
				' text-align-last: ' +
				text_align +
				';' +
				' direction: ' +
				text_align +
				'; } ' +
				'</style>'
		);

		//set input box shadow
		jQuery('#cp-temporary-inline-css-for-input').remove();
		jQuery('head').append(
			'<div id="cp-temporary-inline-css-for-input"></div>'
		);
		if (parseInt(input_shadow) === 1) {
			jQuery('#cp-temporary-inline-css-for-input').html(
				'<style>' +
					'.enable_input_shadow .cp-input ,.enable_input_shadow input.cp-number ,.enable_input_shadow select.cp-dropdown { ' +
					' -webkit-box-shadow: inset 1px 1px 2px 0px ' +
					input_shadow_color +
					'!important;' +
					' -moz-box-shadow: inset 1px 1px 2px 0px ' +
					input_shadow_color +
					'!important;' +
					' box-shadow: inset 1px 1px 2px 0px ' +
					input_shadow_color +
					'!important;' +
					'} ' +
					'</style>'
			);
		}

		//  Set either 10% darken color for 'HOVER'
		//  Or 0.10% darken color for 'GRADIENT'
		jQuery('#smile_button_bg_hover_color', window.parent.document).val(
			c_hover
		);
		jQuery('#smile_button_bg_gradient_color', window.parent.document).val(
			light
		);
	}

	jQuery(window).on('load', function () {
		if (jQuery('.woocommerce')[0]) {
			jQuery(window.parent.document)
				.find("div [data-element='add_to_cart']")
				.addClass('cp_cart');
		} else {
			jQuery(window.parent.document)
				.find("div [data-element='add_to_cart']")
				.remove();
		}
	});

	/*
	 *function for social media style
	 */
	function social_media_css(data) {
		const cp_social_icon_style = data.cp_social_icon_style;
		const cp_social_icon_shape = data.cp_social_icon_shape;
		const cp_social_icon_effect = data.cp_social_icon_effect;
		const cp_social_icon_column = data.cp_social_icon_column;
		const cp_social_enable_icon_color = data.cp_social_enable_icon_color;
		const icon_color = data.cp_social_icon_color;
		const icon_bgcolor = data.cp_social_icon_bgcolor;
		const icon_hover = data.cp_social_icon_hover;
		const icon_bghover = data.cp_social_icon_bghover;
		const cp_social_icon_align = data.cp_social_icon_align;
		const cp_social_text_hover_color = data.cp_social_text_hover_color;
		const cp_social_text_color = data.cp_social_text_color;
		let social_style = '';

		const c_hover = darkerColor(icon_bghover, 0.05);
		const light = darkerColor(icon_bgcolor, 0.05);

		jQuery('#cp-social-icon-css').remove();

		//apply css
		jQuery('head').append('<div id="cp-social-icon-css"></div>');

		//to use user defined color for icon
		if (parseInt(cp_social_enable_icon_color) === 1) {
			if (cp_social_icon_effect === '3D') {
				social_style +=
					'.cp_3D li,' +
					'  .cp_social_networks.cp_social_simple.cp_3D li i ,' +
					'  .cp_social_networks.cp_social_circle.cp_3D li i{' +
					'    box-shadow: 0 4px ' +
					light +
					'!important;' +
					' }' +
					'.cp_3D li:hover,' +
					'  .cp_social_networks.cp_social_simple.cp_3D li:hover i ,' +
					'  .cp_social_networks.cp_social_circle.cp_3D li:hover i {' +
					'    box-shadow: 0 4px ' +
					c_hover +
					'!important;' +
					' }';
				if (
					cp_social_icon_shape === 'square' &&
					cp_social_icon_style === 'cp-icon-style-simple'
				) {
					social_style +=
						'.cp_3D .cp_social_share {' +
						'     padding: 5px;' +
						' }';
				}
			}
			//if icon style==normal
			social_style +=
				'.cp-icon-style-simple.cp-normal i,' +
				'  .cp_social_networks.cp_social_simple.cp-icon-style-simple.cp-normal i {' +
				'    color:' +
				icon_color +
				'!important;' +
				'    background-color:transparent!important;' +
				' }';
			social_style +=
				'.cp-icon-style-simple.cp-normal li:hover i ,' +
				'  .cp_social_networks.cp_social_simple.cp-icon-style-simple.cp-normal li:hover i {' +
				'    color:' +
				icon_hover +
				'!important;' +
				'    background-color:transparent!important;' +
				' }';
			//text color
			social_style +=
				'.cp_social_networks .cp_social_network_label, ' +
				'  .cp_social_networks .cp_social_networkname,' +
				'  .cp_social_networks .cp_social_count {' +
				'     color: ' +
				cp_social_text_color +
				'!important;' +
				' }';
			social_style +=
				'.cp_social_networks li:hover .cp_social_network_label,' +
				'  .cp_social_networks li:hover .cp_social_networkname,' +
				'  .cp_social_networks li:hover .cp_social_count ,' +
				'  .cp_social_networks li:hover .cp_social_count span {' +
				'     color: ' +
				cp_social_text_hover_color +
				'!important;' +
				' }';
		} else if (
			cp_social_icon_effect === '3D' &&
			cp_social_icon_shape === 'square' &&
			cp_social_icon_style === 'cp-icon-style-simple'
		) {
			social_style +=
				'.cp_3D .cp_social_share {' + '     padding: 5px;' + ' }';
		}

		//apply no of column to container
		if (cp_social_icon_column === 'auto') {
			social_style +=
				' .cp_social_networks .cp_social_icons_container {' +
				'     margin-bottom: -15px!important;' +
				' }';

			social_style +=
				' .cp_social_networks.cp_social_autowidth .cp_social_icons_container {' +
				'     text-align: ' +
				cp_social_icon_align +
				';' +
				' }';
		}

		//  Set either 10% darken color for 'HOVER'
		//  Or 0.10% darken color for 'GRADIENT'
		jQuery('#smile_social_lighten', window.parent.document).val(light);
		jQuery('#smile_social_darken', window.parent.document).val(c_hover);

		jQuery('#cp-social-icon-css').html(
			'<style>' + social_style + '</style>'
		);
	}

	//function for start counter
	function start_count_timer(data) {
		const date_time_picker = data.date_time_picker;
		const defaultCountdown = jQuery('#cp_defaultCountdown');
		const counter_bg_color = data.counter_bg_color;
		let counter_font = data.counter_font;
		let counter_option = data.counter_option;
		const cp_gmt_offset = data.cp_gmt_offset;
		const cp_counter_timezone = data.cp_counter_timezone;
		const disable_datepicker = data.disable_datepicker;
		const datepicker_advance_option = data.datepicker_advance_option;
		let countupto = '';
		let counter_timer = '';
		let format = '';
		let layoutopt = '';
		let counter_digit = '';
		let labelsname = [
			'Year',
			'Month',
			'Weeks',
			'Days',
			'Hours',
			'Minutes',
			'Seconds',
		];
		let layouutformat = '';
		const vw = jQuery(window).width();

		if (counter_option.length > 0) {
			counter_option = counter_option.split('|');
			jQuery.each(counter_option, function (i, v) {
				format += v;
			});
		} else {
			format = 'YOWDHMS';
		}

		if (counter_font === '' && counter_font === 'undefined') {
			counter_font = 'inherit';
		}

		for (let i = 0, len = format.length; i < len; i++) {
			const lower = format[i].toLowerCase();
			layouutformat += '{' + lower + 'n}';

			if (i + 1 !== len) {
				layouutformat += ' {' + lower + 'l}, ';
			} else {
				layouutformat += ' {' + lower + 'l}';
			}
		}

		if (parseInt(disable_datepicker) !== 1) {
			defaultCountdown.cp_countdown('destroy');
			defaultCountdown.hide();
		} else {
			defaultCountdown.show();
		}

		if (datepicker_advance_option !== 'style_2') {
			layoutopt = layouutformat;
		} else {
			const lt = format.length;
			//if counter digit greater than 4 then compress labels
			if (vw <= 610 && lt >= 4) {
				labelsname = ['Y', 'M', 'W', 'D', 'H', 'Mn', 'S'];
			}
		}

		defaultCountdown.cp_countdown('destroy');
		countupto = new Date(date_time_picker);

		//timezone
		if (cp_counter_timezone === 'wordpress') {
			defaultCountdown.cp_countdown({
				until: countupto,
				format,
				timezone: cp_gmt_offset,
				layout: layoutopt,
				labels: labelsname,
			});
		} else {
			defaultCountdown.cp_countdown({
				until: countupto,
				format,
				layout: layoutopt,
				labels: labelsname,
			});
		}

		jQuery('#cp-count-timer-css').remove();
		let count_timer_css = '';
		//apply css
		jQuery('head').append('<div id="cp-count-timer-css"></div>');

		switch (datepicker_advance_option) {
			case 'style_1':
				counter_digit = '';
				counter_digit += 'background: transparent;';
				counter_digit += 'font-family:' + counter_font + ';';
				// counter_digit += 'font-size:' + digit_text_size +'px!important;';
				count_timer_css +=
					'#cp_defaultCountdown{' + counter_digit + '; } ';
				break;
			case 'style_2':
				counter_digit = '';
				counter_digit += 'background:' + counter_bg_color + ';';
				counter_digit += 'font-family:' + counter_font + ';';

				//timer text css
				counter_timer += 'font-family: ' + counter_font + ';';
				count_timer_css +=
					'#cp_defaultCountdown  .cp_countdown-amount {  ' +
					counter_digit +
					'; } ' +
					'#cp_defaultCountdown  .cp_countdown-period { ' +
					counter_timer +
					'; } ';
				break;
		}
		jQuery('#cp-count-timer-css').html(
			'<style>' + count_timer_css + '</style>'
		);
	}

	function submit_button_animation(data) {
		const button_animation = data.button_animation,
			cp_submit = jQuery('.cp-submit');

		cp_submit.removeClass(function (index, css) {
			return (css.match(/(^|\s)smile-\S+/g) || []).join(' ');
		});

		cp_submit.addClass('smile-animated ' + button_animation);
	}

	// To Fix - Uncaught The editor instance is already attached to the provided element.(For all modules)
	// disableAutoInline set to true exactly after loading ckeditor.js.
	// Need to turn off automatic editor creation first by setting the CKEDITOR.disableAutoInline option to true.
	CKEDITOR.disableAutoInline = true;

	/**
	 * Initialize CKEditor for submit button
	 */
	jQuery(document).ready(function () {
		//  Highlight MultiField
		jQuery('body').on('click', '.cp-form-field', function (e) {
			parent.setFocusElement('form_fields');
			e.stopPropagation();
		});

		//Wocomerrce plugin active

		//  Add div for from CSS
		jQuery('head').append('<div id="cp-customizer-form-css"></div>');

		//  1. Initialize CKEditor for submit button
		if (jQuery('#cp_button_editor').length) {
			// Turn off automatic editor creation first.
			CKEDITOR.inline('cp_button_editor');
			CKEDITOR.instances.cp_button_editor.config.toolbar = 'Small';

			//  1+ Add class 'cp-no-responsive' to manage the line height of cp-highlight
			CKEDITOR.instances.cp_button_editor.on(
				'instanceReady',
				function () {
					CKEDITOR.instances.cp_button_editor.getData();
				}
			);

			CKEDITOR.instances.cp_button_editor.on('change', function () {
				const data = CKEDITOR.instances.cp_button_editor.getData();
				parent.updateHTML(data, 'smile_button_title');
			});

			// Use below code to 'reinitialize' CKEditor
			// IN ANY CASE IF CKEDITOR IS NOT INITIALIZED THEN USE BELOW CODE
			CKEDITOR.instances.cp_button_editor.on(
				'instanceReady',
				function (ev) {
					const editor = ev.editor;
					editor.setReadOnly(false);
				}
			);
		}
		jQuery('body').on('click', '.cp_social_media_wrapper', function (e) {
			parent.setFocusElement('cp_social_icon');
			e.stopPropagation();
		});
	});

	/**
	 *  Customizer
	 *
	 * Use this trigger for Store & Retrieve the LIVE changes from customizer
	 */
	jQuery(document).on(
		'smile_customizer_field_change',
		function (e, single_data) {
			// Only for Placeholder
			only_for_placeholder();

			const modules = smile_global_data.option;

			//count down
			const style = smile_global_data.style || null;
			if (
				(cp_isValid(style) && style === 'countdown') ||
				'disable_datepicker' in single_data ||
				'datepicker_advance_option' in single_data ||
				'counter_font' in single_data
			) {
				start_count_timer(smile_global_data);
			}

			if (
				modules === 'smile_info_bar_styles' &&
				'button_animation' in single_data
			) {
				submit_button_animation(single_data);
			}
		}
	);

	jQuery(document).on('smile_data_continue_received', function (e, data) {
		const style = data.style || null;
		if (
			cp_isValid(style) &&
			style !== 'social_media' &&
			typeof data.btn_style !== 'undefined'
		) {
			form_submit_button(data);
		}

		if (cp_isValid(style)) {
			if (typeof smile_global_data.cp_social_icon !== 'undefined') {
				social_media_css(data);
			}
		}
	});

	parent
		.jQuery(window.parent.document)
		.on('smile-colorpicker-change', function (e, el, val) {
			if (jQuery(el).hasClass('counter_bg_color')) {
				smile_global_data.counter_bg_color = val;
				start_count_timer(smile_global_data);
			}
			if (jQuery(el).hasClass('cp_social_icon_color')) {
				smile_global_data.cp_social_icon_color = val;
				social_media_css(smile_global_data);
			}
		});

	parent
		.jQuery(window.parent.document)
		.on('smile-datepicker-change', function (e, el, val) {
			smile_global_data.date_time_picker = val;
			if (jQuery(el).hasClass('date_time_picker')) {
				start_count_timer(smile_global_data);
			}
		});

	parent
		.jQuery(window.parent.document)
		.on('smile-checkbox-change', function (e, el, val) {
			smile_global_data.counter_option = val;
			if (jQuery(el).hasClass('counter_option')) {
				start_count_timer(smile_global_data);
			}
		});

	/**
	 *  Customizer
	 *
	 * Use this trigger for Store & Retrieve the LIVE changes from customizer
	 */
	jQuery(document).on('smile_data_received', function (e, data) {
		const cp_submit = jQuery('.cp-submit');
		let button_title = data.button_title;
		const fields = data.form_fields;
		const cp_social_icon = data.cp_social_icon;
		const disable_datepicker = data.disable_datepicker;
		const button_animation = data.button_animation;

		// Update Submit button HTML
		button_title = htmlEntities(button_title);

		cp_submit.html(button_title);
		if (
			button_title !== '' &&
			typeof button_title !== 'undefined' &&
			jQuery('#cp_button_editor').length
		) {
			if (CKEDITOR.instances.cp_button_editor !== undefined) {
				CKEDITOR.instances.cp_button_editor.setData(button_title);
			}
		}

		if (typeof fields !== 'undefined' && fields !== null) {
			// Only for Placeholder
			only_for_placeholder();
		}

		if (typeof cp_social_icon !== 'undefined' && cp_social_icon !== '') {
			//social media style
			social_media_css(data);
		}

		if (
			typeof disable_datepicker !== 'undefined' &&
			disable_datepicker !== null
		) {
			//count down
			start_count_timer(data);
		}

		if (
			typeof button_animation !== 'undefined' &&
			button_animation !== null
		) {
			setTimeout(function () {
				submit_button_animation(data);
			}, 1500);
		}
	});
})(jQuery);
