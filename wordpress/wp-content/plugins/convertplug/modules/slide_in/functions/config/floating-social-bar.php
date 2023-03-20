<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

if ( function_exists( 'smile_framework_add_options' ) ) {
	smile_framework_add_options(
		'Convert_Plug_Smile_Slide_Ins',
		'floating_social_bar',
		array(
			'style_name'    => 'Floating Social Bar',
			'demo_url'      => CP_PLUGIN_URL . 'modules/slide_in/assets/demos/floating_social_bar/floating_social_bar.html',
			'demo_dir'      => plugin_dir_path( __FILE__ ) . '../../assets/demos/floating_social_bar/floating_social_bar.html',
			'img_url'       => CP_PLUGIN_URL . 'modules/slide_in/assets/demos/floating_social_bar/floating_social_bar.png',
			'customizer_js' => CP_PLUGIN_URL . 'modules/slide_in/assets/demos/floating_social_bar/customizer.js',
			'category'      => 'All,Social',
			'tags'          => 'Social,Share,Facebook,Twitter,Google,Digg,Reddit,Pinterest,LinkedIn,Myspace,Blogger,Tumblr,StumbleUpon',
			'options'       => array(),
		)
	);
}
