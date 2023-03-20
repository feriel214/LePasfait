<?php
/**
 * The style "default" of the Widget "Instagram"
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.47
 */

$args = get_query_var('trx_addons_args_widget_instagram');
extract($args);

// Before widget (defined by themes)
trx_addons_show_layout($before_widget);
			
// Widget title if one was input (before and after defined by themes)
trx_addons_show_layout($title, $before_title, $after_title);

$count = max(1, $count);
$columns = max(1, $columns);
	
// Widget body
?><div class="widget_instagram_wrap">
	<div class="widget_instagram_images widget_instagram_images_columns_<?php
	echo esc_attr($columns);
	if ($columns_gap > 0) {
		echo ' ' . esc_attr(trx_addons_add_inline_css_class('margin-right:-'.trx_addons_prepare_css_value($columns_gap)));
	}
?>"><?php
		$resp = trx_addons_widget_instagram_get_recent_photos(array(
				'media' => !empty($media) ? $media : 'all',
				'hashtag' =>  !empty($hashtag) ? $hashtag : '',
				'count' => max(1, (int) $count)
		));
		if (!empty($resp['data']) && is_array($resp['data'])) {
			$user = '';
			foreach($resp['data'] as $v) {
				if (empty($user) && !empty($v['user']['username']))
					$user = $v['user']['username'];
				$class = trx_addons_add_inline_css_class(
								'width:'.round(100/$columns, 4).'%;'
								. ($columns_gap > 0
									? 'padding: 0 '.trx_addons_prepare_css_value($columns_gap).' '.trx_addons_prepare_css_value($columns_gap).' 0;'
									: ''
									)
								);
				printf( '<div class="widget_instagram_images_item_wrap %6$s">'
							. ($links != 'none' && ($v['type'] != 'video' || $links == 'instagram')
								? '<a href="%5$s" target="_blank"'
								: '<div'
								)
							. ' title="%4$s"'
							. ' class="widget_instagram_images_item widget_instagram_images_item_type_'.esc_attr($v['type'])
								. ($v['type'] == 'video'	// && $links != 'none'
										? ' ' . trx_addons_add_inline_css_class('background-image: url('.esc_url($v['images']['standard_resolution']['url']).');')
										: ''
									)
								. '"'
						. '>'
						   		. ($v['type'] == 'video'
						   			? ($links != 'instagram'
						   				? trx_addons_get_video_layout(array(
						   												'link' => $v['videos']['standard_resolution']['url'],
						   												'cover' => $links != 'none' ? $v['images']['standard_resolution']['url'] : '',
																		'show_cover' => false,	//$links != 'none',
						   												'popup' => $links == 'popup'
						   												))
						   				: '')
						   			: '<img src="%1$s" width="%2$d" height="%3$d" alt="%4$s">'
						   			)
						   		. '<span class="widget_instagram_images_item_counters">'
						   			. '<span class="widget_instagram_images_item_counter_likes trx_addons_icon-heart' . (empty($v['likes']['count']) ? '-empty' : '') . '">'
						   				. esc_attr($v['likes']['count'])
						   			. '</span>'
						   			. '<span class="widget_instagram_images_item_counter_comments trx_addons_icon-comment' . (empty($v['comments']['count']) ? '-empty' : '') . '">'
						   				. esc_attr($v['comments']['count'])
						   			. '</span>'
						   		. '</span>'
							. ($links != 'none' && ($v['type'] != 'video' || $links == 'instagram')
								? '</a>'
								: '</div>'
								)
						. '</div>',
						esc_url($v['images']['standard_resolution']['url']),
						$v['images']['standard_resolution']['width'],
						$v['images']['standard_resolution']['height'],
						$v['caption']['text'],
						esc_url($links == 'instagram' ? $v['link'] : $v['images']['standard_resolution']['url']),
						$class
						);
			}
		}
	?></div><?php	

	if ($follow && !empty($user)) {
		$url = 'https://instagram.com/' . $user;
		?><div class="widget_instagram_follow_link_wrap"><a href="<?php echo esc_url($url); ?>"
					class="<?php echo esc_attr(apply_filters('trx_addons_filter_widget_instagram_link_classes', 'widget_instagram_follow_link sc_button', $args)); ?>"
					target="_blank"><?php
			esc_html_e('Follow Me', 'trx_addons');
		?></a></div><?php
	}
?></div><?php	

// After widget (defined by themes)
trx_addons_show_layout($after_widget);
?>