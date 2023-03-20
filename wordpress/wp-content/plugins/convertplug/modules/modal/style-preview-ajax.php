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
* Preview Style
*/
require_once CP_BASE_DIR_MODAL . '/functions/functions.options.php';

$style           = isset( $_GET['style'] ) ? sanitize_text_field( $_GET['style'] ) : '';
$settings_method = isset( $_GET['method'] ) ? esc_attr( sanitize_text_field( $_GET['method'] ) ) : '';
$template_name   = isset( $_GET['temp_name'] ) ? esc_attr( sanitize_text_field( $_GET['temp_name'] ) ) : '';

$options       = Convert_Plug_Smile_Modals::$options;
$style_options = $options[ $style ]['options'];

$settings_encoded = cp_get_live_preview_settings( 'modal', $settings_method, $style_options, $template_name );

$smile_modal = '[smile_modal style="' . $style . '" settings_encoded="' . $settings_encoded . ' "][/smile_modal]';

$html_smile_modal = do_shortcode( $smile_modal );

echo ( htmlentities( $html_smile_modal, ENT_QUOTES, 'utf-8' ) ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

