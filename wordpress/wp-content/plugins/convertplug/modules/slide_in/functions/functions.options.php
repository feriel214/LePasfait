<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

$dir     = plugin_dir_path( __FILE__ );
$configs = glob( $dir . 'config/*.php' );
foreach ( $configs as $settings ) {
	require_once $settings;
}

if ( is_admin() ) {
	require_once 'functions.common.php';

	add_action( 'admin_enqueue_scripts', 'cp_enqueue_ultimate_google_fonts' );
	if ( ! function_exists( 'cp_enqueue_ultimate_google_fonts' ) ) {
		/**
		 * Function Name: cp_enqueue_ultimate_google_fonts.
		 */
		function cp_enqueue_ultimate_google_fonts() {
			$selected_fonts = get_option( 'cplus_ultimate_selected_google_fonts' );
			if ( ! empty( $selected_fonts ) ) {
				$count     = count( $selected_fonts );
				$font_call = '';
				foreach ( $selected_fonts as $key => $sfont ) {
					if ( 0 !== $key ) {
						$font_call .= '|';
					}
					$font_call .= $sfont['font_family'];
					if ( isset( $sfont['variants'] ) ) :
						$variants = $sfont['variants'];
						if ( ! empty( $variants ) ) {
							$variants_count = count( $variants );
							$font_call     .= ':';
							foreach ( $variants as $vkey => $variant ) {
								$variant_selected = $variant['variant_selected'];
								if ( 'true' === $variant_selected || is_admin() ) {
									$font_call .= $variant['variant_value'];
									if ( ( $variants_count - 1 ) !== $vkey && 0 < $variants_count ) {
										$font_call .= ',';
									}
								}
							}
						}
					endif;
				}

				$link = 'https://fonts.googleapis.com/css?family=' . $font_call;
				wp_register_style( 'convert-plus-ultimate-selected-google-fonts-style', $link, array(), CP_VERSION );
			}
		}
	}
}
