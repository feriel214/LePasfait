<?php
/**
 * The style "default" of the Blogger
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.2
 */

$args = get_query_var('trx_addons_args_sc_blogger');

/**
 * Filters
 */
$show_filters = !empty($args['show_filters']);
$show_all_filters =  !empty($args['show_all_filters']);

$query_args = array(
// Attention! Parameter 'suppress_filters' is damage WPML-queries!
	'post_status' => 'publish',
	'ignore_sticky_posts' => true
);
if (empty($args['ids'])) {
	$query_args['posts_per_page'] = $args['count'];
	if (!trx_addons_is_off($args['pagination']) && $args['page'] > 1)
		$query_args['paged'] = $args['page'];
	else
		$query_args['offset'] = $args['offset'];
}
$query_args = trx_addons_query_add_sort_order($query_args, $args['orderby'], $args['order']);
$query_args = trx_addons_query_add_posts_and_cats($query_args, $args['ids'], $args['post_type'], $args['cat'], $args['taxonomy']);

if ($show_filters) {
	$list_terms = $tabs = array();
	//Generate filters
	if (count($args['ids_filters']) > 0) {
		foreach ($args['ids_filters'] as $ids_filter) {
			$term = get_term_by( 'id', $ids_filter, $args['taxonomy_filters'] );
			if ($term) {
				$list_terms[$ids_filter] = apply_filters('trx_addons_extended_taxonomy_name', $term->name, $term);
			}
		}
	} else {
		$only_children = $args['cat'] != '' && $args['taxonomy_filters'] == $args['taxonomy'];
		$list_terms = $args['taxonomy_filters'] == 'category' && !$only_children
			? trx_addons_get_list_categories()
			: trx_addons_get_list_terms(false, $args['taxonomy_filters'], $only_children ? array('parent' => $args['cat']) : array());
	}

    $tabs = count($tabs) > 0 ? $tabs : $list_terms;

	if (count($tabs) > 0) {
		if (empty($args['filters_active'])) $args['filters_active'] = 'all';
		if ($args['filters_active'] != 'all') {
			$query_args = trx_addons_query_add_posts_and_cats($query_args, '', '', $args['filters_active'], $args['taxonomy_filters']);
		}
	}
}
$query = new WP_Query( $query_args );
if ( $query->found_posts > 0 || count($tabs) > 0 ) {
	$posts_count = ($args['count'] > $query->found_posts) ? $query->found_posts : $args['count'];
	if ($args['columns'] < 1) $args['columns'] = $posts_count;
	//$args['columns'] = min($args['columns'], $posts_count);
	$args['columns'] = max(1, min(12, (int) $args['columns']));
	$args['slider'] = $args['slider'] > 0 && $posts_count > $args['columns'];
	$args['slides_space'] = max(0, (int) $args['slides_space']);
	?><div <?php if (!empty($args['id'])) echo ' id="'.esc_attr($args['id']).'"'; ?>
		class="sc_blogger sc_blogger_<?php
			echo esc_attr($args['type']);
			if (!empty($args['class'])) echo ' '.esc_attr($args['class']); 
			?>"<?php
		if (!empty($args['css'])) echo ' style="'.esc_attr($args['css']).'"';
		?>><?php


		trx_addons_sc_show_titles('sc_blogger', $args);
		
		/**
		 * Filters
		 */
		if ($show_filters && count($tabs) > 0) {
			$sc_blogger_filters_ajax = true;
			$sc_blogger_filters_id = 'blogger_filters';
			$args['sc'] = 'sc_blogger';
			?><div class="sc_item_filters sc_blogger_filters sc_blogger_tabs sc_blogger_tabs_ajax" data-params="<?php echo esc_attr(serialize($args)); ?>" >
				<ul class="sc_item_filters_titles sc_blogger_filters_titles"><?php


					//Add "All filters" tab
					if ($show_all_filters) {
						$sc_bloggertitle = empty($args['all_btn_text_filters']) ? esc_html__('All','trx_addons') : $args['all_btn_text_filters'];
						$sc_bloggerid = 'all';
						?>
						<li>
						<a href="<?php echo esc_url(trx_addons_get_hash_link(sprintf('#%s_%s_content', $sc_blogger_filters_id, $sc_bloggerid))); ?>"
							class="sc_blogger_filters_all<?php echo($args['filters_active'] == $sc_bloggerid ? ' active' : ''); ?>"
							data-tab="<?php echo esc_attr($sc_bloggerid); ?>"
							data-page="1"><?php echo($sc_bloggertitle); ?></a>
						</li><?php
					}

					foreach ($tabs as $sc_bloggerid => $sc_bloggertitle) {
						?><li>
							<a href="<?php echo esc_url(trx_addons_get_hash_link(sprintf('#%s_%s_content', $sc_blogger_filters_id, $sc_bloggerid))); ?>"
							   <?php echo ($args['filters_active'] == $sc_bloggerid ? ' class="active"' : '' );?>
							   data-tab="<?php echo esc_attr($sc_bloggerid); ?>"
							   data-page="1"><?php echo trx_addons_sc_blogger_remove_terms_counter($sc_bloggertitle); ?></a>
						</li><?php
					}
					?>
				</ul>
			</div><?php
		}
		if ($args['slider']) {
			$args['slides_min_width'] = $args['type']=='modern' ? 390 : 220;
			trx_addons_sc_show_slider_wrap_start('sc_blogger', $args);
		} else if ($args['columns'] > 1 && empty($args['posts_container'])) {
			?><div class="sc_blogger_columns_wrap sc_item_columns sc_item_posts_container <?php echo esc_attr(trx_addons_get_columns_wrap_class()) . ($args['type']!='plain' ? ' columns_padding_bottom' : ''); ?>"><?php
		} else {
			?><div class="sc_blogger_content sc_item_content sc_item_posts_container<?php if (!empty($args['posts_container'])) echo ' '.esc_attr($args['posts_container']); ?>"><?php
		}	

		while ( $query->have_posts() ) { $query->the_post();
			if (!apply_filters('trx_addons_filter_sc_blogger_template', false, $args)) {
				trx_addons_get_template_part(array(
											TRX_ADDONS_PLUGIN_SHORTCODES . 'blogger/tpl.'.trx_addons_esc($args['type']).'-item.php',
											TRX_ADDONS_PLUGIN_SHORTCODES . 'blogger/tpl.default-item.php'
											),
											'trx_addons_args_sc_blogger', 
											$args
										);
			}
		}

		wp_reset_postdata();
	
		?></div><?php

		if ($args['slider']) {
			trx_addons_sc_show_slider_wrap_end('sc_blogger', $args);
		}

		trx_addons_sc_show_pagination('sc_blogger', $args, $query);

		trx_addons_sc_show_links('sc_blogger', $args);

	?></div><?php
}
?>