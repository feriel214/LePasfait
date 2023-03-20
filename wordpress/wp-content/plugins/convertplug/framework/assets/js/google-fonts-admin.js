/* eslint-env jquery */
const $gf = jQuery.noConflict();
$gf(document).ready(function () {
	$gf('body').on('click', '#cplus-refresh-google-fonts', function () {
		$gf(this).next('.spinner').css({
			display: 'inline-block',
			float: 'none',
			'vertical-align': 'middle',
			visibility: 'visible',
		});
		const data = {
			action: 'cplus_ultimate_google_fonts_refresh',
			security: jQuery('#cplus_refresh_font_list_nonce').val(),
		};
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		$gf.post(ajaxurl, data, function (response) {
			const object = JSON.parse(response);
			const count = object.count;
			const msg = object.message;
			let dmsg;
			if (count === 0) {
				dmsg = msg;
			} else {
				dmsg = msg + ' Please wait, Page is reloading.';
				window.location.reload(true);
			}
			$gf('#vc-gf-msg')
				.html('<div class="updated">' + dmsg + '</div>')
				.hide();
			$gf('#vc-gf-msg').slideDown(300);
			$gf('#cplus-refresh-google-fonts')
				.next('.spinner')
				.css({ display: 'none' });
			setTimeout(function () {
				$gf('#vc-gf-msg').slideUp(300);
			}, 4000);
		});
	});
	$gf('body').on('click', '.add-google-font', function () {
		const button = $gf(this);
		const font_family = $gf(this).attr('data-font_family');
		const font_name = $gf(this).attr('data-font_name');
		if (!button.hasClass('font-added')) {
			button.next('.spinner').show();
			button
				.next('.spinner')
				.css({ float: 'right', visibility: 'visible' });

			const variants_array = new Array();
			const subsets_array = new Array();
			button
				.parent()
				.find('.variants')
				.find('.font-variant')
				.each(function (iv, variant) {
					const $v = $gf(variant);
					const temp_arr = {};
					const variant_value = $v.find('.font-variant-inputs').val();
					temp_arr.variant_value = variant_value;
					if ($v.find('.font-variant-inputs').is(':checked')) {
						temp_arr.variant_selected = true;
					} else {
						temp_arr.variant_selected = false;
					}
					variants_array.push(temp_arr);
				});
			button
				.parent()
				.find('.subsets')
				.find('.font-subset')
				.each(function (iv, subset) {
					const $s = $gf(subset);
					const temp_arr = {};
					const subset_value = $s.find('.font-subset-inputs').val();
					temp_arr.subset_value = subset_value;
					if ($s.find('.font-subset-inputs').is(':checked')) {
						temp_arr.subset_selected = true;
					} else {
						temp_arr.subset_selected = false;
					}
					subsets_array.push(temp_arr);
				});
			const data = {
				action: 'cplus_add_google_font',
				font_family,
				font_name,
				security_nonce: jQuery('#cp_add_font_nonce').val(),
				variants: variants_array,
				subsets: subsets_array,
			};
			$gf.post(ajaxurl, data, function () {
				button.next('.spinner').hide();
				button.val('Added in collection');
				button.addClass('font-added');
				let xclass = '';
				if (variants_array.length > 0 || subsets_array.length > 0) {
					xclass = 'have-variants';
				}

				let gshtml =
					'<div class="selected-font"><div class="selected-font-top fopened ' +
					xclass +
					'"><div class="font-header" style="font-family:\'' +
					font_name +
					'\'">' +
					font_name +
					'</div>';
				if (variants_array.length > 0)
					gshtml += '<i class="dashicons dashicons-arrow-down"></i>';
				gshtml +=
					'<div class="clear"></div></div><span class="font-delete" data-font_name="' +
					font_name +
					'"><i class="dashicons dashicons-no-alt"></i></span>';

				if (variants_array.length > 0 || subsets_array.length > 0) {
					gshtml +=
						'<div class="selected-font-content" style="display:block">';
					let temp_id = '';
					let temp;
					if ((temp = font_name.split(' '))) {
						const ctemp = temp.length;
						$gf.each(temp, function (i, val) {
							temp_id += val;
							if (ctemp - 1 !== i) temp_id += '-';
						});
					} else {
						temp_id = font_name;
					}

					let temp_subset_class = '';

					if (variants_array.length > 0) {
						gshtml += '<div class="selected-font-varient-wrapper">';
						$gf.each(variants_array, function (index, variant) {
							const lid =
								temp_id +
								'-dynamic-' +
								variant.variant_value +
								'-' +
								index;
							let font_style = "font-family:'" + font_name + "';";
							if (/italic/i.test(variant.variant_value)) {
								font_style += 'font-style:italic;';
							}
							let weight = 'normal';
							if ((weight = variant.variant_value.match(/\d+/))) {
								font_style += 'font-weight:' + weight + ';';
							}
							gshtml +=
								'<span class="font-variant"><input type="checkbox" id="' +
								lid +
								'" value="' +
								variant.variant_value +
								'" class="selected-variant-checkbox" /><label style="' +
								font_style +
								'" for="' +
								lid +
								'">' +
								variant.variant_value +
								'</label></span>';
						});
						gshtml += '</div>';
						temp_subset_class = 'selected-font-subset-wrapper';
					}

					if (subsets_array.length > 0) {
						gshtml += '<div class="' + temp_subset_class + '">';
						$gf.each(subsets_array, function (index, subset) {
							const lid =
								temp_id +
								'-dynamic-subset-' +
								subset.subset_value +
								'-' +
								index;
							gshtml +=
								'<span class="font-subset"><input type="checkbox" id="' +
								lid +
								'" value="' +
								subset.subset_value +
								'" class="selected-subset-checkbox" /><label for="' +
								lid +
								'">' +
								subset.subset_value +
								'</label></span>';
						});
						gshtml += '</div>';
					}

					gshtml +=
						'<input type="button" class="button alignleft update-google-font-button" value="Update font" data-font_name="' +
						font_name +
						'" /><span class="spinner fspinner"></span><div class="clear"></div></div>';
				}

				gshtml += '</div>';
				$gf('#fonts-selected-wrapper').prepend(gshtml);
			});
		}
	});
	$gf('body').on('click', '.font-delete', function () {
		$gf('.google-font-overlay').remove();
		$gf('.google-font-confirmation').remove();
		const font_name = $gf(this).attr('data-font_name');
		$gf('body').append('<div class="google-font-overlay"></div>');
		$gf('body').append(
			'<div class="google-font-confirmation"><div class="google-font-confirmation-header"><h3>Are you sure you want to remove this font?</h3></div><div class="google-font-message"><input type="button" id="" data-gfont_name="' +
				font_name +
				'" class="google-font-message-buttons google-font-message-delete gfont-buttons" value="Yes"/><input type="button" id="" class="google-font-message-buttons google-font-message-no-delete gfont-buttons" value="No"/></div><a href="javacript:void(0)" class="gfont-anchor-buttons gfont-buttons google-font-message-no-delete"><i class="dashicons dashicons-no-alt"></i></a></div>'
		);
		$gf('.google-font-overlay').fadeIn(100);
		$gf('.google-font-confirmation').fadeIn(100);
	});
	$gf('body').on('click', '.gfont-buttons', function () {
		if ($gf(this).hasClass('google-font-message-delete')) {
			const font_name = $gf(this).attr('data-gfont_name');
			const data = {
				action: 'cplus_delete_google_font',
				security_nonce: jQuery('#cp_delete_font_nonce').val(),
				font_name,
			};
			$gf.post(ajaxurl, data, function () {
				$gf('.font-delete').each(function () {
					const button = $gf(this);
					const bfont_name = $gf(this).attr('data-font_name');
					if (bfont_name === font_name) button.parent().remove();
				});
			});
		}
		$gf('.google-font-confirmation').fadeOut(200);
		$gf('.google-font-overlay').fadeOut(200);
	});
	$gf('body').on('click', '.update-google-font-button', function () {
		const font_name = $gf(this).attr('data-font_name');
		const parent = $gf(this).parent();
		const variant_array = new Array();
		const subset_array = new Array();
		$gf(parent)
			.find('.font-variant')
			.each(function (index, variant_wrap) {
				const temp_array = {};
				const variant_checkbox = $gf(variant_wrap).find(
					'.selected-variant-checkbox'
				);
				temp_array.variant_value = $gf(variant_checkbox).val();
				if ($gf(variant_checkbox).is(':checked'))
					temp_array.variant_selected = true;
				else temp_array.variant_selected = false;
				variant_array.push(temp_array);
			});
		$gf(parent)
			.find('.font-subset')
			.each(function (index, subset_wrap) {
				const temp_array = {};
				const subset_checkbox = $gf(subset_wrap).find(
					'.selected-subset-checkbox'
				);
				temp_array.subset_value = $gf(subset_checkbox).val();
				if ($gf(subset_checkbox).is(':checked'))
					temp_array.subset_selected = true;
				else temp_array.subset_selected = false;
				subset_array.push(temp_array);
			});
		const data = {
			action: 'cplus_update_google_font',
			security_nonce: jQuery('#cp_update_font_nonce').val(),
			font_name,
			variants: variant_array,
			subsets: subset_array,
		};
		$gf(this).next('.fspinner').addClass('fspinner-show');
		$gf.post(ajaxurl, data, function () {
			$gf('.fspinner').removeClass('fspinner-show');
		});
	});
	$gf('body').on('click', '.selected-font-top', function () {
		if ($gf(this).hasClass('fopened')) {
			$gf(this).parent().find('.selected-font-content').slideUp(200);
			$gf(this).removeClass('fopened');
			return;
		}
		$gf('.selected-font .selected-font-content').slideUp(200);
		$gf('.selected-font-top').removeClass('fopened');
		$gf(this).addClass('fopened');
		$gf(this).parent().find('.selected-font-content').slideToggle(200);
	});
	//get google fonts
	cplus_get_google_fonts();
});
function cplus_get_google_fonts() {
	$gf('#load-more').show();
	const $list_wrapper = $gf('#fonts-list-wrapper');
	const gstart = $list_wrapper.attr('data-gstart');
	const gfetch = $list_wrapper.attr('data-gfetch');
	const gsearch = $gf('#search_gfont').val();
	const data = {
		action: 'cplus_get_google_fonts',
		start: gstart,
		fetch: gfetch,
		search: gsearch,
		security_nonce: jQuery('#cp_search_font_nonce').val(),
	};
	$gf.post(ajaxurl, data, function (response) {
		const object = JSON.parse(response);
		const font_count = object.fonts_count;
		const fonts = object.fonts;
		const is_search = object.is_search;
		let ghtml = '';
		if (font_count === 0 && is_search === 'false') {
			ghtml =
				'<div class="gfont">It seems you don\'t have any Google Fonts yet. But you can download them now with <a href="javascript:void(0)" id="cplus-refresh-google-fonts">just a click.</a> <span class="spinner"></span></div>';
			$list_wrapper.html(ghtml);
		} else if (fonts.length === 0) {
			ghtml = '<div class="gfont">';
			ghtml +=
				'Bummer, there are no font families that match. Try with other search keyword';
			ghtml += '</div>';
			$list_wrapper.html(ghtml);
		} else {
			ghtml = convert_json_to_html(fonts);
			$list_wrapper.append(ghtml);
		}
		$gf('#load-more').hide();
		$list_wrapper.attr('data-gstart', parseInt(gstart) + parseInt(gfetch));
	});
}
function convert_json_to_html(object) {
	let html = '';
	$gf.each(object, function (index, gfont) {
		let font_call = gfont.font_call;
		const font_name = gfont.font_name;
		const font_variants = gfont.variants;
		const font_subsets = gfont.subsets;
		const selected = gfont.selected;
		let temp_id = '';
		let temp;
		if ((temp = font_name.split(' '))) {
			const ctemp = temp.length;
			$gf.each(temp, function (i, val) {
				temp_id += val;
				if (ctemp - 1 !== i) temp_id += '-';
			});
		} else {
			temp_id = font_name;
		}

		const variants_length = font_variants.length;
		const subsets_length = font_subsets.length;
		let button_text = 'Add to Collection';
		let button_class = '';

		if (selected === 'true') {
			button_text = 'Added in collection';
			button_class = 'font-added';
		}
		html += '<div class="gfont">';
		html +=
			'<div class="font-header" style="font-family:\'' +
			font_name +
			'\'">' +
			font_name +
			'</div>';
		html +=
			'<input type="button" class="add-google-font alignright ' +
			button_class +
			'" data-font_family="' +
			font_call +
			'" data-font_name="' +
			font_name +
			'" value="' +
			button_text +
			'"/><span class="spinner"></span><div class="clear"></div>';
		if (variants_length > 1) {
			font_call += ':';
			html += '<span class="variants">';
			$gf.each(font_variants, function (vindex, variant) {
				if (variant !== 'regular') {
					let font_style = "font-family:'" + font_name + "';";
					if (/italic/i.test(variant)) {
						font_style += 'font-style:italic;';
					}
					let weight = 'normal';
					if ((weight = variant.match(/\d+/))) {
						font_style += 'font-weight:' + weight + ';';
					}
					html += '<span class="font-variant">';
					html +=
						'<input type="checkbox" class="font-variant-inputs" value="' +
						variant +
						'" id="' +
						temp_id +
						'-' +
						variant +
						'-' +
						vindex +
						'"/>';
					html +=
						'<label for="' +
						temp_id +
						'-' +
						variant +
						'-' +
						vindex +
						'" style="' +
						font_style +
						'">' +
						variant +
						'</label>';
					html += '</span>';
					font_call += variant;
					if (variants_length > 0 && variants_length - 1 !== vindex) {
						font_call += ',';
					}
				}
			});
			html += '</span>';
		} //end of varients
		if (subsets_length > 1) {
			html += '<span class="subsets">';
			$gf.each(font_subsets, function (sindex, subset) {
				html += '<span class="font-subset">';
				html +=
					'<input type="checkbox" class="font-subset-inputs" value="' +
					subset +
					'" id="' +
					temp_id +
					'-' +
					subset +
					'-' +
					sindex +
					'"/>';
				html += '</span>';
			});
			html += '</span>';
		} //end of subsets
		html += '<div class="clear"></div>';
		html += '</div>';
		$gf('head').append(
			'<link href="https://fonts.googleapis.com/css?family=' +
				font_call +
				'" type="text/css" media="all" rel="stylesheet"/>'
		);
	});
	return html;
}
$gf(window).on('scroll', function () {
	if (
		$gf(window).height() + $gf(window).scrollTop() ===
		$gf(document).height()
	) {
		const gsearch = $gf('#search_gfont').val();
		if (gsearch === '') cplus_get_google_fonts();
	}
});
$gf(document).ready(function () {
	let typingTimer; //timer identifier
	const doneTypingInterval = 500; //time in ms, 2 second for example
	//on keyup, start the countdown
	$gf('#search_gfont').on('keyup', function () {
		clearTimeout(typingTimer);
		typingTimer = setTimeout(search_gfont, doneTypingInterval);
	});
	//on keydown, clear the countdown
	$gf('#search_gfont').on('keydown', function () {
		clearTimeout(typingTimer);
	});
});
function search_gfont() {
	const gsearch = $gf('#search_gfont').val();
	const $list_wrapper = $gf('#fonts-list-wrapper');
	$list_wrapper.html('');
	if (gsearch === '') {
		$list_wrapper.attr('data-gstart', parseInt(0));
	}
	cplus_get_google_fonts();
}
$gf(document).ready(function () {
	const $menu = jQuery('.fonts-selected-list'),
		$window = jQuery(window),
		offset = $menu.offset();
	$window.on('scroll', function () {
		if ($window.scrollTop() + 35 + 15 > offset.top) {
			$menu.addClass('uagffixed');
		} else {
			$menu.removeClass('uagffixed');
		}
	});
});
