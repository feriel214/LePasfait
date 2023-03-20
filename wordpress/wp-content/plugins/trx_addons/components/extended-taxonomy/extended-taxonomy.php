<?php
/**
 * Add Extended Taxonomy functionality
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.49
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Define component's subfolder
if ( !defined('TRX_ADDONS_EXTENDED_TAXONOMY') ) define('TRX_ADDONS_EXTENDED_TAXONOMY', TRX_ADDONS_PLUGIN_COMPONENTS . 'extended-taxonomy/');


// Add component to the global list
if (!function_exists('trx_addons_extended_taxonomy_add_to_components')) {
	add_filter( 'trx_addons_components_list', 'trx_addons_extended_taxonomy_add_to_components' );
	function trx_addons_extended_taxonomy_add_to_components($list=array()) {
		$list['extended-taxonomy'] = array(
					'title' => __('Extended taxonomy', 'trx_addons')
					);
		return $list;
	}
}

// Check if component is enabled
if (!function_exists('trx_addons_extended_taxonomy_enable')) {
	function trx_addons_extended_taxonomy_enable() {
		static $enable = null;
		if ($enable === null) {
			$enable = trx_addons_components_is_allowed('components', 'extended-taxonomy')
				&& apply_filters('trx_addons_filter_extended_taxonomy', true);
		}
		return $enable;
	}
}

// Merge component's specific styles to the single stylesheet
if ( !function_exists( 'trx_addons_extended_taxonomy_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_extended_taxonomy_merge_styles');
	function trx_addons_extended_taxonomy_merge_styles($list) {
		if (trx_addons_extended_taxonomy_enable()) {
			$list[] = TRX_ADDONS_EXTENDED_TAXONOMY . 'css/_extended-taxonomy.scss';
		}
		return $list;
	}
}

// Add admin scripts and styles
if ( !function_exists( 'trx_addons_extended_taxonomy_admin_scripts' ) ) {
	add_action('admin_enqueue_scripts', 'trx_addons_extended_taxonomy_admin_scripts');
	function trx_addons_extended_taxonomy_admin_scripts() {
		if (!trx_addons_extended_taxonomy_enable()) return;
		wp_enqueue_style('trx_addons-extended_taxonomy', trx_addons_get_file_url(TRX_ADDONS_EXTENDED_TAXONOMY . 'css/admin.css'), array(), null);
	}
}

// Add 'Extended Taxonomy' parameters in the ThemeREX Addons Options
if (!function_exists('trx_addons_extended_taxonomy_options')) {
	add_filter( 'trx_addons_filter_options', 'trx_addons_extended_taxonomy_options');
	function trx_addons_extended_taxonomy_options($options) {
		if (trx_addons_extended_taxonomy_enable()) {
			trx_addons_array_insert_before($options, 'theme_specific_section', array(
				// Section 'Extended Taxonomy'
				'extended_taxonomy_section' => array(
					"title" => esc_html__('Extended Taxonomy', 'trx_addons'),
					"desc" => wp_kses_data( __("Extended taxonomy settings", 'trx_addons') ),
					"type" => "section"
				),
				'extended_taxonomy_info' => array(
					"title" => esc_html__('Extended Taxonomy', 'trx_addons'),
					"desc" => wp_kses_data( __("Add some extended taxonomy attributes", 'trx_addons') ),
					"type" => "info"
				),
				"extended_taxonomy_attributes" => array(
					"title" => esc_html__("Attributes to show", 'trx_addons'),
					"desc" => wp_kses_data( __("Select taxonomy attributes to show", 'trx_addons') ),
					"dir" => 'horizontal',
					"std" => array('color' => 1, 'image' => 1, 'icon' => 1),
					"options" => apply_filters('trx_addons_filter_extended_taxonomy_attributes', array(
						'color' => esc_html__("Text color", 'trx_addons'),
						'color_hover' => esc_html__("Text hover", 'trx_addons'),
						'color_bg' => esc_html__("Background color", 'trx_addons'),
						'color_bg_hover' => esc_html__("Background hover", 'trx_addons'),
						'icon_font' => esc_html__("Icon", 'trx_addons'),
						'image' => esc_html__("Image", 'trx_addons'),
						'icon' => esc_html__("Small Image", 'trx_addons'))
					),
					"type" => "checklist"
				),
				"extended_taxonomy_tax" => array(
					"title" => esc_html__("Taxonomy list", 'trx_addons'),
					"desc" => wp_kses_data( __("Select taxonomy to add extended taxonomy in it", 'trx_addons') ),
					"dir" => 'horizontal',
					"std" => array( 'category' => 1, 'post_tag' => 1 ),
					"options" => array(),
					"type" => "checklist"
				),
			) );
		}
		return $options;
	}
}

// Fill 'Taxonomy list' before show ThemeREX Addons Options
if (!function_exists('trx_addons_extended_taxonomy_before_show_options')) {
	add_filter( 'trx_addons_filter_before_show_options', 'trx_addons_extended_taxonomy_before_show_options', 10, 2);
	function trx_addons_extended_taxonomy_before_show_options($options, $pt='') {
		if (trx_addons_extended_taxonomy_enable() && isset($options['extended_taxonomy_tax'])) {
			$options['extended_taxonomy_tax']['options'] = trx_addons_get_list_extended_taxonomy_list();
		}
		return $options;
	}
}

// Allow title image override
if (!function_exists('trx_addons_extended_taxonomy_featured_image_override')) {
	add_filter( 'trx_addons_filter_featured_image_override', 'trx_addons_extended_taxonomy_featured_image_override');
	function trx_addons_extended_taxonomy_featured_image_override($override) {
		if (trx_addons_extended_taxonomy_enable()) {
			$taxonomy = empty($taxonomy) ? get_query_var( 'taxonomy' ) : '';
			$taxonomy = empty($taxonomy) ? 'category' : $taxonomy;
			if ($allowed = in_array($taxonomy, trx_addons_extended_taxonomy_attr_get_selected('tax'))) {
				$override = !get_header_image();
			}
		}
		return $override;
	}
}

// Return list of allowed custom post's taxonomies
if ( !function_exists( 'trx_addons_get_list_extended_taxonomy_list' ) ) {
	function trx_addons_get_list_extended_taxonomy_list($prepend_inherit=false) {
		static $list = false;
		if ($list === false) {
			$list = array();
			$post_types = get_post_types(array(
				'public' => true,
				'exclude_from_search' => false
			), 'object');
			if (is_array($post_types)) {
				foreach ($post_types as $pt) {
					$terms = get_object_taxonomies( $pt->name, array(
						'hide_empty' => false,
					) );
					foreach ($terms as $t) {
						$list[$t->name] = sprintf('%s (%s)', $t->label, $pt->label);
					}
				}
			}
		}
		return $prepend_inherit
			? trx_addons_array_merge(array('inherit' => esc_html__("Inherit", 'trx_addons')), $list)
			: $list;
	}
}

// Return list of allowed custom post's taxonomies
if ( !function_exists( 'trx_addons_get_list_extended_taxonomy_pt_list' ) ) {
	function trx_addons_get_list_extended_taxonomy_pt_list($prepend_inherit=false) {
		$list = array();
		if (trx_addons_extended_taxonomy_enable()) {
			global $wp_taxonomies;
			$attrs = trx_addons_extended_taxonomy_attr_get_selected('tax');
			foreach ($attrs as $tax) {
				if (($pt = ( isset( $wp_taxonomies[$tax] ) ) ? $wp_taxonomies[$tax]->object_type : array())) {
					$list[] = $pt[0];
				}
			}
			$list = array_unique($list);
		}
		return $prepend_inherit
			? trx_addons_array_merge(array('inherit' => esc_html__("Inherit", 'trx_addons')), $list)
			: $list;
	}
}


// Return terms meta value. If its meta is empty - try to get parents value
if (!function_exists('trx_addons_extended_taxonomy_get_name')) {
	add_filter('trx_addons_filter_term_name', 'trx_addons_extended_taxonomy_get_name', 10, 2);
	add_filter('trx_addons_extended_taxonomy_name', 'trx_addons_extended_taxonomy_get_name', 10, 2);
	function trx_addons_extended_taxonomy_get_name($term_name='', $term_obj=false) {
		if ( ! trx_addons_extended_taxonomy_enable()
			|| empty($term_obj)
			|| apply_filters( 'trx_addons_filter_disallow_term_name_modify',
							is_admin() && ! in_array( trx_addons_get_value_gp( 'action' ), array( 'trx_addons_item_pagination', 'elementor', 'elementor_ajax', 'wp_ajax_elementor_ajax' ) ),
							$term_name,
                			$term_obj)
		) {
			return $term_name;
		}
		$css = '';
		$css_body = '';
		$classes = [];
		$icon = '';
		$meta_name_prefix = "extended_taxonomy_";
		$term_name = $term_name ? $term_name : $term_obj->name;
		$attrs = trx_addons_extended_taxonomy_attr_get_selected();
		$uniqid = 'extended_taxonomy_custom_'.mt_rand();	//sanitize_html_class(uniqid('extended_taxonomy_custom_'));
		if (in_array('color', $attrs)) {
			$val = trx_addons_extended_taxonomy_get_meta($term_obj->term_id, $term_obj->taxonomy, $meta_name_prefix . 'color');
			$css_body .= empty($val) ? '' : 'color: ' . $val . ';';
		}
		if (in_array('color_bg', $attrs)) {
			$val = trx_addons_extended_taxonomy_get_meta($term_obj->term_id, $term_obj->taxonomy, $meta_name_prefix . 'color_bg');
			$css_body .= empty($val) ? '' : 'background-color: ' . $val . ';';
			$classes[] = 'trx_addons_extended_taxonomy_bg';
		}
		if (!empty($css_body)) {
			$css .= ".{$uniqid} {" . $css_body . "}";
			$css .= ".{$uniqid} .trx_addons_extended_taxonomy_icon {" . $css_body . "}";
			$css_body = '';
		}
		if (in_array('color_hover', $attrs)) {
			$val = trx_addons_extended_taxonomy_get_meta($term_obj->term_id, $term_obj->taxonomy, $meta_name_prefix . 'color_hover');
			$css_body .= empty($val) ? '' : 'color: ' . $val . ';';
		}
		if (in_array('color_bg_hover', $attrs)) {
			$val = trx_addons_extended_taxonomy_get_meta($term_obj->term_id, $term_obj->taxonomy, $meta_name_prefix . 'color_bg_hover');
			$css_body .= empty($val) ? '' : 'background-color: ' . $val . ';';
		}
		if (!empty($css_body)) {
			$css .= ".{$uniqid}:hover {" . $css_body . "}";
			$css .= ".{$uniqid}:hover .trx_addons_extended_taxonomy_icon {" . $css_body . "}";
		}
		if (!empty($css)) {		
			$css = apply_filters('trx_addons_extended_taxonomy_inline_css', $css, $term_obj);
			trx_addons_add_inline_css($css);
		}
		if (in_array('icon_font', $attrs)) {
			$icon_font = trx_addons_extended_taxonomy_get_meta($term_obj->term_id, $term_obj->taxonomy, $meta_name_prefix . 'icon_font');
			$icon = empty($icon_font) || trx_addons_is_off($icon_font) ? '' :  '<span class="trx_addons_extended_taxonomy_icon ' . $icon_font . '"></span>';
		}
		if (empty($icon) && in_array('icon', $attrs)) {
			$icon = trx_addons_get_taxonomy_icon($term_obj->term_id, $term_obj->taxonomy);
			if (!empty($icon)) {
				$trx_addons_attr = trx_addons_getimagesize($icon);
				$icon = '<img class="trx_addons_extended_taxonomy_img" src="' . esc_url($icon) . '" alt="' . esc_attr($term_name) . '" '
							. ((!empty($trx_addons_attr[3])) ? $trx_addons_attr[3] : '') 
						. '>';
			}
		}

		$term_name = (!empty($css) || !empty($icon)
						? '<span class="trx_addons_extended_taxonomy ' . esc_attr($uniqid) . (count($classes) > 0 ? ' '.join(' ', $classes) : '') . '">' 
						: '')
						. $icon
						. $term_name
					. (!empty($css) || !empty($icon)
						? '</span>' 
						: '');

		return $term_name;
	}
}

// Return attribute's title
if ( !function_exists( 'trx_addons_extended_taxonomy_get_title' ) ) {
	function trx_addons_extended_taxonomy_get_title($attr) {
		$title = '';
		switch ($attr) {
			case 'icon': $title = esc_html__("Small Image", 'trx_addons'); break;
			case 'color': $title = esc_html__("Color", 'trx_addons'); break;
			case 'color_hover': $title = esc_html__("Hover", 'trx_addons'); break;
			case 'color_bg': $title = esc_html__("Background color", 'trx_addons'); break;
			case 'color_bg_hover': $title = esc_html__("Background hover", 'trx_addons'); break;
			case 'icon_font': $title = esc_html__("Icon", 'trx_addons'); break;
			case 'image': $title = esc_html__("Image", 'trx_addons'); break;
		}
		return $title;
	}
}


//-------------------------------------------------------
//--  Extended Taxonomy code
//-------------------------------------------------------

// Returns list of a selected taxonomy attributes
if ( !function_exists( 'trx_addons_extended_taxonomy_attr_get_selected' ) ) {
	function trx_addons_extended_taxonomy_attr_get_selected($attr_name='attributes') {
		$attrs = trx_addons_get_option("extended_taxonomy_{$attr_name}", array(), false);
		if (is_array($attrs)) {
			foreach ($attrs as $pt => $val) {
				if (empty($val)) {
					unset($attrs[$pt]);
				}
			}
		}
		return array_keys((array)$attrs);
	}
}

// Add actions with specific post types
if ( !function_exists( 'trx_addons_extended_taxonomy_add_actions' ) ) {
	add_action( 'after_setup_theme', 'trx_addons_extended_taxonomy_add_actions', 10);
	function trx_addons_extended_taxonomy_add_actions() {
		if ( trx_addons_extended_taxonomy_enable() ) {
			$tax_list = trx_addons_extended_taxonomy_attr_get_selected('tax');
			if (is_array($tax_list)) {
				foreach ($tax_list as $tax_name) {
					add_action("{$tax_name}_add_form_fields", 'trx_addons_extended_taxonomy_show_fields', 10, 1);
					add_action("{$tax_name}_edit_form_fields", 'trx_addons_extended_taxonomy_show_fields', 10, 1);
					add_action("create_{$tax_name}", 'trx_addons_extended_taxonomy_save_meta');
					add_action("edited_{$tax_name}", 'trx_addons_extended_taxonomy_save_meta');

					// FrontEnd output generation
					add_filter( "the_{$tax_name}_list", 'trx_addons_extended_taxonomy_filter_change_list', 10, 2 );
					add_filter( "term_links-{$tax_name}", 'trx_addons_extended_taxonomy_filter_change_links', 10, 1 );
				}
			}
		}
	}
}

// Return terms meta value. If its meta is empty - try to get parents value
if (!function_exists('trx_addons_extended_taxonomy_get_meta')) {
	function trx_addons_extended_taxonomy_get_meta($term_id, $taxonomy, $key) {
		$term_id = trx_addons_extended_taxonomy_sanitize_term_id($term_id, $taxonomy);
		$value = get_term_meta($term_id, $key, 1);
		if (empty($value)) {
			//Get ancestors
			$ancestors = get_ancestors($term_id, $taxonomy);
			foreach ($ancestors as $ancestor) {
				if (!empty($anc_val = get_term_meta($ancestor, $key, 1))) {
					$value = $anc_val;
					break;
				}
			}
		}
		return $value;
	}
}

// Return current term id
if (!function_exists('trx_addons_extended_taxonomy_sanitize_term_id')) {
	function trx_addons_extended_taxonomy_sanitize_term_id($term_id=0, $taxonomy='') {
		if ($term_id == 0) {
			if ($taxonomy=='category') {
				if (is_category())
					$term_id = (int) get_query_var('cat');
			} else if (!empty($taxonomy)) {
				if (is_tax($taxonomy)) {
					$term = get_term_by('slug', get_query_var($taxonomy), $taxonomy, OBJECT);
					if (!empty($term->term_id))
						$term_id = $term->term_id;
				}
			} else if (is_tax()) {
				$term = get_queried_object();
				if (!empty($term->term_id))
					$term_id = $term->term_id;
			}
		}
		return $term_id;
	}
}

// Modify taxonomy name, one by one by
if (!function_exists('trx_addons_extended_taxonomy_filter_change_list')) {
	//Handler of the add_filter( "the_{$tax_name}_list", 'trx_addons_extended_taxonomy_filter_change_list', 10, 2 );
	function trx_addons_extended_taxonomy_filter_change_list($terms, $post_id=0) {
		if (!is_array($terms)) $terms = array($terms);
		foreach ($terms as $k=>$term) {
			$terms[$k]->name = apply_filters('trx_addons_extended_taxonomy_name', $term->name, $term);
		}
		return $terms;
	}
}


// Modify taxonomy links, one by one by
if (!function_exists('trx_addons_extended_taxonomy_filter_change_links')) {
	//Handler of the add_filter( "term_links-{$tax_name}", 'trx_addons_extended_taxonomy_filter_change_links', 10, 1 );
	function trx_addons_extended_taxonomy_filter_change_links ($links) {
		$links = is_array($links) ? $links : array($links);
		$term_slug = str_replace('term_links-', '', current_filter());
		foreach ($links as &$link) {
			preg_match_all('/>(.*?)<\/a>/', $link, $matches);
			$old_name = isset($matches[1][0]) ? $matches[1][0] : false;
			if ($old_name) {
				$term = get_term_by('name', $old_name, $term_slug);
				$term->name = apply_filters('trx_addons_extended_taxonomy_name', $term->name, $term);
				$link = str_replace($old_name . '</a>', $term->name . '</a>', $link);
			}
		}
		return $links;
	}
}

if (!function_exists('trx_addons_extended_taxonomy_show_fields')) {
	//Handler of the add_action("{$tax_name}_add_form_fields", 'trx_addons_extended_taxonomy_show_fields', 10, 1);
	//Handler of the add_action("{$tax_name}_edit_form_fields", 'trx_addons_extended_taxonomy_show_fields', 10, 1);
	function trx_addons_extended_taxonomy_show_fields ($term = false) {
		$attrs = trx_addons_extended_taxonomy_attr_get_selected();
		if (!is_array($attrs) || count($attrs) < 1) { return; }
		$term_id = !empty($term->term_id) ? $term->term_id : 0;
		$term_tax = !empty($term->taxonomy) ? $term->taxonomy : 0;
		trx_addons_enqueue_wp_color_picker();

		// Show Icon and Image fields
		if (in_array('image', $attrs)) {
			// Category's image
			$attrs = array_diff($attrs, array('image'));
			echo ((int)$term_id > 0 ? '<tr' : '<div') . ' class="form-field">'
				. ((int)$term_id > 0 ? '<th valign="top" scope="row">' : '<div>');
			?><label
					for="trx_addons_taxonomy_image"><?php esc_html_e('Large image URL:', 'trx_addons'); ?></label><?php
			echo ((int)$term_id > 0 ? '</th>' : '</div>')
				. ((int)$term_id > 0 ? '<td valign="top">' : '<div>');
			$cat_img = $term_id > 0 ? trx_addons_get_taxonomy_image($term_id, $term_tax) : '';
			?><input id="trx_addons_taxonomy_image" class="trx_addons_image_selector_field"
					 name="trx_addons_taxonomy_image" value="<?php echo esc_url($cat_img); ?>"><?php
			if (empty($cat_img)) $cat_img = trx_addons_get_no_image();
			trx_addons_show_layout(trx_addons_options_show_custom_field('trx_addons_category_image_button', array('type' => 'mediamanager', 'linked_field_id' => 'trx_addons_taxonomy_image'), $cat_img));
			echo (int)$term_id > 0 ? '</td></tr>' : '</div></div>';
		}
		if (in_array('icon', $attrs)) {
			// Category's icon
			$attrs = array_diff($attrs, array('icon'));
			echo ((int)$term_id > 0 ? '<tr' : '<div') . ' class="form-field">'
				. ((int)$term_id > 0 ? '<th valign="top" scope="row">' : '<div>');
			?><label
					for="trx_addons_taxonomy_icon"><?php esc_html_e('Small image (icon) URL:', 'trx_addons'); ?></label><?php
			echo ((int)$term_id > 0 ? '</th>' : '</div>')
				. ((int)$term_id > 0 ? '<td valign="top">' : '<div>');
			$cat_img = $term_id > 0 ? trx_addons_get_taxonomy_icon($term_id, $term_tax) : '';
			?><input id="trx_addons_taxonomy_icon" class="trx_addons_thumb_selector_field"
					 name="trx_addons_taxonomy_icon" value="<?php echo esc_url($cat_img); ?>"><?php
			if (empty($cat_img)) $cat_img = trx_addons_get_no_image();
			trx_addons_show_layout(trx_addons_options_show_custom_field('trx_addons_category_icon_button', array('type' => 'mediamanager', 'linked_field_id' => 'trx_addons_taxonomy_icon'), $cat_img));
			echo (int)$term_id > 0 ? '</td></tr>' : '</div></div>';
		}

		foreach ($attrs as $attr_name) {
			$meta_name = "extended_taxonomy_{$attr_name}";
			$args = array(
				'val' => is_object($term) ? get_term_meta( $term_id, $meta_name, 1 ) : '',
				'desc' => '',
				'title' => trx_addons_extended_taxonomy_get_title($attr_name),
				'type' => explode('_', $attr_name)[0]
			);
			if ($attr_name === 'icon_font') {
				$style = trx_addons_get_setting('icons_type');
				$args['style'] = $style;
				$args['type'] = 'icons';
				$args['options'] = trx_addons_get_list_icons($style);
			}

			echo ((int) $term_id > 0 ? '<tr' : '<div') . ' class="form-field trx-addons-extended-taxonomy">'
				. ((int) $term_id > 0 ? '<td valign="top" colspan="2">' : '');
			echo trx_addons_options_show_field($meta_name, $args);
			echo (int) $term_id > 0 ? '</td></tr>' : '</div>';
		}
	}
}

// Save the fields to the taxonomy, using our callback function
if (!function_exists('trx_addons_extended_taxonomy_save_meta')) {
	function trx_addons_extended_taxonomy_save_meta($term_id) {
		$attrs = trx_addons_extended_taxonomy_attr_get_selected();
		if (!is_array($attrs) || count($attrs) < 1) { return; }

		if (isset($_POST['trx_addons_taxonomy_image'])) {
			trx_addons_set_term_meta(array(
				'term_id' => $term_id,
				'key' => 'image'
			),
				$_POST['trx_addons_taxonomy_image']
			);
		}
		if (isset($_POST['trx_addons_taxonomy_icon'])) {
			trx_addons_set_term_meta(array(
				'term_id' => $term_id,
				'key' => 'icon'
			),
				$_POST['trx_addons_taxonomy_icon']
			);
		}

		foreach ($attrs as $attr_name) {

			$meta_name = "extended_taxonomy_{$attr_name}";

			if (isset($_POST["trx_addons_options_field_$meta_name"])) {
				trx_addons_set_term_meta(array(
						'term_id' => $term_id,
						'key' => $meta_name
					),
					$_POST["trx_addons_options_field_$meta_name"]
				);
			}
		}
	}
}


// Add images and icons to categories
//--------------------------------------------------------------------------

// Return image from the taxonomy (TODO remove trx_addons_get_category_image() and replace it with trx_addons_get_taxonomy_image)
if (!function_exists('trx_addons_get_taxonomy_image')) {
	function trx_addons_get_taxonomy_image($term_id=0, $taxonomy='') {
		// Old way
		// $image = trx_addons_get_term_meta(array('term_id' => $term_id, 'key' => 'image', 'taxonomy' => $taxonomy));
		$taxonomy = empty($taxonomy) ? get_query_var( 'taxonomy' ) : $taxonomy;
		$taxonomy = empty($taxonomy) ? 'category' : $taxonomy;
		$image =  trx_addons_extended_taxonomy_get_meta($term_id, $taxonomy,'image');
		if (empty($image)) {
			//$image_id = get_term_meta($term_id, 'thumbnail_id', true);
			$image_id = trx_addons_get_term_meta(array('taxonomy' => '', 'term_id' => $term_id, 'key' => 'thumbnail_id'));
			if (!empty($image_id)) {
				$image_src = wp_get_attachment_image_src($image_id);
				if (!empty($image_src[0]))
					$image = $image_src[0];
			}
		}
		return trx_addons_remove_protocol($image);
	}
}

// Return small image (icon) from the category (TODO remove trx_addons_get_category_icon() and replace it with trx_addons_get_taxonomy_icon)
if (!function_exists('trx_addons_get_taxonomy_icon')) {
	function trx_addons_get_taxonomy_icon($term_id=0, $taxonomy='', $icon_only=true) {
		$taxonomy = empty($taxonomy) ? get_query_var( 'taxonomy' ) : $taxonomy;
		$taxonomy = empty($taxonomy) ? 'category' : $taxonomy;
		$image =  trx_addons_extended_taxonomy_get_meta($term_id, $taxonomy,'icon');
		if (empty($image) && !$icon_only) {
			$image_id = trx_addons_get_term_meta(array('taxonomy' => '', 'term_id' => $term_id, 'key' => 'thumbnail_id'));
			if (!empty($image_id)) {
				$image_src = wp_get_attachment_image_src($image_id);
				if (!empty($image_src[0]))
					$image = $image_src[0];
			}
		}
		return trx_addons_remove_protocol($image);
	}
}
