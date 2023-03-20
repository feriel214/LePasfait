<#
/**
 * Template to represent shortcode as Widget in the Elementor preview area
 *
 * Written as a Backbone JavaScript template and used to generate the live preview.
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.41
 */

// Default values
settings = trx_addons_array_merge({
									'align':			'none',
									'link':				{ 'url': '', 'is_external': '' },
									'link_style':		'default',
									'image':			{ 'url': '' },
									'bg_image':			{ 'url': '' },
									'icon':				'',
									'icon_position':	'',
									'size':				'',
									'new_window':		typeof settings.link.is_external !== 'undefined' && settings.link.is_external == 'on',
									'css': 				''
									}, settings);

settings.css += settings.bg_image.url != '' 
						? 'background-image:url(' + settings.bg_image.url + ');' 
						: '';
var button_classes = "<?php echo esc_attr(apply_filters('trx_addons_filter_sc_item_link_classes', 'sc_button', 'sc_button')); ?>";

if (!trx_addons_is_off(settings.align)) {
#><div class="sc_item_button sc_button_wrap sc_align_<# print(settings.align); #>"><#
}
	#><a href="<# print(settings.link.url); #>"<#
		if (settings._element_id != '') print(' id="'+settings._element_id+'_button"');
		#> class="<# print(button_classes
							+ ' sc_button_' + settings.type
							+ (settings.class != '' ? ' ' + settings.class : '')
       	                    + (settings.size != '' ? ' sc_button_size_' + settings.size : '')
           	                + (settings.bg_image.url != '' ? ' sc_button_bg_image' : '')
               	            + (settings.image.url != '' ? ' sc_button_with_image' : '')
                   	        + (settings.icon != '' ? ' sc_button_with_icon' : '')
                       	    + (settings.icon_position != '' ? ' sc_button_icon_' + settings.icon_position : '')
                            ); #>"<#
		if (settings.new_window) print(' target="_blank"');
		if (settings.css != '') print(' style="' + settings.css + '"');
		#>><#
	
		// Icon or Image
		if (!trx_addons_is_off(settings.image.url) || !trx_addons_is_off(settings.icon)) {
			#><span class="sc_button_icon"><#
				if (settings.image.url != '') {
					#><img class="sc_icon_as_image" src="{{ settings.image.url }}" alt="<?php esc_attr_e('Icon', 'trx_addons'); ?>"><#
				} else if (settings.icon.indexOf('//') >= 0) {
					if (settings.icon.indexOf('.svg') >= 0) {
						#><object type="image/svg+xml" data="{{ settings.icon }}" border="0"></object><#
					} else {
						#><img class="sc_icon_as_image" src="{{ settings.icon }}" alt="<?php esc_attr_e('Icon', 'trx_addons'); ?>"><#
					}
				} else {
					#><span class="{{ settings.icon }}"></span><#
				}
			#></span><#
		}
		if (settings.title != '' || settings.subtitle != '') {
			#><span class="sc_button_text<# if (!trx_addons_is_off(settings.text_align)) print(' sc_align_'+settings.text_align); #>"><#
				if (settings.subtitle != '') {
					#><span class="sc_button_subtitle">{{ settings.subtitle }}</span><#
				}
				if (settings.title != '') {
					#><span class="sc_button_title">{{ settings.title }}</span><#
				}
			#></span><!-- /.sc_button_text --><#
		}
	#></a><!-- /.sc_button --><#
if (!trx_addons_is_off(settings.align)) {
#></div><!-- /.sc_item_button --><#
}
#>