<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

if ( ! function_exists( 'cp_get_form_hidden_fields' ) ) {
	/**
	 * Function Name: cp_get_form_hidden_fields  Mailer - We will also optimize this by filter. If in any style we need the form then apply filter otherwise nope..
	 *
	 * @param  array $a settings array.
	 * @return mixed    content.
	 */
	function cp_get_form_hidden_fields( $a ) {
		$mailer            = explode( ':', $a['mailer'] );
		$on_success_action = '';
		$on_success        = '';
		$on_redirect       = '';
		$mailer_id         = '';
		$list_id           = '';
		$style_id          = isset( $a['style_id'] ) ? esc_attr( $a['style_id'] ) : '';

		if ( '' !== $a['mailer'] && 'custom-form' !== $a['mailer'] ) {
			$smile_lists = get_option( 'smile_lists' );

			$list   = ( isset( $smile_lists[ $a['mailer'] ] ) ) ? $smile_lists[ $a['mailer'] ] : '';
			$mailer = ( '' !== $list ) ? $list['list-provider'] : '';

			if ( 'Convert Plug' === $mailer ) {
				$mailer_id = 'cp';
				$list_id   = esc_attr( $a['mailer'] );
			} else {
				$mailer_id = strtolower( $mailer );
				$list_id   = ( '' !== $list ) ? $list['list'] : '';
			}

			$on_success = ( isset( $a['on_success'] ) ) ? stripslashes( $a['on_success'] ) : '';
			if ( isset( $on_success ) && 'redirect' === $on_success ) {
				$on_success_action = esc_url( $a['redirect_url'] );
				if ( isset( $a['on_redirect'] ) && '' !== $a['on_redirect'] ) {
					$on_redirect .= '<input type="hidden" name="redirect_to" value="' . esc_url( $a['on_redirect'] ) . '" />';
					if ( 'download' === $a['on_redirect'] && isset( $a['download_url'] ) && '' !== $a['download_url'] ) {
						$on_redirect .= '<input type="hidden" name="download_url" value="' . esc_url( $a['download_url'] ) . '" />';
					}
				}
			} elseif ( isset( $a['success_message'] ) ) {
				$on_success_action = do_shortcode( html_entity_decode( stripcslashes( htmlspecialchars( $a['success_message'], ENT_QUOTES, 'utf-8' ) ), ENT_QUOTES, 'utf-8' ) );
			}
		}
		ob_start();
		$uid = md5( uniqid( wp_rand(), true ) );

		global $wp;
		$current_url = home_url( add_query_arg( array(), $wp->request ) );
		$nonce       = wp_create_nonce( 'cp-submit-form-' . $style_id );
		$style_name  = isset( $a['new_style'] ) ? esc_attr( stripcslashes( htmlspecialchars( $a['new_style'], ENT_QUOTES, 'utf-8' ) ) ) : '';

		$user_role = isset( $a['cp_new_user_role'] ) ? $a['cp_new_user_role'] : 'None';

		$data          = get_option( 'convert_plug_settings' );
		$is_enable_pot = isset( $data['cp-disable-pot'] ) ? $data['cp-disable-pot'] : '1';
		?>
		<input type="hidden" id="<?php echo esc_attr( wp_rand() ); ?>_wpnonce" name="_wpnonce" value="<?php echo esc_attr( $nonce ); ?>">
		<input type="hidden" name="cp-page-url" value="<?php echo esc_url( $current_url ); ?>" />
		<input type="hidden" name="param[user_id]" value="cp-uid-<?php echo esc_attr( $uid ); ?>" />
		<input type="hidden" name="param[date]" value="<?php echo esc_attr( gmdate( 'j-n-Y' ) ); ?>" />
		<input type="hidden" name="list_parent_index" value="<?php echo isset( $a['mailer'] ) ? esc_attr( $a['mailer'] ) : ''; ?>" />
		<input type="hidden" name="action" value="<?php echo esc_attr( $mailer_id ); ?>_add_subscriber" />
		<input type="hidden" name="list_id" value="<?php echo esc_attr( $list_id ); ?>" />
		<input type="hidden" name="style_id" value="<?php echo esc_attr( $style_id ); ?>" />
		<input type="hidden" name="msg_wrong_email" value='<?php echo isset( $a['msg_wrong_email'] ) ? do_shortcode( html_entity_decode( stripcslashes( htmlspecialchars( $a['msg_wrong_email'], ENT_QUOTES, 'utf-8' ) ), ENT_QUOTES, 'utf-8' ) ) : ''; ?>' />
		<input type="hidden" name="<?php echo esc_attr( $on_success ); ?>" value="<?php echo esc_attr( $on_success_action ); ?>" />
		<input type="hidden" name="cp_module_name" value="<?php echo esc_attr( $style_name ); ?>" />
		<input type="hidden" name="cp_module_type" value="" />
		<?php if ( '1' === $is_enable_pot ) { ?>
		<input type="text" name="cp_set_hp" value="" style="display: none;"/>
			<?php
		}
		$html = ob_get_clean();
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

add_filter( 'cp_form_hidden_fields', 'cp_get_form_hidden_fields', 10, 1 );

add_filter( 'cp_valid_mx_email', 'cp_valid_mx_email_init' );

if ( ! function_exists( 'cp_valid_mx_email_init' ) ) {
	/**
	 * Function Name: cp_valid_mx_email_init Filter 'cp_valid_mx_email' for MX - Email validation.
	 *
	 * @param  [type] $email string parameter.
	 * @return [type]        string parameter.
	 * @since 1.0
	 */
	function cp_valid_mx_email_init( $email ) {
		// Proceed If global check box enabled for MX Record from @author tab.
		if ( apply_filters( 'cp_enabled_mx_record', $email ) ) {
			if ( cp_is_valid_mx_email( $email ) ) {
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}
}

if ( ! function_exists( 'cp_is_valid_mx_email' ) ) {
	/**
	 * Function Name: cp_is_valid_mx_email.
	 *
	 * @param  string $email  string parameter.
	 * @param  string $record string parameter.
	 * @return boolval(var)         true/false.
	 */
	function cp_is_valid_mx_email( $email, $record = 'MX' ) {
		list( $user, $domain ) = explode( '@', $email );
		return checkdnsrr( $domain, $record );
	}
}

add_filter( 'cp_enabled_mx_record', 'cp_enabled_mx_record_init' );
/**
 * Function Name: cp_enabled_mx_record_init Check MX record globally enabled or not [Setting found in @author tab].
 *
 * @return boolval(var)         true/false.
 */
function cp_enabled_mx_record_init() {
	$data                 = get_option( 'convert_plug_settings' );
	$is_enable_mx_records = isset( $data['cp-enable-mx-record'] ) ? $data['cp-enable-mx-record'] : 0;
	if ( $is_enable_mx_records ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Function Name: cp_is_style_visible Check if style is visible here or not.
 *
 * @param  array $settings  array parameter.
 * @return boolval(var)         true/false.
 * @since 2.1.0
 */
function cp_is_style_visible( $settings ) {

	global $post;
	$post_id = ( ! is_404() && ! is_search() && ! is_archive() && ! is_home() ) ? $post->ID : '';

	$category       = get_queried_object_id();
	$cat_ids        = wp_get_post_categories( $post_id );
	$post_type      = get_post_type( $post );
	$taxonomies     = get_post_taxonomies( $post );
	$term_cat_id    = '';
	$tag_arr        = array();
	$show_module    = true;
	$show_countries = true;
	$taxtterm_id    = get_the_tags(); // tags.
	$term_id        = '';

	if ( $taxtterm_id ) {
		foreach ( $taxtterm_id as $tag ) {
			array_push( $tag_arr, $tag->term_id );
		}
	}

	// Check if popup is visible for current device or not?
	$hide_on_devices = isset( $settings['hide_on_device'] ) ? apply_filters( 'smile_render_setting', $settings['hide_on_device'] ) : '';
	if ( '' !== $hide_on_devices ) {
		$show_module = cplus_is_current_device( $hide_on_devices );
	}

	// Check if Country is visible to view popup or not?
	$visible_geotarget = isset( $settings['enable_geotarget'] ) ? $settings['enable_geotarget'] : '1';
	$show_countries    = true;

	if ( '1' === $visible_geotarget ) {
		require_once CP_BASE_DIR . '/framework/class-cp-geolocation-target.php';

		$country_type            = isset( $settings['country_type'] ) ? $settings['country_type'] : 'basic-all-countries';
		$specific_country        = '';
		$hide_specific_countries = isset( $settings['hide_specific_countries'] ) ? apply_filters( 'smile_render_setting', $settings['hide_specific_countries'] ) : '';

		if ( 'specifics-geo' == $country_type ) {
			$specific_country        = isset( $settings['specific_countries'] ) ? apply_filters( 'smile_render_setting', $settings['specific_countries'] ) : '';
			$hide_specific_countries = '';
		}

		$show_countries = cplus_is_geo_location( $country_type, $specific_country, $hide_specific_countries );
	}

	if ( $show_module && $show_countries ) {
		$target_pages_keywords = array( 'post-', 'tax-', 'special-' );
		$global_display        = isset( $settings['global'] ) ? apply_filters( 'smile_render_setting', $settings['global'] ) : '';

		$exclude_from = isset( $settings['exclude_from'] ) ? apply_filters( 'smile_render_setting', $settings['exclude_from'] ) : '';

		if ( '' !== $exclude_from ) {
			$exclude_from = str_replace( $target_pages_keywords, '', $exclude_from );
			$exclude_from = ( '' !== $exclude_from ) ? explode( ',', $exclude_from ) : '';
		}

		$exclusive_on = isset( $settings['exclusive_on'] ) ? apply_filters( 'smile_render_setting', $settings['exclusive_on'] ) : '';
		if ( '' !== $exclusive_on ) {
			$exclusive_on = str_replace( $target_pages_keywords, '', $exclusive_on );
			$exclusive_on = ( '' !== $exclusive_on ) ? explode( ',', $exclusive_on ) : '';
		}

		// exclude post type.
		$exclude_cpt = isset( $settings['exclude_post_type'] ) ? apply_filters( 'smile_render_setting', $settings['exclude_post_type'] ) : '';
		if ( '' !== $exclude_cpt ) {
			$exclude_cpt = str_replace( $target_pages_keywords, '', $exclude_cpt );
			$exclude_cpt = ( '' !== $exclude_cpt ) ? explode( ',', $exclude_cpt ) : '';
		}

		// exclusive taxonomy.
		$exclusive_tax = isset( $settings['exclusive_post_type'] ) ? apply_filters( 'smile_render_setting', $settings['exclusive_post_type'] ) : '';
		if ( '' !== $exclusive_tax ) {
			$exclusive_tax = str_replace( $target_pages_keywords, '', $exclusive_tax );
			$exclusive_tax = ( '' !== $exclusive_tax ) ? explode( ',', $exclusive_tax ) : '';
		}

		if ( ! $global_display ) {
			if ( ! $settings['enable_custom_class'] ) {
				$settings['custom_class']        = 'priority_modal';
				$settings['enable_custom_class'] = true;
			} else {
				$settings['custom_class'] = $settings['custom_class'] . ',priority_modal';
			}
		}

		$show_for_logged_in = isset( $settings['show_for_logged_in'] ) ? $settings['show_for_logged_in'] : '';

		$all_users = isset( $settings['all_users'] ) ? $settings['all_users'] : '';

		if ( $all_users ) {
			$show_for_logged_in = 0;
		}

		if ( $global_display ) {
			$display = true;
			if ( is_404() ) {
				if ( is_array( $exclude_from ) && in_array( '404', $exclude_from ) ) {
					$display = false;
				}
			}
			if ( is_search() ) {
				if ( is_array( $exclude_from ) && in_array( 'search', $exclude_from ) ) {
					$display = false;
				}
			}
			if ( is_front_page() ) {
				if ( is_array( $exclude_from ) && in_array( 'front_page', $exclude_from ) ) {
					$display = false;
				}
			}
			if ( is_home() ) {
				if ( is_array( $exclude_from ) && in_array( 'blog', $exclude_from ) ) {
					$display = false;
				}
			}
			if ( is_author() ) {
				if ( is_array( $exclude_from ) && in_array( 'author', $exclude_from ) ) {
					$display = false;
				}
			}

			if ( is_archive() ) {
				$term_id = '';
				$obj     = get_queried_object();
				if ( isset( $obj->term_id ) ) {
					$term_id = $obj->term_id;
				}

				// check if this woocomerce archive page.
				if ( function_exists( 'is_woocommerce' ) && function_exists( 'is_shop' ) ) {
					if ( is_shop() ) {
						$term_id = wc_get_page_id( 'shop' );
					}
				}

				if ( is_array( $exclude_from ) && in_array( $term_id, $exclude_from ) ) {
					$display = false;
				} elseif ( is_array( $exclude_from ) && in_array( 'archive', $exclude_from ) ) {
					$display = false;
				}
			}

			if ( $post_id ) {
				if ( is_array( $exclude_from ) && in_array( $post_id, $exclude_from ) ) {
					$display = false;
				}
			}

			if ( ! empty( $cat_ids ) ) {
				foreach ( $cat_ids as $cat_id ) {
					$term = get_term_by( 'id', $cat_id, 'category' );
					if ( isset( $term->term_id ) ) {
						$term_cat_id = $term->term_id;
					}
					if ( is_array( $exclude_from ) && in_array( $term_cat_id, $exclude_from ) ) {
						$display = false;
					}
				}
			}
			// check for tag.
			if ( ! empty( $tag_arr ) ) {
				foreach ( $tag_arr as $tag_id ) {
					if ( is_array( $exclude_from ) && in_array( $tag_id, $exclude_from ) ) {
						$display = false;
					}
				}
			}

			if ( ! empty( $exclude_cpt ) && is_array( $exclude_cpt ) ) {
				foreach ( $exclude_cpt as $taxonomy ) {
					$taxonomy = str_replace( 'cp-', '', $taxonomy );
					if ( is_singular( $taxonomy ) ) {
						$display = false;
					}

					if ( 'category' === $taxonomy && is_category() ) {
						$display = false;
					}

					if ( 'post_tag' === $taxonomy && is_tag() ) {
						$display = false;
					}

					if ( is_tax( $taxonomy ) ) {
						$display = false;
					}

					if ( 'is_attachment' === $taxonomy && is_attachment() ) {
						$display = false;
					}
				}
			}

			global $wp_query;
			if ( $wp_query->is_page ) {
				$loop    = is_front_page() ? 'front' : 'page';
				$obj     = get_queried_object();
				$page_id = '';
				if ( is_object( $obj ) && 'page' === $loop && '' !== $obj && null !== $obj ) {
					$page_id = $obj->ID;
				}

				if ( is_array( $exclude_from ) && in_array( $page_id, $exclude_from ) ) {
					$display = false;
				}
			}
		} else {
			$display = false;
			if ( is_array( $exclusive_on ) && ! empty( $exclusive_on ) ) {
				foreach ( $exclusive_on as $page ) {
					if ( is_page( $page ) ) {

						$display = true;

					}
				}
			}

			if ( is_404() ) {
				if ( is_array( $exclusive_on ) && in_array( '404', $exclusive_on ) ) {
					$display = true;
				}
			}
			if ( is_search() ) {
				if ( is_array( $exclusive_on ) && in_array( 'search', $exclusive_on ) ) {
					$display = true;
				}
			}
			if ( is_front_page() ) {
				if ( is_array( $exclusive_on ) && in_array( 'front_page', $exclusive_on ) ) {
					$display = true;
				}
			}
			if ( is_home() ) {
				if ( is_array( $exclusive_on ) && in_array( 'blog', $exclusive_on ) ) {
					$display = true;
				}
			}
			if ( is_author() ) {
				if ( is_array( $exclusive_on ) && in_array( 'author', $exclusive_on ) ) {
					$display = true;
				}
			}
			if ( is_archive() ) {
				$obj     = get_queried_object();
				$term_id = '';
				if ( isset( $obj->term_id ) ) {
					$term_id = $obj->term_id;
				}

				// check if this woocomerce archive page.
				if ( function_exists( 'is_woocommerce' ) && function_exists( 'is_shop' ) ) {
					if ( is_shop() ) {
						$term_id = wc_get_page_id( 'shop' );
					}
				}
				if ( is_array( $exclusive_on ) && in_array( $term_id, $exclusive_on ) ) {
					$display = true;
				} elseif ( is_array( $exclusive_on ) && in_array( 'archive', $exclusive_on ) ) {
					$display = true;
				}
			}

			if ( $post_id ) {
				if ( is_array( $exclusive_on ) && in_array( $post_id, $exclusive_on ) ) {
					$display = true;
				}
			}

			if ( ! empty( $cat_ids ) ) {
				foreach ( $cat_ids as $cat_id ) {
					$term = get_term_by( 'id', $cat_id, 'category' );
					if ( isset( $term->term_id ) ) {
						$term_cat_id = $term->term_id;
					}
					if ( is_array( $exclusive_on ) && in_array( $term_cat_id, $exclusive_on ) ) {
						$display = true;
					}
				}
			}
			// check for tag.
			if ( ! empty( $tag_arr ) ) {
				foreach ( $tag_arr as $tag_id ) {
					if ( is_array( $exclusive_on ) && in_array( $tag_id, $exclusive_on ) ) {
						$display = true;
					}
				}
			}

			if ( ! empty( $exclusive_tax ) ) {

				foreach ( $exclusive_tax as $taxonomy ) {
					$taxonomy = str_replace( 'cp-', '', $taxonomy );

					if ( is_singular( $taxonomy ) ) {
						$display = true;
					}

					if ( 'category' === $taxonomy && is_category() ) {

						$display = true;
					}

					if ( 'post_tag' === $taxonomy && is_tag() ) {
						$display = true;
					}

					if ( is_tax( $taxonomy ) ) {
						$display = true;
					}

					if ( 'is_attachment' === $taxonomy && is_attachment() ) {
						$display = true;
					}
				}
			}
		}

		if ( ! $show_for_logged_in ) {
			$exc_flag              = false;
			$excl_visible_to_users = isset( $settings['excl_visible_to_users'] ) ? apply_filters( 'smile_render_setting', $settings['excl_visible_to_users'] ) : '';
			$exc_flag              = cp_check_user_role( $excl_visible_to_users );

			if ( is_user_logged_in() && ! $exc_flag ) {
				$display = false;
			}
		} else {

			$visible_to_users = isset( $settings['visible_to_users'] ) ? apply_filters( 'smile_render_setting', $settings['visible_to_users'] ) : '';

			$user_present = cp_check_user_role( $visible_to_users );
			if ( $user_present ) {
				$display = false;
			}
		}

		$style_id = $settings['style_id'];

		// Filter target page settings.
		$display = apply_filters( 'cp_target_page_settings', $display, $style_id );

		// Filter to check URL Settings.
		$display = apply_filters( 'cp_target_url_settings', $display, $style_id );

		return $display;
	} else {
		return false;
	}
}

/**
 * Function Name: cp_check_user_role description]
 *
 * @param  string $user_val string parameter.
 * @return boolval(var)         true/false.
 */
function cp_check_user_role( $user_val ) {
	$user_present = false;
	if ( $user_val ) {
		$user_role    = explode( '|', $user_val );
		$user_role    = array_map( 'strtolower', $user_role );
		$current_user = wp_get_current_user();
		$current_role = strtolower( $current_user->roles ? $current_user->roles[0] : false );
		if ( in_array( $current_role, $user_role ) ) {
			$user_present = true;
		}
	}
	return $user_present;
}

/**
 * Function Name: cp_display_style_inline  display style inline.
 *
 * @return string string parameter.
 * @since 2.1.0
 */
function cp_display_style_inline() {

	$before_content_string = '';
	$after_content_string  = '';

	$cp_modules = get_option( 'convert_plug_modules' );

	if ( is_array( $cp_modules ) ) {

		foreach ( $cp_modules as $module ) {

			$module       = strtolower( str_replace( '_Popup', '', $module ) );
			$style_arrays = cp_get_live_styles( $module );

			if ( is_array( $style_arrays ) ) {

				foreach ( $style_arrays as $key => $style_array ) {

					$display          = false;
					$display_inline   = false;
					$settings_encoded = '';
					$style_settings   = array();
					$settings_array   = maybe_unserialize( $style_array['style_settings'] );
					foreach ( $settings_array as $key => $setting ) {
						$style_settings[ $key ] = apply_filters( 'smile_render_setting', $setting );
					}

					$style_id    = $style_array['style_id'];
					$modal_style = $style_settings['style'];

					if ( is_array( $style_settings ) && ! empty( $style_settings ) ) {
						$settings = maybe_unserialize( $style_array['style_settings'] );

						if ( isset( $settings['enable_display_inline'] ) && '1' === $settings['enable_display_inline'] ) {
							$display_inline  = true;
							$inline_position = $settings['inline_position'];
						}

						$css              = isset( $settings['custom_css'] ) ? urldecode( $settings['custom_css'] ) : '';
						$display          = cp_is_style_visible( $settings );
						$settings         = wp_json_encode( $settings );
						$settings_encoded = base64_encode( $settings );
					}

					if ( $display && $display_inline ) {

						ob_start();

						echo do_shortcode( '[smile_' . $module . ' display="inline" style_id = ' . $style_id . ' style="' . $modal_style . '" settings_encoded="' . $settings_encoded . ' "][/smile_' . $module . ']' );
						apply_filters( 'cp_custom_css', $style_id, $css );

						switch ( $inline_position ) {
							case 'before_post':
								$before_content_string .= ob_get_contents();
								break;
							case 'after_post':
								$after_content_string .= ob_get_contents();
								break;
							case 'both':
								$after_content_string  .= ob_get_contents();
								$before_content_string .= ob_get_contents();
								break;
						}

						ob_end_clean();
					}
				}
			}
		}
	}

	$output_string = array( $before_content_string, $after_content_string );
	return $output_string;
}

/**
 * Function Name: cp_get_live_styles Get live styles list for particular module.
 *
 * @param  string $module string modulename.
 * @return array         array apraemter.
 * @since 2.1.0
 */
function cp_get_live_styles( $module ) {

	$styles              = get_option( 'smile_' . $module . '_styles' );
	$style_variant_tests = get_option( $module . '_variant_tests' );
	$debug_data          = get_option( 'convert_plug_debug' );
	$debug_mode          = isset( $debug_data['cp-dev-mode'] ) ? $debug_data['cp-dev-mode'] : '0';
	$animation_type      = array( 'sign', 'rotate', 'fade', 'slide', 'sidefall', 'flash', 'hinge', 'jello', 'lightspeed', 'newspaper', 'pulse', 'roll', 'rubberband', 'shake', 'fall', 'swing', 'tada', 'wobble', 'zoom', 'slit', 'flip', 'bounce', 'superscaled' ); // Animation Types.
	$live_array          = array();
	if ( ! empty( $styles ) ) {
		foreach ( $styles as $key => $style ) {
			$settings = maybe_unserialize( $style['style_settings'] );

			$split_tests = isset( $style_variant_tests[ $style['style_id'] ] ) ? $style_variant_tests[ $style['style_id'] ] : '';
			if ( is_array( $split_tests ) && ! empty( $split_tests ) ) {
				$split_array = array();
				$live        = isset( $settings['live'] ) ? (int) $settings['live'] : false;
				if ( $live ) {
					array_push( $split_array, $styles[ $key ] );
				}
				foreach ( $split_tests as $key => $test ) {
					$settings = maybe_unserialize( $test['style_settings'] );
					$live     = isset( $settings['live'] ) ? (int) $settings['live'] : false;
					if ( $live ) {
						array_push( $split_array, $test );
					}
				}
				if ( ! empty( $split_array ) ) {
					$key   = array_rand( $split_array, 1 );
					$array = $split_array[ $key ];
					array_push( $live_array, $array );
				}
			} else {
				$live = isset( $settings['live'] ) ? (int) $settings['live'] : false;
				if ( $live ) {
					$overlay_animation = '';
					$exit_animation    = isset( $settings['exit_animation'] ) ? $settings['exit_animation'] : '';
					if ( isset( $settings['entry_animation'] ) ) {
						$overlay_animation = $settings['entry_animation'];
					} elseif ( isset( $settings['overlay_effect'] ) ) {
						$overlay_animation = $settings['overlay_effect'];
					}
					$button_animation = isset( $settings['button_animation'] ) ? $settings['button_animation'] : '';
					enqueue_animation_styles( $overlay_animation, $exit_animation, $button_animation, $debug_mode, $animation_type ); // Enqueue Animation for Live module.

					array_push( $live_array, $styles[ $key ] );
				}
			}
		}
	}

	return $live_array;
}

/**
 * Function Name: enqueue_animation_styles Enqueue Animation for particular module.
 *
 * @param  string $overlay_animation Overlay Animation.
 * @param  string $exit_animation Exit Animation.
 * @param string $button_animation Button Animation.
 * @param string $debug_mode Developer mode.
 * @param array  $animation_type Array of animation types.
 * @since 3.5.23
 */
function enqueue_animation_styles( $overlay_animation, $exit_animation, $button_animation, $debug_mode, $animation_type ) {

	$overlay = strtolower( $overlay_animation );
	$exit    = strtolower( $exit_animation );
	$button  = strtolower( $button_animation );
	foreach ( $animation_type as $key => $file_name ) {
		if ( strpos( $overlay, $file_name ) || strpos( $exit, $file_name ) || strpos( $button, $file_name ) ) {
			if ( '1' === $debug_mode ) {
				wp_enqueue_style( 'cp-animate-' . $animation_type[ $key ], CP_PLUGIN_URL . 'modules/assets/css/unminified-animation/' . $animation_type[ $key ] . '.css', array(), CP_VERSION );
			} else {
				wp_enqueue_style( 'cp-animate-' . $animation_type[ $key ], CP_PLUGIN_URL . 'modules/assets/css/minified-animation/' . $animation_type[ $key ] . '.min.css', array(), CP_VERSION );
			}
		}
	}
}

/**
 * Function Name: cp_enqueue_social_media Enqueue Social Media Files.
 *
 * @param  string $module string module name.
 * @param  array  $live_styles array for getting live modules.
 * @param  string $cp_dev_mode string developer mode.
 * @since 3.5.23
 */
function cp_enqueue_social_media( $module, $live_styles, $cp_dev_mode = '0' ) {
	foreach ( $live_styles as $style ) {
		if ( preg_match( '/cp_social_icon/', $style['style_settings'] ) ) {
			if ( '1' === $cp_dev_mode ) {
				// Register Social Media Template styles.
				wp_enqueue_style( 'convert-plus-social-media-style', CP_PLUGIN_URL . 'modules/assets/css/cp-social-media-style.css', array(), CP_VERSION );
				wp_enqueue_style( 'convert-plus-social-icon-style', CP_PLUGIN_URL . 'modules/assets/css/social-icon-css.css', array(), CP_VERSION );
				wp_enqueue_style( 'convert-plus-social-icon-dev-style', CP_PLUGIN_URL . 'modules/assets/css/social-icon.css', array(), CP_VERSION );
				break;
			} else {
				wp_enqueue_style( 'convert-plus-social-style' );
				break;
			}
		}
	}
}

if ( ! function_exists( 'cp_notify_error_to_admin' ) ) {
	/**
	 * Function Name: cp_notify_error_to_admin Notify form submission errors to admin
	 *
	 * @param  string $page_url string parameter.
	 * @since 2.3.0
	 */
	function cp_notify_error_to_admin( $page_url ) {

		$data               = get_option( 'convert_plug_settings' );
		$cp_change_ntf_id   = isset( $data['cp_change_ntf_id'] ) ? $data['cp_change_ntf_id'] : 1;
		$cp_notify_email_to = isset( $data['cp_notify_email_to'] ) ? $data['cp_notify_email_to'] : get_option( 'admin_email' );

		if ( 1 == $cp_change_ntf_id || '1' == $cp_change_ntf_id ) {
			$email_name = array();
			$email_name = explode( ',', $cp_notify_email_to );
			$to_arr     = array();
			foreach ( $email_name as $key => $email ) {
				$to = sanitize_email( $email );
				array_push( $to_arr, $to );
			}

			// prepare content for email.
			$subject = 'Issue with the ' . CP_PLUS_NAME . ' configuration';

			$body = 'Hello there, <p>There appears to be an issue with the ' . CP_PLUS_NAME . ' configuration on your website. Someone tried to fill out ' . CP_PLUS_NAME . ' form on ' . esc_url( $page_url ) . " and regretfully, it didn't go through.</p>";

			$body .= 'Please try filling out the form yourself or read more why this could happen here.';

			$body .= '<br>---<p>This e-mail was sent from ' . CP_PLUS_NAME . ' on ' . get_bloginfo( 'name' ) . ' (' . site_url() . ')</p>';

			// get admin email.
			$admin_notifi_time = get_option( 'cp_notified_admin_time' );

			if ( ! $admin_notifi_time ) {
				cp_send_mail( $to_arr, $subject, $body );
				update_option( 'cp_notified_admin_time', gmdate( 'Y-m-d H:i:s' ) );
			} else {
				// getting previously saved notification time.
				$saved_timestamp = strtotime( $admin_notifi_time );

				// getting current date.
				$c_date = strtotime( gmdate( 'Y-m-d H:i:s' ) );

				// Getting the value of current date - 24 hours.
				$old_date = $c_date - 86400; // 86400 seconds in 24 hrs.

				// if last email was sent time is greater than 24 hours, sent one more notification email.
				if ( $old_date > $saved_timestamp ) {
					cp_send_mail( $to, $subject, $body );
					update_option( 'cp_notified_admin_time', gmdate( 'Y-m-d H:i:s' ) );
				}
			}
		}
	}
}

if ( ! function_exists( 'cp_send_mail' ) ) {
	/**
	 * Function Name: cp_send_mail Sends an email.
	 *
	 * @param  string $to      string parameter.
	 * @param  string $subject string parameter.
	 * @param  string $body    string parameter.
	 * @return string          string parameter.
	 * @since 2.3.0
	 */
	function cp_send_mail( $to, $subject, $body ) {

		// set headers for email.
		$headers = array( 'Content-Type: text/html; charset=UTF-8' );

		if ( wp_mail( $to, $subject, $body, $headers ) ) {
			$msg = 'success';
		} else {
			$msg = 'error';
		}
		return $msg;
	}
}

/**
 * Function Name: cp_generate_scheduled_info description]
 *
 * @param  string $style_settings string parameter.
 * @return string                 string parameter.
 */
function cp_generate_scheduled_info( $style_settings ) {

	$schedule_data = maybe_unserialize( $style_settings );
	$title         = '';

	if ( isset( $schedule_data['schedule'] ) ) {
		$scheduled_array = $schedule_data['schedule'];
		if ( is_array( $scheduled_array ) ) {
			$startdate = gmdate( 'j M Y ', strtotime( $scheduled_array['start'] ) );
			$enddate   = gmdate( 'j M Y ', strtotime( $scheduled_array['end'] ) );
			$first     = gmdate( 'j-M-Y (h:i A)', strtotime( $scheduled_array['start'] ) );
			$second    = gmdate( 'j-M-Y (h:i A)', strtotime( $scheduled_array['end'] ) );
			$title     = 'Scheduled From ' . $first . ' To ' . $second;
		}
	}

	$status = '<span class="change-status"><span data-live="2" class="cp-status"><i class="connects-icon-clock"></i><span class="scheduled-info" title="' . $title . '">' . __( 'Scheduled', 'smile' ) . '</span></span>';

	return $status;
}

if ( ! function_exists( 'cp_get_live_preview_settings' ) ) {
	/**
	 * Function Name: cp_get_live_preview_settings.
	 *
	 * @param  string $module          string parameter.
	 * @param  string $settings_method string parameter.
	 * @param  string $style_options   string parameter.
	 * @param  string $template_name   string parameter.
	 * @return array                  array parameter
	 */
	function cp_get_live_preview_settings( $module, $settings_method, $style_options, $template_name ) {

		$settings = array();
		if ( 'internal' === $settings_method ) {

			foreach ( $style_options as $key => $value ) {
				$settings[ $value['name'] ] = $value['opts']['value'];
			}

			$settings['affiliate_setting'] = false;
			$settings['style']             = 'preview';
			$settings_encoded              = base64_encode( wp_json_encode( $settings ) );

		} else {

			$settings = get_option( 'cp_' . $module . '_' . $template_name, '' );

			if ( is_array( $settings ) ) {

				$settings = get_option( 'cp_' . $module . '_' . $template_name, '' );

				$style_setting_arr          = $settings['style_settings'];
				$style_setting_arr['style'] = 'preview';

			} else {
				$demo_dir = CP_BASE_DIR . 'modules/' . $module . '/presets/' . $template_name . '.txt';

				$handle = fopen( $demo_dir, 'r' ); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fopen

				$settings = fread( $handle, filesize( $demo_dir ) ); //phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fread

				$settings = json_decode( $settings, true );

				$style_setting_arr = $settings['style_settings'];

				$style_setting_arr['style'] = 'preview';
			}

			$style_setting_arr['cp_image_link_url'] = 'external';

			$import_style = array();
			foreach ( $style_setting_arr as $title => $value ) {
				if ( ! is_array( $value ) ) {
					$value                  = htmlspecialchars_decode( $value );
					$import_style[ $title ] = $value;
				} else {
					foreach ( $value as $ex_title => $ex_val ) {
						$val[ $ex_title ] = htmlspecialchars_decode( $ex_val );
					}
					$import_style[ $title ] = $val;
				}
			}

			$settings_encoded = base64_encode( wp_json_encode( $import_style ) );
		}

		return $settings_encoded;

	}
}


if ( ! function_exists( 'cp_is_connected' ) ) {
	/**
	 * Function Name: cp_is_connected.
	 *
	 * @return boolval(var)         true/false.
	 */
	function cp_is_connected() {

		$is_conn  = false;
		$response = wp_remote_get( 'http://downloads.brainstormforce.com' );

		$response_code = wp_remote_retrieve_response_code( $response );

		if ( 200 === $response_code ) {
			$is_conn = true; // action when connected.
		} else {
			$is_conn = false; // action in connection failure.
		}

		return $is_conn;
	}
}

if ( ! function_exists( 'cp_get_edit_link' ) ) {
	/**
	 * Function Name: cp_get_edit_link .
	 *
	 * @param  string $style_id string parameter.
	 * @param  string $module   string parameter.
	 * @param  string $theme    string parameter.
	 * @return string           string parameter.
	 */
	function cp_get_edit_link( $style_id, $module, $theme ) {

		$url = '';

		$data  = get_option( 'convert_plug_settings' );
		$esval = isset( $data['cp-edit-style-link'] ) ? $data['cp-edit-style-link'] : 0;

		if ( $esval ) {

			// get module styles.
			$styles = get_option( 'smile_' . $module . '_styles' );

			// get variant style for module.
			$variant_styles = get_option( $module . '_variant_tests' );

			$parent_style     = false;
			$variant_style    = false;
			$variant_style_id = '';

			if ( is_array( $styles ) ) {
				foreach ( $styles as $style ) {

					// check if it is parent style.
					if ( $style['style_id'] === $style_id ) {
						$parent_style = true;
						break;
					}

					if ( is_array( $variant_styles ) ) {
						if ( isset( $variant_styles[ $style['style_id'] ] ) ) {
							foreach ( $variant_styles[ $style['style_id'] ] as $child_style ) {

								// check if it is child/ variant style.
								if ( $child_style['style_id'] === $style_id ) {
									$variant_style    = true;
									$variant_style_id = $style['style_id'];
									break;
								}
							}
						}
					}
				}
			}

			if ( $parent_style ) {
				$baseurl = 'admin.php?page=smile-' . $module . '-designer&style-view=edit&style=' . $style_id . '&theme=' . $theme;
				$url     = admin_url( $baseurl );
			} else {
				$baseurl = 'admin.php?page=smile-' . $module . '-designer&style-view=variant&variant-test=edit&variant-style=' . $style_id . '&style=' . $theme . '&parent-style=' . $theme . '&style_id=' . $variant_style_id . '&theme=' . $theme;
				$url     = admin_url( $baseurl );
			}
		}

		return $url;

	}
}

if ( ! function_exists( 'cp_notify_sub_to_admin' ) ) {
	/**
	 * Function Name: cp_notify_sub_to_admin Notify subscription to admin.
	 *
	 * @param  string $list_name       string parameter.
	 * @param  string $subscriber_data string parameter.
	 * @param  string $sub_email       string parameter.
	 * @param  string $email_sub       string parameter.
	 * @param  string $email_body      string parameter.
	 * @param  string $cp_page_url     string parameter.
	 * @param  string $style_name      string parameter.
	 * @since 2.3.0
	 */
	function cp_notify_sub_to_admin( $list_name, $subscriber_data, $sub_email, $email_sub, $email_body, $cp_page_url, $style_name ) {
		$email_name   = array();
		$email_name   = explode( ',', $sub_email );
		$to_arr       = array();
		$body_content = '';
		$content      = '';
		// prepare content for email.
		$subject = 'Congratulations! You have a New Subscriber!';
		$body    = '<p>You’ve got a new subscriber to the Campaign: ' . $list_name . '</p>';
		$body   .= '<p>Here is the information :</p>';
		$subject = isset( $email_sub ) ? $email_sub : $subject;

		foreach ( $subscriber_data as $key => $value ) {
			if ( 'user_id' !== $key ) {
				$body_content .= ucfirst( $key ) . ' : ' . $value . '<br>';
			}
		}

		$body .= $body_content;
		$body .= '<p>Congratulations! Wish you many more.<br>This e-mail was sent from Convert Plus module of ' . $style_name . ' on ' . get_bloginfo( 'name' ) . ' (' . esc_url( site_url() ) . ')</p>';

		$current_url = esc_url( $cp_page_url );
		$content     = str_replace( '{{style_name}}', $style_name, $email_body );
		$content     = str_replace( '{{list_name}}', $list_name, $content );
		$content     = str_replace( '{{content}}', $body_content, $content );
		$content     = str_replace( '{{blog_name}}', get_bloginfo( 'name' ), $content );
		$content     = str_replace( '{{site_url}}', esc_url( site_url() ), $content );
		$content     = str_replace( '{{page_url}}', esc_url( $current_url ), $content );
		$content     = str_replace( '{{CP_PLUS_NAME}}', CP_PLUS_NAME, $content );
		$body        = isset( $email_body ) ? do_shortcode( html_entity_decode( stripcslashes( htmlspecialchars( $content, ENT_QUOTES, 'utf-8' ) ), ENT_QUOTES, 'utf-8' ) ) : $body;

		foreach ( $email_name as $key => $email ) {
			$to = sanitize_email( $email );
			array_push( $to_arr, $to );
		}
		// get subscriber email.
		cp_send_mail( $to_arr, $subject, $body );
	}
}

/**
 * Function Name: get_style_details.
 *
 * @param  string $style_id string parameter.
 * @param  string $module   string parameter.
 * @return array           array parameter.
 */
function get_style_details( $style_id, $module ) {

	$style_type          = '';
	$parent              = '';
	$styles              = get_option( 'smile_' . $module . '_styles' );
	$smile_variant_tests = get_option( $module . '_variant_tests' );

	if ( is_array( $styles ) ) {
		foreach ( $styles as $key => $style ) {
			if ( $style['style_id'] === $style_id ) {
				// main style.
				$style_type = 'main';
			}
		}
	}

	if ( '' === $style_type ) {
		if ( is_array( $smile_variant_tests ) ) {
			foreach ( $smile_variant_tests as $key => $value ) {

				if ( is_array( $value ) && ! empty( $value ) ) {
					foreach ( $value as $variant ) {
						if ( isset( $variant['style_id'] ) ) {
							if ( $variant['style_id'] === $style_id ) {
								// variant style.
								$style_type = 'variant';
								$parent     = $key;
							}
						}
					}
				}
			}
		}
	}

	$style_details = array(
		'type'         => $style_type,
		'parent_style' => $parent,
	);

	return $style_details;

}

/**
 * Function Name: cp_sanitize_array Sanitize all values from an array.
 *
 * @param  array $array array parameter.
 * @return array         array parameter.
 * @since 2.3.2.1
 */
function cp_sanitize_array( &$array ) {

	if ( is_array( $array ) ) {
		foreach ( $array as &$value ) {

			if ( ! is_array( $value ) ) {

				// sanitize if value is not an array.
				$value = sanitize_text_field( $value );

			} else {
				// go inside this function again.
				cp_sanitize_array( $value );
			}
		}
	}
	return $array;
}

if ( ! function_exists( 'cp_get_protocol_settings_init' ) ) {
	/**
	 * Function Name: cp_get_protocol_settings_init Get sites protocol
	 *
	 * @param  string $img string parameter.
	 * @return string      string parameter.
	 * @since 2.3.3.1
	 */
	function cp_get_protocol_settings_init( $img ) {
		$protocol    = 'http://';
		$replace_img = $img;

		if ( isset( $_SERVER['HTTPS'] ) ) {
			$protocol = ( $_SERVER['HTTPS'] && 'off' !== $_SERVER['HTTPS'] ) ? 'https://' : 'http://';
		}

		if ( 'https://' === $protocol ) {
			$replace_img = str_replace( 'http://', 'https://', $img );
		}

		return $replace_img;
	}
}
add_filter( 'cp_get_protocol_settings', 'cp_get_protocol_settings_init' );

if ( ! function_exists( 'generate_box_shadow' ) ) {
	/**
	 * Function Name: generate_box_shadow.
	 *
	 * @param  string $string string parameter.
	 * @return string         string parameter.
	 */
	function generate_box_shadow( $string ) {
		$pairs  = explode( '|', $string );
		$result = array();
		foreach ( $pairs as $pair ) {
			$pair               = explode( ':', $pair );
			$result[ $pair[0] ] = $pair[1];
		}

		$res = '';
		if ( isset( $result['type'] ) && 'outset' !== $result['type'] ) {
			$res .= $result['type'] . ' ';
		}

		$res .= $result['horizontal'] . 'px ';
		$res .= $result['vertical'] . 'px ';
		$res .= $result['blur'] . 'px ';
		$res .= $result['spread'] . 'px ';
		$res .= $result['color'];

		$style  = 'box-shadow:' . $res . ';';
		$style .= '-webkit-box-shadow:' . $res . ';';
		$style .= '-moz-box-shadow:' . $res . ';';

		if ( 'none' === $result['type'] ) {
			$style = '';
		}

		return $style;
	}
}

if ( ! function_exists( 'cp_enqueue_google_fonts' ) ) {
	/**
	 * Function Name: cp_enqueue_google_fonts.
	 *
	 * @param  string $fonts string parameter.
	 */
	function cp_enqueue_google_fonts( $fonts = '' ) {

		$pairs  = '';
		$gfonts = '';
		$ar     = '';

		$basic_fonts = array(
			'Arial',
			'Arial Black',
			'Comic Sans MS',
			'Courier New',
			'Georgia',
			'Impact',
			'Lucida Sans Unicode',
			'Palatino Linotype',
			'Tahoma',
			'Times New Roman',
			'Trebuchet MS',
			'Verdana',
		);

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

		$allfonts = array_merge( $default_google_fonts, $basic_fonts );

		if ( false !== strpos( $fonts, ',' ) ) {
			$pairs = explode( ',', $fonts );
		}

		// Extract selected - Google Fonts.
		if ( ! empty( $pairs ) ) {
			foreach ( $pairs as $key => $value ) {
				if ( isset( $value ) && ! empty( $value ) ) {
					if ( ! in_array( $value, $basic_fonts ) ) {
						$gfonts .= str_replace( ' ', '+', $value ) . '|';
					}
				}
			}

			$gfonts .= implode( '|', $default_google_fonts );

		} else {
			$gfonts = implode( '|', $default_google_fonts );
		}

		// Check the google fonts is enabled from BackEnd.
		$data         = get_option( 'convert_plug_settings' );
		$is_gf_enable = isset( $data['cp-google-fonts'] ) ? $data['cp-google-fonts'] : 1;

		// Register & Enqueue selected - Google Fonts.
		if ( ! empty( $gfonts ) && $is_gf_enable ) {
			$media = '"all"';
			echo "<link rel='stylesheet' type='text/css' id='cp-google-fonts' href='https://fonts.googleapis.com/css?family=" . esc_attr( esc_url( $gfonts ) ) . "' media='none' onload = 'if(media!=" . esc_attr( $media ) . ')media=' . esc_attr( $media ) . "'>"; //phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedStylesheet

		}
	}
}

if ( ! function_exists( 'cp_is_not_empty' ) ) {
	/**
	 * Function Name: cp_is_not_empty Check values are empty or not.
	 *
	 * @param  sring $vl sring parameter.
	 * @return boolval(var)         true/false.
	 * @since 0.1.5
	 */
	function cp_is_not_empty( $vl ) {
		if ( isset( $vl ) && '' !== $vl ) {
			return true;
		} else {
			return false;
		}
	}
}

if ( ! function_exists( 'cp_add_css' ) ) {
	/**
	 * Function Name: cp_add_css Generate CSS from dev input.
	 *
	 * @param  string $prop   string parameter.
	 * @param  string $val    string parameter.
	 * @param  string $suffix string parameter.
	 * @return string         string parameter.
	 */
	function cp_add_css( $prop, $val, $suffix = '' ) {
		$op = '';
		if ( '' !== $val ) {
			if ( '' !== $suffix ) {
				$op = $prop . ':' . esc_attr( $val ) . $suffix . ';';
			} else {
				$op = $prop . ':' . esc_attr( $val ) . ';';
			}
		}
		return $op;
	}
}

add_filter( 'cp_custom_css', 'cp_custom_css_filter', 99, 2 );

if ( ! function_exists( 'cp_custom_css_filter' ) ) {
	/**
	 * Function Name: cp_custom_css_filter Add Custom CSS.
	 *
	 * @param  string $style_id string parameter.
	 * @param  string $css      string parameter.
	 * @since 0.1.5
	 */
	function cp_custom_css_filter( $style_id, $css ) {
		if ( '' !== $css ) {
			echo '<style type="text/css" id="custom-css-' . esc_attr( $style_id ) . '">' . $css . '</style>'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
}

if ( ! function_exists( 'generate_border_css' ) ) {
	/**
	 * Function Name: generate_border_css.
	 *
	 * @param  string $string string parameter.
	 * @return string         string parameter.
	 */
	function generate_border_css( $string ) {
		$pairs  = explode( '|', $string );
		$result = array();
		foreach ( $pairs as $pair ) {
			$pair               = explode( ':', $pair );
			$result[ $pair[0] ] = $pair[1];
		}

		$css_code1 = '';
		if ( isset( $result['br_type'] ) && ( '1' === $result['br_type'] || 1 === $result['br_type'] ) ) {
			$css_code1 .= $result['br_tl'] . 'px ' . $result['br_tr'] . 'px ' . $result['br_br'] . 'px ';
			$css_code1 .= $result['br_bl'] . 'px';
		} else {
			$css_code1 .= $result['br_all'] . 'px';
		}

		$result['border_width'] = ' ';
		$text                   = '';
		$text                  .= 'border-radius: ' . $css_code1 . ';';
		$text                  .= '-moz-border-radius: ' . $css_code1 . ';';
		$text                  .= '-webkit-border-radius: ' . $css_code1 . ';';
		$text                  .= 'border-style: ' . $result['style'] . ';';
		$text                  .= 'border-color: ' . $result['color'] . ';';
		$text                  .= 'border-width: ' . $result['border_width'] . 'px;';

		if ( isset( $result['bw_type'] ) && ( '1' === $result['bw_type'] || 1 === $result['bw_type'] ) ) {
			$text .= 'border-top-width:' . $result['bw_t'] . 'px;';
			$text .= 'border-left-width:' . $result['bw_l'] . 'px;';
			$text .= 'border-right-width:' . $result['bw_r'] . 'px;';
			$text .= 'border-bottom-width:' . $result['bw_b'] . 'px;';
		} else {
			$text .= 'border-width:' . $result['bw_all'] . 'px;';
		}

		return $text;
	}
}

if ( ! function_exists( 'cp_get_wp_image_url_init' ) ) {
	/**
	 * Function Name: cp_get_wp_image_url_init Get WordPress attachment url.
	 *
	 * @param  string $wp_image image url.
	 * @return string           string paramter.
	 * @since 0.1.5
	 */
	function cp_get_wp_image_url_init( $wp_image = '' ) {
		if ( cp_is_not_empty( $wp_image ) ) {
			$wp_image = explode( '|', $wp_image );
			$wp_image = wp_get_attachment_image_src( $wp_image[0], $wp_image[1] );
			$wp_image = $wp_image[0];
			$wp_image = cp_get_protocol_settings_init( $wp_image );
		}
		return $wp_image;
	}
}
add_filter( 'cp_get_wp_image_url', 'cp_get_wp_image_url_init' );

add_filter( 'cp_get_custom_class', 'cp_get_custom_class_init' );

if ( ! function_exists( 'cp_get_custom_class_init' ) ) {
	/**
	 * Function Name: cp_get_custom_class_init Set custom class for modal
	 *
	 * @param  string $custom_class string parameter.
	 * @param  string $style_id     string parameter.
	 * @return string               string parameter.
	 * @since 0.1.5
	 */
	function cp_get_custom_class_init( $custom_class, $style_id ) {
		$custom_class  = str_replace( ' ', ' ', trim( $custom_class ) );
		$custom_class  = str_replace( ',', ' ', trim( $custom_class ) );
		$custom_class .= ' cp-' . $style_id;
		$custom_class  = trim( $custom_class );
		return $custom_class;
	}
}


if ( ! function_exists( 'cp_hide_image_on_mobile_init' ) ) {
	/**
	 * Function Name: cp_hide_image_on_mobile_init Hide Image - On Mobile.
	 *
	 * @param  string $image_displayon_mobile string paraemters.
	 * @param  string $image_resp_width       string paraemters.
	 * @return string                         string paraemters.
	 * @since 0.1.5
	 */
	function cp_hide_image_on_mobile_init( $image_displayon_mobile, $image_resp_width ) {
		$hide_image = '';
		if ( '1' === $image_displayon_mobile ) {
			$hide_image = ' data-hide-img-on-mobile=' . $image_resp_width;
		}
		return $hide_image;
	}
}
add_filter( 'cp_hide_image_on_mobile', 'cp_hide_image_on_mobile_init' );

if ( ! function_exists( 'cp_is_module_scheduled' ) ) {
	/**
	 * Function Name: cp_is_module_scheduled Check schedule of module
	 *
	 * @param  string  $schedule string parameter.
	 * @param  integer $live     integer val.
	 * @return string           string parameter.
	 * @since 0.1.5
	 */
	function cp_is_module_scheduled( $schedule, $live ) {
		$op = '';
		if ( is_array( $schedule ) && '2' === $live ) {
			$op = ' data-scheduled=true data-start="' . esc_attr( $schedule['start'] ) . '" data-end="' . esc_attr( $schedule['end'] ) . '" ';
		} else {
			$op = ' data-scheduled=false ';
		}
		return $op;
	}
}


if ( ! function_exists( 'get_offset_by_time_zone' ) ) {
	/**
	 * Function Name: get_offset_by_time_zone Find timezone offset.
	 *
	 * @param  string $localtimezone string parameter.
	 * @return string                string parameter.
	 */
	function get_offset_by_time_zone( $localtimezone ) {
		if ( empty( $localtimezone ) ) {
			return $localtimezone;
		}
		$time           = new DateTime( gmdate( 'Y-m-d H:i:s' ), new DateTimeZone( $localtimezone ) );
		$timezoneoffset = $time->format( 'P' );
		return $timezoneoffset;
	}
}

add_filter( 'cp_get_scroll_class', 'cp_get_scroll_class_init' );

if ( ! function_exists( 'cp_get_scroll_class_init' ) ) {
	/**
	 * Function Name: cp_get_scroll_class_init.
	 *
	 * @param  string $scroll_class string parameter.
	 * @return string               string parameter.
	 * @since 0.1.5
	 */
	function cp_get_scroll_class_init( $scroll_class ) {
		$scroll_class = $scroll_class;
		$scroll_class = str_replace( ' ', '', trim( $scroll_class ) );
		$scroll_class = str_replace( ',', ' ', trim( $scroll_class ) );
		$scroll_class = trim( $scroll_class );
		return $scroll_class;
	}
}

if ( ! function_exists( 'cp_has_redirect_init' ) ) {
	/**
	 * Function Name: cp_has_redirect_init Check slidein has redirection
	 *
	 * @param  string $on_success    string parameter.
	 * @param  string $redirect_url  string parameter.
	 * @param  string $redirect_data string parameter.
	 * @param  string $on_redirect   string parameter.
	 * @param  string $download_url  string parameter.
	 * @return string                string parameter.
	 */
	function cp_has_redirect_init( $on_success, $redirect_url, $redirect_data, $on_redirect, $download_url ) {
		$op = '';
		if ( 'redirect' === $on_success && '' !== $redirect_url && '1' === $redirect_data ) {
			$op = ' data-redirect-lead-data="' . $redirect_data . '" ';
		}
		if ( 'redirect' === $on_success && '' !== $redirect_url && '' !== $on_redirect ) {
			$op .= ' data-redirect-to ="' . $on_redirect . '" ';
		}
		return $op;
	}
}
add_filter( 'cp_has_redirect', 'cp_has_redirect_init' );

if ( ! function_exists( 'cp_has_enabled_or_disabled_init' ) ) {
	/**
	 * Function Name: cp_has_enabled_or_disabled_initSet value Enabled or Disabled.
	 *
	 * @param  string $modal_exit_intent string parameters.
	 * @return string                    string parameters.
	 */
	function cp_has_enabled_or_disabled_init( $modal_exit_intent ) {
		$op = ( '' !== $modal_exit_intent && '0' !== $modal_exit_intent ) ? 'enabled' : 'disabled';
		return $op;

	}
}
add_filter( 'cp_has_enabled_or_disabled', 'cp_has_enabled_or_disabled_init' );

if ( ! function_exists( 'cp_has_enabled_or_disabled_init' ) ) {
	/**
	 * Function Name: cp_has_enabled_or_disabled_initSet value Enabled or Disabled.
	 *
	 * @param  string $modal_cart_exit_intent string parameters.
	 * @return string                    string parameters.
	 */
	function cp_has_enabled_or_disabled_init( $modal_cart_exit_intent ) {
		$op = ( '' !== $modal_cart_exit_intent && '0' !== $modal_cart_exit_intent ) ? 'enabled' : 'disabled';

		return $op;
	}
}
add_filter( 'cp_has_enabled_or_disabled', 'cp_has_enabled_or_disabled_init' );


if ( ! function_exists( 'cp_get_module_image_url_init' ) ) {
	/**
	 * Function Name: cp_get_module_image_url_init Get Modal Image URL.
	 *
	 * @param  string $module_type           string parameters.
	 * @param  string $module_img_custom_url string parameters.
	 * @param  string $module_img_src        string parameters.
	 * @param  string $module_image          string parameters.
	 * @return [type]                        string parameters.
	 * @since 0.1.5
	 */
	function cp_get_module_image_url_init( $module_type = '', $module_img_custom_url = '', $module_img_src = '', $module_image = '' ) {

		$modal_new_image = '';
		if ( '' === $module_img_src ) {
			$module_img_custom_url = 'upload_img';
		}

		if ( '' !== $module_img_src && 'custom_url' === $module_img_src ) {
			$modal_new_image = $module_img_custom_url;
		} elseif ( isset( $module_img_src ) && 'upload_img' === $module_img_src ) {
			if ( false !== strpos( $module_image, 'http' ) ) {
				$modal_new_image = explode( '|', $module_image );
				$modal_new_image = $modal_new_image[0];
			} else {
				$modal_new_image = apply_filters( 'cp_get_wp_image_url', $module_image );
			}
			$modal_new_image = cp_get_protocol_settings_init( $modal_new_image );
		} else {
			$modal_new_image = '';
		}
		return $modal_new_image;
	}
}
add_filter( 'cp_get_module_image_url', 'cp_get_module_image_url_init' );


if ( ! function_exists( 'cp_get_module_image_alt_init' ) ) {
	/**
	 * Function Name: cp_get_module_image_alt_init.
	 *
	 * @param  string $module_type    string parameters.
	 * @param  string $module_img_src string parameters.
	 * @param  string $module_image   string parameters.
	 * @return string                string parameters.
	 */
	function cp_get_module_image_alt_init( $module_type = '', $module_img_src = '', $module_image = '' ) {

		$alt = '';

		if ( '' === $module_img_src ) {
			$module_img_src = 'upload_img';
		}

		if ( isset( $module_img_src ) && 'upload_img' === $module_img_src ) {
			if ( false !== strpos( $module_image, 'http' ) ) {
				$alt = '';
			} else {
				$modal_image_alt = explode( '|', $module_image );
				if ( count( $modal_image_alt ) > 2 && '' !== $modal_image_alt[2] ) {
					$alt = 'alt=' . $modal_image_alt[2] . '';
				}
			}
		}
		return $alt;
	}
}
add_filter( 'cp_get_module_image_alt', 'cp_get_module_image_alt_init' );

if ( ! function_exists( 'generate_back_gradient' ) ) {
	/**
	 * Function Name:generate_back_gradient Gradient generator.
	 *
	 * @param  string $val string parameters.
	 * @return string      string parameters.
	 */
	function generate_back_gradient( $val ) {
		$grad_arr    = explode( '|', $val );
		$first_color = $grad_arr[0];
		$sec_color   = $grad_arr[1];
		$first_deg   = $grad_arr[2];
		$sec_deg     = $grad_arr[3];
		$grad_type   = $grad_arr[4];
		$direction   = $grad_arr[5];
		$grad_name   = '';
		$grad_css    = '';

		switch ( $direction ) {
			case 'center_left':
				$grad_name = 'left';
				break;
			case 'center_Right':
				$grad_name = 'right';
				break;

			case 'top_center':
				$grad_name = 'top';
				break;

			case 'top_left':
				$grad_name = 'top left';
				break;

			case 'top_right':
				$grad_name = 'top right';
				break;

			case 'bottom_center':
				$grad_name = 'bottom';
				break;

			case 'bottom_left':
				$grad_name = 'bottom left';
				break;

			case 'bottom_right':
				$grad_name = 'bottom right';
				break;

			case 'center_center':
				$grad_name = 'center';
				if ( 'linear' === $grad_type ) {
					$grad_name = 'top left';
				}
				break;

			case 'default':
				break;
		}

		if ( 'linear' === $grad_type ) {
			$ie_css  = $grad_type . '-gradient(to ' . $grad_name . ', ' . $first_color . ' ' . $first_deg . '%, ' . $sec_color . ' ' . $sec_deg . '%)';
			$web_css = '-webkit-' . $grad_type . '-gradient(' . $grad_name . ', ' . $first_color . ' ' . $first_deg . '%, ' . $sec_color . ' ' . $sec_deg . '%)';
			$o_css   = '-o-' . $grad_type . '-gradient(' . $grad_name . ', ' . $first_color . ' ' . $first_deg . '%, ' . $sec_color . ' ' . $sec_deg . '%)';
			$mz_css  = '-moz-' . $grad_type . '-gradient(' . $grad_name . ', ' . $first_color . ' ' . $first_deg . '%, ' . $sec_color . ' ' . $sec_deg . '%)';
		} else {
			$ie_css  = $grad_type . '-gradient( ellipse farthest-corner at ' . $grad_name . ', ' . $first_color . ' ' . $first_deg . '%, ' . $sec_color . ' ' . $sec_deg . '%)';
			$web_css = '-webkit-' . $grad_type . '-gradient( ellipse farthest-corner at ' . $grad_name . ', ' . $first_color . ' ' . $first_deg . '%, ' . $sec_color . ' ' . $sec_deg . '%)';
			$o_css   = '-o-' . $grad_type . '-gradient( ellipse farthest-corner at ' . $grad_name . ', ' . $first_color . ' ' . $first_deg . '%, ' . $sec_color . ' ' . $sec_deg . '%)';
			$mz_css  = '-moz-' . $grad_type . '-gradient( ellipse farthest-corner at ' . $grad_name . ', ' . $first_color . ' ' . $first_deg . '%, ' . $sec_color . ' ' . $sec_deg . '%)';
		}

		$grad_css .= 'background:' . $web_css . ';background:' . $o_css . ';background:' . $mz_css . ';background:' . $ie_css . ';';

		return $grad_css;
	}
}

if ( ! function_exists( 'cplus_is_current_device' ) ) {
	/**
	 * Function Name: cplus_is_current_device Gives current device value.
	 *
	 * @param  string $device device value.
	 * @return bool         boolval.
	 * @since 3.0.4
	 */
	function cplus_is_current_device( $device ) {
		$is_current_device = true;
		$device_name       = '';

		if ( cplus_is_desktop_device() ) {
			$device_name = 'desktop';
		} elseif ( cplus_is_medium_device() ) {
			$device_name = 'tablet';
		} elseif ( wp_is_mobile() && ( ! cplus_is_medium_device() ) ) {
			$device_name = 'mobile';
		}

		if ( '' != $device ) {
			$device_array = explode( '|', $device );
			if ( ! empty( $device_array ) ) {
				if ( in_array( $device_name, $device_array ) ) {
					$is_current_device = false;
				}
			}
		}

		return $is_current_device;
	}
}


if ( ! function_exists( 'cplus_is_medium_device' ) ) {
	/**
	 * Check if current device is medium device
	 *
	 * @since 3.0.4
	 * @return bool $is_medium
	 */
	function cplus_is_medium_device() {

		if ( empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
			$is_medium = false;
		} elseif ( false !== strpos( $_SERVER['HTTP_USER_AGENT'], 'iPad' ) ) {
			$is_medium = true;
		} else {
			$is_medium = false;
		}

		return $is_medium;
	}
}


if ( ! function_exists( 'cplus_is_desktop_device' ) ) {
	/**
	 * Check if current device is desktop device
	 *
	 * @since 3.0.4
	 * @return bool $is_desktop
	 */
	function cplus_is_desktop_device() {

		if ( empty( $_SERVER['HTTP_USER_AGENT'] ) ) {
			$is_desktop = false;
		} elseif ( false !== strpos( $_SERVER['HTTP_USER_AGENT'], 'Macintosh' ) || false !== strpos( $_SERVER['HTTP_USER_AGENT'], 'Windows' )
		) {
			$is_desktop = true;
		} else {
			$is_desktop = false;
		}

		return $is_desktop;
	}
}


if ( ! function_exists( 'cp_get_styles' ) ) {
	/**
	 * CP Get Styles the user roles will be assigned only for the contacts added through the
	 */
	function cp_get_styles() {
		return array(
			'modal',
			'infobar',
			'slidein',
		);
	}
}

if ( ! function_exists( 'cp_add_new_user_role' ) ) {
	/**
	 * Add subscriber as new user to site.
	 *
	 * @param  array $param array of form parameters.
	 */
	function cp_add_new_user_role( $param ) {

		$user_role = '';

		if ( ! in_array( $param['style_name'], cp_get_styles() ) ) {
			return;
		}

		if ( 'modal' === $param['style_name'] ) {
			$module_data         = get_option( 'smile_modal_styles' );
			$module_variant_data = get_option( 'modal_variant_tests' );

		}

		if ( 'infobar' === $param['style_name'] ) {
			$module_data         = get_option( 'smile_info_bar_styles' );
			$module_variant_data = get_option( 'info_bar_variant_tests' );
		}

		if ( 'slidein' === $param['style_name'] ) {
			$module_data         = get_option( 'smile_slide_in_styles' );
			$module_variant_data = get_option( 'slide_in_variant_tests' );

		}

		$user_email = isset( $param['email'] ) ? $param['email'] : '';
		$id         = username_exists( $user_email );
		$website    = site_url();

		foreach ( $module_data  as $key => $value ) {
			if ( '' != $module_data[ $key ]['style_settings'] ) {
				if ( '' != $module_data[ $key ]['style_id'] && $param['style_id'] == $module_data[ $key ]['style_id'] ) {
					$prev_styles_array = maybe_unserialize( $module_data[ $key ]['style_settings'] );
					$user_role         = $prev_styles_array['cp_new_user_role'];
				}
			}
		}
		if ( $module_variant_data ) {
			foreach ( $module_variant_data as $key => $value ) {
				foreach ( $value as $key_data => $data_value ) {
					if ( '' != $data_value['style_settings'] ) {
						$data_variant = maybe_unserialize( $data_value['style_settings'] );
						if ( '' != $data_variant['variant_style_id'] && $param['style_id'] == $data_variant['variant_style_id'] ) {
							$user_role = $data_variant['cp_new_user_role'];

						}
					}
				}
			}
		}

		if ( '' !== $user_role && 'none' !== $user_role && 'None' !== $user_role && ! $id && email_exists( $user_email ) == false ) {

			$random_password = wp_generate_password( 12, false );

			$userdata = array(
				'user_login' => $user_email,
				'user_email' => $user_email,
				'user_url'   => $website,
				'user_pass'  => $random_password,
				'role'       => strtolower( $user_role ),
			);

			$user_id = wp_insert_user( $userdata );
			wp_new_user_notification( $user_id, null, 'both' );
		}

		return true;
	}
}


if ( ! function_exists( 'cp_get_the_user_ip' ) ) {
	/**
	 * Function Name: cp_get_the_user_ip.
	 *
	 * @return [type] [description]
	 */
	function cp_get_the_user_ip() {
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			// check ip from share internet.
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			// to check ip is pass from proxy.
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return apply_filters( 'wpb_get_ip', $ip );
	}
}

/**
 * Function Name: cp_get_module_images_new.
 *
 * @param  string $bg_image            image.
 * @param  string $bg_image_src        image_src.
 * @param  string $bg_image_custom_url custom_url.
 * @return string                      image_url.
 */
function cp_get_module_images_new( $bg_image, $bg_image_src, $bg_image_custom_url ) {

	if ( isset( $bg_image_src ) && ! empty( $bg_image_src ) ) {

		$module_bg_image = '';
		if ( 'custom_url' === $bg_image_src ) {
			$module_bg_image = $bg_image_custom_url;
		} elseif ( 'upload_img' === $bg_image_src ) {
			if ( isset( $bg_image ) ) {
				if ( false !== strpos( $bg_image, 'http' ) ) {
					$module_bg_image = explode( '|', $bg_image );
					$module_bg_image = $module_bg_image[0];
				} else {
					$module_bg_image = apply_filters( 'cp_get_wp_image_url', $bg_image );
				}
				$module_bg_image = cp_get_protocol_settings_init( $module_bg_image );
			}
		} else {
			$module_bg_image = '';
		}

		return $module_bg_image;
	}
}

/**
 * Function Name: cp_get_image_size_opt description]
 *
 * @param  string $opt_bg image_size.
 * @return string         custom css for image-size.
 */
function cp_get_image_size_opt( $opt_bg ) {
	if ( isset( $opt_bg ) && false !== strpos( $opt_bg, '|' ) ) {
		$bg_setting  = '';
		$opt_bg      = explode( '|', $opt_bg );
		$bg_repeat   = $opt_bg[0];
		$bg_pos      = $opt_bg[1];
		$bg_size     = $opt_bg[2];
		$bg_setting .= 'background-repeat: ' . $bg_repeat . ';';
		$bg_setting .= 'background-position: ' . $bg_pos . ';';
		$bg_setting .= 'background-size: ' . $bg_size . ';';
		return $bg_setting;
	}
}

add_filter( 'cp_get_custom_selector', 'cp_get_custom_slector_init' );

if ( ! function_exists( 'cp_get_custom_slector_init' ) ) {
	/**
	 * Function Name: cp_get_custom_slector_init Set custom class for modal
	 *
	 * @param  string $custom_selector        string parameter.
	 * @return string                       string parameter.
	 * @since 0.1.5
	 */
	function cp_get_custom_slector_init( $custom_selector ) {
		$custom_selector = str_replace( ' ', '', trim( $custom_selector ) );
		$custom_selector = trim( $custom_selector );
		return $custom_selector;
	}
}

add_filter( 'cp_get_custom_selector_class', 'cp_get_custom_slector_class_init' );

if ( ! function_exists( 'cp_get_custom_slector_class_init' ) ) {
	/**
	 * Function Name: cp_get_custom_slector_class_init Set custom class for modal
	 *
	 * @param  string $custom_selector        string parameter.
	 * @return string                       string parameter.
	 * @since 0.1.5
	 */
	function cp_get_custom_slector_class_init( $custom_selector ) {
		$custom_selector = str_replace( ' ', '', trim( $custom_selector ) );
		$custom_selector = str_replace( ',', ' ', trim( $custom_selector ) );
		$custom_selector = trim( $custom_selector );
		return $custom_selector;
	}
}

add_filter( 'cp_get_timezone', 'cp_get_timezone_init' );

if ( ! function_exists( 'cp_get_timezone_init' ) ) {
	/**
	 * Function Name: cp_get_timezone_init return timezone.
	 *
	 * @return string  string parameter.
	 * @since 3.3.2
	 */
	function cp_get_timezone_init() {

		$timezone = '';
		$timezone = get_option( 'timezone_string' );
		if ( '' === $timezone ) {
			$toffset  = get_option( 'gmt_offset' );
			$timezone = '' . $toffset . '';
		}
		return $timezone;

	}
}

if ( ! function_exists( 'cp_get_form_process_html' ) ) {
	/**
	 * Name: cp_get_form_process_html return form html.
	 *
	 * @param  string $style css.
	 * @return string        css and html.
	 */
	function cp_get_form_process_html( $style ) {

		$op = '<div class ="cp-form-processing" >
			<div class="smile-absolute-loader" style="visibility: visible;">
				<div class="smile-loader" style = "' . $style . '" >
					<div class="smile-loading-bar"></div>
					<div class="smile-loading-bar"></div>
					<div class="smile-loading-bar"></div>
					<div class="smile-loading-bar"></div>
				</div>
			</div>
		</div>';
		return $op;
	}
}

if ( ! function_exists( 'cp_get_close_adj_position' ) ) {
	/**
	 * Nmae: cp_get_close_adj_position Return adjacent close position.
	 *
	 * @param  string $position posiiton.
	 * @return string           posiiton.
	 */
	function cp_get_close_adj_position( $position ) {
		$close_adj_class = '';
		switch ( $position ) {
			case 'top_left':
				$close_adj_class .= ' cp-adjacent-left';
				break;
			case 'top_right':
				$close_adj_class .= ' cp-adjacent-right';
				break;
			case 'bottom_left':
				$close_adj_class .= ' cp-adjacent-bottom-left';
				break;
			case 'bottom_right':
				$close_adj_class .= ' cp-adjacent-bottom-right';
				break;
		}
		return $close_adj_class;
	}
}

if ( ! function_exists( 'cp_get_tooltip_position' ) ) {
	/**
	 * Name: cp_get_tooltip_position .
	 *
	 * @param  string $close_adjacent_position posiiton.
	 * @return string.
	 */
	function cp_get_tooltip_position( $close_adjacent_position ) {
		$position = '';
		switch ( $close_adjacent_position ) {
			case 'top_left':
				$position = 'right';
				break;
			case 'top_right':
				$position = 'left';
				break;
		}
			return $position;
	}
}



if ( ! function_exists( 'cplus_is_geo_location' ) ) {
	/**
	 * Function name cplus_is_geo_location to check if module is visible for given countries.
	 *
	 * @param  string $country_type     type of countries.
	 * @param  string $specific_country country names.
	 * @param  string $hide_specific_countries country names.
	 * @return boolval(true/false)      true/false.
	 */
	function cplus_is_geo_location( $country_type, $specific_country, $hide_specific_countries ) {

		$country_arr = array();

		if ( '' !== $specific_country ) {
			$country_arr = explode( ',', $specific_country );
		}

		// Get Ip address of the user.
		$ipaddress = '';
		if ( getenv( 'HTTP_CLIENT_IP' ) ) {
			$ipaddress = getenv( 'HTTP_CLIENT_IP' );
		} elseif ( getenv( 'HTTP_X_FORWARDED_FOR' ) ) {
			$ipaddress = getenv( 'HTTP_X_FORWARDED_FOR' );
		} elseif ( getenv( 'HTTP_X_FORWARDED' ) ) {
			$ipaddress = getenv( 'HTTP_X_FORWARDED' );
		} elseif ( getenv( 'HTTP_FORWARDED_FOR' ) ) {
			$ipaddress = getenv( 'HTTP_FORWARDED_FOR' );
		} elseif ( getenv( 'HTTP_FORWARDED' ) ) {
			$ipaddress = getenv( 'HTTP_FORWARDED' );
		} elseif ( getenv( 'REMOTE_ADDR' ) ) {
			$ipaddress = getenv( 'REMOTE_ADDR' );
		} else {
			$ipaddress = 'UNKNOWN';
		}
		$user_ip = $ipaddress;

		$visitor_ip_location = Convert_Plug_Geolocation_Target::geolocate_ip( $user_ip );

		$arr_all_counries = array(
			'AF' => __( 'Afghanistan', 'smile' ),
			'AX' => __( '&#197;land Islands', 'smile' ),
			'AL' => __( 'Albania', 'smile' ),
			'DZ' => __( 'Algeria', 'smile' ),
			'AS' => __( 'American Samoa', 'smile' ),
			'AD' => __( 'Andorra', 'smile' ),
			'AO' => __( 'Angola', 'smile' ),
			'AI' => __( 'Anguilla', 'smile' ),
			'AQ' => __( 'Antarctica', 'smile' ),
			'AG' => __( 'Antigua and Barbuda', 'smile' ),
			'AR' => __( 'Argentina', 'smile' ),
			'AM' => __( 'Armenia', 'smile' ),
			'AW' => __( 'Aruba', 'smile' ),
			'AU' => __( 'Australia', 'smile' ),
			'AT' => __( 'Austria', 'smile' ),
			'AZ' => __( 'Azerbaijan', 'smile' ),
			'BS' => __( 'Bahamas', 'smile' ),
			'BH' => __( 'Bahrain', 'smile' ),
			'BD' => __( 'Bangladesh', 'smile' ),
			'BB' => __( 'Barbados', 'smile' ),
			'BY' => __( 'Belarus', 'smile' ),
			'BE' => __( 'Belgium', 'smile' ),
			'PW' => __( 'Belau', 'smile' ),
			'BZ' => __( 'Belize', 'smile' ),
			'BJ' => __( 'Benin', 'smile' ),
			'BM' => __( 'Bermuda', 'smile' ),
			'BT' => __( 'Bhutan', 'smile' ),
			'BO' => __( 'Bolivia', 'smile' ),
			'BQ' => __( 'Bonaire, Saint Eustatius and Saba', 'smile' ),
			'BA' => __( 'Bosnia and Herzegovina', 'smile' ),
			'BW' => __( 'Botswana', 'smile' ),
			'BV' => __( 'Bouvet Island', 'smile' ),
			'BR' => __( 'Brazil', 'smile' ),
			'IO' => __( 'British Indian Ocean Territory', 'smile' ),
			'VG' => __( 'British Virgin Islands', 'smile' ),
			'BN' => __( 'Brunei', 'smile' ),
			'BG' => __( 'Bulgaria', 'smile' ),
			'BF' => __( 'Burkina Faso', 'smile' ),
			'BI' => __( 'Burundi', 'smile' ),
			'KH' => __( 'Cambodia', 'smile' ),
			'CM' => __( 'Cameroon', 'smile' ),
			'CA' => __( 'Canada', 'smile' ),
			'CV' => __( 'Cape Verde', 'smile' ),
			'KY' => __( 'Cayman Islands', 'smile' ),
			'CF' => __( 'Central African Republic', 'smile' ),
			'TD' => __( 'Chad', 'smile' ),
			'CL' => __( 'Chile', 'smile' ),
			'CN' => __( 'China', 'smile' ),
			'CX' => __( 'Christmas Island', 'smile' ),
			'CC' => __( 'Cocos (Keeling) Islands', 'smile' ),
			'CO' => __( 'Colombia', 'smile' ),
			'KM' => __( 'Comoros', 'smile' ),
			'CG' => __( 'Congo (Brazzaville)', 'smile' ),
			'CD' => __( 'Congo (Kinshasa)', 'smile' ),
			'CK' => __( 'Cook Islands', 'smile' ),
			'CR' => __( 'Costa Rica', 'smile' ),
			'HR' => __( 'Croatia', 'smile' ),
			'CU' => __( 'Cuba', 'smile' ),
			'CW' => __( 'Cura&ccedil;ao', 'smile' ),
			'CY' => __( 'Cyprus', 'smile' ),
			'CZ' => __( 'Czech Republic', 'smile' ),
			'DK' => __( 'Denmark', 'smile' ),
			'DJ' => __( 'Djibouti', 'smile' ),
			'DM' => __( 'Dominica', 'smile' ),
			'DO' => __( 'Dominican Republic', 'smile' ),
			'EC' => __( 'Ecuador', 'smile' ),
			'EG' => __( 'Egypt', 'smile' ),
			'SV' => __( 'El Salvador', 'smile' ),
			'GQ' => __( 'Equatorial Guinea', 'smile' ),
			'ER' => __( 'Eritrea', 'smile' ),
			'EE' => __( 'Estonia', 'smile' ),
			'ET' => __( 'Ethiopia', 'smile' ),
			'FK' => __( 'Falkland Islands', 'smile' ),
			'FO' => __( 'Faroe Islands', 'smile' ),
			'FJ' => __( 'Fiji', 'smile' ),
			'FI' => __( 'Finland', 'smile' ),
			'FR' => __( 'France', 'smile' ),
			'GF' => __( 'French Guiana', 'smile' ),
			'PF' => __( 'French Polynesia', 'smile' ),
			'TF' => __( 'French Southern Territories', 'smile' ),
			'GA' => __( 'Gabon', 'smile' ),
			'GM' => __( 'Gambia', 'smile' ),
			'GE' => __( 'Georgia', 'smile' ),
			'DE' => __( 'Germany', 'smile' ),
			'GH' => __( 'Ghana', 'smile' ),
			'GI' => __( 'Gibraltar', 'smile' ),
			'GR' => __( 'Greece', 'smile' ),
			'GL' => __( 'Greenland', 'smile' ),
			'GD' => __( 'Grenada', 'smile' ),
			'GP' => __( 'Guadeloupe', 'smile' ),
			'GU' => __( 'Guam', 'smile' ),
			'GT' => __( 'Guatemala', 'smile' ),
			'GG' => __( 'Guernsey', 'smile' ),
			'GN' => __( 'Guinea', 'smile' ),
			'GW' => __( 'Guinea-Bissau', 'smile' ),
			'GY' => __( 'Guyana', 'smile' ),
			'HT' => __( 'Haiti', 'smile' ),
			'HM' => __( 'Heard Island and McDonald Islands', 'smile' ),
			'HN' => __( 'Honduras', 'smile' ),
			'HK' => __( 'Hong Kong', 'smile' ),
			'HU' => __( 'Hungary', 'smile' ),
			'IS' => __( 'Iceland', 'smile' ),
			'IN' => __( 'India', 'smile' ),
			'ID' => __( 'Indonesia', 'smile' ),
			'IR' => __( 'Iran', 'smile' ),
			'IQ' => __( 'Iraq', 'smile' ),
			'IE' => __( 'Ireland', 'smile' ),
			'IM' => __( 'Isle of Man', 'smile' ),
			'IL' => __( 'Israel', 'smile' ),
			'IT' => __( 'Italy', 'smile' ),
			'CI' => __( 'Ivory Coast', 'smile' ),
			'JM' => __( 'Jamaica', 'smile' ),
			'JP' => __( 'Japan', 'smile' ),
			'JE' => __( 'Jersey', 'smile' ),
			'JO' => __( 'Jordan', 'smile' ),
			'KZ' => __( 'Kazakhstan', 'smile' ),
			'KE' => __( 'Kenya', 'smile' ),
			'KI' => __( 'Kiribati', 'smile' ),
			'KW' => __( 'Kuwait', 'smile' ),
			'KG' => __( 'Kyrgyzstan', 'smile' ),
			'LA' => __( 'Laos', 'smile' ),
			'LV' => __( 'Latvia', 'smile' ),
			'LB' => __( 'Lebanon', 'smile' ),
			'LS' => __( 'Lesotho', 'smile' ),
			'LR' => __( 'Liberia', 'smile' ),
			'LY' => __( 'Libya', 'smile' ),
			'LI' => __( 'Liechtenstein', 'smile' ),
			'LT' => __( 'Lithuania', 'smile' ),
			'LU' => __( 'Luxembourg', 'smile' ),
			'MO' => __( 'Macao S.A.R., China', 'smile' ),
			'MK' => __( 'Macedonia', 'smile' ),
			'MG' => __( 'Madagascar', 'smile' ),
			'MW' => __( 'Malawi', 'smile' ),
			'MY' => __( 'Malaysia', 'smile' ),
			'MV' => __( 'Maldives', 'smile' ),
			'ML' => __( 'Mali', 'smile' ),
			'MT' => __( 'Malta', 'smile' ),
			'MH' => __( 'Marshall Islands', 'smile' ),
			'MQ' => __( 'Martinique', 'smile' ),
			'MR' => __( 'Mauritania', 'smile' ),
			'MU' => __( 'Mauritius', 'smile' ),
			'YT' => __( 'Mayotte', 'smile' ),
			'MX' => __( 'Mexico', 'smile' ),
			'FM' => __( 'Micronesia', 'smile' ),
			'MD' => __( 'Moldova', 'smile' ),
			'MC' => __( 'Monaco', 'smile' ),
			'MN' => __( 'Mongolia', 'smile' ),
			'ME' => __( 'Montenegro', 'smile' ),
			'MS' => __( 'Montserrat', 'smile' ),
			'MA' => __( 'Morocco', 'smile' ),
			'MZ' => __( 'Mozambique', 'smile' ),
			'MM' => __( 'Myanmar', 'smile' ),
			'NA' => __( 'Namibia', 'smile' ),
			'NR' => __( 'Nauru', 'smile' ),
			'NP' => __( 'Nepal', 'smile' ),
			'NL' => __( 'Netherlands', 'smile' ),
			'NC' => __( 'New Caledonia', 'smile' ),
			'NZ' => __( 'New Zealand', 'smile' ),
			'NI' => __( 'Nicaragua', 'smile' ),
			'NE' => __( 'Niger', 'smile' ),
			'NG' => __( 'Nigeria', 'smile' ),
			'NU' => __( 'Niue', 'smile' ),
			'NF' => __( 'Norfolk Island', 'smile' ),
			'MP' => __( 'Northern Mariana Islands', 'smile' ),
			'KP' => __( 'North Korea', 'smile' ),
			'NO' => __( 'Norway', 'smile' ),
			'OM' => __( 'Oman', 'smile' ),
			'PK' => __( 'Pakistan', 'smile' ),
			'PS' => __( 'Palestinian Territory', 'smile' ),
			'PA' => __( 'Panama', 'smile' ),
			'PG' => __( 'Papua New Guinea', 'smile' ),
			'PY' => __( 'Paraguay', 'smile' ),
			'PE' => __( 'Peru', 'smile' ),
			'PH' => __( 'Philippines', 'smile' ),
			'PN' => __( 'Pitcairn', 'smile' ),
			'PL' => __( 'Poland', 'smile' ),
			'PT' => __( 'Portugal', 'smile' ),
			'PR' => __( 'Puerto Rico', 'smile' ),
			'QA' => __( 'Qatar', 'smile' ),
			'RE' => __( 'Reunion', 'smile' ),
			'RO' => __( 'Romania', 'smile' ),
			'RU' => __( 'Russia', 'smile' ),
			'RW' => __( 'Rwanda', 'smile' ),
			'BL' => __( 'Saint Barth&eacute;lemy', 'smile' ),
			'SH' => __( 'Saint Helena', 'smile' ),
			'KN' => __( 'Saint Kitts and Nevis', 'smile' ),
			'LC' => __( 'Saint Lucia', 'smile' ),
			'MF' => __( 'Saint Martin (French part)', 'smile' ),
			'SX' => __( 'Saint Martin (Dutch part)', 'smile' ),
			'PM' => __( 'Saint Pierre and Miquelon', 'smile' ),
			'VC' => __( 'Saint Vincent and the Grenadines', 'smile' ),
			'SM' => __( 'San Marino', 'smile' ),
			'ST' => __( 'S&atilde;o Tom&eacute; and Pr&iacute;ncipe', 'smile' ),
			'SA' => __( 'Saudi Arabia', 'smile' ),
			'SN' => __( 'Senegal', 'smile' ),
			'RS' => __( 'Serbia', 'smile' ),
			'SC' => __( 'Seychelles', 'smile' ),
			'SL' => __( 'Sierra Leone', 'smile' ),
			'SG' => __( 'Singapore', 'smile' ),
			'SK' => __( 'Slovakia', 'smile' ),
			'SI' => __( 'Slovenia', 'smile' ),
			'SB' => __( 'Solomon Islands', 'smile' ),
			'SO' => __( 'Somalia', 'smile' ),
			'ZA' => __( 'South Africa', 'smile' ),
			'GS' => __( 'South Georgia/Sandwich Islands', 'smile' ),
			'KR' => __( 'South Korea', 'smile' ),
			'SS' => __( 'South Sudan', 'smile' ),
			'ES' => __( 'Spain', 'smile' ),
			'LK' => __( 'Sri Lanka', 'smile' ),
			'SD' => __( 'Sudan', 'smile' ),
			'SR' => __( 'Suriname', 'smile' ),
			'SJ' => __( 'Svalbard and Jan Mayen', 'smile' ),
			'SZ' => __( 'Swaziland', 'smile' ),
			'SE' => __( 'Sweden', 'smile' ),
			'CH' => __( 'Switzerland', 'smile' ),
			'SY' => __( 'Syria', 'smile' ),
			'TW' => __( 'Taiwan', 'smile' ),
			'TJ' => __( 'Tajikistan', 'smile' ),
			'TZ' => __( 'Tanzania', 'smile' ),
			'TH' => __( 'Thailand', 'smile' ),
			'TL' => __( 'Timor-Leste', 'smile' ),
			'TG' => __( 'Togo', 'smile' ),
			'TK' => __( 'Tokelau', 'smile' ),
			'TO' => __( 'Tonga', 'smile' ),
			'TT' => __( 'Trinidad and Tobago', 'smile' ),
			'TN' => __( 'Tunisia', 'smile' ),
			'TR' => __( 'Turkey', 'smile' ),
			'TM' => __( 'Turkmenistan', 'smile' ),
			'TC' => __( 'Turks and Caicos Islands', 'smile' ),
			'TV' => __( 'Tuvalu', 'smile' ),
			'UG' => __( 'Uganda', 'smile' ),
			'UA' => __( 'Ukraine', 'smile' ),
			'AE' => __( 'United Arab Emirates', 'smile' ),
			'GB' => __( 'United Kingdom (UK)', 'smile' ),
			'US' => __( 'United States (US)', 'smile' ),
			'UM' => __( 'United States (US) Minor Outlying Islands', 'smile' ),
			'VI' => __( 'United States (US) Virgin Islands', 'smile' ),
			'UY' => __( 'Uruguay', 'smile' ),
			'UZ' => __( 'Uzbekistan', 'smile' ),
			'VU' => __( 'Vanuatu', 'smile' ),
			'VA' => __( 'Vatican', 'smile' ),
			'VE' => __( 'Venezuela', 'smile' ),
			'VN' => __( 'Vietnam', 'smile' ),
			'WF' => __( 'Wallis and Futuna', 'smile' ),
			'EH' => __( 'Western Sahara', 'smile' ),
			'WS' => __( 'Samoa', 'smile' ),
			'YE' => __( 'Yemen', 'smile' ),
			'ZM' => __( 'Zambia', 'smile' ),
			'ZW' => __( 'Zimbabwe', 'smile' ),
		);

		$arr_eu_countries = array(
			'AL' => __( 'Albania', 'smile' ),
			'AD' => __( 'Andorra', 'smile' ),
			'AM' => __( 'Armenia', 'smile' ),
			'AT' => __( 'Austria', 'smile' ),
			'AZ' => __( 'Azerbaijan', 'smile' ),
			'BY' => __( 'Belarus', 'smile' ),
			'BE' => __( 'Belgium', 'smile' ),
			'BA' => __( 'Bosnia and Herzegovina', 'smile' ),
			'BG' => __( 'Bulgaria', 'smile' ),
			'HR' => __( 'Croatia', 'smile' ),
			'CY' => __( 'Cyprus', 'smile' ),
			'CZ' => __( 'Czech Republic', 'smile' ),
			'DK' => __( 'Denmark', 'smile' ),
			'EE' => __( 'Estonia', 'smile' ),
			'FI' => __( 'Finland', 'smile' ),
			'FR' => __( 'France', 'smile' ),
			'GE' => __( 'Georgia', 'smile' ),
			'DE' => __( 'Germany', 'smile' ),
			'GR' => __( 'Greece', 'smile' ),
			'HU' => __( 'Hungary', 'smile' ),
			'IS' => __( 'Iceland', 'smile' ),
			'IE' => __( 'Ireland', 'smile' ),
			'IT' => __( 'Italy', 'smile' ),
			'KZ' => __( 'Kazakhstan', 'smile' ),
			'LV' => __( 'Latvia', 'smile' ),
			'LI' => __( 'Liechtenstein', 'smile' ),
			'LT' => __( 'Lithuania', 'smile' ),
			'LU' => __( 'Luxembourg', 'smile' ),
			'MK' => __( 'Macedonia', 'smile' ),
			'MT' => __( 'Malta', 'smile' ),
			'MD' => __( 'Moldova', 'smile' ),
			'MC' => __( 'Monaco', 'smile' ),
			'ME' => __( 'Montenegro', 'smile' ),
			'NL' => __( 'Netherlands', 'smile' ),
			'NO' => __( 'Norway', 'smile' ),
			'PL' => __( 'Poland', 'smile' ),
			'PT' => __( 'Portugal', 'smile' ),
			'RO' => __( 'Romania', 'smile' ),
			'RU' => __( 'Russia', 'smile' ),
			'SM' => __( 'San Marino', 'smile' ),
			'RS' => __( 'Serbia', 'smile' ),
			'SK' => __( 'Slovakia', 'smile' ),
			'SI' => __( 'Slovenia', 'smile' ),
			'ES' => __( 'Spain', 'smile' ),
			'SE' => __( 'Sweden', 'smile' ),
			'CH' => __( 'Switzerland', 'smile' ),
			'TR' => __( 'Turkey', 'smile' ),
			'UA' => __( 'Ukraine', 'smile' ),
			'GB' => __( 'United Kingdom (UK)', 'smile' ),
			'VA' => __( 'Vatican', 'smile' ),
		);

		$show_popup = false;

		switch ( $country_type ) {
			case 'all':
				$show_popup = true;
				break;

			case 'basic-eu':
				foreach ( $arr_eu_countries as $key => $value ) {
					if ( $visitor_ip_location['country'] == $key ) {
						$show_popup = true;
						break;
					}
				}
				break;

			case 'basic-non-eu':
				$arr_country_code = array_keys( $arr_eu_countries );
				if ( ! in_array( $visitor_ip_location['country'], $arr_country_code ) ) {
					$show_popup = true;
				}
				break;

			case 'specifics-geo':
				if ( ! empty( $country_arr ) ) {
					$visitor_country = $arr_all_counries[ $visitor_ip_location['country'] ];
					if ( in_array( $visitor_country, $country_arr ) ) {
						$show_popup = true;
					}
				}
				break;

			default:
				break;
		}

		// check if country is present in Exclude countries?
		if ( '' !== $hide_specific_countries ) {
			$hide_country_arr = explode( ',', $hide_specific_countries );
			$visitor_country  = $arr_all_counries[ $visitor_ip_location['country'] ];

			if ( in_array( $visitor_country, $hide_country_arr ) ) {
				$show_popup = false;
			}
		}

		return $show_popup;
	}
}

if ( ! function_exists( 'cp_get_setting' ) ) {

	/**
	 * Helper function to get style setting by style id.
	 *
	 * @param  string $style_id style ID.
	 * @param  string $style_type  style type.
	 * @param string $setting_key settings key to fetch.
	 * @return string $setting_key settings key to fetch.
	 */
	function cp_get_setting( $style_id, $style_type, $setting_key ) {
		if ( 'modal' === $style_type ) {
			$module_data         = get_option( 'smile_modal_styles' );
			$module_variant_data = get_option( 'modal_variant_tests' );

		}
		if ( 'infobar' === $style_type ) {
			$module_data         = get_option( 'smile_info_bar_styles' );
			$module_variant_data = get_option( 'info_bar_variant_tests' );

		}
		if ( 'slidein' === $style_type ) {
			$module_data         = get_option( 'smile_slide_in_styles' );
			$module_variant_data = get_option( 'slide_in_variant_tests' );

		}
		if ( isset( $module_data ) ) {
			foreach ( $module_data  as $key => $value ) {
				if ( '' != $module_data[ $key ]['style_settings'] ) {
					if ( '' != $module_data[ $key ]['style_id'] && $style_id == $module_data[ $key ]['style_id'] ) {
						$prev_styles_array = maybe_unserialize( $module_data[ $key ]['style_settings'] );
							$setting_key   = $prev_styles_array['mailer'];
							return $setting_key;

					}
				}
			}
		}

		if ( isset( $module_variant_data ) ) {
			foreach ( $module_variant_data as $key => $value ) {
				foreach ( $value as $key_data => $data_value ) {
					if ( '' != $data_value['style_settings'] ) {
						$data_variant = maybe_unserialize( $data_value['style_settings'] );
						if ( '' != $data_variant['variant_style_id'] && $style_id == $data_variant['variant_style_id'] ) {
							$setting_key = $data_variant['mailer'];
							return $setting_key;
						}
					}
				}
			}
		}
			return '';
	}
}


