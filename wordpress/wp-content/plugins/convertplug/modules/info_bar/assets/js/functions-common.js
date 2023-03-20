/* eslint-env jquery */
/* eslint-disable no-shadow */
(function (e) {
	function t() {
		const e = document.createElement('p');
		let t = false;
		if (e.addEventListener)
			e.addEventListener(
				'DOMAttrModified',
				function () {
					t = true;
				},
				false
			);
		else if (e.attachEvent)
			e.attachEvent('onDOMAttrModified', function () {
				t = true;
			});
		else return false;
		e.setAttribute('id', 'target');
		return t;
	}
	function n(t, n) {
		if (t) {
			const r = this.data('attr-old-value');
			if (n.attributeName.indexOf('style') >= 0) {
				if (!r.style) r.style = {};
				const i = n.attributeName.split('.');
				n.attributeName = i[0];
				n.oldValue = r.style[i[1]];
				n.newValue = i[1] + ':' + this.prop('style')[e.camelCase(i[1])];
				r.style[i[1]] = n.newValue;
			} else {
				n.oldValue = r[n.attributeName];
				n.newValue = this.attr(n.attributeName);
				r[n.attributeName] = n.newValue;
			}
			this.data('attr-old-value', r);
		}
	}
	const r = window.MutationObserver || window.WebKitMutationObserver;
	e.fn.attrchange = function (i) {
		const s = { trackValues: false, callback: e.noop };
		if (typeof i === 'function') {
			s.callback = i;
		} else {
			e.extend(s, i);
		}
		if (s.trackValues) {
			e(this).each(function (t, n) {
				const r = {};
				for (let i, t = 0, s = n.attributes, o = s.length; t < o; t++) {
					i = s.item(t);
					r[i.nodeName] = i.value;
				}
				e(this).data('attr-old-value', r);
			});
		}
		if (r) {
			const o = {
				subtree: false,
				attributes: true,
				attributeOldValue: s.trackValues,
			};
			const u = new r(function (t) {
				t.forEach(function (t) {
					const n = t.target;
					if (s.trackValues) {
						t.newValue = e(n).attr(t.attributeName);
					}
					s.callback.call(n, t);
				});
			});
			return this.each(function () {
				u.observe(this, o);
			});
		} else if (t()) {
			return this.on('DOMAttrModified', function (e) {
				if (e.originalEvent) e = e.originalEvent;
				e.attributeName = e.attrName;
				e.oldValue = e.prevValue;
				s.callback.call(this, e);
			});
		} else if ('onpropertychange' in document.body) {
			return this.on('propertychange', function (t) {
				t.attributeName = window.event.propertyName;
				n.call(e(this), s.trackValues, t);
				s.callback.call(this, t);
			});
		}
		return this;
	};
})(jQuery);

jQuery(window).on('load', function () {
	parent.customizerLoaded();
	cp_color_for_list_tag();
	cp_infobar_social_responsive();
});

jQuery(document).on('smile_data_on_load', function (e, data) {
	cp_ckeditors_setup(data);

	/**
	 * 	2.	Blinking cursor
	 *
	 * @param  string style Module Style Name
	 */
	const style = smile_global_data.style || null;
	const modal_title_color = smile_global_data.modal_title_color || null;

	cp_blinking_cursor('.cp-info-bar-msg');
	switch (style) {
		case 'blank':
			cp_blinking_cursor('.cp-content-editor', modal_title_color);
			break;
	}
});

/**
 * trigger smile_data_received
 */
jQuery(document).on('smile_data_received', function (e, data) {
	global_initial_call(data);
});

/**
 * Trigger ColorPicker Change
 */
parent
	.jQuery(window.parent.document)
	.on('smile-colorpicker-change', function (e, el, val) {
		if (jQuery(el).hasClass('bg_color')) {
			smile_global_data.bg_color = val;
			apply_gradient_color(smile_global_data);
		}

		if (jQuery(el).hasClass('bg_img_color')) {
			smile_global_data.bg_img_color = val;
			apply_gradient_color(smile_global_data);
		}

		//close image settings
		if (jQuery(el).hasClass('close_text_color')) {
			smile_global_data.close_text_color = val;
			cp_info_bar_close_img_settings(smile_global_data);
		}

		//close image settings
		if (jQuery(el).hasClass('toggle_button_bg_color')) {
			smile_global_data.toggle_button_bg_color = val;
			toggle_button_css(smile_global_data);
		}
	});

function global_initial_call(data) {
	const style = data.style || null;

	switch (style) {
		case 'free_trial':
			single__toggle_class(
				'.cp-image',
				'cp_ifb_hide_img',
				data.image_displayon_mobile,
				1
			);
			break;
	}

	cp_set_image(data, 'info_bar');

	//for info bar position
	cp_info_bar_position_setup(data);

	//animation
	cp_info_bar_animation_setup(data);

	//shadow
	enable_shadow(data);

	//border
	border_settings(data);

	//background setup
	if (typeof data.bg_color !== 'undefined') {
		apply_gradient_color(data);
	}

	//custom css
	cp_add_custom_css(data);

	// Background Image
	cp_update_bg_image(
		data,
		'.cp-info-bar-body',
		'',
		'info_bar_bg_image',
		'info_bar_bg_image_src'
	);

	//close image settings
	cp_info_bar_close_img_settings(data);

	//toggle_functionality
	cp_toggle_button(data);
	toggle_button_css(data);
	toggle_button_text(data);
	toggle_button_font(data);
}

/**
 * gradient color picker cahnge event
 */
parent.jQuery(window.parent.document).on('cp-grad-color-change', function () {
	apply_gradient_color(smile_global_data);
});

jQuery(window).on('pageshow', function () {
	// Width & Height values of image.
	const cp_image = jQuery('.cp-image-container img');
	cp_image.attr('width', cp_image.css('width'));
	cp_image.attr('height', cp_image.css('height'));
});

/**
 * trigger smile_customizer_field_change
 */
jQuery(document).on('smile_customizer_field_change', function (e, single_data) {
	//for info bar position
	if ('infobar_position' in single_data) {
		cp_info_bar_position_setup(single_data);
	}

	//	Animations
	if ('entry_animation' in single_data || 'exit_animation' in single_data) {
		cp_info_bar_animation_setup(smile_global_data);
	}

	//Animations
	if ('enable_shadow' in single_data) {
		enable_shadow(single_data);
	}

	//enable border
	if ('enable_border' in single_data) {
		border_settings(smile_global_data);
	}

	/**
	 * Info bar Image
	 */
	//	AJAX update image URL
	if (
		'info_bar_image_size' in single_data ||
		'info_bar_img_src' in single_data ||
		'info_bar_img_custom_url' in single_data
	) {
		cp_update_ajax_info_bar_image_src(smile_global_data);
	}

	/**
	 * Info bar Background Image
	 */
	//	AJAX update image size - full / thumbnail / medium etc.
	if ('info_bar_bg_image_size' in single_data) {
		cp_update_ajax_bg_image_size(
			smile_global_data,
			'.cp-info-bar-body',
			'',
			'info_bar_bg_image',
			'opt_bg'
		);
	}

	if (
		'info_bar_bg_image_src' in single_data ||
		'info_bar_bg_image_custom_url' in single_data
	) {
		cp_update_bg_image(
			smile_global_data,
			'.cp-info-bar-body',
			'',
			'info_bar_bg_image',
			'info_bar_bg_image_src'
		);
		apply_gradient_color(smile_global_data);
	}

	//	Background Image - 	repeat
	const opt_bg_rpt = single_data.opt_bg_rpt || null;
	if ('opt_bg_rpt' in single_data) {
		add_css('.cp-info-bar-body', 'background-repeat', opt_bg_rpt);
	}

	//	Background Image - 	position
	const opt_bg_pos = single_data.opt_bg_pos || null;
	if ('opt_bg_pos' in single_data) {
		add_css('.cp-info-bar-body', 'background-position', opt_bg_pos);
	}

	//	Background Image - 	size
	const opt_bg_size = single_data.opt_bg_size || null;
	if ('opt_bg_size' in single_data) {
		add_css('.cp-info-bar-body', 'background-size', opt_bg_size);
	}

	//close image settings
	if (
		'close_info_bar' in single_data ||
		'close_txt' in single_data ||
		'close_ib_image_src' in single_data
	) {
		cp_info_bar_close_img_settings(smile_global_data);
	}

	//toggle_functionality
	if ('toggle_btn' in single_data || 'toggle_btn_visible' in single_data) {
		cp_toggle_button(smile_global_data);
		toggle_button_css(smile_global_data);
	}

	//	toggle class - hide image
	const image_displayon_mobile = single_data.image_displayon_mobile || null;
	if ('image_displayon_mobile' in single_data) {
		single__toggle_class(
			'.cp-image',
			'cp_ifb_hide_img',
			image_displayon_mobile,
			1
		);
	}

	//toggle button css
	if ('toggle_btn_gradient' in single_data) {
		toggle_button_css(smile_global_data);
	}

	//toggle_button text
	if ('toggle_button_title' in single_data) {
		toggle_button_text(single_data);
	}

	//toggle_button font
	if ('toggle_button_font' in single_data) {
		toggle_button_font(single_data);
	}

	// gradient background
	if ('bg_gradient' in single_data) {
		apply_gradient_color(smile_global_data);
	}
});

jQuery(document).on('smile_data_continue_received', function (e, data) {
	//add custom css
	cp_add_custom_css(data);

	//close button settings
	cp_info_bar_close_img_settings(data);
});

/**
 * Update baground image size by AJAX
 *
 * Also, Replaced [data-css-image-url] with updated image size. [Which is used to updated image URL without AJAX.]
 *
 * @param {string} data Global Data values.
 */
/* eslint-disable no-unused-vars */
function cp_update_ajax_info_bar_bg_image_size(data) {
	const cp_info_bar_body = jQuery('.cp-info-bar-body'),
		info_bar_bg_image = data.info_bar_bg_image,
		info_bar_bg_image_size = data.info_bar_bg_image_size;

	let opt_bg = data.opt_bg;
	opt_bg = opt_bg.split('|');

	if (info_bar_bg_image !== '') {
		const img_data = {
			action: 'cp_get_image',
			img_id: info_bar_bg_image,
			size: info_bar_bg_image_size,
			security_nonce: media_nonce.media_nonce,
		};
		jQuery.ajax({
			url: smile_ajax.url,
			data: img_data,
			type: 'POST',
			success(img) {
				cp_info_bar_body.css({
					'background-image': 'url(' + img + ')',
				});

				//	UPDATE - [data-css-image-url] to get updated image URL. [Which is used to updated image URL without AJAX.]
				jQuery(
					'.smile-input[name="info_bar_bg_image"]',
					window.parent.document
				).attr('data-css-image-url', img);

				//	Set Background Image - Position, Repeat & Size
				image_positions(
					data,
					'.cp-info-bar-body',
					'',
					'info_bar_bg_image'
				);
			},
		});
	}
}

function apply_gradient_color(data) {
	const bg_color = data.bg_color,
		ifb_bg_gradient = data.bg_gradient,
		bg_img_color = data.bg_img_color,
		info_bar_bg_image_src = data.info_bar_bg_image_src;
	let ifb_bg_style = '';
	const overlay = jQuery('.cp-info-bar-body-overlay');

	if (typeof ifb_bg_gradient !== 'undefined' && ifb_bg_gradient === '1') {
		//	store it!
		const new_grad = jQuery(
				'.module_bg_gradient',
				window.parent.document
			).val(),
			val_arr = new_grad.split('|'),
			first_color = val_arr[0],
			sec_color = val_arr[1],
			first_deg = val_arr[2],
			sec_deg = val_arr[3],
			grad_type = val_arr[4],
			direction = val_arr[5];
		let grad_name = '';

		const gradient_val =
			first_color +
			'|' +
			sec_color +
			'|' +
			first_deg +
			'|' +
			sec_deg +
			'|' +
			grad_type +
			'|' +
			direction;

		jQuery('.module_bg_gradient', window.parent.document).val(gradient_val);

		switch (direction) {
			case 'center_left':
				grad_name = 'left';
				break;
			case 'center_Right':
				grad_name = 'right';
				break;

			case 'top_center':
				grad_name = 'top';
				break;

			case 'top_left':
				grad_name = 'top left';
				break;

			case 'top_right':
				grad_name = 'top right';
				break;

			case 'bottom_center':
				grad_name = 'bottom';
				break;

			case 'bottom_left':
				grad_name = 'bottom left';
				break;

			case 'bottom_right':
				grad_name = 'bottom right';
				break;

			case 'center_center':
				grad_name = 'center';
				if (grad_type === 'linear') {
					grad_name = 'top left';
				}
				break;

			case 'default':
				break;
		}

		let ie_css = '',
			web_css = '',
			o_css = '',
			mz_css = '';
		if (grad_type === 'linear') {
			ie_css =
				grad_type +
				'-gradient(to ' +
				grad_name +
				', ' +
				first_color +
				' ' +
				first_deg +
				'%, ' +
				sec_color +
				' ' +
				sec_deg +
				'%)';
			web_css =
				'-webkit-' +
				grad_type +
				'-gradient(' +
				grad_name +
				', ' +
				first_color +
				' ' +
				first_deg +
				'%, ' +
				sec_color +
				' ' +
				sec_deg +
				'%)';
			o_css =
				'-o-' +
				grad_type +
				'-gradient(' +
				grad_name +
				', ' +
				first_color +
				' ' +
				first_deg +
				'%, ' +
				sec_color +
				' ' +
				sec_deg +
				'%)';
			mz_css =
				'-moz-' +
				grad_type +
				'-gradient(' +
				grad_name +
				', ' +
				first_color +
				' ' +
				first_deg +
				'%, ' +
				sec_color +
				' ' +
				sec_deg +
				'%)';
		} else {
			ie_css =
				grad_type +
				'-gradient( ellipse farthest-corner at ' +
				grad_name +
				', ' +
				first_color +
				' ' +
				first_deg +
				'%, ' +
				sec_color +
				' ' +
				sec_deg +
				'%)';
			web_css =
				'-webkit-' +
				grad_type +
				'-gradient( ellipse farthest-corner at ' +
				grad_name +
				', ' +
				first_color +
				' ' +
				first_deg +
				'%, ' +
				sec_color +
				' ' +
				sec_deg +
				'%)';
			o_css =
				'-o-' +
				grad_type +
				'-gradient( ellipse farthest-corner at ' +
				grad_name +
				', ' +
				first_color +
				' ' +
				first_deg +
				'%, ' +
				sec_color +
				' ' +
				sec_deg +
				'%)';
			mz_css =
				'-moz-' +
				grad_type +
				'-gradient( ellipse farthest-corner at ' +
				grad_name +
				', ' +
				first_color +
				' ' +
				first_deg +
				'%, ' +
				sec_color +
				' ' +
				sec_deg +
				'%)';
		}
		/* eslint-disable no-dupe-keys */
		overlay.css({
			background: web_css,
			background: o_css,
			background: mz_css,
			background: ie_css,
		});
	} else {
		overlay.css({ background: '' });
		if (info_bar_bg_image_src === 'none') {
			ifb_bg_style +=
				'.cp-info-bar-body-overlay {' +
				'     background: ' +
				bg_color +
				'}';
		} else {
			ifb_bg_style +=
				'.cp-info-bar-body-overlay {' +
				'     background: ' +
				bg_img_color +
				'}';
		}
	}

	jQuery('#cp-ifb-bg-css').remove();

	jQuery(
		'<style id="cp-ifb-bg-css">' + ifb_bg_style + '</style>'
	).insertAfter('#cp-preview-css');
}

function add_css(selector, property, value) {
	jQuery(selector).css(property, value);
}

const cp_empty_classes = {
	'.cp-info-bar-msg': '.cp-msg-container',
	'.cp-info-bar-desc': '.cp-info-bar-desc-container',
	'.cp-content-editor': '.cp-content-container',
};

jQuery(document).ready(function () {
	jQuery('body').on('click', '.cp-ifb-toggle-btn', function (e) {
		parent.setFocusElement('toggle_button_title');
		e.stopPropagation();
	});
	jQuery('body').on('click', '.cp-submit', function (e) {
		parent.setFocusElement('btn_style');
		e.stopPropagation();
	});
	jQuery('body').on('click', '.cp-image', function (e) {
		parent.setFocusElement('info_bar_image');
		e.stopPropagation();
	});
	jQuery('body').on('click', '.cp-info-bar', function () {
		parent.setFocusElement('bg_color');
	});
	jQuery('body').on('click', '#button_editor', function (e) {
		parent.setFocusElement('button_bg_color');
		e.stopPropagation();
	});
	jQuery('body').on('click', '.ib-close', function (e) {
		parent.setFocusElement('close_img');
		e.stopPropagation();
	});
	jQuery('body').on('click', '#sec_button_editor', function (e) {
		parent.setFocusElement('ifb_button_bg_color');
		e.stopPropagation();
	});
	jQuery('body').on('click', '.cp-count-down-container', function (e) {
		parent.setFocusElement('counter_bg_color');
		e.stopPropagation();
	});

	//call to cp_add_empty_class if html in empty
	jQuery.each(cp_empty_classes, function (key, value) {
		if (jQuery(value).length !== 0) {
			jQuery(value).on('focusout', function () {
				cp_add_empty_class(key, value);
			});

			jQuery(value).on('focusin', function () {
				cp_remove_empty_class(value);
			});
		}
	});

	jQuery('html').css('overflow', 'hidden');

	if (jQuery('#title_editor').length !== 0) {
		// Turn off automatic editor creation first.
		CKEDITOR.inline('title_editor');

		//	Initially set show CKEditor of 'cp-title'
		//	Ref: http://docs.ckeditor.com/#!/api/CKEDITOR.focusManager
		const focusManager = new CKEDITOR.focusManager(
			CKEDITOR.instances.title_editor
		);
		focusManager.focus();

		CKEDITOR.instances.title_editor.config.toolbar = 'Small';
		CKEDITOR.instances.title_editor.on('change', function () {
			// Remove Blinking cursor
			jQuery('.cp-info-bar-container').find('.blinking-cursor').remove();

			const data = CKEDITOR.instances.title_editor.getData();
			parent.updateHTML(htmlEntities(data), 'smile_infobar_title');
			cp_color_for_list_tag();
		});

		// Use below code to 'reinitialize' CKEditor
		// IN ANY CASE IF CKEDITOR IS NOT INITIALIZED THEN USE BELOW CODE
		CKEDITOR.instances.title_editor.on('instanceReady', function (ev) {
			const editor = ev.editor;
			editor.setReadOnly(false);
		});
	}

	if (jQuery('#description_editor').length !== 0) {
		// Turn off automatic editor creation first.
		CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;
		CKEDITOR.inline('description_editor');
		CKEDITOR.instances.description_editor.config.toolbar = 'Small';
		CKEDITOR.instances.description_editor.on('change', function () {
			const data = CKEDITOR.instances.description_editor.getData();
			parent.updateHTML(htmlEntities(data), 'smile_infobar_description');
			cp_color_for_list_tag();
		});

		// Use below code to 'reinitialize' CKEditor
		// IN ANY CASE IF CKEDITOR IS NOT INITIALIZED THEN USE BELOW CODE
		CKEDITOR.instances.description_editor.on(
			'instanceReady',
			function (ev) {
				const editor = ev.editor;
				editor.setReadOnly(false);
			}
		);
	}

	if (jQuery('#ib_content_editor').length !== 0) {
		// Turn off automatic editor creation first.
		CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;
		CKEDITOR.inline('ib_content_editor');
		CKEDITOR.instances.ib_content_editor.config.toolbar = 'Small';
		CKEDITOR.instances.ib_content_editor.on('change', function () {
			const data = CKEDITOR.instances.ib_content_editor.getData();
			parent.updateHTML(htmlEntities(data), 'smile_info_bar_content');
			cp_color_for_list_tag();
		});

		// Use below code to 'reinitialize' CKEditor
		// IN ANY CASE IF CKEDITOR IS NOT INITIALIZED THEN USE BELOW CODE
		CKEDITOR.instances.ib_content_editor.on('instanceReady', function (ev) {
			const editor = ev.editor;
			editor.setReadOnly(false);
		});
	}

	//close infobar on click of toggle button
	jQuery('body').on('click', '.cp-ifb-toggle-btn', show_ifb);

	//close modal on click of button
	jQuery('body').on('click', '.ib-close', close_ifb);

	//another submit button
	if (jQuery('#sec_button_editor').length !== 0) {
		// Turn off automatic editor creation first.
		CKEDITOR.config.enterMode = CKEDITOR.ENTER_BR;
		CKEDITOR.inline('sec_button_editor');
		CKEDITOR.instances.sec_button_editor.config.toolbar = 'Small';
		CKEDITOR.instances.sec_button_editor.on('change', function () {
			const data = CKEDITOR.instances.sec_button_editor.getData();
			parent.updateHTML(htmlEntities(data), 'smile_ifb_button_title');
			cp_color_for_list_tag();
		});

		// Use below code to 'reinitialize' CKEditor
		// IN ANY CASE IF CKEDITOR IS NOT INITIALIZED THEN USE BELOW CODE
		CKEDITOR.instances.sec_button_editor.on('instanceReady', function (ev) {
			const editor = ev.editor;
			editor.setReadOnly(false);
		});
	}

	// remove blinking cursor
	jQuery('body').on(
		'click select',
		'.cp-highlight,.cp-name,.cp-email',
		function () {
			jQuery('.cp-info-bar-container').find('.blinking-cursor').remove();
		}
	);

	jQuery('button').on('click', function (e) {
		e.preventDefault();
	});
	jQuery(this).on('submit', 'form', function (e) {
		e.preventDefault();
	});

	jQuery('#title_editor').attrchange({
		callback() {
			const cHeight = jQuery('.cp-info-bar').outerHeight();
			if (jQuery('body').hasClass('managePageDown')) {
				jQuery('body')
					.stop()
					.animate({ marginTop: cHeight + 'px' }, 1000);
			}
		},
	});

	jQuery('#description_editor').attrchange({
		callback() {
			const cHeight = jQuery('.cp-info-bar').outerHeight();
			if (jQuery('body').hasClass('managePageDown')) {
				jQuery('body')
					.stop()
					.animate({ marginTop: cHeight + 'px' }, 1000);
			}
		},
	});

	jQuery('#ib_content_editor').attrchange({
		callback() {
			const cHeight = jQuery('.cp-info-bar').outerHeight();
			if (jQuery('body').hasClass('managePageDown')) {
				jQuery('body')
					.stop()
					.animate({ marginTop: cHeight + 'px' }, 1000);
			}
		},
	});
});

/**
 * Removes &nbsp; and <br> tags from html string
 *
 * @param {string} string HTML String.
 */
function cp_get_clean_string(string) {
	let cleanString = string
		.replace(/[<]br[^>]*[>]/gi, '')
		.replace(/[&]nbsp[;]/gi, '')
		.replace(/[\u200B]/g, '');
	cleanString = cleanString.trim();
	return cleanString;
}

/**
 * Add cp-empty class
 *
 * @param {string} element   Class name
 * @param {string} container Container class
 */
function cp_add_empty_class(element, container) {
	const cleanString = cp_get_clean_string(jQuery(element).html());

	//  title
	if (cleanString.length === 0) {
		jQuery(container).addClass('cp-empty');
		jQuery(element).html(cleanString);
	} else {
		jQuery(container).removeClass('cp-empty');
	}
}

/**
 * Removes cp-empty class from container
 *
 * @param {string} element Class name
 */
function cp_remove_empty_class(element) {
	if (jQuery(element).length !== 0) {
		jQuery(element).removeClass('cp-empty');
	}
}

/**
 * for editor setup
 *
 * @param {string} data Global Data values.
 */
function cp_ckeditors_setup(data) {
	const cp_title = jQuery('#title_editor'),
		ib_content_editor = jQuery('#ib_content_editor'),
		cp_info_bar_desc = jQuery('.cp-info-bar-desc'),
		cp_second_submit_btn = jQuery('.cp-second-submit-btn'),
		varient_style_id = data.variant_style_id,
		cp_info_bar = jQuery('.cp-info-bar');

	let content = data.info_bar_content,
		title = data.infobar_title,
		description = data.infobar_description,
		ifb_button_title = data.ifb_button_title,
		style_id = data.style_id;

	if (varient_style_id !== '' && typeof varient_style_id !== 'undefined') {
		style_id = varient_style_id;
	}

	//add style id as class to container
	cp_info_bar.addClass(style_id);

	//title
	title = htmlEntities(title);
	cp_title.html(title);
	if (
		jQuery('#title_editor').length !== 0 &&
		CKEDITOR.instances.title_editor !== undefined
	) {
		CKEDITOR.instances.title_editor.setData(title);
	}

	//description
	description = htmlEntities(description);
	cp_info_bar_desc.html(description);
	if (
		jQuery('#description_editor').length !== 0 &&
		CKEDITOR.instances.description_editor !== undefined
	) {
		CKEDITOR.instances.description_editor.setData(description);
	}

	//content
	content = htmlEntities(content);
	ib_content_editor.html(content);
	if (
		jQuery('#ib_content_editor').length !== 0 &&
		CKEDITOR.instances.ib_content_editor !== undefined
	) {
		CKEDITOR.instances.ib_content_editor.setData(content);
	}

	//second button
	ifb_button_title = htmlEntities(ifb_button_title);
	cp_second_submit_btn.html(ifb_button_title);
	if (
		jQuery('#sec_button_editor').length !== 0 &&
		CKEDITOR.instances.sec_button_editor !== undefined
	) {
		CKEDITOR.instances.sec_button_editor.setData(ifb_button_title);
	}
}

/**
 * for close image setup
 *
 * @param {string} data Global Data values.
 */
function cp_info_bar_close_img_settings(data) {
	const cp_close = jQuery('.ib-close'),
		ib_body_container = jQuery('.cp-info-bar-body'),
		close_info_bar = data.close_info_bar,
		close_txt = data.close_txt,
		close_text_color = data.close_text_color,
		close_img = data.close_img,
		close_img_size = data.close_img_size,
		close_ib_image_src = data.close_ib_image_src,
		close_img_position = data.close_info_bar_pos,
		adjacent_close_position = data.adjacent_close_position;

	let adj_class = '';

	switch (adjacent_close_position) {
		case 'top_left':
			adj_class = 'cp-adjacent-left';
			break;
		case 'top_right':
			adj_class = 'cp-adjacent-right';
			break;
		case 'bottom_left':
			adj_class = 'cp-adjacent-bottom-left';
			break;
		case 'bottom_right':
			adj_class = 'cp-adjacent-bottom-right';
			break;
	}

	cp_close.show();
	cp_close.removeAttr('class');

	if (close_img_position === '0') {
		cp_close.appendTo(jQuery('.cp-info-bar-body'));
		ib_body_container
			.addClass('ib-close-inline')
			.removeClass('ib-close-outside');
	} else {
		cp_close.appendTo(jQuery('.cp-info-bar-container'));
		ib_body_container
			.addClass('ib-close-outside')
			.removeClass('ib-close-inline');
	}

	if (close_info_bar === 'close_img') {
		if (close_ib_image_src === 'upload_img') {
			cp_close.addClass('ib-close ib-img-close');
			if (close_img.indexOf('http') === -1) {
				const img_data = {
					action: 'cp_get_image',
					img_id: close_img,
					size: close_img_size,
					security_nonce: media_nonce.media_nonce,
				};
				jQuery.ajax({
					url: smile_ajax.url,
					data: img_data,
					type: 'POST',
					success(img) {
						cp_close.html('<img src="' + img + '"/>');
						jQuery(document).trigger('cp_ajax_loaded', [data]);
					},
				});
			} else {
				const close_img_full_src = close_img.split('|');
				const close_img_src = close_img_full_src[0];
				cp_close.html(
					'<img class="ib-img-default" src="' + close_img_src + '"/>'
				);
			}
		} else if (close_ib_image_src === 'custom_url') {
			cp_close.addClass('ib-close ib-img-close');
			const info_bar_close_img_custom_url =
				data.info_bar_close_img_custom_url;
			cp_close.html(
				'<img src="' + info_bar_close_img_custom_url + '" />'
			);
			jQuery('.cp-overlay-close').removeClass('cp-text-close');
		} else if (close_ib_image_src === 'pre_icons') {
			cp_close.addClass('ib-close ib-img-close');
			const close_icon = data.close_icon;
			/* eslint-disable no-undef */
			const close_icon_url =
				cp.module_img_dir + '/' + close_icon + '.png';
			cp_close.html('<img src="' + close_icon_url + '"/>');
			jQuery('.cp-overlay-close').removeClass('cp-text-close');
		} else {
			cp_close.addClass('ib-close');
			cp_close.hide();
		}
	} else if (close_info_bar === 'close_txt') {
		cp_close.removeAttr('style');
		cp_close.addClass('ib-close ib-text-close');
		cp_close.html(
			'<span style="color:' +
				close_text_color +
				'">' +
				close_txt +
				'</span>'
		);
	} else {
		cp_close.addClass('ib-close');
		cp_close.hide();
	}

	cp_close.removeClass(
		'cp-adjacent-left cp-adjacent-right cp-adjacent-bottom-left cp-adjacent-bottom-right'
	);
	cp_close.addClass(adj_class);
}

/**
 * for info bar  animation
 *
 * @param {string} data Global Data values.
 */
function cp_info_bar_animation_setup(data) {
	const cp_animate = jQuery('.cp-info-bar'),
		exit_animation = data.exit_animation,
		entry_animation = data.entry_animation,
		toggle_btn_visible = data.toggle_btn_visible;

	const entry_anim =
		typeof cp_animate.attr('data-entry-animation') !== 'undefined'
			? cp_animate.attr('data-entry-animation')
			: '';
	const exit_anim =
		typeof cp_animate.attr('data-exit-animation') !== 'undefined'
			? cp_animate.attr('data-exit-animation')
			: '';

	cp_animate.removeClass('smile-animated');

	if (cp_animate.hasClass('cp-ifb-hide') || toggle_btn_visible === '1') {
		// do not apply animations to info bar
		cp_animate.attr('data-entry-animation', entry_animation);
		cp_animate.attr('data-exit-animation', exit_animation);
	} else {
		if (
			!cp_animate.hasClass(exit_animation) &&
			exit_animation !== exit_anim
		) {
			setTimeout(function () {
				if (exit_animation !== 'none') {
					cp_animate.removeClass(exit_anim);
					cp_animate.removeClass(entry_anim);
					cp_animate.addClass('smile-animated ' + exit_animation);
					cp_animate.attr('data-entry-animation', entry_animation);
					cp_animate.attr('data-exit-animation', exit_animation);
				}
				setTimeout(function () {
					cp_animate.removeClass(exit_anim);
					cp_animate.removeClass(exit_animation);
					cp_animate.removeClass(entry_anim);
					cp_animate.addClass('smile-animated ' + entry_anim);
					cp_animate.attr('data-exit-animation', exit_animation);
				}, 1000);
			}, 500);
		}

		if (
			!cp_animate.hasClass(entry_animation) &&
			entry_animation !== entry_anim
		) {
			setTimeout(function () {
				if (entry_animation !== 'none') {
					cp_animate.removeClass(exit_anim);
					cp_animate.removeClass(entry_anim);
					cp_animate.addClass('smile-animated ' + entry_animation);
					cp_animate.attr('data-entry-animation', entry_animation);
					cp_animate.attr('data-exit-animation', exit_animation);
				}
			}, 500);
		}
	}
}

/**
 * info bar position -top/bottom/fixed
 *
 * @param {string} data Global Data values.
 */
function cp_info_bar_position_setup(data) {
	const cp_info_bar = jQuery('.cp-info-bar'),
		position = data.infobar_position,
		animate_push_page = data.animate_push_page;

	let page_down = data.page_down,
		ib_height = '';

	//for top/bottom position
	if (!cp_info_bar.hasClass(position)) {
		cp_info_bar.removeClass('cp-pos-top');
		cp_info_bar.removeClass('cp-pos-bottom');
		cp_info_bar.addClass(position);
	}
	page_down = parseInt(page_down);

	cp_info_bar.addClass('ib-fixed');

	ib_height = cp_info_bar.outerHeight();

	if (position === 'cp-pos-top') {
		cp_info_bar.css('top', '0');
		if (page_down) {
			jQuery('body').addClass('managePageDown');
			if (animate_push_page === '1') {
				jQuery('body')
					.stop()
					.animate({ marginTop: ib_height + 'px' }, 1000);
			} else {
				jQuery('body').css('margin-top', ib_height + 'px');
			}
		} else {
			jQuery('body').removeClass('managePageDown');
			if (animate_push_page === '1') {
				jQuery('body').stop().animate({ marginTop: '0px' }, 1000);
			} else {
				jQuery('body').css('margin-top', '0px');
			}
			setTimeout(function () {
				jQuery('body').removeAttr('style');
			}, 1500);
		}
	} else {
		cp_info_bar.css('top', 'auto');
		jQuery('body').removeAttr('style');
	}
}

/**
 * Enable shadow
 *
 * @param {string} data Global Data values.
 */
function enable_shadow(data) {
	const cp_info_bar = jQuery('.cp-info-bar');

	if (
		typeof data.enable_shadow !== 'undefined' &&
		data.enable_shadow === '1'
	) {
		cp_info_bar.addClass('cp-info-bar-shadow');
	} else {
		cp_info_bar.removeClass('cp-info-bar-shadow');
	}
}

/**
 *Enable Border
 *
 * @param {string} data Global Data values.
 */
function border_settings(data) {
	const cp_info_bar = jQuery('.cp-info-bar'),
		enable_border = data.enable_border,
		bg_color = data.bg_color;
	let border_style = '',
		border_darken = data.border_darken;

	if (cp_isValid(enable_border) && enable_border === '1') {
		jQuery('#cp-border-style').remove();
		cp_info_bar.addClass('cp-info-bar-border');

		//	If border color is not set then add bg color darken color as a border color
		if (border_darken === '') {
			// Generate the BORDER COLOR
			border_darken = darkerColor(bg_color, 0.05);

			//	store it!
			jQuery('#smile_border_darken', window.parent.document).val(
				border_darken
			);
		}

		border_style +=
			'.cp-pos-top.cp-info-bar-border {' +
			'     border-bottom: 2px solid ' +
			border_darken +
			'}' +
			'.cp-pos-bottom.cp-info-bar-border {' +
			'     border-top: 2px solid ' +
			border_darken +
			'}';

		jQuery('head').append(
			'<style id="cp-border-style">' + border_style + '</style>'
		);
	} else {
		cp_info_bar.removeClass('cp-info-bar-border');
	}
}

/**
 * Adds custom css
 *
 * @param {string} data Global Data values.
 */
function cp_add_custom_css(data) {
	const custom_css = data.custom_css || null;
	if (cp_isValid(custom_css)) {
		jQuery('#cp-custom-style').remove();
		jQuery('head').append(
			'<style id="cp-custom-style">' + custom_css + '</style>'
		);
	}
}

/**
 * Remove - Background Image
 *
 * Also, Replaced [data-css-image-url] with empty. [Which is used to updated image URL without AJAX.]
 */
parent.jQuery(window.parent.document).on('cp-image-remove', function () {
	const cp_info_bar_body = jQuery('.cp-info-bar-body');
	cp_info_bar_body.css({ 'background-image': '' });
	//	REMOVE - [data-css-image-url] to get updated image for FULLWIDTH
	jQuery(
		'.smile-input[name="info_bar_bg_image"]',
		window.parent.document
	).attr('data-css-image-url', '');
});

/**
 * Change - Background image
 */
parent
	.jQuery(window.parent.document)
	.on('cp-image-change', function (e, name, url, val) {
		//	Info bar - Background Image
		// Process for Info bar background image - for variable 'info_bar_bg_image'
		if (
			name === 'info_bar_bg_image' &&
			name !== 'undefined' &&
			name !== null
		) {
			cp_change_bg_img(
				smile_global_data,
				'.cp-info-bar-body',
				'',
				name,
				'opt_bg',
				url,
				val
			);
		}

		//	Info bar - Image
		// Process for info bar image - for variable 'info_bar_image'
		if (
			name === 'info_bar_image' &&
			name !== 'undefined' &&
			name !== null
		) {
			const info_bar_image_size = smile_global_data.info_bar_image_size,
				cp_img_container = jQuery('.cp-image-container img');

			//	Changed images is always big.
			//	So, If image size is != full then call the image though AJAX
			if (
				typeof info_bar_image_size !== undefined &&
				info_bar_image_size !== 'full'
			) {
				//	Update image - ID|SIZE
				cp_update_ajax_info_bar_image_src(smile_global_data);
			} else {
				cp_img_container.attr('src', url);
			}
		}
	});

parent
	.jQuery(window.parent.document)
	.on('cp-image-default', function (e, name, url, val) {
		//	Info bar - Background Image
		// Process for Info bar  background image - for variable 'info_bar_bg_image'
		if (
			name === 'info_bar_bg_image' &&
			name !== 'undefined' &&
			name !== null
		) {
			cp_change_bg_img(
				smile_global_data,
				'.cp-info-bar-body',
				'',
				name,
				'opt_bg',
				url,
				val
			);
		}

		//	Info bar - Image
		// Process for Info bar image - for variable 'info_bar_image'
		if (
			name === 'info_bar_image' &&
			name !== 'undefined' &&
			name !== null
		) {
			const info_bar_image_size = smile_global_data.info_bar_image_size,
				cp_img_container = jQuery('.cp-image-container img');

			smile_global_data.info_bar_image = url;

			//	Changed images is always big.
			//	So, If image size is != full then call the image though AJAX
			if (
				typeof info_bar_image_size !== undefined &&
				info_bar_image_size !== 'full'
			) {
				//	Update image - ID|SIZE
				cp_update_ajax_info_bar_image_src(smile_global_data);
			} else {
				cp_img_container.attr('src', url);
			}
		}

		jQuery('.smile-input[name=' + name + ']', window.parent.document).attr(
			'data-css-image-url',
			url
		);
	});

/**
 * Update Info bar Image URL by AJAX
 *
 * @param {string} data Global Data values.
 */
function cp_update_ajax_info_bar_image_src(data) {
	const info_bar_image_size = data.info_bar_image_size,
		info_bar_image = data.info_bar_image,
		info_bar_image_src = data.info_bar_img_src,
		infobar_img = jQuery('.cp-image-container img');

	switch (info_bar_image_src) {
		case 'upload_img':
			// 	file not exists
			if (
				typeof info_bar_image !== 'undefined' &&
				info_bar_image.indexOf('http') === -1 &&
				info_bar_image !== ''
			) {
				const image_details = info_bar_image.split('|'),
					img_id = image_details[0],
					img_size = info_bar_image_size;

				const img_data = {
					action: 'cp_get_image',
					img_id,
					size: img_size,
				};
				jQuery.ajax({
					url: smile_ajax.url,
					data: img_data,
					type: 'POST',
					success(img_url) {
						infobar_img.attr('src', img_url);
					},
				});
			} else if (
				typeof info_bar_image !== 'undefined' &&
				Number(info_bar_image.indexOf('http')) !== -1
			) {
				if (info_bar_image.indexOf('|') !== -1) {
					const url = info_bar_image.split('|');
					infobar_img.attr('src', url[0]);
				} else {
					jQuery('.cp-image-container img').attr(
						'src',
						info_bar_image
					);
				}
			} else {
				const img_src = jQuery(
					'.smile-input[name="info_bar_image"]',
					window.parent.document
				).attr('data-css-image-url');
				infobar_img.attr('src', img_src);
			}
			break;
		case 'custom_url':
			const custom_url = data.info_bar_img_custom_url;
			infobar_img.attr('src', custom_url);
			break;

		case 'none':
			infobar_img.attr('src', '');
			break;
	}
}

/**
 * for single toggle class
 *
 * @param {string} selector     Selector for toggle class.
 * @param {string} toggle_class Toggle Class Name.
 * @param {string} value        Value string.
 * @param {string} required     Required value.
 */
function single__toggle_class(selector, toggle_class, value, required) {
	if (
		(typeof value !== 'undefined' && value === '') ||
		Number(value) === required
	) {
		jQuery(selector).addClass(toggle_class);
	} else {
		jQuery(selector).removeClass(toggle_class);
	}
}

/**
 * Change color of li inside editor
 */
function cp_color_for_list_tag() {
	jQuery('.cp-info-bar').each(function () {
		const moadal_style = 'cp-info-bar';

		jQuery(this)
			.find('li')
			.each(function () {
				if (
					jQuery(this).parents('.cp_social_networks').length === 0 &&
					jQuery(this).parents('.custom-html-form').length === 0
				) {
					const parent_li = jQuery(this)
							.parents('div')
							.attr('class')
							.split(' ')[0],
						cnt = jQuery(this).index() + 1,
						font_size = jQuery(this)
							.find('.cp_font')
							.css('font-size');
					let color = jQuery(this).find('span').css('color'),
						style_type = '';

					let list_type = jQuery(this).parent();
					list_type = list_type[0].nodeName.toLowerCase();

					//apply style type to list
					if (list_type === 'ul') {
						style_type = jQuery(this)
							.closest('ul')
							.css('list-style-type');
						if (style_type === 'none') {
							jQuery(this)
								.closest('ul')
								.css('list-style-type', 'disc');
						}
					} else {
						style_type = jQuery(this)
							.closest('ol')
							.css('list-style-type');
						if (style_type === 'none') {
							jQuery(this)
								.closest('ol')
								.css('list-style-type', 'decimal');
						}
					}

					//apply color to list
					jQuery(this)
						.find('span')
						.each(function () {
							const spancolor = jQuery(this).css('color');
							if (spancolor.length > 0) {
								color = spancolor;
							}
						});

					let font_style = '';
					jQuery('.cp-li-color-css-' + cnt).remove();
					jQuery('.cp-li-font-css-' + cnt).remove();
					if (font_size) {
						font_style = 'font-size:' + font_size;
						jQuery('head').append(
							'<style class="cp-li-font-css' +
								cnt +
								'">.' +
								moadal_style +
								' .' +
								parent_li +
								' li:nth-child(' +
								cnt +
								'){ ' +
								font_style +
								'}</style>'
						);
					}
					if (color) {
						jQuery('head').append(
							'<style class="cp-li-color-css' +
								cnt +
								'">.' +
								moadal_style +
								' .' +
								parent_li +
								' li:nth-child(' +
								cnt +
								'){ color: ' +
								color +
								';}</style>'
						);
					}
				}
			});
	});
}
/**
 * Toggle button text
 *
 * @param {string} data Global Data values.
 */
function toggle_button_font(data) {
	let font = 'sans-serif',
		toggle_font_style = '';

	jQuery('#cp-toggle-btn-font-css').remove();
	jQuery('head').append('<div id="cp-toggle-btn-font-css"></div>');

	if (data.toggle_button_font) {
		font = data.toggle_button_font + ',' + font;
	}
	toggle_font_style +=
		'.cp-ifb-toggle-btn {' + '     font-family: ' + font + ';' + '}';

	//Append ALL CSS
	jQuery('#cp-toggle-btn-font-css').html(
		'<style>' + toggle_font_style + '</style>'
	);
}

/**
 * toggle_button_text
 *
 * @param {string} data Global Data values.
 */
function toggle_button_text(data) {
	const toggle_button_title = data.toggle_button_title,
		cp_ifb_toggle_btn = jQuery('.cp-ifb-toggle-btn');

	cp_ifb_toggle_btn.html(toggle_button_title);
}

/**
 * cp_toggle_button settings
 *
 * @param {string} data Global Data values.
 * @param {string} e
 */
function cp_toggle_button(data, e) {
	let toggle_btn = data.toggle_btn;
	const close_info_bar = data.close_info_bar,
		toggle_btn_visible = data.toggle_btn_visible,
		cp_ifb_toggle_btn = jQuery('.cp-ifb-toggle-btn'),
		cp_info_bar_body = jQuery('.cp-info-bar-body'),
		cp_info_bar_container = jQuery('.cp-info-bar-container');

	//	Disable toggle if
	//	Close Link is == 'do_not_close'
	if (close_info_bar === 'do_not_close') {
		toggle_btn = 0;
	}

	if (toggle_btn === '1') {
		cp_info_bar_container.addClass('cp-ifb-with-toggle');
	} else {
		cp_info_bar_container.removeClass('cp-ifb-with-toggle');
		cp_info_bar_container.removeClass('cp-ifb-click');
	}

	const name = 'cp-unsaved-changes';
	let is_cookie = '';
	const nameEQ = name + '=';
	const ca = document.cookie.split(';');
	for (let i = 0; i < ca.length; i++) {
		let c = ca[i];
		while (c.charAt(0) === ' ') {
			c = c.substring(1, c.length);
		}
		if (Number(c.indexOf(nameEQ)) === 0) {
			is_cookie = c.substring(nameEQ.length, c.length);
		}
	}

	let btn_animation = '',
		exit_animation = '',
		entry_animation = '',
		cp_info_bar_class = '';

	if (toggle_btn === '1' && toggle_btn_visible === '1') {
		if (jQuery('.cp-info-bar-container').hasClass('cp-ifb-with-toggle')) {
			btn_animation = 'smile-slideInDown';
			exit_animation =
				typeof cp_info_bar_container.data('exit-animation') !==
				'undefined'
					? cp_info_bar_container.data('exit-animation')
					: '';
			entry_animation =
				typeof cp_info_bar_container.data('entry-animation') !==
				'undefined'
					? cp_info_bar_container.data('entry-animation')
					: '';

			if (cp_info_bar_container.hasClass('cp-pos-bottom')) {
				btn_animation = 'smile-slideInUp';
			}
			cp_info_bar_body.removeClass('cp-flex');
			cp_info_bar_container.removeClass(entry_animation);

			cp_info_bar_container.removeClass('smile-animated');

			cp_info_bar_class = cp_info_bar_container.attr('class');
			cp_info_bar_container.attr(
				'class',
				cp_info_bar_class + ' ' + exit_animation
			);

			setTimeout(function () {
				cp_ifb_toggle_btn.removeClass('cp-ifb-hide');
				cp_ifb_toggle_btn.addClass(
					'cp-ifb-show smile-animated ' + btn_animation + ''
				);
				cp_info_bar_container.removeClass('smile-animated');
				cp_info_bar_container.addClass('cp-ifb-hide');
			}, 500);
		} else {
			e.preventDefault();
		}
	} else {
		btn_animation = 'smile-slideInDown';
		exit_animation =
			typeof cp_info_bar_container.data('exit-animation') !== 'undefined'
				? cp_info_bar_container.data('exit-animation')
				: '';
		entry_animation =
			typeof cp_info_bar_container.data('entry-animation') !== 'undefined'
				? cp_info_bar_container.data('entry-animation')
				: '';
		cp_info_bar_container.removeClass('cp-ifb-hide');
		cp_info_bar_container.removeClass('smile-animated');
		cp_info_bar_container.removeClass(entry_animation);
		cp_info_bar_container.removeClass(exit_animation);
		if (cp_info_bar_container.hasClass('cp-pos-bottom')) {
			btn_animation = 'smile-slideInUp';
		}

		cp_info_bar_class = cp_info_bar_container.attr('class');
		cp_ifb_toggle_btn.removeClass(
			'cp-ifb-show smile-animated ' + btn_animation + ''
		);
		cp_info_bar_container.attr(
			'class',
			cp_info_bar_class + ' smile-animated ' + entry_animation
		);

		setTimeout(function () {
			cp_ifb_toggle_btn.addClass('cp-ifb-hide');
			cp_info_bar_body.addClass('cp-flex');
		}, 10);
	}
}

/**
 * show infobar on click of toggle button
 *
 */
function show_ifb() {
	const cp_ifb_toggle_btn = jQuery('.cp-ifb-toggle-btn'),
		cp_info_bar_body = jQuery('.cp-info-bar-body'),
		cp_info_bar_container = jQuery('.cp-info-bar-container'),
		exit_animation = smile_global_data.exit_animation,
		entry_animation = smile_global_data.entry_animation;

	let btn_animation = 'smile-slideInDown';

	cp_info_bar_container.removeClass('cp-ifb-hide');
	cp_info_bar_container.removeClass(entry_animation);
	cp_info_bar_container.removeClass(exit_animation);
	if (cp_info_bar_container.hasClass('cp-pos-bottom')) {
		btn_animation = 'smile-slideInUp';
	}

	cp_info_bar_container.addClass('cp-ifb-click');

	const cp_info_bar_class = cp_info_bar_container.attr('class');

	cp_ifb_toggle_btn.removeClass(
		'cp-ifb-show smile-animated ' + btn_animation + ''
	);

	cp_info_bar_container.attr(
		'class',
		cp_info_bar_class + ' smile-animated ' + entry_animation
	);

	setTimeout(function () {
		cp_ifb_toggle_btn.addClass('cp-ifb-hide');
		cp_info_bar_body.addClass('cp-flex');
	}, 10);
}

/**
 * hide info bar on click of toggle button
 *
 * @param {string} e
 */
function close_ifb(e) {
	if (jQuery('.cp-info-bar-container').hasClass('cp-ifb-with-toggle')) {
		const cp_ifb_toggle_btn = jQuery('.cp-ifb-toggle-btn'),
			cp_info_bar_body = jQuery('.cp-info-bar-body'),
			cp_info_bar_container = jQuery('.cp-info-bar-container'),
			exit_animation = smile_global_data.exit_animation,
			entry_animation = smile_global_data.entry_animation;

		let btn_animation = 'smile-slideInDown';

		if (cp_info_bar_container.hasClass('cp-pos-bottom')) {
			btn_animation = 'smile-slideInUp';
		}

		cp_info_bar_container.removeClass('cp-ifb-click');
		cp_info_bar_container.removeClass(entry_animation);

		const cp_info_bar_class = cp_info_bar_container.attr('class');
		cp_info_bar_container.attr(
			'class',
			cp_info_bar_class + ' smile-animated ' + exit_animation
		);

		setTimeout(function () {
			cp_ifb_toggle_btn.removeClass('cp-ifb-hide');
			cp_ifb_toggle_btn.addClass(
				'cp-ifb-show smile-animated ' + btn_animation + ''
			);
			cp_info_bar_container.removeClass('smile-animated');
			cp_info_bar_container.addClass('cp-ifb-hide');
			cp_info_bar_body.removeClass('cp-flex');
		}, 500);
	} else {
		e.preventDefault();
	}
}

/**
 * Toggle button css
 *
 * @param {string} data Global Data values.
 */
function toggle_button_css(data) {
	const toggle_btn_gradient = data.toggle_btn_gradient,
		toggle_button_bg_color = data.toggle_button_bg_color,
		cp_ifb_toggle_btn = jQuery('.cp-ifb-toggle-btn');

	const c_normal = toggle_button_bg_color;
	const c_hover = darkerColor(toggle_button_bg_color, 0.05);
	const light = lighterColor(toggle_button_bg_color, 0.3);

	cp_ifb_toggle_btn.css('background', c_normal);

	// button animation
	const button_class = cp_ifb_toggle_btn.attr('class');
	cp_ifb_toggle_btn.attr('class', button_class);

	//  button style
	const slideclassList = [
		'cp-btn-flat',
		'cp-btn-3d',
		'cp-btn-outline',
		'cp-btn-gradient',
	];
	jQuery.each(slideclassList, function (i, v) {
		cp_ifb_toggle_btn.removeClass(v);
	});

	let toggle_btn_style = '';
	if (toggle_btn_gradient === '1') {
		toggle_btn_style = 'cp-btn-gradient';
	} else {
		toggle_btn_style = 'cp-btn-flat';
	}
	cp_ifb_toggle_btn.addClass(toggle_btn_style);

	jQuery('#cp-toggle-btn-inline-css').remove();
	jQuery('head').append('<div id="cp-toggle-btn-inline-css"></div>');

	switch (toggle_btn_style) {
		case 'cp-btn-flat':
			jQuery('#cp-toggle-btn-inline-css').html(
				'<style>' +
					'.cp-ifb-toggle-btn.' +
					toggle_btn_style +
					'{ background: ' +
					c_normal +
					'!important;' +
					'; } ' +
					'.cp-ifb-toggle-btn.' +
					toggle_btn_style +
					':hover { background: ' +
					c_hover +
					'!important; } ' +
					'</style>'
			);
			break;

		case 'cp-btn-gradient': //  Apply box shadow to submit button - If its set & equals to - 1
			jQuery('#cp-toggle-btn-inline-css').html(
				'<style>' +
					'.cp-ifb-toggle-btn.' +
					toggle_btn_style +
					' {' +
					//   + '     border: none ;'
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
					'.cp-ifb-toggle-btn.' +
					toggle_btn_style +
					':hover {' +
					'     background: ' +
					c_normal +
					' !important;' +
					'}' +
					'</style>'
			);
			break;
	}

	//  Set either 10% darken color for 'HOVER'
	//  Or 0.10% darken color for 'GRADIENT'
	jQuery('#smile_toggle_button_bg_hover_color', window.parent.document).val(
		c_hover
	);
	jQuery(
		'#smile_toggle_button_bg_gradient_color',
		window.parent.document
	).val(light);
}

/**
 *for responsive social media icon*
 */
function cp_infobar_social_responsive() {
	const wh = jQuery(window).width();
	jQuery('.cp-info-bar-body')
		.find('.cp_social_networks')
		.each(function () {
			const column_no = jQuery(this).data('column-no');
			let classname = '';
			if (wh < 768) {
				jQuery(this).removeClass('cp_social_networks');
				jQuery(this).removeClass(column_no);
				classname = jQuery(this).attr('class');
				jQuery(this).attr(
					'class',
					'cp_social_networks cp_social_autowidth ' + ' ' + classname
				);
			} else {
				jQuery(this).removeClass('cp_social_networks');
				jQuery(this).removeClass('cp_social_autowidth');
				jQuery(this).removeClass(column_no);
				classname = jQuery(this).attr('class');
				jQuery(this).attr(
					'class',
					'cp_social_networks ' + ' ' + column_no + ' ' + classname
				);
			}
		});
}
