<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );


if ( isset( $_GET['_wpnonce'] ) && ! wp_verify_nonce( $_GET['_wpnonce'], 'slide_in_edit' ) ) {
	return;
}

$slide_in_new_url = esc_url(
	add_query_arg(
		array(
			'page'       => 'smile-slide_in-designer',
			'style-view' => 'new',
		),
		admin_url( 'admin.php' )
	)
);

$slide_in_url = add_query_arg(
	array(
		'page' => 'smile-slide_in-designer',
	),
	admin_url( 'admin.php' )
);

	$style = esc_attr( $_GET['style'] );
if ( ! isset( $style ) && '' !== $style ) {
	header( $slide_in_new_url );
}
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
		<h2> <?php esc_html_e( 'Edit SlideIn Style', 'smile' ); ?>
			<a class="add-new-h2" href="<?php echo esc_attr( esc_url( $slide_in_url ) ); ?>" title="<?php esc_html_e( 'Go to main page', 'smile' ); ?>"><?php esc_html_e( 'Back to Main Page', 'smile' ); ?></a>
		</h2>
		<div class="message"></div>
		<div class="smile-style-wrapper">
			<div id="smile-default-styles">
				<div class="smile-default-styles theme-browser rendered">
					<div class="themes">
						<?php
						if ( function_exists( 'smile_style_dashboard' ) ) {
							smile_style_dashboard( 'Convert_Plug_Smile_Slide_Ins', 'smile_slide_in_styles', 'slide_in' );
						}
						?>
					</div>
				</div>
			</div>
		</div>
	</div>

