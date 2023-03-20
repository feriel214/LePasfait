/* eslint-env jquery */
(function () {
	'use strict';
	jQuery('.action-download-contact').on('click', function (e) {
		e.preventDefault();
		const form = jQuery(this).parents('form');
		form.trigger('submit');
	});

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

	jQuery(document).ready(function () {
		if (jQuery('.bsf-connect-optins .column-delete').length >= 1) {
			jQuery('table tbody td').on('click', function () {
				if (!jQuery(this).hasClass('column-delete')) {
					window.location = jQuery(this).data('href');
				}
				return false;
			});
		}

		if (
			jQuery('.bsf-contact-list-top-search').hasClass(
				'bsf-cntlist-top-search-act'
			)
		) {
			jQuery('.bsf-cntlst-top-search-input').focus().trigger('click');
		}

		//delete contacts
		jQuery('.delete-contact').on('click', function (e) {
			e.preventDefault();
			const $this = jQuery(this);
			swal(
				{
					title: 'Are you sure?',
					text: 'You will not be able to recover this Contact!',
					type: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#DD6B55',
					confirmButtonText: 'Yes, delete it!',
					cancelButtonText: 'No, cancel it!',
					closeOnConfirm: false,
					closeOnCancel: false,
					showLoaderOnConfirm: true,
				},
				function (isConfirm) {
					if (isConfirm) {
						jQuery(document).trigger('trashContact', [$this]);
					} else {
						swal('Cancelled', 'Your contact is safe', 'error');
					}
				}
			);
		});

		//delete contact
		jQuery(document).on('trashContact', function (event, $this) {
			const action = 'cp_trash_contact',
				list_id = $this.data('list'),
				user_id = $this.data('user-id'),
				email_id = $this.data('email'),
				mailer = $this.data('mailer'),
				data = {
					list_id,
					user_id,
					email_id,
					action,
					mailer,
					security_nonce: jQuery('#delete-contact-nonce').val(),
				};

			jQuery.ajax({
				url: ajaxurl,
				data,
				method: 'POST',
				dataType: 'JSON',
				success(result) {
					if (result.status === 'success') {
						swal({
							title: 'Removed!',
							text: 'The contact you have selected is removed.',
							type: 'success',
							timer: 2000,
							showConfirmButton: false,
						});
					} else {
						swal({
							title: 'Error!',
							text: 'Something went wrong! Please try again.',
							type: 'error',
							timer: 2000,
							showConfirmButton: false,
						});
					}
					setTimeout(function () {
						document.location = document.location;
					}, 800);
				},
			});
		});
	});

	jQuery('.delete-list').on('click', function (e) {
		e.preventDefault();

		const action = 'cp_is_list_assigned';
		const list_id = jQuery(this).data('list-id');
		const data = {
			list_id,
			action,
			security_nonce: cplus_contact_nonce.cp_is_list_assigned,
		};
		const $this = jQuery(this);

		jQuery.ajax({
			url: ajaxurl,
			data,
			method: 'POST',
			dataType: 'JSON',
			success(result) {
				if (result.message === 'no') {
					swal(
						{
							title: 'Are you sure?',
							text: 'You will not be able to recover this list!',
							type: 'warning',
							showCancelButton: true,
							confirmButtonColor: '#DD6B55',
							confirmButtonText: 'Yes, delete it!',
							cancelButtonText: 'No, cancel it!',
							closeOnConfirm: false,
							closeOnCancel: false,
							showLoaderOnConfirm: true,
						},
						function (isConfirm) {
							if (isConfirm) {
								jQuery(document).trigger('trashStyle', [$this]);
							} else {
								swal(
									'Cancelled',
									'Your campaign is safe ',
									'error'
								);
							}
						}
					);
				} else {
					const assigned_to_list = result.assigned_to;
					const style_count = result.style_count;
					let ulstring = '<ul>';
					let style_countStr;
					jQuery.each(assigned_to_list, function (index, value) {
						if (index > 2) {
							return false;
						}
						jQuery.each(value, function (style, link) {
							ulstring +=
								"<li><a target='_blank' href='" +
								link +
								"'>" +
								style +
								'</a></li>';
						});
					});

					if (assigned_to_list.length > 3) {
						ulstring += '<li>& more ...</li>';
					}
					ulstring += '</ul>';

					if (style_count > 1) {
						style_countStr = style_count + ' Styles -';
					} else {
						style_countStr = style_count + ' Style -';
					}

					swal({
						title: 'Error!',
						html: true,
						text:
							'You can not delete this campaign as it is being used in' +
							style_countStr +
							ulstring +
							'Please change submission settings of above and try again.',
						type: 'error',
					});
					return false;
				}
			},
		});
	});

	jQuery(document).on('trashStyle', function (e, $this) {
		const ok = true;
		if (ok) {
			const action = 'cp_trash_list';
			const list_id = $this.data('list-id');
			const list_mailer = $this.data('list-mailer');
			const data = {
				action,
				list_id,
				mailer: list_mailer,
				security_nonce: cplus_contact_nonce.cp_delete_list,
			};

			jQuery.ajax({
				url: ajaxurl,
				data,
				method: 'POST',
				dataType: 'JSON',
				success(result) {
					if (result.status === 'success') {
						swal({
							title: 'Removed!',
							text: 'The campaign list you have selected is removed.',
							type: 'success',
							timer: 2000,
							showConfirmButton: false,
						});
					} else {
						swal({
							title: 'Error!',
							text: 'Something went wrong! Please try again.',
							type: 'error',
							timer: 2000,
							showConfirmButton: false,
						});
					}
					setTimeout(function () {
						document.location = document.location;
					}, 800);
				},
			});
		}
	});

	jQuery(document).on('click', '.bsf-cntlst-top-search-submit', function () {
		jQuery('.bsf-cntlst-top-search').trigger('submit');
	});

	jQuery(document).ready(function () {
		if (
			jQuery('.bsf-contact-list-top-search').hasClass(
				'bsf-cntlist-top-search-act'
			)
		) {
			jQuery('.bsf-cntlst-top-search-input').focus().trigger('click');
		}
		jQuery('.has-tip').frosty();
	});
})();
