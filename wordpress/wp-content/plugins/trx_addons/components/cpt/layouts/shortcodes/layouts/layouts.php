<?php
/**
 * Shortcode: Display any previously created layout
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.06
 */


// trx_sc_layouts
//-------------------------------------------------------------
/*
[trx_sc_layouts id="unique_id" layout="layout_id"]
*/
if ( !function_exists( 'trx_addons_sc_layouts' ) ) {
	function trx_addons_sc_layouts($atts, $content=null) {	
		$atts = trx_addons_sc_prepare_atts('trx_sc_layouts', $atts, array(
			// Individual params
			"type" => "default",
			"layout" => "",
			"content" => "",		// Alternative content
			// Panels parameters
			"position" => "right",
			"size" => 300,
			"modal" => 0,
			"show_on_load" => "none",
			// Common params
			"id" => "",
			"popup_id" => "",		// Alter name for id in Elementor ('id' is reserved by Elementor)
			"class" => "",
			"css" => ""
			)
		);

		$output = '';

		if (empty($atts['content']) && !empty($content))
			$atts['content'] = $content;
		
		if (!empty($atts['popup_id']))
			$atts['id'] = $atts['popup_id'];

		// If content specified and no layout selected
		if (!empty($atts['content']) && empty($atts['layout'])) {
			$atts['layout'] = '';
			// Remove tags p if content contain shortcodes
			if (strpos($atts['content'], '[') !== false)
				$atts['content'] = shortcode_unautop($atts['content']);
			// Do shortcodes inside content
			$atts['content'] = apply_filters('widget_text_content', $atts['content']);

		// Get translated version of specified layout
		} else if (!empty($atts['layout'])) {
			$atts['layout'] = apply_filters('trx_addons_filter_get_translated_layout', $atts['layout']);
		}
		
		// Add 'size' as class
		if ($atts['type'] == 'panel') {
			if (empty($atts['size'])) $atts['size'] = 'auto';
			$atts['class'] .= (!empty($atts['class']) ? ' ' : '') 
								. trx_addons_add_inline_css_class(
									trx_addons_get_css_dimensions_from_values(
										in_array($atts['position'], array('left', 'right')) ? $atts['size'] : '',
										in_array($atts['position'], array('top', 'bottom')) ? $atts['size'] : ''
									)
								);
		}
		ob_start();
		trx_addons_get_template_part(array(
										TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'layouts/tpl.'.trx_addons_esc($atts['type']).'.php',
                                        TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'layouts/tpl.default.php'
                                        ),
                                        'trx_addons_args_sc_layouts',
                                        $atts
                                    );
		$output = ob_get_contents();
		ob_end_clean();

		// Remove init classes from the output in the popup
		if (in_array($atts['type'], array('popup', 'panel'))) {
			$output = str_replace(  'wp-audio-shortcode',
									'wp-audio-shortcode-noinit',
									$output
									);
			trx_addons_add_inline_html(apply_filters('trx_addons_sc_output', $output, 'trx_sc_layouts', $atts, $content));
			return '';
		} else {
			return apply_filters('trx_addons_sc_output', $output, 'trx_sc_layouts', $atts, $content);
		}
	}
}


// Add [trx_sc_layouts] in the VC shortcodes list
if (!function_exists('trx_addons_sc_layouts_add_in_vc')) {
	function trx_addons_sc_layouts_add_in_vc() {

		add_shortcode("trx_sc_layouts", "trx_addons_sc_layouts");

	    if (!trx_addons_exists_vc()) return;

		vc_lean_map( "trx_sc_layouts", 'trx_addons_sc_layouts_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Layouts extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_layouts_add_in_vc', 20);
}


// Return params
if (!function_exists('trx_addons_sc_layouts_add_in_vc_params')) {
	function trx_addons_sc_layouts_add_in_vc_params() {
		// If open params in VC Editor
		list($vc_edit, $vc_params) = trx_addons_get_vc_form_params('trx_sc_layouts');
		$layouts = trx_addons_array_merge(	array(
												0 => __('- Use content -', 'trx_addons')
											),
											trx_addons_get_list_posts(false, array(
														'post_type' => TRX_ADDONS_CPT_LAYOUTS_PT,
														'meta_key' => 'trx_addons_layout_type',
														'meta_value' => 'custom',
														'not_selected' => false
														)));
		$default = trx_addons_array_get_first($layouts);
		$layout = $vc_edit && !empty($vc_params['layout']) ? $vc_params['layout'] : $default;

		return apply_filters('trx_addons_sc_map', array(
				"base" => "trx_sc_layouts",
				"name" => esc_html__("Layouts", 'trx_addons'),
				"description" => wp_kses_data( __("Display previously created custom layouts", 'trx_addons') ),
				"category" => esc_html__('Layouts', 'trx_addons'),
				"icon" => 'icon_trx_sc_layouts',
				"class" => "trx_sc_layouts",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array_merge(
					array(
						array(
							"param_name" => "type",
							"heading" => esc_html__("Type", 'trx_addons'),
							"description" => wp_kses_data( __("Select shortcodes's type", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-4',
							"std" => "default",
							"value" => array_flip(apply_filters('trx_addons_sc_type', trx_addons_get_list_sc_layouts_type(), 'trx_sc_layouts' )),
							"type" => "dropdown"
						),
						array(
							"param_name" => "layout",
							"heading" => esc_html__("Layout", 'trx_addons'),
							"description" => wp_kses_post( __("Select any previously created layout to insert to this page", 'trx_addons')
															. '<br>'
															. sprintf('<a href="%1$s" class="trx_addons_post_editor'.(intval($layout)==0 ? ' trx_addons_hidden' : '').'" target="_blank">%2$s</a>',
																		admin_url( sprintf( "post.php?post=%d&amp;action=edit", $layout ) ),
																		__("Open selected layout in a new tab to edit", 'trx_addons')
																	)
														),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-8',
							'save_always' => true,
							"value" => array_flip($layouts),
							"std" => $default,
							"type" => "dropdown"
						),
						array(
							"param_name" => "position",
							"heading" => esc_html__("Panel position", 'trx_addons'),
							"description" => wp_kses_data( __("Dock the panel to the specified side of the window", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-4',
					        'save_always' => true,
							"value" => array_flip(trx_addons_get_list_sc_layouts_panel_positions()),
							"std" => 'right',
							"type" => "dropdown",
							'dependency' => array(
								'element' => 'type',
								'value' => array('panel')
							)
						),
						array(
							"param_name" => "size",
							"heading" => esc_html__("Size of the panel", 'trx_addons'),
							"description" => wp_kses_data( __("Size (width or height) of the panel", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"value" => 300,
							"type" => "textfield",
							'dependency' => array(
								'element' => 'type',
								'value' => array('panel')
							)
						),
						array(
							"param_name" => "modal",
							"heading" => esc_html__("Modal", 'trx_addons'),
							"description" => wp_kses_data( __("Disable clicks on the rest window area", 'trx_addons') ),
							'edit_field_class' => 'vc_col-sm-4',
							"std" => 0,
							"value" => array(esc_html__("Modal", 'trx_addons') => 1 ),
							"type" => "checkbox",
							'dependency' => array(
								'element' => 'type',
								'value' => array('panel')
							)
						),
						array(
							"param_name" => "show_on_load",
							"heading" => esc_html__("Show on page load", 'trx_addons'),
							"description" => wp_kses_post( __("Display this popup (panel) when the page is loaded", 'trx_addons') ),
							"admin_label" => true,
							'edit_field_class' => 'vc_col-sm-4',
							'save_always' => true,
							'dependency' => array(
								'element' => 'type',
								'value' => array('panel', 'popup')
							),
							"value" => array_flip( trx_addons_get_list_layouts_show_on_load() ),
							"std" => "none",
							"type" => "dropdown"
						),
						array(
							'param_name' => 'content',
							'heading' => esc_html__( 'Content', 'trx_addons' ),
							"description" => wp_kses_data( __("Alternative content to be used instead of the selected layout", 'trx_addons') ),
							'value' => '',
							'holder' => 'div',
							'type' => 'textarea_html',
						)
					),
					trx_addons_vc_add_id_param()
				)
			), 'trx_sc_layouts' );
	}
}




// Elementor Widget
//------------------------------------------------------
if (!function_exists('trx_addons_sc_layouts_add_in_elementor')) {
	add_action( 'elementor/widgets/widgets_registered', 'trx_addons_sc_layouts_add_in_elementor' );
	function trx_addons_sc_layouts_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Layouts_Widget')) return;	

		class TRX_Addons_Elementor_Widget_Layouts extends TRX_Addons_Elementor_Layouts_Widget {

			/**
			 * Widget base constructor.
			 *
			 * Initializing the widget base class.
			 *
			 * @since 1.6.44
			 * @access public
			 *
			 * @param array      $data Widget data. Default is an empty array.
			 * @param array|null $args Optional. Widget default arguments. Default is null.
			 */
			public function __construct( $data = [], $args = null ) {
				parent::__construct( $data, $args );
				$this->add_plain_params([
					'size' => 'size+unit',
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
				return 'trx_sc_layouts';
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
				return __( 'Layouts', 'trx_addons' );
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
				return 'eicon-gallery-masonry';
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
				// If open params in Elementor Editor
				$params = $this->get_sc_params();
				// Prepare lists
				$layouts = trx_addons_array_merge(	array(
														0 => __('- Use content -', 'trx_addons')
														),
													trx_addons_get_list_posts(false, array(
																'post_type' => TRX_ADDONS_CPT_LAYOUTS_PT,
																'meta_key' => 'trx_addons_layout_type',
																'meta_value' => 'custom',
																'not_selected' => false
																)));
				$default = trx_addons_array_get_first($layouts);
				$layout = !empty($params['layout']) ? $params['layout'] : $default;

				$this->start_controls_section(
					'section_sc_layouts',
					[
						'label' => __( 'Layouts', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Type', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_type', trx_addons_get_list_sc_layouts_type(), 'trx_sc_layouts'),
						'default' => 'default'
					]
				);

				$this->add_control(
					'layout', 
					[
						'label' => __("Layout", 'trx_addons'),
						'label_block' => false,
						'description' => wp_kses_post( __("Select any previously created layout to insert to this page", 'trx_addons')
														. '<br>'
														. sprintf('<a href="%1$s" class="trx_addons_post_editor'.(intval($layout)==0 ? ' trx_addons_hidden' : '').'" target="_blank">%2$s</a>',
																	admin_url( sprintf( "post.php?post=%d&amp;action=elementor", $layout ) ),
																	__("Open selected layout in a new tab to edit", 'trx_addons')
																)
													),
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => $layouts,
						'default' => $default
					]
				);

				$this->add_control(
					'position', 
					[
						'label' => __("Panel position", 'trx_addons'),
						'label_block' => false,
						'description' => wp_kses_data( __("Dock the panel to the specified side of the window", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => trx_addons_get_list_sc_layouts_panel_positions(),
						'default' => 'right',
						'condition' => ['type' => 'panel']
					]
				);
				
				$this->add_control(
					'size',
					[
						'label' => __( 'Size', 'trx_addons' ),
						'description' => wp_kses_data( __("Size (width or height) of the panel", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 300,
							'unit' => 'px'
						],
						'range' => [
							'%' => [
								'min' => 5,
								'max' => 100
							],
							'px' => [
								'min' => 50,
								'max' => 1920
							]
						],
						'size_units' => ['%', 'px'],
						'condition' => ['type' => 'panel']
					]
				);

				$this->add_control(
					'modal',
					[
						'label' => __( 'Modal', 'trx_addons' ),
						'label_block' => false,
						'description' => wp_kses_data( __("Disable clicks on the rest window area", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
						'condition' => ['type' => 'panel']
					]
				);

				$this->add_control(
					'show_on_load', 
					[
						'label' => __("Show on load", 'trx_addons'),
						'label_block' => false,
						'description' => wp_kses_data( __("Display this popup (panel) when the page is loaded", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => trx_addons_get_list_layouts_show_on_load(),
						'default' => 'none',
						'condition' => [
							'type' => ['popup', 'panel']
						]
					]
				);

				$this->add_control(
					'popup_id',
					[
						'label' => __( "Popup (panel) ID", 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( "Popup (panel) ID is required!", 'trx_addons' ),
						'default' => '',
						'condition' => [
							'type' => ['popup', 'panel']
						]
					]
				);

				$this->add_control(
					'content',
					[
						'label' => __( 'Content', 'trx_addons' ),
						'label_block' => true,
						"description" => wp_kses_data( __("Alternative content to be used instead of the selected layout", 'trx_addons') ),
						'type' => \Elementor\Controls_Manager::WYSIWYG,
						'default' => '',
						'separator' => 'none',
						'condition' => ['layout' => '0']
					]
				);
				
				$this->end_controls_section();
			}
		}
		
		// Register widget
		\Elementor\Plugin::$instance->widgets_manager->register_widget_type( new TRX_Addons_Elementor_Widget_Layouts() );
	}
}

// Disable our widgets (shortcodes) to use in Elementor
// because we create special Elementor's widgets instead
if (!function_exists('trx_addons_sc_layouts_black_list')) {
	add_action( 'elementor/widgets/black_list', 'trx_addons_sc_layouts_black_list' );
	function trx_addons_sc_layouts_black_list($list) {
		$list[] = 'TRX_Addons_SOW_Widget_Layouts';
		return $list;
	}
}



// SOW Widget
//------------------------------------------------------
if (class_exists('TRX_Addons_SOW_Widget')) {
	class TRX_Addons_SOW_Widget_Layouts extends TRX_Addons_SOW_Widget {
		
		function __construct() {
			parent::__construct(
				'trx_addons_sow_widget_layouts',
				esc_html__('ThemeREX Layouts', 'trx_addons'),
				array(
					'classname' => 'widget_layouts',
					'description' => __('Display previously created layout (header, footer, etc.)', 'trx_addons')
				),
				array(),
				false,
				TRX_ADDONS_PLUGIN_DIR
			);
	
		}

		// Return array with all widget's fields
		function get_widget_form() {
			// If open params in SOW Editor
			list($vc_edit, $vc_params) = trx_addons_get_sow_form_params('TRX_Addons_SOW_Widget_Layouts');
			// Prepare lists
			$layouts = trx_addons_array_merge(	array(
													0 => __('- Use content -', 'trx_addons')
													),
												trx_addons_get_list_posts(false, array(
															'post_type' => TRX_ADDONS_CPT_LAYOUTS_PT,
															'meta_key' => 'trx_addons_layout_type',
															'meta_value' => 'custom',
															'not_selected' => false
															)));
			$default = trx_addons_array_get_first($layouts);
			$layout = $vc_edit && !empty($vc_params['layout']) ? $vc_params['layout'] : $default;
			return apply_filters('trx_addons_sow_map', array_merge(
				array(
					'type' => array(
						'label' => __('Type', 'trx_addons'),
						"description" => wp_kses_data( __("Select shortcodes's type", 'trx_addons') ),
						'default' => 'default',
						'options' => apply_filters('trx_addons_sc_type', trx_addons_get_list_sc_layouts_type(), $this->get_sc_name()),
						'type' => 'select',
						'state_emitter' => array(
							'callback' => 'conditional',
							'args'     => array(
								'use_side[default]: val!="panel"',
								'use_side[hide]: val=="panel"',
							)
						)
					),
					"layout" => array(
						"label" => esc_html__("Layout", 'trx_addons'),
						"description" => wp_kses_post( __("Select any previously created layout to insert to this page", 'trx_addons')
															. '<br>'
															. sprintf('<a href="%1$s" class="trx_addons_post_editor'.(intval($layout)==0 ? ' trx_addons_hidden' : '').'" target="_blank">%2$s</a>',
																		admin_url( sprintf( "post.php?post=%d&amp;action=edit", $layout ) ),
																		__("Open selected layout in a new tab to edit", 'trx_addons')
																	)
														),
						"options" => $layouts,
						"type" => "select"
					),
					"position" => array(
						"label" => esc_html__("Panel position", 'trx_addons'),
						'description' => wp_kses_data( __("Dock the panel to the specified side of the window", 'trx_addons') ),
						"options" => trx_addons_get_list_sc_layouts_panel_positions(),
						"default" => "right",
						"type" => "select",
						'state_handler' => array(
							"use_side[default]" => array('show'),
							"use_side[hide]" => array('hide')
						),
					),
					"size" => array(
						"label" => esc_html__("Size of the panel", 'trx_addons'),
						"description" => wp_kses_data( __("Size (width or height) of the panel", 'trx_addons') ),
						"default" => "300px",
						"type" => "measurement",
						'state_handler' => array(
							"use_side[default]" => array('show'),
							"use_side[hide]" => array('hide')
						),
					),
					"modal" => array(
						"label" => esc_html__("Modal", 'trx_addons'),
						"description" => wp_kses_data( __("Disable clicks on the rest window area", 'trx_addons') ),
						"default" => false,
						"type" => "checkbox",
						'state_handler' => array(
							"use_side[default]" => array('show'),
							"use_side[hide]" => array('hide')
						),
					),
					"content" => array(
						"label" => esc_html__("Content", 'trx_addons'),
						"description" => wp_kses_data( __("Alternative content to be used instead of the selected layout", 'trx_addons') ),
						"type" => "textarea"
					)
				),
				trx_addons_sow_add_id_param()
			), $this->get_sc_name());
		}

	}
	siteorigin_widget_register('trx_addons_sow_widget_layouts', __FILE__, 'TRX_Addons_SOW_Widget_Layouts');
}
?>