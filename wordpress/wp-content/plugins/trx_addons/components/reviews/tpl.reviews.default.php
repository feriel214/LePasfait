<?php
/**
 * Default template to display the "post reviews" block on the single page
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.34
 */


$trx_addons_args = get_query_var('trx_addons_args_sc_reviews');

$allow_voting = !empty($trx_addons_args['allow_voting']) ? $trx_addons_args['allow_voting'] : 0;
$post_id      = !empty($trx_addons_args['post_id'])
				? $trx_addons_args['post_id']
				: ($trx_addons_args['location'] == 'comment' 
					? 'c'.get_comment_ID() 
					: 'p'.get_the_ID()); 
$rating_value = trx_addons_get_post_rating($post_id);

if ($allow_voting || $rating_value > 0) {
	$rating_max_level = !empty($trx_addons_args['rating_max_level']) && !trx_addons_is_inherit($trx_addons_args['rating_max_level']) ? $trx_addons_args['rating_max_level'] : trx_addons_get_option('rating_max_level');
	$rating_style     = !empty($trx_addons_args['rating_style']) && !trx_addons_is_inherit($trx_addons_args['rating_style']) ? $trx_addons_args['rating_style'] : trx_addons_get_option('rating_style');
	$rating_color     = !empty($trx_addons_args['rating_color']) ? $trx_addons_args['rating_color'] : trx_addons_get_option('rating_color');
	$rating_text_template = !empty($trx_addons_args['rating_text_template'])
									? ($trx_addons_args['rating_text_template'] != '#'
										? $trx_addons_args['rating_text_template']
										: ''
										)
									: trx_addons_get_option('rating_text_template');

	$rating_real_value = trx_addons_rating2show($rating_value, $rating_max_level);
	$rating_max = $rating_max_level == '100' ? '10' : $rating_max_level;

	$mark_default_css = (!empty($rating_color) ? ($rating_style == 'bar' ? 'background-color: '.esc_attr($rating_color).';' : 'color: '.esc_attr($rating_color)).';' : '');
	$mark_hover_css = $mark_default_css . 'width: '.esc_attr($rating_value).'%;';

	$icon_present = '';
	if (empty($trx_addons_args['icon_type']))
		$trx_addons_args['icon_type'] = '';
	$rating_icons = !empty($trx_addons_args['icon_type']) && !empty($trx_addons_args['icon_' . $trx_addons_args['icon_type']]) && $trx_addons_args['icon_' . $trx_addons_args['icon_type']] != 'empty' 
				? $trx_addons_args['icon_' . $trx_addons_args['icon_type']] 
				: '';
	if (!empty($rating_icons)) {
		if (strpos($icon_present, $trx_addons_args['icon_type'])===false)
			$icon_present .= (!empty($icon_present) ? ',' : '') . $trx_addons_args['icon_type'];
	} else {
		if (!empty($trx_addons_args['icon']) && strtolower($trx_addons_args['icon'])!='none') $rating_icons = $trx_addons_args['icon'];
	}
	if (empty($rating_icons)) $rating_icons = trx_addons_get_option('rating_icons');
			
	?><div<?php
			if (!empty($trx_addons_args['id']))		echo ' id="'.esc_attr($trx_addons_args['id']).'"';
			if (!empty($trx_addons_args['class']))	echo ' class="'.esc_attr($trx_addons_args['class']).'"';
			if (!empty($trx_addons_args['css']))	echo ' style="'.esc_attr($trx_addons_args['css']).'"';
	?>>
		<div class="rating_item rating_style_<?php echo esc_attr($rating_style); echo ($allow_voting ? ' allow_voting' : ''); ?>"
			 data-mark="<?php echo esc_attr($rating_value); ?>"
			 data-level="<?php echo esc_attr($rating_max_level); ?>"
			 data-post_id="<?php echo esc_attr($post_id); ?>"><?php
			if (!empty($trx_addons_args['title'])) {
				?><h5 class="rating_title"><?php echo esc_html($trx_addons_args['title']); ?></h5><?php
			}
			?><div class="mark_wrap"><?php
				if ($allow_voting) {
					?><span class="rat_bubble">
						<span class="value"></span>
						<span class="loader"></span>
					</span><?php
				}
				// Stars
				?><div class="mark_default"<?php echo (!empty($mark_default_css) ? ' style="'.esc_attr($mark_default_css).'"' : ''); ?>><?php
					if ($rating_style == 'icons' || $rating_style == 'text') {
						for ($i = 0; $i < $rating_max; $i++) {
							?><span class="star_review <?php echo esc_attr(!empty($rating_icons) ? $rating_icons : 'trx_addons_icon-star'); ?>"></span><?php
						}
					}
				?></div><?php
				// Stars hover
				?><div class="mark_hover"<?php echo (!empty($mark_hover_css) ? ' style="'.esc_attr($mark_hover_css).'"' : ''); ?>><?php
					if ($rating_style == 'icons' || $rating_style == 'text') {
						for ($i = 0; $i < $rating_max; $i++) {
							?><span class="star_review <?php echo esc_attr(!empty($rating_icons) ? $rating_icons : 'trx_addons_icon-star'); ?>"></span><?php
						}
					} else {
						?><span class="mark<?php echo ($rating_value < 50 ? ' right' : ' left'); ?>"><?php echo esc_html($rating_real_value); ?></span><?php
					}
				?></div><?php
				if ($allow_voting) {
					?><div class="message_error"></div><?php
					?><div class="message_success"><?php echo esc_html__('Thank you for your vote!', 'trx_addons'); ?></div><?php
				} 
			?></div><?php
			if (!empty($rating_text_template)) {
				$rating_votes_number = strpos($rating_text_template, '{{V}}') !== false || strpos($rating_text_template, '{{v}}') !== false
											? trx_addons_get_rating_votes($post_id)
											: 0;
				$rating_text_template = str_replace(
											array('{{X}}', '{{Y}}', '{{v}}', '{{V}}'),
											array('<span class="rating_text_value">' . esc_html($rating_real_value) . '</span>',
												  '<span class="rating_text_max">' . esc_html($rating_max_level) . '</span>',
												  '<span class="rating_text_votes">' . esc_html($rating_votes_number) . '</span>',
												  '<span class="rating_text_votes">' . esc_html($rating_votes_number) . '</span> <span class="rating_text_votes_label">' . esc_html(_n('vote', 'votes', $rating_votes_number, 'trx_addons')) . '</span>'
											),
											$rating_text_template);
				trx_addons_show_layout($rating_text_template, '<div class="rating_text">', '</div>');
			}
		?></div>
	</div><?php

	trx_addons_load_icons($icon_present);
}
