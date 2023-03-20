<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

if ( function_exists( 'smile_framework_add_options' ) ) {
	$style_arr = array(
		'style_name'    => 'Countdown',
		'demo_url'      => CP_PLUGIN_URL . 'modules/modal/assets/demos/countdown/countdown.html',
		'demo_dir'      => plugin_dir_path( __FILE__ ) . '../../assets/demos/countdown/countdown.html',
		'img_url'       => CP_PLUGIN_URL . 'modules/modal/assets/demos/countdown/countdown.png',
		'customizer_js' => CP_PLUGIN_URL . 'modules/modal/assets/demos/countdown/customizer.js',
		'category'      => 'All,Offers,Updates',
		'tags'          => 'Countdown,Offer,Update,Hurry,Limited,Time',
		'options'       => array(),
	);

	smile_framework_add_options( 'Convert_Plug_Smile_Modals', 'countdown', $style_arr );
}
