<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

if ( isset( $_REQUEST['cp_admin_page_nonce'] ) && ! wp_verify_nonce( $_REQUEST['cp_admin_page_nonce'], 'cp_admin_page' ) ) {
			wp_die( 'No direct script access allowed!' );
}

global $cp_analytics_start_time,$cp_analytics_end_time;
if ( isset( $_GET['campaign'] ) ) {
	$list_ids   = esc_attr( $_GET['campaign'] );
	$list_array = explode( '||', $list_ids );
} else {
	$list_array = array( 'all' );
}

$s_date      = ( isset( $_GET['sd'] ) && ! empty( $_GET['sd'] ) ) ? esc_attr( $_GET['sd'] ) : $cp_analytics_start_time;
$e_date      = ( isset( $_GET['ed'] ) && ! empty( $_GET['ed'] ) ) ? esc_attr( $_GET['ed'] ) : $cp_analytics_end_time;
$chart_type  = ( isset( $_GET['cType'] ) && ! empty( $_GET['cType'] ) ) ? esc_attr( $_GET['cType'] ) : 'line';
$smile_lists = get_option( 'smile_lists' );

// to unset deactivated / inactive mailer addons.
if ( is_array( $smile_lists ) ) {
	foreach ( $smile_lists as $key => $list ) {
		$provider = $list['list-provider'];
		if ( 'Convert Plug' !== $provider ) {
			if ( ! isset( Smile_Framework::$addon_list[ $provider ] ) && ! isset( Smile_Framework::$addon_list[ strtolower( $provider ) ] ) ) {
				unset( $smile_lists[ $key ] );
			}
		}
	}
}
?>

<div class="wrap about-wrap bsf-connect bsf-connect-analytics bend">
	<div class="wrap-container">
		<div class="bend-heading-section">
			<?php wp_nonce_field( 'cp_analytics_contact', 'cp_analytics_nonce_contact' ); ?>
			<h1><?php esc_html_e( 'Analytics', 'smile' ); ?> <a class="add-new-h2" href="?page=contact-manager"><?php esc_html_e( 'Back to Campaigns List', 'smile' ); ?></a></h1>
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
		<hr class="bsf-extensions-lists-separator" style="margin: 22px 0px 45px 0px;">
	</hr>

	<div class="row cp-analytics-filter-section" style="display:none">
		<div class="container form-container analytics-form">
			<div class="col-sm-2 form-col-5">
				<label class="analytics-form-label"><?php esc_html_e( 'Select Campaign', 'smile' ); ?></label>
				<select id="list-dropdown"  multiple >
					<option value="all" 
					<?php
					if ( in_array( 'all', $list_array ) ) {
						selected( $list_array[0], 'all' );}
					?>
						><?php esc_html_e( 'All Campaigns', 'smile' ); ?></option>
						<?php foreach ( $smile_lists as $key => $value ) { ?>
						<option value="<?php echo esc_attr( $key ); ?>" 
							<?php
							if ( in_array( (string) $key, $list_array, true ) ) {
								selected( $key, $key ); }
							?>
								><?php echo esc_attr( $value['list-name'] ); ?></option>
								<?php } ?>
							</select>
						</div>
						<div class="col-sm-2 form-col-5">
							<label class="analytics-form-label"><?php esc_html_e( 'Start Date', 'smile' ); ?><span class="cp-analatics-italic"> <?php esc_html_e( '(dd-mm-yyyy)', 'smile' ); ?></span></label>
							<input type="text" placeholder="Start Date" id="cp-startDate" name="sDate" value="<?php echo esc_attr( $s_date ); ?>"/>
						</div>
						<div class="col-sm-2 form-col-5">
							<label class="analytics-form-label"><?php esc_html_e( 'End Date', 'smile' ); ?><span class="cp-analatics-italic"><?php esc_html_e( '(dd-mm-yyyy)', 'smile' ); ?></label>
							<input type="text" placeholder="End Date" id="cp-endDate" name="eDate" value="<?php echo esc_attr( $e_date ); ?>"/>
						</div>
						<div class="col-sm-2 form-col-5">
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
										<div class="col-sm-2 form-col-5">
											<button class="button-primary cp-chart-submit" type="submit" id="submit-query">Submit</button>
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
