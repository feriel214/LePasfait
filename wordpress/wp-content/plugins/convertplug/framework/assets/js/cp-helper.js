/* eslint-env jquery */
/**
 * ConvertPlug
 *
 * Triggers & Functions
 *
 *	1.	htmlEntities
 *	2. 	Google Fonts for CKEditor
 *	3. 	CKEditors Setup - ( Modal, SlideIn, InfoBar )
 */

/**
 * 1. 	htmlEntities
 *
 * @param {string} str
 */
// eslint-disable-next-line no-unused-vars
function htmlEntities(str) {
	return String(str)
		.replace(/&amp;/g, '&')
		.replace(/&lt;/g, '<')
		.replace(/&gt;/g, '>')
		.replace(/&quot;/g, '"');
}

/**
 * 2. 	cp_isValid()
 *
 * @param {string} s
 */
// eslint-disable-next-line no-unused-vars
function cp_isValid(s) {
	if ('undefined' !== typeof s && null !== s && '' !== s) {
		return true;
	}
	return false;
}

/**
 * 3.	JS Darken / Lighten color
 *
 * @param {string} num
 * @param {string} totalChars
 */
// CP Darker / Lighter colors - {Start}
const pad = function (num, totalChars) {
	const padding = '0';
	num = num + '';
	while (num.length < totalChars) {
		num = padding + num;
	}
	return num;
};

// Ratio is between 0 and 1
const changeColor = function (color, ratio, darker) {
	// Trim trailing/leading whitespace
	color = color.replace(/^\s*|\s*$/, '');

	// Expand three-digit hex
	color = color.replace(
		/^#?([a-f0-9])([a-f0-9])([a-f0-9])$/i,
		'#$1$1$2$2$3$3'
	);

	// Calculate ratio
	const difference = Math.round(ratio * 256) * (darker ? -1 : 1);
	// Determine if input is RGB(A)
	const rgb = color.match(
		new RegExp(
			'^rgba?\\(\\s*' +
				'(\\d|[1-9]\\d|1\\d{2}|2[0-4][0-9]|25[0-5])' +
				'\\s*,\\s*' +
				'(\\d|[1-9]\\d|1\\d{2}|2[0-4][0-9]|25[0-5])' +
				'\\s*,\\s*' +
				'(\\d|[1-9]\\d|1\\d{2}|2[0-4][0-9]|25[0-5])' +
				'(?:\\s*,\\s*' +
				'(0|1|0?\\.\\d+))?' +
				'\\s*\\)$',
			'i'
		)
	);
	const alpha = !!rgb && rgb[4] !== null ? rgb[4] : null;
	// Convert hex to decimal
	const decimal = !!rgb
		? [rgb[1], rgb[2], rgb[3]]
		: color
				.replace(
					/^#?([a-f0-9][a-f0-9])([a-f0-9][a-f0-9])([a-f0-9][a-f0-9])/i,
					function () {
						return (
							parseInt(arguments[1], 16) +
							',' +
							parseInt(arguments[2], 16) +
							',' +
							parseInt(arguments[3], 16)
						);
					}
				)
				.split(/,/);

	// Return RGB(A)
	return !!rgb
		? 'rgb' +
				(alpha !== null ? 'a' : '') +
				'(' +
				Math[darker ? 'max' : 'min'](
					parseInt(decimal[0], 10) + difference,
					darker ? 0 : 255
				) +
				', ' +
				Math[darker ? 'max' : 'min'](
					parseInt(decimal[1], 10) + difference,
					darker ? 0 : 255
				) +
				', ' +
				Math[darker ? 'max' : 'min'](
					parseInt(decimal[2], 10) + difference,
					darker ? 0 : 255
				) +
				(alpha !== null ? ', ' + alpha : '') +
				')'
		: // Return hex
		  [
				'#',
				pad(
					Math[darker ? 'max' : 'min'](
						parseInt(decimal[0], 10) + difference,
						darker ? 0 : 255
					).toString(16),
					2
				),
				pad(
					Math[darker ? 'max' : 'min'](
						parseInt(decimal[1], 10) + difference,
						darker ? 0 : 255
					).toString(16),
					2
				),
				pad(
					Math[darker ? 'max' : 'min'](
						parseInt(decimal[2], 10) + difference,
						darker ? 0 : 255
					).toString(16),
					2
				),
		  ].join('');
};
// eslint-disable-next-line no-unused-vars
const lighterColor = function (color, ratio) {
	return changeColor(color, ratio, false);
};
// eslint-disable-next-line no-unused-vars
const darkerColor = function (color, ratio) {
	return changeColor(color, ratio, true);
};

/**
 *	2. 	Google Fonts for CKEditor
 *
 *	Add selected Google fonts from Google Font Manager to CKEditor
 */

/* Embed Google font link to <head>  */
function cp_append_gfonts(Fonts) {
	jQuery('head').append(
		'<link id="cp-google-fonts" rel="stylesheet" href="https://fonts.googleapis.com/css?family=' +
			Fonts +
			'" type="text/css" media="all">'
	);
}
/*	Append to CKEditor 	*/
function cp_append_to_ckeditor(CKFonts) {
	if (typeof CKFonts !== 'undefined' && CKFonts !== null && CKFonts !== '') {
		CKEDITOR.config.font_names = CKFonts;
	}
}
/* 	Extract Google Fonts */
// eslint-disable-next-line no-unused-vars
function cp_get_gfonts(GFonts) {
	let Fonts = '',
		CKFonts = '';

	if (typeof GFonts !== 'undefined' && GFonts !== null && GFonts !== '') {
		//	for multiple fonts
		if (GFonts.indexOf(',') >= 0) {
			const basicFonts = [
				'Arial',
				'Arial Black',
				'Comic Sans MS',
				'Courier New',
				'Georgia',
				'Impact',
				'Lucida Sans Unicode',
				'Palatino Linotype',
				'Tahoma',
				'Times New Roman',
				'Trebuchet MS',
				'Verdana',
			];

			//	Extract Added Google Fonts
			const pairs = GFonts.split(',');
			pairs.forEach(function (pair) {
				if (
					typeof pair !== 'undefined' &&
					pair !== null &&
					pair !== ''
				) {
					if (jQuery.inArray(pair, basicFonts) < 0) {
						Fonts += pair.replace(' ', '+') + '|';
					}
					CKFonts += pair + ';';
				}
			});

			//	append google fonts
			cp_append_gfonts(Fonts);

			//	Append selected google fonts to - CKEditor
			cp_append_to_ckeditor(CKFonts);
		} else {
			//	for single font
			Fonts += GFonts.replace(' ', '+') + '|';
			CKFonts += GFonts + ';';

			//	append google fonts
			cp_append_gfonts(Fonts);

			//	Append selected google fonts to - CKEditor
			cp_append_to_ckeditor(CKFonts);
		}
	}
}

/**
 *	Adds blinking cursor
 *
 * @param {string} container ( HTML container class for cursor)
 * @param {string} bgcolor   ( background color for cursor )
 */
// eslint-disable-next-line no-unused-vars
function cp_blinking_cursor(container, bgcolor) {
	setTimeout(function () {
		if (jQuery(container).find('.blinking-cursor').length === 0) {
			let font_size = parseInt(jQuery(container).data('font-size')) + 2;
			const fontArray = Array();

			if (jQuery(container + ' span.cp_font').length) {
				jQuery(container + ' span.cp_font').each(function () {
					fontArray.push(parseInt(jQuery(this).data('font-size')));
				});

				const maxFontSize = Math.max.apply(Math, fontArray);
				font_size = maxFontSize + 2;
			}
			let style = '';
			if (bgcolor) {
				style += 'background-color:' + bgcolor + ';';
			}
			if (font_size) {
				style += 'font-size: ' + font_size + 'px !important;';
			}
			jQuery(container).append(
				'<i style="' + style + '" class="blinking-cursor">|</i>'
			);
		}
	}, 500);
}

/**
 * background image
 *
 * @param {Array}  data
 * @param {Object} sel1
 * @param {Object} sel2
 * @param {string} option
 * @param {string} src_option
 */
// eslint-disable-next-line no-unused-vars
function cp_update_bg_image(data, sel1, sel2, option, src_option) {
	const sel1_elem = jQuery(sel1),
		sel2_elem = jQuery(sel2),
		bg_img_src = data[src_option];
	let modal_size = '';
	if (bg_img_src === 'custom_url') {
		const image_url = data[option + '_custom_url'];

		if (sel2 !== '') {
			modal_size = data.modal_size;
			if (modal_size === 'cp-modal-custom-size') {
				sel2_elem.css('background-image', '');
				sel1_elem.css('background-image', 'url(' + image_url + ')');
			} else {
				sel1_elem.css('background-image', '');
				sel2_elem.css('background', 'url(' + image_url + ')');
			}
		} else {
			sel1_elem.css('background-image', 'url(' + image_url + ')');
		}
	} else if (bg_img_src === 'upload_img') {
		const upload_img_url =
			jQuery(
				'.smile-input[name="' + option + '"]',
				window.parent.document
			).attr('data-css-image-url') || '';

		if (upload_img_url !== '') {
			if (sel2 !== '') {
				modal_size = data.modal_size;
				if (modal_size === 'cp-modal-custom-size') {
					sel2_elem.css('background-image', '');
					sel1_elem.css(
						'background-image',
						'url(' + upload_img_url + ')'
					);
				} else {
					sel1_elem.css('background-image', '');
					sel2_elem.css('background', 'url(' + upload_img_url + ')');
				}
			} else {
				sel1_elem.css(
					'background-image',
					'url(' + upload_img_url + ')'
				);
			}
		} else if (sel2 !== '') {
			if (modal_size === 'cp-modal-custom-size') {
				sel1_elem.css('background-image', '');
			} else {
				sel2_elem.css('background-image', '');
			}
		} else {
			sel1_elem.css('background-image', '');
		}
	} else {
		sel1_elem.css('background-image', '');
		sel2_elem.css('background-image', '');
	}

	//  Set Background Image - Position, Repeat & Size
	if (typeof data.opt_bg !== 'undefined') {
		image_positions(data, sel1, sel2, 'opt_bg');
	}

	if (typeof data.form_opt_bg !== 'undefined') {
		image_positions(data, sel1, sel2, 'form_opt_bg');
	}

	if (typeof data.content_opt_bg !== 'undefined') {
		image_positions(data, sel1, sel2, 'content_opt_bg');
	}

	if (typeof data.overlay_bg !== 'undefined') {
		image_positions(data, sel1, sel2, 'overlay_bg');
	}
}

//  Set Background Image - Position, Repeat & Size
function image_positions(data, sel1, sel2, position_option) {
	const sel2_elem = jQuery(sel2),
		sel1_elem = jQuery(sel1);
	const pos_option = data[position_option].split('|'),
		bg_repeat = pos_option[0],
		bg_pos = pos_option[1],
		bg_size = pos_option[2];

	if (sel2 !== '') {
		const modal_size = data.modal_size;
		if (modal_size === 'cp-modal-custom-size') {
			sel1_elem.css({
				'background-position': bg_pos,
				'background-repeat': bg_repeat,
				'background-size': bg_size,
			});
		} else {
			sel2_elem.css({
				'background-position': bg_pos,
				'background-repeat': bg_repeat,
				'background-size': bg_size,
			});
		}
	} else {
		sel1_elem.css({
			'background-position': bg_pos,
			'background-repeat': bg_repeat,
			'background-size': bg_size,
		});
	}
}
// eslint-disable-next-line no-unused-vars
function cp_change_bg_img(
	smile_global_data,
	sel1,
	sel2,
	option,
	bg_option,
	url,
	val
) {
	const sel2_elem = jQuery(sel2),
		sel1_elem = jQuery(sel1);
	const modal_bg_image_size = smile_global_data[option + '_size'],
		opt_bg = smile_global_data[bg_option].split('|'),
		bg_repeat = opt_bg[0],
		bg_pos = opt_bg[1],
		bg_size = opt_bg[2];

	//  UPDATE - [data-css-image-url] to get updated image for FULLWIDTH
	jQuery('.smile-input[name=' + option + ']', window.parent.document).attr(
		'data-css-image-url',
		url
	);

	//  Changed images is always big.
	//  So, If image size is != full then call the image though AJAX
	if (modal_bg_image_size !== 'full') {
		//  Concat image - REPEAT/POSITION/SIZE
		smile_global_data[bg_option] = bg_repeat + '|' + bg_pos + '|' + bg_size;
		//  Update image - ID|SIZE
		smile_global_data[option] = val;

		cp_update_ajax_bg_image_size(
			smile_global_data,
			sel1,
			sel2,
			option,
			bg_option
		);
	} else if (sel2 !== '') {
		const modal_size = smile_global_data.modal_size;
		if (modal_size === 'cp-modal-custom-size') {
			sel2_elem.css({ 'background-image': '' });
			sel1_elem.css({ 'background-image': 'url(' + url + ')' });
		} else {
			sel1_elem.css({ 'background-image': '' });
			sel2_elem.css({ 'background-image': 'url(' + url + ')' });
		}
	} else {
		sel1_elem.css({ 'background-image': 'url(' + url + ')' });
	}
	//  Set Background Image - Position, Repeat & Size
	image_positions(smile_global_data, sel1, sel2, bg_option);
}

/**
 * Update image size by AJAX
 *
 * Also, Replaced [data-css-image-url] with updated image size. [Which is used to updated image URL without AJAX.]
 *
 * @param {Object} smile_global_data
 * @param {Object} sel1
 * @param {Object} sel2
 * @param {string} option
 * @param {string} bg_option
 */
function cp_update_ajax_bg_image_size(
	smile_global_data,
	sel1,
	sel2,
	option,
	bg_option
) {
	const sel2_elem = jQuery(sel2),
		sel1_elem = jQuery(sel1),
		modal_size = smile_global_data.modal_size,
		modal_bg_image = smile_global_data[option],
		modal_bg_image_size = smile_global_data[option + '_size'];

	//file not exists
	if (modal_bg_image !== '') {
		const img_data = {
			action: 'cp_get_image',
			img_id: modal_bg_image,
			size: modal_bg_image_size,
			security_nonce: media_nonce.media_nonce,
		};
		jQuery.ajax({
			url: smile_ajax.url,
			data: img_data,
			type: 'POST',
			success(img) {
				if (sel2 !== '') {
					if (modal_size === 'cp-modal-custom-size') {
						sel2_elem.css({ 'background-image': '' });
						sel1_elem.css({
							'background-image': 'url(' + img + ')',
						});
					} else {
						sel1_elem.css({ 'background-image': '' });
						sel2_elem.css({
							'background-image': 'url(' + img + ')',
						});
					}
				} else {
					sel1_elem.css({ 'background-image': 'url(' + img + ')' });
				}

				//  UPDATE - [data-css-image-url] to get updated image URL. [Which is used to updated image URL without AJAX.]
				jQuery(
					'.smile-input[name=' + option + ']',
					window.parent.document
				).attr('data-css-image-url', img);

				//  Set Background Image - Position, Repeat & Size

				image_positions(smile_global_data, sel1, sel2, bg_option);
			},
		});
	}
}

// set image for style on load of customizer
// eslint-disable-next-line no-unused-vars
function cp_set_image(smile_global_data, option) {
	const image = smile_global_data[option + '_image'];
	let img_src = smile_global_data[option + '_img_src'];
	const img_container = jQuery('.cp-image-container img');

	switch (img_src) {
		case 'upload_img':
			if (image !== '') {
				if (image.indexOf('http') === -1) {
					const image_details = image.split('|'),
						img_id = image_details[0],
						img_size = image_details[1];
					const img_data = {
						action: 'cp_get_image',
						img_id,
						size: img_size,
						security_nonce: media_nonce.media_nonce,
					};
					jQuery.ajax({
						url: smile_ajax.url,
						data: img_data,
						type: 'POST',
						success(img_url) {
							img_container.attr('src', img_url);
						},
					});
				} else {
					img_src = jQuery(
						'.smile-input[name="' + option + '_image"]',
						window.parent.document
					).attr('data-css-image-url');
					img_container.attr('src', img_src);
				}
			}
			break;

		case 'custom_url':
			const custom_url = smile_global_data[option + '_img_custom_url'];
			img_container.attr('src', custom_url);
			break;

		case 'none':
			img_container.attr('src', '');
			break;
	}
}
