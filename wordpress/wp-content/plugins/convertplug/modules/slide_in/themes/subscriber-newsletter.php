<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

if ( ! function_exists( 'slide_in_theme_subscriber_newsletter' ) ) {
	/**
	 * Function name: slide_in_theme_subscriber_newsletter.
	 *
	 * @param  array  $atts    array attributes.
	 * @param  string $content string parameters.
	 * @return mixed(value)          html/array.
	 */
	function slide_in_theme_subscriber_newsletter( $atts, $content = null ) {
		/**
		 * Define Variables
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
			'uid'         => $uid,
			'uid_class'   => $uid_class,
			'style_class' => 'cp-subscriber-newsletter',
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
			$cp_form_vars,
			$style_settings,
			$atts
		);

		/**
		 *  Extract short-code variables.
		 *
		 *  @array      $all         All merged arrays.
		 *  @array      array()      Its required as per WP. Merged $style_settings in $all.
		 */
		$a            = shortcode_atts( $all, $style_settings );
		$imgclass     = '';
		$image_width  = isset( $a['image_width'] ) ? $a['image_width'] : '';
		$image_height = isset( $a['image_height'] ) ? $a['image_height'] : '';
		$image_style  = cp_add_css( 'left', $a['image_horizontal_position'], 'px' );
		$image_style .= cp_add_css( 'top', $a['image_vertical_position'], 'px' );
		$image_style .= cp_add_css( 'max-width', $a['image_size'], 'px' );
		$image_style .= cp_add_css( 'width', $image_width, 'px' );
		$image_style .= cp_add_css( 'height', $image_height, 'px' );

		$cp_module_img_custom_url = isset( $a['slidein_img_custom_url'] ) ? $a['slidein_img_custom_url'] : '';
		$cp_module_img_src        = isset( $a['slidein_img_src'] ) ? $a['slidein_img_src'] : '';
		$cp_module_image          = isset( $a['slidein_image'] ) ? $a['slidein_image'] : '';

		// Filters & Actions.
		$slidein_image = cp_get_module_image_url_init( 'slide_in', $cp_module_img_custom_url, $cp_module_img_src, $cp_module_image );

		// Filters & Actions for modal_image_alt.
		$slidein_image_alt = cp_get_module_image_alt_init( 'slide_in', $cp_module_img_src, $cp_module_image );

		// Before filter.
		apply_filters_ref_array( 'cp_slidein_global_before', array( $a ) );

		?>
		<div class="cp-row cp-columns-equalized">
			<div class="cp-text-container <?php echo esc_attr( $imgclass ); ?> cp-columns-equalized-center" >
				<?php
				if ( ! isset( $a['slidein_img_src'] ) ) {
					$a['slidein_img_src'] = 'upload_img';
				}

				if ( isset( $a['slidein_img_src'] ) && 'none' !== $a['slidein_img_src'] ) {
					?>
					<div class="cp-image-container  ">
						<img style="<?php echo esc_attr( $image_style ); ?>" src="<?php echo esc_url( $slidein_image ); ?>" class="cp-image" <?php echo esc_attr( $slidein_image_alt ); ?> width=<?php echo esc_attr( $image_width ); ?> height=<?php echo esc_attr( $image_height ); ?> >
					</div>

					<?php } ?>

				<div class="cp-title-container 
				<?php
				if ( '' === trim( $a['slidein_title1'] ) ) {
					echo 'cp-empty'; }
				?>
					">
				<h2 class="cp-title cp_responsive"><?php echo do_shortcode( html_entity_decode( stripcslashes( $a['slidein_title1'] ) ) ); ?></h2>
				</div>			
				<div class="cp-desc-container ">
					<div class="cp-description cp_responsive " ><?php echo do_shortcode( html_entity_decode( stripcslashes( $a['slidein_short_desc1'] ) ) ); ?></div>
				</div>
				<div class="cp-form-container  cp-form-container-newsletter ">
					<?php
					/**
					 * Embed CP Form.
					 */
					apply_filters_ref_array( 'cp_get_form', array( $a ) );
					?>
				</div>
				<div class="cp-info-container 
				<?php
				if ( '' === trim( $a['slidein_confidential'] ) ) {
					echo 'cp-empty'; }
				?>
					" >
					<?php echo do_shortcode( html_entity_decode( stripcslashes( $a['slidein_confidential'] ) ) ); ?>
				</div>
			</div><!--row-->

			</div><!-- end of text container-->
			<?php
			// After filter.
			apply_filters_ref_array( 'cp_slidein_global_after', array( $a ) );
			return ob_get_clean();
	}
}
