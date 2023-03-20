<?php
/**
 * Plugin support: Uber Menu
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.5
 */

// Check if plugin installed and activated
if ( !function_exists( 'trx_addons_exists_ubermenu' ) ) {
	function trx_addons_exists_ubermenu() {
		return class_exists('UberMenu');
	}
}
	

// Return true if theme location assigned to UberMenu
if ( !function_exists( 'trx_addons_ubermenu_check_location' ) ) {
	function trx_addons_ubermenu_check_location($loc) {
		$rez = false;
		if (trx_addons_exists_ubermenu()) {
			$theme_loc = ubermenu_op( 'auto_theme_location', 'main' );
			$rez = !empty($theme_loc[$loc]);
		}
		return $rez;
	}
}


// One-click import support
//------------------------------------------------------------------------

// Check plugin in the required plugins
if ( !function_exists( 'trx_addons_ubermenu_importer_required_plugins' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_importer_required_plugins',	'trx_addons_ubermenu_importer_required_plugins', 10, 2 );
	function trx_addons_ubermenu_importer_required_plugins($not_installed='', $list='') {
		if (strpos($list, 'ubermenu')!==false && !trx_addons_exists_ubermenu() )
			$not_installed .= '<br>' . esc_html__('UberMenu', 'trx_addons');
		return $not_installed;
	}
}

// Set plugin's specific importer options
if ( !function_exists( 'trx_addons_ubermenu_importer_set_options' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_importer_options', 'trx_addons_ubermenu_importer_set_options', 10, 1 );
	function trx_addons_ubermenu_importer_set_options($options=array()) {
		if ( trx_addons_exists_ubermenu() && in_array('ubermenu', $options['required_plugins']) ) {
			$options['additional_options'][]	= 'ubermenu_%';				// Add slugs to export options of this plugin
		}
		return $options;
	}
}

// Prevent import plugin's specific options if plugin is not installed
if ( !function_exists( 'trx_addons_ubermenu_importer_check_options' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_import_theme_options', 'trx_addons_ubermenu_importer_check_options', 10, 4 );
	function trx_addons_ubermenu_importer_check_options($allow, $k, $v, $options) {
		if ($allow && strpos($k, 'ubermenu_')===0) {
			$allow = trx_addons_exists_ubermenu() && in_array('ubermenu', $options['required_plugins']);
		}
		return $allow;
	}
}




// OCDI support
//------------------------------------------------------------------------
// Set plugin's specific importer options
if ( !function_exists( 'trx_addons_ocdi_ubermenu_set_options' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_ocdi_options', 'trx_addons_ocdi_ubermenu_set_options' );
	function trx_addons_ocdi_ubermenu_set_options($ocdi_options){
		$ocdi_options['import_ubermenu_file_url'] = 'ubermenu.txt';
		return $ocdi_options;		
	}
}

// Export UberMenu
if ( !function_exists( 'trx_addons_ocdi_ubermenu_export' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_ocdi_export_files', 'trx_addons_ocdi_ubermenu_export' );
	function trx_addons_ocdi_ubermenu_export($output){
		$list = array();
		if (trx_addons_exists_ubermenu() && in_array('ubermenu', trx_addons_ocdi_options('required_plugins'))) {
			// Get plugin data from database
			$options = array('ubermenu_%');
			$list = trx_addons_ocdi_export_options($options, $list);
			
			// Save as file
			$file_path = TRX_ADDONS_PLUGIN_OCDI . "export/ubermenu.txt";
			trx_addons_fpc(trx_addons_get_file_dir($file_path), serialize($list));
			
			// Return file path
			$output .= '<h4><a href="'. trx_addons_get_file_url($file_path).'" download>'.esc_html__('UberMenu', 'trx_addons').'</a></h4>';
		}
		return $output;
	}
}

// Add plugin to import list
if ( !function_exists( 'trx_addons_ocdi_ubermenu_import_field' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_ocdi_import_fields', 'trx_addons_ocdi_ubermenu_import_field' );
	function trx_addons_ocdi_ubermenu_import_field($output){
		$list = array();
		if (trx_addons_exists_ubermenu() && in_array('ubermenu', trx_addons_ocdi_options('required_plugins'))) {
			$output .= '<label><input type="checkbox" name="ubermenu" value="ubermenu">'. esc_html__( 'UberMenu', 'trx_addons' ).'</label><br/>';
		}
		return $output;
	}
}

// Import UberMenu
if ( !function_exists( 'trx_addons_ocdi_ubermenu_import' ) ) {
	if (is_admin()) add_action( 'trx_addons_action_ocdi_import_plugins', 'trx_addons_ocdi_ubermenu_import', 10, 1 );
	function trx_addons_ocdi_ubermenu_import( $import_plugins){
		if (trx_addons_exists_ubermenu() && in_array('ubermenu', $import_plugins)) {
			trx_addons_ocdi_import_dump('ubermenu');
			echo esc_html__('UberMenu import complete.', 'trx_addons') . "\r\n";
		}
	}
}
?>