<?php
/**
 * The template to display the portfolio single page
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.5
 */

get_header();

while ( have_posts() ) { the_post();

	$meta = get_post_meta(get_the_ID(), 'trx_addons_options', true);

	do_action('trx_addons_action_before_article', 'portfolio.single');
	
	?><article id="post-<?php the_ID(); ?>" <?php post_class( 'portfolio_page itemscope portfolio_page_details_'.esc_attr($meta['details_position']) ); trx_addons_seo_snippets('', 'Article'); ?>><?php

		do_action('trx_addons_action_article_start', 'portfolio.single');

		// Project details before the content
		if (in_array($meta['details_position'], array('right', 'bottom'))) ob_start();
		?><section class="portfolio_page_details_wrap<?php
			if (in_array($meta['details_position'], array('right', 'left'))) echo ' sc_column_fixed';
		?>"><?php
			// Subtitle
			if (!empty($meta['subtitle'])) {
				?><h5 class="portfolio_page_subtitle"><?php trx_addons_show_layout(trx_addons_prepare_macros($meta['subtitle'])); ?></h5><?php
			}
			// Excerpt
			if (has_excerpt()) {
				?><div class="portfolio_page_description"><?php
					the_excerpt();
				?></div><?php
			}
			// Details
			if (!empty($meta['details']) && count($meta['details']) > 0) {
				?><div class="portfolio_page_details"><?php
					foreach($meta['details'] as $item) {
						if (empty($item['title']) || empty($item['value'])) continue;
						?><span class="portfolio_page_details_item"><?php
							// Title
							?><span class="portfolio_page_details_item_title"><?php echo esc_html($item['title']); ?></span><?php
							// Value
							if (!empty($item['link'])) {
								?><a href="<?php echo esc_url($item['link']); ?>"<?php
							} else {
								?><span<?php
							}
							?> class="portfolio_page_details_item_value"><?php
								// Icon
								if (!empty($item['icon'])) {
									$icon = $item['icon'];
									$img = $svg = '';
									$icon_type = 'icons';
									if (trx_addons_is_url($icon)) {
										if (strpos($icon, '.svg') !== false) {
											$svg = $icon;
											$icon_type = 'svg';
										} else {
											$img = $icon;
											$icon_type = 'images';
										}
										$icon = basename($icon);
									}
									?><span class="portfolio_page_details_item_icon sc_icon_type_<?php echo esc_attr($icon_type); ?> <?php echo esc_attr($icon); ?>"><?php
										if (!empty($svg)) {
											trx_addons_show_layout(trx_addons_get_svg_from_file($svg));
										} else if (!empty($img)) {
											$attr = trx_addons_getimagesize($img);
											?><img class="sc_icon_as_image" src="<?php echo esc_url($img); ?>" alt="<?php esc_attr_e('Icon', 'trx_addons'); ?>"<?php echo (!empty($attr[3]) ? ' '.trim($attr[3]) : ''); ?>><?php
										}
									?></span><?php
								}
								echo esc_html($item['value']);
							if (!empty($item['link'])) {
								?></a><?php
							} else {
								?></span><?php
							}
						?></span><?php
					}
					// Share
					$trx_addons_output = trx_addons_get_share_links(array(
							'type' => 'list',
							'caption' => '',
							'echo' => false
						));
					if ($trx_addons_output) {
						?><span class="portfolio_page_details_item portfolio_page_details_share"><?php
							// Title
							?><span class="portfolio_page_details_item_title"><?php echo esc_html__('Share', 'trx_addons'); ?></span><?php
							// Value
							?><span class="portfolio_page_details_item_value"><?php trx_addons_show_layout($trx_addons_output); ?></span><?php
						?></span><?php
					}
				?></div><?php
			}
		?></section><?php
		if (in_array($meta['details_position'], array('right', 'bottom'))) {
			$details = ob_get_contents();
			ob_end_clean();
		}

		// Post content
		?><section class="portfolio_page_content_wrap"><?php
			// Gallery
			if (!empty($meta['gallery']) && $meta['gallery_position']!='none') {
				$images = explode('|', $meta['gallery']);
				if ($meta['gallery_position'] == 'bottom') ob_start();
				?><div class="portfolio_page_gallery"><?php
					?><div class="portfolio_page_gallery_content portfolio_page_gallery_type_<?php echo esc_attr($meta['gallery_layout']); ?>"><?php
						// Layout: Slider
						if ($meta['gallery_layout'] == 'slider') {
							trx_addons_show_layout(trx_addons_get_slider_layout(array(
										'mode' => 'custom',
										//'height' => $height
										), $images));
						
						// Layout: Grid or Stream
						} else if (strpos($meta['gallery_layout'], 'grid_')!==false || strpos($meta['gallery_layout'], 'masonry_')!==false || $meta['gallery_layout'] == 'stream') {
							$style   = explode('_', $meta['gallery_layout']);
							$type    = $style[0];
							$columns = empty($style[1]) ? 1 : max(2, $style[1]);
							if ($columns > 1 && $type == 'grid') {
								?><div class="portfolio_page_columns_wrap <?php echo esc_attr(trx_addons_get_columns_wrap_class()); ?> columns_padding_bottom"><?php
							}
							foreach($images as $img) {
								$img_title = '';
								if (($img_id = attachment_url_to_postid($img)) > 0) {
									$img_title = wp_get_attachment_caption($img_id);
								}
								?><div class="<?php
									if ($columns > 1 && $type == 'grid')
										echo esc_attr(trx_addons_get_column_class(1, $columns));
									else
										echo 'portfolio_page_gallery_item';
								?>">
									<figure><?php
										$thumb = trx_addons_add_thumb_size($img, apply_filters('trx_addons_filter_thumb_size', trx_addons_get_thumb_size($type=='stream'
																																	? 'full'
																																	: ($type=='masonry'
																																		? ($columns > 2 ? 'masonry' : 'masonry-big') 
																																		: ($columns > 2 ? 'medium' : 'big'))),
																																'portfolio-single'));
										$attr = trx_addons_getimagesize($thumb);
										?><a href="<?php echo esc_url($img); ?>" title="<?php echo esc_attr($img_title); ?>"><img src="<?php echo esc_url($thumb); ?>" alt="<?php esc_attr_e('Gallery item', 'trx_addons'); ?>"<?php if (!empty($attr[3])) echo ' '.trim($attr[3]); ?>></a><?php
										if (!empty($img_title)) {
											?><figcaption class="wp-caption-text gallery-caption"><?php echo esc_html($img_title); ?></figcaption><?php
										}
									?></figure>
								</div><?php
							}
							if ($columns > 1 && $type == 'grid') {
								?></div><?php
							}
						}
					?></div><?php
					if (!empty($meta['gallery_description'])) {
						?><div class="portfolio_page_gallery_description"><?php
							trx_addons_show_layout(trx_addons_prepare_macros($meta['gallery_description']));
						?></div><?php
					}
				?></div><?php
				// Video
				if (!empty($meta['video'])) {
					?><div class="portfolio_page_video"><?php
						?><div class="portfolio_page_video_content"><?php
							trx_addons_show_layout(trx_addons_get_video_layout(array(
																					'link' => $meta['video']
																				)));
						?></div><?php
						if (!empty($meta['video_description'])) {
							?><div class="portfolio_page_video_description"><?php
								trx_addons_show_layout(trx_addons_prepare_macros($meta['video_description']));
							?></div><?php
						}
					?></div><?php
				}
				if ($meta['gallery_position'] == 'bottom') {
					$gallery = ob_get_contents();
					ob_end_clean();
				}
			}

			// Image
			if ( !trx_addons_sc_layouts_showed('featured') && has_post_thumbnail() && (empty($meta['gallery']) || in_array($meta['gallery_position'], array('none', 'bottom'))) ) {
				?><div class="portfolio_page_featured"><?php
					the_post_thumbnail(
										apply_filters('trx_addons_filter_thumb_size', 'full', 'portfolio-single'),
										trx_addons_seo_image_params(array(
																		'alt' => get_the_title()
																		))
										);
				?></div><?php
			}
			
			// Title
			if ( !trx_addons_sc_layouts_showed('title') ) {
				?><h2 class="portfolio_page_title"><?php the_title(); ?></h2><?php
			}
		
			// Post content
			?><div class="portfolio_page_content entry-content"<?php trx_addons_seo_snippets('articleBody'); ?>><?php
				the_content( );
			?></div><?php
			
			// Gallery after the content
			if ($meta['gallery_position'] == 'bottom' && !empty($gallery)) trx_addons_show_layout($gallery);
		
		?></section><!-- .entry-content --><?php

		// Project details after the content
		if (in_array($meta['details_position'], array('right', 'bottom')) && !empty($details)) {
			trx_addons_show_layout($details);
		}

		do_action('trx_addons_action_article_end', 'portfolio.single');

	?></article><?php

	do_action('trx_addons_action_after_article', 'portfolio.single');

	// If comments are open or we have at least one comment, load up the comment template.
	if ( comments_open() || get_comments_number() ) {
		comments_template();
	}
}

get_footer();
?>