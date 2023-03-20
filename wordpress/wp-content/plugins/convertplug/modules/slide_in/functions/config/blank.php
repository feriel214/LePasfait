<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

if ( function_exists( 'smile_framework_add_options' ) ) {
	smile_framework_add_options(
		'Convert_Plug_Smile_Slide_Ins',
		'blank',
		array(
			'style_name'    => 'Blank',
			'demo_url'      => CP_PLUGIN_URL . 'modules/slide_in/assets/demos/blank/blank.html',
			'demo_dir'      => plugin_dir_path( __FILE__ ) . '../../assets/demos/blank/blank.html',
			'img_url'       => CP_PLUGIN_URL . 'modules/slide_in/assets/demos/blank/blank.png',
			'customizer_js' => CP_PLUGIN_URL . 'modules/slide_in/assets/demos/blank/customizer.js',
			'category'      => 'All,offers',
			'tags'          => 'offers,default,special,discount,mascot,blank',
			'options'       => array(),
		)
	);
}
