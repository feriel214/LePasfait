<?php

/*
  Plugin Name: 3CX Live Chat
  Plugin URI: https://www.3cx.com/wp-live-chat/
  Description: The easiest to use website live chat plugin. Let your visitors chat with you and increase sales conversion rates with 3CX Live Chat.
  Version: 10.0.4
  Author: 3CX
  Author URI: https://www.3cx.com/wp-live-chat/
  Domain Path: /languages
  License: GPLv2 or later
  License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

// If this file is called directly, abort.
if (!defined('WPINC')) {
  die;
}

require plugin_dir_path(__FILE__) . 'config.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wplc-plugin-activator.php
 */
function activate_wplc_plugin()
{
  if (!current_user_can('activate_plugins')) {
    return;
  }
  require_once plugin_dir_path(__FILE__) . 'includes/class-wplc-plugin-activator.php';
  wplc_Plugin_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wplc-plugin-deactivator.php
 */
function deactivate_wplc_plugin()
{
  if (!current_user_can('activate_plugins')) {
    return;
  }
  $plugin = isset($_REQUEST['plugin']) ? $_REQUEST['plugin'] : '';
  check_admin_referer("deactivate-plugin_{$plugin}");
  require_once plugin_dir_path(__FILE__) . 'includes/class-wplc-plugin-deactivator.php';
  wplc_Plugin_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_wplc_plugin');
register_deactivation_hook(__FILE__, 'deactivate_wplc_plugin');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-wplc-plugin.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    10.0.0
 */
function run_wplc_plugin()
{

  $plugin = new wplc_Plugin();
  $plugin->run();
}
run_wplc_plugin();
