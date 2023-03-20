<?php
/**
 * The template to display shortcode's title, subtitle and description
 * on the Elementor's preview page
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.41
 */

extract(get_query_var('trx_addons_args_sc_show_titles'));
if (empty($size)) $size = 'large';
?><#
var title_align = settings.title_align ? ' sc_align_'+settings.title_align : '';
var title_style = settings.title_style ? ' sc_item_title_style_'+settings.title_style : '';
var title_class = "<?php echo esc_attr(apply_filters('trx_addons_filter_sc_item_title_class', 'sc_item_title '.$sc.'_title', $sc)); ?>";
var subtitle_class = "<?php echo esc_attr(apply_filters('trx_addons_filter_sc_item_subtitle_class', 'sc_item_subtitle '.$sc.'_subtitle', $sc)); ?>";
var description_class = "<?php echo esc_attr(apply_filters('trx_addons_filter_sc_item_description_class', 'sc_item_descr '.$sc.'_descr', $sc)); ?>";
var title_html  = '';
var subtitle_html = '';
if (settings.subtitle) {
	subtitle_html += '<h6 class="' + subtitle_class + title_align + title_style + '">'
					+ trx_addons_prepare_macros(settings.subtitle)
					+ '</h6>';
}
if (<?php echo trx_addons_get_setting('subtitle_above_title') ? '1' : '0'; ?>) title_html += subtitle_html;
if (settings.title) {
	var title_tag = !trx_addons_is_off(settings.title_tag)
					? settings.title_tag
					: "<?php echo esc_attr(apply_filters('trx_addons_filter_sc_item_title_tag', 'large' == $size ? 'h2' : ('tiny' == $size ? 'h4' : 'h3'))); ?>";
	var title_tag_class = (!trx_addons_is_off(settings.title_tag)
					? ' sc_item_title_tag'
					: '');
	var title_tag_style = settings.title_color != ''
					? (settings.title_style != 'gradient'
						? 'color:' + settings.title_color
						: ''
						)
					: '';
	title_html += '<' + title_tag + ' class="' + title_class + title_align + title_style + title_tag_class + '"' + (title_tag_style != '' ? ' style="' + title_tag_style + '"' : '') + '>'
					+ (settings.title_style == 'gradient'
						? '<span class="trx_addons_text_gradient"'
							+ (settings.title_color != ''
								? ' style="'
									+ 'background:linear-gradient(' 
										+ Math.max(0, Math.min(360, settings.gradient_direction.size)) + 'deg,'
										+ (settings.title_color2 ? settings.title_color2 : 'transparent') + ','
										+ settings.title_color
										+ ');'
									+ '"'
								: '')
							+ '>'
								+ trx_addons_prepare_macros(settings.title)
							+ '</span>'
						: trx_addons_prepare_macros(settings.title)
						)
					+ '</' + title_tag + '>';
}
if (<?php echo trx_addons_get_setting('subtitle_above_title') ? '0' : '1'; ?>) title_html += subtitle_html;
if (settings.description) {
	title_html += '<div class="' + description_class + title_align + '">'
					+ trx_addons_prepare_macros(settings.description)
					+ '</div>';
}
print(title_html);
#>