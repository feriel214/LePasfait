<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );
?>
<div class="wrap bsf-page-wrapper ultimate-about">
	<div class="wrap-container">
		<div class="heading-section">
			<div class="bsf-pr-header bsf-left-header" style="margin-bottom: 55px;">
				<h2><?php echo esc_html__( 'Resources!', 'smile' ); ?></h2>
				<div class="bsf-pr-decription"></div>
			</div>

			<div class="right-logo-section">
				<div class="bsf-company-logo">
				</div><!--company-logo-->
			</div><!--right-logo-section-->
		</div>	<!--heading section-->

		<div class="inside bsf-wrap">
			<div class="container">
				<?php
				if (
					( isset( $connects ) && ( true === $connects || 'true' === $connects ) ) ||
					( ! isset( $connects ) )
				) :
					?>
					<?php
					$contact_manager = add_query_arg(
						array(
							'page' => 'contact-manager',
						),
						admin_url( 'admin.php' )
					);
					?>
				<div class="col-sm-3 col-lg-3 resource-block-section">
					<a class="resource-block-link" href="<?php echo esc_attr( esc_url( $contact_manager ) ); ?>">
						<div class="resource-block-icon">
							<span class="dashicons dashicons-share"></span>
						</div>
						<div class="resource-block-content">
							<?php echo esc_html__( 'Connects', 'smile' ); ?>
						</div>
					</a>
				</div><!--col-sm-3-->
			<?php endif; ?>

			<div class="col-sm-3 col-lg-3 resource-block-section">
				<a class="resource-block-link" href="<?php echo esc_attr( esc_url( bsf_exension_installer_url( '14058953' ) ) ); ?>">
					<div class="resource-block-icon">
						<span class="dashicons dashicons-admin-plugins"></span>
					</div>
					<div class="resource-block-content">
						<?php echo esc_html__( 'Addons', 'smile' ); ?>
					</div>
				</a>
			</div><!--col-sm-3-->

			<?php
			if (
				( isset( $icon_manager ) && ( true === $icon_manager || 'true' === $icon_manager ) ) ||
				( ! isset( $icon_manager ) )
			) :
				?>
					<?php
					$bsf_font_icon_manager = add_query_arg(
						array(
							'page' => 'bsf-font-icon-manager',
						),
						admin_url( 'admin.php' )
					);
					?>
			<div class="col-sm-3 col-lg-3 resource-block-section">
				<a class="resource-block-link" href="<?php echo esc_attr( esc_url( $bsf_font_icon_manager ) ); ?>">
					<div class="resource-block-icon">
						<span class="dashicons dashicons-awards"></span>
					</div>
					<div class="resource-block-content">
						<?php echo esc_html__( 'Font Icon Manager', 'smile' ); ?>
					</div>
				</a>
			</div><!--col-sm-3-->
		<?php endif; ?>

		<?php
		if (
			( isset( $google_fonts ) && ( true === $google_fonts || 'true' === $google_fonts ) ) ||
			( ! isset( $google_fonts ) )
		) :
			?>
				<?php
				$bsf_google_font_manager = add_query_arg(
					array(
						'page' => 'cplus-bsf-google-font-manager',
					),
					admin_url( 'admin.php' )
				);
				?>
		<div class="col-sm-3 col-lg-3 resource-block-section">
			<a class="resource-block-link" href="<?php echo esc_attr( esc_url( $bsf_google_font_manager ) ); ?>">
				<div class="resource-block-icon">
					<span class="dashicons dashicons-edit"></span>
				</div>
				<div class="resource-block-content">
					<?php echo esc_html__( 'Google Fonts Manager', 'smile' ); ?>
				</div>
			</a>
		</div><!--col-sm-3-->
	<?php endif; ?>
				<?php
				if (
				( isset( $google_recaptcha ) && ( true === $google_recaptcha || 'true' === $google_recaptcha ) ) ||
				( ! isset( $google_recaptcha ) )
				) :
					?>
					<?php
					$bsf_google_recaptcha_manager = add_query_arg(
						array(
							'page' => 'bsf-google-recaptcha-manager',
						),
						admin_url( 'admin.php' )
					);
					?>
		<div class="col-sm-3 col-lg-3 resource-block-section">
			<a class="resource-block-link" href="<?php echo esc_attr( esc_url( $bsf_google_recaptcha_manager ) ); ?>">
				<div class="resource-block-icon">
					<span class="dashicons dashicons-edit"></span>
				</div>
				<div class="resource-block-content">
					<?php echo esc_html__( 'Google Recaptcha Manager', 'smile' ); ?>
				</div>
			</a>
		</div><!--col-sm-3-->
			<?php endif; ?>

			<?php
			if (
				( isset( $maxmind_database ) && ( true === $maxmind_database || 'true' === $maxmind_database ) ) ||
				( ! isset( $maxmind_database ) )
				) :
				?>
				<?php

				$bsf_maxmind_database_manager = add_query_arg(
					array(
						'page' => 'bsf-maxmind-database-manager',
					),
					admin_url( 'admin.php' )
				);
				?>
		<div class="col-sm-3 col-lg-3 resource-block-section">
			<a class="resource-block-link" href="<?php echo esc_url( $bsf_maxmind_database_manager ); ?>">
				<div class="resource-block-icon">
					<span class="dashicons dashicons-location"></span>
				</div>
				<div class="resource-block-content">
					<?php echo esc_html__( 'MaxMind Database Manager', 'smile' ); ?>
				</div>
			</a>
		</div><!--col-sm-3-->
			<?php endif; ?>


	<?php if ( class_exists( 'CP_Wp_Comment_Form' ) ) : ?>
			<?php
			$cp_wp_comment_form = add_query_arg(
				array(
					'page' => 'cp-wp-comment-form',
				),
				admin_url( 'admin.php' )
			);
			?>
		<div class="col-sm-3 col-lg-3 resource-block-section">
			<a class="resource-block-link" href="<?php echo esc_attr( esc_url( $cp_wp_comment_form ) ); ?>">
				<div class="resource-block-icon">
					<span class="dashicons dashicons-testimonial"></span>
				</div>
				<div class="resource-block-content">
					<?php echo esc_html__( 'WP Comment Form', 'smile' ); ?>
				</div>
			</a>
		</div><!--col-sm-3-->
	<?php endif; ?>

	<?php if ( class_exists( 'CP_Wp_Registration_Form' ) ) : ?>
			<?php
			$cp_wp_registration_form = add_query_arg(
				array(
					'page' => 'cp-wp-registration-form',
				),
				admin_url( 'admin.php' )
			);
			?>
		<div class="col-sm-3 col-lg-3 resource-block-section">
			<a class="resource-block-link" href="<?php echo esc_attr( esc_url( $cp_wp_registration_form ) ); ?>">
				<div class="resource-block-icon">
					<span class="dashicons dashicons-welcome-write-blog"></span>
				</div>
				<div class="resource-block-content">
					<?php echo esc_html__( 'WP Registration Form', 'smile' ); ?>
				</div>
			</a>
		</div><!--col-sm-3-->
	<?php endif; ?>

	<?php if ( class_exists( 'CP_Woocommerce_Checkout_Form' ) && class_exists( 'WooCommerce' ) ) : ?>
		<?php
		$cp_woocheckout_form = add_query_arg(
			array(
				'page' => 'cp-woocheckout-form',
			),
			admin_url( 'admin.php' )
		);
		?>
		<div class="col-sm-3 col-lg-3 resource-block-section">
			<a class="resource-block-link" href="<?php echo esc_attr( esc_url( $cp_woocheckout_form ) ); ?>">
				<div class="resource-block-icon">
					<span class="dashicons dashicons-cart"></span>
				</div>
				<div class="resource-block-content">
					<?php echo esc_html__( 'WooCommerce Checkout Form', 'smile' ); ?>
				</div>
			</a>
		</div><!--col-sm-3-->
	<?php endif; ?>

	<?php if ( class_exists( 'CP_Contact_Form7' ) && class_exists( 'WPCF7' ) ) : ?>
		<?php
		$cp_contact_form7 = add_query_arg(
			array(
				'page' => 'cp-contact-form7',
			),
			admin_url( 'admin.php' )
		);
		?>
		<div class="col-sm-3 col-lg-3">
			<a class="resource-block-link" href="<?php echo esc_attr( esc_url( $cp_contact_form7 ) ); ?>">
				<div class="resource-block-icon">
					<span class="dashicons dashicons-clipboard"></span>
				</div>
				<div class="resource-block-content">
					<?php echo esc_html__( 'Contact Form 7', 'smile' ); ?>
				</div>
			</a>
		</div><!--col-sm-3-->
	<?php endif; ?>
</div><!--container-->

</div><!--bsf-wrap-->
</div><!--wrap-container-->
</div><!--wrap-->
