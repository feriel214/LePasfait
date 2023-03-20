<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

if ( function_exists( 'smile_framework_add_options' ) ) {
	smile_framework_add_options(
		'Convert_Plug_Smile_Info_Bars',
		'social_info_bar',
		array(
			'style_name'    => 'Social Info Bar',
			'demo_url'      => CP_PLUGIN_URL . 'modules/info_bar/assets/demos/social_info_bar/social_info_bar.html',
			'demo_dir'      => plugin_dir_path( __FILE__ ) . '../../assets/demos/social_info_bar/social_info_bar.html',
			'img_url'       => CP_PLUGIN_URL . 'modules/info_bar/assets/demos/social_info_bar/social_info_bar.png',
			'customizer_js' => CP_PLUGIN_URL . 'modules/info_bar/assets/demos/social_info_bar/customizer.js',
			'category'      => 'All,Social',
			'tags'          => 'Social,Share,Facebook,Twitter,Google,Digg,Reddit,Pinterest,LinkedIn,Myspace,Blogger,Tumblr,StumbleUpon',
			'options'       => array(),
		)
	);
}
