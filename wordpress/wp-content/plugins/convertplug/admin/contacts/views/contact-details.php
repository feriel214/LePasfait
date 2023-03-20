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

/**
 * Checks to see if the specified email address has a Gravatar image.
 *
 * @param  string $email_address email_id.
 * @return boolean                 Whether or not the user has a gravatar.
 */
function has_gravatar( $email_address ) {

	// Build the Gravatar URL by hasing the email address.
	$url = 'http://www.gravatar.com/avatar/' . md5( strtolower( trim( $email_address ) ) ) . '?d=404';

	// Now check the headers.
	$headers = get_headers( $url );

	// If 200 is found, the user has a Gravatar; otherwise, they don't.
	return preg_match( '|200|', $headers[0] ) ? true : false;

} // end example_has_gravatar.


$list_id   = intval( $_GET['list'] );                     // Get current mailer by ID.
$provider  = '';
$list_name = '';

// Find current mailer.
$smile_lists = get_option( 'smile_lists' );    // Get all mailers.
if ( $smile_lists ) {
	if ( isset( $smile_lists[ $list_id ] ) ) {
		$list      = $smile_lists[ $list_id ];
		$list_name = $list['list-name'];
		$provider  = $list['list-provider'];
	}
}


$mailer    = str_replace( ' ', '_', strtolower( trim( $provider ) ) );
$list_name = str_replace( ' ', '_', strtolower( trim( $list_name ) ) );

if ( 'convert_plug' !== $mailer ) {
	$list_option = 'cp_' . $mailer . '_' . $list_name;
	$contacts    = get_option( $list_option );
} else {
	$list_option = 'cp_connects_' . $list_name;
	$contacts    = get_option( $list_option );
} ?>

<div class="wrap about-wrap bsf-connect bsf-connect-list bsf-connect-user-info bend">
	<div class="wrap-container">

		<!-- Detailed User Info -->
		<?php

		// Current User.

		if ( isset( $_REQUEST['cp_admin_page_nonce'] ) && ! wp_verify_nonce( $_REQUEST['cp_admin_page_nonce'], 'cp_admin_page' ) ) {
			wp_die( 'No direct script access allowed!' );
		}

		$current_user_name = ( isset( $_GET['id'] ) ) ? sanitize_text_field( $_GET['id'] ) : '';
		$current_email     = ( isset( $_GET['email'] ) ) ? sanitize_email( $_GET['email'] ) : '';
		if ( ! empty( $contacts ) ) {

			foreach ( $contacts as $key => $list ) {

				$email   = isset( $list['email'] ) ? $list['email'] : 'NA';
				$name    = ( isset( $list['name'] ) && '' !== $list['name'] ) ? $list['name'] : 'NA';
				$date    = gmdate( 'j M Y', strtotime( $list['date'] ) );
				$time    = isset( $list['time'] ) ? gmdate( ' g:i:s a', strtotime( $list['time'] ) ) : '';
				$user_id = ( isset( $list['user_id'] ) && '' !== $list['user_id'] ) ? $list['user_id'] : '';
				// Check Current User.
				if ( empty( $current_user_name ) ) {
					$is_valid = ( $current_email === $email ) ? true : false;
				} else {
					$is_valid = ( $current_user_name === $user_id ) ? true : false;
				}

				if ( $is_valid ) {
					?>

					<div class="bend-heading-section bsf-connect-header bsf-connect-list-header 
					<?php
					if ( empty( $contacts ) ) {
						echo 'bsf-connect-empty-header'; }
					?>
						">
						<h1><span class="cp-strip-text" style="max-width: 460px;top: 10px;" title="
							<?php
							if ( ! empty( $name ) ) {
								echo esc_attr( $name );
							} else {
								echo esc_html__( 'Subscriber Details', 'smile' ); }
							?>
								">
								<?php
								if ( ! empty( $name ) ) {
									echo esc_attr( $name );
								} else {
									echo esc_html__( 'Subscriber Details', 'smile' ); }

									$cp_contact_manager = add_query_arg(
										array(
											'page' => 'contact-manager',
											'view' => 'contacts',
											'list' => $_GET['list'],
										),
										admin_url( 'admin.php' )
									);
								?>
								</span> <a class="add-new-h2" href="<?php echo esc_attr( esc_url( $cp_contact_manager ) ); ?>"><?php esc_html_e( 'Back to Campaigns List', 'smile' ); ?></a></h1>
								<h3 style="margin-bottom: 55px;">
									<?php
									if ( 'NA' !== $email ) {
										echo esc_attr( $email );
									} else {
										echo esc_attr( $date ); }
									?>
									</h3>
									<div class="bend-head-logo">
										<?php
										// has Gravatar?
										if ( has_gravatar( $email ) ) {
											echo '<div class="user-thumb"><span class="connect-list-gravtar-img">' . get_avatar( $email, '96', 'https://support.brainstormforce.com/wp-content/uploads/2015/07/default-gravtar.png' ) . '</span></div>';
										} else {
											echo '<div class="user-icon"><i class="connects-icon-head"></i></div>';
										}
										?>
										<div class="bend-product-ver">
											<?php echo esc_attr( $date ); ?>
										</div>
									</div>
								</div><!-- bend-heading section -->

								<div class="bend-content-wrap">
									<hr class="bsf-extensions-lists-separator" style="margin: 22px 0px 30px 0px;"></hr>
									<div class="container bsf-connect-content">
										<table  class="wp-list-table widefat fixed bsf-connect-optins bsf-connect-optins-list">
											<tbody id="the-list" class="smile-style-data">

												<tr>
													<td scope="col" class="manage-column">
														<label><?php echo esc_html__( 'EMAIL ADDRESS', 'smile' ); ?></label>
														<?php echo esc_attr( $email ); ?>
													</td>
													<td scope="col" class="manage-column">
														<label><?php echo esc_html__( 'DATE', 'smile' ); ?></label>
														<?php echo esc_attr( $date ); ?>
													</td>
												</tr>

												<tr>
													<?php
													// Show all the details though loop.
													// except 'email' & 'date'.
													unset( $list['date'] );
													if ( isset( $list['email'] ) ) {
														unset( $list['email'] );
													}

													// Build 2x2 <td> for rest of the fields.
													$i = 0;
													ksort( $list );
													foreach ( $list as $k => $v ) {

														if ( isset( $v ) && ! empty( $v ) ) {
															$i++;
															if ( 0 === $i % 2 ) {
																$i / 2;
																?>
																<td scope="col" class="manage-column">

																	<label><?php echo esc_html( strtoupper( $k ) ); ?></label>
																	<?php
																	if ( 'time' === $k ) {
																		$v = $time;
																	}
																	echo esc_attr( $v );
																	?>
																</td>
																<?php
															} else {
																?>
															</tr>
															<tr><td scope="col" class="manage-column">
																<label><?php echo esc_html( strtoupper( $k ) ); ?></label>
																<?php
																if ( 'time' === $k ) {
																	$v = $time;
																}
																echo esc_attr( $v );
																?>
															</td>
																<?php
															}
														}
													}
													?>
											</tr>
											<?php
				}
			}
			?>
								</tbody>
							</table>
						</div>
					</div><!-- .bend-content-wrap -->
					<?php } ?>
				</div><!-- .wrap-container -->
			</div><!-- .wrap -->
