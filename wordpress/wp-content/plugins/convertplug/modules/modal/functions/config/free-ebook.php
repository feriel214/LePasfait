<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

if ( function_exists( 'smile_framework_add_options' ) ) {
	$style_arr = array(
		'style_name'    => 'Free Ebook',
		'demo_url'      => CP_PLUGIN_URL . 'modules/modal/assets/demos/free_ebook/free_ebook.html',
		'demo_dir'      => plugin_dir_path( __FILE__ ) . '../../assets/demos/free_ebook/free_ebook.html',
		'img_url'       => CP_PLUGIN_URL . 'modules/modal/assets/demos/free_ebook/free_ebook.png',
		'customizer_js' => CP_PLUGIN_URL . 'modules/modal/assets/demos/free_ebook/customizer.js',
		'category'      => 'All,Exit Intent',
		'tags'          => 'Ebook,Download,Freebie,Case Study,Image,Free,List,Bullets,Button',
		'options'       => array(),
	);

	smile_framework_add_options( 'Convert_Plug_Smile_Modals', 'free_ebook', $style_arr );
}
