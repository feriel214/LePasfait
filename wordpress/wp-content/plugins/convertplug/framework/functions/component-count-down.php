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
 *  Module  - Count Down
 *
 * 1.   count_down Array Setup
 * 2.   Global array for shortcode variables
 * 3.   Generate Output by 'cp_get_count_down' filter
 * 4.   Generate & Append CSS
 *
 *  Use same names for variables & array
 *  For '$your_options_name' use '$your_options_name_VARS'
 *
 *  E.g.    $cp_count_down
 *          $cp_count_down_vars
 *
 * @since  1.1.1
 */
global $cp_count_down;
global $cp_count_down_vars;

/**
 * 1.   count_down Array Setup.
 */

$label_arr = array(
	__( 'Year', 'smile' ),
	__( 'Month', 'smile' ),
	__( 'Weeks', 'smile' ),
	__( 'Days', 'smile' ),
	__( 'Hours', 'smile' ),
	__( 'Minutes', 'smile' ),
	__( 'Seconds', 'smile' ),
);

$label_arr = ( implode( ',', $label_arr ) );

$compact_labels = array(
	__( 'Y', 'smile' ),
	__( 'M', 'smile' ),
	__( 'W', 'smile' ),
	__( 'D', 'smile' ),
	__( 'H', 'smile' ),
	__( 'Mn', 'smile' ),
	__( 'S', 'smile' ),
);

$compact_labels = ( implode( ',', $compact_labels ) );

$cp_count_down = array(
	array(
		'type'         => 'switch',
		'class'        => '',
		'name'         => 'disable_datepicker',
		'opts'         => array(
			'title' => __( 'Enable Countdown Timer', 'smile' ),
			'value' => true,
			'on'    => __( 'YES', 'smile' ),
			'off'   => __( 'NO', 'smile' ),
		),
		'section'      => 'Design',
		'panel'        => 'Countdown Timer',
		'section_icon' => 'connects-icon-image',
	),
	array(
		'type'         => 'datetimepicker',
		'class'        => '',
		'name'         => 'date_time_picker',
		'opts'         => array(
			'title' => __( 'Countdown Timer', 'smile' ),
			'value' => '',
		),
		'dependency'   => array(
			'name'     => 'disable_datepicker',
			'operator' => '==',
			'value'    => 'true',
		),
		'section'      => 'Design',
		'panel'        => 'Countdown Timer',
		'section_icon' => 'connects-icon-image',
	),
	array(
		'type'         => 'dropdown',
		'class'        => '',
		'name'         => 'datepicker_advance_option',
		'opts'         => array(
			'title'   => __( 'Countdown Timer Style', 'smile' ),
			'value'   => 'style_1',
			'options' => array(
				__( 'Style 1', 'smile' ) => 'style_1',
				__( 'Style 2', 'smile' ) => 'style_2',
			),
		),
		'dependency'   => array(
			'name'     => 'disable_datepicker',
			'operator' => '==',
			'value'    => 'true',
		),
		'section'      => 'Design',
		'panel'        => 'Countdown Timer',
		'section_icon' => 'connects-icon-image',
	),
	array(
		'type'         => 'google_fonts',
		'name'         => 'counter_font',
		'opts'         => array(
			'title'  => __( 'Counter Font', 'smile' ),
			'value'  => 'Raleway',
			'use_in' => 'panel',
		),
		'dependency'   => array(
			'name'     => 'disable_datepicker',
			'operator' => '==',
			'value'    => 'true',
		),
		'section'      => 'Design',
		'panel'        => 'Countdown Timer',
		'section_icon' => 'connects-icon-image',
	),
	array(
		'type'         => 'colorpicker',
		'class'        => '',
		'name'         => 'counter_bg_color',
		'opts'         => array(
			'title' => __( 'Countdown Background Color', 'smile' ),
			'value' => '#1bce7c',
		),
		'dependency'   => array(
			'name'     => 'datepicker_advance_option',
			'operator' => '==',
			'value'    => 'style_2',
		),
		'section'      => 'Design',
		'panel'        => 'Countdown Timer',
		'section_icon' => 'connects-icon-image',
	),
	array(
		'type'         => 'colorpicker',
		'class'        => '',
		'name'         => 'counter_digit_text_color',
		'opts'         => array(
			'title'        => __( 'Digit Color', 'smile' ),
			'value'        => 'rgb(255, 255, 255)',
			'css_selector' => '#cp_defaultCountdown , #cp_defaultCountdown .cp_countdown-amount',
			'css_property' => 'color',
			'css_preview'  => true,
		),
		'dependency'   => array(
			'name'     => 'disable_datepicker',
			'operator' => '==',
			'value'    => 'true',
		),
		'section'      => 'Design',
		'panel'        => 'Countdown Timer',
		'section_icon' => 'connects-icon-image',
	),

	array(
		'type'         => 'colorpicker',
		'class'        => '',
		'name'         => 'counter_digit_border_color',
		'opts'         => array(
			'title'        => __( 'Digit Border Color', 'smile' ),
			'value'        => '#1bce7c',
			'css_selector' => '#cp_defaultCountdown .cp_countdown-amount',
			'css_property' => 'border-color',
			'css_preview'  => true,
		),
		'dependency'   => array(
			'name'     => 'datepicker_advance_option',
			'operator' => '!==',
			'value'    => 'style_1',
		),
		'section'      => 'Design',
		'panel'        => 'Countdown Timer',
		'section_icon' => 'connects-icon-image',
	),
	array(
		'type'         => 'slider',
		'class'        => '',
		'name'         => 'counter_digit_text_size',
		'opts'         => array(
			'title'        => __( 'Digit Font Size', 'smile' ),
			'value'        => 15,
			'min'          => 10,
			'max'          => 100,
			'step'         => 1,
			'suffix'       => 'px',
			'css_selector' => '.cp-count-down #cp_defaultCountdown , .cp-count-down #cp_defaultCountdown .cp_countdown-amount',
			'css_property' => 'font-size',
			'css_preview'  => true,
		),
		'dependency'   => array(
			'name'     => 'disable_datepicker',
			'operator' => '==',
			'value'    => 'true',
		),
		'section'      => 'Design',
		'panel'        => 'Countdown Timer',
		'section_icon' => 'connects-icon-image',
	),
	array(
		'type'         => 'slider',
		'class'        => '',
		'name'         => 'counter_border_radius',
		'opts'         => array(
			'title'        => __( 'Counter Border Radius', 'smile' ),
			'value'        => 5,
			'min'          => 0,
			'max'          => 100,
			'step'         => 1,
			'suffix'       => 'px',
			'css_selector' => '.cp-count-down #cp_defaultCountdown , .cp-count-down #cp_defaultCountdown .cp_countdown-amount',
			'css_property' => 'border-radius',
			'css_preview'  => true,
		),
		'dependency'   => array(
			'name'     => 'datepicker_advance_option',
			'operator' => '!==',
			'value'    => 'style_1',
		),
		'section'      => 'Design',
		'panel'        => 'Countdown Timer',
		'section_icon' => 'connects-icon-image',
	),
	array(
		'type'         => 'colorpicker',
		'class'        => '',
		'name'         => 'counter_timer_text_color',
		'opts'         => array(
			'title'        => __( 'Time Unit Color', 'smile' ),
			'value'        => '#fff',
			'css_selector' => '#cp_defaultCountdown .cp_countdown-period',
			'css_property' => 'color',
			'css_preview'  => true,
		),
		'dependency'   => array(
			'name'     => 'datepicker_advance_option',
			'operator' => '!==',
			'value'    => 'style_1',
		),
		'section'      => 'Design',
		'panel'        => 'Countdown Timer',
		'section_icon' => 'connects-icon-image',
	),
	array(
		'type'         => 'slider',
		'class'        => '',
		'name'         => 'counter_timer_text_size',
		'opts'         => array(
			'title'        => __( 'Time Unit Font Size', 'smile' ),
			'value'        => 15,
			'min'          => 10,
			'max'          => 40,
			'step'         => 1,
			'suffix'       => 'px',
			'css_selector' => '#cp_defaultCountdown .cp_countdown-period',
			'css_property' => 'font-size',
			'css_preview'  => true,
		),
		'dependency'   => array(
			'name'     => 'datepicker_advance_option',
			'operator' => '!==',
			'value'    => 'style_1',
		),
		'section'      => 'Design',
		'panel'        => 'Countdown Timer',
		'section_icon' => 'connects-icon-image',
	),
	array(
		'type'         => 'checkbox',
		'class'        => '',
		'name'         => 'counter_option',
		'opts'         => array(
			'title'   => __( 'Select Time Units To Display In Countdown Timer', 'smile' ),
			'value'   => 'D|H|M|S',
			'options' => array(
				__( 'Years', 'smile' )   => 'Y',
				__( 'Months', 'smile' )  => 'O',
				__( 'Weeks', 'smile' )   => 'W',
				__( 'Days', 'smile' )    => 'D',
				__( 'Hours', 'smile' )   => 'H',
				__( 'Minutes', 'smile' ) => 'M',
				__( 'Seconds', 'smile' ) => 'S',
			),
		),
		'dependency'   => array(
			'name'     => 'disable_datepicker',
			'operator' => '==',
			'value'    => 'true',
		),
		'section'      => 'Design',
		'panel'        => 'Countdown Timer',
		'section_icon' => 'connects-icon-image',
	),
	array(
		'type'         => 'tags',
		'class'        => '',
		'name'         => 'countdown_label',
		'opts'         => array(
			'title' => __( 'Countdown Label', 'smile' ),
			'value' => $label_arr,
		),
		'dependency'   => array(
			'name'     => 'hidden',
			'operator' => '==',
			'value'    => 'hide',
		),
		'section'      => 'Design',
		'panel'        => 'Countdown Timer',
		'section_icon' => 'connects-icon-image',
	),

	array(
		'type'         => 'tags',
		'class'        => '',
		'name'         => 'countdown_compact_label',
		'opts'         => array(
			'title' => __( 'Countdown CompactLabel', 'smile' ),
			'value' => $compact_labels,
		),
		'dependency'   => array(
			'name'     => 'hidden',
			'operator' => '==',
			'value'    => 'hide',
		),
		'section'      => 'Design',
		'panel'        => 'Countdown Timer',
		'section_icon' => 'connects-icon-image',
	),

);

/**
 * Global array for shortcode variables.
 */
$cp_count_down_vars = generate_global_shortcode_vars( $cp_count_down );

add_filter( 'cp_get_count_down', 'cp_get_count_down_init' );

if ( ! function_exists( 'cp_get_count_down_init' ) ) {
	/**
	 * Function Name: cp_get_count_down_init  Generate Output by 'cp_get_count_down' filter..
	 *
	 * @param  array $a settings array.
	 */
	function cp_get_count_down_init( $a ) {

		// apply count down styles.
		apply_filters_ref_array( 'cp_count_down_css', array( $a ) );

		$show_datepicker  = '';
		$countdown_option = '';
		$advance_dtpicker = '';
		$show_datepicker  = 'show';
		if ( 'style_2' === $a['datepicker_advance_option'] ) {
			$advance_dtpicker = $a['datepicker_advance_option'];
		} else {
			$advance_dtpicker = 0;
		}
		$label_name     = array( 'Year', 'Month', 'Weeks', 'Days', 'Hours', 'Minutes', 'Seconds' );
		$cm_label_name  = array( 'Y', 'M', 'W', 'D', 'H', 'Mn', 'S' );
		$label_arr      = isset( $a['countdown_label'] ) ? explode( ',', $a['countdown_label'] ) : $label_name;
		$compact_labels = isset( $a['countdown_compact_label'] ) ? explode( ',', $a['countdown_compact_label'] ) : $cm_label_name;

		// filters to change labelname and compactlabel for counter.
		$style_id       = isset( $a['style_id'] ) ? $a['style_id'] : '';
		$label_arr      = apply_filters( 'cp_change_countdown_label', $label_arr, $style_id );
		$compact_labels = apply_filters( 'cp_change_countdown_cmp_label', $compact_labels, $style_id );

		$countdown_option .= ' data-advnce-countdown =' . $advance_dtpicker;
		$countdown_option .= ' data-showcounter =' . $show_datepicker;
		$countdown_option .= ' data-counter-labels =' . wp_json_encode( $label_arr );
		$countdown_option .= ' data-counter-compact-labels =' . wp_json_encode( $compact_labels );

		// build HTML structure for count down.
		if ( $a['disable_datepicker'] ) {
			echo '<span id="cp_defaultCountdown" class="cp_count_down_main" data-timeformat = "' . esc_attr( $a['counter_option'] ) . '" data-date="' . esc_attr( $a['date_time_picker'] ) . '" ' . esc_attr( $countdown_option ) . '></span>';
		}

	}
}


add_filter( 'cp_count_down_css', 'cp_count_down_css_init' );
/**
 * Function Name: cp_count_down_css_init Generate & Append CSS for counter.
 *
 * @param  array $a array parameter.
 */
function cp_count_down_css_init( $a ) {

	if ( isset( $a['disable_datepicker'] ) && '1' === $a['disable_datepicker'] ) {
		$counter_digit = '';
		$timer_digit   = '';

		$counter_digit .= 'color: ' . $a['counter_digit_text_color'] . ';';
		if ( '' === $a['counter_font'] ) {
			$a['counter_font'] = 'inherit';
		}
		$uid            = $a['uid'];
		$counter_digit .= 'font-family: ' . $a['counter_font'] . ';';
		$counter_digit .= 'font-size: ' . $a['counter_digit_text_size'] . 'px;';
		$counter_digit .= 'border-color: ' . $a['counter_digit_border_color'] . ';';

		// timer text css.
		$timer_digit .= 'color: ' . $a['counter_timer_text_color'] . ';';
		$timer_digit .= 'font-size: ' . $a['counter_timer_text_size'] . 'px;';
		$timer_digit .= 'font-family: ' . $a['counter_font'] . ';';

		$border_rad = isset( $a['counter_border_radius'] ) ? $a['counter_border_radius'] : '5';

		if ( 'style_2' === $a['datepicker_advance_option'] ) {
			$counter_digit .= 'background: ' . $a['counter_bg_color'] . ';';
			$counter_digit .= 'border-radius: ' . $border_rad . 'px;';
			echo '<style class="cp-counter">.content-' . esc_attr( $uid ) . ' #cp_defaultCountdown  .cp_countdown-amount {  ' . esc_attr( $counter_digit ) . '; }
			.content-' . esc_attr( $uid ) . ' #cp_defaultCountdown  .cp_countdown-period { ' . esc_attr( $timer_digit ) . '; } 
			.content-' . esc_attr( $uid ) . ' .cp-count-down #cp_defaultCountdown {font-size: ' . esc_attr( $a['counter_digit_text_size'] ) . 'px;}
			</style>';
		} else {
			$counter_digit .= 'background: transparent;';
			echo '<style class="cp-counter">.content-' . esc_attr( $uid ) . ' #cp_defaultCountdown {  ' . esc_attr( $counter_digit ) . '; } </style>';
		}

		// countdown script.
		if ( ! wp_script_is( 'convert-plus-countdown-style', 'enqueued' ) ) {
			wp_register_style( 'convert-plus-countdown-style', CP_PLUGIN_URL . 'modules/assets/css/jquery.countdown.css', array(), CP_VERSION );
			wp_register_script( 'convert-plus-counter-plugin-js', CP_PLUGIN_URL . 'modules/assets/js/jquery.plugin.min.js', array( 'jquery' ), CP_VERSION, true );
			wp_register_script( 'convert-plus-countdown-js', CP_PLUGIN_URL . 'modules/assets/js/jquery.countdown.js', array( 'jquery' ), CP_VERSION, true );
			wp_register_script( 'convert-plus-countdown-script', CP_PLUGIN_URL . 'modules/assets/js/jquery.countdown.script.js', array( 'jquery' ), CP_VERSION, true );

			wp_enqueue_style( 'convert-plus-countdown-style' );
			wp_enqueue_script( 'convert-plus-counter-plugin-js' );
			wp_enqueue_script( 'convert-plus-countdown-js' );
			wp_enqueue_script( 'convert-plus-countdown-script' );
		}
	}
}
