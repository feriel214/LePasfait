/* global jQuery:false, elementor:false */

jQuery(document).ready(function() {
	"use strict";

	// Hide Elementor's responsive breacpoints
	if (TRX_ADDONS_STORAGE['add_hide_on_xxx'] == 'replace') {
		jQuery('body').addClass('replace_hide_on_xxx');
	}

	// Refresh taxonomies when post type is changed
	//-------------------------------------------------------------------------------
	var tax_lists = {},
		fields_state = false,
		pmv = false;

	// Refresh taxonomies and terms lists when post type is changed in Elementor editor
	jQuery('#elementor-panel')
		.on('change', 'select[data-setting*="post_type"],select[data-setting*="taxonomy"]', function (e) {
			var refresh_obj = jQuery(this),
				refresh_post_type = refresh_obj.data('setting').indexOf('post_type')==0,
				refresh_taxonomy = refresh_obj.data('setting').indexOf('taxonomy')==0,
				prev_post_type,
				cat_flds;

			if (refresh_post_type) {
				cat_flds = refresh_obj.parents('.elementor-control').nextAll().filter(function () {
					return jQuery('select[data-setting*="taxonomy"]', jQuery(this)).length > 0;
				});
				cat_flds = cat_flds.find('select');
			} else if (refresh_taxonomy) {
				prev_post_type = refresh_obj.parents('.elementor-control').prev().find('select').val();
				cat_flds = [refresh_obj.parents('.elementor-control').next().find('select')];
			}
			if (cat_flds.length > 0) {
				jQuery.each(cat_flds, function(index, cat_fld){
					if (cat_fld.length === 0) return true;
					cat_fld = jQuery(cat_fld);
					var cat_wrapper = cat_fld.closest('.elementor-control-field'),
						cat_lbl = jQuery('label.elementor-control-title', cat_wrapper);
				// Restore fields values when panel is just opened
				if (fields_state !== false 
						&& refresh_taxonomy
						&& fields_state.post_type == prev_post_type
				   	&& fields_state.tax_val !== false) {
						refresh_obj.val(fields_state.tax_val);
						fields_state.tax_val = false;
						refresh_obj.trigger('change');
						return true;
				}
				trx_addons_refresh_list(cat_fld.data('setting').indexOf('taxonomy')==0
											? 'taxonomies'
											: 'terms',
						refresh_obj.val(),
										cat_fld,
										cat_lbl);
				});
			}
			return false;
		});

	// Store taxonomies and terms to restore it when shortcode params open again
	jQuery('#elementor-panel')
		.on('change', 'select[data-setting*="cat"],select[data-setting*="category"]', function () {
			var tax_fld = jQuery(this).parents('.elementor-control').prev().find('select[data-setting*="taxonomy"]');
			if (tax_fld.length > 0) {
				var post_fld = tax_fld.parents('.elementor-control').prev().find('select[data-setting*="post_type"]');
				if (post_fld.length > 0) {
					// Restore fields values when panel is just opened
					if (fields_state !== false && fields_state.post_type == post_fld.val()) {
						jQuery(this).val(fields_state.terms_val);
						fields_state = false;
						jQuery(this).trigger('change');	// Refresh preview area
					} else {
						tax_lists[post_fld.data('element-cid')] = {
							'taxonomies': tax_fld.html(),	//.data('items'),
							'terms': jQuery(this).html()	//.data('items')
						};
					}
				}
			}
		})
		.on('click', '.elementor-panel-navigation-tab', function() {
			if (pmv !== false)
				trx_addons_elementor_open_panel(pmv.panel, pmv.model, pmv.view, true);
		});
	
	// Add Elementor's hooks and elements
	if (window.elementor !== undefined && window.elementor.hooks !== undefined) {
		// Add hook on panel open to refresh taxonomies
		elementor.hooks.addAction( 'panel/open_editor/widget', trx_addons_elementor_open_panel);
		// Add hook on panel open to refresh 'layout editor' link
		elementor.hooks.addAction( 'panel/open_editor/widget', trx_addons_elementor_refresh_layout_selector_link);
	}

	// Store taxonomies and terms to restore it when shortcode params open again
	function trx_addons_elementor_open_panel( panel, model, view, tab_chg ) {
		if (panel.content !== undefined) {
			//Reset panel, model, view
			if (arguments[3]===undefined || arguments[3]===false)
				var tab_chg = false;
			if (!tab_chg) pmv = false;
			var post_fld = panel.content.$el.find( 'select[data-setting*="post_type"]' );
			var tax_fld = panel.content.$el.find( 'select[data-setting*="taxonomy"]' );
			var terms_fld = panel.content.$el.find( 'select[data-setting*="cat"],select[data-setting*="category"]' );
			// If this widget haven't fields 'post_type', 'taxonomy' or 'cat' - exit
			if (post_fld.length==0 || tax_fld.length == 0 || terms_fld.length == 0)
				return;
			// Save panel, model, view to use it when tabs are clicked
			if (!tab_chg) pmv = {'panel':panel, 'model': model, 'view':view};
			// Add view.cid to the field 'post_type'
			var el_cid = view.cid;
			post_fld.attr('data-element-cid', el_cid);
			var post_type = post_fld.val();
			var tax_val = model.getSetting(tax_fld.data('setting'));
			var terms_val = model.getSetting(terms_fld.data('setting'));
			// If list of taxonomies is correct - exit
			if (tax_fld.find('option[value="'+tax_val+'"]').length > 0 && terms_fld.find('option[value="'+terms_val+'"]').length > 0)
				return;
			// If we have stored list of items - use it
			if (tax_lists[el_cid] !== undefined) {
				tax_fld.html(tax_lists[el_cid].taxonomies).val(tax_val);
				terms_fld.html(tax_lists[el_cid].terms).val(terms_val);
			} else {
				fields_state = {'post_type': post_type, 'tax_val': tax_val, 'terms_val': terms_val};
				post_fld.trigger('change');
			}
		}
	}

	// Refresh 'Edit layout' link on panel creation
	function trx_addons_elementor_refresh_layout_selector_link( panel, model, view, tab_chg ) {
		if (panel.content !== undefined) {
			panel.content.$el.find( 'a.trx_addons_post_editor' ).each(function() {
				trx_addons_layout_selector_refresh_link(jQuery(this));
			});
		}
	}

	
	// Return layout with social icons
	//--------------------------------------------------------------------
	window.trx_addons_get_settings_icon = function(icon) {
		return typeof icon == 'object'
						? ( typeof icon['icon'] != 'undefined'
							? icon['icon']
							: ''
							)
						: icon;
	};

	window.trx_addons_get_socials_links = function(icons, style, show) {
		var output = '',
			show_icons = show.indexOf('icons') >= 0,
			show_names = show.indexOf('names') >= 0;
		if (icons.length > 0 && typeof icons[0].name != 'undefined') {
			var sn='', fn='', title='', url='';
			for (var i=0; i<icons.length; i++) {
				sn = trx_addons_get_settings_icon( icons[i].name );
				fn = style=='icons' ? sn.replace('trx_addons_icon-', '').replace('icon-', '') : trx_addons_get_basename(sn);
				title = icons[i].title !== '' ? icons[i].title : trx_addons_proper(fn);
				url = icons[i].url;
				if (trx_addons_is_off(url)) continue;
				output += '<a target="_blank" href="' + url + '"'
								+ ' class="social_item social_item_style_' + style + ' sc_icon_type_' + style + ' social_item_type_' + show + '">'
							+ (show_icons
								? '<span class="social_icon social_icon_' + fn + '"'
									+ (style=='bg' ? ' style="background-image: url(' + sn + ');"' : '')
									+ '>'
										+ (style=='icons' 
											? '<span class="' + sn + '"></span>' 
											: (style == 'svg'
												? '<object type="image/svg+xml" data="' + sn + '" border="0"></object>'
												: (style=='images' 
													? '<img src="' + sn + '" alt="' + title + '" />' 
													: '<span class="social_hover" style="background-image: url(' + sn + ');"></span>'
													)
												)
										 	)
									+ '</span>'
								: '')
							+ (show_names
								? '<span class="social_name social_' + fn + '">' + title + '</span>'
								: '')
						+ '</a>';
			}
		}
		return output;
	};


	// Add theme-specific shapes to the sections
	//-----------------------------------------------------

	// Add Elementor's hooks
	var last_view = false;
	if (window.elementor !== undefined && window.elementor.hooks !== undefined) {
		// Add hook on section's panel open
		elementor.hooks.addAction( 'panel/open_editor/section', function( panel, model, view ) {
			last_view = view;
		});
	}

	// Get theme-specific shape
	jQuery('#elementor-panel')
		.on('input', 'select[data-setting*="shape_divider"]', function (e) {
			var shape = jQuery(this).val(),
				setting = jQuery(this).data('setting'),
				side = setting.replace('shape_divider_', '');
			if (shape.indexOf('trx_addons_') == 0 && last_view !== false && TRX_ADDONS_STORAGE['shapes_url'] !== '') {
				trx_addons_load_shape(last_view.$el.find('.elementor-shape-'+side), shape);
			}
		});

	// Load theme-specific shape to the container
	function trx_addons_load_shape(cont, shape) {
		if (cont.length > 0 && shape !== '') {
			cont.empty().attr( 'data-shape', shape );
			shape = TRX_ADDONS_STORAGE['shapes_url'] + shape.replace('trx_addons_', '') + '.svg';
			jQuery.get( shape, function( data ) {
				cont.append(data.childNodes[0]).attr('data-negative', 'false');
			} );
		}
	}


	// Scroll active category to top of the panel
	//---------------------------------------------------------
	jQuery('#elementor-panel')
		.on('click', '.elementor-panel-category-title', function(e) {
			var cat = jQuery(this).closest('.elementor-panel-category');
			setTimeout(function(){
				if (cat.hasClass('elementor-active')) {
					var height = jQuery('#elementor-panel-elements-navigation').outerHeight()
								+ jQuery('#elementor-panel-elements-search-area').outerHeight(),
						skip = false;
					jQuery('#elementor-panel-categories > .elementor-panel-category').each(function() {
						if (skip || cat.attr('id') == jQuery(this).attr('id')) {
							skip = true;
							return;
						}
						height += jQuery(this).outerHeight() + 2;
					});
					jQuery('#elementor-panel-content-wrapper').scrollTop(height);
				}
			}, 300);
		});
			// Prepare global atts for the new Elementor version: add array keys by 'name' from __globals__
	// After the update Elementor 3.0+ (or later) for settings with type ::COLOR global selector appears
	// Color value from this selects is not placed to the appropriate settings
	window.trx_addons_elm_prepare_global_params = function( args, clear ) {
		for ( var k in args ) {
			if ( typeof args[k] == 'object' ) {
				if ( k == '__globals__' ) {
					for ( var k1 in args[k] ) {
						if ( args[k][k1] ) {
							args[k1] = trx_addons_apply_filters( 'trx_addons_filter_prepare_global_param', args[k][k1], k1, clear );
						}
					}
				} else {
					args[k] = trx_addons_elm_prepare_global_params( args[k], clear );
				}
			}
		}
		return args;
	};

	// Return CSS-var from global color key, i.e. 'globals/colors?id=1855627f'
	trx_addons_add_filter( 'trx_addons_filter_prepare_global_param', function( value, key, clear ) {
		var prefix = 'globals/colors?id=';
		if ( value.indexOf( prefix ) === 0 ) {
			if ( clear ) {
				value = '';
			} else {
				var id = value.replace( prefix, '' );
				value = 'var(--e-global-color-' + id + ')';
			}
		}
		return value;
	} );

});
(function() {
	"use strict";

	// Restore original values for atts with global settings for the new Elementor version: clear array keys by 'name' from __globals__
	// After the update Elementor 3.0+ (or later) for settings with type ::COLOR global selector appears
	// Color value from this selects is not placed to the appropriate settings
	window.trx_addons_elm_restore_global_params = function( args ) {
		return trx_addons_elm_prepare_global_params( args, true );
	};

})();