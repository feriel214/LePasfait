/* eslint-env jquery */
(function ($) {
	'use strict';
	/**
	 * JavaScript class for working with third party services.@since 3.0.1
	 */
	let class_id = '';
	let modal = '';
	let doc_ref = '';
	const date = new Date();
	let scheduled = '';
	let dev_mode = '';
	let exit = '';
	let delay = '';
	let load_on_refresh = '';
	let scrollTill = '';
	let nounce = '';
	let parent_id = '';
	let cookieName = '';
	let temp_cookie = '';
	let cookie = '';
	let tmp_cookie = '';
	let referrer = '';
	let ref_check = '';
	let referred = true;
	let is_open = true;
	let isAutoPlay = '';
	let data = '';
	let inactive_time = '';
	let scrollTilllength = '';
	let scrollValue = '';
	let afterpost = false;
	let Youtube_on_tab = false;
	let scroll_class = '';
	let iframes = '';
	let styleArray = Array();
	const custom_class_arr = Array();
	let style = '';
	let info_bar = '';
	let toggle_visible = '';
	let anim = '';
	let ab_height = '';
	let custom_class = '';
	let module_type = '';
	let module = '';
	let cp_height = '';
	let slidein = '';
	let slidein_container = '';
	let delay_set = '';
	let ajax_run = true;
	let custom_selector = '';
	let floating_status = 0;
	let custom_style = '';
	let window_style = '';
	const add_flag = false;

	const ConvertPlus = {
		/**
		 * Initializes the all class variables.
		 *
		 * @param {Object} e
		 * @param {Object} element
		 * @param {Object} event
		 * @since 4.0.1
		 */
		init(e, element, event) {
			class_id = element.data('class-id');
			module_type = element.data('module-type');
			dev_mode = element.data('dev-mode');
			exit = element.data('exit-intent');
			modal = $('.' + class_id);
			delay_set = element.data('onload-delay');
			delay = delay_set * 1000; // convert delay time from seconds to miliseconds
			load_on_refresh = element.data('load-on-refresh');
			scrollTill = element.data('onscroll-value');
			nounce = element.find('.cp-impress-nonce').val();
			referrer = element.data('referrer-domain');
			ref_check = element.data('referrer-check');
			doc_ref = document.referrer.toLowerCase();
			isAutoPlay =
				modal.find('.cp-youtube-frame').attr('data-autoplay') || '0';
			inactive_time = element.data('inactive-time');
			scrollTilllength = jQuery('.cp-load-after-post').length;
			scrollValue = element.data('after-content-value');
			scroll_class = element.data('scroll-class');
			afterpost = element.hasClass('cp-after-post');
			custom_class = element.data('custom-class');
			custom_selector = element.data('custom-selector');
			iframes = modal.find('iframe');

			if (isAutoPlay !== '') {
				isAutoPlay =
					modal.find('.cp-youtube-continer').attr('data-autoplay') ||
					'0';
			}
			if (module_type === 'info-bar') {
				cookieName = element.data('info_bar-id');
				parent_id = element.data('parent-style');
				style = element.data('info_bar-style');
				info_bar = element;
				toggle_visible = element.data('toggle-visible');
				module = info_bar;
				afterpost = element.hasClass('ib-after-post');
				ConvertPlus._infoBarPos(info_bar); //set inofbar position
				scheduled = ConvertPlus._isScheduled(info_bar);
			} else if (module_type === 'modal') {
				parent_id = modal.data('parent-style');
				cookieName = element.data('modal-id');
				style = element.data('modal-style');
				scheduled = ConvertPlus._isScheduled(modal);
				module = modal;
				custom_style = modal
					.find('.cp-modal-body')
					.data('custom-style');
				window_style = modal
					.find('.cp-modal-content')
					.data('window-style');
			} else if (module_type === 'slide_in') {
				slidein = $('.' + class_id);
				cookieName = slidein.data('slidein-id');
				toggle_visible = element.data('toggle-visible');
				style = slidein.data('slidein-style');
				afterpost = element.hasClass('si-after-post');
				slidein_container = element.closest(
					'.cp-slidein-popup-container'
				);
				module = $('.' + class_id);
				scheduled = ConvertPlus._isScheduled(slidein);
				parent_id = slidein.data('parent-style');
				custom_style = slidein
					.find('.cp-slidein-body')
					.data('custom-style');
			}
			if (module_type === 'modal' && module.hasClass('cp-window-size')) {
				modal.windowSize();
			}

			if (typeof parent_id !== 'undefined') {
				cookieName = parent_id;
			}

			temp_cookie = 'temp_' + cookieName;
			ConvertPlus._removeCookie(temp_cookie);

			switch (event) {
				case 'load':
					if (delay_set !== '') {
						this._CploadEvent();
					}
					this._CpCustomClass();
					this._CpLoadImages();

					this._CpIframe();
					if (module_type === 'slide_in') {
						this._close_button_tootip();
					}
					break;

				case 'scroll':
					this._CpscrollEvent(e);
					break;

				case 'mouseleave':
					this._CpmouseleaveEvent(e);
					break;

				case 'closepopup':
					this._CpclosepopupEvent(e);
					break;

				case 'idle':
					this._CpidleEvent();
					break;
			}
		},
		/**
		 * Check modals visibility on first load
		 *
		 * @param {Object} md
		 */
		_hide_on_page_load(md) {
			let display = false;
			let cpdisabled_upto, numLoads;
			if (load_on_refresh === 'disabled') {
				cpdisabled_upto = md.data('load-on-count') - 1;
				numLoads = parseInt(
					ConvertPlus._getPageCookie(cookieName + 'pageLoads'),
					10
				);

				if (isNaN(numLoads) || numLoads <= 0) {
					ConvertPlus._setPageCookie(cookieName + 'pageLoads', 1);
				} else {
					ConvertPlus._setPageCookie(
						cookieName + 'pageLoads',
						numLoads + 1
					);
				}

				const count_load = ConvertPlus._getPageCookie(
					cookieName + 'pageLoads'
				);
				if (count_load > cpdisabled_upto) {
					display = true;
				}
			} else {
				ConvertPlus._removeCookie(cookieName + 'pageLoads');
			}
			return display;
		},
		/**
		 * Clsoe popup Event
		 *
		 * @param {Object} event
		 */
		_CpclosepopupEvent(event) {
			const type = module_type;
			let container,
				cp_animate,
				template,
				popupCookie,
				popupCookieName,
				cookieTime,
				entry_anim,
				exit_anim,
				animatedwidth,
				vw,
				parent_style_id,
				animate_push_page,
				page_push_down,
				iframe;

			if (type === 'modal' && typeof modal !== 'undefined') {
				cookieTime = modal.data('closed-cookie-time');
				cp_animate = modal.find('.cp-animate-container');
				entry_anim = modal.data('overlay-animation');
				exit_anim = cp_animate.data('exit-animation');
				animatedwidth = cp_animate.data('disable-animationwidth');
				vw = jQuery(window).width();
				parent_style_id = modal.data('parent-style');
				iframe = modal.find('iframe');

				// sets the volume to zero of vimeo video
				if (iframe.length === 1 && iframe.attr('src') !== undefined) {
					if (
						iframe[0].attributes.src.value.includes(
							'https://player.vimeo.com/'
						)
					) {
						const player = new Vimeo.Player(iframe);
						player.setVolume(0);
					}
				}

				if (typeof parent_style_id !== 'undefined') {
					popupCookieName = parent_style_id;
				} else {
					popupCookieName = modal.data('modal-id');
				}
				ConvertPlus._createCookie(temp_cookie, true, 1);
				popupCookie = ConvertPlus._getCookie(popupCookieName);
				ConvertPlus._cpExecuteVideoAPI(modal, 'pause');
				if (typeof event !== 'undefined') {
					event.preventDefault();
				}
				if (!popupCookie) {
					if (cookieTime) {
						ConvertPlus._createCookie(
							popupCookieName,
							true,
							cookieTime
						);
						ConvertPlus._cpExecuteVideoAPI(modal, 'pause');
					}
				}

				if (
					exit_anim === 'cp-overlay-none' ||
					(typeof animatedwidth !== 'undefined' &&
						vw <= animatedwidth)
				) {
					modal.removeClass('cp-open');
					if (modal.hasClass('cp-hide-inline-style')) {
						exit_anim = 'cp-overlay-none';
					}

					exit_anim = 'cp-overlay-none';
					if (jQuery('.cp-open').length < 1) {
						jQuery('html').removeAttr('style');
					}
				}

				cp_animate.removeClass(entry_anim);
				if (
					vw >= animatedwidth ||
					typeof animatedwidth === 'undefined'
				) {
					cp_animate.addClass(exit_anim);
				}
				if (exit_anim !== 'cp-overlay-none') {
					setTimeout(function () {
						ConvertPlus._cpExecuteVideoAPI(modal, 'pause');
						if (jQuery('.cp-open').length < 1) {
							jQuery('html').removeAttr('style');
						}
						setTimeout(function () {
							cp_animate.removeClass(exit_anim);
						}, 500);

						modal.removeClass('cp-open');
						jQuery('.cp-overlay').removeClass('cp-open');
					}, 1000);
				}
			} else if (type === 'info-bar') {
				entry_anim = info_bar.data('entry-animation');
				exit_anim = info_bar.data('exit-animation');
				cookieTime = info_bar.data('closed-cookie-time');
				popupCookieName = info_bar.data('info_bar-id');
				animate_push_page = info_bar.data('animate-push-page');
				page_push_down = info_bar.data('push-down') || null;
				parent_style_id = info_bar.data('parent-style');

				jQuery('html').removeClass('cp-ib-open');

				if (typeof parent_style_id !== 'undefined') {
					popupCookieName = parent_style_id;
				}
				temp_cookie = 'temp_' + popupCookieName;

				//  If not has 'cp-ifb-with-toggle' class for smooth toggle
				if (!info_bar.hasClass('cp-ifb-with-toggle')) {
					info_bar.removeClass(entry_anim);
					info_bar.addClass(exit_anim);
				}

				if (info_bar.hasClass('cp-pos-top')) {
					if (page_push_down) {
						const cp_top_offset_container = jQuery(
								'#cp-top-offset-container'
							).val(),
							offset_def_settings = jQuery(
								'#cp-top-offset-container'
							).data('offset_def_settings');
						if (typeof offset_def_settings !== 'undefined') {
							let mTop = offset_def_settings.margin_top,
								top = offset_def_settings.top;

							setTimeout(function () {
								if (info_bar.hasClass('cp-ifb-hide')) {
									mTop = 0;
									top = 0;
								}
								if (parseInt(animate_push_page) === 1) {
									if (cp_top_offset_container === '') {
										jQuery('body').animate({
											marginTop: mTop,
											top,
										});
									} else {
										jQuery(cp_top_offset_container).animate(
											{
												'margin-top': mTop,
												top,
											}
										);
									}
								} else if (cp_top_offset_container === '') {
									jQuery('body').css({
										'margin-top': mTop,
										top,
									});
								} else {
									jQuery(cp_top_offset_container).css({
										'margin-top': mTop,
										top,
									});
								}
							}, 2000);
						}
					}

					if (jQuery('.ib-display').length === 1) {
						const admin_bar_height =
								jQuery('#wpadminbar').outerHeight(),
							cp_push_down_support_container = jQuery(
								'#cp-push-down-support'
							).val();

						if (jQuery('#wpadminbar').length) {
							if (parseInt(animate_push_page) === 1) {
								jQuery(cp_push_down_support_container).animate(
									{ top: admin_bar_height },
									1000
								);
							} else {
								jQuery(cp_push_down_support_container).css(
									'top',
									admin_bar_height
								);
							}
						} else if (parseInt(animate_push_page) === 1) {
							jQuery(cp_push_down_support_container).animate(
								{ top: '0px' },
								1000
							);
						} else {
							jQuery(cp_push_down_support_container).css(
								'top',
								'0px'
							);
						}
					}
				}

				ConvertPlus._createCookie(temp_cookie, true, 1);
				if (cookieTime) {
					ConvertPlus._createCookie(
						popupCookieName,
						true,
						cookieTime
					);
				}

				if (
					info_bar.hasClass('cp-hide-inline-style') ||
					info_bar.hasClass('cp-close-ifb')
				) {
					exit_anim = 'cp-overlay-none';
				}

				if (info_bar.hasClass('cp-close-ifb')) {
					setTimeout(function () {
						info_bar.hide();
						info_bar.removeClass('ib-display');
						//  If not has 'cp-ifb-with-toggle' class for smooth toggle
						info_bar.removeClass(exit_anim);
						info_bar.addClass(entry_anim);

						jQuery('html').css('overflow-x', 'auto');
					}, 3000);
				}

				if (exit_anim !== 'cp-overlay-none') {
					setTimeout(function () {
						if (!info_bar.hasClass('cp-ifb-with-toggle')) {
							info_bar.hide();
							info_bar.removeClass('ib-display');
							//  If not has 'cp-ifb-with-toggle' class for smooth toggle
							info_bar.removeClass(exit_anim);
							info_bar.addClass(entry_anim);
						}
						jQuery('html').css('overflow-x', 'auto');
					}, 3000);
				} else {
					setTimeout(function () {
						if (!info_bar.hasClass('cp-ifb-with-toggle')) {
							info_bar.hide();
							info_bar.removeClass('ib-display');
							//  If not has 'cp-ifb-with-toggle' class for smooth toggle
							exit_anim = 'cp-overlay-none';
							info_bar.removeClass(exit_anim);
							info_bar.addClass(entry_anim);
						}
						jQuery('html').css('overflow-x', 'auto');
					}, 100);
				}
			} else if (type === 'slide_in') {
				container = slidein.parents('.cp-slidein-popup-container');
				template = container.data('template');
				cookieTime = slidein.data('closed-cookie-time');
				cp_animate = slidein.find('.cp-animate-container');
				entry_anim = slidein.data('overlay-animation');
				exit_anim = cp_animate.data('exit-animation');
				parent_style_id = slidein.data('parent-style');

				jQuery('html').removeClass('cp-si-open');

				if (typeof parent_style_id !== 'undefined') {
					popupCookieName = parent_style_id;
				} else {
					popupCookieName = slidein.data('slidein-id');
				}

				temp_cookie = 'temp_' + popupCookieName;
				ConvertPlus._createCookie(temp_cookie, true, 1);
				popupCookie = ConvertPlus._getCookie(popupCookieName);
				if (typeof event !== 'undefined') {
					event.preventDefault();
				}
				if (!popupCookie) {
					if (cookieTime) {
						ConvertPlus._createCookie(
							popupCookieName,
							true,
							cookieTime
						);
					}
				}

				if (
					slidein.hasClass('cp-hide-inline-style') ||
					slidein.hasClass('cp-close-slidein')
				) {
					exit_anim = 'cp-overlay-none';
				}

				if (
					slidein.hasClass('cp-close-slidein') ||
					slidein.hasClass('cp-close-after-x')
				) {
					slidein.removeClass('si-open');
				}

				animatedwidth = cp_animate.data('disable-animationwidth');
				vw = jQuery(window).width();
				if (
					exit_anim === 'cp-overlay-none' ||
					(typeof animatedwidth !== 'undefined' &&
						vw <= animatedwidth)
				) {
					if (slidein.hasClass('cp-slide-without-toggle')) {
						slidein.removeClass('si-open');
					}
					exit_anim = 'cp-overlay-none';
					if (jQuery('.cp-slidein-global.si-open').length < 1) {
						jQuery('html').removeAttr('style');
					}
				}

				if (!template) {
					cp_animate.removeClass(entry_anim);
					animatedwidth = cp_animate.data('disable-animationwidth');
					vw = jQuery(window).width();

					if (
						vw >= animatedwidth ||
						typeof animatedwidth === 'undefined'
					) {
						cp_animate.addClass(exit_anim);
					}

					if (exit_anim !== 'cp-overlay-none') {
						setTimeout(function () {
							if (slidein.hasClass('cp-slide-without-toggle')) {
								slidein.removeClass('si-open');
							}

							if (
								jQuery('.cp-slidein-global.si-open').length < 1
							) {
								jQuery('html').removeAttr('style');
							}
							setTimeout(function () {
								if (!slidein.hasClass('do_not_close')) {
									if (
										slidein.data('form-action') ===
										'disappear'
									) {
										slidein.removeClass('si-open');
									} else {
										cp_animate.removeClass(exit_anim);
									}
								}
							});
						}, 1000);
					}
				}
			}
			jQuery('html').removeClass('cp-mp-open');
			jQuery('html').removeClass('cp-oveflow-hidden');
			jQuery('html').removeClass('customize-support');
			jQuery('html').removeClass('cp-exceed-viewport');
			jQuery('html').removeClass('cp-exceed-vieport cp-window-viewport');
			jQuery('html').removeClass('cp-custom-viewport');
			jQuery('html').removeClass('cp-overflow-hidden');
		},
		/**
		 * Get Custom Class for modules
		 *
		 */
		_CpCustomClass() {
			if (typeof custom_class !== 'undefined' && custom_class !== '') {
				custom_class = custom_class.split(' ');
				jQuery.each(custom_class, function (index, classname) {
					if (typeof classname !== 'undefined' && classname !== '') {
						custom_class_arr.push(classname);
					}
				});
			}
		},
		_CpLoadImages() {
			const md = module;
			const type = module_type;
			const c_style = custom_style;
			const w_style = window_style;

			if ('modal' === type) {
				if ('undefined' !== typeof c_style) {
					md.find('.cp-modal-body').attr('style', c_style);
					md.find('.cp-modal-body').removeAttr('data-custom-style');
				}

				if ('undefined' !== typeof w_style) {
					md.find('.cp-modal-content').attr('style', w_style);
					md.find('.cp-modal-content').removeAttr(
						'data-window-style'
					);
				}
			} else if ('slide_in' === type) {
				if ('undefined' !== typeof c_style) {
					md.find('.cp-slidein-body').attr('style', c_style);
					md.find('.cp-slidein-body').removeAttr('data-custom-style');
				}
			}

			//md.find('.cp-image').
		},
		/**
		 * Check video popup
		 *
		 */
		_CpIframe() {
			jQuery.each(iframes, function (index, iframe) {
				let src = iframe.src;
				const youtube = src.search('youtube.com');
				const vimeo = src.search('vimeo.com');
				src = src.replace('&autoplay=1', '');
				src = src.replace('&mute=1', '');
				if (youtube !== -1) {
					const yt_src =
						src.indexOf('?') === -1
							? src + '?enablejsapi=1'
							: src + '&enablejsapi=1';
					if (iframe.dataset.autoplay === '1') {
						//YouTube Autoplay + Mute: Chromium browsers do not allow autoplay in most cases. However, muted autoplay is always allowed.
						iframe.src =
							yt_src +
							'&autoplay=' +
							iframe.dataset.autoplay +
							'&mute=1';
					} else {
						iframe.src = yt_src;
					}
					iframe.id = 'yt-' + class_id;
				}
				if (vimeo !== -1) {
					iframe.src = iframe.src + '?api=1';
					iframe.id = 'vim-' + class_id;
				}
			});
		},
		/**
		 * load popup
		 *
		 */
		_CploadEvent() {
			const md = module;
			const type = module_type;
			const id = style;
			let display = false;
			let invoke = false;
			let first_time_user = true;

			if (load_on_refresh === 'disabled') {
				first_time_user = ConvertPlus._hide_on_page_load(md);
			}
			if (
				typeof md !== 'undefined' &&
				ConvertPlus._canCpShow() &&
				first_time_user &&
				delay
			) {
				setTimeout(function () {
					display = ConvertPlus._isOtherPopupOpen(type);

					if (type === 'slide_in') {
						invoke = ConvertPlus._check_slide_open(md);
						if (invoke) {
							display = true;
						}
					}

					if (display) {
						ConvertPlus._displayPopup(md, type, id);
					}
				}, parseInt(delay));
			}
			if (dev_mode === 'enabled') {
				ConvertPlus._removeCookie(cookieName);
			}
		},
		/**
		 * Exit intent trigger
		 *
		 * @param {Object} e
		 */
		_CpmouseleaveEvent(e) {
			const md = module;
			const type = module_type;
			const id = style;
			let invoke = false;
			let display = false;

			if (
				exit === 'enabled' &&
				typeof md !== 'undefined' &&
				ConvertPlus._canCpShow()
			) {
				if (e.clientY <= 0) {
					display = ConvertPlus._isOtherPopupOpen(type);

					if (type === 'slide_in') {
						invoke = ConvertPlus._check_slide_open(md);
						if (invoke) {
							display = true;
						}
					}
					if (display) {
						ConvertPlus._displayPopup(md, type, id);
					}
				}
			}
			if (dev_mode === 'enabled') {
				ConvertPlus._removeCookie(cookieName);
			}
		},
		/**
		 * Function to check multiple slidein ar open or not
		 *
		 * @param {Object} md
		 */
		_check_slide_open(md) {
			let display = false;
			if (
				md.find('.cp-slide-in-float-on').length !== 0 &&
				jQuery('.si-open').find('.cp-slide-in-float-on').length <= 1
			) {
				floating_status = 1;
			}
			if (jQuery('.si-open').length <= floating_status) {
				display = true;
			}
			return display;
		},
		/**
		 * On scroll event
		 *
		 */
		_CpscrollEvent() {
			let scrolled = jQuery(window).scrollTop();
			const scrollPercent =
				(100 * jQuery(window).scrollTop()) /
				(jQuery(document).height() - jQuery(window).height());
			const md = module;
			let display = false;
			let invoke = false;
			let load_on_scroll = 'disable';
			let scrollTill_post = '';
			let is_on_screen = '';
			let load_on_scroll_class = '';
			const id = style;
			const type = module_type;

			if (scrollTill) {
				load_on_scroll = 'enable';
				scrolled = scrollPercent.toFixed(0);
			}

			if (typeof scroll_class !== 'undefined') {
				load_on_scroll_class = 'enable';
			}

			if (ConvertPlus._canCpShow()) {
				display = ConvertPlus._isOtherPopupOpen(type);
				if (scrolled >= scrollTill && load_on_scroll === 'enable') {
					invoke = true;
				} else if (afterpost) {
					if (scrollTilllength > 0) {
						scrollTill_post =
							jQuery('.cp-load-after-post').offset().top - 30;
						scrollTill_post =
							scrollTill_post -
							(jQuery(window).height() * scrollValue) / 100;

						if (scrolled >= scrollTill_post) {
							invoke = true;
						}
					}
				} else if (load_on_scroll_class === 'enable') {
					scroll_class = scroll_class.split(' ');
					$.each(scroll_class, function (index, classname) {
						const position = jQuery(classname).position();
						if (
							typeof position !== 'undefined' &&
							position !== ' '
						) {
							is_on_screen = ConvertPlus._cp_modal_isOnScreen(
								jQuery(classname)
							);
							if (display && is_on_screen) {
								ConvertPlus._displayPopup(md, type, id);
							}
						}
					});
				}

				if (display && invoke) {
					ConvertPlus._displayPopup(md, type, id);
				}
			}

			if (dev_mode === 'enabled') {
				ConvertPlus._removeCookie(cookieName);
			}
		},
		/**
		 * Idle event for modules.
		 *
		 */
		_CpidleEvent() {
			const md = module;
			const type = module_type;
			const id = style;
			let display = false;
			if (ConvertPlus._canCpShow()) {
				display = ConvertPlus._isOtherPopupOpen(type);
				if (display && typeof inactive_time !== 'undefined') {
					ConvertPlus._displayPopup(md, type, id);
				}
			}
		},
		/**
		 * Display popup.
		 *
		 * @param {Object} md
		 * @param {string} type
		 * @param {string} id
		 */
		_displayPopup(md, type, id) {
			styleArray = Array();

			const is_ipression_counted = ConvertPlus._getCookie(
				'cp-impression-added-for' + id
			);
			if (type === 'modal') {
				$(window).trigger('modalOpen', [md]);
				$(document).trigger('resize');
				const frame = md.find('.cp-youtube-frame');
				const frame_length = frame.length;
				let lazy_video = false;
				if (frame_length >= 1) {
					isAutoPlay =
						md.find('.cp-youtube-frame').attr('data-autoplay') ||
						'0';
				} else {
					lazy_video = true;
					isAutoPlay =
						md.find('.cp-youtube-continer').attr('data-autoplay') ||
						'0';
				}

				if (parseInt(isAutoPlay) === 1) {
					if (lazy_video) {
						md.find('.cp-youtube-continer').trigger('click', [
							isAutoPlay,
						]);
					} else {
						ConvertPlus._cpExecuteVideoAPI(md, 'play');
					}
				}

				md.addClass('cp-open cp-visited-popup');
				if (
					!is_ipression_counted &&
					!md.hasClass('cp_impression_counted') &&
					!md.hasClass('cp-disabled-impression')
				) {
					if (styleArray.indexOf(id) === -1) {
						styleArray.push(id);
					}
					md.addClass('cp_impression_counted');
					ConvertPlus._createCookie(
						'cp-impression-added-for' + id,
						true,
						1
					);
					if (styleArray.length !== 0) {
						ConvertPlus.update_impressions(styleArray);
					}
				}
				//  Show YouTube CTA form
				ConvertPlus._youtube_show_cta(md);
			} else if (type === 'info-bar') {
				if (
					!is_ipression_counted &&
					!md.hasClass('cp_impression_counted') &&
					!md.hasClass('cp-disabled-impression')
				) {
					if (styleArray.indexOf(id) === -1) {
						styleArray.push(id);
					}
					if (
						styleArray.length !== 0 &&
						typeof toggle_visible === 'undefined'
					) {
						ConvertPlus.update_impressions(styleArray);

						ConvertPlus._createCookie(
							'cp-impression-added-for' + id,
							true,
							1
						);

						jQuery('[data-info_bar-style=' + style + ']').each(
							function () {
								jQuery(this).addClass('cp_impression_counted');
							}
						);
					}
				}
				if (md.hasClass('cp-pos-top')) {
					if (jQuery('body').hasClass('admin-bar')) {
						ab_height = jQuery('#wpadminbar').outerHeight();
						md.css('top', ab_height + 'px');
					}
				} else {
					cp_height = md.find('.cp-info-bar-body').outerHeight();
					md.css('min-height', cp_height + 'px');
				}

				md.addClass('ib-display');
				jQuery(document).trigger('resize');
				jQuery(document).trigger('infobarOpen', [md]);
				setTimeout(function () {
					anim = md.find('.cp-submit').data('animation');
					md.find('.cp-submit').addClass(anim);
				}, 2000);
			} else if (type === 'slide_in') {
				ConvertPlus._adjustToggleButton(slidein_container);
				jQuery(window).trigger('slideinOpen', [md]);

				md.addClass('si-open');

				if (
					!is_ipression_counted &&
					!md.hasClass('cp_impression_counted') &&
					!md.hasClass('cp-disabled-impression')
				) {
					if (styleArray.indexOf(id) === -1) {
						styleArray.push(id);
					}
					if (
						styleArray.length !== 0 &&
						typeof toggle_visible === 'undefined'
					) {
						ConvertPlus.update_impressions(styleArray);

						ConvertPlus._createCookie(
							'cp-impression-added-for' + id,
							true,
							1
						);

						jQuery('[data-slidein-style=' + style + ']').each(
							function () {
								jQuery(this).addClass('cp_impression_counted');
							}
						);
					}
				}
			}
		},
		/**
		 * Update impression for modules.
		 *
		 * @param {Object} styles
		 */
		update_impressions(styles) {
			if (ajax_run === true) {
				nounce = jQuery('.cp-impress-nonce').val();
				data = {
					action: 'smile_update_impressions',
					impression: true,
					styles,
					option: 'smile_modal_styles',
					security: nounce,
				};
				jQuery.ajax({
					url: smile_ajax.url,
					data,
					type: 'POST',
					dataType: 'HTML',
					security: jQuery('.cp-impress-nonce').val(),
					beforeSend() {
						ajax_run = false;
					},
				});
			} else {
				setTimeout(function () {
					nounce = jQuery('.cp-impress-nonce').val();
					data = {
						action: 'smile_update_impressions',
						impression: true,
						styles,
						option: 'smile_modal_styles',
						security: nounce,
					};
					jQuery.ajax({
						url: smile_ajax.url,
						data,
						type: 'POST',
						dataType: 'HTML',
						security: jQuery('.cp-impress-nonce').val(),
						beforeSend() {
							// do your stuff
							ajax_run = false;
						},
					});
				}, 2000);
			}
		},
		/**
		 * Check if another popup is ope or not
		 *
		 * @param {string} type
		 */
		_isOtherPopupOpen(type) {
			let condition = '';
			let float_count, open_count;
			if (type === 'modal') {
				condition =
					$('.cp-open').length <= 0 &&
					!modal.hasClass('cp-visited-popup');
			} else if (type === 'info-bar') {
				condition = jQuery('.ib-display').length <= 0;
			} else if (type === 'slide_in') {
				float_count = jQuery('.si-open').find(
					'.cp-slide-in-float-on'
				).length;
				open_count = 1;
				if (float_count === 0) {
					open_count = 0;
				}
				condition =
					jQuery('.si-open').length <= open_count &&
					jQuery('.si-open').find('.cp-slide-in-float-on').length <=
						1 &&
					!slidein.hasClass('cp_impression_counted');
			}
			return condition;
		},

		/**
		 * Check visibility of module
		 *
		 */
		_canCpShow() {
			cookie = ConvertPlus._getCookie(cookieName);
			tmp_cookie = ConvertPlus._getCookie(temp_cookie);

			if (dev_mode === 'enabled') {
				if (tmp_cookie) {
					cookie = true;
				} else {
					cookie = ConvertPlus._getCookie(cookieName);
				}
			} else {
				cookie = ConvertPlus._getCookie(cookieName);
			}

			if (module_type === 'slide_in') {
				if (dev_mode === 'enabled') {
					ConvertPlus._removeCookie(cookieName + '-conversion');
				} else {
					if (
						cookie &&
						slidein.hasClass('cp-always-minimize-widget')
					) {
						slidein.addClass('cp-minimize-widget');
						cookie = false;
					}
					const conversion_cookies = ConvertPlus._getCookie(
						cookieName + '-conversion'
					);
					if (
						conversion_cookies &&
						slidein.hasClass('cp-always-minimize-widget')
					) {
						cookie = true;
					}
				}
			}

			if (cookie === null) {
				cookie = false;
			}

			if (typeof referrer !== 'undefined' && referrer !== '') {
				referred = ConvertPlus._isReferrer(
					referrer,
					doc_ref,
					ref_check
				);
			}

			is_open = ConvertPlus._isOtherPopupOpen(module_type);
			return !cookie && scheduled && referred && is_open;
		},
		/**
		 * Check fullscreen popup
		 *
		 * @param {string} type
		 */
		_isWindowSize(type) {
			return type.hasClass('cp-window-size');
		},
		/**
		 * Remove/get/create cookies
		 *
		 * @param {string} name
		 */
		_removeCookie(name) {
			ConvertPlus._createCookie(name, '', -1);
		},
		_createCookie(name, value, days) {
			let expires = '';
			// If we have a days value, set it in the expiry of the cookie.
			if (days) {
				const cookieDate = new Date();
				cookieDate.setTime(
					cookieDate.getTime() + days * 24 * 60 * 60 * 1000
				);
				expires = '; expires=' + cookieDate.toGMTString();
			}
			// Write the cookie.
			document.cookie = name + '=' + value + expires + '; path=/';
		},
		_getCookie(name) {
			const nameEQ = name + '=';
			const ca = document.cookie.split(';');
			for (let i = 0; i < ca.length; i++) {
				let c = ca[i];
				while (c.charAt(0) === ' ') {
					c = c.substring(1, c.length);
				}
				if (c.indexOf(nameEQ) === 0) {
					return c.substring(nameEQ.length, c.length);
				}
			}
			return null;
		},
		_setPageCookie(cpCookieName, cookieValue, nDays) {
			const today = new Date(),
				expire = new Date();
			if (nDays === null || nDays === 0) {
				nDays = 1;
			}
			expire.setTime(today.getTime() + 3600000 * 24 * nDays);

			document.cookie =
				cpCookieName +
				'=' +
				escape(cookieValue) +
				';expires=' +
				expire.toGMTString();
		},
		_getPageCookie(cpCookieName) {
			const theCookie = ' ' + document.cookie;
			let ind = theCookie.indexOf(' ' + cpCookieName + '=');
			if (ind === -1) {
				ind = theCookie.indexOf(';' + cpCookieName + '=');
			}
			if (ind === -1 || cpCookieName === '') {
				return '';
			}
			let ind1 = theCookie.indexOf(';', ind + 1);
			if (ind1 === -1) {
				ind1 = theCookie.length;
			}
			return unescape(
				theCookie.substring(ind + cpCookieName.length + 2, ind1)
			);
		},
		/**
		 * Scheduled or not?
		 *
		 * @param {Object} is_scheduled_modal
		 */
		_isScheduled(is_scheduled_modal) {
			const sys_timestring = is_scheduled_modal.data('timezonename');
			const cp_module_tzoffset = is_scheduled_modal.data('tz-offset');
			let sys_ltime;
			const new_sys_date = new Date();
			const utc_time =
				new_sys_date.getTime() +
				new_sys_date.getTimezoneOffset() * 60000; // turn date to utc_time
			const cp_new_date = new Date(
				utc_time + 3600000 * cp_module_tzoffset
			); // set new Date object
			const is_scheduled = is_scheduled_modal.data('scheduled');

			if (typeof is_scheduled !== 'undefined' && is_scheduled) {
				let start = is_scheduled_modal.data('start');
				let end = is_scheduled_modal.data('end');
				start = Date.parse(start);
				end = Date.parse(end);

				if (sys_timestring === 'system') {
					sys_ltime = Date.parse(date);
				} else {
					sys_ltime = Date.parse(cp_new_date);
				}

				if (sys_ltime >= start && sys_ltime <= end) {
					return true;
				}
				return false;
			}
			return true;
		},
		/**
		 * Referere detection
		 *
		 * @param {string} str_referrer
		 * @param {string} doc_refer
		 * @param {string} ref_check_opt
		 */
		_isReferrer(str_referrer, doc_refer, ref_check_opt) {
			let display = false;

			if (typeof doc_refer !== 'undefined') {
				const doc_refs = ConvertPlus._stripTrailingSlash(
					doc_refer.replace(/.*?:\/\//g, '')
				);
				const referrers = str_referrer.split(',');

				jQuery.each(referrers, function (i, ref_url) {
					let url = ConvertPlus._stripTrailingSlash(ref_url);
					let doc_referrer = doc_refs.replace('www.', '');
					let url_arr = '';
					let _domain = '';
					url = ConvertPlus._stripTrailingSlash(
						url.replace(/.*?:\/\//g, '')
					);
					url = url.replace('www.', '');
					url_arr = url.split('*');

					if (doc_referrer.indexOf('reddit.com') !== -1) {
						doc_referrer = 'reddit.com';
					} else if (doc_referrer.indexOf('t.co') !== -1) {
						doc_referrer = 'twitter.com';
					}

					if (doc_referrer.indexOf('plus.google.co') !== -1) {
						doc_referrer = 'plus.google.com';
					} else if (doc_referrer.indexOf('google.co') !== -1) {
						doc_referrer = 'google.com';
					}

					_domain = url_arr[0];
					_domain = ConvertPlus._stripTrailingSlash(_domain);

					if (ref_check_opt === 'display') {
						if (url.indexOf('*') !== -1) {
							if (
								_domain === doc_referrer ||
								doc_referrer.indexOf(_domain) !== -1
							) {
								display = true;
								return false;
							}
							display = false;
							return false;
						} else if (
							url === doc_referrer ||
							doc_referrer.indexOf(_domain) !== -1
						) {
							display = true;
							return false;
						}
						display = false;
					} else if (ref_check_opt === 'hide') {
						if (url.indexOf('*') !== -1) {
							if (
								_domain === doc_referrer ||
								doc_referrer.indexOf(_domain) !== -1
							) {
								display = false;
								return false;
							}
							display = true;
							return false;
						} else if (
							url === doc_referrer ||
							doc_referrer.indexOf(_domain) !== -1
						) {
							display = false;
							return false;
						}
						display = true;
					}
				});
			}
			return display;
		},
		/**
		 * [_stripTrailingSlash description]
		 *
		 * @param {string} url [description]
		 */
		_stripTrailingSlash(url) {
			if (url.substr(-1) === '/') {
				return url.substr(0, url.length - 1);
			}
			return url;
		},

		/**
		 * Youtube API execution
		 *
		 * @param {Object} obj    [description]
		 * @param {string} status [description]
		 */
		_cpExecuteVideoAPI(obj, status) {
			const cp_iframes = obj.find('iframe');
			jQuery.each(cp_iframes, function (index, frame) {
				let src = frame.src;
				if (parseInt(isAutoPlay) === '1') {
					src = frame.getAttribute('data_y_src');
					if (src === '' || src === null) {
						src = frame.src;
					}
				}

				// Youtube API
				const youtube = src.search('youtube.com');

				if (Youtube_on_tab === true) {
					status = 'play';
				}

				if (youtube >= 1) {
					const youtube_frame = frame.contentWindow;
					if (status === 'play') {
						youtube_frame.postMessage(
							'{"event":"command","func":"playVideo","args":""}',
							'*'
						);
						if (iframes.hasClass('cp-youtube-frame')) {
							iframes.removeAttr('data_y_src');
							iframes.attr('allow', 'autoplay');
							iframes.attr(
								'src',
								src.replace('autoplay=0', 'autoplay=1')
							);
						}
					} else {
						if (parseInt(isAutoPlay) === 1) {
							iframes.attr('data_y_src', src);
							iframes.removeAttr('src');
						}
						iframes.removeAttr('allow');
						iframes.attr(
							'data_y_src',
							src.replace('autoplay=0', 'autoplay=0')
						);
						iframes.removeAttr('src');
						youtube_frame.postMessage(
							'{"event":"command","func":"pauseVideo","args":""}',
							'*'
						);
						youtube_frame.postMessage(
							'{"event":"command","func":"stopVideo","args":""}',
							'*'
						);
					}
				}
				// Vimeo API
				const vimeo = src.search('vimeo.com');
				if (vimeo >= 1) {
					const vimeo_frame = frame.contentWindow;
					if (status === 'play') {
						vimeo_frame.postMessage('{"method":"play"}', '*');
					} else {
						vimeo_frame.postMessage('{"method":"pause"}', '*');
					}
				}
			});
		},
		/**
		 * [_youtube_show_cta description]
		 *
		 * @param {Object} cp_modal_form_container
		 */
		_youtube_show_cta(cp_modal_form_container) {
			const cp_form = cp_modal_form_container.find('.cp-form-container');
			if (
				cp_modal_form_container
					.find('.cp-modal-body')
					.hasClass('cp-youtube') &&
				!cp_form.hasClass('cp-youtube-cta-none')
			) {
				let cta_delay = cp_form.attr('data-cta-delay') || '';

				if (cta_delay !== '' && cta_delay !== null) {
					cta_delay = parseInt(cta_delay * 1000);
					cp_form.slideUp('500');
					setTimeout(function () {
						//  show CTA after complete delay time
						cp_form.slideDown('500');
					}, cta_delay);
				}
			}
		},
		/**
		 * [_check_responsive_font_sizes description]
		 *
		 */
		_check_responsive_font_sizes() {
			//  Apply font sizes
			jQuery('.cp_responsive[data-font-size-init]').each(function (
				index,
				el
			) {
				const p = jQuery(el),
					cp_data_html = jQuery(this).html();

				if (
					cp_data_html.toLowerCase().indexOf('cp_font') >= 0 &&
					cp_data_html.match('^<span') &&
					cp_data_html.match('</span>$')
				) {
					p.addClass('cp-no-responsive');
				} else {
					p.removeClass('cp-no-responsive');
				}
			});
		},
		/**
		 * Name:_count_inline_impressions Count inline impression for modules.
		 *
		 * @param {Object} cp_modal_data
		 */
		_count_inline_impressions(cp_modal_data) {
			const type = cp_modal_data.data('module-type');
			let main_class = '';

			if (type === 'modal') {
				main_class = '.cp-modal-inline-end';
			} else if (type === 'info-bar') {
				main_class = '.cp-info_bar-inline-end';
			} else if (type === 'slide_in') {
				main_class = '.cp-slide_in-inline-end';
			}

			jQuery(main_class).each(function () {
				const elem = jQuery(this);
				const is_visible = ConvertPlus._isScrolledIntoStyleView(elem);
				const style_id = elem.data('style');
				const is_ipression_counted = ConvertPlus._getCookie(
					'cp-impression-added-for' + style_id
				);
				let condition;
				let check_class;

				if (type === 'modal') {
					condition =
						!jQuery(
							'.cp-overlay[data-modal-style=' + style_id + ']'
						).hasClass('cp_impression_counted') &&
						!jQuery(
							'.cp-overlay[data-modal-style=' + style_id + ']'
						).hasClass('cp-disabled-impression');
					check_class =
						'.cp-overlay[data-modal-style=' + style_id + ']';
				} else if (type === 'info-bar') {
					condition =
						!jQuery(
							'[data-info_bar-style=' + style_id + ']'
						).hasClass('cp_impression_counted') &&
						!jQuery(
							'[data-info_bar-style=' + style_id + ']'
						).hasClass('cp-disabled-impression');
					check_class = '[data-info_bar-style=' + style_id + ']';
				} else if (type === 'slide_in') {
					condition =
						!jQuery(
							'[data-slidein-style=' + style_id + ']'
						).hasClass('cp_impression_counted') &&
						!jQuery(
							'[data-slidein-style=' + style_id + ']'
						).hasClass('cp-disabled-impression');
					check_class = '[data-slidein-style=' + style_id + ']';
				}

				if (is_visible && !is_ipression_counted) {
					styleArray = Array();
					if (condition) {
						styleArray.push(style_id);
						ConvertPlus.update_impressions(styleArray);
						ConvertPlus._createCookie(
							'cp-impression-added-for' + style_id,
							true,
							1
						);
					}
					jQuery(check_class).each(function () {
						elem.addClass('cp_impression_counted');
					});
				}
			});
		},

		/**
		 * _close_button_tootip style for Close tooltip.
		 *
		 */
		_close_button_tootip() {
			if (module_type === 'modal' && module_type !== 'undefined') {
				jQuery('.cp-overlay').each(function () {
					const $this = jQuery(this);
					const classname = $this
						.find('.cp-tooltip-icon')
						.data('classes');
					const tcolor = $this.find('.cp-tooltip-icon').data('color');
					const tbgcolor = $this
						.find('.cp-tooltip-icon')
						.data('bgcolor');
					const fontfamily = $this
						.find('.cp-tooltip-icon')
						.data('font-family');
					let new_tooltip_position = '';

					if (
						$this
							.find('.cp-overlay-close')
							.hasClass('cp-adjacent-left')
					) {
						new_tooltip_position = 'right';
					} else if (
						$this
							.find('.cp-overlay-close')
							.hasClass('cp-adjacent-right')
					) {
						new_tooltip_position = 'left';
					}

					$this.find('.cp-tooltip-icon').removeAttr('data-position');
					$this
						.find('.cp-tooltip-icon')
						.attr('data-position', new_tooltip_position);

					const position = new_tooltip_position;

					jQuery('body').addClass('customize-support');

					if (typeof classname !== 'undefined') {
						jQuery('.' + classname).remove();
					}

					jQuery('head').append(
						'<style class="cp-tooltip-css ' +
							classname +
							'">.customize-support .tip.' +
							classname +
							'{color: ' +
							tcolor +
							';background-color:' +
							tbgcolor +
							';border-color:' +
							tbgcolor +
							';font-family:' +
							fontfamily +
							'; }</style>'
					);

					if (position === 'left') {
						jQuery('head').append(
							'<style class="cp-tooltip-css ' +
								classname +
								'">.customize-support .tip.' +
								classname +
								'[class*="arrow"]:before , .' +
								classname +
								'[class*="arrow"]:before {border-left-color: ' +
								tbgcolor +
								' ;border-top-color:transparant}</style>'
						);
					} else if (position === 'right') {
						jQuery('head').append(
							'<style class="cp-tooltip-css ' +
								classname +
								'">.customize-support .tip.' +
								classname +
								'[class*="arrow"]:before , .' +
								classname +
								'[class*="arrow"]:before{border-right-color: ' +
								tbgcolor +
								';border-left-color:transparent }</style>'
						);
					} else {
						jQuery('head').append(
							'<style class="cp-tooltip-css ' +
								classname +
								'">.customize-support .tip.' +
								classname +
								'[class*="arrow"]:before , .' +
								classname +
								'[class*="arrow"]:before{border-top-color: ' +
								tbgcolor +
								';border-left-color:transparent }</style>'
						);
					}
				});
			} else if (
				module_type === 'slide_in' &&
				module_type !== 'undefined'
			) {
				const classname = module.find('.has-tip').data('classes'),
					tcolor = module.find('.has-tip').data('color'),
					tbgcolor = module.find('.has-tip').data('bgcolor'),
					position = module.find('.has-tip').data('position');

				jQuery('body').addClass('customize-support');

				jQuery('head').append(
					'<style class="cp-tooltip-css">.customize-support .tip.' +
						classname +
						'{color: ' +
						tcolor +
						';background-color:' +
						tbgcolor +
						';font-size:13px;border-color:' +
						tbgcolor +
						' }</style>'
				);
				if (position === 'left') {
					jQuery('head').append(
						'<style class="cp-tooltip-css">.customize-support .tip.' +
							classname +
							'[class*="arrow"]:before , .' +
							classname +
							'[class*="arrow"]:before {border-left-color: ' +
							tbgcolor +
							' ;border-top-color:transparent}</style>'
					);
				} else if (position === 'right') {
					jQuery('head').append(
						'<style class="cp-tooltip-css">.customize-support .tip.' +
							classname +
							'[class*="arrow"]:before , .' +
							classname +
							'[class*="arrow"]:before{border-right-color: ' +
							tbgcolor +
							';border-left-color:transparent }</style>'
					);
				} else {
					jQuery('head').append(
						'<style class="cp-tooltip-css">.customize-support .tip.' +
							classname +
							'[class*="arrow"]:before , .' +
							classname +
							'[class*="arrow"]:before{border-top-color: ' +
							tbgcolor +
							';border-left-color:transparent }</style>'
					);
				}
			}
		},
		/**
		 * check if element is visible in view port
		 *
		 * @param {Object} elem [description]
		 */
		_isScrolledIntoStyleView(elem) {
			const $elem = elem,
				$window = $(window),
				docViewTop = $window.scrollTop(),
				docViewBottom = docViewTop + $window.height(),
				elemTop = $elem.offset().top,
				elemBottom = elemTop + $elem.height();

			return elemBottom <= docViewBottom && elemTop >= docViewTop;
		},
		/**
		 * check if element is visible in screen
		 *
		 * @param {Object} obj
		 */
		_cp_modal_isOnScreen(obj) {
			const win = $(window);
			const viewport = {
				top: win.scrollTop(),
				left: win.scrollLeft(),
			};
			viewport.right = viewport.left + win.width();
			viewport.bottom = viewport.top + win.height();

			const bounds = obj.offset();
			bounds.right = bounds.left + obj.outerWidth();
			bounds.bottom = bounds.top + obj.outerHeight();
			return !(
				viewport.right < bounds.left ||
				viewport.left > bounds.right ||
				viewport.bottom < bounds.top ||
				viewport.top > bounds.bottom
			);
		},
		/**
		 * check info bar position.
		 *
		 * @param {Object} cp_info_bar
		 */
		_infoBarPos(cp_info_bar) {
			if (cp_info_bar.hasClass('cp-pos-top')) {
				cp_info_bar.css('top', '0');
			} else if (cp_info_bar.hasClass('ib-fixed')) {
				cp_info_bar.css('top', 'auto');
			} else {
				const toggle = cp_info_bar.data('toggle');
				let body_ht = jQuery('body').parent('html').height();
				const toggle_ht = cp_info_bar
					.find('.cp-ifb-toggle-btn')
					.outerHeight();
				const cp_ib_height = cp_info_bar
					.find('.cp-info-bar-body')
					.outerHeight();

				if (parseInt(toggle) === 1) {
					body_ht = body_ht - cp_ib_height + toggle_ht;
				}
				if (!cp_info_bar.hasClass('cp-info-bar-inline')) {
					cp_info_bar.css('top', body_ht + 'px');
				}
				cp_info_bar.css('min-height', cp_ib_height + 'px');
			}
			if (jQuery('body').hasClass('admin-bar')) {
				if (cp_info_bar.hasClass('cp-pos-top')) {
					const cp_info_bar_height =
						jQuery('#wpadminbar').outerHeight();
					if (!cp_info_bar.hasClass('cp-info-bar-inline')) {
						cp_info_bar.css('top', cp_info_bar_height + 'px');
					}
				}
			}
		},
		/**
		 * Style for fullscreen popup
		 *
		 */
		_windowSize() {
			const cp_content_container = this.find('.cp-content-container'),
				cp_info_bar = this.find('.cp-info-bar'),
				cp_info_bar_content = this.find('.cp-info-bar-content'),
				cp_info_bar_body = this.find('.cp-info-bar-body');
			cp_info_bar.removeAttr('style');
			cp_info_bar_content.removeAttr('style');
			cp_content_container.removeAttr('style');
			cp_info_bar_body.removeAttr('style');
			const ww = jQuery(window).width() + 30;
			const wh = jQuery(window).height();
			jQuery(this).find('iframe').css('width', ww);

			cp_content_container.css({
				'max-width': ww + 'px',
				width: '100%',
				height: wh + 'px',
				padding: '0',
				margin: '0 auto',
			});
			cp_info_bar_content.css({ 'max-width': ww + 'px', width: '100%' });
			cp_info_bar.css({
				'max-width': ww + 'px',
				width: '100%',
				left: '0',
				right: '0',
			});
			cp_info_bar_body.css({
				'max-width': ww + 'px',
				width: '100%',
				height: wh + 'px',
			});
		},
		/**
		 * Set infobar height
		 *
		 * @param {string} t [description]
		 */
		_cp_set_ifb_ht(t) {
			const h = parseInt(jQuery(t).outerHeight());
			const vw = jQuery(window).outerWidth();
			const ua = window.navigator.userAgent;
			let msie = 0;
			if (typeof ua !== 'undefined') {
				msie = ua.indexOf('MSIE ');
			}
			//  is IE browser?
			if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) {
				if (vw > 768) {
					jQuery(t)
						.find('.cp-info-bar-body')
						.css({ height: h + 'px' });
				} else {
					jQuery(t).find('.cp-info-bar-body').css({ height: 'auto' });
				}
			}
		},
		/**
		 * Color for inline list tag from modules.
		 *
		 * @param {string} t [description]
		 */
		_cp_ifb_color_for_list_tag(t) {
			const moadal_style = jQuery(t).data('class');
			jQuery(t)
				.find('li')
				.each(function () {
					if (
						jQuery(this).parents('.cp_social_networks').length === 0
					) {
						const $this = jQuery(this);
						const parent_li = $this
							.parents('div')
							.attr('class')
							.split(' ')[0];
						const cnt = $this.index() + 1;
						const font_size = $this
							.find('.cp_font')
							.css('font-size');
						let color = $this.find('span').css('color');
						let list_type = $this.parent();
						list_type = list_type[0].nodeName.toLowerCase();
						let style_type = '';

						//apply style type to list
						if (list_type === 'ul') {
							style_type = $this
								.closest('ul')
								.css('list-style-type');
							if (style_type === 'none') {
								$this
									.closest('ul')
									.css('list-style-type', 'disc');
							}
						} else {
							style_type = $this
								.closest('ol')
								.css('list-style-type');
							if (style_type === 'none') {
								$this
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
		},
		/**
		 * Push page down for info bar
		 *
		 * @param {Object} md [description]
		 */
		_apply_push_page_down(md) {
			setTimeout(function () {
				const cptoggle_visible = md.data('toggle-visible') || null,
					toggle = false;
				ConvertPlus._push_page_down(md, toggle, cptoggle_visible);
			}, 300);
		},
		_push_page_down(cp_push_info_bar, toggle, cp_toggle_visible) {
			const page_down = cp_push_info_bar.data('push-down') || null,
				animate_push_page = cp_push_info_bar.data('animate-push-page'),
				cp_top_offset_container = jQuery(
					'#cp-top-offset-container'
				).val();

			if (page_down && !cp_toggle_visible) {
				if (cp_push_info_bar.hasClass('cp-pos-top')) {
					let cp_push_page_css = '';
					const push_margin = ConvertPlus._cal_top_margin_push_down(
						cp_push_info_bar,
						animate_push_page,
						toggle
					);
					const apply_css = isNaN(parseFloat(push_margin));
					if (!apply_css) {
						if (parseInt(animate_push_page) === 1) {
							if (cp_top_offset_container === '') {
								jQuery('body')
									.removeClass('cp_push_no_scroll')
									.addClass('cp_push_scroll_animate');
								cp_push_page_css =
									'body.cp_push_scroll_animate{margin-top:' +
									push_margin +
									'px!important}';
							} else {
								cp_push_page_css =
									cp_top_offset_container +
									'{margin-top:' +
									push_margin +
									'px}';
							}
						} else if (cp_top_offset_container === '') {
							jQuery('body')
								.removeClass('cp_push_scroll_animate')
								.addClass('cp_push_no_scroll');
							cp_push_page_css =
								'body.cp_push_no_scroll{margin-top:' +
								push_margin +
								'px!important}';
						} else {
							cp_push_page_css =
								cp_top_offset_container +
								'{margin-top:' +
								push_margin +
								'px}';
						}
						$('.cp-push-page-css').remove();
						$('head').append(
							'<style class="cp-push-page-css">' +
								cp_push_page_css +
								'</style>'
						);
					}
				}
			}
		},
		_cal_top_margin_push_down(cpinfo_bar, animate_push_page, toggle) {
			const cp_top_offset_container = jQuery(
				'#cp-top-offset-container'
			).val();
			let offset_def_settings, site_offset;
			let wpadminbar = jQuery('#wpadminbar').outerHeight(); // Calculate WP admin Bar Height
			const ib_height = cpinfo_bar.outerHeight(); // Calculate Info Bar Height

			if (cp_top_offset_container === '' && rs_flag <= 1) {
				site_offset = jQuery('body').offset().top;
				offset_def_settings = {
					margin_top: jQuery('body').css('margin-top'),
					top: jQuery('body').css('top'),
				};
			} else if (jQuery(cp_top_offset_container).length > 0) {
				site_offset = jQuery(cp_top_offset_container).offset().top;
				offset_def_settings = {
					margin_top: jQuery(cp_top_offset_container).css(
						'margin-top'
					),
					top: jQuery(cp_top_offset_container).css('top'),
				};
			}

			if (typeof offset_def_settings !== 'undefined') {
				const seetings_string = JSON.stringify(offset_def_settings);
				jQuery('#cp-top-offset-container').attr(
					'data-offset_def_settings',
					seetings_string
				);
			}

			if (typeof site_offset === 'undefined') {
				site_offset = 0;
			}

			if (typeof wpadminbar === 'undefined') {
				wpadminbar = 0;
			}

			let push_down_top = ib_height + site_offset - wpadminbar;
			const push_down_top_support = ib_height + site_offset;
			let cp_push_down_support_htop = push_down_top_support - 0;

			if (toggle) {
				cp_push_down_support_htop = wpadminbar + ib_height;
				push_down_top = ib_height;
			}
			if (parseInt(animate_push_page) === 1) {
				jQuery('#cp-push-down-support')
					.stop()
					.animate({ top: cp_push_down_support_htop + 'px' }, 1200);
			} else {
				jQuery('#cp-push-down-support').css(
					'top',
					cp_push_down_support_htop + 'px'
				);
			}
			return push_down_top;
		},
		/**
		 * Check toggele functionality
		 *
		 */
		_cp_ifb_toggle() {
			jQuery('.cp-info-bar').each(function (index, el) {
				const cp_info_bar_toggle = jQuery(el);
				cp_info_bar_toggle
					.find('.cp-ifb-toggle-btn')
					.on('click', function () {
						const cp_ifb_toggle_btn = jQuery(this);
						const cp_info_bar =
							cp_ifb_toggle_btn.closest('.cp-info-bar');
						let btn_animation = 'smile-slideInDown';
						const exit_animation =
							cp_info_bar.data('exit-animation');
						const entry_animation =
							cp_info_bar.data('entry-animation');
						const cp_info_bar_body =
							cp_info_bar.find('.cp-info-bar-body');
						const toggle_visibility =
							cp_info_bar.data('toggle-visible');
						const is_imp_added =
							cp_info_bar.data('impression-added');
						const style_id = cp_info_bar.data('info_bar-id');

						if (toggle_visibility) {
							if (
								typeof is_imp_added === 'undefined' &&
								!cp_info_bar.hasClass('cp-disabled-impression')
							) {
								styleArray = [style_id];
								ConvertPlus.update_impressions(styleArray);
								cp_info_bar.data('impression-added', 'true');
							}
						}

						let toggle = false;
						const push_toggle_visible = null;

						ConvertPlus._push_page_down(
							cp_info_bar,
							toggle,
							push_toggle_visible
						);

						cp_info_bar.removeClass(entry_animation);
						cp_info_bar.removeClass(exit_animation);

						if (cp_info_bar.hasClass('cp-pos-bottom')) {
							btn_animation = 'smile-slideInUp';
						}

						const cp_info_bar_class = cp_info_bar.attr('class');

						cp_ifb_toggle_btn.removeClass(
							'cp-ifb-show smile-animated ' + btn_animation + ''
						);
						cp_info_bar.attr('class', cp_info_bar_class);
						cp_info_bar.attr(
							'class',
							cp_info_bar_class +
								' smile-animated ' +
								entry_animation
						);
						cp_info_bar.removeClass('cp-ifb-hide');

						cp_ifb_toggle_btn.addClass('cp-ifb-hide');
						cp_info_bar_body.addClass('cp-flex');
						cp_info_bar.find('.ib-close').css({
							visibility: 'visible',
						});

						toggle = true;
						ConvertPlus._push_page_down(info_bar, toggle);
					});

				//click of close button
				cp_info_bar_toggle.find('.ib-close').on('click', function () {
					const cp_info_bar = jQuery(this).parents('.cp-info-bar');
					const cp_ifb_toggle_btn =
						cp_info_bar.find('.cp-ifb-toggle-btn');
					const cp_info_bar_body =
						cp_info_bar.find('.cp-info-bar-body');
					let btn_animation = 'smile-slideInDown';
					const exit_animation = cp_info_bar.data('exit-animation');
					const entry_animation = cp_info_bar.data('entry-animation');
					const data_toggle = cp_info_bar.data('toggle');
					const form = cp_info_bar.find('.form-main').attr('class');

					if (parseInt(data_toggle) === 1) {
						//  Toggle button animation class
						if (cp_info_bar.hasClass('cp-pos-bottom')) {
							btn_animation = 'smile-slideInUp';
						}

						cp_info_bar.removeClass(entry_animation);
						const cp_info_bar_class = cp_info_bar.attr('class');
						cp_info_bar.attr(
							'class',
							cp_info_bar_class + ' ' + exit_animation
						);

						setTimeout(function () {
							//  Toggle button animation
							cp_ifb_toggle_btn.removeClass('cp-ifb-hide');
							cp_ifb_toggle_btn.addClass(
								'cp-ifb-show smile-animated ' +
									btn_animation +
									''
							);
							cp_info_bar.removeClass('smile-animated');
							cp_info_bar.removeClass(exit_animation);
							cp_info_bar.addClass('cp-ifb-hide');
							cp_info_bar_body.removeClass('cp-flex');
							cp_info_bar.find('.ib-close').css({
								visibility: 'hidden',
							});
							if (typeof form !== 'undefined') {
								cp_info_bar
									.find('.smile-optin-form')[0]
									.reset();
								cp_info_bar
									.find('.cp-form-processing-wrap')
									.css('display', 'none');
								cp_info_bar
									.find('.cp-form-processing')
									.removeAttr('style');
								cp_info_bar
									.find('.cp-msg-on-submit')
									.removeAttr('style');
								cp_info_bar.find('.cp-m-success').remove();
								cp_info_bar.find('.cp-m-error').remove();
							}
						}, 1500);
					}
				});
			});
		},
		/**
		 * set toggle button position.
		 *
		 * @param {Object} container [description]
		 */
		_adjustToggleButton(container) {
			if (container.find('.cp-slidein-toggle').length > 0) {
				const slide_in_head = container
					.find('.cp-slidein-head')
					.outerHeight();
				container
					.find('.cp-animate-container')
					.css({ height: slide_in_head + 'px', opacity: '0' });
			}
		},
	};

	/* Load after x sec Event */
	$(window).on('load', function () {
		$('.cp-global-load').each(function (event) {
			let cp_inactive_time = jQuery(this).data('inactive-time');
			if (typeof cp_inactive_time !== 'undefined') {
				cp_inactive_time = cp_inactive_time * 1000;
				jQuery(document).idleTimer({
					timeout: cp_inactive_time,
					idle: false,
				});
			}
			ConvertPlus.init(event, $(this), 'load');

			if (typeof window.orientation !== 'undefined') {
				Youtube_on_tab = true;
			}
		});

		// z-index fixes for manual display
		$('.cp-modal-global').each(function () {
			const style_id = $(this).data('modal-style');
			if (typeof style_id !== 'undefined' && style_id !== '') {
				const container = jQuery(
					'.cp-modal-popup-container.' + style_id
				);
				if (!container.hasClass('cp-inline-modal-container')) {
					container.appendTo(document.body);
					$(this).appendTo(document.body);
				}
			}
		});

		jQuery('html').addClass('cp-overflow-hidden');

		const custom_uniqueNames = [];
		jQuery.each(custom_class_arr, function (i, el) {
			if ($.inArray(el, custom_uniqueNames) === -1)
				custom_uniqueNames.push(el);
		});

		//click event for open module on custom class
		jQuery.each(custom_uniqueNames, function (index, value) {
			if ('' !== value && 'undefined' !== value && null !== value) {
				let check_val = '.' + value,
					is_custom = false;

				if (value.indexOf('#') !== -1 || value.indexOf('.') !== -1) {
					let str = value;
					str = str.replace(
						/^(?:\[[^\]]*\]|\([^()]*\))\s*|\s*(?:\[[^\]]*\]|\([^()]*\))/g,
						''
					);
					check_val = str;
					is_custom = true;
				}

				jQuery('body').on('click', check_val, function (event) {
					let element;
					let type;
					let condition;
					let cp_data_class_id;
					let cp_modal_click;
					let target;
					let id;
					if (is_custom) {
						element = jQuery(
							".cp-global-load[data-custom-selector='" +
								custom_selector +
								"']"
						);
					} else {
						element = jQuery('.cp-global-load' + check_val);
					}

					type = element.data('module-type');
					let is_inner_class = false;
					if (!jQuery(this).hasClass('global_info_bar_container')) {
						//event.preventDefault();
					} else {
						is_inner_class = true;
					}

					if (type === 'modal') {
						const modal_id = element.data('modal-style');

						if (
							!jQuery('.cp-modal-popup-container.' + modal_id)
								.find('.cp-animate-container')
								.hasClass('cp-form-submit-success')
						) {
							event.preventDefault();
							cp_data_class_id = element.data('class-id');
							cp_modal_click = $('.' + cp_data_class_id);

							if (cp_modal_click.hasClass('cp-window-size')) {
								cp_modal_click.windowSize();
							}

							if (
								$('.global_modal_container.cp-open').length <= 0
							) {
								ConvertPlus._displayPopup(
									cp_modal_click,
									type,
									modal_id
								);
								const cp_tooltip = cp_modal_click
									.find('.cp-tooltip-icon')
									.data('classes');
								$('head').append(
									'<style class="cp-tooltip-close-css">.tip.' +
										cp_tooltip +
										'{ display:block; }</style>'
								);
							}

							//LAzy load video.
							const frame = cp_modal_click.find(
								'.cp-youtube-continer'
							);
							const frame_length = frame.length;
							if (frame_length >= 1) {
								const autoplay = cp_modal_click
									.find('.cp-youtube-continer')
									.data('autoplay');
								cp_modal_click
									.find('.cp-youtube-continer')
									.trigger('click', [autoplay]);
							} else {
								const src = cp_modal_click
									.find('.cp-youtube-frame')
									.attr('data_y_src');
								cp_modal_click
									.find('.cp-youtube-frame')
									.attr('src', src);
								cp_modal_click
									.find('.cp-youtube-frame')
									.removeAttr('data_y_src');
							}

							if (styleArray.length !== 0) {
								if (
									!$(this).hasClass('cp-disabled') &&
									!cp_modal_click.hasClass(
										'cp-disabled-impression'
									)
								) {
									ConvertPlus.update_impressions(styleArray);
									$(document).trigger(
										'cp_custom_class_clicked',
										[this]
									);
								}
							}
						}
					} else if (type === 'info-bar' && !is_inner_class) {
						if (
							!jQuery(this).hasClass('global_info_bar_container')
						) {
							event.preventDefault();
						}
						target = element.first();
						id = target.data('info_bar-id');
						if (!target.hasClass('cp-form-submit-success')) {
							cp_data_class_id = target.data('custom-class');
							if (ConvertPlus._isOtherPopupOpen(type)) {
								target.css('display', 'block');
								ConvertPlus._displayPopup(target, type, id);
							}
						}
					} else if (type === 'slide_in') {
						if (!jQuery(this).hasClass('slidein-overlay')) {
							event.preventDefault();
							type = element.data('module-type');
							cp_data_class_id = element.data('class-id');
							slidein = $('.' + cp_data_class_id);
							style = slidein.data('slidein-style');
							condition =
								jQuery('.si-open').length <= 1 &&
								jQuery('.si-open').find('.cp-slide-in-float-on')
									.length <= 1;

							if (condition) {
								slidein
									.find('.cp-animate-container')
									.removeClass('cp-hide-slide');
								ConvertPlus._displayPopup(slidein, type, style);
							}
						}
					}
				});
			}
		});
	});

	/* check if event is already fired */
	function cp_is_triggered(elem) {
		const cpmodule_type = elem.data('module-type');
		let condition = true;
		const cpclass_id = elem.data('class-id');
		let cpslide_in;

		if (cpmodule_type === 'modal') {
			modal = $('.' + cpclass_id);
			condition =
				modal.hasClass('cp-open') || modal.hasClass('cp-visited-popup');
		} else if (cpmodule_type === 'slide_in') {
			cpslide_in = $('.' + cpclass_id);
			condition = cpslide_in.hasClass('si-open');
		} else if (cpmodule_type === 'info-bar') {
			condition = elem.hasClass('ib-display');
		}
		return condition;
	}

	// Sets cookies.
	window.createCookie = function (name, value, days) {
		// If we have a days value, set it in the expiry of the cookie.
		let expires = '';
		if (days) {
			const cookieDate = new Date();
			cookieDate.setTime(
				cookieDate.getTime() + days * 24 * 60 * 60 * 1000
			);
			expires = '; expires=' + cookieDate.toGMTString();
		}
		// Write the cookie.
		document.cookie = name + '=' + value + expires + '; path=/';
	};

	//	Email validation
	window.isValidEmailAddress = function (emailAddress) {
		const pattern = new RegExp(
			/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i
		);
		return pattern.test(emailAddress);
	};

	window.validate_it = function (current_ele, value) {
		if (!value.trim()) {
			return true;
		} else if (current_ele.hasClass('cp-email')) {
			if (!isValidEmailAddress(value)) {
				return true;
			}
			return false;
		} else if (current_ele.hasClass('cp-textfeild')) {
			if (/^[a-zA-Z0-9- ]*$/.test(value) === false) {
				return true;
			}
			return false;
		}

		return false;
	};

	/* Exit Intent Event */
	$(document).on('mouseleave', function (event) {
		let result;
		$('.cp-global-load').each(function () {
			const element = $(this),
				exit_intent = element.data('exit-intent'),
				add_to_cart = element.data('add-to-cart'),
				item_present = ConvertPlus._getCookie(
					'woocommerce_items_in_cart'
				);

			if (exit_intent === 'enabled' && parseInt(add_to_cart) === 1) {
				result = cp_is_triggered(element);
				if (
					result === false &&
					(add_flag || parseInt(item_present) === 1)
				) {
					ConvertPlus.init(event, element, 'mouseleave');
				}
			} else if (exit_intent === 'enabled') {
				result = cp_is_triggered(element);
				if (result === false) {
					ConvertPlus.init(event, element, 'mouseleave');
				}
			}
		});
	});

	/* Idle Event */
	jQuery(document).on('idle.idleTimer', function (event) {
		$('.cp-global-load').each(function () {
			ConvertPlus.init(event, $(this), 'idle');
		});
	});

	/*Google Recaptcha */
	jQuery(window).on('load', function () {
		if (jQuery('.g-recaptcha-response')[0]) {
			jQuery('.cp-onload ').addClass(
				'cp-recaptcha cp-recaptcha-index-1 cp-recaptcha-index-2 cp-recaptcha-index-3 cp-recaptcha-index-4 cp-recaptcha-index-5 cp-recaptcha-index-6 cp-recaptcha-index-7 '
			);
			jQuery('.g-recaptcha-response').addClass('cp-recaptcha-required');
			jQuery('.cp-recaptcha-required').prop('required', true);
			jQuery('.g-recaptcha-response')
				.parent()
				.addClass('cp-g-recaptcha-response');
		}
		const element = jQuery('.cp-module'),
			cp_module_type = element.data('module-type');
		if (cp_module_type === 'info-bar') {
			if (
				jQuery('.g-recaptcha').parents('.cp-info-bar-container')
					.length === 1
			) {
				jQuery('.cp-info-bar-body .cp-submit').addClass(
					'cp-recaptcha-css'
				);
				jQuery(
					'.ib-form-container .cp-form-container .cp-form-layout-3 .cp-submit .cp-recaptcha-css '
				).css('display', 'inline', '!important');
				jQuery(
					'.ib-form-container .cp-form-container .cp-form-layout-3 .cp-submit-wrap'
				).css('padding-bottom', '40px');
			}
		}
	});

	jQuery(document).ready(function () {
		ConvertPlus._check_responsive_font_sizes();
		jQuery('.blinking-cursor').remove();

		$('.cp-global-load').each(function () {
			ConvertPlus._count_inline_impressions($(this));
		});

		/*infobar functions*/
		ConvertPlus._cp_ifb_toggle();
	});

	jQuery(window).on('load', function () {
		/*load after content add extra spces for some theme*/
		clearTimeout($.data(this, 'cp_check_empty_span'));
		$.data(
			this,
			'cp_check_empty_span',
			setTimeout(function () {
				const load_after_post = jQuery('.cp-load-after-post')
					.parent()
					.text()
					.trim();
				if (typeof load_after_post !== 'undefined') {
					const post_lenght = load_after_post.trim().length;
					if (post_lenght === 0) {
						const check_xtheme_preview = jQuery(
							window.parent.document
						).find('.cs-preview-frame-container').length;
						if (check_xtheme_preview !== 1) {
							jQuery('.cp-load-after-post')
								.parent()
								.addClass('cp-empty-content');
						}
					}
				}

				const deviceAgent = navigator.userAgent.toLowerCase();
				const agentID = deviceAgent.match(/(iphone|ipod|ipad)/);
				if (agentID) {
					jQuery('html').addClass('cp-iphone-browser');
				}

				// load images after page load.
				[].forEach.call(
					jQuery('.cp-module').find('img[data-src]'),
					function (img) {
						img.setAttribute('src', img.getAttribute('data-src'));
						img.onload = function () {
							img.removeAttribute('data-src');
						};
					}
				);
			}, 1000)
		);
	});

	let rs_flag = 0;
	let resizeTimer;

	jQuery(window).on('resize', function () {
		clearTimeout(resizeTimer);

		resizeTimer = setTimeout(function () {
			ConvertPlus._close_button_tootip();

			jQuery('.cp-info-bar.ib-display').each(function () {
				const md = jQuery(this);
				rs_flag++;
				ConvertPlus._apply_push_page_down(md, 'resize');
			});

			jQuery('.cp-info-bar').each(function () {
				ConvertPlus._infoBarPos(jQuery(this));
			});
		}, 1000);
	});

	jQuery(window).on('modalOpen', function () {
		ConvertPlus._close_button_tootip();
	});

	jQuery(document).on('cp_conversion_done', function (e, $this, style_id) {
		if (
			!jQuery($this).parents('.cp-form-container').find('.cp-email')
				.length > 0
		) {
			const is_only_conversion = jQuery($this)
				.parents('.cp-form-container')
				.find('[name="only_conversion"]').length;
			if (is_only_conversion > 0) {
				const cookieTime = modal.data('conversion-cookie-time'),
					cp_conversion_cookie = ConvertPlus._getCookie(style_id);
				if (!cp_conversion_cookie) {
					if (cookieTime) {
						ConvertPlus._createCookie(style_id, true, cookieTime);
					}
				}
				jQuery($this).addClass('cp-disabled');
			}
		}
	});

	// Custom class impression count
	jQuery(document).on('cp_custom_class_clicked', function (e, $this) {
		jQuery($this).addClass('cp-disabled');
	});

	// Close modal on click of close button
	jQuery(document).on('click', '.cp-form-submit-error', function () {
		const $this = jQuery(this),
			cp_form_processing_wrap = $this.find('.cp-form-processing-wrap'),
			cp_tooltip = $this.find('.cp-tooltip-icon').data('classes'),
			cp_msg_on_submit = $this.find('.cp-msg-on-submit');

		cp_form_processing_wrap.hide();
		$this.removeClass('cp-form-submit-error');
		cp_msg_on_submit.html('');
		cp_msg_on_submit.removeAttr('style');
		jQuery('head').append(
			'<style class="cp-tooltip-css">.tip.' +
				cp_tooltip +
				'{display:block }</style>'
		);
	});

	jQuery('.cp-overlay').on('idle.idleTimer', function () {
		const cp_overlayModal = jQuery('.cp-overlay');
		jQuery(document).trigger('closeModal', [cp_overlayModal]);
		const cp_tooltip = cp_overlayModal
			.find('.cp-tooltip-icon')
			.data('classes');
		setTimeout(function () {
			jQuery('head').append(
				'<style id="cp-tooltip-close-css">.tip.' +
					cp_tooltip +
					'{ display:none; }</style>'
			);
		}, 1000);
	});

	jQuery(document).on('idle.idleTimer', function () {
		if (jQuery('.ib-display').hasClass('cp-close-after-x')) {
			const cp_info_bar_idle = jQuery('.ib-display');
			jQuery(document).trigger('cp_close_info_bar', [cp_info_bar_idle]);
		}

		if (jQuery('.slidein-overlay').hasClass('cp-close-after-x')) {
			const cp_slidein_idle = jQuery('.slidein-overlay');
			jQuery(document).trigger('closeSlideIn', [cp_slidein_idle]);
		}
	});

	//close modal on cp-close class
	jQuery(document).on('click', '.cp-close', function () {
		if (!jQuery(this).parents('.cp-overlay').hasClass('do_not_close')) {
			const cp_close_overlay_modal = jQuery(this).parents('.cp-overlay');
			jQuery(document).trigger('closeModal', [cp_close_overlay_modal]);
		}
	});

	//close modal on cp-inner-close class
	jQuery(document).on('click', '.cp-inner-close', function () {
		const cp_close_modal = jQuery(this).parents('.cp-overlay');
		jQuery(document).trigger('closeModal', [cp_close_modal]);
	});

	// Close modal on click of close button
	jQuery(document).on('closeModal', function (event, cp_close_modal) {
		const id = cp_close_modal.data('class'),
			overlay = $('.cp-global-load[data-class-id=' + id + ']');
		ConvertPlus.init(event, overlay, 'closepopup');
	});

	jQuery(document).on(
		'cp_close_info_bar',
		function (event, cp_close_info_bar) {
			ConvertPlus.init(event, cp_close_info_bar, 'closepopup');
		}
	);

	//set cookies for optin widget style
	jQuery('body').on('click', '.cp-slidein-head .cp-widget-open', function () {
		const cp_slidein = jQuery(this).parents('.slidein-overlay'),
			cookieTime = cp_slidein.data('closed-cookie-time'),
			slide_in_cookieName = cp_slidein.data('slidein-id'),
			slidein_temp_cookie = 'temp_' + slide_in_cookieName;

		ConvertPlus._createCookie(slidein_temp_cookie, true, 1);

		const slide_in_cookie = ConvertPlus._getCookie(slide_in_cookieName);

		if (!slide_in_cookie) {
			if (cookieTime) {
				cp_slidein.addClass('cp-always-minimize-widget');
				ConvertPlus._createCookie(
					slide_in_cookieName,
					true,
					cookieTime
				);
			}
		}
	});

	// Close Slide In on click of close button
	jQuery(document).on('closeSlideIn', function (event, closeSlidein) {
		const id = closeSlidein.data('class'),
			overlay = $('.si-onload[data-class-id=' + id + ']');
		ConvertPlus.init(event, overlay, 'closepopup');
	});

	//set tab index for input
	jQuery('.smile-optin-form').each(function () {
		const option = $(this).parents('.cp-module').data('module-name');
		$(this).find('input[name="cp_module_type"]').val(option);
		const last_input = jQuery(this).find('input.cp-input').last();
		if (last_input.hasClass('cp-input')) {
			last_input.addClass('cp-last-field');
		}
	});

	jQuery('input.cp-input').on('keydown', function (e) {
		const keyCode = window.event ? e.which : e.keyCode;
		if (keyCode === 9 && jQuery(this).hasClass('cp-last-field')) {
			e.preventDefault();
			const form = jQuery(this).parents('.smile-optin-form');
			form.find('.cp-submit').attr('tabindex', -1).trigger('focus');
		}
	});

	$(document).on('scroll', function (event) {
		//scroll event trigger
		clearTimeout($.data(this, 'CP_scrollEvent'));
		$.data(
			this,
			'CP_scrollEvent',
			setTimeout(function () {
				$('.cp-global-load').each(function () {
					const element = $(this),
						scroll_chk = element.data('onscroll-value'),
						cp_scroll_class = element.data('scroll-class');
					const after_post =
						element.hasClass('cp-after-post') ||
						element.hasClass('ib-after-post') ||
						element.hasClass('si-after-post');

					if (
						(typeof cp_scroll_class !== 'undefined' &&
							cp_scroll_class !== '') ||
						scroll_chk !== '' ||
						after_post
					) {
						const result = cp_is_triggered(element);
						if (result === false) {
							ConvertPlus.init(event, element, 'scroll');
						}
					}

					ConvertPlus._count_inline_impressions($(this));
				});
			}, 200)
		);

		//Add compatibility support for avada theme push page down
		clearTimeout($.data(this, 'CP_scrollTimer'));
		$.data(
			this,
			'CP_scrollTimer',
			setTimeout(function () {
				$('.cp-ib-onload.cp-pos-top').each(function () {
					let fusion_class, admin_bar_height, fixed_css;
					const element = $(this),
						ht = element.outerHeight(),
						page_push_down = element.data('push-down') || null;

					if (
						page_push_down &&
						element.hasClass('ib-display') &&
						element.hasClass('ib-fixed')
					) {
						const is_avada_header = jQuery(
							'.fusion-header-wrapper'
						).find('.fusion-sticky-menu-');
						let is_avada_sticky_menu = '';
						if (typeof is_avada_header !== 'undefined') {
							is_avada_sticky_menu = is_avada_header.length;
						}

						if (is_avada_sticky_menu > 0) {
							fusion_class = '.fusion-header';
							if (
								jQuery('body').hasClass(
									'fusion-header-layout-v4'
								) ||
								jQuery('body').hasClass(
									'fusion-header-layout-v5'
								)
							) {
								fusion_class = '.fusion-secondary-main-menu';
							}
							admin_bar_height =
								jQuery('#wpadminbar').outerHeight();
							const total_ht = ht + admin_bar_height;
							jQuery(fusion_class).addClass('cp-fusion-header');
							jQuery('.cp_fusion_css').remove();

							if (
								element
									.find('.cp-ifb-toggle-btn')
									.hasClass('cp-ifb-show')
							) {
								fixed_css =
									'.cp-fusion-header{top:' +
									admin_bar_height +
									'px !important}';
								$('head').append(
									"<style class='cp_fusion_css' type='text/css'>" +
										fixed_css +
										'</style>'
								);
							} else {
								fixed_css =
									'.cp-fusion-header{top:' +
									total_ht +
									'px !important}';
								$('head').append(
									"<style class='cp_fusion_css' type='text/css'>" +
										fixed_css +
										'</style>'
								);
							}

							jQuery(fusion_class).addClass('cp-scroll-start');
						}
					} else {
						fusion_class = '.fusion-header';
						if (
							jQuery('body').hasClass(
								'fusion-header-layout-v4'
							) ||
							jQuery('body').hasClass('fusion-header-layout-v5')
						) {
							fusion_class = '.fusion-secondary-main-menu';
						}
						jQuery(fusion_class).addClass('cp-fusion-header');
						admin_bar_height = jQuery('#wpadminbar').outerHeight();
						fixed_css =
							'.cp-fusion-header{top:' +
							admin_bar_height +
							'px !important}';
						$('head').append(
							"<style class='cp_fusion_css' type='text/css'>" +
								fixed_css +
								'</style>'
						);
					}
				});
			}, 100)
		);
	});

	//Add compatibility support for avada theme push page down
	jQuery(document).on('infobarOpen', function (e, info_bar_data) {
		const element = info_bar_data,
			ht = element.outerHeight(),
			page_push_down = element.data('push-down') || null;

		const is_avada_sticky_menu = jQuery('.fusion-header-wrapper').find(
			'.fusion-sticky-menu-'
		).length;
		if (is_avada_sticky_menu && page_push_down) {
			let fusion_class = '.fusion-header';
			if (
				jQuery('body').hasClass('fusion-header-layout-v4') ||
				jQuery('body').hasClass('fusion-header-layout-v5')
			) {
				fusion_class = '.fusion-secondary-main-menu';
			}

			const admin_bar_height = jQuery('#wpadminbar').outerHeight(),
				total_ht = ht + admin_bar_height,
				old_top = jQuery(fusion_class).css('top');

			jQuery(fusion_class).attr('data-old-top', old_top);
			const data_toggle = element.data('toggle-visible');
			if (!data_toggle && element.hasClass('ib-fixed')) {
				jQuery('.cp_fusion_css').remove();
				jQuery(fusion_class).addClass('cp-fusion-header');
				const fixed_css =
					'.cp-fusion-header{top:' + total_ht + 'px !important}';
				$('head').append(
					"<style class='cp_fusion_css' type='text/css'>" +
						fixed_css +
						'</style>'
				);

				jQuery(fusion_class).addClass('cp-scroll-start');
			}
		}
	});

	//Add compatibility support for avada theme push page down
	jQuery(document).on(
		'cp_close_info_bar',
		function (event, cp_close_info_bar) {
			const element = cp_close_info_bar,
				page_push_down = element.data('push-down') || null;
			let fusion_class = '.fusion-header';

			if (
				jQuery('body').hasClass('fusion-header-layout-v4') ||
				jQuery('body').hasClass('fusion-header-layout-v5')
			) {
				fusion_class = '.fusion-secondary-main-menu';
			}

			if (page_push_down) {
				jQuery('.cp_fusion_css').remove();
			}

			cp_close_info_bar.addClass('cp-stop-scroll');
			jQuery(fusion_class).removeClass('cp-scroll-start');
			$('.cp-push-page-css').remove();
		}
	);

	//close gravity form & Custom analytics for Contact form.
	jQuery(document).on('gform_confirmation_loaded', function (event, form_id) {
		const form = jQuery('#gf_' + form_id),
			style_id = form.parents('.cp-module').data('style-id'),
			style_name = form.parents('.cp-module').data('module-name'),
			is_closed = form.parents('.cp-module').data('close-gravity');

		if (style_id !== undefined) {
			jQuery(document).trigger('cp_custom_analytics', [style_id]);
		}
		if (parseInt(is_closed) === 1) {
			jQuery(document).trigger('cp_custom_close_module', [
				form,
				style_name,
			]);
		}
	});

	//Custom analytics for Contact form.
	// eslint-disable-next-line  @wordpress/no-global-event-listener
	document.addEventListener(
		'wpcf7submit',
		function (event) {
			const status = event.detail.status;
			const form_id = event.detail.unitTag;

			if (status === 'mail_sent') {
				const form = jQuery('#' + form_id);
				const style_id = form.parents('.cp-module').data('style-id'),
					style_name = form.parents('.cp-module').data('module-name'),
					is_closed = form
						.parents('.cp-module')
						.data('close-gravity');

				if (style_id !== undefined) {
					jQuery(document).trigger('cp_custom_analytics', [style_id]);
				}
				if (parseInt(is_closed) === 1) {
					jQuery(document).trigger('cp_custom_close_module', [
						form,
						style_name,
					]);
				}
			}
		},
		false
	);

	//custom analytics for ninja form.
	jQuery(document).on('nfFormSubmitResponse', function (event, response) {
		const form_id = 'nf-form-' + response.id + '-cont',
			form = jQuery('#' + form_id),
			style_id = form.parents('.cp-module').data('style-id'),
			style_name = form.parents('.cp-module').data('module-name'),
			is_closed = form.parents('.cp-module').data('close-gravity');

		if (style_id !== undefined) {
			jQuery(document).trigger('cp_custom_analytics', [style_id]);
		}
		if (parseInt(is_closed) === 1) {
			jQuery(document).trigger('cp_custom_close_module', [
				form,
				style_name,
			]);
		}
	});

	//close module after custom conversion
	jQuery(window).on('cp_custom_close_module', function (e, form, style_name) {
		if (style_name === 'modal') {
			const cp_custom_modal = form.parents('.cp-open');
			jQuery(document).trigger('closeModal', [cp_custom_modal]);
		} else if (style_name === 'slidein') {
			const cp_custom_slidein = form.parents('.slidein-overlay');
			jQuery(document).trigger('closeSlideIn', [cp_custom_slidein]);
		} else if (style_name === 'infobar') {
			const cp_custom_info_bar = form.parents('.cp-info-bar');
			jQuery(document).trigger('cp_close_info_bar', [cp_custom_info_bar]);
		}
	});

	//Custom conversion
	jQuery(window).on('cp_custom_analytics', function (e, style_id) {
		setTimeout(function () {
			const cp_custom_nounce = jQuery('.cp-impress-nonce').val(),
				cp_custom_ana_data = {
					action: 'custom_form_update_conversions',
					conversion: true,
					style_id,
					option: 'smile_modal_styles',
					security: cp_custom_nounce,
				};
			jQuery.ajax({
				url: smile_ajax.url,
				data: cp_custom_ana_data,
				type: 'POST',
				dataType: 'HTML',
				security: jQuery('.cp-impress-nonce').val(),
				beforeSend() {
					// do your stuff
					ajax_run = false;
				},
			});
		}, 2000);
	});

	jQuery('.cp-youtube-continer').on('click', function (e, auto) {
		e.preventDefault();
		const iframe = jQuery('<iframe/>');
		let src = jQuery(this).data('custom-url');
		const cpstyle = jQuery(this).data('custom-css');
		const classname = jQuery(this).data('class');
		const wt = jQuery(this).data('width');
		const ht = jQuery(this).data('height');
		src = src.replace('autoplay=0', 'autoplay=1');

		if (auto === null || auto === '1' || auto === 'undefined') {
			src = src.replace('autoplay=0', 'autoplay=1');
		} else {
			src = src.replace('autoplay=1', 'autoplay=0');
		}

		iframe.attr('class', classname);
		iframe.attr('frameborder', '0');
		iframe.attr('allowfullscreen', '1');
		iframe.attr('style', cpstyle);
		iframe.attr('allow', 'autoplay;encrypted-media;');
		iframe.attr('src', src);
		if (wt !== '' || ht !== '') {
			iframe.attr('width', wt);
			iframe.attr('height', ht);
		}
		jQuery(this).html(iframe);
	});
})(jQuery);
