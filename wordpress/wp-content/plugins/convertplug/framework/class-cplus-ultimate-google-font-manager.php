<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

/*
* Add-on Name: Google Font Manager
* Add-on URI: https://www.brainstormforce.com
* Usage:
	# VC Params Type: (Note - "ultimate_google_fonts_style" param must be next to "ultimate_google_fonts" param only)
		1) ultimate_google_fonts - for dropdown of google fonts in collection
			For Ex -
			array(
				"type" => "ultimate_google_fonts",
				"heading" => __("Font Family", "smile"),
				"param_name" => "heading_font"
			),
		2) ultimate_google_fonts_style - for respective google font style or default style
			For Ex -
			array(
				"type" => "ultimate_google_fonts_style",
				"heading" 		=>	__("Font Style", "smile"),
				"param_name"	=>	"heading_style"
			),
	# In respective comoponent shortcode process function
		1) Get font family -
			For Ex -
			$font_family = get_ultimate_font_family($heading_font);
		2) Get font style -
			For Ex -
			$font_style = get_ultimate_font_style($heading_style);
			// deprecated since 3.7.0 - automatically detected font and enqueue accordingly
		3) Enqueue the respective fonts - Note send number of font param as a parameter in array
			For Ex -
			$args = array(
				$heading_font
			);
			enquque_ultimate_google_fonts($args);
*/
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

if ( isset( $_REQUEST['cp_admin_page_nonce'] ) && ! wp_verify_nonce( $_REQUEST['cp_admin_page_nonce'], 'cp_admin_page' ) ) {
	wp_die( 'No direct script access allowed!' );
}

if ( ! class_exists( 'Convert_Plug_Ultimate_Google_Font_Manager' ) ) {
	/**
	 * Class Convert_Plug_Ultimate_Google_Font_Manager.
	 */
	class Convert_Plug_Ultimate_Google_Font_Manager {

		/**
		 * Constructor.
		 */
		public function __construct() {

			add_action( 'wp_ajax_cplus_ultimate_google_fonts_refresh', array( $this, 'refresh_google_fonts_list' ) );
			add_action( 'wp_ajax_cplus_get_google_fonts', array( $this, 'get_google_fonts_list' ) );
			add_action( 'wp_ajax_cplus_add_google_font', array( $this, 'add_selected_google_font' ) );
			add_action( 'wp_ajax_cplus_delete_google_font', array( $this, 'delete_selected_google_font' ) );
			add_action( 'wp_ajax_cplus_update_google_font', array( $this, 'update_selected_google_font' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_ultimate_google_fonts' ) );
		}

		/**
		 * Function Name: admin_google_font_scripts.
		 */
		public function admin_google_font_scripts() {
			wp_register_script( 'convert-plus-ultimate-google-fonts-script', SMILE_FRAMEWORK_URI . '/assets/js/google-fonts-admin.js', array( 'jquery' ), CP_VERSION, false );
			wp_enqueue_script( 'convert-plus-ultimate-google-fonts-script' );
			wp_register_style( 'convert-plus-ultimate-google-fonts-style', SMILE_FRAMEWORK_URI . '/assets/css/google-fonts-admin.css', array(), CP_VERSION );
			wp_enqueue_style( 'convert-plus-ultimate-google-fonts-style' );
		}

		/**
		 * Function Name: enqueue_selected_ultimate_google_fonts.
		 */
		public function enqueue_selected_ultimate_google_fonts() {
			$selected_fonts    = get_option( 'cplus_ultimate_selected_google_fonts' );
			$subset_main_array = array();
			if ( ! empty( $selected_fonts ) ) {

				$count     = count( $selected_fonts );
				$font_call = '';
				foreach ( $selected_fonts as $key => $sfont ) {
					$variants_array = array();
					if ( 0 !== $key ) {
						$font_call .= '|';
					}
					$font_call .= $sfont['font_family'];
					if ( isset( $sfont['variants'] ) ) :
						$variants = $sfont['variants'];
						if ( ! empty( $variants ) ) {
							$font_call .= ':';
							foreach ( $variants as  $variant ) {
								$variant_selected = $variant['variant_selected'];
								if ( 'true' === $variant_selected || is_admin() ) {
									array_push( $variants_array, $variant['variant_value'] );
								}
							}
							$variants_count = count( $variants_array );
							if ( 0 !== $variants_count ) {
								$font_call .= 'normal,';
							}
							foreach ( $variants_array as $vkey => $variant ) {
								$font_call .= $variant;
								if ( ( $variants_count - 1 ) !== $vkey && 0 < $variants_count ) {
									$font_call .= ',';
								}
							}
						}
					endif;

					if ( ! empty( $sfont['subsets'] ) ) {
						$subset_array = array();
						foreach ( $sfont['subsets'] as $tsubset ) {
							if ( 'true' === $tsubset['subset_selected'] || true === $tsubset['subset_selected'] ) {
								array_push( $subset_main_array, $tsubset['subset_value'] );
							}
						}
					}
				}

				$subset_string = '';

				if ( ! empty( $subset_main_array ) ) {
					$subset_main_array = array_unique( $subset_main_array );

					$subset_string     = '&subset=';
					$subset_count      = count( $subset_main_array );
					$subset_main_array = array_values( $subset_main_array );

					foreach ( $subset_main_array as $skey => $subset ) {
						if ( '' !== $subset ) {
							$subset_string .= $subset;
							if ( ( $subset_count - 1 ) !== $skey ) {
								$subset_string .= ',';
							}
						}
					}
				}

				$link = 'https://fonts.googleapis.com/css?family=' . $font_call;

				$font_api_call = $link . $subset_string;

				wp_register_style( 'convert-plus-ultimate-selected-google-fonts-style', $font_api_call, array(), CP_VERSION );
				wp_enqueue_style( 'convert-plus-ultimate-selected-google-fonts-style' );
			}
		}

		/**
		 * Function Name: enqueue_ultimate_google_fonts.
		 */
		public function enqueue_ultimate_google_fonts() {
			$selected_fonts = get_option( 'cplus_ultimate_selected_google_fonts' );
			if ( ! empty( $selected_fonts ) ) {
				$count     = count( $selected_fonts );
				$font_call = '';
				foreach ( $selected_fonts as $key => $sfont ) {
					if ( 0 !== $key ) {
						$font_call .= '|';
					}
					$font_call .= $sfont['font_family'];
					if ( isset( $sfont['variants'] ) ) :
						$variants = $sfont['variants'];
						if ( ! empty( $variants ) ) {
							$variants_count = count( $variants );
							$font_call     .= ':';
							foreach ( $variants as $vkey => $variant ) {
								$variant_selected = $variant['variant_selected'];
								if ( 'true' === $variant_selected || is_admin() ) {
									$font_call .= $variant['variant_value'];
									if ( ( $variants_count - 1 ) != $vkey && 0 < $variants_count ) {
										$font_call .= ',';
									}
								}
							}
						}
					endif;
				}
				$link = 'https://fonts.googleapis.com/css?family=' . $font_call;
				wp_register_style( 'convert-plus-ultimate-selected-google-fonts-style', $link, array(), CP_VERSION );
				wp_enqueue_style( 'convert-plus-ultimate-selected-google-fonts-style' );
			}
		}

		/**
		 * Function Name: ultimate_font_manager_dashboard.
		 */
		public function cplus_ultimate_font_manager_dashboard() {
			?>
			<div class="wrap uavc-gfont">
				<h2>
					<?php esc_attr_e( 'Google Fonts Manager', 'smile' ); ?>
					<input style="cursor:pointer" type="button" class="add-new-h2" id="cplus-refresh-google-fonts" value="<?php esc_attr_e( 'Refresh Font List', 'smile' ); ?>"/>
					&nbsp;<span class="spinner"></span>
				</h2>
				<?php
				$cplus_refresh_font_list_nonce = wp_create_nonce( 'cplus_refresh_font_list_nonce' );
				$add_font_nonce                = wp_create_nonce( 'cp_add_font_nonce' );
				$delete_font_nonce             = wp_create_nonce( 'cp_delete_font_nonce' );
				$update_font_nonce             = wp_create_nonce( 'cp_update_font_nonce' );
				$search_font_nonce             = wp_create_nonce( 'cp_search_font_nonce' );

				?>
				<input type="hidden" id="cplus_refresh_font_list_nonce" value="<?php echo esc_attr( $cplus_refresh_font_list_nonce ); ?>">
				<input type="hidden" id="cp_add_font_nonce" value="<?php echo esc_attr( $add_font_nonce ); ?>">
				<input type="hidden" id="cp_delete_font_nonce" value="<?php echo esc_attr( $delete_font_nonce ); ?>">
				<input type="hidden" id="cp_update_font_nonce" value="<?php echo esc_attr( $update_font_nonce ); ?>">
				<input type="hidden" id="cp_search_font_nonce" value="<?php echo esc_attr( $search_font_nonce ); ?>">

				<div id="vc-gf-msg"></div>
				<div class="nav">

					<input type="text" id="search_gfont" name="search_gfont" placeholder="<?php esc_html_e( 'Search font..', 'smile' ); ?>"/>
				</div>
				<div class="fonts-list">
					<div id="fonts-list-wrapper" style="overflow:auto" data-gstart="0" data-gfetch="20"></div>
					<div id="load-more" class="spinner" style="float:left"></div>
				</div>
				<div class="fonts-selected-list">
					<h3><?php esc_html_e( 'Your Font Collection', 'smile' ); ?></h3>
					<div id="fonts-selected-wrapper">
						<?php
						$selected_fonts = get_option( 'cplus_ultimate_selected_google_fonts' );
						if ( ! empty( $selected_fonts ) ) {
							foreach ( $selected_fonts as $key => $sfont ) {
								?>
								<div class="selected-font">
									<div class="selected-font-top <?php echo ( ! empty( $sfont['variants'] ) || ! empty( $sfont['subsets'] ) ) ? 'have-variants' : ''; ?>">
										<div class="font-header" style="font-family:'<?php echo esc_attr( sanitize_text_field( $sfont['font_name'] ) ); ?>'"><?php echo esc_attr( sanitize_text_field( $sfont['font_name'] ) ); ?></div>
										<?php if ( ! empty( $sfont['variants'] ) || ! empty( $sfont['subsets'] ) ) : ?>
											<i class="dashicons dashicons-arrow-down"></i>
										<?php endif; ?>
										<div class="clear"></div>
									</div>
									<span class="font-delete" data-font_name="<?php echo esc_attr( $sfont['font_name'] ); ?>"><i class="dashicons dashicons-no-alt"></i></span>
									<?php
									$is_varients = false;
									if ( ! empty( $sfont['variants'] ) || ! empty( $sfont['subsets'] ) ) :
										?>
										<div class="selected-font-content">
											<?php
											$lid = str_replace( ' ', '-', sanitize_text_field( $sfont['font_name'] ) );

											$variant_font = 'font-family:\'' . sanitize_text_field( $sfont['font_name'] ) . '\';';

											if ( ! empty( $sfont['variants'] ) ) :
												$is_varients = true;
												?>
												<div class="selected-font-varient-wrapper">
													<?php
													foreach ( $sfont['variants'] as $svkey => $svariants ) {
														$variant_style = $variant_font;
														if ( preg_match( '/italic/i', $svariants['variant_value'] ) ) {
															$variant_style .= 'font-style:italic;';
														}
														$weight = 'normal';
														$weight = preg_replace( '/\D/', '', $svariants['variant_value'] );
														if ( $weight ) {
															$variant_style .= 'font-weight:' . $weight . ';';
														}
														$tlid = $lid . '-' . $svkey;
														?>
														<span class="font-variant">
															<input type="checkbox" id="<?php echo esc_attr( $tlid ); ?>" value="<?php echo esc_attr( $svariants['variant_value'] ); ?>" class="selected-variant-checkbox" <?php echo ( 'true' === $svariants['variant_selected'] ) ? 'checked' : ''; ?> />
															<label style="<?php echo esc_attr( $variant_style ); ?>" for="<?php echo esc_attr( $tlid ); ?>"><?php echo esc_html( $svariants['variant_value'] ); ?></label>
														</span>
														<?php
													}
													?>
												</div>
												<?php
											endif;
											if ( ! empty( $sfont['subsets'] ) ) :
												?>
												<div class="<?php echo ( $is_varients ) ? 'selected-font-subset-wrapper' : ''; ?>">
													<?php
													foreach ( $sfont['subsets'] as $sbkey => $ssubset ) {
														$slid = $lid . '-subset-' . $sbkey;
														?>
														<span class="font-subset">
															<input type="checkbox" id="<?php echo esc_attr( $slid ); ?>" value="<?php echo esc_attr( $ssubset['subset_value'] ); ?>" class="selected-subset-checkbox" <?php echo ( 'true' === $ssubset['subset_selected'] ) ? 'checked' : ''; ?> />
															<label style="" for="<?php echo esc_attr( $slid ); ?>"><?php echo esc_html( $ssubset['subset_value'] ); ?></label>
														</span>
														<?php
													}
													?>
												</div>
												<?php
											endif;
											?>
											<input type="button" class="button alignleft update-google-font-button" value="<?php esc_attr_e( 'Update font', 'smile' ); ?>" data-font_name="<?php echo esc_attr( $sfont['font_name'] ); ?>" />
											<span class="spinner fspinner"></span>
											<div class="clear"></div>
										</div>
										<?php
									endif;
									?>
								</div>
								<?php
							}
						}
						?>
					</div>
				</div>
				<div class="clear"></div>
			</div>
			<?php
		}

		/**
		 * Function Name: refresh_google_fonts_list.
		 */
		public function refresh_google_fonts_list() {

			if ( ! current_user_can( 'access_cp' ) ) {
				die( -1 );
			}
			check_ajax_referer( 'cplus_refresh_font_list_nonce', 'security' );

			$fonts      = array();
			$temp_count = 0;
			$temp       = get_option( 'cplus_ultimate_google_fonts' );
			if ( ! empty( $temp ) ) {
				$temp_count = count( $temp );
			}
			$error = false;

			if ( true === $error || 0 === count( $fonts ) ) {
				$error = false;
				try {
					$filename = 'https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyD_6TR2RyX2VRf8bABDRXCcVqdMXB5FQvs';
					$fonts    = wp_remote_get( $filename );
					$fonts    = json_decode( $fonts['body'] );
				} catch ( Exception $e ) {
					$error = true;
				}
			}
			if ( true !== $error || 0 === count( $fonts ) ) {
				$google_fonts      = $fonts->items;
				$google_font_count = count( $google_fonts );
				update_option( 'cplus_ultimate_google_fonts', $google_fonts );
				$response['count'] = ( $google_font_count - $temp_count );
				$total             = ( $google_font_count - $temp_count );
				/* translators:%s total count */
				$response['message'] = sprintf( __( '%s new fonts added.', 'smile' ), $total );
			} else {
				$response['count']   = 0;
				$response['message'] = __( 'Fonts could not be downloaded as there might be some issue with file_get_contents or wp_remote_get due to your server configuration.', 'smile' );
			}
			wp_send_json( wp_json_encode( $response ) );
		}

		/**
		 * Function Name: get_google_fonts_list.
		 */
		public function get_google_fonts_list() {

			if ( ! current_user_can( 'access_cp' ) ) {
				die( -1 );
			}

			check_ajax_referer( 'cp_search_font_nonce', 'security_nonce' );
			$google_fonts = get_option( 'cplus_ultimate_google_fonts' );
			$response     = array();
			$fonts        = array();
			$search       = '';
			if ( ! empty( $google_fonts ) ) :
				$selected_google_fonts = get_option( 'cplus_ultimate_selected_google_fonts' );
				$temp_selected         = array();
				if ( ! empty( $selected_google_fonts ) ) {
					foreach ( $selected_google_fonts as $selected_font ) {
						array_push( $temp_selected, $selected_font['font_name'] );
					}
				}
				$start_count      = sanitize_text_field( $_POST['start'] );
				$fetch_count      = sanitize_text_field( $_POST['fetch'] );
				$search           = trim( sanitize_text_field( $_POST['search'] ) );
				$font_slice_array = array();

				if ( '' !== $search ) {
					$temp = array();
					foreach ( $google_fonts as $tkey => $tfont ) {
						if ( stripos( $tfont->family, $search ) !== false ) {
							array_push( $temp, $google_fonts[ $tkey ] );
						}
					}
					$font_slice_array = $temp;
				} else {
					$font_slice_array = array_slice( $google_fonts, $start_count, $fetch_count );
				}

				$count = count( $font_slice_array );
				foreach ( $font_slice_array as $key => $tempfont ) {
					$fontinfo = array();
					if ( in_array( $tempfont->family, $temp_selected ) ) {
						$already_selected = 'true';
					} else {
						$already_selected = 'false';
					}
					$font_call = str_replace( ' ', '+', $tempfont->family );
					$variants  = $tempfont->variants;
					$subsets   = $tempfont->subsets;
					$fontinfo  = array(
						'font_name' => $tempfont->family,
						'font_call' => $font_call,
						'variants'  => $variants,
						'subsets'   => $subsets,
						'selected'  => $already_selected,
					);
					array_push( $fonts, $fontinfo );
				}
			endif;
			$response['fonts'] = $fonts;
			if ( is_array( $google_fonts ) ) {
				$response['fonts_count'] = count( $google_fonts );
			}
			if ( '' !== $search ) {
				$response['is_search'] = 'true';
			} else {
				$response['is_search'] = 'false';
			}
			wp_send_json( wp_json_encode( $response ) );
		}

		/**
		 * Function Name: add_selected_google_font.
		 */
		public function add_selected_google_font() {

			if ( ! current_user_can( 'access_cp' ) ) {
				die( -1 );
			}

			check_admin_referer( 'cp_add_font_nonce', 'security_nonce' );

			$font_family = sanitize_text_field( $_POST['font_family'] );
			$font_name   = sanitize_text_field( $_POST['font_name'] );
			$variants    = cp_sanitize_array( $_POST['variants'] );
			$subsets     = cp_sanitize_array( $_POST['subsets'] );

			$fonts = get_option( 'cplus_ultimate_selected_google_fonts' );

			if ( empty( $fonts ) ) {
				$fonts = array();
			}
			$new_font = array(
				'font_family' => $font_family,
				'font_name'   => $font_name,
				'variants'    => $variants,
				'subsets'     => $subsets,
			);

			array_push( $fonts, $new_font );
			update_option( 'cplus_ultimate_selected_google_fonts', $fonts );
			echo 'Added';
			die();
		}

		/**
		 * Function Name: delete_selected_google_font.
		 */
		public function delete_selected_google_font() {

			if ( ! current_user_can( 'access_cp' ) ) {
				die( -1 );
			}

			check_admin_referer( 'cp_delete_font_nonce', 'security_nonce' );

			$font_name = sanitize_text_field( $_POST['font_name'] );
			$fonts     = get_option( 'cplus_ultimate_selected_google_fonts' );

			if ( is_array( $fonts ) ) {
				foreach ( $fonts as $key => $font ) {
					if ( $font['font_name'] == $font_name ) {
						unset( $fonts[ $key ] );
					}
				}
			}

			$fonts = array_values( $fonts );
			update_option( 'cplus_ultimate_selected_google_fonts', $fonts );
			echo 'Deleted';
			die();
		}

		/**
		 * Function Name: update_selected_google_font.
		 */
		public function update_selected_google_font() {

			if ( ! current_user_can( 'access_cp' ) ) {
				die( -1 );
			}

			check_admin_referer( 'cp_update_font_nonce', 'security_nonce' );

			$font_name = sanitize_text_field( $_POST['font_name'] );
			$variants  = cp_sanitize_array( $_POST['variants'] );
			$subsets   = cp_sanitize_array( $_POST['subsets'] );
			$fonts     = get_option( 'cplus_ultimate_selected_google_fonts' );

			if ( is_array( $fonts ) ) {
				foreach ( $fonts as $key => $font ) {
					if ( $font['font_name'] == $font_name ) {
						$fonts[ $key ]['variants'] = $variants;
						$fonts[ $key ]['subsets']  = $subsets;
						$x                         = $key;
					}
				}
			}

			update_option( 'cplus_ultimate_selected_google_fonts', $fonts );
			echo 'Updated';
			die();
		}
	}
	// Instantiate the Google Font Manager.
	new Convert_Plug_Ultimate_Google_Font_Manager();
}
if ( ! function_exists( 'enquque_ultimate_google_fonts' ) ) {
	/**
	 * Function Name: enquque_ultimate_google_fonts.
	 *
	 * @param  array $enqueue_fonts array parameter.
	 */
	function enquque_ultimate_google_fonts( $enqueue_fonts ) {
		$selected_fonts = get_option( 'cplus_ultimate_selected_google_fonts' );

		$fonts       = array();
		$subset_call = '';
		if ( ! empty( $enqueue_fonts ) ) {
			foreach ( $enqueue_fonts as $key => $efont ) {
				$font_name       = '';
				$font_call       = '';
				$font_variant    = '';
				$font_arr        = array();
				$font_call_arr   = array();
				$font_weight_arr = array();
				$font_arr        = explode( '|', $efont );

				if ( isset( $font_arr[1] ) ) {
					$font_call_arr = explode( ':', $font_arr[1] );
					if ( isset( $font_arr[2] ) ) {
						$font_weight_arr = explode( ':', $font_arr[2] );
					}
					if ( isset( $font_call_arr[1] ) && '' !== $font_call_arr[1] ) {
						$font_call = $font_call_arr[1];
						$font_name = $font_call_arr[1];

						foreach ( $selected_fonts as $sfont ) {
							if ( $font_name === $sfont['font_family'] ) {
								if ( ! empty( $sfont['subsets'] ) ) {
									$subset_array = array();
									foreach ( $sfont['subsets'] as $tsubset ) {
										if ( 'true' === $tsubset['subset_selected'] ) {
											array_push( $subset_array, $tsubset['subset_value'] );
										}
									}
									if ( ! empty( $subset_array ) ) :
										$subset_call = '&subset=';
										$j           = count( $subset_array );
										foreach ( $subset_array as $subkey => $subset ) {
											$subset_call .= $subset;
											if ( ( $j - 1 ) !== $subkey ) {
												$subset_call .= ',';
											}
										}
									endif;
								}
							}
						}

						if ( isset( $font_weight_arr[1] ) && '' !== $font_weight_arr[1] ) {
							$font_variant = $font_weight_arr[1];
						}
						$eq_name = str_replace( ' ', '-', $font_name );
						if ( '' !== $font_variant || 'regular' !== $font_variant ) {
							$font_call .= ':' . $font_variant;
							$eq_name   .= '-' . $font_variant;
						}
						$link = 'https://fonts.googleapis.com/css?family=' . $font_call . $subset_call;

						if ( ! wp_script_is( 'convert-plus-ultimate-' . $eq_name, 'registered' ) ) {
							wp_register_style( 'convert-plus-ultimate-' . $eq_name, $link, array(), CP_VERSION );
							wp_enqueue_style( 'convert-plus-ultimate-' . $eq_name );
						} elseif ( wp_script_is( 'convert-plus-ultimate-' . $eq_name, 'registered' ) ) {
							wp_enqueue_style( 'convert-plus-ultimate-' . $eq_name );
						}
					}
				} else // font is without varients.
				{
					$eq_name = $font_arr[0];
					$link    = 'https://fonts.googleapis.com/css?family=' . $eq_name;

					if ( '' !== $eq_name ) {
						if ( ! wp_script_is( 'convert-plus-ultimate-' . $eq_name, 'registered' ) ) {
							wp_register_style( 'convert-plus-ultimate-' . $eq_name, $link, array(), CP_VERSION );
							wp_enqueue_style( 'convert-plus-ultimate-' . $eq_name );
						} elseif ( wp_script_is( 'convert-plus-ultimate-' . $eq_name, 'registered' ) ) {
							wp_enqueue_style( 'convert-plus-ultimate-' . $eq_name );
						}
					}
				}
			}
		}
	}
}
if ( ! function_exists( 'get_ultimate_font_family' ) ) {
	/**
	 * Function Name: get_ultimate_font_family.
	 *
	 * @param  string $font_attributes string parameter.
	 * @return string                  string parameter.
	 */
	function get_ultimate_font_family( $font_attributes ) {
		if ( ! empty( $font_attributes ) ) {
			$font_family_arr = explode( '|', $font_attributes );
			$font_family_str = $font_family_arr[0];
			$font_family     = explode( ':', $font_family_str );
			if ( isset( $font_family[1] ) && '' !== $font_family[1] ) {
				return $font_family[1];
			} else {
				return '';
			}
		} else {
			return '';
		}
	}
}
if ( ! function_exists( 'get_ultimate_font_style' ) ) {
	/**
	 * Function Name: get_ultimate_font_style.
	 *
	 * @param  string $font_style string parameter.
	 * @return string                  string parameter.
	 */
	function get_ultimate_font_style( $font_style ) {
		$weight_match = 0;
		$temp         = '';
		if ( '' !== $font_style ) {
			$font_styles = explode( ',', $font_style ); // split by comma<strong></strong>.
			foreach ( $font_styles as $fstyle ) {
				$temp .= $fstyle; // convert to css.
				if ( preg_match( '/font-weight:/i', $fstyle ) ) {
					$weight_match++;
				}
			}
		}
		// hack to font weight to normal if font weight not available.
		if ( 0 === $weight_match ) {
			$temp .= 'font-weight:normal;';
		}
		return $temp;
	}
}

if ( ! function_exists( 'enquque_ultimate_google_fonts_optimzed' ) ) {
	/**
	 * Function Name: enquque_ultimate_google_fonts_optimzed.
	 *
	 * @param  array $enqueue_fonts array parameter.
	 */
	function enquque_ultimate_google_fonts_optimzed( $enqueue_fonts ) {
		$selected_fonts = get_option( 'cplus_ultimate_selected_google_fonts' );

		$main              = array();
		$subset_main_array = array();
		$fonts             = array();
		$subset_call       = '';

		if ( ! empty( $enqueue_fonts ) ) {
			$font_count = 0;
			foreach ( $enqueue_fonts as $key => $efont ) {
				$font_name       = '';
				$font_call       = '';
				$font_variant    = '';
				$font_arr        = array();
				$font_call_arr   = array();
				$font_weight_arr = array();
				$font_arr        = explode( '|', $efont );

				$font_name = trim( $font_arr[0] );

				if ( ! isset( $main[ $font_name ] ) ) {
					$main[ $font_name ] = array();
				}

				if ( ! empty( $font_name ) ) :

					$font_count++;
					if ( isset( $font_arr[1] ) ) {
						$font_call_arr = explode( ':', $font_arr[1] );

						if ( isset( $font_arr[2] ) ) {
							$font_weight_arr = explode( ':', $font_arr[2] );
						}

						if ( isset( $font_call_arr[1] ) && '' !== $font_call_arr[1] ) {
							$font_variant  = $font_call_arr[1];
							$pre_font_call = $font_name;

							if ( '' !== $font_variant && 'regular' !== $font_variant ) {
								$main[ $font_name ]['varients'][] = $font_variant;
								array_push( $main[ $font_name ]['varients'], $font_variant );
								if ( ! empty( $main[ $font_name ]['varients'] ) ) {
									$main[ $font_name ]['varients'] = array_values( array_unique( $main[ $font_name ]['varients'] ) );
								}
							}
						}
					}

					foreach ( $selected_fonts as $sfont ) {
						if ( $sfont['font_family'] == $font_name ) {
							if ( ! empty( $sfont['subsets'] ) ) {
								$subset_array = array();
								foreach ( $sfont['subsets'] as $tsubset ) {
									if ( 'true' === $tsubset['subset_selected'] ) {
										array_push( $subset_array, $tsubset['subset_value'] );
									}
								}
								if ( ! empty( $subset_array ) ) :
									$subset_call = '';
									$j           = count( $subset_array );
									foreach ( $subset_array as $subkey => $subset ) {
										$subset_call .= $subset;
										if ( ( $j - 1 ) != $subkey ) {
											$subset_call .= ',';
										}
									}
									array_push( $subset_main_array, $subset_call );
								endif;
							}
						}
					}
				endif;
			}

			$link       = 'https://fonts.googleapis.com/css?family=';
			$main_count = count( $main );
			$mcount     = 0;

			foreach ( $main as $font => $font_data ) {
				if ( '' !== $font ) {
					$link .= $font;
					if ( 'Open+Sans+Condensed' === $font && empty( $font_data['varients'] ) ) {
						$link .= ':300';
					}
					if ( ! empty( $font_data['varients'] ) ) {
						$link         .= ':regular,';
						$varient_count = count( $font_data['varients'] );
						foreach ( $font_data['varients'] as $vkey => $varient ) {
							$link .= $varient;
							if ( ( $varient_count - 1 ) != $vkey ) {
								$link .= ',';
							}
						}
					}

					if ( ! empty( $font_data['subset'] ) ) {
						$subset_string .= '&subset=' . $font_data['subset'];
					}

					if ( ( $main_count - 1 ) !== $mcount ) {
						$link .= '|';
					}
					$mcount++;
				}
			}

			$subset_string = '';

			if ( ! empty( $subset_array ) ) {
				$subset_main_array = array_unique( $subset_main_array );

				$subset_string     = '&subset=';
				$subset_count      = count( $subset_main_array );
				$subset_main_array = array_values( $subset_main_array );

				foreach ( $subset_main_array as $skey => $subset ) {
					if ( '' !== $subset ) {
						$subset_string .= $subset;
						if ( ( $subset_count - 1 ) != $skey ) {
							$subset_string .= ',';
						}
					}
				}
			}

			$font_api_call = $link . $subset_string;

			if ( 0 < $font_count ) {
				wp_enqueue_style( 'convert-plus-ultimate-google-fonts', $font_api_call, array(), ULTIMATE_VERSION );
			}
		}
	}
}
