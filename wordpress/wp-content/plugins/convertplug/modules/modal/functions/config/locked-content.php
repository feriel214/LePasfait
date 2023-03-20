<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

if ( function_exists( 'smile_framework_add_options' ) ) {
	$style_arr = array(
		'style_name'    => 'Locked Content',
		'demo_url'      => CP_PLUGIN_URL . 'modules/modal/assets/demos/locked_content/locked_content.html',
		'demo_dir'      => plugin_dir_path( __FILE__ ) . '../../assets/demos/locked_content/locked_content.html',
		'img_url'       => CP_PLUGIN_URL . 'modules/modal/assets/demos/locked_content/locked_content.png',
		'customizer_js' => CP_PLUGIN_URL . 'modules/modal/assets/demos/locked_content/customizer.js',
		'category'      => 'All,Optins',
		'tags'          => 'Optin,Email,Locked,Premium,Access,Close',
		'options'       => array(),
	);
	smile_framework_add_options( 'Convert_Plug_Smile_Modals', 'locked_content', $style_arr );
}
