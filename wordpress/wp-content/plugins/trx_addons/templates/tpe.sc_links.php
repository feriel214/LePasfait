<?php
/**
 * Template to display shortcode's link in the Elementor preview area
 *
 * Written as a Backbone JavaScript template and used to generate the live preview.
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.41
 */
extract(get_query_var('trx_addons_args_sc_show_links'));
?><#

// Default values
settings = trx_addons_array_merge({
									'link_image':	{ 'url': '' }
									}, settings);

var title_align = settings.title_align ? ' sc_align_'+settings.title_align : '';

if (typeof settings.link_image != 'undefined' && settings.link_image.url != '') {
	#><div class="<?php echo esc_attr($sc); ?>_button_image sc_item_button_image{{ title_align }}"><#
		if (settings.link.url != '') {
			#><a href="{{ settings.link.url }}"<# if (settings.link.is_external == 'on') print(' target="_blank"'); #>><#
		}
		#><img src="{{ settings.link_image.url }}" alt="<?php esc_attr_e("Button's image", 'trx_addons'); ?>"><#
		if (settings.link.url != '') {
			#></a><#
		}
	#></div><#
} else if (settings.link.url != '' && settings.link_text !='') {
	#><?php	$element->sc_show_button($sc); ?><#
}
#>