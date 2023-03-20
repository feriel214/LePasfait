<?php
/**
 * Shortcode: Single Post Meta
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.49
 */
	
// Merge shortcode specific styles into single stylesheet
if ( !function_exists( 'trx_addons_sc_layouts_meta_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_sc_layouts_meta_merge_styles');
	add_filter("trx_addons_filter_merge_styles_layouts", 'trx_addons_sc_layouts_meta_merge_styles');
	function trx_addons_sc_layouts_meta_merge_styles($list) {
		$list[] = TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'meta/_meta.scss';
		return $list;
	}
}


// trx_sc_layouts_meta
//-------------------------------------------------------------
/*
[trx_sc_layouts_meta id="unique_id"]
*/
if ( !function_exists( 'trx_addons_sc_layouts_meta' ) ) {
	function trx_addons_sc_layouts_meta($atts, $content=null){
		$atts = trx_addons_sc_prepare_atts('trx_sc_layouts_meta', $atts, array(
				// Individual params
				"type" => "",
				"components" => "",
				"counters" => "",
				"seo" => "",
				"post_type" => array(),
				// Common params
				"id" => "",
				"class" => "",
				"css" => "",
			)
		);
		
		$output = '';

		if ( empty($atts['post_type']) || in_array( get_post_type(), $atts['post_type'] ) ) {
			ob_start();
			trx_addons_get_template_part( array(
												TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'meta/tpl.'.trx_addons_esc($atts['type']).'.php',
												TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'meta/tpl.default.php'
											),
											'trx_addons_args_sc_layouts_meta',
											$atts
										);
			$output = ob_get_contents();
			ob_end_clean();
		}
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_layouts_meta', $atts, $content);
	}
}


// Add [trx_sc_layouts_meta] in the VC shortcodes list
if (!function_exists('trx_addons_sc_layouts_meta_add_in_vc')) {
	function trx_addons_sc_layouts_meta_add_in_vc() {
		
		add_shortcode("trx_sc_layouts_meta", "trx_addons_sc_layouts_meta");
		
		if (!trx_addons_exists_vc()) return;
		
		vc_lean_map("trx_sc_layouts_meta", 'trx_addons_sc_layouts_meta_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Layouts_Meta extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_layouts_meta_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_layouts_meta_add_in_vc_params')) {
	function trx_addons_sc_layouts_meta_add_in_vc_params() {

		$components = apply_filters('trx_addons_filter_get_list_meta_parts', array());
		$counters = apply_filters('trx_addons_filter_get_list_counters', array());

		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_layouts_meta",
				"name" => esc_html__("Layouts: Single Post Meta", 'trx_addons'),
				"description" => wp_kses_data( __("Add post meta", 'trx_addons') ),
				"category" => esc_html__('ThemeREX', 'trx_addons'),
				"icon" => 'icon_trx_sc_layouts_meta',
				"class" => "trx_sc_layouts_meta",
				'content_element' => true,
				'is_container' => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "type",
							"heading" => esc_html__("Layout", 'trx_addons'),
							"description" => wp_kses_data( __("Select shortcodes's layout", 'trx_addons') ),
							"admin_label" => true,
							'save_always' => true,
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "default",
							"value" => array_flip(apply_filters('trx_addons_sc_type', trx_addons_get_list_sc_layouts_meta(), 'trx_sc_layouts_meta')),
							"type" => "dropdown"
						),
						array(
							"param_name" => "components",
							"heading" => esc_html__("Choose components", 'trx_addons'),
							"description" => wp_kses_data( __("Display specified post meta elements", 'trx_addons') ),
							"admin_label" => true,
							'save_always' => true,
							'edit_field_class' => 'vc_col-sm-4',
							"std" => isset($components[0]) ? $components[0] : "",
							"value" => array_flip( $components ),
							"multiple" => true,
							"type" => "select"
						),
						array(
							"param_name" => "counters",
							"heading" => esc_html__("Counters", 'trx_addons'),
							"description" => wp_kses_data( __("Display specified counters", 'trx_addons') ),
							"admin_label" => true,
							'save_always' => true,
							'edit_field_class' => 'vc_col-sm-4',
							"std" => isset($counters[0]) ? $counters[0] : "",
							"value" => array_flip( $counters ),
							"multiple" => true,
							"type" => "select"
						),
					),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_layouts_meta' );
	}
}




// Elementor Widget
//------------------------------------------------------
if (!function_exists('trx_addons_sc_layouts_meta_add_in_elementor')) {
	add_action( 'elementor/widgets/widgets_registered', 'trx_addons_sc_layouts_meta_add_in_elementor' );
	function trx_addons_sc_layouts_meta_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Widget')) return;	

		class TRX_Addons_Elementor_Widget_Layouts_Meta extends TRX_Addons_Elementor_Widget {

			/**
			 * Retrieve widget name.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget name.
			 */
			public function get_name() {
				return 'trx_sc_layouts_meta';
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
				return __( 'Layouts: Single Post Meta', 'trx_addons' );
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
				return 'eicon-t-letter';
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
				return ['trx_addons-layouts'];
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

				$components = apply_filters('trx_addons_filter_get_list_meta_parts', array());
				$counters = apply_filters('trx_addons_filter_get_list_counters', array());

				$this->start_controls_section(
					'section_sc_layouts_meta',
					[
						'label' => __( 'Single Post Meta', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_type', trx_addons_get_list_sc_layouts_meta(), 'trx_sc_layouts_meta'),
						'default' => 'default',
					]
				);

				$this->add_control(
					'components',
					[
						'label' => __( 'Choose components', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __('Display specified post meta elements', 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SELECT2,
						'options' => $components,
						'multiple' => true,
						'default' => 'date',
					]
				);

				$this->add_control(
					'counters',
					[
						'label' => __( 'Counters', 'trx_addons' ),
						'description' => wp_kses_data( __('Select single post type to display post meta', 'trx_addons') ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT2,
						'options' => $counters,
						'multiple' => true,
						'default' => isset($counters[0]) ? $counters[0] : ''
					]
				);

				$this->end_controls_section();
			}
		}
		
		// Register widget
		\Elementor\Plugin::$instance->widgets_manager->register_widget_type( new TRX_Addons_Elementor_Widget_Layouts_Meta() );
	}
}


// Disable our widgets (shortcodes) to use in Elementor
// because we create special Elementor's widgets instead
if (!function_exists('trx_addons_sc_layouts_meta_black_list')) {
	add_action( 'elementor/widgets/black_list', 'trx_addons_sc_layouts_meta_black_list' );
	function trx_addons_sc_layouts_meta_black_list($list) {
		$list[] = 'TRX_Addons_SOW_Widget_Layouts_Meta';
		return $list;
	}
}
