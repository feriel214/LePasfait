/* eslint-env jquery */
(function ($) {
	$(document).ready(function () {
		$('.smile-select').each(function (index, el) {
			$(el).on('change', function () {
				//  Partial Refresh -   Apply text-align, border-style etc.
				const data_element = $(el);
				const data_element_val = $(el).val();
				const data_css_preview =
					data_element.attr('data-css-preview') || '';
				const data_element_selector =
					data_element.attr('data-css-selector') || '';
				const data_property =
					data_element.attr('data-css-property') || '';
				const data_unit = data_element.attr('data-unit') || 'px';
				const data_value = data_element_val;
				partial_refresh_dropdown(
					data_css_preview,
					data_element_selector,
					data_property,
					data_unit,
					data_value
				);
				$(document).trigger('smile-select-change', [el]);
				$(document).trigger('smile-select-dropdown-change', [
					el,
					data_value,
				]);
			});
		});

		//  Partial Refresh
		//  -   Apply text-align, border-style etc.
		const a = $('.smile-select');
		const o = $('.smile-select').val();
		const css_preview = a.attr('data-css-preview') || '';
		const selector = a.attr('data-css-selector') || '';
		const property = a.attr('data-css-property') || '';
		const unit = a.attr('data-unit') || 'px';
		const value = o;
		partial_refresh_dropdown(css_preview, selector, property, unit, value);
	});

	function partial_refresh_dropdown(
		css_preview,
		selector,
		property,
		unit,
		value
	) {
		//  apply css by - inline
		if (
			css_preview !== 1 ||
			null === css_preview ||
			'undefined' === css_preview
		) {
			jQuery('#smile_design_iframe')
				.contents()
				.find(selector)
				.css(property, value);
		}
		//  apply css by - after css generation
		jQuery(document).trigger('updated', [
			css_preview,
			selector,
			property,
			value,
			unit,
		]);
	}
})(jQuery);
