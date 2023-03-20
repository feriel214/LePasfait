<?php
/**
 * Plugin support: Contact Form 7
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.5
 */


// Check if Contact Form 7 installed and activated
if ( !function_exists( 'trx_addons_exists_cf7' ) ) {
	function trx_addons_exists_cf7() {
		return class_exists('WPCF7') && class_exists('WPCF7_ContactForm');
	}
}

// Return forms list, prepended inherit (if need)
if ( !function_exists( 'trx_addons_get_list_cf7' ) ) {
	function trx_addons_get_list_cf7($prepend_inherit=false) {
		static $list = false;
		if ($list === false) {
			$list = array();
			if (trx_addons_exists_cf7()) {
				// Attention! Using WP_Query is damage 'post_type' in the main query
				global $wpdb;
				$rows = $wpdb->get_results( 'SELECT id, post_title'
												. ' FROM ' . esc_sql($wpdb->prefix . 'posts') 
												. ' WHERE post_type="' . esc_sql(WPCF7_ContactForm::post_type) . '"'
														. ' AND post_status' . (current_user_can('read_private_pages') && current_user_can('read_private_posts') ? ' IN ("publish", "private")' : '="publish"')
														. ' AND post_password=""'
												. ' ORDER BY post_title' );
				if (is_array($rows) && count($rows) > 0) {
					foreach ($rows as $row) {
						$list[$row->id] = $row->post_title;
					}
				}
			}
		}
		return $prepend_inherit ? trx_addons_array_merge(array('inherit' => esc_html__("Inherit", 'trx_addons')), $list) : $list;
	}
}



// Filter 'wpcf7_mail_components' before Contact Form 7 send mail
// to replace recipient for 'Cars' and 'Properties'
// Also customer can use the '{{ title }}' in the 'Subject' and 'Message'
// to replace it with the post title when send a mail
if ( !function_exists( 'trx_addons_cpt_properties_wpcf7_mail_components' ) ) {
	add_filter('wpcf7_mail_components',	'trx_addons_cpt_properties_wpcf7_mail_components', 10, 3);
	function trx_addons_cpt_properties_wpcf7_mail_components($components, $form, $mail_obj=null) {
		if (is_object($form) && method_exists($form, 'id') && (int)$form->id() > 0 ) {
			$data = get_transient(sprintf('trx_addons_cf7_%d_data', (int) $form->id()));
			if (!empty($data['agent'])) {
				$agent_id = (int) $data['agent'];
				$agent_email = '';
				if ($agent_id > 0) {			// Agent
					$meta = get_post_meta($agent_id, 'trx_addons_options', true);
					$agent_email = $meta['email'];
				} else if ($agent_id < 0) {		// Author
					$user_id = abs($agent_id);
					$user_data = get_userdata($user_id);
					$agent_email = $user_data->user_email;
				}
				if (!empty($agent_email)) $components['recipient'] = $agent_email;
			}
			if (!empty($data['item']) && (int) $data['item'] > 0) {
				$post = get_post($data['item']);
				foreach(array('subject', 'body') as $k) {
					$components[$k] = str_replace(
													array(
														'{{ title }}'
													),
													array(
														$post->post_title
													),
													$components[$k]
												);
				}
			}
		}
		return $components;
	}
}


// One-click import support
//------------------------------------------------------------------------

// Check plugin in the required plugins
if ( !function_exists( 'trx_addons_cf7_importer_required_plugins' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_importer_required_plugins',	'trx_addons_cf7_importer_required_plugins', 10, 2 );
	function trx_addons_cf7_importer_required_plugins($not_installed='', $list='') {
		if (strpos($list, 'contact-form-7')!==false && !trx_addons_exists_cf7() )
			$not_installed .= '<br>' . esc_html__('Contact Form 7', 'trx_addons');
		return $not_installed;
	}
}

// Set plugin's specific importer options
if ( !function_exists( 'trx_addons_cf7_importer_set_options' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_importer_options',	'trx_addons_cf7_importer_set_options' );
	function trx_addons_cf7_importer_set_options($options=array()) {
		if ( trx_addons_exists_cf7() && in_array('contact-form-7', $options['required_plugins']) ) {
			$options['additional_options'][] = 'wpcf7';
		}
		return $options;
	}
}

// Prevent import plugin's specific options if plugin is not installed
if ( !function_exists( 'trx_addons_cf7_importer_check_options' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_import_theme_options', 'trx_addons_cf7_importer_check_options', 10, 4 );
	function trx_addons_cf7_importer_check_options($allow, $k, $v, $options) {
		if ($allow && $k == 'wpcf7') {
			$allow = trx_addons_exists_cf7() && in_array('contact-form-7', $options['required_plugins']);
		}
		return $allow;
	}
}

// Add checkbox to the one-click importer
if ( !function_exists( 'trx_addons_cf7_importer_show_params' ) ) {
	if (is_admin()) add_action( 'trx_addons_action_importer_params',	'trx_addons_cf7_importer_show_params', 10, 1 );
	function trx_addons_cf7_importer_show_params($importer) {
		if ( trx_addons_exists_cf7() && in_array('contact-form-7', $importer->options['required_plugins']) ) {
			$importer->show_importer_params(array(
				'slug' => 'contact-form-7',
				'title' => esc_html__('Import Contact Form 7', 'trx_addons'),
				'part' => 1
			));
		}
	}
}

// Check if the row will be imported
if ( !function_exists( 'trx_addons_cf7_importer_check_row' ) ) {
	if (is_admin()) add_filter('trx_addons_filter_importer_import_row', 'trx_addons_cf7_importer_check_row', 9, 4);
	function trx_addons_cf7_importer_check_row($flag, $table, $row, $list) {
		if ($flag || strpos($list, 'contact-form-7')===false) return $flag;
		if ( trx_addons_exists_cf7() ) {
			if ($table == 'posts')
				$flag = $row['post_type']==WPCF7_ContactForm::post_type;
		}
		return $flag;
	}
}




// OCDI support
//------------------------------------------------------------------------
// Set plugin's specific importer options
if ( !function_exists( 'trx_addons_ocdi_cf7_set_options' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_ocdi_options', 'trx_addons_ocdi_cf7_set_options' );
	function trx_addons_ocdi_cf7_set_options($ocdi_options){
		$ocdi_options['import_cf7_file_url'] = 'contact-form-7.txt';
		return $ocdi_options;		
	}
}

// Export Contact Form 7
if ( !function_exists( 'trx_addons_ocdi_cf7_export' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_ocdi_export_files', 'trx_addons_ocdi_cf7_export' );
	function trx_addons_ocdi_cf7_export($output){
		$list = array();
		if (trx_addons_exists_cf7() && in_array('contact-form-7', trx_addons_ocdi_options('required_plugins'))) {
			// Get plugin data from database
			$options = array('wpcf7');
			$list = trx_addons_ocdi_export_options($options, $list);
			
			// Save as file
			$file_path = TRX_ADDONS_PLUGIN_OCDI . "export/contact-form-7.txt";
			trx_addons_fpc(trx_addons_get_file_dir($file_path), serialize($list));
			
			// Return file path
			$output .= '<h4><a href="'. trx_addons_get_file_url($file_path).'" download>'.esc_html__('Contact Form 7', 'trx_addons').'</a></h4>';
		}
		return $output;
	}
}

// Add plugin to import list
if ( !function_exists( 'trx_addons_ocdi_cf7_import_field' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_ocdi_import_fields', 'trx_addons_ocdi_cf7_import_field' );
	function trx_addons_ocdi_cf7_import_field($output){
		$list = array();
		if (trx_addons_exists_cf7() && in_array('contact-form-7', trx_addons_ocdi_options('required_plugins'))) {
			$output .= '<label><input type="checkbox" name="contact-form-7" value="contact-form-7">'. esc_html__( 'Contact Form 7', 'trx_addons' ).'</label><br/>';
		}
		return $output;
	}
}

// Import Contact Form 7
if ( !function_exists( 'trx_addons_ocdi_cf7_import' ) ) {
	if (is_admin()) add_action( 'trx_addons_action_ocdi_import_plugins', 'trx_addons_ocdi_cf7_import', 10, 1 );
	function trx_addons_ocdi_cf7_import( $import_plugins){
		if (trx_addons_exists_cf7() && in_array('contact-form-7', $import_plugins)) {
			trx_addons_ocdi_import_dump('cf7');
			echo esc_html__('Contact Form 7 import complete.', 'trx_addons') . "\r\n";
		}
	}
}
?>