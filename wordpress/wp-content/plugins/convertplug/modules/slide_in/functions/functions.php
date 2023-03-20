<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

if ( ! function_exists( 'slidein_generate_style_css' ) ) {
	/**
	 * Function Name:slidein_generate_style_css.
	 *
	 * @param  string $custom_css  string paremeter.
	 */
	function slidein_generate_style_css( $custom_css ) {
		echo '<style type="text/css" id="">' . $custom_css . '</style>'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}

if ( ! function_exists( 'cp_has_overaly_setting_init' ) ) {
	/**
	 * Function Name: cp_has_overaly_setting_init Check slidein overlay settings
	 *
	 * @param  string $overlay_effect         string paremeter.
	 * @param  string $disable_overlay_effect string paremeter.
	 * @param  string $hide_animation_width   string paremeter.
	 * @return string                         string paremeter.
	 * @since 0.1.5
	 */
	function cp_has_overaly_setting_init( $overlay_effect, $disable_overlay_effect, $hide_animation_width ) {
		$op = ' data-overlay-animation = "' . $overlay_effect . '" ';
		if ( '1' === $disable_overlay_effect ) {
			$op .= ' data-disable-animationwidth="' . $hide_animation_width . '" ';
		}
		return $op;
	}
}
add_filter( 'cp_has_overaly_setting', 'cp_has_overaly_setting_init' );

if ( ! function_exists( 'cp_slidein_global_settings_init' ) ) {
	/**
	 * Function Name: cp_slidein_global_settings_init Global Settings - SlideIn.
	 *
	 * @param  string $closed_cookie      string paremeter.
	 * @param  string $conversion_cookie  string paremeter.
	 * @param  string $style_id           string paremeter.
	 * @param  string $style_details      string paremeter.
	 * @return string                     string paremeter.
	 * @since 0.1.5
	 */
	function cp_slidein_global_settings_init( $closed_cookie, $conversion_cookie, $style_id, $style_details ) {

		$parent_style = $style_details['parent_style'];

		$op  = ' data-closed-cookie-time="' . $closed_cookie . '"';
		$op .= ' data-conversion-cookie-time="' . $conversion_cookie . '" ';
		$op .= ' data-slidein-id="' . $style_id . '" ';

		if ( '' !== $parent_style ) {
			$op .= ' data-parent-style="' . $parent_style . '" ';
		}

		$op .= ' data-slidein-style="' . $style_id . '" ';
		$op .= ' data-option="smile_slide_in_styles" ';
		return $op;
	}
}
add_filter( 'cp_slidein_global_settings', 'cp_slidein_global_settings_init' );

if ( ! function_exists( 'cp_slidein_global_before_init' ) ) {
	/**
	 * Function Name:cp_slidein_global_before_init SlideIn Before.
	 *
	 * @param  array $a array parameter.
	 */
	function cp_slidein_global_before_init( $a ) {

		$style_id                 = ( isset( $a['style_id'] ) ) ? $a['style_id'] : '';
		$style_type               = '';
		$referrer_data            = '';
		$autoclose_data           = '';
		$bg_repeat                = '';
		$bg_pos                   = '';
		$bg_size                  = '';
		$bg_setting               = '';
		$close_img_class          = '';
		$close_img                = '';
		$timezone                 = '';
		$el_class                 = '';
		$slide_bg_style           = '';
		$close_html               = '';
		$slidein_size_style       = '';
		$close_class              = '';
		$font                     = '';
		$impression_disable_class = '';
		$scroll_data              = '';
		$scroll_class             = '';
		$inactive_data            = '';
		$data_redirect            = '';
		$overlay_effect           = '';
		$slideshadow              = '';
		$slideradius              = '';
		$placeholder_font         = '';
		$hide_image               = '';
		$load_on_count            = '';
		$slide_position           = '';
		$load_after_scroll        = '';
		$load_on_duration         = '';
		$close_btn_on_duration    = '';
		$form_data_onsubmit       = '';
		$optin_widgetclass        = '';
		$cp_close_body            = '';
		$inline_text              = '';
		$toggleclass              = '';
		$slide_toggle_class       = '';
		$slide_in_bg_image        = '';
		$customcss                = '';
		$inset                    = '';
		$css_style                = '';
		$style_details            = get_style_details( $style_id, 'slide_in' );
		$custom_class             = isset( $a['custom_css'] ) ? $a['custom_css'] : '';
		$a['image_resp_width']    = '768';
		$convert_plug_settings    = get_option( 'convert_plug_settings' );
		$images_on_load           = isset( $convert_plug_settings['cp-lazy-img'] ) ? $convert_plug_settings['cp-lazy-img'] : 0;
		// Print CSS of the style.
		if ( '' !== $custom_class ) {
			slidein_generate_style_css( $custom_class );
		}

		$hide_from = isset( $a['hide_from'] ) ? $a['hide_from'] : '';

		// check referrer detection.
		$referrer_check  = ( isset( $a['enable_referrer'] ) && (int) $a['enable_referrer'] ) ? 'display' : 'hide';
		$referrer_domain = ( 'display' === $referrer_check ) ? $a['display_to'] : $hide_from;

		if ( '' !== $referrer_check ) {
			$referrer_data  = 'data-referrer-domain="' . $referrer_domain . '"';
			$referrer_data .= ' data-referrer-check="' . $referrer_check . '"';
		}

		// check close after few second.
		$autoclose_on_duration = ( isset( $a['autoclose_on_duration'] ) && (int) $a['autoclose_on_duration'] ) ? $a['autoclose_on_duration'] : '';
		$close_module_duration = ( isset( $a['close_module_duration'] ) && (int) $a['close_module_duration'] ) ? $a['close_module_duration'] : '';

		// check if inline display is set.
		$is_inline = ( isset( $a['display'] ) && 'inline' === $a['display'] ) ? true : false;

		if ( '' !== $autoclose_on_duration && ! $is_inline && ( isset( $a['toggle_btn'] ) && '1' !== $a['toggle_btn'] ) && ( isset( $a['close_slidein'] ) && 'do_not_close' !== $a['close_slidein'] ) ) {
			$autoclose_data = 'data-close-after = "' . $close_module_duration . '"';
		}

		// Enqueue Google Fonts.
		if ( isset( $a['cp_google_fonts'] ) ) {
			cp_enqueue_google_fonts( $a['cp_google_fonts'] );
		}

		if ( isset( $a['opt_bg'] ) && false !== strpos( $a['opt_bg'], '|' ) ) {
			$opt_bg      = explode( '|', $a['opt_bg'] );
			$bg_repeat   = $opt_bg[0];
			$bg_pos      = $opt_bg[1];
			$bg_size     = $opt_bg[2];
			$bg_setting .= 'background-repeat: ' . $bg_repeat . ';';
			$bg_setting .= 'background-position: ' . $bg_pos . ';';
			$bg_setting .= 'background-size: ' . $bg_size . ';';
		}

		// Time Zone.
		$cp_settings   = get_option( 'convert_plug_settings' );
		$timezone_name = $cp_settings['cp-timezone'];
		$timezone      = cp_get_timezone_init();

		// SlideIn - Padding.
		if ( isset( $a['style'] ) && 'floating_social_bar' === $a['style'] ) {
			$a['content_padding'] = 1;
			if ( 'center-left' === $a['slidein_position'] ) {
				$a['overlay_effect'] = 'smile-slideInLeft';
				$a['exit_animation'] = 'smile-slideOutLeft';
			} else {
				$a['overlay_effect'] = 'smile-slideInRight';
				$a['exit_animation'] = 'smile-slideOutRight';
			}
		}

		if ( isset( $a['content_padding'] ) && ! empty( $a['content_padding'] ) ) {
			$el_class .= ' cp-no-padding ';
		}

		// SlideIn - Background Image & Background Color.
		$slidein_bg_color      = ( isset( $a['slidein_bg_color'] ) ) ? $a['slidein_bg_color'] : '';
		$slidein_bg_img_color  = ( isset( $a['slidein_bg_img_color'] ) ) ? $a['slidein_bg_img_color'] : '';
		$slide_in_bg_image_src = isset( $a['slide_in_bg_image_src'] ) ? $a['slide_in_bg_image_src'] : '';

		if ( 'upload_img' === $a['slide_in_bg_image_src'] ) {
			if ( isset( $a['slide_in_bg_image'] ) && ! empty( $a['slide_in_bg_image'] ) ) {
				$slide_in_bg_image = apply_filters( 'cp_get_wp_image_url', $a['slide_in_bg_image'] );
			}
		} elseif ( 'custom_url' === $a['slide_in_bg_image_src'] ) {
			$slide_in_bg_image = $a['slide_in_bg_image_custom_url'];
		}

		// Variables.
		$uid = ( isset( $a['uid'] ) && '' != $a['uid'] ) ? $a['uid'] : '';

		// Background - (Background Color / Gradient).
		$slidein_bg_color = isset( $a['slidein_bg_color'] ) ? $a['slidein_bg_color'] : '';
		if ( isset( $a['slidein_bg_gradient'] ) && '' !== $a['slidein_bg_gradient'] && '1' === $a['slidein_bg_gradient'] ) {
			$grad_css        = '';
			$module_gradient = isset( $a['module_bg_gradient'] ) ? $a['module_bg_gradient'] : '';
			if ( '' !== $module_gradient ) {
				$grad_css        = generate_back_gradient( $module_gradient );
				$slide_bg_style .= '.slidein-overlay.content-' . $uid . ' .cp-slidein-body-overlay {' . $grad_css . '}';
			} else {
				$slide_bg_style .= '.slidein-overlay.content-' . $uid . ' .cp-slidein-body-overlay {
					background: -webkit-linear-gradient(' . $a['slidein_bg_gradient_lighten'] . ', ' . $slidein_bg_color . ');
					background: -o-linear-gradient(' . $a['slidein_bg_gradient_lighten'] . ', ' . $slidein_bg_color . ');
					background: -moz-linear-gradient(' . $a['slidein_bg_gradient_lighten'] . ', ' . $slidein_bg_color . ');
					background: linear-gradient(' . $a['slidein_bg_gradient_lighten'] . ', ' . $slidein_bg_color . ');
				}';
			}
		} else {
			if ( 'none' === $slide_in_bg_image_src ) {
				$slide_bg_style .= '.slidein-overlay.content-' . $uid . ' .cp-slidein-body-overlay {
					background: ' . $slidein_bg_color . ';
				}';
			} else {
				$slide_bg_style .= '.slidein-overlay.content-' . $uid . ' .cp-slidein-body-overlay {
					background: ' . $slidein_bg_img_color . ';
				}';
			}
		}
		echo '<style class="cp-slidebg-color" type="text/css">' . $slide_bg_style . '</style>'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		if ( '' !== $slide_in_bg_image ) {
			$customcss .= 'background-image:url(' . $slide_in_bg_image . ');' . $bg_setting . ';';
		}

		if ( $images_on_load ) {
			$lazy_custom_load_css = 'data-custom-style = "' . $customcss . '"';
		} else {
			$lazy_custom_load_css = 'style = "' . $customcss . '"';
		}

		// SlideIn - Box Shadow.
		if ( isset( $a['box_shadow'] ) && '' !== $a['box_shadow'] ) {
			$box_shadow_str = generate_box_shadow( $a['box_shadow'] );
			if ( false !== strpos( $box_shadow_str, 'inset' ) ) {
				$inset .= $box_shadow_str . ';';
				$inset .= 'opacity:1';
			} else {
				$css_style .= $box_shadow_str;
			}
		}

		// Check 'has_content_border' is set for that style and add border to slidein content (optional).
		// This option is style dependent - Developer will disable it by adding this variable.
		if ( ! isset( $a['has_content_border'] ) || ( isset( $a['has_content_border'] ) && $a['has_content_border'] ) ) {
			if ( isset( $a['border'] ) && '' !== $a['border'] ) {
				$css_style .= generate_border_css( $a['border'] );
			}
		}

		$slide_in_ht         = isset( $a['cp_slidein_height'] ) ? $a['cp_slidein_height'] : '';
		$slidein_size_style .= cp_add_css( 'height', $slide_in_ht );

		if ( isset( $a['cp_slidein_width'] ) ) {
			$slidein_size_style .= cp_add_css( 'max-width', $a['cp_slidein_width'], 'px' );
		}

		// {START} - SAME FOR BEFORE & AFTER NEED TO CREATE FUNCTION IT's TEMP.
		$close_img_prop  = cp_si_close_image_setup( $a );
		$close_img       = $close_img_prop['close_img'];
		$close_img_class = $close_img_prop['close_img_class'];
		$close_alt       = $close_img_prop['close_alt'];

		if ( isset( $a['close_slidein'] ) && 'close_txt' !== $a['close_slidein'] && isset( $a['cp_close_image_height'] ) && isset( $a['cp_close_image_width'] ) ) {
			$cp_close_image_width  = $a['cp_close_image_width'] . 'px';
			$cp_close_image_height = $a['cp_close_image_height'] . 'px';
		} else {
			$cp_close_image_width  = 'auto';
			$cp_close_image_height = 'auto';
		}

		if ( '' !== $close_alt ) {
			$close_alt = 'alt=' . $close_alt . '';
		} else {
			$close_alt = 'close-link';
		}

		if ( isset( $a['close_slidein'] ) && 'close_txt' === $a['close_slidein'] ) {
			$font_family = '';
			if ( isset( $a['close_text_font'] ) && '' !== $a['close_text_font'] ) {
				$font_family = ' font-family:' . $a['close_text_font'];
			}
			$close_html = '<span style="color:' . $a['close_text_color'] . ';' . $font_family . '">' . $a['close_txt'] . '</span>';
		} elseif ( isset( $a['close_slidein'] ) && 'close_img' === $a['close_slidein'] ) {
			if ( $images_on_load ) {
				$close_html = '<img class="cp-close-img ' . esc_attr( $close_img_class ) . '" data-src="' . esc_url( $close_img ) . '" ' . esc_attr( $close_alt ) . ' width ="' . esc_attr( $cp_close_image_width ) . '" height ="' . esc_attr( $cp_close_image_height ) . '"/>';
			} else {
				$close_html = '<img class="' . esc_attr( $close_img_class ) . '" src="' . esc_url( $close_img ) . '" ' . esc_attr( $close_alt ) . ' width ="' . esc_attr( $cp_close_image_width ) . '" height ="' . esc_attr( $cp_close_image_height ) . '"/>';
			}
		} else {
			$close_class = ' do_not_close ';
		}
		// {END} - SAME FOR BEFORE & AFTER NEED TO CREATE FUNCTION IT's TEMP.
		if ( $a['autoload_on_scroll'] ) {
			$load_after_scroll = $a['load_after_scroll'];
		}

		if ( $a['autoload_on_duration'] ) {
			$load_on_duration = $a['load_on_duration'];
		}

		if ( isset( $a['display_close_on_duration'] ) && $a['display_close_on_duration'] && 'do_not_close' !== $a['close_slidein'] ) {
			$close_btn_on_duration .= 'data-close-btnonload-delay=' . $a['close_btn_duration'] . ' ';
		}

		$dev_mode = 'disabled';
		if ( ! $a['developer_mode'] ) {
			$a['closed_cookie']     = 0;
			$a['conversion_cookie'] = 0;
			$dev_mode               = 'enabled';
		}

		$cp_settings     = get_option( 'convert_plug_settings' );
		$user_inactivity = isset( $cp_settings['user_inactivity'] ) ? $cp_settings['user_inactivity'] : '3000';

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
				$scroll_data  = 'data-scroll-class="' . $scroll_class . '"';
			}
		}
		// Variables.
		$global_class = ' global_slidein_container ';
		$schedule     = isset( $a['schedule'] ) ? $a['schedule'] : '';
		$is_scheduled = cp_is_module_scheduled( $schedule, $a['live'] );

		if ( isset( $a['on_success'] ) && isset( $a['redirect_url'] ) && isset( $a['redirect_data'] ) && isset( $a['on_redirect'] ) ) {
			$download_url  = '';
			$data_redirect = cp_has_redirect_init( $a['on_success'], $a['redirect_url'], $a['redirect_data'], $a['on_redirect'], $download_url );
		}

		if ( isset( $a['overlay_effect'] ) ) {
			$overlay_effect = $a['overlay_effect'];
		}

		if ( isset( $a['image_displayon_mobile'] ) && isset( $a['image_resp_width'] ) ) {
			$hide_image = cp_hide_image_on_mobile_init( $a['image_displayon_mobile'], $a['image_resp_width'] );
		}

		$disable_overlay_effect = isset( $a['disable_overlay_effect'] ) ? $a['disable_overlay_effect'] : '';
		$hide_animation_width   = isset( $a['hide_animation_width'] ) ? $a['hide_animation_width'] : '';

		$overaly_setting = cp_has_overaly_setting_init( $overlay_effect, $disable_overlay_effect, $hide_animation_width );
		$style_id        = ( isset( $a['style_id'] ) ) ? $a['style_id'] : '';
		$style_class     = ( isset( $a['style_class'] ) ) ? $a['style_class'] : '';
		// Filters.
		$custom_class        = cp_get_custom_class_init( $a['custom_class'], $style_id );
		$slidein_exit_intent = apply_filters( 'cp_has_enabled_or_disabled', $a['slidein_exit_intent'] );

		if ( isset( $a['add_to_cart'] ) ) {
			$add_to_cart = apply_filters( 'cp_has_enabled_or_disabled', $a['add_to_cart'] );
		}

		$load_on_refresh = apply_filters( 'cp_has_enabled_or_disabled', $a['display_on_first_load'] );

		if ( 'disabled' === $load_on_refresh ) {
			$load_on_count = ( isset( $a['page_load_count'] ) ) ? $a['page_load_count'] : '';
		}

		$global_slidein_settings    = cp_slidein_global_settings_init( $a['closed_cookie'], $a['conversion_cookie'], $style_id, $style_details );
		$placeholder_color          = ( isset( $a['placeholder_color'] ) ) ? $a['placeholder_color'] : '';
		$side_btn_style             = '';
		$slidelight                 = '';
		$slidebutton_class          = '';
		$toggle_btn_font_size       = '';
		$toggle_btn_border_radius   = '';
		$toggle_button_border_color = '';
		$toggle_btn_padding_tb      = '';

		if ( isset( $a['placeholder_font'] ) ) {
			if ( '' === $a['placeholder_font'] ) {
				$placeholder_font = 'inherit';
			} else {
				$placeholder_font = $a['placeholder_font'];
			}
		}

		$image_position = ( isset( $a['image_position'] ) ) ? $a['image_position'] : '';
		$exit_animation = isset( $a['exit_animation'] ) ? $a['exit_animation'] : 'slidein-overlay-none';

		// Slide In button css.
		// Apply box shadow to submit button - If its set & equals to - 1.
		if ( isset( $a['side_btn_shadow'] ) && '' !== $a['side_btn_shadow'] ) {
			$slideshadow .= 'box-shadow: 1px 1px 2px 0px rgba(66, 66, 66, 0.6);';
		}

		// Add - border-radius.
		if ( isset( $a['side_btn_border_radius'] ) && '' !== $a['side_btn_border_radius'] ) {
			$slideradius .= 'border-radius: ' . $a['side_btn_border_radius'] . 'px;';
		}

		// slide_btn_gradient.
		if ( isset( $a['slide_button_bg_color'] ) && '' !== $a['slide_button_bg_color'] ) {
			$slidec_normal              = $a['slide_button_bg_color'];
			$slidec_hover               = $a['side_button_bg_hover_color'];
			$slidelight                 = $a['side_button_bg_gradient_color'];
			$slidetext_color            = $a['slide_button_text_color'];
			$toggle_btn_font_size       = ( isset( $a['toggle_btn_font_size'] ) ) ? $a['toggle_btn_font_size'] : '';
			$toggle_btn_border_radius   = ( isset( $a['toggle_btn_border_radius'] ) ) ? $a['toggle_btn_border_radius'] : '';
			$toggle_btn_border_size     = ( isset( $a['toggle_btn_border_size'] ) ) ? $a['toggle_btn_border_size'] : '';
			$toggle_button_border_color = ( isset( $a['toggle_button_border_color'] ) ) ? $a['toggle_button_border_color'] : '';
			$toggle_btn_padding_lrv     = ( isset( $a['toggle_btn_padding_lrv'] ) ) ? $a['toggle_btn_padding_lrv'] : '';
			$toggle_btn_padding_tb      = ( isset( $a['toggle_btn_padding_tb'] ) ) ? $a['toggle_btn_padding_tb'] : '';

			if ( isset( $a['side_btn_gradient'] ) ) {
				$a['slide_btn_gradient'] = $a['side_btn_gradient'];
			}

			$a['side_btn_style'] = '';

			if ( isset( $a['slide_btn_gradient'] ) && '1' === $a['slide_btn_gradient'] ) {
				$a['side_btn_style'] = 'cp-btn-gradient';
			} else {
				$a['side_btn_style'] = 'cp-btn-flat';
			}

			switch ( $a['side_btn_style'] ) {
				case 'cp-btn-flat':
					$side_btn_style .= '.slidein-overlay.content-' . $uid . ' .' . $a['side_btn_style'] . '.cp-slide-edit-btn{ background: ' . $slidec_normal . '!important;' . $slideshadow . ';' . $slideradius . '; color:' . $slidetext_color . '; } '
					. '.slidein-overlay.content-' . $uid . '  .' . $a['side_btn_style'] . '.cp-slide-edit-btn:hover { background: ' . $slidec_hover . '!important; } ';
					break;

				case 'cp-btn-gradient':     // Apply box $shadow to submit button - If its set & equals to - 1.
					$side_btn_style .= '.slidein-overlay.content-' . $uid . ' .' . $a['side_btn_style'] . '.cp-slide-edit-btn {'
					. $slideshadow . $slideradius
					. '     background: -webkit-linear-gradient(' . $slidelight . ', ' . $slidec_normal . ') !important;'
					. '     background: -o-linear-gradient(' . $slidelight . ', ' . $slidec_normal . ') !important;'
					. '     background: -moz-linear-gradient(' . $slidelight . ', ' . $slidec_normal . ') !important;'
					. '     background: linear-gradient(' . $slidelight . ', ' . $slidec_normal . ') !important;'
					. '     color:' . $slidetext_color . '; }'
					. '.slidein-overlay.content-' . $uid . ' .' . $side_btn_style . 'cp-slide-edit-btn:hover {'
					. '     background: ' . $slidec_normal . ' !important;'
					. '}';
					break;
			}
		}

		// Append - Slide In - Toggle CSS.
		if ( isset( $a['toggle_button_font'] ) ) {
			if ( '' === $a['toggle_button_font'] ) {
				$a['toggle_button_font'] = 'inherit';
				$font                    = $a['toggle_button_font'];
			} else {
				$font = 'sans-serif';
				$font = $a['toggle_button_font'] . ',' . $font;
			}
		}
		if ( ( isset( $a['toggle_btn'] ) && '1' === $a['toggle_btn'] ) ) {
			$side_btn_style .= '.slidein-overlay.content-' . $uid . ' .' . $a['side_btn_style'] . '.cp-slide-edit-btn {
				font-family: ' . $font . ';
				font-size: ' . $toggle_btn_font_size . 'px;
				border-radius:' . $toggle_btn_border_radius . 'px;
				border-width:' . $toggle_btn_border_size . 'px;
				border-color:' . $toggle_button_border_color . ';
				padding-left:' . $toggle_btn_padding_lrv . 'px;
				padding-right:' . $toggle_btn_padding_lrv . 'px;
				padding-top:' . $toggle_btn_padding_tb . 'px;
				padding-bottom:' . $toggle_btn_padding_tb . 'px;
				border-color:' . $toggle_button_border_color . ';
			}';
		}

		echo '<style class="cp-slidebtn-submit" type="text/css">' . $side_btn_style . '</style>'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		// check if inline display is set.
		$is_inline = ( isset( $a['display'] ) && 'inline' === $a['display'] ) ? true : false;

		// toggle button setting.
		if ( ( isset( $a['toggle_btn'] ) && '1' === $a['toggle_btn'] ) && 'do_not_close' !== $a['close_slidein'] && '1' === $a['toggle_btn_visible'] && ! $is_inline ) {
			$toggleclass = 'cp-hide-slide';
		}

		if ( ( isset( $a['toggle_btn'] ) && '0' === $a['toggle_btn'] ) && ( isset( $a['close_slidein'] ) && 'do_not_close' !== $a['close_slidein'] ) ) {
			$slide_toggle_class = 'cp-slide-without-toggle';
		}

		if ( $is_inline ) {
			$custom_class   .= ' cp-slidein-inline';
			$cp_close_inline = (int) ( isset( $timezone_settings['cp-close-inline'] ) ? $timezone_settings['cp-close-inline'] : 0 );
			$close_inline    = ( $cp_close_inline ) ? 'cp-close-inline' : 'cp-do-not-close-inline';
			$inline_text     = $custom_class . ' ' . $close_inline;
		} else {
			$custom_class .= ' cp-slidein-global';
		}

		// check if modal should be triggered after post.
		$enable_after_post = (int) ( isset( $a['enable_after_post'] ) ? $a['enable_after_post'] : 0 );
		if ( $enable_after_post ) {
			$custom_class .= ' si-after-post';
		}

		// check if modal should be triggerd if items in the cart.
		$add_to_cart = (int) ( isset( $a['add_to_cart'] ) ? $a['add_to_cart'] : 0 );
		if ( $add_to_cart ) {
			$custom_class .= ' cp-items-in-cart';
		}

		$cp_settings          = get_option( 'convert_plug_debug' );
		$after_content_scroll = isset( $cp_settings['after_content_scroll'] ) ? $cp_settings['after_content_scroll'] : '50';
		$after_content_data   = 'data-after-content-value="' . $after_content_scroll . '"';

		$si_onload = ( isset( $a['manual'] ) && 'true' === $a['manual'] ) ? '' : ' si-onload cp-global-load ';

		$always_visible = ( ( isset( $a['toggle_btn'] ) && '1' === $a['toggle_btn'] ) && ( isset( $a['toggle_btn_visible'] ) && '1' === $a['toggle_btn_visible'] ) ) ? 'data-toggle-visible=true' : '';

		if ( ! $is_inline ) {
			$slide_position = 'slidein-' . $a['slidein_position'];
		}

		$close_adjacent_position = ( isset( $a['adjacent_close_position'] ) ? $a['adjacent_close_position'] : 'cp-adjacent-right' );
		if ( 'top_left' === $close_adjacent_position && ! $is_inline ) {
			$cp_close_body = 'cp-top-img';
		}

		$minimize_widget = isset( $a['minimize_widget'] ) ? $a['minimize_widget'] : '';
		if ( ! $is_inline && '1' === $minimize_widget ) {
			$minimize_widget = 'cp-minimize-widget';
		}

		if ( isset( $a['developer_mode'] ) && $a['developer_mode'] && ( 'optin_widget' === $a['style'] || 'social_widget_box' === $a['style'] ) ) {
			$minimize_widget .= ' cp-always-minimize-widget';
		}

		// form display/hide after sucessfull submission.
		$form_action_onsubmit = isset( $a['form_action_on_submit'] ) ? $a['form_action_on_submit'] : '';

		if ( 'reappear' === $form_action_onsubmit ) {
			$form_data_onsubmit .= 'data-form-action = reappear';
			$form_data_onsubmit .= ' data-form-action-time =' . $a['form_reappear_time'];
		} elseif ( 'disappears' === $form_action_onsubmit ) {
			$form_data_onsubmit .= 'data-form-action = disappear';
			$form_data_onsubmit .= ' data-form-action-time =' . $a['form_disappears_time'];
		}

		$slide_content_data  = '';
		$cp_container_class  = '';
		$cp_overlay_class    = '';
		$slide_overlay_data  = '';
		$slide_overlay_class = '';

		// cp-slidein-popup-container div data.
		$cp_container_class .= 'cp-module cp-slidein-popup-container';
		$cp_container_class .= ' ' . esc_attr( $style_id );
		$cp_container_class .= ' ' . $style_class . '-container';
		$cp_container_class .= ' overlay-show ';
		$custom_selector     = '';
		$custom_selector     = isset( $a['custom_selector'] ) ? cp_get_custom_slector_init( $a['custom_selector'] ) : '';
		if ( '' !== $custom_selector ) {
			$custom_class .= ' ' . cp_get_custom_slector_class_init( $a['custom_selector'] );
		}
		// overlay div data.
		$slide_overlay_data .= 'data-image-position="' . $image_position . '" ';
		$slide_overlay_data .= 'data-placeholder-color ="' . $placeholder_color . '" ';
		$slide_overlay_data .= 'data-timezonename ="' . esc_attr( $timezone_name ) . '" ';
		$slide_overlay_data .= 'data-timezone ="' . esc_attr( $timezone ) . '" ';
		$slide_overlay_data .= 'data-load-on-refresh ="' . $load_on_refresh . '" ';
		$slide_overlay_data .= 'data-custom-class ="' . esc_attr( $custom_class ) . '" ';
		$slide_overlay_data .= 'data-class ="content-' . $uid . '" ';
		$slide_overlay_data .= 'data-placeholder-font ="' . $placeholder_font . '" ';
		$slide_overlay_data .= 'data-load-on-count ="' . esc_attr( $load_on_count ) . '" ';
		$slide_overlay_data .= $global_slidein_settings . '  ';
		$slide_overlay_data .= $is_scheduled . ' ';
		$slide_overlay_data .= $hide_image . ' ';
		$slide_overlay_data .= $overaly_setting . ' ';
		$slide_overlay_data .= $data_redirect . ' ';
		$slide_overlay_data .= $form_data_onsubmit . ' ';
		$slide_overlay_data .= $autoclose_data . ' ';
		$slide_overlay_data .= $close_btn_on_duration . ' ';

		$slide_overlay_class  = 'slidein-overlay';
		$slide_overlay_class .= ' ' . $global_class;
		$slide_overlay_class .= ' ' . $inline_text;
		$slide_overlay_class .= ' ' . esc_attr( $slide_toggle_class );
		$slide_overlay_class .= ' content-' . $uid;
		$slide_overlay_class .= ' ' . $close_class;
		$slide_overlay_class .= ' ' . $minimize_widget;
		$slide_overlay_class .= ' ' . $impression_disable_class;

		$slide_content_data .= 'data-dev-mode="' . esc_attr( $dev_mode ) . '" ';
		$slide_content_data .= 'data-load-on-refresh="' . esc_attr( $load_on_refresh ) . '" ';
		$slide_content_data .= 'data-custom-class="' . esc_attr( $custom_class ) . '" ';
		$slide_content_data .= 'data-exit-intent="' . esc_attr( $slidein_exit_intent ) . '" ';
		$slide_content_data .= 'data-add-to-cart = "' . esc_attr( $add_to_cart ) . '"';

		$slide_content_data .= 'data-onscroll-value="' . esc_attr( $load_after_scroll ) . '" ';
		$slide_content_data .= 'data-onload-delay ="' . esc_attr( $load_on_duration ) . '" ';
		$slide_content_data .= 'data-overlay-class = "overlay-zoomin" ';
		$slide_content_data .= 'data-class-id = "content-' . $uid . '" ';
		$slide_content_data .= $global_slidein_settings . ' ';
		$slide_content_data .= $inactive_data . ' ';
		$slide_content_data .= $always_visible . ' ';
		$slide_content_data .= $scroll_data . ' ';
		$slide_content_data .= $after_content_data . ' ';
		$slide_content_data .= $referrer_data . ' ';
		$custom_css_class    = isset( $a['custom_css_class'] ) ? $a['custom_css_class'] : '';
		$slide_content_data .= 'data-custom-selector = "' . esc_attr( $custom_selector ) . '"';
		$cp_close_gravity    = isset( $convert_plug_settings['cp-close-gravity'] ) ? $convert_plug_settings['cp-close-gravity'] : 1;
		$gr_data             = '';
		$gr_data             = 'data-close-gravity = "' . esc_attr( $cp_close_gravity ) . '"';

		ob_start();
		if ( ! $is_inline ) {
			?>
			<div <?php echo wp_kses_post( $slide_content_data ); ?> class="<?php echo esc_attr( $si_onload ); ?> overlay-show <?php echo esc_attr( $custom_class ); ?>"  data-module-type="slide_in" ></div>
			<?php } ?>
			<div class="<?php echo esc_attr( $cp_container_class ); ?>" data-style-id ="<?php echo esc_attr( $style_id ); ?>" data-module-name ="slidein" <?php echo wp_kses_post( $gr_data ); ?> >
				<div class="
				<?php
				echo esc_attr( $slide_overlay_class );
				echo esc_attr( $custom_css_class );
				?>
				" <?php echo wp_kses_post( $slide_overlay_data ); ?> >
					<div class="cp-slidein <?php echo esc_attr( $slide_position ); ?>" style="<?php echo esc_attr( $slidein_size_style ); ?>">
						<div class="cp-animate-container <?php echo esc_attr( $toggleclass ); ?>" <?php echo wp_kses_post( $overaly_setting ); ?> data-exit-animation="<?php echo esc_attr( $exit_animation ); ?>">
							<div class="cp-slidein-content" id="slide-in-animate-<?php echo esc_attr( $style_id ); ?>" style="<?php echo esc_attr( $css_style ); ?>;">
								<div class="cp-slidein-body <?php echo esc_attr( $style_class ) . ' ' . esc_attr( $el_class ); ?> <?php echo esc_attr( $cp_close_body ); ?>" <?php echo wp_kses_post( $lazy_custom_load_css ); ?> >
									<div class="cp-slidein-body-overlay cp_cs_overlay" style="<?php echo esc_attr( $inset ); ?>;"></div>
									<?php
	}
}
							add_filter( 'cp_slidein_global_before', 'cp_slidein_global_before_init' );
							/*--------------------------------------------------------------*/

if ( ! function_exists( 'cp_slidein_global_after_init' ) ) {
	/**
	 * Function Name: cp_slidein_global_after_init SlideIn After.
	 *
	 * @param  array $a array parameter.
	 * @since 0.1.5
	 */
	function cp_slidein_global_after_init( $a ) {

		$edit_link = '';
		if ( is_user_logged_in() ) {
			// if user has access to CP_PLUS_SLUG, then only display edit style link.
			if ( current_user_can( 'access_cp' ) ) {
				if ( isset( $a['style_id'] ) ) {
					if ( isset( $a['style_id'] ) ) {
						$edit_link = cp_get_edit_link( $a['style_id'], 'slide_in', $a['style'] );
					}
				}
			}
		}

		$style_id = ( isset( $a['style_id'] ) ) ? $a['style_id'] : '';

		if ( isset( $a['close_slidein'] ) && 'close_txt' !== $a['close_slidein'] && isset( $a['cp_close_image_height'] ) && isset( $a['cp_close_image_width'] ) ) {
			$cp_close_image_width  = $a['cp_close_image_width'] . 'px';
			$cp_close_image_height = $a['cp_close_image_height'] . 'px';
		} else {
			$cp_close_image_width  = 'auto';
			$cp_close_image_height = 'auto';
		}

		// {START} - SAME FOR BEFORE & AFTER NEED TO CREATE FUNCTION IT's TEMP.
		$close_img_class       = '';
		$close_alt             = '';
		$close_alt             = 'close-link';
		$close_img_prop        = cp_si_close_image_setup( $a );
		$close_img             = $close_img_prop['close_img'];
		$close_img_class       = $close_img_prop['close_img_class'];
		$close_alt             = $close_img_prop['close_alt'];
		$convert_plug_settings = get_option( 'convert_plug_settings' );
		$images_on_load        = isset( $convert_plug_settings['cp-lazy-img'] ) ? $convert_plug_settings['cp-lazy-img'] : 0;

		if ( '' !== $close_alt ) {
			$close_alt = 'alt=' . $close_alt . '';
		}

		$close_html  = '';
		$el_class    = '';
		$close_class = '';

		if ( isset( $a['content_padding'] ) && $a['content_padding'] ) {
			$el_class .= 'cp-no-padding ';
		}
		$close_tooltip     = '';
		$close_tooltip_end = '';

		if ( isset( $a['close_slidein'] ) && 'close_txt' === $a['close_slidein'] ) {
			$close_class .= 'cp-text-close';
			if ( '1' === $a['close_slidein_tooltip'] ) {
				$close_tooltip     = '<span class=" cp-close-tooltip cp-tooltip-icon has-tip cp-tipcontent-' . $a['style_id'] . 'data-classes="close-tip-content-' . $a['style_id'] . '" data-position="left"  title="' . $a['tooltip_title'] . '"  data-color="' . $a['tooltip_title_color'] . '" data-bgcolor="' . $a['tooltip_background'] . '" data-closeid ="cp-tipcontent-' . $a['style_id'] . '" data-position="left" >';
				$close_tooltip_end = '</span>';
			}
			$font_family = '';
			if ( isset( $a['close_text_font'] ) && '' !== $a['close_text_font'] ) {
				$font_family = ' font-family:' . $a['close_text_font'];
			}
			$close_html = '<span style="color:' . $a['close_text_color'] . ';' . $font_family . '">' . $a['close_txt'] . '</span>';
		} elseif ( isset( $a['close_slidein'] ) && 'close_img' === $a['close_slidein'] ) {
			$close_class .= 'cp-image-close';
			if ( $images_on_load ) {
				$close_html = '<img class="cp-close-img ' . esc_attr( $close_img_class ) . '" data-src="' . esc_url( $close_img ) . '" ' . esc_attr( $close_alt ) . ' width ="' . esc_attr( $cp_close_image_width ) . '" height ="' . esc_attr( $cp_close_image_height ) . '"/>';
			} else {
					$close_html = '<img class="' . esc_attr( $close_img_class ) . '" src="' . esc_url( $close_img ) . '" ' . esc_attr( $close_alt ) . ' width ="' . esc_attr( $cp_close_image_width ) . '" height ="' . esc_attr( $cp_close_image_height ) . '"/>';
			}
		} else {
			$close_class = 'do_not_close';
		}

		if ( isset( $a['display_close_on_duration'] ) && $a['display_close_on_duration'] && 'do_not_close' !== $a['close_slidein'] ) {

			if ( '1' !== $a['toggle_btn_visible'] ) {
				$close_class .= ' cp-hide-close';
			} else {
				if ( isset( $a['toggle_btn'] ) && '0' === $a['toggle_btn'] && '1' === $a['toggle_btn_visible'] ) {
					$close_class .= ' cp-hide-close';
				}
			}
		}

		// {END} - SAME FOR BEFORE & AFTER NEED TO CREATE FUNCTION IT's TEMP.
		$tooltip_position        = 'left';
		$close_adjacent_position = ( isset( $a['adjacent_close_position'] ) ? $a['adjacent_close_position'] : 'cp-adjacent-right' );
		if ( '' !== $close_adjacent_position ) {
			$tooltip_position = cp_get_tooltip_position( $close_adjacent_position );
		}

		$tooltip_class = '';
		$tooltip_style = '';
		if ( isset( $a['close_slidein_tooltip'] ) && '1' === $a['close_slidein_tooltip'] ) {
			$tooltip_class .= 'cp_closewith_tooltip';
			$tooltip_style .= 'color:' . $a['tooltip_title_color'] . ';background-color:' . $a['tooltip_background'] . ';border-top-color: ' . $a['tooltip_background'] . ';';
		}

		// Generate border radius for form processing.
		$css_code1        = '';
		$form_process_css = '';
		if ( isset( $a['border'] ) && '' !== $a['border'] ) {
			$pairs  = explode( '|', $a['border'] );
			$result = array();
			foreach ( $pairs as $pair ) {
				$pair               = explode( ':', $pair );
				$result[ $pair[0] ] = $pair[1];
			}

			$css_code1             .= $result['br_tl'] . 'px ' . $result['br_tr'] . 'px ' . $result['br_br'] . 'px ';
			$css_code1             .= $result['br_bl'] . 'px';
			$result['border_width'] = ' ';
			$form_process_css      .= 'border-radius: ' . $css_code1 . ';';
			$form_process_css      .= '-moz-border-radius: ' . $css_code1 . ';';
			$form_process_css      .= '-webkit-border-radius: ' . $css_code1 . ';';
		}

		// check if inline display is set.
		$is_inline = ( isset( $a['display'] ) && 'inline' === $a['display'] ) ? true : false;

		if ( isset( $a['toggle_btn'] ) && '1' === $a['toggle_btn'] && '1' === $a['toggle_btn_visible'] && ! $is_inline ) {
			$slide_in_btn_class = '';
		} else {
			$slide_in_btn_class = 'cp-slide-hide-btn';
		}

		$msg_color = isset( $a['message_color'] ) ? $a['message_color'] : '';

		?>
		<?php
		// add nounce field to modal.
		$nonce = wp_create_nonce( 'cp-impress-nonce' );
		?>
		<input type="hidden" class="cp-impress-nonce" name="cp-impress-nonce" value="<?php echo esc_attr( $nonce ); ?>">

	</div><!-- .cp-slidein-body -->
</div><!-- .cp-slidein-content -->

		<?php if ( isset( $a['form_layout'] ) && 'cp-form-layout-4' !== $a['form_layout'] ) { ?>
<div class="cp-form-processing-wrap" style="<?php echo esc_attr( $form_process_css ); ?>;">
	<div class="cp-form-after-submit">
		<div class ="cp-form-processing" style="">
		<div class ="cp-form-processing" >
			<div class="smile-absolute-loader" style="visibility: visible;">
				<div class="smile-loader" style = "width: 100px;" >
					<div class="smile-loading-bar"></div>
					<div class="smile-loading-bar"></div>
					<div class="smile-loading-bar"></div>
					<div class="smile-loading-bar"></div>
				</div>
			</div>
		</div>
		</div>
		<div class ="cp-msg-on-submit" style="color:<?php echo esc_attr( $msg_color ); ?>;"></div>
	</div>
</div>
		<?php } ?>

		<?php
		$close_overlay_class     = 'cp-inside-close';
		$close_adj_class         = '';
		$close_adjacent_position = ( isset( $a['adjacent_close_position'] ) ? $a['adjacent_close_position'] : 'cp-adjacent-right' );

		$close_adj_class     .= cp_get_close_adj_position( $close_adjacent_position );
		$close_overlay_class .= $close_adj_class;

		if ( ! $is_inline ) {
			?>
	<div class="slidein-overlay-close <?php echo esc_attr( $close_class ) . ' ' . esc_attr( $close_overlay_class ); ?>" style="width: <?php echo esc_attr( $cp_close_image_width ); ?>">
			<?php if ( isset( $a['close_slidein_tooltip'] ) && '1' === $a['close_slidein_tooltip'] ) { ?>
		<span class=" cp-tooltip-icon cp-inside-tip has-tip cp-tipcontent-<?php echo esc_attr( $a['style_id'] ); ?>" data-offset="20" data-classes="close-tip-content-<?php echo esc_attr( $a['style_id'] ); ?>" data-position="<?php echo esc_attr( $tooltip_position ); ?>"  title="<?php echo esc_attr( $a['tooltip_title'] ); ?>"  data-color="<?php echo esc_attr( $a['tooltip_title_color'] ); ?>" data-bgcolor="<?php echo esc_attr( $a['tooltip_background'] ); ?>" data-closeid ="cp-tipcontent-<?php echo esc_attr( $a['style_id'] ); ?>">
			<?php } ?>
			<?php
				echo $close_html; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
			<?php
			if ( isset( $a['close_slidein_tooltip'] ) && '1' === $a['close_slidein_tooltip'] ) {
				?>
			</span><?php } ?>
		</div>
				<?php } ?>
	</div><!-- .cp-animate-container -->
		<?php
		if ( '' !== $edit_link ) {

			$edit_link_text = 'Edit With ' . CP_PLUS_NAME;

			$edit_link_txt = apply_filters( 'cp_style_edit_link_text', $edit_link_text );

			echo "<div class='cp_edit_link'><a target='_blank' href=" . esc_attr( $edit_link ) . " rel='noopener' >" . esc_attr( $edit_link_txt ) . '</a></div>';
		}
		?>
</div><!-- .cp-slidein -->

		<?php if ( $is_inline ) { ?>
<span class="cp-slide_in-inline-end" data-style="<?php echo esc_attr( $style_id ); ?>"></span>
		<?php } ?>


		<?php
		if ( isset( $a['toggle_btn'] ) && '1' === $a['toggle_btn'] && 'do_not_close' !== $a['close_slidein'] ) {
			if ( '1' === $a['slide_btn_gradient'] ) {
				$slidebutton_class = 'cp-btn-gradient';
			} else {
				$slidebutton_class = 'cp-btn-flat';
			}

			$slide_btn_animation = '';

			if ( 'center-left' === $a['slidein_position'] || 'top-left' === $a['slidein_position'] || 'top-center' === $a['slidein_position'] || 'top-right' === $a['slidein_position'] ) {
				$slide_btn_animation = 'smile-slideInDown';
			}

			if ( 'center-right' === $a['slidein_position'] || 'bottom-left' === $a['slidein_position'] || 'bottom-center' === $a['slidein_position'] || 'bottom-right' === $a['slidein_position'] ) {
				$slide_btn_animation = 'smile-slideInUp';
			}

			$a['side_btn_style'] = '';
			if ( '1' === $a['slide_btn_gradient'] ) {
				$a['side_btn_style'] = 'cp-btn-gradient';
			} else {
				$a['side_btn_style'] = 'cp-btn-flat';
			}

			?>
	<div class="cp-toggle-container <?php echo esc_attr( $slidebutton_class ); ?> slidein-<?php echo esc_attr( $a['slidein_position'] ); ?> <?php echo esc_attr( $slide_in_btn_class ); ?>">
		<div class="<?php echo esc_attr( $a['side_btn_style'] ); ?> cp-slide-edit-btn smile-animated  <?php echo esc_attr( $slide_btn_animation ); ?> ;" ><?php echo esc_attr( wp_specialchars_decode( $a['slide_button_title'] ) ); ?></div>
	</div>
			<?php
		}
		?>

</div><!-- .slidein-overlay -->
</div><!-- .cp-slidein-popup-container -->
		<?php
	}
}

add_filter( 'cp_slidein_global_after', 'cp_slidein_global_after_init' );

if ( ! function_exists( 'cp_si_close_image_setup' ) ) {
	/**
	 * Function Name: cp_si_close_image_setup Get close image.
	 *
	 * @param  array $a array parameters.
	 * @return array    array parameters.
	 */
	function cp_si_close_image_setup( $a ) {
		$close_img       = '';
		$close_img_class = '';
		$close_alt       = '';
		$close_alt       = 'close-link';
		if ( ! isset( $a['close_si_image_src'] ) ) {
			$a['close_si_image_src'] = 'upload_img';
		}

		if ( isset( $a['close_si_image_src'] ) && 'upload_img' === $a['close_si_image_src'] ) {

			if ( isset( $a['close_img'] ) && ! empty( $a['close_img'] ) ) {
				if ( false !== strpos( $a['close_img'], 'http' ) ) {
					$close_img = $a['close_img'];
					if ( false !== strpos( $close_img, '|' ) ) {
						$close_img = explode( '|', $close_img );
						$close_img = $close_img[0];
						$close_img = cp_get_protocol_settings_init( $close_img );
					}
					$close_img_class = 'cp-default-close';
				} else {
					$close_img     = apply_filters( 'cp_get_wp_image_url', $a['close_img'] );
					$close_img_alt = explode( '|', $a['close_img'] );
					$close_img     = cp_get_protocol_settings_init( $close_img );

					if ( count( $close_img_alt ) > 2 ) {
						$close_alt = $close_img_alt[2];
					}
				}
			}
		} elseif ( isset( $a['close_si_image_src'] ) && 'custom_url' === $a['close_si_image_src'] ) {
			$close_img = $a['slide_in_close_img_custom_url'];
		} elseif ( 'pre_icons' === $a['close_si_image_src'] ) {
			$icon_url  = CP_PLUGIN_URL . 'modules/assets/images/' . $a['close_icon'] . '.png';
			$close_img = $icon_url;
			$close_img = cp_get_protocol_settings_init( $close_img );
		}

		$close_img_prop = array(
			'close_img'       => $close_img,
			'close_img_class' => $close_img_class,
			'close_alt'       => $close_alt,
		);

		return $close_img_prop;

	}
}
