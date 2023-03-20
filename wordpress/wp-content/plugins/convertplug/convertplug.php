<?php
/**
 *  Plugin Name: Convert Plus
	Plugin URI: https://www.convertplug.com/plus
	Author: Brainstorm Force
	Author URI: https://www.brainstormforce.com
	Version: 3.5.24
	Description: Welcome to Convert Plus - the easiest WordPress plugin to convert website traffic into leads. Convert Plus will help you build email lists, drive traffic, promote videos, offer coupons and much more!
	Text Domain: smile
	License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 *  @package Convert_Plus.
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

if ( ! defined( 'CP_VERSION' ) ) {
	define( 'CP_VERSION', '3.5.24' );
}

if ( ! defined( 'CP_BASE_DIR' ) ) {
	define( 'CP_BASE_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( '__CP_ROOT__' ) ) {
	define( '__CP_ROOT__', dirname( __FILE__ ) );
}

if ( ! defined( 'CP_BASE_URL' ) ) {
	define( 'CP_BASE_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'CP_DIR_NAME' ) ) {
	define( 'CP_DIR_NAME', plugin_basename( dirname( __FILE__ ) ) );
}
if ( ! defined( 'CP_DIR_FILE_NAME' ) ) {
	define( 'CP_DIR_FILE_NAME', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'CP_PLUS_NAME' ) ) {
	define( 'CP_PLUS_NAME', 'Convert Plus' );
}

if ( ! defined( 'CP_PLUS_SLUG' ) ) {
	define( 'CP_PLUS_SLUG', 'convert-plus' );
}

if ( is_admin() ) {
	register_activation_hook( __FILE__, 'on_cp_activate' );
}

if ( ! defined( 'CP_PLUGIN_URL' ) ) {
	define( 'CP_PLUGIN_URL', plugins_url( '/', __FILE__ ) );
}

	define( 'BSF_REMOVE_14058953_FROM_REGISTRATION_LISTING', true );

$cplus_previous_version = get_option( 'cp_previous_version' );

if ( version_compare( $cplus_previous_version, CP_VERSION, '<' ) ) {
	// Update - MaxMind Geolite2 Convert Plus database create directory in uploads folder.
	create_cplus_maxmind_folder_on_activation();
	// UAVC Google font options conflict with CPlus Google Font.
	cplus_google_font_create_update();
	// Delete cp_preset_screenshots folder.
	cp_delete_preset_screenshots();
}

/**
 * Function for activation hook.
 *
 * @since 1.0
 */
function on_cp_activate() {
	update_option( 'convert_plug_redirect', true );
	update_site_option( 'bsf_force_check_extensions', true );
	update_option( 'dismiss-cp-update-notice', false );
	update_site_option( 'bsf_force_check_extensions', true );

	$cp_previous_version = get_option( 'cp_previous_version' );

	if ( ! $cp_previous_version ) {
		update_option( 'cp_is_new_user', true );
	} else {
		update_option( 'cp_is_new_user', false );
	}

	// save previous version of plugin in option.
	update_option( 'cp_previous_version', CP_VERSION );

	/**
	 * Action will run after plugin installer is loaded
	 */
	do_action( 'after_cp_activate' );

	create_cplus_maxmind_folder_on_activation();

	global $wp_version;
	$wp  = '3.5';
	$php = '5.3.2';
	if ( version_compare( PHP_VERSION, $php, '<' ) ) {
		$flag = 'PHP';
	} elseif ( version_compare( $wp_version, $wp, '<' ) ) {
		$flag = 'WordPress';
	} else {
		return;
	}
	$version = ( 'PHP' == $flag ) ? $php : $wp;

	deactivate_plugins( basename( __FILE__ ) );
	wp_die(
		'<p><strong>' . esc_attr( CP_PLUS_NAME ) . ' </strong> requires <strong>' . esc_attr( $flag ) . '</strong> version <strong>' . esc_attr( $version ) . '</strong> or greater. Please contact your host.</p>',
		'Plugin Activation Error',
		array(
			'response'  => 200,
			'back_link' => true,
		)
	);
}

// Add class for the Convert Plus.
require_once dirname( __FILE__ ) . '/classes/class-convert-plug.php';

if ( is_admin() ) {
	// Load Astra Notices library.
	require_once dirname( __FILE__ ) . '/lib/astra-notices/class-astra-notices.php';
}

// BSF Analytics library.
if ( ! class_exists( 'BSF_Analytics_Loader' ) ) {
	require_once dirname( __FILE__ ) . '/admin/bsf-analytics/class-bsf-analytics-loader.php';
}

$bsf_analytics = BSF_Analytics_Loader::get_instance();

$bsf_analytics->set_entity(
	array(
		'bsf' => array(
			'product_name'    => 'Convert Plus',
			'path'            => dirname( __FILE__ ) . '/admin/bsf-analytics',
			'author'          => 'Brainstorm Force',
			'time_to_display' => '+24 hours',
		),
	)
);

/**
 * Function Name: cplus_google_font_create_update - Create and Update Google Font.
 */
function cplus_google_font_create_update() {
	$cplus_ultimate_google_fonts          = get_option( 'ultimate_google_fonts' );
	$cplus_ultimate_selected_google_fonts = get_option( 'ultimate_selected_google_fonts' );
	if ( false !== $cplus_ultimate_google_fonts ) {
		add_option( 'cplus_ultimate_google_fonts', $cplus_ultimate_google_fonts );
	}
	if ( false !== $cplus_ultimate_selected_google_fonts ) {
		add_option( 'cplus_ultimate_selected_google_fonts', $cplus_ultimate_selected_google_fonts );
	}
	// Update CPlus version number.
	update_option( 'cp_previous_version', CP_VERSION );
}

// load google fonts class.
if ( is_admin() ) {
	require_once CP_BASE_DIR . '/framework/class-cplus-ultimate-google-font-manager.php';
	require_once CP_BASE_DIR . '/classes/class-convert-plug-maxmind-geolocation.php';
	require_once CP_BASE_DIR . '/classes/class-convert-plug-filesystem.php';
}

// set global variables.
global $cp_analytics_start_time,$cp_analytics_end_time,$color_pallet,$cp_default_dateformat;

$color_pallet = array(
	'rgba(26, 188, 156,1.0)',
	'rgba(46, 204, 113,1.0)',
	'rgba(52, 152, 219,1.0)',
	'rgba(155, 89, 182,1.0)',
	'rgba(52, 73, 94,1.0)',
	'rgba(241, 196, 15,1.0)',
	'rgba(230, 126, 34,1.0)',
	'rgba(231, 76, 60,1.0)',
	'rgba(236, 240, 241,1.0)',
	'rgba(149, 165, 166,1.0)',
);

$cp_analytics_end_time = current_time( 'd-m-Y' );
$date                  = date_create( $cp_analytics_end_time );
date_sub( $date, date_interval_create_from_date_string( '9 days' ) );
$cp_analytics_start_time = date_format( $date, 'd-m-Y' );

// bsf core.
$bsf_core_version_file = realpath( dirname( __FILE__ ) . '/admin/bsf-core/version.yml' );
if ( is_file( $bsf_core_version_file ) ) {
	global $bsf_core_version, $bsf_core_path;
	$bsf_core_dir = realpath( dirname( __FILE__ ) . '/admin/bsf-core/' );
	$version      = file_get_contents( $bsf_core_version_file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	if ( version_compare( $version, $bsf_core_version, '>' ) ) {
		$bsf_core_version = $version;
		$bsf_core_path    = $bsf_core_dir;
	}
}

add_action( 'init', 'bsf_core_load', 999 );
if ( ! function_exists( 'bsf_core_load' ) ) {
	/**
	 * Function Name: bsf_core_load.
	 */
	function bsf_core_load() {
		global $bsf_core_version, $bsf_core_path;
		if ( is_file( realpath( $bsf_core_path . '/index.php' ) ) ) {
			include_once realpath( $bsf_core_path . '/index.php' );
		}
	}
}

add_filter( 'bsf_core_style_screens', 'cp_bsf_core_style_hooks' );

/**
 * Function Name: cp_bsf_core_style_hooks.
 *
 * @param  array $hooks array of pages.
 * @return array        array of pages.
 */
function cp_bsf_core_style_hooks( $hooks ) {
	$resources_page_hook = CP_PLUS_SLUG . '_page_cp-resources';
	array_push( $hooks, $resources_page_hook );
	return $hooks;
}

if ( ! function_exists( 'cp_bsf_extensions_menu' ) ) {
	/**
	 * Function Name: cp_bsf_extensions_menu Register Convertplug Add-ons installer menu.
	 */
	function cp_bsf_extensions_menu() {
		if ( is_multisite() ) {
			$parent = 'settings.php';
		} else {
			$parent = CP_PLUS_SLUG;
		}

		add_submenu_page(
			$parent,
			__( 'Addons', 'smile' ),
			__( 'Addons', 'smile' ),
			'manage_options',
			'bsf-extensions-14058953',
			'cplus_extension_installer'
		);
	}
}

if ( ! function_exists( 'cplus_extension_installer' ) ) {
	/**
	 * Function Name: cplus_extension_installer Installs Convertplug Add-ons installer menu.
	 */
	function cplus_extension_installer() {
		include_once BSF_UPDATER_PATH . '/plugin-installer/index.php';
	}
}

add_action( 'network_admin_menu', 'cp_bsf_extensions_menu', 9999 );
add_action( 'admin_menu', 'cp_bsf_extensions_menu', 9999 );

/**
 * Multisite Extension menue for ConvertPlus.
 */
function cp_register_options_page() {
	$page = add_menu_page( 'Convert Plus Add-ons', __( 'Convert Plus Add-ons', 'smile' ), 'access_cp', 'bsf-extensions-14058953', '', 'div' );
}

/**
 * Function to create convertplus_uploads folder for MaxMind Geolite2 database.
 *
 * @since 3.5.7
 */
function create_cplus_maxmind_folder_on_activation() {

	// Allow us to easily interact with the filesystem.
	require_once ABSPATH . 'wp-admin/includes/file.php';
	WP_Filesystem();
	global $wp_filesystem;

			// Install files and folders for uploading files and prevent hotlinking.
	$upload_dir = wp_upload_dir();
	$files      = array(
		'base'    => $upload_dir['basedir'] . '/convertplus_uploads',
		'file'    => '.htaccess',
		'content' => 'deny from all',
	);
	if ( wp_mkdir_p( $files['base'] ) && ! file_exists( trailingslashit( $files['base'] ) . $files['file'] ) ) {
		$wp_filesystem->put_contents( $files['base'] . '/' . $files['file'], $files['content'], FS_CHMOD_FILE );
	}
}

/**
 * Delete cp_preset_screenshots folder.
 *
 * @since x.x.x
 */
function cp_delete_preset_screenshots() {

	// Allow us to easily interact with the filesystem.
	require_once ABSPATH . 'wp-admin/includes/file.php';
	WP_Filesystem();
	global $wp_filesystem;

	// Delete cp_preset_screenshots folder.
	$upload_dir          = wp_upload_dir();
	$cp_screenshots_path = trailingslashit( $upload_dir['basedir'] ) . 'cp_preset_screenshots/';
	if ( $wp_filesystem->exists( $cp_screenshots_path ) ) {
		$wp_filesystem->delete( $cp_screenshots_path, true );
	}
}


if ( is_multisite() ) {
	add_action( 'network_admin_menu', 'cp_register_options_page', 9 );
}


/**
 * Heading for the extensions installer screen.
 *
 * @return String: Heading to which will appear on Extensions installer page.
 */
function cp_bsf_extensioninstaller_heading() {
	return CP_PLUS_NAME . ' Addons';
}

add_filter( 'bsf_extinstaller_heading_14058953', 'cp_bsf_extensioninstaller_heading' );

/**
 * Sub Heading for the extensions installer screen.
 *
 * @return String: Sub Heading to which will appear on Extensions installer page.
 */
function cp_bsf_extensioninstaller_subheading() {
	return 'Add-ons extend the functionality of ' . CP_PLUS_NAME . '. With these addons, you can connect with third party softwares, integrate new features and make ' . CP_PLUS_NAME . ' even more powerful.';
}

add_filter( 'bsf_extinstaller_subheading_14058953', 'cp_bsf_extensioninstaller_subheading' );
/**
 * Heading for the extensions installer screen.
 *
 * @return String: Heading to which will appear on Extensions installer page.
 */
function cp_extensioninstaller_heading() {
	return CP_PLUS_NAME . ' Addons';
}

add_filter( 'bsf_extinstaller_heading_14058953', 'cp_extensioninstaller_heading' );

/**
 * Sub Heading for the extensions installer screen.
 *
 * @return String: Sub Heading to which will appear on Extensions installer page.
 */
function cp_extensioninstaller_subheading() {
	return 'Add-ons extend the functionality of ' . CP_PLUS_NAME . '. With these addons, you can connect with third party softwares, integrate new features and make ' . CP_PLUS_NAME . ' even more powerful.';
}

add_filter( 'bsf_extinstaller_subheading_14058953', 'cp_extensioninstaller_subheading' );


// BSF CORE commom functions.
if ( ! function_exists( 'bsf_get_option' ) ) {
	/**
	 * Function Name: bsf_get_option.
	 *
	 * @param  boolean $request true/false.
	 * @return boolean          true/false.
	 */
	function bsf_get_option( $request = false ) {
		$bsf_options = get_option( 'bsf_options' );
		if ( ! $request ) {
			return $bsf_options;
		} else {
			return ( isset( $bsf_options[ $request ] ) ) ? $bsf_options[ $request ] : false;
		}
	}
}
if ( ! function_exists( 'bsf_update_option' ) ) {
	/**
	 * Fucntion name: bsf_update_option.
	 *
	 * @param  string $request string parameters.
	 * @param  string $value   string parameters.
	 * @return boolean          true/false.
	 */
	function bsf_update_option( $request, $value ) {
		$bsf_options             = get_option( 'bsf_options' );
		$bsf_options[ $request ] = $value;
		return update_option( 'bsf_options', $bsf_options );
	}
}

add_action( 'wp_ajax_bsf_dismiss_notice', 'bsf_dismiss_notice' );
if ( ! function_exists( 'bsf_dismiss_notice' ) ) {
	/**
	 * Function Name: bsf_dismiss_notice.
	 */
	function bsf_dismiss_notice() {
		$notice = 'hide-bsf-core-notice';
		$x      = bsf_update_option( $notice, true );
		echo ( $x ) ? true : false;
		die();
	}
}

add_action( 'admin_init', 'bsf_core_check', 10 );
if ( ! function_exists( 'bsf_core_check' ) ) {
	/**
	 * Function Name: bsf_core_check.
	 */
	function bsf_core_check() {
		if ( ! defined( 'BSF_CORE' ) ) {
			if ( ! bsf_get_option( 'hide-bsf-core-notice' ) ) {
				add_action( 'admin_notices', 'bsf_core_admin_notice' );
			}
		}
	}
}

add_action( 'admin_init', 'cp_bsf_update_bg_type', 10 );
if ( ! function_exists( 'cp_bsf_update_bg_type' ) ) {
	/**
	 * Function Name: cp_bsf_update_bg_type.
	 */
	function cp_bsf_update_bg_type() {
		update_option( 'cp_new_bg_type', false );
		$cp_bg_type = get_option( 'cp_new_bg_type' );
		if ( ! $cp_bg_type ) {
			update_option( 'cp_new_bg_type', true );
		} else {
			update_option( 'cp_new_bg_type', false );
		}
	}
}

if ( ! function_exists( 'bsf_core_admin_notice' ) ) {
	/**
	 * Function Name: bsf_core_admin_notice.
	 */
	function bsf_core_admin_notice() {
		?>
		<script type="text/javascript">
			(function($){
				$(document).ready(function(){
					$(document).on( "click", ".bsf-notice", function() {
						var bsf_notice_name = $(this).attr("data-bsf-notice");
						$.ajax({
							url: ajaxurl,
							method: 'POST',
							data: {
								action: "bsf_dismiss_notice",
								notice: bsf_notice_name
							},
							success: function(response) {
								console.log(response);
							}
						})
					})
				});
			})(jQuery);
		</script>
		<div class="bsf-notice update-nag notice is-dismissible" data-bsf-notice="hide-bsf-core-notice">
			<p><?php esc_html_e( 'License registration and extensions are not part of plugin/theme anymore. Kindly download and install "BSF CORE" plugin to manage your licenses and extensins.', 'smile' ); ?></p>
		</div>
		<?php
	}
}

if ( isset( $_GET['hide-bsf-core-notice'] ) && 're-enable' === $_GET['hide-bsf-core-notice'] ) { // phpcs:ignore WordPress.CSRF.NonceVerification.NoNonceVerification
	$x = bsf_update_option( 'hide-bsf-core-notice', false );
}

add_action( 'wp_ajax_cp_dismiss_notice', 'cp_dismiss_notice' );
if ( ! function_exists( 'cp_dismiss_notice' ) ) {
	/**
	 * Function Name: cp_dismiss_notice.
	 */
	function cp_dismiss_notice() {
		$notice = $_POST['notice']; //phpcs:ignore WordPress.Security.NonceVerification.Missing
		$x      = update_option( $notice, true );
		echo ( $x ) ? true : false;
		die();
	}
}

if ( ! function_exists( 'cp_php_version_notice' ) ) {
	/**
	 * Function Name: cp_dismiss_notice display admin notice for outdated php version.
	 */
	function cp_php_version_notice() {
		?>
		<div class="notice notice-warning cp-php-warning is-dismissible">
			<p><?php esc_html_e( 'Your server seems to be running outdated, unsupported and vulnerable version of PHP. You are advised to contact your host and upgrade to PHP 5.6 or greater.', 'smile' ); ?></p>
		</div>
		<?php
	}
}

if ( ! function_exists( 'cp_phardata_notice' ) ) {
	/**
	 * Function Name: cp_dismiss_notice display admin notice for plugin rebranding.
	 */
	function cp_phardata_notice() {
		?>

		<script type="text/javascript">
			(function($){
				$(document).ready(function(){
					$(document).on( "click", ".cp-phardata-warning", function() {
						$.ajax({
							url: ajaxurl,
							method: 'POST',
							data: {
								action: "cp_dismiss_phardata_notice"
							},
							success: function(response) {
								console.log(response);
							}
						})
					})
				});
			})(jQuery);
		</script>

		<div class="notice notice-warning cp-phardata-warning is-dismissible">
			<p>
			<?php
				echo wp_kses_post( 'In order to continue using GeoLocation tracking, you will need to have the PharData extension enabled on your website. Please read the note', 'smile' );
				echo sprintf(
					wp_kses_post( '<a href="https://www.convertplug.com/plus/docs/enable-geo-targeting-in-convert-plus/">here</a>', 'smile' )
				);
			?>
			</p>
		</div>
		<?php
	}
}

// end of common functions.
