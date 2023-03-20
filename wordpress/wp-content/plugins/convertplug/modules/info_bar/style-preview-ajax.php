<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

if ( isset( $_REQUEST['cp_admin_page_nonce'] ) && ! wp_verify_nonce( $_REQUEST['cp_admin_page_nonce'], 'cp_admin_page' ) ) {
	return;
}

/*
 * Preview Style.
 */
require_once CP_BASE_DIR_IFB . 'functions/functions.options.php';

$style           = isset( $_GET['style'] ) ? sanitize_text_field( $_GET['style'] ) : '';
$settings_method = isset( $_GET['method'] ) ? sanitize_text_field( $_GET['method'] ) : '';
$template_name   = isset( $_GET['temp_name'] ) ? sanitize_text_field( $_GET['temp_name'] ) : '';

$options       = Convert_Plug_Smile_Info_Bars::$options;
$style_options = $options[ $style ]['options'];

$settings_encoded = cp_get_live_preview_settings( 'info_bar', $settings_method, $style_options, $template_name );

echo '<style type="text/css">
.cp-overlay {
	background: rgb(0, 0, 0);
	opacity: 0.2;
	filter: alpha(opacity=70);
	position: fixed;
	top: 0;
	right: 0;
	bottom: 0;
	left: 0;
	z-index: 100050;
}
</style>';
echo '<div class="cp-overlay"></div>';

$smile_info_bar = '[smile_info_bar style="' . $style . '" settings_encoded="' . $settings_encoded . ' "][/smile_info_bar]';

$html = do_shortcode( $smile_info_bar );

echo ( htmlentities( $html, ENT_QUOTES, 'utf-8' ) ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

