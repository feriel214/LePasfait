<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

if ( function_exists( 'smile_framework_add_options' ) ) {
	smile_framework_add_options(
		'Convert_Plug_Smile_Info_Bars',
		'blank',
		array(
			'style_name'    => 'Blank',
			'demo_url'      => CP_PLUGIN_URL . 'modules/info_bar/assets/demos/blank/blank.html',
			'demo_dir'      => plugin_dir_path( __FILE__ ) . '../../assets/demos/blank/blank.html',
			'img_url'       => CP_PLUGIN_URL . 'modules/info_bar/assets/demos/blank/blank.png',
			'customizer_js' => CP_PLUGIN_URL . 'modules/info_bar/assets/demos/blank/customizer.js',
			'category'      => 'All,Offers',
			'tags'          => 'tshirt,t-shirt,subscribe,join,offers',
			'options'       => array(),
		)
	);
}
