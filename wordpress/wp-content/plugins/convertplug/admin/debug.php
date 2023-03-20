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
			echo sprintf( esc_html__( '%s &mdash; Debugging!', 'smile' ), esc_attr( CP_PLUS_NAME ) );
			?>
			</h1>
			<h3>
				<?php
				esc_html_e(
					'Below are some settings that will help you to debug the js and css and some extra functionality.
					',
					'smile'
				);
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
		<div class="msg"></div>
		<div class="bend-content-wrap smile-settings-wrapper">
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
					$cp_settings       = add_query_arg(
						array(
							'page' => CP_PLUS_SLUG,
							'view' => 'settings',
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
				<a class="nav-tab" href="<?php echo esc_attr( esc_url( $cp_about ) ); ?>" title="<?php esc_html_e( 'About', 'smile' ); ?>" rel="noopener"><?php echo esc_html__( 'About', 'smile' ); ?></a>

				<a class="nav-tab" href="<?php echo esc_attr( esc_url( $cp_modules ) ); ?>" title="<?php esc_html_e( 'Modules', 'smile' ); ?>" rel="noopener"><?php echo esc_html__( 'Modules', 'smile' ); ?></a>

				<a class="nav-tab" href="<?php echo esc_attr( esc_url( $cp_knowledge_base ) ); ?>" title="<?php esc_html_e( 'knowledge Base', 'smile' ); ?>" rel="noopener"><?php echo esc_html__( 'Knowledge Base', 'smile' ); ?></a>
				<?php
				if ( isset( $_REQUEST['cp_admin_page_nonce'] ) && wp_verify_nonce( $_REQUEST['cp_admin_page_nonce'], 'cp_admin_page' ) ) {
					if ( isset( $_GET['author'] ) ) {
						?>
				<a class="nav-tab nav-tab-active" href="<?php echo esc_attr( esc_url( $cp_debug_author ) ); ?>" title="<?php esc_html_e( 'Debug', 'smile' ); ?>" rel="noopener"><?php echo esc_html__( 'Debug', 'smile' ); ?></a>
						<?php
					}
				}
				?>
			</h2>
			<div id="smile-settings">
				<div class="container cp-started-content">
					<form id="convert_plug_debug" class="cp-options-list">
						<?php wp_nonce_field( 'smile_update_debug_nonce', 'security_nonce' ); ?>
						<input type="hidden" name="action" value="smile_update_debug" />
						<div class="debug-section">
							<?php
							$data       = get_option( 'convert_plug_debug' );
							$dmval      = isset( $data['cp-dev-mode'] ) ? $data['cp-dev-mode'] : 0;
							$is_checked = ( $dmval ) ? ' checked="checked" ' : '';
							$uniq       = uniqid();
							?>
							<p>
								<label for="hide-options" style="width:320px; display: inline-block;"><strong><?php esc_html_e( 'Developer Mode', 'smile' ); ?></strong>
									<span class="cp-tooltip-icon has-tip" data-position="top" style="cursor: help;" title="<?php esc_html_e( 'Enable developer mode to load all beautified CSS and JS.', 'smile' ); ?>">
										<i class="dashicons dashicons-editor-help"></i>
									</span>
								</label>
								<label class="switch-wrapper" style="display: inline-block;margin: 0;height: 20px;">
									<input type="text"  id="cp-dev-mode" class="form-control smile-input smile-switch-input" name="cp-dev-mode" value="<?php echo esc_attr( $dmval ); ?>" />
									<input type="checkbox" <?php echo esc_attr( $is_checked ); ?> id="smile_cp-dev-mode_btn_<?php echo esc_attr( $uniq ); ?>"  class="ios-toggle smile-input smile-switch-input switch-checkbox smile-switch " value="<?php echo esc_attr( $dmval ); ?>" >
									<label class="smile-switch-btn checkbox-label" data-on="ON"  data-off="OFF" data-id="cp-dev-mode" for="smile_cp-dev-mode_btn_<?php echo esc_attr( $uniq ); ?>"></label>
								</label>
							</p><!-- Contact Form 7 - Styles -->
						</div><!-- .debug-section -->

						<div class="debug-section">
							<h4>Page Push Down Support:</h4>
							<p>
								<?php
								$data                 = get_option( 'convert_plug_debug' );
								$push_page_input      = isset( $data['push-page-input'] ) ? $data['push-page-input'] : '';
								$top_offset_container = isset( $data['top-offset-container'] ) ? $data['top-offset-container'] : '';
								?>
								<label for="hide-options" style="width:320px; display: inline-block;"><strong><?php esc_html_e( 'Fixed Header Class / ID', 'smile' ); ?></strong>
									<span class="cp-tooltip-icon has-tip" data-position="top" style="cursor: help;" title="<?php esc_html_e( 'For effective execution of push page down functionality of Info Bar, please enter class / ID of fixed header of your theme. e.g. #ID, .class', 'smile' ); ?>">
										<i class="dashicons dashicons-editor-help"></i>
									</span>
								</label>
								<input type="text" name="push-page-input" value="<?php echo esc_attr( $push_page_input ); ?>">
							</p>
							<p>
								<label for="hide-options" style="width:320px; display: inline-block;"><strong><?php esc_html_e( 'Top Offset Class / ID', 'smile' ); ?></strong>
									<span class="cp-tooltip-icon has-tip" data-position="top" style="cursor: help;" title="<?php esc_html_e( 'For effective execution of push page down functionality of Info Bar, please enter class / ID of Top offset container of your theme. e.g. #ID, .class', 'smile' ); ?>">
										<i class="dashicons dashicons-editor-help"></i>
									</span>
								</label>
								<input type="text" name="top-offset-container" value="<?php echo esc_attr( $top_offset_container ); ?>">
							</p>
						</div><!-- .debug-section -->

						<!-- Contact Form 7 - Styles -->

						<div class="debug-section">
							<?php
							$data       = get_option( 'convert_plug_debug' );
							$gfval      = isset( $data['cp-cf7-styles'] ) ? $data['cp-cf7-styles'] : 1;
							$is_checked = ( $gfval ) ? ' checked="checked" ' : '';
							$uniq       = uniqid();
							?>
							<p>
								<label for="hide-options" style="width:320px; display: inline-block;"><strong><?php esc_html_e( 'Predefine Contact Form 7 Style', 'smile' ); ?></strong>
									<span class="cp-tooltip-icon has-tip" data-position="top" style="cursor: help;" title="<?php esc_html_e( 'Enable Predefined Style to your Contact Form 7.', 'smile' ); ?>">
										<i class="dashicons dashicons-editor-help"></i>
									</span>
								</label>
								<label class="switch-wrapper" style="display: inline-block;margin: 0;height: 20px;">
									<input type="text"  id="cp-cf7-styles" class="form-control smile-input smile-switch-input"  name="cp-cf7-styles" value="<?php echo esc_attr( $gfval ); ?>" />
									<input type="checkbox" <?php echo esc_attr( $is_checked ); ?> id="smile_cp-cf7-styles_btn_<?php echo esc_attr( $uniq ); ?>"  class="ios-toggle smile-input smile-switch-input switch-checkbox smile-switch " value="<?php echo esc_attr( $gfval ); ?>" >
									<label class="smile-switch-btn checkbox-label" data-on="ON"  data-off="OFF" data-id="cp-cf7-styles" for="smile_cp-cf7-styles_btn_<?php echo esc_attr( $uniq ); ?>"></label>
								</label>
							</p><!-- Contact Form 7 - Styles -->
						</div><!-- .debug-section -->

						<div class="debug-section">
							<?php
							$data                 = get_option( 'convert_plug_debug' );
							$hide_admin_bar       = isset( $data['cp-hide-bar'] ) ? $data['cp-hide-bar'] : '';
							$selected_wp          = ( 'WordPress' === $hide_admin_bar ) ? 'selected' : '';
							$selected_css         = ( 'css' === $hide_admin_bar ) ? 'selected' : '';
							$after_content_scroll = isset( $data['after_content_scroll'] ) ? $data['after_content_scroll'] : '50';
							?>
							<p>
								<label for="after_content_scroll" style="width:320px; display: inline-block;"><strong><?php esc_html_e( 'After Content Scroll %', 'smile' ); ?></strong>
									<span class="cp-tooltip-icon has-tip" data-position="top" style="cursor: help;" title="<?php esc_html_e( 'Page scroll % to trigger the modal after content.', 'smile' ); ?>">
										<i class="dashicons dashicons-editor-help"></i>
									</span>
								</label>
								<input type="number" id="after_content_scroll" name="after_content_scroll" min="1" max="10000" value="<?php echo esc_attr( $after_content_scroll ); ?>"/> <span class="description"><?php esc_html_e( ' %', 'smile' ); ?></span>
							</p>
						</div><!-- .debug-section -->

						<div class="debug-section">
							<p>
								<label for="hide-options" style="width:320px; display: inline-block;"><strong><?php esc_html_e( 'Hide Admin Bar Using', 'smile' ); ?></strong>
									<span class="cp-tooltip-icon has-tip" data-position="top" style="cursor: help;" title="<?php esc_html_e( 'Depending on your selection, the WordPress admin bar will be hidden for you in customizer.', 'smile' ); ?>">
										<i class="dashicons dashicons-editor-help"></i>
									</span>
								</label>
								<select id="hide-options" name="cp-hide-bar">
									<option value="css" <?php echo esc_attr( $selected_css ); ?>><?php esc_html_e( 'CSS', 'smile' ); ?></option>
									<option value="wordpress" <?php echo esc_attr( $selected_wp ); ?>><?php esc_html_e( 'WordPress Filter', 'smile' ); ?></option>
								</select>
							</p>
						</div><!-- .debug-section -->
						<?php
						$sub_def_action              = isset( $data['cp-post-sub-action'] ) ? $data['cp-post-sub-action'] : 'process_success';
						$selected_already_sbuscribed = ( 'already_sub_msg' === $sub_def_action ) ? 'selected' : '';
						$selected_msg_success        = ( 'process_success' === $sub_def_action ) ? 'selected' : '';

						?>
						<div class="debug-section">
							<p>
								<label for="post-sub-action" style="width:320px; display: inline-block;"><strong><?php esc_html_e( 'Default Action - when user is already subscribed', 'smile' ); ?></strong>
									<span class="cp-tooltip-icon has-tip" data-position="top" style="cursor: help;" title="<?php esc_html_e( 'Depending on your selection, action will be taken if user is already subscribed.', 'smile' ); ?>">
										<i class="dashicons dashicons-editor-help"></i>
									</span>
								</label>
								<select id="post-sub-action" name="cp-post-sub-action">
									<option value="already_sub_msg" <?php echo esc_attr( $selected_already_sbuscribed ); ?>><?php esc_html_e( 'Show message as already subscribed', 'smile' ); ?></option>
									<option value="process_success" <?php echo esc_attr( $selected_msg_success ); ?>><?php esc_html_e( 'Update and process as success', 'smile' ); ?></option>
								</select>
							</p>
						</div><!-- .debug-section -->

						<div class="debug-section">
							<?php
							$dival      = isset( $data['cp-display-debug-info'] ) ? $data['cp-display-debug-info'] : 0;
							$is_checked = ( $dival ) ? ' checked="checked" ' : '';
							$uniq       = uniqid();
							?>
							<p>
								<label for="hide-options" style="width:320px; display: inline-block;"><strong><?php esc_html_e( 'Display Debug Info', 'smile' ); ?></strong>
									<span class="cp-tooltip-icon has-tip" data-position="top" style="cursor: help;" title="<?php esc_html_e( 'Enable this option to display debug info in HTML comments.', 'smile' ); ?>">
										<i class="dashicons dashicons-editor-help"></i>
									</span>
								</label>
								<label class="switch-wrapper" style="display: inline-block;margin: 0;height: 20px;">
									<input type="text"  id="cp-display-debug-info" class="form-control smile-input smile-switch-input" name="cp-display-debug-info" value="<?php echo esc_attr( $dival ); ?>" />
									<input type="checkbox" <?php echo esc_attr( $is_checked ); ?> id="smile_cp-display-debug-info_btn_<?php echo esc_attr( $uniq ); ?>"  class="ios-toggle smile-input smile-switch-input switch-checkbox smile-switch " value="<?php echo esc_attr( $dival ); ?>" >
									<label class="smile-switch-btn checkbox-label" data-on="ON"  data-off="OFF" data-id="cp-display-debug-info" for="smile_cp-display-debug-info_btn_<?php echo esc_attr( $uniq ); ?>"></label>
								</label>
							</p><!-- Contact Form 7 - Styles -->
						</div><!-- .debug-section -->

					</form>
					<button type="button" class="button button-primary button-update-debug-settings"><?php esc_html_e( 'Save Settings', 'smile' ); ?></button>
				</div>
			</div>
		</div>
	</div>
