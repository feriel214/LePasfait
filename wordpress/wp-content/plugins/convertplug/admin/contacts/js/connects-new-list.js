/* eslint-env jquery */
(function ($) {
	'use strict';
	let provider = jQuery('#bsf-cnlist-list-provider');
	let connect_url = '';
	$(document).ready(function () {
		connect_url = jQuery('#cp-connect-url').val();
		let val = provider.length
			? provider.val().toLowerCase()
			: 'convert plug';

		if (cplus_connect_list.addons_lists.length !== 0) {
			jQuery('#save-btn').attr('data-provider', val);
			provider.on('change', function () {
				if (jQuery(this).val() === 'Convert Plug') {
					jQuery('.bsf-cnlist-save-btn').show();
					jQuery('.bsf-cnlist-next-btn').hide();
					jQuery('#save-btn').removeAttr('disabled');
				} else {
					jQuery('.bsf-cnlist-save-btn').hide();
					jQuery('#save-btn').attr('disabled', 'disabled');
					jQuery('.bsf-cnlist-next-btn').show();
				}
			});
		}

		const step = cpcpGetUrlVars().step;
		if (step === '2') {
			jQuery('.smile-absolute-loader').css('visibility', 'visible');
			jQuery('#bsf-cnlist-list-name').removeClass('has-error');

			jQuery('.bsf-cnlist-provider-description').fadeOut(300);
			val = cpcpGetUrlVars()['list-provider'];

			jQuery('#save-btn').attr('data-provider', val);

			jQuery('#save-btn').attr('disabled', 'true');
			const action = 'get_' + val + '_data';
			const data = 'action=' + action;

			jQuery.ajax({
				url: ajaxurl,
				data,
				method: 'POST',
				dataType: 'JSON',
				success(result) {
					if (result.isconnected) {
						jQuery('.bsf-cnlist-mailer-help').hide();
					} else if (
						typeof result.helplink !== 'undefined' &&
						result.helplink !== ''
					) {
						jQuery('.bsf-cnlist-mailer-help').show();
						jQuery('.bsf-cnlist-mailer-help a').attr(
							'href',
							result.helplink
						);
					} else {
						jQuery('.bsf-cnlist-mailer-help').hide();
					}

					if (val === 'convertfox') {
						jQuery('.bsf-cnlist-mailer-help').show();
						jQuery('.bsf-cnlist-mailer-help').css('top', '10px');
					}

					jQuery('.bsf-cnlist-mailer-help a').attr(
						'href',
						result.helplink
					);
					jQuery('.bsf-cnlist-mailer-data').html(result.data);
					jQuery('.bsf-cnlist-mailer-data').slideDown(300);
					jQuery('.smile-absolute-loader').css(
						'visibility',
						'hidden'
					);

					setTimeout(function () {
						jQuery('.bsf-cnlist-form-wizard.step-1').css(
							'transform',
							'translateX(-100px)'
						);
					}, 800);

					setTimeout(function () {
						jQuery('.bsf-cnlist-form-wizard.step-1').removeClass(
							'active in'
						);
						jQuery('.bsf-cnlist-form-wizard.step-2')
							.addClass('in active')
							.css('transform', 'translateX(0px)');
					}, 1200);

					if (jQuery('#' + val + '-list').length > 0) {
						jQuery('#save-btn').removeAttr('disabled');
					}
					jQuery('.select2-infusionsoft-list').select2();
					jQuery('.wizard-prev').removeClass('disabled');
				},
			});
		}
	});

	jQuery(document).on('click', '.update-mailer', function () {
		jQuery('.bsf-cnlist-mailer-data input[type="text"]').val('');
		jQuery(this).replaceWith(
			'<button id="auth-' +
				jQuery(this).attr('data-mailer') +
				'" class="button button-secondary auth-button" disabled="true">Authenticate</button><span class="spinner" style="float: none;"></span>'
		);
	});

	jQuery('#save-btn').on('click', function (e) {
		e.preventDefault();

		if (jQuery('#bsf-cnlist-list-name').val() === '') {
			jQuery('html, body').animate(
				{
					scrollTop:
						jQuery('.bsf-cnlist-list-name').offset().top - 100,
				},
				500
			);
			jQuery('#bsf-cnlist-list-name').trigger('focus');
			jQuery('#bsf-cnlist-list-name').addClass(
				'connect-new-list-required'
			);
			return false;
		}

		let is_campaign_exists = false;
		const campaignName = jQuery('#bsf-cnlist-list-name').val();

		jQuery.ajax({
			url: ajaxurl,
			data: {
				campaign: campaignName,
				action: 'is_campaign_exists',
			},
			async: false,
			method: 'POST',
			dataType: 'JSON',
			success(result) {
				if (result.status === 'error') {
					jQuery('.cp-validation-error').show();
					jQuery('.cp-validation-error').html(result.message);
					is_campaign_exists = true;
				} else {
					jQuery('.cp-validation-error').html('');
				}
			},
		});

		if (is_campaign_exists) {
			return false;
		}
		let data;
		if (cplus_connect_list.addons_lists.length !== 0) {
			data = jQuery('#bsf-cnlist-contact-form').serialize();
		} else {
			data =
				jQuery('#bsf-cnlist-contact-form').serialize() +
				'&list-provider=Convert+Plug';
		}

		provider = jQuery(this).data('provider');
		let mailer_list_id = '',
			mailer_list_name = '';
		if (provider === 'madmimi') {
			mailer_list_name = jQuery(
				'#' + provider + '-list option:selected'
			).text();
			mailer_list_id = jQuery(
				'#' + provider + '-list option:selected'
			).text();
			data +=
				'&list=' +
				mailer_list_id +
				'&provider_list=' +
				mailer_list_name;
		} else if (provider === 'sendy') {
			mailer_list_name = jQuery('#sendy_list_ids').val();
			mailer_list_id = jQuery('#sendy_list_ids').val();
			data +=
				'&list=' +
				mailer_list_id +
				'&provider_list=' +
				mailer_list_name;
		} else if (provider === 'infusionsoft') {
			const lists_arr = new Array();
			let selected_id = '';
			let name = '';
			if (
				jQuery('#' + provider + '-list option:selected').text() !== ''
			) {
				jQuery('#' + provider + '-list option:selected').each(
					function () {
						selected_id = jQuery(this).val();
						name = jQuery(this).text();
						lists_arr.push(
							'{"' + selected_id + '" : "' + name + '"}'
						);
					}
				);
			} else {
				selected_id = -1;
				name = -1;
				lists_arr.push('{"' + selected_id + '" : "' + name + '"}');
			}
			mailer_list_id = JSON.stringify(lists_arr);
			mailer_list_name = JSON.stringify(lists_arr);

			const infusionsoft_action_id = jQuery(
				'#infusionsoft_action_id'
			).val();
			data +=
				'&list=' +
				mailer_list_id +
				'&provider_list=' +
				mailer_list_name +
				'&infusionsoft_action_id=' +
				infusionsoft_action_id;
		} else if (provider === 'ontraport') {
			mailer_list_id = jQuery(
				'#' + provider + '-list option:selected'
			).val();
			mailer_list_name = jQuery(
				'#' + provider + '-list option:selected'
			).text();
			data +=
				'&list=' +
				mailer_list_id +
				'&provider_list=' +
				mailer_list_name;
		} else {
			mailer_list_id = jQuery('#' + provider + '-list ').val();
			mailer_list_name = jQuery(
				'#' + provider + '-list option:selected'
			).text();
			data +=
				'&list=' +
				mailer_list_id +
				'&provider_list=' +
				mailer_list_name;
		}

		const loading = jQuery(this).next('.spinner');
		loading.css('visibility', 'visible');
		jQuery.ajax({
			url: ajaxurl,
			data,
			method: 'POST',
			dataType: 'JSON',
			success(result) {
				if (result.status === 'error') {
					jQuery('.cp-validation-error').show();
					jQuery('.cp-validation-error').html(result.message);
					return false;
				}
				jQuery('.cp-validation-error').html('');

				if (result.message === 'added') {
					swal({
						title: 'Added!',
						text: 'The campaign you just created, is added to the list.',
						type: 'success',
						timer: 2000,
						showConfirmButton: false,
					});
				} else {
					swal({
						title: 'Error!',
						text: 'Error adding the campaign to the list. Please try again.',
						type: 'error',
						timer: 2000,
						showConfirmButton: false,
					});
				}
				setTimeout(function () {
					document.location = 'admin.php?page=contact-manager';
				}, 600);
			},
			error() {
				swal({
					title: 'Error!',
					text: 'Error adding the campaign to the list. Please try again.',
					type: 'error',
					timer: 2000,
					showConfirmButton: false,
				});
			},
		});
	});

	/************** JQuery change events *************/

	jQuery(document).on('click', '.wizard-next', function () {
		if (jQuery('#bsf-cnlist-list-name').val() === '') {
			jQuery('#bsf-cnlist-list-name').addClass(
				'connect-new-list-required'
			);
			jQuery('#bsf-cnlist-list-name').trigger('focus');
			return false;
		}
		let is_campaign_exists = false;
		const campaignName = jQuery('#bsf-cnlist-list-name').val();
		jQuery.ajax({
			url: ajaxurl,
			data: {
				campaign: campaignName,
				action: 'is_campaign_exists',
				security_nonce: cplus_connect_list.is_campaign_exists_nonce,
			},
			async: false,
			method: 'POST',
			dataType: 'JSON',
			success(result) {
				if (result.status === 'error') {
					jQuery('.cp-validation-error').show();
					jQuery('.cp-validation-error').html(result.message);
					is_campaign_exists = true;
				} else {
					jQuery('.cp-validation-error').html('');
				}
			},
		});

		if (is_campaign_exists) {
			return false;
		}

		jQuery('.smile-absolute-loader').css('visibility', 'visible');
		jQuery('#bsf-cnlist-list-name').removeClass('has-error');
		jQuery(this).addClass('disabled');
		jQuery('.wizard-prev').removeClass('disabled');
		jQuery('.bsf-cnlist-save-btn').show();
		jQuery('.wizard-next').hide();

		jQuery('.bsf-cnlist-provider-description').fadeOut(300);
		const val = jQuery('#bsf-cnlist-list-provider').val().toLowerCase();
		jQuery('#save-btn').attr('data-provider', val);

		jQuery('#save-btn').attr('disabled', 'true');
		const action = 'get_' + val + '_data';
		const data = 'action=' + action;

		jQuery.ajax({
			url: ajaxurl,
			data,
			method: 'POST',
			dataType: 'JSON',
			success(result) {
				if (result.isconnected) {
					jQuery('.bsf-cnlist-mailer-help').hide();
				} else if (
					typeof result.helplink !== 'undefined' &&
					result.helplink !== ''
				) {
					jQuery('.bsf-cnlist-mailer-help').show();
					jQuery('.bsf-cnlist-mailer-help a').attr(
						'href',
						result.helplink
					);
				} else {
					jQuery('.bsf-cnlist-mailer-help').hide();
				}

				if (val === 'convertfox') {
					jQuery('.bsf-cnlist-mailer-help').show();
					jQuery('.bsf-cnlist-mailer-help').css('top', '10px');
				}

				jQuery('.bsf-cnlist-mailer-data').html(result.data);
				jQuery('.bsf-cnlist-mailer-data').slideDown(300);
				jQuery('.smile-absolute-loader').css('visibility', 'hidden');

				setTimeout(function () {
					jQuery('.bsf-cnlist-form-wizard.step-1').css(
						'transform',
						'translateX(-100px)'
					);
				}, 800);

				setTimeout(function () {
					jQuery('.bsf-cnlist-form-wizard.step-1').removeClass(
						'active in'
					);
					jQuery('.bsf-cnlist-form-wizard.step-2')
						.addClass('in active')
						.css('transform', 'translateX(0px)');

					const params =
						'&step=2&list-provider=' +
						val +
						'&campaign=' +
						campaignName;
					const push_state_url = connect_url + params;

					window.history.pushState(
						'connect_url',
						'Connects',
						push_state_url
					);
				}, 1200);

				if (jQuery('#' + val + '-list').length > 0) {
					jQuery('#save-btn').removeAttr('disabled');
				}
				jQuery('.select2-infusionsoft-list').select2();
			},
		});
	});

	jQuery(document).on('click', '.wizard-prev', function () {
		if (!jQuery(this).hasClass('disabled')) {
			setTimeout(function () {
				jQuery('.bsf-cnlist-form-wizard.step-2').css(
					'transform',
					'translateX(-100px)'
				);
			}, 200);

			setTimeout(function () {
				jQuery('.bsf-cnlist-form-wizard.step-2').removeClass(
					'active in'
				);
				jQuery('.bsf-cnlist-form-wizard.step-1')
					.addClass('in active')
					.css('transform', 'translateX(0px)');
				jQuery('.wizard-next').removeClass('disabled');
				jQuery('.wizard-prev').addClass('disabled');
				jQuery('.bsf-cnlist-save-btn').hide();
				jQuery('.wizard-next').show();
				jQuery('.bsf-cnlist-next-btn').show();

				const list_provider = cpcpGetUrlVars()['list-provider'];
				if (typeof list_provider !== 'undefined') {
					jQuery('#bsf-cnlist-list-provider').val(list_provider);
				}

				const params = '&step=1';
				const push_state_url = connect_url + params;

				window.history.pushState(
					'connect_url',
					'Connects',
					push_state_url
				);
			}, 600);
		}
	});

	jQuery(document).on(
		'keyup change keydown',
		'#bsf-cnlist-list-name',
		function () {
			if (jQuery(this).val() !== '') {
				jQuery(this).removeClass('connect-new-list-required');
			}
		}
	);

	function cpcpGetUrlVars() {
		const vars = {};
		window.location.href.replace(
			/[?&]+([^=&]+)=([^&]*)/gi,
			function (m, key, value) {
				vars[key] = value;
			}
		);
		return vars;
	}
})(jQuery);
