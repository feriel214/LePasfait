/**
 * Shortcodes common scripts
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.2
 */

/* global jQuery:false */
/* global TRX_ADDONS_STORAGE:false */


(function() {

	"use strict";
	
	// Fullheight elements
	//jQuery(document).on('action.init_hidden_elements', trx_addons_sc_fullheight);
	//jQuery(document).on('action.resize_trx_addons', trx_addons_sc_fullheight);

	function trx_addons_sc_fullheight(e, container) {
	
		if (container === undefined) container = jQuery('body');
		if (container === undefined || container.length === undefined || container.length == 0) return;
	
		container.find('.trx_addons_stretch_height').each(function () {
			// If item now invisible
			if (jQuery(this).parents('div:hidden,article:hidden').length > 0) {
				return;
			}
			var fullheight_item = jQuery( this ),
				fullheight_row  = jQuery(this).parents('.vc_row-o-full-height');
			if (fullheight_row.length > 0) {
				if (fullheight_row.css('height') != 'auto') {
					fullheight_item.height( fullheight_row.height() );
				} else if (fullheight_item.css( 'height' ) != 'auto') {
					fullheight_item.height( 'auto' );
				}
			} else {
				var wh = jQuery( window ).height() >= 698
					? jQuery( window ).height() - trx_addons_fixed_rows_height()
					: 'auto';
				if ( wh > 0 ) {
					if ( fullheight_item.data( 'display' ) != fullheight_item.css( 'display' ) ) {
						fullheight_item.css( 'display', fullheight_item.data( 'display' ) );
					}
					if ( fullheight_item.css( 'height', 'auto' ).outerHeight() <= wh ) {
						fullheight_item.css( 'height', wh );
					}
				} else if ( wh == 'auto' && fullheight_item.css( 'height' ) != 'auto' ) {
					if (fullheight_item.data( 'display' ) == undefined) {
						fullheight_item.attr( 'data-display', fullheight_item.css( 'display' ) );
					}
					fullheight_item.css( {'height': wh, 'display': 'block'} );
				}
			}
		});
	}


	// Equal height elements
	jQuery(document).on('action.resize_trx_addons', trx_addons_sc_equalheight);

	function trx_addons_sc_equalheight(e, container) {
		if (container === undefined) container = jQuery('body');
		if (container===undefined || container.length === undefined || container.length == 0) return;
		container.find('[data-equal-height],.trx_addons_equal_height').each(function () {
			var eh_wrap = jQuery(this);
			var eh_items_selector = eh_wrap.data('equal-height');
			if (eh_items_selector === undefined) eh_items_selector = '>*';
			var max_h = 0;
			var items = [];
			var row_y = 0;
			var i=0;
			eh_wrap.find(eh_items_selector).each(function() {
				var el = jQuery(this);
				el.css('visibility', 'hidden').height('auto');
				var el_height = el.height();
				var el_offset = el.offset().top;
				if (row_y == 0) row_y = el_offset;
				if (row_y < el_offset) {
					if (items.length > 0) {
						if (max_h > 0) {
							for (i=0; i<items.length; i++)
								items[i].css('visibility', 'visible').height(max_h);
						}
						items = [];
						max_h = 0;
					}
					row_y = el_offset;
				}
				if (el_height > max_h) max_h = el_height;
				items.push(el);
			});
			if (items.length > 0) {
				for (i=0; i<items.length; i++) {
					items[i].css('visibility', 'visible');
					if (max_h > 0) items[i].height(max_h);
				}
			}
		});
	}

	jQuery(document).on('action.init_hidden_elements', function() {
		var pagination_busy = false;
		// Load next page by AJAX		
		jQuery('.sc_item_filters:not(.inited),.sc_item_pagination:not(.inited)')
			.addClass('inited')
			.on('click', 'a', function(e) {
				if (!pagination_busy) {
					pagination_busy = true;
					var link = jQuery(this),
						link_wrap = link.parents('.sc_item_filters,.sc_item_pagination'),
						sc = link_wrap.parent(),
						posts = link_wrap.siblings('.sc_item_posts_container'),
						load_more = link_wrap.hasClass('sc_item_pagination_load_more');
					if (load_more) {
						link_wrap.addClass('loading');
					} else {
						posts.append('<div class="trx_addons_loading"></div>');
					}
					jQuery.post(TRX_ADDONS_STORAGE['ajax_url'], {
						action: 'trx_addons_item_pagination',
						nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
						params: link_wrap.data('params'),
						page: link.parents('.sc_item_filters').length > 0 ? 1 : link.data('page'),
						filters_active: link.parents('.sc_item_filters').length > 0 
											? link.data('tab') 
											: (link_wrap.siblings('.sc_item_filters').find('a.active').length > 0
												? link_wrap.siblings('.sc_item_filters').find('a.active').data('tab')
												: 'all'
												)
					}).done(function(response) {
						var rez = {};
						if (response=='' || response==0) {
							rez = { error: TRX_ADDONS_STORAGE['msg_ajax_error'] };
						} else {
							try {
								rez = JSON.parse(response);
							} catch (e) {
								rez = { error: TRX_ADDONS_STORAGE['msg_ajax_error'] };
								console.log(response);
							}
						}
						if (rez.error === '') {
							// Add inline styles
							if (rez.css !== '') {
								var	selector = 'trx_addons-inline-styles-inline-css',
									inline_css = jQuery('#'+selector);
								if (inline_css.length == 0)
									jQuery('body').append('<style id="'+selector+'" type="text/css">' + rez.css + '</style>');
								else
									inline_css.append(rez.css);
							}
							// Append posts
							if (load_more) {
								// Append posts to the tabs container
								if (posts.find('[class*="_tabs_list_item"]').length > 0) {
									posts.find('[class*="_tabs_list_item"]').parent().append(jQuery(rez.data).find('.sc_item_posts_container [class*="_tabs_list_item"]').parent().html());
									posts.find('[class$="_tabs_content"]').append(jQuery(rez.data).find('.sc_item_posts_container [class$="_tabs_content"]').html());
									// Remove active classes in appended items
									posts.find('[class*="_tabs_list_item_active"]').each(function(idx) {
										if (idx > 0) {
											var classes = jQuery(this).attr('class').split(' '),
												found = false;
											for (var i=0; i<classes.length; i++) {
												if (classes[i].indexOf('_tabs_list_item_active') > 0) {
													classes[i] = '';
													found = true;
													break;
												}
											}
											if (found) jQuery(this).attr('class', classes.join(' '));
										}
									});
									posts.find('[class$="_tabs_content"] [class*="_item_active"]').each(function(idx) {
										if (idx > 0) {
											var classes = jQuery(this).attr('class').split(' '),
												found = false;
											for (var i=0; i<classes.length; i++) {
												if (classes[i].indexOf('_item_active') > 0) {
													classes[i] = '';
													found = true;
													break;
												}
											}
											if (found) jQuery(this).attr('class', classes.join(' '));
										}
									});

								// Append regular posts
								} else
									posts.append(jQuery(rez.data).find('.sc_item_posts_container').html());
								jQuery(document).trigger('action.init_hidden_elements', [posts]);
								if (link.data('page') >= link.data('max-page'))
									pagination_wrap.fadeOut();
								else
									link.data('page', Number(link.data('page')) + 1);

							// Replace posts
							} else {
								posts.find('.trx_addons_loading').fadeOut();
								sc.fadeOut(function() {
									sc.after(jQuery(rez.data).hide());
									var sc_new = sc.next();
									sc.remove();
									sc_new.fadeIn();
									jQuery(document).trigger('action.init_hidden_elements', [sc_new]);
								});
							}
						} else {
							alert(rez.error);
							posts.find('.trx_addons_loading').remove();
						}
						if (load_more) pagination_wrap.removeClass('loading');
						pagination_busy = false;
					});
				}
				e.preventDefault();
				return false;
			});

		// Load post's details by AJAX and show in the popup
		jQuery('.sc_post_details_popup:not(.inited)')
			.addClass('inited')
			.on('click', 'a', function(e) {
				trx_addons_show_post_details(jQuery(this).parents('[data-post_id]'), true);
				e.preventDefault();
				return false;
			});
		if (jQuery('.sc_post_details_popup.inited').length > 0) {
			jQuery('body:not(.sc_post_details_popup_inited)')
				.addClass('sc_post_details_popup_inited')
				.on('click', '#trx_addons_post_details_popup_overlay, .trx_addons_post_details_popup_close', function(e) {
					jQuery('#trx_addons_post_details_popup').fadeOut();
					jQuery('#trx_addons_post_details_popup_overlay').fadeOut();
				})
				.on('click', '.trx_addons_post_details_popup_prev,.trx_addons_post_details_popup_next', function(e) {
					var popup = jQuery('#trx_addons_post_details_popup');
					var post_item = popup.data('post_item');
					if (!post_item || post_item.length == 0) return;
					var posts_items = post_item.parents('.sc_item_columns,.sc_item_slider').find('[data-post_id]');
					var cur_idx = -1;
					posts_items.each(function(idx) {
						if (jQuery(this).data('post_id') == post_item.data('post_id')) cur_idx = idx;
					});
					if (cur_idx == -1) return;
					post_item = jQuery(this).hasClass('trx_addons_post_details_popup_prev') 
									? (cur_idx > 0 ? posts_items.eq(cur_idx-1) : false)
									: (cur_idx < posts_items.length-1 ? posts_items.eq(cur_idx+1) : false);
					if (!post_item || post_item.length == 0) return;
					popup.fadeOut();
					trx_addons_show_post_details(post_item, false);
				});
		}
		
		function trx_addons_show_post_details(post_item, show_overlay) {
			jQuery.post(TRX_ADDONS_STORAGE['ajax_url'], {
				action: 'trx_addons_post_details_in_popup',
				nonce: TRX_ADDONS_STORAGE['ajax_nonce'],
				post_id: post_item.data('post_id'),
				post_type: post_item.data('post_type')
			}).done(function(response) {
				var rez = {};
				if (response=='' || response==0) {
					rez = { error: TRX_ADDONS_STORAGE['msg_ajax_error'] };
				} else {
					try {
						rez = JSON.parse(response);
					} catch (e) {
						rez = { error: TRX_ADDONS_STORAGE['msg_ajax_error'] };
						console.log(response);
					}
				}
				var msg = rez.error === '' ? rez.data : rez.error;
				var popup = jQuery('#trx_addons_post_details_popup');
				var overlay = jQuery('#trx_addons_post_details_popup_overlay');
				if (popup.length == 0) {
					jQuery('body').append(
						'<div id="trx_addons_post_details_popup_overlay"></div>'
						+ '<div id="trx_addons_post_details_popup">'
							+ '<div class="trx_addons_post_details_content"></div>'
							+ '<span class="trx_addons_post_details_popup_close trx_addons_icon-cancel"></span>'
							+ '<span class="trx_addons_post_details_popup_prev trx_addons_icon-left"></span>'
							+ '<span class="trx_addons_post_details_popup_next trx_addons_icon-right"></span>'
						+ '</div>');
					popup = jQuery('#trx_addons_post_details_popup');
					overlay = jQuery('#trx_addons_post_details_popup_overlay');
				}
				popup.data('post_item', post_item).find('.trx_addons_post_details_content').html(msg);
				if (show_overlay) overlay.fadeIn();
				popup.fadeIn();
			});
		}
	});

})();