<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

if ( function_exists( 'smile_framework_add_options' ) ) {

	smile_framework_add_options(
		'Convert_Plug_Smile_Info_Bars',
		'countdown',
		array(
			'style_name'    => 'Count Down',
			'demo_url'      => CP_PLUGIN_URL . 'modules/info_bar/assets/demos/countdown/countdown.html',
			'demo_dir'      => plugin_dir_path( __FILE__ ) . '../../assets/demos/countdown/countdown.html',
			'img_url'       => CP_PLUGIN_URL . 'modules/info_bar/assets/demos/countdown/countdown.png',
			'customizer_js' => CP_PLUGIN_URL . 'modules/info_bar/assets/demos/countdown/customizer.js',
			'category'      => 'All,Optins',
			'tags'          => 'form,Countdown',
			'options'       => array(),
		)
	);
}
