<?php
/**
 * Template
 *
 * @package Fusion-White-Label-Branding
 */

// phpcs:disable VariableAnalysis.CodeAnalysis.VariableAnalysis.SelfOutsideClass
?>
<?php Fusion_White_Label_Branding_Admin::get_admin_screens_header( 'dashboard' ); ?>
	<section class="avada-db-card avada-db-card-first avada-db-dashboard-start">
		<h1 class="avada-db-dashboard-heading"><?php esc_html_e( 'Welcome to Avada Custom Branding', 'fusion-white-label-branding' ); ?></h1>
		<p><?php esc_html_e( 'Avada Custom Branding is now installed and ready to use! You can brand Avada and the constituent functionality with your own custom branding through easy to use options! Click on the settings tab to find out all you can do with this amazing Avada Custom Branding plugin.', 'fusion-white-label-branding' ); ?></p>
		<p><?php esc_html_e( 'Avada Custom Branding is bundled with Avada and is therefore covered by the 6 months of free support for every Avada license you purchase.', 'fusion-white-label-branding' ); ?></p>

		<div class="avada-db-card-notice">
			<i class="fusiona-info-circle"></i>
			<p class="avada-db-card-notice-heading">
				<?php esc_html_e( 'Please make sure Avada version is up to date. Recommended compatibility is Avada 7.0 and newer.', 'fusion-white-label-branding' ); ?>
			</p>
		</div>
	</section>

	<section class="avada-db-card>
		<img src="<?php echo esc_url( FUSION_WHITE_LABEL_BRANDING_PLUGIN_URL . 'assets/images/welcome-screen.jpg' ); ?>" />
	</section>

	<section class="avada-db-card avada-db-support-channels">
		<h2 class="avada-db-support-channels-heading"><?php esc_html_e( 'Channels Of Support', 'fusion-white-label-branding' ); ?></h2>

		<div class="avada-db-card-grid">
			<div class="avada-db-card-notice">
				<div class="avada-db-card-notice-heading">
					<i class="fusiona-exclamation-sign"></i>
					<h3><?php esc_html_e( 'Quick Start Guide', 'fusion-white-label-branding' ); ?></h3>
				</div>
				<p class="avada-db-card-notice-content">
					<?php esc_html_e( 'We understand that it can be a daunting process getting started with WordPress. In light of this, we have prepared a starter pack for you, which includes all you need to know.', 'fusion-white-label-branding' ); ?>
				</p>
				<p class="avada-db-card-notice-content">
					<a href="<?php echo esc_url( self::$theme_fusion_url ) . 'documentation/avada/getting-started/avada-quick-start-guide/'; ?>" class="button button-primary" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Starter Guide', 'fusion-white-label-branding' ); ?></a>
				</p>
			</div>

			<div class="avada-db-card-notice">
				<div class="avada-db-card-notice-heading">
					<i class="fusiona-documentation"></i>
					<h3><?php esc_html_e( 'Documentation', 'fusion-white-label-branding' ); ?></h3>
				</div>
				<p class="avada-db-card-notice-content">
					<?php esc_html_e( 'This is the place to go to reference different aspects of the product. Our online documentaiton is an incredible resource for learning the ins and outs of using the Avada Website Builder.', 'fusion-white-label-branding' ); ?>
				</p>
				<p class="avada-db-card-notice-content">
					<a href="<?php echo esc_url( self::$theme_fusion_url ) . 'documentation/avada/'; ?>" class="button button-primary" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Documentation', 'fusion-white-label-branding' ); ?></a>
				</p>
			</div>

			<div class="avada-db-card-notice">
				<div class="avada-db-card-notice-heading">
					<i class="fusiona-author"></i>
					<h3><?php esc_html_e( 'Submit A Ticket', 'fusion-white-label-branding' ); ?></h3>
				</div>
				<p class="avada-db-card-notice-content">
					<?php esc_html_e( 'We offer excellent support through our advanced ticket system. Make sure to register your purchase first to access our support services and other resources.', 'fusion-white-label-branding' ); ?>
				</p>
				<p class="avada-db-card-notice-content">
					<a href="<?php echo esc_url_raw( self::$theme_fusion_url ) . 'support/submit-a-ticket/'; ?>" class="button button-primary" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Submit A Ticket', 'fusion-white-label-branding' ); ?></a>
				</p>
			</div>
		</div>

		<div class="avada-db-card-grid">
			<div class="avada-db-card-notice">
				<div class="avada-db-card-notice-heading">
					<i class="fusiona-video"></i>
					<h3><?php esc_html_e( 'Video Tutorials', 'fusion-white-label-branding' ); ?></h3>
				</div>
				<p class="avada-db-card-notice-content">
					<?php esc_html_e( 'Nothing is better than watching a video to learn. We have a growing library of narrated HD video tutorials to help teach you the different aspects of using the Avada Website Builder.', 'fusion-white-label-branding' ); ?>
				</p>
				<p class="avada-db-card-notice-content">
					<a href="<?php echo esc_url( self::$theme_fusion_url ) . 'documentation/avada/videos/'; ?>" class="button button-primary" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Watch Videos', 'fusion-white-label-branding' ); ?></a>
				</p>
			</div>

			<div class="avada-db-card-notice">
				<div class="avada-db-card-notice-heading">
					<i class="fusiona-users"></i>
					<h3><?php esc_html_e( 'Community Forum', 'fusion-white-label-branding' ); ?></h3>
				</div>
				<p class="avada-db-card-notice-content">
					<?php esc_html_e( 'We also have a community forum for user to user interactions. Ask and help other Avada users! Please note that ThemeFusion does not provide product support here.', 'fusion-white-label-branding' ); ?>
				</p>
				<p class="avada-db-card-notice-content">
					<a href="<?php echo esc_url( self::$theme_fusion_url ) . 'community/forum/'; ?>" class="button button-large button-primary" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Community Forum', 'fusion-white-label-branding' ); ?></a>
				</p>
			</div>

			<div class="avada-db-card-notice">
				<div class="avada-db-card-notice-heading">
					<i class="dashicons dashicons-facebook"></i>
					<h3><?php esc_html_e( 'Facebook Group', 'fusion-white-label-branding' ); ?></h3>
				</div>
				<p class="avada-db-card-notice-content">
					<?php esc_html_e( 'We have an amazing Facebook Group! Share with other Avada users and help grow our community. Please note, ThemeFusion does not provide support here.', 'fusion-white-label-branding' ); ?>
				</p>
				<p class="avada-db-card-notice-content">
					<a href="https://www.facebook.com/groups/AvadaUsers/" class="button button-primary" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Facebook Group', 'fusion-white-label-branding' ); ?></a>
				</p>
			</div>
		</div>
	</section>
<?php Fusion_White_Label_Branding_Admin::get_admin_screens_footer(); ?>
