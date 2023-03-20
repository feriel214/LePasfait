<?php
/**
 * Widget: Popular posts
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.0
 */

// Load widget
if (!function_exists('trx_addons_widget_popular_posts_load')) {
	add_action( 'widgets_init', 'trx_addons_widget_popular_posts_load' );
	function trx_addons_widget_popular_posts_load() {
		register_widget('trx_addons_widget_popular_posts');
	}
}

// Widget Class
class trx_addons_widget_popular_posts extends TRX_Addons_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'widget_popular_posts', 'description' => esc_html__('Display any post types', 'trx_addons'));
		parent::__construct( 'trx_addons_widget_popular_posts', esc_html__('ThemeREX Universal Posts Listing', 'trx_addons'), $widget_ops );
	}

	// Show widget
	function widget($args, $instance) {

		$title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '');
		$tabs = array(
			array(
				'title'   => isset($instance['title_1']) ? $instance['title_1'] : '',
				'orderby' => isset($instance['orderby_1']) ? $instance['orderby_1'] : 'views',
				'post_type'	  => isset($instance['post_type_1']) ? $instance['post_type_1'] : 'post',
				'taxonomy'=> isset($instance['taxonomy_1']) ? $instance['taxonomy_1'] : 'category',
				'cat'    => isset($instance['cat_1']) ? $instance['cat_1'] : 0,
				'content' => ''
				),
			array(
				'title'   => isset($instance['title_2']) ? $instance['title_2'] : '',
				'orderby' => isset($instance['orderby_2']) ? $instance['orderby_2'] : 'comments',
				'post_type'	  => isset($instance['post_type_2']) ? $instance['post_type_2'] : 'post',
				'taxonomy'=> isset($instance['taxonomy_2']) ? $instance['taxonomy_2'] : 'category',
				'cat'    => isset($instance['cat_2']) ? $instance['cat_2'] : 0,
				'content' => ''
				),
			array(
				'title'   => isset($instance['title_3']) ? $instance['title_3'] : '',
				'orderby' => isset($instance['orderby_3']) ? $instance['orderby_3'] : 'likes',
				'post_type'	  => isset($instance['post_type_3']) ? $instance['post_type_3'] : 'post',
				'taxonomy'=> isset($instance['taxonomy_3']) ? $instance['taxonomy_3'] : 'category',
				'cat'    => isset($instance['cat_3']) ? $instance['cat_3'] : 0,
				'content' => ''
				)
			);

		$number = isset($instance['number']) ? (int) $instance['number'] : '';

		$tabs_count = 0;

		for ($i=0; $i<3; $i++) {
			if (empty($tabs[$i]['title'])) continue;
			$tabs_count++;
			$q_args = array(
				'post_status' => current_user_can('read_private_pages') && current_user_can('read_private_posts') ? array('publish', 'private') : 'publish',
				'post_password' => '',
				'posts_per_page' => $number,
				'ignore_sticky_posts' => true,
				'order' => 'DESC',
			);
			if ($tabs[$i]['orderby'] == 'views') {				// Most popular
				$q_args['meta_key'] = 'trx_addons_post_views_count';
				$q_args['orderby'] = 'meta_value_num';
			} else if ($tabs[$i]['orderby'] == 'likes') {		// Most liked
				$q_args['meta_key'] = 'trx_addons_post_likes_count';
				$q_args['orderby'] = 'meta_value_num';
			} else if ($tabs[$i]['orderby'] == 'comments') {	// Most commented
				$q_args['orderby'] = 'comment_count';
			} else if ($tabs[$i]['orderby'] == 'rating') {		// Ordered by rating
				$q_args['meta_key'] = 'trx_addons_post_rating';
				$q_args['orderby'] = 'meta_value_num';
			} else if ($tabs[$i]['orderby'] == 'title' || $tabs[$i]['orderby'] == 'post_title') {	// Title
				$q_args['orderby'] = 'title';
				$q_args['order'] = 'asc';
			} else if ($tabs[$i]['orderby'] == 'rand' || $tabs[$i]['orderby'] == 'random') {		// Random posts
				$q_args['orderby'] = 'rand';
			} else {											// Recent posts
				$q_args['orderby'] = 'date';
			}
			$q_args = trx_addons_query_add_posts_and_cats(apply_filters('trx_addons_filter_widget_posts_query_args', $q_args), '', $tabs[$i]['post_type'], $tabs[$i]['cat'], $tabs[$i]['taxonomy']);

			$q = new WP_Query($q_args); 
			
			// Loop posts
			if ( $q->have_posts() ) {
				$post_number = 0;
				set_query_var('trx_addons_output_widgets_posts', '');
				while ($q->have_posts()) { $q->the_post();
					$post_number++;
					trx_addons_get_template_part('templates/tpl.posts-list.php',
												'trx_addons_args_widgets_posts',
												apply_filters('trx_addons_filter_widget_posts_args',
													array(
														'counters' => in_array($tabs[$i]['orderby'], array('views', 'likes', 'comments', 'rating')) ? $tabs[$i]['orderby'] : 'comments',
														'show_image' => isset($instance['show_image']) ? (int) $instance['show_image'] : 0,
														'show_date' => isset($instance['show_date']) ? (int) $instance['show_date'] : 0,
														'show_author' => isset($instance['show_author']) ? (int) $instance['show_author'] : 0,
														'show_counters'	=> isset($instance['show_counters']) ? (int) $instance['show_counters'] : 0,
														'show_categories' => isset($instance['show_categories']) ? (int) $instance['show_categories'] : 0,
														'show_rating' => $tabs[$i]['orderby'] == 'rating'
														),
													$instance, 'trx_addons_widget_popular_posts')
												);
					if ($post_number >= $number) break;
				}
				$tabs[$i]['content'] .= get_query_var('trx_addons_output_widgets_posts');
			}
		}

		wp_reset_postdata();

		if ( $tabs[0]['title'].$tabs[0]['content'].$tabs[1]['title'].$tabs[1]['content'].$tabs[2]['title'].$tabs[2]['content'] ) {

			trx_addons_get_template_part(TRX_ADDONS_PLUGIN_WIDGETS . 'popular_posts/tpl.default.php',
										'trx_addons_args_widget_popular_posts', 
										apply_filters('trx_addons_filter_widget_args',
											array_merge($args, compact('title', 'tabs', 'tabs_count')),
											$instance, 'trx_addons_widget_popular_posts')
										);

			if (!is_customize_preview() && $tabs_count > 1) {
				wp_enqueue_script('jquery-ui-tabs', false, array('jquery','jquery-ui-core'), null, true);
				wp_enqueue_script('jquery-effects-fade', false, array('jquery','jquery-effects-core'), null, true);
			}
		}
	}

	// Update the widget settings
	function update($new_instance, $instance) {
		$instance = array_merge($instance, $new_instance);
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['title_1'] = strip_tags($new_instance['title_1']);
		$instance['title_2'] = strip_tags($new_instance['title_2']);
		$instance['title_3'] = strip_tags($new_instance['title_3']);
		$instance['orderby_1'] = strip_tags($new_instance['orderby_1']);
		$instance['orderby_2'] = strip_tags($new_instance['orderby_2']);
		$instance['orderby_3'] = strip_tags($new_instance['orderby_3']);
		$instance['post_type_1'] = strip_tags($new_instance['post_type_1']);
		$instance['post_type_2'] = strip_tags($new_instance['post_type_2']);
		$instance['post_type_3'] = strip_tags($new_instance['post_type_3']);
		$instance['taxonomy_1'] = strip_tags($new_instance['taxonomy_1']);
		$instance['taxonomy_2'] = strip_tags($new_instance['taxonomy_2']);
		$instance['taxonomy_3'] = strip_tags($new_instance['taxonomy_3']);
		$instance['cat_1'] = strip_tags($new_instance['cat_1']);
		$instance['cat_2'] = strip_tags($new_instance['cat_2']);
		$instance['cat_3'] = strip_tags($new_instance['cat_3']);
		$instance['number'] = (int) $new_instance['number'];
		$instance['show_date'] = (int) $new_instance['show_date'];
		$instance['show_image'] = (int) $new_instance['show_image'];
		$instance['show_author'] = (int) $new_instance['show_author'];
		$instance['show_counters'] = (int) $new_instance['show_counters'];
		$instance['show_categories'] = (int) $new_instance['show_categories'];
		return apply_filters('trx_addons_filter_widget_args_update', $instance, $new_instance, 'trx_addons_widget_popular_posts');
	}

	// Displays the widget settings controls on the widget panel
	function form($instance) {
		// Set up some default widget settings
		$instance = wp_parse_args( (array) $instance, apply_filters('trx_addons_filter_widget_args_default', array(
			'title' => '', 
			'title_1' => __('Viewed', 'trx_addons'), 
			'title_2' => __('Commented', 'trx_addons'), 
			'title_3' => __('Liked', 'trx_addons'), 
			'post_type_1' => 'post', 
			'post_type_2' => 'post', 
			'post_type_3' => 'post', 
			'taxonomy_1' => 'category', 
			'taxonomy_2' => 'category', 
			'taxonomy_3' => 'category', 
			'cat_1' => 0, 
			'cat_2' => 0, 
			'cat_3' => 0, 
			'orderby_1' => 'views', 
			'orderby_2' => 'comments', 
			'orderby_3' => 'likes', 
			'number' => '4', 
			'show_date' => '1', 
			'show_image' => '1', 
			'show_author' => '1', 
			'show_counters' => '1',
			'show_categories' => '1'
			), 'trx_addons_widget_popular_posts')
		);

		do_action('trx_addons_action_before_widget_fields', $instance, 'trx_addons_widget_popular_posts');

		$this->show_field(array('name' => 'title',
								'title' => __('Widget title:', 'trx_addons'),
								'value' => $instance['title'],
								'type' => 'text'));
		
		do_action('trx_addons_action_after_widget_title', $instance, 'trx_addons_widget_popular_posts');

		
		$this->show_field(array('name' => 'tab_1',
								'title' => __('Tab 1', 'trx_addons'),
								'type' => 'info'));

		$this->show_field(array('name' => 'title_1',
								'title' => __('Title:', 'trx_addons'),
								'value' => $instance['title_1'],
								'type' => 'text'));

		$this->show_field(array('name' => 'orderby_1',
								'title' => __("Order by:", 'trx_addons'),
								'value' => $instance['orderby_1'],
								'options' => trx_addons_get_list_widget_query_orderby(),
								'type' => 'select'));

		$this->show_field(array('name' => 'post_type_1',
								'title' => __('Post type:', 'trx_addons'),
								'value' => $instance['post_type_1'],
								'options' => trx_addons_get_list_posts_types(),
								'class' => 'trx_addons_post_type_selector',
								'type' => 'select'));
		
		$this->show_field(array('name' => 'taxonomy_1',
								'title' => __('Taxonomy:', 'trx_addons'),
								'value' => $instance['taxonomy_2'],
								'options' => trx_addons_get_list_taxonomies(false, $instance['post_type_1']),
								'class' => 'trx_addons_taxonomy_selector',
								'type' => 'select'));


		$tax_obj = get_taxonomy($instance['taxonomy_1']);

		$this->show_field(array('name' => 'cat_1',
								'title' => __('Category:', 'trx_addons'),
								'value' => $instance['cat_1'],
								'options' => trx_addons_array_merge(
										array(0 => sprintf(__('- %s -', 'trx_addons'), $tax_obj->label)),
										trx_addons_get_list_terms(false, $instance['taxonomy_1'], array('pad_counts' => true))),
								'class' => 'trx_addons_terms_selector',
								'type' => 'select'));


		$this->show_field(array('name' => 'tab_2',
								'title' => __('Tab 2', 'trx_addons'),
								'type' => 'info'));
		
		$this->show_field(array('name' => 'title_2',
								'title' => __('Title:', 'trx_addons'),
								'value' => $instance['title_2'],
								'type' => 'text'));

		$this->show_field(array('name' => 'orderby_2',
								'title' => __("Order by:", 'trx_addons'),
								'value' => $instance['orderby_2'],
								'options' => trx_addons_get_list_widget_query_orderby(),
								'type' => 'select'));

		$this->show_field(array('name' => 'post_type_2',
								'title' => __('Post type:', 'trx_addons'),
								'value' => $instance['post_type_2'],
								'options' => trx_addons_get_list_posts_types(),
								'class' => 'trx_addons_post_type_selector',
								'type' => 'select'));
		
		$this->show_field(array('name' => 'taxonomy_2',
								'title' => __('Taxonomy:', 'trx_addons'),
								'value' => $instance['taxonomy_2'],
								'options' => trx_addons_get_list_taxonomies(false, $instance['post_type_2']),
								'class' => 'trx_addons_taxonomy_selector',
								'type' => 'select'));


		$tax_obj = get_taxonomy($instance['taxonomy_2']);

		$this->show_field(array('name' => 'cat_2',
								'title' => __('Category:', 'trx_addons'),
								'value' => $instance['cat_2'],
								'options' => trx_addons_array_merge(
										array(0 => sprintf(__('- %s -', 'trx_addons'), $tax_obj->label)),
										trx_addons_get_list_terms(false, $instance['taxonomy_2'], array('pad_counts' => true))),
								'class' => 'trx_addons_terms_selector',
								'type' => 'select'));
		
		
		$this->show_field(array('name' => 'tab_3',
								'title' => __('Tab 3', 'trx_addons'),
								'type' => 'info'));
		
		$this->show_field(array('name' => 'title_3',
								'title' => __('Title:', 'trx_addons'),
								'value' => $instance['title_3'],
								'type' => 'text'));

		$this->show_field(array('name' => 'orderby_3',
								'title' => __("Order by:", 'trx_addons'),
								'value' => $instance['orderby_3'],
								'options' => trx_addons_get_list_widget_query_orderby(),
								'type' => 'select'));

		$this->show_field(array('name' => 'post_type_3',
								'title' => __('Post type:', 'trx_addons'),
								'value' => $instance['post_type_3'],
								'options' => trx_addons_get_list_posts_types(),
								'class' => 'trx_addons_post_type_selector',
								'type' => 'select'));
		
		$this->show_field(array('name' => 'taxonomy_3',
								'title' => __('Taxonomy:', 'trx_addons'),
								'value' => $instance['taxonomy_3'],
								'options' => trx_addons_get_list_taxonomies(false, $instance['post_type_3']),
								'class' => 'trx_addons_taxonomy_selector',
								'type' => 'select'));


		$tax_obj = get_taxonomy($instance['taxonomy_3']);

		$this->show_field(array('name' => 'cat_3',
								'title' => __('Category:', 'trx_addons'),
								'value' => $instance['cat_3'],
								'options' => trx_addons_array_merge(
										array(0 => sprintf(__('- %s -', 'trx_addons'), $tax_obj->label)),
										trx_addons_get_list_terms(false, $instance['taxonomy_3'], array('pad_counts' => true))),
								'class' => 'trx_addons_terms_selector',
								'type' => 'select'));
		
		$this->show_field(array('name' => 'info',
								'title' => __('Common params', 'trx_addons'),
								'type' => 'info'));

		$this->show_field(array('name' => 'number',
								'title' => __('Number posts to show:', 'trx_addons'),
								'value' => max(1, (int) $instance['number']),
								'type' => 'text'));

		$this->show_field(array('name' => 'show_image',
								'title' => __("Show post's image:", 'trx_addons'),
								'value' => (int) $instance['show_image'],
								'options' => trx_addons_get_list_show_hide(false, true),
								'type' => 'switch'));

		$this->show_field(array('name' => 'show_author',
								'title' => __("Show post's author:", 'trx_addons'),
								'value' => (int) $instance['show_author'],
								'options' => trx_addons_get_list_show_hide(false, true),
								'type' => 'switch'));

		$this->show_field(array('name' => 'show_date',
								'title' => __("Show post's date:", 'trx_addons'),
								'value' => (int) $instance['show_date'],
								'options' => trx_addons_get_list_show_hide(false, true),
								'type' => 'switch'));

		$this->show_field(array('name' => 'show_counters',
								'title' => __("Show post's counters:", 'trx_addons'),
								'value' => (int) $instance['show_counters'],
								'options' => trx_addons_get_list_show_hide(false, true),
								'type' => 'switch'));

		$this->show_field(array('name' => 'show_categories',
								'title' => __("Show post's categories:", 'trx_addons'),
								'value' => (int) $instance['show_categories'],
								'options' => trx_addons_get_list_show_hide(false, true),
								'type' => 'switch'));
		
		do_action('trx_addons_action_after_widget_fields', $instance, 'trx_addons_widget_popular_posts');
	}
}


// trx_widget_popular_posts
//-------------------------------------------------------------
/*
[trx_widget_popular_posts id="unique_id" title="Widget title" title_popular="title for the tab 'most popular'" title_commented="title for the tab 'most commented'" title_liked="title for the tab 'most liked'" number="4"]
*/
if ( !function_exists( 'trx_addons_sc_widget_popular_posts' ) ) {
	function trx_addons_sc_widget_popular_posts($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_widget_popular_posts', $atts, array(
			// Individual params
			"title" => "",
			"title_1" => "",
			"title_2" => "",
			"title_3" => "",
			"orderby_1" => "views",
			"orderby_2" => "comments",
			"orderby_3" => "likes",
			"post_type_1" => "post",
			"post_type_2" => "post",
			"post_type_3" => "post",
			"taxonomy_1" => "category",
			"taxonomy_2" => "category",
			"taxonomy_3" => "category",
			"cat_1" => 0,
			"cat_2" => 0,
			"cat_3" => 0,
			"number" => 4,
			"show_date" => 1,
			"show_image" => 1,
			"show_author" => 1,
			"show_counters" => 1,
			"show_categories" => 1,
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
			)
		);
		if ($atts['show_date']=='') $atts['show_date'] = 0;
		if ($atts['show_image']=='') $atts['show_image'] = 0;
		if ($atts['show_author']=='') $atts['show_author'] = 0;
		if ($atts['show_counters']=='') $atts['show_counters'] = 0;
		if ($atts['show_categories']=='') $atts['show_categories'] = 0;
		extract($atts);
		$type = 'trx_addons_widget_popular_posts';
		$output = '';
		global $wp_widget_factory;
		if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $type ] ) ) {
			$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
							. ' class="widget_area sc_widget_popular_posts' 
								. (trx_addons_exists_vc() ? ' vc_widget_popular_posts wpb_content_element' : '') 
								. (!empty($class) ? ' ' . esc_attr($class) : '') 
								. '"'
							. ($css ? ' style="'.esc_attr($css).'"' : '')
						. '>';
			ob_start();
			the_widget( $type, $atts, trx_addons_prepare_widgets_args($id ? $id.'_widget' : 'widget_popular_posts', 'widget_popular_posts') );
			$output .= ob_get_contents();
			ob_end_clean();
			$output .= '</div>';
		}
		return apply_filters('trx_addons_sc_output', $output, 'trx_widget_popular_posts', $atts, $content);
	}
}


// Add [trx_widget_popular_posts] in the VC shortcodes list
if (!function_exists('trx_addons_sc_widget_popular_posts_add_in_vc')) {
	function trx_addons_sc_widget_popular_posts_add_in_vc() {
		
		add_shortcode("trx_widget_popular_posts", "trx_addons_sc_widget_popular_posts");
		
		if (!trx_addons_exists_vc()) return;
		
		vc_lean_map("trx_widget_popular_posts", 'trx_addons_sc_widget_popular_posts_add_in_vc_params');
		class WPBakeryShortCode_Trx_Widget_Popular_Posts extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_widget_popular_posts_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_widget_popular_posts_add_in_vc_params')) {
	function trx_addons_sc_widget_popular_posts_add_in_vc_params() {

		// If open params in VC Editor
		list($vc_edit, $vc_params) = trx_addons_get_vc_form_params('trx_widget_popular_posts');
		// Prepare lists
		$post_type_1 = $vc_edit && !empty($vc_params['post_type_1']) ? $vc_params['post_type_1'] : 'post';
		$taxonomy_1 = $vc_edit && !empty($vc_params['taxonomy_1']) ? $vc_params['taxonomy_1'] : 'category';
		$tax_obj_1 = get_taxonomy($taxonomy_1);
		$post_type_2 = $vc_edit && !empty($vc_params['post_type_2']) ? $vc_params['post_type_2'] : 'post';
		$taxonomy_2 = $vc_edit && !empty($vc_params['taxonomy_2']) ? $vc_params['taxonomy_2'] : 'category';
		$tax_obj_2 = get_taxonomy($taxonomy_2);
		$post_type_3 = $vc_edit && !empty($vc_params['post_type_3']) ? $vc_params['post_type_3'] : 'post';
		$taxonomy_3 = $vc_edit && !empty($vc_params['taxonomy_3']) ? $vc_params['taxonomy_3'] : 'category';
		$tax_obj_3 = get_taxonomy($taxonomy_3);

		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_widget_popular_posts",
				"name" => esc_html__("Popular Posts", 'trx_addons'),
				"description" => wp_kses_data( __("Insert popular posts list with thumbs, post's meta and category", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_widget_popular_posts',
				"class" => "trx_widget_popular_posts",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "title",
							"heading" => esc_html__("Widget title", 'trx_addons'),
							"description" => wp_kses_data( __("Title of the widget", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-8',
							"type" => "textfield"
						),
						array(
							"param_name" => "number",
							"heading" => esc_html__("Number posts to show", 'trx_addons'),
							"description" => wp_kses_data( __("How many posts display in widget?", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-4',
							"value" => "4",
							"type" => "textfield"
						),

						array(
							"param_name" => "show_image",
							"heading" => esc_html__("Show post's image", 'trx_addons'),
							"description" => wp_kses_data( __("Do you want display post's featured image?", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4 vc_new_row',
							"std" => "1",
							"value" => array("Show image" => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "show_author",
							"heading" => esc_html__("Show post's author", 'trx_addons'),
							"description" => wp_kses_data( __("Do you want display post's author?", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "1",
							"value" => array("Show author" => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "show_date",
							"heading" => esc_html__("Show post's date", 'trx_addons'),
							"description" => wp_kses_data( __("Do you want display post's publish date?", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "1",
							"value" => array("Show date" => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "show_counters",
							"heading" => esc_html__("Show post's counters", 'trx_addons'),
							"description" => wp_kses_data( __("Do you want display post's counters?", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4 vc_new_row',
							"std" => "1",
							"value" => array("Show counters" => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "show_categories",
							"heading" => esc_html__("Show post's categories", 'trx_addons'),
							"description" => wp_kses_data( __("Do you want display post's categories?", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "1",
							"value" => array("Show categories" => "1" ),
							"type" => "checkbox"
						),

						array(
							"param_name" => "title_1",
							"heading" => esc_html__("Title", 'trx_addons'),
							"description" => wp_kses_data( __("Tab 1 title", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-8',
							"group" => esc_html__("Tab 1", 'trx_addons'),
							"admin_label" => true,
							"type" => "textfield"
						),
						array(
							"param_name" => "orderby_1",
							"heading" => esc_html__("Order by", 'trx_addons'),
							"description" => wp_kses_data( __("Select posts order", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"group" => esc_html__("Tab 1", 'trx_addons'),
							"std" => 'post',
							"value" => array_flip(trx_addons_get_list_widget_query_orderby()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "post_type_1",
							"heading" => esc_html__("Post type", 'trx_addons'),
							"description" => wp_kses_data( __("Select post type to show posts", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4 vc_new_row',
							"group" => esc_html__("Tab 1", 'trx_addons'),
							"std" => 'post',
							"value" => array_flip(trx_addons_get_list_posts_types()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "taxonomy_1",
							"heading" => esc_html__("Taxonomy", 'trx_addons'),
							"description" => wp_kses_data( __("Select taxonomy to show posts", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"group" => esc_html__("Tab 1", 'trx_addons'),
							"std" => 'category',
							"value" => array_flip(trx_addons_get_list_taxonomies(false, $post_type_1)),
							"type" => "dropdown"
						),
						array(
							"param_name" => "cat_1",
							"heading" => esc_html__("Category", 'trx_addons'),
							"description" => wp_kses_data( __("Select category to show posts", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"group" => esc_html__("Tab 1", 'trx_addons'),
							"value" => array_flip(trx_addons_array_merge(array(0=>sprintf(__('- %s -', 'trx_addons'), $tax_obj_1->label)),
																			 $taxonomy_1 == 'category' 
																				? trx_addons_get_list_categories() 
																				: trx_addons_get_list_terms(false, $taxonomy_1)
																			)),
							"std" => "0",
							"type" => "dropdown"
						),

						array(
							"param_name" => "title_2",
							"heading" => esc_html__("Title", 'trx_addons'),
							"description" => wp_kses_data( __("Tab 2 title", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-8',
							"admin_label" => true,
							"group" => esc_html__("Tab 2", 'trx_addons'),
							"type" => "textfield"
						),
						array(
							"param_name" => "orderby_2",
							"heading" => esc_html__("Order by", 'trx_addons'),
							"description" => wp_kses_data( __("Select posts order", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"group" => esc_html__("Tab 2", 'trx_addons'),
							"std" => 'post',
							"value" => array_flip(trx_addons_get_list_widget_query_orderby()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "post_type_2",
							"heading" => esc_html__("Post type", 'trx_addons'),
							"description" => wp_kses_data( __("Select post type to show posts", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4 vc_new_row',
							"group" => esc_html__("Tab 2", 'trx_addons'),
							"std" => 'post',
							"value" => array_flip(trx_addons_get_list_posts_types()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "taxonomy_2",
							"heading" => esc_html__("Taxonomy", 'trx_addons'),
							"description" => wp_kses_data( __("Select taxonomy to show posts", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"group" => esc_html__("Tab 2", 'trx_addons'),
							"std" => 'category',
							"value" => array_flip(trx_addons_get_list_taxonomies(false, $post_type_2)),
							"type" => "dropdown"
						),
						array(
							"param_name" => "cat_2",
							"heading" => esc_html__("Category", 'trx_addons'),
							"description" => wp_kses_data( __("Select category to show posts", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"group" => esc_html__("Tab 2", 'trx_addons'),
							"value" => array_flip(trx_addons_array_merge(array(0=>sprintf(__('- %s -', 'trx_addons'), $tax_obj_2->label)),
																			 $taxonomy_2 == 'category' 
																				? trx_addons_get_list_categories() 
																				: trx_addons_get_list_terms(false, $taxonomy_2)
																			)),
							"std" => "0",
							"type" => "dropdown"
						),
						
						array(
							"param_name" => "title_3",
							"heading" => esc_html__("Title", 'trx_addons'),
							"description" => wp_kses_data( __("Tab 3 title", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-8',
							"admin_label" => true,
							"group" => esc_html__("Tab 3", 'trx_addons'),
							"type" => "textfield"
						),
						array(
							"param_name" => "orderby_3",
							"heading" => esc_html__("Order by", 'trx_addons'),
							"description" => wp_kses_data( __("Select posts order", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"group" => esc_html__("Tab 3", 'trx_addons'),
							"std" => 'post',
							"value" => array_flip(trx_addons_get_list_widget_query_orderby()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "post_type_3",
							"heading" => esc_html__("Post type", 'trx_addons'),
							"description" => wp_kses_data( __("Select post type to show posts", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4 vc_new_row',
							"group" => esc_html__("Tab 3", 'trx_addons'),
							"std" => 'post',
							"value" => array_flip(trx_addons_get_list_posts_types()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "taxonomy_3",
							"heading" => esc_html__("Taxonomy", 'trx_addons'),
							"description" => wp_kses_data( __("Select taxonomy to show posts", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"group" => esc_html__("Tab 3", 'trx_addons'),
							"std" => 'category',
							"value" => array_flip(trx_addons_get_list_taxonomies(false, $post_type_3)),
							"type" => "dropdown"
						),
						array(
							"param_name" => "cat_3",
							"heading" => esc_html__("Category", 'trx_addons'),
							"description" => wp_kses_data( __("Select category to show posts", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"group" => esc_html__("Tab 3", 'trx_addons'),
							"value" => array_flip(trx_addons_array_merge(array(0=>sprintf(__('- %s -', 'trx_addons'), $tax_obj_3->label)),
																			 $taxonomy_3 == 'category' 
																				? trx_addons_get_list_categories() 
																				: trx_addons_get_list_terms(false, $taxonomy_3)
																			)),
							"std" => "0",
							"type" => "dropdown"
						)
					),
					trx_addons_vc_add_id_param()
				)
			), 'trx_widget_popular_posts');
	}
}




// Elementor Widget
//------------------------------------------------------
if (!function_exists('trx_addons_sc_widget_popular_posts_add_in_elementor')) {
	add_action( 'elementor/widgets/widgets_registered', 'trx_addons_sc_widget_popular_posts_add_in_elementor' );
	function trx_addons_sc_widget_popular_posts_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Widget')) return;	

		class TRX_Addons_Elementor_Widget_Popular_Posts extends TRX_Addons_Elementor_Widget {

			/**
			 * Widget base constructor.
			 *
			 * Initializing the widget base class.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @param array      $data Widget data. Default is an empty array.
			 * @param array|null $args Optional. Widget default arguments. Default is null.
			 */
			public function __construct( $data = [], $args = null ) {
				parent::__construct( $data, $args );
				$this->add_plain_params([
					'number' => 'size'
				]);
			}

			/**
			 * Retrieve widget name.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget name.
			 */
			public function get_name() {
				return 'trx_widget_popular_posts';
			}

			/**
			 * Retrieve widget title.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget title.
			 */
			public function get_title() {
				return __( 'Widget: Popular Posts', 'trx_addons' );
			}

			/**
			 * Retrieve widget icon.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget icon.
			 */
			public function get_icon() {
				return 'eicon-post-list';
			}

			/**
			 * Retrieve the list of categories the widget belongs to.
			 *
			 * Used to determine where to display the widget in the editor.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return array Widget categories.
			 */
			public function get_categories() {
				return ['trx_addons-widgets'];
			}

			/**
			 * Register widget controls.
			 *
			 * Adds different input fields to allow the user to change and customize the widget settings.
			 *
			 * @since 1.6.41
			 * @access protected
			 */
			protected function register_controls() {

				// If open params in Elementor Editor
				$params = $this->get_sc_params();
				// Prepare lists
				$post_type_1 = !empty($params['post_type_1']) ? $params['post_type_1'] : 'post';
				$taxonomy_1 = !empty($params['taxonomy_1']) ? $params['taxonomy_1'] : 'category';
				$tax_obj_1 = get_taxonomy($taxonomy_1);
				$post_type_2 = !empty($params['post_type_2']) ? $params['post_type_2'] : 'post';
				$taxonomy_2 = !empty($params['taxonomy_2']) ? $params['taxonomy_2'] : 'category';
				$tax_obj_2 = get_taxonomy($taxonomy_2);
				$post_type_3 = !empty($params['post_type_3']) ? $params['post_type_3'] : 'post';
				$taxonomy_3 = !empty($params['taxonomy_3']) ? $params['taxonomy_3'] : 'category';
				$tax_obj_3 = get_taxonomy($taxonomy_3);

				$this->start_controls_section(
					'section_sc_popular_posts',
					[
						'label' => __( 'Widget: Popular Posts', 'trx_addons' ),
					]
				);

				$this->add_control(
					'title',
					[
						'label' => __( 'Title', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "Widget title", 'trx_addons' ),
						'default' => ''
					]
				);

				$this->add_control(
					'number',
					[
						'label' => __( 'Number posts to show', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 4,
						],
						'range' => [
							'px' => [
								'min' => 1,
								'max' => 12
							]
						]
					]
				);

				$this->add_control(
					'details',
					[
						'label' => __( 'Details', 'elementor' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

				$this->add_control(
					'show_image',
					[
						'label' => __( "Show post's image", 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Hide', 'trx_addons' ),
						'label_on' => __( 'Show', 'trx_addons' ),
						'default' => 1,
						'return_value' => '1'
					]
				);

				$this->add_control(
					'show_author',
					[
						'label' => __( "Show post's author", 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Hide', 'trx_addons' ),
						'label_on' => __( 'Show', 'trx_addons' ),
						'default' => 1,
						'return_value' => '1'
					]
				);

				$this->add_control(
					'show_date',
					[
						'label' => __( "Show post's date", 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Hide', 'trx_addons' ),
						'label_on' => __( 'Show', 'trx_addons' ),
						'default' => 1,
						'return_value' => '1'
					]
				);

				$this->add_control(
					'show_counters',
					[
						'label' => __( "Show post's counters", 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Hide', 'trx_addons' ),
						'label_on' => __( 'Show', 'trx_addons' ),
						'default' => 1,
						'return_value' => '1'
					]
				);

				$this->add_control(
					'show_categories',
					[
						'label' => __( "Show post's categories", 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Hide', 'trx_addons' ),
						'label_on' => __( 'Show', 'trx_addons' ),
						'default' => 1,
						'return_value' => '1'
					]
				);

				$this->end_controls_section();

				$this->start_controls_section(
					'section_sc_popular_posts_tabs',
					[
						'label' => __( 'Tabs', 'trx_addons' ),
						'tab' => \Elementor\Controls_Manager::TAB_LAYOUT
					]
				);

				$this->add_control(
					'tab_1',
					[
						'label' => __( 'Tab 1', 'elementor' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'none',
					]
				);

				$this->add_control(
					'title_1',
					[
						'label' => __( 'Tab title', 'trx_addons' ),
						'label_block' => false,
						'description' => __( 'If empty - tab is not showed!', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "Popular", 'trx_addons' ),
						'default' => __( "Popular", 'trx_addons' )
					]
				);

				$this->add_control(
					'orderby_1',
					[
						'label' => __( 'Order by', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => trx_addons_get_list_widget_query_orderby(),
						'default' => 'views'
					]
				);

				$this->add_control(
					'post_type_1',
					[
						'label' => __( 'Post type', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => trx_addons_get_list_posts_types(),
						'default' => 'post'
					]
				);

				$this->add_control(
					'taxonomy_1',
					[
						'label' => __( 'Taxonomy', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => trx_addons_get_list_taxonomies(false, $post_type_1),
						'default' => 'category'
					]
				);

				$this->add_control(
					'cat_1',
					[
						'label' => __( 'Category', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => trx_addons_array_merge(array(0=>sprintf(__('- %s -', 'trx_addons'), $tax_obj_1->label)),
																		 $taxonomy_1 == 'category' 
																			? trx_addons_get_list_categories() 
																			: trx_addons_get_list_terms(false, $taxonomy_1)
																		),
						'default' => '0'
					]
				);

				$this->add_control(
					'tab_2',
					[
						'label' => __( 'Tab 2', 'elementor' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

				$this->add_control(
					'title_2',
					[
						'label' => __( 'Most commented tab title', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "Commented", 'trx_addons' ),
						'default' => __( "Commented", 'trx_addons' )
					]
				);

				$this->add_control(
					'orderby_2',
					[
						'label' => __( 'Order by', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => trx_addons_get_list_widget_query_orderby(),
						'default' => 'comments'
					]
				);

				$this->add_control(
					'post_type_2',
					[
						'label' => __( 'Post type', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => trx_addons_get_list_posts_types(),
						'default' => 'post'
					]
				);

				$this->add_control(
					'taxonomy_2',
					[
						'label' => __( 'Taxonomy', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => trx_addons_get_list_taxonomies(false, $post_type_2),
						'default' => 'category'
					]
				);

				$this->add_control(
					'cat_2',
					[
						'label' => __( 'Category', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => trx_addons_array_merge(array(0=>sprintf(__('- %s -', 'trx_addons'), $tax_obj_2->label)),
																		 $taxonomy_2 == 'category' 
																			? trx_addons_get_list_categories() 
																			: trx_addons_get_list_terms(false, $taxonomy_2)
																		),
						'default' => '0'
					]
				);

				$this->add_control(
					'tab_3',
					[
						'label' => __( 'Tab 3', 'elementor' ),
						'type' => \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

				$this->add_control(
					'title_3',
					[
						'label' => __( 'Most liked tab title', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "Liked", 'trx_addons' ),
						'default' => __( "Liked", 'trx_addons' )
					]
				);

				$this->add_control(
					'orderby_3',
					[
						'label' => __( 'Order by', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => trx_addons_get_list_widget_query_orderby(),
						'default' => 'likes'
					]
				);

				$this->add_control(
					'post_type_3',
					[
						'label' => __( 'Post type', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => trx_addons_get_list_posts_types(),
						'default' => 'post'
					]
				);

				$this->add_control(
					'taxonomy_3',
					[
						'label' => __( 'Taxonomy', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => trx_addons_get_list_taxonomies(false, $post_type_3),
						'default' => 'category'
					]
				);

				$this->add_control(
					'cat_3',
					[
						'label' => __( 'Category', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => trx_addons_array_merge(array(0=>sprintf(__('- %s -', 'trx_addons'), $tax_obj_3->label)),
																		 $taxonomy_3 == 'category' 
																			? trx_addons_get_list_categories() 
																			: trx_addons_get_list_terms(false, $taxonomy_3)
																		),
						'default' => '0'
					]
				);

				$this->end_controls_section();
			}
		}
		
		// Register widget
		\Elementor\Plugin::$instance->widgets_manager->register_widget_type( new TRX_Addons_Elementor_Widget_Popular_Posts() );
	}
}


// Disable our widgets (shortcodes) to use in Elementor
// because we create special Elementor's widgets instead
if (!function_exists('trx_addons_widget_popular_posts_black_list')) {
	add_action( 'elementor/widgets/black_list', 'trx_addons_widget_popular_posts_black_list' );
	function trx_addons_widget_popular_posts_black_list($list) {
		$list[] = 'trx_addons_widget_popular_posts';
		return $list;
	}
}
?>