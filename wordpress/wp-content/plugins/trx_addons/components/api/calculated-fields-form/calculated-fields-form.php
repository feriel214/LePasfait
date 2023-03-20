<?php
/**
 * Plugin support: Calculated Fields Form
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.5
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	die( '-1' );
}

// Check if plugin is installed and activated
if ( !function_exists( 'trx_addons_exists_calculated_fields_form' ) ) {
	function trx_addons_exists_calculated_fields_form() {
		return defined( 'CP_CALCULATEDFIELDSF_VERSION' );
	}
}

// Return forms list, prepended inherit (if need)
if ( !function_exists( 'trx_addons_get_list_calculated_fields_form' ) ) {
	function trx_addons_get_list_calculated_fields_form($prepend_inherit=false) {
		static $list = false;
		if ($list === false) {
			$list = array();
			if (trx_addons_exists_calculated_fields_form()) {
				global $wpdb;
				$rows = $wpdb->get_results( 'SELECT id, form_name FROM ' . esc_sql($wpdb->prefix . CP_CALCULATEDFIELDSF_FORMS_TABLE) );
				if (is_array($rows) && count($rows) > 0) {
					foreach ($rows as $row) {
						$list[$row->id] = $row->form_name;
					}
				}
			}
		}
		return $prepend_inherit ? trx_addons_array_merge(array('inherit' => esc_html__("Inherit", 'trx_addons')), $list) : $list;
	}
}



// VC support
//------------------------------------------------------------------------

// Add [cff] in the VC shortcodes list
if (!function_exists('trx_addons_sc_calculated_fields_form_add_in_vc')) {
	function trx_addons_sc_calculated_fields_form_add_in_vc() {

		if (!trx_addons_exists_vc() || !trx_addons_exists_calculated_fields_form()) return;

		vc_lean_map( "CP_CALCULATED_FIELDS", 'trx_addons_sc_calculated_fields_form_add_in_vc_params');
		class WPBakeryShortCode_Cp_Calculated_Fields extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_calculated_fields_form_add_in_vc', 20);
}

// Return params for [cff]
if (!function_exists('trx_addons_sc_calculated_fields_form_add_in_vc_params')) {
	function trx_addons_sc_calculated_fields_form_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "CP_CALCULATED_FIELDS",
				"name" => esc_html__("Calculated fields form", "trx_addons"),
				"description" => esc_html__("Insert Calculated Fields Form", "trx_addons"),
				"category" => esc_html__('Content', 'trx_addons'),
				'icon' => 'icon_trx_sc_calcfields',
				"class" => "trx_sc_single trx_sc_calcfields",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "id",
						"heading" => esc_html__("Form ID", "trx_addons"),
						"description" => esc_html__("Select Form to insert to the current page", "trx_addons"),
						"admin_label" => true,
				        'save_always' => true,
						"value" => array_flip(trx_addons_get_list_calculated_fields_form()),
						"type" => "dropdown"
					)
				)
			), 'CP_CALCULATED_FIELDS');
			
	}
}




// Elementor Widget
//------------------------------------------------------
if (!function_exists('trx_addons_sc_calculated_fields_form_add_in_elementor')) {
	add_action( 'elementor/widgets/widgets_registered', 'trx_addons_sc_calculated_fields_form_add_in_elementor' );
	function trx_addons_sc_calculated_fields_form_add_in_elementor() {

		if (!trx_addons_exists_calculated_fields_form() || !class_exists('TRX_Addons_Elementor_Widget')) return;
		
		class TRX_Addons_Elementor_Widget_Calculated_Fields_Form extends TRX_Addons_Elementor_Widget {

			/**
			 * Retrieve widget name.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget name.
			 */
			public function get_name() {
				return 'trx_sc_calculated_fields_form';
			}

			/**
			 * Retrieve widget title.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget title.
			 */
			public function get_title() {
				return __( 'Calculated Fields Form', 'trx_addons' );
			}

			/**
			 * Retrieve widget icon.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget icon.
			 */
			public function get_icon() {
				return 'eicon-form-horizontal';
			}

			/**
			 * Retrieve the list of categories the widget belongs to.
			 *
			 * Used to determine where to display the widget in the editor.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return array Widget categories.
			 */
			public function get_categories() {
				return ['trx_addons-support'];
			}

			/**
			 * Register widget controls.
			 *
			 * Adds different input fields to allow the user to change and customize the widget settings.
			 *
			 * @since 1.6.41
			 * @access protected
			 */
			protected function register_controls() {
				$this->start_controls_section(
					'section_sc_calculated_fields_form',
					[
						'label' => __( 'Calculated Fields Form', 'trx_addons' ),
					]
				);

				$forms = trx_addons_get_list_calculated_fields_form();
				$id = (int) trx_addons_array_get_first($forms);

				$this->add_control(
					'form_id',
					[
						'label' => __( 'Form ID', 'trx_addons' ),
						'label_block' => false,
						'type' => \Elementor\Controls_Manager::SELECT,
						'options' => $forms,
						'default' => ''.$id 	// Convert to string
					]
				);
				
				$this->end_controls_section();
			}

			// Return widget's layout
			public function render() {
				if (shortcode_exists('CP_CALCULATED_FIELDS')) {
					$atts = $this->sc_prepare_atts($this->get_settings(), $this->get_sc_name());
					trx_addons_show_layout(do_shortcode(sprintf('[CP_CALCULATED_FIELDS id="%s"]', $atts['form_id'])));
				} else
					$this->shortcode_not_exists('CP_CALCULATED_FIELDS', 'Calculated Fields Form');
			}
		}
		
		// Register widget
		\Elementor\Plugin::$instance->widgets_manager->register_widget_type( new TRX_Addons_Elementor_Widget_Calculated_Fields_Form() );
	}
}


// One-click import support
//------------------------------------------------------------------------

// Check plugin in the required plugins
if ( !function_exists( 'trx_addons_calculated_fields_form_importer_required_plugins' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_importer_required_plugins',	'trx_addons_calculated_fields_form_importer_required_plugins', 10, 2 );
	function trx_addons_calculated_fields_form_importer_required_plugins($not_installed='', $list='') {
		if (strpos($list, 'calculated-fields-form')!==false && !trx_addons_exists_calculated_fields_form() )
			$not_installed .= '<br>' . esc_html__('Calculated Fields Form', 'trx_addons');
		return $not_installed;
	}
}

// Set plugin's specific importer options
if ( !function_exists( 'trx_addons_calculated_fields_form_importer_set_options' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_importer_options', 'trx_addons_calculated_fields_form_importer_set_options', 10, 1 );
	function trx_addons_calculated_fields_form_importer_set_options($options=array()) {
		if ( trx_addons_exists_calculated_fields_form() && in_array('calculated-fields-form', $options['required_plugins']) ) {
			$options['additional_options'][] = 'CP_CFF_LOAD_SCRIPTS';				// Add slugs to export options of this plugin
			$options['additional_options'][] = 'CP_CALCULATEDFIELDSF_USE_CACHE';
			$options['additional_options'][] = 'CP_CALCULATEDFIELDSF_EXCLUDE_CRAWLERS';
			if (is_array($options['files']) && count($options['files']) > 0) {
				foreach ($options['files'] as $k => $v) {
					$options['files'][$k]['file_with_calculated-fields-form'] = str_replace('name.ext', 'calculated-fields-form.txt', $v['file_with_']);
				}
			}
		}
		return $options;
	}
}

// Prevent import plugin's specific options if plugin is not installed
if ( !function_exists( 'trx_addons_calculated_fields_form_importer_check_options' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_import_theme_options', 'trx_addons_calculated_fields_form_importer_check_options', 10, 4 );
	function trx_addons_calculated_fields_form_importer_check_options($allow, $k, $v, $options) {
		if ($allow && in_array($k, array('CP_CFF_LOAD_SCRIPTS', 'CP_CALCULATEDFIELDSF_USE_CACHE', 'CP_CALCULATEDFIELDSF_EXCLUDE_CRAWLERS'))) {
			$allow = trx_addons_exists_calculated_fields_form() && in_array('calculated-fields-form', $options['required_plugins']);
		}
		return $allow;
	}
}

// Add checkbox to the one-click importer
if ( !function_exists( 'trx_addons_calculated_fields_form_importer_show_params' ) ) {
	if (is_admin()) add_action( 'trx_addons_action_importer_params',	'trx_addons_calculated_fields_form_importer_show_params', 10, 1 );
	function trx_addons_calculated_fields_form_importer_show_params($importer) {
		if ( trx_addons_exists_calculated_fields_form() && in_array('calculated-fields-form', $importer->options['required_plugins']) ) {
			$importer->show_importer_params(array(
				'slug' => 'calculated-fields-form',
				'title' => esc_html__('Import Calculated Fields Form', 'trx_addons'),
				'part' => 1
			));
		}
	}
}

// Import posts
if ( !function_exists( 'trx_addons_calculated_fields_form_importer_import' ) ) {
	if (is_admin()) add_action( 'trx_addons_action_importer_import',	'trx_addons_calculated_fields_form_importer_import', 10, 2 );
	function trx_addons_calculated_fields_form_importer_import($importer, $action) {
		if ( trx_addons_exists_calculated_fields_form() && in_array('calculated-fields-form', $importer->options['required_plugins']) ) {
			if ( $action == 'import_calculated-fields-form' ) {
				$importer->response['start_from_id'] = 0;
				$importer->import_dump('calculated-fields-form', esc_html__('Calculated Fields Form', 'trx_addons'));
			}
		}
	}
}

// Display import progress
if ( !function_exists( 'trx_addons_calculated_fields_form_importer_import_fields' ) ) {
	if (is_admin()) add_action( 'trx_addons_action_importer_import_fields',	'trx_addons_calculated_fields_form_importer_import_fields', 10, 1 );
	function trx_addons_calculated_fields_form_importer_import_fields($importer) {
		if ( trx_addons_exists_calculated_fields_form() && in_array('calculated-fields-form', $importer->options['required_plugins']) ) {
			$importer->show_importer_fields(array(
				'slug'	=> 'calculated-fields-form', 
				'title'	=> esc_html__('Calculated Fields Form', 'trx_addons')
				)
			);
		}
	}
}

// Export posts
if ( !function_exists( 'trx_addons_calculated_fields_form_importer_export' ) ) {
	if (is_admin()) add_action( 'trx_addons_action_importer_export',	'trx_addons_calculated_fields_form_importer_export', 10, 1 );
	function trx_addons_calculated_fields_form_importer_export($importer) {
		if ( trx_addons_exists_calculated_fields_form() && in_array('calculated-fields-form', $importer->options['required_plugins']) ) {
			trx_addons_fpc($importer->export_file_dir('calculated-fields-form.txt'), serialize( array(
				CP_CALCULATEDFIELDSF_FORMS_TABLE => $importer->export_dump(CP_CALCULATEDFIELDSF_FORMS_TABLE)
				) )
			);
		}
	}
}

// Display exported data in the fields
if ( !function_exists( 'trx_addons_calculated_fields_form_importer_export_fields' ) ) {
	if (is_admin()) add_action( 'trx_addons_action_importer_export_fields',	'trx_addons_calculated_fields_form_importer_export_fields', 10, 1 );
	function trx_addons_calculated_fields_form_importer_export_fields($importer) {
		if ( trx_addons_exists_calculated_fields_form() && in_array('calculated-fields-form', $importer->options['required_plugins']) ) {
			$importer->show_exporter_fields(array(
				'slug'	=> 'calculated-fields-form',
				'title' => esc_html__('Calculated Fields Form', 'trx_addons')
				)
			);
		}
	}
}






// OCDI support
//------------------------------------------------------------------------
// Set plugin's specific importer options
if ( !function_exists( 'trx_addons_ocdi_calculated_fields_form_set_options' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_ocdi_options', 'trx_addons_ocdi_calculated_fields_form_set_options' );
	function trx_addons_ocdi_calculated_fields_form_set_options($ocdi_options){
		$ocdi_options['import_calculated_fields_form_file_url'] = 'calculated_fields_form.txt';
		return $ocdi_options;		
	}
}

// Export Calculated Fields Form
if ( !function_exists( 'trx_addons_ocdi_calculated_fields_form_export' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_ocdi_export_files', 'trx_addons_ocdi_calculated_fields_form_export' );
	function trx_addons_ocdi_calculated_fields_form_export($output){
		$list = array();
		if (trx_addons_exists_calculated_fields_form() && in_array('calculated-fields-form', trx_addons_ocdi_options('required_plugins'))) {
			// Get plugin data from database			
			$tables = array('cp_calculated_fields_form_settings');
			$list = trx_addons_ocdi_export_tables($tables, $list);

			$options = array('CP_CFF_LOAD_SCRIPTS', 'CP_CALCULATEDFIELDSF_USE_CACHE', 'CP_CALCULATEDFIELDSF_EXCLUDE_CRAWLERS');
			$list = trx_addons_ocdi_export_options($options, $list);
			
			// Save as file
			$file_path = TRX_ADDONS_PLUGIN_OCDI . "export/calculated_fields_form.txt";
			trx_addons_fpc(trx_addons_get_file_dir($file_path), serialize($list));
			
			// Return file path
			$output .= '<h4><a href="'. trx_addons_get_file_url($file_path).'" download>'.esc_html__('Calculated Fields Form', 'trx_addons').'</a></h4>';
		}
		return $output;
	}
}

// Add plugin to import list
if ( !function_exists( 'trx_addons_ocdi_calculated_fields_form_import_field' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_ocdi_import_fields', 'trx_addons_ocdi_calculated_fields_form_import_field' );
	function trx_addons_ocdi_calculated_fields_form_import_field($output){
		$list = array();
		if (trx_addons_exists_calculated_fields_form() && in_array('calculated-fields-form', trx_addons_ocdi_options('required_plugins'))) {
			$output .= '<label><input type="checkbox" name="calculated_fields_form" value="calculated_fields_form">'. esc_html__( 'Calculated Fields Form', 'trx_addons' ).'</label><br/>';
		}
		return $output;
	}
}

// Import Calculated Fields Form
if ( !function_exists( 'trx_addons_ocdi_calculated_fields_form_import' ) ) {
	if (is_admin()) add_action( 'trx_addons_action_ocdi_import_plugins', 'trx_addons_ocdi_calculated_fields_form_import', 10, 1 );
	function trx_addons_ocdi_calculated_fields_form_import($import_plugins){
		if (trx_addons_exists_calculated_fields_form() && in_array('calculated_fields_form', $import_plugins)) {
			trx_addons_ocdi_import_dump('calculated_fields_form');
			echo esc_html__('Calculated Fields Form import complete.', 'trx_addons') . "\r\n";
		}
	}
}
?>