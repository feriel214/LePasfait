/**
 * Shortcode Accordion
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.2
 */
/* global jQuery:false */
/* global TRX_ADDONS_STORAGE:false */

// Init handlers
jQuery(document).ready(function() {

	if (jQuery('.sc_accordionposts:not(.inited)').length > 0) {
		jQuery('.sc_accordionposts:not(.inited)')
			.addClass('inited')
			.on('click', '.sc_accordionposts_item_header, .sc_accordionposts_item_icon, .sc_accordionposts_item_subtitle, .section_icon', function(e) {
				var $wrapper = jQuery(this).closest('.sc_accordionposts'),
					$parent = jQuery(this).closest('.sc_accordionposts_item');

				// Close others
				if (!$parent.hasClass('active')) {
					jQuery('.sc_accordionposts_item', $wrapper).removeClass('active')
						.find('.sc_accordionposts_item_header').slideDown();
					jQuery('.sc_accordionposts_item_inner, .sc_accordionposts_item_subtitle', $wrapper).slideUp(0);
				}
				jQuery('body,html').animate( {scrollTop: ($parent.offset().top - trx_addons_fixed_rows_height() )}, 300);
				jQuery('.sc_accordionposts_item_header, .sc_accordionposts_item_inner, .sc_accordionposts_item_subtitle', $parent).slideToggle(300, function() {
					jQuery(document).trigger('action.init_hidden_elements', [$parent]);
				});
				$parent.toggleClass('active');
				jQuery(window).trigger('resize');
				return false;
			});
	}

});