<?php
/**
 * Plugin support: WP GDPR Compliance
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.49
 */

// Check if plugin installed and activated
if ( !function_exists( 'trx_addons_exists_wp_gdpr_compliance' ) ) {
	function trx_addons_exists_wp_gdpr_compliance() {
		return class_exists( 'WPGDPRC\WPGDPRC' );
	}
}


// One-click import support
//------------------------------------------------------------------------

// Check plugin in the required plugins
if ( !function_exists( 'trx_addons_wp_gdpr_compliance_importer_required_plugins' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_importer_required_plugins',	'trx_addons_wp_gdpr_compliance_importer_required_plugins', 10, 2 );
	function trx_addons_wp_gdpr_compliance_importer_required_plugins($not_installed='', $list='') {
		if (strpos($list, 'wp-gdpr-compliance')!==false && !trx_addons_exists_wp_gdpr_compliance() )
			$not_installed .= '<br>' . esc_html__('WP GDPR Compliance', 'trx_addons');
		return $not_installed;
	}
}

// Set plugin's specific importer options
if ( !function_exists( 'trx_addons_wp_gdpr_compliance_importer_set_options' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_importer_options',	'trx_addons_wp_gdpr_compliance_importer_set_options' );
	function trx_addons_wp_gdpr_compliance_importer_set_options($options=array()) {
		if ( trx_addons_exists_wp_gdpr_compliance() && in_array('wp-gdpr-compliance', $options['required_plugins']) ) {
			if (is_array($options)) {
				$options['additional_options'][] = 'wpgdprc_%';
			}
		}
		return $options;
	}
}

// Prevent import plugin's specific options if plugin is not installed
if ( !function_exists( 'trx_addons_wp_gdpr_compliance_importer_check_options' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_import_theme_options', 'trx_addons_wp_gdpr_compliance_importer_check_options', 10, 4 );
	function trx_addons_wp_gdpr_compliance_importer_check_options($allow, $k, $v, $options) {
		if ($allow && strpos($k, 'wpgdprc_')===0) {
			$allow = trx_addons_exists_wp_gdpr_compliance() && in_array('wp-gdpr-compliance', $options['required_plugins']);
		}
		return $allow;
	}
}




// OCDI support
//------------------------------------------------------------------------

// Set plugin's specific importer options
if ( !function_exists( 'trx_addons_ocdi_wp_gdpr_compliance_feed_set_options' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_ocdi_options', 'trx_addons_ocdi_wp_gdpr_compliance_feed_set_options' );
	function trx_addons_ocdi_wp_gdpr_compliance_feed_set_options($ocdi_options){
		$ocdi_options['import_wp-gdpr-compliance_file_url'] = 'wp-gdpr-compliance.txt';
		return $ocdi_options;		
	}
}

// Export plugin's data
if ( !function_exists( 'trx_addons_ocdi_wp_gdpr_compliance_export' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_ocdi_export_files', 'trx_addons_ocdi_wp_gdpr_compliance_export' );
	function trx_addons_ocdi_wp_gdpr_compliance_export($output){
		$list = array();
		if (trx_addons_exists_wp_gdpr_compliance() && in_array('wp-gdpr-compliance', trx_addons_ocdi_options('required_plugins'))) {
			// Get plugin data from database
			$options = array('wpgdprc_%');
			$list = trx_addons_ocdi_export_options($options, $list);
			
			// Save as file
			$file_path = TRX_ADDONS_PLUGIN_OCDI . "export/wp-gdpr-compliance.txt";
			trx_addons_fpc(trx_addons_get_file_dir($file_path), serialize($list));
			
			// Return file path
			$output .= '<h4><a href="'. trx_addons_get_file_url($file_path).'" download>'.esc_html__('WP GDPR Compliance', 'trx_addons').'</a></h4>';
		}
		return $output;
	}
}

// Add plugin to import list
if ( !function_exists( 'trx_addons_ocdi_wp_gdpr_compliance_import_field' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_ocdi_import_fields', 'trx_addons_ocdi_wp_gdpr_compliance_import_field' );
	function trx_addons_ocdi_wp_gdpr_compliance_import_field($output){
		$list = array();
		if (trx_addons_exists_wp_gdpr_compliance() && in_array('wp-gdpr-compliance', trx_addons_ocdi_options('required_plugins'))) {
			$output .= '<label><input type="checkbox" name="wp-gdpr-compliance" value="wp-gdpr-compliance">'. esc_html__( 'WP GDPR Compliance', 'trx_addons' ).'</label><br/>';
		}
		return $output;
	}
}

// Import plugin's data
if ( !function_exists( 'trx_addons_ocdi_wp_gdpr_compliance_import' ) ) {
	if (is_admin()) add_action( 'trx_addons_action_ocdi_import_plugins', 'trx_addons_ocdi_wp_gdpr_compliance_import', 10, 1 );
	function trx_addons_ocdi_wp_gdpr_compliance_import( $import_plugins){
		if (trx_addons_exists_wp_gdpr_compliance() && in_array('wp-gdpr-compliance', $import_plugins)) {
			trx_addons_ocdi_import_dump('wp-gdpr-compliance');
			echo esc_html__('WP GDPR Compliance import complete.', 'trx_addons') . "\r\n";
		}
	}
}
