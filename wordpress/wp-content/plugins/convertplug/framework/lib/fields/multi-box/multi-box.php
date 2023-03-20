<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

// Add new input type "switch".
if ( function_exists( 'smile_add_input_type' ) ) {
	smile_add_input_type( 'multi_box', 'multi_box_settings_field' );
}

add_action( 'admin_enqueue_scripts', 'smile_multi_box_scripts' );
/**
 * Function Name:smile_multi_box_scripts description.
 *
 * @param  array $hook ap page list.
 */
function smile_multi_box_scripts( $hook ) {
	if ( isset( $_REQUEST['cp_admin_page_nonce'] ) && wp_verify_nonce( $_REQUEST['cp_admin_page_nonce'], 'cp_admin_page' ) ) {
		$cp_page = strpos( $hook, CP_PLUS_SLUG );
		$data    = get_option( 'convert_plug_debug' );
		wp_enqueue_script( 'jquery-ui-sortable' );

		if ( false !== $cp_page || isset( $_GET['view'] ) ) {
			if ( ( isset( $data['cp-dev-mode'] ) && '1' === $data['cp-dev-mode'] ) && isset( $_GET['style-view'] ) && ( 'edit' === $_GET['style-view'] || 'variant' === $_GET['style-view'] ) ) {
				wp_enqueue_style( 'convert-plus-multi-box', SMILE_FRAMEWORK_URI . '/lib/fields/multi-box/multi-box.css', array(), CP_VERSION );
				wp_enqueue_script( 'convert-plus-multi-box', SMILE_FRAMEWORK_URI . '/lib/fields/multi-box/multi-box.js', array( 'jquery', 'convert-plus-swal-js' ), '1.0.0', true );
			}
		}
	}
}

/**
 * Function name: available_form_input_types.
 *
 * @return arrayarray val.
 */
function available_form_input_types() {
	$array = array(
		'textfield',
		'email',
		'textarea',
		'number',
		'dropdown',
		'hidden',
		'checkbox',
		'googlerecaptcha',
	);
	return $array;
}

/**
 * Function Name:render_multi_box.
 *
 * @param  string $uniq  string parameter.
 * @param  string $value string parameter.
 * @return string        string parameter.
 */
function render_multi_box( $uniq, $value ) {
	$output = '';

	$input_types = array();
	$input_types = available_form_input_types();

	$uniq = uniqid( $uniq );

	// remove backslashes.
	$value = preg_replace( '/\\\\/', '', $value );

	$value_mix_array = explode( '|', $value );
	$_value_array    = array();
	if ( ! empty( $value_mix_array ) ) {
		foreach ( $value_mix_array as $key => $value_mix_string ) {
			$_array_temp = explode( '->', $value_mix_string );
			if ( ! empty( $_array_temp ) ) {
				$_name  = ( isset( $_array_temp[0] ) ) ? $_array_temp[0] : '';
				$_value = ( isset( $_array_temp[1] ) ) ? $_array_temp[1] : '';
				if ( '' !== $_name ) {
					$_value_array[ $_name ] = $_value;
				}
			}
		}
	}

	$current_input_name_val  = ( isset( $_value_array['input_name'] ) ) ? $_value_array['input_name'] : 'CP_FIELD_' . wp_rand( 00, 99 );
	$current_input_label_val = ( isset( $_value_array['input_label'] ) ) ? htmlspecialchars( $_value_array['input_label'], ENT_QUOTES, 'utf-8' ) : '';
	$accordion_label         = ( '' !== $current_input_label_val ) ? $current_input_label_val : $current_input_name_val;

	$is_hidden          = false;
	$is_dropdown        = false;
	$need_placeholder   = false;
	$is_textarea        = false;
	$is_googlerecaptcha = false;

	$output           .= '<div class="multi-box">';
	$output           .= '<div class="toggle-accordion-head">
	<span class="mb-mini-box accordion-head-label">' . $accordion_label . '</span>
	<span class="mb-mini-box multi-box-delete"><i class="dashicons dashicons-no-alt"></i></span>
	<!--<span class="mb-mini-box multi-box-handle"><i class="dashicons dashicons-sort"></i></span>
	<span class="mb-mini-box"><i class="dashicons dashicons-arrow-down"></i></span>-->
	</div>';
	$output           .= '<div class="toggle-accordion-content">';
	$output           .= '<div class="multi-box-field">';
	$output           .= '<span class="cp-tooltip-icon has-tip" data-position="right" title="The Field Type attribute specifies the type of &lt; input &gt; element to display." style="cursor: help;
	float: right;"><i class="dashicons dashicons-editor-help"></i></span>';
	$output           .= '<label>' . __( 'Field Type', 'smile' ) . '</label>';
	$output           .= '<select class="cp_mb_select" name="input_type" id="mb-input_type-' . $uniq . '">';
	$current_input_val = ( isset( $_value_array['input_type'] ) ) ? $_value_array['input_type'] : '';
	if ( ! empty( $input_types ) ) :
		foreach ( $input_types as $key => $type ) :
			$selected = ( $current_input_val === $type ) ? 'selected="selected"' : '';
			$output  .= '<option value="' . $type . '" ' . $selected . '>' . ucfirst( $type ) . '</option>';
			if ( $current_input_val === $type && 'hidden' === $type ) {
				$is_hidden = true;
			} elseif ( $current_input_val === $type && 'dropdown' === $type ) {
				$is_dropdown = true;
			} elseif ( $current_input_val === $type && 'textarea' === $type ) {
				$is_textarea = true;
			} elseif ( $current_input_val === $type && ( 'textfield' === $type || 'email' === $type || 'number' === $type ) ) {
				$need_placeholder = true;
			} elseif ( $current_input_val === $type && ( 'checkbox' === $type ) ) {
				$is_checkbox = true;
			} elseif ( $current_input_val === $type && ( 'googlerecaptcha ' === $type ) ) {
				$is_googlerecaptcha = true;
			}
		endforeach;
	endif;
	$output .= '</select>';
	$output .= '</div>';

	$output .= '<div class="multi-box-field">';
	$output .= '<span class="cp-tooltip-icon has-tip" data-position="right" title="The Field Label defines a label for an &lt; input &gt; element." style="cursor: help;
	float: right;"><i class="dashicons dashicons-editor-help"></i></span>';
	$output .= '<label>' . __( 'Field Label', 'smile' ) . '</label>';
	$output .= '<input type="text" class="cp_mb_input" id="mb-input_label-' . $uniq . '" name="input_label" value="' . $current_input_label_val . '"/>';
	$output .= '</div>';

	$output .= '<div class="multi-box-field">';
	$output .= '<span class="cp-tooltip-icon has-tip" data-position="right" title="The Field Name attribute specifies the name of &lt; input &gt; element. This attribute is used to reference form data after a form is submitted. <br/><br/>Please enter single word, no spaces, no special characters, no start with number. Underscores(_) allowed." style="cursor: help;
	float: right;"><i class="dashicons dashicons-editor-help"></i></span>';
	$output .= '<label>' . __( 'Field Name (Required)', 'smile' ) . '</label>';
	$output .= '<input type="text" class="cp_mb_input" id="mb-input_name-' . $uniq . '" name="input_name" value="' . $current_input_name_val . '"/>';
	$output .= '</div>';

	$current_input_placeholder = ( isset( $_value_array['input_placeholder'] ) ) ? $_value_array['input_placeholder'] : '';
	$placeholder_style         = ( $need_placeholder ) ? 'style="display:block"' : 'style="display:none"';

	$output .= '<div class="multi-box-field" ' . $placeholder_style . '>';
	$output .= '<span class="cp-tooltip-icon has-tip" data-position="right" title="The placeholder attribute specifies a short hint that describes the expected value of an input field (e.g. a sample value or a short description of the expected format)." style="cursor: help;
	float: right;"><i class="dashicons dashicons-editor-help"></i></span>';
	$output .= '<label>' . __( 'Placeholder', 'smile' ) . '</label>';
	$output .= '<input type="text" class="cp_mb_input" id="mb-input_placeholder-' . $uniq . '" name="input_placeholder" value="' . $current_input_placeholder . '"/>';
	$output .= '</div>';

	$dropdown_style_for_options = ( $is_dropdown ) ? 'style="display:block"' : 'style="display:none"';
	$current_dropdown_options   = ( isset( $_value_array['dropdown_options'] ) ) ? $_value_array['dropdown_options'] : __( 'Enter Your Options Here', 'smile' );

	$output .= '<div class="multi-box-field" ' . $dropdown_style_for_options . '>';
	$output .= '<span class="cp-tooltip-icon has-tip" data-position="right" title="Enter the options for your dropdown list. Enter each option on new line." style="cursor: help;
	float: right;"><i class="dashicons dashicons-editor-help"></i></span>';
	$output .= '<label>' . __( 'Dropdown Choice Options', 'smile' ) . '</label>';
	$output .= '<textarea class="cp_mb_input" id="mb-dropdown_options-' . $uniq . '" name="dropdown_options">' . $current_dropdown_options . '</textarea>';
	$output .= '</div>';

	$hidden_style_for_require = ( $is_hidden ) ? 'style="display:none"' : '';

	$hidden_style_for_row = ( $is_textarea ) ? 'style="display:block"' : 'style="display:none"';
	$current_row_value    = ( isset( $_value_array['row_value'] ) ) ? $_value_array['row_value'] : '';

	$output .= '<div class="multi-box-field" ' . $hidden_style_for_row . '>';
	$output .= '<span class="cp-tooltip-icon has-tip" data-position="right" title="Textarea height specifies the visible height of a text area, in lines." style="cursor: help;
	float: right;"><i class="dashicons dashicons-editor-help"></i></span>';
	$output .= '<label>' . __( 'Textarea Height', 'smile' ) . '</label>';
	$output .= '<input type="number" class="cp_mb_input" id="mb-row_value-' . $uniq . '" name="row_value" value="' . $current_row_value . '" min="0" />';
	$output .= '</div>';

	$googlerecaptcha_style_for_row = ( $is_googlerecaptcha ) ? 'style="display:block"' : 'style="display:none"';

	$current_row_value1 = ( isset( $_value_array['row_value'] ) ) ? $_value_array['row_value'] : '';

	$output .= '<div class="multi-box-field" ' . $googlerecaptcha_style_for_row . '>';
	$output .= '<span class="cp-tooltip-icon has-tip" data-position="right" title="Textarea height specifies the visible height of a text area, in lines." style="cursor: help;
	float: right;"><i class="dashicons dashicons-editor-help"></i></span>';
	$output .= '</div>';

	$output           .= '<div class="multi-box-field" ' . $hidden_style_for_require . '>';
	$current_input_val = ( isset( $_value_array['input_require'] ) ) ? $_value_array['input_require'] : 'true';
	$checked           = ( 'true' === $current_input_val || true === $current_input_val ) ? 'checked="checked"' : '';
	$output           .= '<span class="cp-tooltip-icon has-tip" data-position="right" title="When Required Field is checked, it specifies that an input field must be filled out before submitting the form." style="cursor: help;
	float: right;"><i class="dashicons dashicons-editor-help"></i></span>';
	$output           .= '<input type="checkbox" class="cp_mb_checkbox" id="mb-input_require-' . $uniq . '" name="input_require" value="" ' . $checked . '/> <label for="mb-input_require-' . $uniq . '">' . __( 'Required Field', 'smile' ) . '</label>';
	$output           .= '</div>';

	$hidden_style_for_hidden = ( $is_hidden ) ? 'style="display:block"' : 'style="display:none"';
	$current_hidden_val      = ( isset( $_value_array['hidden_value'] ) ) ? $_value_array['hidden_value'] : '';

	$output .= '<div class="multi-box-field" ' . $hidden_style_for_hidden . '>';
	$output .= '<span class="cp-tooltip-icon has-tip" data-position="right" title="The Field Value attribute specifies the value for your hidden element." style="cursor: help;
	float: right;"><i class="dashicons dashicons-editor-help"></i></span>';
	$output .= '<label>' . __( 'Field Value', 'smile' ) . '</label>';
	$output .= '<input type="text" class="cp_mb_input" id="mb-hidden_value-' . $uniq . '" name="hidden_value" value="' . $current_hidden_val . '" required/>';
	$output .= '</div>';

	$output .= '</div> <!-- toggle-accordion-content -->';
	$output .= '</div> <!-- multi-box -->';
	return $output;
}

/**
 * Function Name:multi_box_settings_field Function to handle new input type "multi_box".
 *
 * @param  string $name     settings provided when using the input type "multi_box".
 * @param  string $settings holds the default / updated value.
 * @param  string $value    html output generated by the function.
 * @return string           html output generated by the function.
 */
function multi_box_settings_field( $name, $settings, $value ) {
	$input_name      = $name;
	$type            = isset( $settings['type'] ) ? $settings['type'] : '';
	$class           = isset( $settings['class'] ) ? $settings['class'] : '';
	$multi_box_nonce = wp_create_nonce( 'cp_multi_box_nonce' );

	$uniq = uniqid();

	$output  = '<div class="multi-box-wrapper" id="mb-wrapper-' . $uniq . '" data-id="' . $uniq . '">';
	$output .= '<textarea id="multi-box-input-' . $uniq . '" class="content cp-hidden form-control smile-input smile-' . $type . ' ' . $input_name . ' ' . $type . ' ' . $class . '" name="' . $input_name . '" rows="6" cols="6" style="display: block !important;">' . $value . '</textarea>';
	$output .= '<div class="multi-box-inner">';

	$boxes = explode( ';', $value );
	if ( ! empty( $boxes ) ) {
		foreach ( $boxes as $key => $box_value ) {
			$output .= render_multi_box( $uniq, $box_value );
		}
	}

	$output .= '</div> <!-- multi-box-inner -->';
	$output .= '<input type="hidden" id="cp_multi_box_nonce" value="' . esc_attr( $multi_box_nonce ) . '">';
	$output .= '<div class="multi-box-add-new">Add New Field<i class="dashicons dashicons-plus"></i></div>';
	$output .= '</div>';
	return $output;
}

add_action( 'wp_ajax_repeat_multi_box', 'repeat_multi_box_callback' );
/**
 * Function Name:repeat_multi_box_callback.
 */
function repeat_multi_box_callback() {
	if ( ! current_user_can( 'access_cp' ) ) {
			die( -1 );
	}

	check_ajax_referer( 'cp_multi_box_nonce', 'security_nonce' );
	$uniq = $_POST['id'];

	if ( '' === $uniq ) {
		$response['type']    = 'error';
		$response['message'] = 'No wrapper ID found';
		wp_send_json( wp_json_encode( $response ) );
	}

	$value  = '';
	$output = render_multi_box( $uniq, $value );

	$response['type']    = 'success';
	$response['message'] = $output;
	wp_send_json( wp_json_encode( $response ) );
}

/**
 * Function Name:repeat_multi_box_callback function to convert dropdown string to array.
 *
 * @param  string $string  settings provided when using the input type "multi_box".
 */
function mb_dropdown_string_to_array( $string ) {
	$lines = explode( PHP_EOL, $string );
	$array = array();
	foreach ( $lines as $key => $line ) {
		$line = trim( $line );
		if ( '' === $line ) {
			continue;
		}
		$temp          = array();
		$line_to_array = explode( '+', $line );
		$label         = ( isset( $line_to_array[0] ) ) ? ucfirst( $line_to_array[0] ) : ucfirst( $line );
		$value         = ( isset( $line_to_array[1] ) ) ? $line_to_array[1] : $line;
		$temp['label'] = trim( $label );
		$temp['value'] = trim( $value );
		array_push( $array, $temp );
	}
	return $array;
}
