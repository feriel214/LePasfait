<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

if ( ! function_exists( 'modal_theme_blank' ) ) {
	/**
	 * Function Name: modal_theme_blank.
	 *
	 * @param  array  $atts    array parameters.
	 * @param  string $content string parameter.
	 * @return mixed          string parameter.
	 */
	function modal_theme_blank( $atts, $content = null ) {

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
			'style_class' => 'cp-blank',
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
			$atts
		);

		// Extract short code variables.
		$a = shortcode_atts( $all, array() );

		$cp_row_class    = '';
		$cp_row_eq_class = '';
		if ( isset( $a['cp_custom_height'] ) && '1' === $a['cp_custom_height'] ) {
			$cp_row_class    = 'cp-row-center';
			$cp_row_eq_class = 'cp-row-equalized-center';
		}

		// Before filter.
		apply_filters_ref_array( 'cp_modal_global_before', array( $a ) );

		?>
		<!-- BEFORE CONTENTS -->
		<div class="cp-row <?php echo esc_attr( $cp_row_class ); ?>">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php echo esc_attr( $cp_row_eq_class ); ?> cp_responsive">
				<?php
				$content = html_entity_decode( $a['modal_title1'], ENT_QUOTES, 'utf-8' );
				$content = htmlspecialchars_decode( $content, ENT_QUOTES );
				$content = htmlspecialchars( $content, ENT_QUOTES, 'utf-8' );
				$content = html_entity_decode( $content, ENT_QUOTES, 'utf-8' );
				echo do_shortcode( stripslashes( $content ) );
				?>
			</div>
		</div>
		<!-- AFTER CONTENTS -->
		<?php
		// After filter.
		apply_filters_ref_array( 'cp_modal_global_after', array( $a ) );

		return ob_get_clean();
	}
}
