<?php
/**
 * Shortcode: Blog item parts
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.50
 */
	
// Merge shortcode specific styles into single stylesheet
if ( !function_exists( 'trx_addons_sc_layouts_blog_item_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_sc_layouts_blog_item_merge_styles');
	add_filter("trx_addons_filter_merge_styles_layouts", 'trx_addons_sc_layouts_blog_item_merge_styles');
	function trx_addons_sc_layouts_blog_item_merge_styles($list) {
		$list[] = TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'blog_item/_blog_item.scss';
		return $list;
	}
}


// trx_sc_layouts_blog_item
//-------------------------------------------------------------
/*
[trx_sc_layouts_blog_item id="unique_id"]
*/
if ( !function_exists( 'trx_addons_sc_layouts_blog_item' ) ) {
	function trx_addons_sc_layouts_blog_item($atts, $content=null){
		$atts = trx_addons_sc_prepare_atts('trx_sc_layouts_blog_item', $atts, array(
				// Individual params
				"type" => "",
				"thumb_bg" => 0,
				"thumb_ratio" => "16:9",
				"thumb_mask" => '#000',
				"thumb_mask_opacity" => 0.3,
				"thumb_hover_mask" => '#000',
				"thumb_hover_opacity" => 0.1,
				"thumb_size" => "full",
				"title_tag" => "h4",
				"meta_parts" => "",
				"counters" => "",
				"custom_meta_key" => "",
				"button_text" => __("Read more", 'trx_addons'),
				"button_link" => "post",
				"button_type" => "default",
				"seo" => "",
				"position" => "static",
				"hide_overflow" => 0,
				"animation_in" => 'none',
				"animation_out" => 'none',
				"text_color" => '',
				"text_hover" => '',
				"font_zoom" => 1,
				"post_type" => array(),
				// Common params
				"id" => "",
				"class" => "",
				"css" => "",
			)
		);
		
		$output = '';

		$is_preview = function_exists('trx_addons_elm_is_preview') && trx_addons_elm_is_preview() && !trx_addons_sc_stack_check('trx_sc_blogger');

		if (!empty($atts['post_type']) && $is_preview) {
			if (!is_array($atts['post_type'])) {
				$atts['post_type'] = array($atts['post_type']);
			}
			$atts['post_type'][] = TRX_ADDONS_CPT_LAYOUTS_PT;
		}

		if ( empty($atts['post_type']) || in_array( get_post_type(), $atts['post_type'] ) ) {
			ob_start();
			trx_addons_get_template_part( array(
												TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'blog_item/tpl.'.trx_addons_esc($atts['type']).'.php',
												TRX_ADDONS_PLUGIN_CPT_LAYOUTS_SHORTCODES . 'blog_item/tpl.default.php'
											),
											'trx_addons_args_sc_layouts_blog_item',
											$atts
										);
			$output = ob_get_contents();
			ob_end_clean();
		}
		return apply_filters('trx_addons_sc_output', $output, 'trx_sc_layouts_blog_item', $atts, $content);
	}
}




// Elementor Widget
//------------------------------------------------------
if (!function_exists('trx_addons_sc_layouts_blog_item_add_in_elementor')) {
	add_action( 'elementor/widgets/widgets_registered', 'trx_addons_sc_layouts_blog_item_add_in_elementor' );
	function trx_addons_sc_layouts_blog_item_add_in_elementor() {
		
		if (!class_exists('TRX_Addons_Elementor_Widget')) return;	

		class TRX_Addons_Elementor_Widget_Layouts_Blog_Item extends TRX_Addons_Elementor_Widget {

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
					'thumb_mask_opacity' => 'size',
					'thumb_hover_opacity' => 'size',
					'font_zoom' => 'size',
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
				return 'trx_sc_layouts_blog_item';
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
				return __( 'Layouts: Blog item parts', 'trx_addons' );
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
				return 'eicon-image-box';
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

				$post_types = trx_addons_get_list_posts_types();
				$meta_parts = apply_filters('trx_addons_filter_get_list_meta_parts', array());
				$counters = apply_filters('trx_addons_filter_get_list_counters', array());

				$this->start_controls_section(
					'section_sc_layouts_blog_item',
					[
						'label' => __( 'Blog item part', 'trx_addons' ),
					]
				);

				$this->add_control(
					'type',
					[
						'label' => __( 'Layout', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => trx_addons_get_list_sc_layouts_blog_item_parts(),
						'default' => 'title',
					]
				);

				// Featured params
				$this->add_control(
					'thumb_bg',
					[
						'label' => __( 'Use as background', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
						'condition' => [
							'type' => 'featured'
						]
					]
				);

				$this->add_control(
					'thumb_ratio',
					[
						'label' => __( 'Image ratio', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => '16:9',
						'condition' => [
							'thumb_bg' => '1'
						]
					]
				);

				$this->add_control(
					'thumb_size',
					[
						'label' => __( 'Image size', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => trx_addons_get_list_thumbnail_sizes(),
						'default' => 'full',
						'condition' => [
							'type' => 'featured',
							'thumb_bg' => ''
						]
					]
				);

				$this->add_control(
					'thumb_mask',
					[
						'label' => __( 'Image mask color', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'condition' => [
							'type' => 'featured'
						]
					]
				);

				$this->add_control(
					'thumb_mask_opacity',
					[
						'label' => __( 'Image mask opacity', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 0.3,
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 1,
								'step' => 0.05
							],
						],
						'condition' => [
							'type' => 'featured'
						]
					]
				);

				$this->add_control(
					'thumb_hover_mask',
					[
						'label' => __( 'Hovered mask color', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'condition' => [
							'type' => 'featured'
						]
					]
				);

				$this->add_control(
					'thumb_hover_opacity',
					[
						'label' => __( 'Hovered mask opacity', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 0.1,
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 0,
								'max' => 1,
								'step' => 0.05
							],
						],
						'condition' => [
							'type' => 'featured'
						]
					]
				);

				// Title params
				$this->add_control(
					'title_tag',
					[
						'label' => __( 'Title tag', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => trx_addons_get_list_sc_title_tags('', false),
						'default' => 'h4',
						'condition' => [
							'type' => 'title'
						]
					]
				);

				// Meta params
				$this->add_control(
					'meta_parts',
					[
						'label' => __( 'Choose meta parts', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT2,
						'options' => $meta_parts,
						'multiple' => true,
						'default' => 'date',
						'condition' => [
							'type' => 'meta'
						]
					]
				);

				$this->add_control(
					'counters',
					[
						'label' => __( 'Counters', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT2,
						'options' => $counters,
						'multiple' => true,
						'default' => isset($counters[0]) ? $counters[0] : '',
						'condition' => [
							'type' => 'meta'
						]
					]
				);

				// Custom meta params
				$this->add_control(
					'custom_meta_key',
					[
						'label' => __( 'Name of the custom meta', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => '',
						'condition' => [
							'type' => 'custom'
						]
					]
				);

				// Button params
				$this->add_control(
					'button_type',
					[
						'label' => __( 'Button type', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => apply_filters('trx_addons_sc_type', trx_addons_components_get_allowed_layouts('sc', 'button'), 'trx_sc_button'),
						'default' => 'default',
						'condition' => [
							'type' => 'button'
						]
					]
				);

				$this->add_control(
					'button_link',
					[
						'label' => __( 'Button link to', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => [
							'post' => __('Single post', 'trx_addons'),
							'product' => __('Linked product', 'trx_addons'),
							'cart' => __('Add to cart', 'trx_addons'),
						],
						'default' => 'post',
						'condition' => [
							'type' => 'button'
						]
					]
				);

				$this->add_control(
					'button_text',
					[
						'label' => __( 'Button caption', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::TEXT,
						'default' => __('Read more', 'trx_addons'),
						'condition' => [
							'type' => 'button'
						]
					]
				);


				// Common params
				$this->add_control(
					'position',
					[
						'label' => __( 'Position', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => array_merge( array('static' => __('Static', 'trx_addons')), trx_addons_get_list_sc_positions()),
						'default' => 'static',
						'condition' => [
							'type' => ['title', 'meta', 'excerpt', 'custom', 'button'],
						],
						'prefix_class' => 'sc_layouts_blog_item_position_',
					]
				);

				$this->add_control(
					'font_zoom',
					[
						'label' => __( 'Zoom font size', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::SLIDER,
						'default' => [
							'size' => 1,
							'unit' => 'px'
						],
						'size_units' => [ 'px' ],
						'range' => [
							'px' => [
								'min' => 0.3,
								'max' => 3,
								'step' => 0.1
							],
						],
						'condition' => [
							'type' => ['title', 'excerpt', 'content', 'meta', 'custom', 'button'],
						],
						'selectors' => [
							'{{WRAPPER}} .sc_layouts_blog_item' => 'font-size: {{SIZE}}em;',
						]
					]
				);

				$this->add_control(
					'hide_overflow',
					[
						'label' => __( 'Hide overflow', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SWITCHER,
						'label_off' => __( 'Off', 'trx_addons' ),
						'label_on' => __( 'On', 'trx_addons' ),
						'return_value' => '1',
						'condition' => [
							'type' => ['title', 'meta', 'custom'],
							'position' => array_keys(trx_addons_get_list_sc_positions())
						]
					]
				);

				$this->add_control(
					'animation_in',
					[
						'label' => __( 'Hover animation in', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => trx_addons_get_list_animations_in(),
						'default' => 'none',
						'condition' => [
							'type' => ['title', 'meta', 'excerpt', 'custom', 'button'],
							'position' => array_keys(trx_addons_get_list_sc_positions())
						]
					]
				);

				$this->add_control(
					'animation_out',
					[
						'label' => __( 'Hover animation out', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => trx_addons_get_list_animations_out(),
						'default' => 'none',
						'condition' => [
							'type' => ['title', 'meta', 'excerpt', 'custom', 'button'],
							'position' => array_keys(trx_addons_get_list_sc_positions())
						]
					]
				);

				$this->add_control(
					'text_color',
					[
						'label' => __( 'Text color', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'condition' => [
							'type' => ['title', 'meta', 'excerpt', 'custom', 'button']
						]
					]
				);

				$this->add_control(
					'text_hover',
					[
						'label' => __( 'Text color (hovered)', 'trx_addons' ),
						'type' => \Elementor\Controls_Manager::COLOR,
						'default' => '',
//						'global' => array(
//							'active' => false,
//						),
						'condition' => [
							'type' => ['title', 'meta', 'excerpt', 'custom', 'button']
						]
					]
				);

				$this->add_control(
					'post_type',
					[
						'label' => __( 'Supported post types', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT2,
						'options' => $post_types,
						'multiple' => true,
						'default' => ''
					]
				);
				
				$this->end_controls_section();
			}
			
			/**
			 * Get stack.
			 *
			 * Retrieve the widget stack of controls.
			 *
			 * @since 1.9.2
			 * @access public
			 *
			 * @param bool $with_common_controls Optional. Whether to include the common controls. Default is true.
			 *
			 * @return array Widget stack of controls.
			 */
			public function get_stack( $with_common_controls = true ) {
				$stack = parent::get_stack($with_common_controls);
				if ($with_common_controls) {
					$controls_to_change = [
						'_padding', '_padding_tablet', '_padding_mobile',
						'_background_color', '_background_gradient_angle', '_background_gradient_position',
						'_background_image', '_background_position', '_background_attachment', '_background_repeat',
						'_background_size', '_background_video_fallback',
						'_background_hover_color', '_background_hover_gradient_angle', '_background_hover_gradient_position',
						'_background_hover_image', '_background_hover_position', '_background_hover_attachment', '_background_hover_repeat',
						'_background_hover_size', '_background_hover_video_fallback',
						'_border_border', '_border_width', '_border_color', '_border_radius', '_box_shadow_box_shadow',
						'_border_hover_border', '_border_hover_width', '_border_hover_color', '_border_radius_hover', '_box_shadow_hover_box_shadow',
						'_border_hover_transition'
					];
					foreach ($controls_to_change as $control_name) {
						if (!isset($stack['controls'][$control_name])) continue;
						if (isset($stack['controls'][$control_name]['selectors']) && is_array($stack['controls'][$control_name]['selectors'])) {
							$new_selectors = array();
							foreach ($stack['controls'][$control_name]['selectors'] as $k=>$v) {
								$new_selectors[str_replace('.elementor-widget-container', '.elementor-widget-container .sc_layouts_blog_item', $k)] = $v;
							}
							$stack['controls'][$control_name]['selectors'] = $new_selectors;
						}
					}
					// Add unit 'rem'
					$controls_to_change = [
						'_padding', '_padding_tablet', '_padding_mobile',
						'_margin', '_margin_tablet', '_margin_mobile',
					];
					foreach ($controls_to_change as $control_name) {
						if (!isset($stack['controls'][$control_name])) continue;
						if (isset($stack['controls'][$control_name]['size_units']) && is_array($stack['controls'][$control_name]['size_units'])) {
							$stack['controls'][$control_name]['size_units'][] = 'rem';
						}
					}
				}
				return $stack;
			}
		}
		
		// Register widget
		\Elementor\Plugin::$instance->widgets_manager->register_widget_type( new TRX_Addons_Elementor_Widget_Layouts_Blog_Item() );
	}
}


// Change widget's controls behaviour before render
if ( !function_exists( 'trx_addons_sc_layouts_blog_item_change_elm_params' ) ) {
	add_action( 'elementor/frontend/widget/before_render', 'trx_addons_sc_layouts_blog_item_change_elm_params', 10, 1 );
	function trx_addons_sc_layouts_blog_item_change_elm_params($widget) {
		if ( $widget->get_name() == 'trx_sc_layouts_blog_item' ) {
/*
			// Add animations to the wrapper
			// Deprecated! Animation have been applied to the child node '.sc_layouts_blog_item' to avoid conflict CSS tansformations
			$args = $widget->get_settings();
			if ( !wp_is_mobile()
					&& (
						( !empty($args['animation_in']) && !trx_addons_is_off($args['animation_in']) )
						||
						( !empty($args['animation_out']) && !trx_addons_is_off($args['animation_out']) )
						)
			) {
				$widget->add_render_attribute( '_wrapper', 'data-hover-animation', 'animated fast' );
				if ( !empty($args['animation_in']) && !trx_addons_is_off($args['animation_in']) ) {
					$widget->add_render_attribute( '_wrapper', 'data-animation-in', $args['animation_in'] );
				}
				if ( !empty($args['animation_out']) && !trx_addons_is_off($args['animation_out']) ) {
					$widget->add_render_attribute( '_wrapper', 'data-animation-out', $args['animation_out'] );
				}
			}
*/
		}
	}
}


// Disable our widgets (shortcodes) to use in Elementor
// because we create special Elementor's widgets instead
if (!function_exists('trx_addons_sc_layouts_blog_item_black_list')) {
	add_action( 'elementor/widgets/black_list', 'trx_addons_sc_layouts_blog_item_black_list' );
	function trx_addons_sc_layouts_blog_item_black_list($list) {
		$list[] = 'TRX_Addons_SOW_Widget_Layouts_Blog_Item';
		return $list;
	}
}
