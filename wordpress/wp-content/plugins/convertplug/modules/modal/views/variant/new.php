<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

if ( isset( $_REQUEST['cp_admin_page_nonce'] ) && ! wp_verify_nonce( $_REQUEST['cp_admin_page_nonce'], 'cp_admin_page' ) ) {
	return;
}

?>
<div class="wrap smile-add-style bend">
	<div class="wrap-container">
		<div class="bend-heading-section">
			<?php
				$style         = isset( $_GET['style'] ) ? sanitize_text_field( $_GET['style'] ) : '';
				$variant_style = isset( $_GET['variant-style'] ) ? sanitize_text_field( $_GET['variant-style'] ) : '';
				$theme         = isset( $_GET['theme'] ) ? sanitize_text_field( $_GET['theme'] ) : '';
				$url           = add_query_arg(
					array(
						'page'          => 'smile-modal-designer',
						'style-view'    => 'variant',
						'variant-style' => $variant_style,
						'style'         => $style,
						'theme'         => $theme,
					),
					admin_url( 'admin.php' )
				);

				?>

			<h1 style="font-size: 38px;" title="<?php echo esc_attr( $style ); ?>">
				<?php esc_html_e( 'Create new variant for', 'smile' ); ?>
				<span class="cp-strip-text" style="max-width: 260px;top: 10px;"><?php echo esc_attr( $style ); ?></span></h1>
				<h3><a class="add-new-h2" href="<?php echo esc_attr( esc_url( $url ) ); ?>" title="<?php esc_attr_e( "Back to Variant's List", 'smile' ); ?>">
					<?php esc_html_e( 'Back to Variants List', 'smile' ); ?>
				</a></h3>
				<div class="col-sm-8 col-sm-offset-2">
					<h3>
						<?php esc_html_e( "Give a name to your variant and hit enter. Don't worry - you can change this in future if you don't like it anymore.", 'smile' ); ?>
					</h3>
				</div>
				<div class="container">
					<div class="col-sm-6 col-sm-offset-3 smile-style-name-section">
						<input type="text" id="style-title" name="style-title" placeholder="<?php esc_attr_e( 'Enter title for new variant', 'smile' ); ?>" />
					</div>
				</div>
				<div class="bend-content-wrap smile-add-style-content">
					<div class="container ">
						<div class="smile-style-category">
							<?php
							if ( function_exists( 'smile_style_dashboard' ) ) {
								smile_style_dashboard( 'Convert_Plug_Smile_Modals', 'modal_variant_tests', 'modal' );
							}
							?>
							<!-- .styles-list --> 
						</div>
						<!-- .smile-style-category --> 
					</div>
					<!-- .container --> 
				</div>
			</div>
			<!-- .bend-content-wrap --> 
		</div>
		<!-- .wrap-container --> 
	</div>
	<!-- .wrap -->
