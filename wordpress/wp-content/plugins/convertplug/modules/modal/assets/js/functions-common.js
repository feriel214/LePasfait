/* eslint-env jquery */
/* eslint-disable no-dupe-keys */
/**
 * Add class 'cp-no-responsive' to manage the line height of cp-highlight
 *
 * @param {string} sel
 * @param {Array}  data
 */
function cp_set_no_responsive(sel, data) {
	if (
		data.toLowerCase().indexOf('cp_font') >= 0 &&
		data.match('^<span') &&
		data.match('</span>$')
	) {
		sel.addClass('cp-no-responsive');
	} else {
		sel.removeClass('cp-no-responsive');
	}
}

/**
 * Removes &nbsp; and <br> tags from html string
 *
 * @param {string} string
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
 * @param {string} element
 * @param {string} container
 */
function cp_add_empty_class(element, container) {
	const cleanString = cp_get_clean_string(jQuery(element).html());

	// modal title
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
 * @param {string} element
 */
function cp_remove_empty_class(element) {
	if (jQuery(element).length !== 0) {
		jQuery(element).removeClass('cp-empty');
	}
}

/**
 * cp_affilate_settings()
 *
 * @param {Array} data
 */
function cp_affilate_settings(data) {
	const cp_affilate_link = jQuery('.cp-affilate-link');

	const affiliate_setting = data.affiliate_setting;
	let affiliate_title = data.affiliate_title;

	//affiliate link settings
	if (String(affiliate_setting) === '1') {
		cp_affilate_link.css({ display: 'inline-block' });
	} else {
		cp_affilate_link.css({ display: 'none' });
	}

	affiliate_title = htmlEntities(affiliate_title);
	cp_affilate_link.find('a').html(affiliate_title);
	if (
		affiliate_title !== '' &&
		typeof affiliate_title !== 'undefined' &&
		jQuery('#afl_editor').length
	) {
		if (CKEDITOR.instances.afl_editor !== undefined) {
			CKEDITOR.instances.afl_editor.setData(affiliate_title);
		}
	}
}

function cp_affilate_disable(data) {
	const affiliate_setting = data.affiliate_setting,
		cp_affilate_link = jQuery('.cp-affilate-link');
	if (String(affiliate_setting) === '1') {
		cp_affilate_link.css({ display: 'inline-block' });
	} else {
		cp_affilate_link.css({ display: 'none' });
	}
}

/**
 * Tool tip related settings
 *
 * @param {Array} data
 */
function cp_tooltip_settings(data) {
	let close_tooltip = '',
		close_tooltip_end = '',
		tooltip_class = '',
		offset_position = '',
		tooltip_title = data.tooltip_title,
		adj_class = '';

	const tooltip_title_color = data.tooltip_title_color,
		tooltip_background = data.tooltip_background,
		close_modal_tooltip = data.close_modal_tooltip,
		close_modal = data.close_modal,
		close_img = data.close_img,
		close_txt = data.close_txt,
		overlay_close = jQuery('.cp-overlay-close'),
		close_position = data.close_position,
		cp_animate_container = jQuery('.cp-animate-container'),
		close_text_color = data.close_text_color,
		modal_overlay = jQuery('.cp-overlay'),
		close_img_size = data.close_img_size,
		close_image_src = data.close_image_src,
		cp_close_image_width = data.cp_close_image_width,
		cp_close_image_height = data.cp_close_image_height,
		adjacent_close_position = data.adjacent_close_position;

	switch (adjacent_close_position) {
		case 'top_left':
			adj_class = 'cp-adjacent-left';
			break;
		case 'top_right':
			adj_class = 'cp-adjacent-right';
			break;
	}

	const modal_size =
		typeof data.modal_size === 'undefined'
			? 'cp-modal-custom-size'
			: data.modal_size;

	const close_img_default = close_img;
	if (String(close_position) === 'adj_modal') {
		overlay_close.removeClass('cp-outside-close');
		if (String(close_modal) !== 'close_txt') {
			overlay_close.appendTo(cp_animate_container);
		} else {
			overlay_close.appendTo(modal_overlay);
		}
		overlay_close.removeClass(
			'cp-adjacent-left cp-adjacent-right cp-adjacent-bottom-left cp-adjacent-bottom-right'
		);
		overlay_close
			.removeClass('cp-inside-close')
			.addClass('cp-adjacent-close');
		overlay_close.addClass(adj_class);
	} else if (String(close_position) === 'inside_modal') {
		overlay_close.removeClass('cp-outside-close');
		if (String(close_modal) !== 'close_txt') {
			overlay_close.appendTo(cp_animate_container);
			overlay_close
				.addClass('cp-inside-close')
				.removeClass('cp-adjacent-close');
			overlay_close.removeClass(
				'cp-adjacent-left cp-adjacent-right cp-adjacent-bottom-left cp-adjacent-bottom-right'
			);
			overlay_close.addClass(adj_class);
		} else {
			overlay_close.appendTo(modal_overlay);
		}
	} else {
		overlay_close.appendTo(modal_overlay);
		overlay_close.addClass('cp-outside-close');
		overlay_close.removeClass('cp-inside-close cp-adjacent-close');
		overlay_close.removeClass(
			'cp-adjacent-left cp-adjacent-right cp-adjacent-bottom-left cp-adjacent-bottom-right'
		);
		overlay_close.addClass(adj_class);
	}
	let psid = '';
	if (String(close_modal_tooltip) === '1') {
		psid = modal_overlay.data('ps-id');

		if (String(close_position) === 'adj_modal') {
			if (modal_size !== 'cp-modal-custom-size') {
				offset_position = 45;
			} else {
				offset_position = 35;
			}
			tooltip_class = '';
		} else {
			tooltip_class = 'cp-custom-tooltip';
			offset_position = 30;
		}

		jQuery('.has-tip:empty').remove();
	} else {
		jQuery('.has-tip:empty').remove();
	}

	tooltip_title = tooltip_title.replace(/\\/g, '');
	jQuery('#smile_tooltip_title', window.parent.document).val(
		htmlEntities(tooltip_title)
	);

	close_tooltip =
		'<span class="' +
		tooltip_class +
		' cp-tooltip-icon has-tip cp-tipcontent-' +
		psid +
		'" data-classes="close-tip-content-' +
		psid +
		'"  title="' +
		tooltip_title +
		'" data-original-title ="' +
		tooltip_title +
		'" data-color="' +
		tooltip_title_color +
		'" data-bgcolor="' +
		tooltip_background +
		'" data-closeid ="cp-tipcontent-' +
		psid +
		'" data-offset="' +
		offset_position +
		'" >';
	close_tooltip_end = '</span>';

	if (String(close_modal) === 'close_txt') {
		jQuery('.cp-overlay-close')
			.removeClass('cp-image-close cp-adjacent-close cp-inside-close')
			.addClass('cp-text-close');
		overlay_close.html(
			close_tooltip +
				'<span class ="close-txt">' +
				close_txt +
				'</span>' +
				close_tooltip_end
		);
		overlay_close.css({ color: close_text_color });
		overlay_close.removeClass(
			'cp-adjacent-left cp-adjacent-right cp-adjacent-bottom-left cp-adjacent-bottom-right'
		);
		overlay_close.addClass(adj_class);
	} else if (String(close_modal) === 'close_img') {
		if (String(close_image_src) === 'upload_img') {
			if (close_img_default.indexOf('http') === -1) {
				if (String(close_modal) === 'close_img' && close_img !== '') {
					jQuery('.cp-overlay-close')
						.removeClass('cp-text-close')
						.addClass('cp-image-close');
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
							overlay_close.html(
								close_tooltip +
									'<img src="' +
									img +
									'" />' +
									close_tooltip_end
							);
							jQuery(document).trigger('cp_ajax_loaded', [data]);
						},
					});
				} else {
					jQuery('.cp-overlay-close').removeClass(
						'cp-text-close cp-image-close'
					);
					overlay_close.html('');
				}
			} else if (close_img_default.indexOf('http') !== -1) {
				const close_img_full_src = close_img.split('|'),
					close_img_src = close_img_full_src[0];
				jQuery('.cp-overlay-close')
					.removeClass('cp-text-close')
					.addClass('cp-image-close');
				overlay_close.html(
					close_tooltip +
						'<img class="cp-default-close" src="' +
						close_img_src +
						'" />' +
						close_tooltip_end
				);
			}
		} else if (String(close_image_src) === 'custom_url') {
			const modal_close_img_custom_url = data.modal_close_img_custom_url;
			overlay_close.html(
				close_tooltip +
					'<img src="' +
					modal_close_img_custom_url +
					'" />' +
					close_tooltip_end
			);
			jQuery('.cp-overlay-close').removeClass('cp-text-close');
		} else if (String(close_image_src) === 'pre_icons') {
			const close_icon = data.close_icon;
			/* eslint-disable no-undef */
			const close_icon_url =
				cp.module_img_dir + '/' + close_icon + '.png';
			overlay_close.html(
				close_tooltip +
					'<img src="' +
					close_icon_url +
					'" />' +
					close_tooltip_end
			);
			jQuery('.cp-overlay-close').removeClass('cp-text-close');
		} else {
			jQuery('.cp-overlay-close').removeClass(
				'cp-text-close cp-image-close'
			);
			overlay_close.html('');
		}
	} else {
		jQuery('.cp-overlay-close').removeClass('cp-text-close cp-image-close');
		overlay_close.html('');
	}

	overlay_close.css('background', 'transparent');

	if (String(close_modal) === 'do_not_close') {
		overlay_close.css('background', 'none');
	}

	if (String(close_modal) !== 'close_txt') {
		overlay_close.css('width', cp_close_image_width + 'px');
		overlay_close.css('height', cp_close_image_height + 'px');
	} else {
		overlay_close.css('width', 'auto');
		overlay_close.css('height', 'auto');
	}
}

/**
 * Function to reinitialize tooltip
 *
 * @param {Array} data
 */
function cp_tooltip_reinitialize(data) {
	const close_modal_tooltip = data.close_modal_tooltip,
		cp_overlay_close = jQuery('.cp-overlay-close'),
		modal_overlay = jQuery('.cp-overlay'),
		tooltip_background = data.tooltip_background,
		tooltip_title_color = data.tooltip_title_color,
		close_position = data.close_position,
		psid = modal_overlay.data('ps-id'),
		adjacent_close_position = data.adjacent_close_position;

	const modal_size =
		typeof data.modal_size === 'undefined'
			? 'cp-modal-custom-size'
			: data.modal_size;
	let new_tip_position = '';
	let tip_position = '';
	switch (adjacent_close_position) {
		case 'top_left':
			new_tip_position = 'right';
			break;
		case 'top_right':
			new_tip_position = 'left';
			break;
	}

	//tool tip for modal close
	if (String(close_modal_tooltip) === '1') {
		const tooltip_classname = 'cp-tipcontent-' + psid;
		const tclass = 'close-tip-content-' + psid;

		if (String(modal_size) === 'cp-modal-window-size') {
			jQuery('.has-tip').data('position', new_tip_position);
			tip_position = new_tip_position;
		} else {
			switch (close_position) {
				case 'out_modal':
					jQuery('.has-tip').data('position', new_tip_position);
					tip_position = new_tip_position;
					break;
				case 'adj_modal':
					jQuery('.has-tip').data('position', new_tip_position);
					tip_position = new_tip_position;
					break;
				case 'inside_modal':
					jQuery('.has-tip').data('position', new_tip_position);
					tip_position = new_tip_position;
					break;
			}
		}

		if (cp_overlay_close.hasClass('cp-text-close')) {
			jQuery('.has-tip').data('position', new_tip_position);
			tip_position = new_tip_position;
		}

		jQuery('.has-tip').attr('data-position', tip_position);

		jQuery('.' + tooltip_classname).frosty({
			className: 'tip close-tip-content-' + psid,
		});

		jQuery('.cp-backend-tooltip-css').remove();

		jQuery('head').append(
			'<style class="cp-backend-tooltip-css">.customize-support .tip.' +
				tclass +
				'{color: ' +
				tooltip_title_color +
				';background-color:' +
				tooltip_background +
				';border-color:' +
				tooltip_background +
				';border-radius:7px;padding:15px 30px;font-size:13px; }</style>'
		);

		if (String(tip_position) === 'left') {
			jQuery('head').append(
				'<style class="cp-backend-tooltip-css">.customize-support .' +
					tclass +
					'[class*="arrow"]:before{border-left-color: ' +
					tooltip_background +
					';border-width:8px;margin-top:-8px;border-top-color:transparent }</style>'
			);
		} else if (String(tip_position) === 'right') {
			jQuery('head').append(
				'<style class="cp-backend-tooltip-css">.customize-support .' +
					tclass +
					'[class*="arrow"]:before{border-right-color: ' +
					tooltip_background +
					';border-width:8px;margin-top:0px; border-left-color:transparent}</style>'
			);
		} else {
			jQuery('head').append(
				'<style class="cp-backend-tooltip-css">.customize-support .' +
					tclass +
					'[class*="arrow"]:before{border-top-color: ' +
					tooltip_background +
					';border-width:8px;margin-top:0px; border-left-color:transparent}</style>'
			);
		}
	}
}

/**
 * Modal image related settings
 *
 * @param {Array} data
 */
function cp_image_processing(data) {
	const vw = jQuery(window).width(),
		image_displayon_mobile = data.image_displayon_mobile,
		image_resp_width = '768',
		cp_text_container = jQuery('.cp-text-container'),
		cp_img_container = jQuery('.cp-image-container'),
		image_position = data.image_position;

	// hide image on mobile devices
	let image_on_left = '';
	if (Number(image_position) === 1) {
		image_on_left = 'cp-right-contain';
	}

	if (Number(image_displayon_mobile) === 1) {
		if (vw <= image_resp_width) {
			if (image_resp_width >= 768) {
				cp_text_container
					.removeClass('col-lg-7 col-md-7 col-sm-7')
					.addClass(
						'col-lg-12 col-md-12 col-sm-12 cp-bigtext-container'
					);
			} else {
				cp_text_container
					.removeClass(
						'col-lg-12 col-md-12 col-sm-12 cp-bigtext-container'
					)
					.addClass('col-lg-7 col-md-7 col-sm-7');
			}
		} else {
			cp_text_container
				.removeClass(
					'col-lg-12 col-md-12 col-sm-12 cp-bigtext-container'
				)
				.addClass('col-lg-7 col-md-7 col-sm-7');
		}

		if (vw <= image_resp_width) {
			cp_img_container.addClass('cp-hide-image');
		} else {
			cp_img_container.removeClass('cp-hide-image');
		}
	} else {
		cp_text_container
			.removeClass('col-lg-12 col-md-12 col-sm-12')
			.addClass('col-lg-7 col-md-7 col-sm-7 ' + image_on_left);
		cp_img_container.removeClass('cp-hide-image');
	}
}

/**
 * Reinitialize Affiliate
 *
 * @param {Array} data
 */
function cp_affilate_reinitialize(data) {
	const affiliate_setting = data.affiliate_setting;
	set_affiliate_link(affiliate_setting);
}

/**
 * Adds custom css
 *
 * @param {Array} data
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
 * Animations in customizer
 *
 * @param {Array} data
 */
function cp_apply_animations(data) {
	const cp_animate = jQuery('.cp-animate-container');

	let overlay_effect = data.overlay_effect,
		exit_animation = data.exit_animation;
	const disable_overlay_effect = data.disable_overlay_effect,
		hide_animation_width = data.hide_animation_width;

	if (Number(disable_overlay_effect) === 1) {
		const vw = jQuery(window).width();
		if (vw <= hide_animation_width) {
			overlay_effect = exit_animation = 'cp-overlay-none';
		}
	} else {
		cp_animate.removeClass('cp-overlay-none');
	}

	const entry_anim =
		typeof cp_animate.attr('data-entry-animation') !== 'undefined'
			? cp_animate.attr('data-entry-animation')
			: '';
	const exit_anim =
		typeof cp_animate.attr('data-exit-animation') !== 'undefined'
			? cp_animate.attr('data-exit-animation')
			: '';

	cp_animate.removeClass('smile-animated');

	if (!cp_animate.hasClass(exit_animation) && exit_animation !== exit_anim) {
		cp_animate.attr('data-exit-animation', exit_animation);
		setTimeout(function () {
			if (exit_animation !== 'none') {
				cp_animate.removeClass(exit_anim);
				cp_animate.removeClass(entry_anim);
				cp_animate.addClass('smile-animated ' + exit_animation);
				cp_animate.attr('data-entry-animation', overlay_effect);
			}
			setTimeout(function () {
				cp_animate.removeClass(exit_anim);
				cp_animate.removeClass(exit_animation);
				cp_animate.removeClass(entry_anim);
				cp_animate.addClass('smile-animated ' + entry_anim);
			}, 1000);
		}, 500);
	}

	if (!cp_animate.hasClass(overlay_effect) && overlay_effect !== entry_anim) {
		setTimeout(function () {
			if (overlay_effect !== 'none') {
				cp_animate.removeClass(exit_anim);
				cp_animate.removeClass(entry_anim);
				cp_animate.addClass('smile-animated ' + overlay_effect);
				cp_animate.attr('data-entry-animation', overlay_effect);
			}
		}, 500);
	}
}

/**
 * Update Image URL by AJAX
 *
 * @param {Array} smile_global_data
 */
function cp_update_ajax_modal_image_src(smile_global_data) {
	const modal_image_size = smile_global_data.modal_image_size,
		modal_image = smile_global_data.modal_image,
		modal_img_src = smile_global_data.modal_img_src;
	const modal_img = jQuery('.cp-image-container img');

	switch (modal_img_src) {
		case 'upload_img':
			// 	file not exists
			if (
				typeof modal_image !== 'undefined' &&
				modal_image.indexOf('http') === -1 &&
				modal_image !== ''
			) {
				const image_details = modal_image.split('|'),
					img_id = image_details[0],
					img_size = modal_image_size;
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
						modal_img.attr('src', img_url);
					},
				});
			} else if (
				typeof modal_image !== 'undefined' &&
				modal_image.indexOf('http') !== -1
			) {
				if (modal_image.indexOf('|')) {
					const url = modal_image.split('|');
					modal_img.attr('src', url[0]);
				} else {
					modal_img.attr('src', modal_image);
				}
			} else {
				const img_src = jQuery(
					'.smile-input[name="modal_image"]',
					window.parent.document
				).attr('data-css-image-url');
				modal_img.attr('src', img_src);
			}
			break;

		case 'custom_url':
			const custom_url = smile_global_data.modal_img_custom_url;
			modal_img.attr('src', custom_url);
			break;

		case 'none':
			modal_img.attr('src', '');
			break;
	}
}

/**
 * [cp_update_ajax_overlay_image_src description]
 *
 */
/*function cp_update_ajax_overlay_image_src( smile_global_data ) {
	var modal_image_size = smile_global_data.modal_image_size,
		modal_image 	 = smile_global_data.modal_image,
		modal_img_src    = smile_global_data.modal_img_src;
		modal_img 		 = jQuery('.cp-image-container img');

	switch( modal_img_src ) {

		case "upload_img":

				// 	file not exists
			if( typeof modal_image !== 'undefined' && modal_image.indexOf('http') === -1 && modal_image !== '' ) {

				var image_details = modal_image.split("|"),
                        img_id = image_details[0],
                        img_size = modal_image_size;
				var img_data = {
					action:'cp_get_image',
					img_id: img_id,
					size: img_size
				};
				jQuery.ajax({
					url: smile_ajax.url,
					data: img_data,
					type: "POST",
					success: function(img_url){
						modal_img.attr( "src", img_url);
					}
				});
			} else if( typeof modal_image !== 'undefined' && modal_image.indexOf('http') != -1 ) {
				if( modal_image.indexOf('|') ) {
					var url = modal_image.split('|');
					modal_img.attr( "src", url[0]);
				} else {
                	modal_img.attr( "src", modal_image );
                }
			} else {
				var img_src = jQuery('.smile-input[name="modal_image"]', window.parent.document ).attr('data-css-image-url');
				modal_img.attr( "src", img_src );
			}
		break;

		case "custom_url":

			var custom_url = smile_global_data.modal_img_custom_url;
			modal_img.attr( "src", custom_url );
		break;

		case "none":
			modal_img.attr( "src", "" );
		break;
	}

}*/

parent
	.jQuery(window.parent.document)
	.on('cp-image-default', function (e, name, url, val) {
		//	Modal - Background Image
		// Process for modal background image - for variable 'modal_bg_image'
		if (
			name === 'modal_bg_image' &&
			name !== 'undefined' &&
			name !== null
		) {
			cp_change_bg_img(
				smile_global_data,
				'.cp-modal-body',
				'.cp-modal-content',
				name,
				'opt_bg',
				url,
				val
			);
		}

		if (name === 'form_bg_image' && name !== 'undefined' && name !== null) {
			cp_change_bg_img(
				smile_global_data,
				'.cp-form-section',
				'',
				name,
				'form_opt_bg',
				url,
				val
			);
		}

		if (
			name === 'content_bg_image' &&
			name !== 'undefined' &&
			name !== null
		) {
			cp_change_bg_img(
				smile_global_data,
				'.cp-content-section',
				'',
				name,
				'content_opt_bg',
				url,
				val
			);
		}

		if (
			name === 'overlay_bg_image' &&
			name !== 'undefined' &&
			name !== null
		) {
			cp_change_bg_img(
				smile_global_data,
				'.cp-overlay',
				'',
				name,
				'overlay_bg',
				url,
				val
			);
		}

		//	Modal - Image
		// Process for modal image - for variable 'modal_image'
		if (name === 'modal_image' && name !== 'undefined' && name !== null) {
			const modal_image_size = smile_global_data.modal_image_size,
				modal_img = jQuery('.cp-image-container img');

			smile_global_data.modal_image = url;
			//	Changed images is always big.
			//	So, If image size is != full then call the image though AJAX
			if (String(modal_image_size) !== 'full') {
				//	Update image - ID|SIZE
				cp_update_ajax_modal_image_src(smile_global_data);
			} else {
				modal_img.attr('src', url);
			}
		}
	});

/**
 * Remove - Modal Image
 *
 * Also, Replaced [data-css-image-url] with empty. [Which is used to updated image URL without AJAX.]
 */
parent.jQuery(window.parent.document).on('cp-image-remove', function (e, name) {
	let sel1 = '',
		sel2 = '';
	switch (name) {
		case 'modal_bg_image':
			sel1 = '.cp-modal-body';
			sel2 = '.cp-modal-content';
			break;
		case 'form_bg_image':
			sel1 = '.cp-form-section';
			sel2 = '';
			break;
		case 'content_bg_image':
			sel1 = '.cp-content-section';
			sel2 = '';
			break;
		case 'overlay_bg_image':
			sel1 = '.cp-overlay';
			sel2 = '';
			break;
	}

	const cp_modal_content = jQuery(sel2),
		cp_modal_body = jQuery(sel1);

	cp_modal_content.css({ 'background-image': '' });
	cp_modal_body.css({ 'background-image': '' });

	//	REMOVE - [data-css-image-url] to get updated image for FULLWIDTH
	jQuery('.smile-input[name=' + name + ']', window.parent.document).attr(
		'data-css-image-url',
		''
	);
});

/**
 * Change - Modal Image
 */
parent
	.jQuery(window.parent.document)
	.on('cp-image-change', function (e, name, url, val) {
		//	Modal - Background Image
		// Process for modal background image - for variable 'modal_bg_image'
		if (
			name === 'modal_bg_image' &&
			name !== 'undefined' &&
			name !== null
		) {
			cp_change_bg_img(
				smile_global_data,
				'.cp-modal-body',
				'.cp-modal-content',
				name,
				'opt_bg',
				url,
				val
			);
		}

		if (name === 'form_bg_image' && name !== 'undefined' && name !== null) {
			cp_change_bg_img(
				smile_global_data,
				'.cp-form-section',
				'',
				name,
				'form_opt_bg',
				url,
				val
			);
		}

		if (
			name === 'content_bg_image' &&
			name !== 'undefined' &&
			name !== null
		) {
			cp_change_bg_img(
				smile_global_data,
				'.cp-content-section',
				'',
				name,
				'content_opt_bg',
				url,
				val
			);
		}

		if (
			name === 'overlay_bg_image' &&
			name !== 'undefined' &&
			name !== null
		) {
			cp_change_bg_img(
				smile_global_data,
				'.cp-overlay',
				'',
				name,
				'overlay_bg',
				url,
				val
			);
		}

		//	Modal - Image
		// Process for modal image - for variable 'modal_image'
		if (name === 'modal_image' && name !== 'undefined' && name !== null) {
			const modal_image_size = smile_global_data.modal_image_size,
				modal_img = jQuery('.cp-image-container img');

			smile_global_data.modal_image = val;
			//	Changed images is always big.
			//	So, If image size is != full then call the image though AJAX
			if (String(modal_image_size) !== 'full') {
				//	Update image - ID|SIZE
				cp_update_ajax_modal_image_src(smile_global_data);
			} else {
				modal_img.attr('src', url);
			}
		}
	});

/**
 * decode HTML char
 *
 * @param {string} text
 */
/* eslint-disable no-unused-vars */
function escapeHtml(text) {
	const decoded = jQuery('<div/>').html(text).text();
	return decoded;
}

/**
 * This function set modal width
 *
 * @param {Array} data
 */
function cp_modal_width_settings(data) {
	const cp_modal = jQuery('.cp-modal'),
		cp_modal_width = data.cp_modal_width,
		modal_size = data.modal_size,
		cp_modal_body = jQuery('.cp-modal-body');

	if (String(modal_size) === 'cp-modal-custom-size') {
		cp_modal.css({ 'max-width': cp_modal_width + 'px', width: '100%' });
		cp_modal_body.css('max-width', 'none');
		cp_modal.removeClass('cp-modal-window-size');
		if (jQuery('.cp-modal-body').hasClass('cp-youtube')) {
			const wh = jQuery(window).height() * (55.5 / 100);
			jQuery('.cp-content-container').css({
				'max-width': cp_modal_width + 'px',
				height: wh + 'px',
			});
		}
		jQuery('.cp_fs_overlay').hide();
		jQuery('.cp_cs_overlay').show();
	} else if (!jQuery('.cp-modal-body').hasClass('cp-youtube')) {
		//	Skip `YouTube` style form window Width
		cp_modal_body.css('max-width', cp_modal_width + 'px');
		cp_modal.removeClass('cp-modal-custom-size');

		jQuery('.cp_cs_overlay').hide();
		jQuery('.cp_fs_overlay').show();
	}
	cp_modal.addClass(modal_size);
}

const cp_empty_classes = {
	'.cp-title': '.cp-title-container',
	'.cp-sec-title': '.cp-sec-title-container',
	'.cp-description': '.cp-desc-container',
	'.cp-info-container': '.cp-info-container',
	'.cp-short-description': '.cp-short-desc-container',
	'.cp-desc-bottom': '.cp-desc-timetable',
	'.cp-mid-description': '.cp-mid-desc-container',
	'.cp-short-title': '.cp-short-title-container',
	'.cp-modal-note': '.modal-note-container',
	'.cp-modal-note-2': '.modal-note-container-2',
};

/**
 * Document Ready
 */
jQuery(document).ready(function () {
	jQuery('body').on('click', '.cp-social-form-form', function (e) {
		parent.setFocusElement('form_bg_color');
		e.stopPropagation();
	});
	jQuery('body').on('click', '.counter-overlay', function (e) {
		parent.setFocusElement('modal_desc_bg_color');
		e.stopPropagation();
	});
	jQuery('body').on('click', '.cp-short-desc-container', function (e) {
		parent.setFocusElement('modal_desc_bg_color');
		e.stopPropagation();
	});
	jQuery('body').on('click', '.counter-desc-overlay', function (e) {
		parent.setFocusElement('form_bg_color');
		e.stopPropagation();
	});
	jQuery('body').on('click', '.cp-count-down-container', function (e) {
		parent.setFocusElement('counter_container_bg_color');
		e.stopPropagation();
	});
	jQuery('body').on('click', '.cp-form-seperator', function (e) {
		parent.setFocusElement('form_bg_color');
		e.stopPropagation();
	});
	jQuery('body').on('click', '#cp_defaultCountdown', function (e) {
		parent.setFocusElement('date_time_picker');
		e.stopPropagation();
	});
	jQuery('body').on('click', '.cp-image', function (e) {
		parent.setFocusElement('modal_image');
		e.stopPropagation();
	});
	jQuery('body').on('click', '.cp-submit', function (e) {
		parent.setFocusElement('btn_style');
		e.stopPropagation();
	});
	jQuery('body').on('click', '.cp-modal-body', function (e) {
		parent.setFocusElement('modal_bg_color');
		e.stopPropagation();
	});
	jQuery('body').on('click', '.cp-overlay', function (e) {
		parent.setFocusElement('modal_overlay_bg_color');
		e.stopPropagation();
	});
	jQuery('body').on('click', '.cp-overlay-close', function (e) {
		parent.setFocusElement('close_modal');
		e.stopPropagation();
	});
	jQuery('body').on('click', '.cp-affilate-link', function (e) {
		parent.setFocusElement('affiliate_username');
		e.stopPropagation();
	});
	jQuery('body').on('click', '.cp-affilate', function (e) {
		parent.setFocusElement('affiliate_username');
		e.stopPropagation();
	});

	//  Model height
	CPModelHeight();

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

	if (jQuery('#mid_desc_editor').length) {
		const sel_mid_desc_editor = jQuery('#mid_desc_editor');

		CKEDITOR.inline('mid_desc_editor');

		CKEDITOR.instances.mid_desc_editor.on('change', function () {
			//	Set class - `cp-modal-exceed`
			CPModelHeight();

			//	Set equalize columns
			cp_column_equilize();

			//Set equalize row for blank style
			cp_row_equilize();

			//set color for li tags
			cp_color_for_list_tag();

			jQuery(document).trigger('ckeditorChange');

			const data = CKEDITOR.instances.mid_desc_editor.getData();
			parent.updateHTML(htmlEntities(data), 'smile_modal_middle_desc');

			//	2. Add class 'cp-no-responsive' to manage the line height of cp-highlight
			cp_set_no_responsive(sel_mid_desc_editor, data);
		});

		// Use below code to 'reinitialize' CKEditor
		// IN ANY CASE IF CKEDITOR IS NOT INITIALIZED THEN USE BELOW CODE
		CKEDITOR.instances.mid_desc_editor.on('instanceReady', function (ev) {
			const editor = ev.editor;
			editor.setReadOnly(false);
		});
	}

	if (jQuery('#main_title_editor').length) {
		const sel_main_title_editor = jQuery('#main_title_editor');

		if (typeof InstallTrigger === 'undefined') {
			CKEDITOR.inline('main_title_editor');
		}

		//	Initially set show CKEditor of 'cp-title'
		//	Ref: http://docs.ckeditor.com/#!/api/CKEDITOR.focusManager
		const focusManager = new CKEDITOR.focusManager(
			CKEDITOR.instances.main_title_editor
		);
		focusManager.focus();

		//	1. Add class 'cp-no-responsive' to manage the line height of cp-highlight
		CKEDITOR.instances.main_title_editor.on('instanceReady', function () {
			const data = CKEDITOR.instances.main_title_editor.getData();
			cp_set_no_responsive(sel_main_title_editor, data);
		});

		CKEDITOR.instances.main_title_editor.on('change', function () {
			// Remove Blinking cursor
			jQuery('.cp-modal-body').find('.blinking-cursor').remove();

			//	Set class - `cp-modal-exceed`
			CPModelHeight();

			//	Set equalize columns
			cp_column_equilize();

			//Set equalize row for blank style
			cp_row_equilize();

			//set color for li tags
			cp_color_for_list_tag();

			jQuery(document).trigger('ckeditorChange');

			const data = CKEDITOR.instances.main_title_editor.getData();
			parent.updateHTML(htmlEntities(data), 'smile_modal_title1');

			//	2. Add class 'cp-no-responsive' to manage the line height of cp-highlight
			cp_set_no_responsive(sel_main_title_editor, data);
		});

		// Use below code to 'reinitialize' CKEditor
		// IN ANY CASE IF CKEDITOR IS NOT INITIALIZED THEN USE BELOW CODE
		CKEDITOR.instances.main_title_editor.on('instanceReady', function (ev) {
			const editor = ev.editor;
			editor.setReadOnly(false);
		});
	}

	if (jQuery('#sec_title_editor').length) {
		const sel_sec_title_editor = jQuery('#sec_title_editor');

		// Turn off automatic editor creation first.
		CKEDITOR.inline('sec_title_editor');

		//	1. Add class 'cp-no-responsive' to manage the line height of cp-highlight
		CKEDITOR.instances.sec_title_editor.on('instanceReady', function () {
			const data = CKEDITOR.instances.sec_title_editor.getData();
			cp_set_no_responsive(sel_sec_title_editor, data);
		});

		CKEDITOR.instances.sec_title_editor.on('change', function () {
			//	Set class - `cp-modal-exceed`
			CPModelHeight();

			//	Set equalize columns
			cp_column_equilize();

			//set color for li tags
			cp_color_for_list_tag();

			const data = CKEDITOR.instances.sec_title_editor.getData();
			parent.updateHTML(htmlEntities(data), 'smile_modal_sec_title');

			//	2. Add class 'cp-no-responsive' to manage the line height of cp-highlight
			cp_set_no_responsive(sel_sec_title_editor, data);
		});

		// Use below code to 'reinitialize' CKEditor
		// IN ANY CASE IF CKEDITOR IS NOT INITIALIZED THEN USE BELOW CODE
		CKEDITOR.instances.sec_title_editor.on('instanceReady', function (ev) {
			const editor = ev.editor;
			editor.setReadOnly(false);
		});
	}

	if (jQuery('#desc_editor').length) {
		const sel_desc_editor = jQuery('#desc_editor');

		// Turn off automatic editor creation first.
		CKEDITOR.inline('desc_editor');
		CKEDITOR.instances.desc_editor.config.toolbar = 'Small';

		//	1. Add class 'cp-no-responsive' to manage the line height of cp-highlight
		CKEDITOR.instances.desc_editor.on('instanceReady', function () {
			const data = CKEDITOR.instances.desc_editor.getData();
			cp_set_no_responsive(sel_desc_editor, data);
		});

		CKEDITOR.instances.desc_editor.on('change', function () {
			//	Set class - `cp-modal-exceed`
			CPModelHeight();

			//	Set equalize columns
			cp_column_equilize();

			//set color for li tags
			cp_color_for_list_tag();

			jQuery(document).trigger('ckeditorChange');

			const data = CKEDITOR.instances.desc_editor.getData();
			parent.updateHTML(data, 'smile_modal_short_desc1');

			//	2. Add class 'cp-no-responsive' to manage the line height of cp-highlight
			cp_set_no_responsive(sel_desc_editor, data);
		});

		// Use below code to 'reinitialize' CKEditor
		// IN ANY CASE IF CKEDITOR IS NOT INITIALIZED THEN USE BELOW CODE
		CKEDITOR.instances.desc_editor.on('instanceReady', function (ev) {
			const editor = ev.editor;
			editor.setReadOnly(false);
		});
	}

	if (jQuery('#info_editor').length) {
		const sel_info_editor = jQuery('#info_editor');

		// Turn off automatic editor creation first.
		CKEDITOR.inline('info_editor');
		CKEDITOR.instances.info_editor.config.toolbar = 'Small';

		//	1. Add class 'cp-no-responsive' to manage the line height of cp-highlight
		CKEDITOR.instances.info_editor.on('instanceReady', function () {
			const data = CKEDITOR.instances.info_editor.getData();
			cp_set_no_responsive(sel_info_editor, data);
		});

		CKEDITOR.instances.info_editor.on('change', function () {
			//	Set class - `cp-modal-exceed`
			CPModelHeight();

			//	Set equalize columns
			cp_column_equilize();

			//set color for li tags
			cp_color_for_list_tag();

			jQuery(document).trigger('ckeditorChange');

			const data = CKEDITOR.instances.info_editor.getData();
			parent.updateHTML(data, 'smile_modal_confidential');

			//	2. Add class 'cp-no-responsive' to manage the line height of cp-highlight
			cp_set_no_responsive(sel_info_editor, data);
		});

		// Use below code to 'reinitialize' CKEditor
		// IN ANY CASE IF CKEDITOR IS NOT INITIALIZED THEN USE BELOW CODE
		CKEDITOR.instances.info_editor.on('instanceReady', function (ev) {
			const editor = ev.editor;
			editor.setReadOnly(false);
		});
	}

	if (jQuery('#short_desc_editor').length) {
		const sel_short_desc_editor = jQuery('#short_desc_editor');

		// Turn off automatic editor creation first.
		CKEDITOR.inline('short_desc_editor');
		CKEDITOR.instances.short_desc_editor.config.toolbar = 'Small';

		//	1. Add class 'cp-no-responsive' to manage the line height of cp-highlight
		CKEDITOR.instances.short_desc_editor.on('instanceReady', function () {
			const data = CKEDITOR.instances.short_desc_editor.getData();
			cp_set_no_responsive(sel_short_desc_editor, data);
		});

		CKEDITOR.instances.short_desc_editor.on('change', function () {
			//	Set class - `cp-modal-exceed`
			CPModelHeight();

			//	Set equalize columns
			cp_column_equilize();

			//set color for li tags
			cp_color_for_list_tag();

			const data = CKEDITOR.instances.short_desc_editor.getData();
			parent.updateHTML(data, 'smile_modal_short_desc');

			//	2. Add class 'cp-no-responsive' to manage the line height of cp-highlight
			cp_set_no_responsive(sel_short_desc_editor, data);
		});

		// Use below code to 'reinitialize' CKEditor
		// IN ANY CASE IF CKEDITOR IS NOT INITIALIZED THEN USE BELOW CODE
		CKEDITOR.instances.short_desc_editor.on('instanceReady', function (ev) {
			const editor = ev.editor;
			editor.setReadOnly(false);
		});
	}

	if (jQuery('#short_title_editor').length) {
		const sel_short_title_editor = jQuery('#short_title_editor');

		// // Turn off automatic editor creation first.
		CKEDITOR.inline('short_title_editor');
		CKEDITOR.instances.short_title_editor.config.toolbar = 'Small';

		// Add class 'cp-no-responsive' to manage the line height of cp-highlight
		CKEDITOR.instances.short_title_editor.on('instanceReady', function () {
			const data = CKEDITOR.instances.short_title_editor.getData();
			cp_set_no_responsive(sel_short_title_editor, data);
		});

		CKEDITOR.instances.short_title_editor.on('change', function () {
			// Set class - `cp-modal-exceed`
			CPModelHeight();

			//	Set equalize columns
			cp_column_equilize();

			//set color for li tags
			cp_color_for_list_tag();

			const data = CKEDITOR.instances.short_title_editor.getData();
			parent.updateHTML(data, 'smile_modal_short_title');

			//	Add class 'cp-no-responsive' to manage the line height of cp-highlight
			cp_set_no_responsive(sel_short_title_editor, data);
		});

		// Use below code to 'reinitialize' CKEditor
		// IN ANY CASE IF CKEDITOR IS NOT INITIALIZED THEN USE BELOW CODE
		CKEDITOR.instances.short_title_editor.on(
			'instanceReady',
			function (ev) {
				const editor = ev.editor;
				editor.setReadOnly(false);
			}
		);
	}

	if (jQuery('#modal_note_1').length) {
		const sel_modal_note_1 = jQuery('#modal_note_1');

		// // Turn off automatic editor creation first.
		CKEDITOR.inline('modal_note_1');
		CKEDITOR.instances.modal_note_1.config.toolbar = 'Small';

		// Add class 'cp-no-responsive' to manage the line height of cp-highlight
		CKEDITOR.instances.modal_note_1.on('instanceReady', function () {
			const data = CKEDITOR.instances.modal_note_1.getData();
			cp_set_no_responsive(sel_modal_note_1, data);
		});

		CKEDITOR.instances.modal_note_1.on('change', function () {
			// Set class - `cp-modal-exceed`
			CPModelHeight();

			//	Set equalize columns
			cp_column_equilize();

			//set color for li tags
			cp_color_for_list_tag();

			const data = CKEDITOR.instances.modal_note_1.getData();
			parent.updateHTML(data, 'smile_modal_note_1');

			//	Add class 'cp-no-responsive' to manage the line height of cp-highlight
			cp_set_no_responsive(sel_modal_note_1, data);
		});

		// Use below code to 'reinitialize' CKEditor
		// IN ANY CASE IF CKEDITOR IS NOT INITIALIZED THEN USE BELOW CODE
		CKEDITOR.instances.modal_note_1.on('instanceReady', function (ev) {
			const editor = ev.editor;
			editor.setReadOnly(false);
		});
	}

	if (jQuery('#modal_note_2').length) {
		const sel_modal_note_2 = jQuery('#modal_note_2');

		// // Turn off automatic editor creation first.
		CKEDITOR.inline('modal_note_2');
		CKEDITOR.instances.modal_note_2.config.toolbar = 'Small';

		// Add class 'cp-no-responsive' to manage the line height of cp-highlight
		CKEDITOR.instances.modal_note_2.on('instanceReady', function () {
			const data = CKEDITOR.instances.modal_note_2.getData();
			cp_set_no_responsive(sel_modal_note_2, data);
		});

		CKEDITOR.instances.modal_note_2.on('change', function () {
			// Set class - `cp-modal-exceed`
			CPModelHeight();

			//	Set equalize columns
			cp_column_equilize();

			//set color for li tags
			cp_color_for_list_tag();

			const data = CKEDITOR.instances.modal_note_2.getData();
			parent.updateHTML(data, 'smile_modal_note_2');

			//	Add class 'cp-no-responsive' to manage the line height of cp-highlight
			cp_set_no_responsive(sel_modal_note_2, data);
		});

		// Use below code to 'reinitialize' CKEditor
		// IN ANY CASE IF CKEDITOR IS NOT INITIALIZED THEN USE BELOW CODE
		CKEDITOR.instances.modal_note_2.on('instanceReady', function (ev) {
			const editor = ev.editor;
			editor.setReadOnly(false);
		});
	}

	if (jQuery('#afl_editor').length) {
		const sel_afl_editor = jQuery('#afl_editor');

		// Turn off automatic editor creation first.
		CKEDITOR.inline('afl_editor');
		CKEDITOR.instances.afl_editor.config.toolbar = 'Small';

		//	1. Add class 'cp-no-responsive' to manage the line height of cp-highlight
		CKEDITOR.instances.afl_editor.on('instanceReady', function () {
			const data = CKEDITOR.instances.afl_editor.getData();
			cp_set_no_responsive(sel_afl_editor, data);
		});

		CKEDITOR.instances.afl_editor.on('change', function () {
			//	Set class - `cp-modal-exceed`
			CPModelHeight();

			//	Set equalize columns
			cp_column_equilize();

			//set color for li tags
			cp_color_for_list_tag();

			jQuery(document).trigger('ckeditorChange');

			const data = CKEDITOR.instances.afl_editor.getData();
			parent.updateHTML(data, 'smile_affiliate_title');

			//	2. Add class 'cp-no-responsive' to manage the line height of cp-highlight
			cp_set_no_responsive(sel_afl_editor, data);
		});

		// Use below code to 'reinitialize' CKEditor
		// IN ANY CASE IF CKEDITOR IS NOT INITIALIZED THEN USE BELOW CODE
		CKEDITOR.instances.afl_editor.on('instanceReady', function (ev) {
			const editor = ev.editor;
			editor.setReadOnly(false);
		});
	}

	if (jQuery('#description_bottom').length) {
		const sel_description_bottom = jQuery('#description_bottom');

		// Turn off automatic editor creation first.
		CKEDITOR.inline('description_bottom');

		//	1. Add class 'cp-no-responsive' to manage the line height of cp-highlight
		CKEDITOR.instances.description_bottom.on('instanceReady', function () {
			const data = CKEDITOR.instances.description_bottom.getData();
			cp_set_no_responsive(sel_description_bottom, data);
		});

		//CKEDITOR.instances.description_bottom.config.toolbar = 'Small';
		CKEDITOR.instances.description_bottom.on('change', function () {
			//	Set class - `cp-modal-exceed`
			CPModelHeight();

			//	Set equalize columns
			cp_column_equilize();

			//set color for li tags
			cp_color_for_list_tag();

			const data = CKEDITOR.instances.description_bottom.getData();
			parent.updateHTML(data, 'smile_modal_content');

			//	2. Add class 'cp-no-responsive' to manage the line height of cp-highlight
			cp_set_no_responsive(sel_description_bottom, data);
		});

		// Use below code to 'reinitialize' CKEditor
		// IN ANY CASE IF CKEDITOR IS NOT INITIALIZED THEN USE BELOW CODE
		CKEDITOR.instances.description_bottom.on(
			'instanceReady',
			function (ev) {
				const editor = ev.editor;
				editor.setReadOnly(false);
			}
		);
	}

	if (jQuery('#count_down_editor').length) {
		const sel_count_down_editor = jQuery('#count_down_editor');

		// Turn off automatic editor creation first.
		CKEDITOR.inline('count_down_editor');

		//	1. Add class 'cp-no-responsive' to manage the line height of cp-highlight
		CKEDITOR.instances.count_down_editor.on('instanceReady', function () {
			const data = CKEDITOR.instances.count_down_editor.getData();
			cp_set_no_responsive(sel_count_down_editor, data);
		});

		//CKEDITOR.instances.description_bottom.config.toolbar = 'Small';
		CKEDITOR.instances.count_down_editor.on('change', function () {
			//	Set class - `cp-modal-exceed`
			CPModelHeight();

			//	Set equalize coloumns
			cp_column_equilize();

			//set color for li tags
			cp_color_for_list_tag();

			const data = CKEDITOR.instances.count_down_editor.getData();
			parent.updateHTML(data, 'smile_count_down_title');

			//	2. Add class 'cp-no-responsive' to manage the line height of cp-highlight
			cp_set_no_responsive(sel_count_down_editor, data);
		});
	}

	// remove blinking cursor
	jQuery('body').on(
		'click select',
		'.cp-highlight,.cp-name,.cp-email',
		function () {
			jQuery('.cp-modal-body').find('.blinking-cursor').remove();
		}
	);

	// Preventing links and form navigation in an iframe
	jQuery('a').on('click', function (e) {
		e.preventDefault();
	});
	jQuery('button').on('click', function (e) {
		e.preventDefault();
	});
	jQuery(this).on('submit', 'form', function (e) {
		e.preventDefault();
	});
});

function single__toggle_class(selector, toggle_class, value, required) {
	if (
		(typeof value !== 'undefined' && String(value) === '') ||
		String(value) === String(required)
	) {
		jQuery(selector).addClass(toggle_class);
	} else {
		jQuery(selector).removeClass(toggle_class);
	}
}

function add_css(selector, property, value) {
	jQuery(selector).css(property, value);
}

function update_modal_size(modal_size) {
	const cp_modal = jQuery('.cp-modal');
	if (!cp_modal.hasClass('cp-modal-exceed')) {
		cp_modal.attr('class', 'cp-modal ' + modal_size);
	} else {
		cp_modal.attr('class', 'cp-modal cp-modal-exceed ' + modal_size);
	}
}

function apply_border_and_shadow(data) {
	const v_horizontal =
			jQuery('#horizontal-length', window.parent.document).val() || '',
		v_vertical =
			jQuery('#vertical-length', window.parent.document).val() || '',
		v_blur = jQuery('#blur-radius', window.parent.document).val() || '',
		v_spread = jQuery('#spread-field', window.parent.document).val() || '',
		v_shadowColor =
			jQuery('#shadow-color', window.parent.document).val() || '',
		v_shadow =
			jQuery('#smile_shadow_type', window.parent.document).val() || '';

	let box_shadow_string = 'type:' + v_shadow + '|';
	box_shadow_string += 'horizontal:' + v_horizontal + '|';
	box_shadow_string += 'vertical:' + v_vertical + '|';
	box_shadow_string += 'blur:' + v_blur + '|';
	box_shadow_string += 'spread:' + v_spread + '|';
	box_shadow_string += 'color:' + v_shadowColor;

	let style =
		jQuery('#select-border :selected', window.parent.document).val() || '';

	const br_all = jQuery('#all-corners', window.parent.document).val() || '',
		br_tl = jQuery('#top-left', window.parent.document).val() || '',
		br_tr = jQuery('#top-right', window.parent.document).val() || '',
		br_bl = jQuery('#bottom-left', window.parent.document).val() || '',
		br_br = jQuery('#bottom-right', window.parent.document).val() || '',
		color = jQuery('#br-color', window.parent.document).val() || '',
		br_type =
			jQuery('#smile_adv_border_opt', window.parent.document).val() || '',
		bw_all = jQuery('#width-allsides', window.parent.document).val() || '',
		bw_t = jQuery('#width-top', window.parent.document).val() || '',
		bw_l = jQuery('#width-left', window.parent.document).val() || '',
		bw_r = jQuery('#width-right', window.parent.document).val() || '',
		bw_b = jQuery('#width-bottom', window.parent.document).val() || '',
		bw_type =
			jQuery(
				'#smile_adv_borderwidth_opt',
				window.parent.document
			).val() || '';

	let border_string = 'br_type:' + br_type + '|';
	border_string += 'br_all:' + br_all + '|';
	border_string += 'br_tl:' + br_tl + '|';
	border_string += 'br_tr:' + br_tr + '|';
	border_string += 'br_br:' + br_br + '|';
	border_string += 'br_bl:' + br_bl + '|';
	border_string += 'style:' + style + '|';
	border_string += 'color:' + color + '|';
	border_string += 'bw_type:' + bw_type + '|';
	border_string += 'bw_all:' + bw_all + '|';
	border_string += 'bw_t:' + bw_t + '|';
	border_string += 'bw_l:' + bw_l + '|';
	border_string += 'bw_r:' + bw_r + '|';
	border_string += 'bw_b:' + bw_b;

	// style dependent variables
	const border_str = border_string, // data.border,
		box_shadow_str = box_shadow_string, //	data.box_shadow,
		cp_md_overlay = jQuery('.cp-modal-body-overlay'),
		cp_modal_content = jQuery('.cp-modal-content');
	style = data.style;

	const box_shadow = generate_box_shadow(box_shadow_str);

	//	Update SELECTOR for 'Flat Discount' style - Apply - Border
	if (style === 'flat_discount') {
		generate_and_apply_border_css('.cp-modal-body-inner', border_str);
	} else {
		generate_and_apply_border_css('.cp-modal-content', border_str);
	}

	// if( jQuery('.cp-modal').hasClass('cp-modal-custom-size') ) {
	if (box_shadow.indexOf('inset') > -1) {
		// UPDATE - [data-css-selector] to set the box shadow target
		jQuery('.smile-input[name="box_shadow"]', window.parent.document).attr(
			'data-css-selector',
			'.cp-modal-body-overlay'
		);

		//	Apply - Box Shadow
		generate_and_apply_box_shadow_css(
			'.cp-modal-body-overlay',
			box_shadow_str
		);
		cp_modal_content.css('box-shadow', 'none');
	} else {
		// UPDATE - [data-css-selector] to set the box shadow target
		jQuery('.smile-input[name="box_shadow"]', window.parent.document).attr(
			'data-css-selector',
			'.cp-modal-content'
		);

		//	Apply - Box Shadow
		generate_and_apply_box_shadow_css('.cp-modal-content', box_shadow_str);
		cp_md_overlay.css('box-shadow', 'none');
	}
}

/**
 * Backgorund color/image/gradient
 *
 * @param {Array} data
 */
function apply_background_type(data) {
	const style = data.style;

	if (style !== 'jugaad') {
		const type = data.module_bg_color_type,
			cp_modal_body = jQuery('.cp-modal-body'),
			bg_color = data.modal_bg_color,
			bg_img_color = data.modal_bg_img_color,
			gradient_val = data.module_bg_gradient;
		let cp_modal_overlay = jQuery('.cp-modal-body-overlay');

		if (String(style) === 'countdown') {
			cp_modal_overlay = jQuery('.cp-counter-container');
		}

		switch (type) {
			case 'simple':
				cp_modal_body.css('background-image', '');
				cp_modal_body.css('background-position', '');
				cp_modal_body.css('background-repeat', '');
				cp_modal_body.css('background-size', '');
				cp_modal_overlay.css('background', '');
				cp_modal_overlay.css('background-color', bg_color);
				break;

			case 'image':
				cp_modal_overlay.css('background', '');
				cp_update_bg_image(
					smile_global_data,
					'.cp-modal-body',
					'.cp-modal-content',
					'modal_bg_image',
					'modal_bg_image_src'
				);
				cp_modal_overlay.css('background-color', bg_img_color);
				break;

			case 'gradient':
				cp_modal_overlay.css('background-color', '');
				cp_modal_body.css('background-image', '');
				cp_modal_body.css('background-position', '');
				cp_modal_body.css('background-repeat', '');
				cp_modal_body.css('background-size', '');
				const grad_css = cp_genrate_bg_type_gradient(gradient_val);
				const val_arr = grad_css.split('|');
				cp_modal_overlay.css({
					background: val_arr[3],
					background: val_arr[2],
					background: val_arr[1],
					background: val_arr[0],
				});

				break;

			case 'default':
				break;
		}
	} else {
		const type_one = data.module_bg1_color_type,
			type_sec = data.module_bg2_color_type,
			cp_modal_body = jQuery('.cp-form-section'),
			cp_modal_overlay = jQuery('.cp-form-section-overlay'),
			cp_modal_body1 = jQuery('.cp-content-section'),
			cp_modal_overlay1 = jQuery('.cp-content-section-overlay'),
			bg_color1 = data.modal_bg1_color,
			gradient_val1 = data.module_bg_gradient_one,
			bg_color2 = data.modal_bg12_color,
			gradient_val2 = data.module_bg_gradient_sec;

		switch (type_one) {
			case 'simple':
				cp_modal_body.css('background-image', '');
				cp_modal_body.css('background-position', '');
				cp_modal_body.css('background-repeat', '');
				cp_modal_body.css('background-size', '');
				cp_modal_overlay.css('background', '');
				cp_modal_overlay.css('background-color', bg_color1);
				break;

			case 'image':
				cp_modal_overlay.css('background', '');
				cp_update_bg_image(
					smile_global_data,
					'.cp-form-section',
					'',
					'form_bg_image',
					'form_bg_image_src'
				);
				cp_modal_overlay.css('background-color', bg_color1);
				break;

			case 'gradient':
				cp_modal_overlay.css('background-color', '');
				cp_modal_body.css('background-image', '');
				cp_modal_body.css('background-position', '');
				cp_modal_body.css('background-repeat', '');
				cp_modal_body.css('background-size', '');
				const grad_css = cp_genrate_bg_type_gradient(gradient_val1);
				const val_arr = grad_css.split('|');
				cp_modal_overlay.css({
					background: val_arr[3],
					background: val_arr[2],
					background: val_arr[1],
					background: val_arr[0],
				});
				break;

			case 'default':
				break;
		}

		//for sec background
		switch (type_sec) {
			case 'simple':
				cp_modal_body1.css('background-image', '');
				cp_modal_body1.css('background-position', '');
				cp_modal_body1.css('background-repeat', '');
				cp_modal_body1.css('background-size', '');
				cp_modal_overlay1.css('background', '');
				cp_modal_overlay1.css('background-color', bg_color2);
				break;

			case 'image':
				cp_modal_overlay1.css('background', '');
				cp_update_bg_image(
					smile_global_data,
					'.cp-content-section',
					'',
					'content_bg_image',
					'content_bg_image_src'
				);
				cp_modal_overlay1.css('background-color', bg_color2);
				break;

			case 'gradient':
				cp_modal_overlay1.css('background-color', '');
				cp_modal_body1.css('background-image', '');
				cp_modal_body1.css('background-position', '');
				cp_modal_body1.css('background-repeat', '');
				cp_modal_body1.css('background-size', '');
				const grad_css = cp_genrate_bg_type_gradient(gradient_val2);
				const val_arr = grad_css.split('|');
				cp_modal_overlay1.css({
					background: val_arr[3],
					background: val_arr[2],
					background: val_arr[1],
					background: val_arr[0],
				});
				break;

			case 'default':
				break;
		}
	}
}

/**
 * [apply_overlay_background_type description]
 *
 * @param {Array} data
 */
function apply_overlay_background_type(data) {
	const type = data.module_overlay_color_type,
		cp_overlay = jQuery('.cp-overlay'),
		cp_overlay_bg = jQuery('.cp-overlay-background'),
		overlay_color = data.modal_overlay_bg_color,
		overlay_img_color = data.modal_img_overlay_bg_color;
	switch (type) {
		case 'simple':
			cp_overlay.css('background-image', '');
			cp_overlay.css('background-position', '');
			cp_overlay.css('background-repeat', '');
			cp_overlay.css('background-size', '');
			cp_overlay.css('background', '');
			cp_overlay_bg.css('background-color', overlay_color);
			break;

		case 'image':
			cp_overlay_bg.css('background', '');
			cp_update_bg_image(
				smile_global_data,
				'.cp-overlay',
				'',
				'overlay_bg_image',
				'overlay_bg_image_src'
			);
			cp_overlay_bg.css('background-color', overlay_img_color);
			break;
	}
}

/**
 * [cp_genrate_bg_type_gradient description]
 *
 * @param {string} gradient_val
 */
function cp_genrate_bg_type_gradient(gradient_val) {
	const val_arr = gradient_val.split('|'),
		first_color = val_arr[0],
		sec_color = val_arr[1],
		first_deg = val_arr[2],
		sec_deg = val_arr[3],
		grad_type = val_arr[4],
		direction = val_arr[5];

	let grad_name = '',
		grad_css = '';

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
			if (String(grad_type) === 'linear') {
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

	if (String(grad_type) === 'linear') {
		ie_css = `${grad_type}-gradient(to ${grad_name}, ${first_color} ${first_deg}%, ${sec_color} ${sec_deg}%)`;
		web_css = `-webkit-${grad_type}-gradient(${grad_name}, ${first_color} ${first_deg}%, ${sec_color} ${sec_deg}%)`;
		o_css = `-o-${grad_type}-gradint(${grad_name}, ${first_color} ${first_deg}%, ${sec_color} ${sec_deg}%)`;
		mz_css = `-moz-${grad_type}-gadient(${grad_name}, ${first_color} ${first_deg}%, ${sec_color} ${sec_deg}%)`;
	} else {
		ie_css = `${grad_type}-gradient( ellipse farthest-corner at ${grad_name}, ${first_color} ${first_deg}%, ${sec_color} ${sec_deg}%)`;
		web_css = `-webkit-${grad_type}-gradient( ellipse farthest-corner at ${grad_name}, ${first_color} ${first_deg}%, ${sec_color} ${sec_deg}%)`;
		o_css = `-o-${grad_type}-gradient( ellipse farthest-corner at ${grad_name}, ${first_color} ${first_deg}%, ${sec_color} ${sec_deg}%)`;
		mz_css = `-moz-${grad_type}-gradient( ellipse farthest-corner at ${grad_name}, ${first_color} ${first_deg}%, ${sec_color} ${sec_deg}%)`;
	}

	grad_css = ie_css + '|' + web_css + '|' + o_css + '|' + mz_css;
	return grad_css;
}

/**
 * Window load
 */
jQuery(window).on('load', function () {
	parent.customizerLoaded();
});

/**
 * trigger after Ajax success
 */
jQuery(document).on('cp_ajax_loaded', function (e, data) {
	// do your stuff here.
	cp_tooltip_reinitialize(data);
});

jQuery(window).on('pageshow', function () {
	// Width & Height values of image.
	const cp_image = jQuery('.cp-image-container img');
	cp_image.attr('width', cp_image.css('width'));
	cp_image.attr('height', cp_image.css('height'));
});

/**
 * Remove - Modal Image
 *
 * Also, Replaced [data-css-image-url] with empty. [Which is used to updated image URL without AJAX.]
 */
parent
	.jQuery(window.parent.document)
	.on('radio_image_click', function (e, elm, val) {
		const elm_data = val;
		const elm_id = elm;

		const single_data = {};
		single_data[elm_id] = decodeURIComponent(elm_data);

		//	Update single instance from global variable 'smile_global_data'
		smile_global_data[elm_id] = decodeURIComponent(elm_data);

		jQuery(document).trigger('smile_customizer_field_change', [
			single_data,
		]);
	});

parent
	.jQuery(window.parent.document)
	.on('smile-colorpicker-change', function (e, el, val) {
		if (jQuery(el).hasClass('content_bg_color')) {
			smile_global_data.content_bg_color = val;
			jugaad_modal_layout_setup(smile_global_data);
		}

		const style = smile_global_data.style || null;

		if (jQuery(el).hasClass('form_bg_color')) {
			smile_global_data.form_bg_color = val;
			if (String(style) === 'jugaad') {
				jugaad_modal_layout_setup(smile_global_data);
			}
		}

		if (jQuery(el).hasClass('modal_title_bg_color')) {
			if (String(style) === 'special_offer') {
				smile_global_data.modal_title_bg_color = val;
				set_triangle_color(smile_global_data);
			}
		}

		if (jQuery(el).hasClass('modal_bg_img_color')) {
			smile_global_data.modal_bg_img_color = val;
			apply_background_type(smile_global_data);
		}
	});

/**
 * trigger smile_customizer_field_change
 */
jQuery(document).on('smile_customizer_field_change', function (e, single_data) {
	if (
		'module_bg_color_type' in single_data ||
		'module_bg1_color_type' in single_data ||
		'module_bg2_color_type' in single_data
	) {
		apply_background_type(smile_global_data);
	}

	if ('module_overlay_color_type' in single_data) {
		apply_overlay_background_type(smile_global_data);
	}

	//	Update box shadow
	if ('shadow_type' in single_data) {
		apply_border_and_shadow(smile_global_data);
	}

	//	toggle - modal padding
	const content_padding = single_data.content_padding || null;
	if ('content_padding' in single_data) {
		single__toggle_class(
			'.cp-modal-body',
			'cp-no-padding',
			content_padding,
			1
		);
	}

	//	toggle - swap image & contents
	const image_position = single_data.image_position || null;
	if ('image_position' in single_data) {
		single__toggle_class(
			'.cp-text-container',
			'cp-right-contain',
			image_position,
			0
		);
	}

	/**
	 * Modal Image
	 */
	//	AJAX update image URL
	if (
		'modal_image_size' in single_data ||
		'modal_img_src' in single_data ||
		'modal_img_custom_url' in single_data
	) {
		cp_update_ajax_modal_image_src(smile_global_data);
	}

	/**
	 * Modal Background Image
	 */
	//	AJAX update image size - full / thumbnail / medium etc.
	if ('modal_bg_image_size' in single_data) {
		cp_update_ajax_bg_image_size(
			smile_global_data,
			'.cp-modal-body',
			'.cp-modal-content',
			'modal_bg_image',
			'opt_bg'
		);
	}

	//	Background Image - 	repeat
	const opt_bg_rpt = single_data.opt_bg_rpt || null;
	if ('opt_bg_rpt' in single_data) {
		if (
			'modal_size' in smile_global_data &&
			String(smile_global_data.modal_size) === 'cp-modal-custom-size'
		) {
			add_css('.cp-modal-body', 'background-repeat', opt_bg_rpt);
		} else {
			add_css('.cp-modal-content', 'background-repeat', opt_bg_rpt);
		}
	}

	//	Background Image - 	position
	const opt_bg_pos = single_data.opt_bg_pos || null;
	if ('opt_bg_pos' in single_data) {
		if (
			'modal_size' in smile_global_data &&
			String(smile_global_data.modal_size) === 'cp-modal-custom-size'
		) {
			add_css('.cp-modal-body', 'background-position', opt_bg_pos);
		} else {
			add_css('.cp-modal-content', 'background-position', opt_bg_pos);
		}
	}

	//	Background Image - 	size
	const opt_bg_size = single_data.opt_bg_size || null;
	if ('opt_bg_size' in single_data) {
		if (
			'modal_size' in smile_global_data &&
			String(smile_global_data.modal_size) === 'cp-modal-custom-size'
		) {
			add_css('.cp-modal-body', 'background-size', opt_bg_size);
		} else {
			add_css('.cp-modal-content', 'background-size', opt_bg_size);
		}
	}

	// Overlay image ---------------
	//	AJAX update image size - full / thumbnail / medium etc.
	if ('overlay_bg_image_size' in single_data) {
		cp_update_ajax_bg_image_size(
			smile_global_data,
			'.cp-overlay',
			'',
			'overlay_bg_image',
			'overlay_bg'
		);
	}

	//	1) Overlay Background Image-repeat
	const overlay_bg_rpt = single_data.overlay_bg_rpt || null;
	if ('overlay_bg_rpt' in single_data) {
		add_css('.cp-overlay', 'background-repeat', overlay_bg_rpt);
	}

	//	2) Overlay Background Image-position
	const overlay_bg_pos = single_data.overlay_bg_pos || null;
	if ('overlay_bg_pos' in single_data) {
		add_css('.cp-overlay', 'background-position', overlay_bg_pos);
	}

	//	2) Overlay Background Image-position
	const overlay_bg_size = single_data.overlay_bg_size || null;
	if ('overlay_bg_size' in single_data) {
		add_css('.cp-overlay', 'background-size', overlay_bg_size);
	}

	//	Animations
	if ('overlay_effect' in single_data || 'exit_animation' in single_data) {
		cp_apply_animations(smile_global_data);
	}

	const style = smile_global_data.style || null;
	if (String(style) !== 'YouTube') {
		if (
			'modal_bg_image_src' in single_data ||
			'modal_bg_image_custom_url' in single_data
		) {
			cp_update_bg_image(
				smile_global_data,
				'.cp-modal-body',
				'.cp-modal-content',
				'modal_bg_image',
				'modal_bg_image_src'
			);
		} else if (
			'form_bg_image_src' in single_data ||
			'form_bg_image_custom_url' in single_data
		) {
			cp_update_bg_image(
				smile_global_data,
				'.cp-form-section',
				'',
				'form_bg_image',
				'form_bg_image_src'
			);
		} else if (
			'content_bg_image_src' in single_data ||
			'content_bg_image_custom_url' in single_data
		) {
			cp_update_bg_image(
				smile_global_data,
				'.cp-content-section',
				'',
				'content_bg_image',
				'content_bg_image_src'
			);
		}
	}

	//	Modal Size
	if (
		'modal_size' in single_data ||
		'modal_bg_image_src' in single_data ||
		'modal_bg_image_custom_url' in single_data ||
		'overlay_bg_image_src' in single_data ||
		'overlay_bg_image_custom_url' in single_data
	) {
		update_modal_size(smile_global_data.modal_size);

		//	Modal width
		cp_modal_width_settings(smile_global_data);

		//	Reapply box_shadow
		apply_border_and_shadow(smile_global_data);

		//rearrange image
		cp_update_bg_image(
			smile_global_data,
			'.cp-modal-body',
			'.cp-modal-content',
			'modal_bg_image',
			'modal_bg_image_src'
		);

		//overlay image
		cp_update_bg_image(
			smile_global_data,
			'.cp-overlay',
			'',
			'overlay_bg_image',
			'overlay_bg_image_src'
		);
	}

	/**
	 * Jugaad Background Image
	 */
	//	AJAX update image size - full / thumbnail / medium etc.
	if ('form_bg_image_size' in single_data) {
		cp_update_ajax_bg_image_size(
			smile_global_data,
			'.cp-form-section',
			'',
			'form_bg_image',
			'form_opt_bg'
		);
	}

	//	Background Image - 	repeat

	if (typeof smile_global_data.form_opt_bg !== 'undefined') {
		const form_opt_bg_rpt = single_data.form_opt_bg_rpt || null;
		if ('form_opt_bg_rpt' in single_data) {
			add_css('.cp-form-section', 'background-repeat', form_opt_bg_rpt);
		}

		//	Background Image - 	position
		const form_opt_bg_pos = single_data.form_opt_bg_pos || null;
		if ('form_opt_bg_pos' in single_data) {
			add_css('.cp-form-section', 'background-position', form_opt_bg_pos);
		}

		//	Background Image - 	size
		const form_opt_bg_size = single_data.form_opt_bg_size || null;
		if ('form_opt_bg_size' in single_data) {
			add_css('.cp-form-section', 'background-size', form_opt_bg_size);
		}

		const content_opt_bg_rpt = single_data.content_opt_bg_rpt || null;
		if ('content_opt_bg_rpt' in single_data) {
			add_css(
				'.cp-content-section',
				'background-repeat',
				content_opt_bg_rpt
			);
		}

		//	Background Image - 	position
		const content_opt_bg_pos = single_data.content_opt_bg_pos || null;
		if ('content_opt_bg_pos' in single_data) {
			add_css(
				'.cp-content-section',
				'background-position',
				content_opt_bg_pos
			);
		}

		//	Background Image - 	size
		const content_opt_bg_size = single_data.content_opt_bg_size || null;
		if ('content_opt_bg_size' in single_data) {
			add_css(
				'.cp-content-section',
				'background-size',
				content_opt_bg_size
			);
		}
	}

	if (
		'modal_layout' in single_data ||
		'modal_col_width' in single_data ||
		'form_separator' in single_data ||
		'form_sep_part_of' in single_data
	) {
		jugaad_modal_layout_setup(smile_global_data);
	}

	//	Affiliate Settings
	if ('affiliate_setting' in single_data) {
		cp_affilate_disable(single_data);
	}
});

jQuery(document).on('smile_data_received', function (e, data) {
	global_initial_call(data);
});

jQuery(document).on('smile_data_continue_received', function (e, data) {
	cp_tooltip_settings(data);
	cp_tooltip_reinitialize(data);
	cp_add_custom_css(data);
	apply_custom_ht(data);
	cp_row_equilize();
});

parent.jQuery(window.parent.document).on('cp-slider-slide', function (e, el) {
	if (
		jQuery(el).hasClass('submit_button_tb_padding') ||
		jQuery(el).hasClass('form_input_padding_tb') ||
		jQuery(el).hasClass('form_input_font_size')
	) {
		CPModelHeight();
		cp_form_sep_top();
	}
});

parent
	.jQuery(window.parent.document)
	.on('smile-select-change smile-switch-change', function () {
		cp_row_equilize();
	});

parent.jQuery(window.parent.document).on('multiBoxUpdated', function () {
	CPModelHeight();
	cp_column_equilize();
	cp_form_sep_top();
});

parent
	.jQuery(window.parent.document)
	.on('smile-radio-image-change', function (e, el) {
		const s = jQuery(el);
		if (s.hasClass('form_layout')) {
			CPModelHeight();
			cp_column_equilize();
		}
		if (
			s.hasClass('form_layout') ||
			s.hasClass('modal_layout') ||
			s.hasClass('form_grid_structure')
		) {
			cp_form_sep_top(); //jugad style seperator
		}
	});

function global_initial_call(data) {
	const style = data.style || null;
	switch (style) {
		case 'optin_to_win':
		case 'direct_download':
		case 'free_ebook':
			// Modal image
			cp_image_processing(data);
			single__toggle_class(
				'.cp-text-container',
				'cp-right-contain',
				data.image_position,
				0
			); //	Toggle - Image position Left or Right
			break;
		case 'special_offer':
			set_triangle_color(data);
			break;
	}

	data.modal_size =
		typeof data.modal_size === 'undefined'
			? 'cp-modal-custom-size'
			: data.modal_size;

	cp_set_image(data, 'modal');

	update_modal_size(data.modal_size);

	//	Custom CSS
	cp_add_custom_css(data);

	//	Apply Animations To Modal
	cp_apply_animations(data);

	//	Affiliate Settings
	cp_affilate_settings(data);
	cp_affilate_reinitialize(data);

	// //	ToolTip Settings
	cp_tooltip_settings(data); // close button and tooltip related settings
	cp_tooltip_reinitialize(data); // reinitialize tooltip on modal resize

	//	Set Modal Width
	cp_modal_width_settings(data);

	if (typeof data.modal_layout !== 'undefined') {
		jugaad_modal_layout_setup(data);
	}

	//	Modal Background Image

	if (typeof data.form_bg_image !== 'undefined') {
		cp_update_bg_image(
			data,
			'.cp-form-section',
			'',
			'form_bg_image',
			'form_bg_image_src'
		);
	}

	if (typeof data.content_bg_image !== 'undefined') {
		cp_update_bg_image(
			data,
			'.cp-content-section',
			'',
			'content_bg_image',
			'content_bg_image_src'
		);
	}

	if (String(style) !== 'jugaad' && String(style) !== 'YouTube') {
		cp_update_bg_image(
			data,
			'.cp-modal-body',
			'.cp-modal-content',
			'modal_bg_image',
			'modal_bg_image_src'
		);
	}

	if (typeof data.overlay_bg_image !== 'undefined') {
		cp_update_bg_image(
			smile_global_data,
			'.cp-overlay',
			'',
			'overlay_bg_image',
			'overlay_bg_image_src'
		);
	}

	//	Border & Shadow
	apply_border_and_shadow(data);

	//Gradient background
	apply_background_type(data);

	//overlay background
	apply_overlay_background_type(data);

	//	'cp_empty_classes' is a classes array defined in another file
	//	Add Cp-empty Class To Empty Containers
	jQuery.each(cp_empty_classes, function (key, value) {
		if (jQuery(value).length !== 0) {
			cp_add_empty_class(key, value);
		}
	});

	if (typeof data.cp_custom_height !== 'undefined') {
		apply_custom_ht(data);
	}

	const content_padding = data.content_padding || null;
	if (typeof content_padding !== 'undefined') {
		single__toggle_class(
			'.cp-modal-body',
			'cp-no-padding',
			content_padding,
			1
		);
	}
	CPModelHeight();
}

/**
 * 3. 	CKEditors Setup - ( Modal, SlideIn, InfoBar )
 *
 * CKEditor setup of all modules.
 *
 * @param {Array} data
 */
function cp_ckeditors_setup(data) {
	const cp_title = jQuery('.cp-title'),
		cp_description = jQuery('.cp-description'),
		cp_short_description = jQuery('.cp-short-description'),
		cp_confidential = jQuery('.cp-info-container'),
		cp_desc_bottom = jQuery('.cp-desc-bottom'),
		cp_mid_desc = jQuery('#mid_desc_editor'),
		cp_modal_popup_container = jQuery('.cp-modal-popup-container'),
		cp_count_down_desc = jQuery('.cp-count-down-desc'),
		cp_sec_title = jQuery('.cp-sec-title'),
		modal_title_color = data.modal_title_color || null,
		modal_sec_title_color = data.modal_sec_title_color || null,
		sec_title = data.modal_sec_title || null,
		tip_color = data.tip_color || null,
		modal_desc_color = data.modal_desc_color || null,
		varient_style_id = data.variant_style_id || null;

	let modal_title = data.modal_title1 || null,
		modal_short_desc = data.modal_short_desc1 || null,
		modal_short_description = data.modal_short_desc || null,
		modal_confidential = data.modal_confidential || null,
		modal_content = data.modal_content || null,
		style_id = data.style_id || null,
		count_down_title = data.count_down_title || null,
		modal_middle_desc = data.modal_middle_desc || null;

	// 	Add Style Id As Class To Container
	if (cp_isValid(varient_style_id)) {
		style_id = varient_style_id;
	}
	cp_modal_popup_container.addClass(style_id);

	// 	Modal Title Editor
	if (jQuery('#main_title_editor').length) {
		if (cp_isValid(modal_title)) {
			modal_title = htmlEntities(modal_title);
			if (typeof InstallTrigger !== 'undefined') {
				CKEDITOR.inline('main_title_editor');
			}

			cp_title.html(modal_title);
			if (CKEDITOR.instances.main_title_editor !== undefined) {
				CKEDITOR.instances.main_title_editor.setData(modal_title);
			}
		}

		//	Color
		if (cp_isValid(modal_title_color)) {
			cp_title.css('color', modal_title_color);
		}
	}

	// 	Secondary Title Editor
	if (jQuery('#sec_title_editor').length) {
		if (cp_isValid(sec_title)) {
			const modal_sec_title = htmlEntities(sec_title);
			cp_sec_title.html(modal_sec_title);
			if (CKEDITOR.instances.sec_title_editor !== undefined) {
				CKEDITOR.instances.sec_title_editor.setData(modal_sec_title);
			}
		}

		//	Color
		if (cp_isValid(modal_sec_title_color)) {
			cp_sec_title.css('color', modal_sec_title_color);
		}
	}

	//	Short Description Editor 1
	if (jQuery('#desc_editor').length) {
		if (cp_isValid(modal_short_desc)) {
			modal_short_desc = htmlEntities(modal_short_desc);
			cp_description.html(modal_short_desc);
			if (CKEDITOR.instances.desc_editor !== undefined) {
				CKEDITOR.instances.desc_editor.setData(modal_short_desc);
			}
		}

		//	Color
		if (cp_isValid(modal_desc_color)) {
			cp_description.css('color', modal_desc_color);
		}
	}

	//	Short Description Editor
	if (jQuery('#short_desc_editor').length) {
		if (cp_isValid(modal_short_description)) {
			modal_short_description = htmlEntities(modal_short_description);
			cp_short_description.html(modal_short_description);
			CKEDITOR.instances.short_desc_editor.setData(
				modal_short_description
			);
		}
	}

	//	Confidential Info Editor
	if (jQuery('#info_editor').length) {
		if (cp_isValid(modal_confidential)) {
			modal_confidential = htmlEntities(modal_confidential);
			cp_confidential.html(modal_confidential);
			if (CKEDITOR.instances.info_editor !== undefined) {
				CKEDITOR.instances.info_editor.setData(modal_confidential);
			}
		}

		//	Color
		if (cp_isValid(tip_color)) {
			cp_confidential.css('color', tip_color);
		}
	}

	//	Description Bottom
	if (jQuery('#description_bottom').length) {
		if (cp_isValid(modal_content)) {
			modal_content = htmlEntities(modal_content);
			cp_desc_bottom.html(modal_content);
			if (CKEDITOR.instances.description_bottom !== undefined) {
				CKEDITOR.instances.description_bottom.setData(modal_content);
			}
		}
	}

	//	Count Down Editor
	if (jQuery('#count_down_editor').length) {
		if (cp_isValid(count_down_title)) {
			count_down_title = htmlEntities(count_down_title);
			cp_count_down_desc.html(count_down_title);
			if (CKEDITOR.instances.count_down_editor !== undefined) {
				CKEDITOR.instances.count_down_editor.setData(count_down_title);
			}
		}
	}

	// 	Extra middle editor
	if (jQuery('#mid_desc_editor').length) {
		if (cp_isValid(modal_middle_desc)) {
			modal_middle_desc = htmlEntities(modal_middle_desc);
			cp_mid_desc.html(modal_middle_desc);
			if (CKEDITOR.instances.mid_desc_editor !== undefined) {
				CKEDITOR.instances.mid_desc_editor.setData(modal_middle_desc);
			}
		}
	}

	//Short title
	if (typeof data.modal_short_title !== 'undefined') {
		let modal_short_title = data.modal_short_title;
		const cp_short_title = jQuery('.cp-short-title');
		modal_short_title = htmlEntities(modal_short_title);
		cp_short_title.html(modal_short_title);
		if (
			jQuery('#short_title_editor').length &&
			CKEDITOR.instances.short_title_editor !== undefined
		) {
			CKEDITOR.instances.short_title_editor.setData(modal_short_title);
		}
	}

	// Modal Note
	if (typeof data.modal_note_1 !== 'undefined') {
		let modal_note_1 = data.modal_note_1;
		const cp_modal_note = jQuery('.cp-modal-note');
		modal_note_1 = htmlEntities(modal_note_1);
		cp_modal_note.html(modal_note_1);
		if (
			jQuery('#modal_note_1').length &&
			CKEDITOR.instances.modal_note_1 !== undefined
		) {
			CKEDITOR.instances.modal_note_1.setData(modal_note_1);
		}
	}

	// Modal Note
	if (typeof data.modal_note_2 !== 'undefined') {
		let modal_note_2 = data.modal_note_2;
		const cp_modal_note_2 = jQuery('.cp-modal-note-2');
		modal_note_2 = htmlEntities(modal_note_2);
		cp_modal_note_2.html(modal_note_2);
		if (
			jQuery('#modal_note_2').length &&
			CKEDITOR.instances.modal_note_2 !== undefined
		) {
			CKEDITOR.instances.modal_note_2.setData(modal_note_2);
		}
	}
}

function jugaad_modal_layout_setup(data) {
	// html container variables
	const form_section = jQuery('.cp-form-section'),
		content_section = jQuery('.cp-content-section');

	// data variables
	const namefield = data.namefield,
		form_bg_color = data.form_bg_color,
		content_bg_color = data.content_bg_color,
		form_separator = data.form_separator,
		form_sep_part_of = data.form_sep_part_of,
		modal_layout = data.modal_layout,
		modal_col_width = data.modal_col_width;

	let addContentClasses = '',
		addFormClasses = '';

	const rmClasses =
		'col-md-6 col-sm-6 col-lg-6 col-md-5 col-sm-5 col-lg-5 col-md-7 col-sm-7 col-lg-7';

	if (
		String(modal_layout) === 'form_left' ||
		String(modal_layout) === 'form_right' ||
		String(modal_layout) === 'form_left_img_top' ||
		String(modal_layout) === 'form_right_img_top'
	) {
		if (Number(modal_col_width) === 0) {
			addFormClasses = addContentClasses = 'col-md-6 col-sm-6 col-lg-6';
		} else {
			addFormClasses = 'col-md-5 col-sm-5 col-lg-5';
			addContentClasses = 'col-md-7 col-sm-7 col-lg-7';
		}
	}

	content_section
		.removeClass(rmClasses + 'form-sep-padding cp-columns-equalized')
		.addClass(addContentClasses);
	jQuery('.cp-form-section')
		.removeClass(rmClasses + ' form-sep-padding')
		.addClass(addFormClasses);
	jQuery('.cp-jugaad-text-container,.cp-image-container')
		.removeClass('col-md-5 col-sm-5 col-lg-5 col-md-7 col-sm-7 col-lg-7')
		.addClass('col-md-12 col-sm-12');
	jQuery('.cp-jugaad-text-container,.cp-info-container').addClass(
		'cp-text-center'
	);

	form_section.insertBefore('.cp-content-section');
	jQuery('.cp-image-container').insertBefore('.cp-jugaad-text-container');
	jQuery('.cp-image-container').removeClass('cp-hidden-element');
	jQuery('.cp-image-container').show();
	let form_sep_pos = 'vertical';
	let form_sep_direction = 'upward';
	jQuery('.cp-modal-body > .cp-row').attr(
		'class',
		'cp-row cp-table ' + modal_layout
	);
	jQuery('.cp-modal-body > .cp-row').css('height', 'auto');

	if (Number(modal_col_width) === 0) {
		jQuery('.cp-modal-body > .cp-row').addClass('form-one-by-two');
	} else {
		jQuery('.cp-modal-body > .cp-row').addClass('form-one-third');
	}

	switch (modal_layout) {
		case 'form_left':
			content_section.css('height', 'auto');
			form_section
				.addClass('form-pos-left')
				.removeClass('form-pos-right form-pos-bottom');
			jQuery('.cp-image-container').addClass('cp-hidden-element');
			jQuery('.cp-jugaad-text-container')
				.addClass('txt-pos-bottom')
				.removeClass('txt-pos-left txt-pos-right');
			if (Number(form_sep_part_of) === 1) {
				form_sep_direction = 'downward';
			}
			break;
		case 'form_right':
			form_section
				.addClass('form-pos-right')
				.removeClass('form-pos-left form-pos-bottom');
			content_section
				.insertBefore('.cp-form-section')
				.css('height', 'auto');
			jQuery('.cp-image-container').addClass('cp-hidden-element');
			jQuery('.cp-jugaad-text-container')
				.addClass('txt-pos-bottom')
				.removeClass('txt-pos-left txt-pos-right');
			if (Number(form_sep_part_of) === 0) {
				form_sep_direction = 'downward';
			}
			break;
		case 'form_left_img_top':
			form_section
				.addClass('form-pos-left cp-column-equalized-center')
				.removeClass('form-pos-right form-pos-bottom');
			jQuery('.cp-jugaad-text-container')
				.addClass('txt-pos-bottom')
				.removeClass(
					'txt-pos-left txt-pos-right cp-column-equalized-center'
				);
			content_section.addClass('cp-column-equalized-center');
			jQuery('.cp-image-container')
				.insertBefore('.cp-jugaad-text-container')
				.removeClass('cp-column-equalized-center');
			if (Number(form_sep_part_of) === 1) {
				form_sep_direction = 'downward';
			}
			break;
		case 'form_right_img_top':
			form_section
				.addClass('form-pos-right cp-column-equalized-center')
				.removeClass('form-pos-left form-pos-bottom');
			content_section
				.insertBefore('.cp-form-section')
				.css('height', 'auto')
				.addClass('cp-column-equalized-center');
			jQuery('.cp-image-container')
				.insertBefore('.cp-jugaad-text-container')
				.removeClass('cp-column-equalized-center');
			jQuery('.cp-jugaad-text-container').removeClass(
				'cp-column-equalized-center'
			);
			if (Number(form_sep_part_of) === 0) {
				form_sep_direction = 'downward';
			}
			break;
		case 'img_left_form_bottom':
			jQuery('.cp-modal-body > .cp-row')
				.addClass('cp-block')
				.removeClass('cp-table');
			form_section
				.addClass('form-pos-bottom')
				.removeClass('form-pos-right form-pos-left');
			jQuery('.cp-form-section,.cp-content-section')
				.removeClass(
					'col-md-5 col-sm-5 col-lg-5 col-md-7 col-sm-7 col-lg-7'
				)
				.addClass('col-md-12 col-sm-12 col-lg-12');
			jQuery('.cp-jugaad-text-container')
				.removeClass(
					'col-md-12 col-sm-12 col-sm-5 col-md-5 col-lg-5 cp-text-center'
				)
				.addClass(
					'col-md-7 col-sm-7 col-lg-7 cp-column-equalized-center'
				);
			jQuery('.cp-image-container')
				.removeClass('col-md-12 col-sm-12 cp-text-center')
				.addClass(
					'col-md-5 col-sm-5 col-lg-5 cp-column-equalized-center'
				);
			content_section
				.insertBefore('.cp-form-section')
				.addClass('cp-columns-equalized');
			jQuery('.cp-image-container').insertBefore(
				'.cp-jugaad-text-container'
			);
			jQuery('.cp-info-container').removeClass('cp-text-center');
			form_sep_pos = 'horizontal';
			if (Number(form_sep_part_of) === 0) {
				form_sep_direction = 'downward';
			}

			jQuery(
				'.cp-form-with-name .cp-name-form,.cp-form-with-name .cp-email-form'
			)
				.addClass('col-md-6 col-sm-6 col-lg-6')
				.removeClass('col-md-12 col-sm-12 col-lg-12');
			if (String(namefield) === '1') {
				jQuery('.cp-name-form,.cp-email-form,.cp-submit-container')
					.addClass('col-md-5 col-sm-5 col-lg-5')
					.removeClass(
						'col-sm-6 col-md-6 col-lg-6 col-sm-12 col-lg-12 col-md-12 col-sm-7 col-md-7'
					);
			}

			break;
		case 'img_right_form_bottom':
			jQuery('.cp-modal-body > .cp-row')
				.addClass('cp-block')
				.removeClass('cp-table')
				.css('height', 'auto');
			form_section
				.addClass('form-pos-bottom')
				.removeClass('form-pos-right form-pos-left');
			jQuery('.cp-form-section,.cp-content-section')
				.removeClass(
					'col-md-5 col-sm-5 col-lg-5 col-md-7 col-sm-7 col-lg-7'
				)
				.addClass('col-md-12 col-sm-12 col-lg-12');
			jQuery('.cp-jugaad-text-container')
				.removeClass(
					'col-md-12 col-sm-12 col-sm-5 col-md-5 col-lg-5 cp-text-center'
				)
				.addClass(
					'col-md-7 col-sm-7 col-lg-7 cp-column-equalized-center'
				);
			jQuery('.cp-image-container')
				.removeClass('col-md-12 col-sm-12 cp-text-center')
				.addClass(
					'col-md-5 col-sm-5 col-lg-5 cp-column-equalized-center'
				);
			content_section
				.insertBefore('.cp-form-section')
				.addClass('cp-columns-equalized');
			jQuery('.cp-jugaad-text-container').insertBefore(
				'.cp-image-container'
			);
			jQuery('.cp-info-container').removeClass('cp-text-center');
			form_sep_pos = 'horizontal';
			if (Number(form_sep_part_of) === 0) {
				form_sep_direction = 'downward';
			}
			jQuery(
				'.cp-form-with-name .cp-name-form,.cp-form-with-name .cp-email-form'
			)
				.addClass('col-md-6 col-sm-6 col-lg-6')
				.removeClass('col-md-12 col-sm-12 col-lg-12');
			if (String(namefield) === '1') {
				jQuery('.cp-name-form,.cp-email-form,.cp-submit-container')
					.addClass('col-md-5 col-sm-5 col-lg-5')
					.removeClass(
						'col-sm-6 col-md-6 col-lg-6 col-sm-12 col-lg-12 col-md-12 col-sm-7 col-md-7'
					);
			}
			break;
		case 'form_bottom_img_top':
			jQuery('.cp-modal-body > .cp-row')
				.addClass('cp-block')
				.removeClass('cp-table');
			form_section
				.addClass('form-pos-bottom')
				.removeClass('form-pos-right form-pos-left');
			jQuery('.cp-form-section,.cp-content-section')
				.removeClass(
					'col-md-5 col-sm-5 col-lg-5 col-md-7 col-sm-7 col-lg-7'
				)
				.addClass('col-md-12 col-sm-12 col-lg-12');
			jQuery('.cp-jugaad-text-container')
				.removeClass(
					'col-md-7 col-sm-7 col-sm-5 col-md-5 col-lg-5 cp-column-equalized-center'
				)
				.addClass('col-md-12 col-sm-12 col-lg-12 cp-text-center');
			jQuery('.cp-image-container')
				.removeClass('col-md-5 col-sm-5 cp-column-equalized-center')
				.addClass('col-md-12 col-sm-12 col-lg-12 cp-text-center');
			content_section
				.insertBefore('.cp-form-section')
				.css('height', 'auto');
			jQuery('.cp-image-container').insertBefore(
				'.cp-jugaad-text-container'
			);
			form_sep_pos = 'horizontal';
			if (Number(form_sep_part_of) === 0) {
				form_sep_direction = 'downward';
			}
			if (String(namefield) === '1') {
				jQuery('.cp-name-form,.cp-email-form,.cp-submit-container')
					.addClass('col-md-5 col-sm-5 col-lg-5')
					.removeClass(
						'col-sm-6 col-md-6 col-lg-6 col-sm-12 col-lg-12 col-md-12 col-sm-7 col-md-7'
					);
			}
			break;
		case 'form_bottom':
			jQuery('.cp-image-container').addClass('cp-hidden-element');
			jQuery('.cp-modal-body > .cp-row')
				.addClass('cp-block')
				.removeClass('cp-table');
			form_section
				.addClass('form-pos-bottom')
				.removeClass('form-pos-right form-pos-left');
			jQuery('.cp-form-section,.cp-content-section')
				.removeClass(
					'col-md-5 col-sm-5 col-lg-5 col-md-7 col-sm-7 col-lg-7'
				)
				.addClass('col-md-12 col-sm-12 col-lg-12');
			jQuery('.cp-jugaad-text-container')
				.removeClass(
					'col-md-7 col-sm-7 col-sm-5 col-md-5 col-lg-5 cp-column-equalized-center'
				)
				.addClass('col-md-12 col-sm-12 col-lg-12 cp-text-center');
			jQuery('.cp-image-container')
				.removeClass('col-md-5 col-sm-5 cp-column-equalized-center')
				.addClass('col-md-12 col-sm-12 col-lg-12 cp-text-center');
			content_section
				.insertBefore('.cp-form-section')
				.css('height', 'auto');
			form_sep_pos = 'horizontal';
			if (Number(form_sep_part_of) === 0) {
				form_sep_direction = 'downward';
			}
			if (String(namefield) === '1') {
				jQuery('.cp-name-form,.cp-email-form,.cp-submit-container')
					.addClass('col-md-5 col-sm-5 col-lg-5')
					.removeClass(
						'col-sm-6 col-md-6 col-lg-6 col-sm-12 col-lg-12 col-md-12 col-sm-7 col-md-7'
					);
			}
			break;
	}

	let svg = '',
		svgFillColor = '';
	if (Number(form_sep_part_of) === 0) {
		svgFillColor = content_bg_color;
	} else {
		svgFillColor = form_bg_color;
	}

	jQuery('.cp-form-separator').remove();

	const viewbox = cp_get_viewbox_svg(form_separator);

	const shape = form_separator;

	if (form_separator !== 'none') {
		svg = cp_get_svg(shape, svgFillColor, viewbox, form_sep_part_of);

		if (String(form_sep_pos) === 'horizontal') {
			if (Number(form_sep_part_of) === 0) {
				jQuery('.cp-modal-body .cp-row .cp-content-section').append(
					'<div class="cp-form-separator ' +
						form_separator +
						' ' +
						modal_layout +
						' ' +
						form_sep_direction +
						' part-of-content cp-fs-' +
						form_sep_pos +
						' cp-fs-' +
						form_sep_pos +
						'-content" >' +
						svg +
						'<div>'
				);
			} else {
				jQuery('.cp-modal-body .cp-row .cp-form-section').append(
					'<div class="cp-form-separator ' +
						form_separator +
						' ' +
						modal_layout +
						' ' +
						form_sep_direction +
						' part-of-form cp-fs-' +
						form_sep_pos +
						' cp-fs-' +
						form_sep_pos +
						'-form" >' +
						svg +
						'<div>'
				);
			}
		} else if (Number(form_sep_part_of) === 0) {
			jQuery('.cp-modal-body .cp-row').append(
				'<div class="cp-form-separator ' +
					form_separator +
					' ' +
					modal_layout +
					' ' +
					form_sep_direction +
					' part-of-content cp-fs-' +
					form_sep_pos +
					' cp-fs-' +
					form_sep_pos +
					'-content" >' +
					svg +
					'<div>'
			);
		} else {
			jQuery('.cp-modal-body .cp-row').append(
				'<div class="cp-form-separator ' +
					form_separator +
					' ' +
					modal_layout +
					' ' +
					form_sep_direction +
					' part-of-form cp-fs-' +
					form_sep_pos +
					' cp-fs-' +
					form_sep_pos +
					'-form" >' +
					svg +
					'<div>'
			);
		}
	}

	cp_column_equilize();
	form_sep_position();
	cp_form_sep_top();
	cp_set_width_svg();
}

function apply_custom_ht(data) {
	//apply custom height to body
	const cp_custom_height = data.cp_custom_height,
		cp_modal_height = data.cp_modal_height,
		cp_modal_body = jQuery('.cp-modal-body');
	if (Number(cp_custom_height) === 1) {
		if (!cp_modal_body.find('.cp-row').hasClass('cp-row-center')) {
			cp_modal_body.find('.cp-row').addClass('cp-row-center');
		}
		if (
			!cp_modal_body
				.find('.cp-subtitle-container')
				.hasClass('cp-row-equalized-center') ||
			!cp_modal_body
				.find('.cp-text-container')
				.hasClass('cp-row-equalized-center')
		) {
			cp_modal_body
				.find('.cp-subtitle-container')
				.addClass('cp-row-equalized-center');
			cp_modal_body
				.find('.cp-text-container')
				.addClass('cp-row-equalized-center');
		}

		cp_modal_body.css('min-height', cp_modal_height + 'px');
	} else {
		cp_modal_body.css('min-height', '');
		cp_modal_body.find('.cp-row').removeClass('cp-row-center');
		cp_modal_body
			.find('.cp-subtitle-container')
			.removeClass('cp-row-equalized-center');
		cp_modal_body
			.find('.cp-text-container')
			.removeClass('cp-row-equalized-center');
	}
}

function set_triangle_color(data) {
	const modal_title_bg_color = data.modal_title_bg_color,
		cp_description = jQuery('.cp-description');
	cp_description.css('border-top-color', modal_title_bg_color);
}
