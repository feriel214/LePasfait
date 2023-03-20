<?php
/**
 * Convert_Plug_Filesystem.
 *
 * @package Convert_Plus
 */

if ( ! class_exists( 'Wp_Filesystem' ) ) {

	/**
	 * Class bsf menu.
	 */
	class Convert_Plug_Filesystem {

		/**
		 * Function Name: prefix_get_filesystem.
		 */
		public static function prefix_get_filesystem() {
			global $wp_filesystem;

			require_once ABSPATH . '/wp-admin/includes/file.php';

			WP_Filesystem();

			return $wp_filesystem;
		}

	}

}
