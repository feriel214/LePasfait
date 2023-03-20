<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

// Prohibit direct script loading.
defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

if ( isset( $_REQUEST['cp_admin_page_nonce'] ) && ! wp_verify_nonce( $_REQUEST['cp_admin_page_nonce'], 'cp_admin_page' ) ) {
	return;
}

global $cp_analytics_start_time,$cp_analytics_end_time;

if ( isset( $_GET['style'] ) && ! empty( $_GET['style'] ) ) {
	$style_id    = sanitize_text_field( $_GET['style'] );
	$style_array = explode( '||', $style_id );
} else {
	$style_array = array( 'all' );
}

$s_date      = ( isset( $_GET['sd'] ) && ! empty( $_GET['sd'] ) ) ? sanitize_text_field( $_GET['sd'] ) : $cp_analytics_start_time;
$e_date      = ( isset( $_GET['ed'] ) && ! empty( $_GET['ed'] ) ) ? sanitize_text_field( $_GET['ed'] ) : $cp_analytics_end_time;
$chart_type  = ( isset( $_GET['cType'] ) && ! empty( $_GET['cType'] ) ) ? sanitize_text_field( $_GET['cType'] ) : 'line';
$comp_factor = ( isset( $_GET['compFactor'] ) && ! empty( $_GET['compFactor'] ) ) ? sanitize_text_field( $_GET['compFactor'] ) : 'imp';

$export_an_nonce  = wp_create_nonce( 'cp-export-analytics' );
$analytics_export = wp_create_nonce( 'cp_analytics_export' );

$form_action = esc_url(
	add_query_arg(
		array(
			'action'           => 'cp_export_analytics',
			'analytics_export' => $analytics_export,
		),
		admin_url( 'admin-post.php' )
	)
);

$modal_page_url = esc_url(
	add_query_arg(
		array(
			'page' => 'smile-modal-designer',
		),
		admin_url( 'admin.php' )
	)
);

?>

<div class="wrap about-wrap bsf-connect bsf-connect-list bend">
	<div class="wrap-container">
		<div class="bend-heading-section">
			<h1><?php echo esc_html_e( 'Modal Analytics', 'smile' ); ?> <a class="add-new-h2" href="<?php echo esc_attr( $modal_page_url ); ?>" rel="noopener"><?php esc_html_e( 'Back to Modal List', 'smile' ); ?></a></h1>
			<div class="bend-head-logo"></div>
		</div>
		<!-- bend-heading section -->

		<div class="msg"></div>
		<div class="bend-content-wrap" style="position: relative;margin-top: 40px !important;">
			<div class="smile-absolute-loader smile-top-fix-loader" style="visibility: visible;-webkit-transition: visibility 100ms linear, background-color 100ms linear;
			-moz-transition: visibility 100ms linear, background-color 100ms linear;
			transition: visibility 100ms linear, background-color 100ms linear;">
			<div class="smile-loader">
				<div class="smile-loading-bar"></div>
				<div class="smile-loading-bar"></div>
				<div class="smile-loading-bar"></div>
				<div class="smile-loading-bar"></div>
			</div>
		</div>
		<hr class="bsf-extensions-lists-separator" style="margin: 22px 0px 30px 0px;">
	</hr>
	<input type="hidden" id="cp-module" value="modal" >	
	<div class="row cp-analytics-filter-section" style="display:none">
		<div class="container form-container analytics-form">
			<div class="col-sm-2">
				<label class="analytics-form-label"><?php esc_html_e( 'Select Modal', 'smile' ); ?></label>
				<select id="style-dropdown" multiple>
					<option value="all" 
					<?php
					if ( in_array( 'all', $style_array ) ) {
						selected( $style_array[0], 'all' );}
					?>
						>All Modals</option>
						<?php foreach ( $prev_styles as $key => $value ) { ?>
							<?php $style_name = urldecode( $value['style_name'] ); ?>
							<?php if ( ! isset( $value['multivariant'] ) ) { ?>
						<option value="<?php echo esc_attr( $value['style_id'] ); ?>" 
								<?php
								if ( in_array( $value['style_id'], $style_array ) ) {
									selected( $value['style_id'], $style_array[0] );}
								?>
								><?php echo esc_attr( $style_name ); ?></option>
								<?php } ?>
								<?php
								if ( isset( $value['style_id'] ) ) {
									if ( is_array( $variant_tests ) && isset( $variant_tests[ $value['style_id'] ] ) ) {
										foreach ( $variant_tests[ $value['style_id'] ] as $key => $variant_test ) {
											$style_name = $variant_test['style_name'];
											$style_id   = $variant_test['style_id'];
											?>

											<option data-variant='true' value="<?php echo esc_attr( $style_id ); ?>" 
												<?php
												if ( in_array( $style_id, $style_array ) ) {
													selected( $style_id, $style_array[0] );}
												?>
													><?php echo esc_html( urldecode( stripslashes( $style_name ) ) ); ?></option>";
													<?php
										}
									}
								}
								?>
										<?php } ?>
									</select>
								</div>
								<div class="col-sm-2">
									<label class="analytics-form-label"><?php esc_html_e( 'Start Date', 'smile' ); ?><span class="cp-analatics-italic"> <?php esc_html_e( '(dd-mm-yyyy)', 'smile' ); ?></span></label>
									<input type="text" placeholder="Start Date" id="cp-startDate" name="sDate" value="<?php echo esc_attr( $s_date ); ?>"/>
								</div>
								<div class="col-sm-2">
									<label class="analytics-form-label"><?php esc_html_e( 'End Date ', 'smile' ); ?><span class="cp-analatics-italic"><?php esc_html_e( 'dd-mm-yyyy', 'smile' ); ?></span></label>
									<input type="text" placeholder="End Date" id="cp-endDate" name="eDate" value="<?php echo esc_attr( $e_date ); ?>"/>
								</div>
								<div class="col-sm-2">
									<label class="analytics-form-label"><?php esc_html_e( 'Graph Type', 'smile' ); ?></label>
									<select id="cp-chart-type">
										<option value="line" 
										<?php
										if ( 'line' === $chart_type ) {
											selected( $chart_type, $chart_type );}
										?>
											>Line</option>
											<option value="bar" 
											<?php
											if ( 'bar' === $chart_type ) {
												selected( $chart_type, $chart_type );}
											?>
												>Bar</option>
												<option value="donut" 
												<?php
												if ( 'donut' === $chart_type ) {
													selected( $chart_type, $chart_type );}
												?>
													>Donut</option>
													<option value="polararea" 
													<?php
													if ( 'polararea' === $chart_type ) {
														selected( $chart_type, $chart_type );}
													?>
														>Polar Area</option>
													</select>
												</div>
												<div class="col-sm-2">
													<label class="analytics-form-label"><?php esc_html_e( 'Comparison Factor', 'smile' ); ?></label>
													<select id="cp-chart-comp-type">
														<?php if ( 'impVsconv' === $comp_factor ) { ?>
															<option value="impVsconv"
															<?php selected( $comp_factor, $comp_factor ); ?>
															><?php esc_html_e( 'Impression Vs Conversion', 'smile' ); ?></option>
														<?php } ?>
														<option value="imp" 
														<?php
														if ( 'imp' === $comp_factor ) {
															selected( $comp_factor, $comp_factor );}
														?>
															><?php esc_html_e( 'Impression', 'smile' ); ?> </option>
															<option value="conv" 
															<?php
															if ( 'conv' === $comp_factor ) {
																selected( $comp_factor, $comp_factor );}
															?>
																><?php esc_html_e( 'Conversion', 'smile' ); ?></option>
																<option value="convRate" 
																<?php
																if ( 'convRate' === $comp_factor ) {
																	selected( $comp_factor, $comp_factor );}
																?>
																	><?php esc_html_e( 'Conversion Rate', 'smile' ); ?></option>
																</select>
															</div>
															<div class="col-sm-2 cp-exp-anltcs">
																<?php wp_nonce_field( 'cp_analytics', 'cp_analytics_nonce' ); ?>

																<button class="col-sm-8 button-primary cp-chart-submit" type="submit" id="submit-query">Submit</button>
															<!-- Export Analytics -->			
															<form method="post" class="col-sm-4 cp-export-analytics" action="<?php echo esc_attr( $form_action ); ?>">					
																<input type="hidden" name="an_data" id ="cp-module-data" value="" />
																<input type="hidden" name="_wpnonce" id ="_wpnonce" value="<?php echo esc_attr( $export_an_nonce ); ?>" />
																<input type="hidden" name="comp_factor" id ="comp_factor" value="<?php echo esc_attr( $comp_factor ); ?>" />
																<a class="action-list action-download-analytics " href="#" target="_top" style="margin-right: 25px !important;" data-comp-factor = "<?php echo esc_attr( $comp_factor ); ?>"><i style="line-height: 30px;font-size: 30px;" class="connects-icon-download"></i><span class="action-tooltip"><?php esc_html_e( 'Export CSV', 'smile' ); ?></span></a>
															</form>			
																<!-- Export Analytics -->	
															</div>						
														</div>
														<!-- .form-container -->
													</div>
													<!-- .row -->

													<div class="row" style="padding-left: 15px;padding-right: 15px;">
														<div class="container cp-graph-area cp-hidden">
															<div class="col-lg-12 col-sm-12 cp-graph-width">
																<div id="canvas-holder" class="chart-holder" >
																	<canvas id="line-chart" />
																</div>
																<div id="chartjs-tooltip"></div>
															</div>
															<div class="col-lg-12 col-sm-12">
																<div id="chart-legend"></div>
															</div>
														</div>
														<!-- .container -->
													</div>
													<!-- .row -->

												</div>
												<!-- .bend-content-wrap -->
											</div>
											<!-- .wrap-container -->
										</div>
										<!-- .wrap -->									
