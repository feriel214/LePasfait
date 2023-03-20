<?php
/**
 * Template to represent shortcode as a widget in the Elementor preview area
 *
 * Written as a Backbone JavaScript template and using to generate the live preview in the Elementor's Editor
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.46
 */

extract(get_query_var('trx_addons_args_widget_custom_links'));

extract(trx_addons_prepare_widgets_args('widget_custom_links_'.mt_rand(), 'widget_custom_links'));

// Before widget (defined by themes)
trx_addons_show_layout($before_widget);
			
// Widget title if one was input (before and after defined by themes)
?><#
if (settings.title != '') {
	#><?php trx_addons_show_layout($before_title); ?><#
	print(settings.title);
	#><?php trx_addons_show_layout($after_title); ?><#
}

// Widget body
if (settings.links.length > 0) {
	#><ul class="custom_links_list"><#
		_.each(settings.links, function(item) {
			if (item.url.url == '') return;
			var target = item.url.is_external ? ' target="_blank"' : '';
			#><li class="custom_links_list_item<# if (item.icon!='' || item.image.url!='') print(' with_icon'); #>"><#
				// Open link
				#><a class="custom_links_list_item_link" href="{{ item.url.url }}"{{ target }}><#
				// Image or Icon
				if (item.image.url != '') {
					#><img class="custom_links_list_item_image" src="{{ item.image.url }}" alt="<?php esc_attr_e("Icon", 'trx_addons'); ?>"><#
				} else if (item.icon != '') {
					var icon = trx_addons_is_off(item.icon) ? '' : item.icon;
					if (typeof item.icon_type == 'undefined') item.icon_type = 'icons';
					if (icon != '') {
						var img = '', svg = '';
						if (icon.indexOf('//') >= 0) {
							if (icon.indexOf('.svg') >= 0) {
								svg = icon;
								item.icon_type = 'svg';
							} else {
								img = icon;
								item.icon_type = 'images';
							}
							icon = trx_addons_get_basename(icon);
						}
						#><span id="{{ id }}_{{ icon }}" class="custom_links_list_item_icon sc_icon_type_{{ item.icon_type }} {{ icon }}"><#
							if (svg != '') {
								#><object type="image/svg+xml" data="{{ svg }}" border="0"></object><#
							} else if (img != '') {
								#><img class="sc_icon_as_image" src="{{ img }}" alt="<?php esc_attr_e("Icon", 'trx_addons'); ?>"><#
							}
						#></span><#
					}
				}
				// Title
				if (item.title != '') {
					item.title = item.title.split('|');
					#><span class="custom_links_list_item_title"><#
						_.each(item.title, function(str) {
							#><span><# print(str); #></span><#
						});
					#></span><#
				}
				// Close link (or button)
				#></a><#
				// Description
				if (item.description != '') {
					#><span class="custom_links_list_item_description"><#
						if (item.description.indexOf('<p>') < 0) {
							item.description = item.description
													.replace(/\[(.*)\]/g, '<b>$1</b>')
													.replace(/\n/g, '|')
													.split('|');
							_.each(item.description, function(str) {
								#><span><# print(str); #></span><#
							});
						} else
							print(item.description);
					#></span><#
				}
				// Button
				if (item.caption != '') {
					#><a class="custom_links_list_item_button sc_button sc_button_simple" href="{{ item.url.url }}"{{ target }}><#
						print(item.caption); 
					#></a><#
				}
			#></li><#
		});
	#></ul><#
}
	
#><?php	

// After widget (defined by themes)
trx_addons_show_layout($after_widget);
?>