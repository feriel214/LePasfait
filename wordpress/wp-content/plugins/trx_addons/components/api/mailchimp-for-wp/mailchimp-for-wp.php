<?php
/**
 * Plugin support: Mail Chimp
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.5
 */

// Check if plugin installed and activated
if ( !function_exists( 'trx_addons_exists_mailchimp' ) ) {
	function trx_addons_exists_mailchimp() {
		return function_exists('__mc4wp_load_plugin') || defined('MC4WP_VERSION');
	}
}

// Hack for MailChimp - disable scroll to form, because it broke layout in the Chrome 
if ( !function_exists( 'trx_addons_mailchimp_scroll_to_form' ) ) {
	add_filter( 'mc4wp_form_auto_scroll', 'trx_addons_mailchimp_scroll_to_form' );
	function trx_addons_mailchimp_scroll_to_form($scroll) {
		return false;
	}
}



// One-click import support
//------------------------------------------------------------------------

// Check plugin in the required plugins
if ( !function_exists( 'trx_addons_mailchimp_importer_required_plugins' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_importer_required_plugins',	'trx_addons_mailchimp_importer_required_plugins', 10, 2 );
	function trx_addons_mailchimp_importer_required_plugins($not_installed='', $list='') {
		if (strpos($list, 'mailchimp-for-wp')!==false && !trx_addons_exists_mailchimp() )
			$not_installed .= '<br>' . esc_html__('MailChimp for WP', 'trx_addons');
		return $not_installed;
	}
}

// Set plugin's specific importer options
if ( !function_exists( 'trx_addons_mailchimp_importer_set_options' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_importer_options',	'trx_addons_mailchimp_importer_set_options' );
	function trx_addons_mailchimp_importer_set_options($options=array()) {
		if ( trx_addons_exists_mailchimp() && in_array('mailchimp-for-wp', $options['required_plugins']) ) {
			if (is_array($options)) {
				$options['additional_options'][] = 'mc4wp_default_form_id';		// Add slugs to export options for this plugin
				$options['additional_options'][] = 'mc4wp_form_stylesheets';
				$options['additional_options'][] = 'mc4wp_flash_messages';
				$options['additional_options'][] = 'mc4wp_integrations';
			}
		}
		return $options;
	}
}

// Prevent import plugin's specific options if plugin is not installed
if ( !function_exists( 'trx_addons_mailchimp_importer_check_options' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_import_theme_options', 'trx_addons_mailchimp_importer_check_options', 10, 4 );
	function trx_addons_mailchimp_importer_check_options($allow, $k, $v, $options) {
		if ($allow && strpos($k, 'mc4wp_')===0) {
			$allow = trx_addons_exists_mailchimp() && in_array('mailchimp-for-wp', $options['required_plugins']);
		}
		return $allow;
	}
}

// Add checkbox to the one-click importer
if ( !function_exists( 'trx_addons_mailchimp_importer_show_params' ) ) {
	if (is_admin()) add_action( 'trx_addons_action_importer_params',	'trx_addons_mailchimp_importer_show_params', 10, 1 );
	function trx_addons_mailchimp_importer_show_params($importer) {
		if ( trx_addons_exists_mailchimp() && in_array('mailchimp-for-wp', $importer->options['required_plugins']) ) {
			$importer->show_importer_params(array(
				'slug' => 'mailchimp-for-wp',
				'title' => esc_html__('Import MailChimp for WP', 'trx_addons'),
				'part' => 1
			));
		}
	}
}

// Check if the row will be imported
if ( !function_exists( 'trx_addons_mailchimp_importer_check_row' ) ) {
	if (is_admin()) add_filter('trx_addons_filter_importer_import_row', 'trx_addons_mailchimp_importer_check_row', 9, 4);
	function trx_addons_mailchimp_importer_check_row($flag, $table, $row, $list) {
		if ($flag || strpos($list, 'mailchimp-for-wp')===false) return $flag;
		if ( trx_addons_exists_mailchimp() ) {
			if ($table == 'posts')
				$flag = $row['post_type']=='mc4wp-form';
		}
		return $flag;
	}
}




// OCDI support
//------------------------------------------------------------------------
// Set plugin's specific importer options
if ( !function_exists( 'trx_addons_ocdi_mailchimp_set_options' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_ocdi_options', 'trx_addons_ocdi_mailchimp_set_options' );
	function trx_addons_ocdi_mailchimp_set_options($ocdi_options){
		$ocdi_options['import_mailchimp_file_url'] = 'mailchimp.txt';
		return $ocdi_options;		
	}
}

// Export MailChimp for WP
if ( !function_exists( 'trx_addons_ocdi_mailchimp_export' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_ocdi_export_files', 'trx_addons_ocdi_mailchimp_export' );
	function trx_addons_ocdi_mailchimp_export($output){
		$list = array();
		if (trx_addons_exists_mailchimp() && in_array('mailchimp-for-wp', trx_addons_ocdi_options('required_plugins'))) {
			// Get plugin data from database
			$options = array('mc4wp_default_form_id', 'mc4wp_form_stylesheets', 'mc4wp_flash_messages', 'mc4wp_integrations');
			$list = trx_addons_ocdi_export_options($options, $list);
			
			// Save as file
			$file_path = TRX_ADDONS_PLUGIN_OCDI . "export/mailchimp.txt";
			trx_addons_fpc(trx_addons_get_file_dir($file_path), serialize($list));
			
			// Return file path
			$output .= '<h4><a href="'. trx_addons_get_file_url($file_path).'" download>'.esc_html__('MailChimp for WP', 'trx_addons').'</a></h4>';
		}
		return $output;
	}
}

// Add plugin to import list
if ( !function_exists( 'trx_addons_ocdi_mailchimp_import_field' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_ocdi_import_fields', 'trx_addons_ocdi_mailchimp_import_field' );
	function trx_addons_ocdi_mailchimp_import_field($output){
		$list = array();
		if (trx_addons_exists_mailchimp() && in_array('mailchimp-for-wp', trx_addons_ocdi_options('required_plugins'))) {
			$output .= '<label><input type="checkbox" name="mailchimp" value="mailchimp">'. esc_html__( 'MailChimp for WP', 'trx_addons' ).'</label><br/>';
		}
		return $output;
	}
}

// Import MailChimp for WP
if ( !function_exists( 'trx_addons_ocdi_mailchimp_import' ) ) {
	if (is_admin()) add_action( 'trx_addons_action_ocdi_import_plugins', 'trx_addons_ocdi_mailchimp_import', 10, 1 );
	function trx_addons_ocdi_mailchimp_import( $import_plugins){
		if (trx_addons_exists_mailchimp() && in_array('mailchimp-for-wp', $import_plugins)) {
			trx_addons_ocdi_import_dump('mailchimp');
			echo esc_html__('MailChimp for WP import complete.', 'trx_addons') . "\r\n";
		}
	}
}
?>