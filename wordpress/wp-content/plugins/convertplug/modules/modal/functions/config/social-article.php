<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

if ( function_exists( 'smile_framework_add_options' ) ) {
	$style_arr = array(
		'style_name'    => 'Social Article',
		'demo_url'      => CP_PLUGIN_URL . 'modules/modal/assets/demos/social_article/social_article.html',
		'demo_dir'      => plugin_dir_path( __FILE__ ) . '../../assets/demos/social_article/social_article.html',
		'img_url'       => CP_PLUGIN_URL . 'modules/modal/assets/demos/social_article/social_article.png',
		'customizer_js' => CP_PLUGIN_URL . 'modules/modal/assets/demos/social_article/customizer.js',
		'category'      => 'All,Social',
		'tags'          => 'Social,Share,Facebook,Twitter,Google,Digg,Reddit,Pinterest,LinkedIn,Myspace,Blogger,Tumblr,StumbleUpon',
		'options'       => array(),
	);

	smile_framework_add_options( 'Convert_Plug_Smile_Modals', 'social_article', $style_arr );
}
