<?php
/**
 * Get Info bar Image URL.
 *
 * @since 0.1.5
 * @package Convert Plus.
 */

// Functions for infobar.
/**
 * Function name: cp_get_ib_image_url.
 *
 * @param  string $ib_image string parameter.
 * @return string           string parameter.
 */
function cp_get_ib_image_url( $ib_image = '' ) {
	if ( false !== strpos( $ib_image, 'http' ) ) {
		$ib_image = explode( '|', $ib_image );
		$ib_image = $ib_image[0];
	} else {
		$ib_image = explode( '|', $ib_image );
		$ib_image = wp_get_attachment_image_src( $ib_image[0], $ib_image[1] );
		$ib_image = $ib_image[0];
	}
	return $ib_image;
}

if ( ! function_exists( 'cp_ib_global_before_init' ) ) {
	/**
	 * Function name: cp_ib_global_before_init Info Bar Before.
	 *
	 * @param  array $a array parameter.
	 * @since 0.2.3
	 */
	function cp_ib_global_before_init( $a ) {

		$style_id                 = ( isset( $a['style_id'] ) ) ? $a['style_id'] : '';
		$uid                      = $a['uid'];
		$style_details            = get_style_details( $style_id, 'info_bar' );
		$is_ib_inline             = ( isset( $a['display'] ) && 'inline' === $a['display'] ) ? true : false;
		$ib_class_name            = '.cp-info-bar';
		$ib_custom_class          = '';
		$ib_custom_id             = '';
		$priority_cls             = '';
		$inactive_data            = '';
		$impression_disable_class = '';
		$scroll_data              = '';
		$scroll_class             = '';
		$is_scheduled             = '';
		$timezone                 = '';

		if ( $is_ib_inline ) {
			$ib_class_name = '.cp-info-bar-inline';
			$uid           = ( isset( $a['style_id'] ) ) ? $a['style_id'] : '';
		}

		// check referrer detection.
		$referrer_check    = ( isset( $a['enable_referrer'] ) && (int) $a['enable_referrer'] ) ? 'display' : 'hide';
		$referrer_domain   = ( 'display' === $referrer_check ) ? $a['display_to'] : $a['hide_from'];
		$referrer_data     = '';
		$autoclose_data    = '';
		$css               = '';
		$cp_info_bar_class = '';

		if ( '' !== $referrer_check ) {
			$referrer_data  = 'data-referrer-domain="' . $referrer_domain . '"';
			$referrer_data .= ' data-referrer-check="' . $referrer_check . '"';
		}

		// check close after few second.
		$autoclose_on_duration = ( isset( $a['autoclose_on_duration'] ) && (int) $a['autoclose_on_duration'] ) ? $a['autoclose_on_duration'] : '';
		$close_module_duration = ( isset( $a['close_module_duration'] ) && (int) $a['close_module_duration'] ) ? $a['close_module_duration'] : '';
		$is_inline             = ( isset( $a['display'] ) && 'inline' === $a['display'] ) ? true : false;

		if ( '' !== $autoclose_on_duration && ! $is_inline && ( isset( $a['toggle_btn'] ) && '1' !== $a['toggle_btn'] ) && ( isset( $a['close_info_bar'] ) && 'do_not_close' !== $a['close_info_bar'] ) ) {
			$autoclose_data = 'data-close-after="' . $close_module_duration . '"';
		}

		// Enqueue Google Fonts.
		cp_enqueue_google_fonts( $a['cp_google_fonts'] );

		$page_down = 0;
		// push down page only if info bar position is at top.
		if ( ! $is_ib_inline && 'cp-pos-top' === $a['infobar_position'] && $a['page_down'] ) {
			$page_down = 1;
		}

		/**
		 *  Shadow & Border.
		 */
		$cp_info_bar_class .= ( '' !== $a['enable_shadow'] && '1' === $a['enable_shadow'] ) ? 'cp-info-bar-shadow' : '';

		// Border.
		if ( '' !== $a['enable_border'] && '1' === $a['enable_border'] ) {
			$cp_info_bar_class .= ' cp-info-bar-border';

			// Generate the BORDER COLOR.
			if ( isset( $a['border_darken'] ) ) {
				$css .= $ib_class_name . '.content-' . $uid . '.cp-pos-top.cp-info-bar-border {
					border-bottom: 2px solid ' . $a['border_darken'] . '
				}
				' . $ib_class_name . '.content-' . $uid . '.cp-pos-bottom.cp-info-bar-border {
					border-top: 2px solid ' . $a['border_darken'] . '
				}';
			}
		}

		// Custom CSS.
		$css .= $a['custom_css'];

		/**
		 *  Toggle Button.
		 */
		$font = 'sans-serif';
		if ( $a['toggle_button_font'] ) {
			$font = $a['toggle_button_font'] . ',' . $font;
		}

		$css .= '.cp-info-bar.content-' . $uid . ' .cp-ifb-toggle-btn {
			font-family: ' . $font . '
		}';

		/**
		 *  Background - (Background Color / Gradient).
		 */
		if ( '' !== $a['bg_gradient'] && '1' === $a['bg_gradient'] ) {
			$grad_css        = '';
			$module_gradient = isset( $a['module_bg_gradient'] ) ? $a['module_bg_gradient'] : '';
			if ( '' !== $module_gradient ) {
				$grad_css = generate_back_gradient( $module_gradient );
				$css     .= $ib_class_name . '.content-' . $uid . ' .cp-info-bar-body-overlay {' . $grad_css . '}';
			} else {
				$css .= $ib_class_name . '.content-' . $uid . ' .cp-info-bar-body-overlay {
					background: -webkit-linear-gradient(' . $a['bg_gradient_lighten'] . ', ' . $a['bg_color'] . ');
					background: -o-linear-gradient(' . $a['bg_gradient_lighten'] . ', ' . $a['bg_color'] . ');
					background: -moz-linear-gradient(' . $a['bg_gradient_lighten'] . ', ' . $a['bg_color'] . ');
					background: linear-gradient(' . $a['bg_gradient_lighten'] . ', ' . $a['bg_color'] . ');
				}';
			}
		} else {
			if ( 'none' !== $a['info_bar_bg_image_src'] ) {
				$css .= $ib_class_name . '.content-' . $uid . ' .cp-info-bar-body-overlay {
					background: ' . $a['bg_img_color'] . ';
				}';
			} else {
				$css .= $ib_class_name . '.content-' . $uid . ' .cp-info-bar-body-overlay {
					background: ' . $a['bg_color'] . ';
				}';
			}
		}

		if ( ! isset( $a['info_bar_bg_image_src'] ) ) {
			$a['info_bar_bg_image_src'] = 'upload_img';
		}

		if ( isset( $a['info_bar_bg_image_src'] ) && ! empty( $a['info_bar_bg_image_src'] ) ) {
			if ( 'custom_url' === $a['info_bar_bg_image_src'] ) {
				$info_bar_bg_image = $a['info_bar_bg_image_custom_url'];
			} elseif ( 'upload_img' === $a['info_bar_bg_image_src'] ) {
				$info_bar_bg_image = apply_filters( 'cp_get_wp_image_url', $a['info_bar_bg_image'] );
			} else {
				$info_bar_bg_image = '';
			}
		}

		if ( '' !== $info_bar_bg_image ) {
			$bg_repeat  = '';
			$bg_pos     = '';
			$bg_size    = '';
			$bg_setting = '';

			if ( false !== strpos( $a['opt_bg'], '|' ) ) {
				$a['opt_bg'] = explode( '|', $a['opt_bg'] );
				$bg_repeat   = $a['opt_bg'][0];
				$bg_pos      = $a['opt_bg'][1];
				$bg_size     = $a['opt_bg'][2];
				$bg_setting .= 'background-repeat: ' . $bg_repeat . ';';
				$bg_setting .= 'background-position: ' . $bg_pos . ';';
				$bg_setting .= 'background-size: ' . $bg_size . ';';
			}
			$css .= $ib_class_name . '.content-' . $uid . ' .cp-info-bar-body {
				background: url(' . $info_bar_bg_image . ');
				' . $bg_setting . '
			}';
		} else {
			$css .= $ib_class_name . '.content-' . $uid . ' .cp-info-bar-body {
				background: ' . $a['bg_color'] . ';
			}';
		}

		$width = $a['infobar_width'] . 'px';

		$css .= $ib_class_name . '.content-' . $uid . ' .cp-ib-container {
			width: ' . $width . ';
		}';

		// append css.
		echo '<style type="text/css">' . $css . '</style>';//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if ( isset( $a['style_id'] ) ) {
			$ib_custom_id = 'cp-' . $a['style_id'];
		}

		// enable launch with css.
		$a['enable_custom_class'] = 1;
		$enable_custom_class      = (int) $a['enable_custom_class'];
		if ( $enable_custom_class ) {
			$ib_custom_class = $a['custom_class'];
			$ib_custom_class = str_replace( ' ', '', trim( $ib_custom_class ) );
			$ib_custom_class = str_replace( ',', ' ', trim( $ib_custom_class ) );
			$ib_custom_class = trim( $ib_custom_class );
		}

		if ( $enable_custom_class && false !== strpos( $ib_custom_class, 'priority_info_bar' ) ) {
			$priority_cls = 'priority_info_bar';
		}

		if ( $enable_custom_class ) {
			$ib_custom_class = trim( str_replace( 'priority_info_bar', '', $ib_custom_class ) );
		}

		$ib_custom_class .= ' cp-' . $style_id;

		$cp_settings     = get_option( 'convert_plug_settings' );
		$user_inactivity = isset( $cp_settings['user_inactivity'] ) ? $cp_settings['user_inactivity'] : '60';

		if ( $a['inactivity'] ) {
			$inactive_data = 'data-inactive-time="' . $user_inactivity . '"';
		}

		// impression disables.
		$impression_disable = ( isset( $cp_settings['cp-disable-impression'] ) ) ? $cp_settings['cp-disable-impression'] : 0;
		if ( $impression_disable ) {
			$impression_disable_class = 'cp-disabled-impression';
		}

		// scroll up to specific class.
		$enable_scroll_class  = isset( $a['enable_scroll_class'] ) ? $a['enable_scroll_class'] : '';
		$enable_custom_scroll = isset( $a['enable_custom_scroll'] ) ? $a['enable_custom_scroll'] : '';

		if ( $enable_custom_scroll ) {
			if ( '' !== $enable_scroll_class ) {
				$scroll_class = cp_get_scroll_class_init( $a['enable_scroll_class'] );
				$scroll_data  = 'data-scroll-class = "' . $scroll_class . '"';
			}
		}

		$schedule     = isset( $a['schedule'] ) ? $a['schedule'] : '';
		$is_scheduled = cp_is_module_scheduled( $schedule, $a['live'] );

		// timezone.
		$cp_settings   = get_option( 'convert_plug_settings' );
		$timezone_name = $cp_settings['cp-timezone'];
		$timezone      = cp_get_timezone_init();

		$schedular_tmz_offset = get_option( 'gmt_offset' );
		if ( '' === $schedular_tmz_offset ) {
			$schedular_tmz_offset = get_offset_by_time_zone( get_option( 'timezone_string' ) );
		}

		$el_class               = '';
		$info_bar_size_style    = '';
		$close_class            = '';
		$load_on_count          = '';
		$close_btn_on_duration  = '';
		$data_redirect          = '';
		$shadow                 = '';
		$radius                 = '';
		$ifb_toggle_btn_style   = '';
		$toggle_btn_style       = '';
		$toggle_container_class = '';
		$form_data_onsubmit     = '';
		$toggl_class_name       = '';
		$ifb_shadow             = '';
		$ifb_radius             = '';
		$ifb_ib_style           = '';
		$ifb_ib_style           = '';
		$ifb_light              = '';
		$ifb_c_normal           = '';

		$custom_class   = cp_get_custom_class_init( $a['custom_class'], $style_id );
		$ib_exit_intent = apply_filters( 'cp_has_enabled_or_disabled', $a['ib_exit_intent'] );

		if ( isset( $a['add_to_cart'] ) ) {
			$add_to_cart = apply_filters( 'cp_has_enabled_or_disabled', $a['add_to_cart'] );
		}

		$load_on_refresh = apply_filters( 'cp_has_enabled_or_disabled', $a['display_on_first_load'] );

		if ( 'disabled' === $load_on_refresh ) {
			$load_on_count = ( isset( $a['page_load_count'] ) ) ? $a['page_load_count'] : '';
		}

		if ( ! $a['autoload_on_scroll'] ) {
			$load_after_scroll = '';
		} else {
			$load_after_scroll = $a['load_after_scroll'];
		}
		$load_on_duration = '';
		if ( $a['autoload_on_duration'] ) {
			$load_on_duration = $a['load_on_duration'];
		}

		if ( isset( $a['display_close_on_duration'] ) && $a['display_close_on_duration'] && 'do_not_close' !== $a['close_info_bar'] ) {
			$close_btn_on_duration .= 'data-close-btnonload-delay=' . $a['close_btn_duration'] . ' ';
		}

		$dev_mode = 'disabled';
		if ( ! $a['developer_mode'] ) {
			$closed_cookie     = 0;
			$conversion_cookie = 0;
			$dev_mode          = 'enabled';
		} else {
			$dev_mode          = 'disabled';
			$closed_cookie     = $a['closed_cookie'];
			$conversion_cookie = $a['conversion_cookie'];
		}

		$on_success  = ( isset( $a['on_success'] ) ? $a['on_success'] : '' );
		$on_redirect = ( isset( $a['on_redirect'] ) ? $a['on_redirect'] : '' );

		if ( 'redirect' === $on_success && '' !== $a['redirect_url'] && (int) $a['redirect_data'] ) {
			$data_redirect .= 'data-redirect-lead-data="' . $a['redirect_data'] . '"';
		}

		if ( 'redirect' === $on_success && '' !== $a['redirect_url'] && '' !== $on_redirect ) {
			$data_redirect .= ' data-redirect-to ="' . $on_redirect . '" ';
		}

		$global_info_bar_settings = 'data-closed-cookie-time="' . $closed_cookie . '" data-conversion-cookie-time="' . $conversion_cookie . '" data-info_bar-id="' . $style_id . '" data-info_bar-style="' . $style_id . '" data-entry-animation="' . $a['entry_animation'] . '" data-exit-animation="' . $a['exit_animation'] . '" data-option="smile_info_bar_styles"' . $inactive_data . ' ' . $scroll_data;
		$style_type               = $style_details['type'];
		$parent_style             = $style_details['parent_style'];

		if ( '' !== $parent_style ) {
			$global_info_bar_settings .= ' data-parent-style="' . $parent_style . '" ';
		}

		$global_class = 'global_info_bar_container';

		if ( $a['fix_position'] ) {
			$global_class .= ' ib-fixed';
		}

		// Apply box shadow to submit button - If its set & equals to - 1.
		if ( isset( $a['btn_shadow'] ) && '' !== $a['btn_shadow'] ) {
			$shadow .= 'box-shadow: 1px 1px 2px 0px rgba(66, 66, 66, 0.6);';
		}

		// Add - border-radius.
		if ( isset( $a['btn_border_radius'] ) && '' !== $a['btn_border_radius'] ) {
			$radius .= 'border-radius: ' . $a['btn_border_radius'] . 'px;';
		}

		// Disable toggle button if button link is 'do_not_close'.
		if ( 'do_not_close' === $a['close_info_bar'] ) {
			$a['toggle_btn'] = 0;
		}

		// toggle btn css.
		$toggle_normal              = ( isset( $a['toggle_button_bg_color'] ) ) ? $a['toggle_button_bg_color'] : '';
		$toggle_hover               = ( isset( $a['toggle_button_bg_hover_color'] ) ) ? $a['toggle_button_bg_hover_color'] : '';
		$toggle_light               = ( isset( $a['toggle_button_bg_gradient_color'] ) ) ? $a['toggle_button_bg_gradient_color'] : '';
		$toggle_text_color          = ( isset( $a['toggle_button_text_color'] ) ) ? $a['toggle_button_text_color'] : '';
		$toggle_btn_font_size       = ( isset( $a['toggle_btn_font_size'] ) ) ? $a['toggle_btn_font_size'] : '';
		$toggle_btn_border_radius   = ( isset( $a['toggle_btn_border_radius'] ) ) ? $a['toggle_btn_border_radius'] : '';
		$toggle_btn_border_size     = ( isset( $a['toggle_btn_border_size'] ) ) ? $a['toggle_btn_border_size'] : '';
		$toggle_button_border_color = ( isset( $a['toggle_button_border_color'] ) ) ? $a['toggle_button_border_color'] : '';
		$toggle_btn_padding_lrv     = ( isset( $a['toggle_btn_padding_lrv'] ) ) ? $a['toggle_btn_padding_lrv'] : '';
		$toggle_btn_padding_tb      = ( isset( $a['toggle_btn_padding_tb'] ) ) ? $a['toggle_btn_padding_tb'] : '';

		if ( '1' === $a['toggle_btn_gradient'] ) {
			$toggle_btn_style = 'cp-btn-gradient';
		} else {
			$toggle_btn_style = 'cp-btn-flat';
		}

		$ifb_toggle_btn_style .= $ib_class_name . '.content-' . $uid . ' .' . $toggle_btn_style . '.cp-ifb-toggle-btn{
			font-size: ' . $toggle_btn_font_size . 'px;
			border-radius:' . $toggle_btn_border_radius . 'px;
			border-width:' . $toggle_btn_border_size . 'px;
			border-color:' . $toggle_button_border_color . ';
			padding-left:' . $toggle_btn_padding_lrv . 'px;
			padding-right:' . $toggle_btn_padding_lrv . 'px;
			padding-top:' . $toggle_btn_padding_tb . 'px;
			padding-bottom:' . $toggle_btn_padding_tb . 'px;
			border-color:' . $toggle_button_border_color . ';
		} ';

		switch ( $toggle_btn_style ) {
			case 'cp-btn-flat':
				$ifb_toggle_btn_style .= $ib_class_name . '.content-' . $uid . ' .' . $toggle_btn_style . '.cp-ifb-toggle-btn{ background: ' . $toggle_normal . '!important; color:' . $toggle_text_color . '; } '
				. $ib_class_name . '.content-' . $uid . '  .' . $toggle_btn_style . '.cp-ifb-toggle-btn:hover { background: ' . $toggle_hover . '!important; } ';
				break;

			case 'cp-btn-gradient':     // Apply box $shadow to submit button - If its set & equals to - 1.
				$ifb_toggle_btn_style .= $ib_class_name . '.content-' . $uid . ' .' . $toggle_btn_style . '.cp-ifb-toggle-btn {'
				. '     background: -webkit-linear-gradient(' . $toggle_light . ', ' . $toggle_normal . ') !important;'
				. '     background: -o-linear-gradient(' . $toggle_light . ', ' . $toggle_normal . ') !important;'
				. '     background: -moz-linear-gradient(' . $toggle_light . ', ' . $toggle_normal . ') !important;'
				. '     background: linear-gradient(' . $toggle_light . ', ' . $toggle_normal . ') !important;'
				. '     color:' . $toggle_text_color . '; }'
				. $ib_class_name . '.content-' . $uid . ' .' . $toggle_btn_style . '.cp-ifb-toggle-btn:hover {'
				. '     background: ' . $toggle_normal . ' !important;'
				. '}';
				break;
		}
		echo '<style class="cp-toggle-btn" type="text/css">' . $ifb_toggle_btn_style . '</style>';//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		// for second button.
		// Apply box ifb_shadow to submit button - If its set & equals to - 1.
		if ( isset( $a['ifb_btn_shadow'] ) && '' !== $a['ifb_btn_shadow'] ) {
			$ifb_shadow .= 'box-shadow: 1px 1px 2px 0px rgba(66, 66, 66, 0.6);';
		}
		// Add - border-radius.
		if ( isset( $a['ifb_btn_border_radius'] ) && '' !== $a['ifb_btn_border_radius'] ) {
			$ifb_radius .= 'border-radius: ' . $a['ifb_btn_border_radius'] . 'px;';
		}

		if ( isset( $a['ifb_btn_style'] ) && '' !== $a['ifb_btn_style'] ) {
			$ifb_c_normal = $a['ifb_button_bg_color'];
			$ifb_c_hover  = $a['ifb_btn_darken'];
			$ifb_light    = $a['ifb_btn_gradiant'];

			switch ( $a['ifb_btn_style'] ) {
				case 'cp-btn-flat':
					$ifb_ib_style .= $ib_class_name . '.content-' . $uid . ' .' . $a['ifb_btn_style'] . '.cp-second-submit-btn{ background: ' . $ifb_c_normal . '!important;' . $ifb_shadow . ';' . $ifb_radius . ' } '
					. $ib_class_name . '.content-' . $uid . '  .' . $a['ifb_btn_style'] . '.cp-second-submit-btn:hover { background: ' . $ifb_c_hover . '!important; } ';
					break;
				case 'cp-btn-3d':
					$ifb_ib_style .= $ib_class_name . '.content-' . $uid . ' .' . $a['ifb_btn_style'] . '.cp-second-submit-btn {background: ' . $ifb_c_normal . '!important; ' . $ifb_radius . ' position: relative ; box-shadow: 0 6px ' . $ifb_c_hover . ';} '
					. $ib_class_name . '.content-' . $uid . ' .' . $a['ifb_btn_style'] . '.cp-second-submit-btn:hover {background: ' . $ifb_c_normal . '!important;top: 2px; box-shadow: 0 4px ' . $ifb_c_hover . ';} '
					. $ib_class_name . '.content-' . $uid . ' .' . $a['ifb_btn_style'] . '.cp-second-submit-btn:active {background: ' . $ifb_c_normal . '!important;top: 6px; box-shadow: 0 0px ' . $ifb_c_hover . ';} ';
					break;
				case 'cp-btn-outline':
					$ifb_ib_style .= $ib_class_name . '.content-' . $uid . ' .' . $a['ifb_btn_style'] . '.cp-second-submit-btn { background: transparent!important;border: 2px solid ' . $ifb_c_normal . ';color: inherit ;' . $ifb_shadow . $ifb_radius . '}'
					. $ib_class_name . '.content-' . $uid . ' .' . $a['ifb_btn_style'] . '.cp-second-submit-btn:hover { background: ' . $ifb_c_hover . '!important;border: 2px solid ' . $ifb_c_hover . ';color: ' . $a['ifb_button_txt_hover_color'] . ' ;}'
					. $ib_class_name . '.content-' . $uid . ' .' . $a['ifb_btn_style'] . '.cp-second-submit-btn:hover span { color: inherit !important ; } ';
					break;
				case 'cp-btn-gradient':     // Apply box $ifb_shadow to submit button - If its set & equals to - 1.
					$ifb_ib_style .= $ib_class_name . '.content-' . $uid . ' .' . $a['ifb_btn_style'] . '.cp-second-submit-btn {'
					. '     border: none ;'
					. $ifb_shadow . $ifb_radius
					. '     background: -webkit-linear-gradient(' . $ifb_light . ', ' . $ifb_c_normal . ') !important;'
					. '     background: -o-linear-gradient(' . $ifb_light . ', ' . $ifb_c_normal . ') !important;'
					. '     background: -moz-linear-gradient(' . $ifb_light . ', ' . $ifb_c_normal . ') !important;'
					. '     background: linear-gradient(' . $ifb_light . ', ' . $ifb_c_normal . ') !important;'
					. '}'
					. $ib_class_name . '.content-' . $uid . ' .' . $a['ifb_btn_style'] . '.cp-second-submit-btn:hover {'
					. '     background: ' . $ifb_c_normal . ' !important;'
					. '}';
					break;
			}
		}

		echo '<style class="cp-ifb-second_submit" type="text/css">' . $ifb_ib_style . '</style>';//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		ob_start();
		$data_debug           = get_option( 'convert_plug_debug' );
		$push_page_input      = isset( $data_debug['push-page-input'] ) ? $data_debug['push-page-input'] : '';
		$top_offset_container = isset( $data_debug['top-offset-container'] ) ? $data_debug['top-offset-container'] : '';

		$ib_close_class = 'ib-close-outside';

		if ( 0 === $a['close_info_bar_pos'] ) {
			$ib_close_class = 'ib-close-inline';
		}

		// check if info bar should be triggered after post.
		$enable_after_post = (int) ( isset( $a['enable_after_post'] ) ? $a['enable_after_post'] : 0 );
		if ( $enable_after_post ) {
			$global_class .= ' ib-after-post';
		}

		// check if modal should be triggerd if items in the cart.
		$add_to_cart = (int) ( isset( $a['add_to_cart'] ) ? $a['add_to_cart'] : 0 );

		if ( $add_to_cart ) {
			$ib_custom_class .= ' cp-items-in-cart';
		}

		// check if inline display is set.
		$cp_info_bar_class .= ' cp-info-bar';
		$is_inline          = ( isset( $a['display'] ) && 'inline' === $a['display'] ) ? true : false;
		$cp_close_inline    = (int) ( isset( $cp_settings['cp-close-inline'] ) ? $cp_settings['cp-close-inline'] : 0 );
		$close_inline       = ( $cp_close_inline ) ? 'cp-close-inline' : 'cp-do-not-close-inline';

		if ( $is_inline ) {
			$cp_info_bar_class   .= ' cp-info-bar-inline ' . $close_inline;
			$cp_info_bar_class   .= ' content-' . $style_id;
			$a['entry_animation'] = '';
			$a['exit_animation']  = '';
		}

		// Enable animation initially.
		if ( ! $is_inline && '1' === $a['toggle_btn'] ) {
			$toggl_class_name = 'cp-ifb-with-toggle';
		}

		if ( ! $is_inline && isset( $a['toggle_btn_visible'] ) && '1' === $a['toggle_btn_visible'] && '1' === $a['toggle_btn'] ) {
			$cp_info_bar_class .= ' cp-ifb-hide';
		} else {
			$toggle_container_class = ' smile-animated ' . $a['entry_animation'];
		}

		$toggle_container_class .= ' ' . $toggl_class_name;

		if ( ( isset( $a['manual'] ) && 'true' === $a['manual'] ) || ( isset( $a['display'] ) && 'inline' === $a['display'] ) ) {
			$ib_onload = '';
		} else {
			$ib_onload = 'cp-ib-onload cp-global-load';
		}

		// Is InfoBar InLine.
		$is_inline            = ( isset( $a['display'] ) && 'inline' === $a['display'] ) ? true : false;
		$after_content_scroll = isset( $data_debug['after_content_scroll'] ) ? $data_debug['after_content_scroll'] : '50';
		$after_content_data   = 'data-after-content-value="' . $after_content_scroll . '"';
		$always_visible       = ( ( isset( $a['toggle_btn'] ) && '1' === $a['toggle_btn'] ) && ( isset( $a['toggle_btn_visible'] ) && '1' === $a['toggle_btn_visible'] ) ) ? 'data-toggle-visible=true' : '';

		// form display/hide after sucessfull submission.
		$form_action_onsubmit = isset( $a['form_action_on_submit'] ) ? $a['form_action_on_submit'] : '';

		if ( 'reappear' === $form_action_onsubmit ) {
			$form_data_onsubmit .= 'data-form-action = reappear';
			$form_data_onsubmit .= ' data-form-action-time =' . $a['form_reappear_time'];
		} elseif ( 'disappears' === $form_action_onsubmit ) {
			$form_data_onsubmit .= 'data-form-action = disappear';
			$form_data_onsubmit .= ' data-form-action-time =' . $a['form_disappears_time'];
		}

		// Custom selector.
		$custom_selector = '';
		$custom_selector = isset( $a['custom_selector'] ) ? cp_get_custom_slector_init( $a['custom_selector'] ) : '';
		if ( '' !== $custom_selector ) {
			$ib_custom_class .= ' ' . cp_get_custom_slector_class_init( $a['custom_selector'] );
		}
		// set data variables and class name for ifb_onload.
		$ifb_inline_cls = ( ! $is_inline ) ? ' content-' . $uid . ' ' . $style_id . ' ' . $ib_custom_class : '';
		$class_ifb      = '';
		$class_ifb     .= 'cp-module cp-info-bar-container cp-clear';
		$class_ifb     .= ' ' . esc_attr( $cp_info_bar_class ) . ' ';
		$class_ifb     .= ' ' . esc_attr( $a['style_class'] ) . ' ';
		$class_ifb     .= ' ' . esc_attr( $ib_onload ) . '';
		$class_ifb     .= ' ' . esc_attr( $a['infobar_position'] ) . ' ';
		$class_ifb     .= ' ' . esc_attr( $global_class ) . '';
		$class_ifb     .= ' ' . esc_attr( $toggle_container_class ) . ' ';
		$class_ifb     .= ' ' . esc_attr( $impression_disable_class ) . ' ';
		$class_ifb     .= ' ' . esc_attr( $ifb_inline_cls ) . ' ';

		$custom_ifb_cls = '';
		$custom_ifb_cls = ( ! $is_inline ) ? ' data-custom-class="' . esc_attr( $ib_custom_class ) . '"' : '';

		$infobar_global_data  = '';
		$infobar_global_data .= 'data-module-type="info-bar" ';
		$infobar_global_data .= 'data-toggle = "' . $a['toggle_btn'] . '" ';
		$infobar_global_data .= 'data-tz-offset = "' . $schedular_tmz_offset . '" ';
		$infobar_global_data .= 'data-dev-mode = "' . esc_attr( $dev_mode ) . '" ';
		$infobar_global_data .= ' data-exit-intent = "' . esc_attr( $ib_exit_intent ) . '" ';
		$infobar_global_data .= 'data-add-to-cart = "' . esc_attr( $add_to_cart ) . '"';

		$infobar_global_data .= 'data-onscroll-value = "' . esc_attr( $load_after_scroll ) . '" ';
		$infobar_global_data .= 'data-onload-delay = "' . esc_attr( $load_on_duration ) . '" ';
		$infobar_global_data .= 'data-timezonename = "' . esc_attr( $timezone_name ) . '" ';
		$infobar_global_data .= 'data-timezone = "' . esc_attr( $timezone ) . '" ';
		$infobar_global_data .= 'data-load-on-count = "' . esc_attr( $load_on_count ) . '"';
		$infobar_global_data .= 'data-load-on-refresh = "' . esc_attr( $load_on_refresh ) . '" ';
		$infobar_global_data .= 'data-push-down = "' . esc_attr( $page_down ) . '" ';
		$infobar_global_data .= 'data-animate-push-page = "' . esc_attr( $a['animate_push_page'] ) . '" ';
		$infobar_global_data .= 'data-class = "content-' . $uid . ' " ';
		$infobar_global_data .= $global_info_bar_settings . '  ';
		$infobar_global_data .= $is_scheduled . '  ';
		$infobar_global_data .= $data_redirect . '  ';
		$infobar_global_data .= $always_visible . ' ';
		$infobar_global_data .= esc_attr( $close_btn_on_duration ) . '  ';
		$infobar_global_data .= $autoclose_data . ' ';
		$infobar_global_data .= esc_attr( $form_data_onsubmit ) . '  ';
		$infobar_global_data .= $custom_ifb_cls . ' ';
		$infobar_global_data .= $referrer_data . '  ';
		$infobar_global_data .= $after_content_data . '  ';
		$infobar_global_data .= 'id = "' . esc_attr( $ib_custom_id ) . ' " ';
		$infobar_global_data .= 'data-custom-selector = "' . esc_attr( $custom_selector ) . '"';

		$custom_css_class = isset( $a['custom_css_class'] ) ? $a['custom_css_class'] : '';

		// Close gravity form after submission or not?
		$cp_close_gravity = isset( $convert_plug_settings['cp-close-gravity'] ) ? $convert_plug_settings['cp-close-gravity'] : 1;
		$gr_data          = '';
		$gr_data          = 'data-close-gravity = "' . esc_attr( $cp_close_gravity ) . '"';

		?>

		<input type="hidden" id="cp-push-down-support" value="<?php echo esc_attr( $push_page_input ); ?>">
		<input type="hidden" id="cp-top-offset-container" value="<?php echo esc_attr( $top_offset_container ); ?>">

		<div <?php echo wp_kses_post( $infobar_global_data ); ?> class="
			<?php
			echo esc_attr( $class_ifb );
			echo esc_attr( $custom_css_class );
			?>
		" style="min-height:<?php echo esc_attr( $a['infobar_height'] ); ?>px;" data-style-id ="<?php echo esc_attr( $style_id ); ?>" data-module-name ="infobar" <?php echo esc_attr( $gr_data ); ?>>
			<div class="cp-info-bar-wrapper cp-clear">
				<div class="cp-info-bar-body-overlay"></div>
				<div class="cp-flex cp-info-bar-body <?php echo esc_attr( $ib_close_class ); ?>" style="min-height:<?php echo esc_attr( $a['infobar_height'] ); ?>px;" data-height=''>
					<div class="cp-flex cp-ib-container">
						<?php
						// add nounce field to modal.
						$nonce = wp_create_nonce( 'cp-impress-nonce' );
						?>
						<input type="hidden" class="cp-impress-nonce" name="cp-impress-nonce" value="<?php echo esc_attr( $nonce ); ?>">

						<?php
	}
}
				add_filter( 'cp_ib_global_before', 'cp_ib_global_before_init' );

if ( ! function_exists( 'cp_ib_global_after_init' ) ) {
	/**
	 * Function Name:cp_ib_global_after_initInfo Bar After
	 *
	 * @param  array $a array parameter.
	 * @since 0.2.3
	 */
	function cp_ib_global_after_init( $a ) {

		$toggle_class          = '';
		$toggle_btn_style      = '';
		$ib_close_html         = '';
		$ib_close_class        = '';
		$close_img_class       = '';
		$img_src               = '';
		$edit_link             = '';
		$font_family           = '';
		$close_alt             = '';
		$close_img_alt         = '';
		$style_id              = ( isset( $a['style_id'] ) ) ? $a['style_id'] : '';
		$close_alt             = 'close-link';
		$convert_plug_settings = get_option( 'convert_plug_settings' );
		$images_on_load        = isset( $convert_plug_settings['cp-lazy-img'] ) ? $convert_plug_settings['cp-lazy-img'] : 0;

		if ( is_user_logged_in() ) {
			// if user has access to ConvertPlug, then only display edit style link.
			if ( current_user_can( 'access_cp' ) ) {
				if ( isset( $a['style_id'] ) ) {
					$edit_link = cp_get_edit_link( $a['style_id'], 'info_bar', $a['style'] );
				}
			}
		}

		if ( ! isset( $a['close_ib_image_src'] ) ) {
			$a['close_ib_image_src'] = 'upload_img';
		}

		if ( 'close_img' === $a['close_info_bar'] ) {
			if ( 'upload_img' === $a['close_ib_image_src'] ) {
				if ( false !== strpos( $a['close_img'], 'http' ) ) {
					$close_img_class = 'ib-img-default';
				}
				$img_src       = cp_get_ib_image_url( $a['close_img'] );
				$img_src       = cp_get_protocol_settings_init( $img_src );
				$close_img_alt = explode( '|', $a['close_img'] );
				$cnt_arr       = count( $close_img_alt );
				if ( $cnt_arr > 2 ) {
					if ( '' !== $close_img_alt[2] ) {
						$close_alt = 'alt=' . $close_img_alt[2] . '';
					}
				} else {
					$close_alt = 'alt=close-image';
				}
			} elseif ( 'custom_url' === $a['close_ib_image_src'] ) {
				$img_src = $a['info_bar_close_img_custom_url'];
			} elseif ( 'pre_icons' === $a['close_ib_image_src'] ) {
				$icon_url = CP_PLUGIN_URL . 'modules/assets/images/' . $a['close_icon'] . '.png';
				$img_src  = $icon_url;
				$img_src  = cp_get_protocol_settings_init( $img_src );
			}

			$close_img_height = isset( $a['close_img_height'] ) ? $a['close_img_height'] : '';
			$close_img_width  = isset( $a['close_img_width'] ) ? $a['close_img_width'] : '';

			if ( $images_on_load ) {
				$ib_close_html = '<img data-src ="' . esc_url( $img_src ) . '" class="cp-close-img ' . esc_attr( $close_img_class ) . '" ' . esc_attr( $close_alt ) . ' width ="' . esc_attr( $close_img_width ) . '" height ="' . esc_attr( $close_img_height ) . '" >';
			} else {
				$ib_close_html = '<img src="' . esc_url( $img_src ) . '" class="' . esc_attr( $close_img_class ) . '" ' . esc_attr( $close_alt ) . ' width ="' . esc_attr( $close_img_width ) . '" height ="' . esc_attr( $close_img_height ) . '" >';
			}
			$ib_close_class = 'ib-img-close';
			$ib_img_width   = 'width:' . esc_attr( $close_img_width ) . 'px;';
		} else {
			if ( isset( $a['close_text_font'] ) && '' !== $a['close_text_font'] ) {
				$font_family = ' font-family:' . $a['close_text_font'];
			}
			$ib_close_html  = '<span style="color:' . $a['close_text_color'] . ';' . $font_family . '">' . $a['close_txt'] . '</span>';
			$ib_close_class = 'ib-text-close';
			$ib_img_width   = '';
		}

		if ( isset( $a['display_close_on_duration'] ) && $a['display_close_on_duration']
			&& 'do_not_close' !== $a['close_info_bar'] ) {
			if ( '1' !== $a['toggle_btn_visible'] ) {
				$ib_close_class .= ' cp-hide-close';
			} else {
				if ( isset( $a['toggle_btn'] ) && '0' === $a['toggle_btn'] && '1' === $a['toggle_btn_visible'] ) {
					$ib_close_class .= ' cp-hide-close';
				}
			}
		}

		// toggle settings.
		// Disable toggle button if button link is 'do_not_close'.
		if ( 'do_not_close' === $a['close_info_bar'] ) {
			$a['toggle_btn'] = 0;
		}

		if ( isset( $a['toggle_btn_visible'] ) && '1' !== $a['toggle_btn_visible'] ) {
			$toggle_class = 'cp-ifb-hide';
		} elseif ( isset( $a['toggle_btn_visible'] ) && '1' === $a['toggle_btn_visible'] && '1' === $a['toggle_btn'] ) {
			$ifb_toggle_btn_anim = 'smile-slideInUp';
			if ( 'cp-pos-top' === $a['infobar_position'] ) {
				$ifb_toggle_btn_anim = 'smile-slideInDown';
			}
			$toggle_btn_style .= 'cp-ifb-show smile-animated ' . $ifb_toggle_btn_anim;
		}

		if ( '1' === $a['toggle_btn_gradient'] ) {
			$toggle_btn_style .= ' cp-btn-gradient';
		} else {
			$toggle_btn_style .= ' cp-btn-flat';
		}

		// Is InfoBar InLine.
		$is_inline = ( isset( $a['display'] ) && 'inline' === $a['display'] ) ? true : false;

		?>
	</div><!-- cp-ib-container -->
		<?php
		$close_adj_class         = '';
		$close_adjacent_position = ( isset( $a['adjacent_close_position'] ) ? $a['adjacent_close_position'] : 'cp-adjacent-right' );
		$close_adj_class        .= cp_get_close_adj_position( $close_adjacent_position );

		if ( ! $is_inline && '0' === $a['close_info_bar_pos'] && 'do_not_close' !== $a['close_info_bar'] ) {
			?>
		<div class="ib-close <?php echo esc_attr( $ib_close_class ); ?> <?php echo esc_attr( $close_adj_class ); ?>" style=" <?php echo esc_attr( $ib_img_width ); ?>"><?php echo do_shortcode( $ib_close_html ); ?></div>
		<?php } ?>
	</div><!-- cp-info-bar-body -->

		<?php
		if ( '' !== $edit_link ) {
			$edit_link_text = 'Edit With ' . CP_PLUS_SLUG;

			$edit_link_txt = apply_filters( 'cp_style_edit_link_text', $edit_link_text );

			echo "<div class='cp_edit_link'><a rel='noopener' target='_blank' href=" . esc_url( $edit_link ) . '>' . esc_attr( $edit_link_txt ) . '</a></div>';
		}
		?>

</div>
<!--toggle button-->
		<?php if ( ! $is_inline && '1' === $a['toggle_btn'] ) { ?>
<div class="cp-ifb-toggle-btn <?php echo esc_attr( $toggle_class . ' ' . $toggle_btn_style ); ?> "><?php echo do_shortcode( $a['toggle_button_title'] ); ?></div>
		<?php } ?>
		<?php
		if ( ! $is_inline && '1' === $a['close_info_bar_pos'] && 'do_not_close' !== $a['close_info_bar'] ) {
			?>
	<div class="ib-close  <?php echo esc_attr( $ib_close_class ); ?> <?php echo esc_attr( $close_adj_class ); ?>" style=" <?php echo esc_attr( $ib_img_width ); ?>"><?php echo do_shortcode( $ib_close_html ); ?></div>
		<?php } ?>

		<?php if ( $is_inline ) { ?>
	<span class="cp-info_bar-inline-end" data-style="<?php echo esc_attr( $style_id ); ?>"></span>
			<?php
		}

		$msg_color = isset( $a['message_color'] ) ? $a['message_color'] : '';
			// Disable loading for ONLY BUTTON.
		if ( isset( $a['mailer'] ) && '' !== $a['mailer'] && 'cp-form-layout-4' !== $a['form_layout'] ) {
			?>
	<div class="cp-form-processing-wrap" style="position: absolute; display:none; ">
		<div class="cp-form-after-submit" style="line-height:<?php echo esc_attr( $a['infobar_height'] ); ?>px;">
			<div class ="cp-form-processing">               
				<?php
				$form_css = 'width: 100px';
				echo wp_kses_post( cp_get_form_process_html( $form_css ) );
				?>
			</div>
			<div class ="cp-msg-on-submit" style="color:<?php echo esc_attr( $msg_color ); ?>"></div>
		</div>
	</div>

		<?php } ?>
</div>
		<?php
	}
}

add_filter( 'cp_ib_global_after', 'cp_ib_global_after_init' );
