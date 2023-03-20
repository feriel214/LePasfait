<?php
/**
 * The admin class for the Avada Custom Branding Branding plugin.
 *
 * @package Fusion-White-Label-Branding
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The main admin class for the plugin.
 *
 * @since 1.0
 */
class Fusion_White_Label_Branding_Admin {

	/**
	 * Company site URL.
	 *
	 * @static
	 * @access public
	 * @since 1.2
	 * @var array
	 */
	public static $theme_fusion_url = 'https://theme-fusion.com/';

	/**
	 * Projects.
	 *
	 * @access public
	 * @since 1.0
	 * @var array
	 */
	private $settings = [];

	/**
	 * Constructor.
	 *
	 * @access public
	 * @since 1.0
	 */
	public function __construct() {

		$this->settings = get_option( 'fusion_branding_settings', [] );

		// Add action links to settings page for easy access.
		add_filter( 'plugin_action_links_' . plugin_basename( FUSION_WHITE_LABEL_BRANDING_PLUGIN_FILE ), [ $this, 'add_action_settings_link' ] );

		// Add Avada Custom Branding admin menu logo.
		add_action( 'admin_head', [ $this, 'admin_menu_styling' ] );

		// Add custom capability to user roles.
		add_action( 'admin_init', [ $this, 'add_user_role_caps' ] );

		// Register admin menu for form dashbaord.
		add_action( 'admin_menu', [ $this, 'admin_menu' ], 1 );
		add_action( 'admin_menu', [ $this, 'admin_menu_label_change' ], 1001 );

		// Save Branding Settings.
		add_action( 'admin_post_save_fusion_branding_settings', [ $this, 'settings_save' ] );

		// Login screen branding.
		add_action( 'login_head', [ $this, 'login_screen_branding' ] );

		// Handles settings export.
		add_action( 'init', [ $this, 'export_settings' ] );

		// Handles settings import.
		add_action( 'init', [ $this, 'import_settings' ] );

		// Handles settings reset.
		add_action( 'init', [ $this, 'reset_settings' ] );

		if ( $this->user_can_see_changes() ) {

			// Process admin menu changes.
			add_action( 'admin_menu', [ $this, 'admin_menu_remove_sub_menus' ], 1001 );
			add_action( 'admin_bar_menu', [ $this, 'remove_wp_nodes' ], 999 );

			// Remove Avada menu from admin bar on frontend.
			add_action( 'wp_before_admin_bar_render', [ $this, 'remove_wp_toolbar_menu' ], 100 );

			// Update strings with custom branding strings.
			add_filter( 'gettext', [ $this, 'update_branding_strings' ], 10, 3 );

			// Hide tabs from Avada admin screen.
			add_action( 'admin_head', [ $this, 'fusion_branding_admin_styles' ] );

			// Change logo on frontend.
			add_action( 'wp_head', [ $this, 'fusion_branding_frontend_styles' ] );

			// Remove dashboard widgets.
			add_action( 'admin_init', [ $this, 'remove_dashboard_meta' ] );

			add_filter( 'avada_wpadminbar_menu_title', [ $this, 'change_wpadminbar_dashicon' ] );
			add_filter( 'fusion_edit_live_title', [ $this, 'change_wpadminbar_dashicon' ] );

			// Add custom welcome dashboard widget.
			$wp_admin_options      = isset( $this->settings['fusion_branding']['wp_admin'] ) ? $this->settings['fusion_branding']['wp_admin'] : [];
			$welcome_panel_title   = isset( $wp_admin_options['welcome_panel_title'] ) && ! empty( $wp_admin_options['welcome_panel_title'] ) ? $wp_admin_options['welcome_panel_title'] : '';
			$welcome_panel_content = isset( $wp_admin_options['welcome_panel_content'] ) && ! empty( $wp_admin_options['welcome_panel_content'] ) ? $wp_admin_options['welcome_panel_content'] : '';
			if ( '' !== $welcome_panel_title || '' !== $welcome_panel_content ) {
				remove_action( 'welcome_panel', 'wp_welcome_panel' );
				add_action( 'all_admin_notices', [ $this, 'update_welcome_panel' ], 999 );
			}

			// Change admin page title.
			add_action( 'admin_title', [ $this, 'change_dashboard_title' ] );

			// Handle admin footer text.
			add_filter( 'admin_footer_text', [ $this, 'admin_footer_text' ] );
			add_filter( 'update_footer', [ $this, 'admin_footer_version_text' ], 99 );

			// Remove screen options tab.
			add_filter( 'screen_options_show_screen', [ $this, 'remove_screen_options' ] );

			// Remove help tab.
			add_action( 'admin_head', [ $this, 'remove_help_tabs' ], 999, 3 );

			// Update portfolio and faq menu labels.
			add_action( 'init', [ $this, 'change_post_object_label' ], 999 );

			// Update Avada welcome screen title.
			add_filter( 'avada_admin_welcome_title', [ $this, 'avada_welcome_screen_title' ] );

			// Update Avada welcome screen about text.
			add_filter( 'avada_admin_welcome_text', [ $this, 'avada_welcome_screen_about_text' ] );

			// Update Avada welcome screen about text.
			add_filter( 'avada_admin_welcome_screen_content', [ $this, 'avada_welcome_screen_content' ] );

			// Disable the dashboard additional resources sections.
			add_filter( 'avada_admin_display_additional_resources', [ $this, 'avada_admin_display_additional_resources' ] );

			// Update Avada version text
			add_filter( 'avada_db_version', [ $this, 'change_avada_db_version' ] );

			// Change the admin social media links.
			add_filter( 'fusion_admin_social_media_links', [ $this, 'change_admin_social_media_links' ] );

			// Change the live editor logo.
			add_action( 'wp_enqueue_scripts', [ $this, 'change_live_editor_styles' ] );

			$fusion_builder_options = isset( $this->settings['fusion_branding']['fusion_builder'] ) ? $this->settings['fusion_branding']['fusion_builder'] : [];
			if ( isset( $fusion_builder_options['disable_live_builder'] ) && '1' === $fusion_builder_options['disable_live_builder'] ) {
				add_filter( 'fusion_load_live_editor', '__return_false' );
			}
		}
	}

	/**
	 * Add settings link on plugins page for easy access.
	 *
	 * @access public
	 * @since 1.0
	 * @param array $links The array of action links.
	 * @return Array The $links array plus the added settings link.
	 */
	public function add_action_settings_link( $links ) {
		$links[] = '<a href="' . admin_url( 'admin.php?page=avada-white-label-branding-settings' ) . '">' . esc_html__( 'Settings', 'fusion-white-label-branding' ) . '</a>';

		return $links;
	}

	/**
	 * Add custom capability to user roles to access the admin settings.
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function add_user_role_caps() {

		// WP Admin Options.
		$wp_admin_options = isset( $this->settings['fusion_branding']['wp_admin'] ) ? $this->settings['fusion_branding']['wp_admin'] : [];
		$user_roles       = isset( $wp_admin_options['user_role_visibility'] ) ? $wp_admin_options['user_role_visibility'] : [];

		// Get all user roles.
		$all_user_roles = get_editable_roles();

		// Add administrator to user roles array.
		$user_roles[] = 'administrator';

		// Set the custom capability name for user roles.
		$capability = 'access_white_label_branding';

		// Add capability to each user roles selected by administrator.
		foreach ( $all_user_roles as $role => $info ) {
			$user_role = get_role( $role );
			if ( in_array( $role, $user_roles, true ) ) {
				$user_role->add_cap( $capability );
			} else {
				$user_role->remove_cap( $capability );
			}
		}
	}

	/**
	 * Admin Menu.
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function admin_menu() {
		$capability    = 'access_white_label_branding';

		$dashboard     = add_menu_page( esc_attr__( 'Avada Custom Branding', 'fusion-white-label-branding' ), esc_attr__( 'Avada Branding', 'fusion-white-label-branding' ), $capability, 'avada-white-label-branding-admin', [ $this, 'dashboard' ], 'dashicons-avada-wlb', '3.555555' );
		$wp_admin      = add_submenu_page( 'fusion-white-label-branding-admin', esc_attr__( 'WP Admin', 'fusion-white-label-branding' ), esc_attr__( 'WP Admin', 'fusion-white-label-branding' ), $capability, 'avada-white-label-branding-settings-wp-admin', [ $this, 'options_wp_admin' ] );
		$wp_login      = add_submenu_page( 'fusion-white-label-branding-admin', esc_attr__( 'WP Login Screen', 'fusion-white-label-branding' ), esc_attr__( 'WP Login Screen', 'fusion-white-label-branding' ), $capability, 'avada-white-label-branding-settings-login-screen', [ $this, 'options_wp_login' ] );
		$avada         = add_submenu_page( 'fusion-white-label-branding-admin', esc_attr__( 'Avada', 'fusion-white-label-branding' ), esc_attr__( 'Avada', 'fusion-white-label-branding' ), $capability, 'avada-white-label-branding-settings-avada', [ $this, 'options_avada' ] );
		$import_export = add_submenu_page( 'fusion-white-label-branding-admin', esc_attr__( 'Import / Export', 'fusion-white-label-branding' ), esc_attr__( 'Import / Export', 'fusion-white-label-branding' ), $capability, 'avada-white-label-branding-import-export', [ $this, 'options_import_export' ] );

		add_action( 'admin_print_styles-' . $dashboard, [ $this, 'admin_styles' ] );

		add_action( 'admin_print_styles-' . $wp_admin, [ $this, 'admin_styles' ] );
		add_action( 'admin_print_scripts-' . $wp_admin, [ $this, 'admin_scripts' ] );

		add_action( 'admin_print_styles-' . $wp_login, [ $this, 'admin_styles' ] );
		add_action( 'admin_print_scripts-' . $wp_login, [ $this, 'admin_scripts' ] );

		add_action( 'admin_print_styles-' . $avada, [ $this, 'admin_styles' ] );
		add_action( 'admin_print_scripts-' . $avada, [ $this, 'admin_scripts' ] );

		add_action( 'admin_print_styles-' . $import_export, [ $this, 'admin_styles' ] );
		add_action( 'admin_print_scripts-' . $import_export, [ $this, 'admin_scripts' ] );

		add_action( 'admin_print_styles', [ $this, 'avada_dashboard_styles' ] );

		if ( function_exists( 'fusion_the_admin_font_async' ) ) {
			add_action( 'admin_footer', 'fusion_the_admin_font_async' );
		}

	}

	/**
	 * Admin scripts.
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function admin_scripts() {

		// Add the color picker css file.
		wp_enqueue_script( 'jquery-color' );
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );

		// ColorPicker Alpha Channel.
		wp_enqueue_script( 'wp-color-picker-alpha', FUSION_WHITE_LABEL_BRANDING_PLUGIN_URL . 'assets/js/wp-color-picker-alpha.js', [ 'wp-color-picker', 'jquery-color' ], FUSION_WHITE_LABEL_BRANDING_VERSION, true );

		// Add media uploader scripts and styles.
		wp_enqueue_media();

		wp_enqueue_script( 'fusion_branding_admin_js', FUSION_WHITE_LABEL_BRANDING_PLUGIN_URL . 'assets/js/fusion-white-label-branding-admin.min.js', [ 'jquery', 'wp-color-picker-alpha' ], FUSION_WHITE_LABEL_BRANDING_VERSION, true );
	}

	/**
	 * Admin styles.
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function admin_styles() {
		wp_enqueue_style( 'fusion_branding_admin_css', FUSION_WHITE_LABEL_BRANDING_PLUGIN_URL . 'assets/css/fusion-white-label-branding-admin.min.css', [], FUSION_WHITE_LABEL_BRANDING_VERSION );

	}

	/**
	 * Admin styles.
	 *
	 * @access public
	 * @since 1.2
	 * @return void
	 */
	public function avada_dashboard_styles() {
		if ( class_exists( 'Avada' ) ) {
			wp_enqueue_style( 'avada_admin_css', trailingslashit( Avada::$template_dir_url ) . 'assets/admin/css/avada-admin.css', [], AVADA_VERSION );
		}
	}

	/**
	 * Loads the template file.
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function dashboard() {
		require_once wp_normalize_path( dirname( __FILE__ ) . '/admin-screens/dashboard.php' );
	}

	/**
	 * Loads the options template file.
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function options_wp_admin() {
		require_once wp_normalize_path( dirname( __FILE__ ) . '/admin-screens/wp-admin.php' );
	}

	/**
	 * Loads the options template file.
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function options_wp_login() {
		require_once wp_normalize_path( dirname( __FILE__ ) . '/admin-screens/wp-login.php' );
	}

	/**
	 * Loads the options template file.
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function options_avada() {
		require_once wp_normalize_path( dirname( __FILE__ ) . '/admin-screens/avada.php' );
	}

	/**
	 * Handles options import / export.
	 *
	 * @access public
	 * @since 1.1
	 * @return void
	 */
	public function options_import_export() {
		require_once wp_normalize_path( dirname( __FILE__ ) . '/admin-screens/import-export.php' );
	}

	/**
	 * Renders the admin screens header with title, logo and tabs.
	 *
	 * @static
	 * @access public
	 * @since 1.2
	 * @param string $screen The current screen.
	 * @return void
	 */
	public static function get_admin_screens_header( $screen = 'dashboard' ) {
		$screen_classes = 'wrap avada-dashboard fusion-white-label-branding-wrap about-wrap avada-db-' . $screen;

		if ( 'settings' === $screen ) {
			$screen_classes .= ' fusion-white-label-branding-settings';
		}
		?>
		<div class="<?php echo esc_html( $screen_classes ); ?>">
			<header class="avada-db-header-main awlb-header">
				<div class="avada-db-header-main-container">
					<a class="avada-db-logo" href="<?php echo esc_url( admin_url( 'admin.php?page=avada-white-label-branding-admin' ) ); ?>" aria-label="<?php esc_attr_e( 'Link to Avada While Label Branding dashboard', 'Avada' ); ?>">
						<i class="avada-db-logo-icon fusiona-avada-logo"></i>
						<div class="avada-db-logo-image">
							<span class="awlb-dashboard-heading"><?php esc_html_e( 'Avada Custom Branding', 'fusion-white-label-branding' ); ?></span>
							<span class="awlb-dashboard-version">
								<?php
								/* translators: The version number. */
								printf( esc_html__( 'v%s', 'fusion-white-label-branding' ), FUSION_WHITE_LABEL_BRANDING_VERSION ); // WPCS: XSS ok.
								?>
							</span>
						</div>
					</a>
					<nav class="avada-db-menu-main">
						<ul class="avada-db-menu">
							<li class="avada-db-menu-item"><a class="avada-db-menu-item-link<?php echo ( 'wp-admin' === $screen ) ? ' avada-db-active' : ''; ?>" href="<?php echo esc_url( ( 'wp-admin' === $screen ) ? '#' : admin_url( 'admin.php?page=avada-white-label-branding-settings-wp-admin' ) ); ?>" ><span class="awlb-menu-icon dashicons-before dashicons-wordpress-alt"></span><span class="avada-db-menu-item-text"><?php esc_html_e( 'WP Admin', 'fusion-white-label-branding' ); ?></span></a></li>
							<li class="avada-db-menu-item"><a class="avada-db-menu-item-link<?php echo ( 'login-screen' === $screen ) ? ' avada-db-active' : ''; ?>" href="<?php echo esc_url( ( 'settings' === $screen ) ? '#' : admin_url( 'admin.php?page=avada-white-label-branding-settings-login-screen' ) ); ?>" ><span class="awlb-menu-icon dashicons-before dashicons-unlock"></span><span class="avada-db-menu-item-text"><?php esc_html_e( 'WP Login Screen', 'fusion-white-label-branding' ); ?></span></a></li>
							<li class="avada-db-menu-item"><a class="avada-db-menu-item-link<?php echo ( 'avada' === $screen ) ? ' avada-db-active' : ''; ?>" href="<?php echo esc_url( ( 'avada' === $screen ) ? '#' : admin_url( 'admin.php?page=avada-white-label-branding-settings-avada' ) ); ?>" ><span class="awlb-menu-icon fusiona-avada-logo"></span><span class="avada-db-menu-item-text">Avada</span></a></li>
							<li class="avada-db-menu-item"><a class="avada-db-menu-item-link<?php echo ( 'import' === $screen ) ? ' avada-db-active' : ''; ?>" href="<?php echo esc_url( ( 'import' === $screen ) ? '#' : admin_url( 'admin.php?page=avada-white-label-branding-import-export' ) ); ?>"><span class="awlb-menu-icon dashicons-before dashicons-update-alt"></span><span class="avada-db-menu-item-text"><?php esc_html_e( 'Import / Export', 'fusion-white-label-branding' ); ?></span></a></li>
						</ul>
					</nav>
				</div>
			</header>

			<div class="avada-db-demos-notices"><h1></h1></div>
		<?php
	}

	/**
	 * Renders the admin screens footer.
	 *
	 * @static
	 * @access public
	 * @since 1.2
	 * @param string $screen The current screen.
	 * @return void
	 */
	public static function get_admin_screens_footer( $screen = 'dashboard' ) {
		?>
			<?php if ( 'slide-edit' !== $screen ) : ?>
				<footer class="avada-db-footer">
					<div class="avada-db-footer-top">
						<nav class="avada-db-footer-menu">
							<span class="avada-db-footer-company"><i class="fusiona-TFicon"></i><strong><?php esc_html_e( 'ThemeFusion', 'Avada' ); ?></strong></span>
							<ul>
								<li>
									<a href="<?php echo esc_url( self::$theme_fusion_url ) . 'documentation/avada/'; ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Documentation', 'Avada' ); ?></a>
								</li>
								<li>
									<a href="<?php echo esc_url( self::$theme_fusion_url ) . 'documentation/avada/videos/'; ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Video Tutorials', 'Avada' ); ?></a>
								</li>
								<li>
									<a href="<?php echo esc_url_raw( self::$theme_fusion_url ) . 'support/submit-a-ticket/'; ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Submit A Ticket', 'Avada' ); ?></a>
								</li>
							</ul>
						</nav>

						<?php echo self::get_social_media_links(); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</div>

					<div class="avada-db-footer-bottom">
						<div class="avada-db-footer-thanks"><?php esc_html_e( 'Thank you for choosing Avada. We are honored and are fully dedicated to making your experience perfect.', 'Avada' ); ?></div>
						<nav class="avada-db-footer-menu-bottom">
							<a href="<?php echo esc_url_raw( self::$theme_fusion_url ) . 'support-policy/'; ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Support Policy', 'Avada' ); ?></a>
						</nav>
					</div>
				</footer>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Get social media links
	 *
	 * @static
	 * @access public
	 * @since 1.2
	 * @return string The social media link markup
	 */
	public static function get_social_media_links() {
		$social_media_markup = '<div class="avada-db-footer-social-media">
		<a href="https://www.facebook.com/ThemeFusion-101565403356430/" target="_blank" class="avada-db-social-icon dashicons dashicons-facebook-alt"></a>
		<a href="https://twitter.com/theme_fusion" target="_blank" class="avada-db-social-icon dashicons dashicons-twitter"></a>
		<a href="https://www.instagram.com/themefusion/" target="_blank" class="avada-db-social-icon dashicons dashicons-instagram"></a>
		<a href="https://www.youtube.com/channel/UC_C7uAOAH9RMzZs-CKCZ62w" target="_blank" class="avada-db-social-icon fusiona-youtube"></a>
		</div>';

		return apply_filters( 'fusion_admin_social_media_links', $social_media_markup );
	}

	/**
	 * Change the menu label for Avada Custom Branding.
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function admin_menu_label_change() {
		global $menu, $submenu;

		// Change Fusion Branding first menu item label to welcome.
		if ( current_user_can( 'edit_theme_options' ) && isset( $submenu['fusion-white-label-branding-admin'] ) ) {
			// @codingStandardsIgnoreLine WordPress.Variables.GlobalVariables.OverrideProhibited
			$submenu['fusion-white-label-branding-admin'][0][0] = esc_html__( 'Welcome', 'fusion-white-label-branding' );
		}

		if ( $this->user_can_see_changes() ) {

			// WP Admin Options.
			$wp_admin_options = isset( $this->settings['fusion_branding']['wp_admin'] ) ? $this->settings['fusion_branding']['wp_admin'] : [];
			$admin_menus      = isset( $wp_admin_options['rename_admin_menu'] ) ? $wp_admin_options['rename_admin_menu'] : [];

			// Avada Options.
			$avada_options = isset( $this->settings['fusion_branding']['avada'] ) ? $this->settings['fusion_branding']['avada'] : [];
			$avada_menus   = isset( $avada_options['rename_admin_menu'] ) ? $avada_options['rename_admin_menu'] : [];

			// Fusion Builder Options.
			$fusion_builder_options = isset( $this->settings['fusion_branding']['fusion_builder'] ) ? $this->settings['fusion_branding']['fusion_builder'] : [];

			// Change dasbhoard menu label.
			if ( isset( $admin_menus ) && ! empty( $admin_menus ) ) {
				foreach ( $admin_menus as $menu_item => $label ) {
					if ( '' !== $label ) {
						switch ( $menu_item ) {
							case 'dashboard':
								// @codingStandardsIgnoreLine WordPress.Variables.GlobalVariables.OverrideProhibited
								$menu[2][0] = $label;
								break;
							case 'posts':
								// @codingStandardsIgnoreLine WordPress.Variables.GlobalVariables.OverrideProhibited
								$menu[5][0] = $label;
								break;
							case 'media':
								// @codingStandardsIgnoreLine WordPress.Variables.GlobalVariables.OverrideProhibited
								$menu[10][0] = $label;
								break;
							case 'pages':
								// @codingStandardsIgnoreLine WordPress.Variables.GlobalVariables.OverrideProhibited
								$menu[20][0] = $label;
								break;
							case 'comments':
								// @codingStandardsIgnoreLine WordPress.Variables.GlobalVariables.OverrideProhibited
								$menu[25][0] = $label;
								break;
							case 'themes':
								// @codingStandardsIgnoreLine WordPress.Variables.GlobalVariables.OverrideProhibited
								$menu[60][0] = $label;
								break;
							case 'plugins':
								// @codingStandardsIgnoreLine WordPress.Variables.GlobalVariables.OverrideProhibited
								$menu[65][0] = $label;
								break;
							case 'users':
								// @codingStandardsIgnoreLine WordPress.Variables.GlobalVariables.OverrideProhibited
								$menu[70][0] = $label;
								break;
							case 'tools':
								// @codingStandardsIgnoreLine WordPress.Variables.GlobalVariables.OverrideProhibited
								$menu[75][0] = $label;
								break;
							case 'settings':
								// @codingStandardsIgnoreLine WordPress.Variables.GlobalVariables.OverrideProhibited
								$menu[80][0] = $label;
								break;
						}
					}
				}
			}

			// If set, change Avada admin menu label.
			if ( isset( $menu['2.111111'] ) && isset( $avada_options['admin_menu_label'] ) && '' !== $avada_options['admin_menu_label'] ) {
				// @codingStandardsIgnoreLine WordPress.Variables.GlobalVariables.OverrideProhibited
				$menu['2.111111'][0] = $avada_options['admin_menu_label'];
			}

			// Change Avada admin menu icon class name if set in settings.
			if ( isset( $menu['2.111111'] ) && ( ! isset( $avada_options['avada_icon_image'] ) || '' === $avada_options['avada_icon_image'] ) && isset( $avada_options['admin_menu_dashicon'] ) && '' !== $avada_options['admin_menu_dashicon'] ) {
				// @codingStandardsIgnoreLine WordPress.Variables.GlobalVariables.OverrideProhibited
				$menu['2.111111'][6] = $avada_options['admin_menu_dashicon'];
			}

			// Change Avada Custom Branding admin menu icon class name if set in settings.
			if ( isset( $menu['3.555555'] ) && ( ! isset( $avada_options['fusion_white_label_branding_icon_image'] ) || '' === $avada_options['fusion_white_label_branding_icon_image'] ) && isset( $avada_options['fusion_white_label_branding_menu_dashicon'] ) && '' !== $avada_options['fusion_white_label_branding_menu_dashicon'] ) {
				// @codingStandardsIgnoreLine WordPress.Variables.GlobalVariables.OverrideProhibited
				$menu['3.555555'][6] = $avada_options['fusion_white_label_branding_menu_dashicon'];
			}
		}
	}

	/**
	 * Remove selected sub-menus from admin menus.
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function admin_menu_remove_sub_menus() {

		// WP Admin Options.
		$wp_admin_options = isset( $this->settings['fusion_branding']['wp_admin'] ) ? $this->settings['fusion_branding']['wp_admin'] : [];
		$wp_admin_menus   = isset( $wp_admin_options['remove_admin_menu'] ) ? $wp_admin_options['remove_admin_menu'] : [];

		// Avada Options.
		$avada_options         = isset( $this->settings['fusion_branding']['avada'] ) ? $this->settings['fusion_branding']['avada'] : [];
		$avada_menus           = isset( $avada_options['remove_main_menu_items'] ) ? $avada_options['remove_main_menu_items'] : [];
		$avada_sub_menus       = isset( $avada_options['remove_sticky_menu_items'] ) ? $avada_options['remove_sticky_menu_items'] : [];
		$avada_menus           = array_merge( $avada_menus, $avada_sub_menus );
		$maintenance_sub_menu  = [ 'patcher', 'plugins', 'support', 'status' ];
		$maintenance_counter   = 0;
		$avada_post_type_menus = isset( $avada_options['remove_post_type_menu'] ) ? $avada_options['remove_post_type_menu'] : [];

		// Remove selected menus from admin menu.
		if ( ! empty( $wp_admin_menus ) ) {
			foreach ( $wp_admin_menus as $menu_slug ) {
				remove_menu_page( $menu_slug );
			}
		}

		// Remove selected sub-menus under Avada admin menu.
		if ( ! empty( $avada_menus ) ) {
			if ( 15 === count( $avada_menus ) ) {
				remove_menu_page( 'avada' );
			} else {
				foreach ( $avada_menus as $menu_slug ) {
					if ( is_array( $menu_slug ) || empty( $menu_slug ) ) {
						continue;
					}
					$parent_slug = 'avada';

					if ( in_array( $menu_slug, $maintenance_sub_menu ) ) {
						$maintenance_counter++;
					}

					if ( 'options' === $menu_slug ) {
						$menu_slug = 'themes.php?page=avada_options';
					} elseif ( 'avada' !== $menu_slug && 'changelog' !== $menu_slug ) {
						$menu_slug = 'avada-' . $menu_slug;
					}

					if ( 'avada-sliders' === $menu_slug ) {
						remove_submenu_page( $parent_slug, esc_url( admin_url( 'edit-tags.php?taxonomy=slide-page&post_type=slide' ) ) );
					} else {
						remove_submenu_page( $parent_slug, $menu_slug );
					}
				}

				if ( 4 === $maintenance_counter ) {
					remove_submenu_page( 'avada', esc_url( admin_url( 'admin.php?page=avada-patcher' ) ) );
				}
			}
		}

		// Remove admin menus for selected post types.
		if ( ! empty( $avada_post_type_menus ) ) {
			foreach ( $avada_post_type_menus as $key => $menu_slug ) {
				remove_menu_page( 'edit.php?post_type=' . $menu_slug );
			}
		}
	}

	/**
	 * Remove WordPress logo from admin bar.
	 *
	 * @access public
	 * @since 1.0
	 * @param  object $wp_admin_bar WP Admin Bar.
	 * @return void
	 */
	public function remove_wp_nodes( $wp_admin_bar ) {
		// WordPress Admin Settings.
		$wp_admin_options = isset( $this->settings['fusion_branding']['wp_admin'] ) ? $this->settings['fusion_branding']['wp_admin'] : [];
		$wp_admin_menus   = isset( $wp_admin_options['remove_admin_menu'] ) ? $wp_admin_options['remove_admin_menu'] : [];

		// Avada Options.
		$avada_options         = isset( $this->settings['fusion_branding']['avada'] ) ? $this->settings['fusion_branding']['avada'] : [];
		$avada_post_type_menus = isset( $avada_options['remove_post_type_menu'] ) ? $avada_options['remove_post_type_menu'] : [];

		// Fusion Slider Options.
		$fusion_slider_options = isset( $this->settings['fusion_branding']['fusion_slider'] ) ? $this->settings['fusion_branding']['fusion_slider'] : [];
		$fusion_slider_menus   = isset( $fusion_slider_options['remove_admin_menu'] ) ? $fusion_slider_options['remove_admin_menu'] : [];

		// Remove Fusion Slide menu from admin bar if selected.
		if ( is_array( $fusion_slider_menus ) && in_array( 'slider', $fusion_slider_menus, true ) ) {
			$wp_admin_bar->remove_node( 'new-slide' );
		}

		// Remove WP Logo is set to yes.
		if ( isset( $wp_admin_options['hide_wordpress_logo'] ) && $wp_admin_options['hide_wordpress_logo'] ) {
			$wp_admin_bar->remove_node( 'wp-logo' );
		}

		// Remove selected menus from admin bar.
		if ( ! empty( $wp_admin_menus ) ) {
			foreach ( $wp_admin_menus as $menu_slug ) {

				switch ( $menu_slug ) {

					case 'edit.php':
						$wp_admin_bar->remove_node( 'new-post' );
						break;

					case 'edit.php?post_type=page':
						$wp_admin_bar->remove_node( 'new-page' );
						break;

					case 'upload.php':
						$wp_admin_bar->remove_node( 'new-media' );
						break;

					case 'users.php':
						$wp_admin_bar->remove_node( 'new-user' );
						break;
				}
			}
		}

		// Remove admin bar menus for selected post types.
		if ( ! empty( $avada_post_type_menus ) ) {
			foreach ( $avada_post_type_menus as $key => $menu_slug ) {
				$wp_admin_bar->remove_node( 'new-' . $menu_slug );
			}
		}
	}

	/**
	 * Remove Avada menu item from admin bar on frontend.
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function remove_wp_toolbar_menu() {
		global $wp_admin_bar;

		// Avada Options.
		$avada_options         = isset( $this->settings['fusion_branding']['avada'] ) ? $this->settings['fusion_branding']['avada'] : [];
		$remove_admin_bar_menu = isset( $avada_options['remove_admin_bar_menu'] ) ? $avada_options['remove_admin_bar_menu'] : false;

        if ( $remove_admin_bar_menu ) {
            $wp_admin_bar->remove_node( 'avada' );
        }
	}

	/**
	 * Remove dashboard widgets.
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function remove_dashboard_meta() {
		$wp_admin_options  = isset( $this->settings['fusion_branding']['wp_admin'] ) ? $this->settings['fusion_branding']['wp_admin'] : [];
		$dashboard_widgets = isset( $wp_admin_options['remove_dashboard_widget'] ) && ! empty( $wp_admin_options['remove_dashboard_widget'] ) ? $wp_admin_options['remove_dashboard_widget'] : [];

		if ( ! empty( $dashboard_widgets ) ) {
			foreach ( $dashboard_widgets as $widget ) {
				switch ( $widget ) {
					case 'quick_press':
						remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
						break;
					case 'recent_drafts':
						remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'side' );
						break;
					case 'right_now':
						remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );
						break;
					case 'activity':
						remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' );
						break;
					case 'primary':
						remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
						break;
					case 'themefusion_news':
						remove_meta_box( 'themefusion-news', 'dashboard', 'side' );
						break;
				}
			}
		}
	}

	/**
	 * Change the WP admin bar menu dashicon.
	 *
	 * @access public
	 * @since 1.2
	 * @param string $avada_parent_menu_title The title string.
	 * @return string The updated title string.
	 */
	public function change_wpadminbar_dashicon( $avada_parent_menu_title ) {
		// Avada Options.
		$avada_options = isset( $this->settings['fusion_branding']['avada'] ) ? $this->settings['fusion_branding']['avada'] : [];

		if ( ( ! isset( $avada_options['avada_icon_image'] ) || '' === $avada_options['avada_icon_image'] ) && isset( $avada_options['admin_menu_dashicon'] ) && '' !== $avada_options['admin_menu_dashicon'] ) {
			$admin_menu_dashicon     = 'awlb-wpadminbar-icon dashicons-before ' . $avada_options['admin_menu_dashicon'];
            $avada_parent_menu_title = '<span class="ab-icon ' . $admin_menu_dashicon . '"></span>' . $avada_parent_menu_title;
		}

		return $avada_parent_menu_title;
	}

	/**
	 * Add custom welcome panel to dashboard.
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function update_welcome_panel() {
		$screen = get_current_screen();
		if ( 'dashboard' === $screen->base ) {
			$wp_admin_options      = isset( $this->settings['fusion_branding']['wp_admin'] ) ? $this->settings['fusion_branding']['wp_admin'] : [];
			$welcome_panel_title   = isset( $wp_admin_options['welcome_panel_title'] ) && ! empty( $wp_admin_options['welcome_panel_title'] ) ? $wp_admin_options['welcome_panel_title'] : '';
			$welcome_panel_content = isset( $wp_admin_options['welcome_panel_content'] ) && ! empty( $wp_admin_options['welcome_panel_content'] ) ? $wp_admin_options['welcome_panel_content'] : '';

			echo '<div class="wrap">';
			echo '<style type="text/css">.wrap h1:not(.dashboard-title) { display: none; }</style>';
			echo '<h1 class="dashboard-title">' . esc_html__( 'Dashboard' ) . '</h1>';

			echo '<div id="welcome-panel" class="welcome-panel">';

			// Display content for welcome panel.
			echo '<div class="fusion-white-label-branding-welcome-panel-wrapper">';

			// Display title for welcome panel.
			if ( '' !== $welcome_panel_title ) {
				echo '<h2 class="fusion-white-label-branding-welcome-panel-title">' . $welcome_panel_title . '</h2>'; // WPCS: XSS ok.
			}

			echo '<div class="fusion-white-label-branding-welcome-panel-content about-wrap">';
			echo $welcome_panel_content; // WPCS: XSS ok.
			echo '</div>';

			echo '</div>';

			echo '</div>';
			echo '</div>';

		}
	}

	/**
	 * Change admin page title.
	 *
	 * @access public
	 * @since 1.0
	 * @param  string $admin_title Admin page title.
	 * @return string $admin_title Updated admin page title.
	 */
	public function change_dashboard_title( $admin_title ) {
		global $current_screen, $title;

		if ( 'dashboard' !== $current_screen->id ) {
			return $admin_title;
		}

		// WP Admin Options.
		$wp_admin_options = isset( $this->settings['fusion_branding']['wp_admin'] ) ? $this->settings['fusion_branding']['wp_admin'] : [];

		// Change dasbhoard label.
		if ( isset( $wp_admin_options['dashboard_menu_label'] ) && '' !== $wp_admin_options['dashboard_menu_label'] ) {
			// @codingStandardsIgnoreLine WordPress.Variables.GlobalVariables.OverrideProhibited
			$change_title = $title = $wp_admin_options['dashboard_menu_label'];
			$admin_title  = str_replace( esc_html__( 'Dashboard' ), $change_title, $admin_title );
		}

		return $admin_title;
	}

	/**
	 * Handles text displayed for admin footer.
	 *
	 * @access public
	 * @since 1.0
	 * @param string $text The default text provided by WordPress.
	 * @return void
	 */
	public function admin_footer_text( $text ) {
		global $allowedposttags;

		$wp_admin_options  = isset( $this->settings['fusion_branding']['wp_admin'] ) ? $this->settings['fusion_branding']['wp_admin'] : [];
		$admin_footer_text = isset( $wp_admin_options['admin_footer_text'] ) && ! empty( $wp_admin_options['admin_footer_text'] ) ? $wp_admin_options['admin_footer_text'] : '';

		if ( '' !== $admin_footer_text ) {
			$text = wp_kses( $admin_footer_text, $allowedposttags );
		}

		echo $text; // WPCS: XSS ok.
	}

	/**
	 * Handles text displayed for admin footer.
	 *
	 * @access public
	 * @since 1.0
	 * @param string $text The default text provided by WordPress.
	 * @return void
	 */
	public function admin_footer_version_text( $text ) {
		global $allowedposttags;

		$wp_admin_options          = isset( $this->settings['fusion_branding']['wp_admin'] ) ? $this->settings['fusion_branding']['wp_admin'] : [];
		$admin_footer_version_text = isset( $wp_admin_options['admin_footer_version_text'] ) && ! empty( $wp_admin_options['admin_footer_version_text'] ) ? $wp_admin_options['admin_footer_version_text'] : '';

		if ( '' !== $admin_footer_version_text ) {
			$text = wp_kses( $admin_footer_version_text, $allowedposttags );
		}

		echo $text; // WPCS: XSS ok.
	}

	/**
	 * Handles screen options tab removal.
	 *
	 * @access public
	 * @since 1.0
	 * @param bool $show_screen Whether to show Screen Options tab. Default true.
	 * @return bool Returns true or false depend on option to show Screen Options tab.
	 */
	public function remove_screen_options( $show_screen ) {
		$wp_admin_options = isset( $this->settings['fusion_branding']['wp_admin'] ) ? $this->settings['fusion_branding']['wp_admin'] : [];
		$screen_options   = isset( $wp_admin_options['remove_screen_options'] ) && ! empty( $wp_admin_options['remove_screen_options'] ) ? $wp_admin_options['remove_screen_options'] : '0';

		if ( $screen_options ) {
			return false;
		} else {
			return $show_screen;
		}
	}

	/**
	 * Removes the Help tab in the WP Admin.
	 *
	 * @param array $old_help  Old help tabs array.
	 * @param int   $screen_id Current Screen ID.
	 * @param obj   $screen    Current Screen.
	 * @return array
	 */
	public function remove_help_tabs() {
		$wp_admin_options = isset( $this->settings['fusion_branding']['wp_admin'] ) ? $this->settings['fusion_branding']['wp_admin'] : [];
		$remove_help_tab  = isset( $wp_admin_options['remove_help_tab'] ) && ! empty( $wp_admin_options['remove_help_tab'] ) ? $wp_admin_options['remove_help_tab'] : '0';

		if ( $remove_help_tab ) {
			get_current_screen()->remove_help_tabs();
		}
	}

	/**
	 * Builder is displayed on the following post types.
	 *
	 * @access public
	 * @since 1.0
	 * @return array Returns allowed post types array.
	 */
	private function allowed_post_types() {

		$options = get_option( 'fusion_builder_settings', [] );

		if ( ! empty( $options ) && isset( $options['post_types'] ) ) {
			// If there are options saved, used them.
			$post_types = ( ' ' === $options['post_types'] ) ? [] : $options['post_types'];
			return apply_filters( 'fusion_builder_allowed_post_types', $post_types );
		} else {
			// Otherwise use defaults.
			return FusionBuilder::default_post_types();
		}

	}

	/**
	 * Handles the saving of branding settings in admin area.
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function settings_save() {
		global $allowedposttags;
		check_admin_referer( 'fusion_branding_save_settings', 'fusion_branding_save_settings' );

		$settings = get_option( 'fusion_branding_settings', [] );
		// @codingStandardsIgnoreLine WordPress.VIP.ValidatedSanitizedInput.InputNotSanitized
		$section  = isset( $_POST['section'] ) ? wp_unslash( $_POST['section'] ) : '';
		$settings['fusion_branding'] = ( isset( $settings['fusion_branding'] ) ) ? $settings['fusion_branding'] : [];

		// Assign section settings.
		if ( isset( $_POST['fusion_branding'] ) && isset( $_POST['fusion_branding'][ $section ] ) ) {
			// @codingStandardsIgnoreLine WordPress.VIP.ValidatedSanitizedInput.InputNotSanitized
			$settings['fusion_branding'][ $section ] = wp_unslash( $_POST['fusion_branding'][ $section ] );
		}

		if ( 'wp_admin' === $section ) {
			$settings['fusion_branding'][ $section ]['welcome_panel_content'] = wp_kses( $settings['fusion_branding'][ $section ]['welcome_panel_content'], $allowedposttags );
			$settings['fusion_branding'][ $section ]['admin_footer_text']     = wp_kses( $settings['fusion_branding'][ $section ]['admin_footer_text'], $allowedposttags );
		}

		if ( 'fusion_builder' === $section ) {
			$settings['fusion_branding'][ $section ]['welcome_screen_content']    = wp_kses( $settings['fusion_branding'][ $section ]['welcome_screen_content'], $allowedposttags );
			$settings['fusion_branding'][ $section ]['welcome_screen_about_text'] = wp_kses( stripslashes( $settings['fusion_branding'][ $section ]['welcome_screen_about_text'] ), $allowedposttags );
		}

		if ( 'avada' === $section ) {
			$settings['fusion_branding'][ $section ]['welcome_screen_content']    = wp_kses( $settings['fusion_branding'][ $section ]['welcome_screen_content'], $allowedposttags );
			$settings['fusion_branding'][ $section ]['welcome_screen_about_text'] = wp_kses( stripslashes( $settings['fusion_branding'][ $section ]['welcome_screen_about_text'] ), $allowedposttags );
		}

		// Update settings.
		update_option( 'fusion_branding_settings', $settings );

		// Redirect back to the corresponding section.
		wp_safe_redirect( admin_url( 'admin.php?page=avada-white-label-branding-settings-' . str_replace( '_', '-', $section ) ) );
		exit;
	}

	/**
	 * Handles the login screen branding.
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function login_screen_branding() {
		$login_screen_options = ( isset( $this->settings['fusion_branding'] ) && isset( $this->settings['fusion_branding']['login_screen'] ) ) ? $this->settings['fusion_branding']['login_screen'] : [];

		if ( ! empty( $login_screen_options ) ) {
			echo '<div class="fusion-branding-overlay"></div>';
			echo '<style type="text/css">';

			if ( isset( $login_screen_options['login_background_color'] ) && '' !== $login_screen_options['login_background_color'] ) {
				echo '.fusion-branding-overlay{position:fixed;top:0;left:0;bottom:0;width:100%;height:100%;background-color:' . esc_attr( $login_screen_options['login_background_color'] ) . ';}';
				echo '#login {position: relative;}';
			}

			if ( isset( $login_screen_options['login_background_image'] ) && '' !== $login_screen_options['login_background_image'] ) {
				echo 'body.login{background-image:url( "' . esc_attr( $login_screen_options['login_background_image'] ) . '");background-repeat:no-repeat;background-position:center center;background-size:cover;}';
			}

			if ( isset( $login_screen_options['login_logo_image'] ) && '' !== $login_screen_options['login_logo_image'] ) {
				echo 'body.login h1 a{background-image: none, url( "' . esc_attr( $login_screen_options['login_logo_image'] ) . '");background-size:contain;width:auto;}';
			}

			if ( isset( $login_screen_options['login_box_background_color'] ) && '' !== $login_screen_options['login_box_background_color'] ) {
				echo 'body.login form{background-color:' . esc_attr( $login_screen_options['login_box_background_color'] ) . ';box-shadow:0 1px 3px ' . esc_attr( $login_screen_options['login_box_background_color'] ) . ';}';
			}

			if ( isset( $login_screen_options['login_box_text_color'] ) && '' !== $login_screen_options['login_box_text_color'] ) {
				echo 'body.login form label{color:' . esc_attr( $login_screen_options['login_box_text_color'] ) . ';}';
			}

			if ( isset( $login_screen_options['login_box_link_color'] ) && '' !== $login_screen_options['login_box_link_color'] ) {
				echo 'body.login #login a{color:' . esc_attr( $login_screen_options['login_box_link_color'] ) . ';}';
			}

			if ( isset( $login_screen_options['login_box_link_hover_color'] ) && '' !== $login_screen_options['login_box_link_hover_color'] ) {
				echo 'body.login #login a:hover{color:' . esc_attr( $login_screen_options['login_box_link_hover_color'] ) . ';}';
			}

			$button_default = isset( $login_screen_options['login_button_background_color'] ) ? $login_screen_options['login_button_background_color'] : '';

			if ( '' !== $button_default ) {
				echo '#wp-submit{background:' . esc_attr( $button_default ) . ';border-color:' . esc_attr( $button_default ) . ';box-shadow:0 1px 0 ' . esc_attr( $button_default ) . ';text-decoration:none;text-shadow:0 -1px 1px ' . esc_attr( $button_default ) . ',1px 0 1px ' . esc_attr( $button_default ) . ',0 1px 1px ' . esc_attr( $button_default ) . ',-1px 0 1px ' . esc_attr( $button_default ) . ';}';
			}

			$button_text = isset( $login_screen_options['login_button_accent_color'] ) ? $login_screen_options['login_button_accent_color'] : '';

			if ( '' !== $button_text ) {
				echo '#wp-submit{color:' . esc_attr( $button_text ) . ';}';
			}

			$button_hover = isset( $login_screen_options['login_button_background_color_hover'] ) ? $login_screen_options['login_button_background_color_hover'] : '';

			if ( '' !== $button_hover ) {
				echo '#wp-submit:hover{background:' . esc_attr( $button_hover ) . ';border-color:' . esc_attr( $button_hover ) . ';}';
			}

			$text_hover = isset( $login_screen_options['login_button_accent_color_hover'] ) ? $login_screen_options['login_button_accent_color_hover'] : '';

			if ( '' !== $text_hover ) {
				echo '#wp-submit:hover{color:' . esc_attr( $text_hover ) . ';}';
			}

			if ( isset( $login_screen_options['remove_lost_password'] ) && $login_screen_options['remove_lost_password'] ) {
				echo '#nav a:last-child{display:none;}';
			}

			echo '</style>';

		}
	}

	/**
	 * Update strings with the new ones.
	 *
	 * @access public
	 * @since 1.0
	 * @param  string $text     String passed via gettext filter.
	 * @param  string $old_text Untranslated string.
	 * @param  string $domain   Text-domain.
	 * @return string $text Updated string.
	 */
	public function update_branding_strings( $text, $old_text, $domain ) {
		global $pagenow;

		// WP Admin Settings.
		$login_screen_options = ( isset( $this->settings['fusion_branding']['login_screen'] ) ) ? $this->settings['fusion_branding']['login_screen'] : [];

		// Avada Options.
		$avada_options = isset( $this->settings['fusion_branding']['avada'] ) ? $this->settings['fusion_branding']['avada'] : [];

		// Fusion Slider Options.
		$fusion_slider_options = isset( $this->settings['fusion_branding']['fusion_slider'] ) ? $this->settings['fusion_branding']['fusion_slider'] : [];

		// Fusion Builder Options.
		$fusion_builder_options = isset( $this->settings['fusion_branding']['fusion_builder'] ) ? $this->settings['fusion_branding']['fusion_builder'] : [];

		if ( isset( $login_screen_options['remove_lost_password'] ) && $login_screen_options['remove_lost_password'] ) {
			add_filter( 'login_link_separator', '__return_false' );
		}

		if ( isset( $avada_options['admin_menu_label'] ) && '' !== $avada_options['admin_menu_label'] ) {
			$text = str_replace( 'Avada', $avada_options['admin_menu_label'], trim( $text, '?' ) );
			$text = str_replace( 'ThemeFusion', $avada_options['admin_menu_label'], trim( $text, '?' ) );
		}

		$post_types = isset( $avada_options['rename_admin_menu'] ) ? $avada_options['rename_admin_menu'] : [];

		if ( class_exists( 'FusionCore_Plugin' ) ) {
			if ( 'fusion-core' === $domain ) {
				$portfolio_label = isset( $post_types['portfolio'] ) ? $post_types['portfolio'] : '';
				if ( '' !== $portfolio_label ) {
					$text = str_replace( 'Portfolio', $portfolio_label, trim( $text, '?' ) );
				}

				if ( isset( $fusion_slider_options['import_export_label'] ) && '' !== $fusion_slider_options['import_export_label'] ) {
					$text = str_replace( 'Export / Import', $fusion_slider_options['import_export_label'], trim( $text, '?' ) );
				}
			}

			// Change Fusion Slider name for Page Options.
			if ( isset( $fusion_slider_options['admin_menu_label'] ) && '' !== $fusion_slider_options['admin_menu_label'] ) {
				$slider_label = $fusion_slider_options['admin_menu_label'];
				$text         = str_replace( 'Fusion Slider', $slider_label, trim( $text, '?' ) );
			}

			$slider_export_label = ( isset( $fusion_slider_options['import_export_label'] ) && '' !== $fusion_slider_options['import_export_label'] ) ? $fusion_slider_options['import_export_label'] : '';
			if ( '' !== $slider_export_label ) {
				$text = str_replace( 'Export / Import', $slider_export_label, trim( $text, '?' ) );
			}
		}

		return $text;
	}

	/**
	 * Admin styles.
	 *
	 * @access public
	 * @since 1.1
	 * @return void
	 */
	public function admin_menu_styling() {

		$style = '<style type="text/css" id="fusion-white-label-branding-menu-css">';

		if ( ! class_exists( 'Avada' ) ) :
			$style .= '.dashicons-avada-wlb {
				background: url(' . FUSION_WHITE_LABEL_BRANDING_PLUGIN_URL . 'assets/images/themefusion.svg ) no-repeat center center;
				background-size: 18px;
			}';
		else :
			$style .= '.dashicons-avada-wlb:before {
				content: "\e936";
				padding: 8px;
				font-size: 18px;
				font-family: "icomoon";
				font-style: normal;
				font-weight: normal;
				font-variant: normal;
				text-transform: none;
				line-height: 1;
			}';
		endif;

		$style .= '</style>';

		echo $style; // WPCS: XSS ok.
	}

	/**
	 * Admin styles.
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function fusion_branding_admin_styles() {
		// Avada Options.
		$avada_options = isset( $this->settings['fusion_branding']['avada'] ) ? $this->settings['fusion_branding']['avada'] : [];
		$avada_menus           = isset( $avada_options['remove_main_menu_items'] ) ? $avada_options['remove_main_menu_items'] : [];
		$avada_sub_menus       = isset( $avada_options['remove_sticky_menu_items'] ) ? $avada_options['remove_sticky_menu_items'] : [];
		$avada_menus           = array_merge( $avada_menus, $avada_sub_menus );
		$maintenance_sub_menu  = [ 'patcher', 'plugins', 'changelog', 'support', 'status' ];
		$maintenance_counter   = 0;
		$layouts_sub_menu      = [ 'layouts', 'layout_sections' ];
		$layouts_counter        = 0;
		$sliders_sub_menu      = [ 'sliders', 'slides', 'slider_export' ];
		$slider_counter        = 0;
		$sub_menus             = array_merge( $maintenance_sub_menu, $layouts_sub_menu, $sliders_sub_menu );

		// WordPress Admin Options.
		$wp_admin_options = isset( $this->settings['fusion_branding']['wp_admin'] ) ? $this->settings['fusion_branding']['wp_admin'] : [];
		$admin_custom_css = isset( $wp_admin_options['admin_custom_css'] ) && '' !== $wp_admin_options['admin_custom_css'] ? $wp_admin_options['admin_custom_css'] : '';

		// Fusion Slider Options.
		$fusion_slider_options = isset( $this->settings['fusion_branding']['fusion_slider'] ) ? $this->settings['fusion_branding']['fusion_slider'] : [];

		$dashboard_widgets = isset( $wp_admin_options['remove_dashboard_widget'] ) && ! empty( $wp_admin_options['remove_dashboard_widget'] ) ? $wp_admin_options['remove_dashboard_widget'] : [];

		$admin_tab_selector = [];

		$style = '<style type="text/css">';

		foreach( $avada_menus as $menu_slug ) {
			if ( is_array( $menu_slug ) || empty( $menu_slug ) ) {
				continue;
			}
			if ( in_array( $menu_slug, $sub_menus ) ) {
				$style .= '.avada-dashboard .avada-db-menu-sub-item-' . str_replace( '_', '-', $menu_slug ) . ' { display: none; }';
			} else {
				$style .= '.avada-dashboard .avada-db-menu-item-' . str_replace( '_', '-', $menu_slug ) . ' { display: none; }';
			}


			if ( in_array( $menu_slug, $maintenance_sub_menu ) ) {
				$maintenance_counter++;
			}

			if ( in_array( $menu_slug, $layouts_sub_menu ) ) {
				$layouts_counter++;
			}

			if ( in_array( $menu_slug, $sliders_sub_menu ) ) {
				$slider_counter++;
			}
		}

		if ( 5 === $maintenance_counter ) {
			$style .= '.avada-dashboard .avada-db-menu-item-maintenance { display: none; }';
		}

		if ( 2 === $layouts_counter ) {
			$style .= '.avada-dashboard .avada-db-menu-item-layouts { display: none; }';
		}

		if ( 3 === $slider_counter ) {
			$style .= '.avada-dashboard .avada-db-menu-item-sliders { display: none; }';
		}

		// Update Avada admin menu icon logo.
		if ( isset( $avada_options['avada_icon_image'] ) && '' !== $avada_options['avada_icon_image'] ) :
			$style .= '#wpadminbar .avada-menu > .ab-item .ab-icon:before,
			#wpadminbar #wp-admin-bar-fb-edit > .ab-item:before,
			#toplevel_page_avada .dashicons-avada:before{';
			$style .= 'background-image: url( ' . $avada_options['avada_icon_image'] . ' ) !important;';
			$style .= 'content: "" !important;
				background-size: 20px 20px;
				background-repeat: no-repeat;
				background-position: center;
				width: 20px;
				height: 20px;';
			$style .= '}';
			$style .= '#wpadminbar .avada-menu > .ab-item .ab-icon:before, #wpadminbar #wp-admin-bar-fb-edit > .ab-item:before { top: 2px; }';

		elseif ( isset( $avada_options['admin_menu_dashicon'] ) && '' !== $avada_options['admin_menu_dashicon'] ) :
			$style .= '#wpadminbar #wp-admin-bar-fb-edit > .ab-item .ab-icon:before { top: 2px; }';
			$style .= '#wpadminbar > #wp-toolbar #wp-admin-bar-fb-edit > .ab-item::before { display: none; }';
		endif;

		// Update Avada logo.
		if ( isset( $avada_options['avada_logo_image'] ) && '' !== $avada_options['avada_logo_image'] ) :
			$style .= '.avada-dashboard .avada-db-header-main .avada-db-logo .avada-db-logo-icon,
				.fusion-builder-logo-wrapper .fusion-builder-logo.fusiona-avada-logo,
				.fusion-builder-toggle-buttons .button.button-large:before, #fusion_builder_switch:before,
				.mce-btn[aria-label="Avada Builder Element Generator"] i:before,
				#qt_content_fusion_shortcodes_text_mode,
				.postbox .hndle .avada-logo-wrapper .fusiona-avada-logo,
				#themefusion-news .fusiona-avada-logo,
				.avada-dashboard .avada-db-footer-company i {';
			$style .= 'background: url( ' . $avada_options['avada_logo_image'] . ' ) no-repeat center !important;';
			$style .= 'background-size: contain !important;';
			$style .= '}';
			$style .= '.avada-dashboard .avada-db-footer-company i {
				width: 15px;
				height: 15px;
				display: inline-block;';
			$style .= '}';

			// Backend builder toggle buttons.
			$style .= '.fusion-builder-toggle-buttons .button.button-large:before, #fusion_builder_switch:before {';
				$style .= 'content: "";
					background-size: 25px 25px !important;
					width: 20px;
					height: 20px;
					box-sizing: content-box;';
			$style .= '}';

			// TinyMCE visual editor button.
			$style .= '.mce-btn[aria-label="Avada Builder Element Generator"] i:before {';
				$style .= 'content: "";
					display: inline-block;
					background-size: 20px 20px !important;
					width: 20px;
					height: 20px;';
			$style .= '}';

			// TinyMCE text editor button.
			$style .= '#qt_content_fusion_shortcodes_text_mode {';
				$style .= 'background-size: 20px 20px !important;
					color: transparent;';
			$style .= '}';

			// Dashboard widget.
			$style .= '#themefusion-news fusion-dbw-version { margin: 0 0.5em; }';

			$style .= '.avada-dashboard .avada-db-header-main .avada-db-logo .avada-db-logo-icon:before,
				.avada-dashboard .avada-db-header-main:not(.awlb-header) .avada-db-logo .avada-db-logo-image,
				.fusion-builder-logo-wrapper .fusion-builder-logo.fusiona-avada-logo:before,
				.postbox .hndle .avada-logo-wrapper .fusiona-avada-logo:before,
				#themefusion-news .fusiona-avada-logo:before,
				#themefusion-news .fusion-dbw-logo img,
				.avada-dashboard .avada-db-footer-company i:before {
					display: none;
				}';
		endif;

		// Remove Dashboard footer.
		if ( isset( $avada_options['remove_dashboard_footer'] ) && $avada_options['remove_dashboard_footer'] ) :
			$style .= '.avada-db-footer { display: none; }';
		endif;

		// Update Avada Custom Branding admin menu icon logo.
		if ( isset( $avada_options['fusion_white_label_branding_icon_image'] ) && '' !== $avada_options['fusion_white_label_branding_icon_image'] ) :
			$style .= '#toplevel_page_avada-white-label-branding-admin .dashicons-avada-wlb {';
			$style .= 'background-image: url( ' . $avada_options['fusion_white_label_branding_icon_image'] . ' );';
			$style .= 'background-size: 20px 20px;
				background-repeat: no-repeat;
				background-position: center;';
			$style .= '}';
			$style .= '#toplevel_page_avada-white-label-branding-admin .dashicons-avada-wlb:before { display: none; }';
		endif;

		// Add styling for welcome panel.
		$style .= '.fusion-white-label-branding-welcome-panel-content.about-wrap {
		  max-width: 100%;
			margin-right: 25px;
			padding-bottom: 25px;
		}';
		$style .= 'h2.fusion-white-label-branding-welcome-panel-title {
		  margin: 0 20px;
		}';

		if ( in_array( 'gutenberg_panel', $dashboard_widgets, true ) ) {
			$style .= '#try-gutenberg-panel {
			  display: none !important;
			}';
		}

		// Update Avada Custom Branding admin menu icon logo.
		if ( isset( $avada_options['remove_changelog'] ) && '1' === $avada_options['remove_changelog'] ) {
			$style .= '.toplevel_page_avada .avada-admin-toggle .avada-admin-toggle-heading { display: none; }';
		}

		if ( '' !== $admin_custom_css ) {
			$style .= $admin_custom_css;
		}

		$style .= '</style>';

		echo $style; // WPCS: XSS ok.
	}

	/**
	 * Admin styles.
	 *
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function fusion_branding_frontend_styles() {
		// Avada Options.
		$avada_options = isset( $this->settings['fusion_branding']['avada'] ) ? $this->settings['fusion_branding']['avada'] : [];
		$style         = '';

		if ( isset( $avada_options['avada_icon_image'] ) && '' !== $avada_options['avada_icon_image'] ) :
			$style .= '#wpadminbar .avada-menu > .ab-item:before,';
			$style .= '#wpadminbar > #wp-toolbar #wp-admin-bar-fb-edit > .ab-item:before {';
			$style .= 'background: url( ' . $avada_options['avada_icon_image'] . ' ) no-repeat center !important;';
			$style .= 'background-size: auto !important;';
			$style .= 'content: "" !important;
						padding: 2px 0;
						width: 20px;
						height: 20px;
						background-size: contain !important;';
			$style .= '}';
		elseif ( isset( $avada_options['admin_menu_dashicon'] ) && '' !== $avada_options['admin_menu_dashicon'] ) :
			$style .= '.awlb-wpadminbar-icon:before { padding: 2px 0; }';
			$style .= '#wpadminbar > #wp-toolbar #wp-admin-bar-fb-edit > .ab-item:before { display: none; }';
		endif;

		if ( $style ) {
			echo '<style type="text/css" id="fusion-branding-style">' . $style . '</style>'; // WPCS: XSS ok.
		}
	}

	/**
	 * Live editor styles.
	 *
	 * @access public
	 * @since 1.2
	 * @return void
	 */
	public function change_live_editor_styles() {
		// Avada Options.
		$avada_options = isset( $this->settings['fusion_branding']['avada'] ) ? $this->settings['fusion_branding']['avada'] : [];

		// Update Avada logo.
		if ( isset( $avada_options['avada_logo_image'] ) && '' !== $avada_options['avada_logo_image'] ) :
			$style = '<style id="awlb-live-editor">';
			$style .= '.fusion-builder-live .fusion-builder-live-toolbar .fusion-toolbar-nav > li.fusion-branding .fusion-builder-logo-wrapper .fusiona-avada-logo {';
			$style .= 'background: url( ' . $avada_options['avada_logo_image'] . ' ) no-repeat center !important;
				background-size: contain !important;
				width: 30px;
				height: 30px;';
			$style .= '}';

			$style .= '.fusion-builder-live .fusion-builder-live-toolbar .fusion-toolbar-nav > li.fusion-branding .fusion-builder-logo-wrapper .fusiona-avada-logo:before {
					display: none;
				}';
			$style .= '</style>';

			echo $style;
		endif;
	}

	/**
	 * Add the title.
	 *
	 * @static
	 * @access protected
	 * @since 1.0
	 * @param string $title The title.
	 * @param string $page  The page slug.
	 */
	protected static function admin_tab( $title, $page ) {

		if ( isset( $_GET['page'] ) ) {
			$active_page = sanitize_text_field( wp_unslash( $_GET['page'] ) ); // WPCS: CSRF ok.
		}

		if ( $active_page === $page ) {
			$link       = 'javascript:void(0);';
			$active_tab = ' nav-tab-active';
		} else {
			$link       = 'admin.php?page=' . $page;
			$active_tab = '';
		}

		echo '<a href="' . esc_url( $link ) . '" class="nav-tab' . esc_attr( $active_tab ) . '">' . esc_html( $title ) . '</a>';

	}

	/**
	 * Change the post type labels for portfolio and faq.
	 *
	 * @static
	 * @access public
	 * @since 1.0
	 * @return void
	 */
	public function change_post_object_label() {
		global $wp_post_types, $wp_taxonomies;

		// Avada Options.
		$avada_options = isset( $this->settings['fusion_branding']['avada'] ) ? $this->settings['fusion_branding']['avada'] : [];
		$post_types    = isset( $avada_options['rename_admin_menu'] ) ? $avada_options['rename_admin_menu'] : [];

		// Fusion Slider Options.
		$fusion_slider_options = isset( $this->settings['fusion_branding']['fusion_slider'] ) ? $this->settings['fusion_branding']['fusion_slider'] : [];

		if ( ! class_exists( 'FusionCore_Plugin' ) ) {
			return;
		}

		// Change taxonomy labels for slide-page.
		$sliders_menu_label = isset( $fusion_slider_options['add_edit_sliders_label'] ) ? $fusion_slider_options['add_edit_sliders_label'] : '';
		$sliders_label      = isset( $fusion_slider_options['sliders_label'] ) ? $fusion_slider_options['sliders_label'] : '';

		if ( isset( $wp_taxonomies['slide-page'] ) ) {
			$slider_labels = $wp_taxonomies['slide-page']->labels;

			if ( '' !== $sliders_menu_label ) {
				$slider_labels->menu_name = $sliders_menu_label;
			}

			if ( '' !== $sliders_label ) {
				$slider_labels->name      = $sliders_label;
				$slider_labels->all_items = $sliders_label;

				/* translators: The taxonomy (slide-page) name. */
				$slider_labels->view_item = sprintf( esc_html__( 'View %s', 'fusion-white-label-branding' ), $sliders_label );

				/* translators: The taxonomy (slide-page) name. */
				$slider_labels->add_new_item = sprintf( esc_html__( 'Add New %s', 'fusion-white-label-branding' ), $sliders_label );

				/* translators: The taxonomy (slide-page) name. */
				$slider_labels->edit_item = sprintf( esc_html__( 'Edit %s', 'fusion-white-label-branding' ), $sliders_label );
			}

			unset( $post_types['slide-page'] );
		}

		// Change taxonomy labels for slide post type.
		$slides_menu_label = isset( $fusion_slider_options['add_edit_slide_label'] ) ? $fusion_slider_options['add_edit_slide_label'] : '';
		$slides_label      = isset( $fusion_slider_options['admin_menu_label'] ) ? $fusion_slider_options['admin_menu_label'] : '';

		if ( isset( $wp_post_types['slide'] ) ) {
			$slide_labels = $wp_post_types['slide']->labels;

			if ( '' !== $slides_menu_label ) {
				$slide_labels->all_items = $slides_menu_label;
			}

			if ( '' !== $slides_label ) {
				$slide_labels->name = $slides_label;

				/* translators: The taxonomy (slide-page) name. */
				$slide_labels->view_item = sprintf( esc_html__( 'View %s', 'fusion-white-label-branding' ), $slides_label );

				/* translators: The taxonomy (slide-page) name. */
				$slide_labels->add_new_item = sprintf( esc_html__( 'Add New %s', 'fusion-white-label-branding' ), $slides_label );

				/* translators: The taxonomy (slide-page) name. */
				$slide_labels->edit_item = sprintf( esc_html__( 'Edit %s', 'fusion-white-label-branding' ), $slides_label );
			}
		}

		// Change taxonomy labels for skills.
		$skills_label = isset( $post_types['skills'] ) ? $post_types['skills'] : '';

		if ( isset( $wp_taxonomies['portfolio_skills'] ) && '' !== $skills_label ) {
			$skill_labels            = $wp_taxonomies['portfolio_skills']->labels;
			$skill_labels->menu_name = $skills_label;
			$skill_labels->name      = $skills_label;

			/* translators: The taxonomy (skills) name. */
			$skill_labels->view_item = sprintf( esc_html__( 'View %s', 'fusion-white-label-branding' ), $skills_label );

			/* translators: The taxonomy (skills) name. */
			$skill_labels->add_new_item = sprintf( esc_html__( 'Add New %s', 'fusion-white-label-branding' ), $skills_label );

			/* translators: The taxonomy (skills) name. */
			$skill_labels->edit_item = sprintf( esc_html__( 'Edit %s', 'fusion-white-label-branding' ), $skills_label );
			unset( $post_types['skills'] );
		}

		// Change taxonomy labels for tags.
		$tags_label = isset( $post_types['tags'] ) ? $post_types['tags'] : '';

		if ( isset( $wp_taxonomies['portfolio_tags'] ) && '' !== $tags_label ) {
			$tag_labels            = $wp_taxonomies['portfolio_tags']->labels;
			$tag_labels->menu_name = $tags_label;
			$tag_labels->name      = $tags_label;

			/* translators: The taxonomy name. */
			$tag_labels->view_item = sprintf( esc_html__( 'View %s', 'fusion-white-label-branding' ), $tags_label );

			/* translators: The taxonomy name. */
			$tag_labels->add_new_item = sprintf( esc_html__( 'Add New %s', 'fusion-white-label-branding' ), $tags_label );

			/* translators: The taxonomy name. */
			$tag_labels->edit_item = sprintf( esc_html__( 'Edit %s', 'fusion-white-label-branding' ), $tags_label );
			unset( $post_types['tags'] );
		}

		foreach ( $post_types as $post_type => $label ) {

			if ( '' !== $label && isset( $wp_post_types[ 'avada_' . $post_type ] ) ) {
				// Change post type labels for portfolio and faq.
				$labels                = $wp_post_types[ 'avada_' . $post_type ]->labels;
				$labels->menu_name     = $label;
				$labels->name          = $label;
				$labels->singular_name = $label;

				/* translators: The post-type name. */
				$labels->add_new = sprintf( esc_html__( 'Add %s', 'fusion-white-label-branding' ), $label );

				/* translators: The post-type name. */
				$labels->add_new_item = sprintf( esc_html__( 'Add %s', 'fusion-white-label-branding' ), $label );

				/* translators: The post-type name. */
				$labels->edit_item = sprintf( esc_html__( 'Edit %s', 'fusion-white-label-branding' ), $label );
				$labels->new_item  = $label;

				/* translators: The post-type name. */
				$labels->all_items = sprintf( esc_html__( 'All %s', 'fusion-white-label-branding' ), $label );

				/* translators: The post-type name. */
				$labels->view_item = sprintf( esc_html__( 'View %s', 'fusion-white-label-branding' ), $label );

				/* translators: The post-type name. */
				$labels->search_items = sprintf( esc_html__( 'Search %s', 'fusion-white-label-branding' ), $label );

				/* translators: The post-type name. */
				$labels->not_found = sprintf( esc_html__( 'No %s found', 'fusion-white-label-branding' ), $label );

				/* translators: The post-type name. */
				$labels->not_found_in_trash = sprintf( esc_html__( 'No %s found in Trash', 'fusion-white-label-branding' ), $label );

				if ( isset( $wp_taxonomies[ $post_type . '_category' ] ) ) {

					// Change taxonomy labels for portfolio and faq.
					$taxonomy_labels = $wp_taxonomies[ $post_type . '_category' ]->labels;

					/* translators: The post-type name. */
					$taxonomy_labels->menu_name = sprintf( esc_html__( '%s Categories', 'fusion-white-label-branding' ), $label );

					/* translators: The post-type name. */
					$taxonomy_labels->name = sprintf( esc_html__( '%s Categories', 'fusion-white-label-branding' ), $label );
				}
			}
		}
	}

	/**
	 * Update Avada Welcome Screen title.
	 *
	 * @access public
	 * @since 1.0
	 * @param string $default_content The default content.
	 * @return string $welcome_screen_title Welcome Screen title.
	 */
	public function avada_welcome_screen_title( $default_content ) {
		$avada_options        = isset( $this->settings['fusion_branding']['avada'] ) ? $this->settings['fusion_branding']['avada'] : [];
		$welcome_screen_title = isset( $avada_options['welcome_screen_title'] ) && ! empty( $avada_options['welcome_screen_title'] ) ? $avada_options['welcome_screen_title'] : $default_content;

		return $welcome_screen_title;
	}

	/**
	 * Update Avada Welcome Screen about text.
	 *
	 * @access public
	 * @since 1.0
	 * @param string $default_content The default content.
	 * @return string $welcome_screen_about_text Welcome Screen about text.
	 */
	public function avada_welcome_screen_about_text( $default_content ) {
		$avada_options             = isset( $this->settings['fusion_branding']['avada'] ) ? $this->settings['fusion_branding']['avada'] : [];
		$welcome_screen_about_text = isset( $avada_options['welcome_screen_about_text'] ) && ! empty( $avada_options['welcome_screen_about_text'] ) ? stripslashes( $avada_options['welcome_screen_about_text'] ) : $default_content;

		return $welcome_screen_about_text;
	}

	/**
	 * Update Avada Welcome Screen content.
	 *
	 * @access public
	 * @since 1.0
	 * @param string $default_content The default content.
	 * @return string $welcome_screen_content Welcome Screen content.
	 */
	public function avada_welcome_screen_content( $default_content ) {
		$avada_options          = isset( $this->settings['fusion_branding']['avada'] ) ? $this->settings['fusion_branding']['avada'] : [];
		$welcome_screen_content = $default_content;

		if ( isset( $avada_options['welcome_screen_content'] ) && ! empty( $avada_options['welcome_screen_content'] ) ) {
			$welcome_screen_content = stripslashes( $avada_options['welcome_screen_content'] ) . '</div></div><style type="text/css">.avada-db-welcome-setup .avada-db-more-info { display: none !important; }</style>';
		}

		return $welcome_screen_content;
	}


	/**
	 * Disable Avada Dashboard Additional Resources.
	 *
	 * @access public
	 * @since 1.2
	 * @param bool $should_display Whether additional resources links should show.
	 * @return bool $welcome_screen_content Whether additional resources links should show.
	 */
	public function avada_admin_display_additional_resources( $should_display ) {
		if ( ! empty( $this->settings['fusion_branding']['avada']['remove_dashboard_additional_resources'] ) && $this->settings['fusion_branding']['avada']['remove_dashboard_additional_resources'] ) {
			return false;
		}

		return $should_display;
	}

	/**
	 * Update Avada version text.
	 *
	 * @access public
	 * @since 1.2
	 * @param string $version_text The version text.
	 * @return string $The updated version text.
	 */
	public function change_avada_db_version( $version_text ) {
		if ( ! empty( $this->settings['fusion_branding']['avada']['version_number_text'] ) ) {
			$version_text = $this->settings['fusion_branding']['avada']['version_number_text'];
		}

	return $version_text;
	}

	/**
	 * Check if admins can view branding changes.
	 *
	 * @access public
	 * @since 1.0
	 * @return bool
	 */
	public function user_can_see_changes() {
		// WordPress admin settings.
		$wp_admin_options = isset( $this->settings['fusion_branding']['wp_admin'] ) ? $this->settings['fusion_branding']['wp_admin'] : [];
		$apply_changes    = isset( $wp_admin_options['apply_changes_for_admin'] ) ? $wp_admin_options['apply_changes_for_admin'] : true;

		$current_user = wp_get_current_user();

		if ( in_array( 'administrator', $current_user->roles, true ) ) {
			return $apply_changes;
		} else {
			return true;
		}
	}

	/**
	 * Download the settings json.
	 *
	 * @access public
	 * @since 1.1
	 * @return void
	 */
	public function export_settings() {

		if ( ! isset( $_GET['action'] ) || 'export_white_label_settings' !== $_GET['action'] ) { // WPCS: CSRF ok.
			return;
		}

		// @codingStandardsIgnoreLine WordPress.VIP.ValidatedSanitizedInput.InputNotSanitized
		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'] ) ) {
			die();
		}

		$section_id = '';

		if ( isset( $_GET['section_id'] ) ) {
			$section_id = sanitize_key( wp_unslash( $_GET['section_id'] ) );
		}

		$settings         = get_option( 'fusion_branding_settings', [] );
		$section_settings = ( 'all' !== $section_id ) ? $settings['fusion_branding'][ $section_id ] : $settings;

		// Assing the settings to a variable to export.
		$export                = [];
		$export[ $section_id ] = $section_settings;

		header( 'Content-Description: File Transfer' );
		header( 'Content-type: application/txt' );
		header( 'Content-Disposition: attachment; filename="avada-custom-branding-settings-' . $section_id . '-' . date( 'd-m-Y' ) . '.json"' );
		header( 'Content-Transfer-Encoding: binary' );
		header( 'Expires: 0' );
		header( 'Cache-Control: must-revalidate' );
		header( 'Pragma: public' );

		echo wp_json_encode( $export );
		die();
	}

	/**
	 * Import the settings from provided json.
	 *
	 * @access public
	 * @since 1.1
	 * @return void
	 */
	public function import_settings() {

		// @codingStandardsIgnoreLine
		if ( ! isset( $_POST['action'] ) || 'import_white_label_settings' !== $_POST['action'] || ! isset( $_POST['json_data'] ) ) {
			return;
		}

		// Check for nonce validation.
		check_ajax_referer( 'branding_settings_import', 'security' );

		$decoded_settings = json_decode( wp_unslash( $_POST['json_data'] ), true );
		$section_id       = key( $decoded_settings );
		$section_settings = $decoded_settings[ $section_id ];

		switch ( $section_id ) {

			case 'all':
				$settings = $section_settings;
				break;

			case 'wp_admin':
			case 'avada':
			case 'login_screen':
				$settings = get_option( 'fusion_branding_settings', [] );
				$settings['fusion_branding'][ $section_id ] = $section_settings;
				break;
		}

		$result = update_option( 'fusion_branding_settings', $settings );

		echo esc_attr( $result );

		die();
	}

	/**
	 * Reset settings.
	 *
	 * @access public
	 * @since 1.1
	 * @return void
	 */
	public function reset_settings() {

		if ( ! isset( $_GET['action'] ) || 'reset-branding-settings' !== $_GET['action'] ) { // WPCS: CSRF ok.
			return;
		}

		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( wp_unslash( $_GET['_wpnonce'] ) ) ) { // WPCS: sanitization ok.
			die();
		}

		$section_id = '';

		if ( isset( $_GET['section_id'] ) ) {
			$section_id = sanitize_key( wp_unslash( $_GET['section_id'] ) );
		}

		$settings = get_option( 'fusion_branding_settings', [] );

		switch ( $section_id ) {

			case 'all':
				$settings['fusion_branding'] = [];
				break;

			case 'wp_admin':
			case 'avada':
			case 'login_screen':
				$settings['fusion_branding'][ $section_id ] = [];
				break;
		}

		update_option( 'fusion_branding_settings', $settings );

		// @codingStandardsIgnoreLine
		wp_safe_redirect( esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) );

		die();
	}

	/**
	 * Reset the admin social media links..
	 *
	 * @access public
	 * @since 1.1.5
	 * @return string The changed admin social media links.
	 */
	public function change_admin_social_media_links( $social_media_markup ) {
		$social_media_links = isset( $this->settings['fusion_branding']['avada']['reset_social_media'] ) ? $this->settings['fusion_branding']['avada']['reset_social_media'] : [];

		if ( ! empty( $social_media_links ) ) {
			$networks = [
				'facebook'  => 'https://www.facebook.com/ThemeFusion-101565403356430/',
				'twitter'   => 'https://twitter.com/theme_fusion',
				'instagram' => 'https://www.instagram.com/themefusion/',
				'youtube'   => 'https://www.youtube.com/channel/UC_C7uAOAH9RMzZs-CKCZ62w',
			];

			foreach( $social_media_links as $network => $link ) {
				if ( '#' === $link ) {
					$social_media_markup = str_replace( $networks[ $network ] . '"', $networks[ $network ] . '" style="display:none;"', $social_media_markup );
				} elseif( '' !== $link ) {
					$social_media_markup = str_replace( $networks[ $network ], $link, $social_media_markup );
				}
			}
		}

		return $social_media_markup;
	}
}
