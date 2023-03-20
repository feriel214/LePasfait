<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Register Functions & Components.
 */

require_once CP_BASE_DIR . '/framework/functions/functions.admin.php';
require_once CP_BASE_DIR . '/framework/functions/component-multi-form.php';
require_once CP_BASE_DIR . '/framework/functions/component-social-media.php';
require_once CP_BASE_DIR . '/framework/functions/component-count-down.php';

/**
 *  Component - Multi Form - Generate DropDown HTML
 */

/**
 * Function Name: mb_dropdown_string_to_html.
 *
 * @param  string $dropdown_options option string.
 * @return string                  option string.
 */
function mb_dropdown_string_to_html( $dropdown_options ) {

	$lines       = explode( PHP_EOL, $dropdown_options );
	$all_options = '';
	foreach ( $lines as $key => $line ) {
		$line = trim( $line );
		if ( '' === $line ) {
			continue;
		}
		$line_to_array = explode( '+', $line );
		$label         = ( isset( $line_to_array[0] ) ) ? ucfirst( $line_to_array[0] ) : ucfirst( $line );
		$value         = ( isset( $line_to_array[1] ) ) ? $line_to_array[1] : $line;
		$all_options  .= '<option value="' . trim( $value ) . '">' . trim( $label ) . '</option>';
	}
	return $all_options;
}

/**
 * Function Name: generate_global_shortcode_vars Generate Global shortcode variables.
 *
 * @param  array $ar array parameters.
 * @return array     array parameters.
 */
function generate_global_shortcode_vars( $ar ) {

	$v = array();

	foreach ( $ar as $key => $value ) {
		if ( isset( $value['name'] ) && ! empty( $value['name'] ) ) {
			$v[ $value['name'] ] = '';
		}
	}

	return $v;
}

/**
 * Helper Functions - Smile Framework.
 */
add_action( 'wp_ajax_framework_update_options', 'framework_update_options' );
add_action( 'wp_ajax_framework_update_preview_data', 'framework_update_preview_data' );

// function to return style settings array.
if ( ! function_exists( 'smile_get_style_settings' ) ) {
	/**
	 * Function Name: smile_get_style_settings.
	 *
	 * @param  array  $option get styles option.
	 * @param  string $style  module type.
	 * @return array         array values.
	 */
	function smile_get_style_settings( $option, $style ) {
		$prev_styles = get_option( $option );
		$styles      = array();
		foreach ( $prev_styles as $key => $settings ) {
			if ( $settings['style_id'] === $style ) {
				$styles = maybe_serialize( $prev_styles[ $key ]['style_settings'] );
			}
		}

		$style_settings = array();
		foreach ( $styles as $key => $setting ) {
			$style_settings[ $key ] = apply_filters( 'smile_render_setting', $setting );
		}
		return $style_settings;
	}
}



