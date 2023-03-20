<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

if ( function_exists( 'smile_framework_add_options' ) ) {
	smile_framework_add_options(
		'Convert_Plug_Smile_Info_Bars',
		'image_preview',
		array(
			'style_name'    => 'Image Preview',
			'demo_url'      => CP_PLUGIN_URL . 'modules/info_bar/assets/demos/image_preview/image_preview.html',
			'demo_dir'      => plugin_dir_path( __FILE__ ) . '../../assets/demos/image_preview/image_preview.html',
			'img_url'       => CP_PLUGIN_URL . 'modules/info_bar/assets/demos/image_preview/image_preview.png',
			'customizer_js' => CP_PLUGIN_URL . 'modules/info_bar/assets/demos/image_preview/customizer.js',
			'category'      => 'All,Offers',
			'tags'          => 'image, cta, call to action',
			'options'       => array(),
		)
	);
}
