/* eslint-env jquery */
// Debug.
jQuery(document).ready(function () {
	jQuery('.cp-new-style-link').on('click', function (e) {
		e.preventDefault();
		e.stopPropagation();
		const src = jQuery(this).attr('href');
		const $this = jQuery(this);

		swal(
			{
				title: 'What would you like to do with current Template ?',
				text: "<span class='cp-discard-popup' style='position: absolute;top: 0;right: 0;'><i class='connects-icon-cross'></i></span>",
				type: 'warning',
				html: true,
				showCancelButton: true,
				confirmButtonColor: '#DD6B55',
				confirmButtonText: 'DELETE IT',
				cancelButtonText: 'SAVE IT',
				closeOnConfirm: false,
				closeOnCancel: true,
				showLoaderOnConfirm: true,
				customClass: 'cp-switch-theme',
			},
			function (isConfirm) {
				const section = jQuery('.cp-section.active');
				if (isConfirm) {
					jQuery(document).trigger('deleteStyle', [$this, false]);
					section.trigger('click');
					setTimeout(function () {
						window.location = src;
					}, 500);
				} else {
					const smile_panel = jQuery('.customize').data('style');
					jQuery('#button-save-' + smile_panel + ' > span').trigger(
						'click'
					);
					section.trigger('click');
					setTimeout(function () {
						window.location = src;
					}, 500);
				}
			}
		);
		jQuery('.cp-switch-theme')
			.prev()
			.css('background-color', 'rgba(0,0,0,.9)');
		jQuery('body').on(
			'click',
			'.cp-switch-theme .cp-discard-popup',
			function (event) {
				event.preventDefault();
				jQuery('.sweet-overlay, .sweet-alert').fadeOut('slow').remove();
			}
		);
	});

	const cp_desing_form = document.getElementById('cp-designer-form');
	if (null !== cp_desing_form) {
		Ps.initialize(cp_desing_form);
		jQuery(document).on('focusElementChanged', function () {
			Ps.update(cp_desing_form);
			setTimeout(function () {
				cp_changeSize();
			}, 600);
		});
		function cp_changeSize() {
			jQuery('.ps-scrollbar-y-rail').remove();

			// update scrollbars
			Ps.update(cp_desing_form);
		}
	}

	const colImpressions = jQuery('.column-impressions').outerHeight();

	jQuery('span.change-status').css({
		height: colImpressions + 'px',
		'line-height': colImpressions + 'px',
	});

	const timestring = jQuery('.cp_timezone').val();
	if (undefined !== timestring) {
		let currenttime = '';
		if ('system' === timestring) {
			currenttime = new Date();
		} else {
			currenttime = jQuery('.cp_currenttime').val();
		}

		//convert date to jquery date format.

		jQuery('#cp_start_time, #cp_end_time').datetimepicker({
			sideBySide: true,
			minDate: currenttime,
			icons: {
				time: 'connects-icon-clock',
				date: 'dashicons dashicons-calendar-alt',
				up: 'dashicons dashicons-arrow-up-alt2',
				down: 'dashicons dashicons-arrow-down-alt2',
				previous: 'dashicons dashicons-arrow-left-alt2',
				next: 'dashicons dashicons-arrow-right-alt2',
				today: 'dashicons dashicons-screenoptions',
				clear: 'dashicons dashicons-trash',
			},
		});

		jQuery('#cp_start_time').on('dp.change', function (e) {
			jQuery('#cp_end_time').data('DateTimePicker').minDate(e.date);
		});
	}
	if (
		jQuery('.bsf-contact-list-top-search').hasClass(
			'bsf-cntlist-top-search-act'
		)
	) {
		jQuery('.bsf-cntlst-top-search-input').focus().trigger('click');
	}

	jQuery(document).on('focus', '.bsf-cntlst-top-search-input', function () {
		jQuery('.bsf-contact-list-top-search').addClass(
			'bsf-cntlist-top-search-act'
		);
	});

	jQuery(document).on(
		'focusout',
		'.bsf-cntlst-top-search-input',
		function () {
			jQuery('.bsf-contact-list-top-search').removeClass(
				'bsf-cntlist-top-search-act'
			);
		}
	);

	jQuery(document).on('click', '.bsf-cntlst-top-search-submit', function () {
		jQuery('.bsf-cntlst-top-search').trigger('submit');
	});

	// Export the popup.
	jQuery('.cp-download-modal, .cp-download-infobar,.cp-download-slidein').on(
		'click',
		function (e) {
			e.preventDefault();
			const export_form = jQuery(this).parents('form');
			export_form.trigger('submit');
		}
	);

	if (jQuery('#convert-plus-select2-js').length >= 1) {
		jQuery('select.select2-geo_target-dropdown').select2({
			placeholder: 'Select Countries',
		});
		jQuery('select.select2-cat-dropdown').select2({
			placeholder: 'Select Categories',
		});
		jQuery('select.select2-pages-dropdown').select2({
			placeholder: 'Select pages / posts',
		});
		jQuery('select.select2-post-types-dropdown').select2({
			placeholder: 'Select post types',
		});
		jQuery('select.select2-taxonomies-dropdown').select2({
			placeholder: 'Select posts from - taxonomies',
		});

		jQuery('select.select2-group_filters-dropdown').select2({
			placeholder: 'Search pages / post / categories',

			ajax: {
				url: ajaxurl,
				dataType: 'json',
				method: 'post',
				delay: 250,
				data(params) {
					return {
						q: params.term, // search term.
						page: params.page,
						action: 'cp_get_posts_by_query',
						security_nonce:
							cplus_var_nonce.cp_get_posts_by_query_nonce,
					};
				},
				processResults(data) {
					// parse the results into the format expected by Select2..
					// since we are using custom formatting functions we do not need to.
					// alter the remote JSON data.

					return {
						results: data,
					};
				},
				cache: true,
			},
			minimumInputLength: 2,
		});
	}
	jQuery('.has-tip').frosty();
	const cp_form_debug = jQuery('#convert_plug_debug');
	const debug_btn = jQuery('.button-update-debug-settings');
	debug_btn.on('click', function () {
		const data = cp_form_debug.serialize();
		data.security_nonce = jQuery('#smile_update_debug_nonce').val();
		jQuery.ajax({
			url: ajaxurl,
			data,
			dataType: 'JSON',
			type: 'POST',
			success(result) {
				if (result.message === 'Settings Updated!') {
					swal('Updated!', result.message, 'success');
					setTimeout(function () {
						window.location = window.location;
					}, 500);
				} else {
					swal('Error', result.message, 'error');
				}
			},
		});
	});

	// Get started.
	jQuery('.cp-started-content-list li').each(function (index, el) {
		jQuery(el).on('hover', function () {
			jQuery(el).siblings().removeClass('cp-started-li-act');
			jQuery(el).addClass('cp-started-li-act');

			const imgId = jQuery(el).attr('data-id');
			if (imgId) {
				jQuery('.' + imgId)
					.siblings()
					.removeClass('active');
				jQuery('.' + imgId).addClass('active');
			}
		});
	});

	// Modules.
	const form_module = jQuery('#convert_plug_modules');
	const btn_update = jQuery('.button-update-modules');
	btn_update.on('click', function () {
		const data = form_module.serialize();
		jQuery.ajax({
			url: ajaxurl,
			data,
			dataType: 'JSON',
			type: 'POST',
			success(result) {
				if (result.message === 'Modules Updated!') {
					swal('Updated!', result.message, 'success');
					setTimeout(function () {
						window.location = window.location;
					}, 500);
				} else {
					swal('Error!', result.message, 'error');
				}
			},
		});
	});

	// Settings.
	//  Toggle Response Messages.
	jQuery('#cp-default-messages')
		.siblings('.smile-switch-btn')
		.each(function (index, el) {
			const self = jQuery(el);
			toggle_response_messages(self);
			self.on('click', function () {
				jQuery('#cp-already-subscribed').parent('p').slideToggle();
			});
		});

	//  Toggle Response subscriber.
	jQuery('#cp-sub-notify')
		.siblings('.smile-switch-btn')
		.each(function (index, el) {
			const self = jQuery(el);
			toggle_response_email(self);
			self.on('click', function () {
				jQuery('#cp-sub-email').parent('p').slideToggle();
				jQuery('#cp-email-sub').parent('p').slideToggle();
				jQuery('#cp-email-body').parent('p').slideToggle();
			});
		});

	// Toggle Response User Roles.
	jQuery('#cp_add_user_role')
		.siblings('.smile-switch-btn')
		.each(function (index, el) {
			const self = jQuery(el);
			toggle_response_roles(self);
			self.on('click', function () {
				jQuery('.cp-user-roles').slideToggle();
			});
		});

	jQuery('#cp_change_ntf_id')
		.siblings('.smile-switch-btn')
		.each(function (index, el) {
			const self = jQuery(el);
			toggle_err_notify_email(self);
			self.on('click', function () {
				jQuery('#cp_notify_email_to').parent('p').slideToggle();
			});
		});

	jQuery('.has-tip').frosty();
	const btn = jQuery('.button-update-settings');
	const inactive = jQuery('#user_inactivity');

	btn.on('click', function () {
		let ser = jQuery('[name]').not('#cp-user-role').serialize();
		const array_values = [];
		let access_role_array = [];
		let new_user_role_array = [];
		jQuery('input[name="cp-user-role"]:checked').each(function () {
			array_values.push(this.value);
		});

		if (jQuery('.cp-access-roles.debug-section').length > 0) {
			jQuery('input[name="cp_access_role"]:checked').each(function () {
				access_role_array.push(this.value);
			});

			access_role_array = access_role_array.join(',');
			ser += '&cp-access-role=' + access_role_array;
		}

		const arrayValues = array_values.join(',');
		ser += '&cp-user-role=' + arrayValues;

		const inactive_time = inactive.val();
		ser += '&user_inactivity=' + inactive_time;

		jQuery('input[name="cp_new_user_role"]:checked').each(function () {
			new_user_role_array.push(this.value);
		});

		new_user_role_array = new_user_role_array.join(',');
		ser += '&cp-new-user-role=' + new_user_role_array;

		/*Conflict with 404-301 plugin. - The plugin was adding two actions in the Convert Plus customizer
		due to which the Convert Plus settings were not getting saved from the custoimizer. */
		let data = ser;
		data = data.replace('action=jj4t3_redirect_form', '');
		data.security_nonce = jQuery('#cp-smile_update_modules-nonce').val();
		data.security_nonce = jQuery('#cp-smile_update_settings-nonce').val();

		jQuery.ajax({
			url: ajaxurl,
			data,
			dataType: 'JSON',
			type: 'POST',
			success(result) {
				if (result.message === 'Settings Updated!') {
					swal('Updated', result.message, 'success');
					setTimeout(function () {
						window.location = window.location;
					}, 500);
				} else {
					swal('Error', result.message, 'error');
				}
			},
		});
	});

	//  Toggle Domain Names.
	jQuery('#cp-disable-domain')
		.siblings('.smile-switch-btn')
		.each(function (index, el) {
			const self = jQuery(el);
			toggle_domain_names(self);
			self.on('click', function () {
				jQuery('#cp-domain-name').parent('p').slideToggle();
			});
		});

	//  Toggle Load CSS and JS
	jQuery('#cp-load-syn')
		.siblings('.smile-switch-btn')
		.each(function (index, el) {
			const self = jQuery(el);
			toggle_domain_names(self);
			self.on('click', function () {
				jQuery('#cp-load-syn').parent('p').slideToggle();
			});
		});
});

//  Toggle Response Messages.
function toggle_response_messages(self) {
	const id = self.data('id');
	const value = self
		.parents('.switch-wrapper')
		.find('#' + id)
		.val();

	if (value === '1') {
		jQuery('#cp-already-subscribed').parent('p').slideDown();
	} else {
		jQuery('#cp-already-subscribed').parent('p').slideUp();
	}
}

//  Toggle toggle_domain_names
function toggle_domain_names(self) {
	const id = self.data('id');
	const value = self
		.parents('.switch-wrapper')
		.find('#' + id)
		.val();
	if (value === '1') {
		jQuery('#cp-domain-name').parent('p').slideDown();
	} else {
		jQuery('#cp-domain-name').parent('p').slideUp();
	}
}

//  Toggle Response email.
function toggle_response_email(self) {
	const id = self.data('id');
	const value = self
		.parents('.switch-wrapper')
		.find('#' + id)
		.val();
	if (value === '1') {
		jQuery('#cp-sub-email').parent('p').slideDown();
		jQuery('#cp-email-sub').parent('p').slideDown();
		jQuery('#cp-email-body').parent('p').slideDown();
	} else {
		jQuery('#cp-sub-email').parent('p').slideUp();
		jQuery('#cp-email-sub').parent('p').slideUp();
		jQuery('#cp-email-body').parent('p').slideUp();
	}
}

// Toggle notification error email
function toggle_err_notify_email(self) {
	const id = self.data('id');
	const value = self
		.parents('.switch-wrapper')
		.find('#' + id)
		.val();

	if (value === '1') {
		jQuery('#cp_notify_email_to').parent('p').slideDown();
	} else {
		jQuery('#cp_notify_email_to').parent('p').slideUp();
	}
}

//  Toggle Response Messages
function toggle_response_roles(self) {
	const id = self.data('id');
	const value = self
		.parents('.switch-wrapper')
		.find('#' + id)
		.val();
	if (value === '1') {
		jQuery('.cp-user-roles').slideDown();
	} else {
		jQuery('.cp-user-roles').slideUp();
	}
}

jQuery('.action-download-analytics ').on('click', function (e) {
	e.preventDefault();
	const form = jQuery(this).parents('form');
	form.trigger('submit');
});
jQuery('#cp-chart-comp-type').on('change', function () {
	const end = this.value;
	jQuery('#comp_factor').val(end);
});
//--------------------------------------------------------------------------------------
let data_id, // eslint-disable-line
	smile_panel = '';
jQuery.extend({
	cpcpGetUrlVars() {
		let vars = [], // eslint-disable-line prefer-const
			hash;
		const hashes = window.location.href
			.slice(window.location.href.indexOf('?') + 1)
			.split('&');
		for (let i = 0; i < hashes.length; i++) {
			hash = hashes[i].split('=');
			vars.push(hash[0]);
			vars[hash[0]] = hash[1];
		}
		return vars;
	},
	cpGetUrlVar(name) {
		return jQuery.cpcpGetUrlVars()[name];
	},
});

jQuery.fn.bgColorFade = function (userOptions) {
	// starting color, ending color, duration in ms
	const options = $.extend(
		{
			start: '#fff79f',
			end: '#fff',
			time: 2000,
		},
		userOptions || {}
	);
	$(this)
		.css({
			backgroundColor: options.start,
		})
		.animate(
			{
				backgroundColor: options.end,
			},
			options.time
		);
	return this;
};
/* eslint-disable */
function getParameterByName(name) {
	/* eslint-enable */
	name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
	const regex = new RegExp('[\\?&]' + name + '=([^&#]*)'),
		results = regex.exec(location.search); // eslint-disable-line no-undef
	return results === null
		? ''
		: decodeURIComponent(results[1].replace(/\+/g, ' '));
}
/* eslint-disable */
function hide_loading(ID) {
	/* eslint-enable */
	jQuery(document).trigger('iframe_load', [ID]);
	setTimeout(function () {
		jQuery('.edit-screen-overlay').fadeOut();

		jQuery('#smile_design_iframe').css('visibility', 'visible');

		jQuery('.design-area-loading').hide();
	}, 500);
}

// Sets cookies.
const createCookie = function (name, value, days) {
	// If we have a days value, set it in the expiry of the cookie.
	let expires;
	if (days) {
		const date = new Date();
		date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
		expires = '; expires=' + date.toGMTString();
	} else {
		expires = '';
	}

	// Write the cookie.
	document.cookie = name + '=' + value + expires + '; path=/';
};

// Retrieves cookies.
const cpGetCookie = function (name) { // Conflict with IONOS Setup Assistant (Must Use Plugin).
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
};

// Removes cookies.
const removeCookie = function (name) {
	createCookie(name, false, -1);
};

jQuery(document).on('iframe_load', function () {
	const smile_panel = jQuery('.customize').data('style'); // eslint-disable-line no-shadow
	jQuery('#button-save-' + smile_panel + ' > span').trigger('click');
	jQuery('a[data-section-id="responsive-sect"]').trigger('click');
	setTimeout(function () {
		jQuery('#button-save-' + smile_panel).trigger('click');
		jQuery('.cp-section').first().trigger('click');
	}, 1000);
});

function smileHandleDependencies() {
	const container = jQuery('.active-customizer').find(
		'.smile-element-container'
	);
	jQuery.each(container, function (element) {
		const $this = jQuery(this);
		const el_name = $this.data('name');
		const el_operator = $this.data('operator');
		const el_value = $this.data('value');
		/* eslint-disable */
		var element = $this.data('element');
		element = jQuery(this)
			.parents('.content')
			.find('#smile_' + element);
		/* eslint-enable */
		let el_id;
		let value;
		if (typeof el_name !== 'undefined') {
			el_id = jQuery(this)
				.parents('.content')
				.find('#smile_' + el_name);
			value = el_id.val();
			let displayProp = el_id
				.closest('.smile-element-container')
				.css('display');
			$this.hide();

			//	We check the #smile_EL_NAME value for dependency
			//	In [Radio Buttons] it does not works, Because It has different ID's
			//	So, We change the selector for radio button
			if (typeof value === 'undefined') {
				el_id = jQuery(this)
					.parents('.content')
					.find(
						"input[type='radio'][name='" + el_name + "']:checked"
					);
				value = el_id.val();
				displayProp = el_id
					.closest('.smile-element-container')
					.css('display'); // eslint-disable-line no-undef
				$this.hide();
			}
			switch (el_operator) {
				case '=':
					// eslint-disable-next-line eqeqeq
					if (value == el_value && displayProp === 'block') {
						$this.show();
					} else {
						$this.hide();
					}
					break;
				case '>':
					if (value > el_value && displayProp === 'block') {
						$this.show();
					} else {
						$this.hide();
					}
					break;
				case '>=':
					if (value >= el_value && displayProp === 'block') {
						$this.show();
					} else {
						$this.hide();
					}
					break;
				case '<':
					if (value < el_value && displayProp === 'block') {
						$this.show();
					} else {
						$this.hide();
					}
					break;
				case '<=':
					if (value <= el_value && displayProp === 'block') {
						$this.show();
					} else {
						$this.hide();
					}
					break;
				case '==':
					// eslint-disable-next-line eqeqeq
					if (value == el_value && displayProp === 'block') {
						$this.show();
					} else {
						$this.hide();
					}
					break;
				case '!=':
					// eslint-disable-next-line eqeqeq
					if (value != el_value && displayProp === 'block') {
						$this.show();
					} else {
						$this.hide();
					}
					break;
				case '!==':
					// eslint-disable-next-line eqeqeq
					if (value != el_value && displayProp === 'block') {
						$this.show();
					} else {
						$this.hide();
					}
					break;
				/* eslint-enable */
				case 'is_contain':
					if (
						value.indexOf(el_value) >= 0 &&
						displayProp === 'block'
					) {
						$this.show();
					} else {
						$this.hide();
					}
					break;
			}
			if ($this.hasClass('hide-for-default')) {
				$this.hide();
			}
		}
	});
}

jQuery(document).ready(function () {
	const btn = jQuery('.customize');
	const collapse = jQuery('.customizer-collapse');
	const copy_btn = jQuery('.copy-style-icon');
	const delete_btn = jQuery('.trash-style-icon');
	const calcel_btn = jQuery('.cancel-title');
	const changeStatus = jQuery('a.change-status');
	const delete_multiplbtn = jQuery('.cp-delete-multiple-modal-style');

	jQuery('body').on('keyup', '#style-title', function () {
		jQuery(this).removeAttr('style');
	});

	// custom html editor

	const htmltextarea = jQuery('#smile_custom_html_form');
	const modef = 'xml';
	const editDivf = jQuery('<div>', {
		position: 'absolute',
		width: 300,
		height: 300,
		class: htmltextarea.attr('class'),
	}).insertBefore(htmltextarea);
	if (htmltextarea.length > 0) {
		htmltextarea.css('visibility', 'hidden');
		const htmlEditor = ace.edit(editDivf[0]); // eslint-disable-line no-undef
		htmlEditor.renderer.setShowGutter(true);

		htmlEditor.getSession().setValue(htmltextarea.val());
		htmlEditor.getSession().setMode('ace/mode/' + modef);
		htmlEditor.getSession().setUseWrapMode(true);

		htmlEditor.on('change', function () {
			htmltextarea.val(htmlEditor.getSession().getValue());
		});
	}

	// custom css editor

	const textarea = jQuery('#smile_custom_css');
	const mode = 'css';
	const editDiv = jQuery('<div>', {
		position: 'absolute',
		width: 300,
		height: 300,
		class: htmltextarea.attr('class'),
	}).insertBefore(textarea);

	if (textarea.length > 0) {
		editDiv.attr('id', 'editor');
		textarea.css('visibility', 'hidden');
		const cssEditor = ace.edit(editDiv[0]); // eslint-disable-line no-undef
		cssEditor.renderer.setShowGutter(true);
		cssEditor.getSession().setValue(textarea.val());
		cssEditor.getSession().setMode('ace/mode/' + mode);
		cssEditor.getSession().setUseWrapMode(true);

		cssEditor.on('change', function () {
			textarea.val(cssEditor.getSession().getValue());
		});
	}

	jQuery('body').on('click', '.style-title', function (e) {
		e.preventDefault();
		const $this = jQuery(this);
		jQuery('.style-title').unbind('click');
		const wrapper = $this.parent('.smile-slug');
		const update = $this.parents('td').find('.button');
		const tr = $this.parents('tbody').find('tr');
		const num = $this.parents('tr').index();
		let value = $this.text();
		value = value.trim();
		let edit = false;
		jQuery.each(tr, function (index) {
			if (index !== num) {
				jQuery(this).addClass('inline-editing');
			}
		});
		const cls = jQuery(this).parents('tr').attr('class');
		if (cls.indexOf('inline-editing') > 0) {
			edit = false;
		} else {
			edit = true;
		}
		if (edit) {
			wrapper.html(
				'<input type="text" id="rename-title" value="' + value + '"/>'
			);
			update.show();
		}
	});

	jQuery('.cp-website-link, .cp-dashboard-link, .close-button').on(
		'click',
		function (e) {
			e.preventDefault();
			const target = jQuery(this).attr('target');
			const cookie = cpGetCookie('cp-unsaved-changes');
			const redirectURL = jQuery(this).data('redirect');
			const smile_panel = jQuery('.customize').data('style'); // eslint-disable-line no-shadow
			let closeOncancel = false;
			let showLoaderOnConfirm = true;
			let title;
			let cancelButtonText;
			let confirmText;
			let cancleMessage;
			const live = jQuery('#smile_live').val();
			const module = jQuery('.customize').data('module');
			if (live === '' || live === '0' || live === 0) {
				closeOncancel = false;
				title = 'Publish & Set This ' + module + ' Live?';
				confirmText = "Yes, Let's Publish It.";
				cancelButtonText = 'No, Keep it Pause!';
				cancleMessage =
					'The ' +
					module +
					' is pause at present. It will not be visible on site until you make it live.';
				showLoaderOnConfirm = false;
			} else if (cookie) {
				title = 'Some changes are not saved yet!';
				confirmText = 'Save it!';
				cancelButtonText = 'Close without saving changes';
				cancleMessage = 'Changes are saved.'; // eslint-disable-line no-unused-vars
			} else {
				removeCookie('cp-unsaved-changes');
				if (target === '_blank') window.open(redirectURL);
				else window.location = redirectURL;
			}

			swal(
				{
					title,
					text: '<span class="cp-discard-popup" style="position: absolute;top: 0;right: 0;"><i class="connects-icon-cross"></i></span>',
					type: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#DD6B55',
					confirmButtonText: confirmText,
					cancelButtonText,
					closeOnConfirm: false,
					closeOnCancel: closeOncancel,
					showLoaderOnConfirm,
					customClass: 'cp-switch-theme',
					html: true,
				},
				function (isConfirm) {
					let section;
					if (isConfirm) {
						if (live === '' || live === '0' || live === 0) {
							jQuery('#smile_live').attr('value', 1);
							section = jQuery('.cp-section.active');
							jQuery(
								'#button-save-' + smile_panel + ' > span'
							).trigger('click');
							jQuery('#button-save-' + smile_panel).trigger(
								'click'
							);
							const module = jQuery('.customize').data('module'); // eslint-disable-line no-shadow
							section.trigger('click');
							if (cookie) {
								swal('Saved & Published!', '', 'success');
							} else {
								swal(
									'Saved & Published!',
									'Your ' + module + ' is live now.',
									'success'
								);
							}
							setTimeout(function () {
								removeCookie('cp-unsaved-changes');
								if (target === '_blank')
									window.open(redirectURL);
								else window.location = redirectURL;
							}, 500);
						} else if (cookie) {
							section = jQuery('.cp-section.active');
							jQuery(
								'#button-save-' + smile_panel + ' > span'
							).trigger('click');
							jQuery('#button-save-' + smile_panel).trigger(
								'click'
							);
							section.trigger('click');
							setTimeout(function () {
								removeCookie('cp-unsaved-changes');
								if (target === '_blank')
									window.open(redirectURL);
								else window.location = redirectURL;
							}, 500);
						}
					} else if (cookie) {
						if (cancelButtonText === 'No, Keep it Pause!') {
							swal(
								{
									title: 'Some changes are not saved yet!',
									text: '<span class="cp-discard-popup" style="position: absolute;top: 0;right: 0;"><i class="connects-icon-cross"></i></span>',
									type: 'warning',
									showCancelButton: true,
									confirmButtonColor: '#DD6B55',
									confirmButtonText: 'Save it!',
									cancelButtonText:
										'Close without saving changes',
									closeOnConfirm: false,
									closeOnCancel: false,
									showLoaderOnConfirm: false,
									customClass: 'cp-switch-theme',
									html: true,
								},
								/* eslint-disable */
								function (isConfirm) {
									/* eslint-enable */
									if (isConfirm) {
										section = jQuery('.cp-section.active');
										jQuery(
											'#button-save-' +
												smile_panel +
												' > span'
										).trigger('click');
										jQuery(
											'#button-save-' + smile_panel
										).trigger('click');
										section.trigger('click');
										setTimeout(function () {
											removeCookie('cp-unsaved-changes');
											jQuery('.cp-discard-popup').trigger(
												'click'
											);
											if (target === '_blank')
												window.open(redirectURL);
											else window.location = redirectURL;
										}, 500);
									} else {
										removeCookie('cp-unsaved-changes');
										jQuery('.cp-discard-popup').trigger(
											'click'
										);
										if (target === '_blank')
											window.open(redirectURL);
										else window.location = redirectURL;
									}
								}
							);
						} else {
							removeCookie('cp-unsaved-changes');
							jQuery('.cp-discard-popup').trigger('click');
							if (target === '_blank') window.open(redirectURL);
							else window.location = redirectURL;
						}
					} else {
						jQuery('.cp-discard-popup').trigger('click');
						removeCookie('cp-unsaved-changes');
						if (target === '_blank') {
							window.open(redirectURL);
						} else {
							window.location = redirectURL;
						}
					}
				}
			);

			jQuery('.cp-switch-theme')
				.prev()
				.css('background-color', 'rgba(0,0,0,.9)');
			jQuery('body').on(
				'click',
				'.cp-switch-theme .cp-discard-popup',
				function () {
					e.preventDefault();
					jQuery('.sweet-overlay, .sweet-alert')
						.fadeOut('slow')
						.remove();
				}
			);

			e.preventDefault();
			return false;
		}
	);

	collapse.on('click', function (e) {
		e.preventDefault();
		e.stopPropagation();
		const wrapper = jQuery(this).parents('.customizer-wrapper');
		const footer_actions = wrapper.find('.customize-footer-actions');
		const section = jQuery('.cp-section.active');
		wrapper.toggleClass('collapsed');
		footer_actions.toggleClass('collapsed');
		if (!footer_actions.hasClass('collapsed')) {
			section.trigger('click');
		}
	});

	calcel_btn.on('click', function () {
		const wrapper = jQuery(this).parents('td').find('.smile-slug');
		const style_name = jQuery(this).data('style');
		const update = jQuery(this).parents('td').find('.button');
		wrapper.html(
			'<span class="style-title" href="#" title="Click to rename">' +
				style_name +
				'</span>'
		);
		update.hide();
		jQuery.each(jQuery(this).parents('tbody').find('tr'), function () {
			jQuery(this).removeClass('inline-editing');
		});
	});
	copy_btn.on('click', function (e) {
		e.preventDefault();
		const style_id = jQuery(this).data('style');
		const action = 'smile_duplicate_style';
		const option = jQuery(this).data('option');
		var module = jQuery(this).data('module'); // eslint-disable-line no-var
		const variant_id = jQuery(this).data('variant-style');
		const stylescreen = jQuery(this).data('stylescreen');
		const data = {
			action,
			module,
			style_id,
			variant_id,
			option,
			stylescreen,
			security_nonce: cplus_var_nonce.duplicate_nonce,
		};
		/* eslint-disable */
		var module = jQuery(this)
			.find('.action-tooltip')
			.html()
			.replace('Duplicate', '');
		/* eslint-enable */
		module = module.trim();
		jQuery.ajax({
			url: ajaxurl,
			type: 'POST',
			dataType: 'JSON',
			data,
			success(result) {
				if (result.message === 'copied') {
					swal({
						title: 'Duplicated!',
						text:
							cplus_vars.duplicate_style +
							' ' +
							module +
							' you have selected has been duplicated.',
						type: 'success',
						timer: 2000,
						showConfirmButton: false,
					});
					setTimeout(function () {
						window.location = window.location;
					}, 500);
				}
			},
		});
	});

	delete_btn.on('click', function (e) {
		e.preventDefault();
		const $this = jQuery(this);
		let module = jQuery(this)
			.find('.action-tooltip')
			.html()
			.replace('Delete', '');
		module = module.trim();
		swal(
			{
				title: 'Are you sure?',
				text: 'You will not be able to recover this ' + module + '!',
				type: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#DD6B55',
				confirmButtonText: 'Yes, delete it!',
				cancelButtonText: 'No, cancel it!',
				closeOnConfirm: false,
				closeOnCancel: true,
				showLoaderOnConfirm: true,
			},
			function (isConfirm) {
				if (isConfirm) {
					jQuery(document).trigger('deleteStyle', [$this, true]);
				}
			}
		);
	});

	jQuery(document).on('deleteStyle', function (e, $this, $reload) {
		const do_delete = true;
		if (do_delete) {
			const style_id = $this.data('style');
			const action = 'smile_delete_style';
			const option = $this.data('option');
			const variant_option = $this.data('variantoption');
			const deleteMethod = $this.data('delete');
			const data = {
				action,
				style_id,
				option,
				variant_option,
				deleteMethod,
				security_nonce: jQuery('#cp-delete-style-nonce').val(),
			};

			jQuery.ajax({
				url: ajaxurl,
				type: 'POST',
				dataType: 'JSON',
				data,
				success(result) {
					if (result.message === 'Deleted') {
						swal({
							title: 'Deleted!',
							text: 'Style you have selected has been deleted.',
							type: 'success',
							timer: 2000,
							showConfirmButton: false,
						});
						if ($reload) {
							setTimeout(function () {
								window.location = window.location;
							}, 500);
						}
					}
				},
			});
		}
	});

	//delete multiple modal
	delete_multiplbtn.on('click', function (e) {
		e.preventDefault();

		const style_ids = Array();
		jQuery("[name='delete_modal'").each(function () {
			if (jQuery(this).is(':checked')) {
				style_ids.push(jQuery(this).val());
			}
		});

		if (style_ids.length > 0) {
			const $this = jQuery(this);
			const module = $this.data('module');

			swal(
				{
					title: 'Are you sure?',
					text: cplus_vars.delete_notice + ' ' + module + '!',
					type: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#DD6B55',
					confirmButtonText: cplus_vars.confirm_delete,
					cancelButtonText: cplus_vars.cancel_delete,
					closeOnConfirm: false,
					closeOnCancel: true,
					showLoaderOnConfirm: true,
				},
				function (isConfirm) {
					if (isConfirm) {
						jQuery(document).trigger('deletemultipleStyle', [
							$this,
							style_ids,
							true,
						]);
					}
				}
			);
		}
	});

	jQuery(document).on(
		'deletemultipleStyle',
		function (e, $this, style_ids, $reload) {
			const style_id = style_ids.join(',');
			const action = 'cp_delete_all_modal_action';
			const option = $this.data('option');
			const variant_option = $this.data('variantoption');
			const deleteMethod = $this.data('delete');
			const data = {
				action,
				style_id,
				option,
				variant_option,
				deleteMethod,
				security_nonce: jQuery('#cp-delete-style-nonce').val(),
			};

			jQuery.ajax({
				url: ajaxurl,
				type: 'POST',
				dataType: 'JSON',
				data,
				success(result) {
					if (result.message === 'Deleted') {
						swal({
							title: 'Deleted!',
							text: cplus_vars.delete_conf_notice,
							type: 'success',
							timer: 2000,
							showConfirmButton: false,
						});
						if ($reload) {
							setTimeout(function () {
								window.location = window.location;
							}, 500);
						}
					}
				},
			});
		}
	);

	btn.each(function () {
		const style = jQuery(this).data('style');
		jQuery('#accordion-' + style).accordion({
			closeAny: true,
		});
	});

	btn.on('click', function (e) {
		e.preventDefault();
		e.stopPropagation();
		const view = jQuery(this).data('view');
		const style_name = jQuery('#style-title').val();
		if (view === 'new') {
			let url = jQuery(this).attr('href');
			if (style_name !== '') {
				const variantStyle = jQuery.cpGetUrlVar('variant-style');
				if (typeof variantStyle !== 'undefined') {
					url += '&style-name=' + style_name;
				}
				window.location = url;
			} else {
				jQuery('#style-title').addClass('smile-new-list-required');
				jQuery('#style-title').trigger('focus');
			}
			return false;
		}
		e.preventDefault();

		if (style_name !== '') {
			const style = jQuery(this).data('style');
			smile_panel = style;
			const data_id = jQuery(this).data('id'); // eslint-disable-line
			jQuery('.theme').removeClass('active');
			jQuery(this).parents('.theme').addClass('active');

			const ID = jQuery(this).data('id');
			const style_id = jQuery('#style-title');
			const new_style = jQuery('#form-' + ID + ' #new_style');
			const $val = style_id.val();
			new_style.attr('value', $val);
			const container = jQuery('.' + style);
			const frame = container.find('.design-content');
			const design_area = frame.find('.live-design-area');
			const frame_url = frame.data('iframe-url');
			const demo_id = frame.data('demo-id');
			const module = frame.data('module');
			const cls = frame.data('class');
			const js_url = frame.data('js-url');
			container.fadeIn('fast');
			container.addClass('active-customizer');
			jQuery('html').css('overflow', 'hidden');

			const data = {
				action: 'framework_update_preview_data',
				demo_id,
				module,
				cls,
				security_nonce:
					cplus_var_nonce.framework_update_preview_data_nonce,
			};

			jQuery.ajax({
				url: ajaxurl,
				data,
				type: 'POST',
				dataType: 'HTML',
				success(result) {
					const iframe =
						'<div class="design-area-loading"><div class="smile-absolute-loader" style="visibility: visible;"> <div class="smile-loader"><div class="smile-loading-bar"></div><div class="smile-loading-bar"></div><div class="smile-loading-bar"></div><div class="smile-loading-bar"></div></div></div></div><iframe id="smile_design_iframe" src="' +
						frame_url +
						'" data-js="' +
						js_url +
						'" onload="hide_loading(data_id);">' +
						result +
						'</iframe>';
					design_area.html(iframe);
					jQuery(document).trigger('smile_iframe_after_load');
				},
			});

			jQuery(document).trigger('smile_panel_loaded', [smile_panel, ID]);

			//	Handling dependencies
			smileHandleDependencies();
		} else {
			jQuery('#style-title').addClass('smile-new-list-required');
			jQuery('#style-title').trigger('focus');
		}
	});

	changeStatus.off().on('click', function (e) {
		e.preventDefault();
		const $this = jQuery(this);
		const style_id = $this.attr('data-style-id');
		const status = $this.attr('data-live');
		const option = $this.attr('data-option');
		const variant = $this.attr('data-variant');
		const action = 'smile_update_status';
		const sch_container = jQuery('.cp-schedular-overlay');
		const sch = $this.attr('data-schedule');
		const obj = jQuery(this).parents('td').find('span.cp-status');

		if (typeof sch !== 'undefined' && sch === '1') {
			sch_container.fadeIn();
			e.stopPropagation();
			jQuery('.cp-schedule-btn')
				.off()
				.on('click', function (event) {
					event.preventDefault();
					const cp_start = jQuery('.cp_start').val();
					const cp_end = jQuery('.cp_end').val();
					if (cp_start !== '' && cp_end !== '') {
						const data = {
							action,
							status,
							style_id,
							option,
							variant,
							cp_start,
							cp_end,
						};
						jQuery(document).trigger('changeStatus', [
							$this,
							data,
							obj,
							status,
						]);
					} else if (cp_start === '')
						jQuery('.cp_start').trigger('focus');
					else jQuery('.cp_end').trigger('focus');
				});
		} else {
			const data = {
				action,
				status,
				style_id,
				option,
				variant,
			};
			jQuery(document).trigger('changeStatus', [
				$this,
				data,
				obj,
				status,
			]);
		}
	});

	jQuery(document).on('changeStatus', function (e, $this, data, obj, status) {
		const old_status = obj.attr('data-live');
		jQuery(obj).addClass('cp-status-loader');

		data.security_nonce = jQuery('#cp-change-status-nonce').val();
		jQuery.ajax({
			url: ajaxurl,
			data,
			type: 'POST',
			dataType: 'JSON',
			success(result) {
				if (result.message === 'status changed') {
					jQuery(document).trigger('dismissPopup');
					jQuery(obj).removeClass('cp-status-loader');
					jQuery(obj).css('color', 'rgb(46, 204, 113)');
					if (status === '0') {
						jQuery(obj).html(
							'<i class="connects-icon-pause"></i><span>Pause</span>'
						);
						jQuery(obj).attr('data-live', 0);
					} else if (status === '1') {
						jQuery(obj).html(
							'<i class="connects-icon-play"></i><span>Live</span>'
						);
						jQuery(obj).attr('data-live', 1);
					} else {
						const start =
							result.settings.schedule.start +
							' ' +
							result.settings.schedule.end;
						jQuery(obj).html(
							'<i class="connects-icon-clock"></i><span>Scheduled ( ' +
								start +
								' )</span>'
						);
						jQuery(obj).attr('data-live', 2);
					}

					if (old_status === '0') {
						$this.html(
							'<i class="connects-icon-pause"></i><span>Pause</span>'
						);
						$this.attr('data-live', 0);
						$this.removeAttr('data-schedule');
					} else if (old_status === '1') {
						$this.html(
							'<i class="connects-icon-play"></i><span>Live</span>'
						);
						$this.attr('data-live', 1);
						$this.removeAttr('data-schedule');
					} else {
						$this.html(
							'<i class="connects-icon-clock"></i><span>Scheduled</span>'
						);
						$this.attr('data-live', 2);
						$this.attr('data-schedule', 1);
					}

					setTimeout(function () {
						jQuery(obj).removeAttr('style');
					}, 2000);
				}
				return false;
			},
		});
	});

	jQuery('span.change-status').on('click', function (e) {
		e.stopPropagation();
		e.preventDefault();
	});

	jQuery('.cp-scheduler-close').on('click', function () {
		jQuery(document).trigger('dismissPopup');
	});

	jQuery('.cp-scheduler-popup').on('click', function (e) {
		e.preventDefault();
		e.stopPropagation();
	});

	jQuery(document).on('dismissPopup', function () {
		const sch_container = jQuery('.cp-schedular-overlay');
		jQuery('span.cp-status').removeClass('cp-status-loader');
		sch_container.fadeOut();
	});

	jQuery('.cp-resp-bar-icon').on('click', function () {
		jQuery('.cp-resp-bar-icon').removeClass('cp-resp-active');
		jQuery(this).addClass('cp-resp-active');
		const cls = jQuery(this).data('res-class');
		jQuery('.live-design-area').attr('class', 'live-design-area ' + cls);
	});

	jQuery('.cp-section').first().trigger('click');
	jQuery('.cp-section').on('click', function (e) {
		e.preventDefault();
		const collapse_customizer = jQuery('.customizer-collapse');
		const wrapper = collapse_customizer.parents('.customizer-wrapper');
		const footer_actions = wrapper.find('.customize-footer-actions');
		const target = jQuery(this).data('section-id');
		if (wrapper.hasClass('collapsed')) {
			wrapper.toggleClass('collapsed');
			footer_actions.toggleClass('collapsed');
		}

		const c = jQuery('#' + target).find('.accordion-frame').length;
		const title_area = jQuery('span.theme-name.site-title');
		const theme_title = jQuery(this)
			.find('.has-tip')
			.data('original-title');
		title_area.html(theme_title);
		if (c === 1) {
			const target_content = jQuery('#' + target).find(
				'.accordion-frame .content'
			);
			const target_link = jQuery('#' + target).find(
				'.accordion-frame > a'
			);
			target_content.show();
			target_link.remove();
		}
	});

	//close all panel on click of cp-section
	let countclick = 0;
	jQuery('.cp-section').on('click', function (e) {
		e.preventDefault();
		countclick++;
		jQuery('.accordion-frame')
			.find('a.heading')
			.each(function () {
				if (!jQuery(this).hasClass('collapsed')) {
					if (countclick > 1) {
						jQuery(this).addClass('collapsed');
						jQuery(this)
							.parents('.accordion-frame')
							.find('.content')
							.css({ display: 'none' });
						jQuery('.ps-scrollbar-y-rail ')
							.find('.ps-scrollbar-y')
							.css({ top: '0px', height: '0px' });
						jQuery('.ps-scrollbar-y-rail ').css({
							top: '0px',
							height: '0px',
						});
					}
				}
			});
	});

	jQuery('.cp-customize-section').on('click', function () {
		const id = jQuery(this).data('section-id');
		jQuery('#' + id + ' .content').show();
	});

	jQuery('.accordion-frame a.heading').on('click', function () {
		const $this = jQuery(this); //.parent(".accordion-frame");
		setTimeout(function () {
			const top = $this.position().top;
			jQuery('.design-form').animate(
				{
					scrollTop: top,
				},
				1000
			);
			jQuery('.ps-scrollbar-y-rail ')
				.find('.ps-scrollbar-y')
				.css({ top: '0px', height: '0px' });
			jQuery('.ps-scrollbar-y-rail ').css({ top: '0px', height: '0px' });
		}, 400);
	});

	jQuery(window).on('keydown', function (event) {
		const section = jQuery('.cp-section.active');
		const customize_smile_panel = jQuery('.customize').data('style');
		if (
			(event.which === 83 && (event.ctrlKey || event.metaKey)) ||
			event.which === 18
		) {
			switch (String.fromCharCode(event.which).toLowerCase()) {
				case 's':
					event.preventDefault();
					countclick = 0;
					jQuery(
						'#button-save-' + customize_smile_panel + ' > span'
					).trigger('click');
					section.trigger('click');
					break;
			}
		}
		return true;
	});
});

// A function to handle sending messages.
// A function to handle sending messages.
function smileSendMessage(e) {
	// Prevent any default browser behavior.
	e.preventDefault();
	e.stopPropagation();
	const save_btn = jQuery('#button-save-' + smile_panel);
	save_btn.addClass('cp-save-loader');

	const form_id = 'form-' + save_btn.data('style');
	const url_string = jQuery('#' + form_id)
		.serialize()
		.replace(/\+/g, '%20');
	const action = jQuery('#' + form_id).data('action');
	const start_date = jQuery('#' + form_id).data('start');
	const end_date = jQuery('#' + form_id).data('end');
	const data = JSON.stringify(url_string);
	const container = save_btn.parents('.customizer-wrapper');
	const frame = container.find('.design-content');
	const frame_url = frame.data('iframe-url');
	const receiver = window.frames[0];

	if (receiver !== undefined) {
		receiver.postMessage(data, frame_url);
	}

	jQuery.ajax({
		url: ajaxurl,
		data: {
			action,
			style_settings: url_string,
			start_date,
			end_date,
			security_nonce: cplus_var_nonce.customizer_nonce,
		},
		type: 'POST',
		dataType: 'HTML',
		success(result) {
			const new_style = jQuery('#new_style');
			const style_id = jQuery('#style-title');
			const value = style_id.val();
			if (value === '') {
				new_style.val(result);
				style_id.val(result);
				style_id.attr('disabled', 'true');
			}
			save_btn.removeClass('cp-save-loader');
			const cookie = cpGetCookie('cp-unsaved-changes');
			if (cookie) {
				swal({
					title: 'Settings Saved!',
					text: '',
					type: 'success',
					timer: 2000,
					showConfirmButton: false,
				});
			}
			// remove unsaved cookie
			removeCookie('cp-unsaved-changes');
		},
		error() {},
	});
}

window.onload = function () {
	const theme = jQuery.cpGetUrlVar('theme');
	const btn = jQuery('.customize');
	if (typeof theme !== 'undefined') {
		setTimeout(function () {
			if (!btn.hasClass('variant-test')) {
				btn.trigger('click');
			}
		}, 500);
	}

	// A function to handle sending messages for live preview.
	function smileLiveData(e) {
		// Handle dependencies
		smileHandleDependencies();

		// Prevent any default browser behavior.
		e.preventDefault();

		const el_id = jQuery(this).parents('form').attr('id');
		const url_string = jQuery('#' + el_id)
			.serialize()
			.replace(/\+/g, '%20');
		const data = JSON.stringify(url_string);
		const container = jQuery(this).parents('.customizer-wrapper');
		const frame = container.find('.design-content');
		const frame_url = frame.data('iframe-url');
		const iframe_container = document.getElementById('smile_design_iframe');
		if (iframe_container) {
			const iframe = window.frames[0];
			// Send the data

			if (typeof iframe !== undefined) {
				iframe.postMessage(data, frame_url);
			}
			// create cookie for unsaved changes
			const cookieName = 'cp-unsaved-changes';
			createCookie(cookieName, true, 1);
		}
	}
	/* eslint-disable */
	jQuery(document).on('smile_panel_loaded', function (e, smile_panel, id) {
		/* eslint-enable */
		// Add an event listener that will execute the sendMessage() function
		// when the send button is clicked.
		const form_id = 'form-' + id;

		const elements = jQuery('#' + form_id + ' .smile-input');
		jQuery.each(elements, function () {
			jQuery(this).trigger('change');
			jQuery(this).on('change', smileLiveData);
			jQuery(this).on('keyup', smileLiveData);
		});

		jQuery(document).on(
			'click',
			'#button-save-' + smile_panel,
			function () {
				smileSendMessage(e);
			}
		);
	});

	jQuery(document).on(
		'click',
		'.cp-vertical-nav a:not(".cp-save")',
		function () {
			const href = jQuery(this).attr('href');
			jQuery('.cp-vertical-nav a').removeClass('active');
			jQuery(this).addClass('active');

			jQuery('.cp-customizer-tab').hide();
			jQuery(href).fadeIn(300);
		}
	);
};
jQuery(document).ready(function () {
	removeCookie('cp-unsaved-changes');
	const a = jQuery('a.mailer-stage');
	const c = jQuery('div.stage-content');
	const i = jQuery('a.mailer-stage-internal');

	a.on('click', function (e) {
		e.preventDefault();
		const id = jQuery(this).attr('href');
		a.removeClass('active-stage');
		c.removeClass('active-stage');
		jQuery(this).addClass('active-stage');
		jQuery(id).addClass('active-stage');
	});

	i.on('click', function (e) {
		e.preventDefault();
		const id = jQuery(this).attr('href');
		const cls = id.replace('#', '');
		a.removeClass('active-stage');
		c.removeClass('active-stage');
		jQuery('.' + cls + ' > a').addClass('active-stage');
		jQuery(id).addClass('active-stage');
	});

	jQuery('.has-tip').frosty({
		offset: 10,
	});

	jQuery("a[href='#top']").on('click', function () {
		jQuery('html, body').animate({ scrollTop: 0 }, '1000');
		return false;
	});

	jQuery('.cp-action-link.customize').on('click', function () {
		const redirectLink = jQuery(this).parent().attr('href');
		window.location.href = redirectLink;
	});

	jQuery('.cp-style-import-link').on('click', function (e) {
		e.preventDefault();
		e.stopPropagation();

		const actionLink = jQuery(this).find('.cp-action-link');
		const item_box = jQuery(this).closest('.cp-style-item-box');
		const item_text = jQuery(this).find('.cp-action-text');

		item_box.addClass('cp-import-processing');

		item_text.html('Importing ...');
		item_text.css('color', '#008000');

		actionLink.addClass('cp-save-loader');

		const module = jQuery(this).data('module');
		const preset = jQuery(this).data('preset');

		const redirect_url = jQuery(this).data('href');

		jQuery
			.ajax({
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'cp_import_presets',
					module,
					preset,
					security_nonce: cplus_var_nonce.presets_nonce,
				},
			})
			.done(function (result) {

				if (result.success !== true) {
					swal('Oops...', 'Something went wrong!', 'error');
					return false;
				}

				actionLink.removeClass('cp-save-loader');
				item_box.removeClass('cp-import-processing');

				window.location = redirect_url;
			})
			.fail(function () {});
	});
});

jQuery(document).on('customize_loaded', function () {
	const smile_panel_cl = jQuery('.customize').data('style');
	jQuery('#button-save-' + smile_panel_cl).trigger('click');
});

jQuery(document).on('click', '.cp-behavior-settings', function () {
	const setttings = jQuery(this).data('settings');
	swal({
		title: '',
		text: setttings,
		animation: 'slide-from-top',
		html: true,
		customClass: 'cp-behavior-alert',
		allowEscapeKey: true,
		allowOutsideClick: true,
	});
});

jQuery(document).on('click', '.cp-reset-analytics', function (e) {
	e.preventDefault();
	const $this = jQuery(this);
	swal(
		{
			title: 'Are you sure?',
			text: 'This action will delete impression & conversion count of your style. You will be not able to recover this data.',
			type: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#DD6B55',
			confirmButtonText: 'Yes, reset it!',
			cancelButtonText: 'No, cancel it!',
			closeOnConfirm: false,
			closeOnCancel: true,
			showLoaderOnConfirm: true,
		},
		function (isConfirm) {
			if (isConfirm) {
				jQuery(document).trigger('resetAnalytics', [$this, true]);
			}
		}
	);
});

//prevent action on escape key
jQuery(document).on('keyup', function (e) {
	if (e.keyCode === 27) {
		const styleView = jQuery.cpGetUrlVar('style-view');
		if (styleView === 'edit') {
			e.preventDefault();
			window.location = '#';
		}
	}
});

jQuery(document).on('resetAnalytics', function (e, $this, $reload) {
	const style_id = $this.data('style');
	const action = 'cp_reset_analytics_action';
	const data = {
		action,
		style_id,
		security_nonce: jQuery('#cp-reset-analytics-nonce').val(),
	};

	jQuery.ajax({
		url: ajaxurl,
		type: 'POST',
		data,
		success(result) {
			if (result === 'reset') {
				swal({
					title: 'Success!',
					text: 'Analytics data for selected style has been reset.',
					type: 'success',
					timer: 2000,
					showConfirmButton: false,
				});
				if ($reload) {
					setTimeout(function () {
						window.location = window.location;
					}, 500);
				}
			}
		},
	});
});

//dependency for jugaad style layout
jQuery(document).on('change_radio_image', function (e, $this) {
	const this_class = $this
		.find('input.smile-radio-image')
		.parents('.panel-jugaad');
	if (this_class.length > 0) {
		const modal_image_style = this_class
			.find('.modal_image')
			.parents('.smile-element-container')
			.css('display');
		if (modal_image_style === 'none') {
			this_class.find('.cp-media-sizes').addClass('hide-cp-media-sizes');
		} else {
			this_class
				.find('.cp-media-sizes')
				.removeClass('hide-cp-media-sizes');
		}
	}
});

jQuery('a[data-section-id="responsive-sect"]').on('click', function (e) {
	e.preventDefault();
	const $this = jQuery(this);
	jQuery(document).trigger('hide_images_on_mobile', [$this, true]);
});

jQuery('.close_btn_duration ').on('keydown', function (e) {
	const kCode = e.which || e.keyCode;
	if (kCode === 190 || kCode === 110) e.preventDefault();
	if (e.which === 86 && (e.ctrlKey || e.metaKey)) e.preventDefault();
});

//Fixed conflict with wp fastest cache
jQuery(document).ready(function (evt) {
	let wpfc_delete_curent_page_cache;
	if (typeof wpfc_delete_curent_page_cache === 'function') {
		wpfc_delete_curent_page_cache(evt);
	}
});

jQuery(document).on('change', '.cp-select-all', function () {
	const $this = jQuery(this);
	const cp_list_opt = $this.closest('.cp-list-optins');
	const table_data = cp_list_opt.find('td.column-delete input');

	if ($this.is(':checked')) {
		table_data.each(function () {
			jQuery(this).prop('checked', true);
		});
		jQuery('.cp-delete-multiple-modal-style').removeClass('disabled');
	} else {
		table_data.each(function () {
			jQuery(this).prop('checked', false);
		});
		jQuery('.cp-delete-multiple-modal-style').addClass('disabled');
	}
});

jQuery(document).on('change', '[name="delete_modal"]', function () {
	let style_cnt = 0;
	jQuery("[name='delete_modal'").each(function () {
		if (jQuery(this).is(':checked')) {
			style_cnt++;
		}
	});

	if (style_cnt > 0) {
		jQuery('.cp-delete-multiple-modal-style').removeClass('disabled');
	} else {
		jQuery('.cp-delete-multiple-modal-style').addClass('disabled');
	}
});
