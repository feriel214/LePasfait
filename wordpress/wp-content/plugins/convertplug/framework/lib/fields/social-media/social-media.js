/* eslint-env jquery */
(function ($) {
	$(document).ready(function () {
		// on change events
		$(document).on(
			'change',
			'.cp_sm_select_action, .cp_sm_select, .cp_sm_input, .cp_sm_checkbox ',
			function () {
				refresh_social_media();
			}
		);

		// before update starts
		function refresh_social_media() {
			$('.social-media-wrapper').each(function (i, wrapper) {
				const id = $(wrapper).attr('data-id');
				setTimeout(function () {
					cp_social_social_media(id);
				}, 100);
			});
		}

		// click new box
		function cp_social_social_media(social_id) {
			const pre_id = social_id;
			const id = 'cp-wrapper-' + social_id;
			const $id = $('#' + id);
			let string = '';

			$id.find('.social-media').each(function (j, box) {
				let box_string = '';

				const temp_name = $(box).find('input[name=input_share]').val();
				const temp_label = $(box).find('.cp_sm_select').val();
				const temp_val = temp_label !== '' ? temp_label : temp_name;
				$(box).find('.accordion-head-label').html(temp_val);

				// order
				box_string += 'order:' + j + '|';

				$(box)
					.find('.cp_sm_select, .cp_sm_input ,.cp_sm_select_action ')
					.each(function (i, input) {
						if ($(input).hasClass('skip-input')) {
							return;
						}
						const name = $(input).attr('name');
						let value = $(input).val();
						if (value.indexOf(':') >= 0) {
							value = encodeURIComponent(value);
						}
						box_string += name + ':' + value + '|';
					});

				$(box)
					.find('.cp_sm_checkbox')
					.each(function (i, check) {
						let value = 'false';
						if ($(check).hasClass('skip-input')) return;
						const name = $(check).attr('name');
						if ($(check).is(':checked')) {
							value = 'true';
						}
						box_string += name + ':' + value + '|';
					});
				box_string = box_string.slice(0, -1); // remove | from end of string
				string += box_string + ';';
			});
			string = string.slice(0, -1); // remove ; from end of string

			const $input = $('#social-media-input-' + pre_id);
			$input.val(string);
			$(document).trigger('socialMediaUpdated', [string, pre_id]);
		} // cp_social_social_media end

		// click on new box
		$('.social-media-add-new').on('click', function () {
			const $icon = $(this).find('i');
			$icon.addClass('rotating');
			const box_wrapper = $(this)
				.find('i')
				.parents('.social-media-wrapper:first');
			const uniq = $(box_wrapper).attr('data-id');
			const buildData = {
				action: 'repeat_social_media',
				id: uniq,
				security: jQuery('#cp_social_media_nonce').val(),
			};

			$.post(ajaxurl, buildData, function (response) {
				$icon.removeClass('rotating');
				const result = JSON.parse(response);
				if (result.type === 'undefined') {
					result.log('Incorrect response');
					return false;
				}
				if (result.type === 'error') {
					return false;
				}
				const new_box = $(box_wrapper)
					.find('.social-media-inner')
					.append(result.message);
				$(document).trigger('socialMediaAdded', [new_box]);
				$(document).trigger('refreshSocialDependancy');
			});
		}); // add new click event

		// on click delete box
		$(document).on('click', '.social-media-delete', function (event) {
			event.preventDefault();
			const box = $(this).parents('.social-media:first');
			swal(
				{
					title: 'Are you sure?',
					text: 'Do you really want to delete this field?<span class="cp-discard-popup" style="position: absolute;top: 0;right: 0;"><i class="connects-icon-cross"></i></span>',
					type: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#DD6B55',
					confirmButtonText: 'Yes, delete it!',
					cancelButtonText: 'No, cancel it!',
					closeOnConfirm: true,
					closeOnCancel: true,
					showLoaderOnConfirm: true,
					customClass: 'cp-confirm-delete-box',
					html: true,
				},
				function (isConfirm) {
					if (isConfirm) {
						$(box).slideUp(100);
						setTimeout(function () {
							$(box).remove();
							refresh_social_media();
						}, 350);
					}
				}
			);
		});

		// on click accordion head toggle
		$(document).on('click', '.toggle-accordion-head', function () {
			const box = $(this).parents('.social-media:first');
			$(box)
				.find('.toggle-accordion-content')
				.slideToggle(250, function () {
					if ($(box).hasClass('active')) {
						$(box).removeClass('active');
					} else {
						$(box).addClass('active');
					}
				});
		});

		// update dependancy on document ready
		$(document).on('refreshSocialDependancy', function () {
			setTimeout(function () {
				$('select[name=input_type]').each(function (i, select) {
					refresh_social_dependancy(select);
				});
				$('select[name=input_action]').each(function (i, select) {
					refresh_social_dependancy(select);
				});

				$('input[name=smile_adv_share_opt]').each(function (i, select) {
					refresh_social_dependancy(select);
				});

				//	Reinitialize ToolTip
				$('.has-tip').frosty({
					offset: 10,
				});

				refresh_social_media();
			}, 150);
		});

		// update dependancy on input type change
		$(document).on('change', 'select[name=input_type]', function () {
			$(document).trigger('onInputTypeChanged', this);
			refresh_social_dependancy(this);
		});
		$(document).on('change', 'select[name=input_action]', function () {
			$(document).trigger('onInputActionChanged', this);
			refresh_social_dependancy(this);
		});

		$(document).on(
			'change',
			'input[name=smile_adv_share_opt]',
			function () {
				$(document).trigger('onShareUrlChanged', this);
				refresh_social_dependancy(this);
			}
		);

		// custom procedure on input types like hidden, dropdown, placeholder

		function refresh_social_dependancy(select) {
			const box = $(select).parents('.social-media:first');
			const val = $(select).val();
			$(box)
				.find(
					'.cp_sm_select_action, .cp_sm_select, .cp_sm_input, .cp_sm_checkbox'
				)
				.removeClass('skip-input');
			let inputtype = $(box).find('.cp_sm_select').val();

			if (inputtype === 'Instagram') {
				$(box)
					.find('.cp_sm_select_action option[value=follow]')
					.removeClass('cp-hide-share');
				$(box)
					.find('.cp_sm_select_action option[value=social_sharing]')
					.addClass('cp-hide-share');
			} else {
				$(box)
					.find('.cp_sm_select_action option[value=follow]')
					.addClass('cp-hide-share');
				$(box)
					.find('.cp_sm_select_action option[value=social_sharing]')
					.removeClass('cp-hide-share');
			}

			if (
				($(box).find('select[name^="input_action"]').val() !==
					'social_sharing' ||
					$(box).find('select[name^="input_action"]').val() ===
						'follow') &&
				inputtype !== 'Instagram'
			) {
				$(box)
					.find(
						'select[name^="input_action"] option[selected="selected"]'
					)
					.removeAttr('selected');
				$(box)
					.find(
						'select[name^="input_action"] option[value="profile_link"]'
					)
					.attr('selected', 'selected');
				$(box)
					.find('input[name="profile_link"]')
					.parents('.social-media-field')
					.addClass('cp-show-profile');
			} else {
				$(box)
					.find('input[name="profile_link"]')
					.parents('.social-media-field')
					.removeClass('cp-show-profile');
			}

			inputtype = $(box).find('.cp_sm_select').val();
			let hidden_dependant_array_to_hide,
				hidden_dependant_array_to_show,
				show_url,
				dropdown_dependant_array_to_hide,
				dropdown_dependant_array_to_show,
				show_action;
			if (val === 'profile_link') {
				hidden_dependant_array_to_hide = [
					'input[name=input_share]',
					'input[name=smile_adv_share_opt]',
					'input[name=input_img]',
				];
				hidden_dependant_array_to_show = ['input[name=profile_link]'];

				$.each(hidden_dependant_array_to_hide, function (i, ele) {
					$(box)
						.find(ele)
						.parents('.social-media-field')
						.slideUp(100);
					$(box).find(ele).addClass('skip-input'); // skip input value to add to string
				});

				$.each(hidden_dependant_array_to_show, function (i, show_ele) {
					$(box)
						.find(show_ele)
						.parents('.social-media-field')
						.slideDown(100);
				});
			} else if (val === 'social_sharing') {
				show_url = $(box).find('.smile-switch-input').val();

				dropdown_dependant_array_to_hide = [
					'input[name=profile_link]',
					'input[name=input_share]',
				];

				if (show_url === '1') {
					dropdown_dependant_array_to_show = [
						'input[name=input_share]',
						'input[name=smile_adv_share_opt]',
					];
				} else {
					dropdown_dependant_array_to_show = [
						'input[name=smile_adv_share_opt]',
					];
				}

				$.each(dropdown_dependant_array_to_hide, function (i, ele) {
					$(box)
						.find(ele)
						.parents('.social-media-field')
						.slideUp(100);
					$(box).find(ele).addClass('skip-input'); // skip input value to add to string
				});

				$.each(
					dropdown_dependant_array_to_show,
					function (i, show_ele) {
						$(box)
							.find(show_ele)
							.parents('.social-media-field')
							.slideDown(100);
					}
				);
			} else if (val === 'follow') {
				hidden_dependant_array_to_hide = [
					'input[name=input_share]',
					'input[name=smile_adv_share_opt]',
					'input[name=profile_link]',
				];
				hidden_dependant_array_to_show = [
					//'input[name=profile_link]'
				];

				$.each(hidden_dependant_array_to_hide, function (i, ele) {
					$(box)
						.find(ele)
						.parents('.social-media-field')
						.slideUp(100);
					$(box).find(ele).addClass('skip-input'); // skip input value to add to string
				});

				$.each(hidden_dependant_array_to_show, function (i, show_ele) {
					$(box)
						.find(show_ele)
						.parents('.social-media-field')
						.slideDown(100);
				});
			} else if (val === '1') {
				show_url = $(box).find('.smile-switch-input').val();
				dropdown_dependant_array_to_hide = ['input[name=profile_link]'];
				dropdown_dependant_array_to_show = [
					'input[name=input_share]',
					'input[name=smile_adv_share_opt]',
				];

				$.each(dropdown_dependant_array_to_hide, function (i, ele) {
					$(box)
						.find(ele)
						.parents('.social-media-field')
						.slideUp(100);
					$(box).find(ele).addClass('skip-input'); // skip input value to add to string
				});

				$.each(
					dropdown_dependant_array_to_show,
					function (i, show_ele) {
						$(box)
							.find(show_ele)
							.parents('.social-media-field')
							.slideDown(100);
					}
				);
			} else if (val === '0') {
				show_action = $(box).find('.cp_sm_select_action').val();
				if (show_action === 'profile_link') {
					dropdown_dependant_array_to_hide = [
						'input[name=input_share]',
						'input[name=input_img]',
						'input[name=smile_adv_share_opt]',
					];
					dropdown_dependant_array_to_show = [
						'input[name=profile_link]',
					];
				} else if (show_action === 'follow') {
					dropdown_dependant_array_to_hide = [
						'input[name=input_share]',
						'input[name=input_img]',
						'input[name=smile_adv_share_opt]',
						'input[name=profile_link]',
					];
					dropdown_dependant_array_to_show = [
						'input[name=profile_link]',
					];
				} else {
					dropdown_dependant_array_to_hide = [
						'input[name=profile_link]',
						'input[name=input_share]',
					];
					dropdown_dependant_array_to_show = [
						'input[name=smile_adv_share_opt]',
					];
				}

				$.each(dropdown_dependant_array_to_hide, function (i, ele) {
					$(box)
						.find(ele)
						.parents('.social-media-field')
						.slideUp(100);
					$(box).find(ele).addClass('skip-input'); // skip input value to add to string
				});

				$.each(
					dropdown_dependant_array_to_show,
					function (i, show_ele) {
						$(box)
							.find(show_ele)
							.parents('.social-media-field')
							.slideDown(100);
					}
				);
			} else if (val === 'Instagram') {
				hidden_dependant_array_to_hide = [
					'input[name=input_share]',
					'input[name=smile_adv_share_opt]',
					//'option[value=social_sharing]'
				];
				hidden_dependant_array_to_show = ['input[name=profile_link]'];
				$.each(hidden_dependant_array_to_hide, function (i, ele) {
					$(box)
						.find(ele)
						.parents('.social-media-field')
						.slideUp(100);
					$(box).find(ele).addClass('skip-input'); // skip input value to add to string
				});
				$.each(hidden_dependant_array_to_show, function (i, show_ele) {
					$(box)
						.find(show_ele)
						.parents('.social-media-field')
						.slideDown(100);
				});
				if (
					$(box).find('select[name^="input_action"]').val() !==
					'follow'
				) {
					$(box)
						.find(
							'select[name^="input_action"] option[selected="selected"]'
						)
						.removeAttr('selected');
					$(box)
						.find(
							'select[name^="input_action"] option[value="profile_link"]'
						)
						.attr('selected', 'selected');
				}
			} else if (val === 'Pinterest') {
				show_url = $(box).find('.smile-switch-input').val();
				show_action = $(box).find('.cp_sm_select_action').val();
				dropdown_dependant_array_to_hide = [
					'input[name=profile_link]',
					'input[name=input_share]',
				];

				if (show_url === '1' && show_action !== 'profile_link') {
					dropdown_dependant_array_to_show = [
						'input[name=input_share]',
						'input[name=smile_adv_share_opt]',
						'input[name=input_img]',
					];
				} else {
					dropdown_dependant_array_to_show = [
						'input[name=smile_adv_share_opt]',
					];
				}
				$.each(dropdown_dependant_array_to_hide, function (i, ele) {
					$(box)
						.find(ele)
						.parents('.social-media-field')
						.slideUp(100);
					$(box).find(ele).addClass('skip-input'); // skip input value to add to string
				});
				$.each(
					dropdown_dependant_array_to_show,
					function (i, show_ele) {
						$(box)
							.find(show_ele)
							.parents('.social-media-field')
							.slideDown(100);
					}
				);
			} else if (val === 'Facebook') {
				dropdown_dependant_array_to_show = [
					'input[name=input_share]',
					'input[name=smile_adv_share_opt]',
					'input[name=fb-access-token]',
				];

				$.each(
					dropdown_dependant_array_to_show,
					function (i, show_ele) {
						$(box)
							.find(show_ele)
							.parents('.social-media-field')
							.slideDown(100);
					}
				);
			}

			if (inputtype !== 'Facebook') {
				dropdown_dependant_array_to_hide = [
					'input[name=fb-access-token]',
				];

				$.each(dropdown_dependant_array_to_hide, function (i, ele) {
					$(box)
						.find(ele)
						.parents('.social-media-field')
						.slideUp(100);
					$(box).find(ele).addClass('skip-input'); // skip input value to add to string
				});
			}
			if (inputtype !== 'Pinterest') {
				dropdown_dependant_array_to_hide = ['input[name=input_img]'];

				$.each(dropdown_dependant_array_to_hide, function (i, ele) {
					$(box)
						.find(ele)
						.parents('.social-media-field')
						.slideUp(100);
					$(box).find(ele).addClass('skip-input'); // skip input value to add to string
				});
			} else {
				show_action = $(box).find('.cp_sm_select_action').val();
				if (show_action !== 'profile_link') {
					dropdown_dependant_array_to_show = [
						'input[name=input_img]',
					];
					$.each(
						dropdown_dependant_array_to_show,
						function (i, show_ele) {
							$(box)
								.find(show_ele)
								.parents('.social-media-field')
								.slideDown(100);
						}
					);
				} else {
					dropdown_dependant_array_to_hide = [
						'input[name=smile_adv_share_opt]',
					];
					dropdown_dependant_array_to_show = [
						'input[name=profile_link]',
					];
					$.each(
						dropdown_dependant_array_to_show,
						function (i, show_ele) {
							$(box)
								.find(show_ele)
								.parents('.social-media-field')
								.slideDown(100);
						}
					);
					$.each(dropdown_dependant_array_to_hide, function (i, ele) {
						$(box)
							.find(ele)
							.parents('.social-media-field')
							.slideUp(100);
						$(box).find(ele).addClass('skip-input'); // skip input value to add to string
					});
				}
			}
		} // refresh dependancy

		// sortable script
		$('.social-media-inner').sortable({
			items: '.social-media',
			//handle: '.social-media-handle',
			opacity: 0.5,
			cursor: 'pointer',
			axis: 'y',
			update() {
				refresh_social_media();
			},
		}); // sortable script

		$('body').on('click', '.cp-discard-popup', function (e) {
			e.preventDefault();
			$('.sweet-overlay, .sweet-alert').fadeOut('slow').remove();
		});
	});
})(jQuery);
