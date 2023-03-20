<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

if ( ! function_exists( 'info_bar_theme_weekly_article' ) ) {
	/**
	 * Function Name: info_bar_theme_weekly_article.
	 *
	 * @param  array  $atts    setting array.
	 * @param  string $content string content.
	 * @return mixed          content.
	 */
	function info_bar_theme_weekly_article( $atts, $content = null ) {
		$style_id         = '';
		$settings_encoded = '';
		shortcode_atts(
			array(
				'style_id'         => '',
				'settings_encoded' => '',
			),
			$atts
		);
		$style_id         = isset( $atts['style_id'] ) ? $atts['style_id'] : '';
		$settings_encoded = $atts['settings_encoded'];

		$settings       = base64_decode( $settings_encoded );
		$style_settings = json_decode( $settings, true );

		foreach ( $style_settings as $key => $setting ) {
			$style_settings[ $key ] = apply_filters( 'smile_render_setting', $setting );
		}

		unset( $style_settings['style_id'] );

		// Generate UID.
		$uid       = uniqid();
		$uid_class = 'content-' . $uid;

		// Individual style variables.
		$individual_vars = array(
			'uid'         => $uid,
			'uid_class'   => $uid_class,
			'style_class' => 'cp-weekly-article',
		);

		global $cp_form_vars;

		/**
		 * Merge short code variables arrays.
		 *
		 * @array   $individual_vars        Individual style EXTRA short code variables.
		 * @array   $style_settings         Individual style short code variables.
		 * @array   $cp_form_vars           CP Form global short code variables.
		 */
		$all = array_merge(
			$individual_vars,
			$style_settings,
			$cp_form_vars,
			$atts
		);

		// Extract short code variables.
		$a = shortcode_atts( $all, $style_settings );

		/** = Before filter.
		 *-----------------------------------------------------------*/
		apply_filters_ref_array( 'cp_ib_global_before', array( $a ) );

		$el_class            = '';
		$info_bar_size_style = '';
		$close_class         = '';

		$button_css = 'background:' . $a['button_bg_color'] . ';';

		// info bar image.
		$cp_module_img_custom_url = isset( $a['info_bar_img_custom_url'] ) ? $a['info_bar_img_custom_url'] : '';
		$cp_module_img_src        = isset( $a['info_bar_img_src'] ) ? $a['info_bar_img_src'] : '';
		$cp_module_image          = isset( $a['info_bar_image'] ) ? $a['info_bar_image'] : '';

		// Filters & Actions.
		$info_bar_image = cp_get_module_image_url_init( 'info-bar', $cp_module_img_custom_url, $cp_module_img_src, $cp_module_image );

		// Filters & Actions for modal_image_alt.
		$info_bar_alt = cp_get_module_image_alt_init( 'info-bar', $cp_module_img_src, $cp_module_image );

		$image_width  = isset( $a['image_width'] ) ? $a['image_width'] : '';
		$image_height = isset( $a['image_height'] ) ? $a['image_height'] : '';

		$imagestyle  = cp_add_css( 'max-width', $a['image_size'], 'px' );
		$imagestyle .= cp_add_css( 'width', $image_width, 'px' );
		$imagestyle .= cp_add_css( 'height', $image_height, 'px' );

		$img_class = '';
		if ( $a['image_displayon_mobile'] ) {
			$img_class .= 'cp_ifb_hide_img';
		}

		// Merge arrays - 'shortcode atts' & 'style options'.
		$a = array_merge( $a, $atts );

		$convert_plug_settings = get_option( 'convert_plug_settings' );
		$images_on_load        = isset( $convert_plug_settings['cp-lazy-img'] ) ? $convert_plug_settings['cp-lazy-img'] : 1;

		?>
		<?php if ( '' !== $info_bar_image ) { ?>
		<div class="cp-image-container">
			<?php if ( $images_on_load ) { ?>
				<img style="<?php echo esc_attr( $imagestyle ); ?>" data-src="<?php echo esc_url( $info_bar_image ); ?>" class="cp-image <?php echo esc_attr( $img_class ); ?>" <?php echo esc_attr( $info_bar_alt ); ?> width=<?php echo esc_attr( $image_width ); ?> height=<?php echo esc_attr( $image_height ); ?> >
			<?php } else { ?>
				<img style="<?php echo esc_attr( $imagestyle ); ?>" src="<?php echo esc_url( $info_bar_image ); ?>" class="cp-image <?php echo esc_attr( $img_class ); ?>" <?php echo esc_attr( $info_bar_alt ); ?> width=<?php echo esc_attr( $image_width ); ?> height=<?php echo esc_attr( $image_height ); ?> >
			<?php } ?>		
		</div> <?php } ?>
		<div class="cp-msg-container <?php echo ( '' === trim( $a['infobar_title'] ) ? 'cp-empty' : '' ); ?>">
			<span class="cp-info-bar-msg"><?php echo do_shortcode( html_entity_decode( stripcslashes( $a['infobar_title'] ) ) ); ?></span>
		</div>
		<div class="cp-flex cp-sub-container">
			<div class="cp-form-container">
				<?php
						/**
						 * Embed CP Form.
						 */
						apply_filters_ref_array( 'cp_get_form', array( $a ) );
				?>
					</div>
					<div class="cp-flex cp-info-bar-desc-container <?php echo ( '' === trim( $a['infobar_description'] ) ? 'cp-empty' : '' ); ?>">
						<div class="cp-info-bar-desc"><?php echo do_shortcode( html_entity_decode( stripcslashes( $a['infobar_description'] ) ) ); ?></div>
					</div>
				</div>
				<?php

				/** = After filter.
				 * -----------------------------------------------------------*/
				apply_filters_ref_array( 'cp_ib_global_after', array( $a ) );

				return ob_get_clean();
	}
}
