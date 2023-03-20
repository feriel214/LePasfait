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

require_once CP_BASE_DIR . 'admin/contacts/views/class-cp-paginator.php';

	// Remove All Styles.
$remove_styles = ( isset( $_GET['remove-styles'] ) ) ? esc_attr( $_GET['remove-styles'] ) : 'false';
if ( 'true' === $remove_styles ) {
	delete_option( 'smile_style_analytics' );
	delete_option( 'slide_in_variant_tests' );
	delete_option( 'smile_slide_in_styles' );
	echo '<div style="background: #2F9DD2;color: #FFF;padding: 16px;margin-top: 20px;margin-right: 20px;text-align: center;font-size: 16px;border-radius: 4px;">Removed All Styles..!</div>';
}

$is_empty = false;

if ( is_array( $prev_styles ) ) {
	foreach ( $prev_styles as $key => $style ) {
		$impressions  = 0;
		$multivariant = false;
		$has_variants = false;
		$style_id     = $style['style_id'];

		if ( isset( $style['multivariant'] ) ) {
			$multivariant = true;
		}

		if ( $variant_tests ) {
			if ( array_key_exists( $style_id, $variant_tests ) && ! empty( $variant_tests[ $style_id ] ) ) {
				$has_variants = true;
			}
		}

		$variants = array();
		$live     = '0';

		if ( $has_variants ) {
			foreach ( $variant_tests[ $style_id ] as $value ) {
				$settings = maybe_unserialize( $value['style_settings'] );
				if ( '1' === $settings['live'] ) {
					$live = '1';
				}
				$variants[] = $value['style_id'];
			}

			foreach ( $variants as $value ) {
				if ( isset( $analytics_data[ $value ] ) ) {
					foreach ( $analytics_data[ $value ] as $value1 ) {
						$impressions = $impressions + $value1['impressions'];
					}
				}
			}
		}

		if ( ! $multivariant ) {
			if ( isset( $analytics_data[ $style_id ] ) ) {
				foreach ( $analytics_data[ $style_id ] as $key1 => $value2 ) {
					$impressions = $impressions + $value2['impressions'];
				}
			}
		}

		$style_settings = maybe_unserialize( $prev_styles[ $key ]['style_settings'] );
		if ( '1' === $style_settings['live'] ) {
			$live = '1';
		}

		if ( $has_variants ) {
			$slideinstatus = $live;
		} else {
			$slideinstatus = $style_settings['live'];
		}

		if ( '2' === $slideinstatus ) {
			$slide_in_status = '1';
		} elseif ( '1' === $slideinstatus ) {
			$slide_in_status = '2';
		} else {
			$slide_in_status = '0';
		}

		$prev_styles[ $key ]['slideinStatus'] = intval( $slideinstatus );
		$prev_styles[ $key ]['status']        = intval( $slide_in_status );
		$prev_styles[ $key ]['impressions']   = $impressions;
	}
	$prev_styles = array_reverse( $prev_styles, true );
}

$limit            = ( isset( $_GET['limit'] ) ) ? intval( $_GET['limit'] ) : 20;
$cont_page        = ( isset( $_GET['cont-page'] ) ) ? intval( $_GET['cont-page'] ) : 1;
$links            = ( isset( $_GET['links'] ) ) ? sanitize_text_field( $_GET['links'] ) : 1;
$slide_in_orderby = ( isset( $_GET['orderby'] ) ) ? sanitize_text_field( $_GET['orderby'] ) : false;
$slide_in_order   = ( isset( $_GET['order'] ) ) ? sanitize_text_field( $_GET['order'] ) : false;
$total            = ( is_array( $prev_styles ) ) ? count( $prev_styles ) : 0;
$maintain_keys    = false;
$search_key       = isset( $_POST['sq'] ) ? sanitize_text_field( $_POST['sq'] ) : '';

if ( isset( $_GET['order'] ) && 'asc' === $_GET['order'] ) {
	$orderlink = 'desc';
} else {
	$orderlink = 'asc';
}

$sorting_style_name_class = 'sorting';
$sorting_list_imp_class   = 'sorting';
$sorting_status_class     = 'sorting';

// define sorting class.
if ( $slide_in_orderby ) {

	switch ( $slide_in_orderby ) {
		case 'style_name':
			$sorting_style_name_class = 'sorting-' . $slide_in_order;
			break;
		case 'impressions':
			$sorting_list_imp_class = 'sorting-' . $slide_in_order;
			break;
		case 'status':
			$sorting_status_class = 'sorting-' . $slide_in_order;
			break;
	}
}

$sq = ( isset( $_GET['sq'] ) && ! empty( $_GET['sq'] ) ) ? sanitize_text_field( $_GET['sq'] ) : $search_key;

if ( isset( $_POST['sq'] ) && '' === $_POST['sq'] ) {
	$sq = '';
}

$search_in_params = array( 'style_name', 'style_id' );

if ( $prev_styles ) {
	$paginator   = new CP_Paginator( $prev_styles );
	$result      = $paginator->get_data( $limit, $cont_page, $slide_in_orderby, $slide_in_order, $sq, $search_in_params, $maintain_keys );
	$prev_styles = $result->data;
}

$slide_in_new_url = add_query_arg(
	array(
		'page'       => 'smile-slide_in-designer',
		'style-view' => 'new',
	),
	admin_url( 'admin.php' )
);

$slide_in_analytics_url = add_query_arg(
	array(
		'page'       => 'smile-slide_in-designer',
		'style-view' => 'analytics',
	),
	admin_url( 'admin.php' )
);

?>
<div class="wrap about-wrap bend cp-slidein-main">
	<div class="wrap-container">
		<div class="bend-heading-section">
			<h1><?php echo esc_html__( 'Slide In Designer', 'smile' ); ?>
			</h1>

			<a href="<?php echo esc_attr( esc_url( $slide_in_new_url ) ); ?>" class="bsf-connect-download-csv" style="margin-right: 25px !important;"><i class="connects-icon-square-plus" style="line-height: 30px;font-size: 22px;"></i>
				<?php esc_html_e( 'Create New Slide In', 'smile' ); ?>
			</a>
			<a href="<?php echo esc_attr( esc_url( $slide_in_analytics_url ) ); ?>"  style="margin-right: 25px !important;" class="bsf-connect-download-csv"><i class="connects-icon-bar-graph-2" style="line-height: 30px;"></i>
				<?php esc_html_e( 'Analytics', 'smile' ); ?>
			</a>
			<a href="#" style="margin-right: 25px !important;" class="bsf-connect-download-csv cp-import-style" data-module="slide_in" data-uploader_title="<?php esc_html_e( 'Upload Your Exported file', 'smile' ); ?>" data-uploader_button_text="<?php esc_html_e( 'Import Style', 'smile' ); ?>" onclick_="jQuery('.cp-import-overlay, .cp-style-importer').fadeIn('fast');"><i class="connects-icon-upload" style="line-height: 30px;font-size: 22px;"></i>
				<?php esc_html_e( 'Import Slide In', 'smile' ); ?>
			</a>
			<?php $search_active_class = ( '' !== $sq ) ? 'bsf-cntlist-top-search-act' : ''; ?>
			<?php if ( 0 !== $total ) { ?>
			<span class="bsf-contact-list-top-search <?php echo esc_attr( $search_active_class ); ?>"><i class="connects-icon-search" style="line-height: 30px;"></i>
				<form method="post" class="bsf-cntlst-top-search">
					<input class="bsf-cntlst-top-search-input" type="search" id="post-search-input" name="sq" placeholder="<?php esc_html_e( 'Search', 'smile' ); ?>" value="<?php echo esc_attr( $sq ); ?>">
					<i class="bsf-cntlst-top-search-submit connects-icon-search"></i>
				</form>
			</span>
			<?php } ?>
			<!-- .bsf-contact-list-top-search -->

			<div class="message"></div>
		</div>
		<!-- bend-heading-section -->

		<div class="bend-content-wrap" style="margin-top: 40px;">
			<hr class="bsf-extensions-lists-separator" style="margin: 22px 0px 30px 0px;">
		</hr>
		<div class="container">
			<?php
			$change_status_nonce   = wp_create_nonce( 'cp-change-style-status' );
			$reset_analytics_nonce = wp_create_nonce( 'cp-reset-analytics' );
			$delete_style_nonce    = wp_create_nonce( 'cp-delete-style' );
			$duplicate_style_nonce = wp_create_nonce( 'cp-duplicate-nonce' );
			?>
			<input type="hidden" id="cp-change-status-nonce" value="<?php echo esc_attr( $change_status_nonce ); ?>" />
			<input type="hidden" id="cp-reset-analytics-nonce" value="<?php echo esc_attr( $reset_analytics_nonce ); ?>" />
			<input type="hidden" id="cp-delete-style-nonce" value="<?php echo esc_attr( $delete_style_nonce ); ?>" />
			<input type="hidden" id="cp-duplicate-nonce" value="<?php echo esc_attr( $duplicate_style_nonce ); ?>" />
			<div id="smile-stored-styles">
				<div id="smile-stored-styles">

					<table class="wp-list-table widefat fixed cp-list-optins cp-slidein-list-optins">
						<?php
						if ( 0 !== $total ) {
							$slide_in_url            = add_query_arg(
								array(
									'page'      => 'smile-slide_in-designer',
									'orderby'   => 'style_name',
									'order'     => $orderlink,
									'sq'        => $search_key,
									'cont-page' => $cont_page,
								),
								admin_url( 'admin.php' )
							);
							$impression_slide_in_url = add_query_arg(
								array(
									'page'      => 'smile-slide_in-designer',
									'orderby'   => 'impressions',
									'order'     => $orderlink,
									'sq'        => $search_key,
									'cont-page' => $cont_page,
								),
								admin_url( 'admin.php' )
							);

							$status_slide_in_url = add_query_arg(
								array(
									'page'      => 'smile-slide_in-designer',
									'orderby'   => 'status',
									'order'     => $orderlink,
									'sq'        => $search_key,
									'cont-page' => $cont_page,
								),
								admin_url( 'admin.php' )
							);

							?>
						<thead>
							<tr>
								<th scope="col" id="style-name" class="manage-column column-style <?php echo esc_attr( $sorting_style_name_class ); ?>">
									<input type="checkbox" name="cp-select-chk" value='' class="cp-select-all"/></th>

									<th scope="col" id="style-name" class="manage-column column-style <?php echo esc_attr( $sorting_style_name_class ); ?>">
										<a href="<?php echo esc_attr( esc_url( $slide_in_url ) ); ?>">
											<span class="connects-icon-ribbon"></span>
											<?php esc_html_e( 'Slide In Name', 'smile' ); ?></a></th>
											<th scope="col" id="impressions" class="manage-column column-impressions <?php echo esc_attr( $sorting_list_imp_class ); ?>">
												<a href="<?php echo esc_attr( esc_url( $impression_slide_in_url ) ); ?>">
													<span class="connects-icon-disc"></span>
													<?php esc_html_e( 'Impressions', 'smile' ); ?></a></th>
													<th scope="col" id="status" class="manage-column column-status <?php echo esc_attr( $sorting_status_class ); ?>"><a href="<?php echo esc_attr( esc_url( $status_slide_in_url ) ); ?>">
														<span class="connects-icon-toggle"></span>
														<?php esc_html_e( 'Status', 'smile' ); ?></a></th>
														<th scope="col" id="actions" class="manage-column column-actions" style="min-width: 300px;"><span class="connects-icon-cog"></span>
															<?php esc_html_e( 'Actions', 'smile' ); ?></th>
														</tr>
													</thead>
													<?php } ?>
													<tbody id="the-list" class="smile-style-data">
														<?php
														$list_count     = 0;
														$slide_in_nonce = wp_create_nonce( 'slide_in_edit' );
														if ( is_array( $prev_styles ) && ! empty( $prev_styles ) ) {
															foreach ( $prev_styles as $key => $style ) {
																$style_name   = $style['style_name'];
																$style_id     = $style['style_id'];
																$impressions  = $style['impressions'];
																$variants     = array();
																$has_variants = false;
																if ( $variant_tests ) {
																	if ( array_key_exists( $style_id, $variant_tests ) && ! empty( $variant_tests[ $style_id ] ) ) {
																		$has_variants = true;
																		foreach ( $variant_tests[ $style_id ] as $value ) {
																			$variants[] = $value['style_id'];
																		}
																	}
																}

																$style_settings = maybe_unserialize( $style['style_settings'] );
																$exp_settings   = array();
																if ( is_array( $style_settings ) ) {
																	foreach ( $style_settings as $style_title => $value ) {
																		if ( ! is_array( $value ) ) {
																			$value = urldecode( $value );

																			if ( is_callable( 'utf8_encode' ) ) {
																				$exp_settings[ $style_title ] = htmlentities( stripslashes( utf8_encode( $value ) ), ENT_QUOTES, 'utf-8' );
																			} else {
																				$exp_settings[ $style_title ] = htmlentities( stripslashes( html_entity_decode( $value, ENT_QUOTES, 'utf-8' ) ), ENT_QUOTES, 'utf-8' );
																			}
																		} else {
																			foreach ( $value as $ex_title => $ex_val ) {
																				$val[ $ex_title ] = $ex_val;
																			}
																			$exp_settings[ $style_title ] = str_replace( '"', '&quot;', $val );
																		}
																	}
																}
																$export                   = $style;
																$export['style_settings'] = $exp_settings;

																$theme           = $style_settings['style'];
																$multivariant    = isset( $style['multivariant'] ) ? true : false;
																$live            = isset( $style['slideinStatus'] ) ? (int) $style['slideinStatus'] : '';
																$is_scheduled    = false;
																$slide_in_status = '';

																if ( $has_variants ) {
																	$variant_slide_in_url = add_query_arg(
																		array(
																			'page'      => 'smile-slide_in-designer',
																			'style-view' => 'variant',
																			'variant-style' => $style_id,
																			'style' => $style_name,
																			'theme' => $theme,
																		),
																		admin_url( 'admin.php' )
																	);

																	$slide_in_status .= '<a href=' . esc_url( $variant_slide_in_url ) . '>';
																} else {
																	$slide_in_status .= '<span class="change-status">';
																}

																if ( 1 === $live ) {
																	$slide_in_status .= '<span data-live="1" class="cp-status cp-main-variant-status"><i class="connects-icon-play"></i><span>' . esc_html__( 'Live', 'smile' ) . '</span></span>';
																} elseif ( 0 === $live ) {
																	$slide_in_status .= '<span data-live="0" class="cp-status cp-main-variant-status"><i class="connects-icon-pause"></i><span>' . esc_html__( 'Pause', 'smile' ) . '</span></span>';
																} else {
																	$schedule_data = maybe_unserialize( $style['style_settings'] );
																	if ( isset( $schedule_data['schedule'] ) ) {
																		$scheduled_array = $schedule_data['schedule'];
																		if ( is_array( $scheduled_array ) ) {
																			$startdate   = gmdate( 'j M Y ', strtotime( $scheduled_array['start'] ) );
																			$enddate     = gmdate( 'j M Y ', strtotime( $scheduled_array['end'] ) );
																			$first       = gmdate( 'j-M-Y (h:i A) ', strtotime( $scheduled_array['start'] ) );
																			$second      = gmdate( 'j-M-Y (h:i A) ', strtotime( $scheduled_array['end'] ) );
																			$style_title = 'Scheduled From ' . $first . ' To ' . $second;
																		}
																	} else {
																		$style_title = '';
																	}

																		$time             = '<span> ( ' . $first . ' to ' . $second . ' )</span>';
																		$slide_in_status .= '<span data-live="2" class="cp-status"><i class="connects-icon-clock"></i><span>' . esc_html__( 'Scheduled', 'smile' ) . $time . '</span></span>';

																}

																if ( $has_variants ) {
																	$slide_in_status .= '</a>';
																}

																if ( ! $has_variants ) {
																	$slide_in_status .= '<ul class="manage-column-menu">';
																	if ( 1 !== $live && '1' !== $live ) {
																		$slide_in_status .= '<li><a href="#" class="change-status" data-style-id="' . $style_id . '" data-live="1" data-option="smile_slide_in_styles"><i class="connects-icon-play"></i><span>' . esc_html__( 'Live', 'smile' ) . '</span></a></li>';
																	}
																	if ( 0 !== $live && '0' !== $live && '' !== $live ) {
																		$slide_in_status .= '<li><a href="#" class="change-status" data-style-id="' . $style_id . '" data-live="0" data-option="smile_slide_in_styles"><i class="connects-icon-pause"></i><span>' . esc_html__( 'Pause', 'smile' ) . '</span></a></li>';
																	}
																	if ( 2 !== $live && '2' !== $live ) {
																		$slide_in_status .= '<li><a href="#" class="change-status" data-style-id="' . $style_id . '" data-live="2" data-option="smile_slide_in_styles" data-schedule="1"><i class="connects-icon-clock"></i><span>' . esc_html__( 'Schedule', 'smile' ) . '</span></a></li>';
																	}
																	$slide_in_status .= '</ul>';
																}
																$slide_in_status .= '</span>';
																?>
																<tr id=<?php echo esc_attr( $key ); ?> class="ui-sortable-handle 
																	<?php
																	if ( $has_variants ) {
																		echo 'cp-variant-exist'; }
																	?>
																		"><?php $list_count++; ?>
																		<td class="column-delete"><input type="checkbox" name="delete_modal" value="<?php echo esc_attr( $style_id ); ?>" /></td>
																		<?php
																		if ( $multivariant || $has_variants ) {
																			$edit_slide_in_url = add_query_arg(
																				array(
																					'page'      => 'smile-slide_in-designer',
																					'style-view' => 'variant',
																					'variant-style' => $style_id,
																					'style' => $style_name,
																					'theme' => $theme,
																					'_wpnonce' => $slide_in_nonce,
																				),
																				admin_url( 'admin.php' )
																			);
																			?>
																		<td class="name column-name"><a href="<?php echo esc_attr( esc_url( $edit_slide_in_url ) ); ?>"  > <?php echo 'Variants of ' . wp_kses_post( $style_name ); ?> </a></td>
																			<?php
																		} else {
																			$edit_slide_in_url = add_query_arg(
																				array(
																					'page'      => 'smile-slide_in-designer',
																					'style-view' => 'edit',
																					'style' => $style_id,
																					'theme' => $theme,
																					'_wpnonce' => $slide_in_nonce,
																				),
																				admin_url( 'admin.php' )
																			);
																			?>
																		<td class="name column-name"><a href="<?php echo esc_attr( esc_url( $edit_slide_in_url ) ); ?>" target ="_blank" > <?php echo esc_attr( urldecode( $style_name ) ); ?> </a></td>
																		<?php } ?>
																		<td class="column-impressions"><?php echo esc_html( $impressions ); ?></td>
																		<td class="column-status"><?php echo wp_kses_post( $slide_in_status ); ?></td>
																		<td class="actions column-actions">
																		<?php
																			$variant_slide_in_url = add_query_arg(
																				array(
																					'page'      => 'smile-slide_in-designer',
																					'style-view' => 'variant',
																					'variant-style' => $style_id,
																					'style' => $style_name,
																					'theme' => $theme,
																				),
																				admin_url( 'admin.php' )
																			);

																		?>
																			<a class="action-list" data-style="<?php echo esc_attr( $style_id ); ?>" data-option="smile_slide_in_styles" href="<?php echo esc_attr( esc_url( $variant_slide_in_url ) ); ?>"><i class="connects-icon-share"></i><span class="action-tooltip">
																				<?php if ( $has_variants ) { ?>
																					<?php esc_html_e( 'See Variants', 'smile' ); ?>
																				<?php } else { ?>
																					<?php esc_html_e( 'Create Variant', 'smile' ); ?>
																				<?php } ?>
																			</span></a>
																			<?php if ( ! $has_variants ) { ?>
																			<a class="action-list copy-style-icon" data-style="<?php echo esc_attr( $style_id ); ?>" data-module="slide_in" data-option="smile_slide_in_styles" style="margin-left: 25px;" href="#"><i class="connects-icon-paper-stack" style="font-size: 20px;"></i><span class="action-tooltip">
																				<?php esc_html_e( 'Duplicate Slide In', 'smile' ); ?>
																			</span></a>
																			<?php } ?>
																			<?php
																			if ( $has_variants ) {
																				$style_for_analytics = implode( '||', $variants );
																				if ( ! $multivariant ) {
																					$style_for_analytics .= '||' . $style_id;
																				}
																				$style_arr = explode( '||', $style_for_analytics );
																				if ( count( $style_arr ) > 1 ) {
																					$comp_factor = 'imp';
																				} else {
																					$comp_factor = 'impVsconv';
																				}
																			} else {
																				$style_for_analytics = $style_id;
																				$comp_factor         = 'impVsconv';
																			}
																			$slide_in_analytics_graph_url = add_query_arg(
																				array(
																					'page'      => 'smile-slide_in-designer',
																					'style-view'   => 'analytics',
																					'compFactor'     => $comp_factor,
																					'style'        => $style_for_analytics,
																				),
																				admin_url( 'admin.php' )
																			);

																			?>
																			<a class="action-list" data-style="<?php echo esc_attr( $style_id ); ?>" data-option="smile_slide_in_styles" style="margin-left: 25px;" href="<?php echo esc_attr( esc_url( $slide_in_analytics_graph_url ) ); ?>"><i class="connects-icon-bar-graph-2"></i><span class="action-tooltip">
																				<?php esc_html_e( 'View Analytics', 'smile' ); ?>
																			</span></a>
																			<?php
																			$export_slidein_nonce = wp_create_nonce( 'export-slidein-' . $style_id );
																			$form_action          = admin_url( 'admin-post.php?action=cp_export_slidein&style_id=' . $style_id . '&style_name=' . urldecode( $style_name ) . '&_wpnonce=' . $export_slidein_nonce );

																			?>

																			<form method="post" class="cp-export-contact" action="<?php echo esc_url( $form_action ); ?>">
																				<input type="hidden" id="cp-export_slidein_nonce" value="<?php echo esc_attr( $export_slidein_nonce ); ?>" />
																				<input type="hidden" name="style_id" value="<?php echo esc_attr( $style_id ); ?>" />
																				<input type="hidden" name="style_name" value="<?php echo esc_attr( $style_name ); ?>" />
																				<a class="action-list cp-download-slidein" href="#" target="_top" style="margin-left: 25px;" ><i  class="connects-icon-download"></i><span class="action-tooltip"><?php esc_html_e( 'Export Settings', 'smile' ); ?></span></a>
																			</form>

																			<?php
																			if ( ! $multivariant && ! $has_variants ) {
																				echo wp_kses_post( apply_filters( 'cp_before_delete_action', $style_settings, 'slide_in' ) );
																			}
																			?>
																			<a class="action-list trash-style-icon" data-delete="hard" data-variantoption="slide_in_variant_tests" data-style="<?php echo esc_attr( $style_id ); ?>" data-option="smile_slide_in_styles" style="margin-left: 25px;" href="#"><i class="connects-icon-trash"></i><span class="action-tooltip">
																				<?php esc_html_e( 'Delete Slide In', 'smile' ); ?>
																			</span></a>
																		</td>
																	</tr>
																	<?php

															}
														} else {
															?>
															<tr>
																<?php
																if ( isset( $_GET['sq'] ) && '' !== $_GET['sq'] && 0 !== $total ) {
																	$is_empty          = true;
																	$slide_in_page_url = add_query_arg(
																		array(
																			'page'      => 'smile-slide_in-designer',
																		),
																		admin_url( 'admin.php' )
																	);
																	?>
																	<th scope="col" colspan="4" class="manage-column cp-list-empty"><?php echo esc_html__( 'No results available.', 'smile' ); ?><a class="add-new-h2" href="<?php echo esc_attr( esc_url( $slide_in_page_url ) ); ?>" title="<?php esc_html_e( 'Back to Slide In list', 'smile' ); ?>">
																		<?php esc_html_e( 'Back to Slide In list', 'smile' ); ?>
																		</a>
																	</th>
																	<?php
																} else {
																	if ( 0 === $total ) {
																		?>
																		<th scope="col" colspan="4" class="manage-column cp-list-empty cp-empty-graphic"><?php echo esc_html__( 'First time being here?', 'smile' ); ?><br> <a class="add-new-h2" href="<?php echo esc_attr( esc_url( $slide_in_new_url ) ); ?>" title="<?php esc_html_e( 'Create New Slide In', 'smile' ); ?>">
																			<?php esc_html_e( "Awesome! Let's start with your first Slide In", 'smile' ); ?>
																		</a>
																	</th>
																		<?php
																	}
																}
																?>
														</tr>
															<?php
														}
														?>
												</tbody>
											</table>

											<!-- Pagination & Search -->
											<div class="row">
												<div class="container" style="max-width:100% !important;width:100% !important;">
													<div class="col-md-5 col-sm-10">
														<?php if ( 0 !== $total ) { ?>
														<a class="button-primary action-tooltip disabled cp-delete-multiple-modal-style" href="#" title="" data-delete="hard" data-module='Slide In' data-option="smile_slide_in_styles" data-id = "" data-variantoption = "slide_in_variant_tests" >
															<?php esc_html_e( 'Delete Selected Slide In', 'smile' ); ?>
														</a>   
														<?php }; ?>
													</div><!-- .col-sm-6 -->
													<div class="col-md-5 col-sm-6">
														<?php
														$flag = true;
														if ( isset( $_GET['sq'] ) && '' !== $_GET['sq'] && $list_count < $limit ) {
															$flag = false;
														}
														if ( $total > $limit && ! $is_empty && $flag ) {
															$base_page_link = '?page=smile-slide_in-designer';
															echo $paginator->create_links( $links, 'pagination bsf-cnt-pagi', '', $sq, $base_page_link ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
														}
														?>
													</div><!-- .col-sm-6 -->
												</div><!-- .container -->
											</div><!-- .row -->

										</div>
										<!-- #smile-stored-styles -->
									</div>
									<!-- .container -->

									<!-- Pagination & Search -->
									<div class="row">
										<div class="container" style="max-width:100% !important;width:100% !important;">
											<div class="col-sm-6">
												<?php if ( $total > $limit ) { ?>
												<p class="search-box">
													<form method="post" class="bsf-cntlst-search">
														<label class="screen-reader-text" for="post-search-input"><?php esc_html_e( 'Search Contacts', 'smile' ); ?>:</label>
														<input type="search" id="post-search-input" name="sq" value="<?php echo esc_attr( $sq ); ?>">
														<input type="submit" id="search-submit" class="button" value="<?php echo esc_html_e( 'Search', 'smile' ); ?>">
													</form>
												</p>
												<?php } ?>
											</div><!-- .col-sm-6 -->
											<div class="col-sm-6">

											</div><!-- .col-sm-6 -->
										</div><!-- .container -->
									</div><!-- .row -->

								</div>
								<!-- .bend-content-wrap -->
							</div>
							<!-- .wrap-container -->
							<?php
							$timezone      = '';
							$timezone_name = $cp_settings['cp-timezone'];
							$timezone      = 'WordPress';
							if ( 'system' === $timezone_name ) {
								$timezone = 'system';
							}

							$date = current_time( 'm/d/Y h:i A' );
							echo ' <input type="hidden" id="cp_timezone_name" class="form-control cp_timezone" value="' . esc_attr( $timezone ) . '" />';
							echo ' <input type="hidden" id="cp_currenttime" class="form-control cp_currenttime" value="' . esc_attr( $date ) . '" />';

							?>
							<!-- scheduler popup -->
							<div class="cp-schedular-overlay">
								<div class="cp-scheduler-popup">
									<div class="cp-scheduler-close"> <span class="connects-icon-cross"></span> </div>
									<div class="cp-row">
										<div class="schedular-title">
											<h3>
												<?php esc_html_e( 'Schedule This Slide In', 'smile' ); ?>
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
															<input type="text" id="cp_start_time" class="form-control cp_start" value="" />
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
																<input type="text" id="cp_end_time" class="form-control cp_end" value=" "/>
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
															<?php esc_html_e( 'Schedule Slide In', 'smile' ); ?>
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
								<!-- .wrap -->
							<!--  cp style import -->
							<div class="cp-import-overlay"></div>
							<div class="cp-style-importer">
								<div class="cp-importer-close"> <span class="connects-icon-cross"></span> </div>
								<div class="cp-import-container">
									<div class="cp-import-slidein">
										<div class="cp-row">
											<div class="cp-slidein-heading">
												<h3><?php esc_html_e( 'Import Slide In', 'smile' ); ?></h3>
											</div>
										</div>
										<div class="cp-row">
											<div class="cp-import-input">
												<input type="file" id="cp-import" />
												<button class="button button-primary"><?php esc_html_e( 'Import', 'smile' ); ?></button>
											</div>
										</div>
									</div>
								</div>
							</div>
