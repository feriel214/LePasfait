<?php
/**
 * Global function for modal.
 *
 * @package Convert Plus.
 */

// Add modal function.
if ( ! function_exists( 'cp_generate_style_css' ) ) {
	/**
	 * Function Name: cp_generate_style_css.
	 *
	 * @param  array $a array parameter.
	 */
	function cp_generate_style_css( $a ) {

		// Custom css.
		$style_id = 'content-' . $a['uid'];
		$style    = '';
		// Custom height only for blank style.
		if ( isset( $a['cp_custom_height'] ) && isset( $a['cp_modal_height'] ) && '1' === $a['cp_custom_height'] ) {
			$style .= '';
			$style .= '.' . $style_id . ' .cp-modal-body { '
			. '		min-height:' . $a['cp_modal_height'] . 'px;}';
		}
		// Append CSS code.
		echo '<style type="text/css">' . $style . '</style>'; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

	}
}

if ( ! function_exists( 'cp_has_overaly_setting_init' ) ) {
	/**
	 * Function Name: cp_has_overaly_setting_init Check modal overlay settings.
	 *
	 * @param  string $overlay_effect         string parameters.
	 * @param  string $disable_overlay_effect string parameters.
	 * @param  string $hide_animation_width   string parameters.
	 * @return string                         string parameters.
	 * @since 0.1.5
	 */
	function cp_has_overaly_setting_init( $overlay_effect, $disable_overlay_effect, $hide_animation_width ) {
		$op = ' data-overlay-animation = "' . $overlay_effect . '" ';
		if ( '1' === $disable_overlay_effect ) {
			$op .= ' data-disable-animationwidth = "' . $hide_animation_width . '" ';
		}
		return $op;
	}
}
add_filter( 'cp_has_overaly_setting', 'cp_has_overaly_setting_init' );

if ( ! function_exists( 'cp_get_affiliate_link_init' ) ) {
	/**
	 * Function Name: cp_get_affiliate_link_init  Affiliate - Link.
	 *
	 * @param  string $affiliate_setting  string parameters.
	 * @param  string $affiliate_username string parameters.
	 * @return string                     string parameters.
	 * @since 0.1.5
	 */
	function cp_get_affiliate_link_init( $affiliate_setting, $affiliate_username ) {
		$op = '';
		if ( '1' === $affiliate_setting || 1 === $affiliate_setting ) {
			if ( '' === $affiliate_username ) {
				$affiliate_username = 'BrainstormForce';
				$op                 = 'https://www.convertplug.com/plus/buy?ref=BrainstormForce';
			} else {
				$op = 'https://www.convertplug.com/plus/buy?ref=' . $affiliate_username . '';
			}
			return $op;
		}
	}
}
add_filter( 'cp_get_affiliate_link', 'cp_get_affiliate_link_init' );

if ( ! function_exists( 'cp_get_affiliate_class_init' ) ) {
	/**
	 * Function Name:cp_get_affiliate_class_init Affiliate - Class.
	 *
	 * @param  string $affiliate_setting string parameters.
	 * @param  string $modal_size        string parameters.
	 * @return string                    string parameters.
	 * @since 0.1.5
	 */
	function cp_get_affiliate_class_init( $affiliate_setting, $modal_size ) {
		$op = '';
		if ( ( '1' === $affiliate_setting || 1 === $affiliate_setting ) && 'cp-modal-custom-size' === $modal_size ) {
			$op .= 'cp-affilate';
		}
		return $op;
	}
}
add_filter( 'cp_get_affiliate_class', 'cp_get_affiliate_class_init' );

if ( ! function_exists( 'cp_get_affiliate_setting_init' ) ) {
	/**
	 * Function Name: cp_get_affiliate_setting_init Affiliate - Setting.
	 *
	 * @param  string $affiliate_setting string parameters.
	 * @return string                    string parameters.
	 * since 0.1.5
	 */
	function cp_get_affiliate_setting_init( $affiliate_setting ) {
		$op = ( '1' === $affiliate_setting ) ? 'data-affiliate_setting=' . $affiliate_setting : 'data-affiliate_setting ="0"';
		return $op;
	}
}
add_filter( 'cp_get_affiliate_setting', 'cp_get_affiliate_setting_init' );

if ( ! function_exists( 'cp_modal_global_settings_init' ) ) {
	/**
	 * Function Name: cp_modal_global_settings_init Global Settings - Modal
	 *
	 * @param  string $closed_cookie     string parameters.
	 * @param  string $conversion_cookie string parameters.
	 * @param  string $style_id          string parameters.
	 * @param  string $style_details     string parameters.
	 * @return string                    string parameters.
	 * @since 0.1.5
	 */
	function cp_modal_global_settings_init( $closed_cookie, $conversion_cookie, $style_id, $style_details ) {

		$style_type   = $style_details['type'];
		$parent_style = $style_details['parent_style'];

		$op  = ' data-closed-cookie-time="' . $closed_cookie . '"';
		$op .= ' data-conversion-cookie-time="' . $conversion_cookie . '" ';
		$op .= ' data-modal-id="' . $style_id . '" ';

		if ( '' !== $parent_style ) {
			$op .= ' data-parent-style="' . $parent_style . '" ';
		}

		$op .= ' data-modal-style="' . $style_id . '" ';
		$op .= ' data-option="smile_modal_styles" ';
		return $op;
	}
}
add_filter( 'cp_modal_global_settings', 'cp_modal_global_settings_init' );

if ( ! function_exists( 'cp_modal_global_before_init' ) ) {
	/**
	 * Function Name: cp_modal_global_before_init  Modal Before.
	 *
	 * @param  array $a array parameters.
	 * @since 0.1.5
	 */
	function cp_modal_global_before_init( $a ) {

		$autoclose_data           = '';
		$timezone                 = '';
		$referrer_data            = '';
		$style_type               = '';
		$bg_setting               = '';
		$el_class                 = '';
		$module_bg_gradient       = '';
		$module_bg_color_type     = '';
		$modal_bg_image           = '';
		$customcss                = '';
		$windowcss                = '';
		$inset                    = '';
		$css_style                = '';
		$close_html               = '';
		$modal_size_style         = '';
		$close_class              = '';
		$close_inline             = '';
		$inline_text              = '';
		$close_img_class          = '';
		$close_img                = '';
		$load_after_scroll        = '';
		$font_family              = '';
		$load_on_duration         = '';
		$close_btn_on_duration    = '';
		$close_modal_on           = '';
		$scroll_data              = '';
		$scroll_class             = '';
		$inactive_data            = '';
		$data_redirect            = '';
		$overlay_effect           = '';
		$hide_image               = '';
		$placeholder_font         = '';
		$impression_disable_class = '';
		$cp_modal_content_class   = '';
		$form_data_onsubmit       = '';
		$lazy_custom_load_css     = '';
		$lazy_window_load_css     = '';
		$modal_body_css           = '';
		$style_id                 = ( isset( $a['style_id'] ) ) ? $a['style_id'] : '';
		$a['image_resp_width']    = '768';
		$convert_plug_settings    = get_option( 'convert_plug_settings' );
		$style_details            = get_style_details( $style_id, 'modal' );
		$images_on_load           = isset( $convert_plug_settings['cp-lazy-img'] ) ? $convert_plug_settings['cp-lazy-img'] : 0;

		if ( ! isset( $a['modal_size'] ) ) {
			$a['modal_size'] = 'cp-modal-custom-size';
		}

		// Print CSS of the style.
		cp_generate_style_css( $a );

		// check referrer detection.
		$referrer_check  = ( isset( $a['enable_referrer'] ) && (int) $a['enable_referrer'] ) ? 'display' : 'hide';
		$referrer_domain = ( 'display' === $referrer_check ) ? $a['display_to'] : $a['hide_from'];

		if ( '' !== $referrer_check ) {
			$referrer_data  = 'data-referrer-domain="' . $referrer_domain . '"';
			$referrer_data .= ' data-referrer-check="' . $referrer_check . '"';
		}

		// check close after few second.
		$autoclose_on_duration = ( isset( $a['autoclose_on_duration'] ) && (int) $a['autoclose_on_duration'] ) ? $a['autoclose_on_duration'] : '';
		$close_module_duration = ( isset( $a['close_module_duration'] ) && (int) $a['close_module_duration'] ) ? $a['close_module_duration'] : '';
		$is_inline             = ( isset( $a['display'] ) && 'inline' === $a['display'] ) ? true : false;

		if ( '' !== $autoclose_on_duration && ( ! $is_inline ) && ( isset( $a['close_modal'] ) && 'do_not_close' !== $a['close_modal'] ) ) {
			$autoclose_data = 'data-close-after = "' . $close_module_duration . '"';
		}
		// Enqueue Google Fonts.
		cp_enqueue_google_fonts( $a['cp_google_fonts'] );

		// get op_bg image sizes.
		$opt_bg = isset( $a['opt_bg'] ) ? $a['opt_bg'] : '';
		if ( isset( $opt_bg ) && false !== strpos( $opt_bg, '|' ) ) {
			$bg_setting .= cp_get_image_size_opt( $opt_bg );
		}

		// Time Zone.
		$timezone      = cp_get_timezone_init();
		$timezone_name = ! isset( $convert_plug_settings['cp-timezone'] ) ? 'WordPress' : $convert_plug_settings['cp-timezone'];

		// Modal - Padding.
		if ( isset( $a['content_padding'] ) && ! empty( $a['content_padding'] ) ) {
			$el_class .= ' cp-no-padding ';
		}

		// check modal_back_type - gradient/simple/image.
		$module_bg_color_type = ( isset( $a['module_bg_color_type'] ) ) ? $a['module_bg_color_type'] : '';
		$bg_type_set          = false;
		$old_user             = true;

		if ( '' !== $module_bg_color_type ) {
			$module_bg_gradient = ( isset( $a['module_bg_gradient'] ) ) ? $a['module_bg_gradient'] : '';
			$bg_type_set        = true;
			$old_user           = false;
		}

		// Modal - Background Image & Background Color.
		$modal_bg_color            = ( isset( $a['modal_bg_color'] ) ) ? $a['modal_bg_color'] : '';
		$modal_bg_image_new        = isset( $a['modal_bg_image'] ) ? $a['modal_bg_image'] : '';
		$modal_bg_image_src        = isset( $a['modal_bg_image_src'] ) ? $a['modal_bg_image_src'] : 'upload_img';
		$modal_bg_image_custom_url = isset( $a['modal_bg_image_custom_url'] ) ? $a['modal_bg_image_custom_url'] : '';
		$modal_bg_img_color        = isset( $a['modal_bg_img_color'] ) ? $a['modal_bg_img_color'] : '';

		if ( isset( $modal_bg_image_src ) && ! empty( $modal_bg_image_src ) ) {
			$modal_bg_image = cp_get_module_images_new( $modal_bg_image_new, $modal_bg_image_src, $modal_bg_image_custom_url );
		}

		if ( '' !== $modal_bg_image ) {
			if ( ( $bg_type_set && 'image' === $module_bg_color_type ) || $old_user ) {
				$customcss .= 'background-image:url(' . $modal_bg_image . ');' . $bg_setting . ';';
				$windowcss .= 'background-image:url(' . $modal_bg_image . ');' . $bg_setting . ';';
			}
		}

		if ( ! $old_user && 'gradient' === $module_bg_color_type && $bg_type_set && 'countdown' !== $a['style'] ) {
			$modal_body_css = generate_back_gradient( $module_bg_gradient );
		} elseif ( 'image' === $module_bg_color_type ) {
			$modal_body_css = 'background-color:' . $modal_bg_img_color . ';';
		} else {
			$modal_body_css = 'background-color:' . $modal_bg_color . ';';
		}

		// Modal - Box Shadow.
		if ( '' !== $a['box_shadow'] ) {
			$box_shadow_str = generate_box_shadow( $a['box_shadow'] );
			if ( false !== strpos( $box_shadow_str, 'inset' ) ) {
				$inset .= $box_shadow_str . ';';
				$inset .= 'opacity:1';
			} else {
				$css_style .= $box_shadow_str;
			}
		}

		// Check 'has_content_border' is set for that style and add border to modal content (optional).
		if ( ! isset( $a['has_content_border'] ) || ( isset( $a['has_content_border'] ) && $a['has_content_border'] ) ) {
			if ( isset( $a['border'] ) && '' !== $a['border'] ) {
				$css_style .= generate_border_css( $a['border'] );
			}
		}

		if ( 'cp-modal-custom-size' === $a['modal_size'] ) {
			$modal_size_style  = cp_add_css( 'width', '100', '%' );
			$modal_ht          = isset( $a['cp_modal_height'] ) ? $a['cp_modal_height'] : 'auto';
			$modal_size_style .= cp_add_css( 'height', $modal_ht );
			$modal_size_style .= cp_add_css( 'max-width', $a['cp_modal_width'], 'px' );
			$windowcss         = '';
		} else {
			$customcss  = 'max-width: ' . $a['cp_modal_width'] . 'px';
			$windowcss .= $box_shadow_str;
		}

		if ( $images_on_load ) {
			$lazy_custom_load_css = 'data-custom-style = "' . $customcss . '"';
			$lazy_window_load_css = 'data-window-style = "' . $css_style . '' . $windowcss . '"';
		} else {
			$lazy_custom_load_css = 'style = "' . $customcss . '"';
			$lazy_window_load_css = 'style = "' . $windowcss . '' . $css_style . '"';
		}

		// {START} - SAME FOR BEFORE & AFTER NEED TO CREATE FUNCTION IT's TEMP.
		$close_img_prop  = cp_close_image_setup( $a );
		$close_img       = $close_img_prop['close_img'];
		$close_img_class = $close_img_prop['close_img_class'];
		$close_alt       = $close_img_prop['close_alt'];

		if ( 'close_txt' !== $a['close_modal'] && isset( $a['cp_close_image_height'] ) && isset( $a['cp_close_image_width'] ) ) {
			$cp_close_image_width  = $a['cp_close_image_width'] . 'px';
			$cp_close_image_height = $a['cp_close_image_height'] . 'px';
		} else {
			$cp_close_image_width  = 'auto';
			$cp_close_image_height = 'auto';
		}

		$data_debug = get_option( 'convert_plug_debug' );

		if ( '' !== $close_alt ) {
			$close_alt = 'alt="' . $close_alt . '"';
		} else {
			$close_alt = 'close-link';
		}

		if ( 'close_txt' === $a['close_modal'] ) {
			if ( isset( $a['close_text_font'] ) && '' !== $a['close_text_font'] ) {
				$font_family = ' font-family:' . $a['close_text_font'];
			}
			$close_html = '<span style="color:' . $a['close_text_color'] . ';' . $font_family . '">' . $a['close_txt'] . '</span>';
		} elseif ( 'close_img' === $a['close_modal'] ) {
			if ( $images_on_load ) {
				$close_html = '<img data-src ="' . esc_url( $close_img ) . '"  class="cp-close-img ' . esc_attr( $close_img_class ) . esc_attr( $close_alt ) . ' width="' . esc_attr( $cp_close_image_width ) . '" height="' . esc_attr( $cp_close_image_height ) . '" />';
			} else {
				$close_html = '<img class="' . esc_attr( $close_img_class ) . '" src="' . esc_url( $close_img ) . '" ' . esc_attr( $close_alt ) . ' width="' . esc_attr( $cp_close_image_width ) . '" height="' . esc_attr( $cp_close_image_height ) . '" />';
			}
		} else {
			$close_class = ' do_not_close ';
		}

		// {END} - SAME FOR BEFORE & AFTER NEED TO CREATE FUNCTION IT's TEMP.
		if ( '1' === $a['autoload_on_scroll'] ) {
			$load_after_scroll = $a['load_after_scroll'];
		}

		if ( $a['autoload_on_duration'] ) {
			$load_on_duration = $a['load_on_duration'];
		}

		if ( isset( $a['display_close_on_duration'] ) && $a['display_close_on_duration'] && 'do_not_close' !== $a['close_modal'] ) {
			$close_btn_on_duration .= 'data-close-btnonload-delay=' . $a['close_btn_duration'] . ' ';
		}

		$dev_mode = 'disabled';
		if ( ! $a['developer_mode'] ) {
			$a['closed_cookie']     = 0;
			$a['conversion_cookie'] = 0;
			$dev_mode               = 'enabled';
		}

		if ( $a['close_modal_on'] ) {
			$close_modal_on = ' close_btn_nd_overlay';
		}

		$user_inactivity = isset( $convert_plug_settings['user_inactivity'] ) ? $convert_plug_settings['user_inactivity'] : '60';

		if ( $a['inactivity'] ) {
			$inactive_data = 'data-inactive-time="' . $user_inactivity . '"';
		}

		// scroll up to specific class.
		$enable_custom_scroll = isset( $a['enable_custom_scroll'] ) ? $a['enable_custom_scroll'] : '';
		$enable_scroll_class  = isset( $a['enable_scroll_class'] ) ? $a['enable_scroll_class'] : '';

		if ( $enable_custom_scroll ) {
			if ( '' !== $enable_scroll_class ) {
				$scroll_class = cp_get_scroll_class_init( $a['enable_scroll_class'] );
				$scroll_data  = 'data-scroll-class="' . $scroll_class . '"';
			}
		}

		// Variables.
		$global_class = ' global_modal_container';
		$schedule     = isset( $a['schedule'] ) ? $a['schedule'] : '';
		$is_scheduled = cp_is_module_scheduled( $schedule, $a['live'] );
		// Filters & Actions.
		if ( isset( $a['on_success'] ) && isset( $a['redirect_url'] ) && isset( $a['redirect_data'] ) && isset( $a['on_redirect'] ) ) {
			$download_url = '';
			if ( isset( $a['download_url'] ) ) {
				$download_url = $a['download_url'];
			}
			$data_redirect = cp_has_redirect_init( $a['on_success'], $a['redirect_url'], $a['redirect_data'], $a['on_redirect'], $download_url );
		}

		if ( isset( $a['overlay_effect'] ) ) {
			$overlay_effect = $a['overlay_effect'];
		}

		if ( isset( $a['image_displayon_mobile'] ) && isset( $a['image_resp_width'] ) ) {
			$hide_image = cp_hide_image_on_mobile_init( $a['image_displayon_mobile'], $a['image_resp_width'] );
		}

		$overaly_setting = cp_has_overaly_setting_init( $overlay_effect, $a['disable_overlay_effect'], $a['hide_animation_width'] );
		$afl_setting     = apply_filters( 'cp_get_affiliate_setting', $a['affiliate_setting'] );
		$style_id        = ( isset( $a['style_id'] ) ) ? $a['style_id'] : '';
		$style_class     = ( isset( $a['style_class'] ) ) ? $a['style_class'] : '';

		// Filters.
		$custom_class      = cp_get_custom_class_init( $a['custom_class'], $style_id );
		$modal_exit_intent = apply_filters( 'cp_has_enabled_or_disabled', $a['modal_exit_intent'] );

		if ( isset( $a['add_to_cart'] ) ) {
			$add_to_cart = apply_filters( 'cp_has_enabled_or_disabled', $a['add_to_cart'] );
		}

		$load_on_refresh = apply_filters( 'cp_has_enabled_or_disabled', $a['display_on_first_load'] );
		$load_on_count   = '';
		if ( 'disabled' === $load_on_refresh ) {
			$load_on_count = ( isset( $a['page_load_count'] ) ) ? $a['page_load_count'] : '';
		}
		$global_modal_settings = cp_modal_global_settings_init( $a['closed_cookie'], $a['conversion_cookie'], $style_id, $style_details );

		$placeholder_color = ( isset( $a['placeholder_color'] ) ) ? $a['placeholder_color'] : '';
		$placeholder_font  = ( isset( $a['placeholder_font'] ) && '' !== $a['placeholder_font'] ) ? $a['placeholder_color'] : 'inherit';

		$image_position = ( isset( $a['image_position'] ) ) ? $a['image_position'] : '';
		$exit_animation = isset( $a['exit_animation'] ) ? $a['exit_animation'] : 'cp-overlay-none';

		$schedular_tmz_offset = get_option( 'gmt_offset' );
		if ( '' === $schedular_tmz_offset ) {
			$schedular_tmz_offset = get_offset_by_time_zone( get_option( 'timezone_string' ) );
		}

		// Container Classes.
		if ( isset( $a['mailer'] ) && ( 'custom-form' === $a['mailer'] ) ) {
			$cp_modal_content_class .= ' cp-custom-form-container';
			// Add - Contact Form 7 Styles.
			$is_cf7_styles_enable    = ( isset( $data_debug['cp-cf7-styles'] ) ) ? $data_debug['cp-cf7-styles'] : 1;
			$cp_modal_content_class .= ( $is_cf7_styles_enable ) ? ' cp-default-cf7-style1' : '';
		}

		$impression_disable = ( isset( $convert_plug_settings['cp-disable-impression'] ) ) ? $convert_plug_settings['cp-disable-impression'] : 0;
		if ( $impression_disable ) {
			$impression_disable_class = 'cp-disabled-impression';
		}

		// check if modal should be triggered after post.
		$enable_after_post = (int) ( isset( $a['enable_after_post'] ) ? $a['enable_after_post'] : 0 );
		if ( $enable_after_post ) {
			$custom_class .= ' cp-after-post';
		}

		// check if modal should be triggerd if items in the cart.
		$add_to_cart = (int) ( isset( $a['add_to_cart'] ) ? $a['add_to_cart'] : 0 );
		if ( $add_to_cart ) {
			$custom_class .= ' cp-items-in-cart';
		}

		// check if inline display is set.
		$is_inline = ( isset( $a['display'] ) && 'inline' === $a['display'] ) ? true : false;

		if ( $is_inline ) {
			$custom_class               .= ' cp-open';
			$close_class                 = 'do_not_close';
			$a['modal_overlay_bg_color'] = 'rgba( 255,255,255,0 );';
			$cp_close_inline             = (int) ( isset( $timezone_settings['cp-close-inline'] ) ? $timezone_settings['cp-close-inline'] : 0 );
			$close_inline                = ( $cp_close_inline ) ? 'cp-close-inline' : 'cp-do-not-close-inline';
			$inline_text                 = 'cp-modal-inline ' . $close_inline;
		} else {
			$custom_class .= ' cp-modal-global';
		}

		/**
		 * Contact Form - Layouts.
		 */
		$form_layout          = ( isset( $a['form_layout'] ) ) ? $a['form_layout'] : '';
		$data_form_layout     = 'data-form-layout="' . $form_layout . '"';
		$after_content_scroll = isset( $data_debug['after_content_scroll'] ) ? $data_debug['after_content_scroll'] : '50';
		$after_content_data   = 'data-after-content-value="' . $after_content_scroll . '"';
		$cp_onload            = ( isset( $a['manual'] ) && 'true' === $a['manual'] ) ? '' : 'cp-onload cp-global-load ';

		$modal_bg_color = isset( $a['modal_bg_color'] ) ? $a['modal_bg_color'] : '';

		if ( 'cp-modal-window-size' === $a['modal_size'] ) {
			$global_class .= ' cp-window-overlay';
		}

		// form display/hide after sucessfull submission.
		$form_action_onsubmit = isset( $a['form_action_on_submit'] ) ? $a['form_action_on_submit'] : '';

		if ( 'reappear' === $form_action_onsubmit ) {
			$form_data_onsubmit  = 'data-form-action = reappear';
			$form_data_onsubmit .= ' data-form-action-time = ' . $a['form_reappear_time'] . '';
		} elseif ( 'disappears' === $form_action_onsubmit ) {
			$form_data_onsubmit  = 'data-form-action = disappear';
			$form_data_onsubmit .= ' data-form-action-time =' . $a['form_disappears_time'] . '';
		}

		$inline_test = ( $is_inline ) ? $inline_text : 'cp-overlay ';
		// Custom selector.
		$custom_selector = '';
		$custom_selector = isset( $a['custom_selector'] ) ? cp_get_custom_slector_init( $a['custom_selector'] ) : '';
		if ( '' !== $custom_selector ) {
			$custom_class .= ' ' . cp_get_custom_slector_class_init( $a['custom_selector'] );
		}

		$content_uid        = 'content-' . $a['uid'];
		$overlay_show_data  = '';
		$overlay_show_data .= 'data-class-id="' . $content_uid . '" ';
		$overlay_show_data .= $referrer_data . ' ';
		$overlay_show_data .= $after_content_data . ' ';
		$overlay_show_data .= 'data-overlay-class = "overlay-zoomin" ';
		$overlay_show_data .= 'data-onload-delay = "' . esc_attr( $load_on_duration ) . '"';
		$overlay_show_data .= 'data-onscroll-value = "' . esc_attr( $load_after_scroll ) . '"';
		$overlay_show_data .= 'data-exit-intent = "' . esc_attr( $modal_exit_intent ) . '"';

		$overlay_show_data .= 'data-add-to-cart = "' . esc_attr( $add_to_cart ) . '"';
		$overlay_show_data .= $global_modal_settings . ' ';
		$overlay_show_data .= $inactive_data . ' ';
		$overlay_show_data .= $scroll_data . ' ';
		$overlay_show_data .= 'data-custom-class = "' . esc_attr( $custom_class ) . '"';
		$overlay_show_data .= 'data-load-on-refresh = "' . esc_attr( $load_on_refresh ) . '"';
		$overlay_show_data .= 'data-dev-mode = "' . esc_attr( $dev_mode ) . '"';
		$overlay_show_data .= 'data-custom-selector = "' . esc_attr( $custom_selector ) . '"';

		$onload_class  = '';
		$onload_class .= 'overlay-show ' . $cp_onload . ' ' . esc_attr( $custom_class );

		$overlay_class  = '';
		$overlay_class .= 'cp-module cp-modal-popup-container ' . esc_attr( $style_id ) . ' ' . $style_class . '-container';
		if ( $is_inline ) {
			$overlay_class .= ' cp-inline-modal-container';
		}

		// global container class and data.
		$global_cont_data  = '';
		$global_cont_data .= $is_scheduled;
		$global_cont_data .= $global_modal_settings;
		$global_cont_data .= 'data-placeholder-font="' . $placeholder_font . '"';
		$global_cont_data .= 'data-custom-class = "' . esc_attr( $custom_class ) . '"';
		$global_cont_data .= 'data-class = "' . esc_attr( $content_uid ) . '"';
		$global_cont_data .= 'data-load-on-refresh = "' . esc_attr( $load_on_refresh ) . '"';
		$global_cont_data .= 'data-load-on-count = "' . esc_attr( $load_on_count ) . '"';
		$global_cont_data .= $hide_image . ' ';
		$global_cont_data .= $afl_setting . ' ';
		$global_cont_data .= $overaly_setting . ' ';
		$global_cont_data .= $data_redirect . ' ';
		$global_cont_data .= esc_attr( $close_btn_on_duration ) . ' ';
		$global_cont_data .= $autoclose_data . ' ';
		$global_cont_data .= esc_attr( $form_data_onsubmit ) . ' ';
		$global_cont_data .= ' data-tz-offset = "' . esc_attr( $schedular_tmz_offset ) . '"';
		$global_cont_data .= 'data-image-position = "' . esc_attr( $image_position ) . '"';
		$global_cont_data .= 'data-placeholder-color = "' . esc_attr( $placeholder_color ) . '"';
		$global_cont_data .= 'data-timezonename = "' . esc_attr( $timezone_name ) . '"';
		$global_cont_data .= 'data-timezone = "' . esc_attr( $timezone ) . '"';

		$global_cont_class  = '';
		$global_cont_class .= $content_uid . ' ';
		$global_cont_class .= esc_attr( $inline_test ) . ' ';
		$global_cont_class .= esc_attr( $close_modal_on ) . ' ';
		$global_cont_class .= esc_attr( $overlay_effect ) . ' ';
		$global_cont_class .= esc_attr( $global_class ) . ' ';
		$global_cont_class .= esc_attr( $close_class ) . ' ';
		$global_cont_class .= esc_attr( $impression_disable_class ) . ' ';

		// Custom class for specific module.
		if ( isset( $a['custom_css_class'] ) ) {
			$global_cont_class .= esc_attr( $a['custom_css_class'] );
		}

		$overlay_style       = '';
		$overlay_color_style = '';
		$overlay_type        = isset( $a['module_overlay_color_type'] ) ? $a['module_overlay_color_type'] : '';
		$overlay_color       = isset( $a['modal_overlay_bg_color'] ) ? $a['modal_overlay_bg_color'] : '';
		$overlay_img         = isset( $a['overlay_bg_image'] ) ? $a['overlay_bg_image'] : '';
		$overlay_custom_url  = isset( $a['overlay_bg_image_custom_url'] ) ? $a['overlay_bg_image_custom_url'] : '';
		$overlay_img_src     = isset( $a['overlay_bg_image_src'] ) ? $a['overlay_bg_image_src'] : '';
		$overlay_img_color   = isset( $a['modal_img_overlay_bg_color'] ) ? $a['modal_img_overlay_bg_color'] : '';
		$overlay_color_style = 'background-color:' . $overlay_color . ';';

		if ( 'image' === $overlay_type ) {
			$overlay_color_style = 'background-color:' . $overlay_img_color . ';';
		}

		$cp_overlay_image = cp_get_module_images_new( $overlay_img, $overlay_img_src, $overlay_custom_url );

		// get overlay image size.
		$overlay_bg_setting = '';
		if ( isset( $a['overlay_bg'] ) && false !== strpos( $a['overlay_bg'], '|' ) ) {
			$overlay_bg_setting .= cp_get_image_size_opt( $a['overlay_bg'] );
		}

		// overlay image style.
		if ( $overlay_type && 'image' === $overlay_type ) {
			$overlay_style = 'background-image:url(' . $cp_overlay_image . ');' . $overlay_bg_setting . ';';
		}

		// Close gravity form after submission or not?
		$cp_close_gravity = isset( $convert_plug_settings['cp-close-gravity'] ) ? $convert_plug_settings['cp-close-gravity'] : 1;
		$gr_data          = '';
		$gr_data          = 'data-close-gravity = "' . esc_attr( $cp_close_gravity ) . '"';

		ob_start();

		?>
		<?php if ( ! $is_inline ) { ?>
		<div <?php echo wp_kses_post( $overlay_show_data ); ?> class="<?php echo esc_attr( $onload_class ); ?>" data-module-type="modal" ></div>
		<?php } ?>

		<div <?php echo wp_kses_post( $data_form_layout ); ?> class="<?php echo esc_attr( $overlay_class ); ?> " data-style-id ="<?php echo esc_attr( $style_id ); ?>"  data-module-name ="modal" data-close-gravity = "<?php echo esc_attr( $cp_close_gravity ); ?>" >
			<div class="<?php echo wp_kses_post( $global_cont_class ); ?>" <?php echo wp_kses_post( $global_cont_data ); ?>  style=" <?php echo esc_attr( $overlay_style ); ?>" >
				<?php
				if ( ! $is_inline ) {
					?>
<div class="cp-overlay-background" style=" <?php echo esc_attr( $overlay_color_style ); ?>"></div><?php } ?>
	<div class="cp-modal <?php echo esc_attr( $a['modal_size'] ); ?>" style="<?php echo esc_attr( $modal_size_style ); ?>">
					<div class="cp-animate-container" <?php echo wp_kses_post( $overaly_setting ); ?> data-exit-animation="<?php echo esc_attr( $exit_animation ); ?>">
						<div class="cp-modal-content <?php echo esc_attr( $cp_modal_content_class ); ?>"   <?php echo wp_kses_post( $lazy_window_load_css ); ?> >	
						<?php if ( isset( $a['modal_size'] ) && 'cp-modal-custom-size' !== $a['modal_size'] ) { ?>
							<div class="cp-modal-body-overlay cp_fs_overlay" style="<?php echo esc_attr( $modal_body_css ); ?>;<?php echo esc_attr( $inset ); ?>;"></div>
							<?php } ?>		
							<div class="cp-modal-body <?php echo esc_attr( $style_class ) . ' ' . esc_attr( $el_class ); ?>" <?php echo wp_kses_post( $lazy_custom_load_css ); ?> >
								<?php if ( 'cp-modal-custom-size' === $a['modal_size'] ) { ?>
								<div class="cp-modal-body-overlay cp_cs_overlay" style="<?php echo esc_attr( $modal_body_css ); ?>;<?php echo esc_attr( $inset ); ?>;"></div>
								<?php } ?>
								<?php
	}
}


add_filter( 'cp_modal_global_before', 'cp_modal_global_before_init' );

if ( ! function_exists( 'cp_modal_global_after_init' ) ) {
	/**
	 * Function Name: cp_modal_global_after_init Modal After.
	 *
	 * @param  array $a settings array.
	 * @since 0.1.5
	 */
	function cp_modal_global_after_init( $a ) {

		$edit_link = '';
		if ( is_user_logged_in() ) {
			// if user has access to CP_PLUS_SLUG, then only display edit style link.
			if ( current_user_can( 'access_cp' ) ) {
				if ( isset( $a['style_id'] ) ) {
					$edit_link = cp_get_edit_link( $a['style_id'], 'modal', $a['style'] );
				}
			}
		}

		if ( ! isset( $a['modal_size'] ) ) {
			$a['modal_size'] = 'cp-modal-custom-size';
		}

		$afilate_link  = cp_get_affiliate_link_init( $a['affiliate_setting'], $a['affiliate_username'] );
		$afilate_class = cp_get_affiliate_class_init( $a['affiliate_setting'], $a['modal_size'] );
		$style_id      = ( isset( $a['style_id'] ) ) ? $a['style_id'] : '';

		if ( 'close_txt' !== $a['close_modal'] && isset( $a['cp_close_image_height'] ) && isset( $a['cp_close_image_width'] ) ) {
			$cp_close_image_width  = $a['cp_close_image_width'] . 'px';
			$cp_close_image_height = $a['cp_close_image_height'] . 'px';
		} else {
			$cp_close_image_width  = 'auto';
			$cp_close_image_height = 'auto';
		}
		// {START} - SAME FOR BEFORE & AFTER NEED TO CREATE FUNCTION IT's TEMP.
		$close_img_class       = '';
		$close_img             = '';
		$close_alt             = '';
		$close_html            = '';
		$el_class              = '';
		$modal_size_style      = '';
		$close_class           = '';
		$close_tooltip         = '';
		$close_tooltip_end     = '';
		$close_img_prop        = cp_close_image_setup( $a );
		$close_img             = $close_img_prop['close_img'];
		$close_img_class       = $close_img_prop['close_img_class'];
		$close_alt             = $close_img_prop['close_alt'];
		$close_alt             = ( '' !== $close_alt ) ? 'alt=' . $close_alt . '' : 'close-link';
		$convert_plug_settings = get_option( 'convert_plug_settings' );
		$images_on_load        = isset( $convert_plug_settings['cp-lazy-img'] ) ? $convert_plug_settings['cp-lazy-img'] : 0;

		if ( isset( $a['content_padding'] ) && $a['content_padding'] ) {
			$el_class .= 'cp-no-padding ';
		}

		if ( 'close_txt' === $a['close_modal'] ) {
			$close_class .= 'cp-text-close';
			if ( '1' === $a['close_modal_tooltip'] ) {
				$close_tooltip     = '<span class="cp-close-tooltip cp-tooltip-icon has-tip cp-tipcontent-' . $a['style_id'] . 'data-classes="close-tip-content-' . $a['style_id'] . '" data-position="left"  title="' . $a['tooltip_title'] . '"  data-color="' . $a['tooltip_title_color'] . '" data-bgcolor="' . $a['tooltip_background'] . '" data-closeid ="cp-tipcontent-' . $a['style_id'] . '" data-font-family ="' . $a['tooltip_text_font'] . '">';
				$close_tooltip_end = '</span>';
			}
			if ( isset( $a['close_text_font'] ) && '' !== $a['close_text_font'] ) {
				$font_family = ' font-family:' . $a['close_text_font'];
			}

			$close_html = '<span style="color:' . $a['close_text_color'] . ';' . $font_family . '">' . $a['close_txt'] . '</span>';
		} elseif ( 'close_img' === $a['close_modal'] ) {
			$close_class .= 'cp-image-close';
			if ( $images_on_load ) {
				$close_html = '<img data-src ="' . esc_url( $close_img ) . '" class="cp-close-img ' . esc_attr( $close_img_class ) . '"' . esc_attr( $close_alt ) . ' width="' . esc_attr( $cp_close_image_width ) . '" height="' . esc_attr( $cp_close_image_height ) . '"/>';
			} else {
				$close_html = '<img class="' . esc_attr( $close_img_class ) . '" src="' . esc_url( $close_img ) . '" ' . esc_attr( $close_alt ) . ' width="' . esc_attr( $cp_close_image_width ) . '" height="' . esc_attr( $cp_close_image_height ) . '" />';
			}
		} else {
			$close_class = 'do_not_close';
		}

		if ( isset( $a['display_close_on_duration'] ) && $a['display_close_on_duration'] && 'do_not_close' !== $a['close_modal'] ) {
			$close_class .= ' cp-hide-close';
		}

		// {END} - SAME FOR BEFORE & AFTER NEED TO CREATE FUNCTION IT's TEMP.
		$tooltip_position = 'left';
		if ( 'cp-modal-custom-size' === $a['modal_size'] ) {
			$tooltip_position = 'top';
		}

		$close_adjacent_position = ( isset( $a['adjacent_close_position'] ) ? $a['adjacent_close_position'] : 'cp-adjacent-right' );
		if ( '' !== $close_adjacent_position ) {
			$tooltip_position = cp_get_tooltip_position( $close_adjacent_position );
		}

		$close_position = ( isset( $a['close_position'] ) ? $a['close_position'] : '' );

		$affiliate_fullsize = '';
		if ( 'cp-modal-custom-size' !== $a['modal_size'] ) {
			$affiliate_fullsize = 'cp-affiliate-fullsize';
		}

		$css_code1 = '';
		if ( ! isset( $a['has_content_border'] ) || ( isset( $a['has_content_border'] ) && $a['has_content_border'] ) ) {
			$css_code1 .= generate_border_css( $a['border'] );
		}

		$msg_bg_image_opts = isset( $a['succes_bg_img_src'] ) ? $a['succes_bg_img_src'] : '';
		$form_process_css  = '';
		$form_process_css  = $css_code1 . ';';
		$form_process_css .= 'border-width: 0px;';
		$form_process_css .= 'background-image:url(' . $msg_bg_image_opts . ')';

		// check if inline display is set.
		$is_inline = ( isset( $a['display'] ) && 'inline' === $a['display'] ) ? true : false;
		if ( $is_inline ) {
			$a['close_modal'] = 'do_not_close';
		}

		// add nounce field to modal.
		$nonce = wp_create_nonce( 'cp-impress-nonce' );

		?>
		<input type="hidden" class="cp-impress-nonce" name="cp-impress-nonce" value="<?php echo esc_attr( $nonce ); ?>">

	</div><!-- .cp-modal-body -->

		<?php
		if ( '' !== $edit_link ) {

			$edit_link_text = 'Edit With ' . CP_PLUS_NAME;

			$edit_link_txt = apply_filters( 'cp_style_edit_link_text', $edit_link_text );

			echo "<div class='cp_edit_link'><a target='_blank' href=" . esc_attr( esc_url( $edit_link ) ) . " rel ='noopener'>" . esc_attr( $edit_link_txt ) . '</a></div>';
		}

			$msg_color = isset( $a['message_color'] ) ? $a['message_color'] : '';
		?>
</div><!-- .cp-modal-content -->

		<?php if ( isset( $a['form_layout'] ) && 'cp-form-layout-4' !== $a['form_layout'] ) { ?>
			<div class="cp-form-processing-wrap" style="<?php echo esc_attr( $form_process_css ); ?>;">
				<div class="cp-form-after-submit">
					<div class ="cp-form-processing">
						<div class ="cp-form-processing" >
							<div class="smile-absolute-loader" style="visibility: visible;">
								<div class="smile-loader" style = "" >
									<div class="smile-loading-bar"></div>
									<div class="smile-loading-bar"></div>
									<div class="smile-loading-bar"></div>
									<div class="smile-loading-bar"></div>
								</div>
							</div>
						</div>
					</div>
					<div class ="cp-msg-on-submit" style="color:<?php echo esc_attr( $msg_color ); ?>"></div>
				</div>
			</div>
	<?php } ?>

		<?php
		$close_adj_class         = '';
		$close_adjacent_position = ( isset( $a['adjacent_close_position'] ) ? $a['adjacent_close_position'] : 'cp-adjacent-right' );

		$close_adj_class .= cp_get_close_adj_position( $close_adjacent_position );

		if ( 'close_img' === $a['close_modal'] && 'out_modal' !== $a['close_position'] ) {
			?>

			<?php
			if ( 'adj_modal' === $a['close_position'] ) {
				$close_overlay_class = 'cp-adjacent-close';
			} else {
				$close_overlay_class = 'cp-inside-close';
			}
			$close_overlay_class .= $close_adj_class;

			?>
	<div class="cp-overlay-close <?php echo esc_attr( $close_class ) . ' ' . esc_attr( $close_overlay_class ); ?>" style="width: <?php echo esc_attr( $cp_close_image_width ); ?>">
			<?php if ( '1' === $a['close_modal_tooltip'] ) { ?>
		<span class=" cp-tooltip-icon cp-inside-tip has-tip cp-tipcontent-<?php echo esc_attr( $a['style_id'] ); ?>" data-classes="close-tip-content-<?php echo esc_attr( $a['style_id'] ); ?>" data-offset="20"  data-position="<?php echo esc_attr( $tooltip_position ); ?>"  title="<?php echo esc_attr( html_entity_decode( stripslashes( $a['tooltip_title'] ), ENT_QUOTES, 'utf-8' ) ); ?>"  data-color="<?php echo esc_attr( $a['tooltip_title_color'] ); ?>" data-font-family="<?php echo esc_attr( $a['tooltip_text_font'] ); ?>" data-bgcolor="<?php echo esc_attr( $a['tooltip_background'] ); ?>" data-closeid ="cp-tipcontent-<?php echo esc_attr( $a['style_id'] ); ?>">
			<?php } ?>
			<?php echo wp_kses_post( $close_html ); ?>
			<?php
			if ( '1' === $a['close_modal_tooltip'] ) {
				?>
			</span><?php } ?>
		</div>

				<?php } ?>
	</div><!-- .cp-animate-container -->

		<?php if ( $is_inline ) { ?>
	<span class="cp-modal-inline-end" data-style="<?php echo esc_attr( $style_id ); ?>"></span>
	<?php } ?>

</div><!-- .cp-modal -->

		<?php

		if ( '1' === $a['affiliate_setting'] || 1 === $a['affiliate_setting'] ) {
			if ( '' === $a['affiliate_title'] ) {
				$a['affiliate_title'] = 'Powered by ' . $a['affiliate_username'];
			}
			?>
	<div class ="cp-affilate-link cp-responsive">
		<a href="<?php echo esc_attr( esc_url( $afilate_link ) ); ?>" target= "_blank" rel="noopener"><?php echo do_shortcode( html_entity_decode( $a['affiliate_title'], ENT_QUOTES, 'utf-8' ) ); ?></a>
	</div>
	<?php } ?><!-- .affiliate link for fullscreen -->

		<?php if ( ( 'out_modal' === $a['close_position'] && 'do_not_close' !== $a['close_modal'] ) || 'close_txt' === $a['close_modal'] ) { ?>
	<div class="cp-overlay-close cp-outside-close <?php echo esc_attr( $close_class ); ?> <?php echo esc_attr( $close_adj_class ); ?>"  style="width: <?php echo esc_attr( $cp_close_image_width ); ?>">
			<?php if ( '1' === $a['close_modal_tooltip'] ) { ?>
		<span class=" cp-close-tooltip cp-tooltip-icon  has-tip cp-tipcontent-<?php echo esc_attr( $a['style_id'] ); ?>" data-classes="close-tip-content-<?php echo esc_attr( $a['style_id'] ); ?>" data-position="<?php echo esc_attr( $tooltip_position ); ?>"  title="<?php echo esc_attr( html_entity_decode( stripslashes( $a['tooltip_title'] ), ENT_QUOTES, 'utf-8' ) ); ?>"  data-color="<?php echo esc_attr( $a['tooltip_title_color'] ); ?>"  data-font-family="<?php echo esc_attr( $a['tooltip_text_font'] ); ?>" data-bgcolor="<?php echo esc_attr( $a['tooltip_background'] ); ?>" data-closeid ="cp-tipcontent-<?php echo esc_attr( $a['style_id'] ); ?>" data-offset="20">
			<?php } ?>
			<?php echo wp_kses_post( $close_html ); ?>
			<?php
			if ( '1' === $a['close_modal_tooltip'] ) {
				?>
			</span><?php } ?>
		</div>
		<?php } ?>
	</div><!-- .cp-overlay -->
</div><!-- .cp-modal-popup-container -->
		<?php
	}
}
add_filter( 'cp_modal_global_after', 'cp_modal_global_after_init' );

if ( ! function_exists( 'cp_close_image_setup' ) ) {
	/**
	 * Function Name: cp_close_image_setup.
	 *
	 * @param  array $a array parameter.
	 * @return array    array parameter.
	 */
	function cp_close_image_setup( $a ) {
		$close_img       = '';
		$close_img_class = '';
		$close_alt       = '';
		$close_alt       = 'close-link';
		if ( ! isset( $a['close_image_src'] ) ) {
			$a['close_image_src'] = 'upload_img';
		}

		if ( 'upload_img' === $a['close_image_src'] ) {

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
					$close_img = apply_filters( 'cp_get_wp_image_url', $a['close_img'] );
					$close_img = cp_get_protocol_settings_init( $close_img );

					$close_img_alt = explode( '|', $a['close_img'] );
					if ( 2 < count( $close_img_alt ) ) {
						$close_alt = $close_img_alt[2];
					}
				}
			}
		} elseif ( 'custom_url' === $a['close_image_src'] ) {
			$close_img = $a['modal_close_img_custom_url'];
		} elseif ( 'pre_icons' === $a['close_image_src'] ) {
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
