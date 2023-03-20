<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

// Add new input type "radio-image".
if ( function_exists( 'smile_add_input_type' ) ) {
	smile_add_input_type( 'radio-image', 'radio_image_settings_field' );
}

add_action( 'admin_enqueue_scripts', 'smile_radio_image_scripts' );
/**
 * Function Name:smile_radio_image_scripts description.
 *
 * @param  array $hook ap page list.
 */
function smile_radio_image_scripts( $hook ) {
	if ( isset( $_REQUEST['cp_admin_page_nonce'] ) && wp_verify_nonce( $_REQUEST['cp_admin_page_nonce'], 'cp_admin_page' ) ) {
		$cp_page = strpos( $hook, CP_PLUS_SLUG );
		$data    = get_option( 'convert_plug_debug' );

		if ( false !== $cp_page && ( isset( $data['cp-dev-mode'] ) && '1' === $data['cp-dev-mode'] ) && isset( $_GET['style-view'] ) && ( 'edit' === $_GET['style-view'] || 'variant' === $_GET['style-view'] ) ) {
			wp_enqueue_style( 'convert-plus-radio-image', SMILE_FRAMEWORK_URI . '/lib/fields/radio-image/radio-image.css', array(), CP_VERSION );
			wp_enqueue_script( 'convert-plus-radio-image', SMILE_FRAMEWORK_URI . '/lib/fields/radio-image/radio-image.js', array(), '1.0.0', true );
		}
	}
}

/**
 * Function Name:radio_image_settings_field Function to handle new input type "radio_image".
 *
 * @param  string $name     settings provided when using the input type "radio_image".
 * @param  string $settings holds the default / updated value.
 * @param  string $value    html output generated by the function.
 * @return string           html output generated by the function.
 */
function radio_image_settings_field( $name, $settings, $value ) {
	$input_name  = $name;
	$type        = isset( $settings['type'] ) ? $settings['type'] : '';
	$class       = isset( $settings['class'] ) ? $settings['class'] : '';
	$options     = isset( $settings['options'] ) ? $settings['options'] : '';
	$max_width   = isset( $settings['width'] ) ? $settings['width'] : '';
	$image_title = isset( $settings['imagetitle'] ) ? $settings['imagetitle'] : '';

	$output    = '';
	$n         = 0;
	$img_title = '';

	foreach ( $options as $key => $img ) {
		$checked = '';
		$cls     = '';
		if ( '' !== $value && (string) $key === (string) $value ) {
			$checked = ' checked="checked"';
			$cls     = 'selected';
		}
		if ( '' !== $image_title ) {
			$description = $image_title[ "title-$n" ];
			$img_title   = 'title = "' . $description . '"';
		}
		$output .= '<div class="smile-radio-image-holder ' . $cls . '">';
		$output .= '<input type="radio" name="' . $input_name . '" value="' . $key . '" data-id="smile_' . $input_name . '" class="form-control smile-input smile-' . $type . ' ' . $input_name . ' ' . $type . '" ' . $checked . '> <label for="smile_' . $key . '_' . $n . '" class="smile-radio-control"><img style="max-width: ;" . $max_width . ";" class="smile-radio-control ' . $input_name . '-' . $key . '" src="' . $img . '" ' . $img_title . '/></label>';

		$output .= '</div>';
		$n++;
	}
	return '<div class="smile-radio-image-wrapper">' . $output . '</div>';
}
