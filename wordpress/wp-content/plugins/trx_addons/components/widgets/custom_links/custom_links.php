<?php
/**
 * Widget: Custom links
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.0.46
 */

// Load widget
if (!function_exists('trx_addons_widget_custom_links_load')) {
	add_action( 'widgets_init', 'trx_addons_widget_custom_links_load' );
	function trx_addons_widget_custom_links_load() {
		register_widget( 'trx_addons_widget_custom_links' );
	}
}

// Widget Class
class trx_addons_widget_custom_links extends TRX_Addons_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'widget_custom_links', 'description' => esc_html__('Custom links with icon, title and description', 'trx_addons') );
		parent::__construct( 'trx_addons_widget_custom_links', esc_html__('ThemeREX Custom Links', 'trx_addons'), $widget_ops );
		add_filter('trx_addons_filter_need_options', array($this, 'meta_box_need_options'));
	}


	// Return true if current screen need to load options scripts and styles
	function meta_box_need_options($need = false) {
		if (!$need) {
			// If current screen is 'Edit Page' with one of ThemeREX Addons custom post types
			$screen = function_exists('get_current_screen') ? get_current_screen() : false;
			$need = is_object($screen) && $screen->id=='widgets';
		}
		return $need;
	}

	// Show widget
	function widget( $args, $instance ) {
		$id = isset($instance['id']) ? $instance['id'] : 'sc_custom_links_'.esc_attr(mt_rand());
		$title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '' );
		$links = isset($instance['links']) ? $instance['links'] : array();
		$icons_animation = isset($instance['icons_animation']) ? $instance['icons_animation'] : 0;
		trx_addons_get_template_part(TRX_ADDONS_PLUGIN_WIDGETS . 'custom_links/tpl.default.php',
									'trx_addons_args_widget_custom_links',
									apply_filters('trx_addons_filter_widget_args',
												array_merge($args, compact('id', 'title', 'links', 'icons_animation')),
												$instance, 'trx_addons_widget_custom_links')
									);
	}

	// Update the widget settings.
	function update( $new_instance, $instance ) {
		$instance = array_merge($instance, $new_instance);
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['icons_animation'] = isset($new_instance['icons_animation']) ? $new_instance['icons_animation'] : 0;
		$instance['links'] = $new_instance['links'];
		if (is_array($instance['links'])) {
			for ($i=0; $i<count($instance['links']); $i++) {
				if (empty($instance['links'][$i]['new_window'])) $instance['links'][$i]['new_window'] = 0;
			}
		}
		return apply_filters('trx_addons_filter_widget_args_update', $instance, $new_instance, 'trx_addons_widget_custom_links');
	}

	// Displays the widget settings controls on the widget panel.
	function form( $instance ) {

		// Remove empty links array
		if (isset($instance['links']) && (!is_array($instance['links']) || count($instance['links']) == 0))
			unset($instance['links']);
		// Set up some default widget settings
		$instance = wp_parse_args( (array) $instance, apply_filters('trx_addons_filter_widget_args_default', array(
			'title' => '',
			'icons_animation' => 0,
			'links' => array(
							array('url'=>'', 'caption'=>'', 'new_window'=>0, 'image'=>'', 'icon'=>'', 'title'=>'', 'description'=>''),
							array('url'=>'', 'caption'=>'', 'new_window'=>0, 'image'=>'', 'icon'=>'', 'title'=>'', 'description'=>''),
							array('url'=>'', 'caption'=>'', 'new_window'=>0, 'image'=>'', 'icon'=>'', 'title'=>'', 'description'=>''),
							array('url'=>'', 'caption'=>'', 'new_window'=>0, 'image'=>'', 'icon'=>'', 'title'=>'', 'description'=>''),
							array('url'=>'', 'caption'=>'', 'new_window'=>0, 'image'=>'', 'icon'=>'', 'title'=>'', 'description'=>'')
							)
			), 'trx_addons_widget_custom_links')
		);
		
		do_action('trx_addons_action_before_widget_fields', $instance, 'trx_addons_widget_custom_links');
		
		$this->show_field(array('name' => 'title',
								'title' => __('Title:', 'trx_addons'),
								'value' => $instance['title'],
								'type' => 'text'));
		$this->show_field(array('name' => "icons_animation",
								'title' => __('Animate icons:', 'trx_addons'),
								'value' => $instance['icons_animation'],
								'type' => 'checkbox'));
		
		do_action('trx_addons_action_after_widget_title', $instance, 'trx_addons_widget_custom_links');

		foreach($instance['links'] as $k=>$link) {
			$this->show_field(array('name' => sprintf('link%d', $k+1),
									'title' => sprintf(__('Link %d', 'trx_addons'), $k+1),
									'type' => 'info'));			
			$this->show_field(array('name' => "links[{$k}][url]",
									'title' => __('Link URL:', 'trx_addons'),
									'value' => $link['url'],
									'type' => 'text'));
			$this->show_field(array('name' => "links[{$k}][new_window]",
									'title' => __('Open in a new window:', 'trx_addons'),
									'value' => $link['new_window'],
									'type' => 'checkbox'));
			$this->show_field(array('name' => "links[{$k}][image]",
									'title' => __('Link image URL:<br>(leave empty if you want to use icon)', 'trx_addons'),
									'value' => $link['image'],
									'type' => 'image'));
			$this->show_field(array('name' => "links[{$k}][icon]",
									'title' => __('Link icon:', 'trx_addons'),
									'value' => $link['icon'],
									'style' => trx_addons_get_setting('icons_type'),
									'options' => trx_addons_get_list_icons(trx_addons_get_setting('icons_type')),
									'type' => 'icons'));
			$this->show_field(array('name' => "links[{$k}][title]",
									'title' => __('Link title:', 'trx_addons'),
									'value' => $link['title'],
									'type' => 'text'));
			$this->show_field(array('name' => "links[{$k}][description]",
									'title' => __('Link description:', 'trx_addons'),
									'value' => $link['description'],
									'type' => 'textarea'));
			$this->show_field(array('name' => "links[{$k}][caption]",
									'title' => __('Button caption:', 'trx_addons'),
									'value' => $link['caption'],
									'type' => 'text'));
		}		

		do_action('trx_addons_action_after_widget_fields', $instance, 'trx_addons_widget_custom_links');
	}
}

	
// Merge widget specific styles into single stylesheet
if ( !function_exists( 'trx_addons_widget_custom_links_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_widget_custom_links_merge_styles');
	function trx_addons_widget_custom_links_merge_styles($list) {
		$list[] = TRX_ADDONS_PLUGIN_WIDGETS . 'custom_links/_custom_links.scss';
		return $list;
	}
}



// trx_widget_custom_links
//-------------------------------------------------------------
/*
[trx_widget_custom_links id="unique_id" title="Widget title" fullwidth="0|1" image="image_url" link="Image_link_url" code="html & js code"]
*/
if ( !function_exists( 'trx_addons_sc_widget_custom_links' ) ) {
	function trx_addons_sc_widget_custom_links($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_widget_custom_links', $atts, array(
			// Individual params
			"title" => "",
			"icons_animation" => "0",
			"links" => "",
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
			)
		);
		if (function_exists('vc_param_group_parse_atts') && !is_array($atts['links']))
			$atts['links'] = (array) vc_param_group_parse_atts( $atts['links'] );
		if (is_array($atts['links']) && count($atts['links']) > 0) {
			foreach ($atts['links'] as $k=>$v) {
				if (!empty($v['description']))
					$atts['links'][$k]['description'] = preg_replace( '/\\[(.*)\\]/', '<b>$1</b>', function_exists('vc_value_from_safe') ? vc_value_from_safe( $v['description'] ) : $v['description'] );
			}
		}
		extract($atts);
		$type = 'trx_addons_widget_custom_links';
		$output = '';
		global $wp_widget_factory;
		if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $type ] ) ) {
			$atts['from_shortcode'] = true;
			$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
							. ' class="widget_area sc_widget_custom_links' 
								. (trx_addons_exists_vc() ? ' vc_widget_custom_links wpb_content_element' : '') 
								. (!empty($class) ? ' ' . esc_attr($class) : '') 
								. '"'
							. ($css ? ' style="'.esc_attr($css).'"' : '')
						. '>';
			ob_start();
			the_widget( $type, $atts, trx_addons_prepare_widgets_args($id ? $id.'_widget' : 'widget_custom_links', 'widget_custom_links') );
			$output .= ob_get_contents();
			ob_end_clean();
			$output .= '</div>';
		}
		return apply_filters('trx_addons_sc_output', $output, 'trx_widget_custom_links', $atts, $content);
	}
}


// Add [trx_widget_custom_links] in the VC shortcodes list
if (!function_exists('trx_addons_sc_widget_custom_links_add_in_vc')) {
	function trx_addons_sc_widget_custom_links_add_in_vc() {
		
		add_shortcode("trx_widget_custom_links", "trx_addons_sc_widget_custom_links");
		
		if (!trx_addons_exists_vc()) return;
		
		vc_lean_map("trx_widget_custom_links", 'trx_addons_sc_widget_custom_links_add_in_vc_params');
		class WPBakeryShortCode_Trx_Widget_Custom_Links extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_widget_custom_links_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_widget_custom_links_add_in_vc_params')) {
	function trx_addons_sc_widget_custom_links_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_widget_custom_links",
				"name" => esc_html__("Custom Links", 'trx_addons'),
				"description" => wp_kses_data( __("Insert widget with list of the custom links", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_widget_custom_links',
				"class" => "trx_widget_custom_links",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "title",
							"heading" => esc_html__("Widget title", 'trx_addons'),
							"description" => wp_kses_data( __("Title of the widget", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"admin_label" => true,
							"type" => "textfield"
						),
						array(
							"param_name" => "icons_animation",
							"heading" => esc_html__("Animation", 'trx_addons'),
							"description" => wp_kses_data( __("Check if you want animate icons. Attention! Animation enabled only if in your theme exists .SVG icon with same name as selected icon", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"std" => "0",
							"value" => array(esc_html__("Animate icons", 'trx_addons') => "1" ),
							"type" => "checkbox"
						),
						array(
							'type' => 'param_group',
							'param_name' => 'links',
							'heading' => esc_html__( 'Links', 'trx_addons' ),
							"description" => wp_kses_data( __("List of the custom links", 'trx_addons') ),
							'value' => urlencode( json_encode( apply_filters('trx_addons_sc_param_group_value', array(
								array(
									'title' => esc_html__( 'One', 'trx_addons' ),
									'description' => '',
									'image' => '',
									'url' => '',
									'new_window' => '0',
									'caption' => '',
									'icon' => '',
									'icon_fontawesome' => 'empty',
									'icon_openiconic' => 'empty',
									'icon_typicons' => 'empty',
									'icon_entypo' => 'empty',
									'icon_linecons' => 'empty'
								),
							), 'trx_widget_custom_links') ) ),
							'params' => apply_filters('trx_addons_sc_param_group_params', array_merge(array(
									array(
										'param_name' => 'title',
										'heading' => esc_html__( 'Title', 'trx_addons' ),
										'description' => esc_html__( 'Enter title for this item', 'trx_addons' ),
										'admin_label' => true,
										'type' => 'textfield',
									),
									array(
										'param_name' => 'url',
										'heading' => esc_html__( 'Link URL', 'trx_addons' ),
										'description' => esc_html__( 'URL to link this item', 'trx_addons' ),
										'edit_field_class' => 'vc_col-sm-6',
										'type' => 'textfield',
									),
									array(
										'param_name' => 'caption',
										'heading' => esc_html__( 'Caption', 'trx_addons' ),
										'description' => esc_html__( 'Caption to create button', 'trx_addons' ),
										'edit_field_class' => 'vc_col-sm-6',
										'type' => 'textfield',
									),
									array(
										"param_name" => "image",
										"heading" => esc_html__("Image", 'trx_addons'),
										"description" => wp_kses_data( __("Select or upload image or specify URL from other site", 'trx_addons') ),
										'edit_field_class' => 'vc_col-sm-6',
										"type" => "attach_image"
									),
									array(
										"param_name" => "new_window",
										"heading" => esc_html__("Open link in a new window", 'trx_addons'),
										"description" => wp_kses_data( __("Check if you want open this link in a new window (tab)", 'trx_addons') ),
										'edit_field_class' => 'vc_col-sm-6',
										"std" => "0",
										"value" => array(esc_html__("New window", 'trx_addons') => "1" ),
										"type" => "checkbox"
									),
								),
								trx_addons_vc_add_icon_param(''),
								array(
									array(
										'param_name' => 'description',
										'heading' => esc_html__( 'Description', 'trx_addons' ),
										'description' => esc_html__( 'Enter short description for this item', 'trx_addons' ),
										'type' => 'textarea_safe',
									),
								)
							), 'trx_widget_custom_links')
						)
					),
					trx_addons_vc_add_id_param()
				)
			), 'trx_widget_custom_links' );
	}
}




// Elementor Widget
//------------------------------------------------------
if (!function_exists('trx_addons_sc_widget_custom_links_add_in_elementor')) {
	add_action( 'elementor/widgets/widgets_registered', 'trx_addons_sc_widget_custom_links_add_in_elementor' );
	function trx_addons_sc_widget_custom_links_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Widget')) return;	

		class TRX_Addons_Elementor_Widget_Custom_Links extends TRX_Addons_Elementor_Widget {

			/**
			 * Retrieve widget name.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget name.
			 */
			public function get_name() {
				return 'trx_widget_custom_links';
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
				return __( 'Widget: Custom Links', 'trx_addons' );
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
				return 'eicon-toggle';
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
				$this->start_controls_section(
					'section_sc_custom_links',
					[
						'label' => __( 'Widget: Custom Links', 'trx_addons' ),
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
					'icons_animation',
					[
						'label' => __( 'Icons animation', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Check if you want animate icons. Attention! Animation enabled only if in your theme exists .SVG icon with same name as selected icon", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1'
					]
				);
				
				$this->add_control(
					'links',
					[
						'label' => '',
						'type' => \Elementor\Controls_Manager::REPEATER,
						'default' => apply_filters('trx_addons_sc_param_group_value', [
							[
								'title' => __( 'First link', 'trx_addons' ),
								'description' => $this->get_default_description(),
								'url' => ['url' => '#', 'is_external' => ''],
								'image' => ['url' => ''],
								'icon' => 'icon-star-empty',
								'caption' => ''
							],
							[
								'title' => __( 'Second link', 'trx_addons' ),
								'description' => $this->get_default_description(),
								'url' => ['url' => '#', 'is_external' => ''],
								'image' => ['url' => ''],
								'icon' => 'icon-heart-empty',
								'caption' => ''
							],
							[
								'title' => __( 'Third link', 'trx_addons' ),
								'description' => $this->get_default_description(),
								'url' => ['url' => '#', 'is_external' => ''],
								'image' => ['url' => ''],
								'icon' => 'icon-clock-empty',
								'caption' => ''
							]
						], 'trx_widget_custom_links'),
						'fields' => apply_filters('trx_addons_sc_param_group_params', array_merge(
							[
								[
									'name' => 'title',
									'label' => __( 'Title', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::TEXT,
									'placeholder' => __( "Link's title", 'trx_addons' ),
									'default' => ''
								],
								[
									'name' => 'url',
									'label' => __( 'Link URL', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::URL,
									'placeholder' => __( 'http://your-link.com', 'trx_addons' ),
									'default' => ''
								],
								[
									'name' => 'caption',
									'label' => __( 'Button caption', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::TEXT,
									'placeholder' => __( "Caption", 'trx_addons' ),
									'default' => ''
								],
								[
									'name' => 'image',
									'label' => __( 'Image', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::MEDIA,
									'default' => [
										'url' => '',
									],
								]
							],
							$this->get_icon_param(),
							[
								[
									'name' => 'description',
									'label' => __( 'Description', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::TEXTAREA,
									'placeholder' => __( "Short description of this item", 'trx_addons' ),
									'default' => '',
									'separator' => 'none',
									'rows' => 10,
									'show_label' => false,
								]
							]),
							'trx_widget_custom_links'),
						'title_field' => '{{{ title }}}',
					]
				);

				$this->end_controls_section();
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
				trx_addons_get_template_part(TRX_ADDONS_PLUGIN_WIDGETS . "custom_links/tpe.custom_links.php",
										'trx_addons_args_widget_custom_links',
										array('element' => $this)
									);
			}
		}
		
		// Register widget
		\Elementor\Plugin::$instance->widgets_manager->register_widget_type( new TRX_Addons_Elementor_Widget_Custom_Links() );
	}
}


// Disable our widgets (shortcodes) to use in Elementor
// because we create special Elementor's widgets instead
if (!function_exists('trx_addons_widget_custom_links_black_list')) {
	add_action( 'elementor/widgets/black_list', 'trx_addons_widget_custom_links_black_list' );
	function trx_addons_widget_custom_links_black_list($list) {
		$list[] = 'trx_addons_widget_custom_links';
		return $list;
	}
}
?>