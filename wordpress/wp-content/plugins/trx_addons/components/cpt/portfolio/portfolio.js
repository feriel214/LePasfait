/* global jQuery:false */
/* global TRX_ADDONS_STORAGE:false */

// Init portfolio gallery
jQuery(document).on('action.init_hidden_elements', function(e, cont) {

	"use strict";
	
	cont.find('[class*="portfolio_page_gallery_type_masonry_"]').each(function() {
		var portfolio = jQuery(this);
		if (portfolio.parents('div:hidden,article:hidden').length > 0) return;
		if (!portfolio.hasClass('inited')) {
			portfolio.addClass('inited');
			trx_addons_when_images_loaded( portfolio, function() {
				portfolio.masonry({
					itemSelector: '.portfolio_page_gallery_item',
					columnWidth: '.portfolio_page_gallery_item',
					percentPosition: true
				});
				jQuery(window).trigger('resize');
			});
		} else {
			// Relayout after 
			setTimeout(function() { portfolio.masonry(); }, 310);
		}
	});

});