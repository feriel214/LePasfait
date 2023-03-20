<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

if ( function_exists( 'smile_framework_add_options' ) ) {

	$style_arr = array(
		'style_name'    => 'Optin to Win',
		'demo_url'      => CP_PLUGIN_URL . 'modules/modal/assets/demos/optin_to_win/optin_to_win.html',
		'demo_dir'      => plugin_dir_path( __FILE__ ) . '../../assets/demos/optin_to_win/optin_to_win.html',
		'img_url'       => CP_PLUGIN_URL . 'modules/modal/assets/demos/optin_to_win/optin_to_win.png',
		'customizer_js' => CP_PLUGIN_URL . 'modules/modal/assets/demos/optin_to_win/customizer.js',
		'category'      => 'All,Optins',
		'tags'          => 'Ebook,Download,Freebie,Case Study,Image,Free,Optin,Email,Subscribe',
		'options'       => array(),
	);

	smile_framework_add_options( 'Convert_Plug_Smile_Modals', 'optin_to_win', $style_arr );
}
