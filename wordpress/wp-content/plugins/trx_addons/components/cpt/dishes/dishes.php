<?php
/**
 * ThemeREX Addons Custom post type: Dishes
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.09
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	die( '-1' );
}


// -----------------------------------------------------------------
// -- Custom post type registration
// -----------------------------------------------------------------

// Define Custom post type and taxonomy constants
if ( ! defined('TRX_ADDONS_CPT_DISHES_PT') ) define('TRX_ADDONS_CPT_DISHES_PT', trx_addons_cpt_param('dishes', 'post_type'));
if ( ! defined('TRX_ADDONS_CPT_DISHES_TAXONOMY') ) define('TRX_ADDONS_CPT_DISHES_TAXONOMY', trx_addons_cpt_param('dishes', 'taxonomy'));

// Register post type and taxonomy
if (!function_exists('trx_addons_cpt_dishes_init')) {
	add_action( 'init', 'trx_addons_cpt_dishes_init' );
	function trx_addons_cpt_dishes_init() {

		// Add dishes parameters to the Meta Box support
		trx_addons_meta_box_register(TRX_ADDONS_CPT_DISHES_PT, array(
			"price" => array(
				"title" => esc_html__("Price",  'trx_addons'),
				"desc" => wp_kses_data( __("The price of the dish", 'trx_addons') ),
				"std" => "",
				"type" => "text"
			),
			"product" => array(
				"title" => __('Link to the dish product',  'trx_addons'),
				"desc" => __("Link to the product page for this dish", 'trx_addons'),
				"std" => '',
				"options" => array(),
				"type" => "select2"
			),
			"spicy" => array(
				"title" => esc_html__("Spicy",  'trx_addons'),
				"desc" => wp_kses_data( __("Spicy level of this dish from 1 to 5", 'trx_addons') ),
				"std" => "",
				"type" => "text"
			),
			"nutritions" => array(
				"title" => esc_html__("Nutritions",  'trx_addons'),
				"desc" => wp_kses_data( __("Nutritional information. Each element on the new row", 'trx_addons') ),
				"std" => "",
				"type" => "textarea"
			),
			"ingredients" => array(
				"title" => esc_html__("Ingredients",  'trx_addons'),
				"desc" => wp_kses_data( __("Ingredients of this dish. Each element on the new row", 'trx_addons') ),
				"std" => "",
				"type" => "textarea"
			)
		));
		
		// Register post type and taxonomy
		register_post_type( TRX_ADDONS_CPT_DISHES_PT, array(
			'label'               => esc_html__( 'Dishes', 'trx_addons' ),
			'description'         => esc_html__( 'Dish Description', 'trx_addons' ),
			'labels'              => array(
				'name'                => esc_html__( 'Dishes', 'trx_addons' ),
				'singular_name'       => esc_html__( 'Dish', 'trx_addons' ),
				'menu_name'           => esc_html__( 'Dishes', 'trx_addons' ),
				'parent_item_colon'   => esc_html__( 'Parent Item:', 'trx_addons' ),
				'all_items'           => esc_html__( 'All Dishes', 'trx_addons' ),
				'view_item'           => esc_html__( 'View Dish', 'trx_addons' ),
				'add_new_item'        => esc_html__( 'Add New Dish', 'trx_addons' ),
				'add_new'             => esc_html__( 'Add New', 'trx_addons' ),
				'edit_item'           => esc_html__( 'Edit Dish', 'trx_addons' ),
				'update_item'         => esc_html__( 'Update Dish', 'trx_addons' ),
				'search_items'        => esc_html__( 'Search Dishes', 'trx_addons' ),
				'not_found'           => esc_html__( 'Not found', 'trx_addons' ),
				'not_found_in_trash'  => esc_html__( 'Not found in Trash', 'trx_addons' ),
			),
			'taxonomies'          => array(TRX_ADDONS_CPT_DISHES_TAXONOMY),
			'supports'            => trx_addons_cpt_param('dishes', 'supports'),
			'public'              => true,
			'hierarchical'        => false,
			'has_archive'         => true,
			'can_export'          => true,
			'show_in_admin_bar'   => true,
			'show_in_menu'        => true,
			'menu_position'       => '52.6',
			'menu_icon'			  => 'dashicons-carrot',
			'capability_type'     => 'post',
			'rewrite'             => array( 'slug' => trx_addons_cpt_param('dishes', 'post_type_slug') )
			)
		);

		register_taxonomy( TRX_ADDONS_CPT_DISHES_TAXONOMY, TRX_ADDONS_CPT_DISHES_PT, array(
			'post_type' 		=> TRX_ADDONS_CPT_DISHES_PT,
			'hierarchical'      => true,
			'labels'            => array(
				'name'              => esc_html__( 'Dishes Group', 'trx_addons' ),
				'singular_name'     => esc_html__( 'Group', 'trx_addons' ),
				'search_items'      => esc_html__( 'Search Groups', 'trx_addons' ),
				'all_items'         => esc_html__( 'All Groups', 'trx_addons' ),
				'parent_item'       => esc_html__( 'Parent Group', 'trx_addons' ),
				'parent_item_colon' => esc_html__( 'Parent Group:', 'trx_addons' ),
				'edit_item'         => esc_html__( 'Edit Group', 'trx_addons' ),
				'update_item'       => esc_html__( 'Update Group', 'trx_addons' ),
				'add_new_item'      => esc_html__( 'Add New Group', 'trx_addons' ),
				'new_item_name'     => esc_html__( 'New Group Name', 'trx_addons' ),
				'menu_name'         => esc_html__( 'Dishes Groups', 'trx_addons' ),
			),
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => trx_addons_cpt_param('dishes', 'taxonomy_slug') )
			)
		);
	}
}


// Fill 'options' arrays when its need in the admin mode
if (!function_exists('trx_addons_cpt_dishes_before_show_options')) {
	add_filter('trx_addons_filter_before_show_options', 'trx_addons_cpt_dishes_before_show_options', 10, 2);
	function trx_addons_cpt_dishes_before_show_options($options, $post_type, $group='') {
		if ($post_type == TRX_ADDONS_CPT_DISHES_PT) {
			foreach ($options as $id=>$field) {
				// Recursive call for options type 'group'
				if ($field['type'] == 'group' && !empty($field['fields'])) {
					$options[$id]['fields'] = trx_addons_cpt_dishes_before_show_options($field['fields'], $post_type, $id);
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
// Add 'Dishes' parameters in the ThemeREX Addons Options
if (!function_exists('trx_addons_cpt_dishes_options')) {
	add_filter( 'trx_addons_filter_options', 'trx_addons_cpt_dishes_options');
	function trx_addons_cpt_dishes_options($options) {
		trx_addons_array_insert_after($options, 'cpt_section', trx_addons_cpt_dishes_get_list_options());
		return $options;
	}
}

// Return parameters list for plugin's options
if (!function_exists('trx_addons_cpt_dishes_get_list_options')) {
	function trx_addons_cpt_dishes_get_list_options($add_parameters=array()) {
		return apply_filters('trx_addons_cpt_list_options', array(
			'dishes_info' => array(
				"title" => esc_html__('Dishes', 'trx_addons'),
				"desc" => wp_kses_data( __('Settings of the dishes archive', 'trx_addons') ),
				"type" => "info"
			),
			'dishes_style' => array(
				"title" => esc_html__('Style', 'trx_addons'),
				"desc" => wp_kses_data( __('Style of the dishes archive', 'trx_addons') ),
				"std" => 'default_2',
				"options" => apply_filters('trx_addons_filter_cpt_archive_styles',
											trx_addons_components_get_allowed_layouts('cpt', 'dishes', 'arh'),
											TRX_ADDONS_CPT_DISHES_PT),
				"type" => "select"
			)
		), 'dishes');
	}
}
------------------- /Old way --------------------- */

	
// Merge shortcode's specific styles into single stylesheet
if ( !function_exists( 'trx_addons_cpt_dishes_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_cpt_dishes_merge_styles');
	function trx_addons_cpt_dishes_merge_styles($list) {
		$list[] = TRX_ADDONS_PLUGIN_CPT . 'dishes/_dishes.scss';
		return $list;
	}
}


// Merge shortcode's specific styles to the single stylesheet (responsive)
if ( !function_exists( 'trx_addons_cpt_dishes_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_cpt_dishes_merge_styles_responsive');
	function trx_addons_cpt_dishes_merge_styles_responsive($list) {
		$list[] = TRX_ADDONS_PLUGIN_CPT . 'dishes/_dishes.responsive.scss';
		return $list;
	}
}


// Return true if it's dishes page
if ( !function_exists( 'trx_addons_is_dishes_page' ) ) {
	function trx_addons_is_dishes_page() {
		return defined('TRX_ADDONS_CPT_DISHES_PT') 
					&& !is_search()
					&& (
						(is_single() && get_post_type()==TRX_ADDONS_CPT_DISHES_PT)
						|| is_post_type_archive(TRX_ADDONS_CPT_DISHES_PT)
						|| is_tax(TRX_ADDONS_CPT_DISHES_TAXONOMY)
						);
	}
}


// Return current page title
if ( !function_exists( 'trx_addons_cpt_dishes_get_blog_title' ) ) {
	add_filter( 'trx_addons_filter_get_blog_title', 'trx_addons_cpt_dishes_get_blog_title');
	function trx_addons_cpt_dishes_get_blog_title($title='') {
		if ( defined('TRX_ADDONS_CPT_DISHES_PT') ) {
			if (is_single() && get_post_type()==TRX_ADDONS_CPT_DISHES_PT) {
				$meta = get_post_meta(get_the_ID(), 'trx_addons_options', true);
				$title = array(
					'text' => get_the_title(),
					'class' => 'dishes_page_title'
				);
				if (!empty($meta['product']) && (int) $meta['product'] > 0) {
					$title['link'] = get_permalink($meta['product']);
					$title['link_text'] = esc_html__('Order now', 'trx_addons');
				}
			} else if ( is_post_type_archive(TRX_ADDONS_CPT_DISHES_PT) ) {
				$obj = get_post_type_object(TRX_ADDONS_CPT_DISHES_PT);
				$title = $obj->labels->all_items;
			}
		}
		return $title;
	}
}


// Replace standard theme templates
//-------------------------------------------------------------

// Change standard single template for the dishes posts
if ( !function_exists( 'trx_addons_cpt_dishes_single_template' ) ) {
	add_filter('single_template', 'trx_addons_cpt_dishes_single_template');
	function trx_addons_cpt_dishes_single_template($template) {
		global $post;
		if (is_single() && $post->post_type == TRX_ADDONS_CPT_DISHES_PT)
			$template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'dishes/tpl.single.php');
		return $template;
	}
}

// Change standard archive template for the dishes posts
if ( !function_exists( 'trx_addons_cpt_dishes_archive_template' ) ) {
	add_filter('archive_template',	'trx_addons_cpt_dishes_archive_template');
	function trx_addons_cpt_dishes_archive_template( $template ) {
		if ( is_post_type_archive(TRX_ADDONS_CPT_DISHES_PT) )
			$template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'dishes/tpl.archive.php');
		return $template;
	}	
}

// Change standard category template for the dishes categories (groups)
if ( !function_exists( 'trx_addons_cpt_dishes_taxonomy_template' ) ) {
	add_filter('taxonomy_template',	'trx_addons_cpt_dishes_taxonomy_template');
	function trx_addons_cpt_dishes_taxonomy_template( $template ) {
		if ( is_tax(TRX_ADDONS_CPT_DISHES_TAXONOMY) )
			$template = trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_CPT . 'dishes/tpl.archive.php');
		return $template;
	}	
}

// Show related posts
if ( !function_exists( 'trx_addons_cpt_dishes_related_posts_after_article' ) ) {
	add_action('trx_addons_action_after_article', 'trx_addons_cpt_dishes_related_posts_after_article', 20, 1);
	function trx_addons_cpt_dishes_related_posts_after_article( $mode ) {
		if ($mode == 'dishes.single' && apply_filters('trx_addons_filter_show_related_posts_after_article', true)) {
			do_action('trx_addons_action_related_posts', $mode);
		}
	}
}

if ( !function_exists( 'trx_addons_cpt_dishes_related_posts_show' ) ) {
	add_filter('trx_addons_filter_show_related_posts', 'trx_addons_cpt_dishes_related_posts_show');
	function trx_addons_cpt_dishes_related_posts_show( $show ) {
		if (!$show && is_single() && get_post_type() == TRX_ADDONS_CPT_DISHES_PT) {
			do_action('trx_addons_action_related_posts', 'dishes.single');
			$show = true;
		}
		return $show;
	}
}

if ( !function_exists( 'trx_addons_cpt_dishes_related_posts' ) ) {
	add_action('trx_addons_action_related_posts', 'trx_addons_cpt_dishes_related_posts', 10, 1);
	function trx_addons_cpt_dishes_related_posts( $mode ) {
		if ($mode == 'dishes.single') {
			$trx_addons_related_style   = explode('_', trx_addons_get_option('dishes_style'));
			$trx_addons_related_type    = $trx_addons_related_style[0];
			$trx_addons_related_columns = empty($trx_addons_related_style[1]) ? 1 : max(1, $trx_addons_related_style[1]);

			trx_addons_get_template_part('templates/tpl.posts-related.php',
												'trx_addons_args_related',
												apply_filters('trx_addons_filter_args_related', array(
																	'class' => 'dishes_page_related sc_dishes sc_dishes_'.esc_attr($trx_addons_related_type),
																	'posts_per_page' => $trx_addons_related_columns,
																	'columns' => $trx_addons_related_columns,
																	'template' => TRX_ADDONS_PLUGIN_CPT . 'dishes/tpl.'.trim($trx_addons_related_type).'-item.php',
																	'template_args_name' => 'trx_addons_args_sc_dishes',
																	'post_type' => TRX_ADDONS_CPT_DISHES_PT,
																	'taxonomies' => array(TRX_ADDONS_CPT_DISHES_TAXONOMY)
																	)
															)
											);
		}
	}
}

// Show contact form
if ( !function_exists( 'trx_addons_cpt_dishes_contact_form_after_article' ) ) {
	add_action('trx_addons_action_after_article', 'trx_addons_cpt_dishes_contact_form_after_article', 15, 1);
	function trx_addons_cpt_dishes_contact_form_after_article( $mode ) {
		if ($mode == 'dishes.single') {
			do_action('trx_addons_action_contact_form', $mode);
		}
	}
}

if ( !function_exists( 'trx_addons_cpt_dishes_contact_form' ) ) {
	add_action('trx_addons_action_contact_form', 'trx_addons_cpt_dishes_contact_form', 10, 1);
	function trx_addons_cpt_dishes_contact_form( $mode ) {
		if ($mode == 'dishes.single') {
			if ( (int) ($form_id = trx_addons_get_option('dishes_form')) > 0 ) {
				?><section class="page_contact_form dishes_page_form">
					<h3 class="section_title page_contact_form_title"><?php
						esc_html_e('Order this dish', 'trx_addons');
					?></h3><?php
					// Add filter 'wpcf7_form_elements' before Contact Form 7 show form to add text
					add_filter('wpcf7_form_elements', 'trx_addons_cpt_dishes_wpcf7_form_elements');
					// Store data for the form for 4 hours
					set_transient(sprintf('trx_addons_cf7_%d_data', $form_id), array(
															'item'  => get_the_ID()
															), 4 * 60 * 60);
					// Display Contact Form 7
					trx_addons_show_layout(do_shortcode('[contact-form-7 id="'.esc_attr($form_id).'"]'));
					// Remove filter 'wpcf7_form_elements' after Contact Form 7 showed
					remove_filter('wpcf7_form_elements', 'trx_addons_cpt_dishes_wpcf7_form_elements');
				?></section><?php
			}
		}
	}
}

// Add filter 'wpcf7_form_elements' before Contact Form 7 show form to add text
if ( !function_exists( 'trx_addons_cpt_dishes_wpcf7_form_elements' ) ) {
	// Handler of the add_filter('wpcf7_form_elements', 'trx_addons_cpt_dishes_wpcf7_form_elements');
	function trx_addons_cpt_dishes_wpcf7_form_elements($elements) {
		$elements = str_replace('</textarea>',
								esc_html(sprintf(__("Hi.\nI'ld like this dish '%s'.\nPlease, get in touch with me.", 'trx_addons'), get_the_title()))
								. '</textarea>',
								$elements
								);
		return $elements;
	}
}



// Admin utils
// -----------------------------------------------------------------

// Show <select> with dishes categories in the admin filters area
if (!function_exists('trx_addons_cpt_dishes_admin_filters')) {
	add_action( 'restrict_manage_posts', 'trx_addons_cpt_dishes_admin_filters' );
	function trx_addons_cpt_dishes_admin_filters() {
		trx_addons_admin_filters(TRX_ADDONS_CPT_DISHES_PT, TRX_ADDONS_CPT_DISHES_TAXONOMY);
	}
}
  
// Clear terms cache on the taxonomy save
if (!function_exists('trx_addons_cpt_dishes_admin_clear_cache')) {
	add_action( 'edited_'.TRX_ADDONS_CPT_DISHES_TAXONOMY, 'trx_addons_cpt_dishes_admin_clear_cache', 10, 1 );
	add_action( 'delete_'.TRX_ADDONS_CPT_DISHES_TAXONOMY, 'trx_addons_cpt_dishes_admin_clear_cache', 10, 1 );
	add_action( 'created_'.TRX_ADDONS_CPT_DISHES_TAXONOMY, 'trx_addons_cpt_dishes_admin_clear_cache', 10, 1 );
	function trx_addons_cpt_dishes_admin_clear_cache( $term_id=0 ) {  
		trx_addons_admin_clear_cache_terms(TRX_ADDONS_CPT_DISHES_TAXONOMY);
	}
}


// AJAX details
// ------------------------------------------------------------
if ( !function_exists( 'trx_addons_callback_ajax_dishes_details' ) ) {
	add_action('wp_ajax_trx_addons_post_details_in_popup',			'trx_addons_callback_ajax_dishes_details');
	add_action('wp_ajax_nopriv_trx_addons_post_details_in_popup',	'trx_addons_callback_ajax_dishes_details');
	function trx_addons_callback_ajax_dishes_details() {
		if ( !wp_verify_nonce( trx_addons_get_value_gp('nonce'), admin_url('admin-ajax.php') ) )
			die();

		if (($post_type = $_REQUEST['post_type']) == TRX_ADDONS_CPT_DISHES_PT) {
			$post_id = $_REQUEST['post_id'];

			$response = array('error'=>'', 'data' => '');
	
			if (!empty($post_id)) {
				global $post;
				$post = get_post($post_id);
				setup_postdata( $post );
				ob_start();
				trx_addons_get_template_part(TRX_ADDONS_PLUGIN_CPT . 'dishes/tpl.details.php');
				$response['data'] = ob_get_contents();
				ob_end_clean();
			} else {
				$response['error'] = '<article class="dishes_page">' . esc_html__('Invalid query parameter!', 'trx_addons') . '</article>';
			}
		
			echo json_encode($response);
			die();
		}
	}
}


// trx_sc_dishes
//-------------------------------------------------------------
/*
[trx_sc_dishes id="unique_id" type="default" cat="category_slug or id" count="3" columns="3" slider="0|1"]
*/
if ( !function_exists( 'trx_addons_sc_dishes' ) ) {
	function trx_addons_sc_dishes($atts, $content=null) {	

		// Exit to prevent recursion
		if (trx_addons_sc_stack_check('trx_sc_dishes')) return '';

		$atts = trx_addons_sc_prepare_atts('trx_sc_dishes', $atts, array(
			// Individual params
			"type" => "default",
			"columns" => "",
			"cat" => '',
			"count" => 3,
			"offset" => 0,
			"orderby" => '',
			"order" => '',
			"ids" => '',
			"popup" => 0,
			"slider" => 0,
			"slider_pagination" => "none",
			"slider_controls" => "none",
			"slides_space" => 0,
			"slides_centered" => 0,
			"slides_overflow" => 0,
			"slider_mouse_wheel" => 0,
			"slider_autoplay" => 1,
			"featured_position" => "top",
			"hide_excerpt" => 0,
			"more_text" => esc_html__('Read more', 'trx_addons'),
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
		if (empty($atts['orderby'])) $atts['orderby'] = 'title';
		if (empty($atts['order'])) $atts['order'] = 'asc';
		$atts['popup'] = max(0, (int) $atts['popup']);
		if ($atts['popup']) $atts['class'] .= (!empty($atts['class']) ? ' ' : '') . 'sc_dishes_popup sc_post_details_popup';
		$atts['slider'] = max(0, (int) $atts['slider']);
		if ($atts['slider'] > 0 && (int) $atts['slider_pagination'] > 0) $atts['slider_pagination'] = 'bottom';
		if ($atts['slider'] > 0) $atts['pagination'] = 'none';

		ob_start();
		trx_addons_get_template_part(array(
										TRX_ADDONS_PLUGIN_CPT . 'dishes/tpl.'.trx_addons_esc($atts['type']).'.php',
										TRX_ADDONS_PLUGIN_CPT . 'dishes/tpl.default.php'
										),
									'trx_addons_args_sc_dishes',
									$atts
									);
		$output = ob_get_contents();
		ob_end_clean();
		
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_dishes', $atts, $content);
	}
}


// Add [trx_sc_dishes] in the VC shortcodes list
if (!function_exists('trx_addons_sc_dishes_add_in_vc')) {
	function trx_addons_sc_dishes_add_in_vc() {
		
		add_shortcode("trx_sc_dishes", "trx_addons_sc_dishes");

		if (!trx_addons_exists_vc()) return;

		vc_lean_map( "trx_sc_dishes", 'trx_addons_sc_dishes_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Dishes extends WPBakeryShortCode {}

	}
	add_action('init', 'trx_addons_sc_dishes_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_dishes_add_in_vc_params')) {
	function trx_addons_sc_dishes_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_dishes",
				"name" => esc_html__("Dishes", 'trx_addons'),
				"description" => wp_kses_data( __("Display dishes from specified group", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_dishes',
				"class" => "trx_sc_dishes",
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
					        'save_always' => true,
							"value" => array_flip(apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('cpt', 'dishes', 'sc'), 'trx_sc_dishes')),
							"type" => "dropdown"
						),
						array(
							"param_name" => "featured_position",
							"heading" => esc_html__("Featured position", 'trx_addons'),
							"description" => wp_kses_data( __("Select the position of the featured element", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-6',
							"std" => "top",
					        'save_always' => true,
							"value" => array_flip(trx_addons_get_list_sc_dishes_positions()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "more_text",
							"heading" => esc_html__("'More' text", 'trx_addons'),
							"description" => wp_kses_data( __("Specify caption of the 'Read more' button. If empty - hide button", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"std" => esc_html__('Read more', 'trx_addons'),
							"type" => "textfield"
						),
						array(
							"param_name" => "pagination",
							"heading" => esc_html__("Pagination", 'trx_addons'),
							"description" => wp_kses_data( __("Add pagination links after posts. Attention! If using slider - pagination not allowed!", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => 'none',
							"value" => array_flip(trx_addons_get_list_sc_paginations()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "hide_excerpt",
							"heading" => esc_html__("Excerpt", 'trx_addons'),
							"description" => wp_kses_data( __("Check if you want hide excerpt", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							'dependency' => array(
								'element' => 'type',
								'value' => array('default', 'float')
							),
							"std" => "0",
							"value" => array(esc_html__("Hide excerpt", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "popup",
							"heading" => esc_html__("Open in the popup", 'trx_addons'),
							"description" => wp_kses_data( __("Open details in the popup or navigate to the single post (default)", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-6',
							"std" => "0",
							"value" => array(esc_html__("Popup", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							"param_name" => "cat",
							"heading" => esc_html__("Group", 'trx_addons'),
							"description" => wp_kses_data( __("Dishes group", 'trx_addons') ),
							"value" => array_merge(array(esc_html__('- Select category -', 'trx_addons') => 0), array_flip(trx_addons_get_list_terms(false, TRX_ADDONS_CPT_DISHES_TAXONOMY))),
							"std" => "0",
							"type" => "dropdown"
						)
					),
					trx_addons_vc_add_query_param(''),
					trx_addons_vc_add_slider_param(),
					trx_addons_vc_add_title_param(),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_dishes' );
	}
}




// Elementor Widget
//------------------------------------------------------
if (!function_exists('trx_addons_sc_dishes_add_in_elementor')) {
	add_action( 'elementor/widgets/widgets_registered', 'trx_addons_sc_dishes_add_in_elementor' );
	function trx_addons_sc_dishes_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Widget')) return;	

		class TRX_Addons_Elementor_Widget_Dishes extends TRX_Addons_Elementor_Widget {

			/**
			 * Retrieve widget name.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget name.
			 */
			public function get_name() {
				return 'trx_sc_dishes';
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
				return __( 'Dishes', 'trx_addons' );
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
				return 'eicon-nerd-chuckle';
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
					'section_sc_dishes',
					[
						'label' => __( 'Dishes', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('cpt', 'dishes', 'sc'), 'trx_sc_dishes'),
						'default' => 'default'
					]
				);

				$this->add_control(
					'featured_position',
					[
						'label' => __( 'Featured position', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => trx_addons_get_list_sc_dishes_positions(),
						'default' => 'top'
					]
				);

				$this->add_control(
					'hide_excerpt',
					[
						'label' => __( 'Excerpt', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Show the past dishes if checked, else - show upcoming dishes", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Show', 'trx_addons' ),
						'label_on' => __( 'Hide', 'trx_addons' ),
						'return_value' => '1',
						'condition' => [
							'type' => ['default', 'float']
						]
					]
				);

				$this->add_control(
					'popup',
					[
						'label' => __( 'Open in the popup', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Open details in the popup or navigate to the single post (default)", 'trx_addons') ),
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
						'default' => esc_html__('Read more', 'trx_addons'),
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
						'options' => trx_addons_array_merge(array(0 => esc_html__('- Select category -', 'trx_addons')), trx_addons_get_list_terms(false, TRX_ADDONS_CPT_DISHES_TAXONOMY)),
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
		\Elementor\Plugin::$instance->widgets_manager->register_widget_type( new TRX_Addons_Elementor_Widget_Dishes() );
	}
}


// Disable our widgets (shortcodes) to use in Elementor
// because we create special Elementor's widgets instead
if (!function_exists('trx_addons_sc_dishes_black_list')) {
	add_action( 'elementor/widgets/black_list', 'trx_addons_sc_dishes_black_list' );
	function trx_addons_sc_dishes_black_list($list) {
		$list[] = 'TRX_Addons_SOW_Widget_Dishes';
		return $list;
	}
}




// SOW Widget
//------------------------------------------------------
if (class_exists('TRX_Addons_SOW_Widget')) {
	class TRX_Addons_SOW_Widget_Dishes extends TRX_Addons_SOW_Widget {
		
		function __construct() {
			parent::__construct(
				'trx_addons_sow_widget_dishes',
				esc_html__('ThemeREX Dishes', 'trx_addons'),
				array(
					'classname' => 'widget_dishes',
					'description' => __('Display dishes', 'trx_addons')
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
						'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('cpt', 'dishes', 'sc'), $this->get_sc_name(), 'sow' ),
						'type' => 'select'
					),
					'featured_position' => array(
						'label' => __('Featured position', 'trx_addons'),
						"description" => wp_kses_data( __("Select the position of the featured element", 'trx_addons') ),
						'default' => 'top',
						'options' => trx_addons_get_list_sc_dishes_positions(),
						'type' => 'select'
					),
					"hide_excerpt" => array(
						"label" => esc_html__("Hide excerpt", 'trx_addons'),
						"description" => wp_kses_data( __("Check if you want hide excerpt", 'trx_addons') ),
						"default" => false,
						"type" => "checkbox"
					),
					"popup" => array(
						"label" => esc_html__("Open in the popup", 'trx_addons'),
						"description" => wp_kses_data( __("Open details in the popup or navigate to the single post (default)", 'trx_addons') ),
						"default" => false,
						"type" => "checkbox"
					),
					"more_text" => array(
						"label" => esc_html__("'More' text", 'trx_addons'),
						"description" => wp_kses_data( __("Specify caption of the 'Read more' button. If empty - hide button", 'trx_addons') ),
						"default" => esc_html__('Read more', 'trx_addons'),
						"type" => "text",
					),
					"pagination" => array(
						"label" => esc_html__("Pagination", 'trx_addons'),
						"description" => wp_kses_data( __("Add pagination links after posts. Attention! If using slider - pagination not allowed!", 'trx_addons') ),
						"default" => 'none',
						"options" => trx_addons_get_list_sc_paginations(),
						"type" => "select"
					),
					"cat" => array(
						"label" => esc_html__("Group", 'trx_addons'),
						"description" => wp_kses_data( __("Select dishes group", 'trx_addons') ),
						"default" => 0,
						"options" => trx_addons_array_merge(array(0 => esc_html__('- Select category -', 'trx_addons')),
															trx_addons_get_list_terms(false, TRX_ADDONS_CPT_DISHES_TAXONOMY)
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
	siteorigin_widget_register('trx_addons_sow_widget_dishes', __FILE__, 'TRX_Addons_SOW_Widget_Dishes');
}
?>