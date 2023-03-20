/* eslint-env jquery */
/* eslint-disable no-unused-vars */
function updateHTML(html, target) {
	html = htmlEntities(html);
	jQuery('#' + target).val(html);
}

function htmlEntities(str) {
	return String(str)
		.replace(/&/g, '&amp;')
		.replace(/</g, '&lt;')
		.replace(/>/g, '&gt;')
		.replace(/'/g, '&#39;')
		.replace(/&quot;/g, '"');
}

function setFocusElement(element) {
	const section = jQuery('.' + element).parents('.cp-customizer-tab:first');
	const section_id = jQuery(section).attr('id');
	jQuery('.cp-vertical-nav')
		.find('a')
		.each(function (i, a) {
			const data_sec_id = jQuery(a).attr('data-section-id');
			if (section_id === data_sec_id) {
				if (!jQuery(a).hasClass('active')) {
					jQuery(a).trigger('click');
				}
			}
		});

	const accordion_anchor = jQuery('.' + element)
		.parents('.accordion-frame:first')
		.find('a.heading');
	const accordion_content = jQuery('.' + element)
		.parents('.accordion-frame:first')
		.find('.content');
	if (accordion_anchor.hasClass('collapsed')) {
		jQuery('.accordion-frame a.heading').addClass('collapsed');
		jQuery('.accordion-frame .content').slideUp();
		accordion_anchor.removeClass('collapsed');
		accordion_content.slideDown();
	}
	const ID = jQuery('.' + element).attr('id');

	if (
		!jQuery('.' + element)
			.parents('.smile-element-container')
			.hasClass('cp-set-focus')
	) {
		setTimeout(function () {
			jQuery('.smile-element-container').removeClass('cp-set-focus');

			jQuery('.' + element)
				.parents('.smile-element-container')
				.addClass('cp-hl-active cp-set-focus');

			jQuery('.design-form').animate(
				{
					scrollTop:
						jQuery('#' + ID)
							.parents('.smile-element-container')
							.offset().top - 100,
				},
				1000
			);
			setTimeout(function () {
				jQuery('.' + element)
					.parents('.smile-element-container')
					.removeClass('cp-hl-active');
				jQuery('.' + element).trigger('focus');
			}, 2000);
		}, 300);
	} else {
		jQuery('.' + element)
			.parents('.smile-element-container')
			.addClass('cp-hl-active');
		setTimeout(function () {
			jQuery('.' + element)
				.parents('.smile-element-container')
				.removeClass('cp-hl-active');
		}, 2000);
	}
	jQuery('body').trigger('click');
}

function customizerLoaded() {
	jQuery(document).trigger('customize_loaded');
}
