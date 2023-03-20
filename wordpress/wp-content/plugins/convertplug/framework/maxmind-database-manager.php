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
	$cplus_maxmind_license_key     = get_option( 'convertplus_maxmind_geolocation_settings' );
	$get_cplus_maxmind_license_key = isset( $cplus_maxmind_license_key['license_key'] ) ? sanitize_text_field( $cplus_maxmind_license_key['license_key'] ) : '';
	$cplus_maxmind_nonce           = wp_create_nonce( 'cp_maxmind' );
	?>
	<div class="wrap-container">
		<div class="bend-heading-section cp-about-header">
			<h1>
				<?php
				/* translators:%s Plugin name*/
				echo sprintf( esc_html__( '%s &mdash; Maxmind Geolocation', 'smile' ), esc_attr( CP_PLUS_NAME ) );
				?>
			</h1>
			<h3>
				<?php
				/* translators:%s Plugin name*/
				echo esc_html__( 'An integration for utilizing MaxMind to do Geolocation lookups. Please note that this integration will only do Country lookups. You can signup from ', 'smile' );
				?>
				<a class="cp-maxmind-database-page-link" href="https://www.maxmind.com/en/geolite2/signup" target="_blank" rel="noopener">here.</a>				
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
			<input type="hidden" name="action" value="cplus_maxmind" />
			<input type="hidden" id="cplus_maxmind_nonce" value="<?php echo esc_attr( $cplus_maxmind_nonce ); ?>">
		<div class="debug-section">
		<table class="cp-postbox-table form-table">
				<tbody>
				<tr>
					<th scope="row">
					<label for="hide-options" style="width:340px; display: inline-block;"><strong><?php esc_html_e( 'MaxMind License Key', 'smile' ); ?></strong>
									<span class="cp-tooltip-icon has-tip" data-position="top" style="cursor: help;" title="<?php esc_html_e( 'MaxMind License Key.', 'smile' ); ?>">
										<i class="dashicons dashicons-editor-help"></i>
									</span>
						</label>
					</th>
					<td>
						<input type="password" id="cplus_maxmind_license_key" name="cplus_maxmind_license_key" value="<?php	echo esc_attr( $get_cplus_maxmind_license_key ); ?>" placeholder="Enter MaxMind License Key" />
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="option-admin-menu-db-path-page"><?php esc_html_e( 'Database File Path', 'smile' ); ?></label>
					</th>
					<td>
					<?php
						$geolite_path = new Convert_Plug_Maxmind_Geolocation();
					?>
						<input type="text" value="<?php echo esc_attr( $geolite_path->get_cplus_database_path() ); ?>" readonly />
						<p class="description">
							<?php esc_html_e( 'The location that the MaxMind database should be stored.', 'smile' ); ?>
						</p>
					</td>
				</tr>
			</tbody>
		</table>
		<button type="button" class="button button-primary button-update-settings"><?php esc_html_e( 'Save Settings', 'smile' ); ?></button>
	</div>
</form>
