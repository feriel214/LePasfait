<?php
/**
 * Convert Plus
 *
 * @since  1.0.0
 * @package Convert_Plus.
 */

if ( ! class_exists( 'Convert_Plug' ) ) {
	// include Smile_Framework class.
	require_once CP_BASE_DIR . '/framework/class-smile-framework.php';

	/**
	 * Class Convert_plug.
	 */
	class Convert_Plug extends Smile_Framework {

		/**
		 * $options array.
		 *
		 * @var array
		 */
		public static $options = array();

		/**
		 * $paths array.
		 *
		 * @var array
		 */
		public $paths = array();

		/**
		 * $cp_dev_mode var for dev mode.
		 *
		 * @var boolean
		 */
		public static $cp_dev_mode = false;

		/**
		 * $cp_editor_enable for enabling editor.
		 *
		 * @var boolean
		 */
		public static $cp_editor_enable = false;

		/**
		 * Constructor.
		 */
		public function __construct() {
			// Fall back support for multi fields.
			add_action( 'wp_loaded', array( $this, 'cp_access_capabilities' ), 1 );
			add_action( 'wp_loaded', array( $this, 'cp_set_options' ), 1 );

			$this->paths            = wp_upload_dir();
			$this->paths['fonts']   = 'smile_fonts';
			$this->paths['fonturl'] = set_url_scheme( trailingslashit( $this->paths['baseurl'] ) . $this->paths['fonts'] );

			add_action( 'admin_menu', array( $this, 'add_admin_menu' ), 99 );
			add_action( 'admin_menu', array( $this, 'add_admin_menu_rename' ), 9999 );
			add_filter( 'custom_menu_order', array( $this, 'cp_submenu_order' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_front_scripts' ), 10 );
			add_action( 'admin_print_scripts', array( $this, 'cp_admin_css' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'cp_admin_scripts' ), 100 );
			add_filter( 'bsf_core_style_screens', array( $this, 'cp_add_core_styles' ) );
			add_action( 'admin_head', array( $this, 'cp_custom_css' ) );
			add_action( 'admin_init', array( $this, 'cp_redirect_on_activation' ), 1 );
			add_filter( 'plugin_action_links_' . CP_DIR_FILE_NAME, array( $this, 'cp_action_links' ), 10, 5 );
			add_action( 'wp_ajax_cp_display_preview_modal', array( $this, 'cp_display_preview_modal' ) );
			add_action( 'wp_ajax_cp_display_preview_info_bar', array( $this, 'cp_display_preview_info_bar' ) );
			add_action( 'wp_ajax_cp_display_preview_slide_in', array( $this, 'cp_display_preview_slide_in' ) );
			add_action( 'plugins_loaded', array( $this, 'cp_load_textdomain' ) );
			add_filter( 'the_content', array( $this, 'cp_add_content' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'smile_frosty_scripts_from_core' ), 100 );

			// de register scripts.
			add_action( 'admin_enqueue_scripts', array( $this, 'cp_dergister_scripts' ), 100 );

			require_once CP_BASE_DIR . '/admin/ajax-actions.php';
			require_once CP_BASE_DIR . '/framework/class-add-convertplug-widget.php';
			add_action( 'widgets_init', 'load_convertplug_widget' );

			// minimum requirement for PHP version.
			$php = '5.4';

			// If current version is less than minimum requirement, display admin notice.
			if ( version_compare( PHP_VERSION, $php, '<' ) ) {
				add_action( 'admin_notices', 'cp_php_version_notice' );
			}

			// Check if PharData extension is available or not?
			$cp_show_phardata_notice = get_option( 'cp_show_phardata_notice' );

			if ( 'no' !== $cp_show_phardata_notice && ( ! class_exists( 'PharData' ) ) ) {
				add_action( 'admin_notices', 'cp_phardata_notice' );
			}

			$data = get_option( 'convert_plug_debug' );

			$display_debug_info = isset( $data['cp-display-debug-info'] ) ? $data['cp-display-debug-info'] : 0;

			if ( $display_debug_info ) {
				add_action( 'admin_footer', array( $this, 'cp_add_debug_info' ) );
			}

			// conflict due to imagify plugin.
			add_action( 'wp_print_scripts', array( $this, 'cp_dequeue_script_imagify' ), 999 );

			self::$cp_dev_mode = isset( $data['cp-dev-mode'] ) ? $data['cp-dev-mode'] : '';

			// skip registration menu.
			add_filter( 'bsf_skip_braisntorm_menu', array( $this, 'cp_skip_brainstorm_menu' ) );

			// Add popup license form on plugin list page.
			add_action( 'plugin_action_links_' . CP_DIR_FILE_NAME, array( $this, 'cp_license_form_and_links' ) );
			add_action( 'network_admin_plugin_action_links_' . CP_DIR_FILE_NAME, array( $this, 'cp_license_form_and_links' ) );

			// change registration page URL.
			add_action( 'bsf_registration_page_url_14058953', array( $this, 'cp_get_registration_page_url' ) );

			// Css Asynchronous Loading.

			$data = get_option( 'convert_plug_settings' );

			if ( isset( $data['cp-load-syn'] ) && '1' === $data['cp-load-syn'] ) {
				add_action( 'wp_head', array( $this, 'cp_load_css_async' ), 7 );
				add_filter( 'style_loader_tag', array( $this, 'cp_link_to_load_css_script' ), 999, 3 );
			}
		}

		/**
		 * Fucntion Name: cp_skip_brainstorm_menu Skip BSF menu from dashboard.
		 *
		 * @param  array $products array of products.
		 * @return array           array of products.
		 * @since 3.1.0
		 */
		public function cp_skip_brainstorm_menu( $products ) {

			$products = array(
				14058953,
				'connects-contact-form-7',
				'connects-woocommerce',
				'connects-ontraport',
				'convertplug-vc',
				'connects-wp-registration-form',
				'connects-wp-comment-form',
				'connects-totalsend',
				'connects-sendreach',
				'connects-ontraport',
				'connects-convertfox',
			);

			return $products;
		}

		/**
		 * Show action links on the plugin screen.
		 *
		 * @param   mixed $links Plugin Action links.
		 * @return  array        Filtered plugin action links.
		 */
		public function cp_license_form_and_links( $links = array() ) {

			if ( function_exists( 'get_bsf_inline_license_form' ) ) {
				$args = array(
					'product_id'              => 14058953,
					'popup_license_form'      => true,
					'bsf_license_allow_email' => true,
				);
				return get_bsf_inline_license_form( $links, $args, 'envato' );
			}

			return $links;
		}

		/**
		 * Get registration page url for addon.
		 *
		 * @since  1.0.0
		 * @return String URL of the licnense registration page.
		 */
		public function cp_get_registration_page_url() {
			$url = admin_url( 'plugins.php?bsf-inline-license-form=14058953' );

			if ( is_multisite() ) {
				$url = network_admin_url( 'plugins.php?bsf-inline-license-form=14058953' );
			}

			return $url;
		}

		/**
		 * Function Name: cp_load_css_async.
		 * Function Description: load_css_async.
		 */
		public function cp_load_css_async() {
			$scripts = '<script>function cpLoadCSS(e,t,n){"use strict";var i=window.document.createElement("link"),o=t||window.document.getElementsByTagName("script")[0];return i.rel="stylesheet",i.href=e,i.media="only x",o.parentNode.insertBefore(i,o),setTimeout(function(){i.media=n||"all"}),i}</script>';

			echo $scripts; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Set options on load of WordPress.
		 *
		 * @since 2.3.2
		 */
		public function cp_set_options() {
			update_option( 'cp_is_displayed_debug_info', false );
		}

		/**
		 * Add Convert Plus access capabilities to user roles.
		 *
		 * @since 2.2.0
		 */
		public function cp_access_capabilities() {
			if ( is_user_logged_in() ) {
				if ( current_user_can( 'manage_options' ) ) {
					global $wp_roles;
					$wp_roles_data = $wp_roles->get_names();
					$roles         = false;

					$cp_settings = get_option( 'convert_plug_settings' );

					if ( isset( $cp_settings['cp-access-role'] ) ) {
						$roles = explode( ',', $cp_settings['cp-access-role'] );
					}

					if ( ! $roles ) {
						$roles = array();
					}

					// give access to administrator.
					$roles[] = 'administrator';

					foreach ( $wp_roles_data as $key => $value ) {
						$role = get_role( $key );

						if ( in_array( $key, $roles ) ) {
							$role->add_cap( 'access_cp' );
						} else {
							$role->remove_cap( 'access_cp' );
						}
					}
				}
			}
		}

		/**
		 * Fuction Name: cp_add_content Add a class at the end of the post for after content trigger.
		 *
		 * @param  string $content content of the post.
		 * @return mixed          content of the post.
		 * @since 1.0.3
		 */
		public function cp_add_content( $content ) {
			if ( is_single() || is_page() ) {
				$content_str_array = cp_display_style_inline();
				$enable_after_post = apply_filters( 'cplus_enable_after_post', true );

				if ( $enable_after_post ) {
					$content .= '<span class="cp-load-after-post"></span>';
				}
				$content  = $content_str_array[0] . $content;
				$content .= $content_str_array[1];
			}
			return $content;
		}

		/**
		 * Load plugin text domain.
		 *
		 * @since 1.0.0
		 */
		public function cp_load_textdomain() {
			load_plugin_textdomain( 'smile', false, CP_DIR_NAME . '/lang' );
		}

		/**
		 * Handle style preview ajax request for modal.
		 *
		 * @since 1.0.0
		 */
		public function cp_display_preview_modal() {
			require_once CP_BASE_DIR . '/modules/modal/style-preview-ajax.php';
			die();
		}

		/**
		 * Handle style preview ajax request for info bar.
		 *
		 * @since 1.0.0
		 */
		public function cp_display_preview_info_bar() {
			require_once CP_BASE_DIR . '/modules/info_bar/style-preview-ajax.php';
			die();
		}

		/**
		 * Ajax Callback for slide in style preview.
		 *
		 * @since 1.0.0
		 */
		public function cp_display_preview_slide_in() {
			require_once CP_BASE_DIR . '/modules/slide_in/style-preview-ajax.php';
			die();
		}

		/**
		 * Adds settings link in plugins action.
		 *
		 * @param  array  $actions action array.
		 * @param  string $plugin_file filenames.
		 * @since 1.0
		 * @return array array of parameter.
		 */
		public function cp_action_links( $actions, $plugin_file ) {
			static $plugin;

			if ( ! isset( $plugin ) ) {
				$plugin = CP_DIR_FILE_NAME;
			}
			if ( $plugin === $plugin_file ) {
				$settings = array( 'settings' => '<a href="' . admin_url( 'admin.php?page=' . CP_PLUS_SLUG . '&view=settings' ) . '">Settings</a>' );
				$actions  = array_merge( $settings, $actions );
			}
			return $actions;
		}

		/**
		 * Enqueue scripts and styles for insert shortcode popup
		 *
		 * @param  array $hook action array.
		 * @since 1.0
		 */
		public function cp_admin_scripts( $hook ) {
			// Store all global CSS variables.
			wp_enqueue_script( 'convert-plus-css-generator', CP_PLUGIN_URL . 'framework/assets/js/css-generator.js', array( 'jquery' ), CP_VERSION, false );

			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_style( 'wp-color-picker' );

			$data = get_option( 'convert_plug_debug' );

			if ( false !== strpos( $hook, CP_PLUS_SLUG ) ) {
				wp_enqueue_style( 'convert-plus-connects-icon', CP_PLUGIN_URL . 'modules/assets/css/connects-icon.css', array(), CP_VERSION );
			}

			if ( isset( $_REQUEST['cp_admin_page_nonce'] ) && ! wp_verify_nonce( $_REQUEST['cp_admin_page_nonce'], 'cp_admin_page' ) ) {
				wp_die( 'No direct script access allowed!' );
			}

			if ( isset( $_GET['hidemenubar'] ) ) {
				// Common File for Convert Plus.
				wp_enqueue_script( 'convert-plus-ckeditor', CP_PLUGIN_URL . 'modules/assets/js/ckeditor/ckeditor.js', array(), CP_VERSION, false );
				wp_enqueue_script( 'convert-plus-contact-form', CP_PLUGIN_URL . 'modules/assets/js/convertplug.js', array( 'jquery', 'convert-plus-ckeditor' ), CP_VERSION, false );

				wp_enqueue_style( 'convert-plus-perfect-scroll-style', CP_PLUGIN_URL . 'admin/assets/css/perfect-scrollbar.min.css', array(), CP_VERSION );
				wp_enqueue_script( 'convert-plus-perfect-scroll-js', CP_PLUGIN_URL . 'admin/assets/js/perfect-scrollbar.jquery.js', array( 'jquery' ), CP_VERSION, false );
			}

			if ( isset( $_GET['style-view'] ) && ( 'edit' === $_GET['style-view'] || 'variant' === $_GET['style-view'] ) ) {
				wp_enqueue_script( 'convert-plus-perfect-scroll-js', CP_PLUGIN_URL . 'admin/assets/js/perfect-scrollbar.jquery.js', array( 'jquery' ), CP_VERSION, false );
				wp_enqueue_style( 'convert-plus-perfect-scroll-style', CP_PLUGIN_URL . 'admin/assets/css/perfect-scrollbar.min.css', array(), CP_VERSION );
				// Fix Bridge theme JS conflict with Customizer.
				wp_dequeue_script( 'jquery-ui-accordion' );

				// ace editor files.
				if ( ! isset( $_GET['hidemenubar'] ) ) {
					wp_enqueue_script( 'convert-plus-ace', CP_PLUGIN_URL . 'admin/assets/js/ace.js', array( 'jquery' ), CP_VERSION, false );
					wp_enqueue_script( 'convert-plus-ace-mode-css', CP_PLUGIN_URL . 'admin/assets/js/mode-css.js', array( 'jquery' ), CP_VERSION, false );
					wp_enqueue_script( 'convert-plus-ace-mode-xml', CP_PLUGIN_URL . 'admin/assets/js/mode-xml.js', array( 'jquery' ), CP_VERSION, false );
					wp_enqueue_script( 'convert-plus-ace-worker-css', CP_PLUGIN_URL . 'admin/assets/js/worker-css.js', array( 'jquery' ), CP_VERSION, false );
					wp_enqueue_script( 'convert-plus-ace-worker-xml', CP_PLUGIN_URL . 'admin/assets/js/worker-xml.js', array( 'jquery' ), CP_VERSION, false );
				}
			}

			if ( isset( $_GET['style-view'] ) && 'new' === sanitize_text_field( $_GET['style-view'] ) ) {
				wp_enqueue_script( 'convert-plug-display-popup-preview', CP_PLUGIN_URL . 'modules/assets/js/popup-preview.js', array( 'jquery' ), CP_VERSION, true );
				if ( CP_PLUS_SLUG . '_page_smile-modal-designer' === $hook ) {
					wp_enqueue_script( 'convert-plug-modal-style-preview', CP_PLUGIN_URL . 'modules/modal/assets/js/style-preview.js', array(), CP_VERSION, true );
				}
				if ( CP_PLUS_SLUG . '_page_smile-info_bar-designer' === $hook ) {
					wp_enqueue_script( 'convert-plug-info-bar-style-preview', CP_PLUGIN_URL . 'modules/info_bar/assets/js/style-preview.js', array(), CP_VERSION, true );
				}
				if ( CP_PLUS_SLUG . '_page_smile-slide_in-designer' === $hook ) {
					wp_enqueue_script( 'convert-plug-slide-in-style-preview', CP_PLUGIN_URL . 'modules/slide_in/assets/js/style-preview.js', array(), CP_VERSION, true );
				}
			}
			if ( CP_PLUS_SLUG . '_page_contact-manager' === $hook ) {
				wp_enqueue_style( 'convert-plus-contacts', CP_PLUGIN_URL . 'admin/contacts/css/cp-contacts.css', array(), CP_VERSION );
				if ( isset( $_GET['view'] ) && 'analytics' === $_GET['view'] ) {
					wp_enqueue_script( 'bsf-charts-js', CP_PLUGIN_URL . 'admin/assets/js/chart.js', array(), CP_VERSION, true );
					wp_enqueue_script( 'bsf-charts-bar-js', CP_PLUGIN_URL . 'admin/assets/js/chart.bar.js', array(), CP_VERSION, true );
					wp_enqueue_script( 'bsf-charts-donut-js', CP_PLUGIN_URL . 'admin/assets/js/chart.donuts.js', array(), CP_VERSION, true );
					wp_enqueue_script( 'bsf-charts-line-js', CP_PLUGIN_URL . 'admin/assets/js/Chart.Line.js', array(), CP_VERSION, true );
					wp_enqueue_script( 'bsf-charts-polararea-js', CP_PLUGIN_URL . 'admin/assets/js/Chart.PolarArea.js', array(), CP_VERSION, true );
					wp_enqueue_script( 'bsf-charts-scripts', CP_PLUGIN_URL . 'admin/contacts/js/connect-analytics.js', array(), CP_VERSION, true );
				}
				wp_enqueue_script( 'convert-plug-contact-scripts', CP_PLUGIN_URL . 'admin/contacts/js/connect-contacts.js', array(), CP_VERSION, true );
				$cplus_contact_nonce = array(
					'cp_is_list_assigned' => wp_create_nonce( 'cp_is_list_assigned' ),
					'cp_delete_list'      => wp_create_nonce( 'cp-delete-list' ),
				);
				wp_localize_script( 'convert-plug-contact-scripts', 'cplus_contact_nonce', $cplus_contact_nonce );

				$nonce_object = array(
					'analytics_nonce' => wp_create_nonce( 'cp_contacts_nonce' ),
				);
				wp_localize_script( 'bsf-charts-scripts', 'analytics_nonce', $nonce_object );

				wp_enqueue_style( 'convert-plus-css-select2', CP_PLUGIN_URL . 'admin/assets/select2/select2.min.css', array(), CP_VERSION );
				wp_enqueue_script( 'convert-plus-select2', CP_PLUGIN_URL . 'admin/assets/select2/select2.min.js', false, '2.4.0.3', true );

				// sweet alert.
				wp_enqueue_script( 'convert-plus-swal-js', CP_PLUGIN_URL . 'admin/assets/js/sweetalert.min.js', array(), CP_VERSION, true );
				wp_enqueue_style( 'convert-plus-swal-style', CP_PLUGIN_URL . 'admin/assets/css/sweetalert.css', array(), CP_VERSION );
			}

			if ( CP_PLUS_SLUG . '_page_bsf-maxmind-database-manager' === $hook ) {
				wp_enqueue_script( 'bsf-maxmind-scripts', CP_PLUGIN_URL . '/framework/assets/js/maxmind-database.js', array(), CP_VERSION, true );
			}

			if ( CP_PLUS_SLUG . '_page_bsf-google-recaptcha-manager' === $hook ) {
				wp_enqueue_script( 'convert-plug-google-recaptcha', CP_PLUGIN_URL . 'admin/assets/js/cplug-google-recaptcha.js', array(), CP_VERSION, true );
			}

			if ( ! isset( $_GET['hidemenubar'] ) && false !== strpos( $hook, CP_PLUS_SLUG ) ) {
				if ( ( isset( $_GET['variant-test'] ) && 'edit' !== $_GET['variant-test'] )
					|| ( isset( $_GET['style-view'] ) && 'edit' !== $_GET['style-view'] )
					|| ( isset( $_GET['style-view'] ) && 'edit' === $_GET['style-view'] && isset( $_GET['theme'] ) && 'countdown' === $_GET['theme'] )
					|| ! isset( $_GET['style-view'] ) ) {
					wp_enqueue_style( 'convert-plus-bootstrap-datetimepicker', CP_PLUGIN_URL . 'modules/assets/css/bootstrap-datetimepicker.min.css', array(), CP_VERSION );

					wp_enqueue_script( 'convert-plus-moment-with-locales', CP_PLUGIN_URL . 'modules/assets/js/moment-with-locales.js', array(), CP_VERSION, true );

					if ( '1' === self::$cp_dev_mode ) {
						wp_enqueue_script( 'convert-plus-bootstrap-datetimepicker', CP_PLUGIN_URL . 'modules/assets/js/bootstrap-datetimepicker.js', array(), CP_VERSION, true );
					} else {
						wp_enqueue_script( 'convert-plus-bootstrap-datetimepicker', CP_PLUGIN_URL . 'modules/assets/js/bootstrap-datetimepicker.min.js', array(), CP_VERSION, true );
					}
				}

				// sweet alert.
				wp_enqueue_script( 'convert-plus-swal-js', CP_PLUGIN_URL . 'admin/assets/js/sweetalert.min.js', array(), CP_VERSION, true );
				wp_enqueue_style( 'convert-plus-swal-style', CP_PLUGIN_URL . 'admin/assets/css/sweetalert.css', array(), CP_VERSION );
			}

			// count down style scripts.
			if ( isset( $_GET['theme'] ) && 'countdown' === $_GET['theme'] ) {
				wp_register_style( 'convert-plus-countdown-style', CP_PLUGIN_URL . 'modules/assets/css/jquery.countdown.css', array(), CP_VERSION );
				wp_register_script( 'convert-plus-counter-plugin-js', CP_PLUGIN_URL . 'modules/assets/js/jquery.plugin.min.js', array( 'jquery' ), CP_VERSION, true );
				wp_register_script( 'convert-plus-countdown-js', CP_PLUGIN_URL . 'modules/assets/js/jquery.countdown.js', array( 'jquery' ), CP_VERSION, true );
				wp_register_script( 'convert-plus-countdown-script', CP_PLUGIN_URL . 'modules/assets/js/jquery.countdown.script.js', array( 'jquery' ), CP_VERSION, true );
			}

			if ( isset( $_GET['theme'] ) && preg_match( '/social/', $_GET['theme'] ) ) {
				wp_enqueue_style( 'convert-plus-social-media-style', CP_PLUGIN_URL . 'modules/assets/css/cp-social-media-style.css', array(), CP_VERSION );
				wp_enqueue_style( 'convert-plus-social-icon-style', CP_PLUGIN_URL . 'modules/assets/css/social-icon-css.css', array(), CP_VERSION );
				wp_enqueue_style( 'convert-plus-social-icon-dev-style', CP_PLUGIN_URL . 'modules/assets/css/social-icon.css', array(), CP_VERSION );
			}

			if ( false !== strpos( $hook, CP_PLUS_SLUG ) ) {
				// developer mode.
				if ( '1' === self::$cp_dev_mode ) {
					wp_enqueue_style( 'convert-plus-admin', CP_PLUGIN_URL . 'admin/assets/css/admin.css', array(), CP_VERSION );
					wp_enqueue_style( 'convert-plus-about', CP_PLUGIN_URL . 'admin/assets/css/about.css', array(), CP_VERSION );
					wp_enqueue_style( 'convert-plus-preview-style', CP_PLUGIN_URL . 'admin/assets/css/preview-style.css', array(), CP_VERSION );
					wp_enqueue_style( 'jquery-ui-accordion', CP_PLUGIN_URL . 'admin/assets/css/accordion.css', array(), CP_VERSION );
					wp_enqueue_style( 'convert-plus-select2', CP_PLUGIN_URL . 'admin/assets/select2/select2.min.css', array(), CP_VERSION );
					wp_enqueue_style( 'convert-plus-contacts', CP_PLUGIN_URL . 'admin/contacts/css/cp-contacts.css', array(), CP_VERSION );
					wp_enqueue_style( 'convert-plus-swal-style', CP_PLUGIN_URL . 'admin/assets/css/sweetalert.css', array(), CP_VERSION );
				} else {
					wp_enqueue_style( 'convert-plus-admin-css', CP_PLUGIN_URL . 'admin/assets/css/admin.min.css', array(), CP_VERSION );
				}
			}

			if ( false !== strpos( $hook, CP_PLUS_SLUG ) && '1' === self::$cp_dev_mode ) {
				if ( ! wp_script_is( 'convert-plus-frosty-script', 'enqueued' ) ) {
					wp_enqueue_script( 'convert-plus-frosty-script', CP_PLUGIN_URL . 'admin/assets/js/frosty.js', array( 'jquery' ), CP_VERSION, true );
				}
			}
		}

		/**
		 * Enqueue font style.
		 *
		 * @since 1.0
		 */
		public function cp_admin_css() {
			wp_enqueue_style( 'convert-plus-admin-css', CP_PLUGIN_URL . 'admin/assets/css/font.css', array(), CP_VERSION );
		}

		/**
		 * Enqueue scripts and styles on frontend.
		 *
		 * @since 1.0
		 */
		public function enqueue_front_scripts() {
			// js for both perfect-scrollbar.jquery.js and idle-timer.min.js.
			if ( '1' === self::$cp_dev_mode ) {
				wp_register_script( 'convert-plus-perfect-scroll-js', CP_PLUGIN_URL . 'admin/assets/js/perfect-scrollbar.jquery.js', array( 'jquery' ), CP_VERSION, false );

				wp_register_script( 'convert-plus-ideal-timer-script', CP_PLUGIN_URL . 'modules/assets/js/idle-timer.min.js', array( 'jquery' ), CP_VERSION, true );

				wp_register_style( 'convert-plus-perfect-scroll-style', CP_PLUGIN_URL . 'admin/assets/css/perfect-scrollbar.min.css', array(), CP_VERSION );
			} else {
				wp_register_script( 'convert-plus-module-main-js', CP_PLUGIN_URL . 'modules/assets/js/cp-module-main.js', array( 'jquery' ), CP_VERSION, false );
				wp_register_style( 'convert-plus-module-main-style', CP_PLUGIN_URL . 'modules/assets/css/cp-module-main.css', array(), CP_VERSION );
				wp_register_style( 'convert-plus-social-style', CP_PLUGIN_URL . 'modules/assets/css/cp-social.min.css', array(), CP_VERSION );
			}

		}

		/**
		 * Add main manu for Convert Plus.
		 *
		 * @since 1.0
		 */
		public function add_admin_menu() {
			$page = add_menu_page( CP_PLUS_NAME . ' Dashboard', CP_PLUS_NAME, 'access_cp', CP_PLUS_SLUG, array( $this, 'admin_dashboard' ), 'div' );

			add_action( 'admin_print_scripts-' . $page, array( $this, 'convert_admin_scripts' ) );
			add_action( 'admin_footer-' . $page, array( $this, 'cp_admin_footer' ) );

			if ( defined( 'BSF_MENU_POS' ) ) {
				$required_place = BSF_MENU_POS;
			} else {
				$required_place = 200;
			}

			if ( function_exists( 'bsf_get_free_menu_position' ) ) {
				$place = bsf_get_free_menu_position( $required_place, 1 );
			} else {
				$place = null;
			}

			if ( ! defined( 'BSF_MENU_POS' ) ) {
				define( 'BSF_MENU_POS', $place );
			}
			global $menu;
			$menu_exist = false;
			foreach ( $menu as $item ) {
				if ( strtolower( 'Brainstorm' ) === strtolower( $item[0] ) ) {
					$menu_exist = true;
				}
			}

			$contacts = add_submenu_page(
				CP_PLUS_SLUG,
				__( 'Connects', 'smile' ),
				__( 'Connects', 'smile' ),
				'access_cp',
				'contact-manager',
				array( $this, 'contacts_manager' )
			);
			add_action( 'admin_footer-' . $contacts, array( $this, 'cp_admin_footer' ) );

			$resources_page = add_submenu_page(
				CP_PLUS_SLUG,
				__( 'Resources', 'smile' ),
				__( 'Resources', 'smile' ),
				'access_cp',
				'cp-resources',
				array( $this, 'cp_resources' )
			);
			add_action( 'admin_footer-' . $resources_page, array( $this, 'cp_admin_footer' ) );

			$cust_page = add_submenu_page(
				'contacts_manager',
				'Hidden!',
				'Hidden!',
				'access_cp',
				'cp_customizer',
				array( $this, 'cp_customizer_render_hidden_page' )
			);

			add_action( 'admin_footer-' . $cust_page, array( $this, 'cp_customizer_render_hidden_page' ) );

			// section wise menu.
			global $bsf_section_menu;
			$section_menu       = array(
				'menu'          => 'cp-resources',
				'is_down_arrow' => true,
			);
			$bsf_section_menu[] = $section_menu;

			$google_manager = add_submenu_page(
				CP_PLUS_SLUG,
				__( 'Google Font Manager', 'smile' ),
				__( 'Google Fonts', 'smile' ),
				'access_cp',
				'cplus-bsf-google-font-manager',
				array( $this, 'cp_font_manager' )
			);

			$google_recaptcha_manager = add_submenu_page(
				CP_PLUS_SLUG,
				__( 'Google Recaptcha Manager', 'smile' ),
				__( 'Google reCaptcha', 'smile' ),
				'access_cp',
				'bsf-google-recaptcha-manager',
				array( $this, 'cp_recaptcha_manager' )
			);
			add_action( 'admin_footer-' . $google_recaptcha_manager, array( $this, 'cp_admin_footer' ) );

			$maxmind_database_manager = add_submenu_page(
				CP_PLUS_SLUG,
				__( 'MaxMind Database Managere', 'smile' ),
				__( 'MaxMind Database', 'smile' ),
				'access_cp',
				'bsf-maxmind-database-manager',
				array( $this, 'cp_maxmind_manager' )
			);
			add_action( 'admin_footer-' . $maxmind_database_manager, array( $this, 'cp_admin_footer' ) );

			add_submenu_page(
				CP_PLUS_SLUG,
				__( 'Knowledge Base', 'smile' ),
				__( 'Knowledge Base', 'smile' ),
				'access_cp',
				'knowledge-base',
				array( $this, 'cp_redirect_to_kb' )
			);

			$ultimate_google_font_manager = new Convert_Plug_Ultimate_Google_Font_Manager();
			add_action( 'admin_print_scripts-' . $google_manager, array( $ultimate_google_font_manager, 'admin_google_font_scripts' ) );
			add_action( 'admin_footer-' . $google_manager, array( $this, 'cp_admin_footer' ) );

			$_REQUEST['cp_admin_page_nonce'] = wp_create_nonce( 'cp_admin_page' );
		}

		/**
		 * Function Name: cp_customizer_render_hidden_page.
		 */
		public function cp_customizer_render_hidden_page() {
			require_once CP_BASE_DIR . 'preview.php';
		}

		/**
		 * Function Name: cp_font_manager.
		 */
		public function cp_font_manager() {
			$ultimate_google_font_manager = new Convert_Plug_Ultimate_Google_Font_Manager();
			$ultimate_google_font_manager->cplus_ultimate_font_manager_dashboard();
		}

		/**
		 * Function Name: add_admin_menu_rename.
		 */
		public function add_admin_menu_rename() {
			global $menu, $submenu;
			if ( isset( $submenu[ CP_PLUS_SLUG ][0][0] ) ) {
				$submenu[ CP_PLUS_SLUG ][0][0] = 'Dashboard'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			}
		}

		/**
		 * Function Name: cp_recaptcha_manager.
		 */
		public function cp_recaptcha_manager() {

			require_once CP_BASE_DIR . 'framework/google-recaptcha-manager.php';
		}

		/**
		 * Function Name: cp_maxmind_manager.
		 */
		public function cp_maxmind_manager() {

			require_once CP_BASE_DIR . 'framework/maxmind-database-manager.php';
		}

		/**
		 * Function Name: cp_resources.
		 */
		public function cp_resources() {
			$icon_manager = false;
			require_once CP_BASE_DIR . 'admin/resources.php';
		}

		/**
		 * Function Name: cp_submenu_order.
		 *
		 * @param mixed $menu_ord order for menu.
		 * @return mixed true/false.
		 */
		public function cp_submenu_order( $menu_ord ) {
			global $submenu;

			if ( ! isset( $submenu[ CP_PLUS_SLUG ] ) ) {
				return false;
			}

			$temp_resource                 = array();
			$temp_connect                  = array();
			$temp_google_font_manager      = array();
			$temp_google_recaptcha_manager = array();
			$temp_font_icon_manager        = array();
			$temp_in_sync                  = array();
			$temp_knowledge_base           = array();
			$temp_maxmind                  = array();

			foreach ( $submenu[ CP_PLUS_SLUG ] as $key => $cp_submenu ) {
				if ( 'cp-resources' === $cp_submenu[2] ) {
					$temp_resource = $submenu[ CP_PLUS_SLUG ][ $key ];
					unset( $submenu[ CP_PLUS_SLUG ][ $key ] );
				}
				if ( 'contact-manager' === $cp_submenu[2] ) {
					$temp_connect = $submenu[ CP_PLUS_SLUG ][ $key ];
					unset( $submenu[ CP_PLUS_SLUG ][ $key ] );
				}
				if ( 'bsf-font-icon-manager' === $cp_submenu[2] ) {
					$temp_font_icon_manager = $submenu[ CP_PLUS_SLUG ][ $key ];
					unset( $submenu[ CP_PLUS_SLUG ][ $key ] );
				}
				if ( 'bsf-extensions-14058953' === $cp_submenu[2] ) {
					$temp_addons = $submenu[ CP_PLUS_SLUG ][ $key ];
					unset( $submenu[ CP_PLUS_SLUG ][ $key ] );
				}
				if ( 'bsf-maxmind-database-manager' === $cp_submenu[2] ) {
					$temp_maxmind = $submenu[ CP_PLUS_SLUG ][ $key ];
					unset( $submenu[ CP_PLUS_SLUG ][ $key ] );
				}
				if ( 'cplus-bsf-google-font-manager' === $cp_submenu[2] ) {
					$temp_google_font_manager = $submenu[ CP_PLUS_SLUG ][ $key ];
					unset( $submenu[ CP_PLUS_SLUG ][ $key ] );
				}

				if ( 'bsf-google-recaptcha-manager' === $cp_submenu[2] ) {
					$temp_google_recaptcha_manager = $submenu[ CP_PLUS_SLUG ][ $key ];
					unset( $submenu[ CP_PLUS_SLUG ][ $key ] );
				}

				if ( 'knowledge-base' === $cp_submenu[2] ) {
					$temp_knowledge_base = $submenu[ CP_PLUS_SLUG ][ $key ];
					unset( $submenu[ CP_PLUS_SLUG ][ $key ] );
				}

				if ( 'cp-wp-comment-form' === $cp_submenu[2] ) {
					$temp_wp_comment_form = $submenu[ CP_PLUS_SLUG ][ $key ];
					unset( $submenu[ CP_PLUS_SLUG ][ $key ] );
				}
				if ( 'cp-wp-registration-form' === $cp_submenu[2] ) {
					$temp_wp_registration_form = $submenu[ CP_PLUS_SLUG ][ $key ];
					unset( $submenu[ CP_PLUS_SLUG ][ $key ] );
				}
				if ( 'cp-woocheckout-form' === $cp_submenu[2] ) {
					$temp_woocheckout_form = $submenu[ CP_PLUS_SLUG ][ $key ];
					unset( $submenu[ CP_PLUS_SLUG ][ $key ] );
				}
				if ( 'cp-contact-form7' === $cp_submenu[2] ) {
					$temp_contact_form7 = $submenu[ CP_PLUS_SLUG ][ $key ];
					unset( $submenu[ CP_PLUS_SLUG ][ $key ] );
				}
			}

			array_filter( $submenu[ CP_PLUS_SLUG ] );

			if ( ! empty( $temp_resource ) ) {
				array_push( $submenu[ CP_PLUS_SLUG ], $temp_resource );
			}
			if ( ! empty( $temp_connect ) ) {
				array_push( $submenu[ CP_PLUS_SLUG ], $temp_connect );
			}
			if ( ! empty( $temp_addons ) ) {
				array_push( $submenu[ CP_PLUS_SLUG ], $temp_addons );
			}
			if ( ! empty( $temp_maxmind ) ) {
				array_push( $submenu[ CP_PLUS_SLUG ], $temp_maxmind );
			}
			if ( ! empty( $temp_google_font_manager ) ) {
				array_push( $submenu[ CP_PLUS_SLUG ], $temp_google_font_manager );
			}

			if ( ! empty( $temp_google_recaptcha_manager ) ) {
				array_push( $submenu[ CP_PLUS_SLUG ], $temp_google_recaptcha_manager );
			}

			if ( ! empty( $temp_knowledge_base ) ) {
				array_push( $submenu[ CP_PLUS_SLUG ], $temp_knowledge_base );
			}
			if ( ! empty( $temp_font_icon_manager ) ) {
				array_push( $submenu[ CP_PLUS_SLUG ], $temp_font_icon_manager );
			}
			if ( ! empty( $temp_wp_comment_form ) ) {
				array_push( $submenu[ CP_PLUS_SLUG ], $temp_wp_comment_form );
			}
			if ( ! empty( $temp_wp_registration_form ) ) {
				array_push( $submenu[ CP_PLUS_SLUG ], $temp_wp_registration_form );
			}
			if ( ! empty( $temp_woocheckout_form ) ) {
				array_push( $submenu[ CP_PLUS_SLUG ], $temp_woocheckout_form );
			}
			if ( ! empty( $temp_contact_form7 ) ) {
				array_push( $submenu[ CP_PLUS_SLUG ], $temp_contact_form7 );
			}

			return $menu_ord;
		}

		/**
		 * Load scripts and styles on admin area of Convert Plus.
		 *
		 * @since 1.0
		 */
		public function convert_admin_scripts() {
			wp_enqueue_script( 'jQuery' );
			wp_enqueue_style( 'thickbox' );

			$data = get_option( 'convert_plug_debug' );

			// developer mode.
			if ( '1' === self::$cp_dev_mode ) {
				// accordion.
				wp_enqueue_script( 'convert-plus-accordion-widget', CP_PLUGIN_URL . 'admin/assets/js/jquery.widget.min.js', array(), CP_VERSION, false );
				wp_enqueue_script( 'convert-plus-accordion', CP_PLUGIN_URL . 'admin/assets/js/accordion.js', array( 'jquery' ), CP_VERSION, false );
				wp_enqueue_script( 'convert-plus-frosty-script', CP_PLUGIN_URL . 'admin/assets/js/frosty.js', array( 'jquery' ), CP_VERSION, true );
				wp_enqueue_script( 'convert-plus-admin', CP_PLUGIN_URL . 'admin/assets/js/admin.js', array( 'convert-plus-frosty-script' ), CP_VERSION, true );

				// shuffle js scripts.
				wp_enqueue_script( 'convert-plus-jquery-modernizer', CP_PLUGIN_URL . 'modules/assets/js/jquery.shuffle.modernizr.js', array(), CP_VERSION, true );
				wp_enqueue_script( 'convert-plus-jquery-shuffle', CP_PLUGIN_URL . 'modules/assets/js/jquery.shuffle.min.js', array(), CP_VERSION, true );
				wp_enqueue_script( 'convert-plus-jquery-shuffle-custom', CP_PLUGIN_URL . 'modules/assets/js/shuffle-script.js', array(), CP_VERSION, true );

				// sweet alert.
				wp_enqueue_script( 'convert-plus-swal-js', CP_PLUGIN_URL . 'admin/assets/js/sweetalert.min.js', array(), CP_VERSION, true );
			} else {
				wp_enqueue_script( 'convert-plus-frosty-script', CP_PLUGIN_URL . 'admin/assets/js/frosty.js', array( 'jquery' ), CP_VERSION, true );
				wp_enqueue_script( 'convert-plus-admin', CP_PLUGIN_URL . 'admin/assets/js/admin.min.js', array(), CP_VERSION, true );
			}
			$nonce_object = array(
				'framework_update_preview_data_nonce' => wp_create_nonce( 'cp_framework_update_preview_data_nonce' ),
				'duplicate_nonce'                     => wp_create_nonce( 'cp_duplicate_nonce' ),
				'presets_nonce'                       => wp_create_nonce( 'cp_presets_nonce' ),
				'cp_import_nonce'                     => wp_create_nonce( 'cp_import_module_nonce' ),
				'customizer_nonce'                    => wp_create_nonce( 'cp_customizer_nonce' ),
				'cp_get_posts_by_query_nonce'         => wp_create_nonce( 'cp_get_posts_by_query_nonce' ),
			);

			wp_localize_script( 'convert-plus-admin', 'cplus_var_nonce', $nonce_object );

			wp_localize_script(
				'convert-plus-admin',
				'cplus_vars',
				array(
					'delete_notice'      => esc_html__( 'You will not be able to recover this selected', 'smile' ),
					'confirm_delete'     => esc_html__( 'Yes, delete it!', 'smile' ),
					'cancel_delete'      => esc_html__( 'No, cancel it!', 'smile' ),
					'delete_conf_notice' => esc_html__( 'Style you have selected has been deleted.', 'smile' ),
					'duplicate_style'    => esc_html__( 'Duplicated', 'smile' ),
				)
			);
			if ( isset( $_REQUEST['cp_admin_page_nonce'] ) && wp_verify_nonce( $_REQUEST['cp_admin_page_nonce'], 'cp_admin_page' ) ) {
				if ( ( isset( $_GET['style-view'] ) && ( 'edit' === $_GET['style-view'] || 'variant' === $_GET['style-view'] ) ) || ! isset( $_GET['style-view'] ) ) {
					wp_enqueue_script( 'convert-plus-select2', CP_PLUGIN_URL . 'admin/assets/select2/select2.min.js', array(), '2.4.0.1', false );
				}
			}

			// REMOVE WP EMOJI.
			remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
			remove_action( 'wp_print_styles', 'print_emoji_styles' );

			remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
			remove_action( 'admin_print_styles', 'print_emoji_styles' );
			/*Conflict with Subway Theme.*/
			remove_action( 'admin_print_scripts', 'qode_admin_jquery' );
		}

		/**
		 * Add footer link for dashboar.
		 *
		 * @since 1.0.1
		 */
		public function cp_admin_footer() {
			echo '<div id="wpfooter" role="contentinfo" class="cp_admin_footer">

            <p id="footer-left" class="alignleft">
            <span id="footer-thankyou">Thank you for using <a href="https://www.convertplug.com/plus" target="_blank" rel="noopener" >' . esc_html( CP_PLUS_NAME ) . '</a>.</span>   </p>
            <p id="footer-upgrade" class="alignright">';
			esc_html_e( 'Version', 'smile' );
			echo ' ' . esc_html( CP_VERSION );echo '</p>
            <div class="clear"></div>
            </div>';
		}

		/**
		 * Load convertPlug dashboard.
		 *
		 * @since 1.0
		 */
		public function admin_dashboard() {
			require_once CP_BASE_DIR . '/admin/admin.php';
		}

		/**
		 * Load convertPlug contacts manager.
		 *
		 * @since 1.0
		 */
		public function contacts_manager() {
			require_once CP_BASE_DIR . '/admin/contacts/admin.php';
		}

		/**
		 * Function Name: cp_add_core_styles.
		 *
		 * @param  array $hooks array of pages.
		 * @return array        array of pages.
		 */
		public function cp_add_core_styles( $hooks ) {
			$contacts_page_hook = CP_PLUS_SLUG . '_page_contact-manager';
			$cpmain_page_hook   = 'toplevel_page_' . CP_PLUS_SLUG;
			array_push( $hooks, $contacts_page_hook, $cpmain_page_hook );
			return $hooks;
		}
		/**
		 * Redirects to the premium version of MailChimp for WordPress (uses JS).
		 */
		public function cp_redirect_to_kb() {
			?><script type="text/javascript">window.location.replace('<?php echo esc_url( admin_url() ); ?>admin.php?page=<?php echo esc_html( CP_PLUS_SLUG ); ?>&view=knowledge_base'); </script>
			<?php
		}

		/**
		 * Load frosty scripts from bsf core.
		 *
		 * @param  array $hook array of pages.
		 * @since 2.1.0
		 */
		public function smile_frosty_scripts_from_core( $hook ) {
			// page hooks array where we need frosty scripts to load.
			$array = array(
				'toplevel_page_' . CP_PLUS_SLUG,
				CP_PLUS_SLUG . '_page_smile-modal-designer',
				CP_PLUS_SLUG . '_page_smile-info_bar-designer',
				CP_PLUS_SLUG . '_page_smile-slide_in-designer',
				CP_PLUS_SLUG . '_page_contact-manager',
				CP_PLUS_SLUG . '_page_role-manager',
				'admin_page_cp_customizer',
				CP_PLUS_SLUG . '_page_cp-wp-registration-form',
			);

			if ( false !== strpos( $hook, CP_PLUS_SLUG ) ) {
				if ( ! wp_script_is( 'convert-plus-frosty-script', 'enqueued' ) ) {
					wp_enqueue_script( 'convert-plus-frosty-script', CP_PLUGIN_URL . 'admin/assets/js/frosty.js', array( 'jquery' ), CP_VERSION, true );
				}
				if ( ! wp_style_is( 'convert-plus-frosty-style', 'enqueued' ) ) {
					wp_enqueue_style( 'convert-plus-frosty-style', CP_PLUGIN_URL . 'admin/assets/css/frosty.css', array(), CP_VERSION, false );
				}
			}
		}

		/**
		 * Function Name:convert_plug_store_module Retrieve and store modules into the static variable $modules.
		 *
		 * @param  [type] $modules_array array of modules in form of "Module Name" => "Module Main File".
		 * @return boolval(var)                true/false.
		 * @since 1.0
		 */
		public static function convert_plug_store_module( $modules_array ) {
			$result = false;
			if ( ! empty( $modules_array ) ) {
				self::$modules = $modules_array;
				$result        = true;
			}
			return $result;
		}

		/**
		 * Created default campaign on activation.
		 *
		 * @since 1.0
		 */
		public function create_default_campaign() {
			// create default campaign.
			$smile_lists = get_option( 'smile_lists' );
			if ( ! $smile_lists ) {
				$data = array();
				$list = array(
					'date'          => gmdate( 'd-m-Y' ),
					'list-name'     => 'First',
					'list-provider' => 'Convert Plug',
					'list'          => '',
					'provider_list' => '',
				);

				$data[] = $list;
				update_option( 'smile_lists', $data );

			}

			$data_settings = get_option( 'convert_plug_settings' );
			if ( ! $data_settings ) {
				$module_setings = array(
					'cp-enable-mx-record'   => '0',
					'cp-default-messages'   => '1',
					'cp-already-subscribed' => 'Already Subscribed...!',
					'cp-double-optin'       => '1',
					'cp-gdpr-optin'         => '1',
					'cp-sub-notify'         => '0',
					'cp-sub-email'          => get_option( 'admin_email' ),
					'cp-email-sub'          => 'Congratulations! You have a New Subscriber!',
					'cp-google-fonts'       => '1',
					'cp-timezone'           => 'wordpress',
					'user_inactivity'       => '60',
					'cp-edit-style-link'    => '0',
					'cp-plugin-support'     => '0',
					'cp-disable-impression' => '0',
					'cp-close-inline'       => '0',
					'cp-disable-storage'    => '0',
					'cp-disable-pot'        => '1',
					'cp-disable-domain'     => '0',
					'cp-domain-name'        => '',
					'cp-lazy-img'           => '0',
					'cp-close-gravity'      => '1',
					'cp-load-syn'           => '0',
					'cp_change_ntf_id'      => '1',
					'cp_notify_email_to'    => get_option( 'admin_email' ),
					'cp-access-role'        => '',
					'cp-user-role'          => 'administrator',
					'cp-new-user-role'      => '',
					'cp-email-body'         => '',
				);
				update_option( 'convert_plug_settings', $module_setings );
			}

		}

		/**
		 * Redirect on activation hook.
		 *
		 * @since 1.0
		 */
		public function cp_redirect_on_activation() {
			if ( true === get_option( 'convert_plug_redirect' ) || '1' === get_option( 'convert_plug_redirect' ) ) {
				update_option( 'convert_plug_redirect', false );
				$this->create_default_campaign();
				if ( ! is_multisite() ) :
					wp_safe_redirect( admin_url( 'admin.php?page=' . CP_PLUS_SLUG ) );
				endif;
			}
		}

		/**
		 * Add custom css for customizer admin page.
		 *
		 * @param array $hook page array.
		 * @since 2.0.1
		 */
		public function cp_custom_css( $hook ) {
			if ( isset( $_REQUEST['cp_admin_page_nonce'] ) && wp_verify_nonce( $_REQUEST['cp_admin_page_nonce'], 'cp_admin_page' ) ) {
				if ( isset( $_GET['page'] ) && 'cp_customizer' === $_GET['page'] ) {
					echo '<style>
                #adminmenuwrap,
                #adminmenuback,
                #wpadminbar,
                #wpfooter,
                    .media-upload-form .notice,
                    .media-upload-form div.error,
                    .update-nag,
                    .updated,
                    .wrap .notice,
                    .wrap div.error,
                    .wrap div.updated,
                    .notice-warning,
                #wpbody-content .error,
                #wpbody-content .notice {
                    display: none !important;
                }
                </style>';

					// Remove WooCommerce's annoying update message.

					remove_action( 'admin_notices', 'woothemes_updater_notice' );

					// Remove admin notices.
					remove_action( 'admin_notices', 'update_nag', 3 );
				}
			}
		}


		/**
		 * Deregister scripts on customizer page
		 *
		 * @param array $hook array parameter for page.
		 * @since 2.3.2
		 */
		public function cp_dergister_scripts( $hook ) {
			$data  = get_option( 'convert_plug_settings' );
			$psval = isset( $data['cp-plugin-support'] ) ? $data['cp-plugin-support'] : 1;

			if ( $psval ) {
				$page_hooks = array(
					CP_PLUS_SLUG . '_page_smile-modal-designer',
					CP_PLUS_SLUG . '_page_smile-info_bar-designer',
					CP_PLUS_SLUG . '_page_smile-slide_in-designer',
					'admin_page_cp_customizer',
				);

				if ( in_array( $hook, $page_hooks ) ) {
					if ( isset( $_REQUEST['cp_admin_page_nonce'] ) && wp_verify_nonce( $_REQUEST['cp_admin_page_nonce'], 'cp_admin_page' ) ) {
						if ( ( isset( $_GET['style-view'] ) && ( 'edit' === $_GET['style-view'] || 'variant' === $_GET['style-view'] ) ) || isset( $_GET['hidemenubar'] ) ) {
							global $wp_scripts;
							$scripts              = $wp_scripts->registered;
							$deregistered_scripts = array();

							if ( is_array( $scripts ) ) {
								foreach ( $scripts as $key => $script ) {
									$source = $script->src;

									// if script is registered by plugin other than ConvertPlg OR by Theme.
									if ( ( strpos( $source, 'wp-content/plugins' ) && ! strpos( $source, 'wp-content/plugins/' . CP_DIR_NAME ) ) || strpos( $source, 'wp-content/themes' ) ) {
										if ( isset( $script->handle ) ) {
											$handle = $script->handle;
											$source = $script->src;

											$deregistered_scripts[ $source ] = $handle;

											// deregister script handle.
											wp_deregister_script( $handle );
										}
									}
								}
							}

							if ( ! empty( $deregistered_scripts ) ) {
								update_option( 'cp_scripts_debug_info', $deregistered_scripts );
							}
						}
					}
				}
			}
		}

		/**
		 * Deregister scripts on customizer page
		 *
		 * @param array $hook array parameter for page.
		 * @since 2.3.2
		 */
		public function cp_dequeue_script_imagify( $hook ) {
			if ( isset( $_REQUEST['cp_admin_page_nonce'] ) && wp_verify_nonce( $_REQUEST['cp_admin_page_nonce'], 'cp_admin_page' ) ) {
				if ( isset( $_GET['page'] ) ) {
					$page_name = esc_attr( $_GET['page'] );

					$page_hooks = array(
						'smile-modal-designer',
						'smile-info_bar-designer',
						'smile-slide_in-designer',
						'admin_page_cp_customizer',
						'contact-manager',
						CP_PLUS_SLUG,
					);

					if ( in_array( $page_name, $page_hooks ) ) {
						wp_dequeue_script( 'chartjs' );
						wp_dequeue_script( 'bsf-core-frosty' );
						wp_dequeue_style( 'bsf-core-frosty-style' );
						wp_dequeue_style( 'imagify-css-sweetalert' );
						wp_dequeue_script( 'imagify-js-admin' );
						wp_dequeue_script( 'imagify-js-sweetalert' );

						if ( function_exists( 'wpjobster_admin_stylesheet' ) ) {
							remove_action( 'admin_head', 'wpjobster_admin_stylesheet' );
						}

						wp_dequeue_script( 'gsas_microdata' );
						wp_dequeue_script( 'gsas_jquery_plugin' );
						wp_dequeue_script( 'gsas_jquery_datepicker_js' );

						wp_dequeue_script( 'wptc-jquery' );
						wp_dequeue_script( 'wptc-actions' );
						wp_dequeue_script( 'wptc-pro-common-listener' );

						// conflict with voux theme.
						wp_dequeue_script( 'thb-admin-meta' );
						wp_dequeue_script( 'ocdi-main-js' );
						// conflict with Easy Pricing Table.
						wp_dequeue_script( 'jscolor' );

						// conflict with the woocommerce_order_attchment_pro.

						if ( function_exists( 'phoen_attchment_plugin_header_scripts' ) ) {
							remove_action( 'admin_head', 'phoen_attchment_plugin_header_scripts' );
						}
					}
				}
			}
		}

		/**
		 * Function Name: cp_link_to_load_css_script.
		 * Function Description: cp_link_to_load_css_script.
		 *
		 * @param string $html html.
		 * @param string $handle handle.
		 * @param string $href href.
		 */
		public function cp_link_to_load_css_script( $html, $handle, $href ) {
			$load_async = array(
				'smile-modal-style',
				'smile-info-bar-style',
				'smile-slide-in-style',
				'cp-module-main-style',
			);

			if ( is_admin() ) {
				return $html;
			}

			$modal_arr = array(
				'blank-cp',
				'countdown-cp',
				'every_design-cp',
				'direct_download-cp',
				'first_order-cp',
				'first_order_2-cp',
				'flat_discount-cp',
				'free_ebook-cp',
				'instant_coupon-cp',
				'jugaad-cp',
				'locked_content-cp',
				'optin_to_win-cp',
				'social_article-cp',
				'social_inline_share-cp',
				'social_media-cp',
				'social_media_with_form-cp',
				'social_widget_box-cp',
				'special_offer-cp',
				'webinar-cp',
				'youtube-cp',
			);

			$infobox_arr = array(
				'blank-cp',
				'countdown-cp',
				'free_trial-cp',
				'get_this_deal-cp',
				'image_preview-cp',
				'newsletter-cp',
				'social_info_bar-cp',
				'weekly_article-cp',
			);

			$slidein_arr = array(
				'blank-cp',
				'floating_social_bar-cp',
				'free_widget-cp',
				'optin-cp',
				'optin_widget-cp',
				'social_fly_in-cp',
				'social_widget_box-cp',
				'subscriber_newsletter-cp',
			);

			foreach ( $modal_arr as $needle ) {
				if ( strpos( $handle, $needle ) !== false ) {
					array_push( $load_async, $handle );
				}
			}

			foreach ( $infobox_arr as $needle ) {
				if ( strpos( $handle, $needle ) !== false ) {
					array_push( $load_async, $handle );
				}
			}

			foreach ( $slidein_arr as $needle ) {
				if ( strpos( $handle, $needle ) !== false ) {
					array_push( $load_async, $handle );
				}
			}

			if ( in_array( $handle, $load_async ) ) {
				$cp_script = "<script>document.addEventListener('DOMContentLoaded', function(event) {  if( typeof cpLoadCSS !== 'undefined' ) { cpLoadCSS('" . $href . "', 0, 'all'); } }); </script>\n";
				$html      = $cp_script;
			}

			return $html;
		}

		/**
		 * Display debug info for excluded scripts.
		 *
		 * @since 2.3.2
		 */
		public function cp_add_debug_info() {
			$is_displayed_info = get_option( 'cp_is_displayed_debug_info' );

			// if debug info is not already displayed.
			if ( ! $is_displayed_info ) {
				$screen = get_current_screen();

				$current_page_hook = $screen->base;

				$page_hooks = array(
					CP_PLUS_SLUG . '_page_smile-modal-designer',
					CP_PLUS_SLUG . '_page_smile-info_bar-designer',
					CP_PLUS_SLUG . '_page_smile-slide_in-designer',
				);
				if ( isset( $_REQUEST['cp_admin_page_nonce'] ) && wp_verify_nonce( $_REQUEST['cp_admin_page_nonce'], 'cp_admin_page' ) ) {
					if ( in_array( $current_page_hook, $page_hooks ) && ! isset( $_GET['hidemenubar'] ) ) {
						update_option( 'cp_is_displayed_debug_info', true );

						$debug_info = get_option( 'cp_scripts_debug_info' );

						$debug_info_html = "<!-- CP Debug Information - List of the JS disabled on customizer screen ----------- \n";

						if ( is_array( $debug_info ) ) {
							foreach ( $debug_info as $src => $handle ) {
								$string           = $handle . ' :- ' . $src;
								$debug_info_html .= $string . "\n";
							}
						}

						$debug_info_html .= '<!-- End - CP Debug Information -->';

						echo esc_html( $debug_info_html );
					}
				}
			}
		}
	}

	/**
	 * Public Function to search style from multidimentional array
	 *
	 * @param  [type] $array style name to be searched.
	 * @param  [type] $style array of styles.
	 * @return [type]        array key if style is found in the given array
	 */
	function search_style( $array, $style ) {
		if ( is_array( $array ) ) {
			foreach ( $array as $key => $data ) {
				$data_style = isset( $data['style_id'] ) ? $data['style_id'] : '';
				if ( $data_style === $style ) {
					return $key;
				}
			}
		}
	}

	/**
	 * Function Name: convert_plug_add_module Public function for accepting requests for adding new module in the convert plug.
	 *
	 * @param  array $modules_array array of modules in form of "Module Name" => "Module Main File".
	 * @return array                array
	 */
	function convert_plug_add_module( $modules_array ) {
		return Convert_Plug::convert_plug_store_module( $modules_array );
	}

	// load modules.
	require_once CP_BASE_DIR . '/modules/config.php';
}
new Smile_Framework();
new Convert_Plug();
