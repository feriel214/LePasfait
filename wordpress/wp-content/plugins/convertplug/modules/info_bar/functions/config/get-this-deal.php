<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

if ( function_exists( 'smile_framework_add_options' ) ) {
	smile_framework_add_options(
		'Convert_Plug_Smile_Info_Bars',
		'get_this_deal',
		array(
			'style_name'    => 'Get This Deal',
			'demo_url'      => CP_PLUGIN_URL . 'modules/info_bar/assets/demos/get_this_deal/get_this_deal.html',
			'demo_dir'      => plugin_dir_path( __FILE__ ) . '../../assets/demos/get_this_deal/get_this_deal.html',
			'img_url'       => CP_PLUGIN_URL . 'modules/info_bar/assets/demos/get_this_deal/get_this_deal.png',
			'customizer_js' => CP_PLUGIN_URL . 'modules/info_bar/assets/demos/get_this_deal/customizer.js',
			'category'      => 'All,Offers',
			'tags'          => 'image, cta, call to action',
			'options'       => array(),
		)
	);
}
