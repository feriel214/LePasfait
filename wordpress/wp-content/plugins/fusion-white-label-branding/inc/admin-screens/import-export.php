<?php
/**
 * Template
 *
 * @package Fusion-White-Label-Branding
 */

// phpcs:disable VariableAnalysis.CodeAnalysis.VariableAnalysis.SelfOutsideClass
?>

<?php Fusion_White_Label_Branding_Admin::get_admin_screens_header( 'import' ); ?>
	<section class="avada-db-card avada-db-card-first avada-db-import-export-start">
		<h1 class="avada-db-import-export-heading"><?php esc_html_e( 'Import / Export', 'fusion-white-label-branding' ); ?></h1>
		<p><?php esc_html_e( 'Here, you can export your settings to a JSON file and can import them to another site. You can simply export the settings for individual sections or all sections at once.', 'fusion-white-label-branding' ); ?>
		</p>

		<div class="avada-db-card-notice">
			<i class="fusiona-info-circle"></i>
			<p class="avada-db-card-notice-heading">
				<?php
				printf(
					/* Translators: The link URL. */
					__( '<a href="%s" target="_blank">Click here</a> to read the documentation of the Avada White Label Branding plugin.', 'fusion-white-label-branding' ), // phpcs:ignore WordPress.Security.EscapeOutput
					esc_url( self::$theme_fusion_url . 'documentation/avada/plugins/how-to-use-fusion-white-label-branding-with-avada/' )
				);
				?>
			</p>
		</div>
	</section>

	<section class="avada-db-card fusion-white-label-option">
		<div class="fusion-white-label-option-title">
			<h2><?php esc_html_e( 'Export Settings', 'fusion-white-label-branding' ); ?></h2>
			<p class="description"><?php esc_html_e( 'Select individual sections to export settings for that section or select "All" to export all sections at once.', 'fusion-white-label-branding' ); ?></p>
		</div>
		<div class="fusion-white-label-option-input">
			<?php
			$sections = [
				'all'      => __( 'All Sections', 'fusion-white-label-branding' ),
				'wp_admin' => __( 'WordPress Admin', 'fusion-white-label-branding' ),
			];

			// Put wp login section at end.
			$sections['login_screen'] = __( 'WordPress Login Screen', 'fusion-white-label-branding' );

			// Add Avada if installed and activated.
			if ( class_exists( 'Avada' ) ) {
				$sections['avada'] = 'Avada';
			}

			foreach ( $sections as $section => $title ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.OverrideProhibited
				$selected = ( 'all' === $section ) ? 'checked' : '';
				?>
				<p>
				<label for="export_section_<?php echo esc_attr( $section ); ?>">
				<input type="radio" <?php echo esc_attr( $selected ); ?> id="export_section_<?php echo esc_attr( $section ); ?>" name="export_section" class="regular-radio export-settings-section" value="<?php echo esc_attr( $section ); ?>" />
				<?php echo esc_html( $title ); ?>
				</label></p>
				<?php
			}
			?>
			<div class="import-export-button-container">
				<input type="hidden" id="all-export-url" value="<?php echo esc_url_raw( wp_nonce_url( admin_url( 'admin-ajax.php?action=export_white_label_settings&section_id=all' ) ) ); ?>" />
				<input type="hidden" id="wp_admin-export-url" value="<?php echo esc_url_raw( wp_nonce_url( admin_url( 'admin-ajax.php?action=export_white_label_settings&section_id=wp_admin' ) ) ); ?>" />
				<input type="hidden" id="login_screen-export-url" value="<?php echo esc_url_raw( wp_nonce_url( admin_url( 'admin-ajax.php?action=export_white_label_settings&section_id=login_screen' ) ) ); ?>" />
				<input type="hidden" id="avada-export-url" value="<?php echo esc_url_raw( wp_nonce_url( admin_url( 'admin-ajax.php?action=export_white_label_settings&section_id=avada' ) ) ); ?>" />
				<input type="button" onclick="window.location = jQuery( '#' + jQuery('.export-settings-section:checked').val() + '-export-url' ).val();" class="button button-secondary button-export-setting button-default" value="<?php esc_attr_e( 'Export Settings', 'fusion-white-label-branding' ); ?> " />
			</div>
		</div>
	</section>

	<section class="avada-db-card fusion-white-label-option">
		<div class="fusion-white-label-option-title">
			<h2><?php esc_html_e( 'Import Settings', 'fusion-white-label-branding' ); ?></h2>
			<p class="description"><?php esc_html_e( 'Upload the exported settings file here and click the button to import the settings.', 'fusion-white-label-branding' ); ?></p>
		</div>
		<div class="fusion-white-label-option-input">
			<input type="file" id="import_settings" name="import_section" class="regular-text import-settings-section" value="" />
			<div class="import-export-button-container fusion-white-label-import-settings">
				<input type="hidden" id="import-nonce" value="<?php echo esc_html( wp_create_nonce( 'branding_settings_import' ) ); ?>" />
				<input type="button" class="button button-secondary button-import-setting button-default" value="<?php esc_attr_e( 'Import Settings', 'fusion-white-label-branding' ); ?> " />
				<span class="import-success-icon dashicons dashicons-yes hidden"></span>
				<div class="awlb-loader-warpper"><div class="avada-db-loader"></div></div>
			</div>
		</div>
	</section>
<?php Fusion_White_Label_Branding_Admin::get_admin_screens_footer(); ?>
