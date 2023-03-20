<?php
/**
 * The template to display block with post meta
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.49
 */

$args = get_query_var('trx_addons_args_sc_layouts_meta');

?><div id="<?php echo esc_attr($args['id']); ?>"
	   class="post_meta sc_layouts_meta sc_layouts_meta_<?php
	   echo esc_attr($args['type']);
	   if (!empty($args['class'])) echo ' '.esc_attr($args['class']);
?>"<?php
	if (!empty($args['css'])) echo ' style="'.esc_attr($args['css']).'"';
?>>
	<?php
	trx_addons_sc_show_post_meta('sc_layouts_meta', apply_filters('trx_addons_filter_show_post_meta', array(
				'components' => is_array($args['components']) ? implode(',', $args['components']) : $args['components'],
				'counters' => is_array($args['counters']) ? implode(',', $args['counters']) : $args['counters'],
				'seo' => false,
				'theme_specific' => false
				), 'sc_layouts_meta', 1)
			);
?></div><!-- .post_meta -->