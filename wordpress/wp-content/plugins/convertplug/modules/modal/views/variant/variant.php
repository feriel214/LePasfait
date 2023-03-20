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
$parent_style  = isset( $_GET['style'] ) ? sanitize_text_field( $_GET['style'] ) : '';
$theme         = isset( $_GET['theme'] ) ? sanitize_text_field( $_GET['theme'] ) : '';

$variants       = array();
$analytics_link = '#';
$multivariant   = false;
if ( $variant_tests ) {
	if ( isset( $variant_tests[ $variant_style ] ) ) {
		foreach ( $variant_tests[ $variant_style ] as $value ) {
			$variants[] = $value['style_id'];
		}
	}
}

if ( $prev_styles ) {
	foreach ( $prev_styles as $key => $style ) {
		if ( $style['style_id'] == $variant_style ) {
			if ( isset( $style['multivariant'] ) ) {
				$multivariant = true;
			}
		}
	}
}

if ( ! $multivariant ) {
	$variants[] = $variant_style;
}

$style_for_analytics = implode( '||', $variants );

if ( 1 < count( $variants ) ) {
	$comp_factor = 'imp';
} else {
	$comp_factor = 'impVsconv';
}

if ( count( $variants ) > 0 ) {
	$analytics_link = '?page=smile-modal-designer&style-view=analytics&compFactor=' . $comp_factor . '&style=' . rawurlencode( $style_for_analytics );
}
?>

<div class="wrap about-wrap bend cp-modal-main">
	<div class="wrap-container">
		<div class="bend-heading-section">
			<h1 style="font-size: 38px;">
				<?php esc_html_e( 'Variants for', 'smile' ); ?>
				<?php
				$rand               = substr( md5( uniqid() ), wp_rand( 0, 26 ), 5 );
				$dynamic_style_name = 'cp_id_' . $rand;

				$modal_url = add_query_arg(
					array(
						'page'          => 'smile-modal-designer',
						'style-view'    => 'variant',
						'variant-test'  => 'edit',
						'action'        => 'new',
						'style_id'      => $variant_style,
						'variant-style' => $dynamic_style_name,
						'style'         => $parent_style,
						'theme'         => $theme,
					),
					admin_url( 'admin.php' )
				);

				$modal_page_url = esc_url(
					add_query_arg(
						array(
							'page' => 'smile-modal-designer',
						),
						admin_url( 'admin.php' )
					)
				);

				$modal_new_url = esc_url(
					add_query_arg(
						array(
							'page'       => 'smile-modal-designer',
							'style-view' => 'new',
						),
						admin_url( 'admin.php' )
					)
				);

				$modal_variant_edit_url = esc_url(
					add_query_arg(
						array(
							'page'          => 'smile-modal-designer',
							'style-view'    => 'variant',
							'variant-test'  => 'edit',
							'action'        => 'new',
							'style_id'      => $variant_style,
							'variant-style' => $dynamic_style_name,
							'style'         => $parent_style,
							'theme'         => $theme,
						),
						admin_url( 'admin.php' )
					)
				);

				?>
				<span class="cp-strip-text" style="max-width: 460px;top: 10px;" title="<?php echo esc_attr( stripslashes( urldecode( $parent_style ) ) ); ?>"><?php echo esc_html( stripslashes( urldecode( $parent_style ) ) ); ?> </span>
			</h1>
			<a target="_blank" rel="noopener" href="<?php echo esc_attr( $modal_variant_edit_url ); ?>" title="Create New Variant" class="bsf-connect-download-csv" style="margin-right: 25px !important;"><i class="connects-icon-square-plus" style="line-height: 30px;font-size: 22px;"></i>
				<?php esc_html_e( 'Create New Variant', 'smile' ); ?>
			</a>
			<a href="<?php echo esc_url( $analytics_link ); ?>"  style="margin-right: 25px !important;" class="bsf-connect-download-csv"><i class="connects-icon-bar-graph-2" style="line-height: 30px;"></i>
				<?php esc_html_e( 'Analytics', 'smile' ); ?>
			</a>
			<a href="<?php echo esc_attr( $modal_page_url ); ?>" style="margin-right: 25px !important;" class="bsf-connect-download-csv"><i class="connects-icon-reply" style="line-height: 30px;font-size: 22px;"></i>
				<?php esc_html_e( 'Back to Modal List', 'smile' ); ?>
			</a>

			<div class="message"></div>
		</div>
		<!-- bend-heading section -->

		<div class="bend-content-wrap" style="margin-top: 40px;">
			<hr class="bsf-extensions-lists-separator" style="margin: 22px 0px 30px 0px;">
		</hr>
		<div class="container">
			<?php
			$change_status_nonce = wp_create_nonce( 'cp-change-style-status' );
			$delete_style_nonce  = wp_create_nonce( 'cp-delete-style' );
			?>
			<input type="hidden" id="cp-change-status-nonce" value="<?php echo esc_attr( $change_status_nonce ); ?>" />
			<input type="hidden" id="cp-delete-style-nonce" value="<?php echo esc_attr( $delete_style_nonce ); ?>" />
			<div id="smile-stored-styles">
				<table class="wp-list-table widefat fixed cp-list-optins cp-modal-list-optins cp-variants-list">
					<thead>
						<tr>
							<th scope="col" id="style-name" class="manage-column column-style"><span class="connects-icon-ribbon"></span>
								<?php esc_html_e( 'Variant Name', 'smile' ); ?></th>
								<th scope="col" id="impressions" class="manage-column column-impressions"><span class="connects-icon-disc"></span>
									<?php esc_html_e( 'Impressions', 'smile' ); ?></th>
									<th scope="col" id="status" class="manage-column column-status"><span class="connects-icon-toggle"></span>
										<?php esc_html_e( 'Status', 'smile' ); ?></th>
										<th scope="col" id="actions" class="manage-column column-actions" style="min-width: 300px;"><span class="connects-icon-cog"></span>
											<?php esc_html_e( 'Actions', 'smile' ); ?></th>
										</tr>
									</thead>
									<tbody id="the-list" class="smile-style-data smile-style-modal-variant">
										<?php

										if ( is_array( $prev_styles ) && ! empty( $prev_styles ) ) {
											foreach ( $prev_styles as $key => $style ) {
												$style_name = $style['style_name'];
												$style_id   = $style['style_id'];
												if ( $style_id == $variant_style ) {

													$impressions = 0;

													if ( isset( $analytics_data[ $style_id ] ) ) {
														foreach ( $analytics_data[ $style_id ] as $key => $value ) {
															$impressions = $impressions + $value['impressions'];
														}
													}

													$style_settings = maybe_unserialize( $style['style_settings'] );
													$theme          = $style_settings['style'];
													$live           = (int) $style_settings['live'];
													if ( '' === $live ) {
														$live = 0;
													}
													$modal_status = '';
													if ( 1 === $live ) {
														$modal_status .= '<span class="change-status"><span data-live="1" class="cp-status"><i class="connects-icon-play"></i><span>' . __( 'Live', 'smile' ) . '</span></span>';
													} elseif ( 0 === $live ) {
														$modal_status .= '<span class="change-status"><span data-live="0" class="cp-status"><i class="connects-icon-pause"></i><span>' . __( 'Pause', 'smile' ) . '</span></span>';
													} else {
														$modal_status .= '<span class="change-status"><span data-live="2" class="cp-status"><i class="connects-icon-clock"></i><span>' . __( 'Scheduled', 'smile' ) . '</span></span>';
													}
													$modal_status .= '<ul class="manage-column-menu">';
													if ( 1 !== $live && '1' !== $live ) {
														$modal_status .= '<li><a href="#" class="change-status" data-style-id="' . $style_id . '" data-live="1" data-option="smile_modal_styles"><i class="connects-icon-play"></i><span>' . __( 'Live', 'smile' ) . '</span></a></li>';
													}
													if ( ( 0 !== $live && '0' !== $live ) && '' !== $live ) {
														$modal_status .= '<li><a href="#" class="change-status" data-style-id="' . $style_id . '" data-live="0" data-option="smile_modal_styles"><i class="connects-icon-pause"></i><span>' . __( 'Pause', 'smile' ) . '</span></a></li>';
													}
													if ( 2 !== $live && '2' !== $live ) {
														$modal_status .= '<li><a href="#" class="change-status" data-style-id="' . $style_id . '" data-live="2" data-option="smile_modal_styles" data-schedule="1"><i class="connects-icon-clock"></i><span>' . __( 'Schedule', 'smile' ) . '</span></a></li>';
													}
													$modal_status .= '</ul>';
													$modal_status .= '</span>';
													?>
													<?php if ( ! isset( $style['multivariant'] ) ) { ?>
													<tr id="<?php echo esc_attr( $key ); ?>" class="ui-sortable-handle">
														<?php
														$modal_edit_style_url = esc_url(
															add_query_arg(
																array(
																	'page'       => 'smile-modal-designer',
																	'style-view' => 'edit',
																	'style' => $style_id,
																	'theme' => $theme,
																),
																admin_url( 'admin.php' )
															)
														);

														?>
														<td class="name column-name"><a rel="noopener" target="_blank" href="<?php echo esc_attr( $modal_edit_style_url ); ?>"> <?php echo esc_html( urldecode( $style_name ) ); ?> </a></td>
														<td class="column-impressions" style="vertical-align: inherit;"><?php echo esc_attr( $impressions ); ?></td>
														<td class="column-status" style="vertical-align: inherit;"><?php echo wp_kses_post( $modal_status ); ?></td>
														<td class="actions column-actions" style="vertical-align: inherit;">
															<a class="action-list copy-style-icon" data-style="<?php echo esc_attr( $style_id ); ?>" data-variant-style="<?php echo esc_attr( $variant_style ); ?>" data-module="modal"  data-stylescreen="multivariant" data-option="modal_variant_tests" href="#"><i class="connects-icon-paper-stack" style="font-size: 20px;"></i><span class="action-tooltip">
																<?php esc_html_e( 'Duplicate Modal', 'smile' ); ?>
															</span></a>
															<?php
															$modal_analytics_url = esc_url(
																add_query_arg(
																	array(
																		'page'       => 'smile-modal-designer',
																		'compFactor' => 'impVsconv',
																		'style-view' => 'analytics',
																		'style' => $style_id,
																	),
																	admin_url( 'admin.php' )
																)
															);

															?>
															<a class="action-list" style="margin-left: 25px;" data-style="<?php echo rawurlencode( $style_id ); ?>" data-option="smile_modal_styles" href="<?php echo esc_attr( $modal_analytics_url ); ?>"><i class="connects-icon-bar-graph-2"></i><span class="action-tooltip">
																<?php esc_html_e( 'View Analytics', 'smile' ); ?>
															</span></a>
															<?php echo wp_kses_post( apply_filters( 'cp_before_delete_action', $style_settings, 'modal' ) ); ?>
															<a class="action-list trash-style-icon" data-delete="soft" data-variantoption="modal_variant_tests" data-style="<?php echo esc_attr( $style_id ); ?>" data-option="smile_modal_styles" style="margin-left: 25px;" href="#"><i class="connects-icon-trash"></i><span class="action-tooltip">
																<?php esc_html_e( 'Delete Modal', 'smile' ); ?>
															</span></a>
														</td>
													</tr>
													<?php } ?>
													<?php
												}
											}
										}
										?>
										<?php
										$variant_tests = isset( $variant_tests[ $variant_style ] ) ? $variant_tests[ $variant_style ] : '';

										if ( is_array( $variant_tests ) && ! empty( $variant_tests ) ) {
											$variant_tests = array_reverse( $variant_tests );
											foreach ( $variant_tests as $key => $variant_test ) {
												$style_name  = $variant_test['style_name'];
												$style_id    = $variant_test['style_id'];
												$impressions = 0;

												if ( isset( $analytics_data[ $style_id ] ) ) {
													foreach ( $analytics_data[ $style_id ] as $key => $value ) {
														$impressions = $impressions + $value['impressions'];
													}
												}

												$style_settings = maybe_unserialize( $variant_test['style_settings'] );
												$theme          = $style_settings['style'];
												$live           = $style_settings['live'];
												if ( '' === $live ) {
													$live = 0;
												}
												$modal_status = '';
												if ( 1 === $live || '1' === $live ) {
													$modal_status .= '<span class="change-status"><span data-live="1" class="cp-status"><i class="connects-icon-play"></i><span>' . __( 'Live', 'smile' ) . '</span></span>';
												} elseif ( 0 === $live || '0' === $live ) {
													$modal_status .= '<span class="change-status"><span data-live="0" class="cp-status"><i class="connects-icon-pause"></i><span>' . __( 'Pause', 'smile' ) . '</span></span>';
												} else {
													$modal_status .= '<span class="change-status"><span data-live="2" class="cp-status"><i class="connects-icon-clock"></i><span>' . __( 'Scheduled', 'smile' ) . '</span></span>';
												}
												$modal_status .= '<ul class="manage-column-menu">';
												if ( 1 !== $live && '1' !== $live ) {
													$modal_status .= '<li><a href="#" class="change-status" data-style-id="' . $style_id . '" data-variant="modal_variant_tests" data-live="1" data-option="modal_variant_tests"><i class="connects-icon-play"></i><span>' . __( 'Live', 'smile' ) . '</span></a></li>';
												}
												if ( ( 0 !== $live && '0' !== $live ) && '' !== $live ) {
													$modal_status .= '<li><a href="#" class="change-status" data-style-id="' . $style_id . '" data-variant="modal_variant_tests" data-live="0" data-option="modal_variant_tests"><i class="connects-icon-pause"></i><span>' . __( 'Pause', 'smile' ) . '</span></a></li>';
												}
												if ( 2 !== $live && '2' !== $live ) {
													$modal_status .= '<li><a href="#" class="change-status" data-style-id="' . $style_id . '" data-variant="modal_variant_tests" data-live="2" data-option="modal_variant_tests" data-schedule="1"><i class="connects-icon-clock"></i><span>' . __( 'Schedule', 'smile' ) . '</span></a></li>';
												}
												$modal_status  .= '</ul>';
												$modal_status  .= '</span>';
												$modal_sort_url = add_query_arg(
													array(
														'page' => 'smile-modal-designer',
														'style-view' => 'variant',
														'variant-test' => 'edit',
														'variant-style' => $style_id,
														'style' => stripslashes( $style_name ),
														'parent-style' => $parent_style,
														'style_id' => $variant_style,
														'theme' => $theme,
													),
													admin_url( 'admin.php' )
												);

												?>
												<tr id="<?php echo esc_attr( $key ); ?>" class="ui-sortable-handle">
													<td class="name column-name"><a target="_blank" rel="noopener" href="<?php echo esc_attr( esc_url( $modal_sort_url ) ); ?>"> <?php echo esc_html( urldecode( stripslashes( $style_name ) ) ); ?> </a></td>
													<td class="column-impressions" style="vertical-align: inherit;"><?php echo esc_html( $impressions ); ?></td>
													<td class="column-status" style="vertical-align: inherit;"><?php echo wp_kses_post( $modal_status ); ?></td>
													<td class="actions column-actions" style="vertical-align: inherit;">
														<a class="action-list copy-style-icon" data-style="<?php echo esc_attr( $style_id ); ?>" data-variant-style="<?php echo esc_attr( $variant_style ); ?>" data-module="modal"  data-option="modal_variant_tests" data-stylescreen="multivariant" href="#"><i class="connects-icon-paper-stack" style="font-size: 20px;"></i><span class="action-tooltip">
															<?php esc_html_e( 'Duplicate Modal', 'smile' ); ?>
														</span></a>

														<?php
														$modal_analytics_url = esc_url(
															add_query_arg(
																array(
																	'page' => 'smile-modal-designer',
																	'style-view' => 'analytics',
																	'compFactor' => 'impVsconv',
																	'style' => $style_id,
																),
																admin_url( 'admin.php' )
															)
														);
														?>
														<a class="action-list" data-style="<?php echo rawurlencode( $style_id ); ?>" data-option="smile_modal_styles" style="margin-left: 25px;" href="<?php echo esc_attr( $modal_analytics_url ); ?>"><i class="connects-icon-bar-graph-2"></i><span class="action-tooltip">
															<?php esc_html_e( 'View Analytics', 'smile' ); ?>
														</span></a>
														<?php echo wp_kses_post( apply_filters( 'cp_before_delete_action', $style_settings, 'modal' ) ); ?>
														<a class="action-list trash-style-icon" data-delete="hard" data-variantoption="modal_variant_tests" data-style="<?php echo esc_attr( $style_id ); ?>" data-option="modal_variant_tests" style="margin-left: 25px;" href="#"><i class="connects-icon-trash"></i><span class="action-tooltip">
															<?php esc_html_e( 'Delete Modal', 'smile' ); ?>
														</span></a>
													</td>
												</tr>
												<?php
											}
										} else {
											$modal_sort_url = add_query_arg(
												array(
													'page' => 'smile-modal-designer',
													'style-view' => 'variant',
													'variant-test' => 'edit',
													'action' => 'new',
													'style_id' => $variant_style,
													'variant-style' => $dynamic_style_name,
													'theme' => $theme,
												),
												admin_url( 'admin.php' )
											);

											?>
											<tr>
												<th class="cp-list-empty cp-empty-graphic" colspan="4"><?php echo esc_attr__( 'FIRST TIME BEING HERE?', 'smile' ); ?><br/><a class="add-new-h2" href="<?php echo esc_attr( esc_url( $modal_sort_url ) ); ?>">
													<?php esc_html_e( "Awesome! Let's start with your first variant", 'smile' ); ?>
												</a></th>
											</tr>
											<?php
										}
										?>
									</tbody>
								</table>
								</div>
								<!-- smile-stored-styles -->
							</div>
							<!-- .container -->
						</div>
						<!-- .bend-content-wrap -->

						<!-- scheduler popup -->
						<div class="cp-schedular-overlay">
							<div class="cp-scheduler-popup">
								<div class="cp-scheduler-close"> <span class="connects-icon-cross"></span> </div>
								<div class="cp-row">
									<div class="schedular-title">
										<h3>
											<?php esc_html_e( 'Schedule This Modal', 'smile' ); ?>
										</h3>
									</div>
								</div>
								<!-- cp-row -->
								<div class="cp-row">
									<div class="scheduler-container">
										<div class="container cp-start-time">
											<div class="col-md-6">
												<h3>
													<?php esc_html_e( 'Enter Starting Time', 'smile' ); ?>
												</h3>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<div class="input-group date">
														<input type="text" id="cp_start_time" class="form-control cp_start" />
														<span class="input-group-addon"><span class="connects-icon-clock"></span></span> </div>
													</div>
												</div>
											</div>
											<div class="container cp-end-time">
												<div class="col-md-6">
													<h3>
														<?php esc_html_e( 'Enter Ending Time', 'smile' ); ?>
													</h3>
												</div>
												<div class="col-md-6">
													<div class="form-group">
														<div class="input-group date">
															<input type="text" id="cp_end_time" class="form-control cp_end" />
															<span class="input-group-addon"><span class="connects-icon-clock"></span></span> </div>
														</div>
														<!-- form-group -->
													</div>
												</div>
												<!-- cp-end-time -->
											</div>
											<!-- scheduler-container -->
										</div>
										<!-- cp-row -->
										<div class="cp-row">
											<div class="cp-actions">
												<div class="cp-action-buttons">
													<button class="button button-primary cp-schedule-btn">
														<?php esc_html_e( 'Schedule Modal', 'smile' ); ?>
													</button>
													<button class="button button-primary cp-schedule-cancel" onclick="jQuery(document).trigger('dismissPopup')">
														<?php esc_html_e( 'Cancel', 'smile' ); ?>
													</button>
												</div>
											</div>
										</div>
										<!-- cp-row -->
									</div>
									<!-- .cp-schedular-popup -->
								</div>
								<!-- .cp-schedular-overlay -->
							</div>
							<!-- .wrap-container -->
						</div>
						<!-- .wrap -->

