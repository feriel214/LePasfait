<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

if ( ! class_exists( 'Convert_Plug_Smile_Framework_Mapper' ) ) {
	/**
	 * Class Convert_Plug_Smile_Framework_Mapper to handle input types registration, activation and callbacks.
	 *
	 * @since 1.0
	 */
	class Convert_Plug_Smile_Framework_Mapper {
		/**
		 * $params store custom input types.
		 *
		 * @var array
		 */
		protected static $params = array();

		/**
		 * Function Name: add_input_type function to add new input field into $params array.
		 *
		 * @param string $type                 input type name.
		 * @param string $input_field_callback allback function for the input field.
		 * @return boolval(var).
		 */
		public static function add_input_type( $type, $input_field_callback ) {

			$result = false;
			if ( ! empty( $type ) && ! empty( $input_field_callback ) ) {
				self::$params[ $type ] = array(
					'callback' => $input_field_callback,
				);
				$result                = true;
			}
			return $result;
		}

		/**
		 * Function Name: render_input_type Calls hook for attribute type.
		 *
		 * @param  string $name               input type name.
		 * @param  string $type                input type settings from shortcode.
		 * @param  string $input_type_settings input type value.
		 * @param  string $input_value         input type value.
		 * @param  string $default_value      input type value.
		 * @return mixed|string         - returns html which will be render in hook.
		 */
		public static function render_input_type( $name, $type, $input_type_settings, $input_value, $default_value = null ) {
			if ( isset( self::$params[ $type ]['callback'] ) ) {
				return call_user_func( self::$params[ $type ]['callback'], $name, $input_type_settings, $input_value, $default_value );
			}
			return '';
		}
	}//end class
} // end class check.

/**
 * Function Name: smile_framework_add_options Helper function to register options and their respective settings.
 *
 * @param  string $class    option name to be stored and retrived.
 * @param  string $name     option name to be stored and retrived.
 * @param  string $settings extra settings for option.
 */
function smile_framework_add_options( $class, $name, $settings ) {
	Smile_Framework::smile_store_data( $class, $name, $settings );
}

/**
 * Function Name: smile_framework_add_options Helper function to register new input type hook.
 *
 * @param  string $type    input type name.
 * @param  string $input_field_callback     hook, will be called when framework interface is loaded.
 * @return boolval(var)           true/false.
 */
function smile_add_input_type( $type, $input_field_callback ) {
	return Convert_Plug_Smile_Framework_Mapper::add_input_type( $type, $input_field_callback );
}

/**
 * Function Name: smile_framework_add_options Call hook for input type html.
 *
 * @param  string $name                input type name.
 * @param  string $type                input type name.
 * @param  string $input_type_settings input type settings from mapper.
 * @param  string $input_value         input type value.
 * @param  string $default_value       input type value.
 * @return string                      returns html which will be render in hook.
 */
function do_input_type_settings_field( $name, $type, $input_type_settings, $input_value, $default_value = null ) {
	return Convert_Plug_Smile_Framework_Mapper::render_input_type( $name, $type, $input_type_settings, $input_value, $default_value );
}

/**
 * [smile_update_options Call hook for update existing styles options.
 *
 * @param  [type] $class    module class name.
 * @param  [type] $name    style name to update options.
 * @param  [type] $options options array to be updated into the style.
 */
function smile_update_options( $class, $name, $options ) {
	Smile_Framework::smile_update_data( $class, $name, $options );
}

/**
 * Function Name: smile_update_default Call hook for update default value for a setting.
 *
 * @param  string $class  module class name.
 * @param  string $style style name, where the option is located.
 * @param  string $name  setting name to update default option.
 * @param  string $value  new default value to be set for the     $name setting.
 */
function smile_update_default( $class, $style, $name, $value ) {
	Smile_Framework::smile_update_value( $class, $style, $name, $value );
}

/**
 * Function Name: smile_remove_option Call hook for removing option from settings.
 *
 * @param  string $class  module class name.
 * @param  string $style style name, where the option is located.
 * @param  string $name  setting name to update default option.
 */
function smile_remove_option( $class, $style, $name ) {
	Smile_Framework::smile_remove_setting( $class, $style, $name );
}

/**
 * Function Name: smile_update_partial Call hook for update partial value for a setting.
 *
 * @param  string $class  module class name.
 * @param  string $style style name, where the option is located.
 * @param  string $name  setting name to update default option.
 * @param  array  $parse_array     new parse array to be set for the .
 */
function smile_update_partial( $class, $style, $name, $parse_array ) {
	Smile_Framework::smile_update_partial_refresh( $class, $style, $name, $parse_array );
}

/**
 * Function Name: cp_register_addon Call hook for adding mailer addon.
 *
 * @param  string $slug    mailer slug.
 * @param  string $setting mailer other settings.
 */
function cp_register_addon( $slug, $setting ) {
	Smile_Framework::smile_add_mailer( $slug, $setting );
}
