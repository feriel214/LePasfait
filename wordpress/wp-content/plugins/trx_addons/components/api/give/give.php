<?php
/**
 * Plugin support: Give
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.50
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	die( '-1' );
}

if ( ! defined( 'TRX_ADDONS_GIVE_FORMS_PT_FORMS' ) )			define( 'TRX_ADDONS_GIVE_FORMS_PT_FORMS', 'give_forms' );
if ( ! defined( 'TRX_ADDONS_GIVE_FORMS_PT_PAYMENT' ) )			define( 'TRX_ADDONS_GIVE_FORMS_PT_PAYMENT', 'give_payment' );
if ( ! defined( 'TRX_ADDONS_GIVE_FORMS_TAXONOMY_CATEGORY' ) )	define( 'TRX_ADDONS_GIVE_FORMS_TAXONOMY_CATEGORY', 'give_forms_category' );
if ( ! defined( 'TRX_ADDONS_GIVE_FORMS_TAXONOMY_TAG' ) )		define( 'TRX_ADDONS_GIVE_FORMS_TAXONOMY_TAG', 'give_forms_tag' );


// Check if plugin is installed and activated
if ( !function_exists( 'trx_addons_exists_give' ) ) {
	function trx_addons_exists_give() {
		return class_exists( 'Give' );
	}
}

// Return true, if current page is Give plugin's page
if ( !function_exists( 'trx_addons_is_give_page' ) ) {
	function trx_addons_is_give_page() {
		$rez = false;
		if (trx_addons_exists_give()) {
			$rez = (is_single() && in_array(get_query_var('post_type'), array(TRX_ADDONS_GIVE_FORMS_PT_FORMS, TRX_ADDONS_GIVE_FORMS_PT_PAYMENT))) 
					|| is_post_type_archive(TRX_ADDONS_GIVE_FORMS_PT_FORMS) 
					|| is_post_type_archive(TRX_ADDONS_GIVE_FORMS_PT_PAYMENT) 
					|| is_tax(TRX_ADDONS_GIVE_FORMS_TAXONOMY_CATEGORY)
					|| is_tax(TRX_ADDONS_GIVE_FORMS_TAXONOMY_TAG);
		}
		return $rez;
	}
}

// Return forms list, prepended inherit (if need)
if ( !function_exists( 'trx_addons_get_list_give_forms' ) ) {
	function trx_addons_get_list_give_forms($prepend_inherit=false) {
		static $list = false;
		if ($list === false) {
			$list = array();
			if (trx_addons_exists_give()) {
				$list = trx_addons_get_list_posts(false, array(
														'post_type' => TRX_ADDONS_GIVE_FORMS_PT_FORMS,
														'not_selected' => false
														));
			}
		}
		return $prepend_inherit ? trx_addons_array_merge(array('inherit' => esc_html__("Inherit", 'trx_addons')), $list) : $list;
	}
}



// Load required scripts and styles
//------------------------------------------------------------------------

// Merge specific styles into single stylesheet
if ( !function_exists( 'trx_addons_give_merge_styles' ) ) {
	add_filter("trx_addons_filter_merge_styles", 'trx_addons_give_merge_styles');
	function trx_addons_give_merge_styles($list) {
		if (trx_addons_exists_give())
			$list[] = TRX_ADDONS_PLUGIN_API . 'give/_give.scss';
		return $list;
	}
}



// Support utils
//------------------------------------------------------------------------

// Plugin init
if ( !function_exists( 'trx_addons_give_init' ) ) {
	add_action("init", 'trx_addons_give_init');
	function trx_addons_give_init() {
		if (trx_addons_exists_give()) {
			remove_action( 'give_single_form_summary', 'give_template_single_title', 5 );
		}
	}
}

// Replace single title with h2 instead h1
if ( !function_exists( 'trx_addons_give_single_title' ) ) {
	add_action("give_single_form_summary", 'trx_addons_give_single_title', 5);
	function trx_addons_give_single_title() {
		?><h2 itemprop="name" class="give-form-title entry-title"><?php the_title(); ?></h2><?php
	}
}


// VC support
//------------------------------------------------------------------------

// Add [trx_addons_give_form] in the VC shortcodes list
if (!function_exists('trx_addons_sc_give_add_in_vc')) {
	function trx_addons_sc_give_add_in_vc() {

		if (!trx_addons_exists_vc() || !trx_addons_exists_give()) return;

		vc_lean_map( "give_form", 'trx_addons_sc_give_add_in_vc_params');
		class WPBakeryShortCode_Trx_Addons_Give_Form extends WPBakeryShortCode {}
	}
	add_action('init', 'trx_addons_sc_give_add_in_vc', 20);
}

// Return params for [trx_addons_give_form]
if (!function_exists('trx_addons_sc_give_add_in_vc_params')) {
	function trx_addons_sc_give_add_in_vc_params() {
		return apply_filters('trx_addons_sc_map', array(
				"base" => "give_form",
				"name" => esc_html__("Give donation form", "trx_addons"),
				"description" => esc_html__("Insert Give Donation form", "trx_addons"),
				"category" => esc_html__('Content', 'trx_addons'),
				'icon' => 'icon_trx_sc_give_forms',
				"class" => "trx_sc_single trx_sc_give_forms",
				"content_element" => true,
				"is_container" => false,
				"show_settings_on_create" => true,
				"params" => array(
					array(
						"param_name" => "id",
						"heading" => esc_html__("Donation form", "trx_addons"),
						"description" => esc_html__("Select Form to insert to the current page", "trx_addons"),
						"admin_label" => true,
				        'save_always' => true,
						"value" => array_flip(trx_addons_get_list_give_forms()),
						"type" => "dropdown"
					)
				)
			), 'trx_addons_give_form');
			
	}
}




// Elementor Widget
//------------------------------------------------------
if (!function_exists('trx_addons_sc_give_add_in_elementor')) {
	add_action( 'elementor/widgets/widgets_registered', 'trx_addons_sc_give_add_in_elementor' );
	function trx_addons_sc_give_add_in_elementor() {

		if (!trx_addons_exists_give() || !class_exists('TRX_Addons_Elementor_Widget')) return;
		
		class TRX_Addons_Elementor_Widget_Give_Form extends TRX_Addons_Elementor_Widget {

			/**
			 * Retrieve widget name.
			 *
			 * @since 1.6.41
			 * @access public
			 *
			 * @return string Widget name.
			 */
			public function get_name() {
				return 'trx_sc_give';
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
				return __( 'Give Donation Form', 'trx_addons' );
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
					'section_sc_give',
					[
						'label' => __( 'Give Donation Form', 'trx_addons' ),
					]
				);

				$forms = trx_addons_get_list_give_forms();
				$id = (int) trx_addons_array_get_first($forms);

				$this->add_control(
					'form_id',
					[
						'label' => __( 'Donation Form', 'trx_addons' ),
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
				if (shortcode_exists('give_form')) {
					$atts = $this->sc_prepare_atts($this->get_settings(), $this->get_sc_name());
					trx_addons_show_layout(do_shortcode(sprintf('[give_form id="%s"]', $atts['form_id'])));
				} else
					$this->shortcode_not_exists('give_form', 'Give');
			}
		}
		
		// Register widget
		\Elementor\Plugin::$instance->widgets_manager->register_widget_type( new TRX_Addons_Elementor_Widget_Give_Form() );
	}
}


// One-click import support
//------------------------------------------------------------------------

// Check plugin in the required plugins
if ( !function_exists( 'trx_addons_give_importer_required_plugins' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_importer_required_plugins',	'trx_addons_give_importer_required_plugins', 10, 2 );
	function trx_addons_give_importer_required_plugins($not_installed='', $list='') {
		if (strpos($list, 'give')!==false && !trx_addons_exists_give() )
			$not_installed .= '<br>' . esc_html__('Give (Donation Form)', 'trx_addons');
		return $not_installed;
	}
}

// Set plugin's specific importer options
if ( !function_exists( 'trx_addons_give_importer_set_options' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_importer_options', 'trx_addons_give_importer_set_options', 10, 1 );
	function trx_addons_give_importer_set_options($options=array()) {
		if ( trx_addons_exists_give() && in_array('give', $options['required_plugins']) ) {
			$options['additional_options'][] = 'give_settings';
			if (is_array($options['files']) && count($options['files']) > 0) {
				foreach ($options['files'] as $k => $v) {
					$options['files'][$k]['file_with_give'] = str_replace('name.ext', 'give.txt', $v['file_with_']);
				}
			}
		}
		return $options;
	}
}

// Prevent import plugin's specific options if plugin is not installed
if ( !function_exists( 'trx_addons_give_importer_check_options' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_import_theme_options', 'trx_addons_give_importer_check_options', 10, 4 );
	function trx_addons_give_importer_check_options($allow, $k, $v, $options) {
		if ($allow && $k == 'give_settings') {
			$allow = trx_addons_exists_give() && in_array('give', $options['required_plugins']);
		}
		return $allow;
	}
}

// Add checkbox to the one-click importer
if ( !function_exists( 'trx_addons_give_importer_show_params' ) ) {
	if (is_admin()) add_action( 'trx_addons_action_importer_params',	'trx_addons_give_importer_show_params', 10, 1 );
	function trx_addons_give_importer_show_params($importer) {
		if ( trx_addons_exists_give() && in_array('give', $importer->options['required_plugins']) ) {
			$importer->show_importer_params(array(
				'slug' => 'give',
				'title' => esc_html__('Import Give (Donation Form)', 'trx_addons'),
				'part' => 1
			));
		}
	}
}

// Import posts
if ( !function_exists( 'trx_addons_give_importer_import' ) ) {
	if (is_admin()) add_action( 'trx_addons_action_importer_import',	'trx_addons_give_importer_import', 10, 2 );
	function trx_addons_give_importer_import($importer, $action) {
		if ( trx_addons_exists_give() && in_array('give', $importer->options['required_plugins']) ) {
			if ( $action == 'import_give' ) {
				$importer->response['start_from_id'] = 0;
				$importer->import_dump('give_formmeta', esc_html__('Give - Form meta', 'trx_addons'));
				$importer->import_dump('give_donors', esc_html__('Give - Donors list', 'trx_addons'));
				$importer->import_dump('give_donormeta', esc_html__('Give - Donor meta', 'trx_addons'));
				$importer->import_dump('give_logs', esc_html__('Give - Logs', 'trx_addons'));
				$importer->import_dump('give_logmeta', esc_html__('Give - Log meta', 'trx_addons'));
				$importer->import_dump('give_paymentmeta', esc_html__('Give - Payment meta', 'trx_addons'));
				$importer->import_dump('give_sequental_ordering', esc_html__('Give - Sequental ordering', 'trx_addons'));
			}
		}
	}
}

// Check if the row will be imported
if ( !function_exists( 'trx_addons_give_importer_check_row' ) ) {
	if (is_admin()) add_filter('trx_addons_filter_importer_import_row', 'trx_addons_give_importer_check_row', 9, 4);
	function trx_addons_give_importer_check_row($flag, $table, $row, $list) {
		if ($flag || strpos($list, 'give')===false) return $flag;
		if ( trx_addons_exists_give() ) {
			if ($table == 'posts')
				$flag = in_array($row['post_type'], array(TRX_ADDONS_GIVE_FORMS_PT_FORMS, TRX_ADDONS_GIVE_FORMS_PT_PAYMENT));
		}
		return $flag;
	}
}

// Display import progress
if ( !function_exists( 'trx_addons_give_importer_import_fields' ) ) {
	if (is_admin()) add_action( 'trx_addons_action_importer_import_fields',	'trx_addons_give_importer_import_fields', 10, 1 );
	function trx_addons_give_importer_import_fields($importer) {
		if ( trx_addons_exists_give() && in_array('give', $importer->options['required_plugins']) ) {
			$importer->show_importer_fields(array(
				'slug'	=> 'give', 
				'title'	=> esc_html__('Give (Donation Form)', 'trx_addons')
				)
			);
		}
	}
}

// Export posts
if ( !function_exists( 'trx_addons_give_importer_export' ) ) {
	if (is_admin()) add_action( 'trx_addons_action_importer_export',	'trx_addons_give_importer_export', 10, 1 );
	function trx_addons_give_importer_export($importer) {
		if ( trx_addons_exists_give() && in_array('give', $importer->options['required_plugins']) ) {
			trx_addons_fpc($importer->export_file_dir('give.txt'), serialize( array(
				'give_formmeta' => $importer->export_dump('give_formmeta'),
				'give_donors' => $importer->export_dump('give_donors'),
				'give_donormeta' => $importer->export_dump('give_donormeta'),
				'give_logs' => $importer->export_dump('give_logs'),
				'give_logmeta' => $importer->export_dump('give_logmeta'),
				'give_paymentmeta' => $importer->export_dump('give_paymentmeta'),
				'give_sequental_ordering' => $importer->export_dump('give_sequental_ordering'),
				) )
			);
		}
	}
}

// Display exported data in the fields
if ( !function_exists( 'trx_addons_give_importer_export_fields' ) ) {
	if (is_admin()) add_action( 'trx_addons_action_importer_export_fields',	'trx_addons_give_importer_export_fields', 10, 1 );
	function trx_addons_give_importer_export_fields($importer) {
		if ( trx_addons_exists_give() && in_array('give', $importer->options['required_plugins']) ) {
			$importer->show_exporter_fields(array(
				'slug'	=> 'give',
				'title' => esc_html__('Give (Donation Form)', 'trx_addons')
				)
			);
		}
	}
}



// OCDI support
//------------------------------------------------------------------------
// Set plugin's specific importer options
if ( !function_exists( 'trx_addons_ocdi_give_set_options' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_ocdi_options', 'trx_addons_ocdi_give_set_options' );
	function trx_addons_ocdi_give_set_options($ocdi_options){
		$ocdi_options['import_give_file_url'] = 'give.txt';
		return $ocdi_options;		
	}
}

// Export Calculated Fields Form
if ( !function_exists( 'trx_addons_ocdi_give_export' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_ocdi_export_files', 'trx_addons_ocdi_give_export' );
	function trx_addons_ocdi_give_export($output){
		$list = array();
		if (trx_addons_exists_give() && in_array('give', trx_addons_ocdi_options('required_plugins'))) {
			// Get plugin data from database			
			$tables = array(
				'give_formmeta',
				'give_donors',
				'give_donormeta',
				'give_logs',
				'give_logmeta',
				'give_paymentmeta',
				'give_sequental_ordering'
			);
			$list = trx_addons_ocdi_export_tables($tables, $list);

			$options = array('give_settings');
			$list = trx_addons_ocdi_export_options($options, $list);
			
			// Save as file
			$file_path = TRX_ADDONS_PLUGIN_OCDI . "export/give.txt";
			trx_addons_fpc(trx_addons_get_file_dir($file_path), serialize($list));
			
			// Return file path
			$output .= '<h4><a href="'. trx_addons_get_file_url($file_path).'" download>'.esc_html__('Give (Donation Form)', 'trx_addons').'</a></h4>';
		}
		return $output;
	}
}

// Add plugin to import list
if ( !function_exists( 'trx_addons_ocdi_give_import_field' ) ) {
	if (is_admin()) add_filter( 'trx_addons_filter_ocdi_import_fields', 'trx_addons_ocdi_give_import_field' );
	function trx_addons_ocdi_give_import_field($output){
		$list = array();
		if (trx_addons_exists_give() && in_array('give', trx_addons_ocdi_options('required_plugins'))) {
			$output .= '<label><input type="checkbox" name="give" value="give">'. esc_html__( 'Give (Donation Form)', 'trx_addons' ).'</label><br/>';
		}
		return $output;
	}
}

// Import Calculated Fields Form
if ( !function_exists( 'trx_addons_ocdi_give_import' ) ) {
	if (is_admin()) add_action( 'trx_addons_action_ocdi_import_plugins', 'trx_addons_ocdi_give_import', 10, 1 );
	function trx_addons_ocdi_give_import($import_plugins){
		if (trx_addons_exists_give() && in_array('give', $import_plugins)) {
			trx_addons_ocdi_import_dump('give');
			echo esc_html__('Give (Donation Form) import complete.', 'trx_addons') . "\r\n";
		}
	}
}
