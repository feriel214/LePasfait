<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

if ( isset( $_GET['_wpnonce'] ) && ! wp_verify_nonce( $_GET['_wpnonce'], 'slide_in_edit' ) ) {
	return;
}
$test = isset( $_GET['variant-test'] ) ? esc_attr( $_GET['variant-test'] ) : 'main';
switch ( $test ) {
	case 'new':
		require_once CP_BASE_DIR_SLIDEIN . '/views/variant/new.php';
		break;
	case 'edit':
		require_once CP_BASE_DIR_SLIDEIN . '/views/variant/edit.php';
		break;
	case 'main':
	default:
		require_once CP_BASE_DIR_SLIDEIN . '/views/variant/variant.php';
		break;
}

