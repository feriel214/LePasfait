<?php
/**
 * ThemeREX Addons: Panel with installation wizard, Theme Options and Support info
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.48
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	die( '-1' );
}

// Define component's subfolder
if ( !defined('TRX_ADDONS_PLUGIN_THEME_PANEL') ) define('TRX_ADDONS_PLUGIN_THEME_PANEL', TRX_ADDONS_PLUGIN_COMPONENTS . 'theme-panel/');
if ( !defined('TRX_ADDONS_PLUGIN_IMPORTER') )    define('TRX_ADDONS_PLUGIN_IMPORTER', TRX_ADDONS_PLUGIN_THEME_PANEL . 'importer/');
if ( !defined('TRX_ADDONS_PLUGIN_INSTALLER') )   define('TRX_ADDONS_PLUGIN_INSTALLER', TRX_ADDONS_PLUGIN_THEME_PANEL . 'installer/');

// Add Admin menu item to show Theme panel
if (!function_exists('trx_addons_theme_panel_admin_menu')) {
	add_action( 'admin_menu', 'trx_addons_theme_panel_admin_menu' );
	function trx_addons_theme_panel_admin_menu() {
		add_menu_page(
			esc_html__('Theme Panel', 'trx_addons'),	//page_title
			esc_html__('Theme Panel', 'trx_addons'),	//menu_title
			'manage_options',							//capability
			'trx_addons_theme_panel',					//menu_slug
			'trx_addons_theme_panel_page_builder',		//callback
			'dashicons-welcome-widgets-menus',			//icon
			'4'											//menu position (after Dashboard)
		);
		$submenu = apply_filters('trx_addons_filter_add_theme_panel_pages', array(
			array(
				esc_html__('Theme Dashboard', 'trx_addons'),//page_title
				esc_html__('Theme Dashboard', 'trx_addons'),//menu_title
				'manage_options',							//capability
				'trx_addons_theme_panel',					//menu_slug
				'trx_addons_theme_panel_page_builder'		//callback
				)
			)
		);
		if (is_array($submenu)) {
			foreach($submenu as $item) {
				add_submenu_page(
					'trx_addons_theme_panel',			//parent menu slug
					$item[0],							//page_title
					$item[1],							//menu_title
					$item[2],							//capability
					$item[3],							//menu_slug
					$item[4]							//callback
				);
			}
		}
	}
}


// Load scripts and styles
if (!function_exists('trx_addons_theme_panel_load_scripts')) {
	add_action("admin_enqueue_scripts", 'trx_addons_theme_panel_load_scripts');
	function trx_addons_theme_panel_load_scripts() {
		if (isset($_REQUEST['page']) && $_REQUEST['page']=='trx_addons_theme_panel') {
			wp_enqueue_style( 'trx_addons-theme_panel', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_THEME_PANEL . 'theme-panel.css'), array(), null );
			wp_enqueue_script( 'trx_addons-theme_panel', trx_addons_get_file_url(TRX_ADDONS_PLUGIN_THEME_PANEL . 'theme-panel.js'), array('jquery'), null, true );
		}
	}
}

// Return true if current screen need to load options scripts and styles
if ( !function_exists( 'trx_addons_theme_panel_need_options' ) ) {
	add_filter('trx_addons_filter_need_options', 'trx_addons_theme_panel_need_options');
	function trx_addons_theme_panel_need_options($need = false) {
		if (!$need) {
			// If current screen is 'Theme Panel'
			$need = isset($_REQUEST['page']) && $_REQUEST['page']=='trx_addons_theme_panel';
		}
		return $need;
	}
}

// Check 'theme activated' status
if ( !function_exists( 'trx_addons_is_theme_activated' ) ) {
	function trx_addons_is_theme_activated() {
		return get_option( sprintf( 'trx_addons_theme_%s_activated', get_option( 'template' ) ) ) == 1
			&& get_option( sprintf( 'purchase_code_%s', get_option( 'template' ) ) ) != '';
	}
}

// Set 'theme activated' status
if ( !function_exists( 'trx_addons_set_theme_activated' ) ) {
	function trx_addons_set_theme_activated($code='', $pro_key='') {
		update_option( sprintf( 'trx_addons_theme_%s_activated', get_option( 'template' ) ), 1);
		if ( !empty($code) ) {
			update_option( sprintf( 'purchase_code_%s', get_option( 'template' ) ), $code );
			if ( substr($pro_key, 0, 4) == 'env_' ) {
				update_option( sprintf( 'envato_purchase_code_%s', get_option( 'template' ) ), $code );
			}
		}
	}
}

// Return 'theme activated' status
if ( !function_exists( 'trx_addons_get_theme_activated_status' ) ) {
	function trx_addons_get_theme_activated_status() {
		return trx_addons_is_theme_activated() ? 'active' : 'inactive';
	}
}

// Return theme activation code
if ( !function_exists( 'trx_addons_get_theme_activation_code' ) ) {
	function trx_addons_get_theme_activation_code() {
		return get_option( sprintf( 'trx_addons_theme_%s_activated', get_option( 'template' ) ) ) == 1
				? get_option( sprintf( 'purchase_code_%s', get_option( 'template' ) ) )
				: '';
	}
}

// Activate theme
if ( !function_exists( 'trx_addons_theme_panel_activate_theme' ) ) {
	add_action('init', 'trx_addons_theme_panel_activate_theme', 9);
	function trx_addons_theme_panel_activate_theme() {
		if (is_admin() && isset($_REQUEST['page']) && $_REQUEST['page']=='trx_addons_theme_panel') {
			// If submit form with activation code
			$nonce = trx_addons_get_value_gp('trx_addons_nonce');
			$code  = trx_addons_get_value_gp('trx_addons_activate_theme_code');
			if ( !empty( $nonce ) ) {
				// Check nonce
				if ( !wp_verify_nonce( $nonce, admin_url() ) ) {
					trx_addons_set_admin_message(__('Security code is invalid! Theme is not activated!', 'trx_addons'), 'error');

				} else if ( empty( $code ) ) {
					trx_addons_set_admin_message(__('Please, specify the purchase code!', 'trx_addons'), 'error');

				// Check code
				} else {
					$theme_info  = trx_addons_get_theme_info(false);
					$upgrade_url = sprintf(
						'http://upgrade.themerex.net/upgrade.php?key=%1$s&src=%2$s&theme_slug=%3$s&theme_name=%4$s&action=check',
						urlencode( $code ),
						urlencode( $theme_info['theme_pro_key'] ),
						urlencode( $theme_info['theme_slug'] ),
						urlencode( $theme_info['theme_name'] )
					);
					$result = trx_addons_fgc( $upgrade_url );
					if ( substr( $result, 0, 5 ) == 'a:2:{' && substr( $result, -1, 1 ) == '}' ) {
						try {
							$result = trx_addons_unserialize( $result );
						} catch ( Exception $e ) {
							$result = array(
								'error' => '',
								'data' => -1
							);
						}
						if ( $result['data'] === 1 ) {
							trx_addons_set_theme_activated($code, $theme_info['theme_pro_key']);
							trx_addons_set_admin_message(__('Congratulations! Your theme is activated successfully.', 'trx_addons'), 'success');
						} elseif ( $result['data'] === -1 ) {
							trx_addons_set_admin_message(__('Bad server answer! Theme is not activated!', 'trx_addons'), 'error');
						} else {
							trx_addons_set_admin_message(__('Your purchase code is invalid! Theme is not activated!', 'trx_addons'), 'error');
						}
					}
				}
			}
		}
	}
}

// Build Theme panel page
if (!function_exists('trx_addons_theme_panel_page_builder')) {
	function trx_addons_theme_panel_page_builder() {
		$tabs   = apply_filters('trx_addons_filter_theme_panel_tabs', array(
								'general' => esc_html__( 'General', 'trx_addons' ),
								'plugins' => esc_html__( 'Plugins', 'trx_addons' ),
								));
		?>
		<div class="trx_addons_theme_panel">

			<?php do_action( 'trx_addons_action_theme_panel_start' ); ?>

			<div class="trx_addons_result">
				<?php
				$result = trx_addons_get_admin_message();
				if (!empty($result['error'])) {
					?><div class="error"><p><?php echo wp_kses_data($result['error']); ?></p></div><?php
				} else if (!empty($result['success'])) {
					?><div class="updated"><p><?php echo wp_kses_data($result['success']); ?></p></div><?php
				}
				?>
			</div>

			<?php do_action( 'trx_addons_action_theme_panel_before_tabs' ); ?>

			<div class="trx_addons_tabs trx_addons_tabs_theme_panel">
				<ul>
					<?php
					foreach($tabs as $tab_id => $tab_title) {
						?><li><a href="#trx_addons_theme_panel_section_<?php echo esc_attr($tab_id); ?>"><?php echo esc_html( $tab_title ); ?></a></li><?php
					}
					?>
				</ul>
				<?php
					$theme_info = trx_addons_get_theme_info();
					foreach($tabs as $tab_id => $tab_title) {
						do_action('trx_addons_action_theme_panel_section', $tab_id, $theme_info);
					}
				?>
			</div>

			<?php do_action( 'trx_addons_action_theme_panel_after_tabs' ); ?>

			<?php require_once trx_addons_get_file_dir(TRX_ADDONS_PLUGIN_THEME_PANEL . 'templates/footer.php');	?>

			<?php do_action( 'trx_addons_action_theme_panel_end' ); ?>

		</div>
		<?php		
	}
}


// Display 'General' section
if ( !function_exists( 'trx_addons_theme_panel_section_general' ) ) {
	add_action('trx_addons_action_theme_panel_section', 'trx_addons_theme_panel_section_general', 10, 2);
	function trx_addons_theme_panel_section_general($tab_id, $theme_info) {
		if ($tab_id !== 'general') return;
		$theme_status = trx_addons_get_theme_activated_status();
		?>
		<div id="trx_addons_theme_panel_section_<?php echo esc_attr($tab_id); ?>" class="trx_addons_tabs_section">

			<?php do_action('trx_addons_action_theme_panel_section_start', $tab_id, $theme_info); ?>

			<div class="trx_addons_theme_panel_theme_<?php echo esc_attr($theme_status); ?>">

				<?php do_action('trx_addons_action_theme_panel_before_section_title', $tab_id, $theme_info); ?>
	
				<h1 class="trx_addons_theme_panel_section_title">
					<?php
					echo esc_html(
						sprintf(
							// Translators: Add theme name and version to the 'Welcome' message
							__( 'Welcome to %1$s v.%2$s', 'trx_addons' ),
							$theme_info['theme_name'],
							$theme_info['theme_version']
						)
					);
					?>
					<span class="trx_addons_theme_panel_section_title_label_<?php echo esc_attr($theme_status); ?>"><?php
						if ($theme_status == 'active') {
							esc_html_e('Activated', 'trx_addons');
						} else {
							esc_html_e('Not activated', 'trx_addons');
						}
					?></span>
				</h1><?php

				do_action('trx_addons_action_theme_panel_after_section_title', $tab_id, $theme_info);

				if ($theme_status == 'active') {
					// Theme is active
					$steps = apply_filters('trx_addons_filter_theme_panel_steps', array(
											'plugins' => esc_html__('Install and activate recommended plugins.', 'trx_addons'),
											));
					?><div class="trx_addons_theme_panel_section_description">
						<p><?php esc_html_e('Thank you for your awesome taste and choosing our theme!', 'trx_addons'); ?></p>
						<p><?php esc_html_e('Before you begin, please do the following steps:', 'trx_addons'); ?></p>
						<ol>
							<?php
							if (is_array($steps)) {
								foreach($steps as $step) {
									?>
									<li><?php echo esc_html($step); ?></li>
									<?php
								}
							}
							?>
						</ol>
					</div>
					<?php

				} else {
					// Theme is not active
					?><div class="trx_addons_theme_panel_section_description">
						<p><?php esc_html_e('Thank you for your awesome taste and choosing our theme!', 'trx_addons'); ?></p>
						<p><?php esc_html_e('Please activate your copy of the theme in order to get access to plugins, demo content, support and updates.', 'trx_addons'); ?></p>
					</div><?php
					do_action('trx_addons_action_theme_panel_activation_form', $tab_id, $theme_info);
				}

				do_action('trx_addons_action_theme_panel_after_section_description', $tab_id, $theme_info);

				if ($theme_status == 'active') {
					?><div class="trx_addons_theme_panel_buttons"><a href="#" class="trx_addons_theme_panel_next_step button button-action"><?php esc_html_e('Start Setup', 'trx_addons'); ?></a></div><?php
				}

			?></div><?php

			// Attention! This is inline-blocks and no spaces allow
			if ($theme_status == 'active' && current_user_can('switch_themes')) {
				?><div class="trx_addons_theme_panel_sys_info">
					<table class="trx_addons_theme_panel_table" border="0" cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<th class="trx_addons_theme_panel_info_param"><?php esc_html_e('System Check', 'trx_addons'); ?></th>
							<th class="trx_addons_theme_panel_info_value"><?php esc_html_e('Current', 'trx_addons'); ?></th>
							<th class="trx_addons_theme_panel_info_advise"><?php esc_html_e('Suggested', 'trx_addons'); ?></th>
						</tr>
						<?php
						$sys_info = trx_addons_get_sys_info();
						foreach ($sys_info as $k=>$item) {
							?>
							<tr>
								<td class="trx_addons_theme_panel_info_param"><?php echo esc_html($item['title']); ?></td>
								<td class="trx_addons_theme_panel_info_value"><?php echo esc_html($item['value']); ?></td>
								<td class="trx_addons_theme_panel_info_advise"><?php echo esc_html($item['recommended']); ?></td>
							</tr>
							<?php
						}
						?>
					</table>
				</div><?php
			}

			do_action('trx_addons_action_theme_panel_section_end', $tab_id, $theme_info);

		?></div><?php
	}
}


// Display the theme activation form
if ( !function_exists( 'trx_addons_theme_panel_activation_form' ) ) {
	add_action('trx_addons_action_theme_panel_activation_form', 'trx_addons_theme_panel_activation_form');
	function trx_addons_theme_panel_activation_form() {
		?>
		<form action="<?php echo esc_url(get_admin_url(null, 'admin.php?page=trx_addons_theme_panel')); ?>" class="trx_addons_theme_panel_section_form" name="trx_addons_theme_panel_activate_form" method="post">
			<input type="hidden" name="trx_addons_nonce" value="<?php echo esc_attr(wp_create_nonce(admin_url())); ?>" />
			<h3 class="trx_addons_theme_panel_section_form_title"><?php esc_html_e('Enter Your Purchase Code:', 'trx_addons'); ?></h3>
			<div class="trx_addons_theme_panel_section_form_field">
				<input type="text" name="trx_addons_activate_theme_code"><input type="submit" value="<?php esc_attr_e('Submit', 'trx_addons'); ?>">
			</div>
			<div class="trx_addons_theme_panel_section_form_field_description">
				<?php
				echo esc_html__( "Can't find the purchase code?", 'trx_addons' )
							. ' '
							. '<a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-" target="_blank">'
								. esc_html__('Follow this guide', 'trx_addons')
							. '</a>';
				?>
			</div>
		</form>
		<?php
	}
}


// Display 'Plugins' section
if ( !function_exists( 'trx_addons_theme_panel_section_plugins' ) ) {
	add_action('trx_addons_action_theme_panel_section', 'trx_addons_theme_panel_section_plugins', 10, 2);
	function trx_addons_theme_panel_section_plugins($tab_id, $theme_info) {
		if ($tab_id !== 'plugins') return;
		?>
		<div id="trx_addons_theme_panel_section_<?php echo esc_attr($tab_id); ?>" class="trx_addons_tabs_section">
			
			<?php
			do_action('trx_addons_action_theme_panel_section_start', $tab_id, $theme_info);

			if ( trx_addons_is_theme_activated() ) {
				?>
				<div class="trx_addons_theme_panel_plugins_installer">

					<?php do_action('trx_addons_action_theme_panel_before_section_title', $tab_id, $theme_info); ?>
		
					<h1 class="trx_addons_theme_panel_section_title">
						<?php esc_html_e( 'Plugins', 'trx_addons' ); ?>
					</h1>

					<?php do_action('trx_addons_action_theme_panel_after_section_title', $tab_id, $theme_info); ?>

					<div class="trx_addons_theme_panel_section_info">
						<p><?php echo wp_kses_data( __( "Install and activate theme-related plugins. Select only those plugins that you're planning to use.", 'trx_addons' ) ); ?></p>
						<p><?php echo wp_kses_data( __( 'You can also install plugins via "Appearance - Install Plugins"', 'trx_addons' ) ); ?></p>
						<p class="trx_addons_theme_panel_section_info_notice"><b><?php esc_html_e('Attention!', 'trx_addons'); ?></b> <?php echo wp_kses_data( __( "Sometimes, the activation of some plugins interferes with the process of other plugins' installation. If a plugin is still on the 'Activating' stage after 1 minute, just reload the page (by pressing F5) and then switch to the 'Plugins' tab; there you should check the required plugins that remained uninstalled and proceed with the installation ('Install & Activate' button below the list of plugins)", 'trx_addons' ) ); ?></p>
					</div>

					<?php do_action('trx_addons_action_theme_panel_before_section_buttons', $tab_id, $theme_info); ?>

					<div class="trx_addons_theme_panel_plugins_buttons">
						<a href="#" class="trx_addons_theme_panel_plugins_button_select button"><?php esc_html_e('Select all', 'trx_addons'); ?></a>
						<a href="#" class="trx_addons_theme_panel_plugins_button_deselect button"><?php esc_html_e('Deselect all', 'trx_addons'); ?></a>
					</div><?php

					do_action('trx_addons_action_theme_panel_before_list_items', $tab_id, $theme_info);

					// List of plugins
					?>
					<ul class="trx_addons_theme_panel_plugins_list"><?php
						if ( is_array( $theme_info['theme_plugins'] ) ) {
							foreach ($theme_info['theme_plugins'] as $plugin_slug => $plugin_data) {
								$plugin_state = trx_addons_plugins_installer_check_plugin_state( $plugin_slug );
								$plugin_link = trx_addons_plugins_installer_get_link( $plugin_slug, $plugin_state );
								?><li class="trx_addons_theme_panel_plugins_list_item">
									<a href="<?php echo esc_url($plugin_link); ?>"
										data-slug="<?php echo esc_attr( $plugin_slug ); ?>"
										data-name="<?php echo esc_attr( $plugin_slug ); ?>"
										data-state="<?php echo esc_attr( $plugin_state ); ?>"
										data-activate-nonce="<?php echo esc_url(trx_addons_plugins_installer_get_link( $plugin_slug, 'activate' )); ?>"
										data-install-progress="<?php esc_attr_e( 'Installing ...', 'trx_addons' ); ?>"
										data-activate-progress="<?php esc_attr_e( 'Activating ...', 'trx_addons' ); ?>"
										data-activate-label="<?php esc_attr_e( 'Not activated', 'trx_addons' ); ?>"
										data-deactivate-label="<?php esc_attr_e( 'Active', 'trx_addons' ); ?>"
									>
										<?php echo esc_html($plugin_data[ 'name' ]); ?>
										<span>
											<?php
												if ($plugin_state == 'install') {
													esc_html_e('Not installed', 'trx_addons');
												} elseif ($plugin_state == 'activate') {
													esc_html_e('Not activated', 'trx_addons');
												} else {
													esc_html_e('Active', 'trx_addons');
												}
											?>
										</span>
									</a>
								</li><?php
							}
						}
					?></ul>

					<?php do_action('trx_addons_action_theme_panel_after_list_items', $tab_id, $theme_info); ?>

					<div class="trx_addons_theme_panel_plugins_buttons">
						<a href="#" class="trx_addons_theme_panel_plugins_install button button-action" disabled="disabled"><?php esc_html_e('Install & Activate', 'trx_addons'); ?></a>
					</div>
					
				</div>

				<?php
				do_action('trx_addons_action_theme_panel_after_section_data', $tab_id, $theme_info);

			} else {
				?>
				<div class="error"><p>
					<?php esc_html_e( 'Activate your theme in order to be able to install theme-specific plugins.', 'trx_addons' ); ?>
				</p></div>
				<?php
			}
			
			do_action('trx_addons_action_theme_panel_section_end', $tab_id, $theme_info);
			?>
		</div>
		<?php
	}
}


// Display buttons after the section's data
if (!function_exists('trx_addons_theme_panel_after_section_data')) {
	add_action('trx_addons_action_theme_panel_after_section_data', 'trx_addons_theme_panel_after_section_data', 10, 2);
	function trx_addons_theme_panel_after_section_data($tab_id, $theme_info) {
		?>
		<div class="trx_addons_theme_panel_buttons">
			<a href="#" class="trx_addons_theme_panel_prev_step button button-primary"><?php esc_html_e('Go Back', 'trx_addons'); ?></a>
			<a href="#" class="trx_addons_theme_panel_next_step button button-action"><?php esc_html_e('Next', 'trx_addons'); ?></a>
		</div>
		<?php
	}
}


// Include parts
//----------------------------------------------------

// Import demo data
if (!function_exists('trx_addons_theme_panel_load_impoter')) {
	add_action( 'after_setup_theme', 'trx_addons_theme_panel_load_impoter' );
	function trx_addons_theme_panel_load_impoter() {
		if (is_admin() && current_user_can('import')) {
			require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_IMPORTER . 'class.importer.php';
			new trx_addons_demo_data_importer();
		}
	}
}

// Plugins installer
require_once TRX_ADDONS_PLUGIN_DIR . TRX_ADDONS_PLUGIN_INSTALLER . 'installer.php';
