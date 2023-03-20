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
	case 'new-list':
		require_once CP_BASE_DIR . '/admin/contacts/views/new-list.php';
		break;
	case 'contacts':
		require_once CP_BASE_DIR . '/admin/contacts/views/contacts.php';
		break;
	case 'analytics':
		require_once CP_BASE_DIR . '/admin/contacts/views/analytics.php';
		break;
	case 'contact-details':
		require_once CP_BASE_DIR . '/admin/contacts/views/contact-details.php';
		break;
	default:
		require_once CP_BASE_DIR . '/admin/contacts/views/dashboard.php';
		break;
}

