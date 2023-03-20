<?php
/**
 * The template to display shortcode's pagination
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.42
 */

extract(get_query_var('trx_addons_args_sc_pagination'));

$max_page = !empty($query->max_num_pages) ? $query->max_num_pages : 1;

if (!trx_addons_is_off($args['pagination']) && $max_page > 1) {
	
	$args['sc'] = $sc;
	
	$align = !empty($args['title_align']) ? ' sc_align_'.trim($args['title_align']) : '';
	
	// Old style: links 'Prev' & 'Next'
	if ($args['pagination'] == 'prev_next') {
		?><nav class="<?php echo esc_attr($sc); ?>_pagination sc_item_pagination sc_item_pagination_prev_next nav-links-old <?php echo esc_attr($align); ?>" data-params="<?php echo esc_attr(serialize($args)); ?>"><?php
			if ($args['page'] > 1) {
				?><span class="nav-prev"><a href="#" data-page="<?php echo esc_attr($args['page'] - 1); ?>"><?php esc_html_e('Previous', 'trx_addons'); ?></a></span><?php
			}
			if ($args['page'] < $max_page) {
				?><span class="nav-next"><a href="#" data-page="<?php echo esc_attr($args['page'] + 1); ?>"><?php esc_html_e('Next', 'trx_addons'); ?></a></span><?php
			}
		?></nav><?php
	
	// Page numbers
	} else if ($args['pagination'] == 'pages') {
		?><nav class="<?php echo esc_attr($sc); ?>_pagination sc_item_pagination sc_item_pagination_pages navigation pagination <?php echo esc_attr($align); ?>" data-params="<?php echo esc_attr(serialize($args)); ?>">
			<div class="nav-links"><?php
				$total = 7;
				$start = max(1, $args['page'] - floor($total/2));
				$end = min($max_page, $start + $total - 1);
				if ($args['page'] > 1) {
					?><a href="#" class="page-numbers prev" data-page="<?php echo esc_attr($args['page'] - 1); ?>"><?php esc_html_e('Previous', 'trx_addons'); ?></a><?php
				}
				for ($i = $start; $i <= $end; $i++) {
					if ($i == $args['page']) {
						?><span class="page-numbers current"'><?php echo esc_html($i); ?></span><?php
					} else {
						?><a href="#" class="page-numbers" data-page="<?php echo esc_attr($i); ?>"><?php echo esc_html($i); ?></a><?php
					}
				}
				if ($args['page'] < $max_page) {
					?><a href="#" class="page-numbers next" data-page="<?php echo esc_attr($args['page'] + 1); ?>"><?php esc_html_e('Next', 'trx_addons'); ?></a><?php
				}
			?></div>
		</nav><?php
	
	// Advances page numbers
	} else if ($args['pagination'] == 'advanced_pages') {
		?><nav class="<?php echo esc_attr($sc); ?>_pagination sc_item_pagination sc_item_pagination_advanced_pages navigation pagination <?php echo esc_attr($align); ?>" data-params="<?php echo esc_attr(serialize($args)); ?>" role="navigation">
		<div class="nav-links"><?php
			$total = 7;
			$start = max(1, $args['page'] - floor($total/2));
			$end = min($max_page, $start + $total - 1);
			if ($args['page'] > 1) {
				?><a href="#" class="page-numbers prev" data-page="<?php echo esc_attr($args['page'] - 1); ?>"><?php esc_html_e('Previous', 'trx_addons'); ?></a><?php
			}
			for ($i = $start; $i <= $end; $i++) {
				if ($i == $args['page']) {
					?><span class="page-numbers current"'><?php echo esc_html($i); ?></span><?php
				} else {
					?><a href="#" class="page-numbers" data-page="<?php echo esc_attr($i); ?>"><?php echo esc_html($i); ?></a><?php
				}
			}
			if ($args['page'] < $max_page) {
				?><a href="#" class="page-numbers next" data-page="<?php echo esc_attr($args['page'] + 1); ?>"><?php esc_html_e('Next', 'trx_addons'); ?></a><?php
			}
			?></div>
		<span class="page-numbers page-count"><?php echo sprintf(esc_html__('Page %d of %d', 'trx_addons'), $args['page'], $max_page); ?></span>
		</nav>

		<?php

	// Load more
	} else if ($args['pagination'] == 'load_more') {
		if ($args['page'] < $max_page) {
			?><nav class="<?php echo esc_attr($sc); ?>_pagination sc_item_pagination sc_item_pagination_load_more nav-links-more <?php echo esc_attr($align); ?>" data-params="<?php echo esc_attr(serialize($args)); ?>">
				<a class="nav-links" data-page="<?php echo esc_attr($args['page']+1); ?>" data-max-page="<?php echo esc_attr($max_page); ?>"><?php
					esc_html_e('Load more', 'trx_addons');
				?></a>
			</nav><?php
		}
	}
}