<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

if ( is_admin() ) {
	require_once CP_BASE_DIR . '/admin/import-ajax.php';
}

$ajax_events = array(
	'cp_trash_list'                  => false,
	'update_style_settings'          => false,
	'update_variant_test_settings'   => false,
	'smile_duplicate_style'          => false,
	'smile_delete_style'             => false,
	'cp_reset_analytics_action'      => false,
	'smile_update_modules'           => false,
	'smile_update_global'            => false,
	'smile_update_status'            => false,
	'smile_update_impressions'       => true,
	'smile_add_list'                 => false,
	'cp_add_subscriber'              => true,
	'get_campaign_analytics_data'    => false,
	'get_campaign_daywise_data'      => false,
	'get_style_analytics_data'       => false,
	'is_campaign_exists'             => false,
	'smile_update_settings'          => false,
	'smile_update_debug'             => false,
	'cp_is_list_assigned'            => false,
	'cp_get_posts_by_query'          => false,
	'cp_get_active_campaigns'        => false,
	'cp_import_presets'              => false,
	'cp_import_presets_step2'        => false,
	'cp_trash_contact'               => false,
	'cp_delete_all_modal_action'     => false,
	'custom_form_update_conversions' => true,
	'cp_dismiss_phardata_notice'     => true,
	'cp_verify_google_recaptcha'     => true,
	'cp_google_recaptcha'            => true,
	'cplus_maxmind'                  => true,
);

foreach ( $ajax_events as $event_slug => $is_nopriv ) {

	add_action( 'wp_ajax_' . $event_slug, $event_slug );

	if ( $is_nopriv ) {
		add_action( 'wp_ajax_nopriv_' . $event_slug, $event_slug );
	}
}

// Style export post actions.
if ( is_admin() ) {
	add_action( 'admin_post_cp_export_list', 'handle_cp_export_list_action' );
	add_action( 'admin_post_cp_export_modal', 'cp_export_modal_action' );
	add_action( 'admin_post_cp_export_infobar', 'cp_export_infobar_action' );
	add_action( 'admin_post_cp_export_slidein', 'cp_export_slidein_action' );
	add_action( 'admin_post_cp_export_all_list', 'handle_cp_export_all_list_action' );
	add_action( 'admin_post_cp_export_analytics', 'cp_export_analytics' );
}

if ( ! function_exists( 'cp_trash_list' ) ) {
	/**
	 * Function Name: cp_trash_list Function to accept ajax call for deleting contact list.
	 */
	function cp_trash_list() {

		if ( ! current_user_can( 'access_cp' ) ) {
			die( -1 );
		}

		check_admin_referer( 'cp-delete-list', 'security_nonce' );

		$lists     = get_option( 'smile_lists' );
		$list_id   = esc_attr( $_POST['list_id'] );
		$mailer    = esc_attr( $_POST['mailer'] );
		$list      = $lists[ $list_id ];
		$list_name = str_replace( ' ', '_', strtolower( trim( $list['list-name'] ) ) );

		if ( 'convert_plug' !== $mailer ) {
			$contacts_option = 'cp_' . $mailer . '_' . $list_name;
		} else {
			$contacts_option = 'cp_connects_' . $list_name;
		}

		unset( $lists[ $list_id ] );

		// Delete option which contains campaign contacts.
		$deleted = delete_option( $contacts_option );
		$status  = update_option( 'smile_lists', $lists );
		if ( $status ) {
			wp_send_json(
				array(
					'status' => 'success',
				)
			);
		} else {
			wp_send_json(
				array(
					'status' => 'error',
				)
			);
		}
	}
}

if ( ! function_exists( 'update_style_settings' ) ) {
	/**
	 * Function Name: update_style_settings Function to accept ajax call for updating style settings.
	 */
	function update_style_settings() {

		if ( ! current_user_can( 'access_cp' ) ) {
			die( -1 );
		}

		check_admin_referer( 'cp_customizer_nonce', 'security_nonce' );

		$data = $_POST['style_settings'];

		$pairs              = explode( '&', $data );
		$settings           = array();
		$cp_social_settings = '';
		$cp_form_fields     = '';

		foreach ( $pairs as $pair ) {
			$pair = explode( '=', $pair );
			if ( isset( $settings[ $pair[0] ] ) ) {
				$settings[ $pair[0] ] = cp_sanitize_style_settings( $settings[ $pair[0] ] ) . ',' . cp_sanitize_style_settings( $pair[1] );
			} elseif ( 'cp_social_icon' === $pair[0] ) {
				$cp_social_icon  = preg_replace( array( '~%7C~', '~%3A~', '~%3B~' ), array( '|', ':', ';' ), $pair[1] );
				$social_networks = explode( '|', $cp_social_icon );
				foreach ( $social_networks as $network ) {
					$values = explode( ':', $network );
					if ( isset( $values[2] ) ) {
						$cp_social_settings .= cp_sanitize_style_settings( $values[0] ) . ':' . cp_sanitize_style_settings( $values[1] ) . ':' . cp_sanitize_style_settings( $values[2] ) . '|';
					} else {
						$cp_social_settings .= cp_sanitize_style_settings( $values[0] ) . ':' . cp_sanitize_style_settings( $values[1] ) . '|';
					}
				}
				$settings[ $pair[0] ] = $cp_social_settings;
			} elseif ( 'form_fields' === $pair[0] ) {
				$fields = explode( '%7C', str_replace( '%20', ' ', $pair[1] ) );
				foreach ( $fields as $field ) {
					if ( str_starts_with( $field, 'input_label' ) || str_starts_with( $field, 'input_placeholder' ) ) {
						$cp_form_fields .= wp_kses_post( $field ) . '%7C';
					} else {
						$values = explode( '%3E', $field );
						if ( isset( $values[2] ) ) {
							$key             = explode( '%3B', $values[1] );
							$cp_form_fields .= cp_sanitize_style_settings( $values[0] ) . '%3E' . cp_sanitize_style_settings( $key[0] ) . '%3Border-%3E' . cp_sanitize_style_settings( $values[2] ) . '%7C';
						} elseif ( 'dropdown_options-' === $values[0] ) {
							$cp_form_fields .= cp_sanitize_style_settings( $values[0] ) . '%3E';
							$options         = explode( '%0D%0A', $values[1] );
							foreach ( $options as $option ) {
								$cp_form_fields .= cp_sanitize_style_settings( $option ) . '%0D%0A';
							}
							$cp_form_fields .= '%7C';
						} else {
							$cp_form_fields .= cp_sanitize_style_settings( $values[0] ) . '%3E' . cp_sanitize_style_settings( $values[1] ) . '%7C';
						}
					}
				}
				$settings[ $pair[0] ] = $cp_form_fields;
			} elseif ( 'success_message' === $pair[0] || 'msg_wrong_email' === $pair[0] ) {
				$settings[ $pair[0] ] = wp_kses_post( urldecode( $pair[1] ) );
			} else {
				if ( str_starts_with( $pair[1], '%26lt%3B' ) || str_starts_with( $pair[1], '%3C' ) ) {
					$settings[ $pair[0] ] = wp_kses_post( $pair[1] ); // CKEditor Source HTML Markup.
				} else {
					$settings[ $pair[0] ] = cp_sanitize_style_settings( urldecode( $pair[1] ) );
				}
			}
		}

		$cp_bg_type = get_option( 'cp_new_bg_type' );
		if ( ! $cp_bg_type ) {
			$settings['module_bg_color_type'] = 'image';
		}

		if ( isset( $settings['success_message'] ) ) {

			$settings['success_message'] = do_shortcode( html_entity_decode( stripcslashes( htmlspecialchars( $settings['success_message'], ENT_QUOTES, 'utf-8' ) ), ENT_QUOTES, 'utf-8' ) );

		}

		$theme_name = ucwords( str_replace( '_', ' ', $settings['style'] ) );

		if ( isset( $settings['style_preset'] ) ) {
			$theme_name = ucwords( str_replace( '_', ' ', $settings['style_preset'] ) );
		}

		if ( isset( $_POST['start_date'] ) && isset( $_POST['end_date'] ) ) {
			$settings['schedule'] = array(
				'start' => sanitize_text_field( $_POST['start_date'] ),
				'end'   => sanitize_text_field( $_POST['end_date'] ),
			);
		}

		$style_type = $settings['style_type'];
		$option     = 'smile_' . $style_type . '_styles';

		$option = $settings['option'];

		$prev_styles = get_option( $option );

		$new_style                   = array();
		$style_id                    = isset( $settings['style_id'] ) && '' !== $settings['style_id'] ? $settings['style_id'] : $theme_name;
		$style_name                  = isset( $settings['new_style'] ) && '' !== $settings['new_style'] ? $settings['new_style'] : $theme_name;
		$style_settings              = maybe_serialize( $settings );
		$key                         = ( is_array( $prev_styles ) && ! empty( $prev_styles ) ) ? search_style( $prev_styles, $style_id ) : null;
		$impressions                 = isset( $prev_styles[ $key ]['impressions'] ) ? $prev_styles[ $key ]['impressions'] : 0;
		$conversion                  = isset( $prev_styles[ $key ]['conversion'] ) ? $prev_styles[ $key ]['conversion'] : 0;
		$new_style['style_name']     = stripslashes( $style_name );
		$new_style['style_id']       = $style_id;
		$new_style['style_settings'] = $style_settings;

		if ( is_array( $prev_styles ) && ! empty( $prev_styles ) ) {
			if ( '' !== $key && is_numeric( $key ) ) {
				$prev_styles[ $key ] = $new_style;
			} else {
				array_push( $prev_styles, $new_style );
			}
		} else {
			$prev_styles = array();
			array_push( $prev_styles, $new_style );
		}

		echo esc_attr( $style_name );
		update_option( $option, $prev_styles );
		die();
	}
}

if ( ! function_exists( 'cp_sanitize_style_settings' ) ) {
	/**
	 * Function Name: cp_sanitize_style_settings
	 *
	 * @param  mixed $value   style value.
	 * @return mixed  sanitized value.
	 */
	function cp_sanitize_style_settings( $value ) {
		if ( isset( $value ) ) {
			if ( str_starts_with( $value, 'https' ) || str_starts_with( $value, 'http' ) ) {
				return esc_url( urldecode( urldecode( $value ) ) );
			} elseif ( str_starts_with( $value, '%D' ) || strpos( $value, '%C3%' ) !== false ) { // Support for most common accents.
				return sanitize_text_field( urldecode( $value ) );
			} else {
				return sanitize_text_field( $value );
			}
		}
	}
}

if ( ! function_exists( 'str_starts_with' ) ) {
	/**
	 * Function Name: str_starts_with
	 *
	 * @param  mixed $haystack   The string to search in.
	 * @param  mixed $needle   The substring to search for in the haystack.
	 * @return bool   Returns true if haystack begins with needle, false otherwise.
	 */
	function str_starts_with( $haystack, $needle ) {
		return '' !== (string) $needle && 0 === strncmp( $haystack, $needle, strlen( $needle ) );
	}
}

if ( ! function_exists( 'update_variant_test_settings' ) ) {
	/**
	 * Function Name: update_variant_test_settings Function to accept ajax call for updating variant test settings.
	 */
	function update_variant_test_settings() {

		if ( ! current_user_can( 'access_cp' ) ) {
			die( -1 );
		}

		$data     = $_POST['style_settings']; //phpcs:ignore WordPress.Security.NonceVerification.Missing
		$pairs    = explode( '&', $data );
		$settings = array();
		foreach ( $pairs as $pair ) {
			$pair = explode( '=', $pair );
			if ( isset( $settings[ $pair[0] ] ) ) {
				$settings[ $pair[0] ] = $settings[ $pair[0] ] . ',' . $pair[1];
			} else {
				$settings[ $pair[0] ] = $pair[1];
			}
		}

		$theme_name = ucwords( str_replace( '_', ' ', $settings['style'] ) );

		$style_type = $settings['style_type'];
		$option     = $style_type . '_variant_tests';

		$style         = $settings['style'];
		$variant_style = $settings['style_id'];
		$v_action      = isset( $settings['variant-action'] ) ? $settings['variant-action'] : '';

		$prev_styles        = get_option( $option );
		$variant_arrays     = isset( $prev_styles[ $variant_style ] ) ? $prev_styles[ $variant_style ] : array();
		$new_style          = array();
		$rand               = substr( md5( uniqid() ), wp_rand( 0, 26 ), 5 );
		$dynamic_style_name = 'cp_id_' . $rand;
		$style_id           = isset( $settings['variant-style'] ) && '' !== $settings['variant-style'] ? $settings['variant-style'] : $theme_name;
		$style_name         = isset( $settings['new_style'] ) && '' !== $settings['new_style'] ? $settings['new_style'] : $theme_name;

		if ( 'new' == $v_action ) {
			unset( $settings['live'] );
			$settings['live'] = '0';
		}

		$style_settings              = maybe_serialize( $settings );
		$key                         = ! empty( $variant_arrays ) ? search_style( $variant_arrays, $style_id ) : null;
		$impressions                 = isset( $variant_arrays[ $key ]['impressions'] ) ? $variant_arrays[ $key ]['impressions'] : 0;
		$conversion                  = isset( $variant_arrays[ $key ]['conversion'] ) ? $variant_arrays[ $key ]['conversion'] : 0;
		$new_style['style_name']     = stripslashes( $style_name );
		$new_style['style_id']       = $style_id;
		$new_style['style_settings'] = $style_settings;

		if ( is_array( $variant_arrays ) && ! empty( $variant_arrays ) ) {
			$ar_key = false;
			foreach ( $variant_arrays as $key => $array ) {
				if ( $style_id == $array['style_id'] ) {
					unset( $prev_styles[ $variant_style ][ $key ] );
				}
			}

			$new_variant_test = array();
			$new_variant_test = $new_style;
			array_push( $prev_styles[ $variant_style ], $new_variant_test );
		} else {
			$new_variant_test              = array();
			$prev_styles[ $variant_style ] = array();
			$new_variant_test              = $new_style;
			array_push( $prev_styles[ $variant_style ], $new_variant_test );
		}

		update_option( $option, $prev_styles );
		echo esc_attr( $style_id );
		die();
	}
}


if ( ! function_exists( 'smile_duplicate_style' ) ) {
	/**
	 * Function to accept ajax call for duplicating stylesvariant test settings.
	 */
	function smile_duplicate_style() {

		if ( ! current_user_can( 'access_cp' ) ) {
			die( -1 );
		}
		check_admin_referer( 'cp_duplicate_nonce', 'security_nonce' );

			$style_id     = isset( $_POST['style_id'] ) ? esc_attr( $_POST['style_id'] ) : '';
			$option       = isset( $_POST['option'] ) ? esc_attr( $_POST['option'] ) : '';
			$module       = isset( $_POST['module'] ) ? esc_attr( $_POST['module'] ) : '';
			$variant_id   = isset( $_POST['variant_id'] ) ? esc_attr( $_POST['variant_id'] ) : '';
			$data_option  = 'smile_' . $module . '_styles';
			$style_screen = ( isset( $_POST['stylescreen'] ) && '' !== $_POST['stylescreen'] ) ? esc_attr( $_POST['stylescreen'] ) : '';
			$prev_styles  = get_option( $data_option );
			$key          = null;

		if ( $prev_styles && '' !== $variant_id ) {
			$key = search_style( $prev_styles, $variant_id );
		} else {
			$key = search_style( $prev_styles, $style_id );
		}

		$rand             = substr( md5( uniqid() ), wp_rand( 0, 26 ), 5 );
		$dynamic_style_id = 'cp_id_' . $rand;
		$modal_arrays     = array();

		$smile_variant_tests = array();
		$smile_variant_tests = get_option( $option );
		$modal_arrays        = $smile_variant_tests;

		// If On variant screen.
		if ( 'multivariant' === $style_screen ) {
			$new_variant_tests = array();
			if ( isset( $smile_variant_tests[ $variant_id ] ) ) {
				$new_variant_tests = $smile_variant_tests[ $variant_id ];
			} else {
				$new_variant_tests                  = array();
				$smile_variant_tests[ $variant_id ] = array();
			}
			$modal_arrays = $smile_variant_tests;

			// Duplicating variant.
			if ( ! empty( $new_variant_tests ) ) {

				$match = false;
				foreach ( $new_variant_tests as $vkey => $array ) {

					// While duplicating variant on variant screen.
					if ( $array['style_id'] == $style_id ) {
						$dynamic_style_id            = 'cp_id_' . $rand;
						$new_style_id                = $dynamic_style_id;
						$new_style                   = $new_variant_tests[ $vkey ];
						$style_name                  = urldecode( $new_style['style_name'] );
						$new_style_name              = smile_duplicate_style_name( $new_variant_tests, trim( $style_name ) );
						$new_style['style_name']     = $new_style_name;
						$new_style['style_id']       = $new_style_id;
						$settings                    = maybe_unserialize( $new_style['style_settings'] );
						$settings['live']            = 0;
						$settings['style_id']        = $new_style_id;
						$settings['variant-style']   = $new_style_id;
						$new_style['style_settings'] = maybe_serialize( $settings );
						array_push( $new_variant_tests, $new_style );
						$modal_arrays[ $variant_id ] = $new_variant_tests;
						$match                       = true;
						break;
					}
				}
				if ( ! $match ) {

					// Duplicating main style on variant screen.
					$new_style                       = $prev_styles[ $key ];
					$style_settings                  = maybe_unserialize( $new_style['style_settings'] );
					$style_settings['live']          = 0;
					$rand                            = substr( md5( uniqid() ), wp_rand( 0, 26 ), 5 );
					$dynamic_style_id                = 'cp_id_' . $rand;
					$new_style_id                    = $dynamic_style_id;
					$style_name                      = urldecode( $new_style['style_name'] );
					$new_style_name                  = smile_duplicate_style_name( $new_variant_tests, $style_name );
					$new_style['style_name']         = $new_style_name;
					$new_style['style_id']           = $new_style_id;
					$style_settings['variant-style'] = $new_style_id;
					$new_style['style_settings']     = maybe_serialize( $style_settings );
					array_push( $new_variant_tests, $new_style );
					$modal_arrays[ $variant_id ] = $new_variant_tests;
				}
			} else {

				// If duplicating main style on variant screen if modal has no variants.
				$smile_variant_tests[ $variant_id ] = array();
				$new_style                          = $prev_styles[ $key ];
				$style_settings                     = maybe_unserialize( $new_style['style_settings'] );
				$style_settings['live']             = 0;
				$rand                               = substr( md5( uniqid() ), wp_rand( 0, 26 ), 5 );
				$dynamic_style_id                   = 'cp_id_' . $rand;
				$new_style_id                       = $dynamic_style_id;
				$style_name                         = urldecode( $new_style['style_name'] );
				$new_style_name                     = smile_duplicate_style_name( $prev_styles, $style_name );
				$new_style['style_name']            = $new_style_name;
				$new_style['style_id']              = $new_style_id;
				$style_settings['variant-style']    = $new_style_id;
				$new_style['style_settings']        = maybe_serialize( $style_settings );
				array_push( $smile_variant_tests[ $variant_id ], $new_style );
				$modal_arrays = $smile_variant_tests;
			}
		} else {

			// If on modal list screen.
			$new_style                   = $prev_styles[ $key ];
			$style_settings              = maybe_unserialize( $new_style['style_settings'] );
			$style_settings['live']      = 0;
			$rand                        = substr( md5( uniqid() ), wp_rand( 0, 26 ), 5 );
			$dynamic_style_id            = 'cp_id_' . $rand;
			$new_style_id                = $dynamic_style_id;
			$style_name                  = urldecode( $new_style['style_name'] );
			$new_style_name              = smile_duplicate_style_name( $prev_styles, $style_name );
			$new_style['style_name']     = $new_style_name;
			$new_style['style_id']       = $new_style_id;
			$style_settings['style_id']  = $new_style_id;
			$new_style['style_settings'] = maybe_serialize( $style_settings );
			array_push( $prev_styles, $new_style );
			$modal_arrays = $prev_styles;
		}

		update_option( $option, $modal_arrays );
		wp_send_json(
			array(
				'message' => 'copied',
			)
		);
	}
}

if ( ! function_exists( 'smile_update_status' ) ) {
	/**
	 * Function to accept ajax call for changing modal status.
	 */
	function smile_update_status() {

		if ( ! current_user_can( 'access_cp' ) ) {
			die( -1 );
		}

		check_admin_referer( 'cp-change-style-status', 'security_nonce' );

		$status         = isset( $_POST['status'] ) ? sanitize_text_field( $_POST['status'] ) : '';
		$style_id       = isset( $_POST['style_id'] ) ? sanitize_text_field( $_POST['style_id'] ) : '';
		$option         = isset( $_POST['option'] ) ? sanitize_text_field( $_POST['option'] ) : '';
		$variant_option = isset( $_POST['variant'] ) ? sanitize_text_field( $_POST['variant'] ) : '';
		$cp_start       = isset( $_POST['cp_start'] ) ? sanitize_text_field( $_POST['cp_start'] ) : '';
		$cp_end         = isset( $_POST['cp_end'] ) ? sanitize_text_field( $_POST['cp_end'] ) : '';
		$prev_styles    = get_option( $option );

		$key          = search_style( $prev_styles, $style_id );
		$modal_arrays = array();

		$smile_variant_tests = get_option( $variant_option );

		$key = search_style( $prev_styles, $style_id );

		if ( null !== $key ) {

			$new_style        = $prev_styles[ $key ];
			$settings         = maybe_unserialize( $new_style['style_settings'] );
			$settings['live'] = $status;

			if ( '2' === $status || 2 === $status ) {

				$settings['schedule'] = array(
					'start' => $cp_start,
					'end'   => $cp_end,
				);
			}
			$new_style['style_settings'] = maybe_serialize( $settings );
			$prev_styles[ $key ]         = $new_style;
			$modal_arrays                = $prev_styles;
		} else {
			foreach ( $smile_variant_tests as $key1 => $arrays ) {
				foreach ( $arrays as $key2 => $array ) {
					if ( $array['style_id'] == $style_id ) {
						$modal_arrays     = $array;
						$settings         = maybe_unserialize( $smile_variant_tests[ $key1 ][ $key2 ]['style_settings'] );
						$settings['live'] = $status;
						$smile_variant_tests[ $key1 ][ $key2 ]['style_settings'] = maybe_serialize( $settings );
						break;
					}
				}
			}
			$modal_arrays = $smile_variant_tests;
		}

		update_option( $option, $modal_arrays );
		wp_send_json(
			array(
				'message'  => 'status changed',
				'settings' => $settings,
			)
		);
	}
}

if ( ! function_exists( 'smile_update_impressions' ) ) {
	/**
	 * Function to record impressions for style.
	 */
	function smile_update_impressions() {

		// Verify nonce.
		if ( ! wp_verify_nonce( esc_attr( $_POST['security'] ), 'cp-impress-nonce' ) ) {
			wp_send_json_error();
		}
		global $cp_analytics_end_time;
		$user_role   = '';
		$condition   = true;
		$cp_settings = get_option( 'convert_plug_settings' );

		if ( is_array( $cp_settings ) ) {
			$banneduser = explode( ',', $cp_settings['cp-user-role'] );
		}

		if ( is_user_logged_in() ) {
			$current_user = new WP_User( wp_get_current_user() );
			$user_roles   = $current_user->roles;
			$user_role    = $user_roles[0];
		}

		if ( ! empty( $cp_settings ) ) {
			$condition = ! is_user_logged_in() || ( is_user_logged_in() && ( ! in_array( $user_role, $banneduser ) ) );
		} else {
			$condition = ! is_user_logged_in() || ( is_user_logged_in() && ( 'administrator' !== $user_role ) );
		}

		if ( $condition ) {

			$styles = array_map( 'sanitize_text_field', wp_unslash( $_POST['styles'] ) );

			foreach ( $styles as $style ) {

				$style_id   = $style;
				$impression = esc_attr( $_POST['impression'] );

				// Save analytics data.
				$existing_data = get_option( 'smile_style_analytics' );
				$date          = $cp_analytics_end_time;

				if ( ! $existing_data ) {

					$analytics_data = array(
						$style_id => array(
							$date => array(
								'impressions' => 1,
								'conversions' => 0,
							),
						),
					);

				} else {

					if ( isset( $existing_data[ $style_id ] ) ) {
						$is_date_exist = cp_search_key_in_array( $existing_data[ $style_id ], $date );

						if ( $is_date_exist ) {

							foreach ( $existing_data[ $style_id ] as $key => $value ) {
								if ( $key === $date ) {
									$old_impressions                     = $value['impressions'];
									$old_conversions                     = $value['conversions'];
									$existing_data[ $style_id ][ $date ] = array(
										'impressions' => $old_impressions + 1,
										'conversions' => $old_conversions,
									);
								}
							}
						} else {
							$existing_data[ $style_id ][ $date ] = array(
								'impressions' => 1,
								'conversions' => 0,
							);
						}
					} else {

						$existing_data[ $style_id ] = array(
							$date => array(
								'impressions' => 1,
								'conversions' => 0,
							),
						);
					}
					$analytics_data = $existing_data;
				}

				update_option( 'smile_style_analytics', $analytics_data );
			}

			echo 'impression added';
		}

		die();
	}
}

if ( ! function_exists( 'smile_delete_style' ) ) {
	/**
	 * Function to accept ajax call for deleting existing styles.
	 */
	function smile_delete_style() {

		if ( ! current_user_can( 'access_cp' ) ) {
			die( -1 );
		}

		check_admin_referer( 'cp-delete-style', 'security_nonce' );

		$style_id = esc_attr( $_POST['style_id'] );
		$option   = isset( $_POST['option'] ) ? esc_attr( $_POST['option'] ) : '';

		$variant_option = isset( $_POST['variant_option'] ) ? esc_attr( $_POST['variant_option'] ) : '';
		$prev_styles    = get_option( $option );
		$key            = search_style( $prev_styles, $style_id );

		$has_variants = false;
		$result       = true;
		$modal_arrays = array();

		$smile_variant_tests = get_option( $variant_option );
		if ( $smile_variant_tests && is_array( $smile_variant_tests ) ) {
			$has_variants = array_key_exists( $style_id, $smile_variant_tests );
		}

		if ( $has_variants && null !== $key ) {

			$del_method = esc_attr( $_POST['deleteMethod'] );

			if ( 'soft' === $del_method ) {
				$prev_styles[ $key ]['multivariant']   = true;
				$settings                              = maybe_unserialize( $prev_styles[ $key ]['style_settings'] );
				$settings['live']                      = '0';
				$prev_styles[ $key ]['style_settings'] = maybe_serialize( $settings );

			} else {
				unset( $prev_styles[ $key ] );
				unset( $smile_variant_tests[ $style_id ] );
			}
			update_option( $option, $prev_styles );
			update_option( $variant_option, $smile_variant_tests );

			// Reset analytics data for style.
			cp_reset_analytics( $style_id );

		} else {

			if ( null !== $key ) {
				unset( $prev_styles[ $key ] );
				$modal_arrays = $prev_styles;
				$result       = update_option( $option, $modal_arrays );

				// Reset analytics data for style.
				cp_reset_analytics( $style_id );

			} else {
				foreach ( $smile_variant_tests as $key1 => $arrays ) {
					foreach ( $arrays as $key2 => $array ) {
						if ( $array['style_id'] == $style_id ) {

							$modal_arrays = $array;

							unset( $smile_variant_tests[ $key1 ][ $key2 ] );
							$modal_arrays = $smile_variant_tests;
							$result       = update_option( $variant_option, $modal_arrays );

							// Reset analytics data for style.
							cp_reset_analytics( $style_id );
							break;
						}
					}
				}
			}
		}

		if ( $result ) {
			wp_send_json(
				array(
					'message' => 'Deleted',
				)
			);
		} else {
			wp_send_json(
				array(
					'message' => __( 'Unable to delete the style. Please Try again.', 'smile' ),
				)
			);
		}
		die();
	}
}


if ( ! function_exists( 'cp_delete_all_modal_action' ) ) {
	/**
	 * Delete selecte modules.
	 */
	function cp_delete_all_modal_action() {

		if ( ! current_user_can( 'access_cp' ) ) {
			die( -1 );
		}

		check_admin_referer( 'cp-delete-style', 'security_nonce' );

		$delete_all_ids = esc_attr( $_POST['style_id'] );
		$analtics_data  = get_option( 'smile_style_analytics' );

		$style_array    = explode( ',', $delete_all_ids );
		$option         = isset( $_POST['option'] ) ? esc_attr( $_POST['option'] ) : '';
		$variant_option = isset( $_POST['variant_option'] ) ? esc_attr( $_POST['variant_option'] ) : '';
		$result         = true;
		$prev_styles    = get_option( $option );
		foreach ( $style_array as $key => $value ) {
			$style_id = $value;

			$key = search_style( $prev_styles, $style_id );

			$has_variants = false;

			$modal_arrays = array();

			$smile_variant_tests = get_option( $variant_option );
			if ( $smile_variant_tests && is_array( $smile_variant_tests ) ) {
				$has_variants = array_key_exists( $style_id, $smile_variant_tests );
			}

			if ( $has_variants && null !== $key ) {

				$del_method = esc_attr( $_POST['deleteMethod'] );
				if ( 'soft' === $del_method ) {
					$prev_styles[ $key ]['multivariant']   = true;
					$settings                              = maybe_unserialize( $prev_styles[ $key ]['style_settings'] );
					$settings['live']                      = '0';
					$prev_styles[ $key ]['style_settings'] = maybe_serialize( $settings );
				} else {
					unset( $prev_styles[ $key ] );
					unset( $smile_variant_tests[ $style_id ] );
				}
				update_option( $option, $prev_styles );
				update_option( $variant_option, $smile_variant_tests );

				// Reset analytics data for style.
				cp_reset_analytics( $style_id );
			} else {

				if ( null !== $key ) {
					unset( $prev_styles[ $key ] );
					$modal_arrays = $prev_styles;
					$result       = update_option( $option, $modal_arrays );

					// Reset analytics data for style.
					cp_reset_analytics( $style_id );

				} else {
					foreach ( $smile_variant_tests as $key1 => $arrays ) {
						foreach ( $arrays as $key2 => $array ) {
							if ( $array['style_id'] == $style_id ) {
								$modal_arrays = $array;
								unset( $smile_variant_tests[ $key1 ][ $key2 ] );
								$modal_arrays = $smile_variant_tests;
								$result       = update_option( $variant_option, $modal_arrays );

								// Reset analytics data for style.
								cp_reset_analytics( $style_id );
								break;
							}
						}
					}
				}
			}
		}
		if ( $result ) {
			wp_send_json(
				array(
					'message' => 'Deleted',
				)
			);
		} else {
			wp_send_json(
				array(
					'message' => __( 'Unable to delete the style. Please Try again.', 'smile' ),
				)
			);
		}

	}
}

if ( ! function_exists( 'smile_update_modules' ) ) {
	/**
	 * Function to accept ajax call for updating module list.
	 */
	function smile_update_modules() {

		check_admin_referer( 'cp-smile_update_modules-nonce', 'security_nonce' );

		if ( ! current_user_can( 'access_cp' ) ) {
			die( -1 );
		}

		$module_list = array_map( 'sanitize_text_field', wp_unslash( $_POST ) );
		unset( $module_list['action'] );
		$new_module_list = array();
		foreach ( $module_list as $module => $file ) {
			$new_module_list[] = $module;
		}

		$result = update_option( 'convert_plug_modules', $new_module_list );
		if ( $result ) {
			wp_send_json(
				array(
					'message' => __( 'Modules Updated!', 'smile' ),
				)
			);
		} else {
			wp_send_json(
				array(
					'message' => __( 'No settings were updated. Try again!', 'smile' ),
				)
			);
		}
	}
}


if ( ! function_exists( 'smile_update_global' ) ) {
	/**
	 * Function to accept ajax call for updating globally displayed modal settings.
	 */
	function smile_update_global() {

		if ( ! current_user_can( 'access_cp' ) ) {
			die( -1 );
		}

		if ( isset( $_REQUEST['cp_admin_page_nonce'] ) && ! wp_verify_nonce( $_REQUEST['cp_admin_page_nonce'], 'cp_admin_page' ) ) {
			wp_die( 'No direct script access allowed!' );
		}

		$data   = array_map( 'sanitize_text_field', wp_unslash( $_POST ) );
		$result = update_option( 'smile_global_modal', $data );
		if ( $result ) {
			echo esc_html( 'Updated' );
		} else {
			echo esc_html__( 'Something went wrong! Please try again.', 'smile' );
		}
		die();
	}
}

if ( ! function_exists( 'smile_add_list' ) ) {
	/**
	 * Function to accept ajax call for adding the new contact list.
	 */
	function smile_add_list() {

		if ( ! current_user_can( 'access_cp' ) ) {
			die( -1 );
		}

		check_admin_referer( 'cp-create-list-nonce' );

		$data              = array_map( 'sanitize_text_field', wp_unslash( $_POST ) );
		$cp_addon_list     = Smile_Framework::$addon_list;
		$data['list-name'] = cp_clean_string( $data['list-name'] );
		$old_value         = get_option( 'smile_lists' );
		$arr               = array();
		$provider_list     = array();

		// This if is a case where multiple lists needs to be saved for any campaign.
		if ( isset( $cp_addon_list[ $data['list-provider'] ]['mailer_type'] ) ) {
			if ( 'multiple' === $cp_addon_list[ $data['list-provider'] ]['mailer_type'] ) {
				if ( isset( $data['list'] ) ) {
					$decoded_json = json_decode( $data['list'], true );
					if ( 0 < count( $decoded_json ) ) {
						foreach ( $decoded_json as $d ) {
							$tmp = json_decode( $d, true );
							foreach ( $tmp as $key => $t ) {
								$arr[ $key ]     = $t;
								$provider_list[] = $key;
							}
						}
					}
				}
				$data['list']          = implode( ',', $provider_list );
				$data['provider_list'] = $arr;
			}
		}

		unset( $data['action'] );
		$list_data   = $data;
		$old_value[] = $list_data;
		$status      = update_option( 'smile_lists', $old_value );
		if ( $status ) {
			wp_send_json(
				array(
					'message' => 'added',
				)
			);
		} else {
			wp_send_json(
				array(
					'message' => 'error',
				)
			);
		}
	}
}

if ( ! function_exists( 'cp_clean_string' ) ) {
	/**
	 * Function to remove special characters from string.
	 *
	 * @param string $string string parameters.
	 * @return string clean string.
	 */
	function cp_clean_string( $string ) {

		$string = trim( $string );

		// Remove single and double quotes from string.
		$string = str_replace( array( "'", '"' ), '', $string );
		$string = str_replace( array( '\\', '/' ), '', $string );  // remove slashes.

		return $string;
	}
}

if ( ! function_exists( 'is_campaign_exists' ) ) {
	/**
	 * Function to check if campaign with same name already exists
	 *
	 * @since 1.0
	 */
	function is_campaign_exists() {

		if ( ! current_user_can( 'access_cp' ) ) {
			die( -1 );
		}

		check_admin_referer( 'is_campaign_exists_nonce', 'security_nonce' );

		$campaign_name = sanitize_text_field( $_POST['campaign'] );
		$is_exists     = false;
		$lists         = get_option( 'smile_lists' );

		if ( is_array( $lists ) ) {
			foreach ( $lists as $key => $list ) {
				if ( strtolower( trim( $list['list-name'] ) ) == strtolower( trim( $campaign_name ) ) ) {
					$is_exists = true;
				}
			}
		}

		if ( $is_exists ) {
			wp_send_json(
				array(
					'status'  => 'error',
					'message' => __( 'Campaign with same name already exists', 'smile' ),
				)
			);
		} else {
			wp_send_json(
				array(
					'status' => 'success',
				)
			);
		}
	}
}

/**
 * Function to generate option name to be used to store contacts in list
 *
 * @param string $list_id id for list.
 * @since 2.0.3.1
 */
function cp_generate_option( $list_id ) {

	$smile_lists = get_option( 'smile_lists' );
	$data_option = '';

	if ( is_array( $smile_lists ) ) {
		$list_details = $smile_lists[ $list_id ];

		$list_name = ( '' !== $list_details ) ? str_replace( ' ', '_', strtolower( trim( $list_details['list-name'] ) ) ) : '';

		$mailer = ( '' !== $list_details ) ? $list_details['list-provider'] : '';

		if ( 'Convert Plug' === $mailer ) {
			$mailer_id   = 'cp';
			$data_option = 'cp_connects_' . $list_name;
		} else {
			$mailer_id   = strtolower( $mailer );
			$data_option = 'cp_' . $mailer_id . '_' . $list_name;
		}
	}

	return $data_option;
}


if ( ! function_exists( 'cp_add_subscriber' ) ) {
	/**
	 * Function to accept ajax call for adding contact in a list
	 */
	function cp_add_subscriber() {

		$style_id = isset( $_POST['style_id'] ) ? sanitize_text_field( $_POST['style_id'] ) : '';

		// Verify nonce.
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'cp-submit-form-' . $style_id ) ) {
			wp_send_json_error();
		}

		$only_conversion = false;

		$param           = array_map( 'sanitize_text_field', wp_unslash( $_POST['param'] ) );
		$email           = isset( $_POST['param']['email'] ) ? sanitize_email( $_POST['param']['email'] ) : '';
		$style_type      = isset( $_POST['cp_module_type'] ) ? sanitize_text_field( $_POST['cp_module_type'] ) : '';
		$list_id         = cp_get_setting( $style_id, $style_type, 'mailer' );
		$data_option     = cp_generate_option( $list_id );
		$only_conversion = isset( $_POST['only_conversion'] ) ? true : false;
		$default_action  = isset( $_POST['action'] ) ? sanitize_text_field( $_POST['action'] ) : '';
		$cp_settings     = get_option( 'convert_plug_settings' );
		$disable_storage = isset( $cp_settings['cp-disable-storage'] ) ? $cp_settings['cp-disable-storage'] : 0;
		$store           = false;
		$cp_set_hp       = isset( $_POST['cp_set_hp'] ) ? sanitize_text_field( $_POST['cp_set_hp'] ) : '';

		$domain_disabled = $cp_settings['cp-disable-domain'];
		$domain_name     = $cp_settings['cp-domain-name'];
		$domain_arr      = array();

		if ( $domain_disabled && '' !== $domain_name && '' !== $email ) {

			$domain_arr = explode( ',', $domain_name );
			$domain_arr = array_map( 'trim', $domain_arr );
			// Separate string by @ characters (there should be only one).
			$email_parts  = explode( '@', $email );
			$email_domain = array_pop( $email_parts );

			if ( in_array( $email_domain, $domain_arr ) ) {
				if ( wp_doing_ajax() ) {
					wp_die( -1, 403 );
				} else {
					die( '-1' );
				}
			}
		}

		if ( $cp_set_hp ) {
			if ( wp_doing_ajax() ) {
				wp_die( -1, 403 );
			} else {
				die( '-1' );
			}
		}

		if ( '1' !== $disable_storage ) {
			$store = true;
		}

		if ( isset( $_POST['message'] ) ) {
			$on_success = 'message';
		} elseif ( isset( $_POST['redirect'] ) ) {
			$on_success = 'redirect';
		} else {
			$on_success = 'close';
		}

		$cp_page_url = ( isset( $_POST['cp-page-url'] ) ) ? esc_url( $_POST['cp-page-url'] ) : '';

		$msg_wrong_email = ( isset( $_POST['msg_wrong_email'] ) && '' !== $_POST['msg_wrong_email'] ) ? sanitize_text_field( $_POST['msg_wrong_email'] ) : __( 'Please enter correct email address.', 'smile' );
		$msg             = ( isset( $_POST['message'] ) && '' !== $_POST['message'] ) ? sanitize_text_field( $_POST['message'] ) : __( 'Thank you.', 'smile' );

		if ( 'message' === $on_success ) {
			$action = 'message';
			$url    = 'none';
		} elseif ( 'redirect' === $on_success ) {
			$action = 'redirect';
			$url    = $_POST['redirect'];
		} else {
			$action = 'close';
			$url    = '#';
		}

		$contact       = array();
		$prev_contacts = get_option( $data_option );

		$cp_verify_google_recaptcha = isset( $_POST['cp_verify_google_recaptcha'] ) ? 0 : 1;

		if ( ! $cp_verify_google_recaptcha ) {

			$google_recaptcha = isset( $_POST['g-recaptcha-response'] ) && 1 ? $_POST['g-recaptcha-response'] : '';

			$cp_recaptcha_secret_key = sanitize_text_field( get_option( 'cp_recaptcha_secret_key ' ) );
			$status                  = '';

			// calling google recaptcha api.
			$g_url           = 'https://www.google.com/recaptcha/api/siteverify';
			$google_response = add_query_arg(
				array(
					'secret'   => $cp_recaptcha_secret_key,
					'response' => $google_recaptcha,
					'remoteip' => $_SERVER['REMOTE_ADDR'],
				),
				$g_url
			);
			$cp_response     = wp_remote_get( $google_response );

			$decode_google_response = json_decode( $cp_response['body'] );

			if ( false === $decode_google_response->success ) {
						$detailed_msg = __( 'Invalid Secret Key for Google Recaptcha', 'smile' );
						$msg          = '';
						$status       = 'error';
						$email_status = false;
						$store        = false;

			}

			if ( 'error' == $status ) {
				wp_send_json(
					array(
						'action'       => $action,
						'email_status' => $email_status,
						'status'       => $status,
						'message'      => $msg,
						'detailed_msg' => $detailed_msg,
						'url'          => $url,
					)
				);

			}
		}
		// Check Email in MX records.
		$email_status = true;
		if ( ! $only_conversion ) {

			// Check MX Record setting globally enabled / disabled?
			if ( ! empty( $email ) && ( apply_filters( 'cp_enabled_mx_record', $email ) ) ) {

				if ( filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
					$email_status = apply_filters( 'cp_valid_mx_email', $email );
				} else {
					$email_status = false;
				}
			}
		}

		if ( $email_status ) {

			$status  = 'success';
			$contact = $param;
			$updated = false;
			$index   = false;

			if ( ! empty( $email ) && $prev_contacts ) {
				$index = cp_check_in_array( $email, $prev_contacts, 'email' );
			}

			if ( false !== $index ) {

				$contact['user_id']      = $prev_contacts[ $index ]['user_id'];
				$prev_contacts[ $index ] = $contact;
				$updated                 = true;
				$status                  = 'error';
				// Show message for already subscribed users.
				$default_msg_status = isset( $cp_settings['cp-default-messages'] ) ? $cp_settings['cp-default-messages'] : 1;
				$already_subscribed = isset( $cp_settings['cp-already-subscribed'] ) ? $cp_settings['cp-already-subscribed'] : __( 'Already Subscribed...!', 'smile' );
				$debug_data         = get_option( 'convert_plug_debug' );
				$sub_def_action     = isset( $debug_data['cp-post-sub-action'] ) ? $debug_data['cp-post-sub-action'] : 'process_success';

				if ( $default_msg_status ) {
					$msg = stripslashes( $already_subscribed );
				}

				if ( 'process_success' === $sub_def_action ) {
					$status = 'success';
					$msg    = ( isset( $_POST['message'] ) && '' !== $_POST['message'] ) ? do_shortcode( html_entity_decode( stripcslashes( sanitize_text_field( htmlspecialchars( $_POST['message'], ENT_QUOTES, 'utf-8' ) ) ), ENT_QUOTES, 'utf-8' ) ) : __( 'Thank you.', 'smile' );
				}
			} else {
				if ( $store ) {
					$prev_contacts[] = $contact;
				}
			}

			if ( ! empty( $prev_contacts ) && $store ) {
				$prev_contacts = array_map( 'maybe_unserialize', array_unique( array_map( 'maybe_serialize', $prev_contacts ) ) );
			}

			if ( ! $only_conversion && $store ) {
				update_option( $data_option, $prev_contacts );
			}

			if ( ! $updated ) {
				// Update conversions.
				smile_update_conversions( $style_id );
			}
		} else {
			if ( $only_conversion ) {
				// Update conversions.
				$status = 'success';
				smile_update_conversions( $style_id );
			} else {
				$msg    = $msg_wrong_email;
				$status = 'error';
			}
		}

		// Send subscriber notification to provided email address.
		$sub_optin  = isset( $cp_settings['cp-sub-notify'] ) ? $cp_settings['cp-sub-notify'] : 0;
		$sub_email  = isset( $cp_settings['cp-sub-email'] ) ? $cp_settings['cp-sub-email'] : get_option( 'admin_email' );
		$email_sub  = isset( $cp_settings['cp-email-sub'] ) ? $cp_settings['cp-email-sub'] : '';
		$email_body = isset( $cp_settings['cp-email-body'] ) ? $cp_settings['cp-email-body'] : '';

		if ( 'success' === $status && ! $only_conversion ) {

			if ( '1' === $sub_optin || 1 === $sub_optin ) {
				$list_name  = str_replace( 'cp_connects_', '', $data_option );
				$list_name  = str_replace( '_', ' ', $list_name );
				$page_url   = isset( $cp_settings['cp-page-url'] ) ? $cp_settings['cp-email-body'] : '';
				$style_name = isset( $_POST['cp_module_name'] ) ? esc_attr( $_POST['cp_module_name'] ) : '';
				cp_notify_sub_to_admin( $list_name, $param, $sub_email, $email_sub, $email_body, $cp_page_url, $style_name );
			}

			$param['style_id']   = $style_id;
			$param['style_name'] = $style_type;
			cp_add_new_user_role( $param );
		}
		wp_send_json(
			array(
				'action'       => $action,
				'email_status' => $email_status,
				'status'       => $status,
				'message'      => $msg,
				'url'          => $url,
			)
		);
	}
}

if ( ! function_exists( 'cp_add_subscriber_contact' ) ) {
	/**
	 * Custom function to add contact to list databaseFunction to get data for style analytics.
	 *
	 * @param  string $contacts_option string parameter.
	 * @param  atring $subscriber      string parameter.
	 * @return boolval(var)            true/false.
	 */
	// @codingStandardsIgnoreStart
	function cp_add_subscriber_contact( $contacts_option, $subscriber ) {

		$style_id = isset( $_POST['style_id'] ) ? sanitize_text_field( esc_attr( $_POST['style_id'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing

			$list_id          = isset( $_POST['list_parent_index'] ) ? esc_attr( $_POST['list_parent_index'] ) : '';
			$option           = cp_generate_option( $list_id );
			$cp_page_url      = isset( $_POST['cp-page-url'] ) ? esc_url( $_POST['cp-page-url'] ) : '';
			$data             = get_option( $option );
			$index            = false;
			$updated          = false;
			$only_conversion  = isset( $_POST['only_conversion'] ) ? true : false;
			$popup_style_name = isset( $_POST['cp_module_type'] ) ? esc_attr( $_POST['cp_module_type'] ) : '';

			$email = isset( $subscriber['email'] ) ? strtolower( $subscriber['email'] ) : '';
		if ( $data ) {
			$index = cp_check_in_array( $email, $data, 'email' );
		}

			$subscriber = array_map( 'sanitize_text_field', wp_unslash( $subscriber ) );

			$cp_settings     = get_option( 'convert_plug_settings' );
			$disable_storage = isset( $cp_settings['cp-disable-storage'] ) ? $cp_settings['cp-disable-storage'] : 0;

			$cp_set_hp = isset( $_POST['cp_set_hp'] ) ? esc_attr( $_POST['cp_set_hp'] ) : '';

			$domain_disabled = $cp_settings['cp-disable-domain'];
			$domain_name     = $cp_settings['cp-domain-name'];
			$domain_arr      = array();

		if ( $domain_disabled && '' !== $domain_name && '' !== $email ) {
			$domain_arr = explode( ',', $domain_name );
			$domain_arr = array_map( 'trim', $domain_arr );
			// Separate string by @ characters (there should be only one).
			$email_parts  = explode( '@', $email );
			$email_domain = array_pop( $email_parts );

			if ( in_array( $email_domain, $domain_arr ) ) {
				if ( wp_doing_ajax() ) {
					wp_die( -1, 403 );
				} else {
					die( '-1' );
				}
			}
		}

		if ( $cp_set_hp ) {
			if ( wp_doing_ajax() ) {
				wp_die( -1, 403 );
			} else {
				die( '-1' );
			}
		}

		if ( false !== $index ) {
			unset( $data[ $index ] );
			$data[]  = $subscriber;
			$updated = true;
		} else {
			$data[] = $subscriber;
		}

		if ( ! empty( $data ) ) {
			$data = array_map( 'maybe_unserialize', array_unique( array_map( 'maybe_serialize', $data ) ) );
		}

				$cp_verify_google_recaptcha = isset( $_POST['cp_verify_google_recaptcha'] ) ? 0 : 1;

		if ( ! $cp_verify_google_recaptcha ) {

			$google_recaptcha        = isset( $_POST['g-recaptcha-response'] ) && 1 ? $_POST['g-recaptcha-response'] : '';
			$cp_recaptcha_secret_key = sanitize_text_field( get_option( 'cp_recaptcha_secret_key ' ) );

			$status = '';

			// calling google recaptcha api.
			$g_url           = 'https://www.google.com/recaptcha/api/siteverify';
			$google_response = add_query_arg(
				array(
					'secret'   => $cp_recaptcha_secret_key,
					'response' => $google_recaptcha,
					'remoteip' => $_SERVER['REMOTE_ADDR'],
				),
				$g_url
			);
			$cp_response     = wp_remote_get( $google_response );

			$decode_google_response = json_decode( $cp_response['body'] );

			if ( false === $decode_google_response->success ) {
					$detailed_msg = __( 'Invalid Secret Key for Google Recaptcha', 'smile' );
					$msg          = '';
					$status       = 'error';
					$email_status = false;
					$store        = false;

			}

			if ( 'error' == $status ) {
				wp_send_json(
					array(
						'action'       => $action,
						'email_status' => $email_status,
						'status'       => $status,
						'message'      => $msg,
						'detailed_msg' => $detailed_msg,
						'url'          => $url,
					)
				);
			}
		}
			// Convert array.
			$data1 = array();
			$data  = array_filter( $data );

		if ( '1' !== $disable_storage ) {
			foreach ( $data as $key => $value ) {
				$newdata = array();
				foreach ( $value as $key1 => $value1 ) {
					if ( 'email' === $key1 ) {
						$newdata[ $key1 ] = strtolower( $value1 );
					} else {
						$newdata[ $key1 ] = $value1;
					}
				}
				array_push( $data1, $newdata );
			}

			$update_option = update_option( $option, $data1 );
		}

			// Send subscriber notification to provided email address.
			$sub_optin  = isset( $cp_settings['cp-sub-notify'] ) ? $cp_settings['cp-sub-notify'] : 0;
			$sub_email  = isset( $cp_settings['cp-sub-email'] ) ? $cp_settings['cp-sub-email'] : get_option( 'admin_email' );
			$email_sub  = isset( $cp_settings['cp-email-sub'] ) ? $cp_settings['cp-email-sub'] : '';
			$email_body = isset( $cp_settings['cp-email-body'] ) ? $cp_settings['cp-email-body'] : '';

			$param = array_map( 'sanitize_text_field', wp_unslash( $_POST['param'] ) );

		if ( $update_option && ! $only_conversion ) {

			if ( '1' === $sub_optin || 1 === $sub_optin ) {
				$list_name  = str_replace( 'cp_connects_', '', $option );
				$list_name  = str_replace( '_', ' ', $list_name );
				$style_name = isset( $_POST['cp_module_name'] ) ? esc_attr( $_POST['cp_module_name'] ) : '';
				cp_notify_sub_to_admin( $list_name, $param, $sub_email, $email_sub, $email_body, $cp_page_url, $style_name );
			}

			$param['style_id']   = $style_id;
			$param['style_name'] = $popup_style_name;
			cp_add_new_user_role( $param );

			return $updated;

		}
	}
	// @codingStandardsIgnoreEnd
}

if ( ! function_exists( 'cp_check_in_array' ) ) {
	/**
	 * Custom function to search string in array.
	 *
	 * @param  string $value string parameter.
	 * @param  array  $array array val.
	 * @param  string $key   string val.
	 * @return boolval(var)  true/false.
	 */
	function cp_check_in_array( $value, $array, $key ) {
		if ( is_array( $array ) ) {
			foreach ( $array as $index => $item ) {
				if ( isset( $item[ $key ] ) ) {
					if ( strtolower( $item[ $key ] ) == $value ) {
						return $index;
					}
				}
			}
		}
		return false;
	}
}

/**
 * Function:cp_dismiss_phardata_notice.
 */
function cp_dismiss_phardata_notice() {

	if ( ! current_user_can( 'access_cp' ) ) {
		die( -1 );
	}

	$option = isset( $_POST['action'] ) ? 'cp_show_phardata_notice' : ''; //phpcs:ignore WordPress.Security.NonceVerification.Missing

	if ( '' !== $option ) {
		update_option( $option, 'no' );
	}
}


if ( ! function_exists( 'cp_search_key_in_array' ) ) {
	/**
	 * Custom function to search for key in array
	 *
	 * @param  array  $array array val.
	 * @param  string $key   string val.
	 * @return boolval(var)  true/false.
	 */
	function cp_search_key_in_array( $array, $key ) {
		foreach ( $array as $index => $item ) {
			if ( $key == $index ) {
				return true;
			}
		}
		return false;
	}
}


if ( ! function_exists( 'cp_get_list_name_by_id' ) ) {
	/**
	 * Custom function to retrive list name by its ID.
	 *
	 * @param  string $list_id   list id.
	 * @param  string $provider provider name.
	 * @return string           listname.
	 */
	function cp_get_list_name_by_id( $list_id, $provider ) {
		$list_option = strtolower( $provider ) . '_lists';
		$data        = get_option( $list_option );
		$list_name   = $data[ $list_id ];

		return $list_name;
	}
}

if ( ! function_exists( 'cp_generate_csv' ) ) {
	/**
	 * Function Name: cp_generate_csv.
	 *
	 * @param  array  $data      array parameter.
	 * @param  string $delimiter seperator.
	 * @param  string $enclosure string parameter.
	 * @return string            string parameter.
	 */
	function cp_generate_csv( $data, $delimiter = ',', $enclosure = '"' ) {

		$handle   = fopen( 'php://temp', 'r+' ); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fopen
		$contents = '';
		if ( is_array( $data ) && ! empty( $data ) ) {
			$data = array_values( $data );
			// Get header from keys and set its first character to Upper case.
			$headers = array_change_key_case( $data[0], CASE_LOWER );
			fputcsv( $handle, array_map( 'ucfirst', array_keys( $headers ) ) );

			foreach ( $data as $line ) {
				fputcsv( $handle, $line, $delimiter, $enclosure );
			}

			rewind( $handle );
			while ( ! feof( $handle ) ) {
				$contents .= fread( $handle, 8192 ); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fread
			}
			fclose( $handle ); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fclose
			return $contents;
		} else {
			return __( 'No contacts available to export.', 'smile' );
		}
	}
}

/**
 * Function Name: toLower.
 *
 * @param  string $value string parameter.
 * @return string        string parameter.
 */
function to_lower( $value ) {
	return strtolower( $value );
}

if ( ! function_exists( 'get_campaign_analytics_data' ) ) {
	/**
	 * Custom function to retrieve analytics data for campaign.
	 */
	function get_campaign_analytics_data() {

		if ( ! current_user_can( 'access_cp' ) ) {
			die( -1 );
		}

		check_ajax_referer( 'cp_contacts_nonce', 'security_nonce' );

		$smile_lists = get_option( 'smile_lists' );
		$data        = array();
		$startdate   = sanitize_text_field( $_POST['startDate'] );
		$enddate     = sanitize_text_field( $_POST['endDate'] );

		// To unset deactivated / inactive mailer add ons.
		if ( is_array( $smile_lists ) ) {
			foreach ( $smile_lists as $key => $list ) {
				$provider = $list['list-provider'];
				if ( 'Convert Plug' !== $provider ) {
					if ( ! isset( Smile_Framework::$addon_list[ $provider ] ) && ! isset( Smile_Framework::$addon_list[ strtolower( $provider ) ] ) ) {
						unset( $smile_lists[ $key ] );
					}
				}
			}
		}

		if ( ! is_array( $smile_lists ) ) {
			$message = 'unavailable';
			echo wp_json_encode( $message );
			die();
		}

		if ( is_array( $_POST['listid'] ) ) {
			$list_ids = $_POST['listid'];
		} else {
			$list_ids = explode( ',', $_POST['listid'] );
		}

		if ( in_array( 'all', $list_ids ) ) {
			$list_ids = array_keys( $smile_lists );
		}

		$color_index = 0;
		$total_count = 0;

		if ( ! empty( $smile_lists ) ) {

			foreach ( $smile_lists as $key => $list ) {

				$contact_count = 0;
				$list_name     = $list['list-name'];
				$provider      = $list['list-provider'];
				$list_id       = isset( $list['list'] ) ? $list['list'] : '';

				if ( in_array( $key, $list_ids ) ) {

					$cp_list_id = 'cp_list_' . $key;
					$mailer     = str_replace( ' ', '_', strtolower( trim( $provider ) ) );
					if ( 'convert_plug' !== $mailer ) {
						$contacts_option = 'cp_' . $mailer . '_' . str_replace( ' ', '_', strtolower( trim( $list_name ) ) );
						$list_contacts   = get_option( $contacts_option );
					} else {
						$contacts_option = 'cp_connects_' . str_replace( ' ', '_', strtolower( trim( $list_name ) ) );
						$list_contacts   = get_option( $contacts_option );
					}

					if ( is_array( $list_contacts ) || is_object( $list_contacts ) ) {

						foreach ( $list_contacts as $contact ) {
							$date = strtotime( $contact['date'] );

							if ( '' === $startdate && '' === $enddate ) {
								$contact_count++;
							} elseif ( $date <= strtotime( $enddate ) && $date >= strtotime( $startdate ) ) {
								$contact_count++;
							}
						}
					}

					if ( 0 !== $contact_count ) {

						global $color_pallet;
						if ( $color_index >= count( $color_pallet ) ) {
							$color_index = 0;
						}

						$random_color = array_rand( $color_pallet, count( $color_pallet ) );
						$data[]       = array(
							'color'     => $color_pallet[ $random_color[ $color_index ] ],
							'highlight' => $color_pallet[ $random_color[ $color_index ] ],
							'value'     => $contact_count,
							'label'     => $list_name,
						);
						$color_index++;
					}
				}

				$total_count = $total_count + $contact_count;
			}
		}

		if ( 0 === $total_count || '0' === $total_count ) {
			$message = 'unavailable';
				wp_send_json( $message );

		}

		wp_send_json( $data );

	}
}

if ( ! function_exists( 'get_campaign_daywise_data' ) ) {
	/**
	 * Function to get data for campaign analytics.
	 *
	 * @since 1.0
	 */
	function get_campaign_daywise_data() {

		if ( ! current_user_can( 'access_cp' ) ) {
			die( -1 );
		}

		check_ajax_referer( 'cp_contacts_nonce', 'security_nonce' );

		global $cp_analytics_start_time,$cp_analytics_end_time,$color_pallet;
		$data        = array();
		$date_format = 'M d';
		$startdate   = sanitize_text_field( $_POST['startDate'] );
		$chart_type  = sanitize_text_field( $_POST['chartType'] );
		$enddate     = sanitize_text_field( $_POST['endDate'] );

		$smile_lists = get_option( 'smile_lists' );

		// To unset deactivated / inactive mailer add ons.
		if ( is_array( $smile_lists ) ) {
			foreach ( $smile_lists as $key => $list ) {
				$provider = $list['list-provider'];
				if ( 'Convert Plug' !== $provider ) {
					if ( ! isset( Smile_Framework::$addon_list[ $provider ] ) && ! isset( Smile_Framework::$addon_list[ strtolower( $provider ) ] ) ) {
						unset( $smile_lists[ $key ] );
					}
				}
			}
		}

		if ( ! is_array( $smile_lists ) ) {
			$message = 'unavailable';
			wp_send_json( $message );
		}

		if ( is_array( $_POST['listid'] ) ) {
			$list_ids = $_POST['listid'];
		} else {
			$list_ids = explode( ',', $_POST['listid'] );
		}

		if ( in_array( 'all', $list_ids ) ) {
			if ( $smile_lists ) {
				$list_ids = array_keys( $smile_lists );
			}
		}

		if ( '' === $startdate && '' === $enddate ) {
			$startdate = $cp_analytics_start_time;
			$enddate   = $cp_analytics_end_time;
		} else {
			$startdate = $startdate;
			$enddate   = $enddate;
		}

		$dates_array = get_dates_from_range( $startdate, $enddate, $date_format );

		foreach ( $dates_array as $key => $value ) {
			$data['labels'][] = $key;
		}

		$color_index = 0;
		// Create dataset array.
		foreach ( $list_ids as $list_id ) {

			$date_values = array();
			$list        = $smile_lists[ $list_id ];
			$provider    = $list['list-provider'];
			$list_name   = $list['list-name'];

			$id         = isset( $list['list'] ) ? $list['list'] : '';
			$cp_list_id = 'cp_list_' . $list_id;
			$mailer     = str_replace( ' ', '_', strtolower( trim( $provider ) ) );

			if ( 'convert_plug' !== $mailer ) {
				$contacts_option = 'cp_' . $mailer . '_' . str_replace( ' ', '_', strtolower( trim( $list_name ) ) );
				$contacts        = get_option( $contacts_option );
			} else {
				$contacts_option = 'cp_connects_' . str_replace( ' ', '_', strtolower( trim( $list_name ) ) );
				$contacts        = get_option( $contacts_option );
			}

			if ( $contacts ) {
				// Remove null records from array.
				$contacts = array_filter(
					$contacts,
					function( $k ) {
						return ( null !== $k );
					}
				);

				$counted = array_count_values(
					array_map(
						function( $value ) {
							return $value['date'];
						},
						$contacts
					)
				);

				foreach ( $counted as $key => $value ) {
					$first_date = $key;
					break;
				}
			}

			if ( '' === $startdate && '' === $enddate ) {

				$startdate   = $cp_analytics_start_time;
				$enddate     = $cp_analytics_end_time;
				$dates_array = get_dates_from_range( $startdate, $enddate, $date_format );

				if ( $contacts ) {
					foreach ( $counted as $key => $value ) {

						$date   = strtotime( $key );
						$key    = gmdate( $date_format, strtotime( $key ) );
						$s_date = strtotime( $startdate );
						$e_date = strtotime( $enddate );
						if ( $date <= $e_date && $date >= $s_date ) {
							$dates_array[ $key ] = $value;
						}
					}
				}
			} else {

				$to_date   = ( '' === $enddate ? gmdate( $date_format ) : $enddate );
				$from_date = ( '' === $startdate ? $first_date : $startdate );

				$dates_array = get_dates_from_range( $from_date, $to_date, $date_format );

				if ( $contacts ) {
					foreach ( $counted as $key => $value ) {

						$date   = strtotime( $key );
						$key    = gmdate( $date_format, strtotime( $key ) );
						$s_date = strtotime( $startdate );
						$e_date = strtotime( $enddate );

						if ( $date <= $e_date && $date >= $s_date ) {
							$dates_array[ $key ] = $value;
						}
					}
				}
			}

			$list_data = $dates_array;
			foreach ( $list_data as $key => $value ) {
				$date_values[] = $value;
			}

			if ( $color_index >= count( $color_pallet ) ) {
				$color_index = 0;
			}

			$random_color = array_rand( $color_pallet, count( $color_pallet ) );

			if ( 'bar' === $chart_type ) {
				$data['datasets'][] = array(
					'label'           => urldecode( $list_name ),
					'fillColor'       => $color_pallet[ $random_color[ $color_index ] ],
					'strokeColor'     => $color_pallet[ $random_color[ $color_index ] ],
					'highlightFill'   => $color_pallet[ $random_color[ $color_index ] ],
					'highlightStroke' => $color_pallet[ $random_color[ $color_index ] ],
					'data'            => $date_values,
					'tpl_var_count'   => array_sum( $date_values ),

				);
			} else {
				$data['datasets'][] = array(
					'label'                => urldecode( $list_name ),
					'fillColor'            => 'rgba(229,243,249,0.4)',
					'strokeColor'          => $color_pallet[ $random_color[ $color_index ] ],
					'pointColor'           => $color_pallet[ $random_color[ $color_index ] ],
					'pointStrokeColor'     => $color_pallet[ $random_color[ $color_index ] ],
					'pointHighlightFill'   => $color_pallet[ $random_color[ $color_index ] ],
					'pointHighlightStroke' => 'rgba(68,68,68,0.5)',
					'data'                 => $date_values,
					'tpl_var_count'        => array_sum( $date_values ),
				);
			}

			$color_index++;
		}

		if ( ! array_key_exists( 'datasets', $data ) ) {
			$message = 'unavailable';
			wp_send_json( $message );

		}

		wp_send_json( $data );

	}
}

if ( ! function_exists( 'smile_update_conversions' ) ) {
	/**
	 * Function Name: smile_update_conversions update style conversions.
	 *
	 * @param  string $style_id style id.
	 */
	function smile_update_conversions( $style_id ) {

		global $cp_analytics_end_time;
		$user_role   = '';
		$condition   = true;
		$cp_settings = get_option( 'convert_plug_settings' );

		if ( is_array( $cp_settings ) ) {
			$banneduser = explode( ',', $cp_settings['cp-user-role'] );
		}

		if ( is_user_logged_in() ) {
			$current_user = new WP_User( wp_get_current_user() );
			$user_roles   = $current_user->roles;
			$user_role    = $user_roles[0];
		}

		if ( ! empty( $cp_settings ) ) {
			$condition = ! is_user_logged_in() || ( is_user_logged_in() && ( ! in_array( $user_role, $banneduser ) ) );
		} else {
			$condition = ! is_user_logged_in() || ( is_user_logged_in() && ( 'administrator' !== $user_role ) );
		}

		if ( $condition ) {

			// Save analytics data.
			$existing_data = get_option( 'smile_style_analytics' );

			$date = $cp_analytics_end_time;

			if ( ! is_array( $existing_data ) ) {

				// First conversion.
				$analytics_data = array(
					$style_id => array(
						$date => array(
							'impressions' => 0,
							'conversions' => 1,
						),
					),
				);

			} else {
				if ( isset( $existing_data[ $style_id ] ) ) {
					foreach ( $existing_data[ $style_id ] as $key => $value ) {
						if ( $key === $date ) {
							$old_impressions                     = $value['impressions'];
							$old_conversions                     = $value['conversions'];
							$existing_data[ $style_id ][ $date ] = array(
								'impressions' => $old_impressions,
								'conversions' => $old_conversions + 1,
							);
						}
					}
				} else {
					// First conversion for this particular style.
					$existing_data[ $style_id ] = array(
						$date => array(
							'impressions' => 0,
							'conversions' => 1,
						),
					);
				}
				$analytics_data = $existing_data;
			}

			update_option( 'smile_style_analytics', $analytics_data );
		}
	}
}

if ( ! function_exists( 'cp_export_analytics' ) ) {
	/**
	 * Function to get data for style analytics
	 *
	 * @since 3.3.2
	 */
	function cp_export_analytics() {

		if ( ! current_user_can( 'access_cp' ) ) {
			die( -1 );
		}

		if ( isset( $_GET['analytics_export'] ) && ! wp_verify_nonce( $_GET['analytics_export'], 'cp_analytics_export' ) ) {
			wp_die( 'No direct script access allowed!' );
		}

		$data        = isset( $_POST['an_data'] ) ? stripcslashes( $_POST['an_data'] ) : '';
		$comp_factor = isset( $_POST['comp_factor'] ) ? sanitize_text_field( $_POST['comp_factor'] ) : '';

		if ( 'imp' == $comp_factor ) {
			$comp_factor = 'Impression';
		} elseif ( 'conv' == $comp_factor ) {
			$comp_factor = 'Conversion';
		} elseif ( 'convRate' == $comp_factor ) {
			$comp_factor = 'ConversionRate';
		} elseif ( 'impVsconv' == $comp_factor ) {
			$comp_factor = 'Impression_Vs_Conversion';
		}

		check_admin_referer( 'cp-export-analytics' );
		$data     = json_decode( $data );
		$data_set = isset( $data->datasets ) ? $data->datasets : '';
		$labels   = isset( $data->labels ) ? $data->labels : '';

		$main_data_arr = array();
		$temp_data_arr = array();

		// Add dates to array.
		$main_data_arr['dates'] = $labels;

		foreach ( $data_set as $key => $arr ) {
			foreach ( $arr as $name => $value ) {
				if ( 'label' == $name || 'data' == $name || 'tpl_var_count' == $name ) {

					if ( 'label' == $name ) {
						$style_name = $value;
					}

					if ( 'data' == $name ) {
						$main_data_arr[ $style_name ] = $value;
					}
				}
			}
		}

		// Create array for generatign CSV.
		$final = array();
		foreach ( $labels as $key => $value ) {
			$date               = $value;
			$final_temp         = array();
			$final_temp['date'] = $date;
			foreach ( $main_data_arr as $key => $arr ) {
				if ( 'dates' !== $key ) {
					$style = $key;
					foreach ( $arr as $key => $value ) {
						if ( $date == $labels[ $key ] ) {
							$final_temp[ $style ] = $value;
						}
					}
				}
			}

			array_push( $final, $final_temp );
		}

		$path = plugin_dir_path( __FILE__ );

		if ( is_array( $final ) && $final ) {

			$export_data = cp_generate_csv( $final );
			$content     = $export_data;

			$file_name = $path . $comp_factor . '.csv';
			$file_url  = plugins_url( $comp_factor . '.csv', __FILE__ );
			$handle    = fopen( $file_name, 'w' ); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fopen
			fwrite( $handle, $content ); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fwrite
			fclose( $handle ); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fclose
				header( 'Pragma: public' );   // required.
				header( 'Expires: 0' );   // no cache.
				header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
				header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s', filemtime( $file_name ) ) . ' GMT' );
				header( 'Cache-Control: private', false );
				header( 'Content-Type: application/application/csv' );
				header( 'Content-Disposition: attachment; filename="' . basename( $file_name ) . '"' );
				header( 'Content-Transfer-Encoding: binary' );
				header( 'Content-Length: ' . filesize( $file_name ) );  // provide file size.
				header( 'Connection: close' );
				readfile( $file_name ); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_readfile
				unlink( $file_name );
				exit();

		} else {
			exit();
		}
	}
}

if ( ! function_exists( 'get_style_analytics_data' ) ) {
	/**
	 * Function to get data for style analytics.
	 *
	 * @since 1.0
	 */
	function get_style_analytics_data() {

		if ( ! current_user_can( 'access_cp' ) ) {
			die( -1 );
		}

		check_ajax_referer( 'cp_analytics', 'security' );

		global $cp_analytics_start_time,$cp_analytics_end_time;
		$date_format    = 'M d';
		$color_index    = 0;
		$module         = isset( $_POST['module'] ) ? sanitize_text_field( $_POST['module'] ) : 'modal';
		$analtics_data  = get_option( 'smile_style_analytics' );
		$smile_styles   = get_option( 'smile_' . $module . '_styles' );
		$variant_option = $module . '_variant_tests';
		$variant_tests  = get_option( $variant_option );
		$startdate      = $_POST['startDate'];
		$enddate        = $_POST['endDate'];
		$data           = array();
		$chart_type     = $_POST['chartType'];
		$comp_factor    = $_POST['compFactor'];
		$conversions    = array();
		$impressions    = array();
		$date_values    = array();

		if ( ! is_array( $smile_styles ) ) {
			$message = 'unavailable';
			wp_send_json( $message );
		}

		if ( is_array( $_POST['styleid'] ) ) {
			$style_ids = $_POST['styleid'];

			if ( 1 < count( $_POST['styleid'] ) ) {
				$style = 'multiple';
			} else {
				$style = 'single';
			}
		} else {
			$style     = 'single';
			$style_ids = explode( ',', $_POST['styleid'] );
		}

		if ( in_array( 'all', $style_ids ) ) {
			$style_ids = array();
			foreach ( $smile_styles as $style ) {
				if ( ! isset( $style['multivariant'] ) ) {
					$style_ids[] = $style['style_id'];
				}

				if ( isset( $variant_tests[ $style['style_id'] ] ) ) {
					foreach ( $variant_tests[ $style['style_id'] ] as $value ) {
						$style_ids[] = $value['style_id'];
					}
				}
			}
			$style = 'multiple';
		} else {
			$style_ids[] = $style_ids;

			if ( 'impVsconv' === $comp_factor ) {
				$style = 'single';
			} else {
				$style = 'multiple';
			}
		}
		if ( '' === $startdate && '' === $enddate ) {
			$startdate = $cp_analytics_start_time;
			$enddate   = $cp_analytics_end_time;
		}

		foreach ( $style_ids as $style_id ) {
			$date_values = array();
			$imp_count   = 0;
			$conv_count  = 0;
			$dates_array = get_style_analtics_range( $startdate, $enddate, $date_format, $style );

			$style_name = cp_get_style_name_by_id( $style_id, $smile_styles, $variant_option );

			if ( null !== $style_name ) {

				if ( isset( $analtics_data[ $style_id ] ) ) {
					foreach ( $analtics_data[ $style_id ] as $key => $value ) {

						$date   = strtotime( $key );
						$key    = gmdate( $date_format, strtotime( $key ) );
						$s_date = strtotime( $startdate );
						$e_date = strtotime( $enddate );

						if ( 'line' === $chart_type || 'bar' === $chart_type ) {

							if ( $date <= $e_date && $date >= $s_date ) {
								switch ( $comp_factor ) {
									case 'imp':
										$dates_array[ $key ] = $value['impressions'];
										break;
									case 'conv':
										$dates_array[ $key ] = $value['conversions'];
										break;
									case 'convRate':
										$conversion_rate     = ( $value['conversions'] / $value['impressions'] ) * 100;
										$dates_array[ $key ] = round( $conversion_rate, 2 );
										break;
									case 'impVsconv':
										$dates_array[ $key ] = array(
											'impressions' => $value['impressions'],
											'conversions' => $value['conversions'],
										);
										break;
								}
							}
						} else {
							if ( $date <= $e_date && $date >= $s_date ) {
								$imp_count  = $imp_count + $value['impressions'];
								$conv_count = $conv_count + $value['conversions'];
							}
						}
					}
				}

				$style_data = $dates_array;

				foreach ( $style_data as $key => $value ) {
					$date_values[] = $value;
					if ( 'single' === $style ) {
						$impressions[] = $value['impressions'];
						$conversions[] = $value['conversions'];
					}
				}

				global $color_pallet;

				if ( $color_index >= count( $color_pallet ) ) {
					$color_index = 0;
				}

				$random_color = array_rand( $color_pallet, count( $color_pallet ) );

				if ( 'donut' === $chart_type || 'polararea' === $chart_type ) {

					switch ( $comp_factor ) {
						case 'imp':
							$data_value = $imp_count;
							break;
						case 'conv':
							$data_value = $conv_count;
							break;
						case 'convRate':
							if ( 0 === $imp_count || 0 === $conv_count ) {
								$data_value = 0;
							} else {
								$conv_rate  = ( $conv_count / $imp_count ) * 100;
								$data_value = round( $conv_rate, 2 );
							}
							break;
					}

					if ( 'single' === $style ) {

						if ( 0 !== $imp_count ) {
							$data[] = array(
								'color'     => $color_pallet[ $random_color[ $color_index ] ],
								'highlight' => $color_pallet[ $random_color[ $color_index ] ],
								'value'     => $imp_count,
								'label'     => 'Impressions',
							);
						}

						if ( 0 !== $conv_count ) {
							$data[] = array(
								'color'     => $color_pallet[ $random_color[ $color_index + 1 ] ],
								'highlight' => $color_pallet[ $random_color[ $color_index + 1 ] ],
								'value'     => $conv_count,
								'label'     => 'Conversions',
							);
						}
					} else {
						if ( 0 !== $data_value ) {
							$data[] = array(
								'color'     => $color_pallet[ $random_color[ $color_index ] ],
								'highlight' => $color_pallet[ $random_color[ $color_index ] ],
								'value'     => $data_value,
								'label'     => urldecode( stripslashes( $style_name ) ),
							);
						}
					}
				} else {

					if ( 'single' === $style ) {

						$imp_count  = array_sum( $impressions );
						$conv_count = array_sum( $conversions );

						$data['datasets'][] = array(
							'label'           => 'Impressions',
							'fillColor'       => 'rgba(229,243,249,0.4)',
							'strokeColor'     => $color_pallet[ $random_color[ $color_index ] ],
							'pointColor'      => $color_pallet[ $random_color[ $color_index ] ],
							'highlightFill'   => $color_pallet[ $random_color[ $color_index ] ],
							'highlightStroke' => $color_pallet[ $random_color[ $color_index ] ],
							'data'            => $impressions,
							'tpl_var_count'   => $imp_count,
						);

						$data['datasets'][] = array(
							'label'           => 'Conversions',
							'fillColor'       => 'rgba(229,243,249,0.4)',
							'strokeColor'     => $color_pallet[ $random_color[ $color_index + 1 ] ],
							'pointColor'      => $color_pallet[ $random_color[ $color_index + 1 ] ],
							'highlightFill'   => $color_pallet[ $random_color[ $color_index + 1 ] ],
							'highlightStroke' => $color_pallet[ $random_color[ $color_index + 1 ] ],
							'data'            => $conversions,
							'tpl_var_count'   => $conv_count,
						);

					} else {

						if ( 'convRate' === $comp_factor ) {
							$var_count = cp_calculate_average( $date_values ) . ' %';
						} else {
							$var_count = array_sum( $date_values );
						}

						if ( 'bar' === $chart_type ) {
							$data['datasets'][] = array(
								'label'           => urldecode( stripslashes( $style_name ) ),
								'fillColor'       => $color_pallet[ $random_color[ $color_index ] ],
								'strokeColor'     => $color_pallet[ $random_color[ $color_index ] ],
								'highlightFill'   => $color_pallet[ $random_color[ $color_index ] ],
								'highlightStroke' => $color_pallet[ $random_color[ $color_index ] ],
								'data'            => $date_values,
								'tpl_var_count'   => $var_count,
							);
						} else {
							$data['datasets'][] = array(
								'label'                => urldecode( stripslashes( $style_name ) ),
								'fillColor'            => 'rgba(229,243,249,0.4)',
								'strokeColor'          => $color_pallet[ $random_color[ $color_index ] ],
								'pointColor'           => $color_pallet[ $random_color[ $color_index ] ],
								'pointStrokeColor'     => $color_pallet[ $random_color[ $color_index ] ],
								'pointHighlightFill'   => $color_pallet[ $random_color[ $color_index ] ],
								'pointHighlightStroke' => 'rgba(68,68,68,0.5)',
								'data'                 => $date_values,
								'tpl_var_count'        => $var_count,
							);
						}
					}
				}
			}
			$color_index++;
		}

		if ( empty( $data ) ) {
			$message = 'unavailable';
			wp_send_json( $message );

		}

		if ( 'line' === $chart_type || 'bar' === $chart_type ) {
			foreach ( $dates_array as $key => $value ) {
				if ( 0 !== $key && null !== $key ) {
					$data['labels'][] = $key;
				}
			}
		}

		wp_send_json( $data );
	}
}


if ( ! function_exists( 'get_dates_from_range' ) ) {
	/**
	 * Function Name: get_dates_from_range Function to return array of dates from particular range.
	 *
	 * @param  string $start       string parameter.
	 * @param  string $end         string parameter.
	 * @param  string $date_format string parameter.
	 * @return array              array parameter.
	 */
	function get_dates_from_range( $start, $end, $date_format ) {
		$interval = new DateInterval( 'P1D' );

		$real_end = new DateTime( $end );
		$real_end->add( $interval );

		$period = new DatePeriod(
			new DateTime( $start ),
			$interval,
			$real_end
		);

		foreach ( $period as $date ) {
			$array[ $date->format( $date_format ) ] = 0;
		}

		return $array;
	}
}

if ( ! function_exists( 'get_style_analtics_range' ) ) {
	/**
	 * Function which returns array of dates for impression and conversions.
	 *
	 * @param  string $start       string parameter.
	 * @param  string $end         string parameter.
	 * @param  string $date_format string parameter.
	 * @param  string $type        string parameter.
	 * @return array              array parameter.
	 */
	function get_style_analtics_range( $start, $end, $date_format, $type ) {
		$interval = new DateInterval( 'P1D' );

		$real_end = new DateTime( $end );
		$real_end->add( $interval );

		$period = new DatePeriod(
			new DateTime( $start ),
			$interval,
			$real_end
		);

		foreach ( $period as $date ) {
			if ( 'single' !== $type ) {
				$array[ $date->format( $date_format ) ] = 0;
			} else {
				$array[ $date->format( $date_format ) ] = array(
					'impressions' => 0,
					'conversions' => 0,
				);
			}
		}
		return $array;
	}
}

if ( ! function_exists( 'cp_get_style_name_by_id' ) ) {
	/**
	 * Function to get style name by its ID.
	 *
	 * @param  string $style_id     string parameter.
	 * @param  array  $smile_styles array parameter.
	 * @param  array  $variant      array parameter.
	 * @return string               string parameter.
	 */
	function cp_get_style_name_by_id( $style_id, $smile_styles, $variant ) {
		$styles = $smile_styles;
		if ( ! empty( $styles ) ) {
			foreach ( $styles as $style ) {
				if ( $style['style_id'] == $style_id ) {
					return $style['style_name'];
				}
			}
		}

		$variant_styles = ( '' !== $variant ) ? get_option( $variant ) : false;

		if ( $variant_styles ) {
			foreach ( $variant_styles as $key => $value ) {
				if ( count( $value ) > 0 ) {
					foreach ( $value as $variantstyle ) {
						if ( $variantstyle['style_id'] == $style_id ) {
							$style_name = $variantstyle['style_name'];
							return urldecode( stripslashes( $style_name ) );
						}
					}
				}
			}
		}
	}
}


/**
* Function for updating creating duplicate style name
 *
* @since 1.0
*/
if ( ! function_exists( 'smile_duplicate_style_name' ) ) {
	/**
	 * Function for updating creating duplicate style name.
	 *
	 * @param  array  $prev_styles array parameter.
	 * @param  string $style_name  string parameter.
	 * @return string              string parameter.
	 */
	function smile_duplicate_style_name( $prev_styles, $style_name ) {

		$style_present = false;

		foreach ( $prev_styles as $style ) {

			if ( $style['style_name'] !== $style_name ) {

				if ( 0 === strpos( $style['style_name'], $style_name . '_', 0 ) ) {

					$postfix_number_position = strlen( $style_name ) + 1;
					$postfix_string          = substr( $style['style_name'], $postfix_number_position );
					if ( false === strpos( $postfix_string, '_' ) ) {
						$style_present      = true;
						$incremental_number = $postfix_string + 1;
						$new_style_name     = $style_name . '_' . $incremental_number;
					}
				}
			}
		}

		if ( ! $style_present ) {
			$new_style_name = $style_name . '_1';
		}

		return $new_style_name;
	}
}
if ( ! function_exists( 'cp_google_recaptcha' ) ) {
	/**
	 * Function to accept ajax call for updating User settings.
	 */
	function cp_google_recaptcha() {

		if ( ! current_user_can( 'access_cp' ) ) {
			die( -1 );
		}

		check_admin_referer( 'cp_google_recaptcha-nonce', 'security_nonce' );

		$cp_recaptcha_site_key   = isset( $_POST['cp_recaptcha_site_key'] ) ? $_POST['cp_recaptcha_site_key'] : '';
		$cp_recaptcha_secret_key = isset( $_POST['cp_recaptcha_secret_key'] ) ? $_POST['cp_recaptcha_secret_key'] : '';

		$result = update_option( 'cp_recaptcha_site_key', sanitize_text_field( $cp_recaptcha_site_key ) );
		$result = update_option( 'cp_recaptcha_secret_key ', sanitize_text_field( $cp_recaptcha_secret_key ) );

		if ( $result ) {
			wp_send_json(
				array(
					'message' => __( 'Settings Updated!', 'smile' ),
				)
			);
		} else {
			wp_send_json(
				array(
					'message' => __( 'No settings were updated. Try again!', 'smile' ),
				)
			);

		}

	}
}

if ( ! function_exists( 'cplus_maxmind' ) ) {
	/**
	 * Function to save the setting af MaxMind Integration.
	 */
	function cplus_maxmind() {

		if ( ! current_user_can( 'access_cp' ) ) {
			die( -1 );
		}

		check_admin_referer( 'cp_maxmind', 'security_nonce' );

		$cplus_maxmind_license_key = isset( $_POST['cplus_maxmind_license_key'] ) ? sanitize_text_field( $_POST['cplus_maxmind_license_key'] ) : '';

		$cp_geo = new Convert_Plug_Maxmind_Geolocation();
		$result = $cp_geo->verify_key_and_download_database( $cplus_maxmind_license_key );

		if ( false === $result['error'] ) {
			wp_send_json(
				array(
					'message' => __( 'Settings Updated!', 'smile' ),
				)
			);
		} else {
			wp_send_json(
				array(
					'message' => __( 'No settings were updated. Try again!', 'smile' ),
				)
			);

		}

	}
}



if ( ! function_exists( 'smile_update_settings' ) ) {
	/**
	 * Function to accept ajax call for updating User settings.
	 */
	function smile_update_settings() {

		if ( ! current_user_can( 'access_cp' ) ) {
			die( -1 );
		}

		check_admin_referer( 'cp-smile_update_settings-nonce', 'security_nonce' );

		$email_content = isset( $_POST['cp-email-body'] ) ? $_POST['cp-email-body'] : '';
		unset( $_POST['cp-email-body'] );
		$module_list                  = array_map( 'sanitize_text_field', wp_unslash( $_POST ) );
		$module_list['cp-email-body'] = $email_content;
		unset( $module_list['action'] );
		$new_module_list = array();

		if ( ! isset( $_POST['cp-access-role'] ) ) {
			$old_settings                      = get_option( 'convert_plug_settings' );
			$new_module_list['cp-access-role'] = $old_settings['cp-access-role'];
		}

		foreach ( $module_list as $module => $file ) {
			$new_module_list[ $module ] = $file;
		}

		$result = update_option( 'convert_plug_settings', $new_module_list );
		if ( $result ) {
			wp_send_json(
				array(
					'message' => __( 'Settings Updated!', 'smile' ),
				)
			);

		} else {
			wp_send_json(
				array(
					'message' => __( 'No settings were updated. Try again!', 'smile' ),
				)
			);
		}
	}
}

if ( ! function_exists( 'smile_update_debug' ) ) {
	/**
	 * Function for ajax callback to save debug options
	 */
	function smile_update_debug() {

		if ( ! current_user_can( 'access_cp' ) ) {
			die( -1 );
		}

		check_admin_referer( 'smile_update_debug_nonce', 'security_nonce' );

		$opts   = array_map( 'sanitize_text_field', wp_unslash( $_POST ) );
		$result = update_option( 'convert_plug_debug', $opts );
		if ( $result ) {
			wp_send_json(
				array(
					'message' => __( 'Settings Updated!', 'smile' ),
				)
			);
		} else {
			wp_send_json(
				array(
					'message' => __( 'No settings were updated. Try again!', 'smile' ),
				)
			);
		}
	}
}

if ( ! function_exists( 'cp_calculate_average' ) ) {
	/**
	 * Function to calculate average of array values.
	 *
	 * @param  array $arr array parameter.
	 * @return mixed      content val.
	 */
	function cp_calculate_average( $arr ) {
		$total = 0;
		$count = count( $arr ); // Total numbers in array.
		foreach ( $arr as $value ) {
			$total = $total + $value; // Total value of array numbers.
		}
		$average = round( ( $total / $count ), 2 ); // Get average value.
		return $average;
	}
}

if ( ! function_exists( 'cp_is_list_assigned' ) ) {
	/**
	 * Function to check if list is assigned to any modal or info bar.
	 */
	function cp_is_list_assigned() {
		if ( ! current_user_can( 'access_cp' ) ) {
			die( -1 );
		}

		check_admin_referer( 'cp_is_list_assigned', 'security_nonce' );

		$list_id     = ( isset( $_POST['list_id'] ) ) ? intval( $_POST['list_id'] ) : '';
		$is_assigned = false;
		$assigned_to = array();

		$modules = array(
			'modal',
			'info_bar',
			'slide_in',
		);

		foreach ( $modules as $module ) {

			$styles        = get_option( 'smile_' . $module . '_styles' );
			$variant_tests = get_option( $module . '_variant_tests' );

			if ( $styles && is_array( $styles ) ) {
				foreach ( $styles as $style ) {
					$style_settings = maybe_unserialize( $style['style_settings'] );
					$style_id       = $style['style_id'];
					$style_name     = urldecode( $style['style_name'] );

					if ( isset( $style_settings['mailer'] ) ) {
						$mailer = $style_settings['mailer'];
						$theme  = $style_settings['style'];

						if ( ! isset( $style['multivariant'] ) ) {
							if ( $mailer == $list_id ) {
								$is_assigned = true;
								$link        = '?page=smile-' . $module . '-designer&style-view=edit&style=' . $style_id . '&theme=' . urlencode_deep( $theme );
								$style_arr   = array(
									$style_name => $link,
								);
								array_push( $assigned_to, $style_arr );
							}
						}

						// Check if list is assigned to any variant.
						if ( $variant_tests && is_array( $variant_tests ) ) {
							if ( isset( $variant_tests[ $style_id ] ) ) {
								foreach ( $variant_tests[ $style_id ] as $key => $variant_test ) {
									$style_settings   = maybe_unserialize( $variant_test['style_settings'] );
									$var_style_name   = urldecode( $variant_test['style_name'] );
									$variant_style_id = $variant_test['style_id'];
									$mailer           = $style_settings['mailer'];
									if ( $mailer == $list_id ) {
										$is_assigned = true;
										$link        = '?page=smile-' . $module . '-designer&style-view=variant&variant-test=edit&variant-style=' . $variant_style_id . '&style=' . stripslashes( $var_style_name );
										$link       .= '&parent-style=' . urlencode_deep( stripslashes( $style_name ) ) . '&style_id=' . $style_id . '&theme=' . urlencode_deep( $theme );
										$style_arr   = array(
											$var_style_name => $link,
										);
										array_push( $assigned_to, $style_arr );
									}
								}
							}
						}
					}
				}
			}
		}

		$assigned_to = apply_filters( 'is_list_assign_check', $assigned_to, $list_id );

		$is_assigned = ( 0 < count( $assigned_to ) ) ? true : false;

		if ( $is_assigned ) {
			$style_count = count( $assigned_to );
			wp_send_json(
				array(
					'message'     => 'yes',
					'assigned_to' => $assigned_to,
					'style_count' => $style_count,
				)
			);
		} else {
			wp_send_json(
				array(
					'message' => 'no',
				)
			);

		}
	}
}

/**
 * Get behavior section settings i.e. Launch,repeat,target control settings.
 *
 * @param  array  $data   array parameter.
 * @param  string $module module name.
 * @return mixed         html/string val.
 */
function get_quick_behavior_settings( $data, $module ) {

	if ( 'modal' === $module ) {
		$exit_intent = $data['modal_exit_intent'];
	} elseif ( 'info_bar' === $module ) {
		$exit_intent = $data['ib_exit_intent'];
	} elseif ( 'slide_in' === $module ) {
		$exit_intent = $data['slidein_exit_intent'];
	}

	$add_to_cart = '';
	$add_to_cart = isset( $data['add_to_cart'] ) && $data['add_to_cart'] ? 'Yes' : 'No';

	$enable_after_content = '';
	$enable_after_content = isset( $data['enable_after_content'] ) && $data['enable_after_content'] ? 'Yes' : 'No';

	// Define launch control parameters and respective values.
	$launch_control = array(

		'Load After Seconds'               => ( $data['autoload_on_duration'] ) ? 'Yes, ' . $data['load_on_duration'] . ' Seconds' : 'No',
		'Load After Scroll'                => ( $data['autoload_on_scroll'] ) ? 'Yes, ' . $data['load_after_scroll'] . '% Scroll' : 'No',
		'Launch After Content'             => $enable_after_content,
		'When User Is Inactive'            => ( $data['inactivity'] ) ? 'Yes' : 'No',
		'Launch With CSS Class'            => ( '' !== $data['custom_class'] ) ? 'Yes, with <b>' . $data['custom_class'] . '</b>' : 'No',
		'Before User Leaves / Exit Intent' => ( $exit_intent ) ? 'Yes' : 'No',
		'Launch When Items Added To Cart'  => ( $add_to_cart ) ? 'Yes' : 'No',

	);

	// Define repeat control parameters and respective values.
	$repeat_control = array(
		'Enable Cookies'               => ( $data['developer_mode'] ) ? 'Yes' : 'No',
		'Do Not Show After Conversion' => ( $data['developer_mode'] ) ? $data['conversion_cookie'] . ' Days' : '',
		'Do Not Show After Closing'    => ( $data['developer_mode'] ) ? $data['closed_cookie'] . ' Days' : '',
	);

	$disabled_pages      = '';
	$enabled_pages       = '';
	$disabled_on         = '';
	$enabled_on          = '';
	$exclude_post_type   = '';
	$exclusive_post_type = '';

	// Pages to exclude.
	if ( isset( $data['exclude_from'] ) && '' !== $data['exclude_from'] ) {
		$disabled_pages = explode( ',', $data['exclude_from'] );
		$exclude_pages  = '';

		foreach ( $disabled_pages as $key => $page ) {
			if ( false !== strpos( $page, 'tax-' ) ) {
				$tax_id     = str_replace( 'tax-', '', $page );
				$type       = cp_get_taxonomy_by_id( $tax_id );
				$page_title = $type;
			} elseif ( false !== strpos( $page, 'post-' ) ) {
				$page_title = get_the_title( str_replace( 'post-', '', $page ) );
			} elseif ( false !== strpos( $page, 'special-' ) ) {
				$page_title = ucfirst( str_replace( 'special-', '', $page ) ) . ' Page';
			}

			$disabled_pages[ $key ] = substr( $page_title, 0, 15 );
		}

		$total_disabled_pages = count( $disabled_pages );

		if ( $total_disabled_pages > 5 ) {
			$disabled_pages     = array_slice( $disabled_pages, 0, 5, true );
			$rem_disabled_pages = $total_disabled_pages - 5;
			$disabled_pages     = implode( ', ', $disabled_pages );
			$disabled_pages    .= ' and ' . $rem_disabled_pages . ' more';
		} else {
			$disabled_pages = implode( ', ', $disabled_pages );
		}
	}

	// Display excluded post types.
	if ( isset( $data['exclude_post_type'] ) && '' !== $data['exclude_post_type'] ) {
		$exclude_post_type = explode( ',', $data['exclude_post_type'] );

		$total_exclude_post_type = count( $exclude_post_type );

		if ( 5 < $total_exclude_post_type ) {
			$exclude_post_type = array_slice( $exclude_post_type, 0, 5, true );

			foreach ( $exclude_post_type as $key => $post_type ) {
				$post_type                 = str_replace( 'cp-', '', $post_type );
				$post_type                 = ucfirst( str_replace( 'post_', '', $post_type ) );
				$exclude_post_type[ $key ] = $post_type;
			}

			$rem_total_exclude_post_type = $total_exclude_post_type - 5;
			$exclude_post_type           = implode( ', ', $exclude_post_type );
			$exclude_post_type          .= ' and ' . $rem_total_exclude_post_type . ' more';
		} else {
			foreach ( $exclude_post_type as $key => $post_type ) {
				$post_type                 = str_replace( 'cp-', '', $post_type );
				$post_type                 = ucfirst( str_replace( 'post_', '', $post_type ) );
				$exclude_post_type[ $key ] = $post_type;
			}
			$exclude_post_type = implode( ', ', $exclude_post_type );
		}
	}

	$disabled_on .= '<ul><li></li>';

	if ( '' !== $disabled_pages ) {
		$disabled_on .= '<li><b>Pages / Posts / Terms</b> - ' . $disabled_pages . '</li>';
	}

	if ( '' !== $exclude_post_type ) {
		$disabled_on .= '<li><b>Post Types / Taxonomies</b> - ' . $exclude_post_type . '</li>';
	}

	$disabled_on .= '</ul>';

	// Display exclusive pages.
	if ( isset( $data['exclusive_on'] ) && '' !== $data['exclusive_on'] ) {
		$enabled_pages = explode( ',', $data['exclusive_on'] );
		$exclude_pages = '';
		foreach ( $enabled_pages as $key => $page ) {
			if ( false !== strpos( $page, 'tax-' ) ) {
				$tax_id     = str_replace( 'tax-', '', $page );
				$type       = cp_get_taxonomy_by_id( $tax_id );
				$page_title = $type;
			} elseif ( false !== strpos( $page, 'post-' ) ) {
				$page_title = get_the_title( str_replace( 'post-', '', $page ) );
			} elseif ( false !== strpos( $page, 'special-' ) ) {
				$page_title = ucfirst( str_replace( 'special-', '', $page ) ) . ' Page';
			}

			$enabled_pages[ $key ] = substr( $page_title, 0, 15 );
		}

		$total_enabled_pages = count( $enabled_pages );

		if ( 5 < $total_enabled_pages ) {
			$enabled_pages     = array_slice( $enabled_pages, 0, 5, true );
			$rem_enabled_pages = $total_enabled_pages - 5;
			$enabled_pages     = implode( ', ', $enabled_pages );
			$enabled_pages    .= ' and ' . $rem_enabled_pages . ' more';
		} else {
			$enabled_pages = implode( ', ', $enabled_pages );
		}
	}

	// Display exclusive post types.
	if ( isset( $data['exclusive_post_type'] ) && '' !== $data['exclusive_post_type'] ) {
		$exclusive_post_type       = explode( ',', $data['exclusive_post_type'] );
		$total_exclusive_post_type = count( $exclusive_post_type );

		if ( 5 < $total_exclusive_post_type ) {
			$exclusive_post_type = array_slice( $exclusive_post_type, 0, 5, true );

			foreach ( $exclusive_post_type as $key => $post_type ) {
				$post_type                   = str_replace( 'cp-', '', $post_type );
				$post_type                   = ucfirst( str_replace( 'post_', '', $post_type ) );
				$exclusive_post_type[ $key ] = $post_type;
			}

			$rem_total_exclusive_post_type = $total_exclusive_post_type - 5;
			$exclusive_post_type           = implode( ', ', $exclusive_post_type );
			$exclusive_post_type          .= ' and ' . $rem_total_exclusive_post_type . ' more';
		} else {
			foreach ( $exclusive_post_type as $key => $post_type ) {
				$post_type                   = str_replace( 'cp-', '', $post_type );
				$post_type                   = ucfirst( str_replace( 'post_', '', $post_type ) );
				$exclusive_post_type[ $key ] = $post_type;
			}
			$exclusive_post_type = implode( ', ', $exclusive_post_type );
		}
	}

	$enabled_on .= '<ul><li></li>';

	if ( '' !== $enabled_pages ) {
		$enabled_on .= '<li><b>Pages / Posts / Terms</b> - ' . $enabled_pages . '</li>';
	}

	if ( '' !== $exclusive_post_type ) {
		$enabled_on .= '<li><b>Post Types / Taxonomies</b> - ' . $exclusive_post_type . '</li>';
	}

	$enabled_on .= '</ul>';

	// Define target pages parameters and respective values.
	$target_pages = array(
		'Enable On Complete Site'   => ( $data['global'] ) ? '<b>Yes</b>' : '<b>No</b>',
		'Exceptionally, Disable On' => ( $data['global'] && '' !== wp_strip_all_tags( $disabled_on ) ) ? $disabled_on : '',
		'Enable Only On'            => ( ! $data['global'] && '' !== wp_strip_all_tags( $enabled_on ) ) ? $enabled_on : '',
	);

	// Define target visitors parameters and respective values.
	$target_visitors = array(
		'Logged In Users'  => ( $data['show_for_logged_in'] ) ? 'Yes' : 'No',
		'First Time Users' => ( $data['display_on_first_load'] ) ? 'Yes' : 'No',
	);

	$behavior_settings  = '<div class=\'cp-row first-row\'><div class=\'col-md-6 cp-behavior-col-first\'><ul>';
	$behavior_settings .= '<li><i class=\'connects-icon-location-2\'></i><b>Launch Control</b></li>';

	foreach ( $launch_control as $key => $value ) {
		if ( '' !== $value ) {
			$behavior_settings .= '<li>' . $key . ' - <b>' . $value . '</b></li>';
		}
	}
	$behavior_settings .= '</ul></div>';

	$behavior_settings .= '<div class=\'col-md-6 cp-behavior-col-second\'><ul><li><i class=\'connects-icon-repeat\'></i><b>Repeat Control</b></li>';

	foreach ( $repeat_control as $key => $value ) {
		if ( '' !== $value ) {
			$behavior_settings .= '<li>' . $key . ' - <b>' . $value . '</b></li>';
		}
	}

	$behavior_settings .= '</div></div><div class=\'cp-row second-row\'><div class=\'col-md-6 cp-behavior-col-first\'><ul><li><i class=\'connects-icon-paper\'></i><b>Target Pages</b></li>';

	foreach ( $target_pages as $key => $value ) {
		if ( '' !== $value ) {
			$behavior_settings .= '<li>' . $key . ' - ' . $value . '</li>';
		}
	}

	$behavior_settings .= '</ul></div>';

	$behavior_settings .= '<div class=\'col-md-6 cp-behavior-col-second\'><ul><li><i class=\'connects-icon-head\'></i><b>Target Visitors</b></li>';

	foreach ( $target_visitors as $key => $value ) {
		if ( '' !== $value ) {
			$behavior_settings .= '<li>' . $key . ' - <b>' . $value . '</b></li>';
		}
	}

	$behavior_settings .= '</ul></div></div>';

	return $behavior_settings;
}

/**
 * Function to add behavior settings icon after delete button.
 *
 * @param  array  $settings setting array.
 * @param  string $module   module name.
 * @return mixed           content.
 */
function cp_before_delete_action_init( $settings, $module ) {
	ob_start();
	// Retrieve behavior related settings.
	$behavior_settings = get_quick_behavior_settings( $settings, $module );
	if ( ! isset( $settings['variant-style'] ) ) {
		$style_id = $settings['style_id'];
	} else {
		$style_id = $settings['variant-style'];
	}

	$analytics_data = get_option( 'smile_style_analytics' );
	// Style Behavior.
	?>
	<a class="action-list cp-behavior-settings" data-position="left" style="margin-left: 25px;" data-settings="<?php echo esc_attr( $behavior_settings ); ?>">
		<span class="action-tooltip">Behavior Quick View</span><i class="connects-icon-paper"></i>
	</a>
	<?php if ( isset( $analytics_data[ $style_id ] ) ) { ?>
	<a class="action-list cp-reset-analytics" data-style="<?php echo esc_attr( $style_id ); ?>" data-position="left" style="margin-left: 25px;cursor:pointer;">
		<span class="action-tooltip">Reset Analytics</span><i class="connects-icon-reload"></i>
	</a>
	<?php } ?>

	<?php
	return ob_get_clean();
}

add_filter( 'cp_before_delete_action', 'cp_before_delete_action_init', 10, 2 );

/**
 * Get taxonomy name by ID.
 *
 * @param  string $tax_id  tax_id.
 * @return boolval(var)    true/false.
 */
function cp_get_taxonomy_by_id( $tax_id ) {

	$args = array(
		'public'   => true,
		'_builtin' => false,
	);

	$output     = 'objects'; // Names or objects, note names is the default.
	$operator   = 'and';
	$taxonomies = get_taxonomies( $args, $output, $operator );

	if ( is_array( $taxonomies ) ) {
		foreach ( $taxonomies as $taxonomy ) {

			$terms = get_terms(
				$taxonomy->name,
				array(
					'orderby'    => 'count',
					'hide_empty' => 0,
				)
			);

			if ( ! empty( $terms ) ) {
				foreach ( $terms as $term ) {
					if ( $tax_id == $term->term_id ) {
						return $term->name;
					}
				}
			}
		}
	}

	$args = array(
		'public'   => true,
		'_builtin' => true,
	);

	$taxonomies = get_taxonomies( $args, $output, $operator );

	if ( is_array( $taxonomies ) ) {
		foreach ( $taxonomies as $taxonomy ) {

			$terms = get_terms(
				$taxonomy->name,
				array(
					'orderby'    => 'count',
					'hide_empty' => 0,
				)
			);

			if ( ! empty( $terms ) ) {
				foreach ( $terms as $term ) {
					if ( $tax_id == $term->term_id ) {
						return $term->name;
					}
				}
			}
		}
	}

	return false;
}

if ( ! function_exists( 'cp_reset_analytics' ) ) {
	/**
	 * Function to reset analytics data for style.
	 *
	 * @param  string $style_id  style id.
	 * @return boolval(var)      val.
	 */
	function cp_reset_analytics( $style_id ) {
		$analytics_data = get_option( 'smile_style_analytics' );
		if ( isset( $analytics_data[ $style_id ] ) ) {
			unset( $analytics_data[ $style_id ] );
		}

		$result = update_option( 'smile_style_analytics', $analytics_data );
		return $result;
	}
}

if ( ! function_exists( 'cp_reset_analytics_action' ) ) {
	/**
	 * Function Name:cp_reset_analytics_action.
	 */
	function cp_reset_analytics_action() {

		if ( ! current_user_can( 'access_cp' ) ) {
			die( -1 );
		}

		check_admin_referer( 'cp-reset-analytics', 'security_nonce' );

		$style_id = esc_attr( $_POST['style_id'] );
		$result   = cp_reset_analytics( $style_id );
		echo 'reset';
		die();
	}
}

if ( ! function_exists( 'cp_get_posts_by_query' ) ) {
	/**
	 * Function Name: .
	 */
	function cp_get_posts_by_query() {

		if ( ! current_user_can( 'access_cp' ) ) {
			die( -1 );
		}
		check_admin_referer( 'cp_get_posts_by_query_nonce', 'security_nonce' );

		$search_string = isset( $_POST['q'] ) ? $_POST['q'] : '';
		$data          = array();
		$result        = array();

		$args = array(
			'public'   => true,
			'_builtin' => false,
		);

		$output     = 'names'; // Names or objects, note names is the default.
		$operator   = 'and';
		$post_types = get_post_types( $args, $output, $operator );

		$post_types['Posts'] = 'post';
		$post_types['Pages'] = 'page';

		foreach ( $post_types as $key => $post_type ) {

			$data = array();

			$query = new WP_Query(
				array(
					's'         => $search_string,
					'post_type' => $post_type,
				)
			);

			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();
					$title  = get_the_title();
					$id     = get_the_id();
					$data[] = array(
						'id'   => 'post-' . $id . '::' . $title,
						'text' => $title,
					);
				}
			}

			if ( is_array( $data ) && ! empty( $data ) ) {
				$result[] = array(
					'text'     => $key,
					'children' => $data,
				);
			}
		}

		$data = array();

		wp_reset_postdata();

		$args = array(
			'public' => true,
		);

		$output     = 'objects'; // Names or objects, note names is the default.
		$operator   = 'and';
		$taxonomies = get_taxonomies( $args, $output, $operator );

		foreach ( $taxonomies as $taxonomy ) {
			$terms = get_terms(
				$taxonomy->name,
				array(
					'orderby'    => 'count',
					'hide_empty' => 0,
					'name__like' => $search_string,
				)
			);

			$data = array();

			$label = ucwords( $taxonomy->label );

			if ( ! empty( $terms ) ) {

				foreach ( $terms as $term ) {

					$data[] = array(
						'id'   => 'tax-' . $term->term_id . '::' . $term->name,
						'text' => $term->name,
					);

				}
			}

			if ( is_array( $data ) && ! empty( $data ) ) {
				$result[] = array(
					'text'     => $label,
					'children' => $data,
				);
			}
		}

		$data = array();

		// Special Pages.
		$spacial_pages = array(
			'blog'       => 'Blog / Posts Page',
			'front_page' => 'Front Page',
			'archive'    => 'Archive Page',
			'author'     => 'Author Page',
			'search'     => 'Search Page',
			'404'        => '404 Page',
		);

		foreach ( $spacial_pages as $page => $title ) {
			$data[] = array(
				'id'   => 'special-' . $page,
				'text' => $title,
			);
		}

		if ( is_array( $data ) && ! empty( $data ) ) {
			$result[] = array(
				'text'     => 'Special Pages',
				'children' => $data,
			);
		}

		// Return the result in json.
		wp_send_json( $result );

	}
}



if ( ! function_exists( 'cp_get_active_campaigns' ) ) {
	/**
	 * Get list of active campaigns.
	 */
	function cp_get_active_campaigns() {

		if ( ! current_user_can( 'access_cp' ) ) {
			die( -1 );
		}

		$source = ( isset( $_POST['source'] ) && 'cp-addon' === $_POST['source'] ) ? true : false; // phpcs:ignore WordPress.Security.NonceVerification.Missing

		if ( $source ) {
			$smile_lists = get_option( 'smile_lists' );
			$req_data    = array();
			// To unset deactivated / inactive mailer addons.
			if ( is_array( $smile_lists ) ) {
				foreach ( $smile_lists as $key => $list ) {
					$provider = $list['list-provider'];
					if ( 'Convert Plug' !== $provider ) {
						if ( ! isset( Smile_Framework::$addon_list[ $provider ] ) && ! isset( Smile_Framework::$addon_list[ strtolower( $provider ) ] ) ) {
							unset( $smile_lists[ $key ] );
						} else {
							$data             = array(
								'list-provider' => $list['list-provider'],
								'list-name'     => $list['list-name'],
							);
							$req_data[ $key ] = $data;
						}
					} else {
						$data             = array(
							'list-provider' => $list['list-provider'],
							'list-name'     => $list['list-name'],
						);
						$req_data[ $key ] = $data;
					}
				}
			}

			wp_send_json( $req_data );
		}

	}
}


if ( ! function_exists( 'cp_import_presets' ) ) {
	/**
	 * Function Name: cp_import_presets.
	 */
	function cp_import_presets() {

		if ( ! current_user_can( 'access_cp' ) ) {
			die( -1 );
		}
		check_admin_referer( 'cp_presets_nonce', 'security_nonce' );

		$module = isset( $_POST['module'] ) ? $_POST['module'] : '';
		$preset = isset( $_POST['preset'] ) ? $_POST['preset'] : '';

		if ( '' !== $module ) {
			$cp_import = new Convert_Plug_Import( $module, $preset );
		}

	}
}

if ( ! function_exists( 'cp_import_presets_step2' ) ) {
	/**
	 * Function Name: cp_import_presets_step2.
	 */
	function cp_import_presets_step2() {

		if ( ! current_user_can( 'access_cp' ) ) {
			die( -1 );
		}

		check_admin_referer( 'cp_presets_nonce', 'security_nonce' );

		$module = isset( $_POST['module'] ) ? sanitize_text_field( $_POST['module'] ) : '';
		if ( '' !== $module ) {
			$cp_import = new Convert_Plug_Import( $module );
			$cp_import->cp_import_preset_frontend( $module );
		}
	}
}

if ( ! function_exists( 'handle_cp_export_list_action' ) ) {
	/**
	 * Function Name: handle_cp_export_list_action.
	 */
	function handle_cp_export_list_action() {

		if ( ! current_user_can( 'access_cp' ) ) {
			die( -1 );
		}

		$list_id = intval( $_POST['list_id'] );

		check_admin_referer( 'export-list-' . $list_id );

		$path = plugin_dir_path( __FILE__ );

		if ( '' !== $list_id ) {

			$smile_lists = get_option( 'smile_lists' );
			$provider    = '';
			$list_name   = '';
			if ( $smile_lists ) {
				if ( isset( $smile_lists[ $list_id ] ) ) {
					$list      = $smile_lists[ $list_id ];
					$list_name = $list['list-name'];
					$provider  = $list['list-provider'];
				}
			}

			$id        = isset( $list['list'] ) ? $list['list'] : '';
			$list_name = str_replace( ' ', '_', strtolower( trim( $list['list-name'] ) ) );
			$mailer    = str_replace( ' ', '_', strtolower( trim( $provider ) ) );

			if ( 'convert_plug' !== $mailer ) {
				$list_option = 'cp_' . $mailer . '_' . $list_name;
				$contacts    = get_option( $list_option );
			} else {
				$list_option = 'cp_connects_' . $list_name;
				$contacts    = get_option( $list_option );
			}

			if ( is_array( $contacts ) && $contacts ) {

				$export_data = cp_generate_csv( $contacts );
				$content     = $export_data;

				$file_name = $path . 'cp_export[' . $list_name . '].csv';
				$file_url  = plugins_url( 'cp_export[' . $list_name . '].csv', __FILE__ );
				$handle    = fopen( $file_name, 'w' );  //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fopen
				fwrite( $handle, $content ); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fwrite
				fclose( $handle ); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fclose

				header( 'Pragma: public' );   // Required.
				header( 'Expires: 0' );   // No Cache.
				header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
				header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s', filemtime( $file_name ) ) . ' GMT' );
				header( 'Cache-Control: private', false );
				header( 'Content-Type: application/application/csv' );
				header( 'Content-Disposition: attachment; filename="' . basename( $file_name ) . '"' );
				header( 'Content-Transfer-Encoding: binary' );
				header( 'Content-Length: ' . filesize( $file_name ) );  // Provide File Size.
				header( 'Connection: close' );
				readfile( $file_name ); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_readfile
				unlink( $file_name );
				exit();

			} else {
				exit();
			}
		}

	}
}


if ( ! function_exists( 'handle_cp_export_all_list_action' ) ) {
	/**
	 * Function Name: handle_cp_export_all_list_action.
	 */
	function handle_cp_export_all_list_action() {

		if ( ! current_user_can( 'access_cp' ) ) {
			die( -1 );
		}

		check_admin_referer( 'export-all-list' );

		$list = $_POST['list_id'];
		$list = stripcslashes( $list );
		$list = json_decode( $list, true );

		$path        = plugin_dir_path( __FILE__ );
		$contact_arr = array();

		foreach ( $list as $key => $value ) {
			$temp_arr  = array();
			$list_name = $value['listName'];
			$mailer    = $value['mailer'];
			$contacts  = $value['contacts'];
			if ( ! empty( $contacts ) ) {
				foreach ( $contacts as $key1 => $value1 ) {
					$temp_arr['mailer']   = $mailer;
					$temp_arr['listName'] = $list_name;
					$user_id              = $value1['user_id'];
					$temp_arr['user_id']  = $value1['user_id'];
					$date                 = $value1['date'];
					$temp_arr['date']     = $value1['date'];
					$email                = $value1['email'];
					$temp_arr['email']    = $value1['email'];
					array_push( $contact_arr, $temp_arr );
				}
			}
		}

		if ( is_array( $contact_arr ) && $contact_arr ) {
			$export_data = cp_generate_csv( $contact_arr );
			$content     = $export_data;

			$file_name = $path . 'cp_export_all.csv';
			$file_url  = plugins_url( 'cp_export_all.csv', __FILE__ );
			$handle    = fopen( $file_name, 'w' ); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fopen
			fwrite( $handle, $content ); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fwrite
			fclose( $handle ); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fclose

				header( 'Pragma: public' );   // Required.
				header( 'Expires: 0' );   // No Cache.
				header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
				header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s', filemtime( $file_name ) ) . ' GMT' );
				header( 'Cache-Control: private', false );
				header( 'Content-Type: application/application/csv' );
				header( 'Content-Disposition: attachment; filename="' . basename( $file_name ) . '"' );
				header( 'Content-Transfer-Encoding: binary' );
				header( 'Content-Length: ' . filesize( $file_name ) );  // Provide file size.
				header( 'Connection: close' );
				readfile( $file_name ); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_readfile
				unlink( $file_name );
				exit();

		} else {
			exit();
		}
	}
}


if ( ! function_exists( 'cp_export_modal_action' ) ) {
	/**
	 * Name:cp_export_modal_action export modal style.
	 */
	function cp_export_modal_action() {

		if ( ! current_user_can( 'access_cp' ) ) {
			die( -1 );
		}

		if ( isset( $_REQUEST['cp_admin_page_nonce'] ) && ! wp_verify_nonce( $_REQUEST['cp_admin_page_nonce'], 'cp_admin_page' ) ) {
			wp_die( 'No direct script access allowed!' );
		}

		$data_style = isset( $_POST['style_id'] ) ? sanitize_text_field( $_POST['style_id'] ) : '';

				check_admin_referer( 'export-modal-' . $data_style );
		$path          = plugin_dir_path( __FILE__ );
		$prev_styles   = get_option( 'smile_modal_styles' );
		$variant_tests = get_option( 'modal_variant_tests' );
		$analytics_key = get_option( 'smile_style_analytics' );

		$analtics_current_data = array();
		if ( is_array( $prev_styles ) ) {
			foreach ( $prev_styles as $key1 => $value1 ) {
				if ( '' != $prev_styles[ $key1 ]['style_settings'] ) {
					$prev_styles_array = maybe_unserialize( $prev_styles[ $key1 ]['style_settings'] );
					if ( is_array( $prev_styles_array ) && ! empty( $prev_styles_array ) && ! empty( $analytics_key[ $data_style ] ) ) {
						$prev_styles_array['analytics']         = $analytics_key[ $data_style ];
						$prev_styles[ $key1 ]['style_settings'] = maybe_serialize( $prev_styles_array );
					}
				}
			}
				update_option( 'smile_modal_styles', $prev_styles );
		}

			$variant_style_settings = array();
			$variant_style_id       = array();

		if ( $variant_tests ) {
			if ( array_key_exists( $data_style, $variant_tests ) && ! empty( $variant_tests[ $data_style ] ) ) {

				foreach ( $variant_tests[ $data_style ] as $key => $variant ) {
					if ( empty( $variant_style_id ) && empty( $variant_style_settings ) ) {
						$variant_style_settings = maybe_unserialize( $variant['style_settings'] );
						$variant_style_id       = $variant_style_settings ['variant_style_id'];
					}
				}

				if ( '' != $variant_tests[ $data_style ][ $key ]['style_settings'] ) {

					$prev_styles_array = maybe_unserialize( $variant_tests[ $data_style ][ $key ]['style_settings'] );

					if ( is_array( $prev_styles_array ) && ! empty( $prev_styles_array ) && isset( $variant_style_settings ['variant_style_id'] ) && ! empty( $analytics_key[ $variant_style_settings ['variant_style_id'] ] ) ) {
						$prev_styles_array['analytics']                         = $analytics_key[ $variant_style_settings ['variant_style_id'] ];
						$variant_tests[ $data_style ][ $key ]['style_settings'] = maybe_serialize( $prev_styles_array );

					}
				}
					update_option( 'modal_variant_tests', $variant_tests );

			}
		}

		$data_style_name = '';

		if ( isset( $_POST['style_name'] ) ? sanitize_text_field( $_POST['style_name'] ) : '' ) {
			$data_style_name = strtolower( stripcslashes( $_POST['style_name'] ) );
			$data_style_name = str_replace( ' ', '_', $data_style_name );
		}

		$data_style_name = $data_style_name . '_' . $data_style;

		if ( '' !== $data_style ) {
			if ( is_array( $prev_styles ) && ! empty( $prev_styles ) ) {
				foreach ( $prev_styles as $key => $style ) {

					$has_variants = false;
					$style_name   = $style['style_name'];
					$style_id     = $style['style_id'];

					if ( $data_style == $style_id ) {

						if ( $variant_tests ) {
							if ( array_key_exists( $data_style, $variant_tests ) && ! empty( $variant_tests[ $data_style ] ) ) {
								$has_variants = true;
							}
						}

						$style_settings = maybe_unserialize( $style['style_settings'] );
						$exp_settings   = array();

						foreach ( $style_settings as $title => $value ) {
							if ( ! is_array( $value ) ) {
								$value                  = urldecode( $value );
								$exp_settings[ $title ] = htmlentities( stripslashes( utf8_encode( $value ) ), ENT_QUOTES, 'UTF-8' );

							} else {
								$val = array();
								foreach ( $value as $ex_title => $ex_val ) {
									$val[ $ex_title ] = $ex_val;
								}
								$exp_settings[ $title ] = str_replace( '"', '&quot;', $val );

							}
						}
						$export = $style;

						$export['style_settings'] = $exp_settings;

						$modal_image      = isset( $style_settings['modal_image'] ) ? $style_settings['modal_image'] : '';
						$close_image      = isset( $style_settings['close_img'] ) ? $style_settings['close_img'] : '';
						$bg_image         = isset( $style_settings['modal_bg_image'] ) ? $style_settings['modal_bg_image'] : '';
						$content_bg_image = isset( $style_settings['content_bg_image'] ) ? $style_settings['content_bg_image'] : '';
						$form_bg_image    = isset( $style_settings['form_bg_image'] ) ? $style_settings['form_bg_image'] : '';
						$overlay_bg_image = isset( $style_settings['overlay_bg_image'] ) ? $style_settings['overlay_bg_image'] : '';

						if ( $has_variants ) {
							foreach ( $variant_tests[ $data_style ] as $variant ) {
								$export['variants'][] = $variant;
							}
						}
					}
				}
			}

			$dir = 'modal_' . $data_style_name;

			if ( ! is_dir( $dir ) ) {
				mkdir( $dir, 0777 );
			}

			// Get images attached to the style through settings, copy them in export directory and store them in media array.
			$media = array();
			if ( '' !== $modal_image ) {
				if ( ( isset( $style_settings['modal_img_src'] ) && 'upload_img' === $style_settings['modal_img_src'] )
					|| ! isset( $style_settings['modal_img_src'] ) ) {

					$modal_image = str_replace( '%7C', '|', $modal_image );
					if ( false !== strpos( $modal_image, 'http' ) ) {
						$modal_image = explode( '|', $modal_image );
						$modal_image = $modal_image[0];
						$modal_image = urldecode( $modal_image );
					} else {
						$modal_image = explode( '|', $modal_image );
						$modal_image = wp_get_attachment_image_src( $modal_image[0], $modal_image[1] );
						$modal_image = $modal_image[0];
					}

					$modal_image_name = basename( $modal_image );
					copy( $modal_image, $dir . '/' . $modal_image_name );

					$media['modal_image'] = $dir . '/' . $modal_image_name;
				}
			}

			if ( '' !== $close_image ) {
				if ( ( isset( $style_settings['close_image_src'] ) && 'upload_img' === $style_settings['close_image_src'] )
				|| ! isset( $style_settings['close_image_src'] ) ) {

					$close_image = str_replace( '%7C', '|', $close_image );
					if ( false !== strpos( $close_image, 'http' ) ) {
						$close_image = explode( '|', $close_image );
						$close_image = $close_image[0];
						$close_image = urldecode( $close_image );
					} else {
						$close_image = explode( '|', $close_image );
						$close_image = wp_get_attachment_image_src( $close_image[0], $close_image[1] );
						$close_image = $close_image[0];
					}

					$close_image_name = basename( $close_image );
					if ( '' !== $close_image_name ) {
						copy( $close_image, $dir . '/' . $close_image_name );
						$media['close_image'] = $dir . '/' . $close_image_name;
					}
				}
			}

			if ( '' !== $bg_image ) {
				if ( ( isset( $style_settings['modal_bg_image_src'] ) && 'upload_img' === $style_settings['modal_bg_image_src'] )
					|| ! isset( $style_settings['modal_bg_image_src'] ) ) {

					$bg_image = str_replace( '%7C', '|', $bg_image );
					if ( false !== strpos( $bg_image, 'http' ) ) {
						$bg_image = explode( '|', $bg_image );
						$bg_image = $bg_image[0];
						$bg_image = urldecode( $bg_image );
					} else {
						$bg_image = explode( '|', $bg_image );
						$bg_image = wp_get_attachment_image_src( $bg_image[0], $bg_image[1] );
						$bg_image = $bg_image[0];
					}

					$bg_image_name = basename( $bg_image );
					copy( $bg_image, $dir . '/' . $bg_image_name );

					$media['modal_bg_image'] = $dir . '/' . $bg_image_name;
				}
			}

			if ( '' !== $content_bg_image ) {
				$content_bg_image = str_replace( '%7C', '|', $content_bg_image );
				if ( false !== strpos( $content_bg_image, 'http' ) ) {
					$content_bg_image = explode( '|', $content_bg_image );
					$content_bg_image = $content_bg_image[0];
					$content_bg_image = urldecode( $content_bg_image );
				} else {
					$content_bg_image = explode( '|', $content_bg_image );
					$content_bg_image = wp_get_attachment_image_src( $content_bg_image[0], $content_bg_image[1] );
					$content_bg_image = $content_bg_image[0];
				}

				$content_bg_image_name = basename( $content_bg_image );
				copy( $content_bg_image, $dir . '/' . $content_bg_image_name );

				$media['content_bg_image'] = $dir . '/' . $content_bg_image;
			}

			if ( '' !== $form_bg_image ) {
				$form_bg_image = str_replace( '%7C', '|', $form_bg_image );
				if ( false !== strpos( $form_bg_image, 'http' ) ) {
					$form_bg_image = explode( '|', $form_bg_image );
					$form_bg_image = $form_bg_image[0];
					$form_bg_image = urldecode( $form_bg_image );
				} else {
					$form_bg_image = explode( '|', $form_bg_image );
					$form_bg_image = wp_get_attachment_image_src( $form_bg_image[0], $form_bg_image[1] );
					$form_bg_image = $form_bg_image[0];
				}

				$form_bg_image_name = basename( $form_bg_image );
				copy( $form_bg_image, $dir . '/' . $form_bg_image_name );

				$media['form_bg_image'] = $dir . '/' . $form_bg_image;
			}

			if ( '' !== $overlay_bg_image ) {
				$overlay_bg_image = str_replace( '%7C', '|', $overlay_bg_image );
				if ( false !== strpos( $overlay_bg_image, 'http' ) ) {
					$overlay_bg_image = explode( '|', $overlay_bg_image );
					$overlay_bg_image = $overlay_bg_image[0];
					$overlay_bg_image = urldecode( $overlay_bg_image );
				} else {
					$overlay_bg_image = explode( '|', $overlay_bg_image );
					$overlay_bg_image = wp_get_attachment_image_src( $overlay_bg_image[0], $overlay_bg_image[1] );
					$overlay_bg_image = $overlay_bg_image[0];
				}

				$overlay_bg_image_name = basename( $overlay_bg_image );
				copy( $overlay_bg_image, $dir . '/' . $overlay_bg_image_name );

				$media['overlay_bg_image'] = $dir . '/' . $overlay_bg_image;
			}

			if ( ! empty( $media ) ) {
				$export['media'] = $media;
			}
			$export['module'] = 'modal';
			$export_data      = wp_json_encode( $export );
			$content          = $export_data;

			$file_name = $dir . '/modal_' . $data_style_name . '.txt';
			$file_url  = plugins_url( $dir . '/modal_' . $data_style_name . '.txt', __FILE__ );
			Convert_Plug_Filesystem::prefix_get_filesystem()->put_contents( $file_name, $content );

			$files       = glob( "{$dir}/*" );
			$export_file = $dir . '.zip';

			smile_create_file( $dir, $files, $export_file );
		}
	}
}


if ( ! function_exists( 'cp_export_infobar_action' ) ) {
	/**
	 * Export infobar.
	 */
	function cp_export_infobar_action() {

		if ( ! current_user_can( 'access_cp' ) ) {
			die( -1 );
		}

		if ( isset( $_REQUEST['cp_admin_page_nonce'] ) && ! wp_verify_nonce( $_REQUEST['cp_admin_page_nonce'], 'cp_admin_page' ) ) {
			wp_die( 'No direct script access allowed!' );
		}

		$data_style = isset( $_POST['style_id'] ) ? sanitize_text_field( $_POST['style_id'] ) : '';

		check_admin_referer( 'export-infobar-' . $data_style );

		$path          = plugin_dir_path( __FILE__ );
		$prev_styles   = get_option( 'smile_info_bar_styles' );
		$variant_tests = get_option( 'info_bar_variant_tests' );

		$analytics_key         = get_option( 'smile_style_analytics' );
		$analtics_current_data = array();
		if ( is_array( $prev_styles ) ) {
			foreach ( $prev_styles as $key1 => $value1 ) {
				if ( '' != $prev_styles[ $key1 ]['style_settings'] ) {
					$prev_styles_array = maybe_unserialize( $prev_styles[ $key1 ]['style_settings'] );
					if ( is_array( $prev_styles_array ) && ! empty( $prev_styles_array ) && ! empty( $analytics_key[ $data_style ] ) ) {
							$prev_styles_array['analytics']         = $analytics_key[ $data_style ];
							$prev_styles[ $key1 ]['style_settings'] = maybe_serialize( $prev_styles_array );
					}
				}
			}
				update_option( 'smile_info_bar_styles', $prev_styles );
		}

			$variant_style_settings = array();
			$variant_style_id       = array();

		if ( $variant_tests ) {
			if ( array_key_exists( $data_style, $variant_tests ) && ! empty( $variant_tests[ $data_style ] ) ) {

				foreach ( $variant_tests[ $data_style ] as $key => $variant ) {
					if ( empty( $variant_style_id ) && empty( $variant_style_settings ) ) {
						$variant_style_settings = maybe_unserialize( $variant['style_settings'] );
						$variant_style_id       = $variant_style_settings ['variant_style_id'];
					}
				}
				if ( '' != $variant_tests[ $data_style ][ $key ]['style_settings'] ) {
					$prev_styles_array = maybe_unserialize( $variant_tests[ $data_style ][ $key ]['style_settings'] );
					if ( is_array( $prev_styles_array ) && ! empty( $prev_styles_array ) && isset( $variant_style_settings ['variant_style_id'] ) && ! empty( $analytics_key[ $variant_style_settings ['variant_style_id'] ] ) ) {
						$prev_styles_array['analytics']                         = $analytics_key[ $variant_style_settings ['variant_style_id'] ];
						$variant_tests[ $data_style ][ $key ]['style_settings'] = maybe_serialize( $prev_styles_array );
					}
				}
					update_option( 'info_bar_variant_tests', $variant_tests );

			}
		}

		$data_style_name = '';
		if ( isset( $_POST['style_name'] ) ) {
			$data_style_name = strtolower( stripcslashes( sanitize_text_field( $_POST['style_name'] ) ) );
			$data_style_name = str_replace( ' ', '_', $data_style_name );
		}

		$data_style_name = $data_style_name . '_' . $data_style;

		if ( '' !== $data_style ) {
			if ( is_array( $prev_styles ) && ! empty( $prev_styles ) ) {
				foreach ( $prev_styles as $key => $style ) {

					$has_variants = false;
					$style_name   = $style['style_name'];
					$style_id     = $style['style_id'];

					if ( $data_style == $style_id ) {

						if ( $variant_tests ) {
							if ( array_key_exists( $data_style, $variant_tests ) && ! empty( $variant_tests[ $data_style ] ) ) {
								$has_variants = true;
							}
						}

						$style_settings = maybe_unserialize( $style['style_settings'] );
						$exp_settings   = array();
						foreach ( $style_settings as $title => $value ) {
							if ( ! is_array( $value ) ) {
								$value                  = urldecode( $value );
								$exp_settings[ $title ] = htmlentities( stripslashes( utf8_encode( $value ) ), ENT_QUOTES, 'UTF-8' );
							} else {
								$val = array();
								foreach ( $value as $ex_title => $ex_val ) {
									$val[ $ex_title ] = $ex_val;
								}
								$exp_settings[ $title ] = str_replace( '"', '&quot;', $val );
							}
						}
						$export                   = $style;
						$export['style_settings'] = $exp_settings;

						$info_bar_image = isset( $style_settings['info_bar_image'] ) ? $style_settings['info_bar_image'] : '';
						$close_image    = isset( $style_settings['close_img'] ) ? $style_settings['close_img'] : '';
						$bg_image       = isset( $style_settings['info_bar_bg_image'] ) ? $style_settings['info_bar_bg_image'] : '';

						if ( $has_variants ) {
							foreach ( $variant_tests[ $data_style ] as $variant ) {
								$export['variants'][] = $variant;
							}
						}
					}
				}
			}
			$dir = 'info_bar_' . $data_style_name;
			if ( ! is_dir( $dir ) ) {
				mkdir( $dir, 0777 );
			}

			// Get images attached to the style through settings, copy them in export directory and store them in media array.
			$media = array();
			if ( '' !== $info_bar_image ) {
				$info_bar_image = str_replace( '%7C', '|', $info_bar_image );
				if ( false !== strpos( $info_bar_image, 'http' ) ) {
					$info_bar_image = explode( '|', $info_bar_image );
					$info_bar_image = $info_bar_image[0];
					$info_bar_image = urldecode( $info_bar_image );
				} else {
					$info_bar_image = explode( '|', $info_bar_image );
					$info_bar_image = wp_get_attachment_image_src( $info_bar_image[0], $info_bar_image[1] );
					$info_bar_image = $info_bar_image[0];
				}

				$info_bar_image_name = basename( $info_bar_image );
				copy( $info_bar_image, $dir . '/' . $info_bar_image_name );

				$media['info_bar_image'] = $dir . '/' . $info_bar_image_name;

			}

			if ( '' !== $close_image ) {
				if ( ( isset( $style_settings['close_ib_image_src'] ) && 'upload_img' === $style_settings['close_ib_image_src'] )
					|| ! isset( $style_settings['close_ib_image_src'] ) ) {

					$close_image = str_replace( '%7C', '|', $close_image );
					if ( false !== strpos( $close_image, 'http' ) ) {
						$close_image = explode( '|', $close_image );
						$close_image = $close_image[0];
						$close_image = urldecode( $close_image );
					} else {
						$close_image = explode( '|', $close_image );
						$close_image = wp_get_attachment_image_src( $close_image[0], $close_image[1] );
						$close_image = $close_image[0];
					}

					$close_image_name = basename( $close_image );
					copy( $close_image, $dir . '/' . $close_image_name );

					$media['close_image'] = $dir . '/' . $close_image_name;
				}
			}

			if ( '' !== $bg_image ) {
				if ( ( isset( $style_settings['info_bar_bg_image_src'] ) && 'upload_img' === $style_settings['info_bar_bg_image_src'] )
				|| ! isset( $style_settings['info_bar_bg_image_src'] ) ) {

					$bg_image = str_replace( '%7C', '|', $bg_image );
					if ( false !== strpos( $bg_image, 'http' ) ) {
						$bg_image = explode( '|', $bg_image );
						$bg_image = $bg_image[0];
						$bg_image = urldecode( $bg_image );
					} else {
						$bg_image = explode( '|', $bg_image );
						$bg_image = wp_get_attachment_image_src( $bg_image[0], $bg_image[1] );
						$bg_image = $bg_image[0];
					}

					$bg_image_name = basename( $bg_image );
					copy( $bg_image, $dir . '/' . $bg_image_name );

					$media['info_bar_bg_image'] = $dir . '/' . $bg_image_name;
				}
			}

			if ( ! empty( $media ) ) {
				$export['media'] = $media;
			}

			$export['module'] = 'info_bar';

			$export_data = wp_json_encode( $export );

			$content = $export_data;

			$file_name = $dir . '/info_bar_' . $data_style_name . '.txt';
			$file_url  = plugins_url( $dir . '/info_bar_' . $data_style_name . '.txt', __FILE__ );
			Convert_Plug_Filesystem::prefix_get_filesystem()->put_contents( $file_name, $content );

			$files       = glob( "{$dir}/*" );
			$export_file = $dir . '.zip';

			smile_create_file( $dir, $files, $export_file );

		}
	}
}

if ( ! function_exists( 'cp_export_slidein_action' ) ) {
	/**
	 * Export slidein settings.
	 */
	function cp_export_slidein_action() {

		if ( ! current_user_can( 'access_cp' ) ) {
			die( -1 );
		}

		if ( isset( $_REQUEST['cp_admin_page_nonce'] ) && ! wp_verify_nonce( $_REQUEST['cp_admin_page_nonce'], 'cp_admin_page' ) ) {
			wp_die( 'No direct script access allowed!' );
		}

		$data_style = isset( $_POST['style_id'] ) ? sanitize_text_field( $_POST['style_id'] ) : '';

		check_admin_referer( 'export-slidein-' . $data_style );

		$path          = plugin_dir_path( __FILE__ );
		$prev_styles   = get_option( 'smile_slide_in_styles' );
		$variant_tests = get_option( 'slide_in_variant_tests' );

		$analytics_key = get_option( 'smile_style_analytics' );

		$analtics_current_data = array();
		if ( is_array( $prev_styles ) ) {
			foreach ( $prev_styles as $key1 => $value1 ) {
				if ( '' != $prev_styles[ $key1 ]['style_settings'] ) {
					$prev_styles_array = maybe_unserialize( $prev_styles[ $key1 ]['style_settings'] );
					if ( is_array( $prev_styles_array ) && ! empty( $prev_styles_array ) && ! empty( $analytics_key[ $data_style ] ) ) {
						$prev_styles_array['analytics']         = $analytics_key[ $data_style ];
						$prev_styles[ $key1 ]['style_settings'] = maybe_serialize( $prev_styles_array );
					}
				}
			}
				update_option( 'smile_slide_in_styles', $prev_styles );
		}

			$variant_style_settings = array();
			$variant_style_id       = array();

		if ( $variant_tests ) {
			if ( array_key_exists( $data_style, $variant_tests ) && ! empty( $variant_tests[ $data_style ] ) ) {

				foreach ( $variant_tests[ $data_style ] as $key => $variant ) {
					if ( is_array( $prev_styles_array ) && ! empty( $prev_styles_array ) && ! empty( $analytics_key[ $data_style ] ) ) {
							$variant_style_settings = maybe_unserialize( $variant['style_settings'] );
							$variant_style_id       = $variant_style_settings ['variant_style_id'];
					}
				}
				if ( '' != $variant_tests[ $data_style ][ $key ]['style_settings'] ) {

					$prev_styles_array = maybe_unserialize( $variant_tests[ $data_style ][ $key ]['style_settings'] );
					if ( is_array( $prev_styles_array ) && ! empty( $prev_styles_array ) && isset( $variant_style_settings ['variant_style_id'] ) && ! empty( $analytics_key[ $variant_style_settings ['variant_style_id'] ] ) ) {
						$prev_styles_array['analytics']                         = $analytics_key[ $variant_style_settings ['variant_style_id'] ];
						$variant_tests[ $data_style ][ $key ]['style_settings'] = maybe_serialize( $prev_styles_array );
					}
				}
					update_option( 'slide_in_variant_tests', $variant_tests );

			}
		}

		$data_style_name = '';
		if ( isset( $_POST['style_name'] ) ) {
			$data_style_name = strtolower( stripcslashes( sanitize_text_field( $_POST['style_name'] ) ) );
			$data_style_name = str_replace( ' ', '_', $data_style_name );
		}

		$data_style_name = $data_style_name . '_' . $data_style;

		if ( '' !== $data_style ) {
			if ( is_array( $prev_styles ) && ! empty( $prev_styles ) ) {
				foreach ( $prev_styles as $key => $style ) {

					$has_variants = false;
					$style_name   = $style['style_name'];
					$style_id     = $style['style_id'];

					if ( $data_style == $style_id ) {

						if ( $variant_tests ) {
							if ( array_key_exists( $data_style, $variant_tests ) && ! empty( $variant_tests[ $data_style ] ) ) {
								$has_variants = true;
							}
						}

						$style_settings = maybe_unserialize( $style['style_settings'] );
						$exp_settings   = array();
						foreach ( $style_settings as $title => $value ) {
							if ( ! is_array( $value ) ) {
								$value                  = urldecode( $value );
								$exp_settings[ $title ] = htmlentities( stripslashes( utf8_encode( $value ) ), ENT_QUOTES, 'UTF-8' );
							} else {
								$val = array();
								foreach ( $value as $ex_title => $ex_val ) {
									$val[ $ex_title ] = $ex_val;
								}
								$exp_settings[ $title ] = str_replace( '"', '&quot;', $val );
							}
						}
						$export                   = $style;
						$export['style_settings'] = $exp_settings;

						$slidein_image = isset( $style_settings['slidein_image'] ) ? $style_settings['slidein_image'] : '';
						$close_image   = isset( $style_settings['close_img'] ) ? $style_settings['close_img'] : '';
						$bg_image      = isset( $style_settings['slide_in_bg_image'] ) ? $style_settings['slide_in_bg_image'] : '';

						if ( $has_variants ) {
							foreach ( $variant_tests[ $data_style ] as $variant ) {
								$export['variants'][] = $variant;
							}
						}
					}
				}
			}
			$dir = 'slide_in_' . $data_style_name;
			if ( ! is_dir( $dir ) ) {
				mkdir( $dir, 0777 );
			}

			// Get images attached to the style through settings, copy them in export directory and store them in media array.
			$media = array();

			if ( '' !== $close_image ) {
				if ( ( isset( $style_settings['close_si_image_src'] ) && 'upload_img' === $style_settings['close_si_image_src'] )
					|| ! isset( $style_settings['close_si_image_src'] ) ) {

					$close_image = str_replace( '%7C', '|', $close_image );
					if ( false !== strpos( $close_image, 'http' ) ) {
						$close_image = explode( '|', $close_image );
						$close_image = $close_image[0];
						$close_image = urldecode( $close_image );
					} else {
						$close_image = explode( '|', $close_image );
						$close_image = wp_get_attachment_image_src( $close_image[0], $close_image[1] );
						$close_image = $close_image[0];
					}

					$close_image_name = basename( $close_image );
					copy( $close_image, $dir . '/' . $close_image_name );

					$media['close_image'] = $dir . '/' . $close_image_name;
				}
			}

			if ( '' !== $bg_image ) {
				if ( ( isset( $style_settings['slide_in_bg_image_src'] ) && 'upload_img' === $style_settings['slide_in_bg_image_src'] )
				|| ! isset( $style_settings['slide_in_bg_image_src'] ) ) {

					$bg_image = str_replace( '%7C', '|', $bg_image );
					if ( false !== strpos( $bg_image, 'http' ) ) {
						$bg_image = explode( '|', $bg_image );
						$bg_image = $bg_image[0];
						$bg_image = urldecode( $bg_image );
					} else {
						$bg_image = explode( '|', $bg_image );
						$bg_image = wp_get_attachment_image_src( $bg_image[0], $bg_image[1] );
						$bg_image = $bg_image[0];
					}

					$bg_image_name = basename( $bg_image );
					copy( $bg_image, $dir . '/' . $bg_image_name );

					$media['slide_in_bg_image'] = $dir . '/' . $bg_image_name;
				}
			}

			if ( '' !== $slidein_image ) {
				if ( ( isset( $style_settings['slidein_image_src'] ) && 'upload_img' === $style_settings['slidein_image_src'] )
					|| ! isset( $style_settings['slidein_image_src'] ) ) {

					$slidein_image = str_replace( '%7C', '|', $slidein_image );
					if ( false !== strpos( $slidein_image, 'http' ) ) {
						$slidein_image = explode( '|', $slidein_image );
						$slidein_image = $slidein_image[0];
						$slidein_image = urldecode( $slidein_image );
					} else {
						$slidein_image = explode( '|', $slidein_image );
						$slidein_image = wp_get_attachment_image_src( $slidein_image[0], $slidein_image[1] );
						$slidein_image = $slidein_image[0];
					}

					$slidein_image_name = basename( $slidein_image );
					copy( $slidein_image, $dir . '/' . $slidein_image_name );

					$media['slidein_image'] = $dir . '/' . $slidein_image_name;
				}
			}
			if ( ! empty( $media ) ) {
				$export['media'] = $media;
			}

			$export['module'] = 'slide_in';

			$export_data = wp_json_encode( $export );
			$content     = $export_data;

			$file_name = $dir . '/slide_in_' . $data_style_name . '.txt';
			Convert_Plug_Filesystem::prefix_get_filesystem()->put_contents( $file_name, $content );

			$files       = glob( "{$dir}/*" );
			$export_file = $dir . '.zip';

			smile_create_file( $dir, $files, $export_file );

		}
	}
}

if ( ! function_exists( 'smile_create_file' ) ) {
	/**
	 * Function Name: smile_create_file.
	 *
	 * @param  string $dir         string parameter.
	 * @param  string $files       string parameter.
	 * @param  string $export_file string parameter.
	 */
	function smile_create_file( $dir, $files, $export_file ) {

		if ( ! current_user_can( 'access_cp' ) ) {
			die( -1 );
		}

		$result = smile_create_export_zip( $files, $export_file, true );

		header( 'Pragma: public' );   // Required.
		header( 'Expires: 0' );       // No Cache.
		header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
		header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s', filemtime( $export_file ) ) . ' GMT' );
		header( 'Cache-Control: public' );
		header( 'Content-Type: application/octet-stream' );
		header( 'Content-Disposition: attachment; filename="' . basename( $export_file ) . '"' );
		header( 'Content-Transfer-Encoding: binary' );
		header( 'Content-Length: ' . filesize( $export_file ) );   // Provide file size.
		header( 'Connection: close' );
		flush();
		readfile( $export_file ); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_readfile

		// Remove exported directory and its content.
		foreach ( glob( "{$dir}/*" ) as $file ) {
			unlink( $file );
		}
		unlink( $export_file );
		rmdir( $dir );
		exit();
	}
}

if ( ! function_exists( 'smile_create_export_zip' ) ) {
	/**
	 * Function Name: smile_create_export_zip create zip file.
	 *
	 * @param  array   $files       array files.
	 * @param  string  $destination string parameter.
	 * @param  boolean $overwrite   true/false.
	 * @return boolean              true/false.
	 */
	function smile_create_export_zip( $files = array(), $destination = '', $overwrite = false ) {
		// if the zip file already exists and overwrite is false, return false.
		if ( file_exists( $destination ) && ! $overwrite ) {
			return false;
		}

		// Vars.
		$valid_files = array();
		// If files were passed in...
		if ( is_array( $files ) ) {
			// Cycle through each file.
			foreach ( $files as $file ) {
				// Make sure the file exists.
				if ( file_exists( $file ) ) {
					$valid_files[] = $file;
				}
			}
		}
		// If we have good files...
		if ( count( $valid_files ) ) {
			// Create the archive.
			$zip = new ZipArchive();
			if ( file_exists( $destination ) ) {
				$zip_create = $zip->open( $destination, ZipArchive::OVERWRITE );
			} else {
				$zip_create = $zip->open( $destination, ZipArchive::CREATE );
			}

			if ( true !== $zip_create ) {
				return false;
			}
			// Add the files.
			foreach ( $valid_files as $file ) {
				$zip->addFile( $file, $file );
			}

			// Close the zip -- done!
			$zip->close();

			// Check to make sure the file exists.
			return file_exists( $destination );
		} else {
			return false;
		}
	}
}

if ( ! function_exists( 'cp_trash_contact' ) ) {
	/**
	 * Function to accept ajax call for deleting contact from list.
	 */
	function cp_trash_contact() {

		if ( ! current_user_can( 'access_cp' ) ) {
			die( -1 );
		}

		check_admin_referer( 'cp-delete-contact', 'security_nonce' );

		$lists       = get_option( 'smile_lists' );
		$list_id     = sanitize_text_field( $_POST['list_id'] );
		$user_id     = sanitize_text_field( $_POST['user_id'] );
		$email_id    = sanitize_email( $_POST['email_id'] );
		$list        = $lists[ $list_id ];
		$list_name   = str_replace( ' ', '_', strtolower( trim( $list['list-name'] ) ) );
		$data_option = cp_generate_option( $list_id );
		$mailer      = sanitize_text_field( $_POST['mailer'] );

		if ( 'convert_plug' !== $mailer ) {
			$contacts_option = 'cp_' . $mailer . '_' . $list_name;
		} else {
			$contacts_option = 'cp_connects_' . $list_name;
		}

		$data = get_option( $contacts_option );

		$index = cp_check_in_array( strtolower( $email_id ), $data, 'email' );

		if ( false !== $index ) {
			unset( $data[ $index ] );
		}

		$status = update_option( $contacts_option, $data );

		if ( $status ) {
			wp_send_json(
				array(
					'status' => 'success',
				)
			);

		} else {
			wp_send_json(
				array(
					'status' => 'error',
				)
			);

		}
	}
}

if ( ! function_exists( 'custom_form_update_conversions' ) ) {
	/**
	 * Function Name: custom_form_update_conversions update style conversions.
	 */
	function custom_form_update_conversions() {

		// Verify nonce.
		if ( ! wp_verify_nonce( esc_attr( $_POST['security'] ), 'cp-impress-nonce' ) ) {
			wp_send_json_error();
		}

		$style_id = isset( $_POST['style_id'] ) ? sanitize_text_field( $_POST['style_id'] ) : '';
		global $cp_analytics_end_time;
		$user_role   = '';
		$condition   = true;
		$cp_settings = get_option( 'convert_plug_settings' );

		if ( is_array( $cp_settings ) ) {
			$banneduser = explode( ',', $cp_settings['cp-user-role'] );
		}

		if ( is_user_logged_in() ) {
			$current_user = new WP_User( wp_get_current_user() );
			$user_roles   = $current_user->roles;
			$user_role    = $user_roles[0];
		}

		if ( ! empty( $cp_settings ) ) {
			$condition = ! is_user_logged_in() || ( is_user_logged_in() && ( ! in_array( $user_role, $banneduser ) ) );
		} else {
			$condition = ! is_user_logged_in() || ( is_user_logged_in() && ( 'administrator' !== $user_role ) );
		}

		if ( $condition ) {

			// Save analytics data.
			$existing_data = get_option( 'smile_style_analytics' );

			$date = $cp_analytics_end_time;

			if ( ! is_array( $existing_data ) ) {
				// First conversion.
				$analytics_data = array(
					$style_id => array(
						$date => array(
							'impressions' => 0,
							'conversions' => 1,
						),
					),
				);

			} else {
				if ( isset( $existing_data[ $style_id ] ) ) {
					foreach ( $existing_data[ $style_id ] as $key => $value ) {
						if ( $key === $date ) {
							$old_impressions                     = $value['impressions'];
							$old_conversions                     = $value['conversions'];
							$existing_data[ $style_id ][ $date ] = array(
								'impressions' => $old_impressions,
								'conversions' => $old_conversions + 1,
							);
						}
					}
				} else {
					// First conversion for this particular style.
					$existing_data[ $style_id ] = array(
						$date => array(
							'impressions' => 0,
							'conversions' => 1,
						),
					);
				}

				$analytics_data = $existing_data;
			}

			update_option( 'smile_style_analytics', $analytics_data );
			echo 'custom conversion done';
		}

		die();
	}
}
