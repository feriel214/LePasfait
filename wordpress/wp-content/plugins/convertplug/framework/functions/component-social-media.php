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
 *  Module  - social_media
 *
 * 1.   Social Array Setup
 * 2.   Global array for shortcode variables
 * 3.   Generate Output by 'cp_get_social' filter
 * 4.   Generate & Append CSS
 *
 *  Use same names for variables & array
 *  For '$your_options_name' use '$your_options_name_VARS'
 *
 *  E.g.    $cp_social
 *          $cp_social_vars
 *
 * @since  1.1.1
 */
global $cp_social;
global $cp_social_vars;

/*
 * 1.	Social Array Setup.
 */
$option_array = '';
if ( isset( $_REQUEST['cp_admin_page_nonce'] ) && ! wp_verify_nonce( $_REQUEST['cp_admin_page_nonce'], 'cp_admin_page' ) ) {
	return;
}
if ( isset( $_GET['theme'] ) && 'floating_social_bar' === $_GET['theme'] ) {
	$option_array = array(
		__( 'Normal', 'smile' ) => 'normal',
		__( 'Border', 'smile' ) => 'border',
		__( 'Flip', 'smile' )   => 'flip',
		__( 'Grow', 'smile' )   => 'grow',
	);
} else {
	$option_array = array(
		__( 'Normal', 'smile' ) => 'normal',
		__( 'Slide', 'smile' )  => 'slide',
	);
}

$cp_social = array(
	array(
		'type'         => 'section',
		'class'        => '',
		'name'         => 'social_media_section',
		'opts'         => array(
			'title' => 'Essential Configurations',
			'link'  => '',
			'value' => '',
		),
		'panel'        => 'Social Networks',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
	),
	array(
		'type'         => 'social_media',
		'class'        => '',
		'name'         => 'cp_social_icon',
		'opts'         => array(
			'title' => '',
			'value' => '',
		),
		'panel'        => 'Social Networks',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
	),
	array(
		'type'         => 'section',
		'class'        => '',
		'name'         => 'social_media_layout',
		'opts'         => array(
			'title' => 'Layout',
			'link'  => '',
			'value' => '',
		),
		'panel'        => 'Social Networks',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
	),
	array(
		'type'         => 'radio-image',
		'class'        => '',
		'name'         => 'cp_social_icon_style',
		'opts'         => array(
			'title'      => __( 'Layout', 'smile' ),
			'value'      => 'cp-icon-style-left',
			'options'    => array(
				__( 'cp-icon-style-left', 'smile' )      => CP_BASE_URL . '/modules/assets/images/icon_with_left.png',
				__( 'cp-icon-style-right', 'smile' )     => CP_BASE_URL . '/modules/assets/images/icon_with_right.png',
				__( 'cp-icon-style-simple', 'smile' )    => CP_BASE_URL . '/modules/assets/images/simple_icon.png',
				__( 'cp-icon-style-rectangle', 'smile' ) => CP_BASE_URL . '/modules/assets/images/icon_with_square.png',
			),
			'width'      => '125px',
			'imagetitle' => array(
				__( 'title-0', 'smile' ) => 'Icon At Left',
				__( 'title-1', 'smile' ) => 'Icon At Right',
				__( 'title-2', 'smile' ) => 'Icon At Left Without Background',
				__( 'title-3', 'smile' ) => 'Icon At Top',
			),
		),
		'panel'        => 'Social Networks',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
	),
	array(
		'type'         => 'dropdown',
		'class'        => '',
		'name'         => 'cp_social_icon_column',
		'opts'         => array(
			'title'       => __( 'Number of Columns', 'smile' ),
			'value'       => 'auto',
			'options'     => array(
				__( 'Auto Width', 'smile' ) => 'auto',
				__( '1', 'smile' )          => '1',
				__( '2', 'smile' )          => '2',
				__( '3', 'smile' )          => '3',
				__( '4', 'smile' )          => '4',
				__( '5', 'smile' )          => '5',
				__( '6', 'smile' )          => '6',
			),
			'description' => __( 'Select grid to display social icons', 'smile' ),
		),
		'panel'        => 'Social Networks',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
	),
	array(
		'type'         => 'dropdown',
		'class'        => '',
		'name'         => 'cp_social_icon_align',
		'opts'         => array(
			'title'       => __( ' Icon Container Alignment', 'smile' ),
			'value'       => 'auto',
			'options'     => array(
				__( 'Center', 'smile' ) => 'center',
				__( 'Left', 'smile' )   => 'left',
				__( 'Right', 'smile' )  => 'right',
			),
			'description' => __( 'Select alignment for icon container', 'smile' ),
		),
		'dependency'   => array(
			'name'     => 'cp_social_icon_column',
			'operator' => '==',
			'value'    => 'auto',
		),
		'panel'        => 'Social Networks',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
	),
	array(
		'type'         => 'switch',
		'class'        => '',
		'name'         => 'cp_social_remove_icon_spacing',
		'opts'         => array(
			'title'       => __( 'Remove Icon Spacing', 'smile' ),
			'value'       => false,
			'on'          => __( 'YES', 'smile' ),
			'off'         => __( 'NO', 'smile' ),
			'description' => __( 'Remove gap / spacing between two social icons', 'smile' ),
		),
		'panel'        => 'Social Networks',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
	),
	array(
		'type'         => 'section',
		'class'        => '',
		'name'         => 'social_media_styling',
		'opts'         => array(
			'title' => 'Styling',
			'link'  => '',
			'value' => '',
		),
		'panel'        => 'Social Networks',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
	),
	array(
		'type'         => 'slider',
		'class'        => '',
		'name'         => 'social_container_border',
		'opts'         => array(
			'title'        => __( 'Icon Container Border Radius', 'smile' ),
			'value'        => 5,
			'min'          => 0,
			'max'          => 50,
			'step'         => 1,
			'suffix'       => 'px',
			'css_property' => 'border-radius',
			'css_selector' => '.cp_social_networks.cp_social_left li',
			'css_preview'  => true,
			'description'  => __( 'Apply border radius to icon container.', 'smile' ),
		),
		'dependency'   => array(
			'name'     => 'cp_social_icon_style',
			'operator' => '!==',
			'value'    => 'cp-icon-style-simple',
		),
		'panel'        => 'Social Networks',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
	),
	array(
		'type'         => 'dropdown',
		'class'        => '',
		'name'         => 'cp_social_icon_shape',
		'opts'         => array(
			'title'       => __( 'Icon Shape', 'smile' ),
			'value'       => 'Normal',
			'options'     => array(
				__( 'Normal', 'smile' ) => 'normal',
				__( 'Square', 'smile' ) => 'square',
				__( 'Circle', 'smile' ) => 'circle',
				__( 'Custom', 'smile' ) => 'border_radius',
			),
			'description' => __( 'Provide shape to your icon.', 'smile' ),
		),
		'panel'        => 'Social Networks',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
	),
	array(
		'type'         => 'slider',
		'class'        => '',
		'name'         => 'social_icon_border',
		'opts'         => array(
			'title'        => __( 'Icon Border Radius', 'smile' ),
			'value'        => 5,
			'min'          => 0,
			'max'          => 50,
			'step'         => 1,
			'suffix'       => 'px',
			'css_property' => 'border-radius',
			'css_selector' => '.cp-border_radius .cp_social_icon ,.cp-slidein .cp-icon-style-top.cp-border_radius li',
			'css_preview'  => true,
			'description'  => __( 'Apply border radius to actual icon.', 'smile' ),
		),
		'dependency'   => array(
			'name'     => 'cp_social_icon_shape',
			'operator' => '==',
			'value'    => 'border_radius',
		),
		'panel'        => 'Social Networks',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
	),
	array(
		'type'         => 'dropdown',
		'class'        => '',
		'name'         => 'cp_social_icon_effect',
		'opts'         => array(
			'title'       => __( 'Icon Effect', 'smile' ),
			'value'       => 'gradient',
			'options'     => array(
				__( 'Flat', 'smile' )    => 'flat',
				__( '3D', 'smile' )      => '3D',
				__( 'Overlay', 'smile' ) => 'gradient',
			),
			'description' => __( 'Style your icon container with nice effects.', 'smile' ),
		),
		'panel'        => 'Social Networks',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
	),
	array(
		'type'         => 'dropdown',
		'class'        => '',
		'name'         => 'cp_social_icon_hover_effect',
		'opts'         => array(
			'title'       => __( 'Icon Hover Effect', 'smile' ),
			'value'       => 'Slide',
			'options'     => $option_array,
			'description' => __( 'Apply slide / normal hover effect to icon.', 'smile' ),
		),
		'panel'        => 'Social Networks',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
	),

	array(
		'type'         => 'switch',
		'class'        => '',
		'name'         => 'cp_social_enable_icon_color',
		'opts'         => array(
			'title'       => __( 'Use Custom Colors', 'smile' ),
			'value'       => false,
			'on'          => __( 'YES', 'smile' ),
			'off'         => __( 'NO', 'smile' ),
			'description' => __( 'Style your icons with custom colors.', 'smile' ),
		),
		'panel'        => 'Social Networks',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
	),

	array(
		'type'         => 'colorpicker',
		'class'        => '',
		'name'         => 'cp_social_icon_color',
		'opts'         => array(
			'title'        => __( 'Icon Color', 'smile' ),
			'value'        => 'rgb(255, 255, 255)',
			'css_property' => 'color',
			'css_selector' => '.cp-custom-sc-color i.cp_social_icon , .cp-icon-style-top.cp-normal.cp-custom-sc-color i.cp_social_icon ',
			'css_preview'  => true,
		),
		'dependency'   => array(
			'name'     => 'cp_social_enable_icon_color',
			'operator' => '==',
			'value'    => 'true',
		),
		'panel'        => 'Social Networks',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
	),
	array(
		'type'         => 'colorpicker',
		'class'        => '',
		'name'         => 'cp_social_text_color',
		'opts'         => array(
			'title' => __( 'Text Color', 'smile' ),
			'value' => 'rgb(255, 255, 255)',
		),
		'dependency'   => array(
			'name'     => 'cp_social_enable_icon_color',
			'operator' => '==',
			'value'    => 'true',
		),
		'panel'        => 'Social Networks',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
	),
	array(
		'type'         => 'colorpicker',
		'class'        => '',
		'name'         => 'cp_social_icon_bgcolor',
		'opts'         => array(
			'title'        => __( 'Background Color', 'smile' ),
			'value'        => '#107fc9',
			'css_property' => 'background',
			'css_selector' => '.cp_social_networks.cp-custom-sc-color li ,.cp_social_networks.cp-custom-sc-color.cp_social_simple li .cp_social_icon ,.cp_social_networks.cp-custom-sc-color.cp_social_circle li .cp_social_icon',
			'css_preview'  => true,
		),
		'dependency'   => array(
			'name'     => 'cp_social_enable_icon_color',
			'operator' => '==',
			'value'    => 'true',
		),
		'panel'        => 'Social Networks',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
	),
	array(
		'type'         => 'colorpicker',
		'class'        => '',
		'name'         => 'cp_social_icon_hover',
		'opts'         => array(
			'title'        => __( 'Icon Hover Color', 'smile' ),
			'value'        => 'rgb(255, 255, 255)',
			'css_property' => 'color',
			'css_selector' => '.cp-custom-sc-color i.cp_social_icon:hover ,.cp-custom-sc-color li:hover i.cp_social_icon',
			'css_preview'  => true,
		),
		'dependency'   => array(
			'name'     => 'cp_social_enable_icon_color',
			'operator' => '==',
			'value'    => 'true',
		),
		'panel'        => 'Social Networks',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
	),
	array(
		'type'         => 'colorpicker',
		'class'        => '',
		'name'         => 'cp_social_text_hover_color',
		'opts'         => array(
			'title' => __( 'Text Hover Color', 'smile' ),
			'value' => 'rgb(255, 255, 255)',
		),
		'dependency'   => array(
			'name'     => 'cp_social_enable_icon_color',
			'operator' => '==',
			'value'    => 'true',
		),
		'panel'        => 'Social Networks',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
	),
	array(
		'type'         => 'colorpicker',
		'class'        => '',
		'name'         => 'cp_social_icon_bghover',
		'opts'         => array(
			'title'        => __( 'Background Hover Color', 'smile' ),
			'value'        => '#0e72b4',
			'css_property' => 'background',
			'css_selector' => '.cp_social_networks.cp-custom-sc-color li:hover ,.cp_social_networks.cp-custom-sc-color.cp_social_simple li:hover .cp_social_icon,.cp_social_networks.cp-custom-sc-color.cp_social_circle li:hover .cp_social_icon ',
			'css_preview'  => true,
		),
		'dependency'   => array(
			'name'     => 'cp_social_enable_icon_color',
			'operator' => '==',
			'value'    => 'true',
		),
		'panel'        => 'Social Networks',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
	),
	array(
		'type'         => 'section',
		'class'        => '',
		'name'         => 'social_media_Advanced',
		'opts'         => array(
			'title' => 'Advanced',
			'link'  => '',
			'value' => '',
		),
		'panel'        => 'Social Networks',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
	),
	array(
		'type'         => 'switch',
		'class'        => '',
		'name'         => 'cp_display_nw_name',
		'opts'         => array(
			'title'       => __( 'Display Network Names', 'smile' ),
			'value'       => true,
			'on'          => __( 'YES', 'smile' ),
			'off'         => __( 'NO', 'smile' ),
			'description' => __( 'Show / hide social network name.', 'smile' ),
		),
		'panel'        => 'Social Networks',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
	),

	array(
		'type'         => 'switch',
		'class'        => '',
		'name'         => 'cp_social_share_count',
		'opts'         => array(
			'title'       => __( 'Display Share Counts', 'smile' ),
			'value'       => false,
			'on'          => __( 'YES', 'smile' ),
			'off'         => __( 'NO', 'smile' ),
			'description' => __( 'Show / hide share counts.', 'smile' ),
		),
		'panel'        => 'Social Networks',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
	),
	array(
		'type'         => 'slider',
		'class'        => '',
		'name'         => 'social_min_count',
		'opts'         => array(
			'title'       => __( 'Minimum Count Display', 'smile' ),
			'value'       => 50,
			'min'         => 0,
			'max'         => 1000,
			'step'        => 1,
			'description' => __( 'Display minimum share count number until actual count increases. Actual count will display only on front end.', 'smile' ),
		),
		'dependency'   => array(
			'name'     => 'cp_social_share_count',
			'operator' => '==',
			'value'    => 'true',
		),
		'panel'        => 'Social Networks',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
	),

	// store button darken on hover.
	array(
		'type'         => 'textfield',
		'name'         => 'social_darken',
		'opts'         => array(
			'title' => __( 'Button BG Hover Color', 'smile' ),
			'value' => '',
		),
		'dependency'   => array(
			'name'     => 'hidden',
			'operator' => '==',
			'value'    => 'hide',
		),
		'panel'        => 'Social Networks',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
	),
	// store button lighten gradient.
	array(
		'type'         => 'textfield',
		'name'         => 'social_lighten',
		'opts'         => array(
			'title' => __( 'Button Gradient Color', 'smile' ),
			'value' => '',
		),
		'dependency'   => array(
			'name'     => 'hidden',
			'operator' => '==',
			'value'    => 'hide',
		),
		'panel'        => 'Social Networks',
		'section'      => 'Design',
		'section_icon' => 'connects-icon-disc',
	),

);


/**
 * Global array for shortcode variables.
 */

$cp_social_vars = generate_global_shortcode_vars( $cp_social );


add_filter( 'cp_get_social', 'cp_get_social_init' );

if ( ! function_exists( 'cp_get_social_init' ) ) {
	/**
	 * Function Name: cp_get_social_init Generate Output by 'cp_get_social' filter.
	 *
	 * @param  array $a setting array.
	 */
	function cp_get_social_init( $a ) {
		if ( ! empty( $a['cp_social_icon'] ) ) {

			$cp_social_icon_column         = $a['cp_social_icon_column'];
			$cp_social_icon_style          = $a['cp_social_icon_style'];
			$cp_display_nw_name            = $a['cp_display_nw_name'];
			$cp_social_icon_shape          = $a['cp_social_icon_shape'];
			$cp_social_icon_effect         = $a['cp_social_icon_effect'];
			$cp_social_enable_icon_color   = $a['cp_social_enable_icon_color'];
			$cp_social_icon_color          = $a['cp_social_icon_color'];
			$cp_social_icon_bgcolor        = $a['cp_social_icon_bgcolor'];
			$cp_social_icon_bghover        = $a['cp_social_icon_bghover'];
			$cp_social_share_count         = $a['cp_social_share_count'];
			$social_min_count              = $a['social_min_count'];
			$cp_social_remove_icon_spacing = $a['cp_social_remove_icon_spacing'];
			$cp_social_icon_hover_effect   = $a['cp_social_icon_hover_effect'];

			// apply social styles.
			apply_filters_ref_array( 'cp_social_css', array( $a ) );

			$social_arr = explode( ';', $a['cp_social_icon'] );

			$array = array();
			foreach ( $social_arr as $key => $value ) {
				$single     = explode( '|', $value );
				$item_array = array();
				foreach ( $single as $key1 => $value1 ) {
					$s = explode( ':', $value1, 2 );
					if ( '' !== $s[0] ) {
						$item_array[ $s[0] ] = $s[1];
					}
				}
				array_push( $array, $item_array );
			}

			/**
		 * Build HTML structure for Social_icon.
		 */
			if ( '' === $cp_social_icon_style || 'undefined' === $cp_social_icon_style ) {
				$cp_social_icon_style = 'cp-icon-style-top';
			}
			if ( '' === $cp_display_nw_name || 'undefined' === $cp_display_nw_name ) {
				$cp_display_nw_name = false;
			}
			if ( '' === $cp_social_icon_column || 'undefined' === $cp_social_icon_column ) {
				$cp_social_icon_column = '1';
			}

			if ( '' === $cp_social_icon_effect || 'undefined' === $cp_social_icon_effect ) {
				$cp_social_icon_effect = 'none';
			}

			// apply no of column to container.
			if ( 'auto' === $cp_social_icon_column ) {
				$cp_social_icon_column_class = 'autowidth';
			} else {
				$cp_social_icon_column_class = 'col_' . $cp_social_icon_column;
			}

			// if count and nw name is not present.
			$no_count = '';
			if ( 'cp-icon-style-rectangle' === $cp_social_icon_style && 'gradient' === $cp_social_icon_effect && '1' !== $cp_display_nw_name && '1' !== $cp_social_share_count ) {
				$no_count .= 'cp-no-count-no-share';
			}

			// style class.
			$class_icon_hover_effect = '';
			if ( 'slide' === $cp_social_icon_hover_effect ) {
				switch ( $cp_social_icon_style ) {
					case 'cp-icon-style-simple':
						$class_icon_hover_effect = 'cp_social_slide';
						break;

					case 'cp-icon-style-rectangle':
						$class_icon_hover_effect = 'cp_social_slide';
						break;

					case 'cp-icon-style-right':
						$class_icon_hover_effect = 'cp_social_flip';
						break;

					case 'cp-icon-style-left':
						$class_icon_hover_effect = 'cp_social_flip';
						break;
				}
			}

			// apply style to icon.
			$class_list = '';
			if ( 'cp-icon-style-simple' === $cp_social_icon_style ) {
				$class_list .= 'cp_social_simple ' . $class_icon_hover_effect;
			} else {
				if ( 'cp-icon-style-rectangle' === $cp_social_icon_style ) {
					$class_list .= ' ' . $class_icon_hover_effect;
				} else {
					$class_list .= ' ' . $class_icon_hover_effect;
				}
			}

			// spacing.
			if ( 1 === $cp_social_remove_icon_spacing || '1' === $cp_social_remove_icon_spacing ) {
				$class_list .= ' cp-no-spacing';
			}

			if ( 'cp-icon-style-top' === $cp_social_icon_style ) {
				$cp_social_icon_column_class .= ' cp-hover-' . $cp_social_icon_hover_effect;
				if ( 0 === $cp_social_share_count ) {
					$cp_social_icon_column_class .= ' cp-network-without-count';
				}
			}

			if ( 1 === $cp_social_enable_icon_color || '1' === $cp_social_enable_icon_color ) {
				$class_list .= ' cp-custom-color';
			}

			// instafollow.
			$cp_settings = get_option( 'convert_plug_settings' );
			$show_insta  = isset( $cp_settings['cp-show-insta-settings'] ) ? $cp_settings['cp-show-insta-settings'] : '';
			$insta_data  = '';
			if ( '1' === $show_insta ) {
				if ( $cp_settings['cp-insta-username'] ) {
					$insta_data .= 'data-insta-uname="' . $cp_settings['cp-insta-username'] . '" ';
				}
				if ( $cp_settings['cp-insta-user-id'] ) {
					$insta_data .= 'data-insta-uid="' . $cp_settings['cp-insta-user-id'] . '" ';
				}
				if ( $cp_settings['cp-insta-client-id'] ) {
					$insta_data .= 'data-insta-client-id="' . $cp_settings['cp-insta-client-id'] . '" ';
				}
				if ( $cp_settings['cp-insta-redirect-url'] ) {
					$insta_data .= 'data-insta-redirect-url="' . $cp_settings['cp-insta-redirect-url'] . '" ';
				}
			}

			$social_html  = '';
			$insta_info   = '';
			$social_html .= '<div class="cp_social_networks cp_social_' . $cp_social_icon_column_class . ' cp_social_left cp_social_withcounts cp_social_withnetworknames ' . $cp_social_icon_style . ' ' . $class_list . ' cp-' . $cp_social_icon_shape . ' cp_' . $cp_social_icon_effect . ' ' . $no_count . '" data-column-no ="cp_social_' . $cp_social_icon_column_class . '">';

			$social_html .= ' <ul class="cp_social_icons_container">';

			foreach ( $array as $key => $value ) {
				$input_type   = strtolower( $value['input_type'] );
				$network_name = $value['input_type'];
				$newnw        = $value['network_name'];
				if ( '' !== $newnw ) {
					$network_name = $newnw;
				}

				$profile_link_name = 'javascript:void(0)';
				$current_page      = '';

				if ( isset( $value['profile_link'] ) && '' !== $value['profile_link'] ) {
					$profile_link_name = urldecode( $value['profile_link'] );
				}

				if ( isset( $value['smile_adv_share_opt'] ) ) {
					if ( '1' === $value['smile_adv_share_opt'] ) {
						if ( isset( $value['input_share'] ) ) {
							$current_page = urldecode( $value['input_share'] );
						}
					} else {
						$protocol = 'http://';
						if ( isset( $_SERVER['HTTPS'] ) ) {
							$protocol = ( $_SERVER['HTTPS'] && 'off' !== $_SERVER['HTTPS'] ) ? 'https://' : 'http://';
						}

						$current_page = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
					}
				}

				$input_action = strtolower( $value['input_action'] );

				$url      = '';
				$test_jav = '';
				if ( 'profile_link' === $input_action ) {
					$url = $profile_link_name;

				} elseif ( 'follow' === $input_action ) {
					if ( 'instagram' === $input_type ) {
						$url = 'javascript:void(0)';
					}
				} else {
					switch ( $input_type ) {
						case 'facebook':
							$url = 'http://www.facebook.com/sharer.php?u=' . $current_page;
							break;

						case 'twitter':
							$url = 'https://twitter.com/share?url=' . $current_page;
							break;

						case 'google':
							$url = 'https://plus.google.com/share?url=' . $current_page;
							break;

						case 'pinterest':
							$media_url = isset( $value['input_img'] ) ? $value['input_img'] : '';
							if ( '' !== $media_url ) {
								$url = 'https://pinterest.com/pin/create/button/?url=' . $current_page . '&media=' . $media_url;

							} else {
								$url = 'https://pinterest.com/pin/create/link/?url=' . $current_page;
							}

							break;

						case 'linkedin':
							$url = 'http://www.linkedin.com/shareArticle?url=' . $current_page;
							break;

						case 'digg':
							$url = 'http://digg.com/submit?url=' . $current_page;
							break;

						case 'blogger':
							$url = 'https://www.blogger.com/blog_this.pyra?t&amp;u=' . $current_page;
							break;

						case 'reddit':
							$url = 'http://reddit.com/submit?url=' . $current_page;
							break;

						case 'stumbleupon':
							$url = 'http://www.stumbleupon.com/submit?url=' . $current_page;
							break;

						case 'tumblr':
							$url = 'https://www.tumblr.com/widgets/share/tool?canonicalUrl=' . $current_page;
							break;

						case 'myspace':
							$url = 'https://myspace.com/post?u=' . $current_page;
							break;

					}
				}

				$social_html .= '<li class="cp_social_' . $input_type . '">';

				$display_count_class = '';
				if ( '1' === $cp_social_share_count || 1 == $cp_social_share_count ) {
					$display_count_class = ' cp_social_display_count';
				}

				if ( 'profile_link' === $input_action ) {
					$social_html .= '<a href = ' . $url . " class='cp_social_share " . $display_count_class . "'  target='_blank' >";
				} elseif ( 'follow' === $input_action ) {
					$social_html .= '<a href = ' . $url . " class='cp_social_share cp_scoial_follow " . $display_count_class . "'  target='_blank' $insta_info >";
				} else {
					$social_html .= '<a href="' . $url . '" class="cp_social_share ' . $display_count_class . '" onclick="window.open(this.href,\'mywin\',\'left=20,top=20,width=500,height=500,toolbar=1,resizable=0\');return false">';
				}

				$social_html .= '<i class="cp_social_icon cp_social_icon_' . $input_type . '"></i>';
				// display label.
				if ( '1' === $cp_display_nw_name || '1' === $cp_social_share_count ) {
					$social_html .= '<div class="cp_social_network_label">';
				}

				// display network name.
				if ( '1' === $cp_display_nw_name ) {
					$social_html .= '<div class="cp_social_networkname">' . $network_name . '</div>';
				}

				// display share count.
				if ( '1' === $cp_social_share_count ) {
					if ( '' !== $social_min_count ) {

						$shar_cnt = 0;

						if ( '' == $current_page ) {
							$shar_cnt = 0;
						} else {
							if ( 'facebook' === $input_type ) {
								$access_token = isset( $value['fb-access-token'] ) ? $value['fb-access-token'] : '';
								$current_page = $current_page . '&access_token=' . $access_token;
							}
							$shar_cnt = cp_get_share_count( $input_type, $current_page );

							if ( $shar_cnt <= $social_min_count ) {
								$shar_cnt = $social_min_count;

							}
						}
						$social_html .= '<div class="cp_social_count"><span>' . $shar_cnt . '</span></div>';
					}
				}

				// close label div.
				if ( '1' === $cp_display_nw_name || '1' === $cp_social_share_count ) {
					$social_html .= '</div>';
				}

				if ( 'gradient' === $cp_social_icon_effect ) {
					$social_html .= '<div class="cp_social_overlay"></div>';
				}

				$social_html .= '</a>'
				. ' </li>';

			}

			$social_html .= '</ul>';   // end of cp_social_icons_container.
			$social_html .= '</div>';// end of cp_social_networks.

			echo wp_kses_post( $social_html );

		}// end of empty.

	}
}


add_filter( 'cp_social_css', 'cp_social_css_init' );
/**
 * Generate & Append CSS.
 *
 * @param  array $a setting array.
 */
function cp_social_css_init( $a ) {
	$styleid                     = ( isset( $a['uid_class'] ) ) ? esc_attr( $a['uid_class'] ) : '';
	$cp_social_icon_column       = $a['cp_social_icon_column'];
	$cp_social_icon_style        = $a['cp_social_icon_style'];
	$cp_display_nw_name          = $a['cp_display_nw_name'];
	$cp_social_icon_shape        = $a['cp_social_icon_shape'];
	$cp_social_icon_effect       = $a['cp_social_icon_effect'];
	$cp_social_enable_icon_color = $a['cp_social_enable_icon_color'];
	$icon_color                  = $a['cp_social_icon_color'];
	$icon_bgcolor                = $a['cp_social_icon_bgcolor'];
	$icon_bghover                = $a['cp_social_icon_bghover'];
	$icon_hover                  = $a['cp_social_icon_hover'];
	$social_icon_border          = $a['social_icon_border'];
	$social_container_border     = $a['social_container_border'];
	$cp_social_icon_align        = $a['cp_social_icon_align'];
	$cp_social_text_hover_color  = $a['cp_social_text_hover_color'];
	$cp_social_text_color        = $a['cp_social_text_color'];
	$social_style                = '';

	$light   = $a['social_lighten'];
	$c_hover = $a['social_darken'];

	if ( '' === $cp_social_icon_style || 'undefined' === $cp_social_icon_style ) {
		$cp_social_icon_style = 'cp-icon-style-top';
	}

	// to use user defined color for icon.
	if ( 1 === $cp_social_enable_icon_color || '1' === $cp_social_enable_icon_color ) {
		$social_style = '.' . $styleid . ' .cp_social_networks li ,'
		. '.' . $styleid . ' .cp_social_networks.cp_social_simple li .cp_social_icon ,'
		. '.' . $styleid . ' .cp_social_networks.cp_social_circle li .cp_social_icon {'
		. '    background:' . $icon_bgcolor
		. ' }'
		. '.' . $styleid . ' .cp_social_networks li:hover {'
		. '    background:' . $icon_bghover
		. ' }'
		. '.' . $styleid . '  .cp_social_networks li .cp_social_icon ,'
		. '.' . $styleid . '  .cp_social_networks.cp_social_simple li .cp_social_icon ,'
		. '.' . $styleid . '  .cp_social_networks.cp_social_circle li .cp_social_icon {'
		. '     color:' . $icon_color
		. ' }'
		. '.' . $styleid . ' .cp_social_networks li:hover .cp_social_icon{'
		. '      color: ' . $icon_hover
		. ' }'
		. '.' . $styleid . ' .cp_social_networks.cp_social_simple li:hover .cp_social_icon ,'
		. '.' . $styleid . ' .cp_social_networks.cp_social_circle li:hover .cp_social_icon {'
		. '    background:' . $icon_bghover . '!important'
		. ' }';

		if ( '3D' === $cp_social_icon_effect ) {
			$social_style .= '.' . $styleid . ' .cp_3D li,'
			. '.' . $styleid . ' .cp_social_networks.cp_social_simple.cp_3D li i ,'
			. '.' . $styleid . ' .cp_social_networks.cp_social_circle.cp_3D li i{'
			. '    -moz-box-shadow: 0 4px ' . $light . '!important;'
			. '    -webkit-box-shadow: 0 4px ' . $light . '!important;'
			. '    -o-box-shadow: 0 4px ' . $light . '!important;'
			. '    box-shadow: 0 4px ' . $light . '!important;'
			. ' }'
			. '.' . $styleid . ' .cp_3D li:hover,'
			. '.' . $styleid . ' .cp_social_networks.cp_social_simple.cp_3D li:hover i ,'
			. '.' . $styleid . ' .cp_social_networks.cp_social_circle.cp_3D li:hover i {'
			. '    -moz-box-shadow: 0 4px ' . $c_hover . '!important;'
			. '    -webkit-box-shadow: 0 4px ' . $c_hover . '!important;'
			. '    -o-box-shadow: 0 4px ' . $c_hover . '!important;'
			. '    box-shadow: 0 4px ' . $c_hover . '!important;'
			. ' }';

			if ( 'square' === $cp_social_icon_shape && 'cp-icon-style-simple' === $cp_social_icon_style ) {
				$social_style .= '.' . $styleid . ' .cp_3D .cp_social_share {'
				. '     padding: 5px;'
				. ' }';
			}
		}

		// if icon style is normal.
		$social_style .= '.' . $styleid . ' .cp-icon-style-simple.cp-normal i,'
		. '.' . $styleid . ' .cp_social_networks.cp_social_simple.cp-icon-style-simple.cp-normal i {'
		. '   color:' . $icon_color . '!important;'
		. '    background-color:transparent!important;'
		. ' }'
		. '.' . $styleid . ' .cp-icon-style-simple.cp-normal li:hover i ,'
		. '.' . $styleid . ' .cp_social_networks.cp_social_simple.cp-icon-style-simple.cp-normal li:hover i {'
		. '   color:' . $icon_hover . '!important;'
		. '    background-color:transparent!important;'
		. ' }';

		// apply custom text color.
		$social_style .= '.' . $styleid . ' .cp_social_networks .cp_social_network_label ,'
		. '.' . $styleid . ' .cp_social_networks .cp_social_networkname ,'
		. '.' . $styleid . ' .cp_social_networks .cp_social_count{'
		. '   color:' . $cp_social_text_color . '!important;'
		. ' }'
		. '.' . $styleid . ' .cp_social_networks li:hover .cp_social_network_label, '
		. '.' . $styleid . ' .cp_social_networks li:hover .cp_social_networkname,'
		. '.' . $styleid . ' .cp_social_networks li:hover .cp_social_count,'
		. '.' . $styleid . ' .cp_social_networks li:hover .cp_social_count span{'
		. '   color:' . $cp_social_text_hover_color . '!important;'
		. ' }';

		// set visited color none.
		$social_style .= '.' . $styleid . ' .cp_social_networks li a:visited, .cp_social_networks li a:visited * {'
		. '      color: inherit;'
		. ' }';

	} else {
		if ( ( '3D' === $cp_social_icon_effect && 'square' === $cp_social_icon_shape ) && ( 'cp-icon-style-simple' === $cp_social_icon_style ) ) {
			$social_style .= '.' . $styleid . ' .cp_3D .cp_social_share {'
			. '     padding: 5px;'
			. ' }';
		}
	}

	// if icon shape is custom.
	if ( 'border_radius' === $cp_social_icon_shape ) {
		$social_style .= '.' . $styleid . ' .cp_social_networks i.cp_social_icon {'
		. '     border-radius: ' . $social_icon_border . 'px;'
		. ' }';
		if ( 'cp-icon-style-top' === $cp_social_icon_style ) {
			$social_style .= '.' . $styleid . ' .cp_social_networks li {'
			. '     border-radius: ' . $social_icon_border . 'px!important;'
			. ' }';
		}
	}

	// if apply border-radius to container.
	if ( 'cp-icon-style-simple' !== $cp_social_icon_style && 'cp-icon-style-top' !== $cp_social_icon_style && '' !== $social_container_border ) {
		$social_style .= '.' . $styleid . ' .cp_social_networks.cp_social_left li {'
		. '     border-radius: ' . $social_container_border . 'px;'
		. ' }';
	}

	// apply no of column to container.
	if ( 'auto' === $cp_social_icon_column ) {
		$social_style .= '.' . $styleid . ' .cp_social_networks .cp_social_icons_container {'
		. '     margin-bottom: -15px!important;'
		. ' }';

		$social_style .= '.' . $styleid . ' .cp_social_networks.cp_social_autowidth .cp_social_icons_container {'
		. '     text-align:' . $cp_social_icon_align . ';'
		. ' }';
	}

	// Append CSS code.
	echo '<style type="text/css" class="cp-social-css">' . $social_style . '</style>'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

if ( ! function_exists( 'cp_get_share_count' ) ) {
	/**
	 * Function Name: cp_get_share_count GET Share Count.
	 *
	 * @param  string $type         n/w name.
	 * @param  string $current_page pageurl.
	 * @return integer               count values.
	 */
	function cp_get_share_count( $type, $current_page ) {
		$share_array = array( 'facebook', 'google', 'linkedin', 'pinterest' );
		$sec_array   = array( 'reddit', 'stumbleupon' );
		$count_name  = '';
		$count_data  = '';
		$count_url   = '';
		$count       = '';
		$shares      = '';
		$count_name  = strtolower( $type );

		if ( in_array( $count_name, $share_array ) ) {
			if ( 'facebook' === $count_name ) {
				$count_url = 'https://graph.facebook.com/?id=' . $current_page . '&fields=engagement';
			} elseif ( 'google' === $count_name ) {
				$count_url = 'https://plus.google.com/share?url=' . $current_page;
			} elseif ( 'linkedin' === $count_name ) {
				$count_url = 'http://www.linkedin.com/countserv/count/share?url=' . $current_page . '&format=json';
			} elseif ( 'pinterest' === $count_name ) {
				$count_url = 'https://api.pinterest.com/v1/urls/count.json?callback=pin&url=' . $current_page;
			} else {
				$count_url = 'https://count.donreach.com/?url=' . $current_page;
			}
		}

		if ( in_array( $count_name, $sec_array ) ) {
			if ( 'reddit' === $count_name ) {
				$count_url = 'http://www.reddit.com/api/info.json?&url=' . $current_page;

			} elseif ( 'stumbleupon' === $count_name ) {
				$count_url = 'http://www.stumbleupon.com/services/1.01/badge.getinfo?url=' . $current_page;

			} else {
				$count_url = 'http://share-count.appspot.com/?url=' . $current_page;
			}
		}

		if ( 'twitter' === $count_name ) {
			$count_url = 'http://opensharecount.com/count.json?url=' . $current_page;
		}

		$count_response = wp_remote_get( sprintf( $count_url ) );

		if ( is_wp_error( $count_response ) || ( 200 !== wp_remote_retrieve_response_code( $count_response ) ) ) {
			return false;
		}

		if ( 'pinterest' === $count_name ) {
			$response   = wp_remote_retrieve_body( $count_response );
			$response   = str_replace( 'pin({', '{', $response );
			$response   = str_replace( '})', '}', $response );
			$count_data = json_decode( $response, true );
		} else {
			$count_data = json_decode( wp_remote_retrieve_body( $count_response ), true );
		}

		if ( ! is_array( $count_data ) ) {
			return false;
		}

		if ( 'facebook' === $count_name ) {
			$shares = isset( $count_data['engagement']['share_count'] ) ? $count_data['engagement']['share_count'] : '';
			if ( '' !== $shares ) {
				return $shares;
			}
		} elseif ( 'twitter' === $count_name ) {
			$shares = isset( $count_data['count'] ) ? $count_data['count'] : '';
			if ( '' !== $shares ) {
					return $shares;
			}
		} elseif ( 'google' === $count_name ) {
			$shares = isset( $count_data['share']['share_count'] ) ? $count_data['share']['share_count'] : '';
			if ( '' !== $shares ) {
				return $shares;
			}
		} elseif ( 'linkedin' === $count_name ) {
			$shares = isset( $count_data['fCnt'] ) ? $count_data['fCnt'] : '';
			if ( '' !== $shares ) {
				return $shares;
			}
		} elseif ( 'pinterest' === $count_name ) {
			$shares = isset( $count_data['count'] ) ? $count_data['count'] : '';
			if ( '' !== $shares ) {
				return $shares;
			}
		} elseif ( 'reddit' === $count_name ) {

			if ( '' !== ( $count_data ) ) {
				$children = isset( $count_data['data'] ) ? $count_data['data'] : '';
				$ups      = 0;
				$downs    = 0;
				if ( ! empty( $children ) || null !== $children ) {

					foreach ( $children as $child ) {
						if ( is_array( $child ) ) {
							foreach ( $child as $value ) {
								$ups   += (int) $value['data']['ups'];
								$downs += (int) $value['data']['downs'];
							}
						}
					}
				}
				$score = $ups - $downs;

				if ( 0 > $score ) {
					$score = 0;
				}
			}
			if ( '' !== $score ) {

				return $score;
			}
		} elseif ( 'stumbleupon' === $count_name ) {
			$shares = isset( $count_data['views'] ) ? $count_data['views'] : '';

			if ( '' !== $shares ) {
				return $shares;
			}
		} else {
			$shares = isset( $count_data['shares'] ) ? $count_data['shares'] : '';

		}

		if ( '' !== $shares ) {
			foreach ( $shares as $key => $value ) {
				if ( $count_name == $key ) {
					$count = $value;
				}
			}
		}

		return $count;
	}
}

