<?php
/**
 * ThemeREX Addons Posts and Comments Reviews
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.47
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	die( '-1' );
}

// Define component's subfolder
if ( !defined('TRX_ADDONS_PLUGIN_REVIEWS') ) define('TRX_ADDONS_PLUGIN_REVIEWS', TRX_ADDONS_PLUGIN_COMPONENTS . 'reviews/');


// Add component to the global list
if (!function_exists('trx_addons_reviews_add_to_components')) {
	add_filter( 'trx_addons_components_list', 'trx_addons_reviews_add_to_components' );
	function trx_addons_reviews_add_to_components($list=array()) {
		$list['reviews'] = array(
					'title' => __('Reviews for posts and comments', 'trx_addons')
					);
		return $list;
	}
}

// Check if module is enabled
if (!function_exists('trx_addons_reviews_enable')) {
	function trx_addons_reviews_enable() {
		static $enable = null;
		if ($enable === null) {
			$enable = trx_addons_components_is_allowed('components', 'reviews');
		}
		return $enable;
	}
}

	
// Merge specific styles into single stylesheet
if ( !function_exists( 'trx_addons_reviews_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_reviews_merge_styles');
	function trx_addons_reviews_merge_styles($list) {
		if (trx_addons_reviews_enable()) {
			$list[] = TRX_ADDONS_PLUGIN_REVIEWS . '_reviews.scss';
		}
		return $list;
	}
}

	
// Merge specific scripts into single file
if ( !function_exists( 'trx_addons_reviews_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_reviews_merge_scripts', 11);
	function trx_addons_reviews_merge_scripts($list) {
		if (trx_addons_reviews_enable()) {
			$list[] = TRX_ADDONS_PLUGIN_REVIEWS . 'reviews.js';
		}
		return $list;
	}
}

// Load module-specific scripts
if (!function_exists('trx_addons_reviews_enqueue_scripts')) {
	add_action( 'wp_enqueue_scripts', 'trx_addons_reviews_enqueue_scripts', 20 );	
	function trx_addons_reviews_enqueue_scripts() {   
		if (trx_addons_reviews_enable() && trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_script( 'trx_addons-reviews', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_REVIEWS . 'reviews.js'), array('jquery'), null, true );
		}
	}
}

// Add module-specific vars to the frontend scripts
if (!function_exists('trx_addons_reviews_localize_scripts')) {
	add_filter( 'trx_addons_filter_localize_script', 'trx_addons_reviews_localize_scripts' );	
	function trx_addons_reviews_localize_scripts($vars) {
		$vars['msg_rating_already_marked'] = addslashes(esc_html__('You have already rated this post with mark {{X}}', 'trx_addons'));
		return $vars;
	}
}


// Add 'Reviews' section in the ThemeREX Addons Options
if (!function_exists('trx_addons_reviews_options')) {
	add_filter( 'trx_addons_filter_options', 'trx_addons_reviews_options');
	function trx_addons_reviews_options($options) {
		// Add section 'Reviews'
		if (trx_addons_reviews_enable()) {
			trx_addons_array_insert_before($options, 'sc_section', array(
				'reviews_section' => array(
					"title" => esc_html__('Reviews', 'trx_addons'),
					"type" => "section"
				),
				'reviews_section_info' => array(
					"title" => esc_html__('Reviews settings', 'trx_addons'),
					"desc" => wp_kses_data( __("Settings of posts and comments reviews", 'trx_addons') ),
					"type" => "info"
				),
				'allow_reviews' => array(
					"title" => esc_html__('Allow reviews',  'trx_addons'),
					"desc" => wp_kses_data( __('Allow to review posts and comments',  'trx_addons') ),
					"std" => "1",
					"type" => "checkbox"
				),		
				'reviews_action_name' => array(
					"title" => esc_html__('Action name',  'trx_addons'),
					"desc" => wp_kses_data( __('Write your action name to call the reviews block in any place you want',  'trx_addons') ),
					"dependency" => array(
						"allow_reviews" => array('1')
					),
					"std" => 'trx_addons_action_post_rating',
					"type" => "text"
				),
				"reviews_post_types" => array(
					"title" => esc_html__("Post types", 'trx_addons'),
					"desc" => wp_kses_data( __("Select post types to show reviews", 'trx_addons') ),
					"dir" => 'horizontal',
					"dependency" => array(
						"allow_reviews" => array('1')
					),
					"std" => array( 'post' => 1 ),
					"options" => array(),
					"type" => "checklist"
				),					
				'rating_max_level' => array(
					"title" => esc_html__('Max rating level',  'trx_addons'),
					"desc" => wp_kses_data( __('Maximum level for grading marks',  'trx_addons') ),
					"dependency" => array(
						"allow_reviews" => array('1')
					),
					"std" => "5",
					"options" => array(
						'5'   => esc_html__('5 stars', 'trx_addons'),
						'10'  => esc_html__('10 stars', 'trx_addons'),
						'100' => esc_html__('100%', 'trx_addons')
					),
					"type" => "radio"
				),
				'rating_style' => array(
					"title" => esc_html__('Show rating as', 'trx_addons'),
					"desc" => wp_kses_data( __('Show rating as icons or as progress bars or as text',  'trx_addons') ),
					"dependency" => array(
						"allow_reviews" => array('1')
					),
					"std" => "icons",
					"options" => array(
						'icons'	=> esc_html__('As icons', 'trx_addons'),
						'bar'	=> esc_html__('As progress bar', 'trx_addons'),
						'text'	=> esc_html__('As text (for example: 7.5 / 10)', 'trx_addons'),
					),
					"type" => "radio"
				),			
				'rating_color' => array(
					"title" => esc_html__('Rating color', 'trx_addons'),
					"desc" => wp_kses_data( __('Specify color for rating icons/bar',  'trx_addons') ),
					"dependency" => array(
						"allow_reviews" => array('1')
					),
					"std" => "",
					"type" => "color"
				),			
				"rating_icons" => array(
					"title" => esc_html__("Icon", 'trx_addons'),
					"desc" => wp_kses_data( __('Select icon for rating', 'trx_addons') ),
					"dependency" => array(
						"rating_style" => array('inherit','icons','text')
					),
					"dependency" => array(
						"allow_reviews" => array('1')
					),
					"std" => "trx_addons_icon-star",
					"options" => array(),
					"style" => trx_addons_get_setting('icons_type'),
					"type" => "icons"
				),
				'rating_text_template' => array(
					"title" => esc_html__('Text template',  'trx_addons'),
					"desc" => wp_kses_data( __('Write text template, where {{X}} - is a current value, {{Y}} - is a max value, {{V}} - is a number of votes. For example "Rating {{X}} from {{Y}} (according {{V}})"',  'trx_addons') ),
					"dependency" => array(
						"allow_reviews" => array('1')
					),
					"dependency" => array(
						"rating_style" => array('inherit', 'text')
					),
					"std" => esc_html__('Rating {{X}} from {{Y}}', 'trx_addons'),
					"type" => "text"
				)
			));
		}		
		return $options;
	}
}


// Add 'Rating' to the order list
if (!function_exists('trx_addons_reviews_add_rating_to_order_list')) {
	add_filter( 'trx_addons_filter_popular_posts_orderby', 'trx_addons_reviews_add_rating_to_order_list');
	add_filter( 'trx_addons_filter_get_list_sc_query_orderby', 'trx_addons_reviews_add_rating_to_order_list', 10, 2);
	function trx_addons_reviews_add_rating_to_order_list($list, $keys=array()) {
		if (trx_addons_reviews_enable())
			$list['rating'] = __('Rating', 'trx_addons');
		return $list;
	}
}

// Add order 'Rating' to the query params
if (!function_exists('trx_addons_reviews_add_rating_to_query_args')) {
	add_filter( 'trx_addons_filter_add_sort_order', 'trx_addons_reviews_add_rating_to_query_args', 10, 3);
	function trx_addons_reviews_add_rating_to_query_args($q_args, $orderby, $order) {
		if (trx_addons_reviews_enable()) {
			if ($orderby =='rating') {
				$q_args['meta_key'] = 'trx_addons_post_rating';
				$q_args['orderby'] = 'meta_value_num';
			}
		}
		return $q_args;
	}
}

// Add stars to the counters
if (!function_exists('trx_addons_reviews_add_rating_to_counters')) {
	add_filter( 'trx_addons_filter_show_post_counter', 'trx_addons_reviews_add_rating_to_counters', 10, 3);
	function trx_addons_reviews_add_rating_to_counters($output, $counter, $post_id='p0') {
		if (trx_addons_reviews_enable() && (in_array($counter, array('rating', 'reviews')))) {
			if ($post_id == 'p0') $post_id = 'p'.get_the_ID();
			if (!in_array(substr($post_id, 0, 1), array('c', 'p'))) $post_id = 'p'.$post_id;
			$reviews_post_types = trx_addons_get_option('reviews_post_types');
			if (!empty($reviews_post_types) && !empty($reviews_post_types[get_post_type((int)substr($post_id, 1))])) {
				$post_rating = trx_addons_get_post_rating($post_id, trx_addons_get_option('rating_max_level'));
			} else {
				$post_rating = apply_filters('trx_addons_filter_custom_meta_value', '', 'rating_text');
			}
			if ($post_rating > 0) {
				$output .= ' <a href="' . esc_url(get_permalink()) . '" class="post_counters_item post_counters_rating trx_addons_icon-star">'
								. '<span class="post_counters_number">' . trim($post_rating) . '</span>'
							. '</a> ';
			}
		}
		return $output;
	}
}


// Fill 'Post types' before show ThemeREX Addons Options
if (!function_exists('trx_addons_reviews_before_show_options')) {
	add_filter( 'trx_addons_filter_before_show_options', 'trx_addons_reviews_before_show_options', 10, 2);
	function trx_addons_reviews_before_show_options($options, $pt='') {
		if (trx_addons_reviews_enable() && isset($options['reviews_post_types'])) {
			$options['reviews_post_types']['options'] = trx_addons_get_list_reviews_posts_types();
		}
		return $options;
	}
}


// Return list of allowed post's types
if ( !function_exists( 'trx_addons_get_list_reviews_posts_types' ) ) {
	function trx_addons_get_list_reviews_posts_types($prepend_inherit=false) {
		static $list = false;
		if ($list === false) {
			$list = array();
			$post_types = get_post_types(array(
												'public' => true,
												'exclude_from_search' => false
												), 'object');
			if (is_array($post_types)) {
				foreach ($post_types as $pt) {
					$list[$pt->name] = $pt->label;
				}
			}
		}
		return $prepend_inherit 
					? trx_addons_array_merge(array('inherit' => esc_html__("Inherit", 'trx_addons')), $list) 
					: $list;
	}
}


// Convert rating value to save
if (!function_exists('trx_addons_rating2save')) {
	function trx_addons_rating2save($mark, $max) {
		if (is_array($mark) && isset($mark['total']))
			$mark = $mark['total'] / $mark['votes'];
		return round( $max > 0 && $max != 100 ? $mark * 100 / $max : $mark, 1);
	}
}


// Convert rating value to display
if (!function_exists('trx_addons_rating2show')) {
	function trx_addons_rating2show($mark, $max) {
		if (is_array($mark) && isset($mark['total']))
			$mark = $mark['total'] / $mark['votes'];
		return round( $max > 0 && $max != 100 ? $mark * $max / 100 : $mark, 1);
	}
}


// Return the post rating
if (!function_exists('trx_addons_get_post_rating')) {
	function trx_addons_get_post_rating($post_id='p0', $max=0){
		$rating = 0;
		$type = substr($post_id, 0, 1) == 'c' ? 'comment' : 'post';
		$post_id = (int) substr($post_id, 1);
		if (!$post_id) {
			$post_id = $type=='comment' ? get_comment_ID() : trx_addons_get_the_ID();				
		}
		if ($post_id) {
			$list = $type == 'comment' 
						? get_comment_meta($post_id, 'trx_addons_post_rating_data', true)
						: get_post_meta($post_id, 'trx_addons_post_rating_data', true);
			if (!empty($list) && is_array($list) && $list['votes'] > 0) {
				$rating = trx_addons_rating2show($list, $max);
			}
		}
		return $rating;
	}
}

// Return the number of votes
if (!function_exists('trx_addons_get_rating_votes')) {
	function trx_addons_get_rating_votes($post_id=0){
		$votes = 0;		
		$type = substr($post_id, 0, 1) == 'c' ? 'comment' : 'post';
		$post_id = (int) substr($post_id, 1);
		if (!$post_id) {
			$post_id = $type=='comment' ? get_comment_ID() : trx_addons_get_the_ID();				
		}
		if ($post_id) {
			$key = 'trx_addons_post_rating_data';
			$list = $type == 'comment' 
						? get_comment_meta($post_id, $key, true)
						: get_post_meta($post_id, $key, true);
			if (is_array($list) && !empty($list['votes'])) {
				$votes = $list['votes'];
			}
		}
		return $votes;
	}
}

// Increment the post's rating 
if (!function_exists('trx_addons_add_post_rating')) {
	function trx_addons_add_post_rating($mark, $max, $post_id) {
		$rating = -1;
		if ($post_id) {
			$type = $post_id[0] == 'c' ? 'comment' : 'post';
			$post_id = (int) substr($post_id, 1);
			// Add vote to the list
			$key = 'trx_addons_post_rating_data';
			$key2 = 'trx_addons_post_rating';
			$list = $type == 'comment' 
						? get_comment_meta($post_id, $key, true)
						: get_post_meta($post_id, $key, true);
			$value = trx_addons_rating2save($mark, $max);
			if (!is_array($list) || empty($list)) {
				$list = array(
								'total' => $value,
								'votes' => 1
								);
				if ($type == 'comment') {
					delete_comment_meta($post_id, $key);
					add_comment_meta($post_id, $key, $list);
					delete_comment_meta($post_id, $key2);
					add_comment_meta($post_id, $key2, $value);
				} else {
					delete_post_meta($post_id, $key);
					add_post_meta($post_id, $key, $list);
					delete_post_meta($post_id, $key2);
					add_post_meta($post_id, $key2, $value);
				}
				$rating = $mark;
			} else {
				$list['total'] += $value;
				$list['votes']++;
				if ($type == 'comment') {
					update_comment_meta($post_id, $key, $list);
					update_comment_meta($post_id, $key2, round($list['total'] / $list['votes'], 1));
				} else {
					update_post_meta($post_id, $key, $list);
					update_post_meta($post_id, $key2, round($list['total'] / $list['votes'], 1));
				}
				$rating = trx_addons_rating2show($list, $max);
			}
		} 
		return $rating;
	}
}

// AJAX: Set post rating
if ( !function_exists( 'trx_addons_callback_post_rating' ) ) {
	add_action('wp_ajax_post_rating', 			'trx_addons_callback_post_rating');
	add_action('wp_ajax_nopriv_post_rating',	'trx_addons_callback_post_rating');
	function trx_addons_callback_post_rating() {
		
		if ( !wp_verify_nonce( trx_addons_get_value_gp('nonce'), admin_url('admin-ajax.php') ) )
			die();
		
		$response = array('error' => '', 'rating' => -1);
		
		$post_id = trx_addons_get_value_gp('post_id');
		$mark = (float) trx_addons_get_value_gp('mark');
		$max = (float) trx_addons_get_value_gp('mark_max');
		if (!empty($post_id) && !empty($mark) && !empty($max)) {
			$response['rating'] = trx_addons_add_post_rating($mark, $max, $post_id);
		} else {
			$response['error'] = esc_html('Something went wrong. Please try again.', 'trx_addons');
		}
		
		echo json_encode($response);
		die();
	}
}

// Add custom action call
if ( !function_exists( 'trx_addons_post_rating_init' ) ) {
	add_action( 'init', 'trx_addons_post_rating_init' );
	function trx_addons_post_rating_init() { 
		if (trx_addons_reviews_enable() && !empty(trx_addons_get_option('reviews_action_name'))) {
			add_action(trx_addons_get_option('reviews_action_name'), 'trx_addons_post_rating', 10, 1);
		}
	}
}

// Return post rating layout
if ( !function_exists( 'trx_addons_post_rating' ) ) {
	add_action('trx_addons_action_post_rating', 'trx_addons_post_rating', 10, 1);
	function trx_addons_post_rating($args=array()) {
		if (trx_addons_reviews_enable()) {
			trx_addons_show_layout(trx_addons_sc_reviews($args));
		}
	}
}

// Return value of the custom field for the custom blog items
if ( !function_exists( 'trx_addons_rating_custom_meta_value' ) ) {
	add_filter( 'trx_addons_filter_custom_meta_value', 'trx_addons_rating_custom_meta_value', 11, 2 );
	function trx_addons_rating_custom_meta_value($value, $key) {
		if (empty($value) && trx_addons_reviews_enable()) {
			if (in_array($key, array('rating', 'rating_text', 'rating_bar', 'rating_icons', 'rating_stars'))) {
				$value = trx_addons_sc_reviews(array(
							'post_id' => get_the_ID(),
							'rating_max_level' => 5,
							'rating_style' => $key == 'rating_text' ? 'text' : ($key == 'rating_bar' ? 'bar' : 'icons'),
							'rating_text_template' => $key == 'rating_text' ? '' : '#',	// Don't display text
							'allow_voting' => 0
							));
			}
		}
		return $value;
	}
}


	
	
// trx_sc_reviews
//-------------------------------------------------------------
/*
[trx_sc_reviews id="unique_id" type="default"]
*/
// Reviews are used on single posts and comments. Use parameter "post_id" if you want to show reviews in blog archive or shortcodes.
if ( !function_exists( 'trx_addons_sc_reviews' ) ) {
	function trx_addons_sc_reviews($atts, $content=null) {	
		$atts = trx_addons_sc_prepare_atts('trx_sc_reviews', $atts, array(
			// Individual params
			"type" => "default",
			"title" => "",
			"post_id" => "",		// use it for display in shortcodes or blog archive (not in a singular posts)
			"location" => "post",	// post|comment
			"rating_max_level" => "",
			"rating_style" => "",
			"rating_color" => "",
			"rating_text_template" => "",
			"allow_voting" => "1",
			"icon" => "",
			"icon_type" => '',
			"icon_fontawesome" => "",
			"icon_openiconic" => "",
			"icon_typicons" => "",
			"icon_entypo" => "",
			"icon_linecons" => "",
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
			));

		$output = '';
		$allow_reviews = trx_addons_get_option('allow_reviews');
		$reviews_post_types = trx_addons_get_option('reviews_post_types');
		if ($allow_reviews && !empty($reviews_post_types) && (!empty($atts['post_id']) || is_singular())) {
			if (!empty($reviews_post_types[get_post_type()])) {
				if (!empty($atts['post_id']) && !in_array(substr($atts['post_id'], 0, 1), array('c', 'p'))) {
					$atts['post_id'] = ($atts['location'] == 'comment' ? 'c' : 'p') . intval($atts['post_id']);
				}
				$atts['class'] .= ($atts['class'] ? ' ' : '')
								. ($atts['location'] == 'comment' ? 'comment_rating' : 'post_rating') 
								. ' sc_reviews';
				ob_start();
				trx_addons_get_template_part(TRX_ADDONS_PLUGIN_REVIEWS . 'tpl.reviews.'.trx_addons_esc($atts['type']).'.php',
											'trx_addons_args_sc_reviews',
											$atts
											);
				$output = ob_get_contents();
				ob_end_clean();
			}
		}
		
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_reviews', $atts, $content);
	}
}


// Add [trx_sc_reviews] in the VC shortcodes list
if (!function_exists('trx_addons_sc_reviews_add_in_vc')) {
	function trx_addons_sc_reviews_add_in_vc() {

		if (!trx_addons_reviews_enable()) return;

		add_shortcode("trx_sc_reviews", "trx_addons_sc_reviews");

		if (!trx_addons_exists_vc()) return;

		vc_lean_map( "trx_sc_reviews", 'trx_addons_sc_reviews_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Reviews extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_reviews_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_reviews_add_in_vc_params')) {
	function trx_addons_sc_reviews_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_reviews",
				"name" => esc_html__("Reviews", 'trx_addons'),
				"description" => wp_kses_data( __("Display post reviews block", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_reviews',
				"class" => "trx_sc_reviews",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "type",
							"heading" => esc_html__("Layout", 'trx_addons'),
							"description" => wp_kses_data( __("Select shortcode's layout", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							'admin_label' => true,
							"std" => "default",
					        'save_always' => true,
							"value" => array_flip(apply_filters('trx_addons_sc_type', array(
								'default' => esc_html__('Default', 'trx_addons')
							), 'trx_sc_reviews')),
							"type" => "dropdown"
						),
						array(
							"param_name" => "title",
							"heading" => esc_html__("Title", 'trx_addons'),
							"description" => wp_kses_data( __("Title of the block. ", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							'admin_label' => true,
							"std" => "",
							"type" => "textfield"
						),
						array(
							"param_name" => "rating_max_level",
							"heading" => esc_html__("Max rating level", 'trx_addons'),
							"description" => wp_kses_data( __("Maximum level for grading marks", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							'admin_label' => true,
							"std" => "inherit",
							"value" => array(
								__( 'Inherit', 'trx_addons' ) => 'inherit',
								__( '5 stars', 'trx_addons' ) => '5',
								__( '10 stars', 'trx_addons' ) => '10',
								__( '100%', 'trx_addons' ) => '100'
							),
							"type" => "dropdown"
						),
						array(
							"param_name" => "rating_style",
							"heading" => esc_html__("Show rating as", 'trx_addons'),
							"description" => wp_kses_data( __("Show rating as icons or as progress bars or as text", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							'admin_label' => true,
							"std" => "inherit",
							"value" => array(
								__( 'Inherit', 'trx_addons' ) => 'inherit',
								__( 'As icons', 'trx_addons' ) => 'icons',
								__( 'As progress bar', 'trx_addons' ) => 'bar',
								__( 'As text (for example: 7.5 / 10)', 'trx_addons' ) => 'text'
							),
							"type" => "dropdown"
						),
						array(
							"param_name" => "rating_color",
							"heading" => esc_html__("Color", 'trx_addons'),
							"description" => wp_kses_data( __("Specify color for rating icons/bar", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							'admin_label' => true,
							"std" => "",
							"type" => "colorpicker"
						),
					),
					trx_addons_vc_add_icon_param('', false, 'icons'),
					array(
						array(
							"param_name" => "rating_text_template",
							"heading" => esc_html__("Text template", 'trx_addons'),
							"description" => wp_kses_data( __('Write text template, where {{X}} - is a current value, {{Y}} - is a max value, {{V}} - is a number of votes. For example "Rating {{X}} from {{Y}} (according {{V}})"', 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							'admin_label' => true,
							'dependency' => array(
								'element' => 'rating_style',
								'value' => array('inherit','text')
							),
							"std" => "",
							"type" => "textfield"
						),
						array(
							"param_name" => "allow_voting",
							"heading" => esc_html__("Allow voting", 'trx_addons'),
							"description" => wp_kses_data( __('Allow users to vote the post', 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							'admin_label' => true,
							"std" => "1",
							"value" => array( __( 'Allow', 'trx_addons' ) => '1' ),
							"type" => "checkbox"
						)
					),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_reviews' );
	}
}

// Elementor Widget
//------------------------------------------------------
if (!function_exists('trx_addons_sc_reviews_add_in_elementor')) {
	add_action( 'elementor/widgets/widgets_registered', 'trx_addons_sc_reviews_add_in_elementor' );
	function trx_addons_sc_reviews_add_in_elementor() {
		
		if (!trx_addons_reviews_enable() || !class_exists('TRX_Addons_Elementor_Widget')) return;	

		class TRX_Addons_Elementor_Widget_Reviews extends TRX_Addons_Elementor_Widget {

			/**
			 * Retrieve widget name.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget name.
			 */
			public function get_name() {
				return 'trx_sc_reviews';
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
				return __( 'Reviews', 'trx_addons' );
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
				return 'eicon-favorite';
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
				return ['trx_addons-elements'];
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
				$this->start_controls_section(
					'section_sc_reviews',
					[
						'label' => __( 'Reviews', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_type', array(
														'default' => esc_html__('Default', 'trx_addons')
													), 'trx_sc_reviews'),
						'default' => 'default',
					]
				);

				$this->add_control(
					'title',
					[
						'label' => __( 'Title', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'label_block' => false,
						'placeholder' => __( "Title", 'trx_addons' ),
						'default' => ''
					]
				);

				$this->add_control(
					'rating_max_level',
					[
						'label' => __( 'Max rating level', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => [
									'inherit' => esc_html__('Inherit', 'trx_addons'),
									'5' => esc_html__('5 stars', 'trx_addons'),
									'10' => esc_html__('10 stars', 'trx_addons'),
									'100' => esc_html__('100%', 'trx_addons')
									],
						'default' => 'inherit'
					]
				);

				$this->add_control(
					'rating_style',
					[
						'label' => __( 'Show rating as', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => [
									'inherit' => esc_html__('Inherit', 'trx_addons'),
									'icons' => esc_html__('As icons', 'trx_addons'),
									'bar' => esc_html__('As progress bar', 'trx_addons'),
									'text' => esc_html__('As text (for example: 7.5 / 10)', 'trx_addons')
									],
						'default' => 'inherit'
					]
				);

				$this->add_control(
					'rating_color',
					[
						'label' => __( 'Color', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
					]
				);

				$this->add_icon_param('', [], 'icons');

				$this->add_control(
					'rating_text_template',
					[
						'label' => __( 'Text template', 'trx_addons' ),
						'description' => wp_kses_data( __('Write text template, where {{X}} - is a current value, {{Y}} - is a max value, {{V}} - is a number of votes. For example "Rating {{X}} from {{Y}} (according {{V}})"', 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::TEXT,
						'label_block' => false,
						'default' => ''
					]
				);

				$this->add_control(
					'allow_voting',
					[
						'label' => __( 'Allow voting', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
					]
				);
			}

			/**
			 * Render widget's template for the editor.
			 *
			 * Written as a Backbone JavaScript template and used to generate the live preview.
			 *
			 * @since 1.6.41
			 * @access protected
			 */
			protected function _content_template() {
				trx_addons_get_template_part(TRX_ADDONS_PLUGIN_REVIEWS . "tpe.reviews.php",
										'trx_addons_args_sc_reviews',
										array('element' => $this)
									);
			}
		}
		
		// Register widget
		\Elementor\Plugin::$instance->widgets_manager->register_widget_type( new TRX_Addons_Elementor_Widget_Reviews() );
	}
}




// SOW Widget
//------------------------------------------------------
if (class_exists('TRX_Addons_SOW_Widget')) {
	class TRX_Addons_SOW_Widget_Reviews extends TRX_Addons_SOW_Widget {
		
		function __construct() {
			parent::__construct(
				'trx_addons_sow_widget_reviews',
				esc_html__('ThemeREX Reviews', 'trx_addons'),
				array(
					'classname' => 'widget_reviews',
					'description' => __('Display post reviews block', 'trx_addons')
				),
				array(),
				false,
				TRX_ADDONS_PLUGIN_DIR
			);
	
		}


		// Return array with all widget's fields
		function get_widget_form() {
			return apply_filters('trx_addons_sow_map', array_merge(
				array(
					'type' => array(
						'label' => __('Layout', 'trx_addons'),
						"description" => wp_kses_data( __("Select shortcodes's layout", 'trx_addons') ),
						'default' => 'default',
						'options' => apply_filters('trx_addons_sc_type', array(
														'default' => esc_html__('Default', 'trx_addons')
													), 'trx_sc_reviews'),
						'type' => 'select'
					),
					'title' => array(
						'label' => __('Title', 'trx_addons'),
						'description' => esc_html__( 'Title of the block', 'trx_addons' ),
						'type' => 'text'
					),
					'rating_max_level' => array(
						'label' => __('Max rating level', 'trx_addons'),
						'description' => esc_html__( 'Maximum level for grading marks', 'trx_addons' ),
						"default" => 'inherit',
						"options" => array(
								__( 'Inherit', 'trx_addons' ) => 'inherit',
								__( '5 stars', 'trx_addons' ) => '5',
								__( '10 stars', 'trx_addons' ) => '10',
								__( '100%', 'trx_addons' ) => '100'
							),
						"type" => "select"
					),
					'rating_style' => array(
						'label' => __('Show rating as', 'trx_addons'),
						'description' => esc_html__( 'Show rating as icons or as progress bars or as text', 'trx_addons' ),
						"default" => 'inherit',
						"options" => array(
								__( 'Inherit', 'trx_addons' ) => 'inherit',
								__( 'As icons', 'trx_addons' ) => 'icons',
								__( 'As progress bar', 'trx_addons' ) => 'bar',
								__( 'As text (for example: 7.5 / 10)', 'trx_addons' ) => 'text'
							),
						"type" => "select"
					),
					"rating_color" => array(
						"label" => esc_html__("Color", 'trx_addons'),
						"description" => wp_kses_data( __("Specify color for rating icons/bar", 'trx_addons') ),
						"type" => "color"
					)
				),
				trx_addons_sow_add_icon_param('', false, 'icons'),
				array(
					'rating_text_template' => array(
						'label' => __('Text template', 'trx_addons'),
						'description' => esc_html__( 'Write text template, where {{X}} - is a current value, {{Y}} - is a max value, , {{V}} - is a number of votes. For example "Rating {{X}} from {{Y}} (according {{V}})"', 'trx_addons' ),
						'type' => 'text'
					),
					'allow_voting' => array(
						'label' => __('Allow voting', 'trx_addons'),
						'description' => esc_html__( 'Allow users to vote the post', 'trx_addons' ),
						"default" => true,
						"type" => "checkbox"
					)
				),
				trx_addons_sow_add_id_param()
			), $this->get_sc_name());
		}

	}
	siteorigin_widget_register('trx_addons_sow_widget_reviews', __FILE__, 'TRX_Addons_SOW_Widget_Reviews');
}


// Widget 
//--------------------------------------------------------------------
if(!function_exists('trx_addons_reviews_add_widget')){
	add_filter('trx_addons_widgets_list', 'trx_addons_reviews_add_widget', 10, 1);
	function trx_addons_reviews_add_widget($array=array()){		
		if (trx_addons_reviews_enable()) {
			$array['rating_posts'] = array(
				'title' => __('Posts by rating', 'trx_addons')
			);
		}
		return $array;
	}
}

// Include files with widget
if (!function_exists('trx_addons_reviews_widgets_load')) {
	add_action( 'after_setup_theme', 'trx_addons_reviews_widgets_load', 10 );
	function trx_addons_reviews_widgets_load() {
		if (trx_addons_reviews_enable()) {
			$fdir = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_REVIEWS . "rating_posts/rating_posts.php");
			if (trx_addons_components_is_allowed('widgets', 'rating_posts') && $fdir != '') { 
				include_once $fdir;
			}
		}
	}
}
?>