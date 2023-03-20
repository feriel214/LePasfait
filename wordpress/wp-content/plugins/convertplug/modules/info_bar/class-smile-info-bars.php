<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

if ( ! defined( 'CP_BASE_DIR_IFB' ) ) {
	define( 'CP_BASE_DIR_IFB', plugin_dir_path( __FILE__ ) );
}
require_once CP_BASE_DIR_IFB . '/functions/functions.php';

if ( ! class_exists( 'Convert_Plug_Smile_Info_Bars' ) ) {
	/**
	 * Class Convert_Plug_Smile_Info_Bars.
	 */
	class Convert_Plug_Smile_Info_Bars extends Convert_Plug {
















		/**
		 * $settings array.
		 *
		 * @var array
		 */
		public static $settings = array();

		/**
		 * $options array.
		 *
		 * @var array
		 */
		public static $options = array();

		/**
		 * Constructor.
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'add_admin_menu_page' ), 999 );
			add_action( 'admin_head', array( $this, 'load_customizer_scripts' ) );
			add_action( 'wp_footer', array( $this, 'load_info_bar_globally' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_front_scripts' ), 101 );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
			add_action( 'init', array( $this, 'register_theme_templates' ) );
			add_filter( 'admin_body_class', array( $this, 'cp_admin_body_class' ) );
			require_once CP_BASE_DIR_IFB . 'info-bar-preset.php';
		}

		/**
		 * Function Name: cp_admin_body_class.
		 *
		 * @param  string $classes string parameter.
		 * @return string          string parameter.
		 */
		public function cp_admin_body_class( $classes ) {
			if ( isset( $_REQUEST['cp_admin_page_nonce'] ) && wp_verify_nonce( $_REQUEST['cp_admin_page_nonce'], 'cp_admin_page' ) ) {
				if ( isset( $_GET['style-view'] ) && 'new' === $_GET['style-view'] && false === strpos( $classes, 'cp-add-new-style' ) ) {
					$classes .= ' cp-add-new-style';
					$classes  = implode( ' ', array_unique( explode( ' ', 'cp-add-new-style' ) ) );
				}
				return $classes;
			}
		}

		/**
		 * Function Name: register_theme_templates.
		 */
		public function register_theme_templates() {
			$dir    = plugin_dir_path( __FILE__ );
			$themes = glob( $dir . 'themes/*.php' );
			foreach ( $themes as $theme ) {
				require_once $theme;
			}
		}

		/**
		 * Function Name: add_admin_menu_page.
		 */
		public function add_admin_menu_page() {
			$page = add_submenu_page(
				CP_PLUS_SLUG,
				'Info Bar Designer',
				'Info Bar',
				'access_cp',
				'smile-info_bar-designer',
				array( $this, 'info_bar_dashboard' )
			);
			$obj  = new Convert_Plug();
			add_action( 'admin_print_scripts-' . $page, array( $obj, 'convert_admin_scripts' ) );
			add_action( 'admin_print_scripts-' . $page, array( $this, 'info_bar_admin_scripts' ) );
			add_action( 'admin_footer-' . $page, array( $this, 'cp_admin_footer' ) );
		}

		/**
		 * Function Name: info_bar_admin_scripts.
		 */
		public function info_bar_admin_scripts() {
			if ( isset( $_REQUEST['cp_admin_page_nonce'] ) && wp_verify_nonce( $_REQUEST['cp_admin_page_nonce'], 'cp_admin_page' ) ) {
				if ( ( isset( $_GET['style-view'] ) && ( 'edit' === $_GET['style-view'] || 'variant' === $_GET['style-view'] ) ) || ! isset( $_GET['style-view'] ) ) {
					wp_enqueue_script( 'convert-plus-info_bar-receiver', CP_PLUGIN_URL . 'modules/info_bar/assets/js/receiver.js', array( 'jquery' ), CP_VERSION, true );
					wp_enqueue_style( 'convert-plus-contacts', CP_PLUGIN_URL . 'admin/contacts/css/cp-contacts.css', array(), CP_VERSION );
					wp_enqueue_media();
					wp_enqueue_script( 'convert-plus-info_bar-importer', CP_PLUGIN_URL . 'modules/assets/js/admin-media.js', array( 'jquery' ), CP_VERSION, true );
				}

				if ( isset( $_GET['style-view'] ) && 'analytics' === $_GET['style-view'] ) {
					wp_enqueue_style( 'convert-plus-select2', CP_PLUGIN_URL . 'admin/assets/select2/select2.min.css', array(), CP_VERSION );
					wp_enqueue_script( 'convert-plus-select2', CP_PLUGIN_URL . 'admin/assets/select2/select2.min.js', array( 'jquery' ), CP_VERSION, true );
					wp_enqueue_script( 'bsf-charts-js', CP_PLUGIN_URL . 'admin/assets/js/chart.js', array( 'jquery' ), CP_VERSION, true );
					wp_enqueue_script( 'bsf-charts-bar-js', CP_PLUGIN_URL . 'admin/assets/js/chart.bar.js', array( 'jquery' ), CP_VERSION, true );
					wp_enqueue_script( 'bsf-charts-donut-js', CP_PLUGIN_URL . 'admin/assets/js/chart.donuts.js', array( 'jquery' ), CP_VERSION, true );
					wp_enqueue_script( 'bsf-charts-line-js', CP_PLUGIN_URL . 'admin/assets/js/Chart.Line.js', array( 'jquery' ), CP_VERSION, true );
					wp_enqueue_script( 'bsf-charts-polararea-js', CP_PLUGIN_URL . 'admin/assets/js/Chart.PolarArea.js', array( 'jquery' ), CP_VERSION, true );
					wp_enqueue_script( 'bsf-style-analytics-js', CP_PLUGIN_URL . 'modules/assets/js/style-analytics.js', array( 'jquery' ), CP_VERSION, true );
				}
			}
		}

		/**
		 * Function Name: info_bar_dashboard.
		 */
		public function info_bar_dashboard() {
			if ( isset( $_REQUEST['cp_admin_page_nonce'] ) && wp_verify_nonce( $_REQUEST['cp_admin_page_nonce'], 'cp_admin_page' ) ) {
				$page           = isset( $_GET['style-view'] ) ? esc_attr( $_GET['style-view'] ) : 'main';
				$analytics_data = get_option( 'smile_style_analytics' );
				$variant_tests  = get_option( 'info_bar_variant_tests' );
				$prev_styles    = get_option( 'smile_info_bar_styles' );

				// load default option set.
				if ( is_admin() ) {
					require_once CP_BASE_DIR_IFB . '/functions/functions.options.php';
				}

				switch ( $page ) {
					case 'main':
						require_once CP_BASE_DIR_IFB . '/views/main.php';
						break;
					case 'new':
						$default_google_fonts = array(
							'Lato',
							'Open Sans',
							'Libre Baskerville',
							'Montserrat',
							'Neuton',
							'Raleway',
							'Roboto',
							'Sacramento',
							'Varela Round',
							'Pacifico',
							'Bitter',
						);
						$gfonts               = implode( ',', $default_google_fonts );
						require_once CP_BASE_DIR_IFB . '/functions/functions.php';
						if ( function_exists( 'cp_enqueue_google_fonts' ) ) {
							cp_enqueue_google_fonts( $gfonts );
						}
						require_once CP_BASE_DIR_IFB . '/views/new-style.php';
						break;
					case 'edit':
						require_once CP_BASE_DIR_IFB . '/views/edit.php';
						break;
					case 'variant':
						require_once CP_BASE_DIR_IFB . '/views/variant.php';
						break;
					case 'analytics':
						require_once CP_BASE_DIR_IFB . '/views/analytics.php';
						break;
				}
			}
		}

		/**
		 * Function Name: load_info_bar_globally.
		 */
		public function load_info_bar_globally() {
			$info_bar_style        = '';
			$info_bar_style_delay  = '';
			$info_bar_cookie_delay = '';
			$live_styles           = cp_get_live_styles( 'info_bar' );
			$prev_styles           = get_option( 'smile_info_bar_styles' );
			$smile_variant_tests   = get_option( 'info_bar_variant_tests' );

			if ( is_array( $live_styles ) && ! empty( $live_styles ) ) {
				global $post;
				$info_bar_arrays = $live_styles;
				$taxonomies      = get_post_taxonomies( $post );

				foreach ( $info_bar_arrays as $key => $info_bar_array ) {
					$display          = false;
					$settings_encoded = '';

					$style_settings     = array();
					$global_display     = '';
					$pages_to_exclude   = '';
					$cats_to_exclude    = '';
					$exclusive_pages    = '';
					$exclusive_cats     = '';
					$show_for_logged_in = '';
					$settings_array     = maybe_unserialize( $info_bar_array['style_settings'] );

					foreach ( $settings_array as $key => $setting ) {
						$style_settings[ $key ] = apply_filters( 'smile_render_setting', $setting );
					}

					$style_id       = $info_bar_array['style_id'];
					$info_bar_style = $style_settings['style'];

					if ( is_array( $style_settings ) && ! empty( $style_settings ) ) {
						$settings = maybe_unserialize( $info_bar_array['style_settings'] );
						$css      = isset( $settings['custom_css'] ) ? urldecode( $settings['custom_css'] ) : '';

						$display = cp_is_style_visible( $settings );

						// Remove back slashes from settings.
						$settings = stripslashes_deep( $settings );

						$settings         = wp_json_encode( $settings );
						$settings_encoded = base64_encode( $settings );
					}

					if ( $display ) {
						// Developer mode.
						if ( '1' === self::$cp_dev_mode ) {
							$script_handlers = array(
								'convert-plus-ideal-timer-script',
								'convert-plus-info-bar-script',
								'convert-plus-common-script',
								'convert-plus-info-bar-mailer-script',
							);

							$list = 'enqueued';

							foreach ( $script_handlers as $handler ) {
								if ( ! wp_script_is( $handler, $list ) ) {
									wp_enqueue_script( $handler );
								}
							}
						} else {
							if ( ! wp_script_is( 'convert-plus-module-main-js', 'enqueued' ) ) {
								wp_enqueue_script( 'convert-plus-module-main-js' );
							}

							if ( ! wp_style_is( 'convert-plus-module-main-style', 'enqueued' ) ) {
								wp_enqueue_style( 'convert-plus-module-main-style' );
							}

							if ( ! wp_script_is( 'convert-plus-info-bar-script', 'enqueued' ) ) {
								wp_enqueue_script( 'convert-plus-info-bar-script' );
							}
						}

						// Convert style name to lowercase - ( Case sensitive URL's not accessible by server ).
						$info_bar_style = strtolower( $info_bar_style );

						// Generate style ID.
						$id = $info_bar_style . '-' . $style_id;

						// Individual Style Path.
						$file_name = 'assets/demos/' . strtolower( $info_bar_style ) . '/' . strtolower( $info_bar_style ) . '.min.css';
						$url       = CP_PLUGIN_URL . 'modules/info_bar/' . $file_name;
						wp_enqueue_style( esc_attr( $id ), esc_url( $url ), array(), CP_VERSION );

						echo do_shortcode( '[smile_info_bar style_id = ' . $style_id . ' style="' . $info_bar_style . '" settings_encoded="' . $settings_encoded . ' "][/smile_info_bar]' );

						$css = isset( $settings['custom_css'] ) ? urldecode( $settings['custom_css'] ) : '';
						apply_filters( 'cp_custom_css', $style_id, $css );
					}
				}
			}
		}

		/**
		 * Function Name: load_customizer_scripts.
		 */
		public function load_customizer_scripts() {
			if ( isset( $_REQUEST['cp_admin_page_nonce'] ) && wp_verify_nonce( $_REQUEST['cp_admin_page_nonce'], 'cp_admin_page' ) ) {
				if ( ( isset( $_GET['hidemenubar'] ) && isset( $_GET['module'] ) && 'info_bar' === esc_attr( $_GET['module'] ) ) ) {
					// countdown js.
					if ( isset( $_GET['theme'] ) && 'countdown' === $_GET['theme'] ) {
						wp_enqueue_style( 'convert-plus-countdown-style' );
						wp_enqueue_script( 'convert-plus-counter-plugin-js' );
						wp_enqueue_script( 'convert-plus-countdown-js' );
					}

					wp_enqueue_script( 'convert-plus-admin-customizer-js', CP_PLUGIN_URL . 'modules/assets/js/admin.customizer.js', array( 'jquery' ), CP_VERSION, true );
					wp_enqueue_script( 'convert-plus-info_bar-common-functions-js', CP_PLUGIN_URL . 'modules/info_bar/assets/js/functions-common.js', array( 'jquery' ), CP_VERSION, true );

					require_once CP_BASE_DIR_IFB . '/functions/functions.options.php';
					$demo_html     = '';
					$customizer_js = '';
					$settings      = $this::$options;
					foreach ( $settings as $style => $options ) {
						if ( esc_attr( $_GET['theme'] ) === $style ) {
							$customizer_js = $options['customizer_js'];
						}
					}
					if ( '' !== $customizer_js ) {
						wp_enqueue_script( 'convert-plus-style-customizer-js', $customizer_js, array( 'jquery' ), CP_VERSION, true );
					}
				}
			}
		}

		/**
		 * Function Name: enqueue_admin_scripts.
		 *
		 * @param  array $hook page name array.
		 */
		public function enqueue_admin_scripts( $hook ) {
			if ( isset( $_REQUEST['cp_admin_page_nonce'] ) && wp_verify_nonce( $_REQUEST['cp_admin_page_nonce'], 'cp_admin_page' ) ) {
				if ( ( isset( $_GET['hidemenubar'] ) && 'info_bar' === $_GET['module'] )
				|| ( isset( $_GET['style-view'] ) && 'new' === $_GET['style-view'] && CP_PLUS_SLUG . '_page_smile-info_bar-designer' === $hook ) ) {
					wp_enqueue_style( 'convert-plus-animate-style', CP_PLUGIN_URL . 'modules/assets/css/animate.css', array(), CP_VERSION );
					wp_enqueue_style( 'convert-plus-info-bar', CP_PLUGIN_URL . 'modules/info_bar/assets/css/info_bar.min.css', array(), CP_VERSION );

					if ( isset( $_GET['module'] ) && 'info_bar' === $_GET['module'] && ! wp_script_is( 'convert-plus-info_bar-common-functions-js', 'enqueued' ) ) {
						wp_enqueue_script( 'convert-plus-info_bar-common-functions-js', CP_PLUGIN_URL . 'modules/info_bar/assets/js/functions-common.js', array( 'jquery' ), CP_VERSION, true );
					}

					wp_localize_script(
						'convert-plus-info_bar-common-functions-js',
						'cp',
						array(
							'demo_dir'       => CP_PLUGIN_URL . 'modules/info_bar/assets/demos',
							'module'         => 'info_bar',
							'module_img_dir' => CP_PLUGIN_URL . 'modules/assets/images',
						)
					);

					// Add 'Theme Name' as a class to <html> tag.
					// To provide theme compatibility.
					$theme_name = wp_get_theme();
					$theme_name = $theme_name->get( 'Name' );
					$theme_name = strtolower( preg_replace( '/[\s_]/', '-', $theme_name ) );

					wp_localize_script( 'convert-plus-info_bar-common-functions-js', 'cp_active_theme', array( 'slug' => $theme_name ) );
					wp_localize_script( 'convert-plus-info_bar-common-functions-js', 'smile_ajax', array( 'url' => admin_url( 'admin-ajax.php' ) ) );
				}
			}
		}

		/**
		 * Function Name: enqueue_front_scripts.
		 */
		public function enqueue_front_scripts() {

			$live_styles = cp_get_live_styles( 'info_bar' );

			// If any style is live or info_bar is in live preview mode then only enqueue scripts and styles.
			if ( $live_styles && 0 < count( $live_styles ) ) {
				$handler = '';
				if ( '1' === self::$cp_dev_mode ) {
					// Register Social Media styles.
					cp_enqueue_social_media( 'info_bar', $live_styles, '1' );
					// Register styles.
					wp_enqueue_style( 'convert-plus-info-bar-style', CP_PLUGIN_URL . 'modules/info_bar/assets/css/info_bar.css', array(), CP_VERSION );
					wp_enqueue_style( 'convert-plus-style', CP_PLUGIN_URL . 'modules/assets/css/convertplug.css', array(), CP_VERSION );

					// Register Animation CSS.
					wp_enqueue_style( 'convert-plus-animate', CP_PLUGIN_URL . 'modules/assets/css/unminified-animation/main-animation.css', array(), CP_VERSION ); // Main Animation File required for all types.

					// Register scripts.
					wp_register_script( 'convert-plus-common-script', CP_PLUGIN_URL . 'modules/assets/js/convertplug-common.js', array( 'jquery' ), CP_VERSION, true );

					wp_register_script( 'convert-plus-info-bar-script', CP_PLUGIN_URL . 'modules/info_bar/assets/js/info_bar.js', array( 'jquery', 'convert-plus-common-script' ), CP_VERSION, true );

					wp_register_script(
						'convert-plus-info-bar-mailer-script',
						CP_PLUGIN_URL . 'modules/info_bar/assets/js/mailer.js',
						array( 'jquery' ),
						CP_VERSION,
						true
					);
				} else {
					// Register Social Media styles.
					cp_enqueue_social_media( 'info_bar', $live_styles );
					wp_enqueue_style( 'convert-plus-info-bar-style', CP_PLUGIN_URL . 'modules/info_bar/assets/css/info_bar.min.css', array(), CP_VERSION );
					wp_register_script( 'convert-plus-info-bar-script', CP_PLUGIN_URL . 'modules/info_bar/assets/js/info_bar.min.js', array( 'jquery' ), CP_VERSION, true );
				}
			}

			wp_localize_script( 'convert-plus-info-bar-script', 'smile_ajax', array( 'url' => admin_url( 'admin-ajax.php' ) ) );
		}
	}

	$convert_plug_smile_info_bars = new Convert_Plug_Smile_Info_Bars();
}

if ( ! function_exists( 'smile_info_bar_popup' ) ) {
	/**
	 * Function Name: smile_info_bar_popup.
	 *
	 * @param  array  $atts    array parameter.
	 * @param  string $content string parameter.
	 */
	function smile_info_bar_popup( $atts, $content = null ) {
		shortcode_atts(
			array(
				'style'      => '',
				'style_name' => '',
			),
			$atts
		);
		$style      = isset( $atts['style'] ) ? $atts['style'] : '';
		$style_name = isset( $atts['style_name'] ) ? $atts['style_name'] : '';
		$output     = '';
		$func       = 'info_bar_theme_' . $style;

		$settings = base64_decode( $atts['settings_encoded'] );
		if ( isset( $atts['website'] ) && 'yes' === $atts['website'] ) {
			$settings       = maybe_unserialize( $settings );
			$style_settings = $settings;
		} else {
			$style_settings = json_decode( $settings, true );
		}

		// Remove back slashes from settings.
		$settings = stripslashes_deep( $style_settings );

		$settings                 = wp_json_encode( $settings );
		$settings_encoded         = base64_encode( $settings );
		$atts['settings_encoded'] = $settings_encoded;

		if ( function_exists( $func ) ) {
			$output = $func( $atts );
		}
		echo $output; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	add_shortcode( 'smile_info_bar', 'smile_info_bar_popup' );
}

if ( ! function_exists( 'cp_info_bar_custom' ) ) {
	/**
	 * Function Name: cp_info_bar_custom.
	 *
	 * @param  array  $atts    array parameter.
	 * @param  string $content string parameter.
	 */
	function cp_info_bar_custom( $atts, $content = null ) {
		ob_start();
		$id      = '';
		$display = '';
		shortcode_atts(
			array(
				'id'      => '',
				'display' => '',
			),
			$atts
		);

		$id      = isset( $atts['id'] ) ? $atts['id'] : '';
		$display = isset( $atts['display'] ) ? $atts['display'] : '';

		$live_styles = cp_get_live_styles( 'info_bar' );
		$live_array  = '';
		$settings    = '';
		foreach ( $live_styles as $key => $info_bar_array ) {
			$style_id = $info_bar_array['style_id'];

			$settings = maybe_unserialize( $info_bar_array['style_settings'] );
			if ( isset( $settings['variant_style_id'] ) && $id === $settings['style_id'] ) {
				$id = $settings['variant_style_id'];
			}

			if ( $id === $style_id ) {
				$live_array     = $info_bar_array;
				$settings       = maybe_unserialize( $info_bar_array['style_settings'] );
				$settings_array = maybe_unserialize( $info_bar_array['style_settings'] );
				foreach ( $settings_array as $key => $setting ) {
					$style_settings[ $key ] = apply_filters( 'smile_render_setting', $setting );
				}
				$info_bar_style                  = $style_settings['style'];
				$global                          = $style_settings['global'];
				$style_settings['display']       = $display;
				$style_settings['custom_class'] .= isset( $style_settings['custom_class'] ) ? $style_settings['custom_class'] . ',cp-trigger-' . $style_id : 'cp-trigger-' . $style_id;
				$display                         = cp_is_style_visible( $style_settings );

				// Remove back slashes from settings.
				$settings = stripslashes_deep( $settings );

				$encode_settings  = wp_json_encode( $style_settings );
				$settings_encoded = base64_encode( $encode_settings );

				echo '<span class="cp-trigger-shortcode cp-trigger-' . esc_attr( $style_id ) . ' cp-' . esc_attr( $style_id ) . '">' . do_shortcode( $content ) . '</span>';
				if ( $display ) {
					// Generate style ID.
					$id = $info_bar_style . '-' . $style_id;

					// Individual Style Path.
					$file_name = '/assets/demos/' . strtolower( $info_bar_style ) . '/' . strtolower( $info_bar_style ) . '.min.css';
					$url       = CP_PLUGIN_URL . 'modules/info_bar/' . $file_name;

					// Check file exist or not - and append to the head.
					wp_enqueue_style( esc_attr( $id ), esc_url( $url ), array(), CP_VERSION );
					echo do_shortcode( '[smile_info_bar manual="true" style_id = ' . $style_id . ' style="' . $info_bar_style . '" settings_encoded="' . $settings_encoded . ' "][/smile_info_bar]' );
					$css = isset( $settings['custom_css'] ) ? urldecode( $settings['custom_css'] ) : '';
					apply_filters( 'cp_custom_css', $style_id, $css );
				}
				break;
			}
		}
		return ob_get_clean();
	}
	add_shortcode( 'cp_info_bar', 'cp_info_bar_custom' );
}
