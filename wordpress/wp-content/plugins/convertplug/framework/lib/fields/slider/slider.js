/* eslint-env jquery */
jQuery(document).ready(function (jQuery) {
	const slider_input = jQuery('.smile-slider');
	jQuery.each(slider_input, function () {
		const $this = jQuery(this);
		const slider_id = $this.attr('id').replace('smile_', 'slider_');
		const input_id = $this.attr('id');
		const val = $this.val();
		const minimum = $this.data('min');
		const maximum = $this.data('max');
		const step = $this.data('step');

		//  Partial Refresh
		//  -	Apply to height, width, font-size, line-height etc.
		let css_preview = $this.attr('data-css-preview') || '';
		let selector = $this.attr('data-css-selector') || '';
		let property = $this.attr('data-css-property') || '';
		let value = $this.val();
		let unit = $this.attr('data-unit') || 'px';
		partial_slider_css(css_preview, selector, property, value, unit);

		jQuery('#' + input_id).on('keyup change', function () {
			value = jQuery(this).val();
			jQuery('#' + slider_id).slider('value', value);
			const leftMarginToSlider = jQuery('#' + slider_id)
				.find('.ui-slider-handle')
				.css('left');
			jQuery('#' + slider_id)
				.find('.range-quantity')
				.css('width', leftMarginToSlider);

			//  Partial Refresh
			//  -	Apply to height, width, font-size, line-height etc.
			const a = jQuery('#' + input_id);
			css_preview = a.attr('data-css-preview') || '';
			selector = a.attr('data-css-selector') || '';
			property = a.attr('data-css-property') || '';
			unit = a.attr('data-unit') || 'px';
			partial_slider_css(css_preview, selector, property, value, unit);

			//  Trigger
			jQuery(document).trigger('cp-slider-change', [a, value]);
		});
		jQuery('#' + slider_id).slider({
			value: val,
			min: minimum,
			max: maximum,
			step,
			slide(event, ui) {
				jQuery('#' + input_id)
					.val(ui.value)
					.keyup();
				const leftMarginToSlider = jQuery('#' + slider_id)
					.find('.ui-slider-handle')
					.css('left');
				jQuery('#' + slider_id)
					.find('.range-quantity')
					.css('width', leftMarginToSlider);

				//  Partial Refresh
				//  -	Apply to height, width, font-size, line-height etc.
				const a = jQuery('#' + input_id);
				css_preview = a.attr('data-css-preview') || '';
				selector = a.attr('data-css-selector') || '';
				property = a.attr('data-css-property') || '';
				unit = a.attr('data-unit') || 'px';
				partial_slider_css(
					css_preview,
					selector,
					property,
					value,
					unit
				);

				//  Trigger
				jQuery(document).trigger('cp-slider-slide', [a, value]);
			},
		});
		jQuery('#' + input_id).val(jQuery('#' + slider_id).slider('value'));
		const leftMarginToSlider = jQuery('#' + slider_id)
			.find('.ui-slider-handle')
			.css('left');
		jQuery('#' + slider_id)
			.find('.range-quantity')
			.css('width', leftMarginToSlider);

		//  apply css by - inline
		function partial_slider_css(
			ps_css_preview,
			ps_selector,
			ps_property,
			ps_value,
			ps_unit
		) {
			if (
				ps_css_preview !== 1 ||
				null === ps_css_preview ||
				'undefined' === ps_css_preview
			) {
				const apply_to = jQuery('#smile_design_iframe')
					.contents()
					.find(ps_selector);
				switch (ps_property) {
					case 'padding-tb':
						apply_to.css({
							'padding-top': ps_value + 'px',
							'padding-bottom': ps_value + 'px',
						});
						break;
					case 'padding-lr':
						apply_to.css({
							'padding-left': ps_value + 'px',
							'padding-right': ps_value + 'px',
						});
						break;
					case 'margin-tb':
						apply_to.css({
							'margin-top': ps_value + 'px',
							'margin-bottom': ps_value + 'px',
						});
						break;
					case 'margin-lr':
						apply_to.css({
							'margin-left': ps_value + 'px',
							'margin-right': ps_value + 'px',
						});
						break;
					case 'width-max':
						apply_to.css({
							'max-width': ps_value + 'px',
							width: ps_value + 'px',
						});
						break;
					case 'width':
						apply_to.attr(ps_property, ps_value + 'px');
						apply_to.css(ps_property, ps_value + 'px');
						break;
					case 'height':
						apply_to.attr(ps_property, ps_value + 'px');
						apply_to.css(ps_property, ps_value + 'px');
						break;
					default:
						apply_to.css(ps_property, ps_value + 'px');
						break;
				}
			}
			//  apply css by - after css generation
			jQuery(document).trigger('updated', [
				ps_css_preview,
				ps_selector,
				ps_property,
				ps_value,
				ps_unit,
			]);
		}
	});
});
