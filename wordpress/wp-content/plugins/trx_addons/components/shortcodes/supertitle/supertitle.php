<?php
/**
 * Shortcode: Super title
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.49
 */


if ( ! defined( 'ABSPATH' ) ) { exit; }



// Merge shortcode's specific styles to the single stylesheet
if ( !function_exists( 'trx_addons_sc_supertitle_merge_styles' ) ) {
	add_filter('trx_addons_filter_merge_styles', 'trx_addons_sc_supertitle_merge_styles');
	function trx_addons_sc_supertitle_merge_styles($list) {
		$list[] = TRX_ADDONS_PLUGIN_SHORTCODES . 'supertitle/_supertitle.scss';
		return $list;
	}
}

// Merge shortcode's specific styles to the single stylesheet (responsive)
if ( !function_exists( 'trx_addons_sc_supertitle_merge_styles_responsive' ) ) {
	add_filter('trx_addons_filter_merge_styles_responsive', 'trx_addons_sc_supertitle_merge_styles_responsive');
	function trx_addons_sc_supertitle_merge_styles_responsive($list) {
		$list[] = TRX_ADDONS_PLUGIN_SHORTCODES . 'supertitle/_supertitle.responsive.scss';
		return $list;
	}
}

// Check if there are side title
if ( !function_exists( 'trx_addons_sc_supertitle_has_side_title' ) ) {
	function trx_addons_sc_supertitle_has_side_title($sc_args) {
		$side = is_rtl() ? 'left' : 'right';
		if (!empty($sc_args['items']) && in_array( $side, array_column($sc_args['items'], 'align'))) {
			return true;
		}
		return false;
	}
}


// [trx_sc_supertitle]
//-------------------------------------------------------------
/*
[trx_sc_supertitle id="unique_id" "type" => "default"]
*/
if ( !function_exists( 'trx_addons_sc_supertitle' ) ) {
	function trx_addons_sc_supertitle($atts, $content=null) {
		$atts = trx_addons_sc_prepare_atts('trx_sc_supertitle', $atts, array(
				// Individual params
				'type' => 'default',
				'icon_column' => 8,
				'header_column' => 8,
				'items' => '',
				'icon' => '',
				'icon_color' => '',
				'icon_size' => '',
				'icon_bg_color' => '',
				'image' => '',
				// Common params
				'id' => '',
				'class' => '',
				'css' => ''
			)
		);
		$atts['header_column'] = max(0, min($atts['header_column'], 11));
		if (function_exists('vc_param_group_parse_atts') && !is_array($atts['items']))
			$atts['items'] = (array) vc_param_group_parse_atts( $atts['items'] );
		$output = '';
		if (is_array($atts['items']) && count($atts['items']) > 0) {
			$output = trx_addons_get_template_part_as_string(array(
				TRX_ADDONS_PLUGIN_SHORTCODES . 'supertitle/tpl.'.trx_addons_esc($atts['type']).'.php',
				TRX_ADDONS_PLUGIN_SHORTCODES . 'supertitle/tpl.default.php'
				),
				'trx_addons_args_sc_supertitle',
				$atts
			);
		}
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_supertitle', $atts, $content);
	}
}


// Add [trx_sc_supertitle] in the VC shortcodes list
if (!function_exists('trx_addons_sc_supertitle_add_in_vc')) {
	function trx_addons_sc_supertitle_add_in_vc() {

		add_shortcode('trx_sc_supertitle', 'trx_addons_sc_supertitle');

		if (!trx_addons_exists_vc()) return;

		vc_lean_map('trx_sc_supertitle', 'trx_addons_sc_supertitle_add_in_vc_params');
		class WPBakeryShortCode_Trx_Sc_Supertitle extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_supertitle_add_in_vc', 20);
}

// Return params
if (!function_exists('trx_addons_sc_supertitle_add_in_vc_params')) {
	function trx_addons_sc_supertitle_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				'base' => 'trx_sc_supertitle',
				'name' => esc_html__('Super Title', 'trx_addons'),
				'description' => wp_kses_data( __("Insert 'Super Title' element", 'trx_addons') ),
				'category' => esc_html__('ThemeREX', 'trx_addons'),
				'icon' => 'icon_trx_sc_supertitle',
				'class' => 'trx_sc_supertitle',
				'content_element' => true,
				'is_container' => false,
				'show_settings_on_create' => true,
				'params' => array_merge(
					array(
						array(
							'param_name' => 'type',
							'heading' => esc_html__('Layout', 'trx_addons'),
							'description' => wp_kses_data( __("Select shortcodes's layout", 'trx_addons') ),
							'admin_label' => true,
							'save_always' => true,
							'edit_field_class' => 'vc_col-sm-6',
							'std' => 'default',
							'value' => array_flip(apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'supertitle'), 'trx_sc_supertitle')),
							'type' => 'dropdown'
						),
						array(
							'param_name' => 'icon_column',
							'heading' => esc_html__('Icon column size', 'trx_addons'),
							'description' => wp_kses_data( __("Specify width of the icon column from 0 (no icon column) to 6", 'trx_addons') ),
							'save_always' => true,
							'edit_field_class' => 'vc_col-sm-6',
							'std' => '1',
							'type' => 'textfield'
						),
						array(
							'param_name' => 'header_column',
							'heading' => esc_html__('Left column size', 'trx_addons'),
							'description' => wp_kses_data( __("Specify width of the main column from 0 (no main column) to 12. Attention! Summ Icon column + Main column must be less or equal to 12", 'trx_addons') ),
							'save_always' => true,
							'edit_field_class' => 'vc_col-sm-6',
							'std' => '8',
							'type' => 'textfield'
						),
						array(
							'param_name' => 'image',
							'heading' => esc_html__('Choose media', 'trx_addons'),
							'description' => wp_kses_data( __('Select or upload image or specify URL from other site to use it as icon', 'trx_addons') ),
							'type' => 'attach_image'
						),
					),
					trx_addons_vc_add_icon_param(''),
					array(
						array(
							'param_name' => 'icon_color',
							'heading' => esc_html__( 'Color', 'trx_addons' ),
							'description' => esc_html__( 'Selected color will be applied to the Super Title icon or border (if no icon selected)', 'trx_addons' ),
							'edit_field_class' => 'vc_col-sm-4',
							'std' => '',
							'type' => 'colorpicker'
						),
						array(
							'param_name' => 'icon_bg_color',
							'heading' => esc_html__( 'Background color', 'trx_addons' ),
							'description' => esc_html__( 'Selected background color will be applied to the Super Title icon', 'trx_addons' ),
							'edit_field_class' => 'vc_col-sm-4',
							'std' => '',
							'type' => 'colorpicker'
						),
						array(
							'param_name' => 'icon_size',
							'heading' => esc_html__( 'Icon size or image width', 'trx_addons' ),
							'description' => esc_html__( 'For example, use 14px or 1em.', 'trx_addons' ),
							'admin_label' => true,
							'edit_field_class' => 'vc_col-sm-4',
							'type' => 'textfield',
						),
						array(
							'type' => 'param_group',
							'param_name' => 'items',
							'heading' => esc_html__( 'Items', 'trx_addons' ),
							'description' => wp_kses_data( __('Select icons, specify title and/or description for each item', 'trx_addons') ),
							'value' => urlencode( json_encode( apply_filters('trx_addons_sc_param_group_value', array(
								array(
									'item_type' => 'text',
									'text' => esc_html__( 'Main title', 'trx_addons' ),
									'align' => 'left',
									'item_icon' => '',
									'color' => '',
									'color2' => '',
									'gradient_direction' => '0',
								),
							), 'trx_sc_supertitle') ) ),
							'params' => apply_filters('trx_addons_sc_param_group_params', array_merge(array(
								array(
									'param_name' => 'item_type',
									'heading' => esc_html__('Item Type', 'trx_addons'),
									'description' => wp_kses_data( __('Select type of the item', 'trx_addons') ),
									'admin_label' => true,
									'edit_field_class' => 'vc_col-sm-6',
									'std' => 'text',
									'value' => array_flip(trx_addons_get_list_sc_supertitle_item_types()),
									'type' => 'dropdown'
								),

								/*
								* Title
								*/
								array(
									'param_name' => 'text',
									'heading' => esc_html__('Text', 'trx_addons'),
									'description' => '',
									'admin_label' => true,
									'edit_field_class' => 'vc_col-sm-12',
									'dependency' => array(
										'element' => 'item_type',
										'value' => 'text'
									),
									'type' => 'textarea_safe'
								),
								array(
									'param_name' => 'link',
									'heading' => esc_html__( 'Link text', 'trx_addons' ),
									'description' => esc_html__( 'Specify link for the text', 'trx_addons' ),
									'dependency' => array(
										'element' => 'item_type',
										'value' => 'text'
									),
									'edit_field_class' => 'vc_col-sm-12',
									'admin_label' => true,
									'type' => 'textfield',
								),
								array(
									'param_name' => 'new_window',
									'heading' => esc_html__('Open in the new tab', 'trx_addons'),
									'description' => wp_kses_data( __("Open this link in the new browser's tab", 'trx_addons') ),
									'edit_field_class' => 'vc_col-sm-12',
									'dependency' => array(
										'element' => 'item_type',
										'value' => 'text'
									),
									'std' => 0,
									'value' => array(esc_html__('Open in the new tab', 'trx_addons') => 1 ),
									'type' => 'checkbox'
								),
								array(
									'param_name' => 'tag',
									'heading' => esc_html__('HTML Tag', 'trx_addons'),
									'description' => wp_kses_data( __('Select HTML wrapper of the item', 'trx_addons') ),
									'edit_field_class' => 'vc_col-sm-6',
									'std' => 'h2',
									'dependency' => array(
										'element' => 'item_type',
										'value' => 'text'
									),
									'value' => array_flip(trx_addons_get_list_sc_title_tags('', true)),
									'type' => 'dropdown'
								),

								/*
								* Media
								*/
								array(
									'param_name' => 'media',
									'heading' => esc_html__('Choose media', 'trx_addons'),
									'description' => wp_kses_data( __('Select or upload image or specify URL from other site to use it as icon', 'trx_addons') ),
									'edit_field_class' => 'vc_col-sm-12',
									'dependency' => array(
										'element' => 'item_type',
										'value' => 'media'
									),
									'type' => 'attach_image'
								),

								/*
								* Icon
								*/
								array(
									'param_name' => 'item_icon',
									'heading' => esc_html__('Icon', 'trx_addons'),
									'description' => wp_kses_data( __('Select icon', 'trx_addons') ),
									'value' => trx_addons_get_list_icons(trx_addons_get_setting('icons_type')),
									'edit_field_class' => 'vc_col-sm-12',
									'std' => '',
									'dependency' => array(
										'element' => 'item_type',
										'value' => 'icon'
									),
									'style' => trx_addons_get_setting('icons_type'),
									'type' => 'icons'
								),
								array(
									'param_name' => 'size',
									'heading' => esc_html__( 'Size', 'trx_addons' ),
									'description' => esc_html__( 'For example, use 14px or 1em.', 'trx_addons' ),
									'admin_label' => true,
									'edit_field_class' => 'vc_col-sm-12',
									'dependency' => array(
										'element' => 'item_type',
										'value' => 'icon'
									),
									'type' => 'textfield',
								),

								array(
									'param_name' => 'float_position',
									'heading' => esc_html__('Float', 'trx_addons'),
									'description' => '',
									'edit_field_class' => 'vc_col-sm-6',
									'std' => 'left',
									'dependency' => array(
											'element' => 'item_type',
											'value' => array('icon', 'media')
									),
									'value' => array_flip(trx_addons_get_list_sc_aligns(false, false)),
									'type' => 'dropdown'
								),

								/*
								* Common
								*/
								array(
									'param_name' => 'align',
									'heading' => esc_html__('Alignment', 'trx_addons'),
									'description' => '',
									'edit_field_class' => 'vc_col-sm-6',
									'std' => 'left',
									'value' => apply_filters('trx_addons_sc_supertitle_item_type', array(
										__( 'Left', 'trx_addons' ) => 'left',
										__( 'Right', 'trx_addons' ) => 'right',
									)),
									'type' => 'dropdown'
								),
								array(
									'param_name' => 'inline',
									'heading' => esc_html__('Inline', 'trx_addons'),
									'description' => '',
									'edit_field_class' => 'vc_col-sm-12',
									'std' => 0,
									'value' => array(esc_html__('Make it inline', 'trx_addons') => 1 ),
									'type' => 'checkbox'
								),
								array(
									'param_name' => 'color',
									'heading' => esc_html__( 'Color', 'trx_addons' ),
									'description' => esc_html__( 'Selected color will also be applied to the subtitle.', 'trx_addons' ),
									'edit_field_class' => 'vc_col-sm-4',
									'std' => '',
									'dependency' => array(
										'element' => 'item_type',
										'value' => array('icon', 'text')
									),
									'type' => 'colorpicker'
								),
								array(
									'param_name' => 'color2',
									'heading' => esc_html__( 'Color 2', 'trx_addons' ),
									'description' => esc_html__( 'If not empty - used for gradient.', 'trx_addons' ),
									'edit_field_class' => 'vc_col-sm-4',
									'std' => '',
									'dependency' => array(
										'element' => 'item_type',
										'value' => array('text')
									),
									'type' => 'colorpicker'
								),
								array(
									'param_name' => 'gradient_direction',
									'heading' => esc_html__( 'Gradient direction', 'trx_addons' ),
									'description' => esc_html__( 'Gradient direction in degress (0 - 360)', 'trx_addons' ),
									'admin_label' => true,
									'edit_field_class' => 'vc_col-sm-4',
									'std' => '',
									'dependency' => array(
										'element' => 'color2',
										'not_empty' => true
									),
									'type' => 'textfield',
								),
							) ), 'trx_sc_supertitle')
						)
					),
						trx_addons_vc_add_id_param()
					)
				), 'trx_sc_supertitle'
			);
	}
}


// Elementor Widget
//------------------------------------------------------

if (!function_exists('trx_addons_sc_supertitle_add_in_trx_addons')) {
	add_action( 'elementor/widgets/widgets_registered', 'trx_addons_sc_supertitle_add_in_trx_addons' );
	function trx_addons_sc_supertitle_add_in_trx_addons() {

		if (!class_exists('TRX_Addons_Elementor_Widget')) return;

		class TRX_Addons_Elementor_Widget_Supertitle extends TRX_Addons_Elementor_Widget {


			/**
			 * Widget base constructor.
			 *
			 * Initializing the widget base class.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @param array			$data Widget data. Default is an empty array.
			 * @param array|null	$args Optional. Widget default arguments. Default is null.
			 */
			public function __construct( $data = [], $args = null ) {
				parent::__construct( $data, $args );
				$this->add_plain_params([
					'size' => 'size+unit',
					'icon_size' => 'size+unit',
					'icon_column' => 'size',
					'header_column' => 'size',
					'gradient_direction' => 'size'
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
				return 'trx_sc_supertitle';
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
				return __( 'Super Title', 'trx_addons' );
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
					'section_sc_supertitle',
					[
						'label' => __( 'Super Title', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'supertitle'), 'trx_sc_supertitle'),
						'default' => 'default'
					]
				);

				$this->add_control(
					'icon_column',
					[
						'label' => __( 'Icon column size', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'description' => wp_kses_data( __("Specify width of the ison (left) column from 0 (no left column) to 6", 'trx_addons') ),
						'default' => [
							'size' => 1,
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 6
							]
						],
					]
				);

				$this->add_control(
					'header_column',
					[
						'label' => __( 'Main (middle) column size', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'description' => wp_kses_data( __("Specify width of the main (middle) column from 0 (no left column) to 12. Attention! Summ Icon column + Main column must be less or equal to 12", 'trx_addons') ),
						'default' => [
							'size' => 8,
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 12
							]
						],
					]
				);

				$this->add_control(
					'image',
					[
						'label' => __( 'Choose media', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::MEDIA,
						'default' => [
							'url' => '',
						]
					]
				);

				$this->add_icon_param();

				$this->add_control(
					'icon_color',
					[
						'label' => __( 'Color', 'elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
//						'global' => array(
//							'active' => false,
//						),
						'default' => '',
						'selectors' => [
							'{{WRAPPER}} .sc_supertitle_no_icon' => 'background-color: {{VALUE}};',
							'{{WRAPPER}} .sc_icon_type_icons' => 'color: {{VALUE}};',
						]
					]
				);

				$this->add_control(
					'icon_bg_color',
					[
						'label' => __( 'Background color', 'elementor' ),
						'type' => \Elementor\Controls_Manager::COLOR,
//						'global' => array(
//							'active' => false,
//						),
						'default' => '',
						'selectors' => [
							'{{WRAPPER}} .sc_icon_type_icons' => 'background-color: {{VALUE}}; border-radius: 50%; padding: 20%;',
						]
					]
				);

				$this->add_control(
					'icon_size',
					[
						'label' => __( 'Icon size or Image width', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 3.3,
							'unit' => 'em'
						],
						'size_units' => [ 'em', 'px' ],
						'range' => [
							'em' => [
								'min' => 0,
								'max' => 20
							],
							'px' => [
								'min' => 0,
								'max' => 200
							]
						],
					]
				);

				$this->add_control(
					'items',
					[
						'label' => '',
						'type' => \Elementor\Controls_Manager::REPEATER,
						'default' => apply_filters('trx_addons_sc_param_group_value', [
							[
								'text' => esc_html__( 'Main title', 'trx_addons' ),
								'align' => 'left',
								'tag' => 'h2',
								'item_type' => 'text',
								'color' => '#aa0000',
								'color2' => '',
							],
							[
								'text' => esc_html__( 'Subtitle left', 'trx_addons' ),
								'align' => 'left',
								'tag' => 'h6',
								'item_type' => 'text',
								'color' => '#0000aa',
								'color2' => '',
							],
							[
								'text' => esc_html__( 'Subtitle right', 'trx_addons' ),
								'align' => 'right',
								'tag' => 'h5',
								'item_type' => 'text',
								'color' => '#00aa00',
								'color2' => '',
							],
						], 'trx_sc_supertitle'),

						'fields' => apply_filters('trx_addons_sc_param_group_params', array_merge(
							[
								[
									'name' => 'item_type',
									'label' => __( 'Item Type', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::SELECT,
									'options' => trx_addons_get_list_sc_supertitle_item_types(),
									'default' => 'text'
								],


								/*
								* Title
								 */
								[
									'name' => 'text',
									'label' => __( 'Text', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::TEXTAREA,
									'dynamic' => [
										'active' => true,
									],
									'placeholder' => __( 'Enter your text', 'trx_addons' ),
									'default' => __( 'Add Your Super Title Text Here', 'trx_addons' ),
									'condition' => [
										'item_type' => 'text'
									]
								],
								[
									'name' => 'link',
									'label' => __( 'Link', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::URL,
									'dynamic' => [
										'active' => true,
									],
									'placeholder' => __( 'https://your-link.com', 'trx_addons' ),
									'default' => [
										'url' => '',
									],
									'separator' => 'before',
									'condition' => [
										'item_type' => 'text'
									]
								],
								[
									'name' => 'tag',
									'label' => __( 'HTML Tag', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::SELECT,
									'options' => trx_addons_get_list_sc_title_tags('', true),
									'default' => 'h2',
									'condition' => [
										'item_type' => 'text'
									]
								],

								/*
								* Media
								 */
								[
									'name' => 'media',
									'label' => __( 'Choose Image', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::MEDIA,
									'default' => [
										'url' => '',
									],
									'condition' => [
										'item_type' => 'media'
									]
								],

								/*
								* Icon
								 */
								[
									'name' => 'item_icon',
									'label' =>  __('Icon', 'trx_addons'),
									'type' => 'icons',
									'label_block' => false,
									'default' => '',
									'options' => trx_addons_get_list_icons(trx_addons_get_setting('icons_type')),
									'condition' => [
										'item_type' => 'icon'
									],
								],
								[
									'name' => 'size',
									'label' => __( 'Icon Size', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::SLIDER,
									'default' => [
										'size' => 5,
										'unit' => 'em'
									],
									'size_units' => [ 'em', 'px' ],
									'range' => [
										'em' => [
											'min' => 0,
											'max' => 100
										],
										'px' => [
											'min' => 0,
											'max' => 1000
										]
									],
									'condition' => [
										'item_type' => 'icon'
									],
								],
								[
									'name' => 'color',
									'label' => __( 'Color', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::COLOR,
									'default' => '',
									'description' => '',
			//						'global' => array(
			//							'active' => false,
			//						),
									'condition' => [
										'item_type' => ['text', 'icon']
									]
								],
								[
									'name' => 'color2',
									'label' => __( 'Color 2', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::COLOR,
									'default' => '',
									'description' => '',
			//						'global' => array(
			//							'active' => false,
			//						),
									'condition' => [
										'item_type' => ['text']
									]
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
										'item_type' => 'text',
										'color2!' => ''
									],
								],
								[
									'name' => 'float_position',
									'label' => __( 'Float', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::SELECT,
									'options' => trx_addons_get_list_sc_aligns(false, false),
									'default' => 'left',
									'condition' => [
										'item_type' => ['media', 'icon']
									]
								],

								/*
								* Common
								 */
								[
									'name' => 'align',
									'label' => __( 'Alignment', 'trx_addons' ),
									'type' => \Elementor\Controls_Manager::CHOOSE,
									'options' => [
										'left' => [
											'title' => __( 'Left', 'trx_addons' ),
											'icon' => 'fa fa-align-left',
										],
										'right' => [
											'title' => __( 'Right', 'trx_addons' ),
											'icon' => 'fa fa-align-right',
										],
									],
									'default' => 'left',
								],
								[
									'name' => 'inline',
									'label' => __( 'Inline', 'trx_addons' ),
									'label_block' => false,
									'type' => \Elementor\Controls_Manager::SWITCHER,
									'label_off' => __( 'Off', 'trx_addons' ),
									'label_on' => __( 'On', 'trx_addons' ),
									'default' => '',
									'return_value' => '1'
								],

							]), 'trx_sc_supertitle'),
						'title_field' => '{{{ item_type }}}: {{{ align }}}',
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
				trx_addons_get_template_part(TRX_ADDONS_PLUGIN_SHORTCODES . 'supertitle/tpe.default.php',
					'trx_addons_args_sc_supertitle',
					array('element' => $this)
				);
			}

		}

		// Register widget
		\Elementor\Plugin::$instance->widgets_manager->register_widget_type( new TRX_Addons_Elementor_Widget_Supertitle() );
	}
}
