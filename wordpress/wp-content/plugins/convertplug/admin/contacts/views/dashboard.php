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

$total              = 0;
$limit              = ( isset( $_GET['limit'] ) ) ? intval( $_GET['limit'] ) : 10;
$campaign_page      = ( isset( $_GET['cont-page'] ) ) ? intval( $_GET['cont-page'] ) : 1;
$links              = ( isset( $_GET['links'] ) ) ? esc_attr( $_GET['links'] ) : 1;
$dashboard_orderby  = ( isset( $_GET['orderby'] ) ) ? sanitize_text_field( $_GET['orderby'] ) : 'date';
$dashboard_order    = ( isset( $_GET['order'] ) ) ? sanitize_text_field( $_GET['order'] ) : 'desc';
$maintain_keys      = true;
$smile_lists        = get_option( 'smile_lists' );
$uninstalled_addons = array();
// to unset deactivated / inactive mailer addons.
if ( is_array( $smile_lists ) ) {
	foreach ( $smile_lists as $key => $list ) {
		$provider = $list['list-provider'];
		if ( 'Convert Plug' !== $provider ) {
			if ( ! isset( Smile_Framework::$addon_list[ $provider ] ) && ! isset( Smile_Framework::$addon_list[ strtolower( $provider ) ] ) ) {

				$uninstalled_addons[] = $provider;
				unset( $smile_lists[ $key ] );
			}
		}
	}
}


// push contact count to smile_lists array.
$all_contacts = array();
if ( is_array( $smile_lists ) ) {
	foreach ( $smile_lists as $key => $list ) {
		$temp_contact             = array();
		$provider                 = $list['list-provider'];
		$temp_contact['provider'] = $list['list-provider'];
		$list_name                = str_replace( ' ', '_', strtolower( trim( $list['list-name'] ) ) );
		$temp_contact['listName'] = $list_name;
		$list_id                  = isset( $list['list'] ) ? $list['list'] : '';
		$temp_contact['list_id']  = $list_id;
		$mailer                   = str_replace( ' ', '_', strtolower( trim( $provider ) ) );
		$temp_contact['mailer']   = $mailer;

		if ( 'convert_plug' !== $mailer ) {
			$list_option = 'cp_' . $mailer . '_' . $list_name;
		} else {
			$list_option = 'cp_connects_' . $list_name;
		}
		$list_contacts = get_option( $list_option );

		$temp_contact['contacts'] = $list_contacts;
		array_push( $all_contacts, $temp_contact );

		$contacts                        = ! empty( $list_contacts ) ? count( $list_contacts ) : 0;
		$smile_lists[ $key ]['contacts'] = $contacts;
	}
}
if ( is_array( $smile_lists ) ) {
	$total = count( $smile_lists );
}
require_once CP_BASE_DIR . '/admin/contacts/views/class-cp-paginator.php';

// redirect to first page for search results.
if ( isset( $_POST['sq'] ) ) {
	$search_key      = esc_attr( $_POST['sq'] );
	$redirect_string = '?page=contact-manager&limit=' . $limit . '&sq=' . $search_key . '&cont-page=1';
	echo "<script> window.location.href= '$redirect_string'; </script>"; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
} else {
	$search_key = '';
}

if ( isset( $_GET['order'] ) && 'asc' === sanitize_text_field( $_GET['order'] ) ) {
	$orderlink = 'order=desc';
} else {
	$orderlink = 'order=asc';
}

$sorting_list_class      = 'sorting';
$sorting_list_name_class = 'sorting';
$sorting_provider_class  = 'sorting';
$sorting_contacts_class  = 'sorting';

// define sorting class.
if ( isset( $_GET['orderby'] ) ) {

	$dashboard_order   = isset( $_GET['order'] ) ? sanitize_text_field( $_GET['order'] ) : '';
	$dashboard_orderby = isset( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : '';

	switch ( $dashboard_orderby ) {
		case 'list':
			$sorting_list_class = 'sorting-' . $dashboard_order;
			break;
		case 'list-name':
			$sorting_list_name_class = 'sorting-' . $dashboard_order;
			break;
		case 'list-provider':
			$sorting_provider_class = 'sorting-' . $dashboard_order;
			break;
		case 'contacts':
			$sorting_contacts_class = 'sorting-' . $dashboard_order;
			break;
	}
}

if ( isset( $_GET['sq'] ) && ! empty( $_GET['sq'] ) ) {
	$sq = sanitize_text_field( $_GET['sq'] );
} else {
	$sq = $search_key;
}

if ( isset( $_POST['sq'] ) && '' === sanitize_text_field( $_POST['sq'] ) ) {
	$sq = '';
}

// define parameters for search.
$search_in_params = array( 'list-name', 'list-provider', 'provider_list' );

if ( $smile_lists && isset( $orderby ) && isset( $order ) && isset( $page ) ) {
	$paginator   = new CP_Paginator( $smile_lists );
	$result      = $paginator->get_data( $limit, $page, $orderby, $order, $sq, $search_in_params, $maintain_keys );
	$smile_lists = $result->data;
}

$export_all_list_nonce = wp_create_nonce( 'export-all-list' );

$list_ids_arr       = wp_json_encode( $all_contacts );
$form_export_action = admin_url( 'admin-post.php?action=cp_export_all_list&_wpnonce=' . $export_all_list_nonce );
?>

<div class="wrap about-wrap bsf-connect bsf-connect-campaign bend">
	<div class="wrap-container">

		<div class="bend-heading-section bsf-connect-header">
		<?php
		$new_list_url  = add_query_arg(
			array(
				'page' => 'contact-manager',
				'view' => 'new-list',
				'step' => '1',
			),
			admin_url( 'admin.php' )
		);
		$analytics_url = add_query_arg(
			array(
				'page' => 'contact-manager',
				'view' => 'analytics',
			),
			admin_url( 'admin.php' )
		);
		?>
			<h1> <?php echo esc_html__( 'Connects', 'smile' ); ?> <a class="add-new-h2" href="<?php echo esc_attr( esc_url( $new_list_url ) ); ?>" title="<?php esc_html_e( 'Create new campaign', 'smile' ); ?>"><?php esc_html_e( 'Create New Campaign', 'smile' ); ?></a> </h1>
			<h3 style="margin-bottom: 30px;"><?php esc_html_e( 'Connects is a tool to capture, sync, manage & analyze your contacts all in one place. Create campaigns & integrate them with your favorite CRM software. It comes with built-in analytics as well.', 'smile' ); ?></h3>
			<a href="<?php echo esc_attr( esc_url( $new_list_url ) ); ?>"  style="margin-right: 25px !important;" class="bsf-connect-download-csv"><i class="connects-icon-square-plus" style="line-height: 30px;font-size: 22px;"></i>
				<?php esc_html_e( 'Create New Campaign', 'smile' ); ?>
			</a>
			<a href="<?php echo esc_attr( esc_url( $analytics_url ) ); ?>"  style="margin-right: 25px !important;" class="bsf-connect-download-csv"><i class="connects-icon-bar-graph-2" style="line-height: 30px;"></i>
				<?php esc_html_e( 'Analytics', 'smile' ); ?>
			</a>
			<form method="post" class="cp-export-contact" action="<?php echo esc_url( $form_export_action ); ?>">
				<input type="hidden" name="list_id" value='<?php echo esc_attr( $list_ids_arr ); ?>' />
				<a class="action-list action-download-contact bsf-connect-download-csv" href="#" target="_top" style="margin-right: 25px !important;"><i style="line-height: 30px;" class="connects-icon-download"></i><span class="action-tooltip"><?php esc_html_e( 'Export All contacts', 'smile' ); ?></span></a>
			</form>
			<?php $search_active_class = ( '' !== $sq ) ? 'bsf-cntlist-top-search-act' : ''; ?>
			<span class="bsf-contact-list-top-search <?php echo esc_attr( $search_active_class ); ?>"><i class="connects-icon-search" style="line-height: 30px;"></i>
				<form method="post" class="bsf-cntlst-top-search">
					<input class="bsf-cntlst-top-search-input" type="search" id="post-search-input" name="sq" placeholder="<?php esc_html_e( 'Search', 'smile' ); ?>" value="<?php echo esc_attr( $sq ); ?>">
					<i class="bsf-cntlst-top-search-submit connects-icon-search"></i>
				</form>
			</span><!-- .bsf-contact-list-top-search -->
			<div class="bend-head-logo">
				<div class="bend-product-ver">
					<?php esc_html_e( 'Connects', 'smile' ); ?>
				</div>
			</div>
		</div><!-- bend-heading section -->


		<div class="bend-content-wrap" style="margin-top: 30px;">
			<hr class="bsf-extensions-lists-separator" style="margin: 22px 0px 30px 0px;"></hr>
			<div class="container bsf-connect-content">
				<?php
				$campaign_service       = add_query_arg(
					array(
						'page'      => 'contact-manager',
						'orderby'   => 'list-provider',
						'order'     => $orderlink,
						'sq'        => $search_key,
						'cont-page' => $campaign_page,
					),
					admin_url( 'admin.php' )
				);
					$campaign_list_name = add_query_arg(
						array(
							'page'      => 'contact-manager',
							'orderby'   => 'list-name',
							'order'     => $orderlink,
							'sq'        => $search_key,
							'cont-page' => $campaign_page,
						),
						admin_url( 'admin.php' )
					);
					$campaign_list      = add_query_arg(
						array(
							'page'      => 'contact-manager',
							'orderby'   => 'list',
							'order'     => $orderlink,
							'sq'        => $search_key,
							'cont-page' => $campaign_page,
						),
						admin_url( 'admin.php' )
					);

					$campaign_contacts = add_query_arg(
						array(
							'page'      => 'contact-manager',
							'orderby'   => 'contacts',
							'order'     => $orderlink,
							'sq'        => $search_key,
							'cont-page' => $campaign_page,
						),
						admin_url( 'admin.php' )
					);
					?>

				<table class="wp-list-table widefat fixed bsf-connect-optins bsf-connect-optins-campaign">
					<thead>
						<tr>
							<th scope="col" id="provider" class="manage-column column-provider <?php echo esc_attr( $sorting_provider_class ); ?>">
								<a href="<?php echo esc_attr( esc_url( $campaign_service ) ); ?>">
									<span class="connects-icon-share"></span> <?php esc_html_e( 'Service', 'smile' ); ?>
								</a>
							</th>
							<th scope="col" id="list-id" class="manage-column column-id <?php echo esc_attr( $sorting_list_name_class ); ?>">
								<a href="<?php echo esc_attr( esc_url( $campaign_list_name ) ); ?>">
									<span class="connects-icon-bar-graph-2"></span> <?php esc_html_e( 'Campaign', 'smile' ); ?>
								</a>
							</th>
							<th scope="col" class="manage-column column-provider <?php echo esc_attr( $sorting_list_class ); ?>">
								<a href="<?php echo esc_attr( esc_url( $campaign_list ) ); ?>">
									<span class="connects-icon-align-justify"></span> <?php esc_html_e( 'List', 'smile' ); ?>
								</a>
							</th>
							<th scope="col" id="contacts" class="manage-column column-contacts <?php echo esc_attr( $sorting_contacts_class ); ?>">
								<a href="<?php echo esc_attr( esc_url( $campaign_contacts ) ); ?>">
									<span class="connects-icon-head"></span> <?php esc_html_e( 'Contacts', 'smile' ); ?>
								</a>
							</th>
							<th scope="col" id="actions" class="manage-column column-actions sorting"><span class="connects-icon-cog"></span> <?php esc_html_e( 'Actions', 'smile' ); ?></th>
						</tr>
					</thead>
					<tbody id="the-list" class="smile-style-data">
						<?php

						if ( ! empty( $smile_lists ) ) {
							foreach ( $smile_lists as $key => $list ) {
								$provider           = $list['list-provider'];
								$list_name          = $list['list-name'];
								$list_id            = $list['list'];
								$mailer             = str_replace( ' ', '_', strtolower( trim( $provider ) ) );
								$contacts           = $list['contacts'];
								$provider_list_name = $list['provider_list'];
								$date               = gmdate( 'j M Y', strtotime( $list['date'] ) );
								$campaign_date      = gmdate( 'j M Y', strtotime( $list['date'] ) );
								$onclick            = '';
								if ( 0 === $contacts ) {
									$onclick = ' onclick="alert(\'' . __( 'Contact list is empty.', 'smile' ) . '\'); return false;" ';
								}
								if ( 'Convert Plug' === $provider ) {
									$provider_list_name = 'Default';
									$provider_name      = CP_PLUS_NAME;
								} else {
									$provider_name = Smile_Framework::$addon_list[ strtolower( $provider ) ]['name'];
								}
								?>
								<tr>
									<td scope="col" class="manage-column column-provider <?php echo esc_attr( str_replace( ' ', '-', strtolower( $provider ) ) ); ?>"><span>
										<?php if ( 0 < $contacts ) { ?>
											<?php
											$contact_list = add_query_arg(
												array(
													'page' => 'contact-manager',
													'view' => 'contacts',
													'list' => $key,
												),
												admin_url( 'admin.php' )
											);
											?>
										<a href="<?php echo esc_url( $contact_list ); ?>"><?php echo esc_attr( $provider_name ); ?></a>
											<?php
										} else {
											echo esc_attr( $provider_name );
										}
										?>
								</span>
							</td>
							<td scope="col" class="manage-column column-id">
								<?php if ( $contacts > 0 ) { ?>
									<?php
										$contact_list = add_query_arg(
											array(
												'page' => 'contact-manager',
												'view' => 'contacts',
												'list' => $key,
											),
											admin_url( 'admin.php' )
										);
									?>
								<a title="Created on <?php echo esc_attr( $campaign_date ); ?>" href="<?php echo esc_attr( esc_url( $contact_list ) ); ?>"><?php echo esc_attr( $list_name ); ?></a>
									<?php
								} else {
									echo esc_attr( $list_name );
								}
								?>
						</td>
						<td scope="col" class="manage-column column-list">
								<?php
								if ( isset( Smile_Framework::$addon_list[ strtolower( $provider ) ]['mailer_type'] ) ) {
									if ( 'multiple' === Smile_Framework::$addon_list[ strtolower( $provider ) ]['mailer_type'] ) {
										$str = array();
										if ( 0 < count( $provider_list_name ) && is_array( $provider_list_name ) ) {

											foreach ( $provider_list_name as $list_names ) {
												$str[] = $list_names;
											}

											$first_tag = array_shift( $provider_list_name );
											$tooltip   = implode( ', ', $provider_list_name );

											$tooltip_html = '<span data-position="top" class="cp-tooltip-icon has-tip" title="' . $tooltip . '"><a style="cursor: help;" href="javascript:void(0);">' . count( $provider_list_name ) . ' More</a></span>';

											$first_tag = ( '-1' !== $first_tag ) ? $first_tag : 'No tags associated with this campaign.';

											if ( 'infusionsoft' === $provider ) {
												$first_tag = ( '' !== $first_tag ) ? $first_tag : 'No tags associated with this campaign.';
											}

											$first_tag = ( '' !== $first_tag ) ? $first_tag : 'No tags associated with this campaign.';

											echo ( 1 < count( $str ) ) ? $first_tag . ' & ' . $tooltip_html : $first_tag;//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

										} else {
											if ( is_array( $provider_list_name ) ) {
												echo esc_html( 'No list.' );
											} else {
												echo esc_attr( $provider_list_name );
											}
										}
									}
								} else {
									if ( 'ontraport' === $provider ) {
										echo ( '-1' !== $list['list'] ) ? esc_attr( $provider_list_name ) : 'No tags associated with this campaign.';
									} elseif ( 'zapier' === $provider ) {
										echo esc_html( 'No list associated with this campaign.' );
									} else {
										echo esc_attr( $provider_list_name );
									}
								}
								?>
						</td>
						<td scope="col" class="manage-column column-contacts"><?php echo esc_attr( $contacts ); ?></td>
						<td class="actions column-actions" style="vertical-align: inherit;">

								<?php
								$form_action = '';
								if ( 0 < $contacts ) {
									$export_list_nonce = wp_create_nonce( 'export-list-' . $key );

									$form_action = admin_url( 'admin-post.php?action=cp_export_list&list_id=' . $key . '&_wpnonce=' . $export_list_nonce );
								}

								$delete_list_nonce = wp_create_nonce( 'cp-delete-list' );
								$analytics_url     = add_query_arg(
									array(
										'page'     => 'contact-manager',
										'view'     => 'analytics',
										'campaign' => $key,
									),
									admin_url( 'admin.php' )
								);

								?>
							<form method="post" class="cp-export-contact" action="<?php echo esc_url( $form_action ); ?>">
								<input type="hidden" name="list_id" value="<?php echo esc_attr( $key ); ?>" />
								<a class="action-list action-download-contact" href="#"<?php echo esc_attr( $onclick ); ?> target="_top" data-list-id="<?php echo esc_attr( $key ); ?>"><i style="font-size: 17px;top: -1px;position: relative;" class="connects-icon-download"></i><span class="action-tooltip"><?php esc_html_e( 'Export', 'smile' ); ?></span></a>
							</form>
								<input type="hidden" name="list_id" value="<?php echo esc_attr( $key ); ?>" />
							<a class="action-list list-analytics" style="margin-left: 6px;" data-list-id="<?php echo esc_attr( $key ); ?>"<?php echo esc_attr( $onclick ); ?> href="<?php echo esc_attr( esc_url( $analytics_url ) ); ?>"><i class="connects-icon-bar-graph-2"></i><span class="action-tooltip"><?php esc_html_e( 'Analytics', 'smile' ); ?></span></a>
							<a class="action-list delete-list" style="margin-left: 6px;" data-list-id="<?php echo esc_attr( $key ); ?>" data-list-mailer="<?php echo esc_attr( $mailer ); ?>" href="#"><i class="connects-icon-trash"></i><span class="action-tooltip"><?php esc_html_e( 'Delete', 'smile' ); ?></span></a>
						</td>
					</tr>
								<?php
							}
						} else {
							?>
							<tr>
								<?php
								if ( isset( $_GET['sq'] ) && '' !== $_GET['sq'] ) {

									?>
									<?php
										$new_list_url = add_query_arg(
											array(
												'page' => 'contact-manager',
												'view' => 'new-list',
												'step' => '1',
											),
											admin_url( 'admin.php' )
										);
									$back_campaign    = add_query_arg(
										array(
											'page' => 'contact-manager',
										),
										admin_url( 'admin.php' )
									);
									?>
					<th scope="col" class="manage-column bsf-connect-column-empty" colspan="5"><?php esc_html_e( 'No results available. ', 'smile' ); ?><a class="add-new-h2" style="position:relative;top:-2px;" href="<?php echo esc_attr( esc_url( $back_campaign ) ); ?>" title="<?php esc_html_e( 'back to campaign list', 'smile' ); ?>"><?php esc_html_e( 'back to campaign list', 'smile' ); ?></a></th>
					<?php } else { ?>
					<th scope="col" class="manage-column bsf-connect-column-empty cp-empty-graphic" colspan="5"><?php esc_html_e( 'First time being here?', 'smile' ); ?> <br><a class="add-new-h2" href="<?php echo esc_attr( esc_url( $new_list_url ) ); ?>" title="<?php esc_html_e( 'Create new campaign', 'smile' ); ?>"><?php esc_html_e( "Awesome! Let's start with your first campaign", 'smile' ); ?></a></th>
					<?php } ?>
				</tr>
							<?php
						}
						?>
		</tbody>
	</table>

	<!-- Start Pagination -->
	<div class="row">

			<div class="container" style="max-width:100% !important;width:100% !important;margin-top: 41px !important;">
			<div class="col-sm-6">
				<?php
				$analytics_url = add_query_arg(
					array(
						'page' => 'contact-manager',
						'view' => 'analytics',
					),
					admin_url( 'admin.php' )
				);

				$new_list_url = esc_url(
					add_query_arg(
						array(
							'page' => 'contact-manager',
							'view' => 'new-list',
							'step' => '1',
						),
						admin_url( 'admin.php' )
					)
				);

				?>
					<a class="button-primary bsf-connect-add-contact-list" href="<?php echo esc_attr( $new_list_url ); ?>" title="<?php esc_html_e( 'Create new list', 'smile' ); ?>"><?php esc_html_e( 'Create New Campaign', 'smile' ); ?></a>
				<a class="button-primary bsf-connect-campaign-analytics" href="<?php echo esc_attr( esc_url( $analytics_url ) ); ?>" title="<?php esc_html_e( 'Analytics', 'smile' ); ?>"><?php esc_html_e( 'Analytics', 'smile' ); ?></a>
			</div><!-- col-sm-6 -->
			<div class="col-sm-6">
				<?php
				if ( $total > $limit && isset( $paginator ) ) {
					$base_page_link = '?page=contact-manager';
					echo $paginator->create_links( $links, 'pagination bsf-cnt-pagi', '', $sq, $base_page_link ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
			</div><!-- col-sm-6 -->
		</div><!-- container -->
	</div><!-- row -->
	<!-- End Pagination -->
</div>
<!-- bsf-connect-content -->

<!-- Start Search -->
<div class="row">
	<div class="container" style="max-width:100% !important;width:100% !important;margin-top: 41px !important;">
		<div class="col-sm-6">
			<?php if ( $total > $limit ) { ?>
			<p class="search-box">
				<form method="post" class="bsf-cntlst-search">
					<label class="screen-reader-text" for="post-search-input"><?php esc_html_e( 'Search Contacts:', 'smile' ); ?></label>
					<input type="search" id="post-search-input" name="sq" value="<?php echo esc_attr( $sq ); ?>">
					<input type="submit" id="search-submit" class="button" value="Search">
				</form>
			</p>
			<?php } ?>
		</div><!-- .col-sm-6 -->
		<div class="col-sm-6">

		</div><!-- col-sm-6 -->
	</div><!-- container -->
</div><!-- row -->
<!-- End Search -->

</div>
<!-- bend-content-wrap -->
</div>
<!-- wrap-container -->
</div>
<!-- bend -->

