<?php
/**
 * The style "default" of the Accordion Posts
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.49
 */

$args = get_query_var('trx_addons_args_sc_accordionposts');

$icon_present = '';

?><div <?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'"'; ?>
	class="sc_accordionposts sc_accordionposts_<?php
		echo esc_attr($args['type']);
		if (!empty($args['class'])) echo ' '.esc_attr($args['class']);
	?>"<?php
	if (!empty($args['css'])) echo ' style="'.esc_attr($args['css']).'"';
?>><?php

trx_addons_sc_show_titles('sc_accordionposts', $args);

?><div class="sc_accordionposts_content sc_item_content"><?php

foreach ($args['accordions'] as $item) {
	?><section class="sc_accordionposts_item sc_accordionposts_item_<?php
		echo esc_attr($args['type']);
	?>">
		<div class="sc_accordionposts_item_top">
			<?php
			if (!empty($item['icon']) && strtolower($item['icon'])!='none') $icon = $item['icon'];
			if (!empty($icon)) {
				?><div class="sc_accordionposts_item_icon <?php echo esc_attr($icon); ?>"
					<?php
					if (!empty($item['bg_color']) || !empty($item['color']))
						echo ' style="'
							. (!empty($item['color']) ? 'color:'.esc_attr($item['color']).';' : '')
							. (!empty($item['bg_color']) ? 'background-color:'.esc_attr($item['bg_color']).';' : '')
							. '"';
					?>>
				</div><?php
			}
			?>
			<div class="sc_accordionposts_item_header">
				<?php
				if ($item['advanced_rolled_content'] == '1' && !empty($item['advanced_rolled_content'])) {
					if (!empty($item['rolled_content'])) {
						trx_addons_show_layout($item['rolled_content']);
					}
				} else if (!empty($item['title'])) {
					$item['title'] = explode('|', $item['title']);
					?><h2 class="sc_accordionposts_item_title"><?php
					foreach ($item['title'] as $str) {
						?><span><?php echo esc_html($str); ?></span><?php
					}
					?></h2><?php
				}
				?>
			</div><!-- /.sc_accordionposts_item_header -->
			<?php if (!empty($item['subtitle'])) { ?>
				<h2 class="sc_accordionposts_item_subtitle">
					<span <?php
					if (!empty($item['bg_color']) || !empty($item['color']))
						echo ' style="'
							. (!empty($item['color']) ? 'color:'.esc_attr($item['color']).';' : '')
							. (!empty($item['bg_color']) ? 'background-color:'.esc_attr($item['bg_color']).';' : '')
							. '"';
					?>>
						<?php trx_addons_show_layout($item['subtitle']);  ?>
					</span>
				</h2><!-- /.sc_accordionposts_item_subtitle -->
			<?php } ?>

			<span class="section_icon"></span>
		</div>

	<div class="sc_accordionposts_item_inner">
		<?php
		if ( !empty($item['post_id']) && $item['content_source'] == 'page' && $item['post_id'] != get_the_ID())
			do_action( 'trx_addons_action_show_layout', $item['post_id'] );
		else if ( !empty($item['layout_id']) && $item['content_source'] == 'layout' && $item['layout_id'] != get_the_ID())
			do_action( 'trx_addons_action_show_layout', $item['layout_id'] );
		else if (!empty($item['inner_content']))
			trx_addons_show_layout($item['inner_content']);
		?></div><!-- /.sc_accordionposts_item_inner -->
	</section><!-- /.sc_accordionposts_item --><?php
}
?></div><?php

trx_addons_sc_show_links('sc_accordionposts', $args);

?></div><!-- /.sc_accordionposts --><?php

trx_addons_load_icons($icon_present);
?>