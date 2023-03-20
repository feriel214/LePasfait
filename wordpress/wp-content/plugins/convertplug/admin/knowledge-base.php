<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

?>
<div class="wrap about-wrap about-cp bend">
	<div class="wrap-container">
		<div class="bend-heading-section cp-about-header">
			<h1>
				<?php
				/* translators:%s Plugin name*/
				echo sprintf( esc_html__( '%s &mdash; Knowledge Base', 'smile' ), esc_attr( CP_PLUS_NAME ) );
				?>
			</h1>
			<h3>
				<?php
				/* translators:%s Plugin name*/
				echo sprintf( esc_html__( 'We are here to help you solve all your doubts, queries and issues you might face while using %s In case of a problem, you can peep into our knowledge base and find a quick solution for it', 'smile' ), esc_attr( CP_PLUS_NAME ) );
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
					<a class="nav-tab" href="<?php echo esc_attr( esc_url( $cp_about ) ); ?>" title="<?php esc_html_e( 'About', 'smile' ); ?>"><?php echo esc_html__( 'About', 'smile' ); ?></a>				
					<a class="nav-tab" href="<?php echo esc_attr( esc_url( $cp_modules ) ); ?>" title="<?php esc_html_e( 'Modules', 'smile' ); ?>"><?php echo esc_html__( 'Modules', 'smile' ); ?></a>
					<a class="nav-tab nav-tab-active" href="<?php echo esc_attr( esc_url( $cp_knowledge_base ) ); ?>" title="<?php esc_html_e( 'knowledge Base', 'smile' ); ?>"><?php echo esc_html__( 'Knowledge Base', 'smile' ); ?></a>
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
		<div class="container" style="padding: 50px 0 0 0;">
			<div class="col-md-12 text-center" style="overflow:hidden;">
				<?php
				$knowledge_url = 'https://www.convertplug.com/plus/docs/';
				?>
				<a style="max-width:330px;" class="button-primary cp-started-footer-button" href="<?php echo esc_attr( esc_url( $knowledge_url ) ); ?>" target="_blank" rel="noopener">Click Here For Knowledge Base</a>
			</div>
		</div><!-- container -->

	</div><!-- bend-content-wrap -->
</div><!-- .wrap-container -->
</div><!-- .bend -->
