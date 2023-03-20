/* eslint-env jquery */
(function () {
	/**
	 *  1. FitText.js 1.2 - (http://sam.zoy.org/wtfpl/)
	  -----------------------------------------------------------*/
	(function ($) {
		$.fn.fitText = function (kompressor, options) {
			// Setup options
			const compressor = kompressor || 1,
				settings = $.extend(
					{
						minFontSize: Number.NEGATIVE_INFINITY,
						maxFontSize: Number.POSITIVE_INFINITY,
					},
					options
				);
			return this.each(function () {
				// Store the object
				const $this = $(this);
				// Resizer() resizes items based on the object width divided by the compressor * 10
				const resizer = function () {
					$this.css(
						'font-size',
						Math.max(
							Math.min(
								$this.width() / (compressor * 10),
								parseFloat(settings.maxFontSize)
							),
							parseFloat(settings.minFontSize)
						)
					);
				};
				// Call once to set.
				resizer();
				// Call on resize. Opera debounces their resize by default.
				$(window).on(
					'resize.fittext orientationchange.fittext',
					resizer
				);
			});
		};
	})(jQuery);

	/**
	 * 2. CP Responsive - (Required - FitText.js)
	 *
	 * Required to call on READY & LOAD
	 * -----------------------------------------------------------
	 *
	 * @param {Object} s
	 * @param {string} fs
	 */
	function CPApplyFlatText(s, fs) {
		if (typeof s.fitText === 'function') {
			if (
				s.hasClass('cp-description') ||
				s.hasClass('cp-short-description') ||
				s.hasClass('cp-info-container')
			) {
				s.fitText(1.7, { minFontSize: '12px', maxFontSize: fs });
			} else {
				s.fitText(1.2, { minFontSize: '16px', maxFontSize: fs });
			}
		}
	}
	function CPAutoResponsiveResize() {
		jQuery('.cp_responsive').each(function (index, el) {
			const s = jQuery(el);
			let fs = s.css('font-size'),
				lh = '';

			const ww = jQuery(window).width(),
				CKE_FONT = s.attr('data-font-size'),
				Def_FONT = s.attr('data-font-size-init'),
				CKE_LINE_HEIGHT = s.attr('data-line-height'),
				Def_LineHeight = s.attr('data-line-height-init');

			if (CKE_FONT) {
				fs = CKE_FONT; //  1. CKEditor font sizes from editor
			} else if (Def_FONT) {
				fs = Def_FONT; //  2. Initially stored font size
			}

			//  Initially set empty line height
			if (CKE_LINE_HEIGHT) {
				lh = CKE_LINE_HEIGHT; //  1. CKEditor font sizes from editor
			} else if (Def_LineHeight) {
				lh = Def_LineHeight; //  2. Initially stored font size
			}

			if (ww <= 800) {
				//  Apply default line-height - If it does not contain class - `cp_line_height`
				s.css({ display: 'block', 'line-height': '1.15em' });
				CPApplyFlatText(s, fs);
			} else {
				s.css({ display: '', 'line-height': lh });

				check_responsive_font_sizes();

				//  Apply `fit-text` for all CKEditor elements - ( .cp-title,  .cp-description etc. )
				if (typeof s.fitText === 'function') {
					s.fitText(1.2, { minFontSize: fs, maxFontSize: fs });
				}
			}
		});
	}

	jQuery(document).ready(function () {
		//  Set normal values in data attribute to reset these on window resize
		setTimeout(function () {
			CPResponsiveTypoInit();

			//for link color change
			cp_color_for_list_tag();
		}, 1500);

		// hide image for small devices
		hide_image_on_smalldevice();

		// box shadow for all form style
		apply_boxshaddow();

		// function to call CP_slide_in_height() when text area resize
		apply_resize_on_textarea();

		if (jQuery('.slidein-overlay').length > 0) {
			let count = 0;
			jQuery('.slidein-overlay').each(function () {
				if (
					!jQuery(this)
						.find('.cp-slidein-content')
						.hasClass('ps-container')
				) {
					if (
						!jQuery(this)
							.find('.cp-slidein-content')
							.hasClass('si-open')
					) {
						count++;
						const old_id = jQuery(this)
							.find('.cp-slidein-content')
							.attr('id');
						jQuery(this)
							.find('.cp-slidein-content')
							.attr('id', old_id + '-' + count);
					}
					const id = jQuery(this)
						.find('.cp-slidein-content')
						.attr('id');
					if (typeof Ps !== 'undefined') {
						Ps.initialize(document.getElementById(id));
					}
				}
			});
		}

		setTimeout(function () {
			//hide sidebar for slidein
			hide_sidebar();
		}, 500);

		cp_slidein_social_responsive();

		//setTimeout(function() {
		cp_slide_in_column_equilize();
		// }, 1000 );
	});

	jQuery(window).on('resize', function () {
		/*  = Responsive Typography
		 *-----------------------------------------------------------*/
		CPAutoResponsiveResize();

		// hide image for small devices
		hide_image_on_smalldevice();

		CP_slide_in_height();

		cp_slidein_social_responsive();

		//setTimeout(function() {
		cp_slide_in_column_equilize();
		//}, 300 );
	});

	/**
	 *	 This function will hide image on small devices
	 */
	function hide_image_on_smalldevice() {
		jQuery('.slidein-overlay').each(function () {
			const vw = jQuery(window).innerWidth();
			const hidewidth = jQuery(this).data('hide-img-on-mobile');
			if (hidewidth) {
				if (vw <= hidewidth) {
					jQuery(this)
						.find('.cp-image-container')
						.addClass('cp-hide-image');
				} else {
					jQuery(this)
						.find('.cp-image-container')
						.removeClass('cp-hide-image');
				}
			}
		});
	}
})(jQuery);

/**
 *  Check inner span has set font size
 */
check_responsive_font_sizes();
function check_responsive_font_sizes() {
	//  Apply font sizes
	jQuery('.cp_responsive[data-font-size-init]').each(function (index, el) {
		if (jQuery(el).find('.cp_font').length) {
			//  Added class `cp-no-responsive` to over ride the init font size of all - elements i.e. - cp-title, .cp-description etc.
			//  Add for only parents not inner child's
			if (!jQuery(el).hasClass('.cp_font, .cp_line_height')) {
				jQuery(el).addClass('cp-no-responsive');
			}
		} else {
			//  If child element not found class - `cp_font` then remove class `cp-no-responsive`
			jQuery(el).removeClass('cp-no-responsive');
		}
	});
}

/**
 *  Set normal values in data attribute to reset these on window resize
 */
function CPResponsiveTypoInit() {
	//  1. Add font size attribute
	jQuery('.cp_responsive').each(function (index, el) {
		const s = jQuery(el);
		let hasData;
		//  Add attribute `data-line-height-init` for all `cp_responsive` classes. Except `.cp_line_height` which is added from editor.
		if (!s.hasClass('cp_line_height')) {
			//  Set `init` font size data attribute
			const fs = s.css('font-size');
			hasData = s.attr('data-font-size');
			if (!hasData) {
				s.attr('data-font-size-init', fs);
			}
		}

		//  Add attribute `data-line-height-init` for all `cp_responsive` classes. Except `.cp_font` which is added from editor.
		if (!s.hasClass('cp_font')) {
			//  Set `init` line height data attribute
			const lh = s.css('line-height');
			hasData = s.attr('data-line-height');
			if (!hasData) {
				s.attr('data-line-height-init', lh);
			}
		}
	});

	check_responsive_font_sizes();

	//  Slide In height
	CP_slide_in_height();
}

/**
 * This function adjust height for Slide In
 * Loop for all live Slide In's
 *
 */
function CP_slide_in_height() {
	setTimeout(function () {
		//  Loop all live Slide In's
		jQuery('.cp-slidein-popup-container').each(function (index, element) {
			const slide_in_overlay = jQuery(this).find('.slidein-overlay');

			if (slide_in_overlay.hasClass('si-open')) {
				const t = jQuery(element),
					slidein = t.find('.cp-slidein'),
					slide_overlay = t.find('.slidein-overlay'),
					slidein_body_height = t
						.find('.cp-slidein-body')
						.outerHeight();

				if (slidein_body_height > jQuery(window).height()) {
					slidein.addClass('cp-slidein-exceed');
					slide_overlay.each(function (i, el) {
						if (jQuery(el).hasClass('si-open')) {
							jQuery('html').addClass('cp-exceed-vieport');
						}
					});
					slidein.css('height', slidein_body_height);
				} else {
					slidein.removeClass('cp-slidein-exceed');
					jQuery('html').removeClass('cp-exceed-vieport');
					slidein.css('height', '');
				}
			}
		});
	}, 1200);
}

// function to change color for list type according to span color
function cp_color_for_list_tag() {
	jQuery('.slidein-overlay').each(function () {
		const moadal_style = jQuery(this)
			.find('.cp-slidein-body')
			.attr('class')
			.split(' ')[1];

		jQuery(this)
			.find('li')
			.each(function () {
				if (
					jQuery(this).parents('.cp_social_networks').length === 0 ||
					jQuery(this).parents('.custom-html-form').length === 0
				) {
					const is_responsive_cls =
						jQuery(this).parents('.cp_responsive').length;
					if (is_responsive_cls) {
						const parent_li = jQuery(this)
								.parents('.cp_responsive')
								.attr('class')
								.split(' ')[0],
							cnt = jQuery(this).index() + 1,
							font_size = jQuery(this)
								.find('.cp_font')
								.css('font-size');
						let color = jQuery(this).find('span').css('color');
						let list_type = jQuery(this).parent();

						list_type = list_type[0].nodeName.toLowerCase();
						let style_type = '';

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
				}
			});
	});
}

//function for box shadow for form field

function apply_boxshaddow(data) {
	jQuery('.slidein-overlay').each(function () {
		let border_color = jQuery(this)
				.find('.cp-form-container')
				.find('.cp-email')
				.css('border-color'),
			classname = jQuery(this).data('class'),
			cont_class = jQuery(this).data('class');
		const moadal_style = jQuery(this)
			.find('.cp-slidein-body')
			.attr('class')
			.split(' ')[1];

		if (jQuery(this).hasClass('ps-container')) {
			cont_class = jQuery(this).data('ps-id');
			border_color = data;
			classname = 'slidein-overlay';
		}

		jQuery('.cp-box-shaddow-' + cont_class).remove();
		jQuery('head').append(
			'<style class="cp-box-shaddow-' +
				cont_class +
				'">.' +
				classname +
				' .cp-slidein .' +
				moadal_style +
				' input.cp-email:focus,  .' +
				classname +
				' .cp-slidein .' +
				moadal_style +
				' input.cp-name:focus {  box-shadow: 0 0 4px ' +
				border_color +
				';}</style>'
		);
	});
}

/*
 * for social media responsive icon*
 */
function cp_slidein_social_responsive() {
	const wh = jQuery(window).width();
	jQuery('.cp-slidein-body')
		.find('.cp_social_networks')
		.each(function () {
			if (
				!jQuery(this)
					.parents('.cp-slidein-body')
					.hasClass('cp-floating-social-bar')
			) {
				const column_no = jQuery(this).data('column-no');
				let classname = '';
				if (wh < 768) {
					jQuery(this).removeClass('cp_social_networks');
					jQuery(this).removeClass(column_no);
					classname = jQuery(this).attr('class');
					jQuery(this).attr(
						'class',
						'cp_social_networks cp_social_autowidth ' +
							' ' +
							classname
					);
				} else {
					jQuery(this).removeClass('cp_social_networks');
					jQuery(this).removeClass('cp_social_autowidth');
					jQuery(this).removeClass(column_no);
					classname = jQuery(this).attr('class');
					jQuery(this).attr(
						'class',
						'cp_social_networks ' +
							' ' +
							column_no +
							' ' +
							classname
					);
				}
			}
		});
}

/* Toggle Slide In on click of button */
jQuery('body').on('click', '.cp-slidein-head .cp-slidein-toggle', function (e) {
	const this_cls = jQuery(this);
	toggle_widget_call(e, this_cls);
});

/* Toggle Slide In on click of slidein header */
jQuery('body').on('click', '.cp-minimize-onhead', function (e) {
	const this_cls = jQuery(this).find('.cp-slidein-toggle');
	toggle_widget_call(e, this_cls);
});

/**
 * toggle_widget_call
 *
 * @param {Object} e
 * @param {Object} this_cls
 */
function toggle_widget_call(e, this_cls) {
	e.preventDefault();
	this_cls.toggleClass('cp-widget-open');
	toggle_widget(this_cls, 600);
	e.stopPropagation();
}

/**
 * toggle_widget
 *
 * @param {Object} this_cls
 * @param {number} val
 */
function toggle_widget(this_cls, val) {
	const slidein = this_cls.parents('.si-open'),
		slidein_container = slidein.find('.cp-slidein');
	let border_width = slidein_container
		.find('.cp-slidein-content')
		.css('border-bottom-width');

	if (this_cls.hasClass('cp-widget-open')) {
		slidein_container.animate(
			{
				bottom: 0,
			},
			val
		);
	} else {
		let cp_slidein_body_ht;
		if (slidein_container.hasClass('cp-slidein-exceed')) {
			cp_slidein_body_ht = slidein_container.height();
		} else {
			cp_slidein_body_ht = slidein.find('.cp-slidein-body').outerHeight();
		}
		const cp_slidein_header_ht = slidein
			.find('.cp-slidein-head')
			.outerHeight();
		let bottomCss = cp_slidein_body_ht - cp_slidein_header_ht + 2;

		if (typeof border_width !== 'undefined' && border_width !== '') {
			border_width = border_width.replace('-', 'px');
			border_width = parseInt(border_width);
			if (slidein_container.hasClass('cp-slidein-exceed')) {
				bottomCss = bottomCss - border_width;
			} else {
				bottomCss = border_width + bottomCss;
			}
		}

		slidein_container.animate(
			{
				bottom: '-' + bottomCss + 'px',
			},
			val
		);

		setTimeout(function () {
			slidein_container
				.parents('.slidein-overlay')
				.removeClass('cp-hide-slide-widget');
		}, val);
	}
}

jQuery(this).on('smile_data_received', function (data) {
	const minimize_widget = data.minimize_widget || null;
	if (minimize_widget === 1) {
		jQuery('.cp-slidein-toggle').removeClass('cp-widget-open');
	}
	set_optin_widget_bottom();

	cp_slide_in_column_equilize();
});

function set_optin_widget_bottom() {
	setTimeout(function () {
		jQuery('.cp-slidein-popup-container').each(function () {
			if (jQuery(this).find('.cp-slidein-toggle').length > 0) {
				const slidein_container = jQuery(this).find('.cp-slidein');

				if (
					jQuery(this)
						.find('.cp-slidein-toggle')
						.hasClass('cp-widget-open')
				) {
					slidein_container.animate(
						{
							bottom: 0,
						},
						600
					);
				} else {
					let cp_slidein_body_ht;
					if (slidein_container.hasClass('cp-slidein-exceed')) {
						cp_slidein_body_ht = slidein_container.height();
					} else {
						cp_slidein_body_ht = jQuery(this)
							.find('.cp-slidein-body')
							.outerHeight();
					}

					const cp_slidein_header_ht = jQuery(this)
						.find('.cp-slidein-head')
						.outerHeight();
					let bottomCss =
						cp_slidein_body_ht - cp_slidein_header_ht + 2;
					let border_width = slidein_container
						.find('.cp-slidein-content')
						.css('border-bottom-width');

					if (
						typeof border_width !== 'undefined' &&
						border_width !== ''
					) {
						border_width = border_width.replace('-', 'px');
						border_width = parseInt(border_width);
						if (slidein_container.hasClass('cp-slidein-exceed')) {
							bottomCss = bottomCss - border_width;
						} else {
							bottomCss = border_width + bottomCss;
						}
					}

					slidein_container.animate(
						{
							bottom: '-' + bottomCss + 'px',
						},
						600
					);
				}
			}
		});
	}, 600);
}

function apply_resize_on_textarea() {
	jQuery('.slidein-overlay').each(function () {
		jQuery(this)
			.find('.cp-textarea')
			.each(function () {
				const textareas = jQuery(this);
				textareas.mouseup(function () {
					CP_slide_in_height();
				});
			});
	});
}

//hide sidebar for social media style
jQuery('body').on('click', '.cp_social_hide_sidebar', function (e) {
	e.preventDefault();
	const btn = jQuery(this);
	const slidein_container = jQuery(this).closest('.cp-slidein'),
		animate_container = slidein_container.find('.cp-animate-container');
	let entry_animation = animate_container.data('overlay-animation'),
		exit_animation = animate_container.data('exit-animation');
	const slidein_position = slidein_container.attr('class').split(' ')[1];

	const width = slidein_container.css('max-width');
	jQuery(this).toggleClass('cp_hidden_sidebar');

	switch (slidein_position) {
		case 'slidein-center-right':
			entry_animation = 'smile-slideInRight';
			exit_animation = 'smile-slideOutRight';
			break;
		case 'slidein-center-left':
			entry_animation = 'smile-slideInLeft';
			exit_animation = 'smile-slideOutLeft';
			break;
	}

	animate_container.attr('class', 'cp-animate-container');
	if (jQuery(this).hasClass('cp_hidden_sidebar')) {
		animate_container.attr(
			'class',
			'cp-animate-container smile-animated ' + exit_animation
		);
	} else {
		animate_container.attr(
			'class',
			'cp-animate-container smile-animated ' + entry_animation
		);
	}

	slidein_container.css('left', '');
	btn.css('left', '');

	slidein_container.css('right', '');
	btn.css('right', '');

	setTimeout(function () {
		if (btn.hasClass('cp_hidden_sidebar')) {
			if (slidein_position === 'slidein-center-left') {
				slidein_container.css('left', '-' + width);
				btn.css('left', '+' + width);
			} else if (slidein_position === 'slidein-center-right') {
				slidein_container.css('right', '-' + width);
				btn.css('right', '+' + width);
			}
		}
	}, 600);
});

function hide_sidebar() {
	jQuery('.slidein-overlay').each(function () {
		const slidein_overlay = jQuery(this).find('.cp-slidein');
		if (
			jQuery(this)
				.find('.cp_social_networks')
				.hasClass('cp-icon-style-top')
		) {
			slidein_overlay.append(
				'<span class="cp_social_hide_sidebar cp_social_icon">+</span>'
			);
		}
	});
}

/**
 * This function will apply height to cp-columns-equalized class
 */
function cp_slide_in_column_equilize() {
	setTimeout(function () {
		jQuery('.cp-columns-equalized').each(function () {
			// if modal is open then only apply equalize properties
			if (
				jQuery(this).closest('.slidein-overlay').hasClass('si-open') ||
				jQuery(this)
					.closest('.slidein-overlay')
					.hasClass('cp-slidein-inline')
			) {
				const wh = jQuery(window).width();

				const childClasses = Array();
				jQuery(this)
					.children('.cp-column-equalized-center')
					.each(function () {
						const contHeight = jQuery(this).outerHeight();
						childClasses.push(contHeight);
					});

				let count = 0;
				if (jQuery(this).find('.cp-image-container').length > 0) {
					jQuery(this)
						.find('.cp-highlight')
						.each(function () {
							count++;
						});
				}

				const pad_top = parseInt(jQuery(this).css('padding-top'));
				const pad_bottom = parseInt(jQuery(this).css('padding-top'));

				const tot_padding = pad_top + pad_bottom;

				let maxHeight =
					Math.max.apply(Math, childClasses) + tot_padding;
				maxHeight = maxHeight - count;

				if (wh > 768) {
					jQuery(this).css('height', maxHeight);
				} else {
					jQuery(this).css('height', 'auto');
				}
			}
		});
	}, 200);
}
