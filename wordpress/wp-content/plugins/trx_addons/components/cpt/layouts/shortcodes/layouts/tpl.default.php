<?php
/**
 * The style "default" of the Layouts
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.06
 */

$args = get_query_var('trx_addons_args_sc_layouts');
if (!empty($args['layout']) || !empty($args['content'])) {
	if ($args['type'] == 'panel' && (int) $args['modal'] == 1) {
		?><div class="sc_layouts_panel_hide_content"></div><?php
	}
	?><div <?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'"'; ?>
		class="sc_layouts sc_layouts_<?php
				echo esc_attr($args['type']);
				if (!empty($args['layout'])) echo ' sc_layouts_' . esc_attr($args['layout']);
				if ($args['type'] == 'panel') echo ' sc_layouts_panel_' . esc_attr($args['position']);
				if (!trx_addons_is_off($args['show_on_load'])) echo ' sc_layouts_show_' . esc_attr($args['show_on_load']);
				if (!empty($args['class'])) echo ' '.esc_attr($args['class']);
		?>"><?php
		// Show layout
		if (!empty($args['content']) && empty($args['layout']))
			trx_addons_show_layout($args['content']);
		else if (!empty($args['layout']))
			trx_addons_cpt_layouts_show_layout($args['layout']);
		// Add Close button
		if ($args['type'] == 'panel') {
			?><a href="#" class="sc_layouts_panel_close"></a><?php
		}
	?></div><?php
}
?>