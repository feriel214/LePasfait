/* eslint-env jquery */
(function ($) {
	'use strict';

	/**
	 *  1. FitText.js 1.2 - (http://sam.zoy.org/wtfpl/)
	  -----------------------------------------------------------*/
	(function () {
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

	jQuery(document).ready(change_placeholdercolor);
	function change_placeholdercolor() {
		jQuery('.cp-info-bar').each(function () {
			const placeholder_color = jQuery(this).data('placeholder-color'),
				uid = jQuery(this).data('class'),
				defaultColor = placeholder_color,
				styleContent =
					'.' +
					uid +
					' ::-webkit-input-placeholder {color: ' +
					defaultColor +
					'!important;} .' +
					uid +
					' :-moz-placeholder {color: ' +
					defaultColor +
					'!important;} .' +
					uid +
					' ::-moz-placeholder {color: ' +
					defaultColor +
					'!important;}';

			jQuery(
				"<style type='text/css'>" + styleContent + '</style>'
			).appendTo('head');
		});
	}

	jQuery(document).on('ib_conversion_done', function (e, $this) {
		// do your stuff
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

	jQuery(document).on('infobarOpen', function (e, data) {
		let close_btn_delay = data.data('close-btnonload-delay');
		// convert delay time from seconds to miliseconds
		close_btn_delay = Math.round(close_btn_delay * 1000);

		jQuery('html').addClass('cp-ib-open');

		if (close_btn_delay) {
			setTimeout(function () {
				data.find('.ib-close').removeClass('cp-hide-close');
			}, close_btn_delay);
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
	});

	jQuery(document).ready(function () {
		jQuery('.cp-info-bar').each(function () {
			if (jQuery('body').hasClass('admin-bar')) {
				const admin_bar_ht = jQuery('#wpadminbar').outerHeight();
				if (
					jQuery(this).hasClass('cp-pos-top') &&
					!jQuery(this).hasClass('cp-info-bar-inline')
				) {
					jQuery(this).css('top', admin_bar_ht + 'px');
				}
			}
			let inactive_time = jQuery(this).data('inactive-time');
			if (typeof inactive_time !== 'undefined') {
				inactive_time = inactive_time * 1000;
				jQuery(document).idleTimer({
					timeout: inactive_time,
					idle: false,
				});
			}
		});

		// close info bar
		jQuery('.ib-close').on('click', function (e) {
			e.preventDefault();
			const info_bar = jQuery(this).parents('.cp-info-bar');
			jQuery(document).trigger('cp_close_info_bar', [info_bar]);
		});
	});
})(jQuery);
