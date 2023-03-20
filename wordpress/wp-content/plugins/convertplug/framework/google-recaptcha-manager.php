<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );
?>
<div class="wrap about-wrap about-cp bend">
	<?php
	$cp_recaptcha_site_key   = get_option( 'cp_recaptcha_site_key' );
	$cp_recaptcha_secret_key = get_option( 'cp_recaptcha_secret_key' );

	?>
	<div class="wrap-container">
		<div class="bend-heading-section cp-about-header">
			<h1>
				<?php
				/* translators:%s Plugin name*/
				echo sprintf( esc_html__( '%s &mdash; Google Recaptcha', 'smile' ), esc_attr( CP_PLUS_NAME ) );
				?>
			</h1>
			<h3>
				<?php
				/* translators:%s Plugin name*/
				echo esc_html__( 'Google reCAPTCHA is a free service that protects your site from spam and abuse. It uses advanced risk analysis techniques to tell humans and bots apart. In Convert Plus Google reCAPTCHA v2 is used. Get a Google reCAPTCHA keys from', 'smile' );
				?>
				<a class="cp-google-recaptcha-page-link" href="https://www.google.com/recaptcha/intro/v3.html" target="_blank" rel="noopener">here</a>
				<?php
				echo esc_html__( 'and paste it below', 'smile' );
				?>
			</h3>
			<div class="bend-head-logo">
				<div class="bend-product-ver">
					<?php
					esc_html_e( 'Version', 'smile' );
					echo ' ' . esc_attr( CP_VERSION );
					?>
				</div>
			</div>
		</div><!-- bend-heading section -->
		<form id="convert_plug_settings" class="cp-options-list"> 
			<input type="hidden" name="action" value="cp_google_recaptcha" />
			<input type="hidden" name="security_nonce" value="<?php echo esc_attr( wp_create_nonce( 'cp_google_recaptcha-nonce' ) ); ?>" />
		<div class="debug-section">
		<table class="cp-postbox-table form-table">
				<tbody>
				<tr>
					<th scope="row">
					<label for="hide-options" style="width:340px; display: inline-block;"><strong><?php esc_html_e( 'Google Recaptcha Site Key', 'smile' ); ?></strong>
									<span class="cp-tooltip-icon has-tip" data-position="top" style="cursor: help;" title="<?php esc_html_e( 'Google Recaptcha Site Key.', 'smile' ); ?>">
										<i class="dashicons dashicons-editor-help"></i>
									</span>
						</label>
					</th>
					<td>
						<input type="text" id="cp_recaptcha_site_key" name="cp_recaptcha_site_key" value="<?php echo esc_attr( $cp_recaptcha_site_key ); ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row">
					<label for="hide-options" style="width:340px; display: inline-block;"><strong><?php esc_html_e( 'Google Recaptcha Secret Key', 'smile' ); ?></strong>
									<span class="cp-tooltip-icon has-tip" data-position="top" style="cursor: help;" title="<?php esc_html_e( 'Google Recaptcha Secret Key.', 'smile' ); ?>">
										<i class="dashicons dashicons-editor-help"></i>
									</span>
						</label>
					</th>
					<td>
						<input type="text" id="cp_recaptcha_secret_key" name="cp_recaptcha_secret_key" value="<?php echo esc_attr( $cp_recaptcha_secret_key ); ?>" />
					</td>
				</tr>
			</tbody>
		</table>
		<button type="button" class="button button-primary button-update-settings"><?php esc_html_e( 'Save Settings', 'smile' ); ?></button>
	</div>
</form>

