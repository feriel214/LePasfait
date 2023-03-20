<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

if ( function_exists( 'smile_framework_add_options' ) ) {
	$style_arr = array(
		'style_name'    => 'Direct Download',
		'demo_url'      => CP_PLUGIN_URL . 'modules/modal/assets/demos/direct_download/direct_download.html',
		'demo_dir'      => plugin_dir_path( __FILE__ ) . '../../assets/demos/direct_download/direct_download.html',
		'img_url'       => CP_PLUGIN_URL . 'modules/modal/assets/demos/direct_download/direct_download.png',
		'customizer_js' => CP_PLUGIN_URL . 'modules/modal/assets/demos/direct_download/customizer.js',
		'category'      => 'All,Exit Intent',
		'tags'          => 'Ebook,Download,Freebie,Case Study,Image,Button',
		'options'       => array(),
	);
	smile_framework_add_options( 'Convert_Plug_Smile_Modals', 'direct_download', $style_arr );
}
