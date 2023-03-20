<?php
/**
 * Plugin Name: Avada Custom Branding
 * Plugin URI: https://theme-fusion.com
 * Description: Custom Branding plugin for the Avada Website Builder.
 * Version: 1.2
 * Author: ThemeFusion
 * Author URI: https://theme-fusion.com
 *
 * @package Fusion-White-Label-Branding
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Plugin version.
if ( ! defined( 'FUSION_WHITE_LABEL_BRANDING_VERSION' ) ) {
	define( 'FUSION_WHITE_LABEL_BRANDING_VERSION', '1.2' );
}
// Plugin Folder Path.
if ( ! defined( 'FUSION_WHITE_LABEL_BRANDING_PLUGIN_DIR' ) ) {
	define( 'FUSION_WHITE_LABEL_BRANDING_PLUGIN_DIR', wp_normalize_path( plugin_dir_path( __FILE__ ) ) );
}
// Plugin Folder URL.
if ( ! defined( 'FUSION_WHITE_LABEL_BRANDING_PLUGIN_URL' ) ) {
	define( 'FUSION_WHITE_LABEL_BRANDING_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
// Plugin Root File.
if ( ! defined( 'FUSION_WHITE_LABEL_BRANDING_PLUGIN_FILE' ) ) {
	define( 'FUSION_WHITE_LABEL_BRANDING_PLUGIN_FILE', __FILE__ );
}

register_activation_hook( __FILE__, array( 'Fusion_White_Label_Branding', 'activation' ) );

if ( ! class_exists( 'Fusion_White_Label_Branding' ) ) {
	require_once trailingslashit( FUSION_WHITE_LABEL_BRANDING_PLUGIN_DIR ) . 'inc/class-fusion-white-label-branding.php';
}

/**
 * Instantiate Fusion_White_Label_Branding class.
 *
 * @since 1.0
 * @return void
 */
function fusion_white_label_branding_activate() {
	Fusion_White_Label_Branding::get_instance();
}
add_action( 'after_setup_theme', 'fusion_white_label_branding_activate', 11 );

/**
 * Instantiate FusionBuilder class.
 * We'll add the hook on 'after_setup_theme' with a priority 9999
 * to be sure it runs AFTER the Avada/Fusion-Builder patchers.
 */
function fusion_white_label_branding_patcher() {

	// Include Fusion-Library.
	$class_files = array(
		'Fusion_Helper'                => 'inc/patcher/class-fusion-helper.php',
		'Fusion_Cache'                 => 'inc/patcher/class-fusion-cache.php',
		'Fusion_Patcher'               => 'inc/patcher/class-fusion-patcher.php',
		'Fusion_Patcher_Admin_Notices' => 'inc/patcher/class-fusion-patcher-admin-notices.php',
		'Fusion_Patcher_Admin_Screen'  => 'inc/patcher/class-fusion-patcher-admin-screen.php',
		'Fusion_Patcher_Apply_Patch'   => 'inc/patcher/class-fusion-patcher-apply-patch.php',
		'Fusion_Patcher_Cache'         => 'inc/patcher/class-fusion-patcher-cache.php',
		'Fusion_Patcher_Checker'       => 'inc/patcher/class-fusion-patcher-checker.php',
		'Fusion_Patcher_Client'        => 'inc/patcher/class-fusion-patcher-client.php',
		'Fusion_Patcher_Filesystem'    => 'inc/patcher/class-fusion-patcher-filesystem.php',
	);
	foreach ( $class_files as $classname => $file ) {
		if ( ! class_exists( $classname ) ) {
			require_once FUSION_WHITE_LABEL_BRANDING_PLUGIN_DIR . $file;
		}
	}
	new Fusion_Patcher(
		array(
			'context'     => 'fusion-white-label-branding',
			'version'     => FUSION_WHITE_LABEL_BRANDING_VERSION,
			'name'        => 'Avada Custom Branding',
			'parent_slug' => 'fusion-white-label-branding-admin',
			'page_title'  => esc_attr__( 'Avada Patcher', 'fusion-builder' ),
			'menu_title'  => esc_attr__( 'Avada Patcher', 'fusion-builder' ),
			'classname'   => 'Fusion_White_Label_Branding',
		)
	);
}
add_action( 'after_setup_theme', 'fusion_white_label_branding_patcher', 9999 );
