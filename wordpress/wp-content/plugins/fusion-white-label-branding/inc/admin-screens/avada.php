<?php
/**
 * Template
 *
 * @package Fusion-White-Label-Branding
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$settings      = get_option( 'fusion_branding_settings', [] );
$avada_options = isset( $settings['fusion_branding']['avada'] ) ? $settings['fusion_branding']['avada'] : [];
Fusion_White_Label_Branding_Admin::get_admin_screens_header( 'avada' );
?>
<section class="avada-db-card avada-db-card-first avada-db-import-export-start">
	<h1 class="avada-db-import-wp-admin-heading">
		<?php
		/* Translators: %s: Avada. */
		printf( esc_html__( '%s Custom Branding Settings', 'fusion-white-label-branding' ), 'Avada' );
		?>
	</h1>
	<p>
		<?php
		/* Translators: %s: Avada. */
		printf( esc_html__( 'These settings will change items on %s admin pages.', 'fusion-white-label-branding' ), 'Avada' );
		?>
	</p>
</section>

<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
	<section class="avada-db-card fusion-white-label-option">
		<div class="fusion-white-label-option-title">
			<?php /* translators: Avada */ ?>
			<h2><?php printf( esc_html__( '%s Label', 'fusion-white-label-branding' ), 'Avada' ); ?></h2>
			<?php /* translators: Avada */ ?>
			<p class="description"><?php printf( esc_html__( 'Replaces all instances of "%s".', 'fusion-white-label-branding' ), 'Avada' ); ?></p>
		</div>
		<div class="fusion-white-label-option-input">
			<input type="text" id="admin_menu_label" name="fusion_branding[avada][admin_menu_label]" class="regular-text" value="<?php echo ( isset( $avada_options['admin_menu_label'] ) ) ? esc_attr( $avada_options['admin_menu_label'] ) : ''; ?>" />
		</div>
	</section>

	<section class="avada-db-card fusion-white-label-option">
		<div class="fusion-white-label-option-title">
			<?php /* translators: Avada */ ?>
			<h2><?php printf( esc_html__( 'Remove %s Admin Bar Menu', 'fusion-white-label-branding' ), 'Avada' ); ?></h2>
			<?php /* translators: Avada */ ?>
			<p class="description"><?php printf( esc_html__( 'Removes the %s menu item from admin bar on frontend.', 'fusion-white-label-branding' ), 'Avada' ); ?></p>
		</div>
		<div class="fusion-white-label-option-input">
			<div class="fusion-branding-option-field">
				<div class="fusion-form-radio-button-set ui-buttonset">
					<?php
					$remove_admin_bar_menu = '0';
					if ( isset( $avada_options['remove_admin_bar_menu'] ) ) {
						$remove_admin_bar_menu = $avada_options['remove_admin_bar_menu'];
					}
					?>
					<input type="hidden" class="button-set-value" value="<?php echo esc_attr( $remove_admin_bar_menu ); ?>" name="fusion_branding[avada][remove_admin_bar_menu]" id="remove_admin_bar_menu" />
					<a data-value="1" class="ui-button buttonset-item<?php echo ( $remove_admin_bar_menu ) ? ' ui-state-active' : ''; ?>" href="#"><?php esc_html_e( 'Yes', 'fusion-white-label-branding' ); ?></a>
					<a data-value="0" class="ui-button buttonset-item<?php echo ( ! $remove_admin_bar_menu ) ? ' ui-state-active' : ''; ?>" href="#"><?php esc_html_e( 'No', 'fusion-white-label-branding' ); ?></a>
				</div>
			</div>
		</div>
	</section>

	<section class="avada-db-card fusion-white-label-option">
		<div class="fusion-white-label-option-title">
			<h2><?php esc_html_e( 'Remove Dashboard Main Menu Items', 'fusion-white-label-branding' ); ?></h2>
			<?php /* translators: Avada */ ?>
			<p class="description"><?php printf( esc_html__( 'Removes the selected menu items from the dashboard main menu and the %s WP admin menu.', 'fusion-white-label-branding' ), 'Avada' ); ?></p>
		</div>
		<div class="fusion-white-label-option-input">
			<?php
			$avada_submenus = [
				'avada'             => __( 'Dashboard', 'fusion-white-label-branding' ),
				'options'           => __( 'Options', 'fusion-white-label-branding' ),
				'prebuilt-websites' => __( 'Websites', 'fusion-white-label-branding' ),
				'patcher'           => __( 'Patcher', 'fusion-white-label-branding' ),
				'plugins'           => __( 'Plugins', 'fusion-white-label-branding' ),
				'changelog'         => __( 'Changelog', 'fusion-white-label-branding' ),
				'support'           => __( 'Support', 'fusion-white-label-branding' ),
				'status'            => __( 'Status', 'fusion-white-label-branding' ),
			];
			$selected_menus = isset( $avada_options['remove_main_menu_items'] ) ? $avada_options['remove_main_menu_items'] : [];
			?>
			<?php foreach ( $avada_submenus as $menu => $title ) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride.OverrideProhibited ?>
				<?php $selected_menu = in_array( $menu, $selected_menus, true ) ? ' checked="checked"' : ''; ?>
				<span>
					<label for="remove_main_menu_items_<?php echo esc_attr( $menu ); ?>">
						<input type="checkbox" id="remove_main_menu_items_<?php echo esc_attr( $menu ); ?>" <?php echo esc_html( $selected_menu ); ?> name="fusion_branding[avada][remove_main_menu_items][]" class="regular-checkbox" value="<?php echo esc_attr( $menu ); ?>" />
						<?php echo esc_html( $title ); ?>
					</label>
				</span>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="avada-db-card fusion-white-label-option">
		<div class="fusion-white-label-option-title">
			<h2><?php esc_html_e( 'Remove Dashboard Sticky Menu Items', 'fusion-white-label-branding' ); ?></h2>
			<?php /* translators: Fusion Builder */ ?>
			<p class="description"><?php printf( esc_html__( 'Removes the selected menu items from the dashboard sticky menu and the %s WP admin menu.', 'fusion-white-label-branding' ), 'Avada' ); ?></p>
		</div>
		<div class="fusion-white-label-option-input">
			<?php
			$fusion_builder_submenus = [
				'layouts'         => esc_html__( 'Layouts', 'fusion-white-label-branding' ),
				'layout_sections' => esc_html__( 'Layout Sections', 'fusion-white-label-branding' ),
				'icons'           => esc_html__( 'Icons', 'fusion-white-label-branding' ),
				'library'         => esc_html__( 'Library', 'fusion-white-label-branding' ),
				'sliders'         => esc_html__( 'Sliders', 'fusion-white-label-branding' ),
				'slides'          => esc_html__( 'Slides', 'fusion-white-label-branding' ),
				'slider_export'   => esc_html__( 'Export / Import', 'fusion-white-label-branding' ),
			];

			$selected_menus = isset( $avada_options['remove_sticky_menu_items'] ) ? $avada_options['remove_sticky_menu_items'] : [];
			foreach ( $fusion_builder_submenus as $menu => $title ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.OverrideProhibited
				$selected_menu = in_array( $menu, $selected_menus, true ) ? ' checked="checked"' : '';
				?>
				<span>
				<label for="remove_sticky_menu_items_<?php echo esc_html( $menu ); ?>">
				<input type="checkbox" id="remove_sticky_menu_items_<?php echo esc_html( $menu ); ?>" <?php echo esc_html( $selected_menu ); ?> name="fusion_branding[avada][remove_sticky_menu_items][]" class="regular-checkbox" value="<?php echo esc_html( $menu ); ?>" />
				<?php echo esc_html( $title ); ?></label></span>
				<?php
			}
			?>
		</div>
	</section>

	<?php if ( class_exists( 'FusionCore_Plugin' ) ) : ?>
		<section class="avada-db-card fusion-white-label-option">
			<div class="fusion-white-label-option-title">
				<?php /* translators: Avada */ ?>
				<h2><?php printf( esc_html__( 'Rename %s Post Type Menus', 'fusion-white-label-branding' ), 'Avada' ); ?></h2>
				<?php /* translators: Avada */ ?>
				<p class="description"><?php printf( esc_html__( 'Renames the admin menu labels for %s post types.', 'fusion-white-label-branding' ), 'Avada' ); ?></p>
			</div>
			<div class="fusion-white-label-option-input">
				<?php
				$admin_menus = [
					'faq'       => esc_html__( 'FAQs', 'fusion-white-label-branding' ),
					'portfolio' => esc_html__( 'Portfolio', 'fusion-white-label-branding' ),
					'skills'    => esc_html__( 'Skills', 'fusion-white-label-branding' ),
					'tags'      => esc_html__( 'Tags', 'fusion-white-label-branding' ),
				];

				$saved_menus = isset( $avada_options['rename_admin_menu'] ) ? $avada_options['rename_admin_menu'] : [];
				foreach ( $admin_menus as $menu => $title ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.OverrideProhibited
					$value = ( isset( $saved_menus[ $menu ] ) && '' !== $saved_menus[ $menu ] ) ? $saved_menus[ $menu ] : '';
					?>
					<p>
					<label for="rename_admin_menu_<?php echo esc_attr( $menu ); ?>">
					<input type="text" id="rename_admin_menu_<?php echo esc_attr( $menu ); ?>" placeholder="<?php echo esc_attr( $title ); ?>" name="fusion_branding[avada][rename_admin_menu][<?php echo esc_attr( $menu ); ?>]" class="regular-text" value="<?php echo esc_attr( $value ); ?>" />
					<span><?php echo esc_html( $title ); ?></span>
					</label></p>
					<?php
				}
				?>
			</div>
		</section>

		<section class="avada-db-card fusion-white-label-option">
			<div class="fusion-white-label-option-title">
				<h2><?php esc_html_e( 'Remove Post Type Menus', 'fusion-white-label-branding' ); ?></h2>
				<?php /* translators: Avada */ ?>
				<p class="description"><?php printf( esc_html__( 'Removes the selected admin menus for post types registered for "%s".', 'fusion-white-label-branding' ), 'Avada' ); ?></p>
			</div>
			<div class="fusion-white-label-option-input">
				<?php
				$avada_post_types = [
					'avada_portfolio'     => __( 'Portfolio', 'fusion-white-label-branding' ),
					'avada_faq'           => __( 'FAQ', 'fusion-white-label-branding' ),
					'themefusion_elastic' => __( 'Elastic Slider', 'fusion-white-label-branding' ),
				];

				$selected_menus = isset( $avada_options['remove_post_type_menu'] ) ? $avada_options['remove_post_type_menu'] : [];
				foreach ( $avada_post_types as $menu => $title ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.OverrideProhibited
					$selected_menu = in_array( $menu, $selected_menus, true ) ? ' checked="checked"' : '';
					?>
					<span>
					<label for="remove_post_type_menu_<?php echo esc_attr( $menu ); ?>">
					<input type="checkbox" id="remove_post_type_menu_<?php echo esc_attr( $menu ); ?>" <?php echo esc_html( $selected_menu ); ?> name="fusion_branding[avada][remove_post_type_menu][]" class="regular-checkbox" value="<?php echo esc_attr( $menu ); ?>" />
					<?php echo esc_html( $title ); ?></label></span>
					<?php
				}
				?>
			</div>
		</section>
	<?php endif; ?>
	<section class="avada-db-card fusion-white-label-option">
		<div class="fusion-white-label-option-title">
			<h2>
				<?php
				// translators: Avada.
				printf( esc_html__( '%s Logo Image', 'fusion-white-label-branding' ), 'Avada' );
				?>
			</h2>
			<p class="description">
				<?php
				// Translators: Avada.
				printf( __( 'Controls the logo image on %s admin pages. <br/><strong>Recommended size:</strong> 75px x 75px.', 'fusion-white-label-branding' ), 'Avada' ); // phpcs:ignore WordPress.Security.EscapeOutput
				?>
			</p>
		</div>
		<div class="fusion-white-label-option-input">
			<div class="avada_logo_image_preview branding-image-preview">
				<?php
				if ( ! empty( $avada_options['avada_logo_image'] ) ) {
					echo '<img src="' . esc_url( $avada_options['avada_logo_image'] ) . '" />';
				}
				?>
			</div>
			<div class="image-upload-container">
				<div class="upload-field-input">
					<input type="text" id="avada_logo_image" name="fusion_branding[avada][avada_logo_image]" class="image-field" value="<?php echo ( isset( $avada_options['avada_logo_image'] ) ) ? esc_attr( $avada_options['avada_logo_image'] ) : ''; ?>" />
				</div>
				<div class="upload-field-buttons">
					<?php // translators: Avada. ?>
					<input type="button" data-title="<?php printf( esc_attr__( '%s Logo Image', 'fusion-white-label-branding' ), 'Avada' ); ?>" data-button-title="<?php esc_attr_e( 'Use Logo', 'fusion-white-label-branding' ); ?>" data-image-id="avada_logo_image" class="button button-secondary button-upload-image button-default" value="<?php esc_attr_e( 'Upload Logo Image', 'fusion-white-label-branding' ); ?>" />
					<input type="button" onclick="jQuery('#avada_logo_image').val('').trigger( 'change' ); jQuery( '.avada_logo_image_preview').html(''); jQuery( this ).hide();" class="button button-secondary button-remove-image button-default <?php echo ( ! isset( $avada_options['avada_logo_image'] ) || '' === $avada_options['avada_logo_image'] ) ? 'hidden' : ''; ?>" value="<?php esc_attr_e( 'Remove Image', 'fusion-white-label-branding' ); ?> " />
				</div>
			</div>
		</div>
	</section>

	<section class="avada-db-card fusion-white-label-option">
		<div class="fusion-white-label-option-title">
			<h2><?php esc_html_e( 'Version Number Text', 'fusion-white-label-branding' ); ?></h2>
			<?php /* translators: Avada */ ?>
			<p class="description"><?php printf( esc_html__( 'Replaces version number text beneath the logo on %s admin screens.', 'fusion-white-label-branding' ), 'Avada' ); ?></p>
		</div>
		<div class="fusion-white-label-option-input">
			<input type="text" id="version_number_text" name="fusion_branding[avada][version_number_text]" class="regular-text" value="<?php echo ( isset( $avada_options['version_number_text'] ) ) ? esc_attr( $avada_options['version_number_text'] ) : ''; ?>" />
		</div>
	</section>

	<section class="avada-db-card fusion-white-label-option">
		<div class="fusion-white-label-option-title">
			<h2>
				<?php
				// translators: Avada.
				printf( esc_html__( '%s Menu Icon Image', 'fusion-white-label-branding' ), 'Avada' );
				?>
			</h2>
			<p class="description">
				<?php
				// Translators: Avada.
				printf( __( 'Controls the menu icon image on %s admin menu and replaces the %s logo. <br/><strong>Recommended size:</strong> 40px x 40px.', 'fusion-white-label-branding' ), 'Avada', 'Avada' ); // phpcs:ignore WordPress.Security.EscapeOutput
				?>
			</p>
		</div>
		<div class="fusion-white-label-option-input">
			<div class="avada_icon_image_preview branding-image-preview">
				<?php
				if ( ! empty( $avada_options['avada_icon_image'] ) ) {
					echo '<img src="' . esc_url( $avada_options['avada_icon_image'] ) . '" />';
				}
				?>
			</div>
			<div class="image-upload-container">
				<div class="upload-field-input">
					<input type="text" id="avada_icon_image" name="fusion_branding[avada][avada_icon_image]" class="image-field" value="<?php echo ( isset( $avada_options['avada_icon_image'] ) ) ? esc_attr( $avada_options['avada_icon_image'] ) : ''; ?>" />
				</div>
				<div class="upload-field-buttons">
					<?php // translators: Avada. ?>
					<input type="button" data-title="<?php printf( esc_attr__( '%s Menu Icon Image', 'fusion-white-label-branding' ), 'Avada' ); ?>" data-button-title="<?php esc_attr_e( 'Use Menu Icon', 'fusion-white-label-branding' ); ?>" data-image-id="avada_icon_image" class="button button-secondary button-upload-image button-default" value="<?php esc_attr_e( 'Upload Menu Icon Image', 'fusion-white-label-branding' ); ?>" />
					<input type="button" onclick="jQuery('#avada_icon_image').val('').trigger( 'change' ); jQuery( '.avada_icon_image_preview').html(''); jQuery( this ).hide();" class="button button-secondary button-remove-image button-default <?php echo ( ! isset( $avada_options['avada_icon_image'] ) || '' === $avada_options['avada_icon_image'] ) ? 'hidden' : ''; ?>" value="<?php esc_attr_e( 'Remove Image', 'fusion-white-label-branding' ); ?> " />
				</div>
			</div>
		</div>
	</section>

	<section class="avada-db-card fusion-white-label-option">
		<div class="fusion-white-label-option-title">
			<?php // Translators: Avada. ?>
			<h2><?php printf( esc_html__( 'Dashicon Name for %s Menu Icon', 'fusion-white-label-branding' ), 'Avada' ); ?></h2>
			<?php /* translators: Fusion */ ?>
			<p class="description">
				<?php
				printf(
					/* Translators: %1$s: "Avada". %2$s: The link URL. */
					__( 'Replaces the image icon and default icon for %1$s logo in admin menu. <a target="_blank" href="%2$s">Click Here</a> to see all dashicons.', 'fusion-white-label-branding' ), // phpcs:ignore WordPress.Security.EscapeOutput
					'Avada',
					'https://developer.wordpress.org/resource/dashicons/'
				);
				?>
				</p>
		</div>
		<div class="fusion-white-label-option-input">
			<input type="text" id="admin_menu_dashicon" placeholder="dashicons-awards" name="fusion_branding[avada][admin_menu_dashicon]" class="regular-text" value="<?php echo ( isset( $avada_options['admin_menu_dashicon'] ) ) ? esc_attr( $avada_options['admin_menu_dashicon'] ) : ''; ?>" />
		</div>
	</section>

	<section class="avada-db-card fusion-white-label-option">
		<div class="fusion-white-label-option-title">
			<?php /* translators: Avada */ ?>
			<h2><?php printf( esc_html__( '%s Dashboard Welcome Section', 'fusion-white-label-branding' ), 'Avada' ); ?></h2>
			<?php /* translators: Avada */ ?>
			<p class="description"><?php printf( esc_html__( 'Controls the content of the %s dashboard welcome section.', 'fusion-white-label-branding' ), 'Avada' ); ?></p>
		</div>
		<div class="fusion-white-label-option-input">
			<input type="text" id="welcome_screen_title" name="fusion_branding[avada][welcome_screen_title]" class="regular-text" value="<?php echo ( isset( $avada_options['welcome_screen_title'] ) ) ? esc_attr( $avada_options['welcome_screen_title'] ) : ''; ?>" />
			<p class="description"><?php esc_html_e( 'Dashboard welcome section heading.', 'fusion-white-label-branding' ); ?></p>
			<br/>
			<textarea id="welcome_screen_about_text" name="fusion_branding[avada][welcome_screen_about_text]" class="regular-textarea"><?php echo ( isset( $avada_options['welcome_screen_about_text'] ) ) ? esc_attr( $avada_options['welcome_screen_about_text'] ) : ''; ?></textarea>
			<p class="description"><?php esc_html_e( 'Dashboard welcome section sub-heading.', 'fusion-white-label-branding' ); ?></p>
			<br/>
			<?php
			$content  = isset( $avada_options['welcome_screen_content'] ) ? $avada_options['welcome_screen_content'] : '';
			$settings = [
				'media_buttons' => true,
				'textarea_name' => 'fusion_branding[avada][welcome_screen_content]',
				'editor_height' => '200',
			];
			wp_editor( $content, 'welcome_panel_content', $settings );
			?>
			<p class="description"><?php esc_html_e( 'Dashboard welcome section content.', 'fusion-white-label-branding' ); ?></p>
		</div>
	</section>

	<section class="avada-db-card fusion-white-label-option">
		<div class="fusion-white-label-option-title">
			<?php /* translators: Avada */ ?>
			<h2><?php printf( esc_html__( 'Remove %s Dashboard Additional Resources Links', 'fusion-white-label-branding' ), 'Avada' ); ?></h2>
			<?php /* translators: Avada */ ?>
			<p class="description"><?php printf( esc_html__( 'Removes the additional resources sections from the %s dashboard.', 'fusion-white-label-branding' ), 'Avada' ); ?></p>
		</div>
		<div class="fusion-white-label-option-input">
			<div class="fusion-branding-option-field">
				<div class="fusion-form-radio-button-set ui-buttonset">
					<?php
					$remove_dashboard_additional_resources = '0';
					if ( isset( $avada_options['remove_dashboard_additional_resources'] ) ) {
						$remove_dashboard_additional_resources = $avada_options['remove_dashboard_additional_resources'];
					}
					?>
					<input type="hidden" class="button-set-value" value="<?php echo esc_attr( $remove_dashboard_additional_resources ); ?>" name="fusion_branding[avada][remove_dashboard_additional_resources]" id="remove_dashboard_additional_resources" />
					<a data-value="1" class="ui-button buttonset-item<?php echo ( $remove_dashboard_additional_resources ) ? ' ui-state-active' : ''; ?>" href="#"><?php esc_html_e( 'Yes', 'fusion-white-label-branding' ); ?></a>
					<a data-value="0" class="ui-button buttonset-item<?php echo ( ! $remove_dashboard_additional_resources ) ? ' ui-state-active' : ''; ?>" href="#"><?php esc_html_e( 'No', 'fusion-white-label-branding' ); ?></a>
				</div>
			</div>
		</div>
	</section>

	<section class="avada-db-card fusion-white-label-option">
		<div class="fusion-white-label-option-title">
			<?php /* translators: Avada */ ?>
			<h2><?php printf( esc_html__( 'Remove %s Admin Dashboard Footer', 'fusion-white-label-branding' ), 'Avada' ); ?></h2>
			<?php /* translators: Avada */ ?>
			<p class="description"><?php printf( esc_html__( 'Removes the %s admin dashboard footer.', 'fusion-white-label-branding' ), 'Avada' ); ?></p>
		</div>
		<div class="fusion-white-label-option-input">
			<div class="fusion-branding-option-field">
				<div class="fusion-form-radio-button-set ui-buttonset">
					<?php
					$remove_dashboard_footer = '0';
					if ( isset( $avada_options['remove_dashboard_footer'] ) ) {
						$remove_dashboard_footer = $avada_options['remove_dashboard_footer'];
					}
					?>
					<input type="hidden" class="button-set-value" value="<?php echo esc_attr( $remove_dashboard_footer ); ?>" name="fusion_branding[avada][remove_dashboard_footer]" id="remove_dashboard_footer" />
					<a data-value="1" class="ui-button buttonset-item<?php echo ( $remove_dashboard_footer ) ? ' ui-state-active' : ''; ?>" href="#"><?php esc_html_e( 'Yes', 'fusion-white-label-branding' ); ?></a>
					<a data-value="0" class="ui-button buttonset-item<?php echo ( ! $remove_dashboard_footer ) ? ' ui-state-active' : ''; ?>" href="#"><?php esc_html_e( 'No', 'fusion-white-label-branding' ); ?></a>
				</div>
			</div>
		</div>
	</section>

	<?php
	/**
	 * The section below is a WIP.
	 */
	?>
	<?php if ( false ) : // phpcs:ignore Generic.CodeAnalysis.UnconditionalIfStatement ?>
		<section class="avada-db-card fusion-white-label-option">
			<div class="fusion-white-label-option-title">
				<h2><?php esc_html_e( 'Change Social Media Links', 'fusion-white-label-branding' ); ?></h2>
				<p class="description"><?php esc_html_e( 'Change the social media links for the admin area. Set to "#" to remove the network.', 'fusion-white-label-branding' ); ?></p>
			</div>
			<div class="fusion-white-label-option-input">
				<?php
				$social_media = [
					'facebook'  => esc_html__( 'Facebook', 'fusion-white-label-branding' ),
					'twitter'   => esc_html__( 'Twitter', 'fusion-white-label-branding' ),
					'instagram' => esc_html__( 'Instagram', 'fusion-white-label-branding' ),
					'youtube'   => esc_html__( 'YouTube', 'fusion-white-label-branding' ),
				];

				$saved_social_media = isset( $avada_options['reset_social_media'] ) ? $avada_options['reset_social_media'] : [];
				foreach ( $social_media as $network => $title ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.OverrideProhibited
					$value = ( isset( $saved_social_media[ $network ] ) && '' !== $saved_social_media[ $network ] ) ? $saved_social_media[ $network ] : '';
					?>
					<p>
					<label for="reset_social_media<?php echo esc_attr( $network ); ?>">
					<input type="text" id="reset_social_media<?php echo esc_attr( $network ); ?>" placeholder="<?php echo esc_attr( $title ); ?>" name="fusion_branding[avada][reset_social_media][<?php echo esc_attr( $network ); ?>]" class="regular-text" value="<?php echo esc_attr( $value ); ?>" />
					<span><?php echo esc_html( $title ); ?></span>
					</label>
					</p>
					<?php
				}
				?>
			</div>
		</section>
	<?php endif; ?>

	<section class="avada-db-card fusion-white-label-option">
		<div class="fusion-white-label-option-title">
			<h2>
				<?php
				// translators: Avada Custom Branding.
				printf( esc_html__( '%s Menu Icon Image', 'fusion-white-label-branding' ), 'Avada Custom Branding' );
				?>
			</h2>
			<p class="description">
				<?php
				printf(
					/* Translators: %s: Avada Custom Branding. */
					__( 'Controls the menu icon image on %s admin menu and replaces the %s logo. <br/><strong>Recommended size:</strong> 40px x 40px.', 'fusion-white-label-branding' ), // phpcs:ignore WordPress.Security.EscapeOutput
					 'Avada Custom Branding', 'Avada'
				);
				?>
			</p>
		</div>
		<div class="fusion-white-label-option-input">
			<div class="fusion_white_label_branding_icon_image_preview branding-image-preview">
				<?php
				if ( ! empty( $avada_options['fusion_white_label_branding_icon_image'] ) ) {
					echo '<img src="' . esc_url( $avada_options['fusion_white_label_branding_icon_image'] ) . '" />';
				}
				?>
			</div>
			<div class="image-upload-container">
				<div class="upload-field-input">
					<input type="text" id="fusion_white_label_branding_icon_image" name="fusion_branding[avada][fusion_white_label_branding_icon_image]" class="image-field" value="<?php echo ( isset( $avada_options['fusion_white_label_branding_icon_image'] ) ) ? esc_attr( $avada_options['fusion_white_label_branding_icon_image'] ) : ''; ?>" />
				</div>
				<div class="upload-field-buttons">
					<?php // translators: Avada Custom Branding. ?>
					<input type="button" data-title="<?php printf( esc_attr__( '%s Menu Icon Image', 'fusion-white-label-branding' ), 'Avada Custom Branding' ); ?>" data-button-title="<?php esc_attr_e( 'Use Menu Icon', 'fusion-white-label-branding' ); ?>" data-image-id="fusion_white_label_branding_icon_image" class="button button-secondary button-upload-image button-default" value="<?php esc_attr_e( 'Upload Menu Icon Image', 'fusion-white-label-branding' ); ?>" />
					<input type="button" onclick="jQuery('#fusion_white_label_branding_icon_image').val('').trigger( 'change' ); jQuery( '.fusion_white_label_branding_icon_image_preview').html(''); jQuery( this ).hide();" class="button button-secondary button-remove-image button-default <?php echo ( ! isset( $avada_options['fusion_white_label_branding_icon_image'] ) || '' === $avada_options['fusion_white_label_branding_icon_image'] ) ? 'hidden' : ''; ?>" value="<?php esc_attr_e( 'Remove Image', 'fusion-white-label-branding' ); ?> " />
				</div>
			</div>
		</div>
	</section>

	<section class="avada-db-card fusion-white-label-option">
		<div class="fusion-white-label-option-title">
			<?php // Translators: Avada Custom Branding. ?>
			<h2><?php printf( esc_html__( 'Dashicon Name for %s Menu Icon', 'fusion-white-label-branding' ), 'Avada Custom Branding' ); ?></h2>
			<?php // translators: Avada Custom Branding. ?>
			<p class="description">
				<?php
				printf(
					/* Translators: %1$s: "Avada Custom Branding". %2$s: The link URL. */
					__( 'Replaces the image icon and default icon of the %1$s logo in admin menu.<br/> <a target="_blank" href="%2$s">Click Here</a> to see all dashicons.', 'fusion-white-label-branding' ), // phpcs:ignore WordPress.Security.EscapeOutput
					esc_html__( 'Avada Custom Branding', 'fusion-white-label-branding' ),
					'https://developer.wordpress.org/resource/dashicons/'
				);
				?>
				</p>
		</div>
		<div class="fusion-white-label-option-input">
			<input type="text" id="fusion_white_label_branding_menu_dashicon" placeholder="dashicons-nametag" name="fusion_branding[avada][fusion_white_label_branding_menu_dashicon]" class="regular-text" value="<?php echo ( isset( $avada_options['fusion_white_label_branding_menu_dashicon'] ) ) ? esc_attr( $avada_options['fusion_white_label_branding_menu_dashicon'] ) : ''; ?>" />
		</div>
	</section>

	<section class="avada-db-card avada-db-card-transparent">
		<input type="hidden" name="action" value="save_fusion_branding_settings">
		<input type="hidden" name="section" value="avada">
		<?php wp_nonce_field( 'fusion_branding_save_settings', 'fusion_branding_save_settings' ); ?>
		<input type="submit" class="button button-primary fusion-branding-save-settings" value="<?php esc_attr_e( 'Save Settings', 'fusion-white-label-branding' ); ?>" />
		<a onclick="return confirm('<?php esc_attr_e( 'Are you sure, you want to reset these settings?\n\nThis action can not be undone.', 'fusion-white-label-branding' ); ?>');" href="<?php echo esc_url_raw( wp_nonce_url( admin_url( 'admin-ajax.php?action=reset-branding-settings&section_id=avada' ) ) ); ?>" class="button button-secondary fusion-branding-reset-section-avada fusion-branding-reset-settings"><?php esc_attr_e( 'Reset Section', 'fusion-white-label-branding' ); ?></a>
		<a onclick="return confirm('<?php esc_attr_e( 'Are you sure, you want to reset all settings?\n\nThis action can not be undone.', 'fusion-white-label-branding' ); ?>');" href="<?php echo esc_url_raw( wp_nonce_url( admin_url( 'admin-ajax.php?action=reset-branding-settings&section_id=all' ) ) ); ?>" class="button button-secondary fusion-branding-reset-section-all fusion-branding-reset-settings"><?php esc_attr_e( 'Reset All', 'fusion-white-label-branding' ); ?></a>
	</section>
</form>
<?php Fusion_White_Label_Branding_Admin::get_admin_screens_footer(); ?>
