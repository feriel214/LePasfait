<?php
/**
 * ThemeREX Addons Custom post type: Courses
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.2
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	die( '-1' );
}


// -----------------------------------------------------------------
// -- Custom post type registration
// -----------------------------------------------------------------

// Define Custom post type and taxonomy constants
if ( ! defined('TRX_ADDONS_CPT_COURSES_PT') ) define('TRX_ADDONS_CPT_COURSES_PT', trx_addons_cpt_param('courses', 'post_type'));
if ( ! defined('TRX_ADDONS_CPT_COURSES_TAXONOMY') ) define('TRX_ADDONS_CPT_COURSES_TAXONOMY', trx_addons_cpt_param('courses', 'taxonomy'));

// Register post type and taxonomy
if (!function_exists('trx_addons_cpt_courses_init')) {
	add_action( 'init', 'trx_addons_cpt_courses_init' );
	function trx_addons_cpt_courses_init() {

		// Add Courses parameters to the Meta Box support
		trx_addons_meta_box_register(TRX_ADDONS_CPT_COURSES_PT, array(
			"date" => array(
				"title" => esc_html__("Date",  'trx_addons'),
				"desc" => wp_kses_data( __("Start date in format: yyyy-mm-dd", 'trx_addons') ),
				"std" => "",
				"type" => "date"
			),
			"time" => array(
				"title" => esc_html__("Time",  'trx_addons'),
				"desc" => wp_kses_data( __("The time for start of classes. For example: 7.00pm - 9.00pm, 16:00 - 18:00, etc.", 'trx_addons') ),
				"std" => "",
				"type" => "time"
			),
			"duration" => array(
				"title" => esc_html__("Duration",  'trx_addons'),
				"desc" => wp_kses_data( __("The duration of course", 'trx_addons') ),
				"std" => "",
				"type" => "text"
			),
			"price" => array(
				"title" => esc_html__("Price",  'trx_addons'),
				"desc" => wp_kses_data( __("The price of course. For example: $99.90, $100.00/month, etc.", 'trx_addons') ),
				"std" => "",
				"type" => "text"
			),
			"product" => array(
				"title" => __('Link to course product',  'trx_addons'),
				"desc" => __("Link to product page for this course", 'trx_addons'),
				"std" => '',
				"options" => array(),
				"type" => "select2")
		));
		
		// Register post type and taxonomy
		register_post_type( TRX_ADDONS_CPT_COURSES_PT, array(
			'label'               => esc_html__( 'Courses', 'trx_addons' ),
			'description'         => esc_html__( 'Course Description', 'trx_addons' ),
			'labels'              => array(
				'name'                => esc_html__( 'Courses', 'trx_addons' ),
				'singular_name'       => esc_html__( 'Course', 'trx_addons' ),
				'menu_name'           => esc_html__( 'Courses', 'trx_addons' ),
				'parent_item_colon'   => esc_html__( 'Parent Item:', 'trx_addons' ),
				'all_items'           => esc_html__( 'All Courses', 'trx_addons' ),
				'view_item'           => esc_html__( 'View Course', 'trx_addons' ),
				'add_new_item'        => esc_html__( 'Add New Course', 'trx_addons' ),
				'add_new'             => esc_html__( 'Add New', 'trx_addons' ),
				'edit_item'           => esc_html__( 'Edit Course', 'trx_addons' ),
				'update_item'         => esc_html__( 'Update Course', 'trx_addons' ),
				'search_items'        => esc_html__( 'Search Courses', 'trx_addons' ),
				'not_found'           => esc_html__( 'Not found', 'trx_addons' ),
				'not_found_in_trash'  => esc_html__( 'Not found in Trash', 'trx_addons' ),
			),
			'taxonomies'          => array(TRX_ADDONS_CPT_COURSES_TAXONOMY),
			'supports'            => trx_addons_cpt_param('courses', 'supports'),
			'public'              => true,
			'hierarchical'        => false,
			'has_archive'         => true,
			'can_export'          => true,
			'show_in_admin_bar'   => true,
			'show_in_menu'        => true,
			'menu_position'       => '52.4',
			'menu_icon'			  => 'dashicons-welcome-learn-more',
			'capability_type'     => 'post',
			'rewrite'             => array( 'slug' => trx_addons_cpt_param('courses', 'post_type_slug') )
			)
		);

		register_taxonomy( TRX_ADDONS_CPT_COURSES_TAXONOMY, TRX_ADDONS_CPT_COURSES_PT, array(
			'post_type' 		=> TRX_ADDONS_CPT_COURSES_PT,
			'hierarchical'      => true,
			'labels'            => array(
				'name'              => esc_html__( 'Courses Group', 'trx_addons' ),
				'singular_name'     => esc_html__( 'Group', 'trx_addons' ),
				'search_items'      => esc_html__( 'Search Groups', 'trx_addons' ),
				'all_items'         => esc_html__( 'All Groups', 'trx_addons' ),
				'parent_item'       => esc_html__( 'Parent Group', 'trx_addons' ),
				'parent_item_colon' => esc_html__( 'Parent Group:', 'trx_addons' ),
				'edit_item'         => esc_html__( 'Edit Group', 'trx_addons' ),
				'update_item'       => esc_html__( 'Update Group', 'trx_addons' ),
				'add_new_item'      => esc_html__( 'Add New Group', 'trx_addons' ),
				'new_item_name'     => esc_html__( 'New Group Name', 'trx_addons' ),
				'menu_name'         => esc_html__( 'Courses Groups', 'trx_addons' ),
			),
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => trx_addons_cpt_param('courses', 'taxonomy_slug') )
			)
		);
	}
}


// Fill 'options' arrays when its need in the admin mode
if (!function_exists('trx_addons_cpt_courses_before_show_options')) {
	add_filter('trx_addons_filter_before_show_options', 'trx_addons_cpt_courses_before_show_options', 10, 2);
	function trx_addons_cpt_courses_before_show_options($options, $post_type, $group='') {
		if ($post_type == TRX_ADDONS_CPT_COURSES_PT) {
			foreach ($options as $id=>$field) {
				// Recursive call for options type 'group'
				if ($field['type'] == 'group' && !empty($field['fields'])) {
					$options[$id]['fields'] = trx_addons_cpt_courses_before_show_options($field['fields'], $post_type, $id);
					continue;
				}
				// Skip elements without param 'options'
				if (!isset($field['options']) || count($field['options'])>0) {
					continue;
				}
				// Fill the 'product' array
				if ($id == 'product') {
					$options[$id]['options'] = trx_addons_get_list_posts(false, 'product');
				}
			}
		}
		return $options;
	}
}

/* ------------------- Old way - moved to the cpt.php now ---------------------
// Add 'Courses' parameters in the ThemeREX Addons Options
if (!function_exists('trx_addons_cpt_courses_options')) {
	add_filter( 'trx_addons_filter_options', 'trx_addons_cpt_courses_options');
	function trx_addons_cpt_courses_options($options) {
		trx_addons_array_insert_after($options, 'cpt_section', trx_addons_cpt_courses_get_list_options());
		return $options;
	}
}

// Return parameters list for plugin's options
if (!function_exists('trx_addons_cpt_courses_get_list_options')) {
	function trx_addons_cpt_courses_get_list_options($add_parameters=array()) {
		return apply_filters('trx_addons_cpt_list_options', array(
			'courses_info' => array(
				"title" => esc_html__('Courses', 'trx_addons'),
				"desc" => wp_kses_data( __('Settings of the courses archive', 'trx_addons') ),
				"type" => "info"
			),
			'courses_style' => array(
				"title" => esc_html__('Style', 'trx_addons'),
				"desc" => wp_kses_data( __('Style of the courses archive', 'trx_addons') ),
				"std" => 'default_2',
				"options" => apply_filters('trx_addons_filter_cpt_archive_styles',
											trx_addons_components_get_allowed_layouts('cpt', 'courses', 'arh'),
											TRX_ADDONS_CPT_COURSES_PT),
				"type" => "select"
			)
		), 'courses');
	}
}
------------------- /Old way --------------------- */

	
// Merge shortcode's specific styles into single stylesheet
if ( !function_exists( 'trx_addons_cpt_courses_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_cpt_courses_merge_styles');
	function trx_addons_cpt_courses_merge_styles($list) {
		$list[] = TRX_ADDONS_PLUGIN_CPT . 'courses/_courses.scss';
		return $list;
	}
}


// Merge shortcode's specific styles to the single stylesheet (responsive)
if ( !function_exists( 'trx_addons_cpt_courses_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_cpt_courses_merge_styles_responsive');
	function trx_addons_cpt_courses_merge_styles_responsive($list) {
		$list[] = TRX_ADDONS_PLUGIN_CPT . 'courses/_courses.responsive.scss';
		return $list;
	}
}

	
// Add sort in the query for the courses
if ( !function_exists( 'trx_addons_cpt_courses_add_sort_order' ) ) {
	add_filter('trx_addons_filter_add_sort_order',	'trx_addons_cpt_courses_add_sort_order', 10, 3);
	function trx_addons_cpt_courses_add_sort_order($q, $orderby, $order='desc') {
		if ($orderby == 'courses_date') {
			$q['order'] = $order;
			$q['orderby'] = 'meta_value';
			$q['meta_key'] = 'trx_addons_courses_date';
		}
		return $q;
	}
}


// Save courses date for search, sorting, etc.
if ( !function_exists( 'trx_addons_cpt_courses_save_post_options' ) ) {
	add_filter('trx_addons_filter_save_post_options', 'trx_addons_cpt_courses_save_post_options', 10, 3);
	function trx_addons_cpt_courses_save_post_options($options, $post_id, $post_type) {
		if ($post_type == TRX_ADDONS_CPT_COURSES_PT) {
			$tm = explode('-', str_replace(' ', '', strtoupper($options['time'])));
			$tm_add = strpos($tm[0], 'PM')!==false ? 12 : 0;
			$tm = explode(':', str_replace(array('.', 'AM', 'PM', ' '), array(':', '', '', ''), $tm[0]));
			update_post_meta($post_id, 'trx_addons_courses_date', $options['date'].' '.(!empty($tm[1]) ? ($tm[0]+$tm_add).':'.$tm[1] : $tm[0]));
			update_post_meta($post_id, 'trx_addons_courses_price', $options['price']);
		}
		return $options;
	}
}


// Return true if it's courses page
if ( !function_exists( 'trx_addons_is_courses_page' ) ) {
	function trx_addons_is_courses_page() {
		return defined('TRX_ADDONS_CPT_COURSES_PT') 
					&& !is_search()
					&& (
						(is_single() && get_post_type()==TRX_ADDONS_CPT_COURSES_PT)
						|| is_post_type_archive(TRX_ADDONS_CPT_COURSES_PT)
						|| is_tax(TRX_ADDONS_CPT_COURSES_TAXONOMY)
						);
	}
}


// Return current page title
if ( !function_exists( 'trx_addons_cpt_courses_get_blog_title' ) ) {
	add_filter( 'trx_addons_filter_get_blog_title', 'trx_addons_cpt_courses_get_blog_title');
	function trx_addons_cpt_courses_get_blog_title($title='') {
		if ( defined('TRX_ADDONS_CPT_COURSES_PT') ) {
			if (is_single() && get_post_type()==TRX_ADDONS_CPT_COURSES_PT) {
				$meta = get_post_meta(get_the_ID(), 'trx_addons_options', true);
				$title = array(
					'text' => get_the_title(),
					'class' => 'courses_page_title'
				);
				if (!empty($meta['product']) && (int) $meta['product'] > 0) {
					$title['link'] = get_permalink($meta['product']);
					$title['link_text'] = esc_html__('Join the Course', 'trx_addons');
				}
			} else if ( is_post_type_archive(TRX_ADDONS_CPT_COURSES_PT) ) {
				$obj = get_post_type_object(TRX_ADDONS_CPT_COURSES_PT);
				$title = $obj->labels->all_items;
			}
		}
		return $title;
	}
}


// Replace standard theme templates
//-------------------------------------------------------------

// Change standard single template for the courses posts
if ( !function_exists( 'trx_addons_cpt_courses_single_template' ) ) {
	add_filter('single_template', 'trx_addons_cpt_courses_single_template');
	function trx_addons_cpt_courses_single_template($template) {
		global $post;
		if (is_single() && $post->post_type == TRX_ADDONS_CPT_COURSES_PT)
			$template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'courses/tpl.single.php');
		return $template;
	}
}

// Change standard archive template for the courses posts
if ( !function_exists( 'trx_addons_cpt_courses_archive_template' ) ) {
	add_filter('archive_template',	'trx_addons_cpt_courses_archive_template');
	function trx_addons_cpt_courses_archive_template( $template ) {
		if ( is_post_type_archive(TRX_ADDONS_CPT_COURSES_PT) )
			$template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'courses/tpl.archive.php');
		return $template;
	}	
}

// Change standard category template for the courses categories (groups)
if ( !function_exists( 'trx_addons_cpt_courses_taxonomy_template' ) ) {
	add_filter('taxonomy_template',	'trx_addons_cpt_courses_taxonomy_template');
	function trx_addons_cpt_courses_taxonomy_template( $template ) {
		if ( is_tax(TRX_ADDONS_CPT_COURSES_TAXONOMY) )
			$template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'courses/tpl.archive.php');
		return $template;
	}	
}

// Show related posts
if ( !function_exists( 'trx_addons_cpt_courses_related_posts_after_article' ) ) {
	add_action('trx_addons_action_after_article', 'trx_addons_cpt_courses_related_posts_after_article', 20, 1);
	function trx_addons_cpt_courses_related_posts_after_article( $mode ) {
		if ($mode == 'courses.single' && apply_filters('trx_addons_filter_show_related_posts_after_article', true)) {
			do_action('trx_addons_action_related_posts', $mode);
		}
	}
}

if ( !function_exists( 'trx_addons_cpt_courses_related_posts_show' ) ) {
	add_filter('trx_addons_filter_show_related_posts', 'trx_addons_cpt_courses_related_posts_show');
	function trx_addons_cpt_courses_related_posts_show( $show ) {
		if (!$show && is_single() && get_post_type() == TRX_ADDONS_CPT_COURSES_PT) {
			do_action('trx_addons_action_related_posts', 'courses.single');
			$show = true;
		}
		return $show;
	}
}

if ( !function_exists( 'trx_addons_cpt_courses_related_posts' ) ) {
	add_action('trx_addons_action_related_posts', 'trx_addons_cpt_courses_related_posts', 10, 1);
	function trx_addons_cpt_courses_related_posts( $mode ) {
		if ($mode == 'courses.single') {
			$trx_addons_related_style   = explode('_', trx_addons_get_option('courses_style'));
			$trx_addons_related_type    = $trx_addons_related_style[0];
			$trx_addons_related_columns = empty($trx_addons_related_style[1]) ? 1 : max(1, $trx_addons_related_style[1]);
			
			trx_addons_get_template_part('templates/tpl.posts-related.php',
												'trx_addons_args_related',
												apply_filters('trx_addons_filter_args_related', array(
																	'class' => 'courses_page_related sc_courses sc_courses_'.esc_attr($trx_addons_related_type),
																	'posts_per_page' => $trx_addons_related_columns,
																	'columns' => $trx_addons_related_columns,
																	'template' => TRX_ADDONS_PLUGIN_CPT . 'courses/tpl.'.trim($trx_addons_related_type).'-item.php',
																	'template_args_name' => 'trx_addons_args_sc_courses',
																	'post_type' => TRX_ADDONS_CPT_COURSES_PT,
																	'taxonomies' => array(TRX_ADDONS_CPT_COURSES_TAXONOMY),
																	'more_text' => __('Learn more', 'trx_addons')
																	)
															)
											);
		}
	}
}

// Show contact form
if ( !function_exists( 'trx_addons_cpt_courses_contact_form_after_article' ) ) {
	add_action('trx_addons_action_after_article', 'trx_addons_cpt_courses_contact_form_after_article', 15, 1);
	function trx_addons_cpt_courses_contact_form_after_article( $mode ) {
		if ($mode == 'courses.single') {
			do_action('trx_addons_action_contact_form', $mode);
		}
	}
}

if ( !function_exists( 'trx_addons_cpt_courses_contact_form' ) ) {
	add_action('trx_addons_action_contact_form', 'trx_addons_cpt_courses_contact_form', 10, 1);
	function trx_addons_cpt_courses_contact_form( $mode ) {
		if ($mode == 'courses.single') {
			if ( (int) ($form_id = trx_addons_get_option('courses_form')) > 0 ) {
				?><section class="page_contact_form courses_page_form">
					<h3 class="section_title page_contact_form_title"><?php
						esc_html_e('Join this course', 'trx_addons');
					?></h3><?php
					// Add filter 'wpcf7_form_elements' before Contact Form 7 show form to add text
					add_filter('wpcf7_form_elements', 'trx_addons_cpt_courses_wpcf7_form_elements');
					// Store data for the form for 4 hours
					set_transient(sprintf('trx_addons_cf7_%d_data', $form_id), array(
															'item'  => get_the_ID()
															), 4 * 60 * 60);
					// Display Contact Form 7
					trx_addons_show_layout(do_shortcode('[contact-form-7 id="'.esc_attr($form_id).'"]'));
					// Remove filter 'wpcf7_form_elements' after Contact Form 7 showed
					remove_filter('wpcf7_form_elements', 'trx_addons_cpt_courses_wpcf7_form_elements');
				?></section><?php
			}
		}
	}
}

// Add filter 'wpcf7_form_elements' before Contact Form 7 show form to add text
if ( !function_exists( 'trx_addons_cpt_courses_wpcf7_form_elements' ) ) {
	// Handler of the add_filter('wpcf7_form_elements', 'trx_addons_cpt_courses_wpcf7_form_elements');
	function trx_addons_cpt_courses_wpcf7_form_elements($elements) {
		$elements = str_replace('</textarea>',
								esc_html(sprintf(__("Hi.\nI'ld like to join the course '%s'.\nPlease, get in touch with me.", 'trx_addons'), get_the_title()))
								. '</textarea>',
								$elements
								);
		return $elements;
	}
}



// Admin utils
// -----------------------------------------------------------------

// Show <select> with courses categories in the admin filters area
if (!function_exists('trx_addons_cpt_courses_admin_filters')) {
	add_action( 'restrict_manage_posts', 'trx_addons_cpt_courses_admin_filters' );
	function trx_addons_cpt_courses_admin_filters() {
		trx_addons_admin_filters(TRX_ADDONS_CPT_COURSES_PT, TRX_ADDONS_CPT_COURSES_TAXONOMY);
	}
}
  
// Clear terms cache on the taxonomy save
if (!function_exists('trx_addons_cpt_courses_admin_clear_cache')) {
	add_action( 'edited_'.TRX_ADDONS_CPT_COURSES_TAXONOMY, 'trx_addons_cpt_courses_admin_clear_cache', 10, 1 );
	add_action( 'delete_'.TRX_ADDONS_CPT_COURSES_TAXONOMY, 'trx_addons_cpt_courses_admin_clear_cache', 10, 1 );
	add_action( 'created_'.TRX_ADDONS_CPT_COURSES_TAXONOMY, 'trx_addons_cpt_courses_admin_clear_cache', 10, 1 );
	function trx_addons_cpt_courses_admin_clear_cache( $term_id=0 ) {  
		trx_addons_admin_clear_cache_terms(TRX_ADDONS_CPT_COURSES_TAXONOMY);
	}
}


// trx_sc_courses
//-------------------------------------------------------------
/*
[trx_sc_courses id="unique_id" type="default" cat="category_slug or id" count="3" columns="3" slider="0|1"]
*/
if ( !function_exists( 'trx_addons_sc_courses' ) ) {
	function trx_addons_sc_courses($atts, $content=null) {	

		// Exit to prevent recursion
		if (trx_addons_sc_stack_check('trx_sc_courses')) return '';

		$atts = trx_addons_sc_prepare_atts('trx_sc_courses', $atts, array(
			// Individual params
			"type" => "default",
			"columns" => '',
			"cat" => '',
			"count" => 3,
			"offset" => 0,
			"orderby" => '',
			"order" => '',
			"ids" => '',
			"slider" => 0,
			"slider_pagination" => "none",
			"slider_controls" => "none",
			"slides_space" => 0,
			"slides_centered" => 0,
			"slides_overflow" => 0,
			"slider_mouse_wheel" => 0,
			"slider_autoplay" => 1,
			"past" => "0",
			"more_text" => esc_html__('More info', 'trx_addons'),
			"pagination" => "none",
			"page" => 1,
			"title" => "",
			"subtitle" => "",
			"description" => "",
			"link" => '',
			"link_style" => 'default',
			"link_image" => '',
			"link_text" => esc_html__('Learn more', 'trx_addons'),
			"title_align" => "left",
			"title_style" => "default",
			"title_tag" => '',
			"title_color" => '',
			"title_color2" => '',
			"gradient_direction" => '',
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
			)
		);

		if (!empty($atts['ids'])) {
			$atts['ids'] = str_replace(array(';', ' '), array(',', ''), $atts['ids']);
			$atts['count'] = count(explode(',', $atts['ids']));
		}
		$atts['count'] = max(1, (int) $atts['count']);
		$atts['offset'] = max(0, (int) $atts['offset']);
		if (empty($atts['orderby'])) $atts['orderby'] = 'courses_date';
		if (empty($atts['order'])) $atts['order'] = 'desc';
		$atts['slider'] = max(0, (int) $atts['slider']);
		if ($atts['slider'] > 0 && (int) $atts['slider_pagination'] > 0) $atts['slider_pagination'] = 'bottom';
		if ($atts['slider'] > 0) $atts['pagination'] = 'none';

		ob_start();
		trx_addons_get_template_part(array(
										TRX_ADDONS_PLUGIN_CPT . 'courses/tpl.'.trx_addons_esc($atts['type']).'.php',
										TRX_ADDONS_PLUGIN_CPT . 'courses/tpl.default.php'
										),
									'trx_addons_args_sc_courses',
									$atts
									);
		$output = ob_get_contents();
		ob_end_clean();

		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_courses', $atts, $content);
	}
}


// Add [trx_sc_courses] in the VC shortcodes list
if (!function_exists('trx_addons_sc_courses_add_in_vc')) {
	function trx_addons_sc_courses_add_in_vc() {

		add_shortcode("trx_sc_courses", "trx_addons_sc_courses");
		
		if (!trx_addons_exists_vc()) return;

		vc_lean_map( "trx_sc_courses", 'trx_addons_sc_courses_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Courses extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_courses_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_courses_add_in_vc_params')) {
	function trx_addons_sc_courses_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_courses",
				"name" => esc_html__("Courses", 'trx_addons'),
				"description" => wp_kses_data( __("Display courses from specified group", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_courses',
				"class" => "trx_sc_courses",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "type",
							"heading" => esc_html__("Layout", 'trx_addons'),
							"description" => wp_kses_data( __("Select shortcode's layout", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-6',
							"std" => "default",
							"value" => array_flip(apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('cpt', 'courses', 'sc'), 'trx_sc_courses')),
							"type" => "dropdown"
						),
						array(
							"param_name" => "more_text",
							"heading" => esc_html__("'More' text", 'trx_addons'),
							"description" => wp_kses_data( __("Specify caption of the 'Read more' button. If empty - hide button", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"std" => esc_html__('More info', 'trx_addons'),
							"type" => "textfield"
						),
						array(
							"param_name" => "past",
							"heading" => esc_html__("Past courses", 'trx_addons'),
							"description" => wp_kses_data( __("Show the past courses if checked, else - show upcoming courses", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-6',
							"std" => "0",
							"value" => array(esc_html__("Show past courses", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "pagination",
							"heading" => esc_html__("Pagination", 'trx_addons'),
							"description" => wp_kses_data( __("Add pagination links after posts. Attention! If using slider - pagination not allowed!", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"std" => 'none',
							"value" => array_flip(trx_addons_get_list_sc_paginations()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "cat",
							"heading" => esc_html__("Group", 'trx_addons'),
							"description" => wp_kses_data( __("Courses group", 'trx_addons') ),
							"value" => array_merge(array(esc_html__('- Select category -', 'trx_addons') => 0), array_flip(trx_addons_get_list_terms(false, TRX_ADDONS_CPT_COURSES_TAXONOMY))),
							"std" => "0",
							"type" => "dropdown"
						)
					),
					trx_addons_vc_add_query_param(''),
					trx_addons_vc_add_slider_param(),
					trx_addons_vc_add_title_param(),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_courses' );
	}
}




// Elementor Widget
//------------------------------------------------------
if (!function_exists('trx_addons_sc_courses_add_in_elementor')) {
	add_action( 'elementor/widgets/widgets_registered', 'trx_addons_sc_courses_add_in_elementor' );
	function trx_addons_sc_courses_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Widget')) return;	

		class TRX_Addons_Elementor_Widget_Courses extends TRX_Addons_Elementor_Widget {

			/**
			 * Retrieve widget name.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget name.
			 */
			public function get_name() {
				return 'trx_sc_courses';
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
				return __( 'Courses', 'trx_addons' );
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
				return 'eicon-edit';
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
				return ['trx_addons-cpt'];
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
					'section_sc_courses',
					[
						'label' => __( 'Courses', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('cpt', 'courses', 'sc'), 'trx_sc_courses'),
						'default' => 'default'
					]
				);

				$this->add_control(
					'past',
					[
						'label' => __( 'Past courses', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Show the past courses if checked, else - show upcoming courses", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1'
					]
				);

				$this->add_control(
					'more_text',
					[
						'label' => __( "'More' text", 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => esc_html__('More info', 'trx_addons'),
					]
				);

				$this->add_control(
					'pagination',
					[
						'label' => __( 'Pagination', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Add pagination links after posts. Attention! If using slider - pagination not allowed!", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => trx_addons_get_list_sc_paginations(),
						'default' => 'none'
					]
				);

				$this->add_control(
					'cat',
					[
						'label' => __( 'Group', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => trx_addons_array_merge(array(0 => esc_html__('- Select category -', 'trx_addons')), trx_addons_get_list_terms(false, TRX_ADDONS_CPT_COURSES_TAXONOMY)),
						'default' => '0'
					]
				);
				
				$this->add_query_param('');

				$this->end_controls_section();
				
				$this->add_slider_param();
				
				$this->add_title_param();
			}
		}
		
		// Register widget
		\Elementor\Plugin::$instance->widgets_manager->register_widget_type( new TRX_Addons_Elementor_Widget_Courses() );
	}
}


// Disable our widgets (shortcodes) to use in Elementor
// because we create special Elementor's widgets instead
if (!function_exists('trx_addons_sc_courses_black_list')) {
	add_action( 'elementor/widgets/black_list', 'trx_addons_sc_courses_black_list' );
	function trx_addons_sc_courses_black_list($list) {
		$list[] = 'TRX_Addons_SOW_Widget_Courses';
		return $list;
	}
}




// SOW Widget
//------------------------------------------------------
if (class_exists('TRX_Addons_SOW_Widget')) {
	class TRX_Addons_SOW_Widget_Courses extends TRX_Addons_SOW_Widget {
		
		function __construct() {
			parent::__construct(
				'trx_addons_sow_widget_courses',
				esc_html__('ThemeREX Courses', 'trx_addons'),
				array(
					'classname' => 'widget_courses',
					'description' => __('Display courses', 'trx_addons')
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
						'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('cpt', 'courses', 'sc'), $this->get_sc_name(), 'sow' ),
						'type' => 'select'
					),
					"more_text" => array(
						"label" => esc_html__("'More' text", 'trx_addons'),
						"description" => wp_kses_data( __("Specify caption of the 'Read more' button. If empty - hide button", 'trx_addons') ),
						"default" => esc_html__('More info', 'trx_addons'),
						"type" => "text"
					),
					"pagination" => array(
						"label" => esc_html__("Pagination", 'trx_addons'),
						"description" => wp_kses_data( __("Add pagination links after posts. Attention! If using slider - pagination not allowed!", 'trx_addons') ),
						"default" => 'none',
						"options" => trx_addons_get_list_sc_paginations(),
						"type" => "select"
					),
					"past" => array(
						"label" => esc_html__("Past courses", 'trx_addons'),
						"description" => wp_kses_data( __("Show the past courses if checked, else - show upcoming courses", 'trx_addons') ),
						"default" => false,
						"type" => "checkbox"
					),
					"cat" => array(
						"label" => esc_html__("Group", 'trx_addons'),
						"description" => wp_kses_data( __("Select courses group", 'trx_addons') ),
						"default" => 0,
						"options" => trx_addons_array_merge(array(0 => esc_html__('- Select category -', 'trx_addons')),
															trx_addons_get_list_terms(false, TRX_ADDONS_CPT_COURSES_TAXONOMY)
															),
						"type" => "select"
					)
				),
				trx_addons_sow_add_query_param(''),
				trx_addons_sow_add_slider_param(),
				trx_addons_sow_add_title_param(),
				trx_addons_sow_add_id_param()
			), $this->get_sc_name());
		}

	}
	siteorigin_widget_register('trx_addons_sow_widget_courses', __FILE__, 'TRX_Addons_SOW_Widget_Courses');
}
?>