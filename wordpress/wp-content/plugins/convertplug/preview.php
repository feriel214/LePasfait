<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

if ( isset( $_REQUEST['cp_admin_page_nonce'] ) && ! wp_verify_nonce( $_REQUEST['cp_admin_page_nonce'], 'cp_admin_page' ) ) {
	wp_die( 'No direct script access allowed!' );
}

$module = isset( $_GET['module'] ) ? sanitize_text_field( $_GET['module'] ) : '';
$theme  = isset( $_GET['theme'] ) ? esc_attr( $_GET['theme'] ) : '';
$class  = isset( $_GET['class'] ) ? sanitize_text_field( $_GET['class'] ) : '';


if ( '' !== $module ) {

	if ( file_exists( CP_BASE_DIR . '/modules/' . $module . '/functions/functions.options.php' ) ) {

		require_once CP_BASE_DIR . '/modules/' . $module . '/functions/functions.options.php';

		$settings = $class::$options;
		foreach ( $settings as $style => $options ) {
			if ( $style === $theme ) {
				$demo_html     = $options['demo_url'];
				$demo_dir      = $options['demo_dir'];
				$customizer_js = $options['customizer_js'];
			}
		}

		$post_content = Convert_Plug_Filesystem::prefix_get_filesystem()->get_contents( $demo_dir );
		print_r( $post_content ); //phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_print_r
	}
}
