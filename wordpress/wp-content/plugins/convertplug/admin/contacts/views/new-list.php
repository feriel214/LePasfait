<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

// Add new input type "border".
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

if ( isset( $_REQUEST['cp_admin_page_nonce'] ) && ! wp_verify_nonce( $_REQUEST['cp_admin_page_nonce'], 'cp_admin_page' ) ) {
	wp_die( 'No direct script access allowed!' );
}
$cp_addon_list = Smile_Framework::$addon_list;

wp_enqueue_script( 'convert-plug-connects-list', CP_PLUGIN_URL . 'admin/contacts/js/connects-new-list.js', array( 'jquery' ), CP_VERSION, true );
wp_localize_script(
	'convert-plug-connects-list',
	'cplus_connect_list',
	array(
		'addons_lists'             => $cp_addon_list,
		'is_campaign_exists_nonce' => wp_create_nonce( 'is_campaign_exists_nonce' ),
	)
);

?>
<div class="wrap about-wrap bsf-connect bsf-connect-new-list bend">
	<div class="wrap-container">
		<div class="bend-heading-section bsf-connect-header bsf-cnlist-header">
			<h1><?php esc_html_e( 'Create New Campaign', 'smile' ); ?></h1>
		</div>
		<!-- bend-heading section -->
		<div class="msg"></div>
		<input type="hidden" id="cp-connect-url" value="<?php echo esc_url( admin_url( 'admin.php?page=contact-manager&view=new-list' ) ); ?>">
		<div class="bend-content-wrap">
			<div class="smile-absolute-loader">
				<div class="smile-loader" style="transform: none !important;top: 120px !important;">
					<div class="smile-loading-bar"></div>
					<div class="smile-loading-bar"></div>
					<div class="smile-loading-bar"></div>
					<div class="smile-loading-bar"></div>
				</div>
			</div>
			<hr class="bsf-extensions-lists-separator" style="margin: -20px 0px 45px 0px;"></hr>
			<div class="container bsf-cnlist-content">
				<div class="bsf-cnlist-form col-sm-6 col-sm-offset-3">
					<div class="cp-wizard-progress">
						<div class="cp-wizard-progress-bar"></div>
					</div>
					<form id="bsf-cnlist-contact-form">
						<div class="container">
							<div class="col-sm-12">
								<div class="bsf-cnlist-form-row">
									<input type="hidden" name="action" value="smile_add_list" />
									<input type="hidden" name="date" value="<?php echo esc_attr( gmdate( 'j-n-Y' ) ); ?>" />
									<?php wp_nonce_field( 'cp-create-list-nonce' ); ?>
								</div>
								<?php
								$step1_class = '';
								$step2_class = '';

								if ( isset( $_GET['step'] ) && '1' == esc_attr( $_GET['step'] ) ) {
									$step1_class = 'active in';
								}

								if ( isset( $_GET['step'] ) && '2' == esc_attr( $_GET['step'] ) ) {
									$step2_class = 'active in';
								}
								?>
								<div class="step-1 bsf-cnlist-form-wizard <?php echo esc_attr( $step1_class ); ?>">
									<div class="steps-section">
										<div class="bsf-cnlist-form-row bsf-cnlist-list-name" >
											<label for="bsf-cnlist-list-name" >
												<?php esc_html_e( 'Campaign Name', 'smile' ); ?>
											</label>
											<?php $campaign_name = isset( $_GET['campaign'] ) ? sanitize_text_field( $_GET['campaign'] ) : ''; ?>
											<input type="text" id="bsf-cnlist-list-name" name="list-name" autofocus="autofocus" value="<?php echo esc_attr( $campaign_name ); ?>" />
											<span class="cp-validation-error"></span>
										</div>

										<?php
										if ( ! empty( $cp_addon_list ) ) {
											?>

											<div class="bsf-cnlist-form-row bsf-cnlist-list-provider" >
												<label for="bsf-cnlist-list-provider" >
													<?php esc_html_e( 'Do you want to sync connects with any third party software?', 'smile' ); ?>
												</label>
												<select id="bsf-cnlist-list-provider" class="bsf-cnlist-select" 

												name="list-provider">
													<option value="Convert Plug">No</option>
													<?php
													$list_provider = isset( $_GET['list-provider'] ) ? esc_attr( $_GET['list-provider'] ) : '';
													foreach ( $cp_addon_list as $slug => $setting ) {
														$selected = ( $slug == $list_provider ) ? 'selected' : '';
														echo '<option value="' . esc_attr( $slug ) . '" ' . esc_attr( $selected ) . ' >' . esc_html( $setting['name'] ) . '</option>';
													}
													?>
												</select>
												<div class="bsf-cnlist-list-provider-spinner"></div>
											</div>
										<?php } ?>
										<div class="bsf-cnlist-form-row short-description" >
											<p class="description">
												<?php
												echo wp_kses_post(
													'Your connects can be synced to CRM & Mailer softwares like HubSpot, MailChimp, etc.<br><br><strong>Important Note</strong> - If you need to integrate with third party CRM & Mailer software like MailChimp, Infusionsoft, etc. please install the respective addon from.',
													'smile'
												);
												echo sprintf(
													wp_kses_post( '<a href="' . bsf_exension_installer_url( '14058953' ) . '">here</a>', 'smile' )
												);
												?>
												</p>
										</div>
									</div><!-- .steps-section -->
								</div>
								<!-- .step-1    -->
								<div class="step-2 bsf-cnlist-form-wizard <?php echo esc_attr( $step2_class ); ?>" >
									<div class="steps-section">
										<div class="col-sm-12">
											<div class="bsf-cnlist-form-row bsf-cnlist-mailer-data" style="display:none;"></div>
											<div class="bsf-cnlist-mailer-help">
												<?php
												$docs_url = 'https://www.convertplug.com/plus/docs-category/mailer-integration/';
												?>
												<a href="<?php echo esc_url( $docs_url ); ?>" target="_blank" rel="noopener" ><?php esc_html_e( 'Where to find this?', 'smile' ); ?></a>
											</div><!-- .bsf-cnlist-mailer-help -->
										</div>
									</div><!-- .steps-section -->
								</div>
								<!-- .step-2    -->
							</div>
						</div>

						<div class="container bsf-new-list-wizard">
							<div class="col-sm-6">
								<button class="wizard-prev button button-primary disabled" type="button">
									<?php esc_html_e( 'Previous', 'smile' ); ?>
								</button>
							</div>
							<div class="col-sm-6">
								<div class="bsf-cnlist-save-btn" >
									<button id="save-btn" class="wizard-save button button-primary" data-provider="">
										<?php esc_html_e( 'Create Campaign', 'smile' ); ?>
									</button>
								</div>
								<div class="bsf-cnlist-next-btn" style="display:none;">
									<button class="wizard-next button button-primary" type="button" style="display: inline-block;">
										<?php esc_html_e( 'Next', 'smile' ); ?>
									</button>
								</div>
							</div>
						</div><!-- .bsf-new-list-wizard -->
					</form>
				</div>
				<!-- .bsf-cnlist-form -->
			</div>
			<!-- .container -->
		</div>
		<!-- .bend-content-wrap -->
	</div>
	<!-- .wrap-container -->
</div>
<!-- .wrap -->
