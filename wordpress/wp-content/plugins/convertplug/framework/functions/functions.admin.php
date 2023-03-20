<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Function Name: framework_update_preview_data Update preview page data before customizing the element.
 *
 * @since 1.0.0
 */
function framework_update_preview_data() {

	check_ajax_referer( 'cp_framework_update_preview_data_nonce', 'security_nonce' );
	if ( ! current_user_can( 'access_cp' ) ) {
		die( -1 );
	}

	$preview_page = get_option( 'smile-preview-page' );
	if ( isset( $_POST['demo_id'] ) ) {
		$demo_id = $_POST['demo_id'];
		$class   = isset( $_POST['cls'] ) ? sanitize_text_field( $_POST['cls'] ) : '';

		$module = isset( $_POST['module'] ) ? sanitize_text_field( $_POST['module'] ) : '';
		require_once CP_BASE_DIR . '/modules/' . $module . '/functions/functions.options.php';

		$demo_html     = '';
		$customizer_js = '';
		$settings      = $class::$options;
		foreach ( $settings as $style => $options ) {
			if ( $style === $demo_id ) {
				$demo_html     = $options['demo_url'];
				$demo_dir      = $options['demo_dir'];
				$customizer_js = $options['customizer_js'];
			}
		}

		$post_content = Convert_Plug_Filesystem::prefix_get_filesystem()->get_contents( $demo_dir );

		wp_send_json( $post_content );
	} else {
		echo 'Not Ok';
	}
	die();
}

/**
 * Function Name: framework_update_options Save options to the database after processing them.
 *
 * @param  array $data Options array to save.
 */
function framework_update_options( $data ) {

	if ( ! current_user_can( 'access_cp' ) ) {
			die( -1 );
	}

	if ( empty( $data ) ) {
		return;
	}
	if ( null !== $key ) { // Update one specific value.
		set_theme_mod( $key, $data );
	} else { // Update all values in $data.
		foreach ( $data as $k => $v ) {
			if ( ! isset( $smof_data[ $k ] ) || $v !== $smof_data[ $k ] ) { // Only write to the DB when we need to.
				set_theme_mod( $k, $v );
			}
		}
	}
	die();
}

if ( ! function_exists( 'smile_framework_create_dependency' ) ) {
	/**
	 * Function Name: smile_framework_create_dependency.
	 *
	 * @param  string $name  option name.
	 * @param  array  $array option values.
	 * @return boolval(var)  true/false.
	 */
	function smile_framework_create_dependency( $name, $array ) {
		if ( is_array( $array ) ) {
			$dependency = '';
			$element    = $array['name'];
			$operator   = $array['operator'];
			$value      = $array['value'];
			$type       = isset( $array['type'] ) ? $array['type'] : '';

			if ( 'media' === $type ) {
				$uid     = $_SESSION[ $element ];
				$element = $element . '_' . $uid;
			}

			$dependency = 'data-name="' . $element . '" data-element="' . $name . '" data-operator="' . $operator . '" data-value="' . $value . '"';

			return $dependency;
		} else {
			return false;
		}
	}
}

if ( ! function_exists( 'smile_framework_get_styles' ) ) {
	/**
	 * Function Name: smile_framework_get_styles.
	 *
	 * @param  string $option  option name.
	 * @return array style settings array.
	 */
	function smile_framework_get_styles( $option ) {
		$prev_styles = get_option( $option );
		$styles      = array();
		if ( is_array( $prev_styles ) && ! empty( $prev_styles ) ) {
			foreach ( $prev_styles as $key => $style ) {
				$style_id            = isset( $style['style_id'] ) ? $style['style_id'] : '';
				$style_name          = isset( $style['style_name'] ) ? $style['style_name'] : '';
				$styles[ $style_id ] = $style_name;
			}
		}
		return $styles;
	}
}

add_filter( 'smile_render_setting', 'smile_render_setting', 1 );
/**
 * Function Name: smile_render_setting.
 *
 * @param  array $setting  option array.
 * @return array style settings array.
 */
function smile_render_setting( $setting ) {
	if ( ! is_array( $setting ) ) {
		return urldecode( $setting );
	} else {
		return $setting;
	}
}

if ( ! function_exists( 'cp_import_upload_prefilter' ) ) {
	if ( isset( $_REQUEST['cp_admin_page_nonce'] ) && wp_verify_nonce( $_REQUEST['cp_admin_page_nonce'], 'cp_admin_page' ) ) {
		$cp_page = isset( $_POST['admin_page'] ) ? sanitize_text_field( $_POST['admin_page'] ) : '';
		if ( isset( $cp_page ) && 'import' === $cp_page ) {
			add_filter( 'wp_handle_upload_prefilter', 'cp_import_upload_prefilter' );
		}
	}
	/**
	 * Function Name: cp_import_upload_prefilter.
	 *
	 * @param  string $file  string val of option.
	 * @return array style settings array.
	 */
	function cp_import_upload_prefilter( $file ) {
		if ( isset( $_REQUEST['cp_admin_page_nonce'] ) && wp_verify_nonce( $_REQUEST['cp_admin_page_nonce'], 'cp_admin_page' ) ) {

			$page = isset( $_POST['admin_page'] ) ? sanitize_text_field( $_POST['admin_page'] ) : '';

			$is_cp_page = isset( $_POST['page'] ) ? $_POST['page'] : '';
			$action_arr = array(
				'smile-modal-designer',
				'smile-info_bar-designer',
				'smile-slide_in-designer',
			);

			$is_cp = false;
			if ( $is_cp_page && in_array( $is_cp_page, $action_arr ) ) {
				$is_cp = true;
			}

			if ( isset( $page ) && 'import' === $page && $is_cp ) {
				$ext = pathinfo( $file['name'], PATHINFO_EXTENSION );

				if ( 'zip' !== $ext ) {
					$file['error'] = 'The uploaded ' . $ext . ' file is not supported. Please upload the exported text file. e.g. .zip';
				}
			}

			return $file;
		}
	}
}

if ( ! function_exists( 'smile_backend_create_folder' ) ) {
	/**
	 * Function Name: smile_backend_create_folder creates a folder for the theme framework.
	 *
	 * @param  string  $folder  folder name.
	 * @param  boolean $addindex index.
	 * @return boolean           true/false.
	 */
	function smile_backend_create_folder( &$folder, $addindex = true ) {
		if ( is_dir( $folder ) && false === $addindex ) {
			return true;
		}
		$created = wp_mkdir_p( trailingslashit( $folder ) );

		if ( false === $addindex ) {
			return $created;
		}
		$index_file = trailingslashit( $folder ) . 'index.html';
		if ( file_exists( $index_file ) ) {
			return $created;
		}

		// Add an index file for security.
		Convert_Plug_Filesystem::prefix_get_filesystem()->put_contents( $index_file, 'Sorry, browsing the directory is not allowed!' );

		return $created;
	}
}
