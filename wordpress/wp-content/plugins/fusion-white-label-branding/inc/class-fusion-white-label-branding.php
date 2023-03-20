<?php
/**
 * The main class for the Avada Custom Branding Branding plugin.
 *
 * @package Fusion-White-Label-Branding
 */

/**
 * Main Fusion_White_Label_Branding Class.
 *
 * @since 1.0
 */
class Fusion_White_Label_Branding {

	/**
	 * The one, true instance of this object.
	 *
	 * @since 1.0
	 * @static
	 * @access private
	 * @var object
	 */
	private static $instance;

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @since 1.0
	 * @static
	 * @access public
	 */
	public static function get_instance() {

		// If an instance hasn't been created and set to $instance create an instance and set it to $instance.
		if ( null === self::$instance ) {
			self::$instance = new Fusion_White_Label_Branding();
		}
		return self::$instance;
	}

	/**
	 * Initializes the plugin by setting localization, hooks, filters,
	 * and administrative functions.
	 *
	 * @since 1.0
	 * @access private
	 */
	private function __construct() {

		// Include required files.
		$this->includes();

		// Load plugin textdomain.
		$this->textdomain();
	}

	/**
	 * Include required files.
	 *
	 * @access private
	 * @since 1.0
	 * @return void
	 */
	private function includes() {
		require_once FUSION_WHITE_LABEL_BRANDING_PLUGIN_DIR . 'inc/class-fusion-white-label-branding-admin.php';
		new Fusion_White_Label_Branding_Admin();
	}

	/**
	 * Loads the plugin language files.
	 *
	 * @access public
	 * @since 1.1
	 * @return void
	 */
	public function textdomain() {

		// Set text domain.
		$domain = 'fusion-white-label-branding';

		// Load textdomain for plugin.
		load_plugin_textdomain( $domain, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Run on plugin activation. Process options migration etc.
	 *
	 * @access private
	 * @since 1.1
	 * @return void
	 */
	public static function activation() {
		$settings               = get_option( 'fusion_branding_settings', array() );
		$option_update_required = false;

		if ( isset( $settings['fusion_branding']['avada']['remove_changelog'] ) ) {
			if ( isset( $settings['fusion_branding']['avada']['remove_admin_menu'] ) ) {
				$settings['fusion_branding']['avada']['remove_admin_menu'][] = 'changelog';
			} else {
				$settings['fusion_branding']['avada']['remove_admin_menu'] = ['changelog'];
			}
			unset( $settings['fusion_branding']['avada']['remove_changelog'] );
			$option_update_required = true;
		}

		if ( isset( $settings['fusion_branding']['avada']['theme_options_menu_label'] ) ) {
			unset( $settings['fusion_branding']['avada']['theme_options_menu_label'] );
			$option_update_required = true;
		}

		if ( isset( $settings['fusion_branding']['avada']['version_number_box_background'] ) ) {
			unset( $settings['fusion_branding']['avada']['version_number_box_background'] );
			$option_update_required = true;
		}

		if ( isset( $settings['fusion_branding']['avada']['version_number_box_color'] ) ) {
			unset( $settings['fusion_branding']['avada']['version_number_box_color'] );
			$option_update_required = true;
		}

		if ( ! isset( $settings['fusion_branding']['avada']['remove_main_menu_items'] ) && isset( $settings['fusion_branding']['avada']['remove_admin_menu'] ) ) {
			$settings['fusion_branding']['avada']['remove_main_menu_items'][] = [];

			foreach ( $settings['fusion_branding']['avada']['remove_admin_menu'] as $menu_item ) {
				switch ( $menu_item ) {
					case 'avada':
						$settings['fusion_branding']['avada']['remove_main_menu_items'][] = 'avada';
						break;
					case 'theme_options':
						$settings['fusion_branding']['avada']['remove_main_menu_items'][] = 'options';
						break;
					case 'demos':
						$settings['fusion_branding']['avada']['remove_main_menu_items'][] = 'prebuilt-websites';
						break;
					case 'fusion-patcher':
						$settings['fusion_branding']['avada']['remove_main_menu_items'][] = 'patcher';
						break;
					case 'plugins':
						$settings['fusion_branding']['avada']['remove_main_menu_items'][] = 'plugins';
						break;
					case 'changelog':
						$settings['fusion_branding']['avada']['remove_main_menu_items'][] = 'changelog';
						break;
					case 'support':
						$settings['fusion_branding']['avada']['remove_main_menu_items'][] = 'support';
						break;
					case 'system-status':
						$settings['fusion_branding']['avada']['remove_main_menu_items'][] = 'status';
						break;
				}
			}

			unset( $settings['fusion_branding']['avada']['remove_admin_menu'] );
			$option_update_required = true;
		}

		if ( ! isset( $settings['fusion_branding']['avada']['remove_sticky_menu_items'] ) ) {
			$settings['fusion_branding']['avada']['remove_sticky_menu_items'] = [];

			if ( isset( $settings['fusion_branding']['fusion_builder']['remove_admin_menu'] ) ) {
				$settings['fusion_branding']['avada']['remove_sticky_menu_items'] = $settings['fusion_branding']['fusion_builder']['remove_admin_menu'];
				unset( $settings['fusion_branding']['fusion_builder'] );
				$option_update_required = true;
			}

			if ( isset( $settings['fusion_branding']['fusion_slider']['remove_admin_menu'] ) ) {
				foreach ( $settings['fusion_branding']['fusion_slider']['remove_admin_menu'] as $menu_item ) {
					switch ( $menu_item ) {
						case 'slider':
							$settings['fusion_branding']['avada']['remove_sticky_menu_items'][] = 'sliders';
							break;
						case 'slide-page':
							$settings['fusion_branding']['avada']['remove_sticky_menu_items'][] = 'slides';
							break;
						case 'import-export':
							$settings['fusion_branding']['avada']['remove_sticky_menu_items'][] = 'slider_export';
							break;
					}
				}

				unset( $settings['fusion_branding']['fusion_slider'] );
				$option_update_required = true;
			}
		}

		if ( $option_update_required ) {
			update_option( 'fusion_branding_settings', $settings );
		}
	}
}
