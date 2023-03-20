/* eslint-env jquery */
(function ($) {
	'use strict';

	function slide_in_process_cp_form(t) {
		const form = jQuery(t),
			data = form.serialize(),
			info_container = jQuery(t)
				.parents('.cp-animate-container')
				.find('.cp-msg-on-submit'),
			spinner = jQuery(t)
				.parents('.cp-animate-container')
				.find('.cp-form-processing'),
			slidein = jQuery(t).parents('.global_slidein_container'),
			cp_form_processing_wrap = jQuery(t)
				.parents('.cp-animate-container')
				.find('.cp-form-processing-wrap'),
			cp_animate_container = jQuery(t).parents('.cp-animate-container'),
			cp_tooltip = slidein.find('.cp-tooltip-icon').data('classes'),
			cookieTime = slidein.data('conversion-cookie-time'),
			dont_close = jQuery(t)
				.parents('.global_slidein_container')
				.hasClass('do_not_close'),
			redirectdata = jQuery(t)
				.parents('.global_slidein_container')
				.data('redirect-lead-data'),
			redirect_to = jQuery(t)
				.parents('.global_slidein_container')
				.data('redirect-to'),
			//download_url 			= jQuery(t).parents(".global_slidein_container").data("download-url");
			form_action_on_submit = jQuery(t)
				.parents('.global_slidein_container')
				.data('form-action'),
			cp_optin_widget = jQuery(t)
				.parents('.global_slidein_container')
				.find('.cp-slidein-body')
				.hasClass('cp-optin-widget');
		let form_action_dealy = jQuery(t)
			.parents('.global_slidein_container')
			.data('form-action-time');
		form_action_dealy = parseInt(form_action_dealy * 1000);
		const redirect_link =
			jQuery(t).find('.btn-subscribe').attr('data-redirect-link') || '';
		const redirect_link_target =
			jQuery(t)
				.find('.btn-subscribe')
				.attr('data-redirect-link-target') || '_blank';

		const parent_id = slidein.data('parent-style');
		let cookieName;
		if (typeof parent_id !== 'undefined') {
			cookieName = parent_id;
		} else {
			cookieName = slidein.data('slidein-id');
		}

		// Check for required fields are not empty
		// And create query strings to send to redirect URL after form submission
		let query_string = '';
		let redirect_with = '';
		let cf_response = '';
		form.find('.cp-input').each(function (index) {
			const $this = jQuery(this);

			if (!$this.hasClass('cp-submit-button')) {
				// Check condition for Submit Button
				const input_name = $this.attr('name'),
					input_value = $this.val();

				let res = input_name.replace(/param/gi, function myFunction() {
					return '';
				});
				res = res.replace('[', '');
				res = res.replace(']', '');

				query_string += index !== 0 ? '&' : '';
				query_string += res + '=' + input_value;

				const input_required = $this.attr('required') ? true : false;

				if (input_required) {
					if (validate_it($this, input_value)) {
						$this.addClass('cp-input-error');
					} else {
						$this.removeClass('cp-input-error');
					}
				}
			}
		});

		//	All form fields Validation
		let fail = 0;
		form.find('select, textarea, input').each(function (i, el) {
			if (jQuery(el).prop('required')) {
				const type = jQuery(el).attr('type');
				if (type === 'checkbox' && $(this).prop('checked') === false) {
					fail++;
					setTimeout(function () {
						jQuery(el).addClass('cp-error');
					}, 100);
				} else if (!jQuery(el).val()) {
					fail++;
					setTimeout(function () {
						jQuery(el).addClass('cp-error');
					}, 100);
				} else if (jQuery(el).hasClass('cp-email')) {
					//	Client side email Validation
					//	If not empty value, Then validate email
					const email = jQuery(el).val();

					if (isValidEmailAddress(email)) {
						jQuery(el).removeClass('cp-error');
					} else {
						setTimeout(function () {
							jQuery(el).addClass('cp-error');
						}, 100);
						fail++;
					}
				} else {
					jQuery(el).removeClass('cp-error');
				}
			}
		});

		//submit if fail count never got greater than 0
		if (fail > 0) {
		} else {
			cp_form_processing_wrap.show();

			info_container.fadeOut(120, function () {
				jQuery(this).show().css({ visibility: 'hidden' });
			});

			// Show processing spinner
			spinner.hide().css({ visibility: 'visible' }).fadeIn(100);

			jQuery.ajax({
				url: smile_ajax.url,
				data,
				type: 'POST',
				dataType: 'HTML',
				success(result) {
					if (cookieTime) {
						if (slidein.find('.cp-slidein-toggle').length > 0) {
							createCookie(
								cookieName + '-conversion',
								true,
								cookieTime
							);
						} else {
							//removeCookie(cookieName+'-conversion');
							createCookie(cookieName, true, cookieTime);
						}
					}
					const obj = JSON.parse(result);
					let cls = '';
					let msg_string = '';

					if (
						typeof obj.status !== 'undefined' &&
						obj.status !== null
					) {
						cls = obj.status;
					}

					if (
						typeof obj.cf_response !== 'undefined' &&
						obj.cf_response !== null
					) {
						cf_response = obj.cf_response;
						jQuery(document).trigger('cp_cf_response_done', [
							this,
							slidein,
							cf_response,
						]);
					}

					//	is valid - Email MX Record
					if (obj.email_status) {
						form.find('.cp-email').removeClass('cp-error');
					} else {
						setTimeout(function () {
							form.find('.cp-email').addClass('cp-error');
						}, 100);
						form.find('.cp-email').trigger('focus');
					}

					let detailed_msg =
						typeof obj.detailed_msg !== 'undefined' &&
						obj.detailed_msg !== null
							? obj.detailed_msg
							: '';
					if (detailed_msg !== '' && detailed_msg !== null) {
						detailed_msg =
							"<h5>Here is More Information:</h5><div class='cp-detailed-message'>" +
							detailed_msg +
							'</div>';
						detailed_msg +=
							"<div class='cp-admin-error-notice'>Read How to Fix This, click <a rel='noopener' target='_blank' href='https://www.convertplug.com/plus/docs/something-went-wrong/'>here</a></div>";
						detailed_msg += "<div class='cp-go-back'>Go Back</div>";
						msg_string +=
							'<div class="cp-only-admin-msg">[Only you can see this message]</div>';
					}

					// remove backslashes from success message
					obj.message = obj.message.replace(/\\/g, '');

					// The Detailed message when the Google recaptcha Inavlid secret Key.
					if (
						obj.detailed_msg ===
						'Invalid Secret Key for Google Recaptcha'
					) {
						setTimeout(function () {
							form.find('.g-recaptcha').addClass('cp-error');
						}, 100);
						form.find('.g-recaptcha').trigger('focus');
					}

					//	show message error/success
					if (
						typeof obj.message !== 'undefined' &&
						obj.message !== null
					) {
						info_container
							.hide()
							.css({ visibility: 'visible' })
							.fadeIn(120);
						//info_container.html( '<div class="cp-m-'+cls+'">'+obj.message+'</div>' );
						msg_string +=
							'<div class="cp-m-' +
							cls +
							'"><div class="cp-error-msg">' +
							obj.message +
							'</div>' +
							detailed_msg +
							'</div>';
						info_container.html(msg_string);
						cp_animate_container.addClass('cp-form-submit-' + cls);
					}

					if (
						typeof obj.action !== 'undefined' &&
						obj.action !== null
					) {
						//	Show processing spinner
						spinner.fadeOut(100, function () {
							jQuery(this).show().css({ visibility: 'hidden' });
						});

						//	Hide error/success message
						info_container
							.hide()
							.css({ visibility: 'visible' })
							.fadeIn(120);

						if (cls === 'success') {
							//hide tool tip
							jQuery('head').append(
								'<style class="cp-tooltip-css">.tip.' +
									cp_tooltip +
									'{display:none }</style>'
							);

							// 	Redirect if status is [success]
							if (obj.action === 'redirect') {
								cp_form_processing_wrap.hide();
								slidein.hide();
								const url = obj.url;
								let urlstring = '';
								if (url.indexOf('?') > -1) {
									urlstring = '&';
								} else {
									urlstring = '?';
								}

								let redirect_url =
									url + urlstring + decodeURI(query_string);
								if (redirectdata === 1) {
									redirect_url = redirect_url;
								} else {
									redirect_url = obj.url;
								}

								if (redirect_to !== 'download') {
									redirect_with = redirect_to;
									const win_open = window.open(
										redirect_url,
										'_' + redirect_with
									);
									if (win_open === '') {
										document.location.href = redirect_url;
									}
								} else if (redirect_url !== '') {
									const redirect_file =
										redirect_url.split(',');
									jQuery.each(
										redirect_file,
										function (index, urlParam) {
											redirect_url = urlParam;
											cp_slidein_download_file(
												redirect_url
											);
										}
									);
								}
							} else {
								cp_form_processing_wrap.show();

								if (form_action_on_submit === 'disappear') {
									slidein.removeClass('cp-hide-inline-style');
									slidein.removeClass('cp-close-slidein');
									setTimeout(function () {
										if (
											slidein.hasClass(
												'cp-slidein-inline'
											)
										) {
											slidein.addClass(
												'cp-hide-inline-style'
											);
										}
										if (
											slidein.find('.cp-toggle-container')
												.length >= 1 ||
											cp_optin_widget === true
										) {
											slidein.addClass(
												'cp-close-slidein'
											);
										}

										jQuery(document).trigger(
											'closeSlideIn',
											[slidein]
										);
									}, form_action_dealy);
								} else if (
									form_action_on_submit === 'reappear'
								) {
									setTimeout(function () {
										info_container.empty();
										cp_form_processing_wrap.css({
											display: 'none',
										});
										info_container.removeAttr('style');
										spinner.removeAttr('style');
										form.trigger('reset');
									}, form_action_dealy);
								}
							}

							if (
								dont_close &&
								!slidein.hasClass('cp-do-not-close-inline')
							) {
								setTimeout(function () {
									slidein.addClass('cp-hide-inline-style');
									jQuery(document).trigger('closeSlideIn', [
										slidein,
									]);
								}, 3000);
							}
						}
					}

					if (redirect_link !== 'undefined' && redirect_link !== '') {
						if (
							navigator.userAgent
								.toLowerCase()
								.match(/(ipad|iphone)/)
						) {
							document.location = redirect_link;
						} else {
							window.open(redirect_link, redirect_link_target);
						}
					}
				},
				error() {
					//	Show form & Hide processing spinner
					cp_form_processing_wrap.hide();
					spinner.fadeOut(100, function () {
						jQuery(this).show().css({ visibility: 'hidden' });
					});
				},
			});
		}
	}

	jQuery(document).ready(function () {
		jQuery('.cp-slidein-popup-container')
			.find('.smile-optin-form')
			.each(function (index, el) {
				// enter key press
				jQuery(el)
					.find('input')
					.keypress(function (event) {
						if (event.which === 13) {
							event.preventDefault();
							const check_sucess = jQuery(this)
								.parents('.cp-animate-container')
								.hasClass('cp-form-submit-success');
							if (!check_sucess) {
								slide_in_process_cp_form(el);
							}
						}
					});

				// submit add subscriber request
				jQuery(el)
					.find('.btn-subscribe')
					.on('click', function (e) {
						jQuery(el).find('.cp-input').removeClass('cp-error');
						if (!jQuery(this).hasClass('cp-disabled')) {
							slide_in_process_cp_form(el);
							jQuery(document).trigger('si_conversion_done', [
								this,
							]);
						}
						e.preventDefault();
					});

				jQuery(el)
					.find('.btn-subscribe')
					.keypress(function (event) {
						if (event.which === 13) {
							event.preventDefault();
							const check_sucess = jQuery(this)
								.parents('.cp-animate-container')
								.hasClass('cp-form-submit-success');
							if (!check_sucess) {
								slide_in_process_cp_form(el);
							}
						}
					});
			});
	});

	function cp_slidein_download_file(fileURL) {
		const link = jQuery('<a>');
		const index = fileURL.lastIndexOf('/') + 1;
		const fileName = fileURL.substr(index);
		link.attr('href', fileURL);
		link.attr('download', fileName);
		link.text('cpro_anchor_link');
		link.addClass('cplus_dummy_anchor');
		link.attr('target', '_blank');
		jQuery('body').append(link);
		jQuery('.cplus_dummy_anchor')[0].click();

		setTimeout(function () {
			jQuery('.cplus_dummy_anchor').remove();
		}, 500);
	}
})(jQuery);
