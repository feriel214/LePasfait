<?php
/**
 * The template to display shortcode's title, subtitle and description
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.08
 */

extract(get_query_var('trx_addons_args_sc_show_titles'));
$align = !empty($args['title_align']) ? ' sc_align_'.trim($args['title_align']) : '';
$style = !empty($args['title_style']) ? ' sc_item_title_style_'.trim($args['title_style']) : '';
$subtitle = '';
if (!empty($args['subtitle'])) {
	$subtitle .= '<h6 class="' . esc_attr(apply_filters('trx_addons_filter_sc_item_subtitle_class', 'sc_item_subtitle '.$sc.'_subtitle'.$align.$style, $sc)) . '">'
				. trx_addons_prepare_macros($args['subtitle'])
				. '</h6>';
}
if (trx_addons_get_setting('subtitle_above_title')) {
	trx_addons_show_layout($subtitle);
}
if (!empty($args['title'])) {
	if (empty($size)) $size = 'large';	//is_page() ? 'large' : 'normal';
	$title_tag = !empty($args['title_tag']) && !trx_addons_is_off($args['title_tag'])
					? $args['title_tag']
					: apply_filters('trx_addons_filter_sc_item_title_tag', 'large' == $size ? 'h2' : ('tiny' == $size ? 'h4' : 'h3'));
	$title_tag_class = (!empty($args['title_tag']) && !trx_addons_is_off($args['title_tag'])
							? ' sc_item_title_tag'
							: '')
						. (!empty($args['title_color'])
							? ($args['title_style'] != 'gradient'
								? ' '.trx_addons_add_inline_css_class('color:' . esc_attr($args['title_color']) . ' !important')
								: ''
								)
							: '');
	?><<?php echo esc_attr($title_tag); ?> class="<?php 
		echo esc_attr(apply_filters('trx_addons_filter_sc_item_title_class', 'sc_item_title '.$sc.'_title'.$align.$style.$title_tag_class, $sc));
		?>"><?php
		if ($args['title_style'] == 'gradient') {
			echo '<span class="trx_addons_text_gradient"'
					. (!empty($args['title_color'])
						? ' style="'
							. 'background:' . esc_attr($args['title_color']) . ';'
							. 'background:linear-gradient('
							. max(0, min(360, (int) $args['gradient_direction'])) . 'deg,'
							. esc_attr(!empty($args['title_color2']) ? $args['title_color2'] : 'transparent') . ','
							. esc_attr($args['title_color']) . ');'
							. '"'
						: '')
					. '>'
					. trx_addons_prepare_macros($args['title'])
					. '</span>';

		} else {
			trx_addons_show_layout(trx_addons_prepare_macros($args['title']));
		}
	?></<?php echo esc_attr($title_tag); ?>><?php
}
if (!trx_addons_get_setting('subtitle_above_title')) {
	trx_addons_show_layout($subtitle);
}
if (!empty($args['description'])) {
	?><div class="<?php echo esc_attr(apply_filters('trx_addons_filter_sc_item_description_class', 'sc_item_descr '.$sc.'_descr'.$align, $sc)); ?>"><?php trx_addons_show_layout(wpautop(do_shortcode(trx_addons_prepare_macros($args['description'])))); ?></div><?php
}