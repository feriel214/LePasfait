<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

if ( ! defined( 'CP_BASE_DIR_SLIDEIN' ) ) {
	define( 'CP_BASE_DIR_SLIDEIN', plugin_dir_path( __FILE__ ) );
}

require_once CP_BASE_DIR_SLIDEIN . '/functions/functions.php';

if ( ! class_exists( 'Convert_Plug_Smile_Slide_Ins' ) ) {
	/**
	 * Class Convert_Plug_Smile_Slide_Ins.
	 */
	class Convert_Plug_Smile_Slide_Ins extends Convert_Plug {
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

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_front_scripts' ), 100 );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
			add_action( 'admin_menu', array( $this, 'add_admin_menu_page' ), 999 );
			add_action( 'admin_head', array( $this, 'load_customizer_scripts' ) );
			add_action( 'wp_footer', array( $this, 'load_slide_in_globally' ) );
			add_action( 'init', array( $this, 'register_theme_templates' ) );
			add_filter( 'admin_body_class', array( $this, 'cp_admin_body_class' ) );
			require_once CP_BASE_DIR_SLIDEIN . 'slide-in-preset.php';
		}

		/**
		 * Function Name: cp_admin_body_class.
		 *
		 * @param  string $classes string parameter.
		 * @return string          string parameter.
		 */
		public function cp_admin_body_class( $classes ) {
			if ( isset( $_REQUEST['cp_admin_page_nonce'] ) && wp_verify_nonce( $_REQUEST['cp_admin_page_nonce'], 'cp_admin_page' ) ) {
				if ( isset( $_GET['style-view'] ) && 'new' === $_GET['style-view'] ) {
					$classes  = str_replace( 'cp-add-new-style', '', $classes );
					$classes .= 'cp-add-new-style';
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
				'Slide In Designer',
				'Slide In',
				'access_cp',
				'smile-slide_in-designer',
				array( $this, 'slide_in_dashboard' )
			);
			$obj  = new parent();
			add_action( 'admin_print_scripts-' . $page, array( $obj, 'convert_admin_scripts' ) );
			add_action( 'admin_print_scripts-' . $page, array( $this, 'slide_in_admin_scripts' ) );
			add_action( 'admin_footer-' . $page, array( $this, 'cp_admin_footer' ) );
		}

		/**
		 * Function Name: slide_in_admin_scripts.
		 */
		public function slide_in_admin_scripts() {
			if ( isset( $_REQUEST['cp_admin_page_nonce'] ) && wp_verify_nonce( $_REQUEST['cp_admin_page_nonce'], 'cp_admin_page' ) ) {
				if ( ( isset( $_GET['style-view'] ) && ( 'edit' === $_GET['style-view'] || 'variant' === $_GET['style-view'] ) ) || ! isset( $_GET['style-view'] ) ) {
					wp_enqueue_script( 'convert-plus-slide_in-receiver', CP_PLUGIN_URL . 'modules/slide_in/assets/js/receiver.js', array(), CP_VERSION, false );
					wp_enqueue_style( 'convert-plus-contacts', CP_PLUGIN_URL . 'admin/contacts/css/cp-contacts.css', array(), CP_VERSION );
					wp_enqueue_media();
					wp_enqueue_script( 'convert-plus-slide_in-importer', CP_PLUGIN_URL . 'modules/assets/js/admin-media.js', array( 'jquery' ), CP_VERSION, true );
				}

				if ( isset( $_GET['style-view'] ) && 'analytics' === $_GET['style-view'] ) {
					wp_enqueue_style( 'convert-plus-css-select2', CP_PLUGIN_URL . 'admin/assets/select2/select2.min.css', array(), CP_VERSION );
					wp_enqueue_script( 'convert-plus-select2', CP_PLUGIN_URL . 'admin/assets/select2/select2.min.js', array(), CP_VERSION, false );
					wp_enqueue_script( 'bsf-charts-js', CP_PLUGIN_URL . 'admin/assets/js/chart.js', array(), CP_VERSION, false );
					wp_enqueue_script( 'bsf-charts-bar-js', CP_PLUGIN_URL . 'admin/assets/js/chart.bar.js', array(), CP_VERSION, false );
					wp_enqueue_script( 'bsf-charts-donut-js', CP_PLUGIN_URL . 'admin/assets/js/chart.donuts.js', array(), CP_VERSION, false );
					wp_enqueue_script( 'bsf-charts-line-js', CP_PLUGIN_URL . 'admin/assets/js/Chart.Line.js', array(), CP_VERSION, false );
					wp_enqueue_script( 'bsf-charts-polararea-js', CP_PLUGIN_URL . 'admin/assets/js/Chart.PolarArea.js', array(), CP_VERSION, false );
					wp_enqueue_script( 'bsf-style-analytics-js', CP_PLUGIN_URL . 'modules/assets/js/style-analytics.js', array(), CP_VERSION, false );
				}
			}
		}

		/**
		 * Function Name: slide_in_dashboard.
		 */
		public function slide_in_dashboard() {
			if ( isset( $_REQUEST['cp_admin_page_nonce'] ) && wp_verify_nonce( $_REQUEST['cp_admin_page_nonce'], 'cp_admin_page' ) ) {
				$page           = isset( $_GET['style-view'] ) ? esc_attr( $_GET['style-view'] ) : 'main';
				$prev_styles    = get_option( 'smile_slide_in_styles' );
				$variant_tests  = get_option( 'slide_in_variant_tests' );
				$analytics_data = get_option( 'smile_style_analytics' );

				// load default option set.
				if ( is_admin() ) {
					require_once CP_BASE_DIR_SLIDEIN . '/functions/functions.options.php';
				}

				switch ( $page ) {
					case 'main':
						require_once CP_BASE_DIR_SLIDEIN . '/views/main.php';
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
						require_once CP_BASE_DIR_SLIDEIN . '/functions/functions.php';
						if ( function_exists( 'cp_enqueue_google_fonts' ) ) {
							cp_enqueue_google_fonts( $gfonts );
						}
						require_once CP_BASE_DIR_SLIDEIN . '/views/new-style.php';
						break;
					case 'edit':
						require_once CP_BASE_DIR_SLIDEIN . '/views/edit.php';
						break;
					case 'variant':
						require_once CP_BASE_DIR_SLIDEIN . '/views/variant.php';
						break;
					case 'analytics':
						require_once CP_BASE_DIR_SLIDEIN . '/views/analytics.php';
						break;
				}
			}
		}

		/**
		 * Function Name: load_slide_in_globally.
		 */
		public function load_slide_in_globally() {
			$slide_in_style        = '';
			$slide_in_style_delay  = '';
			$slide_in_cookie_delay = '';
			$live_styles           = cp_get_live_styles( 'slide_in' );
			$prev_styles           = get_option( 'smile_slide_in_styles' );
			$smile_variant_tests   = get_option( 'slide_in_variant_tests' );

			if ( is_array( $live_styles ) && ! empty( $live_styles ) ) {

				global $post;
				$slide_in_arrays = $live_styles;
				$taxonomies      = get_post_taxonomies( $post );

				foreach ( $slide_in_arrays as $key => $slide_in_array ) {
					$display          = false;
					$settings_encoded = '';

					$style_settings     = array();
					$global_display     = '';
					$pages_to_exclude   = '';
					$cats_to_exclude    = '';
					$exclusive_pages    = '';
					$exclusive_cats     = '';
					$show_for_logged_in = '';
					$settings_array     = maybe_unserialize( $slide_in_array['style_settings'] );

					foreach ( $settings_array as $key => $setting ) {
						$style_settings[ $key ] = apply_filters( 'smile_render_setting', $setting );
					}

					$style_id       = $slide_in_array['style_id'];
					$slide_in_style = $style_settings['style'];

					if ( is_array( $style_settings ) && ! empty( $style_settings ) ) {

						$settings = maybe_unserialize( $slide_in_array['style_settings'] );
						$css      = isset( $settings['custom_css'] ) ? urldecode( $settings['custom_css'] ) : '';

						$display = cp_is_style_visible( $settings );

						// Remove back slashes from settings.
						$settings         = stripslashes_deep( $settings );
						$settings         = wp_json_encode( $settings );
						$settings_encoded = base64_encode( $settings );
					}

					if ( $display ) {

						// Developer mode.
						if ( '1' === self::$cp_dev_mode ) {

							$script_handlers = array(
								'convert-plus-slide-in-common',
								'convert-plus-common-script',
								'convert-plus-slide-in-script',
								'convert-plus-ideal-timer-script',
								'convert-plus-slide-in-mailer-script',
								'convert-plus-frosty-script',
								'convert-plus-perfect-scroll-js',
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

							if ( ! wp_script_is( 'convert-plus-slide-in-script', 'enqueued' ) ) {
								wp_enqueue_script( 'convert-plus-slide-in-script' );
							}
						}

						// Generate style ID.
						$id = $slide_in_style . '-' . $style_id;

						// Individual Style Path.
						$file_name = 'assets/demos/' . $slide_in_style . '/' . $slide_in_style . '.min.css';
						$url       = CP_PLUGIN_URL . 'modules/slide_in/' . $file_name;

						// Check file exist or not - and append to the head.
						wp_enqueue_style( $id, $url, array(), CP_VERSION );

						echo '<!-- slide_in Shortcode -->';
						echo do_shortcode( '[smile_slide_in style_id = ' . $style_id . ' style="' . $slide_in_style . '" settings_encoded="' . $settings_encoded . ' "][/smile_slide_in]' );
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
			if ( isset( $_GET['_wpnonce'] ) && ! wp_verify_nonce( $_GET['_wpnonce'], 'slide_in_edit' ) ) {
				return;
			}
			if ( ( isset( $_GET['hidemenubar'] ) && isset( $_GET['module'] ) && 'slide_in' === $_GET['module'] ) ) {
				wp_enqueue_script( 'convert-plus-perfect-scroll-js-back', CP_PLUGIN_URL . 'admin/assets/js/perfect-scrollbar.jquery.js', array( 'jquery' ), CP_VERSION, false );
				wp_enqueue_script( 'convert-plus-common-functions-js' );
				wp_enqueue_script( 'convert-plus-admin-customizer-js', CP_PLUGIN_URL . 'modules/assets/js/admin.customizer.js', array( 'jquery' ), CP_VERSION, false );
				wp_enqueue_script( 'convert-plus-slide_in-editor', CP_PLUGIN_URL . 'modules/assets/js/ckeditor/ckeditor.js', array( 'convert-plus-customizer-js' ), CP_VERSION, false );
				wp_register_script( 'convert-plus-frosty-script', CP_PLUGIN_URL . 'admin/assets/js/frosty.js', array( 'jquery' ), CP_VERSION, true );
				wp_enqueue_script( 'convert-plus-frosty-script' );

				require_once CP_BASE_DIR_SLIDEIN . '/functions/functions.options.php';
				$demo_html     = '';
				$customizer_js = '';
				$settings      = $this::$options;
				foreach ( $settings as $style => $options ) {
					if ( $style == $_GET['theme'] ) {
						$customizer_js = $options['customizer_js'];
					}
				}
				if ( '' !== $customizer_js ) {
					wp_enqueue_script( 'convert-plus-style-customizer-js', $customizer_js, array( 'jquery' ), CP_VERSION, true );
				}
			}
		}

		/**
		 * Function Name: enqueue_admin_scripts.
		 *
		 * @param  array $hook   array parameters.
		 */
		public function enqueue_admin_scripts( $hook ) {
			if ( isset( $_GET['_wpnonce'] ) && ! wp_verify_nonce( $_GET['_wpnonce'], 'slide_in_edit' ) ) {
				return;
			}
			if ( ( isset( $_GET['hidemenubar'] ) && 'slide_in' === $_GET['module'] )
				|| ( isset( $_GET['style-view'] ) && 'new' === $_GET['style-view'] && CP_PLUS_SLUG . '_page_smile-slide_in-designer' === $hook ) ) {
				wp_enqueue_style( 'convert-plus-animate-style', CP_PLUGIN_URL . 'modules/assets/css/animate.css', array(), CP_VERSION );
				wp_enqueue_style( 'convert-plus-slide_in', CP_PLUGIN_URL . 'modules/slide_in/assets/css/slide_in.min.css', array(), CP_VERSION, false );

				$handel = 'jquery';

				wp_register_script( 'convert-plus-slide_in-common', CP_PLUGIN_URL . 'modules/slide_in/assets/js/slide_in.common.js', array( 'jquery' ), CP_VERSION, true );
				wp_register_script( 'convert-plus-common-functions-js', CP_PLUGIN_URL . 'modules/slide_in/assets/js/functions-common.js', 'convert-plus-slide_in-common', CP_VERSION, true );
				wp_enqueue_script( 'convert-plus-slide_in-common' );
				wp_localize_script(
					'convert-plus-slide_in-common',
					'cp',
					array(
						'demo_dir'       => CP_PLUGIN_URL . 'modules/slide_in/assets/demos',
						'module'         => 'slide_in',
						'module_img_dir' => CP_PLUGIN_URL . 'modules/assets/images',
					)
				);

				// Add 'Theme Name' as a class to <html> tag.
				// To provide theme compatibility.
				$theme_name = wp_get_theme();
				$theme_name = $theme_name->get( 'Name' );
				$theme_name = strtolower( preg_replace( '/[\s_]/', '-', $theme_name ) );

				wp_localize_script( 'convert-plus-slide_in-common', 'cp_active_theme', array( 'slug' => $theme_name ) );
				wp_localize_script( 'convert-plus-slide_in-common', 'smile_ajax', array( 'url' => admin_url( 'admin-ajax.php' ) ) );
			}
		}

		/**
		 * Function Name: enqueue_front_scripts.
		 */
		public function enqueue_front_scripts() {

			$live_styles = cp_get_live_styles( 'slide_in' );

			// If any style is live or modal is in live preview mode then only enqueue scripts and styles.
			if ( $live_styles && 0 < count( $live_styles ) ) {

				if ( '1' === self::$cp_dev_mode ) {
					// Register Social Media styles.
					cp_enqueue_social_media( 'slide_in', $live_styles, '1' );
					// Register styles.
					wp_enqueue_style( 'convert-plus-slide-in-style', CP_PLUGIN_URL . 'modules/slide_in/assets/css/slide_in.css', array(), CP_VERSION );
					wp_enqueue_style( 'convert-plus-style', CP_PLUGIN_URL . 'modules/assets/css/convertplug.css', array(), CP_VERSION );
					wp_enqueue_style( 'convert-plus-perfect-scroll-style' );

					// Register Animation CSS.
					wp_enqueue_style( 'convert-plus-animate', CP_PLUGIN_URL . 'modules/assets/css/unminified-animation/main-animation.css', array(), CP_VERSION ); // Main Animation File required for all types.

					// Register scripts.
					wp_enqueue_style( 'convert-plus-frosty-style', CP_PLUGIN_URL . 'admin/assets/css/frosty.css', array(), CP_VERSION );
					wp_register_script( 'convert-plus-slide-in-common', CP_PLUGIN_URL . 'modules/slide_in/assets/js/slide_in.common.js', array( 'jquery' ), CP_VERSION, true );

					wp_register_script( 'convert-plus-common-script', CP_PLUGIN_URL . 'modules/assets/js/convertplug-common.js', array( 'jquery' ), CP_VERSION, true );
					wp_register_script( 'convert-plus-slide-in-script', CP_PLUGIN_URL . 'modules/slide_in/assets/js/slide_in.js', array( 'jquery', 'convert-plus-common-script' ), CP_VERSION, true );

					wp_register_script(
						'convert-plus-slide-in-mailer-script',
						CP_PLUGIN_URL . 'modules/slide_in/assets/js/mailer.js',
						array( 'jquery' ),
						CP_VERSION,
						true
					);
					wp_register_script( 'convert-plus-frosty-script', CP_PLUGIN_URL . 'admin/assets/js/frosty.js', array( 'jquery' ), CP_VERSION, true );

				} else {
					// Register Social Media styles.
					cp_enqueue_social_media( 'slide_in', $live_styles );
					wp_register_script( 'convert-plus-slide-in-script', CP_PLUGIN_URL . 'modules/slide_in/assets/js/slide_in.min.js', array( 'jquery' ), CP_VERSION, true );
					wp_enqueue_style( 'convert-plus-module-main-style' );
					wp_enqueue_style( 'convert-plus-slide-in-style', CP_PLUGIN_URL . 'modules/slide_in/assets/css/slide_in.min.css', array(), CP_VERSION );
				}

				wp_localize_script( 'convert-plus-slide-in-script', 'smile_ajax', array( 'url' => admin_url( 'admin-ajax.php' ) ) );
			}
		}
	}

	$convert_plug_smile_slide_ins = new Convert_Plug_Smile_Slide_Ins();

}

if ( ! function_exists( 'smile_slide_in_popup' ) ) {
	/**
	 * Sunction name : smile_slide_in_popup.
	 *
	 * @param  array  $atts    array parameters.
	 * @param  string $content string parameter.
	 */
	function smile_slide_in_popup( $atts, $content = null ) {
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
		$func       = 'slide_in_theme_' . $style;

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
		echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	add_shortcode( 'smile_slide_in', 'smile_slide_in_popup' );
}


if ( ! function_exists( 'cp_slide_in_custom' ) ) {
	/**
	 * Function name : cp_slide_in_custom.
	 *
	 * @param  array  $atts    array parameters.
	 * @param  string $content string parameter.
	 */
	function cp_slide_in_custom( $atts, $content = null ) {
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
		$id          = isset( $atts['id'] ) ? $atts['id'] : '';
		$display     = isset( $atts['display'] ) ? $atts['display'] : '';
		$live_styles = cp_get_live_styles( 'slide_in' );
		$live_array  = '';
		$settings    = '';
		foreach ( $live_styles as $key => $slide_in_array ) {
			$style_id = $slide_in_array['style_id'];

			$settings = maybe_unserialize( $slide_in_array['style_settings'] );
			if ( isset( $settings['variant_style_id'] ) && $id == $settings['style_id'] ) {
				$id = $settings['variant_style_id'];
			}

			if ( $id == $style_id ) {
				$live_array     = $slide_in_array;
				$settings       = maybe_unserialize( $slide_in_array['style_settings'] );
				$settings_array = maybe_unserialize( $slide_in_array['style_settings'] );
				foreach ( $settings_array as $key => $setting ) {
					$style_settings[ $key ] = apply_filters( 'smile_render_setting', $setting );
				}
				$slide_in_style = $style_settings['style'];
				$global         = $style_settings['global'];
				if ( $display ) {
					$global = false;
				} else {
					$style_settings['global'] = true;
				}
				$style_settings['display']       = $display;
				$style_settings['custom_class'] .= isset( $style_settings['custom_class'] ) ? $style_settings['custom_class'] . ',cp-trigger-' . $style_id : 'cp-trigger-' . $style_id;

				$display = cp_is_style_visible( $settings );

				// Remove back slashes from settings.
				$settings = stripslashes_deep( $settings );

				$encode_settings  = wp_json_encode( $style_settings );
				$settings_encoded = base64_encode( $encode_settings );

				echo '<span class="cp-trigger-shortcode cp-trigger-' . esc_attr( $style_id ) . ' cp-' . esc_attr( $style_id ) . '">' . do_shortcode( $content ) . '</span>';

				if ( $display ) {

					// Individual Style Path.
					$file_name = '/assets/demos/' . $slide_in_style . '/' . $slide_in_style . '.min.css';
					$url       = CP_PLUGIN_URL . 'modules/slide_in/' . $file_name;

					wp_enqueue_style( esc_attr( $id ), esc_url( $url ), array(), CP_VERSION );

					echo do_shortcode( '[smile_slide_in manual="true" style_id = ' . $style_id . ' style="' . $slide_in_style . '" settings_encoded="' . $settings_encoded . ' "][/smile_slide_in]' );
					$css = isset( $settings['custom_css'] ) ? urldecode( $settings['custom_css'] ) : '';
					apply_filters( 'cp_custom_css', $style_id, $css );
				}
				break;
			}
		}
		return ob_get_clean();
	}
	add_shortcode( 'cp_slide_in', 'cp_slide_in_custom' );
}
