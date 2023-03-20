<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

$is_cp_status  = ( function_exists( 'bsf_product_status' ) ) ? bsf_product_status( '14058953' ) : '';
$reg_menu_hide = ( ( defined( 'BSF_UNREG_MENU' ) && ( true === BSF_UNREG_MENU || 'true' === BSF_UNREG_MENU ) ) ||
	( defined( 'BSF_REMOVE_14058953_FROM_REGISTRATION' ) && ( true === BSF_REMOVE_14058953_FROM_REGISTRATION || 'true' === BSF_REMOVE_14058953_FROM_REGISTRATION ) ) ) ? true : false;
if ( true !== $reg_menu_hide ) {
	if ( $is_cp_status ) {
		$reg_menu_hide = true;
	}
}
?>
<div class="wrap about-wrap about-cp bend">
	<div class="wrap-container">
		<div class="bend-heading-section cp-about-header">
			<h1>
			<?php
			/* translators:%s Plugin name*/
			echo sprintf( esc_html__( 'Welcome to %s !', 'smile' ), esc_attr( CP_PLUS_NAME ) );
			?>
			</h1>
			<h3>
			<?php
			/* translators:%s module name %s module name.*/
			echo sprintf( esc_html__( 'Welcome to %1$s - the easiest WordPress plugin to convert website traffic into leads. %2$s will help you build email lists, drive traffic, promote videos, offer coupons and much more!', 'smile' ), esc_attr( CP_PLUS_NAME ), esc_attr( CP_PLUS_NAME ) );
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

		<div class="bend-content-wrap">
			<div class="smile-settings-wrapper">
				<h2 class="nav-tab-wrapper">
				<?php
					$cp_about          = add_query_arg(
						array(
							'page' => CP_PLUS_SLUG,
						),
						admin_url( 'admin.php' )
					);
					$cp_modules        = add_query_arg(
						array(
							'page' => CP_PLUS_SLUG,
							'view' => 'modules',
						),
						admin_url( 'admin.php' )
					);
					$cp_knowledge_base = add_query_arg(
						array(
							'page' => CP_PLUS_SLUG,
							'view' => 'knowledge_base',
						),
						admin_url( 'admin.php' )
					);
					$cp_debug_author   = add_query_arg(
						array(
							'page'   => CP_PLUS_SLUG,
							'view'   => 'debug',
							'author' => 'true',
						),
						admin_url( 'admin.php' )
					);
					?>
					<a class="nav-tab nav-tab-active" href="<?php echo esc_attr( esc_url( $cp_about ) ); ?>" title="<?php esc_html_e( 'About', 'smile' ); ?>"><?php echo esc_html__( 'About', 'smile' ); ?></a>
					<a class="nav-tab" href="<?php echo esc_attr( esc_url( $cp_modules ) ); ?>" title="<?php esc_html_e( 'Modules', 'smile' ); ?>"><?php echo esc_html__( 'Modules', 'smile' ); ?></a>

					<a class="nav-tab" href="<?php echo esc_attr( esc_url( $cp_knowledge_base ) ); ?>" title="<?php esc_html_e( 'knowledge Base', 'smile' ); ?>"><?php echo esc_html__( 'Knowledge Base', 'smile' ); ?></a>

					<?php
					if ( isset( $_REQUEST['cp_admin_page_nonce'] ) && wp_verify_nonce( $_REQUEST['cp_admin_page_nonce'], 'cp_admin_page' ) ) {
						if ( isset( $_GET['author'] ) ) {
							?>
					<a class="nav-tab" href="<?php echo esc_attr( esc_url( $cp_debug_author ) ); ?>" title="<?php esc_html_e( 'Debug', 'smile' ); ?>"><?php echo esc_html__( 'Debug', 'smile' ); ?></a>
							<?php
						}
					}
					?>
				</h2>
			</div><!-- smile-settings-wrapper -->

		</hr>

		<div class="container cp-started-content">
			<div class="container">
				<div class="col-md-6">
					<div class="cp-started-section">

						<h3 class="cp-started-title"><?php esc_html_e( 'Same traffic, but more conversions!', 'smile' ); ?></h3>
						<p class="cp-started-desc">
						<?php
						/* translators:%s Plugin name*/
						echo sprintf( esc_html__( "Let's see how  %s works and some use cases -", 'smile' ), esc_attr( CP_PLUS_NAME ) );
						?>
						</p>

						<div class="cp-started-main-content">

							<ul class="cp-started-content-list">

								<li data-id="img1" class="cp-started-li-act">
									<i class="cp-started-content-icon connects-icon-mail"></i>
									<h5 class="cp-started-content-data"><?php esc_html_e( 'Build Email List', 'smile' ); ?></h5>
								</li>

								<li data-id="img2">
									<i class="cp-started-content-icon connects-icon-video"></i>
									<h5 class="cp-started-content-data"><?php esc_html_e( 'Promote Videos', 'smile' ); ?></h5>
								</li>

								<li data-id="img3">
									<i class="cp-started-content-icon connects-icon-bar-graph"></i>
									<h5 class="cp-started-content-data"><?php esc_html_e( 'Analytics', 'smile' ); ?></h5>
								</li>

								<li data-id="img4">
									<i class="cp-started-content-icon connects-icon-location-2"></i>
									<h5 class="cp-started-content-data"><?php esc_html_e( 'Drive Traffic', 'smile' ); ?></h5>
								</li>

								<li data-id="img5">
									<i class="cp-started-content-icon connects-icon-tag"></i>
									<h5 class="cp-started-content-data"><?php esc_html_e( 'Offer Coupons', 'smile' ); ?></h5>
								</li>

								<li data-id="img6">
									<i class="cp-started-content-icon connects-icon-users"></i>
									<h5 class="cp-started-content-data"><?php esc_html_e( 'Share Updates', 'smile' ); ?></h5>
								</li>

							</ul>

						</div><!-- .cp-started-main-content -->

					</div><!--cp started section-->
				</div><!--col-md-6-->

				<div class="col-md-6">
					<div class="cp-started-section">
						<div class="cp-started-screenshot">
							<div class="imgtarget img1 active"><img src="<?php echo esc_attr( esc_url( CP_PLUGIN_URL . 'admin/assets/img/getting-started/1.png' ) ); ?>" /> </div>
							<div class="imgtarget img2"><img src="<?php echo esc_attr( esc_url( CP_PLUGIN_URL . 'admin/assets/img/getting-started/2.png' ) ); ?>" /> </div>
							<div class="imgtarget img3"><img src="<?php echo esc_attr( esc_url( CP_PLUGIN_URL . 'admin/assets/img/getting-started/3.png' ) ); ?>" /> </div>
							<div class="imgtarget img4"><img src="<?php echo esc_attr( esc_url( CP_PLUGIN_URL . 'admin/assets/img/getting-started/4.png' ) ); ?>" /> </div>
							<div class="imgtarget img5"><img src="<?php echo esc_attr( esc_url( CP_PLUGIN_URL . 'admin/assets/img/getting-started/5.png' ) ); ?>" /> </div>
							<div class="imgtarget img6"><img src="<?php echo esc_attr( esc_url( CP_PLUGIN_URL . 'admin/assets/img/getting-started/6.png' ) ); ?>" /> </div>
						</div>
					</div><!-- cp-started-section -->
				</div><!--col-md-6-->

			</div><!-- .continer -->

			<div class="container cp-started-bottom-content">
				<div class="col-md-4">
					<?php

					$stored_modules = get_option( 'convert_plug_modules' );

					$get_started_url = get_admin_url();
					if ( 'Modal_Popup' === $stored_modules[0] ) {
						$get_started_url .= 'admin.php?page=smile-modal-designer&style-view=new';
					} elseif ( 'Slide_In_Popup' === $stored_modules[0] ) {
						$get_started_url .= 'admin.php?page=smile-slide_in-designer&style-view=new';
					} else {
						$get_started_url .= 'admin.php?page=smile-info_bar-designer&style-view=new';
					}
					?>
					<a class="button-primary cp-started-footer-button" href="<?php echo esc_url( $get_started_url ); ?>"><?php esc_html_e( "LET'S GET STARTED", 'smile' ); ?></a>
				</div>
			</div><!-- cp-started-bottom-content -->

		</div><!-- cp-started-content -->
	</div><!-- bend-content-wrap -->
</div><!-- .wrap-container -->
</div><!-- .bend -->
