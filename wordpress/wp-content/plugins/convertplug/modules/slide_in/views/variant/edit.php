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

$variant_style = isset( $_GET['variant-style'] ) ? sanitize_text_field( $_GET['variant-style'] ) : '';
$style         = isset( $_GET['style'] ) ? sanitize_text_field( $_GET['style'] ) : '';

$url = add_query_arg(
	array(
		'page'          => 'smile-slide_in-designer',
		'style-view'    => 'variant',
		'variant-style' => $variant_style,
		'style'         => $style,
	),
	admin_url( 'admin.php' )
);

?>
<div class="edit-screen-overlay" style="overflow: hidden;background: #FCFCFC;position: fixed;width: 100%;height: 100%;top: 0;left: 0;z-index: 9999999;">
	<div class="smile-absolute-loader" style="visibility: visible;overflow: hidden;">
		<div class="smile-loader">
			<div class="smile-loading-bar"></div>
			<div class="smile-loading-bar"></div>
			<div class="smile-loading-bar"></div>
			<div class="smile-loading-bar"></div>
		</div>
	</div>
</div><!-- .edit-screen-overlay -->
<div class="wrap">
	<h2> <?php esc_html_e( 'Edit Variant Style', 'smile' ); ?>
		<a class="add-new-h2" href="<?php echo esc_attr( esc_url( $url ) ); ?>" title="<?php esc_html_e( 'Back to Variant Tests', 'smile' ); ?>"><?php esc_html_e( 'Back to Variant Tests', 'smile' ); ?></a>
	</h2>
	<div class="message"></div>
	<div class="smile-style-wrapper">
		<div id="smile-default-styles">
			<div class="smile-default-styles theme-browser rendered">
				<div class="themes">
					<?php
					if ( function_exists( 'smile_style_dashboard' ) ) {
						smile_style_dashboard( 'Convert_Plug_Smile_Slide_Ins', 'smile_variant_tests', 'slide_in' );
					}
					?>
				</div>
			</div>
		</div>
	</div>
</div>
