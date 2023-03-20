<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

if ( isset( $_REQUEST['cp_admin_page_nonce'] ) && ! wp_verify_nonce( $_REQUEST['cp_admin_page_nonce'], 'cp_admin_page' ) ) {
	return;
}

$info_bar_new_url = esc_url(
	add_query_arg(
		array(
			'page'       => 'smile-info_bar-designer',
			'style-view' => 'new',
		),
		admin_url( 'admin.php' )
	)
);

$info_bar_url = esc_url(
	add_query_arg(
		array(
			'page' => 'smile-info_bar-designer',
		),
		admin_url( 'admin.php' )
	)
);

$style = esc_attr( $_GET['style'] );
if ( ! isset( $style ) && '' !== $style ) {
	header( $info_bar_new_url );
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
	<h2> <?php esc_attr_e( 'Edit Info Bar Style', 'smile' ); ?>
		<a class="add-new-h2" href="<?php echo esc_attr( $info_bar_url ); ?>" title="<?php esc_attr_e( 'Go to main page', 'smile' ); ?>"><?php esc_attr_e( 'Back to Main Page', 'smile' ); ?></a>
	</h2>
	<div class="message"></div>
	<div class="smile-style-wrapper">
		<div id="smile-default-styles">
			<div class="smile-default-styles theme-browser rendered">
				<div class="themes">
					<?php
					if ( function_exists( 'smile_style_dashboard' ) ) {
						smile_style_dashboard( 'Convert_Plug_Smile_Info_Bars', 'smile_info_bar_styles', 'info_bar' );
					}
					?>
				</div>
			</div>
		</div>
	</div>
</div>
