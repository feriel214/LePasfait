<?php
/**
 * Plugin support: Elementor
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	die( '-1' );
}


// Check if plugin 'Elementor' is installed and activated
// Attention! This function is used in many files and was moved to the api.php
/*
if ( !function_exists( 'trx_addons_exists_elementor' ) ) {
	function trx_addons_exists_elementor() {
		return class_exists('Elementor\Plugin');
	}
}
*/

// Return true if Elementor exists and current mode is preview
if ( !function_exists( 'trx_addons_elm_is_preview' ) ) {
	function trx_addons_elm_is_preview() {
		return trx_addons_exists_elementor() 
				&& (\Elementor\Plugin::$instance->preview->is_preview_mode()
					|| (trx_addons_get_value_gp('post') > 0
						&& trx_addons_get_value_gp('action') == 'elementor'
						)
					);
	}
}

	
// Merge specific styles into single stylesheet
if ( !function_exists( 'trx_addons_elm_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_elm_merge_styles');
	function trx_addons_elm_merge_styles($list) {
		if (trx_addons_exists_elementor()) {
			$list[] = TRX_ADDONS_PLUGIN_API . 'elementor/_elementor.scss';
		}
		return $list;
	}
}


// Merge shortcode's specific styles to the single stylesheet (responsive)
if ( !function_exists( 'trx_addons_elm_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_elm_merge_styles_responsive');
	function trx_addons_elm_merge_styles_responsive($list) {
		if (trx_addons_exists_elementor()) {
			$list[] = TRX_ADDONS_PLUGIN_API . 'elementor/_elementor.responsive.scss';
		}
		return $list;
	}
}

	
// Merge plugin's specific scripts into single file
if ( !function_exists( 'trx_addons_elm_merge_scripts' ) ) {
	add_action("trx_addons_filter_merge_scripts", 'trx_addons_elm_merge_scripts');
	function trx_addons_elm_merge_scripts($list) {
		if (trx_addons_exists_elementor()) {
			$list[] = TRX_ADDONS_PLUGIN_API . 'elementor/elementor.js';
		}
		return $list;
	}
}


// Add responsive sizes
if ( !function_exists( 'trx_addons_elm_sass_responsive' ) ) {
	add_filter("trx_addons_filter_sass_responsive", 'trx_addons_elm_sass_responsive', 11);
	function trx_addons_elm_sass_responsive($list) {
		if (!isset($list['md_lg']))
			$list['md_lg'] = array(
									'min' => $list['sm']['max']+1,
									'max' => $list['lg']['max']
									);
		return $list;
	}
}

// Load required styles and scripts for Elementor Editor mode
if ( !function_exists( 'trx_addons_elm_editor_load_scripts' ) ) {
	add_action("elementor/editor/before_enqueue_scripts", 'trx_addons_elm_editor_load_scripts');
	function trx_addons_elm_editor_load_scripts() {
		trx_addons_load_scripts_admin(true);
		trx_addons_localize_scripts_admin();
		wp_enqueue_style(  'trx_addons-elementor-editor', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_API . 'elementor/elementor.editor.css'), array(), null );
		wp_enqueue_script( 'trx_addons-elementor-editor', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_API . 'elementor/elementor.editor.js'), array('jquery'), null, true );
		do_action('trx_addons_action_pagebuilder_admin_scripts');
	}
}

// Add vars to the Elementors editor
if ( !function_exists( 'trx_addons_elm_localize_admin_scripts' ) ) {
	add_filter( 'trx_addons_filter_localize_script_admin',	'trx_addons_elm_localize_admin_scripts');
	function trx_addons_elm_localize_admin_scripts($vars = array()) {
		$vars['add_hide_on_xxx'] = trx_addons_get_setting('add_hide_on_xxx');
		return $vars;
	}
}

// Load required scripts for Elementor Preview mode
if ( !function_exists( 'trx_addons_elm_preview_load_scripts' ) ) {
	add_action("elementor/frontend/after_enqueue_scripts", 'trx_addons_elm_preview_load_scripts');
	function trx_addons_elm_preview_load_scripts() {
		if (trx_addons_is_on(trx_addons_get_option('debug_mode'))) {
			wp_enqueue_script( 'trx_addons-elementor-preview', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_API . 'elementor/elementor.js'), array('jquery'), null, true );
		}
		do_action('trx_addons_action_pagebuilder_preview_scripts');
	}
}
	
// Add shortcode's specific vars into JS storage
if ( !function_exists( 'trx_addons_elm_localize_script' ) ) {
	add_filter("trx_addons_filter_localize_script", 'trx_addons_elm_localize_script');
	function trx_addons_elm_localize_script($vars) {
		$vars['elementor_stretched_section_container'] = get_option('elementor_stretched_section_container');
		return $vars;
	}
}

// Return url with post edit link
if ( !function_exists( 'trx_addons_elm_post_edit_link' ) ) {
	add_filter( 'trx_addons_filter_post_edit_link', 'trx_addons_elm_post_edit_link', 10, 2 );
	function trx_addons_elm_post_edit_link( $link, $post_id ) {
		if ( trx_addons_exists_elementor() && \Elementor\Plugin::$instance->db->is_built_with_elementor( $post_id ) ) {
			$link = str_replace( 'action=edit', 'action=elementor', $link );
		}
		return $link;
	}
}

// Change "Go Pro" links
//----------------------------------------------
if (!function_exists('trx_addons_elm_change_gopro_plugins') && defined('ELEMENTOR_PLUGIN_BASE')) {
	add_filter( 'plugin_action_links_' . ELEMENTOR_PLUGIN_BASE, 'trx_addons_elm_change_gopro_plugins', 11 );
	function trx_addons_elm_change_gopro_plugins($links) {
		if (!empty($links['go_pro']) && preg_match('/href="([^"]*)"/', $links['go_pro'], $matches) && !empty($matches[1])) {
			$links['go_pro'] = str_replace($matches[1], trx_addons_add_to_url($matches[1], array('ref' => '2496')), $links['go_pro']);
		}
		return $links;
	}
}
if (!function_exists('trx_addons_elm_change_gopro_dashboard')) {
	add_filter( 'elementor/admin/dashboard_overview_widget/footer_actions', 'trx_addons_elm_change_gopro_dashboard', 11 );
	function trx_addons_elm_change_gopro_dashboard($actions) {
		if (!empty($actions['go-pro']['link'])) {
			$actions['go-pro']['link'] = trx_addons_add_to_url($actions['go-pro']['link'], array('ref' => '2496'));
		}
		return $actions;
	}
}
if (!function_exists('trx_addons_elm_change_gopro_menu')) {
	add_filter( 'wp_redirect', 'trx_addons_elm_change_gopro_menu', 11, 2 );
	function trx_addons_elm_change_gopro_menu($link, $status=0) {
		if (strpos($link, '//elementor.com/pro/') !== false) {
			$link = trx_addons_add_to_url($link, array('ref' => '2496'));
		}
		return $link;
	}
}

// Prepare global atts for the new Elementor version: add array keys by 'name' from __globals__
// After the update Elementor 3.0+ (or later) for settings with type ::COLOR global selector appears
// Color value from this selects is not placed to the appropriate settings
if ( !function_exists( 'trx_addons_elm_prepare_global_params' ) ) {
	add_filter( 'trx_addons_filter_sc_prepare_atts', 'trx_addons_elm_prepare_global_params', 10, 2 );
	function trx_addons_elm_prepare_global_params( $args, $sc = '' ) {
		foreach ( $args as $k => $v ) {
			if ( is_array( $v ) ) {
				if ( is_string( $k ) && $k == '__globals__' ) {
					foreach ( $v as $k1 => $v1 ) {
						if ( ! empty( $v1 ) ) {
							$args[ $k1 ] = apply_filters( 'trx_addons_filter_prepare_global_param', $v1, $k1 );
						}
					}
				} else {
					$args[ $k ] = trx_addons_elm_prepare_global_params( $v, $sc );
				}
			}
		}
		return $args;
	}
}

// Return CSS-var from global color key, i.e. 'globals/colors?id=1855627f'
if ( !function_exists( 'trx_addons_elm_prepare_global_color' ) ) {
	add_filter( 'trx_addons_filter_prepare_global_param', 'trx_addons_elm_prepare_global_color', 10, 2 );
	function trx_addons_elm_prepare_global_color( $value, $key ) {
		$prefix = 'globals/colors?id=';
		if ( strpos( $value, $prefix ) === 0 ) {
			$id = str_replace( $prefix, '', $value );
			$value = "var(--e-global-color-{$id})";
		}
		return $value;
	}
}

// Init Elementor's support
//--------------------------------------------------------

// Set Elementor's options at once
if (!function_exists('trx_addons_elm_init_once')) {
	add_action( 'init', 'trx_addons_elm_init_once', 2 );
	function trx_addons_elm_init_once() {
		if (trx_addons_exists_elementor() && !get_option('trx_addons_setup_elementor_options', false)) {
			// Set components specific values to the Elementor's options
			do_action('trx_addons_action_set_elementor_options');
			// Set flag to prevent change Elementor's options again
			update_option('trx_addons_setup_elementor_options', 1);
		}
	}
}

// Add categories for widgets, shortcodes, etc.
if (!function_exists('trx_addons_elm_add_categories')) {
	add_action( 'elementor/elements/categories_registered', 'trx_addons_elm_add_categories' );
	function trx_addons_elm_add_categories($mgr = null) {

		static $added = false;

		if (!$added) {

			if ($mgr == null) $mgr = \Elementor\Plugin::$instance->elements_manager;
			
			// Add a custom category for ThemeREX Addons Shortcodes
			$mgr->add_category( 
				'trx_addons-elements',
				array(
					'title' => __( 'ThemeREX Addons Elements', 'trx_addons' ),
					'icon' => 'eicon-apps', //default icon
					'active' => true,
				)
			);

			// Add a custom category for ThemeREX Addons Widgets
			$mgr->add_category( 
				'trx_addons-widgets',
				array(
					'title' => __( 'ThemeREX Addons Widgets', 'trx_addons' ),
					'icon' => 'eicon-gallery-grid', //default icon
					'active' => false,
				)
			);

			// Add a custom category for ThemeREX Addons CPT
			$mgr->add_category( 
				'trx_addons-cpt',
				array(
					'title' => __( 'ThemeREX Addons Extensions', 'trx_addons' ),
					'icon' => 'eicon-gallery-grid', //default icon
					'active' => false,
				)
			);

			// Add a custom category for third-party shortcodes
			$mgr->add_category( 
				'trx_addons-support',
				array(
					'title' => __( 'ThemeREX Addons Support', 'trx_addons' ),
					'icon' => 'eicon-woocommerce', //default icon
					'active' => false,
				)
			);

			$added = true;
		}
	}
}

// Template to create our classes with widgets
//---------------------------------------------
if (!function_exists('trx_addons_elm_init')) {
	add_action( 'elementor/init', 'trx_addons_elm_init' );
	function trx_addons_elm_init() {

		// Add categories (for old Elementor)
		trx_addons_elm_add_categories();

		// Define class for our shortcodes and widgets
		if (class_exists('\Elementor\Widget_Base') && !class_exists('TRX_Addons_Elementor_Widget')) {
			abstract class TRX_Addons_Elementor_Widget extends \Elementor\Widget_Base {

				// List of shortcodes params,
				// that must be plain and get its value from the elementor's array
				// 'param_name' => ['array_key']
				private $plain_params = array(
					'url' => 'url',
					'link' => 'url',
					'image' => 'url',
					'bg_image' => 'url',
					'columns' => 'size',
					'count' => 'size',
					'offset' => 'size',
					'slides_space' => 'size',
					'gradient_direction' => 'size',
				);
				
				// Set shortcode-specific list of params,
				// that must bubble up to the plain value
				protected function set_plain_params($list) {
					$this->plain_params = $list;
				}
				
				// Add shortcode-specific list of params,
				// that must bubble up to the plain value
				protected function add_plain_params($list) {
					$this->plain_params = array_merge($this->plain_params, $list);
				}

				// Return string with default subtitle
				protected function get_default_subtitle() {
					return __('Subtitle', 'trx_addons');
				}

				// Return string with default description
				protected function get_default_description() {
					return __('Some description text for this item', 'trx_addons');
				}

				/**
				 * Retrieve the list of scripts the widget depended on.
				 *
				 * Used to set scripts dependencies required to run the widget.
				 *
				 * @since 1.6.41
				 *
				 * @access public
				 *
				 * @return array Widget scripts dependencies.
				 */
				public function get_script_depends() {
					return [ 'trx_addons-elementor-preview' ];
				}
				
				// Get all elements from specified post
				protected function get_post_elements($post_id = 0) {
					$meta = array();
					if ($post_id == 0 && trx_addons_get_value_gp('action')=='elementor')
						$post_id = trx_addons_get_value_gp('post');
					if ($post_id > 0) {
						$meta = get_post_meta( $post_id, '_elementor_data', true );
						if (substr($meta, 0, 1) == '[')
							$meta = json_decode( $meta, true );
					}
					return $meta;
				}
				
				// Get sc params from the current post or from the specified _elementor_data (2-nd parameter)
				protected function get_sc_params($sc='', $meta=false) {
					if ($meta === false)
						$meta = $this->get_post_elements();
					if (empty($sc))
						$sc = $this->get_name();
					$params = false;
					if (is_array($meta)) {
						foreach($meta as $v) {
							if (!empty($v['widgetType']) && $v['widgetType'] == $sc) {
								$params = $v['settings'];
								break;
							} else if (!empty($v['elements']) && count($v['elements']) > 0) {
								$params = $this->get_sc_params($sc, $v['elements']);
								if ($params !== false)
									break;
							}
						}
					}
					return $params;
				}

				// Return shortcode's name
				function get_sc_name() {
					return $this->get_name();
				}

				// Return shortcode function's name
				function get_sc_function() {
					return sprintf("trx_addons_%s", str_replace(array('trx_sc_', 'trx_widget_'), array('sc_', 'sc_widget_'), $this->get_sc_name()));
				}

				
				// ADD CONTROLS FOR COMMON PARAMETERS
				// Attention! You can use next tabs to create sections inside:
				// TAB_CONTENT | TAB_STYLE | TAB_ADVANCED | TAB_RESPONSIVE | TAB_LAYOUT | TAB_SETTINGS
				//------------------------------------------------------------

				// Create section with controls from params array
				protected function add_common_controls($group, $params, $add_params) {
					if (!empty($group['label'])) {
						$this->start_controls_section(
							'section_'.$group['section'].'_params',
							[
								'label' => $group['label'],
								'tab' => empty($group['tab']) 
											? \Elementor\Controls_Manager::TAB_CONTENT 
											: $group['tab']
							]
						);
					}
					foreach ($params as $param) {
						if (isset($add_params[$param['name']])) {
							if (empty($add_params[$param['name']]))
								continue;
							else
								$param = array_merge($param, $add_params[$param['name']]);
							unset($add_params[$param['name']]);
						}
						$this->add_control($param['name'], $param);
					}
					if (count($add_params) > 0) {
						foreach ($add_params as $k => $v) {
							if (!empty($v) && is_array($v))
								$this->add_control($k, $v);
						}
					}
					if (!empty($group['label'])) {
						$this->end_controls_section();
					}
				}
				
				// Return parameters of the control with icons selector
				protected function get_icon_param($only_socials=false, $style='') {
					if (trx_addons_get_setting('icons_selector') == 'vc') {
						$params = [
							[
								'name' => 'icon',
								'type' => \Elementor\Controls_Manager::ICON,
								'label' => __( 'Icon', 'trx_addons' ),
								'label_block' => false,
								'default' => '',
							]
						];
					} else {
						if (empty($style))
							$style = $only_socials ? trx_addons_get_setting('socials_type') : trx_addons_get_setting('icons_type');
						$params = [
							[
								'name' => 'icon',
								'type' => 'trx_icons',
								'label' => __( 'Icon', 'trx_addons' ),
								'label_block' => false,
								'default' => '',
								'options' => trx_addons_get_list_icons($style),
								'style' => $style
							]
						];
					}
					return apply_filters('trx_addons_filter_elementor_add_icon_param', $params, $only_socials, $style);
				}

				// Create control with icons selector
				protected function add_icon_param($group='', $add_params=array(), $style='') {
					$this->add_common_controls(
						[
							'label' => $group===false ? __('Icon', 'trx_addons') : $group,
							'section' => 'icon'
						],
						$this->get_icon_param(!empty($add_params['only_socials']), $style),
						$add_params
					);
				}

				// Return 'Slider' parameters
				protected function get_slider_param() {
					$params = [
						[
							"name" => "slider",
							'type' => \Elementor\Controls_Manager::SWITCHER,
							"label" => __("Slider", 'trx_addons'),
							'label_off' => __( 'Off', 'trx_addons' ),
							'label_on' => __( 'On', 'trx_addons' ),
							'return_value' => '1'
						],
						[
							"name" => "slides_space",
							'type' => \Elementor\Controls_Manager::SLIDER,
							"label" => __('Space', 'trx_addons'),
							"description" => wp_kses_data( __('Space between slides', 'trx_addons') ),
							'condition' => [
								'slider' => '1',
							],
							'default' => [
								'size' => 0
							],
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 100
								]
							]
						],
						[
							'name' => 'slider_controls',
							'type' => \Elementor\Controls_Manager::SELECT,
							'label' => __( 'Slider controls', 'trx_addons' ),
							'label_block' => false,
							'options' => trx_addons_get_list_sc_slider_controls(),
							'condition' => [
								'slider' => '1',
							],
							'default' => 'none',
						],
						[
							'name' => 'slider_pagination',
							'type' => \Elementor\Controls_Manager::SELECT,
							'label' => __( 'Slider pagination', 'trx_addons' ),
							'label_block' => false,
							'options' => trx_addons_get_list_sc_slider_paginations(),
							'condition' => [
								'slider' => '1',
							],
							'default' => 'none',
						],
						[
							"name" => "slides_centered",
							'type' => \Elementor\Controls_Manager::SWITCHER,
							"label" => __("Slides centered", 'trx_addons'),
							'label_off' => __( 'Off', 'trx_addons' ),
							'label_on' => __( 'On', 'trx_addons' ),
							'return_value' => '1',
							'condition' => [
								'slider' => '1',
							],
						],
						[
							"name" => "slides_overflow",
							'type' => \Elementor\Controls_Manager::SWITCHER,
							"label" => __("Slides overflow visible", 'trx_addons'),
							'label_off' => __( 'Off', 'trx_addons' ),
							'label_on' => __( 'On', 'trx_addons' ),
							'return_value' => '1',
							'condition' => [
								'slider' => '1',
							],
						],
						[
							"name" => "slider_mouse_wheel",
							'type' => \Elementor\Controls_Manager::SWITCHER,
							"label" => __("Mouse wheel enabled", 'trx_addons'),
							'label_off' => __( 'Off', 'trx_addons' ),
							'label_on' => __( 'On', 'trx_addons' ),
							'return_value' => '1',
							'condition' => [
								'slider' => '1',
							],
						],
						[
							"name" => "slider_autoplay",
							'type' => \Elementor\Controls_Manager::SWITCHER,
							"label" => __("Enable autoplay", 'trx_addons'),
							'label_off' => __( 'Off', 'trx_addons' ),
							'label_on' => __( 'On', 'trx_addons' ),
							'default' => '1',
							'return_value' => '1',
							'condition' => [
								'slider' => '1',
							],
						],
					];
					return apply_filters('trx_addons_filter_elementor_add_slider_param', $params);
				}
				
				// Create controls with 'Slider' params
				protected function add_slider_param($group=false, $add_params=array()) {
					$this->add_common_controls(
						[
							'label' => $group===false ? __('Slider', 'trx_addons') : $group,
							'section' => 'slider',
							'tab' => \Elementor\Controls_Manager::TAB_LAYOUT
						],
						$this->get_slider_param(),
						$add_params
					);
				}

				// Return 'Title' parameters
				protected function get_title_param($button=true) {
					$params = [
						[
							'name' => 'title_style',
							'type' => \Elementor\Controls_Manager::SELECT,
							'label' => __( 'Title style', 'trx_addons' ),
							'label_block' => false,
							'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'title'), 'trx_sc_title'),
							'default' => 'default',
						],
						[
							'name' => 'title_tag',
							'type' => \Elementor\Controls_Manager::SELECT,
							'label' => __( 'Title tag', 'trx_addons' ),
							'label_block' => false,
							'options' => trx_addons_get_list_sc_title_tags(),
							'default' => 'none',
						],
						[
							'name' => 'title_align',
							'type' => \Elementor\Controls_Manager::SELECT,
							'label' => __( 'Title alignment', 'trx_addons' ),
							'label_block' => false,
							'options' => trx_addons_get_list_sc_aligns(),
							'default' => 'none',
						],
						[
							'name' => 'title_color',
							'label' => __( 'Title color', 'trx_addons' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'default' => '',
							'description' => '',
	//						'global' => array(
	//							'active' => false,
	//						),
						],
						[
							'name' => 'title_color2',
							'label' => __( 'Title color 2', 'trx_addons' ),
							'type' => \Elementor\Controls_Manager::COLOR,
							'default' => '',
							'description' => '',
	//						'global' => array(
	//							'active' => false,
	//						),
							'condition' => [
								'title_style' => 'gradient'
							],
						],
						[
							'name' => 'gradient_direction',
							'label' => __( 'Gradient direction', 'trx_addons' ),
							'type' => \Elementor\Controls_Manager::SLIDER,
							'default' => [
								'size' => 0,
								'unit' => 'px'
							],
							'size_units' => [ 'px' ],
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 360
								]
							],
							'condition' => [
								'title_style' => 'gradient'
							],
						],
						[
							'name' => 'title',
							'type' => \Elementor\Controls_Manager::TEXT,
							'label' => __( "Title", 'trx_addons' ),
							"description" => wp_kses_data( __("Title of the block. Enclose any words in {{ and }} to make them italic or in (( and )) to make them bold. If title style is 'accent' - bolded element styled as shadow, italic - as a filled circle", 'trx_addons') ),
							'placeholder' => __( "Title", 'trx_addons' ),
							'default' => ''
						],
						[
							'name' => 'subtitle',
							'type' => \Elementor\Controls_Manager::TEXT,
							'label' => __( "Subtitle", 'trx_addons' ),
							'placeholder' => __( "Title text", 'trx_addons' ),
							'default' => ''
						],
						[
							'name' => 'description',
							'type' => \Elementor\Controls_Manager::TEXTAREA,
							'label' => __( 'Description', 'trx_addons' ),
							'label_block' => true,
							'placeholder' => __( "Short description of this block", 'trx_addons' ),
							'default' => '',
							'separator' => 'none',
							'rows' => 10,
							'show_label' => false,
						]
					];
					// Add button's params
					if ($button) {
						$params[] = [
										'name' => 'link',
										'type' => \Elementor\Controls_Manager::URL,
										'label' => __( "Button's Link", 'trx_addons' ),
										'label_block' => false,
										'placeholder' => __( 'http://your-link.com', 'trx_addons' ),
									];
						$params[] = [
										'name' => 'link_text',
										'type' => \Elementor\Controls_Manager::TEXT,
										'label' => __( "Button's text", 'trx_addons' ),
										'label_block' => false,
										'placeholder' => __( "Link's text", 'trx_addons' ),
										'default' => ''
									];
						$params[] = [
										'name' => 'link_style',
										'type' => \Elementor\Controls_Manager::SELECT,
										'label' => __( "Button's style", 'trx_addons' ),
										'label_block' => false,
										'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'button'), 'trx_sc_button'),
										'default' => 'default',
									];
						$params[] = [
										'name' => 'link_image',
										'type' => \Elementor\Controls_Manager::MEDIA,
										'label' => __( "Button's image", 'trx_addons' ),
										'default' => [
											'url' => '',
										],
									];
					}
					return apply_filters('trx_addons_filter_elementor_add_title_param', $params);
				}
				
				// Create controls with 'Title' params
				protected function add_title_param($group=false, $add_params=array()) {
					$this->add_common_controls(
						[
							'label' => $group===false ? __('Title, Description & Button', 'trx_addons') : $group,
							'section' => 'title',
							'tab' => \Elementor\Controls_Manager::TAB_LAYOUT
						],
						$this->get_title_param(!isset($add_params['button']) || $add_params['button']),
						$add_params
					);
				}

				// Return 'Query' parameters
				protected function get_query_param() {
					$params = [
						[
							'name' => 'ids',
							'type' => \Elementor\Controls_Manager::TEXT,
							'label' => __( "IDs to show", 'trx_addons' ),
							"description" => wp_kses_data( __("Comma separated IDs list to show. If not empty - parameters 'cat', 'offset' and 'count' are ignored!", 'trx_addons') ),
							'placeholder' => __( "IDs list", 'trx_addons' ),
							'default' => ''
						],
						[
							"name" => "count",
							'type' => \Elementor\Controls_Manager::SLIDER,
							"label" => __('Count', 'trx_addons'),
							'condition' => [
								'ids' => '',
							],
							'default' => [
								'size' => 3
							],
							'range' => [
								'px' => [
									'min' => 1,
									'max' => 100
								]
							]
						],
						[
							"name" => "columns",
							'type' => \Elementor\Controls_Manager::SLIDER,
							"label" => __('Columns', 'trx_addons'),
							"description" => wp_kses_data( __("Specify number of columns. If empty - auto detect by items number", 'trx_addons') ),
							'default' => [
								'size' => 0
							],
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 12
								]
							]
						],
						[
							"name" => "offset",
							'type' => \Elementor\Controls_Manager::SLIDER,
							"label" => __('Offset', 'trx_addons'),
							"description" => wp_kses_data( __("Specify number of items to skip before showed items", 'trx_addons') ),
							'condition' => [
								'ids' => '',
							],
							'default' => [
								'size' => 0
							],
							'range' => [
								'px' => [
									'min' => 0,
									'max' => 100
								]
							]
						],
						[
							'name' => 'orderby',
							'type' => \Elementor\Controls_Manager::SELECT,
							'label' => __( 'Order by', 'trx_addons' ),
							'label_block' => false,
							'options' => trx_addons_get_list_sc_query_orderby(),
							'default' => 'none',
						],
						[
							'name' => 'order',
							'type' => \Elementor\Controls_Manager::SELECT,
							'label' => __( 'Order', 'trx_addons' ),
							'label_block' => false,
							'options' => trx_addons_get_list_sc_query_orders(),
							'default' => 'asc',
						]
					];
					return apply_filters('trx_addons_filter_elementor_add_query_param', $params);
				}
				
				// Create controls with 'Query' params
				protected function add_query_param($group=false, $add_params=array()) {
					$this->add_common_controls(
						[
							'label' => $group===false ? __('Query', 'trx_addons') : $group,
							'section' => 'query'
						],
						$this->get_query_param(),
						$add_params
					);
				}

				// Return 'Hide' parameters
				static function get_hide_param($hide_on_frontpage=false) {
					$params = [
						[
							'name' => 'hide_on_wide',
							'type' => \Elementor\Controls_Manager::SWITCHER,
							'label' => __( 'Hide on wide screens', 'trx_addons' ),
							'label_off' => __( 'Off', 'trx_addons' ),
							'label_on' => __( 'On', 'trx_addons' ),
							'return_value' => '1'
						],
						[
							'name' => 'hide_on_desktop',
							'type' => \Elementor\Controls_Manager::SWITCHER,
							'label' => __( 'Hide on desktops', 'trx_addons' ),
							'label_off' => __( 'Off', 'trx_addons' ),
							'label_on' => __( 'On', 'trx_addons' ),
							'return_value' => '1'
						],
						[
							'name' => 'hide_on_notebook',
							'type' => \Elementor\Controls_Manager::SWITCHER,
							'label' => __( 'Hide on notebooks', 'trx_addons' ),
							'label_off' => __( 'Off', 'trx_addons' ),
							'label_on' => __( 'On', 'trx_addons' ),
							'return_value' => '1'
						],
						[
							'name' => 'hide_on_tablet',
							'type' => \Elementor\Controls_Manager::SWITCHER,
							'label' => __( 'Hide on tablets', 'trx_addons' ),
							'label_off' => __( 'Off', 'trx_addons' ),
							'label_on' => __( 'On', 'trx_addons' ),
							'return_value' => '1'
						],
						[
							'name' => 'hide_on_mobile',
							'type' => \Elementor\Controls_Manager::SWITCHER,
							'label' => __( 'Hide on mobile devices', 'trx_addons' ),
							'label_off' => __( 'Off', 'trx_addons' ),
							'label_on' => __( 'On', 'trx_addons' ),
							'return_value' => '1'
						]
					];
					if ($hide_on_frontpage) {
						$params[] = [
							'name' => 'hide_on_frontpage',
							'type' => \Elementor\Controls_Manager::SWITCHER,
							'label' => __( 'Hide on Frontpage', 'trx_addons' ),
							'label_off' => __( 'Off', 'trx_addons' ),
							'label_on' => __( 'On', 'trx_addons' ),
							'return_value' => '1'
						];
						$params[] = [
							'name' => 'hide_on_singular',
							'type' => \Elementor\Controls_Manager::SWITCHER,
							'label' => __( 'Hide on single posts', 'trx_addons' ),
							'label_off' => __( 'Off', 'trx_addons' ),
							'label_on' => __( 'On', 'trx_addons' ),
							'return_value' => '1'
						];
						$params[] = [
							'name' => 'hide_on_other',
							'type' => \Elementor\Controls_Manager::SWITCHER,
							'label' => __( 'Hide on other pages', 'trx_addons' ),
							'label_off' => __( 'Off', 'trx_addons' ),
							'label_on' => __( 'On', 'trx_addons' ),
							'return_value' => '1'
						];
					}
					return apply_filters('trx_addons_filter_elementor_add_hide_param', $params);
				}
				
				// Create controls with 'Hide' params
				protected function add_hide_param($group=false, $add_params=array()) {
					$this->add_common_controls(
						[
							'label' => $group===false ? __('Hide', 'trx_addons') : $group,
							'section' => 'hide',
							'tab' => \Elementor\Controls_Manager::TAB_LAYOUT
						],
						$this->get_hide_param(!empty($add_params['hide_on_frontpage'])),
						$add_params
					);
				}

				
				// RENDER SHORTCODE'S CONTENT
				//------------------------------------------------------------

				// Return widget's layout
				public function render() {
					$sc_func = $this->get_sc_function();
					if (function_exists($sc_func)) {
						trx_addons_sc_stack_push('trx_sc_layouts');		// To prevent wrap shortcodes output to the '<div class="sc_layouts_item"></div>'
						$output = call_user_func($sc_func, $this->sc_prepare_atts($this->get_settings(), $this->get_sc_name()));
						trx_addons_sc_stack_pop();
						trx_addons_show_layout($output);
					}
				}

				// Show message (placeholder) about not existing shortcode
				public function shortcode_not_exists($sc, $plugin) {
					?><div class="trx_addons_sc_not_exists">
						<h5 class="trx_addons_sc_not_exists_title"><?php echo esc_html(sprintf(__('Shortcode %s is not available!', 'trx_addons'), $sc)); ?></h5>
						<div class="trx_addons_sc_not_exists_description">
							<p><?php echo esc_html(sprintf(__('Shortcode "%1$s" from plugin "%2$s" is not available in Elementor Editor!', 'trx_addons'), $sc, $plugin)); ?></p>
							<p><?php esc_html_e('Possible causes:', 'trx_addons'); ?></p>
							<ol class="trx_addons_sc_not_exists_causes">
								<li><?php echo esc_html(sprintf(__('Plugin "%s" is not installed or not active', 'trx_addons'), $plugin)); ?></li>
								<li><?php esc_html_e('The plugin registers a shortcode later than it asks for Elementor Editor', 'trx_addons'); ?></li>
							</ol>
							<p><?php esc_html_e("So in the editor instead of the shortcode you see this message. To see the real shortcode's output - save the changes and open this page in Frontend", 'trx_addons'); ?></p>
						</div>
					</div><?php
				}

				// Prepare params for our shortcodes
				protected function sc_prepare_atts($atts, $sc='', $level=0) {
					if (is_array($atts)) {
						foreach($atts as $k=>$v) {
							// If current element is group (repeater)
							if (is_array($v) && isset($v[0]) && is_array($v[0])) {
								foreach ($v as $k1=>$v1) {
									$atts[$k][$k1] = $this->sc_prepare_atts($v1, $sc, $level+1);
								}

							// Current element is single control
							} else {
								// Make 'xxx' as plain string
								// and add 'xxx_extra' for each plain param
								if (in_array($k, array_keys($this->plain_params))) {
									$prm = explode('+', $this->plain_params[$k]);
									$atts["{$k}_extra"] = $v;
									if (isset($v[$prm[0]])) 
										$atts[$k] = $v = $v[$prm[0]] . (!empty($v[$prm[0]]) && !empty($prm[1]) && isset($v[$prm[1]]) ? $v[$prm[1]] : '');
								}
								// Sinchronize 'id' and '_element_id'
								if ($k == '_element_id') {
									if (empty($atts['id'])) {
										$atts['id'] = !empty($v) 
														? $v . '_sc' // original '_element_id' is already applied to element's wrapper
														: $this->get_sc_name() . '_' . str_replace('.', '', mt_rand());
									}
/*
								// Sinchronize 'class' and '_css_classes'
								// Not used, because 'class' is already applied to element's wrapper
								} else if ($k == '_css_classes') {
									if (empty($atts['class'])) $atts['class'] = $v;
*/
								// Add icon_type='elementor' if attr 'icon' is present and equal to the 'fa fa-xxx'
                                // After update Elementor 2.6.0 'icon' is array (was a string in the previous versions) - convert it to string again
                                  } else if ($k == 'icon') {
                                      if ( is_array( $v ) ) {
                                        $atts['icon_extra'] = $v;
                                        $atts['icon'] = $v = ! empty( $v['icon'] ) ? $v['icon'] : '';
                                      }
                                      if (trx_addons_is_elementor_icon($v)) {
                                        $atts['icon_type'] = 'elementor';
                                    }
                                 }
							}
						}
					}
					return $level == 0 ? apply_filters('trx_addons_filter_elementor_sc_prepare_atts', $atts, $sc) : $atts;
				}

				
				// DISPLAY TEMPLATE'S PARTS
				//------------------------------------------------------------
				
				// Display title, subtitle and description for some shortcodes
				public function sc_show_titles($sc, $size='') {
					trx_addons_get_template_part('templates/tpe.sc_titles.php',
											'trx_addons_args_sc_show_titles',
											array('sc' => $sc, 'size' => $size, 'element' => $this)
										);
					
				}

				// Display link button or image for some shortcodes
				public function sc_show_links($sc) {
					trx_addons_get_template_part('templates/tpe.sc_links.php',
											'trx_addons_args_sc_show_links',
											array('sc' => $sc, 'element' => $this)
										);
				}

				// Display template from the shortcode 'Button'
				public function sc_show_button($sc) {
					?><# 
					var settings_sc_button_old = settings;
					settings = {
						'title': settings.link_text,
						'link': settings.link,
						'type': settings.link_style,
						'class': 'sc_item_button sc_item_button_'+settings.link_style+' <?php echo esc_attr($sc); ?>_button',
						'align': settings.title_align ? settings.title_align : 'none'
					};
					#><?php
					trx_addons_get_template_part(TRX_ADDONS_PLUGIN_SHORTCODES . 'button/tpe.button.php',
											'trx_addons_args_sc_show_button',
											array('sc' => $sc, 'element' => $this)
										);
					?><#
					settings = settings_sc_button_old;
					#><?php
				}

				// Display begin of the slider layout for some shortcodes
				public function sc_show_slider_wrap_start($sc) {
					trx_addons_get_template_part('templates/tpe.sc_slider_start.php',
											'trx_addons_args_sc_show_slider_wrap',
											apply_filters('trx_addons_filter_sc_show_slider_args', array('sc' => $sc, 'element' => $this))
										);
				}

				// Display end of the slider layout for some shortcodes
				public function sc_show_slider_wrap_end($sc) {
					trx_addons_get_template_part('templates/tpe.sc_slider_end.php',
											'trx_addons_args_sc_show_slider_wrap',
											apply_filters('trx_addons_filter_sc_show_slider_args', array('sc' => $sc, 'element' => $this))
										);
				}
			}
		}
	}
}


// Check if icon name is from the Elementor icons
if ( !function_exists( 'trx_addons_is_elementor_icon' ) ) {
	function trx_addons_is_elementor_icon($icon) {
		return !empty($icon) && strpos($icon, 'fa ') !== false;
	}
}



// Output inline CSS
// if current action is 'wp_ajax_elementor_render_widget' or 'admin_action_elementor' (old Elementor) or 'elementor_ajax' (new Elementor)
// (called from Elementor Editor via AJAX or first load page content to the Editor)
//---------------------------------------------------------------------------------------
if (!function_exists('trx_addons_elm_print_inline_css')) {
	add_filter( 'elementor/widget/render_content', 'trx_addons_elm_print_inline_css', 10, 2 );
	function trx_addons_elm_print_inline_css($content, $widget=null) {
		if (doing_action('wp_ajax_elementor_render_widget') || doing_action('admin_action_elementor') || doing_action('elementor_ajax') || doing_action('wp_ajax_elementor_ajax')) {
			$css = trx_addons_get_inline_css(true);
			if (!empty($css)) {
				$content .= sprintf('<style type="text/css">%s</style>', $css);
			}
		}
		return $content;
	}
}



// Register custom controls for Elementor
//------------------------------------------------------------------------
if (!function_exists('trx_addons_elm_register_custom_controls')) {
	add_action( 'elementor/controls/controls_registered', 'trx_addons_elm_register_custom_controls' );
	function trx_addons_elm_register_custom_controls($elementor) {
		$controls = array('trx_icons');
		foreach ($controls as $control_id) {
			$control_filename = str_replace('_', '-', $control_id);
			require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_API . "elementor/params/{$control_filename}/{$control_filename}.php";
			$class_name = 'Trx_Addons_Elementor_Control_' . ucwords( $control_id );
			$elementor->register_control( $control_id, new $class_name() );
		}
	}
}


// Add/Modify/Remove standard Elementor's shortcodes params
//------------------------------------------------------------------------

// Add/Remove shortcodes params to the existings sections
if (!function_exists('trx_addons_elm_add_params_inside_section')) {
	add_action( 'elementor/element/before_section_end', 'trx_addons_elm_add_params_inside_section', 10, 3 );
	function trx_addons_elm_add_params_inside_section($element, $section_id, $args) {

		if (!is_object($element)) return;
		
		$el_name = $element->get_name();
		
		// Add 'Hide bg image on XXX' to the rows
		if ( ($el_name == 'section' && $section_id == 'section_background')
			|| ($el_name == 'column' && $section_id == 'section_style')
			) {

			$element->add_control( 'hide_bg_image_on_tablet', array(
									'type' => \Elementor\Controls_Manager::SWITCHER,
									'label' => __( 'Hide bg image on the tablet', 'trx_addons' ),
									'label_on' => __( 'Hide', 'trx_addons' ),
									'label_off' => __( 'Show', 'trx_addons' ),
									'return_value' => 'tablet',
									'prefix_class' => 'hide_bg_image_on_',
								) );
			$element->add_control( 'hide_bg_image_on_mobile', array(
									'type' => \Elementor\Controls_Manager::SWITCHER,
									'label' => __( 'Hide bg image on the mobile', 'trx_addons' ),
									'label_on' => __( 'Hide', 'trx_addons' ),
									'label_off' => __( 'Show', 'trx_addons' ),
									'return_value' => 'mobile',
									'prefix_class' => 'hide_bg_image_on_',
								) );
		}

		// Add 'Hide on XXX' to the any elements
		$add_hide_on_xxx = trx_addons_get_setting('add_hide_on_xxx');
		if ( ! trx_addons_is_off($add_hide_on_xxx) && class_exists( 'TRX_Addons_Elementor_Widget' ) ) {
			if ($section_id == '_section_responsive') { // && $el_name == 'section'
				$params = TRX_Addons_Elementor_Widget::get_hide_param(false);
				if (is_array($params)) {
					if ($add_hide_on_xxx == 'add') {
						$element->add_control(
							'trx_addons_responsive_heading',
							[
								'label' => __( 'Theme-specific params', 'trx_addons' ),
								'type' => \Elementor\Controls_Manager::HEADING,
								'separator' => 'before',
							]
						);
						$element->add_control(
							'trx_addons_responsive_description',
							[
								'raw' => __( "Theme-specific parameters - you can use them instead of the Elementor's parameters above.", 'trx_addons' ),
								'type' => \Elementor\Controls_Manager::RAW_HTML,
								'content_classes' => 'elementor-descriptor',
							]
						);
					}
					foreach ($params as $p) {
						$element->add_control( $p['name'], array_merge($p, array(
																				'return_value' => $p['name'],
																				'prefix_class' => 'sc_layouts_',
																				))
											);
					}
				}
			}
		}

		// Add 'Extend background' and 'Background mask' to the rows, columns and text-editor
		if ( ($el_name == 'section' && $section_id == 'section_background')
			|| ($el_name == 'column' && $section_id == 'section_style')
			|| ($el_name == 'text-editor' && $section_id == 'section_background')
			) {
			$element->add_control( 'extra_bg', array(
									'type' => \Elementor\Controls_Manager::SELECT,
									'label' => __("Extend background", 'trx_addons'),
									'options' => trx_addons_get_list_sc_content_extra_bg(''),
									'default' => '',
									'prefix_class' => 'sc_extra_bg_'
									) );
			$element->add_control( 'extra_bg_mask', array(
									'type' => \Elementor\Controls_Manager::SELECT,
									'label' => __("Background mask", 'trx_addons'),
									'options' => trx_addons_get_list_sc_content_extra_bg_mask(''),
									'default' => '',
									'prefix_class' => 'sc_bg_mask_'
									) );
		}

		// Add 'Alter height/gap' to the spacer and divider
		if ( ($el_name == 'spacer' && $section_id == 'section_spacer')
				  || ($el_name == 'divider' && $section_id == 'section_divider')) {
			$element->add_control( 'alter_height', array(
									'type' => \Elementor\Controls_Manager::SELECT,
									'label' => $el_name == 'divider' ? __("Alter gap", 'trx_addons') : __("Alter height", 'trx_addons'),
									'label_block' => true,
									'options' => trx_addons_get_list_sc_empty_space_heights(''),
									'default' => '',
									'prefix_class' => 'sc_height_'
									) );
		}
		
		// Add new shapes to the 'Shape dividers' in the section
		global $TRX_ADDONS_STORAGE;
		if ( $el_name == 'section' && $section_id == 'section_shape_divider' && !empty($TRX_ADDONS_STORAGE['shapes_list'])) {
			$sides = array('top', 'bottom');
			$options = $conditions = false;
			$prefix = 'trx_addons';
			foreach ($sides as $side) {
				// Add shapes to the shapes list
				$control_id = "shape_divider_{$side}";
				if ($options === false) {
					$control = $element->get_controls( $control_id );
					$options = $control['options'];
					foreach($TRX_ADDONS_STORAGE['shapes_list'] as $shape) {
						$shape_name = pathinfo($shape, PATHINFO_FILENAME);
						$options["{$prefix}_{$shape_name}"] = ucfirst(str_replace('_', ' ', $shape_name));
					}
				}
				$element->update_control( $control_id, array(
									'options' => $options
								) );

				// Add shapes to the condition for the 'Flip' and 'Width' controls
				$controls = array("flip", "width");
				if ($conditions === false) {
					$conditions = array();
					foreach ($controls as $control_name) {
						$control_id = "shape_divider_{$side}_{$control_name}";
						$control = $element->get_controls( $control_id );
						$conditions[$control_name] = isset($control['condition']) ? $control['condition'] : false;
						if (is_array($conditions[$control_name])) {
							foreach($TRX_ADDONS_STORAGE['shapes_list'] as $shape) {
								$shape_name = pathinfo($shape, PATHINFO_FILENAME);
								foreach ($conditions[$control_name] as $k=>$v) {
									if (is_array($v) && strpos($k, 'shape_divider_')!==false) {
										$v[] = "{$prefix}_{$shape_name}";
										$conditions[$control_name][$k] = $v;
									}
								}
							}
						}
					}
				}
				foreach ($controls as $control_name) {
					$control_id = "shape_divider_{$side}_{$control_name}";
					if ($conditions[$control_name] !== false) {
						$element->update_control( $control_id, array(
										'condition' => $conditions[$control_name]
									) );
					}
				}
			}
		}
	}
}


// Add/Remove column params to the new section
if (!function_exists('trx_addons_elm_add_params_to_columns')) {
	add_action( 'elementor/element/after_section_end', 'trx_addons_elm_add_params_to_columns', 10, 3 );
	function trx_addons_elm_add_params_to_columns($element, $section_id, $args) {

		if ( !is_object($element) ) return;
		
		if ( ($element->get_name() == 'section' && $section_id == 'section_layout')
				||
			 ($element->get_name() == 'column' && $section_id == 'layout')
		) {
			
			$element->start_controls_section( 'section_trx_layout',	array(
																		'tab' => \Elementor\Controls_Manager::TAB_ADVANCED,
																		'label' => __( 'Position', 'trx_addons' )
																	) );
			// Add 'Fix column' to the columns
			if ($element->get_name() == 'column') {
				$element->add_control( 'fix_column', array(
									'type' => \Elementor\Controls_Manager::SWITCHER,
									'label' => __( 'Fix column', 'trx_addons' ),
									'description' => wp_kses_data( __("Fix this column when page scrolling. Attention! At least one column in the row must have a greater height than this column", 'trx_addons') ),
									'label_on' => __( 'Fix', 'trx_addons' ),
									'label_off' => __( 'No', 'trx_addons' ),
									'return_value' => 'fixed',
									'prefix_class' => 'sc_column_',
									) );
			}
			$element->add_control( 'shift_x', array(
									'type' => \Elementor\Controls_Manager::SELECT,
									'label' => __("Shift block along the X-axis", 'trx_addons'),
									'options' => trx_addons_get_list_sc_content_shift(''),
									'default' => '',
									'prefix_class' => 'sc_shift_x_'
									) );
			$element->add_control( 'shift_y', array(
									'type' => \Elementor\Controls_Manager::SELECT,
									'label' => __("Shift block along the Y-axis", 'trx_addons'),
									'options' => trx_addons_get_list_sc_content_shift(''),
									'default' => '',
									'prefix_class' => 'sc_shift_y_'
									) );
			
			$element->add_control( 'push_x', array(
									'type' => \Elementor\Controls_Manager::SELECT,
									'label' => __("Push block along the X-axis", 'trx_addons'),
									'options' => trx_addons_get_list_sc_content_shift(''),
									'default' => '',
									'prefix_class' => 'sc_push_x_'
									) );
			$element->add_control( 'push_y', array(
									'type' => \Elementor\Controls_Manager::SELECT,
									'label' => __("Push block along the Y-axis", 'trx_addons'),
									'options' => trx_addons_get_list_sc_content_shift(''),
									'default' => '',
									'prefix_class' => 'sc_push_y_'
									) );
			
			$element->add_control( 'pull_x', array(
									'type' => \Elementor\Controls_Manager::SELECT,
									'label' => __("Pull next block along the X-axis", 'trx_addons'),
									'options' => trx_addons_get_list_sc_content_shift(''),
									'default' => '',
									'prefix_class' => 'sc_pull_x_'
									) );
			$element->add_control( 'pull_y', array(
									'type' => \Elementor\Controls_Manager::SELECT,
									'label' => __("Pull next block along the Y-axis", 'trx_addons'),
									'options' => trx_addons_get_list_sc_content_shift(''),
									'default' => '',
									'prefix_class' => 'sc_pull_y_'
									) );

			$element->end_controls_section();
		}
	}
}


// Add "Parallax" params to the new section to rows, columns and widgets
if (!function_exists('trx_addons_elm_add_params_parallax')) {
	add_action( 'elementor/element/after_section_end', 'trx_addons_elm_add_params_parallax', 10, 3 );
	add_action( 'elementor/widget/after_section_end', 'trx_addons_elm_add_params_parallax', 10, 3 );
	function trx_addons_elm_add_params_parallax($element, $section_id, $args) {

		if ( !is_object($element) ) return;

		if ( in_array($element->get_name(), array('section', 'column', 'common')) && in_array($section_id, array('section_border', '_section_border')) ) {
			
			$element->start_controls_section( 'section_trx_parallax',	array(
																		'tab' => !empty($args['tab']) ? $args['tab'] : \Elementor\Controls_Manager::TAB_ADVANCED,
																		'label' => __( 'Parallax', 'trx_addons' )
																	) );
			$element->add_control( 'parallax', array(
													'type' => \Elementor\Controls_Manager::SWITCHER,
													'label' => __( 'Parallax', 'trx_addons' ),
													'label_on' => __( 'On', 'trx_addons' ),
													'label_off' => __( 'Off', 'trx_addons' ),
													'return_value' => 'parallax',
													'prefix_class' => 'sc_',
									) );
			$element->add_control( 'parallax_start', array(
													'type' => \Elementor\Controls_Manager::SWITCHER,
													'label' => __( 'Values below are', 'trx_addons' ),
													'label_on' => __( 'Start', 'trx_addons' ),
													'label_off' => __( 'End', 'trx_addons' ),
													'return_value' => 'start',
													'prefix_class' => 'sc_parallax_',
									) );
			$element->add_control( 'parallax_x', array(
													'label' => __( 'The shift along the X-axis (in px)', 'trx_addons' ),
													'type' => \Elementor\Controls_Manager::SLIDER,
													'default' => [
														'size' => 0,
														'unit' => 'px'
													],
													'size_units' => [ 'px' ],
													'range' => [
														'px' => [
															'min' => -500,
															'max' => 500
														]
													],
													'condition' => [
														'parallax' => 'parallax'
													],
									) );
			$element->add_control( 'parallax_y', array(
													'label' => __( 'The shift along the Y-axis (in px)', 'trx_addons' ),
													'type' => \Elementor\Controls_Manager::SLIDER,
													'default' => [
														'size' => 0,
														'unit' => 'px'
													],
													'size_units' => [ 'px' ],
													'range' => [
														'px' => [
															'min' => -500,
															'max' => 500
														]
													],
													'condition' => [
														'parallax' => 'parallax'
													],
									) );

			$element->add_control( 'parallax_opacity', array(
													'label' => __( 'Change the opacity', 'trx_addons' ),
													'type' => \Elementor\Controls_Manager::SLIDER,
													'default' => [
														'size' => 0,
														'unit' => 'px'
													],
													'size_units' => [ 'px' ],
													'range' => [
														'px' => [
															'min' => -1,
															'max' => 0,
															'step' => 0.05
														]
													],
													'condition' => [
														'parallax' => 'parallax'
													],
									) );

			$element->add_control( 'parallax_scale', array(
													'label' => __( 'Change the scale (in %)', 'trx_addons' ),
													'type' => \Elementor\Controls_Manager::SLIDER,
													'default' => [
														'size' => 0,
														'unit' => 'px'
													],
													'size_units' => [ 'px' ],
													'range' => [
														'px' => [
															'min' => -100,
															'max' => 1000,
														]
													],
													'condition' => [
														'parallax' => 'parallax'
													],
									) );

			$element->add_control( 'parallax_rotate', array(
													'label' => __( 'Change the rotation (in deg)', 'trx_addons' ),
													'type' => \Elementor\Controls_Manager::SLIDER,
													'default' => [
														'size' => 0,
														'unit' => 'px'
													],
													'size_units' => [ 'px' ],
													'range' => [
														'px' => [
															'min' => -360,
															'max' => 360,
															'step' => 1
														]
													],
													'condition' => [
														'parallax' => 'parallax'
													],
									) );

			$element->end_controls_section();
		}
	}
}

// Add "data-parallax-params" to the wrapper of the rows, columns and widgets
if ( !function_exists( 'trx_addons_elm_add_data_parallax' ) ) {
	// Before Elementor 2.1.0
	add_action( 'elementor/frontend/element/before_render',  'trx_addons_elm_add_data_parallax', 10, 1 );
	// After Elementor 2.1.0
	add_action( 'elementor/frontend/section/before_render', 'trx_addons_elm_add_data_parallax', 10, 1 );
	add_action( 'elementor/frontend/column/before_render',  'trx_addons_elm_add_data_parallax', 10, 1 );
	add_action( 'elementor/frontend/widget/before_render',  'trx_addons_elm_add_data_parallax', 10, 1 );
	function trx_addons_elm_add_data_parallax($element) {
		$settings = trx_addons_elm_prepare_global_params( $element->get_settings() );
		if (!empty($settings['parallax'])) {
			$element->add_render_attribute( '_wrapper', 'data-parallax-params', json_encode(array(
				'type' => in_array($element->get_name(), array('section', 'column')) ? 'bg' : 'object',
				'x' => !empty($settings['parallax_x']) ? $settings['parallax_x']['size'] : 0,
				'y' => !empty($settings['parallax_y']) ? $settings['parallax_y']['size'] : 0,
				'scale' => !empty($settings['parallax_scale']) ? $settings['parallax_scale']['size'] : 0,
				'rotate' => !empty($settings['parallax_rotate']) ? $settings['parallax_rotate']['size'] : 0,
				'opacity' => !empty($settings['parallax_opacity']) ? $settings['parallax_opacity']['size'] : 0
			)) );
		}
	}
}


// Substitute shapes in the sections
if (!function_exists('trx_addons_elm_before_render')) {
	// Before Elementor 2.1.0
	add_action( 'elementor/frontend/element/before_render', 'trx_addons_elm_before_render', 10, 1 );
	// After Elementor 2.1.0
	add_action( 'elementor/frontend/section/before_render', 'trx_addons_elm_before_render', 10, 1 );
	function trx_addons_elm_before_render($element) {
		if ( is_object($element) ) {
			$el_name = $element->get_name();
			if ( $el_name == 'section' ) {
				$settings = trx_addons_elm_prepare_global_params( $element->get_settings() );
				$sides = array('top', 'bottom');
				$capture = false;
				$prefix = 'trx_addons';
				foreach ($sides as $side) {
					$base_setting_key = "shape_divider_{$side}";
					$shape = $settings[ $base_setting_key ];
					if (strpos($shape, "{$prefix}_") === 0) {
						$capture = true;
						$shapes = \Elementor\Shapes::get_shapes();
						if (!is_array($shapes)) $shapes = array('mountains'=>'');
						$element->set_settings("{$base_setting_key}_{$prefix}", str_replace("{$prefix}_", '', $shape));
						$element->set_settings($base_setting_key, trx_addons_array_get_first($shapes));
						if (!empty($element->active_settings[$base_setting_key])) {
							$element->active_settings[$base_setting_key] = trx_addons_array_get_first($shapes);
						}
					}
				}
				if ($capture) {
					ob_start();
				}
			}
		}
	}
}

if (!function_exists('trx_addons_elm_after_render')) {
	// Before Elementor 2.1.0
	add_action( 'elementor/frontend/element/after_render', 'trx_addons_elm_after_render', 10, 1 );
	// After Elementor 2.1.0
	add_action( 'elementor/frontend/section/after_render', 'trx_addons_elm_after_render', 10, 1 );
	function trx_addons_elm_after_render($element) {
		if ( is_object($element) ) {
			$el_name = $element->get_name();
			if ( $el_name == 'section' ) {
				$settings = trx_addons_elm_prepare_global_params( $element->get_settings() );
				$sides = array('top', 'bottom');
				$replace = array();
				$prefix = 'trx_addons';
				foreach ($sides as $side) {
					$base_setting_key = "shape_divider_{$side}";
					if (!empty($settings[ "{$base_setting_key}_{$prefix}" ])) {
						$replace["elementor-shape-{$side}"] = $settings[ "{$base_setting_key}_{$prefix}" ];
					}
				}
				if (count($replace) > 0) {
					$html = ob_get_contents();
					ob_end_clean();
					foreach ($replace as $class=>$shape) {
						$shape_dir = trx_addons_get_file_dir("css/shapes/{$shape}.svg");
						if (!empty($shape_dir)) {
							$html = preg_replace('~(<div[\s]*class="elementor-shape[\s]+'.$class.'".*>)([\s\S]*)(</div>)~U',
												'$1' . strip_tags(trx_addons_fgc($shape_dir), '<svg><path>') . '$3',
												$html);
						}
					}
					trx_addons_show_layout($html);
				}
			}
		}
	}
}



// Replace widget's args with theme-specific args
if ( !function_exists( 'trx_addons_elm_wordpress_widget_args' ) ) {
	add_filter( 'elementor/widgets/wordpress/widget_args', 'trx_addons_elm_wordpress_widget_args', 10, 2 );
	function trx_addons_elm_wordpress_widget_args($widget_args, $widget) {
		return trx_addons_prepare_widgets_args($widget->get_name(), $widget->get_name(), $widget_args);
	}
}

// Fixed Elementor Pro widgets
if ( !function_exists( 'trx_addons_elm_woocommerce_wordpress_widget_css_class' ) ) {
	add_filter( 'elementor/widgets/wordpress/widget_args', 'trx_addons_elm_woocommerce_wordpress_widget_css_class', 11, 2 );
	function trx_addons_elm_woocommerce_wordpress_widget_css_class($default_widget_args, $widget)
	{
		$widget_instance = $widget->get_widget_instance();
		
		if (!empty($widget_instance->widget_cssclass)) {
			$default_widget_args['after_widget'] = $default_widget_args['after_widget'] == '</aside></div>' ? '</div></aside>' : '';
		}
		return $default_widget_args;
	}
}

// Prepare group atts for the new Elementor version: make associative array from list by key 'name'
if ( !function_exists( 'trx_addons_elm_prepare_group_params' ) ) {
	add_filter( 'trx_addons_sc_param_group_params', 'trx_addons_elm_prepare_group_params', 999 );
	function trx_addons_elm_prepare_group_params( $args ) {
		if ( is_array( $args ) && ! empty( $args[0]['name'] ) ) {
			$new = array();
			foreach( $args as $item ) {
				if ( isset( $item['name'] ) ) {
					$new[ $item['name'] ] = $item;
				}
			}
			$args = $new;
		}
		return $args;
	}
}


// One-click import support
//------------------------------------------------------------------------

// Check plugin in the required plugins
if ( !function_exists( 'trx_addons_elm_importer_required_plugins' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_importer_required_plugins',	'trx_addons_elm_importer_required_plugins', 10, 2 );
	function trx_addons_elm_importer_required_plugins($not_installed='', $list='') {
		if (strpos($list, 'elementor')!==false && !trx_addons_exists_elementor())
			$not_installed .= '<br>' . esc_html__('Elementor (free PageBuilder)', 'trx_addons');
		return $not_installed;
	}
}

// Set plugin's specific importer options
if ( !function_exists( 'trx_addons_elm_importer_set_options' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_importer_options',	'trx_addons_elm_importer_set_options' );
	function trx_addons_elm_importer_set_options($options=array()) {
		if ( trx_addons_exists_elementor() && in_array('elementor', $options['required_plugins']) ) {
			$options['additional_options'][] = 'elementor%';		// Add slugs to export options for this plugin
		}
		return $options;
	}
}

// Prevent import plugin's specific options if plugin is not installed
if ( !function_exists( 'trx_addons_elm_importer_check_options' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_import_theme_options', 'trx_addons_elm_importer_check_options', 10, 4 );
	function trx_addons_elm_importer_check_options($allow, $k, $v, $options) {
		if ($allow && strpos($k, 'elementor')===0) {
			$allow = trx_addons_exists_elementor() && in_array('elementor', $options['required_plugins']);
		}
		return $allow;
	}
}




// OCDI support
//------------------------------------------------------------------------
// Set plugin's specific importer options
if ( !function_exists( 'trx_addons_ocdi_elementor_set_options' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_ocdi_options', 'trx_addons_ocdi_elementor_set_options' );
	function trx_addons_ocdi_elementor_set_options($ocdi_options){
		$ocdi_options['import_elementor_file_url'] = 'elementor.txt';
		return $ocdi_options;		
	}
}

// Export Elementor
if ( !function_exists( 'trx_addons_ocdi_elementor_export' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_ocdi_export_files', 'trx_addons_ocdi_elementor_export' );
	function trx_addons_ocdi_elementor_export($output){
		$list = array();
		if (trx_addons_exists_elementor() && in_array('elementor', trx_addons_ocdi_options('required_plugins'))) {
			// Get plugin data from database
			$options = array('elementor%');
			$list = trx_addons_ocdi_export_options($options, $list);
		
			// Save as file
			$file_path = TRX_ADDONS_PLUGIN_OCDI . "export/elementor.txt";
			trx_addons_fpc(trx_addons_get_file_dir($file_path), serialize($list));
			
			// Return file path
			$output .= '<h4><a href="'. trx_addons_get_file_url($file_path).'" download>'.esc_html__('Elementor (free PageBuilder)', 'trx_addons').'</a></h4>';
		}
		return $output;
	}
}

// Add plugin to import list
if ( !function_exists( 'trx_addons_ocdi_elementor_import_field' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_ocdi_import_fields', 'trx_addons_ocdi_elementor_import_field' );
	function trx_addons_ocdi_elementor_import_field($output){
		$list = array();
		if (trx_addons_exists_elementor() && in_array('elementor', trx_addons_ocdi_options('required_plugins'))) {
			$output .= '<label><input type="checkbox" name="elementor" value="elementor">'. esc_html__( 'Elementor (free PageBuilder)', 'trx_addons' ).'</label><br/>';
		}
		return $output;
	}
}

// Import Elementor
if ( !function_exists( 'trx_addons_ocdi_elementor_import' ) ) {
	if (is_admin()) add_action( 'trx_addons_action_ocdi_import_plugins', 'trx_addons_ocdi_elementor_import', 10, 1 );
	function trx_addons_ocdi_elementor_import($import_plugins){
		if (trx_addons_exists_elementor() && in_array('elementor', $import_plugins)) {
			trx_addons_ocdi_import_dump('elementor');
			echo esc_html__('Elementor import complete.', 'trx_addons') . "\r\n";
		}
	}
}

// Process post meta 
if ( !function_exists( 'trx_addons_ocdi_elementor_post_meta' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_ocdi_process_post_meta', 'trx_addons_ocdi_elementor_post_meta', 10, 2 );
	function trx_addons_ocdi_elementor_post_meta( $keys, $import_plugins ){
		if (trx_addons_exists_elementor() && in_array('elementor', $import_plugins)) {
			return array_merge($keys, array('_elementor_data', '_elementor_css', '_elementor_page_settings'));
		}
		else return $keys;
	}
}


// Fix for Elementor 3.3.0+ - move options 'blogname' and 'blogdescription'
// to the end of the list (after all 'elementor_%' options)
if ( !function_exists( 'trx_addons_elm_importer_theme_options_data' ) ) {
	add_filter( 'trx_addons_filter_import_theme_options_data', 'trx_addons_elm_importer_theme_options_data', 10, 1 );
	function trx_addons_elm_importer_theme_options_data( $data ) {
		if ( isset( $data['blogname'] ) ) {
			$val = $data['blogname'];
			unset( $data['blogname'] );
			$data['blogname'] = $val;
		}
		if ( isset( $data['blogdescription'] ) ) {
			$val = $data['blogdescription'];
			unset( $data['blogdescription'] );
			$data['blogdescription'] = $val;
		}
		return $data;
	}
}

// Move paddings from .elementor-element-wrap to .elementor-column-wrap
// to compatibility with old themes
if ( ! function_exists( 'trx_addons_elm_move_paddings_to_column_wrap' ) ) {
    add_action( 'elementor/element/before_section_end', 'trx_addons_elm_move_paddings_to_column_wrap', 10, 3 );
    function trx_addons_elm_move_paddings_to_column_wrap( $element, $section_id, $args ) {
        if ( is_object( $element ) ) {
            $el_name = $element->get_name();
            // Add one more classname to the selector for paddings of columns
            // to override theme-specific rules
            if ( 'column' == $el_name && 'section_advanced' == $section_id ) {
                $element->update_responsive_control( 'padding', array(
                    'selectors' => array(
                        '{{WRAPPER}} > .elementor-element-populated.elementor-column-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',    // Elm 2.9- (or DOM Optimization == Inactive)
                        '{{WRAPPER}} > .elementor-element-populated.elementor-widget-wrap' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',    // Elm 3.0+
                    )
                ) );
            }
        }
    }
}

