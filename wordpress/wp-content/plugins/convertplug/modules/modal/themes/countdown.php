<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

if ( ! function_exists( 'modal_theme_countdown' ) ) {
	/**
	 * Function Name: modal_theme_countdown.
	 *
	 * @param  array  $atts    array parameters.
	 * @param  string $content string parameter.
	 * @return mixed          string parameter.
	 */
	function modal_theme_countdown( $atts, $content = null ) {
		/**
		 * Define Variables.
		 */
		global $cp_form_vars;

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
			'style_class' => 'cp-count-down',
			'uid'         => $uid,
			'uid_class'   => $uid_class,
		);

		/**
		 * Merge short code variables arrays.
		 *
		 * @array   $individual_vars        Individual style EXTRA short-code variables.
		 * @array   $cp_form_vars           CP Form global short-code variables.
		 * @array   $style_settings         Individual style short-code variables.
		 * @array   $atts                   short-code attributes.
		 */
		$all = array_merge(
			$individual_vars,
			$style_settings,
			$cp_form_vars,
			$style_settings,
			$atts
		);

		// Extract short code variables.
		$a = shortcode_atts( $all, $style_settings );

		// Before filter.
		apply_filters_ref_array( 'cp_modal_global_before', array( $a ) );

		// check modal_back_type - gradient/simple/image.
		$module_bg_color_type = ( isset( $a['module_bg_color_type'] ) ) ? $a['module_bg_color_type'] : '';
		$modal_bg_img_color   = isset( $a['modal_bg_img_color'] ) ? $a['modal_bg_img_color'] : '';
		$bg_type_set          = false;
		$old_user             = true;
		if ( '' !== $module_bg_color_type ) {
			$module_bg_gradient = ( isset( $a['module_bg_gradient'] ) ) ? $a['module_bg_gradient'] : '';
			$bg_type_set        = true;
			$old_user           = false;
		}
		if ( ! $old_user && 'gradient' === $module_bg_color_type && $bg_type_set ) {
			$modal_countdn_css = generate_back_gradient( $module_bg_gradient );
		} elseif ( 'image' === $module_bg_color_type ) {
			$modal_countdn_css = 'background-color:' . $modal_bg_img_color . ';';
		} else {
			$modal_countdn_css = 'background-color:' . $a['modal_bg_color'] . ';';
		}

		?>
		<!-- BEFORE CONTENTS -->
		<div class="cp-row cp-counter-container" style = '<?php echo esc_attr( $modal_countdn_css ); ?>'>
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 cp-text-container" >
				<div class="cp-title-container 
				<?php
				if ( '' === trim( $a['modal_title1'] ) ) {
					echo 'cp-empty'; }
				?>
					">
					<h2 class="cp-title cp_responsive" style="color: <?php echo esc_attr( $a['modal_title_color'] ); ?>;"><?php echo do_shortcode( html_entity_decode( stripcslashes( $a['modal_title1'] ), ENT_QUOTES, 'utf-8' ) ); ?></h2>
				</div>
				<div class="cp-desc-container 
				<?php
				if ( '' === trim( $a['modal_short_desc1'] ) ) {
					echo 'cp-empty'; }
				?>
					">
					<div class="cp-description cp_responsive" style="color: <?php echo esc_attr( $a['modal_desc_color'] ); ?>;"><?php echo do_shortcode( html_entity_decode( stripcslashes( $a['modal_short_desc1'] ), ENT_QUOTES, 'utf-8' ) ); ?></div>
				</div>
				<div class="cp-count-down-container cp-clear"  >
					<div class="counter-overlay" style = 'background:<?php echo esc_attr( $a['counter_container_bg_color'] ); ?>'></div>
					<div class="cp-count-down-desc"><?php echo do_shortcode( html_entity_decode( stripcslashes( $a['count_down_title'] ), ENT_QUOTES, 'utf-8' ) ); ?></div>
					<?php
							// Embed count down.
					apply_filters_ref_array( 'cp_get_count_down', array( $a ) );
					?>
				</div>
				<div class='cp-row cp-form-seperator cp-clear' >		
					<div class="counter-desc-overlay" style = "background:<?php echo esc_attr( $a['form_bg_color'] ); ?>"></div>
					<div class="cp-short-desc-container 
					<?php
					if ( '' === trim( $a['modal_content'] ) ) {
						echo 'cp-empty'; }
					?>
						">
						<div class="cp-short-description cp-desc cp_responsive " ><?php echo do_shortcode( html_entity_decode( stripcslashes( $a['modal_content'] ), ENT_QUOTES, 'utf-8' ) ); ?></div>
					</div> 
					<div class="cp-form-container">
						<div class="cp-submit-container">
							<?php
							// Embed CP Form.
							apply_filters_ref_array( 'cp_get_form', array( $a ) );
							?>
						</div>
					</div>
				</div>	            
			</div><!-- .col-lg-7 col-md-7 col-sm-7 col-xs-12 cp-text-container -->		            
		</div>
		<!-- AFTER CONTENTS -->
		<?php
		// After filter.
		apply_filters_ref_array( 'cp_modal_global_after', array( $a ) );

		return ob_get_clean();
	}
}
