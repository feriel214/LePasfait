<?php
/**
 * Plugin support: Revolution Slider
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.0
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	die( '-1' );
}

// Check if RevSlider installed and activated
// Attention! This function is used in many files and was moved to the api.php
/*
if ( !function_exists( 'trx_addons_exists_revslider' ) ) {
	function trx_addons_exists_revslider() {
		return function_exists('rev_slider_shortcode');
	}
}
*/

// Return Revo Sliders list, prepended inherit (if need)
if ( !function_exists( 'trx_addons_get_list_revsliders' ) ) {
	function trx_addons_get_list_revsliders($prepend_inherit=false) {
		static $list = false;
		if ($list === false) {
			$list = array();
			if (trx_addons_exists_revslider()) {
				global $wpdb;
				$rows = $wpdb->get_results( "SELECT alias, title FROM " . esc_sql($wpdb->prefix) . "revslider_sliders" );
				if (is_array($rows) && count($rows) > 0) {
					foreach ($rows as $row) {
						$list[$row->alias] = $row->title;
					}
				}
			}
		}
		return $prepend_inherit ? array_merge(array('inherit' => esc_html__("Inherit", 'trx_addons')), $list) : $list;
	}
}


// One-click import support
//------------------------------------------------------------------------

// Check plugin in the required plugins
if ( !function_exists( 'trx_addons_revslider_importer_required_plugins' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_importer_required_plugins',	'trx_addons_revslider_importer_required_plugins', 10, 2 );
	function trx_addons_revslider_importer_required_plugins($not_installed='', $list='') {
		if (strpos($list, 'revslider')!==false && !trx_addons_exists_revslider() )
			$not_installed .= '<br>' . esc_html__('Revolution Slider', 'trx_addons');
		return $not_installed;
	}
}

// Set plugin's specific importer options
if ( !function_exists( 'trx_addons_revslider_importer_set_options' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_importer_options',	'trx_addons_revslider_importer_set_options', 10, 2 );
	function trx_addons_revslider_importer_set_options($options=array()) {
		if (trx_addons_exists_revslider() && in_array('revslider', $options['required_plugins'])) {
			if (is_array($options['files']) && count($options['files']) > 0) {
				foreach ($options['files'] as $k => $v) {
					$options['files'][$k]['file_with_revslider'] = str_replace('name.ext', 'revslider.txt', $v['file_with_']);
				}
			}
		}
		return $options;
	}
}

// Add checkbox to the one-click importer
if ( !function_exists( 'trx_addons_revslider_importer_show_params' ) ) {
	if (is_admin()) add_action( 'trx_addons_action_importer_params',	'trx_addons_revslider_importer_show_params', 10, 1 );
	function trx_addons_revslider_importer_show_params($importer) {
		if (trx_addons_exists_revslider() && in_array('revslider', $importer->options['required_plugins'])) {
			$importer->show_importer_params(array(
				'slug' => 'revslider',
				'title' => esc_html__('Import Revolution Sliders', 'trx_addons'),
				'part' => 1
			));
		}
	}
}

// Clear tables
if ( !function_exists( 'trx_addons_revslider_importer_clear_tables' ) ) {
	if (is_admin()) add_action( 'trx_addons_action_importer_clear_tables',	'trx_addons_revslider_importer_clear_tables', 10, 2 );
	function trx_addons_revslider_importer_clear_tables($importer, $clear_tables) {
		if (trx_addons_exists_revslider() && in_array('revslider', $importer->options['required_plugins'])) {
			if (strpos($clear_tables, 'revslider')!==false) {
				if ($importer->options['debug']) dfl(__('Clear Revolution Slider tables', 'trx_addons'));
				trx_addons_revslider_clear_tables();
			}
		}
	}
}

// Clear tables
if ( !function_exists( 'trx_addons_revslider_clear_tables' ) ) {
	function trx_addons_revslider_clear_tables() {
		global $wpdb;
		$res = $wpdb->query("TRUNCATE TABLE " . esc_sql($wpdb->prefix) . "revslider_sliders");
		if ( is_wp_error( $res ) ) dfl( __( 'Failed truncate table "revslider_sliders".', 'trx_addons' ) . ' ' . ($res->get_error_message()) );
		$res = $wpdb->query("TRUNCATE TABLE " . esc_sql($wpdb->prefix) . "revslider_slides");
		if ( is_wp_error( $res ) ) dfl( __( 'Failed truncate table "revslider_slides".', 'trx_addons' ) . ' ' . ($res->get_error_message()) );
		$res = $wpdb->query("TRUNCATE TABLE " . esc_sql($wpdb->prefix) . "revslider_static_slides");
		if ( is_wp_error( $res ) ) dfl( __( 'Failed truncate table "revslider_static_slides".', 'trx_addons' ) . ' ' . ($res->get_error_message()) );
	}
}

// Import posts
if ( !function_exists( 'trx_addons_revslider_importer_import' ) ) {
	if (is_admin()) add_action( 'trx_addons_action_importer_import',	'trx_addons_revslider_importer_import', 10, 2 );
	function trx_addons_revslider_importer_import($importer, $action) {
		if (trx_addons_exists_revslider() && in_array('revslider', $importer->options['required_plugins'])) {
			if ( $action == 'import_revslider' && !empty($importer->options['files'][$importer->options['demo_type']]['file_with_revslider']) ) {
				if (file_exists(WP_PLUGIN_DIR . '/revslider/revslider.php')) {
					require_once WP_PLUGIN_DIR . '/revslider/revslider.php';
					if ($importer->options['debug']) dfl( __('Import Revolution sliders', 'trx_addons') );
					// Get last processed slider
					$last_arh = $importer->response['start_from_id'] = isset($_POST['start_from_id']) ? $_POST['start_from_id'] : '';
					// Get list of the sliders
					if ( ($txt = get_transient('trx_addons_importer_revsliders')) == '' ) {
						if ( ($txt = $importer->get_file($importer->options['files'][$importer->options['demo_type']]['file_with_revslider'])) === false)
							return;
						else
							set_transient('trx_addons_importer_revsliders', $txt, 10 * 60);		// Store to the cache for 10 minutes
					}
					$files = trx_addons_unserialize($txt);
					if (!is_array($files)) $files = explode("\n", str_replace("\r\n", "\n", $files));
					// Remove empty lines
					foreach ($files as $k=>$file) {
						if (trim($file)=='') unset($files[$k]);
					}
					// Process next slider
					if ( class_exists( 'RevSliderSliderImport' ) ) {
						$slider = new RevSliderSliderImport();        // after v.6.0
					} else {
						$slider = new RevSlider();                    // before v.6.0
					}
					// Process files
					$counter = 0;
					$result = 0;
					if (!is_array($_FILES)) $_FILES = array();
					foreach ($files as $file) {
						$counter++;
						if ( ($file = trim($file)) == '' )
							continue;
						if (!empty($last_arh)) {
							if ($file==$last_arh) 
								$last_arh = '';
							continue;
						}
						$need_del = false;
						// Load single file into system temp folder
						if ( ($zip = $importer->download_file($file, round(max(0, $counter-1) / count($files) * 100))) != '') {
							$need_del = substr($zip, 0, 5)=='http:' || substr($zip, 0, 6)=='https:';
							$_FILES["import_file"] = array("tmp_name" => $zip, 'error' => UPLOAD_ERR_OK);
							$response = class_exists( 'RevSliderSliderImport' ) ? $slider->import_slider() : $slider->importSliderFromPost();
							if ($need_del && file_exists($_FILES["import_file"]["tmp_name"]))
								unlink($_FILES["import_file"]["tmp_name"]);
							if ($response["success"] == false) {
								$msg = sprintf(esc_html__('Revolution Slider "%s" import error.', 'trx_addons'), $file);
								unset($importer->response['attempt']);
								$importer->response['error'] = $msg;
								if ($importer->options['debug'])  {
									dfl( $msg );
									dfo( $response );
								}
							} else {
								$importer->response['start_from_id'] = $file;
								$importer->response['result'] = min(100, round($counter / count($files) * 100));
								if ($importer->options['debug']) 
									dfl( sprintf(__('Slider "%s" imported', 'trx_addons'), basename($file)) );
							}
						}
						break;
					}
					if ($counter == count($files)) {
						delete_transient('trx_addons_importer_revsliders');
					}
				} else {
					if ($importer->options['debug']) 
						dfl( sprintf(__('Can not locate plugin Revolution Slider: %s', 'trx_addons'), WP_PLUGIN_DIR.'/revslider/revslider.php') );
				}
			}
		}
	}
}

// Display import progress
if ( !function_exists( 'trx_addons_revslider_importer_import_fields' ) ) {
	if (is_admin()) add_action( 'trx_addons_action_importer_import_fields',	'trx_addons_revslider_importer_import_fields', 10, 1 );
	function trx_addons_revslider_importer_import_fields($importer) {
		if (trx_addons_exists_revslider() && in_array('revslider', $importer->options['required_plugins'])) {
			$importer->show_importer_fields(array(
				'slug'=>'revslider', 
				'title' => esc_html__('Revolution Slider', 'trx_addons')
				)
			);
		}
	}
}

// Export posts
if ( !function_exists( 'trx_addons_revslider_importer_export' ) ) {
	add_action( 'trx_addons_action_importer_export',	'trx_addons_revslider_importer_export', 10, 1 );
	function trx_addons_revslider_importer_export($importer) {
		$list = array_keys(trx_addons_get_list_revsliders());
		$output = '';
		foreach ($list as $alias)
			$output .= ($output ? "\n" : '') . sprintf("revslider/%s.zip", $alias);
		trx_addons_fpc($importer->export_file_dir('revslider.txt'), $output);
	}
}

// Display exported data in the fields
if ( !function_exists( 'trx_addons_revslider_importer_export_fields' ) ) {
	//add_action( 'trx_addons_action_importer_export_fields',	'trx_addons_revslider_importer_export_fields', 10, 1 );
	function trx_addons_revslider_importer_export_fields($importer) {
		$importer->show_exporter_fields(array(
			'slug' => 'revslider',
			'title' => esc_html__('Revolution Sliders', 'trx_addons')
			));
	}
}




// OCDI support
//------------------------------------------------------------------------
// Set plugin's specific importer options
if ( !function_exists( 'trx_addons_ocdi_revslider_set_options' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_ocdi_options', 'trx_addons_ocdi_revslider_set_options' );
	function trx_addons_ocdi_revslider_set_options($ocdi_options){
		$ocdi_options['import_revslider_file_url'] = 'revslider.txt';
		return $ocdi_options;		
	}
}

// Add plugin to import list
if ( !function_exists( 'trx_addons_ocdi_revslider_import_field' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_ocdi_import_fields', 'trx_addons_ocdi_revslider_import_field' );
	function trx_addons_ocdi_revslider_import_field($output){
		$list = array();
		if (trx_addons_exists_revslider() && in_array('revslider', trx_addons_ocdi_options('required_plugins'))) {
			$output .= '<label><input type="checkbox" name="revslider" value="revslider">'. esc_html__( 'Revolution Sliders', 'trx_addons' ).'</label><br/>';
		}
		return $output;
	}
}

// Revolution slider import
if(!function_exists('trx_addons_ocdi_revslider_import')) {
	if (is_admin()) add_action( 'trx_addons_action_ocdi_import_plugins', 'trx_addons_ocdi_revslider_import', 10, 1);	
	function trx_addons_ocdi_revslider_import($import_plugins) {
		if (trx_addons_exists_revslider() && in_array('revslider', $import_plugins)) {			
			if (file_exists(WP_PLUGIN_DIR . '/revslider/revslider.php')) {
				require_once WP_PLUGIN_DIR . '/revslider/revslider.php';
						
				// Delete all data from tables
				trx_addons_revslider_clear_tables();

				// Get revslider file
				$files = file(trx_addons_ocdi_options('import_revslider_file_url'));
				
				if ($files) {
					$demo_url = trx_addons_ocdi_options('demo_url');
					
					if (!is_array($_FILES)) $_FILES = array();
					
					// Process next slider
					$slider = new RevSlider();
					
					// Read file by lines
					foreach ($files as $filename) {				
						$filename = str_replace(array("\r\n", "\n", "\r"), '', $filename);			
						$demo_zip = trailingslashit($demo_url) . $filename;
						
						// Download remote file
						if (substr($demo_zip, 0, 5)=='http:' || substr($demo_zip, 0, 6)=='https:') {							
							$upload_dir = (object) wp_upload_dir();
							$revslider_dir = $upload_dir->basedir . '/revslider/';
							$local_zip = $revslider_dir . basename($filename);
							
							// Delete zip if it already exists
							if (file_exists($local_zip)) {
								unlink($local_zip);
							} 
							
							// Upload zip to uploads/revslider folder
							trx_addons_fpc( $local_zip, trx_addons_fgc($demo_zip));
							
							// Import zip to slider
							$response = $slider->importSliderFromPost(true, true, $local_zip);  
							if ($response["success"] == false) {
								dfl(__('Revolution Slider import error.', 'trx_addons'));
							}

							// Delete zip if exists
							if (file_exists($local_zip)) {
								unlink($local_zip);
							} 
						}
					}
				}		
			}
		}
	}
}

// Allow loading RevSlider scripts and styles
// if it present in the content of the current page
if (!function_exists('trx_addons_check_revslider_in_content')) {
	add_filter( 'revslider_include_libraries', 'trx_addons_check_revslider_in_content' );
	function trx_addons_check_revslider_in_content($load, $post_id=-1) {
		if ( ! $load ) {
			// Load if current page is builder preview
			$load = ( function_exists('trx_addons_elm_is_preview') && trx_addons_elm_is_preview() )
			        ||
			        ( function_exists('trx_addons_gutenberg_is_preview') && trx_addons_gutenberg_is_preview() );
			if ( ! $load ) {
				$content = $post_id > 0 ? get_the_content(null, false, $post_id) : get_the_content();
				// Check shortcode
				$load = is_string($content)
				        && strpos($content, '[rev_slider') !== false;                 // Shortcode rev_slider
				// Check in Gutenberg
				if ( trx_addons_exists_gutenberg() ) {
					$load = is_string($content)
					        && strpos($content, 'wp:trx-addons/slider') !== false     // Our widget slider with param "engine" == "revo"
					        && strpos($content, '"engine":"revo"') !== false;
				}
				// Check in VC
				if ( ! $load && trx_addons_exists_vc() ) {
					$load = is_string($content)
					        && strpos($content, '[trx_widget_slider') !== false     // Our widget slider with param "engine" == "revo"
					        && strpos($content, 'engine="revo"') !== false;
				}
				// Check in Elementor
				if ( ! $load && trx_addons_exists_elementor() ) {
					if ( $post_id < 0 ) $post_id = trx_addons_get_the_ID();
					$cur_page_built_with_elementor = \Elementor\Plugin::instance()->db->is_built_with_elementor( $post_id );
					if ( $cur_page_built_with_elementor ) {
						$meta = get_post_meta( $post_id, "_elementor_data", true );
						$load = is_string($meta)
						        && (
							        ( strpos($meta, '"widgetType":"trx_widget_slider"') !== false             // Our widget slider with param "engine" == "revo"
							          && strpos($meta, '"engine":"revo"') !== false
							        )
							        ||
							        strpos($meta, '"widgetType":"wp-widget-rev-slider-widget"') !== false     // WordPress widget "RevSlider"
						        );
					}
				}
			}
		}
		return $load;
	}
}
?>