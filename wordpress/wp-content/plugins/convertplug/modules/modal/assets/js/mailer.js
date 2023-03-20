/* eslint-env jquery */
(function ($) {
	'use strict';

	function modal_process_cp_form(t) {
		const form = jQuery(t)
				.parents('.cp-modal-body')
				.find('.smile-optin-form'),
			data = form.serialize(),
			info_container = jQuery(t)
				.parents('.cp-animate-container')
				.find('.cp-msg-on-submit'),
			spinner = jQuery(t)
				.parents('.cp-animate-container')
				.find('.cp-form-processing'),
			modal = jQuery(t).parents('.global_modal_container '),
			cp_form_processing_wrap = jQuery(t)
				.parents('.cp-animate-container')
				.find('.cp-form-processing-wrap'),
			cp_animate_container = jQuery(t).parents('.cp-animate-container'),
			cp_tooltip = modal.find('.cp-tooltip-icon').data('classes');

		const cookieTime = modal.data('conversion-cookie-time'),
			dont_close = jQuery(t)
				.parents('.global_modal_container ')
				.hasClass('do_not_close'),
			redirectdata = jQuery(t)
				.parents('.global_modal_container ')
				.data('redirect-lead-data'),
			redirect_to = jQuery(t)
				.parents('.global_modal_container ')
				.data('redirect-to'),
			form_action_on_submit = jQuery(t)
				.parents('.global_modal_container')
				.data('form-action');

		let form_action_dealy = jQuery(t)
			.parents('.global_modal_container')
			.data('form-action-time');

		form_action_dealy = parseInt(form_action_dealy * 1000);

		const parent_id = modal.data('parent-style');
		let cookieName = '';
		if (typeof parent_id !== 'undefined') {
			cookieName = parent_id;
		} else {
			cookieName = modal.data('modal-id');
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
		form.find('select, textarea, input ').each(function (i, el) {
			let fail_log = '';
			if (jQuery(el).prop('required')) {
				const type = jQuery(el).attr('type');
				let name = '';
				if (type === 'checkbox' && $(this).prop('checked') === false) {
					fail++;
					setTimeout(function () {
						jQuery(el).addClass('cp-error');
					}, 100);
					name = jQuery(el).attr('name');
					fail_log += name + ' is required \n';
				} else if (!jQuery(el).val()) {
					fail++;
					setTimeout(function () {
						jQuery(el).addClass('cp-error');
					}, 100);
					name = jQuery(el).attr('name');
					fail_log += name + ' is required \n';
				}
				//	Client side email Validation
				//	If not empty value, Then validate email
				if (jQuery(el).hasClass('cp-email')) {
					const email = jQuery(el).val();

					if (isValidEmailAddress(email)) {
						jQuery(el).removeClass('cp-error');
					} else {
						setTimeout(function () {
							jQuery(el).addClass('cp-error');
						}, 100);
						fail++;
						name = jQuery(el).attr('name') || '';
						/* eslint-disable no-unused-vars */
						fail_log += name + ' is required \n';
					}
				} else {
					jQuery(el).removeClass('cp-error');
				}
			}
		});

		//submit if fail count never got greater than 0
		if (fail === 0) {
			cp_form_processing_wrap.show();

			info_container.fadeOut(120, function () {
				jQuery(this).show().css({ visibility: 'hidden' });
			});

			spinner.hide().css({ visibility: 'visible' }).fadeIn(100);
			jQuery.ajax({
				url: smile_ajax.url,
				data,
				type: 'POST',
				dataType: 'HTML',
				success(result) {
					if (cookieTime) {
						createCookie(cookieName, true, cookieTime);
					}

					const obj = JSON.parse(result);
					let msg_string = '';
					let cls = '';
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
							modal,
							cf_response,
						]);
					}
					//console.log('cf_response: ' + result );

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
							"<div class='cp-admin-error-notice'>Read How to Fix This, click <a target='_blank' rel='noopener' href='https://www.convertplug.com/plus/docs/something-went-wrong/'>here</a></div>";
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
						spinner.fadeOut(100, function () {
							jQuery(this).show().css({ visibility: 'hidden' });
						});

						info_container
							.hide()
							.css({ visibility: 'visible' })
							.fadeIn(120);

						if (cls === 'success') {
							// Hide tool tip
							jQuery('head').append(
								'<style class="cp-tooltip-css">.tip.' +
									cp_tooltip +
									'{display:none }</style>'
							);

							// Redirect if status is [success]
							if (obj.action === 'redirect') {
								cp_form_processing_wrap.hide();
								const url = obj.url;
								let urlstring = '';
								if (url.indexOf('?') > -1) {
									urlstring = '&';
								} else {
									urlstring = '?';
								}

								let redirect_url =
									url + urlstring + decodeURI(query_string);
								if (Number(redirectdata) === 1) {
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
									if (String(win_open) === '') {
										document.location.href = redirect_url;
									}
								} else if (redirect_url !== '') {
									const redirect_file =
										redirect_url.split(',');
									const lt = redirect_file.length;
									let cnt = 1;
									jQuery.each(
										redirect_file,
										function (index, r_url) {
											cnt = cnt + 1;
											redirect_url = r_url;
											cp_download_file(
												redirect_url,
												cnt,
												lt
											);
										}
									);
								}
								modal.removeClass('cp-open');
								jQuery(document).trigger('closeModal', [modal]);
							} else {
								cp_form_processing_wrap.show();

								// if button contains anchor tag then redirect to that url
								if (jQuery(t).find('a').length > 0) {
									const redirect_src = jQuery(t)
										.find('a')
										.attr('href');
									let redirect_target = jQuery(t)
										.find('a')
										.attr('target');
									if (
										redirect_target === '' ||
										typeof redirect_target === 'undefined'
									) {
										redirect_target = '_self';
									}

									if (
										redirect_src !== '' ||
										redirect_src !== '#'
									) {
										window.open(
											redirect_src,
											redirect_target
										);
									}
								}

								if (form_action_on_submit === 'disappear') {
									modal.removeClass('cp-hide-inline-style');
									setTimeout(function () {
										if (modal.hasClass('cp-modal-inline')) {
											modal.addClass(
												'cp-hide-inline-style'
											);
										}
										jQuery(document).trigger('closeModal', [
											modal,
										]);
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
								!modal.hasClass('cp-do-not-close-inline')
							) {
								setTimeout(function () {
									jQuery(document).trigger('closeModal', [
										modal,
									]);
								}, 3000);
							}
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
		jQuery('.cp-modal-popup-container')
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
								modal_process_cp_form(this);
							}
						}
					});

				// submit add subscriber request
				jQuery(el)
					.find('.btn-subscribe')
					.on('click', function (e) {
						const style_id = jQuery(this)
							.parents('.global_modal_container')
							.data('modal-id');
						jQuery(el).find('.cp-input').removeClass('cp-error');
						if (!jQuery(this).hasClass('cp-disabled')) {
							modal_process_cp_form(this);
							jQuery(document).trigger('cp_conversion_done', [
								this,
								style_id,
							]);
							//	Redirect after conversion
							const redirect_link =
								jQuery(this).attr('data-redirect-link') || '';
							const redirect_link_target =
								jQuery(this).attr(
									'data-redirect-link-target'
								) || '_blank';
							if (
								redirect_link !== 'undefined' &&
								redirect_link !== ''
							) {
								if (
									navigator.userAgent
										.toLowerCase()
										.match(/(ipad|iphone)/)
								) {
									document.location = redirect_link;
								} else {
									window.open(
										redirect_link,
										redirect_link_target
									);
								}
							}
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
								modal_process_cp_form(this);
							}
						}
					});
			});
	});

	function cp_download_file(fileURL, cnt, len) {
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

		if (cnt === len) {
			jQuery('html').removeClass('cp-custom-viewport');
			jQuery('html').removeClass('cp-exceed-vieport cp-window-viewport');
		}
	}
})(jQuery);
