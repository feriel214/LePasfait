<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

if ( function_exists( 'smile_framework_add_options' ) ) {
	$style_arr = array(
		'style_name'    => 'Social Fly_In',
		'demo_url'      => CP_PLUGIN_URL . 'modules/slide_in/assets/demos/social_fly_in/social_fly_in.html',
		'demo_dir'      => plugin_dir_path( __FILE__ ) . '../../assets/demos/social_fly_in/social_fly_in.html',
		'img_url'       => CP_PLUGIN_URL . 'modules/slide_in/assets/demos/social_fly_in/social_fly_in.png',
		'customizer_js' => CP_PLUGIN_URL . 'modules/slide_in/assets/demos/social_fly_in/customizer.js',
		'category'      => 'All,Social',
		'tags'          => 'Social,Share,Facebook,Twitter,Google,Digg,Reddit,Pinterest,LinkedIn,Myspace,Blogger,Tumblr,StumbleUpon',
		'options'       => array(),
	);
	smile_framework_add_options( 'Convert_Plug_Smile_Slide_Ins', 'social_fly_in', $style_arr );
}
