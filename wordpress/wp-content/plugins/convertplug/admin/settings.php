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
				/* translators:%s plugin name */
				echo sprintf( esc_html__( '%s &mdash; Settings', 'smile' ), esc_attr( CP_PLUS_NAME ) );
				?>
			</h1>
			<h3>
				<?php
				/* translators:%s plugin name */
				echo sprintf( esc_html__( 'Below are some global settings that are applied to the elements designed with %s If you are just getting started, you probably dont need to do anything here right now', 'smile' ), esc_attr( CP_PLUS_NAME ) );
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
				<a class="nav-tab" href="<?php echo esc_attr( esc_url( $cp_about ) ); ?>" title="<?php esc_html_e( 'About', 'smile' ); ?>"><?php echo esc_html__( 'About', 'smile' ); ?></a>
				<a class="nav-tab" href="<?php echo esc_attr( esc_url( $cp_modules ) ); ?>" title="<?php esc_html_e( 'Modules', 'smile' ); ?>"><?php echo esc_html_e( 'Modules', 'smile' ); ?></a>
				<a class="nav-tab nav-tab-active" href="<?php echo esc_attr( esc_url( $cp_settings ) ); ?>" title="<?php esc_html_e( 'Settings', 'smile' ); ?>"><?php echo esc_html__( 'Settings', 'smile' ); ?></a>

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
			<div id="smile-settings">
				<div class="container cp-started-content">
					<form id="convert_plug_settings" class="cp-options-list">
						<input type="hidden" name="action" value="smile_update_settings" />
						<?php wp_nonce_field( 'cp-smile_update_settings-nonce', 'security_nonce' ); ?>
						<!-- MX Record Validation For Email -->

						<div class="debug-section">
							<?php
							$data       = get_option( 'convert_plug_settings' );
							$gfval      = isset( $data['cp-enable-mx-record'] ) ? $data['cp-enable-mx-record'] : 0;
							$is_checked = ( $gfval ) ? ' checked="checked" ' : '';
							$uniq       = uniqid();
							?>
							<p>
								<label for="hide-options" style="width:340px; display: inline-block;"><strong><?php esc_html_e( 'MX Record Validation For Email', 'smile' ); ?></strong>
									<span class="cp-tooltip-icon has-tip" data-position="top" style="cursor: help;" title="<?php esc_html_e( 'Enable / disable MX lookup email validation method.', 'smile' ); ?>">
										<i class="dashicons dashicons-editor-help"></i>
									</span>
								</label>
								<label class="switch-wrapper" style="display: inline-block;margin: 0;height: 20px;">
									<input type="text"  id="cp-enable-mx-record" class="form-control smile-input smile-switch-input"  name="cp-enable-mx-record" value="<?php echo esc_attr( $gfval ); ?>" />
									<input type="checkbox" <?php echo esc_attr( $is_checked ); ?> id="smile_cp-enable-mx-record_btn_<?php echo esc_attr( $uniq ); ?>"  class="ios-toggle smile-input smile-switch-input switch-checkbox smile-switch " value="<?php echo esc_attr( $gfval ); ?>" >
									<label class="smile-switch-btn checkbox-label" data-on="ON"  data-off="OFF" data-id="cp-enable-mx-record" for="smile_cp-enable-mx-record_btn_<?php echo esc_attr( $uniq ); ?>"></label>
								</label>
							</p><!-- MX Record Validation For Email -->
						</div><!-- .debug-section -->

						<div class="debug-section">
							<!-- Subscription Messages -->
							<h4>Response Message - When User Is Already Subscribed:</h4>
							<!-- Show default messages -->
							<?php
							$data       = get_option( 'convert_plug_settings' );
							$gfval      = isset( $data['cp-default-messages'] ) ? $data['cp-default-messages'] : 1;
							$is_checked = ( $gfval ) ? ' checked="checked" ' : '';
							$uniq       = uniqid();
							?>
							<p>
								<label for="hide-options" style="width:340px; display: inline-block;"><strong><?php esc_html_e( 'Display Your Customized Error Message', 'smile' ); ?></strong>
									<span class="cp-tooltip-icon has-tip" data-position="top" style="cursor: help;" title="<?php esc_html_e( 'If turned OFF, third party mailer error message will be displayed.', 'smile' ); ?>">
										<i class="dashicons dashicons-editor-help"></i>
									</span>
								</label>
								<label class="switch-wrapper" style="display: inline-block;margin: 0;height: 20px;">
									<input type="text"  id="cp-default-messages" class="form-control smile-input smile-switch-input"  name="cp-default-messages" value="<?php echo esc_attr( $gfval ); ?>" />
									<input type="checkbox" <?php echo esc_attr( $is_checked ); ?> id="smile_cp-default-messages_btn_<?php echo esc_attr( $uniq ); ?>"  class="ios-toggle smile-input smile-switch-input switch-checkbox smile-switch " value="<?php echo esc_attr( $gfval ); ?>" >
									<label class="smile-switch-btn checkbox-label" data-on="ON"  data-off="OFF" data-id="cp-default-messages" for="smile_cp-default-messages_btn_<?php echo esc_attr( $uniq ); ?>"></label>
								</label>
							</p><!-- Show default messages -->
							<?php
							$data = get_option( 'convert_plug_settings' );
							$msg  = isset( $data['cp-already-subscribed'] ) ? $data['cp-already-subscribed'] : __( 'Already Subscribed...!', 'smile' );
							?>
							<p 
							<?php
							if ( 1 === $msg ) {
								echo "style='display:none;'"; }
							?>
								>
								<label for="hide-options" style="width:340px; vertical-align: top; display: inline-block;"><strong><?php esc_html_e( 'Enter Custom Message', 'smile' ); ?></strong>
									<span class="cp-tooltip-icon has-tip" data-position="top" style="cursor: help;" title="<?php esc_html_e( 'Enter your custom message to display when user is already subscribed.', 'smile' ); ?>">
										<i class="dashicons dashicons-editor-help"></i>
									</span>
								</label>
								<textarea id="cp-already-subscribed" name="cp-already-subscribed" cols="40" rows="5"><?php echo esc_html( stripslashes( $msg ) ); ?></textarea>
							</p><!-- Subscription Messages -->
						</div><!-- .debug-section -->

						<!-- Google Fonts -->
						<div class="debug-section">
							<!-- Turn On/Off double optin -->
							<?php
							$data          = get_option( 'convert_plug_settings' );
							$d_optin       = isset( $data['cp-double-optin'] ) ? $data['cp-double-optin'] : 1;
							$optin_checked = ( $d_optin ) ? ' checked="checked" ' : '';
							?>
							<p>
								<label for="hide-options" style="width:340px; display: inline-block;"><strong><?php esc_html_e( 'Double Optin Enable', 'smile' ); ?></strong>
									<span class="cp-tooltip-icon has-tip" data-position="top" style="cursor: help;" title="<?php esc_html_e( 'Enable double optin for MailChimp, Benchmark, MyMail.', 'smile' ); ?>">
										<i class="dashicons dashicons-editor-help"></i>
									</span>
								</label>
								<label class="switch-wrapper" style="display: inline-block;margin: 0;height: 20px;">
									<input type="text"  id="cp-double-optin" class="form-control smile-input smile-switch-input"  name="cp-double-optin" value="<?php echo esc_attr( $d_optin ); ?>" />
									<input type="checkbox" <?php echo esc_attr( $optin_checked ); ?> id="smile_cp-double-optin_btn_<?php echo esc_attr( $uniq ); ?>"  class="ios-toggle smile-input smile-switch-input switch-checkbox smile-switch " value="<?php echo esc_attr( $d_optin ); ?>" >
									<label class="smile-switch-btn checkbox-label" data-on="ON"  data-off="OFF" data-id="cp-double-optin" for="smile_cp-double-optin_btn_<?php echo esc_attr( $uniq ); ?>"></label>
								</label>
							</p><!-- end of double optin -->
						</div><!-- .debug-section -->
						<div class="debug-section">
							<!-- Turn On/Off subscriber notification -->
							<?php
							$data        = get_option( 'convert_plug_settings' );
							$sub_optin   = isset( $data['cp-sub-notify'] ) ? $data['cp-sub-notify'] : 0;
							$sub_checked = ( $sub_optin ) ? ' checked="checked" ' : '';
							?>
							<p>
								<label for="hide-options" style="width:340px; display: inline-block;"><strong><?php esc_html_e( 'Subscriber Notification', 'smile' ); ?></strong>
									<span class="cp-tooltip-icon has-tip" data-position="top" style="cursor: help;" title="<?php esc_html_e( 'Enable Subscriber Notification For all Campaign.', 'smile' ); ?>">
										<i class="dashicons dashicons-editor-help"></i>
									</span>
								</label>
								<label class="switch-wrapper" style="display: inline-block;margin: 0;height: 20px;">
									<input type="text"  id="cp-sub-notify" class="form-control smile-input smile-switch-input"  name="cp-sub-notify" value="<?php echo esc_attr( $sub_optin ); ?>" />
									<input type="checkbox" <?php echo esc_attr( $sub_checked ); ?> id="smile_cp-sub-notify_btn_<?php echo esc_attr( $uniq ); ?>"  class="ios-toggle smile-input smile-switch-input switch-checkbox smile-switch " value="<?php echo esc_attr( $sub_optin ); ?>" >
									<label class="smile-switch-btn checkbox-label" data-on="ON"  data-off="OFF" data-id="cp-sub-notify" for="smile_cp-sub-notify_btn_<?php echo esc_attr( $uniq ); ?>"></label>
								</label>
							</p><!-- end of subscriber notification-->
							<?php
							$data       = get_option( 'convert_plug_settings' );
							$sub_email  = isset( $data['cp-sub-email'] ) ? $data['cp-sub-email'] : get_option( 'admin_email' );
							$email_sub  = isset( $data['cp-email-sub'] ) ? $data['cp-email-sub'] : 'Congratulations! You have a New Subscriber!';
							$email_body = isset( $data['cp-email-body'] ) ? $data['cp-email-body'] : '<p>You’ve got a new subscriber to the Campaign: {{list_name}} </p><p>Here is the information :</p>{{content}}<p>Congratulations! Wish you many more.<br>This e-mail was sent from " . {{CP_PLUS_NAME}}. " on {{blog_name}} {{site_url}}</p>';

							?>
							<p 
							<?php
							if ( 1 === $sub_email ) {
								echo "style='display:none;'"; }
							?>
								>
								<label for="hide-options" style="width:340px; vertical-align: top; display: inline-block;"><strong><?php esc_html_e( 'Enter Email Id', 'smile' ); ?></strong>
									<span class="cp-tooltip-icon has-tip" data-position="top" style="cursor: help;" title="<?php esc_html_e( 'This is the email ID or email IDs you wish to receive subscriber notifications on. Separate each email ID with a comma.', 'smile' ); ?>">
										<i class="dashicons dashicons-editor-help"></i>
									</span>
								</label>
								<textarea id="cp-sub-email" name="cp-sub-email" cols="40" rows="5"><?php echo esc_html( stripslashes( $sub_email ) ); ?></textarea>
							</p><!-- Subscription Messages -->
							<p 
							<?php
							if ( 1 === $email_sub ) {
								echo "style='display:none;'"; }
							?>
								>
								<label for="hide-options" style="width:340px; vertical-align: top; display: inline-block;"><strong><?php esc_html_e( 'Enter Subject For Email', 'smile' ); ?></strong>
									<span class="cp-tooltip-icon has-tip" data-position="top" style="cursor: help;" title="<?php esc_html_e( 'The subject of subscriber notification email you will receive.', 'smile' ); ?>">
										<i class="dashicons dashicons-editor-help"></i>
									</span>
								</label>
								<textarea id="cp-email-sub" name="cp-email-sub" cols="40" rows="5"><?php echo esc_html( stripslashes( $email_sub ) ); ?></textarea>
							</p><!-- Subscription Messages -->
							<p 
							<?php
							if ( 1 === $email_body ) {
								echo "style='display:none;'"; }
							?>
								>
								<label for="hide-options" style="width:340px; vertical-align: top; display: inline-block;"><strong><?php esc_html_e( 'Enter Content For Email', 'smile' ); ?></strong>
									<span class="cp-tooltip-icon has-tip" data-position="top" style="cursor: help;" title="<?php esc_html_e( 'This is the main body content of the email. Please do not change the strings within braces. eg: {{list_name}}, {{content}}, etc.', 'smile' ); ?>">
										<i class="dashicons dashicons-editor-help"></i>
									</span>
								</label>
								<textarea id="cp-email-body" name="cp-email-body" cols="40" rows="5"><?php echo esc_html( stripslashes( $email_body ) ); ?></textarea>
							</p><!-- Subscription Messages -->
						</div><!-- .debug-section -->
						<!-- Google Fonts -->
						<div class="debug-section">
							<?php
							$data       = get_option( 'convert_plug_settings' );
							$gfval      = isset( $data['cp-google-fonts'] ) ? $data['cp-google-fonts'] : 1;
							$is_checked = ( $gfval ) ? ' checked="checked" ' : '';
							$uniq       = uniqid();
							?>
							<p>
								<label for="hide-options" style="width:340px; display: inline-block;"><strong><?php esc_html_e( 'Google Fonts', 'smile' ); ?></strong>
									<span class="cp-tooltip-icon has-tip" data-position="top" style="cursor: help;" title="<?php esc_html_e( 'Load Google Fonts at front end.', 'smile' ); ?>">
										<i class="dashicons dashicons-editor-help"></i>
									</span>
								</label>
								<label class="switch-wrapper" style="display: inline-block;margin: 0;height: 20px;">
									<input type="text"  id="cp-google-fonts" class="form-control smile-input smile-switch-input"  name="cp-google-fonts" value="<?php echo esc_attr( $gfval ); ?>" />
									<input type="checkbox" <?php echo esc_attr( $is_checked ); ?> id="smile_cp-google-fonts_btn_<?php echo esc_attr( $uniq ); ?>"  class="ios-toggle smile-input smile-switch-input switch-checkbox smile-switch " value="<?php echo esc_attr( $gfval ); ?>" >
									<label class="smile-switch-btn checkbox-label" data-on="ON"  data-off="OFF" data-id="cp-google-fonts" for="smile_cp-google-fonts_btn_<?php echo esc_attr( $uniq ); ?>"></label>
								</label>
							</p><!-- Google Fonts -->
						</div>

						<div class="debug-section">
							<p>
								<?php

								$cp_settings     = get_option( 'convert_plug_settings' );
								$selected        = '';
								$wselected       = '';
								$loggedinuser    = '';
								$loggedinuser    = explode( ',', $cp_settings['cp-user-role'] );
								$timezone        = $cp_settings['cp-timezone'];
								$user_inactivity = isset( $cp_settings['user_inactivity'] ) ? $cp_settings['user_inactivity'] : '60';
								if ( 'system' === $timezone ) {
									$selected = 'selected';
								}
								if ( 'WordPress' === $timezone ) {
									$wselected = 'selected';
								}
								?>
								<label for="global-timezone" style="width:340px; display: inline-block;"><strong><?php esc_html_e( 'Set Timezone', 'smile' ); ?></strong>
									<?php
									/* translators:%s plugin name */
									$link_1 = sprintf( __( 'Depending on your selection, input will be taken for timer based features in %s', 'smile' ), CP_PLUS_NAME );
									?>
									<span class="cp-tooltip-icon has-tip" data-position="top" style="cursor: help;" title="<?php echo esc_attr( $link_1 ); ?>">
										<i class="dashicons dashicons-editor-help"></i>
									</span>
								</label>
								<select id="global-timezone" name="cp-timezone">
									<option value="wordpress" <?php echo esc_attr( $wselected ); ?> ><?php esc_html_e( 'WordPress Timezone', 'smile' ); ?></option>
									<option value="system" <?php echo esc_attr( $selected ); ?> ><?php esc_html_e( 'System Default Time', 'smile' ); ?></option>
								</select>
							</p>
						</div>

						<div class="debug-section">
							<p>
								<label for="user_inactivity" style="width:340px; display: inline-block;"><strong><?php esc_html_e( 'User Inactivity Time', 'smile' ); ?></strong>
									<span class="cp-tooltip-icon has-tip" data-position="top" style="cursor: help;" title="<?php esc_html_e( 'A module can be triggered when a user is idle for x seconds on your website. You can set the value of X here.', 'smile' ); ?>">
										<i class="dashicons dashicons-editor-help"></i>
									</span>
								</label>
								<input type="number" id="user_inactivity" name="user_inactivity" min="1" max="10000" value="<?php echo esc_attr( $user_inactivity ); ?>"/> <span class="description"><?php esc_html_e( ' Seconds', 'smile' ); ?></span>
							</p>
						</div>

						<div class="debug-section">
							<p>
								<?php

								$psval      = isset( $data['cp-edit-style-link'] ) ? $data['cp-edit-style-link'] : 0;
								$is_checked = ( $psval ) ? ' checked="checked" ' : '';
								$uniq       = uniqid();
								/* translators:%s plugin name */
								$link_2 = sprintf( __( 'Enable style edit link on frontend at bottom right corner of the module, so a user can easily navigate to edit style window. This link will be visible to users who have access to %s backend', 'smile' ), CP_PLUS_NAME );

								?>
								<label for="edit-style-link" style="width:340px; display: inline-block;"><strong><?php esc_html_e( 'Display Style Edit Link On Front End', 'smile' ); ?></strong>

									<span class="cp-tooltip-icon has-tip" data-position="top" style="cursor: help;" title="<?php echo esc_attr( $link_2 ); ?>">
										<i class="dashicons dashicons-editor-help"></i>
									</span>
								</label>
								<label class="switch-wrapper" style="display: inline-block;margin: 0;height: 20px;">
									<input type="text"  id="cp-edit-style-link" class="form-control smile-input smile-switch-input"  name="cp-edit-style-link" value="<?php echo esc_attr( $psval ); ?>" />
									<input type="checkbox" <?php echo esc_attr( $is_checked ); ?> id="smile_cp-edit-style-link_btn_<?php echo esc_attr( $uniq ); ?>"  class="ios-toggle smile-input smile-switch-input switch-checkbox smile-switch " value="<?php echo esc_attr( $gfval ); ?>" >
									<label class="smile-switch-btn checkbox-label" data-on="ON"  data-off="OFF" data-id="cp-edit-style-link" for="smile_cp-edit-style-link_btn_<?php echo esc_attr( $uniq ); ?>"></label>
								</label>
							</p>
						</div>

						<div class="debug-section">
							<p>
								<?php

								$psval      = isset( $data['cp-plugin-support'] ) ? $data['cp-plugin-support'] : 0;
								$is_checked = ( $psval ) ? ' checked="checked" ' : '';
								$uniq       = uniqid();
								/* translators:%s plugin name %s plugin name */
								$link_3 = sprintf( __( 'Enable this option if you are facing any issues to access %1$s customizer ( edit module screen ). After enabling this option %2$s', 'smile' ), CP_PLUS_NAME, CP_PLUS_NAME );
								?>
								<label for="plugin-support" style="width:340px; display: inline-block;"><strong><?php esc_html_e( 'Third Party Plugin Support', 'smile' ); ?></strong>
									<span class="cp-tooltip-icon has-tip" data-position="top" style="cursor: help;" title="<?php echo esc_attr( $link_3 ); ?>">
										<i class="dashicons dashicons-editor-help"></i>
									</span>
								</label>
								<label class="switch-wrapper" style="display: inline-block;margin: 0;height: 20px;">
									<input type="text"  id="cp-plugin-support" class="form-control smile-input smile-switch-input"  name="cp-plugin-support" value="<?php echo esc_attr( $psval ); ?>" />
									<input type="checkbox" <?php echo esc_attr( $is_checked ); ?> id="smile_cp-plugin-support_btn_<?php echo esc_attr( $uniq ); ?>"  class="ios-toggle smile-input smile-switch-input switch-checkbox smile-switch " value="<?php echo esc_attr( $gfval ); ?>" >
									<label class="smile-switch-btn checkbox-label" data-on="ON"  data-off="OFF" data-id="cp-plugin-support" for="smile_cp-plugin-support_btn_<?php echo esc_attr( $uniq ); ?>"></label>
								</label>
							</p>
						</div>

						<!-- disable impression -->
						<div class="debug-section">
							<p>
								<?php
								$disval     = isset( $data['cp-disable-impression'] ) ? $data['cp-disable-impression'] : 0;
								$is_checked = ( $disval ) ? ' checked="checked" ' : '';
								$uniq       = uniqid();
								?>
								<label for="plugin-support" style="width:340px; display: inline-block;"><strong><?php esc_html_e( 'Disable impression for modules', 'smile' ); ?></strong>
									<span class="cp-tooltip-icon has-tip" data-position="top" style="cursor: help;" title="<?php esc_html_e( 'Enable this option if you do not wish to track impressions on modules.', 'smile' ); ?>">
										<i class="dashicons dashicons-editor-help"></i>
									</span>
								</label>
								<label class="switch-wrapper" style="display: inline-block;margin: 0;height: 20px;">
									<input type="text"  id="cp-disable-impression" class="form-control smile-input smile-switch-input"  name="cp-disable-impression" value="<?php echo esc_attr( $disval ); ?>" />
									<input type="checkbox" <?php echo esc_attr( $is_checked ); ?> id="smile_cp-disable-impression_btn_<?php echo esc_attr( $uniq ); ?>"  class="ios-toggle smile-input smile-switch-input switch-checkbox smile-switch " value="<?php echo esc_attr( $disval ); ?>" >
									<label class="smile-switch-btn checkbox-label" data-on="ON"  data-off="OFF" data-id="cp-disable-impression" for="smile_cp-disable-impression_btn_<?php echo esc_attr( $uniq ); ?>"></label>
								</label>
							</p>
						</div>

						<!-- disable impression -->
						<div class="debug-section">
							<p>
								<?php
								$close_inline     = isset( $data['cp-close-inline'] ) ? $data['cp-close-inline'] : 0;
								$is_close_checked = ( $close_inline ) ? ' checked="checked" ' : '';
								$uniq             = uniqid();
								?>
								<label for="plugin-support" style="width:340px; display: inline-block;"><strong><?php esc_html_e( 'Close Inline modules', 'smile' ); ?></strong>
									<span class="cp-tooltip-icon has-tip" data-position="top" style="cursor: help;" title="<?php esc_html_e( 'Enable this option if you wish to close inline modules after submission.', 'smile' ); ?>">
										<i class="dashicons dashicons-editor-help"></i>
									</span>
								</label>
								<label class="switch-wrapper" style="display: inline-block;margin: 0;height: 20px;">
									<input type="text"  id="cp-close-inline" class="form-control smile-input smile-switch-input"  name="cp-close-inline" value="<?php echo esc_attr( $close_inline ); ?>" />
									<input type="checkbox" <?php echo esc_attr( $is_close_checked ); ?> id="smile_cp-close-inline_btn_<?php echo esc_attr( $uniq ); ?>"  class="ios-toggle smile-input smile-switch-input switch-checkbox smile-switch " value="<?php echo esc_attr( $close_inline ); ?>" >
									<label class="smile-switch-btn checkbox-label" data-on="ON"  data-off="OFF" data-id="cp-close-inline" for="smile_cp-close-inline_btn_<?php echo esc_attr( $uniq ); ?>"></label>
								</label>
							</p>
						</div>

						<div class="debug-section">
							<p>
								<table>
									<tr>
										<td style="vertical-align: top;padding-top: 20px;">
											<label style="width:340px; display: inline-block;font-size:14px;"><strong><?php esc_html_e( 'Disable Modal Impression Count For', 'smile' ); ?></strong>
												<span class="cp-tooltip-icon has-tip" data-position="top" style="cursor: help;" title="<?php esc_html_e( 'This setting is used while generating analytics data. For selected WordPress user roles, impressions will not be counted.', 'smile' ); ?>">
													<i class="dashicons dashicons-editor-help"></i>
												</span>
											</label>
										</td>
										<td>
											<ul class="checkbox-grid">
												<?php
												global $wp_roles;
												$roles = $wp_roles->get_names();

												foreach ( $roles as $rkey => $rvalue ) {
													if ( ! empty( $cp_settings ) ) {
														if ( in_array( $rkey, $loggedinuser ) ) {
															echo '<li><input type="checkbox" name="cp-user-role" id="cp-user-role" value="' . esc_attr( $rkey ) . '"  checked >' . esc_attr( $rvalue ) . '</li>';
														} else {
															echo '<li><input type="checkbox" name="cp-user-role" id="cp-user-role" value="' . esc_attr( $rkey ) . '" >' . esc_attr( $rvalue ) . '</li>';
														}
													} else {
														if ( 'administrator' === $rkey ) {

															echo '<li><input type="checkbox" name="cp-user-role" id="cp-user-role" value="' . esc_attr( $rkey ) . '"  checked >' . esc_attr( $rvalue ) . '</li>';

														} else {
															echo '<li><input type="checkbox" name="cp-user-role" id="cp-user-role" value="' . esc_attr( $rkey ) . '" >' . esc_attr( $rvalue ) . '</li>';
														}
													}
												}

												?>
											</ul>
										</td>
									</tr>
								</table>
							</p>
						</div>

						<?php
						if ( current_user_can( 'manage_options' ) ) {

							/* translators:%s plugin name */
							$link_4 = sprintf( __( 'Allow %s Dashboard Access For', 'smile' ), CP_PLUS_NAME );
							/* translators:%s plugin name %s plugin name */
							$link_5 = sprintf( __( '%1$s dashboard access will be provided to selected user roles. By default, Administrator user role has complete access of %2$s  & it can not be changed.', 'smile' ), CP_PLUS_NAME, CP_PLUS_NAME );
							?>
							<div class="debug-section cp-access-roles">
								<p>
									<table>
										<tr>
											<td style="vertical-align: top;padding-top: 20px;">
												<label for="cp-access-user-role" style="width:340px; display: inline-block;font-size:14px;"><strong><?php echo esc_attr( $link_4 ); ?></strong>
													<span class="cp-tooltip-icon has-tip" data-position="top" style="cursor: help;" title="<?php echo esc_attr( $link_5 ); ?>">
														<i class="dashicons dashicons-editor-help"></i>
													</span>
												</label>
											</td>
											<td>
												<ul class="checkbox-grid">
													<?php

													$access_roles = explode( ',', $cp_settings['cp-access-role'] );
													global $wp_roles;
													$roles = $wp_roles->get_names();
													unset( $roles['administrator'] );
													?>
													<?php foreach ( $roles as $key => $cp_role ) { ?>
													<li>
														<input type="checkbox" name="cp_access_role" 
														<?php
														if ( in_array( $key, $access_roles ) ) {
															echo esc_html( "checked='checked';" );  }
														?>
															value="<?php echo esc_attr( $key ); ?>" />
															<?php echo esc_attr( $cp_role ); ?>
														</li>
														<?php } ?>
													</ul>
												</td>
											</tr>
										</table>
									</p>
								</div>
								<?php } ?>

					<!-- disable impression -->
						<div class="debug-section">
							<p>
								<table>
									<tr>
										<td style="vertical-align: top;">
										<?php
										$disable_storage    = isset( $data['cp-disable-storage'] ) ? $data['cp-disable-storage'] : 0;
										$is_storage_checked = ( $disable_storage ) ? ' checked="checked" ' : '';
										$uniq               = uniqid();
										?>
										<label for="plugin-support" style="width:340px; display: inline-block;font-size:14px;"><strong><?php esc_html_e( 'Disable data storage', 'smile' ); ?></strong>
											<span class="cp-tooltip-icon has-tip" data-position="top" style="cursor: help;" title="<?php esc_html_e( 'Enable this option if you wish to do not store information of the user to your site database after submission.', 'smile' ); ?>">
												<i class="dashicons dashicons-editor-help"></i>
											</span>
										</label>
									</td>
								<td>
								<label class="switch-wrapper" style="display: inline-block;margin: 0;height: 20px;">
									<input type="text"  id="cp-disable-storage" class="form-control smile-input smile-switch-input"  name="cp-disable-storage" value="<?php echo esc_attr( $disable_storage ); ?>" />
									<input type="checkbox" <?php echo esc_attr( $is_storage_checked ); ?> id="smile_cp-disable-storage_btn_<?php echo esc_attr( $uniq ); ?>"  class="ios-toggle smile-input smile-switch-input switch-checkbox smile-switch " value="<?php echo esc_attr( $disable_storage ); ?>" >
									<label class="smile-switch-btn checkbox-label" data-on="ON"  data-off="OFF" data-id="cp-disable-storage" for="smile_cp-disable-storage_btn_<?php echo esc_attr( $uniq ); ?>"></label>
								</label>
							</td>
						</tr>
					</table>
							</p>
						</div>

						<!-- Disable Honeypot -->
						<div class="debug-section">
							<p>
								<table>
									<tr>
										<td style="vertical-align: top;">
										<?php
										$disable_pot    = isset( $data['cp-disable-pot'] ) ? $data['cp-disable-pot'] : 1;
										$is_pot_checked = ( $disable_pot ) ? ' checked="checked" ' : '';
										$uniq           = uniqid();
										?>
										<label for="plugin-support" style="width:340px; display: inline-block;font-size:14px;"><strong><?php esc_html_e( 'Honeypot Protection', 'smile' ); ?></strong>
											<span class="cp-tooltip-icon has-tip" data-position="top" style="cursor: help;" title="<?php esc_html_e( 'Enable this option if you wish to protect your site from spam attack.', 'smile' ); ?>">
												<i class="dashicons dashicons-editor-help"></i>
											</span>
										</label>
									</td>
								<td>
								<label class="switch-wrapper" style="display: inline-block;margin: 0;height: 20px;">
									<input type="text"  id="cp-disable-pot" class="form-control smile-input smile-switch-input"  name="cp-disable-pot" value="<?php echo esc_attr( $disable_pot ); ?>" />
									<input type="checkbox" <?php echo esc_attr( $is_pot_checked ); ?> id="smile_cp-disable-pot_btn_<?php echo esc_attr( $uniq ); ?>"  class="ios-toggle smile-input smile-switch-input switch-checkbox smile-switch " value="<?php echo esc_attr( $disable_pot ); ?>" >
									<label class="smile-switch-btn checkbox-label" data-on="ON"  data-off="OFF" data-id="cp-disable-pot" for="smile_cp-disable-pot_btn_<?php echo esc_attr( $uniq ); ?>"></label>
								</label>
							</td>
						</tr>
					</table>
							</p>
						</div>

						<!-- Disable Domain -->
						<div class="debug-section">
							<p>
								<table>
									<tr>
										<td style="vertical-align: top;">
										<?php
										$disable_domain    = isset( $data['cp-disable-domain'] ) ? $data['cp-disable-domain'] : 0;
										$is_domain_checked = ( $disable_domain ) ? ' checked="checked" ' : '';
										$uniq              = uniqid();
										?>
										<label for="plugin-support" style="width:340px; display: inline-block;font-size:14px;"><strong><?php esc_html_e( 'Disable Domain', 'smile' ); ?></strong>
											<span class="cp-tooltip-icon has-tip" data-position="top" style="cursor: help;" title="<?php esc_html_e( 'Enable this option if you wish to disallow some email domains to fill the form.', 'smile' ); ?>">
												<i class="dashicons dashicons-editor-help"></i>
											</span>
										</label>
										</td>
										<td>
										<label class="switch-wrapper" style="display: inline-block;margin: 0;height: 20px;">
											<input type="text"  id="cp-disable-domain" class="form-control smile-input smile-switch-input"  name="cp-disable-domain" value="<?php echo esc_attr( $disable_domain ); ?>" />
											<input type="checkbox" <?php echo esc_attr( $is_domain_checked ); ?> id="smile_cp-disable-domain_btn_<?php echo esc_attr( $uniq ); ?>"  class="ios-toggle smile-input smile-switch-input switch-checkbox smile-switch " value="<?php echo esc_attr( $disable_domain ); ?>" >
											<label class="smile-switch-btn checkbox-label" data-on="ON"  data-off="OFF" data-id="cp-disable-domain" for="smile_cp-disable-domain_btn_<?php echo esc_attr( $uniq ); ?>"></label>
										</label>
										</td>
									</tr>
								</table>								
							</p>
							<?php
							$domain_name = isset( $data['cp-domain-name'] ) ? $data['cp-domain-name'] : '';
							?>
							<p 
							<?php
							if ( 1 === $domain_name ) {
								echo "style='display:none;'"; }
							?>
							>
							<label for="hide-options" style="width:340px; vertical-align: top; display: inline-block;"><strong><?php esc_html_e( 'Enter Domain Names', 'smile' ); ?></strong>
							<span class="cp-tooltip-icon has-tip" data-position="top" style="cursor: help;" title="<?php esc_html_e( 'Enter the email domain name to block the form submission. You cam use comma to seperate out domain names.', 'smile' ); ?>">
							<i class="dashicons dashicons-editor-help"></i>
							</span>
							</label>
							<textarea id="cp-domain-name" name="cp-domain-name" cols="40" rows="5"><?php echo esc_html( stripslashes( $domain_name ) ); ?></textarea>
							</p><!-- Domain names -->
						</div>

						<!-- Lazy load images -->
						<div class="debug-section">
							<p>
								<table>
									<tr>
										<td style="vertical-align: top;">
										<?php
										$lazy_load_img   = isset( $data['cp-lazy-img'] ) ? $data['cp-lazy-img'] : 0;
										$is_lazy_checked = ( $lazy_load_img ) ? ' checked="checked" ' : '';
										$uniq            = uniqid();
										?>
										<label for="plugin-support" style="width:340px; display: inline-block;font-size:14px;"><strong><?php esc_html_e( 'Lazy Load images', 'smile' ); ?></strong>
											<span class="cp-tooltip-icon has-tip" data-position="top" style="cursor: help;" title="<?php esc_html_e( 'Enable this option if you wish to load images aynchronously', 'smile' ); ?>">
												<i class="dashicons dashicons-editor-help"></i>
											</span>
										</label>
										</td>
										<td>
										<label class="switch-wrapper" style="display: inline-block;margin: 0;height: 20px;">
											<input type="text"  id="cp-lazy-img" class="form-control smile-input smile-switch-input"  name="cp-lazy-img" value="<?php echo esc_attr( $lazy_load_img ); ?>" />
											<input type="checkbox" <?php echo esc_attr( $is_lazy_checked ); ?> id="smile_cp-lazy-img_btn_<?php echo esc_attr( $uniq ); ?>"  class="ios-toggle smile-input smile-switch-input switch-checkbox smile-switch " value="<?php echo esc_attr( $lazy_load_img ); ?>" >
											<label class="smile-switch-btn checkbox-label" data-on="ON"  data-off="OFF" data-id="cp-lazy-img" for="smile_cp-lazy-img_btn_<?php echo esc_attr( $uniq ); ?>"></label>
										</label>
									</td>
								</tr>
							</table>
						</p>
						</div>

						<!-- Gravity form -->
						<div class="debug-section">
							<p>
								<table>
									<tr>
										<td style="vertical-align: top;">
										<?php
										$cp_close_gravity = isset( $data['cp-close-gravity'] ) ? $data['cp-close-gravity'] : 1;
										$is_lazy_checked  = ( $cp_close_gravity ) ? ' checked="checked" ' : '';
										$uniq             = uniqid();
										?>
										<label for="plugin-support" style="width:340px; display: inline-block;font-size:14px;"><strong><?php esc_html_e( 'Close Custom Form', 'smile' ); ?></strong>
											<span class="cp-tooltip-icon has-tip" data-position="top" style="cursor: help;" title="<?php esc_html_e( 'Enable this option if you wish to close the custom gravity form, CF7 or Ninja Form inside the modules', 'smile' ); ?>">
												<i class="dashicons dashicons-editor-help"></i>
											</span>
										</label>
										</td>
										<td>
										<label class="switch-wrapper" style="display: inline-block;margin: 0;height: 20px;">
											<input type="text"  id="cp-close-gravity" class="form-control smile-input smile-switch-input"  name="cp-close-gravity" value="<?php echo esc_attr( $cp_close_gravity ); ?>" />
											<input type="checkbox" <?php echo esc_attr( $is_lazy_checked ); ?> id="smile_cp-close-gravity_btn_<?php echo esc_attr( $uniq ); ?>"  class="ios-toggle smile-input smile-switch-input switch-checkbox smile-switch " value="<?php echo esc_attr( $cp_close_gravity ); ?>" >
											<label class="smile-switch-btn checkbox-label" data-on="ON"  data-off="OFF" data-id="cp-close-gravity" for="smile_cp-close-gravity_btn_<?php echo esc_attr( $uniq ); ?>"></label>
										</label>
									</td>
								</tr>
							</table>
						</p>
						</div>

						<!-- Load CSS and JS asynchronously. -->
						<div class="debug-section">
							<?php
							$data       = get_option( 'convert_plug_settings' );
							$gfval      = isset( $data['cp-load-syn'] ) ? $data['cp-load-syn'] : 0;
							$is_checked = ( $gfval ) ? ' checked="checked" ' : '';
							$uniq       = uniqid();
							?>
							<p>
								<label for="hide-options" style="width:340px; display: inline-block;font-size:14px;"><strong><?php esc_html_e( 'Load CSS/JS Asynchronous', 'smile' ); ?></strong>
									<span class="cp-tooltip-icon has-tip" data-position="top" style="cursor: help;" title="<?php esc_html_e( 'Enable this option if you wish to load CSS files Asynchronously', 'smile' ); ?>">
										<i class="dashicons dashicons-editor-help"></i>
									</span>
								</label>
								<label class="switch-wrapper" style="display: inline-block;margin: 0;height: 20px;">
									<input type="text"  id="cp-load-syn" class="form-control smile-input smile-switch-input"  name="cp-load-syn" value="<?php echo esc_attr( $gfval ); ?>" />
									<input type="checkbox" <?php echo esc_attr( $is_checked ); ?> id="smile_cp-load-syn_btn_<?php echo esc_attr( $uniq ); ?>"  class="ios-toggle smile-input smile-switch-input switch-checkbox smile-switch " value="<?php echo esc_attr( $gfval ); ?>" >
									<label class="smile-switch-btn checkbox-label" data-on="ON"  data-off="OFF" data-id="cp-load-syn" for="smile_cp-load-syn_btn_<?php echo esc_attr( $uniq ); ?>"></label>
								</label>
							</p><!-- Google Fonts -->
						</div>

						<div class="debug-section">
							<!-- Turn On/Off subscriber notification -->
							<?php
							$cp_change_ntf_id = isset( $data['cp_change_ntf_id'] ) ? $data['cp_change_ntf_id'] : 1;
							$sub_checked      = ( $cp_change_ntf_id ) ? ' checked="checked" ' : '';
							?>
							<p>
								<label for="hide-options" style="width:340px; display: inline-block;font-size:14px;"><strong><?php esc_html_e( 'Enable Error Notification', 'smile' ); ?></strong>
									<span class="cp-tooltip-icon has-tip" data-position="top" style="cursor: help;" title="<?php esc_html_e( 'Enable Form submission error notification .', 'smile' ); ?>">
										<i class="dashicons dashicons-editor-help"></i>
									</span>
								</label>
								<label class="switch-wrapper" style="display: inline-block;margin: 0;height: 20px;">
									<input type="text"  id="cp_change_ntf_id" class="form-control smile-input smile-switch-input"  name="cp_change_ntf_id" value="<?php echo esc_attr( $cp_change_ntf_id ); ?>" />
									<input type="checkbox" <?php echo esc_attr( $sub_checked ); ?> id="smile_cp_change_ntf_id_btn_<?php echo esc_attr( $uniq ); ?>"  class="ios-toggle smile-input smile-switch-input switch-checkbox smile-switch " value="<?php echo esc_attr( $cp_change_ntf_id ); ?>" >
									<label class="smile-switch-btn checkbox-label" data-on="ON"  data-off="OFF" data-id="cp_change_ntf_id" for="smile_cp_change_ntf_id_btn_<?php echo esc_attr( $uniq ); ?>"></label>
								</label>
							</p><!-- end of subscriber notification-->
							<?php
							$cp_notify_email_to = isset( $data['cp_notify_email_to'] ) ? $data['cp_notify_email_to'] : get_option( 'admin_email' );
							?>
							<p 
							<?php
							if ( 1 === $cp_notify_email_to ) {
								echo "style='display:none;'"; }
							?>
								>
								<label for="hide-options" style="width:340px; vertical-align: top; display: inline-block;font-size:14px;"><strong><?php esc_html_e( 'Enter Email Id', 'smile' ); ?></strong>
									<span class="cp-tooltip-icon has-tip" data-position="top" style="cursor: help;" title="<?php esc_html_e( 'This is the email ID or email IDs you wish to receive subscriber error notifications on. Separate each email ID with a comma.', 'smile' ); ?>">
										<i class="dashicons dashicons-editor-help"></i>
									</span>
								</label>
								<textarea id="cp_notify_email_to" name="cp_notify_email_to" cols="40" rows="5"><?php echo esc_html( stripslashes( $cp_notify_email_to ) ); ?></textarea>
							</p><!-- Subscription Messages -->		
						</div><!-- .debug-section -->
					</form>
								<button type="button" class="button button-primary button-update-settings"><?php esc_html_e( 'Save Settings', 'smile' ); ?></button>
							</div>
						</div>
					</div>
				</div>
