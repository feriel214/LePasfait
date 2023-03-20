/* eslint-env jquery */
(function ($) {
	'use strict';

	jQuery(document).on('smile_data_received', function () {
		CPResponsiveTypoInit();
	});

	// z-index fixes for manual display
	$('.slidein-overlay').each(function () {
		const style_id = $(this).data('slidein-style');
		if (typeof style_id !== 'undefined' && style_id !== '') {
			const container = $(this).parents(
				'.cp-slidein-popup-container.' + style_id
			);
			if (!$(this).hasClass('cp-slidein-inline')) {
				container.appendTo(document.body);
			}
		}
	});

	jQuery(window).on('slideinOpen', function (e, data) {
		jQuery('html').addClass('cp-si-open');
		//show close btn after x sec
		let close_btn_delay = data.data('close-btnonload-delay');
		// convert delay time from seconds to miliseconds
		close_btn_delay = Math.round(close_btn_delay * 1000);

		if (close_btn_delay) {
			setTimeout(function () {
				data.find('.slidein-overlay-close').removeClass(
					'cp-hide-close'
				);
			}, close_btn_delay);
		}

		//  slide in height
		CP_slide_in_height();
		const cp_animate = data.find('.cp-animate-container'),
			animationclass = cp_animate.data('overlay-animation'),
			animatedwidth = cp_animate.data('disable-animationwidth'),
			vw = jQuery(window).width();

		if (vw >= animatedwidth || typeof animatedwidth === 'undefined') {
			jQuery(cp_animate).addClass('smile-animated ' + animationclass);
		}

		if (data.find('.cp-slidein-toggle').length > 0) {
			setTimeout(function () {
				data.find('.cp-animate-container').css('height', 'auto');
				data.find('.cp-animate-container').animate(
					{ opacity: '1' },
					'1000'
				);
			}, '400');
		}

		//for close modal after x  sec of inactive
		let inactive_close_time = data.data('close-after');

		jQuery.idleTimer('destroy');
		if (typeof inactive_close_time !== 'undefined') {
			inactive_close_time = inactive_close_time * 1000;
			setTimeout(function () {
				data.addClass('cp-close-after-x');
			}, inactive_close_time);

			jQuery(document).idleTimer({
				timeout: inactive_close_time,
				idle: false,
			});
		}

		if (jQuery('.kleo-carousel-features-pager').length > 0) {
			setTimeout(function () {
				$(window).trigger('resize');
			}, 1500);
		}

		if (data.hasClass('cp-minimize-widget')) {
			data.addClass('cp-hide-slide-widget');
			setTimeout(function () {
				const this_cls = jQuery('.cp-slidein-head .cp-slidein-toggle');
				toggle_widget(this_cls, 500);
			}, 0);
		} else {
			data.find('.cp-slidein-toggle').addClass('cp-widget-open');
		}

		cp_slide_in_column_equilize();
	});

	//close slide in on cp-close class
	jQuery(document).on('click', '.cp-close', function () {
		if (
			!jQuery(this).parents('.slidein-overlay').hasClass('do_not_close')
		) {
			const slidein = jQuery(this).parents('.slidein-overlay');
			jQuery(document).trigger('closeSlideIn', [slidein]);
		}
	});

	//close slide in on cp-inner-close class
	jQuery(document).on('click', '.cp-inner-close', function () {
		const slidein = jQuery(this).parents('.slidein-overlay');
		jQuery(document).trigger('closeSlideIn', [slidein]);
	});

	jQuery(document).on('click', '.slidein-overlay', function () {
		if (
			!jQuery(this).hasClass('do_not_close') &&
			jQuery(this).hasClass('close_btn_nd_overlay')
		) {
			const slidein = jQuery(this);
			jQuery(document).trigger('closeSlideIn', [slidein]);
		}
	});

	jQuery(document).on('click', '.slidein-overlay .cp-slidein', function (e) {
		e.stopPropagation();
	});

	jQuery(document).on('si_conversion_done', function (e, $this) {
		if (
			!jQuery($this).parents('.cp-form-container').find('.cp-email')
				.length > 0
		) {
			const is_only_conversion = jQuery($this)
				.parents('.cp-form-container')
				.find('[name="only_conversion"]').length;
			if (is_only_conversion > 0) {
				jQuery($this).addClass('cp-disabled');
			}
		}
	});

	function cp_slide_slidein() {
		jQuery('.cp-toggle-container').on('click', function () {
			const slidein_overlay = jQuery(this).closest('.slidein-overlay'),
				toggle_visibility = slidein_overlay
					.closest('.cp-slidein-popup-container')
					.siblings('.overlay-show')
					.data('toggle-visible');

			if (!slidein_overlay.hasClass('cp-slide-without-toggle')) {
				slidein_overlay.removeClass('cp-hide-contianer');
				jQuery(this).toggleClass('cp-slide-hide-btn');

				const cp_animate_container = slidein_overlay.find(
						'.cp-animate-container'
					),
					entryanimation =
						cp_animate_container.data('overlay-animation'),
					cp_slide_edit_btn = jQuery('.cp-toggle-container'),
					animatedwidth = cp_animate_container.data(
						'disable-animationwidth'
					),
					vw = jQuery(window).width(),
					cp_tooltip = slidein_overlay
						.find('.cp-tooltip-icon')
						.data('classes');
				let animateclass = '';

				const is_imp_added = slidein_overlay.data('impression-added');

				if (toggle_visibility === true) {
					if (
						typeof is_imp_added === 'undefined' &&
						!slidein_overlay.hasClass('cp-disabled-impression')
					) {
						//ConvertPlus_slidein.update_impressions( styleArray );
						slidein_overlay.data('impression-added', 'true');
					}
				}

				if (
					vw >= animatedwidth ||
					typeof animatedwidth === 'undefined'
				) {
					animateclass = 'smile-animated ';
				}

				const tootltip = slidein_overlay.find('.has-tip').attr('class');
				if (typeof tootltip !== 'undefined') {
					jQuery('head').append(
						'<style class="cp-tooltip-hide">.tip.' +
							cp_tooltip +
							'{display:block }</style>'
					);
				}

				cp_animate_container.attr(
					'class',
					'cp-animate-container cp-hide-slide'
				);

				setTimeout(function () {
					cp_animate_container.attr(
						'class',
						'cp-animate-container ' +
							animateclass +
							' ' +
							entryanimation
					);
					cp_slide_edit_btn.addClass('cp-slide-hide-btn');
				}, 10);

				cp_slide_in_column_equilize();
				$(window).trigger('resize');
			}
		});

		jQuery('.slidein-overlay-close').on('click', function () {
			if (!jQuery(this).hasClass('do_not_close')) {
				const slidein = jQuery(this).parents('.slidein-overlay'),
					cp_tooltip = slidein
						.find('.cp-tooltip-icon')
						.data('classes');

				jQuery(document).trigger('closeSlideIn', [slidein]);
				jQuery('head').append(
					'<style class="cp-tooltip-hide">.tip.' +
						cp_tooltip +
						'{ display:none; }</style>'
				);
			}

			const slidein_overlay = jQuery(this).closest('.slidein-overlay');
			if (!slidein_overlay.hasClass('cp-slide-without-toggle')) {
				const cp_animate_container = slidein_overlay.find(
						'.cp-animate-container'
					),
					exitanimation = cp_animate_container.data('exit-animation'),
					cp_slide_edit_btn = jQuery('.cp-toggle-container'),
					animatedwidth = cp_animate_container.data(
						'disable-animationwidth'
					),
					vw = jQuery(window).width(),
					form = slidein_overlay.find('.cp-form').attr('class');
				let animateclass = '';

				if (
					vw >= animatedwidth ||
					typeof animatedwidth === 'undefined'
				) {
					animateclass = 'smile-animated ';
				}
				slidein_overlay.addClass('cp-hide-contianer');
				cp_animate_container.attr('class', 'cp-animate-container');
				cp_animate_container.attr(
					'class',
					'cp-animate-container ' + animateclass + ' ' + exitanimation
				);
				if (typeof form !== 'undefined') {
					slidein_overlay.find('.smile-optin-form')[0].reset();
					slidein_overlay
						.find('.cp-form-processing-wrap')
						.css('display', 'none');
					slidein_overlay
						.find('.cp-form-processing')
						.removeAttr('style');
					slidein_overlay
						.find('.cp-msg-on-submit')
						.removeAttr('style');
					slidein_overlay.find('.cp-msg-on-submit').html();
					slidein_overlay.find('.cp-m-success').remove();
				}

				setTimeout(function () {
					cp_animate_container.addClass('cp-hide-slide');
					cp_slide_edit_btn.removeClass('cp-slide-hide-btn');
					cp_animate_container.removeClass(exitanimation);
				}, 500);
			}
		});
	}

	// This function will add placeholder css to head
	function add_placeholdercolor_css() {
		jQuery('.slidein-overlay').each(function () {
			const $this = jQuery(this),
				placeholder_color = $this.data('placeholder-color'),
				placeholder_font = $this.data('placeholder-font'),
				uid = $this.data('class'),
				defaultColor = placeholder_color,
				styleContent =
					'.' +
					uid +
					' input { font-family: ' +
					placeholder_font +
					' } .' +
					uid +
					' ::-webkit-input-placeholder {color: ' +
					defaultColor +
					'!important; font-family: ' +
					placeholder_font +
					'; } .' +
					uid +
					' :-moz-placeholder {color: ' +
					defaultColor +
					'!important; font-family: ' +
					placeholder_font +
					';} .' +
					uid +
					' ::-moz-placeholder {color: ' +
					defaultColor +
					'!important; font-family: ' +
					placeholder_font +
					'; }';

			jQuery(
				'<style id=' +
					uid +
					" type='text/css'>" +
					styleContent +
					'</style>'
			).appendTo('head');
		});

		jQuery('.cp-slidein-inline').each(function () {
			const placeholder_color = jQuery(this).data('placeholder-color'),
				placeholder_font = jQuery(this).data('placeholder-font'),
				uid = jQuery(this).data('slidein-id'),
				defaultColor = placeholder_color,
				styleContent =
					'.' +
					uid +
					' input { font-family: ' +
					placeholder_font +
					' } .' +
					uid +
					' ::-webkit-input-placeholder {color: ' +
					defaultColor +
					'!important; font-family: ' +
					placeholder_font +
					'; } .' +
					uid +
					' :-moz-placeholder {color: ' +
					defaultColor +
					'!important; font-family: ' +
					placeholder_font +
					';} .' +
					uid +
					' ::-moz-placeholder {color: ' +
					defaultColor +
					'!important; font-family: ' +
					placeholder_font +
					'; }';

			jQuery(
				'<style id=' +
					uid +
					" type='text/css'>" +
					styleContent +
					'</style>'
			).appendTo('head');
		});
	}

	jQuery(document).ready(function () {
		jQuery(document).on('keydown', function (e) {
			if (e.which === 27) {
				const cp_overlay = jQuery('.si-open');
				const slidein = cp_overlay;
				if (
					cp_overlay.hasClass('close_btn_nd_overlay') &&
					!cp_overlay.hasClass('do_not_close')
				) {
					jQuery(document).trigger('closeSlideIn', [slidein]);
				}
			}
		});

		//  Set normal values in data attribute to reset these on window resize
		CPResponsiveTypoInit();

		//for open and close slidein on click of button
		cp_slide_slidein();
		// Placeholder css
		add_placeholdercolor_css();
	});
})(jQuery);
