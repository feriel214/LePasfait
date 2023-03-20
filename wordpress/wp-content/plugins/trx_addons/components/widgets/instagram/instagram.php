<?php
/**
 * Widget: Instagram
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.47
 */

// Load widget
if (!function_exists('trx_addons_widget_instagram_load')) {
	add_action( 'widgets_init', 'trx_addons_widget_instagram_load' );
	function trx_addons_widget_instagram_load() {
		register_widget('trx_addons_widget_instagram');
	}
}

// Widget Class
class trx_addons_widget_instagram extends TRX_Addons_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'widget_instagram', 'description' => esc_html__('Last Instagram photos.', 'trx_addons') );
		parent::__construct( 'trx_addons_widget_instagram', esc_html__('ThemeREX Instagram Feed', 'trx_addons'), $widget_ops );
	}

	// Show widget
	function widget( $args, $instance ) {

		$title = apply_filters('widget_title', isset($instance['title']) ? $instance['title'] : '' );
		$links = isset($instance['links']) ? $instance['links'] : 'instagram';
		$follow = isset($instance['follow']) ? $instance['follow'] : 0;
		$hashtag = isset($instance['hashtag']) ? $instance['hashtag'] : '';
		$count = isset($instance['count']) ? $instance['count'] : 1;
		$columns = isset($instance['columns']) ? min($count, (int) $instance['columns']) : 1;
		$columns_gap = isset($instance['columns_gap']) ? max(0, $instance['columns_gap']) : 0;

		trx_addons_get_template_part(TRX_ADDONS_PLUGIN_WIDGETS . 'instagram/tpl.default.php',
									'trx_addons_args_widget_instagram', 
									apply_filters('trx_addons_filter_widget_args',
												array_merge($args, compact('title',
																			'links',
																			'follow',
																			'hashtag',
																			'count',
																			'columns',
																			'columns_gap')
															),
												$instance,
												'trx_addons_widget_instagram')
									);
	}

	// Update the widget settings.
	function update( $new_instance, $instance ) {
		$instance = array_merge($instance, $new_instance);
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['follow'] = !empty($new_instance['follow']) ? 1 : 0;
		$instance['links'] = strip_tags( $new_instance['links'] );
		$instance['hashtag'] = strip_tags( $new_instance['hashtag'] );
		$instance['count'] = (int) $new_instance['count'];
		$instance['columns'] = min($instance['count'], (int) $new_instance['columns']);
		$instance['columns_gap'] = max(0, $new_instance['columns_gap']);
		return apply_filters('trx_addons_filter_widget_args_update', $instance, $new_instance, 'trx_addons_widget_instagram');
	}

	// Displays the widget settings controls on the widget panel.
	function form( $instance ) {
		
		// Set up some default widget settings
		$instance = wp_parse_args( (array) $instance, apply_filters('trx_addons_filter_widget_args_default', array(
			'title' => '', 
			'follow' => 0,
			'links' => 'instagram',
			'hashtag' => '', 
			'count' => 8,
			'columns' => 4,
			'columns_gap' => 0
			), 'trx_addons_widget_instagram')
		);
		
		do_action('trx_addons_action_before_widget_fields', $instance, 'trx_addons_widget_instagram');
		
		$this->show_field(array('name' => 'title',
								'title' => __('Title:', 'trx_addons'),
								'value' => $instance['title'],
								'type' => 'text'));
		
		do_action('trx_addons_action_after_widget_title', $instance, 'trx_addons_widget_instagram');
		
		$this->show_field(array('name' => 'hashtag',
								'title' => __('Hash tag:', 'trx_addons'),
								'description' => __('Filter photos by hashtag. If empty - display all recent photos', 'trx_addons'),
								'value' => $instance['hashtag'],
								'type' => 'text'));
		
		$this->show_field(array('name' => 'count',
								'title' => __('Number of photos:', 'trx_addons'),
								'value' => max(1, min(30, (int) $instance['count'])),
								'type' => 'text'));
		
		$this->show_field(array('name' => 'columns',
								'title' => __('Columns:', 'trx_addons'),
								'value' => max(1, min(12, (int) $instance['columns'])),
								'type' => 'text'));
		
		$this->show_field(array('name' => 'columns_gap',
								'title' => __('Columns gap:', 'trx_addons'),
								'value' => max(0, (int) $instance['columns_gap']),
								'type' => 'text'));
		
		$this->show_field(array('name' => 'links',
								'title' => __('Link images to:', 'trx_addons'),
								'description' => __('Where to send a visitor after clicking on the picture', 'trx_addons'),
								'value' => $instance['links'],
								'options' => trx_addons_get_list_sc_instagram_redirects(),
								'type' => 'select'));
		
		$this->show_field(array('name' => 'follow',
								'title' => __('Show button "Follow me"', 'trx_addons'),
								'description' => __('Add button "Follow me" after images', 'trx_addons'),
								'value' => (int) $instance['follow'],
								'type' => 'checkbox'));
		
		do_action('trx_addons_action_after_widget_fields', $instance, 'trx_addons_widget_instagram');
	}
}


// Merge widget specific styles to the single stylesheet
if ( !function_exists( 'trx_addons_widget_instagram_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_widget_instagram_merge_styles');
	function trx_addons_widget_instagram_merge_styles($list) {
		$list[] = TRX_ADDONS_PLUGIN_WIDGETS . 'instagram/_instagram.scss';
		return $list;
	}
}


// Merge widget specific styles to the single stylesheet (responsive)
if ( !function_exists( 'trx_addons_widget_instagram_merge_styles_responsive' ) ) {
	add_filter("trx_addons_filter_merge_styles_responsive", 'trx_addons_widget_instagram_merge_styles_responsive');
	function trx_addons_widget_instagram_merge_styles_responsive($list) {
		$list[] = TRX_ADDONS_PLUGIN_WIDGETS . 'instagram/_instagram.responsive.scss';
		return $list;
	}
}


// Load required styles and scripts for the admin
if ( !function_exists( 'trx_addons_widget_instagram_load_scripts_admin' ) ) {
	add_action("admin_enqueue_scripts", 'trx_addons_widget_instagram_load_scripts_admin');
	function trx_addons_widget_instagram_load_scripts_admin() {
		wp_enqueue_script( 'trx_addons-widget_instagram', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_WIDGETS . 'instagram/instagram_admin.js'), array('jquery'), null, true );
	}
}

// Localize admin scripts
if ( !function_exists( 'trx_addons_widget_instagram_localize_script_admin' ) ) {
	add_action("trx_addons_filter_localize_script_admin", 'trx_addons_widget_instagram_localize_script_admin');
	function trx_addons_widget_instagram_localize_script_admin($vars) {
		$vars['api_instagram_get_code_uri'] = 'https://api.instagram.com/oauth/authorize/'
												. '?client_id='.urlencode(trx_addons_widget_instagram_get_client_id())
												. '&scope=public_content'
												. '&response_type=code'
												. '&redirect_uri='.urlencode(trx_addons_widget_instagram_rest_get_redirect_uri());
		return $vars;
	}
}

// Return Client ID from Instagram Application
if ( !function_exists( 'trx_addons_widget_instagram_get_client_id' ) ) {
	function trx_addons_widget_instagram_get_client_id() {
		$id = trx_addons_get_option('api_instagram_client_id');
		return !empty($id) ? $id : 'dacc2025c9084968b5c14aceea1404e1';
	}
}

// Return Client Secret from Instagram Application
if ( !function_exists( 'trx_addons_widget_instagram_get_client_secret' ) ) {
	function trx_addons_widget_instagram_get_client_secret() {
		return trx_addons_get_option('api_instagram_client_secret');
	}
}



// trx_widget_instagram
//-------------------------------------------------------------
/*
[trx_widget_instagram id="unique_id" title="Widget title" count="6" columns="3" hashtag="my_hash"]
*/
if ( !function_exists( 'trx_addons_sc_widget_instagram' ) ) {
	function trx_addons_sc_widget_instagram($atts, $content=null){	
		$atts = trx_addons_sc_prepare_atts('trx_widget_instagram', $atts, array(
			// Individual params
			"title" => "",
			'count'	=> 8,
			'columns' => 4,
			'columns_gap' => 0,
			'hashtag' => '',
			'links' => 'instagram',
			'follow' => 0,
			// Common params
			"id" => "",
			"class" => "",
			"css" => ""
			)
		);
		extract($atts);
		$type = 'trx_addons_widget_instagram';
		$output = '';
		if ( (int) $atts['count'] > 0 ) {
			global $wp_widget_factory;
			if ( is_object( $wp_widget_factory ) && isset( $wp_widget_factory->widgets, $wp_widget_factory->widgets[ $type ] ) ) {
				$output = '<div' . ($id ? ' id="'.esc_attr($id).'"' : '')
								. ' class="widget_area sc_widget_instagram' 
									. (trx_addons_exists_vc() ? ' vc_widget_instagram wpb_content_element' : '') 
									. (!empty($class) ? ' ' . esc_attr($class) : '') 
								. '"'
							. ($css ? ' style="'.esc_attr($css).'"' : '')
						. '>';
				ob_start();
				the_widget( $type, $atts, trx_addons_prepare_widgets_args($id ? $id.'_widget' : 'widget_instagram', 'widget_instagram') );
				$output .= ob_get_contents();
				ob_end_clean();
				$output .= '</div>';
			}
		}
		return apply_filters('trx_addons_sc_output', $output, 'trx_widget_instagram', $atts, $content);
	}
}


// Add [trx_widget_instagram] in the VC shortcodes list
if (!function_exists('trx_addons_widget_instagram_reg_shortcodes_vc')) {
	function trx_addons_widget_instagram_reg_shortcodes_vc() {

		add_shortcode("trx_widget_instagram", "trx_addons_sc_widget_instagram");
		
		if (!trx_addons_exists_vc()) return;
		
		vc_lean_map("trx_widget_instagram", 'trx_addons_widget_instagram_reg_shortcodes_vc_params');
		class WPBakeryShortCode_Trx_Widget_Instagram extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_widget_instagram_reg_shortcodes_vc', 20);
}

// Return params
if (!function_exists('trx_addons_widget_instagram_reg_shortcodes_vc_params')) {
	function trx_addons_widget_instagram_reg_shortcodes_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_widget_instagram",
				"name" => esc_html__("Instagram feed", 'trx_addons'),
				"description" => wp_kses_data( __("Display the latest photos from instagram account by hashtag", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_widget_instagram',
				"class" => "trx_widget_instagram",
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
							"class" => "",
							"value" => "",
							"type" => "textfield"
						),
						array(
							"param_name" => "hashtag",
							"heading" => esc_html__("Hashtag", 'trx_addons'),
							"description" => wp_kses_data( __("Hashtag to filter your photos", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"class" => "",
							"value" => "",
							"type" => "textfield"
						),
						array(
							"param_name" => "count",
							"heading" => esc_html__("Number of photos", 'trx_addons'),
							"description" => wp_kses_data( __("How many photos to be displayed?", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"class" => "",
							"value" => "8",
							"type" => "textfield"
						),
						array(
							"param_name" => "columns",
							"heading" => esc_html__("Columns", 'trx_addons'),
							"description" => wp_kses_data( __("Columns number", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"class" => "",
							"value" => "4",
							"type" => "textfield"
						),
						array(
							"param_name" => "columns_gap",
							"heading" => esc_html__("Columns gap", 'trx_addons'),
							"description" => wp_kses_data( __("Gap between images", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"class" => "",
							"value" => "0",
							"type" => "textfield"
						),
						array(
							"param_name" => "links",
							"heading" => esc_html__("Link images to", 'trx_addons'),
							"description" => wp_kses_data( __("Where to send a visitor after clicking on the picture", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"class" => "",
							"std" => "instagram",
							"value" => array_flip(trx_addons_get_list_sc_instagram_redirects()),
							"type" => "dropdown"
						),
						array(
							"param_name" => "follow",
							"heading" => esc_html__('Show button "Follow me"', 'trx_addons'),
							"description" => wp_kses_data( __('Add button "Follow me" after images', 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-6',
							"std" => "1",
							"value" => array("Show Follow Me" => "1" ),
							"type" => "checkbox"
						)
					),
					trx_addons_vc_add_id_param()
				)
			), 'trx_widget_instagram');
	}
}




// Elementor Widget
//------------------------------------------------------
if (!function_exists('trx_addons_sc_widget_instagram_add_in_elementor')) {
	add_action( 'elementor/widgets/widgets_registered', 'trx_addons_sc_widget_instagram_add_in_elementor' );
	function trx_addons_sc_widget_instagram_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Widget')) return;	

		class TRX_Addons_Elementor_Widget_Instagram extends TRX_Addons_Elementor_Widget {

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
					'count' => 'size',
					'columns' => 'size',
					'columns_gap' => 'size+unit'
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
				return 'trx_widget_instagram';
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
				return __( 'Widget: Instagram', 'trx_addons' );
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
				return 'eicon-insert-image';
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
					'section_sc_instagram',
					[
						'label' => __( 'Widget: Instagram', 'trx_addons' ),
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
					'hashtag',
					[
						'label' => __( 'Hashtag', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "#hashtag", 'trx_addons' ),
						'default' => ''
					]
				);
				
				$this->add_control(
					'count',
					[
						'label' => __( 'Number of photos', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 8
						],
						'range' => [
							'px' => [
								'min' => 1,
								'max' => 30
							]
						]
					]
				);
				
				$this->add_control(
					'columns',
					[
						'label' => __( 'Columns', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 4
						],
						'range' => [
							'px' => [
								'min' => 1,
								'max' => 12
							]
						],
						'selectors' => [
							'{{WRAPPER}} .widget_instagram_images .widget_instagram_images_item_wrap' => 'width:calc(100%/{{SIZE}});',
						]
					]
				);

				$this->add_control(
					'columns_gap',
					[
						'label' => __( 'Gap between columns', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 0,
							'unit' => 'px'
						],
						'size_units' => ['px', 'em'],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 100
							],
							'em' => [
								'min' => 0,
								'max' => 10,
								'step' => 0.1
							]
						],
						'selectors' => [
							'{{WRAPPER}} .widget_instagram_images' => 'margin-right: -{{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .widget_instagram_images .widget_instagram_images_item_wrap' => 'padding: 0 {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0;',
						],
					]
				);
				
				$this->add_control(
					'links',
					[
						'label' => __( 'Links', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Where to send a visitor after clicking on the picture", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => trx_addons_get_list_sc_instagram_redirects(),
						'default' => 'instagram'
					]
				);

				$this->add_control(
					'follow',
					[
						'label' => __( 'Show button "Follow Me"', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'default' => '',
						'return_value' => '1'
					]
				);

				$this->end_controls_section();
			}
		}

		// Register widget
		\Elementor\Plugin::$instance->widgets_manager->register_widget_type( new TRX_Addons_Elementor_Widget_Instagram() );
	}
}


// Disable our widgets (shortcodes) to use in Elementor
// because we create special Elementor's widgets instead
if (!function_exists('trx_addons_widget_instagram_black_list')) {
	add_action( 'elementor/widgets/black_list', 'trx_addons_widget_instagram_black_list' );
	function trx_addons_widget_instagram_black_list($list) {
		$list[] = 'trx_addons_widget_instagram';
		return $list;
	}
}

require_once trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_WIDGETS . "instagram/instagram_rest_api.php");
?>