<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

if ( function_exists( 'smile_framework_add_options' ) ) {
	smile_framework_add_options(
		'Convert_Plug_Smile_Info_Bars',
		'weekly_article',
		array(
			'style_name'    => 'Weekly Article',
			'demo_url'      => CP_PLUGIN_URL . 'modules/info_bar/assets/demos/weekly_article/weekly_article.html',
			'demo_dir'      => plugin_dir_path( __FILE__ ) . '../../assets/demos/weekly_article/weekly_article.html',
			'img_url'       => CP_PLUGIN_URL . 'modules/info_bar/assets/demos/weekly_article/weekly_article.png',
			'customizer_js' => CP_PLUGIN_URL . 'modules/info_bar/assets/demos/weekly_article/customizer.js',
			'category'      => 'All,Optins',
			'tags'          => 'form',
			'options'       => array(),
		)
	);
}
