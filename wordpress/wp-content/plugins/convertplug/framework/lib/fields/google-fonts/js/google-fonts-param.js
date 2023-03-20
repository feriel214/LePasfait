/* eslint-env jquery */
$ = jQuery;
jQuery(document).on('smile_panel_loaded', function (e, smile_panel) {
	const smile_panel_id = smile_panel;
	function GoogleFonts() {
		this.htmlCode = jQuery(
			'#accordion-' + smile_panel_id + ' .smile-input-gfonts'
		);
	}

	GoogleFonts.prototype.refresh = function () {
		let inputCode = this.htmlCode.val();
		const use_in = this.htmlCode.data('use-in');

		if (use_in === 'editor') {
			if (typeof inputCode !== 'undefined' && inputCode !== null) {
				const split_font = inputCode.split('|'); //font_family=xyz|font_call=xyz:100,200
				const font_family = split_font[0]; //font_family=xyz
				const font_call = split_font[1]; //font_call=xyz

				inputCode = font_family + '|' + font_call + '|';
				jQuery(this)
					.closest('.smile-element-container')
					.find('.smile-input-gfonts')
					.val(inputCode);
				this.htmlCode.html(inputCode);
				this.htmlCode.trigger('change');
			}
		} else {
			const sel = jQuery(this)
				.closest('.smile-element-container')
				.find('.smile-input-gfonts');
			const vl = jQuery(this).find('option:selected').val();
			sel.val(vl);
			sel.trigger('change');
		}
	};

	GoogleFonts.prototype.update = function (sel, vl) {
		const use_in = sel.data('use-in');
		if (use_in !== 'editor') {
			if (typeof vl !== 'undefined' && vl !== null) {
				sel.val(vl);
				sel.trigger('change');
			}
		}
	};

	function process_vc_gfont_fields($select, random_num, is_font_change) {
		const gFont = $select
			.closest('.smile-element-container')
			.find('.smile-input-gfonts');
		let gFont_val = gFont.val() || '';
		let val = '';
		let fonts = [];

		if (is_font_change === 'false') {
			if (gFont_val !== '') {
				const gfont_name_attr = gFont_val.split('|');
				const gfont_name = gfont_name_attr[0].split(':');
				val = gfont_name[1];
				if (val === '') val = 'default';
			} else val = 'default';

			$select.find('option').each(function (index, option) {
				//	Add all fonts
				val = jQuery(option).val();
				if (typeof val !== 'undefined' && val !== null && val !== '') {
					fonts.push(val);
				}
			});
		} else {
			val = $select.find('option:selected').val();
		}

		gFont_val = gFont_val.split(',');

		// combine fonts from "ultimate_selected_fonts" option and style specific fonts
		const combine_arr = fonts.concat(gFont_val);

		// avoid duplicate fonts , create unique font array
		const uniqueFonts = [];
		jQuery.each(combine_arr, function (i, el) {
			if (jQuery.inArray(el, uniqueFonts) === -1) uniqueFonts.push(el);
		});

		fonts = uniqueFonts.join();

		//	Add all selected Google fonts
		if (gFont.data('use-in') === 'editor') {
			gFont.val(uniqueFonts);
		}
	}

	function _initCallGoogleFonts() {
		//	Ajax Call - on Initial
		jQuery('.ultimate_google_font_param_block select').each(function (
			index
		) {
			const $select = jQuery(this);
			const random_num = Math.floor(Math.random() * 10000000 + index);
			process_vc_gfont_fields($select, random_num, 'false');
		});
	}
	GoogleFonts = new GoogleFonts();
	GoogleFonts.refresh();

	//	init
	_initCallGoogleFonts();

	jQuery('.ultimate_google_font_param_block  select').each(function (
		index,
		element
	) {
		//  Partial Refresh
		//  -   Apply text-align, border-style etc.
		const a = jQuery(element);
		const o = a.val();
		const css_preview = a.attr('data-css-preview') || '';
		const selector = a.attr('data-css-selector') || '';
		const property = a.attr('data-css-property') || '';
		const unit = a.attr('data-unit') || 'px';
		const value = o;
		partial_refresh_font_family(
			css_preview,
			selector,
			property,
			unit,
			value
		);

		a.on('change', function () {
			const sel = jQuery(this)
				.closest('.smile-element-container')
				.find('.smile-input-gfonts');
			const vl = jQuery(this).find('option:selected').val();
			GoogleFonts.update(sel, vl);

			//  Partial Refresh
			//  -   Apply text-align, border-style etc.
			const google_font_param = jQuery(this);
			const val_google_font_param = google_font_param.val();
			const data_css_preview =
				google_font_param.attr('data-css-preview') || '';
			const data_selector =
				google_font_param.attr('data-css-selector') || '';
			const data_property =
				google_font_param.attr('data-css-property') || '';
			const data_unit = google_font_param.attr('data-unit') || 'px';
			const data_value = val_google_font_param;
			partial_refresh_font_family(
				data_css_preview,
				data_selector,
				data_property,
				data_unit,
				data_value
			);

			jQuery(document).trigger('smile-google-font-change', [
				google_font_param,
			]);
		});
	});

	function partial_refresh_font_family(
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
});
