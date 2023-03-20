/* eslint-env jquery */
(function ($) {
	$(document).ready(function () {
		$('.cp_mb_input').on('blur', function () {
			refresh_multi_box();
		});

		refresh_multi_box();

		prevent_keypress();

		// on change events
		$(document).on(
			'change',
			'.cp_mb_select, .cp_mb_input, .cp_mb_checkbox',
			function () {
				setTimeout(function () {
					refresh_multi_box();
				}, 100);
			}
		);

		// before update starts
		function refresh_multi_box() {
			$('.multi-box-wrapper').each(function (i, wrapper) {
				const id = $(wrapper).attr('data-id');
				cp_update_multi_box(id);
			});
		}

		// click new box
		function cp_update_multi_box(multi_id) {
			const pre_id = multi_id;
			const id = 'mb-wrapper-' + multi_id;
			const $id = $('#' + id);
			let string = '';
			let email_ind = 0;
			$id.find('.multi-box').each(function (j, box) {
				let box_string = '';

				const temp_name = $(box).find('input[name=input_name]').val();
				const temp_label = $(box).find('input[name=input_label]').val();
				let temp_val = temp_label !== '' ? temp_label : temp_name;

				//	set the name field for [hidden]
				let temp_type =
					$(box).find('[name="input_type"] option:selected').val() ||
					'';
				//temp_type = temp_type.toLowerCase().replace(/\b[a-z]/g, function(letter) { return letter.toUpperCase(); });
				temp_type = temp_type
					.toLowerCase()
					.replace(/\b[a-z]/g, function (letter) {
						return letter.toUpperCase();
					});

				switch (temp_type) {
					case 'dropdown':
						temp_val = temp_name !== '' ? temp_name : '';
						break;
					case 'hidden':
						temp_val = temp_name !== '' ? temp_name : '';
						break;
					case 'Email':
						temp_val = temp_label !== '' ? temp_label : '';
						if (email_ind === 0) {
							temp_val = 'email';
						} else {
							temp_val = 'email_' + email_ind;
						}
						email_ind++;
						break;
				}
				//	concate - dash
				if (temp_val !== '') temp_val = temp_val + ' - ';

				$(box)
					.find('.accordion-head-label')
					.html(temp_val + temp_type);

				// order
				box_string += 'order->' + j + '|';

				$(box)
					.find('.cp_mb_select, .cp_mb_input')
					.each(function (i, input) {
						const v =
							$(box)
								.find('.cp_mb_select option:selected')
								.val() || '';
						if (!$(input).hasClass('skip-input') || v === 'email') {
							const name = $(input).attr('name');
							const value = $(input).val();
							box_string += name + '->' + value + '|';
						}
					});

				$(box)
					.find('.cp_mb_checkbox')
					.each(function (i, check) {
						let value = 'false';
						if ($(check).hasClass('skip-input')) return;
						const name = $(check).attr('name');
						if ($(check).is(':checked')) {
							value = 'true';
						}
						box_string += name + '->' + value + '|';
					});
				box_string = box_string.slice(0, -1); // remove | from end of string
				string += box_string + ';';
			});
			string = string.slice(0, -1); // remove ; from end of string

			//	Update email name
			//	Add name field for EMAIL
			//  Extract ALL - field
			let new_string = string;
			const all = new_string.split(';');
			let i = 0;
			$.each(all, function (index, val) {
				//  Empty Fields
				let name = '';
				let type = '';
				//  Extract SINGLE - all
				const single = val.split('|');
				$.each(single, function (e, v) {
					const s = v.split('->');
					switch (s[0]) {
						case 'input_name':
							name = s[1];
							break;
						case 'input_type':
							type = s[1];
							break;
					}
				});

				//  For ONLY email field
				if (type === 'email') {
					let email_name = 'email';
					if (i >= 1) {
						email_name = email_name + '_' + i;
					}
					new_string = new_string.replace(name, email_name);
					i++;
				}
			});

			const $input = $('#multi-box-input-' + pre_id);
			$input.val(new_string);
			$input.trigger('change');
			$(document).trigger('multiBoxUpdated', [new_string, pre_id]);
		} // cp_update_multi_box end

		// click on new box
		$('.multi-box-add-new').on('click', function () {
			const $icon = $(this).find('i');
			$icon.addClass('rotating');
			const box_wrapper = $(this)
				.find('i')
				.parents('.multi-box-wrapper:first');
			const uniq = $(box_wrapper).attr('data-id');

			const buildData = {
				action: 'repeat_multi_box',
				id: uniq,
				security_nonce: jQuery('#cp_multi_box_nonce').val(),
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
					.find('.multi-box-inner')
					.append(result.message);
				$(document).trigger('multiBoxAdded', [new_box]);
				$(document).trigger('refreshDependancy');
			});
		}); // add new click event

		// on click delete box
		$(document).on('click', '.multi-box-delete', function (event) {
			event.preventDefault();
			const box = $(this).parents('.multi-box:first');
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
						$(box).slideUp(300);
						setTimeout(function () {
							$(box).remove();
							refresh_multi_box();
							//$(".sweet-overlay, .sweet-alert").fadeOut('slow').remove();
						}, 350);
					}
				}
			);
		});

		// on click accordion head toggle
		$(document).on('click', '.toggle-accordion-head', function () {
			const box = $(this).parents('.multi-box:first');
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
		$(document).on('refreshDependancy', function () {
			setTimeout(function () {
				$('select[name=input_type]').each(function (i, select) {
					refresh_dependancy(select);
				});

				//	Reinitialize ToolTip
				$('.has-tip').frosty({
					offset: 10,
				});

				//	Update input values
				refresh_multi_box();
				prevent_keypress();
			}, 150);
		});
		$(document).trigger('refreshDependancy');

		// update dependancy on input type change
		$(document).on('change', 'select[name=input_type]', function () {
			$(document).trigger('onInputTypeChanged', this);
			refresh_dependancy(this);
		});

		// custom procedure on input types like hidden, dropdown, placeholder
		function refresh_dependancy(select) {
			const box = $(select).parents('.multi-box:first');
			const val = $(select).val();
			let hidden_dependant_array_to_hide, hidden_dependant_array_to_show;
			$(box)
				.find('.cp_mb_select, .cp_mb_input, .cp_mb_checkbox')
				.removeClass('skip-input');

			if (val === 'hidden') {
				hidden_dependant_array_to_hide = [
					'input[name=input_require]',
					'textarea[name=dropdown_options]',
					'input[name=input_placeholder]',
					'input[name=input_label]',
				];
				hidden_dependant_array_to_show = [
					'input[name=hidden_value]',
					'input[name=input_name]',
				];

				$(box).find('input[name=input_require]').attr('checked', false);
				$.each(hidden_dependant_array_to_hide, function (i, ele) {
					$(box).find(ele).parents('.multi-box-field').slideUp(300);
					$(box).find(ele).addClass('skip-input'); // skip input value to add to string
				});

				$.each(hidden_dependant_array_to_show, function (i, show_ele) {
					$(box)
						.find(show_ele)
						.parents('.multi-box-field')
						.slideDown(300);
				});
			} else if (val === 'googlerecaptcha') {
				hidden_dependant_array_to_hide = [
					'textarea[name=dropdown_options]',
					'input[name=input_placeholder]',
					'input[name=input_label]',
					'input[name=row_value]',
					'input[name=hidden_value]',
				];
				hidden_dependant_array_to_show = [
					'input[name=input_require]',
					'input[name=input_name]',
				];

				$.each(hidden_dependant_array_to_hide, function (i, ele) {
					$(box).find(ele).parents('.multi-box-field').slideUp(300);
					$(box).find(ele).addClass('skip-input'); // skip input value to add to string
				});

				$.each(hidden_dependant_array_to_show, function (i, show_ele) {
					$(box)
						.find(show_ele)
						.parents('.multi-box-field')
						.slideDown(300);
				});
			} else if (val === 'dropdown') {
				const dropdown_dependant_array_to_hide = [
					'input[name=hidden_value]',
					'input[name=input_placeholder]',
				];
				const dropdown_dependant_array_to_show = [
					'textarea[name=dropdown_options]',
					'input[name=input_require]',
				];

				$.each(dropdown_dependant_array_to_hide, function (i, ele) {
					$(box).find(ele).parents('.multi-box-field').slideUp(300);
					$(box).find(ele).addClass('skip-input'); // skip input value to add to string
				});

				$.each(
					dropdown_dependant_array_to_show,
					function (i, show_ele) {
						$(box)
							.find(show_ele)
							.parents('.multi-box-field')
							.slideDown(300);
					}
				);
			} else {
				const dependant_array_to_hide = [
					'input[name=hidden_value]',
					'textarea[name=dropdown_options]',
				];
				const dependant_array_to_show = [
					'input[name=input_require]',
					'input[name=input_label]',
				];
				if (val === 'textarea') {
					// placeholder
					dependant_array_to_show.push('input[name=row_value]');
				} else {
					dependant_array_to_hide.push('input[name=row_value]');
				}

				if (
					val === 'textarea' ||
					val === 'textfield' ||
					val === 'email' ||
					val === 'number'
				) {
					// placeholder
					dependant_array_to_show.push(
						'input[name=input_placeholder]'
					);
				} else {
					dependant_array_to_hide.push(
						'input[name=input_placeholder]'
					);
				}

				//	hide name field for email
				if (val === 'email') {
					dependant_array_to_hide.push('input[name=input_name]');
				} else {
					dependant_array_to_show.push('input[name=input_name]');
				}

				$.each(dependant_array_to_hide, function (i, ele) {
					$(box).find(ele).parents('.multi-box-field').slideUp(300);
					$(box).find(ele).addClass('skip-input'); // skip input value to add to string
				});

				$.each(dependant_array_to_show, function (i, show_ele) {
					$(box)
						.find(show_ele)
						.parents('.multi-box-field')
						.slideDown(300);
				});

				//$(box).find('input[name=hidden_value]').val('');
			}
		} // refresh dependancy

		// sortable script
		$('.multi-box-inner').sortable({
			items: '.multi-box',
			//handle: '.multi-box-handle',
			//placeholder: 'ui-state-highlight',
			opacity: 0.5,
			cursor: 'pointer',
			axis: 'y',
			/*start: function(event, ui) {
				ui.placeholder.html(ui.item.html());
			},*/
			update() {
				refresh_multi_box();
			},
		}); // sortable script

		$('body').on('click', '.cp-discard-popup', function (e) {
			e.preventDefault();
			$('.sweet-overlay, .sweet-alert').fadeOut('slow').remove();
		});
	});

	function prevent_keypress() {
		$('input[type=text]').on('keypress', function (event) {
			if (event.keyCode === 13) {
				event.preventDefault();
			}
		});
	}
})(jQuery);
