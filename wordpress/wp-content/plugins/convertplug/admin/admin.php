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

$view = isset( $_GET['view'] ) ? sanitize_text_field( $_GET['view'] ) : '';
switch ( $view ) {
	case 'modules':
		require_once CP_BASE_DIR . '/admin/modules.php';
		break;
	case 'settings':
		require_once CP_BASE_DIR . '/admin/settings.php';
		break;
	case 'debug':
		require_once CP_BASE_DIR . '/admin/debug.php';
		break;
	case 'knowledge_base':
		require_once CP_BASE_DIR . '/admin/knowledge-base.php';
		break;
	default:
		require_once CP_BASE_DIR . '/admin/get-started.php';
		break;
}

