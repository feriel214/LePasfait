/* eslint-env jquery */
$ = jQuery;
jQuery(document).ready(function () {
	jQuery(document).on('smile_panel_loaded', function () {
		const smile_panel = jQuery('.customize').data('style');
		let smile_panel_id = smile_panel;

		function _getFromFieldShadow(value, min, max, elem) {
			let val;

			val = parseFloat(value);
			if (isNaN(val)) {
				val = 0;
			} else if (val < min) {
				val = min;
				value = min;
			} else if (val > max) {
				val = max;
				value = max;
			}
			elem.val(value);

			return val;
		}

		function BoxShadow(options) {
			this.backgroundElement =
				options.backgroundElement ||
				jQuery('#accordion-' + smile_panel_id + ' #box-shadow-wrapper');
			this.htmlElement =
				options.htmlElement ||
				jQuery('#accordion-' + smile_panel_id + ' #box-shadow-object');
			this.htmlCode =
				options.htmlCode ||
				jQuery('#accordion-' + smile_panel_id + ' #box-shadow-code');
			this.horizontal = options.horizontal || 0;
			this.vertical = options.vertical || 0;
			this.blur = options.blur || 0;
			this.spread = options.spread || 0;
			this.shadowColor = options.shadowColor || 'rgba(0, 0, 0, 0.5)';
			this.type = options.type || 'none';
		}

		BoxShadow.prototype.refresh = function () {
			let code = '';
			code += 'type:' + this.type + '|';
			code += 'horizontal:' + this.horizontal + '|';
			code += 'vertical:' + this.vertical + '|';
			code += 'blur:' + this.blur + '|';
			code += 'spread:' + this.spread + '|';
			code += 'color:' + this.shadowColor;

			this.htmlCode.html(code);
		};

		BoxShadow.prototype.partial = function () {
			const v_horizontal = this.horizontal || '0',
				v_vertical = this.vertical || '0',
				v_blur = this.blur || '0',
				v_spread = this.spread || '0',
				v_shadowColor = this.shadowColor || '';

			let v_type = this.type || '';

			//	Set empty for outset. Cause, Default its outset.
			if (v_type === 'outset') {
				v_type = '';
			}

			const box_shadow =
				v_horizontal +
				'px ' +
				v_vertical +
				'px ' +
				v_blur +
				'px ' +
				v_spread +
				'px ' +
				v_shadowColor +
				' ' +
				v_type;

			//	Partial refresh - Apply to [box-shadow]
			partial_refresh_box_shadow(box_shadow);
		};

		function _getAllValuesFromPanelBoxShadow() {
			const smile_panel_cust = jQuery('.customize').data('style');
			smile_panel_id = smile_panel_cust;
			const options = {};
			options.horizontal = parseFloat(
				jQuery(
					'#accordion-' + smile_panel_id + ' #horizontal-length'
				).val()
			);
			options.vertical = parseFloat(
				jQuery(
					'#accordion-' + smile_panel_id + ' #vertical-length'
				).val()
			);
			options.blur = parseFloat(
				jQuery('#accordion-' + smile_panel_id + ' #blur-radius').val()
			);
			options.shadowColor = jQuery(
				'#accordion-' + smile_panel_id + ' #shadow-color'
			).val();
			options.opacity = parseFloat(
				jQuery(
					'#accordion-' + smile_panel_id + ' #shadow-opacity'
				).val()
			);
			options.type = jQuery(
				'#accordion-' + smile_panel_id + ' #smile_shadow_type'
			).val();
			options.spread = parseFloat(
				jQuery('#accordion-' + smile_panel_id + ' #spread-field').val()
			);
			return options;
		}

		const opts = _getAllValuesFromPanelBoxShadow();
		let boxShadow = new BoxShadow(opts);
		boxShadow.refresh();

		// Slider bars.
		jQuery(
			'#accordion-' + smile_panel_id + ' #slider-horizontal-bs'
		).slider({
			value: jQuery(
				'#accordion-' + smile_panel_id + ' #horizontal-length'
			).val(),
			min: -200,
			max: 200,
			step: 1,
			slide(event, ui) {
				const val = _getFromFieldShadow(
					ui.value,
					-200,
					200,
					jQuery(
						'#accordion-' + smile_panel_id + ' #horizontal-length'
					)
				);
				boxShadow.horizontal = val;

				const leftMarginToSlider = jQuery(this)
					.find('.ui-slider-handle')
					.css('left');
				jQuery(this)
					.find('.range-quantity')
					.css('width', leftMarginToSlider);

				boxShadow.partial();
			},
			stop() {
				// boxShadow.refresh();
			},
			create() {
				const leftMarginToSlider = jQuery(this)
					.find('.ui-slider-handle')
					.css('left');
				jQuery(this)
					.find('.range-quantity')
					.css('width', leftMarginToSlider);
			},
		});

		jQuery('#accordion-' + smile_panel_id + ' #slider-vertical-bs').slider({
			value: jQuery(
				'#accordion-' + smile_panel_id + ' #vertical-length'
			).val(),
			min: -200,
			max: 200,
			step: 1,
			slide(event, ui) {
				const val = _getFromFieldShadow(
					ui.value,
					-200,
					200,
					jQuery('#accordion-' + smile_panel_id + ' #vertical-length')
				);
				boxShadow.vertical = val;

				const leftMarginToSlider = jQuery(this)
					.find('.ui-slider-handle')
					.css('left');
				jQuery(this)
					.find('.range-quantity')
					.css('width', leftMarginToSlider);

				boxShadow.partial();
			},
			stop() {
				// boxShadow.refresh();
			},
			create() {
				const leftMarginToSlider = jQuery(this)
					.find('.ui-slider-handle')
					.css('left');
				jQuery(this)
					.find('.range-quantity')
					.css('width', leftMarginToSlider);
			},
		});

		jQuery('#accordion-' + smile_panel_id + ' #slider-blur-bs').slider({
			value: jQuery(
				'#accordion-' + smile_panel_id + ' #blur-radius'
			).val(),
			min: 0,
			max: 300,
			step: 1,
			slide(event, ui) {
				const val = _getFromFieldShadow(
					ui.value,
					0,
					300,
					jQuery('#accordion-' + smile_panel_id + ' #blur-radius')
				);
				boxShadow.blur = val;
				const leftMarginToSlider = jQuery(this)
					.find('.ui-slider-handle')
					.css('left');
				jQuery(this)
					.find('.range-quantity')
					.css('width', leftMarginToSlider);

				boxShadow.partial();
			},
			stop() {
				// boxShadow.refresh();
			},
			create() {
				const leftMarginToSlider = jQuery(this)
					.find('.ui-slider-handle')
					.css('left');
				jQuery(this)
					.find('.range-quantity')
					.css('width', leftMarginToSlider);
			},
		});

		jQuery('#accordion-' + smile_panel_id + ' #slider-spread-field').slider(
			{
				value: jQuery(
					'#accordion-' + smile_panel_id + ' #spread-field'
				).val(),
				min: -200,
				max: 200,
				step: 1,
				slide(event, ui) {
					const val = _getFromFieldShadow(
						ui.value,
						-200,
						200,
						jQuery(
							'#accordion-' + smile_panel_id + ' #spread-field'
						)
					);
					boxShadow.spread = val;
					const leftMarginToSlider = jQuery(this)
						.find('.ui-slider-handle')
						.css('left');
					jQuery(this)
						.find('.range-quantity')
						.css('width', leftMarginToSlider);

					boxShadow.partial();
				},
				stop() {
					// boxShadow.refresh();
				},
				create() {
					const leftMarginToSlider = jQuery(this)
						.find('.ui-slider-handle')
						.css('left');
					jQuery(this)
						.find('.range-quantity')
						.css('width', leftMarginToSlider);
				},
			}
		);

		jQuery('#accordion-' + smile_panel_id + ' #slider-opacity-bs').slider({
			value: jQuery(
				'#accordion-' + smile_panel_id + ' #shadow-opacity'
			).val(),
			min: 0,
			max: 1,
			step: 0.01,
			slide() {
				const leftMarginToSlider = jQuery(this)
					.find('.ui-slider-handle')
					.css('left');
				jQuery(this)
					.find('.range-quantity')
					.css('width', leftMarginToSlider);

				boxShadow.partial();
			},
			stop() {
				// boxShadow.refresh();
			},
			create() {
				const leftMarginToSlider = jQuery(this)
					.find('.ui-slider-handle')
					.css('left');
				jQuery(this)
					.find('.range-quantity')
					.css('width', leftMarginToSlider);
			},
		});

		jQuery('#accordion-' + smile_panel_id + ' #horizontal-length').on(
			'keyup change',
			function () {
				const val = _getFromFieldShadow(
					jQuery(this).val(),
					-200,
					200,
					jQuery(
						'#accordion-' + smile_panel_id + ' #horizontal-length'
					)
				);
				if (val !== false) {
					boxShadow.horizontal = val;
					// boxShadow.refresh();
					jQuery(
						'#accordion-' +
							smile_panel_id +
							' #slider-horizontal-bs'
					).slider('value', val);

					const leftMarginToSlider = jQuery(
						'#accordion-' +
							smile_panel_id +
							' #slider-horizontal-bs'
					)
						.find('.ui-slider-handle')
						.css('left');
					jQuery(
						'#accordion-' +
							smile_panel_id +
							' #slider-horizontal-bs'
					)
						.find('.range-quantity')
						.css('width', leftMarginToSlider);

					boxShadow.partial();
				}
			}
		);

		jQuery('#accordion-' + smile_panel_id + ' #vertical-length').on(
			'keyup change',
			function () {
				const val = _getFromFieldShadow(
					jQuery(this).val(),
					-200,
					200,
					jQuery('#accordion-' + smile_panel_id + ' #vertical-length')
				);
				if (val !== false) {
					boxShadow.vertical = val;
					// boxShadow.refresh();
					jQuery(
						'#accordion-' + smile_panel_id + ' #slider-vertical-bs'
					).slider('value', val);

					const leftMarginToSlider = jQuery(
						'#accordion-' + smile_panel_id + ' #slider-vertical-bs'
					)
						.find('.ui-slider-handle')
						.css('left');
					jQuery(
						'#accordion-' + smile_panel_id + ' #slider-vertical-bs'
					)
						.find('.range-quantity')
						.css('width', leftMarginToSlider);

					boxShadow.partial();
				}
			}
		);

		jQuery('#accordion-' + smile_panel_id + ' #blur-radius').on(
			'keyup change',
			function () {
				const val = _getFromFieldShadow(
					jQuery(this).val(),
					0,
					300,
					jQuery('#accordion-' + smile_panel_id + ' #blur-radius')
				);
				if (val !== false) {
					boxShadow.blur = val;
					// boxShadow.refresh();
					jQuery(
						'#accordion-' + smile_panel_id + ' #slider-blur-bs'
					).slider('value', val);

					const leftMarginToSlider = jQuery(
						'#accordion-' + smile_panel_id + ' #slider-blur-bs'
					)
						.find('.ui-slider-handle')
						.css('left');
					jQuery('#accordion-' + smile_panel_id + ' #slider-blur-bs')
						.find('.range-quantity')
						.css('width', leftMarginToSlider);

					boxShadow.partial();
				}
			}
		);

		jQuery('#accordion-' + smile_panel_id + ' #spread-field').on(
			'keyup change',
			function () {
				const val = _getFromFieldShadow(
					jQuery(this).val(),
					-200,
					200,
					jQuery('#accordion-' + smile_panel_id + ' #spread-field')
				);
				if (val !== false) {
					boxShadow.spread = val;
					// boxShadow.refresh();
					jQuery('#slider-spread-field').slider('value', val);

					const leftMarginToSlider = jQuery(
						'#accordion-' + smile_panel_id + ' #slider-spread-field'
					)
						.find('.ui-slider-handle')
						.css('left');
					jQuery(
						'#accordion-' + smile_panel_id + ' #slider-spread-field'
					)
						.find('.range-quantity')
						.css('width', leftMarginToSlider);

					boxShadow.partial();
				}
			}
		);

		jQuery('#accordion-' + smile_panel_id + ' #smile_shadow_type').on(
			'change',
			function () {
				const type = jQuery(this).val();
				if (type !== 'none') {
					jQuery('.smile-shadow-options').slideDown(600);
				} else {
					jQuery('.smile-shadow-options').slideUp(600);
				}
				boxShadow.type = type;

				boxShadow.partial();

				// boxShadow.refresh();
			}
		);

		jQuery('#accordion-' + smile_panel_id + ' #shadow-color').on(
			'change',
			function () {
				const color = jQuery(this).val();
				boxShadow.shadowColor = color;

				boxShadow.partial();

				// boxShadow.refresh();
			}
		);

		function partial_refresh_box_shadow(value) {
			//	Generate CSS
			const a = jQuery(
				'#accordion-' + smile_panel_id + ' #box-shadow-code'
			);
			const selector = a.attr('data-css-selector') || '';
			const css_preview = a.attr('data-css-preview') || '';
			const unit = a.attr('data-unit') || 'px';
			const property = 'box-shadow';

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

		//	Generate - Border hidden value with ConCat
		jQuery('#button-save-' + smile_panel + ' > span').trigger('click');
		jQuery('#button-save-' + smile_panel).on('click', function () {
			const panel_opts = _getAllValuesFromPanelBoxShadow();
			boxShadow = new BoxShadow(panel_opts);
			boxShadow.refresh();
		});
	});
});
