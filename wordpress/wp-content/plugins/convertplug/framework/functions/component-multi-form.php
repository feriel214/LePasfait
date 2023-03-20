<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * ConvertPlug Form
 *
 *  Module  - Multi Form
 *
 * 1.   Option Array Setup
 * 2.   Global array for shortcode variables
 * 3.   Generate Output by 'cp_get_form' filter
 * 4.   Generate & Append CSS
 *
 *  Use same names for variables & array
 *  For '$your_options_name' use '$your_options_name_VARS'
 *
 *  E.g.    $cp_form
 *          $cp_form_vars
 *
 * @since  1.1.1
 */
global $cp_form;
global $cp_form_vars;

/**
 * 1.   Option Array Setup.
 */
$cp_form = array(

	/**
	 * Hidden Fields.
	 */
	array(
		'type'         => 'textfield',
		'class'        => '',
		'name'         => 'button_title',
		'opts'         => array(
			'title'       => __( 'Button Title', 'smile' ),
			'value'       => 'AVAIL NOW',
			'description' => __( 'Enter the button title.', 'smile' ),
		),
		'panel'        => 'Form Designer',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
		'dependency'   => array(
			'name'     => 'hidden',
			'operator' => '==',
			'value'    => 'hide',
		),
	),

	/**
	 * Form Builder.
	 */
	array(
		'type'         => 'section',
		'class'        => '',
		'name'         => 'form_builder_section',
		'opts'         => array(
			'title' => 'Form Builder',
			'link'  => '',
			'value' => '',
		),
		'panel'        => 'Form Designer',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
		'dependency'   => array(
			'name'     => 'form_layout',
			'operator' => '!==',
			'value'    => 'cp-form-layout-4',
		),
	),
	array(
		'type'         => 'multi_box',
		'class'        => '',
		'name'         => 'form_fields',
		'opts'         => array(
			'title' => '',
			'value' => '',
		),
		'panel'        => 'Form Designer',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
		'dependency'   => array(
			'name'     => 'form_layout',
			'operator' => '!=',
			'value'    => 'cp-form-layout-4',
		),
	),

	// Form Layout.
	array(
		'type'         => 'section',
		'class'        => '',
		'name'         => 'form_layout_section',
		'opts'         => array(
			'title' => 'Form Layout',
			'link'  => '',
			'value' => '',
		),
		'panel'        => 'Form Designer',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
		'dependency'   => array(
			'name'     => 'form_layout',
			'operator' => '!==',
			'value'    => 'cp-form-layout-4',
		),
	),
	array(
		'type'         => 'radio-image',
		'class'        => '',
		'name'         => 'form_layout',
		'opts'         => array(
			'title'      => __( 'Layout', 'smile' ),
			'value'      => '',
			'options'    => array(
				__( 'cp-form-layout-1', 'smile' ) => CP_BASE_URL . 'modules/assets/images/form-layout-1.png',
				__( 'cp-form-layout-2', 'smile' ) => CP_BASE_URL . 'modules/assets/images/form-layout-2.png',
				__( 'cp-form-layout-3', 'smile' ) => CP_BASE_URL . 'modules/assets/images/form-layout-3.png',
				__( 'cp-form-layout-4', 'smile' ) => CP_BASE_URL . 'modules/assets/images/form-layout-4.png',
			),
			'width'      => '130px',
			'imagetitle' => array(
				__( 'title-0', 'smile' ) => 'Vertical - 1 Column Form Layout',
				__( 'title-1', 'smile' ) => 'Vertical - 2 Column Form Layout',
				__( 'title-2', 'smile' ) => 'Horizontal Form Layout',
				__( 'title-3', 'smile' ) => 'Only Submit Button Layout',
			),
		),
		'panel'        => 'Form Designer',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
	),

	// ONLY_BUTTON_LINK.
	array(
		'type'         => 'section',
		'class'        => '',
		'name'         => 'form_only_button_link_section',
		'opts'         => array(
			'title' => 'Submit Button Link',
			'link'  => '',
			'value' => '',
		),
		'panel'        => 'Form Designer',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
		'dependency'   => array(
			'name'     => 'form_layout',
			'operator' => '==',
			'value'    => 'cp-form-layout-4',
		),
	),
	array(
		'type'         => 'textfield',
		'name'         => 'only_button_link',
		'opts'         => array(
			'title'       => __( 'Button Link', 'smile' ),
			'description' => __( 'Provide a link to submit button. Please add http / https prefix to URL. <br/><br/> e.g. https://www.convertplug.com/plus', 'smile' ),
			'value'       => '',
		),
		'dependency'   => array(
			'name'     => 'hidden',
			'operator' => '==',
			'value'    => 'hide',
		),
		'panel'        => 'Form Designer',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
		'dependency'   => array(
			'name'     => 'form_layout',
			'operator' => '==',
			'value'    => 'cp-form-layout-4',
		),
	),
	array(
		'type'         => 'dropdown',
		'class'        => '',
		'name'         => 'only_button_link_target',
		'opts'         => array(
			'title'       => __( 'Button Link Target', 'smile' ),
			'description' => __( 'The button link target specifies where to open the button link.', 'smile' ),
			'value'       => '',
			'options'     => array(
				__( 'Open in New Window (_blank)', 'smile' ) => '_blank',
				__( 'Open in Same Window (_self)', 'smile' ) => '_self',
			),
		),
		'panel'        => 'Form Designer',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
		'dependency'   => array(
			'name'     => 'form_layout',
			'operator' => '==',
			'value'    => 'cp-form-layout-4',
		),
	),

	// Grid Structure.
	array(
		'type'         => 'radio-image',
		'class'        => '',
		'name'         => 'form_grid_structure',
		'opts'         => array(
			'title'       => __( 'Grid Structure', 'smile' ),
			'description' => __( 'Change submit button size depends on your grid selection.', 'smile' ),
			'value'       => '',
			'options'     => array(
				__( 'cp-form-grid-structure-1', 'smile' ) => CP_BASE_URL . 'modules/assets/images/grid-structure-1-2.png',
				__( 'cp-form-grid-structure-2', 'smile' ) => CP_BASE_URL . 'modules/assets/images/grid-structure-1-3.png',
				__( 'cp-form-grid-structure-3', 'smile' ) => CP_BASE_URL . 'modules/assets/images/grid-structure-1-4.png',
			),
			'width'       => '100%',
			'imagetitle'  => array(
				__( 'title-0', 'smile' ) => '50% Submit Button Area',
				__( 'title-1', 'smile' ) => '33.33% Submit Button Area',
				__( 'title-2', 'smile' ) => '25% Submit Button Area',
			),
		),
		'panel'        => 'Form Designer',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
		'dependency'   => array(
			'name'     => 'form_layout',
			'operator' => '==',
			'value'    => 'cp-form-layout-3',
		),
	),

	array(
		'type'         => 'switch',
		'class'        => '',
		'name'         => 'btn_attached_email',
		'opts'         => array(
			'title'       => __( 'Submit & Input box connected', 'smile' ),
			'description' => __( 'Enable this option to attach input field & submit button. <br/>Note - This option will work for only one input field form.', 'smile' ),
			'value'       => false,
			'on'          => __( 'YES', 'smile' ),
			'off'         => __( 'NO', 'smile' ),
		),
		'panel'        => 'Form Designer',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
		'dependency'   => array(
			'name'     => 'form_layout',
			'operator' => '==',
			'value'    => 'cp-form-layout-3',
		),
	),
	// Label settings.
	array(
		'type'         => 'section',
		'class'        => '',
		'name'         => 'form_styling_section',
		'opts'         => array(
			'title' => 'Form Styling',
			'link'  => '',
			'value' => '',
		),
		'panel'        => 'Form Designer',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
		'dependency'   => array(
			'name'     => 'form_layout',
			'operator' => '!==',
			'value'    => 'cp-form-layout-4',
		),
	),

	// Form Styling - Input Styling.
	array(
		'type'         => 'dropdown',
		'class'        => '',
		'name'         => 'form_input_align',
		'opts'         => array(
			'title'        => __( 'Form Text Alignment', 'smile' ),
			'value'        => '',
			'css_property' => 'text-align',
			'css_selector' => '.cp-form-container .cp-form-field button, .cp-form-container .cp-form-field input, .cp-form-container .cp-form-field select, .cp-form-container .cp-form-field textarea, .cp-form-container label, .cp-form-container .cp-form-field .cp-label',
			'css_preview'  => true,
			'options'      => array(
				__( 'Left', 'smile' )   => 'left',
				__( 'Right', 'smile' )  => 'right',
				__( 'Center', 'smile' ) => 'center',
			),
		),
		'panel'        => 'Form Designer',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
		'dependency'   => array(
			'name'     => 'form_layout',
			'operator' => '!==',
			'value'    => 'cp-form-layout-4',
		),
	),
	array(
		'type'         => 'colorpicker',
		'class'        => '',
		'name'         => 'form_input_color',
		'opts'         => array(
			'title'        => __( 'Input Box Text Color', 'smile' ),
			'value'        => 'rgb(153, 153, 153)',
			'css_property' => 'color',
			'css_selector' => '.cp-form-container .cp-form-field button, .cp-form-container .cp-form-field input, .cp-form-container .cp-form-field select, .cp-form-container .cp-form-field textarea, .cp-form-container .cp-form-field .cp-label',
			'css_preview'  => true,
		),
		'panel'        => 'Form Designer',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
		'dependency'   => array(
			'name'     => 'form_layout',
			'operator' => '!==',
			'value'    => 'cp-form-layout-4',
		),
	),
	array(
		'type'         => 'colorpicker',
		'class'        => '',
		'name'         => 'form_input_bg_color',
		'opts'         => array(
			'title'        => __( 'Input Box Background Color', 'smile' ),
			'value'        => 'rgb(255, 255, 255)',
			'css_property' => 'background-color',
			'css_selector' => '.cp-form-container .cp-form-field button, .cp-form-container .cp-form-field input, .cp-form-container .cp-form-field select, .cp-form-container .cp-form-field textarea',
			'css_preview'  => true,
		),
		'panel'        => 'Form Designer',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
		'dependency'   => array(
			'name'     => 'form_layout',
			'operator' => '!==',
			'value'    => 'cp-form-layout-4',
		),
	),
	array(
		'type'         => 'colorpicker',
		'class'        => '',
		'name'         => 'form_input_border_color',
		'opts'         => array(
			'title'        => __( 'Input Box Border Color', 'smile' ),
			'value'        => 'rgb(191, 190, 190)',
			'css_property' => 'border-color',
			'css_selector' => ".cp-form-container .cp-form-field button, .cp-form-container .cp-form-field input, .cp-form-container .cp-form-field select, .cp-form-container .cp-form-field textarea,.cp-form-container input:focus:not([type='radio']):not([type='checkbox']):not([type='range']),.cp-form-container textarea:focus",
			'css_preview'  => true,
		),
		'panel'        => 'Form Designer',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
		'dependency'   => array(
			'name'     => 'form_layout',
			'operator' => '!==',
			'value'    => 'cp-form-layout-4',
		),
	),
	array(
		'type'         => 'switch',
		'class'        => '',
		'name'         => 'input_shadow',
		'opts'         => array(
			'title' => __( 'Input Field Shadow', 'smile' ),
			'value' => false,
			'on'    => __( 'YES', 'smile' ),
			'off'   => __( 'NO', 'smile' ),
		),
		'panel'        => 'Form Designer',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
		'dependency'   => array(
			'name'     => 'form_layout',
			'operator' => '!=',
			'value'    => 'cp-form-layout-4',
		),
	),
	// Store the default initial color of button text.
	array(
		'type'         => 'colorpicker',
		'class'        => '',
		'name'         => 'input_shadow_color',
		'opts'         => array(
			'title' => __( 'Shadow Color', 'smile' ),
			'value' => 'rgba(66, 66, 66, 0.6)',
		),
		'dependency'   => array(
			'name'     => 'input_shadow',
			'operator' => '==',
			'value'    => true,
		),
		'panel'        => 'Form Designer',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
	),
	array(
		'type'         => 'slider',
		'class'        => '',
		'name'         => 'input_border_radius',
		'opts'         => array(
			'title'        => __( 'Input Border Radius', 'smile' ),
			'css_property' => 'border-radius',
			'css_selector' => '.cp-form-container .cp-form-field button, .cp-form-container .cp-form-field input, .cp-form-container .cp-form-field select, .cp-form-container .cp-form-field textarea , .cp-form-field.cp-enable-box-shadow > div',
			'css_preview'  => true,
			'value'        => 3,
			'min'          => 0,
			'max'          => 40,
			'step'         => 1,
		),
		'panel'        => 'Form Designer',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
	),
	array(
		'type'         => 'google_fonts',
		'name'         => 'form_input_font',
		'opts'         => array(
			'title'        => __( 'Input Box Font Name', 'smile' ),
			'value'        => '',
			'use_in'       => 'panel',
			'css_property' => 'font-family',
			'css_selector' => '.cp-form-container .cp-form-field button, .cp-form-container .cp-form-field input, .cp-form-container .cp-form-field select, .cp-form-container .cp-form-field textarea, .cp-form-container .cp-form-field .cp-label',
			'css_preview'  => true,
		),
		'panel'        => 'Form Designer',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
		'dependency'   => array(
			'name'     => 'form_layout',
			'operator' => '!==',
			'value'    => 'cp-form-layout-4',
		),
	),
	array(
		'type'         => 'slider',
		'class'        => '',
		'name'         => 'form_input_font_size',
		'opts'         => array(
			'title'        => __( 'Input Box Font Size', 'smile' ),
			'css_property' => 'font-size',
			'css_selector' => '.cp-form-container .cp-form-field button, .cp-form-container .cp-form-field input, .cp-form-container .cp-form-field select, .cp-form-container .cp-form-field textarea, .cp-form-container .cp-form-field .cp-label',
			'css_preview'  => true,
			'value'        => '15',
			'min'          => 0,
			'max'          => 40,
			'step'         => 1,
			'suffix'       => 'px',
		),
		'panel'        => 'Form Designer',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
		'dependency'   => array(
			'name'     => 'form_layout',
			'operator' => '!==',
			'value'    => 'cp-form-layout-4',
		),
	),

	// Submit Button Padding.
	array(
		'type'         => 'slider',
		'class'        => '',
		'name'         => 'form_input_padding_tb',
		'opts'         => array(
			'title'        => __( 'Input Box Vertical Padding', 'smile' ),
			'css_property' => 'padding-tb',
			'css_selector' => '.cp-form-container .cp-form-field button, .cp-form-container .cp-form-field input, .cp-form-container .cp-form-field select, .cp-form-container .cp-form-field textarea, .cp-form-container .cp-form-field .cp-label',
			'css_preview'  => true,
			'value'        => 10,
			'min'          => 0,
			'max'          => 30,
			'step'         => 1,
			'suffix'       => 'px',
		),
		'panel'        => 'Form Designer',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
		'dependency'   => array(
			'name'     => 'form_layout',
			'operator' => '!==',
			'value'    => 'cp-form-layout-4',
		),
	),
	array(
		'type'         => 'slider',
		'class'        => '',
		'name'         => 'form_input_padding_lr',
		'opts'         => array(
			'title'        => __( 'Input Box Horizontal Padding', 'smile' ),
			'css_property' => 'padding-lr',
			'css_selector' => '.cp-form-container .cp-form-field button, .cp-form-container .cp-form-field input, .cp-form-container .cp-form-field select, .cp-form-container .cp-form-field textarea',
			'css_preview'  => true,
			'value'        => 15,
			'min'          => 0,
			'max'          => 50,
			'step'         => 1,
			'suffix'       => 'px',
		),
		'panel'        => 'Form Designer',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
		'dependency'   => array(
			'name'     => 'form_layout',
			'operator' => '!==',
			'value'    => 'cp-form-layout-4',
		),
	),
	// Label.
	array(
		'type'         => 'switch',
		'class'        => '',
		'name'         => 'form_lable_visible',
		'opts'         => array(
			'title' => __( 'Label Visibility', 'smile' ),
			'value' => false,
			'on'    => __( 'Enable', 'smile' ),
			'off'   => __( 'Disable', 'smile' ),
		),
		'panel'        => 'Form Designer',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
		'dependency'   => array(
			'name'     => 'form_layout',
			'operator' => '!==',
			'value'    => 'cp-form-layout-4',
			'name'     => 'form_layout',
			'operator' => '!==',
			'value'    => 'cp-form-layout-3',
		),
	),
	array(
		'type'         => 'colorpicker',
		'class'        => '',
		'name'         => 'form_lable_color',
		'opts'         => array(
			'title'        => __( 'Label Color', 'smile' ),
			'value'        => 'rgb(153, 153, 153)',
			'css_property' => 'color',
			'css_selector' => '.cp-form-container label',
			'css_preview'  => true,
		),
		'panel'        => 'Form Designer',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
		'dependency'   => array(
			'name'     => 'form_lable_visible',
			'operator' => '==',
			'value'    => 1,
		),
	),
	array(
		'type'         => 'google_fonts',
		'name'         => 'form_label_font',
		'opts'         => array(
			'title'        => __( 'Label Font Name', 'smile' ),
			'value'        => '',
			'use_in'       => 'panel',
			'css_property' => 'font-family',
			'css_selector' => '.cp-form-container label',
		),
		'panel'        => 'Form Designer',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
		'dependency'   => array(
			'name'     => 'form_lable_visible',
			'operator' => '==',
			'value'    => 1,
		),
	),
	array(
		'type'         => 'slider',
		'class'        => '',
		'name'         => 'form_lable_font_size',
		'opts'         => array(
			'title'        => __( 'Label Font Size', 'smile' ),
			'css_selector' => '.cp-form-container label',
			'css_property' => 'font-size',
			'value'        => 15,
			'min'          => 0,
			'max'          => 100,
			'step'         => 1,
			'suffix'       => 'px',
			'css_preview'  => true,
		),
		'panel'        => 'Form Designer',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
		'dependency'   => array(
			'name'     => 'form_lable_visible',
			'operator' => '==',
			'value'    => 1,
		),
	),

	// Submit Button Styling.
	array(
		'type'         => 'section',
		'class'        => '',
		'name'         => 'form_submit_button_styling',
		'opts'         => array(
			'title' => 'Submit Button Styling',
			'link'  => '',
			'value' => '',
		),
		'panel'        => 'Form Designer',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
	),
	array(
		'type'         => 'dropdown',
		'class'        => '',
		'name'         => 'btn_style',
		'opts'         => array(
			'title'       => __( 'Button Style', 'smile' ),
			'value'       => 'cp-btn-flat',
			'description' => __( 'Style your button with nice effects.', 'smile' ),
			'options'     => array(
				__( 'Flat', 'smile' )     => 'cp-btn-flat',
				__( '3D', 'smile' )       => 'cp-btn-3d',
				__( 'Outline', 'smile' )  => 'cp-btn-outline',
				__( 'Gradient', 'smile' ) => 'cp-btn-gradient',
			),
		),
		'panel'        => 'Form Designer',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
	),
	array(
		'type'         => 'colorpicker',
		'class'        => '',
		'name'         => 'button_bg_color',
		'opts'         => array(
			'title'        => __( 'Button Background Color', 'smile' ),
			'value'        => 'rgb(255, 153, 0)',
			'css_property' => 'background',
			'css_selector' => '.cp-submit',
			'css_preview'  => true,
		),
		'panel'        => 'Form Designer',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
	),
	array(
		'type'         => 'slider',
		'class'        => '',
		'name'         => 'btn_border_radius',
		'opts'         => array(
			'title'        => __( 'Border Radius', 'smile' ),
			'css_property' => 'border-radius',
			'css_selector' => '.cp-submit',
			'value'        => 3,
			'min'          => 0,
			'max'          => 40,
			'step'         => 1,
		),
		'panel'        => 'Form Designer',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
	),
	array(
		'type'         => 'switch',
		'class'        => '',
		'name'         => 'btn_shadow',
		'opts'         => array(
			'title' => __( 'Button Shadow', 'smile' ),
			'value' => false,
			'on'    => __( 'YES', 'smile' ),
			'off'   => __( 'NO', 'smile' ),
		),
		'panel'        => 'Form Designer',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
		'dependency'   => array(
			'name'     => 'btn_style',
			'operator' => '!=',
			'value'    => 'cp-btn-3d',
		),
	),
	array(
		'type'         => 'switch',
		'class'        => '',
		'name'         => 'btn_no_follow',
		'opts'         => array(
			'title' => __( 'Make Button Link NoFollow', 'smile' ),
			'value' => false,
			'on'    => __( 'YES', 'smile' ),
			'off'   => __( 'NO', 'smile' ),
		),
		'panel'        => 'Form Designer',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
	),

	// Store the default initial color of button text.
	array(
		'type'         => 'colorpicker',
		'class'        => '',
		'name'         => 'button_txt_hover_color',
		'opts'         => array(
			'title'        => __( 'Submit Button Text Hover Color', 'smile' ),
			'value'        => '#ffffff',
			'css_property' => 'border-color',
			'css_selector' => '.cp-submit:hover',
		),
		'dependency'   => array(
			'name'     => 'btn_style',
			'operator' => '==',
			'value'    => 'cp-btn-outline',
		),
		'panel'        => 'Form Designer',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
	),
	// store button darken on hover.
	array(
		'type'         => 'textfield',
		'name'         => 'button_bg_hover_color',
		'opts'         => array(
			'title' => __( 'Button BG Hover Color', 'smile' ),
			'value' => '',
		),
		'dependency'   => array(
			'name'     => 'hidden',
			'operator' => '==',
			'value'    => 'hide',
		),
		'panel'        => 'Form Designer',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
	),
	// store button lighten gradient.
	array(
		'type'         => 'textfield',
		'name'         => 'button_bg_gradient_color',
		'opts'         => array(
			'title' => __( 'Button Gradient Color', 'smile' ),
			'value' => '',
		),
		'dependency'   => array(
			'name'     => 'hidden',
			'operator' => '==',
			'value'    => 'hide',
		),
		'panel'        => 'Form Designer',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
	),
	array(
		'type'         => 'dropdown',
		'class'        => '',
		'name'         => 'form_submit_align',
		'opts'         => array(
			'title'   => __( 'Button Alignment', 'smile' ),
			'value'   => '',
			'options' => array(
				__( 'Center', 'smile' ) => 'cp-submit-wrap-center',
				__( 'Left', 'smile' )   => 'cp-submit-wrap-left',
				__( 'Right', 'smile' )  => 'cp-submit-wrap-right',
				__( 'Full', 'smile' )   => 'cp-submit-wrap-full',
			),
		),
		'dependency'   => array(
			'name'     => 'form_layout',
			'operator' => '!==',
			'value'    => 'cp-form-layout-3',
		),
		'panel'        => 'Form Designer',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
	),

	// Submit Button Padding.
	array(
		'type'         => 'slider',
		'class'        => '',
		'name'         => 'submit_button_tb_padding',
		'opts'         => array(
			'title'        => __( 'Button Vertical Padding', 'smile' ),
			'css_property' => 'padding-tb',
			'css_selector' => '.cp-submit',
			'value'        => 10,
			'min'          => 0,
			'max'          => 100,
			'step'         => 1,
			'suffix'       => 'px',
		),
		'panel'        => 'Form Designer',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
	),
	array(
		'type'         => 'slider',
		'class'        => '',
		'name'         => 'submit_button_lr_padding',
		'opts'         => array(
			'title'        => __( 'Button Horizontal Padding', 'smile' ),
			'css_property' => 'padding-lr',
			'css_selector' => '.cp-submit',
			'value'        => 15,
			'min'          => 0,
			'max'          => 100,
			'step'         => 1,
			'suffix'       => 'px',
		),
		'panel'        => 'Form Designer',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
	),
	// Note - Button Options.
	array(
		'type'         => 'txt-link',
		'class'        => '',
		'name'         => 'note_button_options',
		'opts'         => array(
			'link'  => __( "Note - Above settings apply to only Built-In Forms. These won't be effective with Custom Forms.", 'smile' ),
			'value' => '',
			'title' => '',
		),
		'panel'        => 'Form Designer',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
	),
);

/**
 * Global array for shortcode variables.
 */

$cp_form_vars = generate_global_shortcode_vars( $cp_form );

add_filter( 'cp_get_form', 'cp_get_form_init' );
if ( ! function_exists( 'cp_get_form_init' ) ) {
	/**
	 * Function Name: cp_get_form_init Generate Output by 'cp_get_form' filter.
	 *
	 * @param  array $a settings array.
	 */
	function cp_get_form_init( $a ) { ?>

		<?php

		$style_id = ( isset( $a['style_id'] ) ) ? esc_attr( $a['style_id'] ) : '';
		// Form Type?
		if ( isset( $a['mailer'] ) && 'custom-form' === $a['mailer'] ) {

			// if Form - Custom?
			echo '<div class="custom-html-form" data-style="' . esc_attr( $style_id ) . '">' . do_shortcode( $a['custom_html_form'] ) . '</div>';

		} else {
			// apply button styles.
			apply_filters_ref_array( 'cp_form_css', array( $a ) );

			// default Form - ConvertPlug.
			$only_button_link        = '';       // Only button - Link after conversion.
			$only_button_link_target = '';       // Only button - Link Target after conversion.
			$class_wrap              = '';       // Class for - 'Form Wrapper'.
			$class_form              = '';       // Class for - 'Form'.
			$class_submit            = '';       // Class for - 'Submit CKEditor'.
			$class_inputs_wrap       = '';       // Class for - 'Inputs Wrap'.
			$class_fields            = '';       // Class for - 'All Inputs - Wrap'.
			$class_inputs            = '';       // Class for - 'All Inputs'.
			$class_input_name        = '';       // Class for - 'Input - Name'.
			$class_input_email       = '';       // Class for - 'Input - Email'.
			$class_row_value         = '';       // Class for - 'textarea - rows'.
			$class_shadow            = '';       // Class for - 'shadow - input'.
			$enable_field_attached   = '';       // Class for - 'enable attached fileds'.

			// Submit button alignment.
			$class_submit         .= ( isset( $a['form_submit_align'] ) && '' != $a['form_submit_align'] ) ? ' ' . $a['form_submit_align'] : '';
			$class_shadow         .= ( isset( $a['input_shadow'] ) && '' != $a['input_shadow'] ) ? 'enable_input_shadow' : '';
			$enable_field_attached = ( isset( $a['btn_attached_email'] ) && ( '1' === $a['btn_attached_email'] || 1 === $a['btn_attached_email'] ) && isset( $a['form_layout'] ) && 'cp-form-layout-3' === $a['form_layout'] ) ? 'enable-field-attached' : '';

			if ( isset( $a['form_layout'] ) ) {

				$class_wrap = $a['form_layout'];

				switch ( $a['form_layout'] ) {

					case 'cp-form-layout-1':
						$class_fields .= ' col-md-12 col-lg-12 col-sm-12 col-xs-12';
						$class_submit .= ' col-md-12 col-lg-12 col-sm-12 col-xs-12';
						break;

					case 'cp-form-layout-2':
						$class_fields .= ' col-md-6 col-lg-6 col-sm-6 col-xs-12 ';
						$class_submit .= ' col-md-12 col-lg-12 col-sm-12 col-xs-12 ';
						break;

					case 'cp-form-layout-3':    // Grid structure for All Input Wrap & Submit.
						switch ( $a['form_grid_structure'] ) {
							case 'cp-form-grid-structure-1':
								$class_submit     .= ' col-xs-12 col-sm-6 col-md-6 col-lg-6 ';
								$class_inputs_wrap = ' col-xs-12 col-sm-6 col-md-6 col-lg-6 ';
								break;
							case 'cp-form-grid-structure-2':
								$class_submit     .= ' col-xs-12 col-sm-4 col-md-4 col-lg-4 ';
								$class_inputs_wrap = ' col-xs-12 col-sm-8 col-md-8 col-lg-8 ';
								break;
							case 'cp-form-grid-structure-3':
							default:
								$class_submit     .= ' col-xs-12 col-sm-3 col-md-3 col-lg-3 ';
								$class_inputs_wrap = ' col-xs-12 col-sm-9 col-md-9 col-lg-9 ';
								break;
						}

						$all_fields = explode( ';', $a['form_fields'] );
						if ( count( $all_fields ) > 0 ) {

							$fields_count = count( $all_fields );

							// Remove hidden fields from count.
							$no_of_hiddens = substr_count( $a['form_fields'], 'input_type->hidden' );

							if ( '' !== $no_of_hiddens ) {
								$fields_count = $fields_count - $no_of_hiddens;
							}

							switch ( $fields_count ) {
								case 1:
									$class_fields = ' col-md-12 col-lg-12 col-sm-12 col-xs-12';
									break;
								case 2:
									$class_fields = ' col-md-6 col-lg-6 col-sm-6 col-xs-12';
									break;
								case 3:
									$class_fields = ' col-md-4 col-lg-4 col-sm-4 col-xs-12';
									break;
								case 4:
								case 5:
									$class_fields = ' col-md-3 col-lg-3 col-sm-3 col-xs-12';
									break;
								case 6:
									$class_fields = ' col-md-2 col-lg-2 col-sm-2 col-xs-12';
									break;
							}
						}
						break;

					case 'cp-form-layout-4':
						$class_submit .= ' col-md-12 col-lg-12 col-sm-12 col-xs-12 ';
						break;

				}
			}
			?>

			<div class="form-main <?php echo esc_attr( $class_wrap ); ?>">

				<form class="cp-form smile-optin-form <?php echo esc_attr( $class_form ); ?> <?php echo esc_attr( $class_shadow ); ?>">

					<?php

					/**
					 * Add hidden fields.
					 */
					apply_filters_ref_array( 'cp_form_hidden_fields', array( $a ) );

					// Conversion for only - Single Button.
					if ( 'cp-form-layout-4' === $a['form_layout'] ) {
						echo '<input type="hidden" name="only_conversion" value="true" />';

						// for only button - add redirect after conversion link.
						$only_button_link        = ( isset( $a['only_button_link'] ) ) ? ' data-redirect-link="' . $a['only_button_link'] . '" ' : '';
						$only_button_link_target = ( isset( $a['only_button_link_target'] ) ) ? ' data-redirect-link-target="' . $a['only_button_link_target'] . '" ' : '';
					}

					$submit_anamtion_class = '';
					$data_anamtion         = '';

					// Show all hidden fields & input fields.
					if ( 'cp-form-layout-4' !== $a['form_layout'] ) {
						?>

						<div class="cp-all-inputs-wrap col-xs-12 <?php echo esc_attr( $class_inputs_wrap ); ?> ">

							<?php

							$data_fields      = '';
							$hidden_fields    = '';
							$name             = '';
							$placeholder      = '';
							$require          = '';
							$type             = '';
							$dropdown_options = '';
							$hidden_value     = '';
							$rows             = '';

							// Conversion for only - Form Inputs.
							if ( 'cp-form-layout-4' !== $a['form_layout'] ) {

								if ( ! empty( $a['form_fields'] ) ) {
									$all         = explode( ';', $a['form_fields'] );
									$parent_last = end( $all );
									$data_value  = array();
									foreach ( $all as $parent_key => $parent_value ) {

										$single   = explode( '|', $parent_value );
										$single_1 = ( isset( $single[1] ) ) ? $single[1] : '';

										if ( '' !== $single && ! ( false !== strpos( $single_1, 'hidden' ) ) ) {
											$data_value['order'] = ( isset( $single[0] ) ) ? $single[0] : '';
										}
									}

									$last_value = explode( '->', $data_value['order'] );
									if ( null !== $last_value ) {
										$last_value  = ( isset( $last_value[1] ) ) ? $last_value[1] : '';
										$parent_last = ( isset( $all[ $last_value ] ) ) ? $all[ $last_value ] : '';
									}

									// Add after 'CP_FIELD_$i' to generate names dynamically.
									// In case if user was not add the NAME field.
									$i = 0;
									foreach ( $all as $parent_key => $parent_value ) {
										$single = explode( '|', $parent_value );

										foreach ( $single as $key => $value ) {

											$s = explode( '->', $value );

											$s[1] = ( isset( $s[1] ) ) ? $s[1] : '';

											// remove all slashes from string.
											$s[1] = stripslashes_deep( $s[1] );
											// Sanitize string.
											// avoided drop down values from sanitize.
											if ( 'dropdown_options' !== $s[0] && 'input_name' !== $s[0] && 'input_label' !== $s[0] ) {
												$s[1] = sanitize_text_field( $s[1] );
											}

											switch ( $s[0] ) {
												case 'input_label':
													$label = $s[1];
													break;
												case 'input_name':
													$name = ( ! empty( $s[1] ) ) ? $s[1] : 'CP_FIELD_' . $i;
													break;
												case 'hidden_value':
													$hidden_value = $s[1];
													break;
												case 'input_placeholder':
													$placeholder = $s[1];
													break;
												case 'input_require':
													$require = ( 'true' === $s[1] ) ? ' required ' : '';
													break;
												case 'input_type':
													$type = $s[1];
													break;
												case 'dropdown_options':
													$dropdown_options = $s[1];
													break;
												case 'row_value':
													$rows = 'rows=' . $s[1];
													break;
											}
										}

										// If last child then add '.col-md-12' for last child.
										// Avoided '.cp-form-layout-3'.
										if ( ( 'cp-form-layout-3' !== $a['form_layout'] ) && ( $parent_value === $parent_last ) && 0 === $i % 2 ) {
											// Increase the NAME counter.
											$class_fields = ' col-md-12 col-lg-12 col-sm-12 col-xs-12 odd-field-fallback';
										}

										// Increase the NAME counter.
										if ( 'hidden' !== $type ) {
											$i++;
										}

										// Store all Hidden fields.
										if ( 'hidden' === $type ) {

											// Retreive current page url.
											if ( ( 'page_url' === $name ) || ( 'PAGE_URL' === $name ) ) {
												if ( '' === $hidden_value ) {
													$hidden_value = esc_url( get_permalink() );
												}
											}

											// Retrieve user IP.
											if ( ( 'IP' === $name || 'ip' === $name || 'ip_address' === $name ) && ( '' === $hidden_value ) ) {
												$hidden_value = cp_get_the_user_ip();
											}

											// Set time.
											if ( 'time' === $name ) {
												$timezone_settings = get_option( 'convert_plug_settings' );
												$timezone_name     = $timezone_settings['cp-timezone'];
												$current_time      = '';

												if ( 'WordPress' === $timezone_name ) {

													$tzstring = get_option( 'timezone_string' );
													$offset   = get_option( 'gmt_offset' );

													if ( empty( $tzstring ) && 0 != $offset && floor( $offset ) == $offset ) {
														$offset_st = $offset > 0 ? "-$offset" : '+' . absint( $offset );
														$tzstring  = 'Etc/GMT' . $offset_st;
													}

													// Issue with the timezone selected, set to 'UTC'.
													if ( empty( $tzstring ) ) {
														$tzstring = 'UTC';
													}

													$timezone     = new DateTimeZone( $tzstring );
													$time         = new DateTime( null, $timezone );
													$hidden_value = $time->format( 'H:i:s' );
												} elseif ( 'system' === $timezone_name ) {
													$hidden_value = current_time( 'H:i:s' );
												}
											}

											$hidden_fields .= '<input class="cp-input cp-' . $type . '"'
											. ' type="' . $type . '"'
											. ' name="param[' . $name . ']"'
											. ' value="' . $hidden_value . '" />';
										} else {

											$last_input = '';
											$btn_class  = '';
											if ( $parent_value === $parent_last && ( '1' === $i || 1 === $i ) ) {

												$last_input = $enable_field_attached;
												// Apply box shadow to submit button - If its set & equals to - 1.
												if ( isset( $a['btn_shadow'] ) && ( '1' === $a['btn_shadow'] || 1 === $a['btn_shadow'] ) && '' !== $last_input ) {
													$last_input .= ' cp-enable-box-shadow';
												}
											} else {
												if ( 'cp-form-layout-3' !== $a['form_layout'] ) {
													$enable_field_attached = '';
												}
											}

											?>


											<div class="cp-form-field <?php echo esc_attr( $class_fields ); ?> <?php echo esc_attr( $last_input ); ?>">
												<?php if ( 'checkbox' !== $type ) { ?>
												<label><?php echo esc_html( $label ); ?></label>
												<?php } ?> 
												<div>
													<?php
													// Show all the fields.
													switch ( $type ) {
														case 'textfield':       // Text.
															echo '<input class="cp-input cp-' . esc_attr( $type ) . '"'
															. ' type="' . esc_attr( $type ) . '"'
															. ' name="param[' . esc_attr( $name ) . ']"'
															. ' placeholder="' . esc_attr( $placeholder ) . '" ' . esc_attr( $require ) . ' />';
															break;
														case 'email':           // Email   -   ADDED DEFAULT NAME FOR EMAIL.
															echo '<input class="cp-input cp-' . esc_attr( $type ) . '"'
															. ' type="' . esc_attr( $type ) . '"'
															. ' name="param[' . esc_attr( $name ) . ']"'
															. ' placeholder="' . esc_attr( $placeholder ) . '" ' . esc_attr( $require ) . ' />';
															break;
														case 'textarea':        // Textarea.
															echo '<textarea class="cp-input cp-' . esc_attr( $type ) . '"' . esc_attr( $require )
															. ' name="param[' . esc_attr( $name ) . ']" placeholder="' . esc_attr( $placeholder ) . '" ' . esc_attr( $rows ) . '></textarea>';
															break;

														case 'googlerecaptcha':        // Google Recaptcha.
															if ( ! wp_script_is( 'convert-plus-google-recaptcha', 'enqueued' ) ) {
																wp_register_script( 'convert-plus-google-recaptcha', 'https://www.google.com/recaptcha/api.js', array( 'jquery' ), CP_VERSION, true );

																wp_enqueue_script( 'convert-plus-google-recaptcha' );
															}

															$google_recaptcha_site_key = esc_attr( get_option( 'cp_recaptcha_site_key' ) );

															echo '<input type = "hidden" name = "cp_verify_google_recaptcha" class = "cp_verify_google_recaptcha" />';
															echo '<div id="id-g-recaptcha" class="g-recaptcha" data-sitekey="' . esc_attr( $google_recaptcha_site_key ) . ' ">
														</div>';
															break;
														case 'number':          // Number.
															echo '<input type="number" min="" max="" step="" value="" class="cp-input cp-' . esc_attr( $type ) . '"'
															. ' name="param[' . esc_attr( $name ) . ']"'
															. ' placeholder="' . esc_attr( $placeholder ) . '" ' . esc_attr( $require ) . ' />';
															break;
														case 'dropdown':        // Drop Down.
															$dropdown_options = mb_dropdown_string_to_html( $dropdown_options );
															if ( '' != $dropdown_options && ! empty( $dropdown_options ) ) {
																echo '<select class="cp-input cp-' . esc_attr( $type ) . '" name="param[' . esc_attr( $name ) . ']"' . esc_attr( $require ) . ' >'
																. $dropdown_options // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
																. '</select>';
															}
															break;
														case 'checkbox':        // checkbox.
															$label = do_shortcode( html_entity_decode( stripcslashes( htmlspecialchars( $label, ENT_QUOTES, 'utf-8' ) ), ENT_QUOTES, 'utf-8' ) );

															echo '<label class="cp-label"><input class="cp-input cp-' . esc_attr( $type ) . '"'
															. ' type="' . esc_attr( $type ) . '"'
															. ' name="param[' . esc_attr( $name ) . ']"'
															. ' placeholder="' . esc_attr( $placeholder ) . '" ' . esc_attr( $require ) . ' /><span class="cp-chbx-label">' . $label // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
															. '</span></label>';
															break;
													}

													?>
									</div></div><!-- .cp-form-field -->

											<?php
										} // 2. Show all input except hidden fields.

									} //	each single field loop.

									if ( 'hidden' == $type ) {
										echo '<input class="cp-input cp-' . esc_attr( $type ) . '"'
										. ' type="' . esc_attr( $type ) . '"'
										. ' name="param[' . esc_attr( $name ) . ']"'
										. ' value="' . esc_attr( $hidden_value ) . '" />';

									}
								}
							}

							// hide btn layout if layout ==3 and email_Attached ==1.
							if ( 'cp-form-layout-3' === $a['form_layout'] && '1' === $a['btn_attached_email'] ) {

								if ( ( '1' === $i ) && ( 'cp-btn-3d' === $a['btn_style'] || 'cp-btn-outline' === $a['btn_style'] ) ) {
									$a['btn_style'] = 'cp-btn-flat';
								}
							}
							?>
					</div>
						<?php
					}
					if ( isset( $a['button_animation'] ) ) {
						$submit_anamtion_class .= 'smile-animated ';
						$data_anamtion          = 'data-animation =' . $a['button_animation'] . '';
					}
					?>

					<div class="cp-submit-wrap <?php echo esc_attr( $class_submit ); ?> <?php echo esc_attr( $enable_field_attached ); ?>">
						<?php
						if ( isset( $a['btn_no_follow'] ) && $a['btn_no_follow'] ) {
							?>
							<a href="#" rel="nofollow noopener" class = "cp_nofollow" >
								<div class="cp-submit btn-subscribe cp_responsive <?php echo esc_attr( $a['btn_style'] ); ?> <?php echo esc_attr( $submit_anamtion_class ); ?> " <?php echo wp_kses_post( $only_button_link ) . wp_kses_post( $only_button_link_target ); ?>  <?php echo esc_attr( $data_anamtion ); ?>>
									<?php echo do_shortcode( html_entity_decode( $a['button_title'], ENT_QUOTES, 'utf-8' ) ); ?>
								</div>
							</a>
							<?php } else { ?>
							<div class="cp-submit btn-subscribe cp_responsive <?php echo esc_attr( $a['btn_style'] ); ?> <?php echo esc_attr( $submit_anamtion_class ); ?> " <?php echo wp_kses_post( $only_button_link ) . wp_kses_post( $only_button_link_target ); ?>  <?php echo esc_attr( $data_anamtion ); ?> rel="noopener">
								<?php echo do_shortcode( html_entity_decode( $a['button_title'], ENT_QUOTES, 'utf-8' ) ); ?>
							</div>
							<?php } ?>
						</div><!-- .cp-submit-wrap -->
					</form><!-- .smile-optin-form -->
				</div>
					<?php
		}
	}
}

	add_filter( 'cp_form_css', 'cp_form_css_init' );
/**
 * Function Name: cp_form_css_init Generate & Append CSS.
 *
 * @param  array $a array parameter.
 */
function cp_form_css_init( $a ) {

	/** = Submit Button - CSS.
	 *-----------------------------------------------------------*/
	$shadow    = '';
	$style     = '';
	$option    = ( isset( $a['option'] ) ) ? esc_attr( $a['option'] ) : '';
	$is_inline = ( isset( $a['display'] ) && 'inline' === $a['display'] ) ? true : false;

	if ( 'smile_info_bar_styles' === $option && $is_inline ) {
		$style_id = ( isset( $a['style_id'] ) ) ? esc_attr( $a['style_id'] ) : '';
		$style_id = 'content-' . $style_id;
	} elseif ( 'info_bar_variant_tests' === $option && isset( $a['variant_style_id'] ) && $is_inline ) {
		$style_id = ( isset( $a['style_id'] ) ) ? esc_attr( $a['style_id'] ) : '';
		$style_id = 'content-' . $style_id;
	} else {
		$style_id = ( isset( $a['uid_class'] ) ) ? esc_attr( $a['uid_class'] ) : '';
	}

	$form_input_align         = ( isset( $a['form_input_align'] ) && '' != $a['form_input_align'] ) ? $a['form_input_align'] : '';
	$form_input_font          = ( isset( $a['form_input_font'] ) && '' != $a['form_input_font'] ) ? $a['form_input_font'] : '';
	$form_label_font          = ( isset( $a['form_label_font'] ) && '' != $a['form_label_font'] ) ? $a['form_label_font'] : '';
	$form_input_color         = ( isset( $a['form_input_color'] ) && '' != $a['form_input_color'] ) ? $a['form_input_color'] : '';
	$form_input_bg_color      = ( isset( $a['form_input_bg_color'] ) && '' != $a['form_input_bg_color'] ) ? $a['form_input_bg_color'] : '';
	$form_input_border_color  = ( isset( $a['form_input_border_color'] ) && '' != $a['form_input_border_color'] ) ? $a['form_input_border_color'] : '';
	$form_input_font_size     = ( isset( $a['form_input_font_size'] ) && '' != $a['form_input_font_size'] ) ? $a['form_input_font_size'] : '';
	$form_input_padding_tb    = ( isset( $a['form_input_padding_tb'] ) && '' != $a['form_input_padding_tb'] ) ? $a['form_input_padding_tb'] : '';
	$form_input_padding_lr    = ( isset( $a['form_input_padding_lr'] ) && '' != $a['form_input_padding_lr'] ) ? $a['form_input_padding_lr'] : '';
	$submit_button_tb_padding = ( isset( $a['submit_button_tb_padding'] ) && '' != $a['submit_button_tb_padding'] ) ? $a['submit_button_tb_padding'] : '';
	$submit_button_lr_padding = ( isset( $a['submit_button_lr_padding'] ) && '' != $a['submit_button_lr_padding'] ) ? $a['submit_button_lr_padding'] : '';
	$input_shadow             = ( isset( $a['input_shadow'] ) && '' != $a['input_shadow'] ) ? $a['input_shadow'] : '';
	$input_shadow_color       = ( isset( $a['input_shadow_color'] ) && '' != $a['input_shadow_color'] ) ? $a['input_shadow_color'] : '';
	$form_layout              = ( isset( $a['form_layout'] ) && '' != $a['form_layout'] ) ? $a['form_layout'] : '';
	$input_border_radius      = ( isset( $a['input_border_radius'] ) && '' != $a['input_border_radius'] ) ? $a['input_border_radius'] : '';
	$btn_attached_email       = ( isset( $a['btn_attached_email'] ) && '' != $a['btn_attached_email'] ) ? $a['btn_attached_email'] : '';
	// Hide Labels?
	if ( '' === $a['form_lable_visible'] || ( '0' === $a['form_lable_visible'] ) || 'cp-form-layout-3' === $form_layout ) {
		$style .= '.' . $style_id . ' .cp-form-container label:not(.cp-label) { ';
		$style .= '   display:none;';
		$style .= '}';
	} elseif ( '1' === $a['form_lable_visible'] ) {
		$style .= '.' . $style_id . ' .cp-form-container label { ';
		$style .= '   display:block!important;';
		$style .= '}';
	}

	// CSS - Label.
	$style .= '.' . $style_id . ' .cp-form-container label { ';
	$style .= '   color: ' . $a['form_lable_color'] . ';';
	$style .= '   font-size: ' . $a['form_lable_font_size'] . 'px;';
	$style .= '	font-family:' . $form_label_font . ';';
	$style .= ' 	text-align: ' . $form_input_align . ';';
	$style .= '} ';

	$text_align = '';
	if ( 'right' === $form_input_align ) {
		$text_align = 'rtl';
	} elseif ( 'left' === $form_input_align ) {
		$text_align = 'ltr';
	}

	// CSS - Select align using 'direction: rtl;'.
	// Cause, Text align for select not working.
	$style .= '.' . $style_id . ' .cp-form-container .cp-form-field select { ';
	$style .= '   text-align-last: ' . $form_input_align . ';';
	$style .= '   direction: ' . $text_align . ';';
	$style .= '}';

	// CSS - Inputs.
	$style .= '.' . $style_id . " .cp-form-container input:focus:not([type='radio']):not([type='checkbox']):not([type='range']), ";
	$style .= '.' . $style_id . ' .cp-form-container textarea:focus, ';
	$style .= '.' . $style_id . ' .cp-form-container .cp-form-field button, ';
	$style .= '.' . $style_id . ' .cp-form-container .cp-form-field input, ';
	$style .= '.' . $style_id . ' .cp-form-container .cp-form-field select, ';
	$style .= '.' . $style_id . ' .cp-form-container .cp-form-field textarea {';
	$style .= ' 	text-align: ' . $form_input_align . ';';
	$style .= ' 	font-size: ' . $form_input_font_size . 'px;';
	$style .= ' 	font-family: ' . $form_input_font . ';';
	$style .= ' 	color: ' . $form_input_color . ';';
	$style .= ' 	background-color: ' . $form_input_bg_color . ';';
	$style .= ' 	border-color: ' . $form_input_border_color . ';';
	$style .= ' 	padding-top: ' . $form_input_padding_tb . 'px;';
	$style .= ' 	padding-bottom: ' . $form_input_padding_tb . 'px;';
	$style .= ' 	padding-left: ' . $form_input_padding_lr . 'px;';
	$style .= ' 	padding-right: ' . $form_input_padding_lr . 'px;';
	$style .= ' 	border-radius: ' . $input_border_radius . 'px;';
	$style .= '}';

	$style .= '.' . $style_id . ' .cp-form-container .cp-form-field  .cp-label { ';
	$style .= ' 	text-align: ' . $form_input_align . ';';
	$style .= ' 	font-size: ' . $form_input_font_size . 'px;';
	$style .= ' 	font-family: ' . $form_input_font . ';';
	$style .= ' 	color: ' . $form_input_color . ';';
	$style .= ' 	padding-top: ' . $form_input_padding_tb . 'px;';
	$style .= ' 	padding-bottom: ' . $form_input_padding_tb . 'px;';
	$style .= ' 	border-radius: ' . $input_border_radius . 'px;';
	$style .= '}';

	// Shadow to input.
	if ( '1' === $input_shadow ) {
		$style .= '.' . $style_id . ' .enable_input_shadow .cp-input ,.enable_input_shadow input.cp-number ,.enable_input_shadow select.cp-dropdown { ';
		$style .= '  -webkit-box-shadow: inset 1px 1px 2px 0px ' . $input_shadow_color . '!important;';
		$style .= '  -moz-box-shadow: inset 1px 1px 2px 0px ' . $input_shadow_color . '!important;';
		$style .= '  box-shadow: inset 1px 1px 2px 0px ' . $input_shadow_color . '!important;';

		$style .= '}';
	}

	// CSS - Submit.
	$style .= '.' . $style_id . ' .cp-form-container .cp-submit { ';
	$style .= ' 	padding-top: ' . $submit_button_tb_padding . 'px;';
	$style .= ' 	padding-bottom: ' . $submit_button_tb_padding . 'px;';
	$style .= ' 	padding-left: ' . $submit_button_lr_padding . 'px;';
	$style .= ' 	padding-right: ' . $submit_button_lr_padding . 'px;';
	$style .= '}';

	$c_hover  = ( isset( $a['button_bg_hover_color'] ) ) ? esc_attr( $a['button_bg_hover_color'] ) : 'not found';
	$c_normal = ( isset( $a['button_bg_color'] ) ) ? esc_attr( $a['button_bg_color'] ) : '';
	$light    = ( isset( $a['button_bg_gradient_color'] ) ) ? esc_attr( $a['button_bg_gradient_color'] ) : '';
	$h_color  = ( isset( $a['button_txt_hover_color'] ) ) ? esc_attr( $a['button_txt_hover_color'] ) : '';

	$class = ( isset( $a['btn_style'] ) ) ? $a['btn_style'] : '';

	// Apply box shadow to submit button - If its set & equals to - 1.
	if ( isset( $a['btn_shadow'] ) && '1' === $a['btn_shadow'] ) {
		$shadow .= 'box-shadow: 1px 1px 2px 0px rgba(66, 66, 66, 0.6);';
	}

	$radius = '';
	if ( isset( $a['btn_border_radius'] ) && '' !== $a['btn_border_radius'] ) {
		$radius = 'border-radius:' . esc_attr( $a['btn_border_radius'] ) . 'px;';
	}

	switch ( $class ) {

		case 'cp-btn-flat':     // Normal.
			$style .= '.' . $style_id . ' .cp-form-container .cp-submit.' . $class . ' { '
			. ' 	background: ' . $c_normal . '!important;'
			. $radius . $shadow
			. '}'

			// Hover.
			. '.' . $style_id . ' .cp-form-container .cp-submit.' . $class . ':hover { '
			. ' 	background: ' . $c_hover . '!important;'
			. '}';
			break;
		case 'cp-btn-3d':       // Normal.
			$style .= '.' . $style_id . ' .cp-form-container .cp-submit.' . $class . ' { '
			. ' 	background: ' . $c_normal . ' !important;'
			. ' 	box-shadow: 0 6px ' . $c_hover . ';'
			. ' 	position: relative;'
			. $radius
			. '}'

			// Hover.
			. '.' . $style_id . ' .cp-form-container .cp-submit.' . $class . ':hover { '
			. ' 	box-shadow: 0 4px ' . $c_hover . ';'
			. ' 	top: 2px;'
			. '}'

			// Active.
			. '.' . $style_id . ' .cp-form-container .cp-submit.' . $class . ':active { '
			. ' 	top: 6px;'
			. '		box-shadow: 0 0px ' . $c_hover . ';'
			. '}';
			break;
		case 'cp-btn-outline':  // Normal.
			$style .= '.' . $style_id . ' .cp-form-container .cp-submit.' . $class . ' { '
			. ' 	background: transparent;'
			. ' 	border: 2px solid ' . $c_normal . ';'
			. ' 	color: inherit;'
			. $shadow . $radius
			. '}'

			// Hover.
			. '.' . $style_id . ' .cp-form-container .cp-submit.' . $class . ':hover { '
			. ' 	background: ' . $c_hover . ';'
			. ' 	border: 2px solid ' . $c_hover . ';'
			. ' 	color: ' . $h_color
			. '}'

			// Inner span color inherit to apply hover color.
			. '.' . $style_id . ' .cp-form-container .cp-submit.' . $class . ':hover span { color: inherit !important ; } ';
			break;
		case 'cp-btn-gradient':     // Normal.
			$style .= '.' . $style_id . ' .cp-form-container .cp-submit.' . $class . ' { '
			. $shadow . $radius
			. ' 	border: none;'
			. '		background: -webkit-linear-gradient(' . $light . ', ' . $c_normal . ');'
			. '		background: -o-linear-gradient(' . $light . ', ' . $c_normal . ');'
			. '		background: -moz-linear-gradient(' . $light . ', ' . $c_normal . ');'
			. '		background: linear-gradient(' . $light . ', ' . $c_normal . ');'
			. '}'

			// Hover.
			. '.' . $style_id . ' .cp-form-container .cp-submit.' . $class . ':hover { '
			. ' 	background: ' . $c_normal . ';'
			. '}';
			break;
	}

	$style .= '.' . $style_id . ' .cp-form-field.cp-enable-box-shadow > div { '
	. 'border-radius: ' . $a['input_border_radius'] . 'px;'
	. '}';

	$free_ebook = isset( $a['modal_desc_bg_color'] ) ? $a['modal_desc_bg_color'] : '';
	if ( '' !== $free_ebook ) {
		$style .= '.' . $style_id . ' .cp-free-ebook .cp-all-inputs-wrap { '
		. 'background-color: ' . $free_ebook . ';'
		. '}';
	}
	// Append CSS code.
	echo '<style type="text/css" class="cp-form-css">' . $style . '</style>'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
