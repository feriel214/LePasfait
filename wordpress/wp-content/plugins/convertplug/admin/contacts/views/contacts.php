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

$smile_lists = get_option( 'smile_lists' );
$provider    = '';
$list_name   = '';
$list_id     = intval( $_GET['list'] );
if ( $smile_lists ) {
	if ( isset( $smile_lists[ $list_id ] ) ) {
		$list      = $smile_lists[ $list_id ];
		$list_name = $list['list-name'];
		$provider  = $list['list-provider'];
	}
}

$total_contacts = 0;

$contact_id = isset( $list['list'] ) ? $list['list'] : '';

$mailer    = str_replace( ' ', '_', strtolower( trim( $provider ) ) );
$list_name = str_replace( ' ', '_', strtolower( trim( $list_name ) ) );
if ( 'convert_plug' !== $mailer ) {
	$list_option = 'cp_' . $mailer . '_' . $list_name;
	$contacts    = get_option( $list_option );
} else {
	$list_option = 'cp_connects_' . $list_name;
	$contacts    = get_option( $list_option );
}

if ( $contacts ) {
	$total_contacts = count( $contacts );
}

require_once CP_BASE_DIR . '/admin/contacts/views/class-cp-paginator.php';

$limit             = ( isset( $_GET['limit'] ) ) ? intval( $_GET['limit'] ) : 10;
$campaign_page     = ( isset( $_GET['cont-page'] ) ) ? intval( $_GET['cont-page'] ) : 1;
$links             = ( isset( $_GET['links'] ) ) ? esc_attr( $_GET['links'] ) : 1;
$dashboard_orderby = ( isset( $_GET['orderby'] ) ) ? sanitize_text_field( $_GET['orderby'] ) : 'name';
$dashboard_order   = ( isset( $_GET['order'] ) ) ? sanitize_text_field( $_GET['order'] ) : 'asc';
$list_id           = intval( $_GET['list'] );
$maintain_keys     = false;

if ( isset( $_POST['sq'] ) ) {
	$search_key      = sanitize_text_field( $_POST['sq'] );
	$redirect_string = '?page=contact-manager&view=contacts&list=' . $list_id . '&limit=' . $limit . '&sq=' . $search_key . '&cont-page=1';
	echo "<script> window.location.href= '$redirect_string'; </script>"; //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
} else {
	$search_key = '';
}

if ( isset( $_GET['order'] ) && 'asc' === sanitize_text_field( $_GET['order'] ) ) {
	$orderlink = 'order=desc';
} else {
	$orderlink = 'order=asc';
}

$sorting_name_class  = 'sorting';
$sorting_email_class = 'sorting';
$sorting_date_class  = 'sorting';

if ( isset( $_GET['orderby'] ) ) {
	switch ( $_GET['orderby'] ) {
		case 'name':
			$sorting_name_class = 'sorting-' . esc_attr( $_GET['order'] );
			break;
		case 'email':
			$sorting_email_class = 'sorting-' . esc_attr( $_GET['order'] );
			break;
		case 'date':
			$sorting_date_class = 'sorting-' . esc_attr( $_GET['order'] );
			break;
	}
}

if ( isset( $_POST['sq'] ) && '' !== $_POST['sq'] ) {
	$search_key = sanitize_text_field( $_POST['sq'] );
} else {
	$search_key = '';
}

if ( isset( $_GET['sq'] ) && ! empty( $_GET['sq'] ) ) {
	$sq = sanitize_text_field( $_GET['sq'] );
} else {
	$sq = $search_key;
}

if ( isset( $_POST['sq'] ) && '' === $_POST['sq'] ) {
	$sq = '';
}


$search_in_params = array( 'name', 'email' );
if ( $contacts ) {

	$paginator = new CP_Paginator( $contacts );
	$result    = $paginator->get_data( $limit, $campaign_page, $dashboard_orderby, $dashboard_order, $sq, $search_in_params, $maintain_keys );

	$contacts = $result->data;
}

?>

<div class="wrap about-wrap bsf-connect bsf-connect-list bend">
	<div class="wrap-container">

		<div class="bend-heading-section bsf-connect-header bsf-connect-list-header 
		<?php
		if ( empty( $contacts ) ) {
			echo 'bsf-connect-empty-header'; }
		?>
			">
			<?php
			$contact_url = add_query_arg(
				array(
					'page' => 'contact-manager',
				),
				admin_url( 'admin.php' )
			);
			?>
			<h1><span class="cp-strip-text" style="max-width: 460px;top: 10px;" title="<?php echo esc_attr( $list_name ); ?>"><?php echo esc_attr( $list_name ); ?></span> <a class="add-new-h2" href="<?php echo esc_attr( esc_url( $contact_url ) ); ?>"><?php esc_html_e( 'Back to Campaigns List', 'smile' ); ?></a></h1>
			<?php if ( 0 < $total_contacts ) { ?>
				<?php
				$export_list_nonce = wp_create_nonce( 'export-list-' . $list_id );

				$form_action = admin_url( 'admin-post.php?action=cp_export_list&list_id=' . $list_id . '&_wpnonce=' . $export_list_nonce );

				?>
			<form method="post" class="cp-export-contact" action="<?php echo esc_url( $form_action ); ?>">
				<input type="hidden" name="list_id" value="<?php echo esc_attr( $list_id ); ?>" />
				<a class="action-list action-download-contact bsf-connect-download-csv" href="#" target="_top" style="margin-right: 25px !important;"><i style="line-height: 30px;" class="connects-icon-download"></i><span class="action-tooltip"><?php esc_html_e( 'Export CSV', 'smile' ); ?></span></a>
			</form>  
				<?php
				$list_id_url = add_query_arg(
					array(
						'page'     => 'contact-manager',
						'view'     => 'analytics',
						'campaign' => $list_id,
					),
					admin_url( 'admin.php' )
				);
				?>
				<a href="<?php echo esc_attr( esc_url( $list_id_url ) ); ?>"  style="margin-right: 25px !important;" class="bsf-connect-download-csv"><i class="connects-icon-bar-graph-2" style="line-height: 30px;"></i>
				<?php esc_html_e( 'Analytics', 'smile' ); ?>
			</a>
				<?php $search_active_class = ( '' !== $sq ) ? 'bsf-cntlist-top-search-act' : ''; ?>
			<span class="bsf-contact-list-top-search <?php echo esc_attr( $search_active_class ); ?>"><i class="connects-icon-search" style="line-height: 30px;"></i>
				<form method="post" class="bsf-cntlst-top-search">
					<input class="bsf-cntlst-top-search-input" type="search" id="post-search-input" name="sq" placeholder="<?php esc_html_e( 'Search', 'smile' ); ?>" value="<?php echo esc_attr( $sq ); ?>">
					<i class="bsf-cntlst-top-search-submit connects-icon-search"></i>
				</form>
			</span><!-- .bsf-contact-list-top-search -->
			<?php } ?>

			<div class="bend-head-logo <?php echo esc_attr( str_replace( ' ', '-', strtolower( $provider ) ) ); ?>">
			</div>

		</div><!-- bend-heading section -->

		<div class="msg"></div>

			<div class="bend-content-wrap">
			<hr class="bsf-extensions-lists-separator" style="margin: 22px 0px 30px 0px;"></hr>
			<div class="container bsf-connect-content">
				<table  class="wp-list-table widefat fixed bsf-connect-optins bsf-connect-optins-list">
					<thead>
						<tr>
							<th scope="col" id="list-id" class="manage-column column-name <?php echo esc_attr( $sorting_name_class ); ?>">
								<?php

								$name_url = add_query_arg(
									array(
										'page'      => 'contact-manager',
										'view'      => 'contacts',
										'orderby'   => 'name',
										'list'      => $list_id,
										'order'     => $orderlink,
										'sq'        => $search_key,
										'cont-page' => $campaign_page,
									),
									admin_url( 'admin.php' )
								);
																						$email_url = add_query_arg(
																							array(
																								'page'      => 'contact-manager',
																								'view'      => 'contacts',
																								'orderby'   => 'email',
																								'list'      => $list_id,
																								'order'     => $orderlink,
																								'sq'        => $search_key,
																								'cont-page' => $campaign_page,
																							),
																							admin_url( 'admin.php' )
																						);

																						$date_url = add_query_arg(
																							array(
																								'page'      => 'contact-manager',
																								'view'      => 'contacts',
																								'orderby'   => 'date',
																								'list'      => $list_id,
																								'order'     => $orderlink,
																								'sq'        => $search_key,
																								'cont-page' => $campaign_page,
																							),
																							admin_url( 'admin.php' )
																						);

																						?>
								<a href="<?php echo esc_attr( esc_url( $name_url ) ); ?>">
									<span class="connects-icon-head"></span>
									<?php esc_html_e( 'Name', 'smile' ); ?></a></th>
									<th scope="col" id="provider" class="manage-column column-email <?php echo esc_attr( $sorting_email_class ); ?>">
										<a href="<?php echo esc_attr( esc_url( $name_url ) ); ?>">
											<span class="connects-icon-mail"></span>
											<?php esc_html_e( 'Email', 'smile' ); ?></a></th>
											<th scope="col" id="date" class="manage-column column-date <?php echo esc_attr( $sorting_date_class ); ?>">
												<a href="<?php echo esc_attr( esc_url( $email_url ) ); ?>">
													<span class="connects-icon-marquee-plus"></span>
													<?php esc_html_e( 'Subscribed On', 'smile' ); ?></a></th>
													<th scope="col" id="delete" class="manage-column column-delete <?php echo esc_attr( $sorting_date_class ); ?>">
														<a href="<?php echo esc_attr( esc_url( $date_url ) ); ?>">
															<span class="connects-icon-trash"></span>
															<?php esc_html_e( 'Delete', 'smile' ); ?></a></th>
														</tr>
													</thead>
													<tbody id="the-list" class="smile-style-data">
														<?php

														if ( ! empty( $contacts ) ) {

															foreach ( $contacts as $key => $list ) {
																$name = ( isset( $list['name'] ) && '' !== $list['name'] ) ? $list['name'] : 'NA';
																if ( 'NA' === $name ) {
																	$name = ( isset( $list['FName'] ) && '' !== $list['FName'] ) ? $list['FName'] : 'NA';
																}
																$email                = ( isset( $list['email'] ) && ! empty( $list['email'] ) ) ? $list['email'] : 'NA';
																$user_id              = ( isset( $list['user_id'] ) && ! empty( $list['user_id'] ) ) ? $list['user_id'] : '';
																$date                 = gmdate( 'j M Y', strtotime( $list['date'] ) );
																$url                  = CP_BASE_URL . 'admin/images/default-gravtar.png';
																$delete_contact_nonce = wp_create_nonce( 'cp-delete-contact' );
																$name_url             = esc_url(
																	add_query_arg(
																		array(
																			'page' => 'contact-manager',
																			'view' => 'contact-details',
																			'list' => $list_id,
																			'id'   => $user_id,
																			'email' => $email,
																		),
																		admin_url( 'admin.php' )
																	)
																);
																$email_url            = esc_url(
																	add_query_arg(
																		array(
																			'page' => 'contact-manager',
																			'view' => 'contact-details',
																			'list' => $list_id,
																			'id'   => $user_id,
																			'email' => $email,
																		),
																		admin_url( 'admin.php' )
																	)
																);
																$date_url             = esc_url(
																	add_query_arg(
																		array(
																			'page' => 'contact-manager',
																			'view' => 'contact-details',
																			'list' => $list_id,
																			'id'   => $user_id,
																			'email' => $email,
																		),
																		admin_url( 'admin.php' )
																	)
																);

																?>
																<tr>  
																	<td scope="col" class="manage-column column-name" data-href="<?php echo esc_attr( $name_url ); ?>" ><span class="connect-list-gravtar-img"><?php echo get_avatar( $email, '96', 'https://support.brainstormforce.com/wp-content/uploads/2015/07/default-gravtar.png' ); ?></span><?php echo esc_attr( $name ); ?></td>
																	<td scope="col" class="manage-column column-email" data-href="<?php echo esc_attr( $email_url ); ?>"><?php echo esc_attr( $email ); ?></td>
																	<td scope="col" class="manage-column column-date" data-href="<?php echo esc_attr( $date_url ); ?>"><?php echo esc_attr( $date ); ?></td>
																	<input type="hidden" id="delete-contact-nonce" value="<?php echo esc_attr( $delete_contact_nonce ); ?>" />
																	<td scope="col" class="manage-column column-delete"><a class="action-list delete-contact" style="margin-left: 6px;" data-list-id="<?php echo esc_attr( $key ); ?>" data-list="<?php echo esc_attr( $_GET['list'] ); ?>" data-user-id="<?php echo esc_attr( $user_id ); ?>" data-email="<?php echo esc_attr( $email ); ?>" href="#" data-mailer = "<?php echo esc_attr( $mailer ); ?>"><i class="connects-icon-trash"></i><span class="action-tooltip"><?php esc_html_e( 'Delete', 'smile' ); ?></span></a></td>
																</tr>
																<?php
															}
														} else {
															?>
																<?php
																$list_url_search = add_query_arg(
																	array(
																		'page' => 'contact-manager',
																		'view' => 'contact',
																		'list' => $list_id,
																	),
																	admin_url( 'admin.php' )
																);
																?>
															<tr data-href="<?php echo esc_attr( esc_url( $list_url_search ) ); ?>">
																<?php if ( isset( $_GET['sq'] ) && esc_attr( $_GET['sq'] ) !== '' ) { ?>
																<th scope="col" class="manage-column bsf-connect-column-empty" colspan="3"><?php esc_html_e( 'No results available.', 'smile' ); ?><a class="add-new-h2" style="position:relative;top:-2px;" href="javascript:void(0);"><?php esc_html_e( 'Back to Contact List', 'smile' ); ?></a></th>
															</tr>
															<?php } else { ?>
															<tr>
																<th scope="col" class="manage-column bsf-connect-column-empty" colspan="3"><?php esc_html_e( 'No contacts available.', 'smile' ); ?></th>
																<?php } ?>
															</tr>
															<?php
														}
														?>
													</tbody>
												</table>
											</div>
											<!-- .container -->

											<div class="row">
												<div class="container" style="max-width:100% !important;width:100% !important;">
													<div class="col-sm-6">
														<p class="search-box">
															<form method="post" class="bsf-cntlst-search">
																<label class="screen-reader-text" for="post-search-input"><?php esc_html_e( 'Search Contacts:', 'smile' ); ?></label>
																<input type="search" id="post-search-input" name="sq" value="<?php echo esc_attr( $sq ); ?>">
																<input type="submit" id="search-submit" class="button" value="Search">
															</form>
														</p>
													</div><!-- .col-sm-6 -->
													<div class="col-sm-6">
														<?php
														if ( $contacts ) {

															$base_page_link = '?page=contact-manager&view=contacts';
															echo $paginator->create_links( $links, 'pagination bsf-cnt-pagi', $list_id, $sq, $base_page_link ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
														}
														?>
														<div class="bsf-cnt-total-contancts"><?php echo esc_attr( $total_contacts ); ?> <?php esc_html_e( 'Contacts', 'smile' ); ?></div>
													</div><!-- .col-sm-6 -->
												</div><!-- .container -->
											</div><!-- .row -->



										</div>
										<!-- .bend-content-wrap -->
									</div>
									<!-- .wrap-container -->
								</div>
								<!-- .wrap -->

