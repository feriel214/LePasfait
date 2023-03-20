<?php
/**
 * Plugin support: WPML
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.38
 */

// Check if plugin installed and activated
// Attention! This function is used in many files and was moved to the api.php
/*
if ( !function_exists( 'trx_addons_exists_wpml' ) ) {
	function trx_addons_exists_wpml() {
		return defined('ICL_SITEPRESS_VERSION') && class_exists('sitepress');
	}
}
*/

// Return default language
if ( !function_exists( 'trx_addons_wpml_get_default_language' ) ) {
	function trx_addons_wpml_get_default_language() {
		return trx_addons_exists_wpml() ? apply_filters( 'wpml_default_language', null ) : '';
	}
}

// Return current language
if ( !function_exists( 'trx_addons_wpml_get_current_language' ) ) {
	function trx_addons_wpml_get_current_language() {
		return trx_addons_exists_wpml() ? apply_filters( 'wpml_current_language', null ) : '';
	}
}


// Create option with current language
if (!function_exists('trx_addons_wpml_add_current_language_option')) {
	add_filter('trx_addons_filter_options', 'trx_addons_wpml_add_current_language_option');
	function trx_addons_wpml_add_current_language_option($options) {
		if (trx_addons_exists_wpml()) {
			$options['wpml_current_language'] = array(
				"title" => '',
				"desc" => '',
				"std" => trx_addons_wpml_get_current_language(),
				"type" => "hidden"
			);
		}
		return $options;
	}
}

// Create translated option's values
if (!function_exists('trx_addons_wpml_replace_translated_options')) {
	add_filter('trx_addons_filter_load_options', 'trx_addons_wpml_replace_translated_options');
	function trx_addons_wpml_replace_translated_options($values) {
		if (trx_addons_exists_wpml()) {
			global $TRX_ADDONS_STORAGE;
			if (is_array($values) && isset($TRX_ADDONS_STORAGE['options']) && is_array($TRX_ADDONS_STORAGE['options'])) {
				$translated = apply_filters('trx_addons_filter_load_options_translated', get_option('trx_addons_options_translated'));
				if (empty($translated)) $translated = array();
				$lang = trx_addons_wpml_get_current_language();
				foreach ($TRX_ADDONS_STORAGE['options'] as $k=>$v) {
					if (empty($v['translate'])) continue;
					$param_name = sprintf('%1$s_lang_%2$s', $k, $lang);
					if (isset($translated[$param_name]))
						$values[$k] = $translated[$param_name];
				}
				// Disable menu cache if WPML is active
				if (!empty($values['menu_cache'])) $values['menu_cache'] = 0;
			}
		}
		return $values;
	}
}

// Disable menu cache if WPML is active
if (!function_exists('trx_addons_wpml_disable_menu_cache')) {
	add_filter('trx_addons_filter_options_save', 'trx_addons_wpml_disable_menu_cache');
	function trx_addons_wpml_disable_menu_cache($values) {
		if (trx_addons_exists_wpml()) {
			if (!empty($values['menu_cache'])) $values['menu_cache'] = 0;
		}
		return $values;
	}
}


// Duplicate translatable options for each language
if (!function_exists('trx_addons_wpml_duplicate_options')) {
	add_filter('trx_addons_filter_options_save', 'trx_addons_wpml_duplicate_options');
	function trx_addons_wpml_duplicate_options($values) {
		if (trx_addons_exists_wpml()) {
			// Detect current language
			if (isset($values['wpml_current_language'])) {
				$tmp = explode('!', $values['wpml_current_language']);
				$lang = $tmp[0];
				unset($values['wpml_current_language']);
			} else {
				$lang = trx_addons_wpml_get_current_language();
			}

			// Duplicate options to the language-specific options and remove original
			if (is_array($values)) {
				$translated = apply_filters('trx_addons_filter_load_options_translated', get_option('trx_addons_options_translated'));
				if (empty($translated)) $translated = array();
				global $TRX_ADDONS_STORAGE;
				if (is_array($values) && isset($TRX_ADDONS_STORAGE['options']) && is_array($TRX_ADDONS_STORAGE['options'])) {
					$changed = false;
					foreach ($TRX_ADDONS_STORAGE['options'] as $k => $v) {
						if (!empty($v['translate']) && isset($values[$k])) {
							$param_name = sprintf('%1$s_lang_%2$s', $k, $lang);
							$translated[$param_name] = $values[$k];
							$changed = true;
						}
					}
					if ($changed) {
						update_option('trx_addons_options_translated', $translated);
					}
				}
			}
		}
		return $values;
	}
}


// One-click import support
//------------------------------------------------------------------------

// Check plugin in the required plugins
if ( !function_exists( 'trx_addons_wpml_importer_required_plugins' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_importer_required_plugins',	'trx_addons_wpml_importer_required_plugins', 10, 2 );
	function trx_addons_wpml_importer_required_plugins($not_installed='', $list='') {
		if (strpos($list, 'sitepress-multilingual-cms')!==false && !trx_addons_exists_wpml() )
			$not_installed .= '<br>' . esc_html__('WPML - Sitepress Multilingual CMS', 'trx_addons');
		return $not_installed;
	}
}

// Set plugin's specific importer options
if ( !function_exists( 'trx_addons_wpml_importer_set_options' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_importer_options',	'trx_addons_wpml_importer_set_options' );
	function trx_addons_wpml_importer_set_options($options=array()) {
		if ( trx_addons_exists_wpml() && in_array('sitepress-multilingual-cms', $options['required_plugins']) ) {
			$options['additional_options'][] = 'icl_sitepress_settings';
		}
		if (is_array($options['files']) && count($options['files']) > 0) {
			foreach ($options['files'] as $k => $v) {
				$options['files'][$k]['file_with_sitepress-multilingual-cms'] = str_replace('name.ext', 'sitepress-multilingual-cms.txt', $v['file_with_']);
			}
		}
		return $options;
	}
}

// Prevent import plugin's specific options if plugin is not installed
if ( !function_exists( 'trx_addons_wpml_importer_check_options' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_import_theme_options',	'trx_addons_wpml_importer_check_options', 10, 4 );
	function trx_addons_wpml_importer_check_options($allow, $k, $v, $options) {
		if ($allow && $k == 'icl_sitepress_settings') {
			$allow = trx_addons_exists_wpml() && in_array('sitepress-multilingual-cms', $options['required_plugins']);
		}
		return $allow;
	}
}

// Add checkbox to the one-click importer
if ( !function_exists( 'trx_addons_wpml_importer_show_params' ) ) {
	if (is_admin()) add_action( 'trx_addons_action_importer_params',	'trx_addons_wpml_importer_show_params', 10, 1 );
	function trx_addons_wpml_importer_show_params($importer) {
		if ( trx_addons_exists_wpml() && in_array('sitepress-multilingual-cms', $importer->options['required_plugins']) ) {
			$importer->show_importer_params(array(
				'slug' => 'sitepress-multilingual-cms',
				'title' => esc_html__('Import Sitepress Multilingual CMS (WPML)', 'trx_addons'),
				'part' => 0
			));
		}
	}
}

// Import posts
if ( !function_exists( 'trx_addons_wpml_importer_import' ) ) {
	if (is_admin()) add_action( 'trx_addons_action_importer_import',	'trx_addons_wpml_importer_import', 10, 2 );
	function trx_addons_wpml_importer_import($importer, $action) {
		if ( trx_addons_exists_woocommerce() && in_array('sitepress-multilingual-cms', $importer->options['required_plugins']) ) {
			if ( $action == 'import_sitepress-multilingual-cms' ) {
				$importer->response['start_from_id'] = 0;
				$importer->import_dump('sitepress-multilingual-cms', esc_html__('Sitepress Multilingual CMS (WPML) data', 'trx_addons'));
			}
		}
	}
}

// Display import progress
if ( !function_exists( 'trx_addons_wpml_importer_import_fields' ) ) {
	if (is_admin()) add_action( 'trx_addons_action_importer_import_fields',	'trx_addons_wpml_importer_import_fields', 10, 1 );
	function trx_addons_wpml_importer_import_fields($importer) {
		if ( trx_addons_exists_wpml() && in_array('sitepress-multilingual-cms', $importer->options['required_plugins']) ) {
			$importer->show_importer_fields(array(
				'slug'=>'sitepress-multilingual-cms', 
				'title' => esc_html__('Sitepress Multilingual CMS (WPML) data', 'trx_addons')
				)
			);
		}
	}
}

// Export posts
if ( !function_exists( 'trx_addons_wpml_importer_export' ) ) {
	if (is_admin()) add_action( 'trx_addons_action_importer_export',	'trx_addons_wpml_importer_export', 10, 1 );
	function trx_addons_wpml_importer_export($importer) {
		if ( trx_addons_exists_wpml() && in_array('sitepress-multilingual-cms', $importer->options['required_plugins']) ) {
			trx_addons_fpc($importer->export_file_dir('sitepress-multilingual-cms.txt'), serialize( array(
				"icl_languages"				=> $importer->export_dump("icl_languages"),
				"icl_locale_map"			=> $importer->export_dump("icl_locale_map"),
				"icl_strings"				=> $importer->export_dump("icl_strings"),
				"icl_string_packages"		=> $importer->export_dump("icl_string_packages"),
				"icl_string_pages"			=> $importer->export_dump("icl_string_pages"),
				"icl_string_positions"		=> $importer->export_dump("icl_string_positions"),
				"icl_translate"				=> $importer->export_dump("icl_translate"),
				"icl_translate_job"			=> $importer->export_dump("icl_translate_job"),
				"icl_translations"			=> $importer->export_dump("icl_translations"),
				"icl_translation_batches"	=> $importer->export_dump("icl_translation_batches"),
				"icl_translation_status"	=> $importer->export_dump("icl_translation_status"),
				) )
			);
		}
	}
}

// Display exported data in the fields
if ( !function_exists( 'trx_addons_wpml_importer_export_fields' ) ) {
	if (is_admin()) add_action( 'trx_addons_action_importer_export_fields',	'trx_addons_wpml_importer_export_fields', 10, 1 );
	function trx_addons_wpml_importer_export_fields($importer) {
		if ( trx_addons_exists_wpml() && in_array('sitepress-multilingual-cms', $importer->options['required_plugins']) ) {
			$importer->show_exporter_fields(array(
				'slug'	=> 'sitepress-multilingual-cms',
				'title' => esc_html__('Sitepress Multilingual CMS (WPML) data', 'trx_addons')
				)
			);
		}
	}
}




// OCDI support
//------------------------------------------------------------------------

// Set plugin's specific importer options
if ( !function_exists( 'trx_addons_ocdi_wpml_set_options' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_ocdi_options', 'trx_addons_ocdi_wpml_set_options' );
	function trx_addons_ocdi_wpml_set_options($ocdi_options){
		$ocdi_options['import_sitepress-multilingual-cms_file_url'] = 'sitepress-multilingual-cms.txt';
		return $ocdi_options;		
	}
}

// Export plugin's data
if ( !function_exists( 'trx_addons_ocdi_wpml_export' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_ocdi_export_files', 'trx_addons_ocdi_wpml_export' );
	function trx_addons_ocdi_wpml_export($output){
		$list = array();
		if (trx_addons_exists_wpml() && in_array('sitepress-multilingual-cms', trx_addons_ocdi_options('required_plugins'))) {
			// Get plugin data from database
			$options = array('icl_sitepress_settings');
			$list = trx_addons_ocdi_export_options($options, $list);
			
			// Save as file
			$file_path = TRX_ADDONS_PLUGIN_OCDI . "export/sitepress-multilingual-cms.txt";
			trx_addons_fpc(trx_addons_get_file_dir($file_path), serialize($list));
			
			// Return file path
			$output .= '<h4><a href="'. trx_addons_get_file_url($file_path).'" download>'.esc_html__('WPML - Sitepress Multilingual CMS', 'trx_addons').'</a></h4>';
		}
		return $output;
	}
}

// Add plugin to import list
if ( !function_exists( 'trx_addons_ocdi_wpml_import_field' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_ocdi_import_fields', 'trx_addons_ocdi_wpml_import_field' );
	function trx_addons_ocdi_wpml_import_field($output){
		$list = array();
		if (trx_addons_exists_wpml() && in_array('sitepress-multilingual-cms', trx_addons_ocdi_options('required_plugins'))) {
			$output .= '<label><input type="checkbox" name="sitepress-multilingual-cms" value="sitepress-multilingual-cms">'. esc_html__( 'WPML - Sitepress Multilingual CMS', 'trx_addons' ).'</label><br/>';
		}
		return $output;
	}
}

// Import plugin's data
if ( !function_exists( 'trx_addons_ocdi_wpml_import' ) ) {
	if (is_admin()) add_action( 'trx_addons_action_ocdi_import_plugins', 'trx_addons_ocdi_wpml_import', 10, 1 );
	function trx_addons_ocdi_wpml_import( $import_plugins){
		if (trx_addons_exists_wpml() && in_array('sitepress-multilingual-cms', $import_plugins)) {
			trx_addons_ocdi_import_dump('sitepress-multilingual-cms');
			echo esc_html__('WPML - Sitepress Multilingual CMS import complete.', 'trx_addons') . "\r\n";
		}
	}
}
?>