<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

if ( function_exists( 'smile_framework_add_options' ) ) {
	$style_arr = array(
		'style_name'    => 'Jugaad',
		'demo_url'      => CP_PLUGIN_URL . 'modules/modal/assets/demos/jugaad/jugaad.html',
		'demo_dir'      => plugin_dir_path( __FILE__ ) . '../../assets/demos/jugaad/jugaad.html',
		'img_url'       => CP_PLUGIN_URL . 'modules/modal/assets/demos/jugaad/jugaad.png',
		'customizer_js' => CP_PLUGIN_URL . 'modules/modal/assets/demos/jugaad/customizer.js',
		'category'      => 'All,Optins,Offers,Exit Intent,Updates',
		'tags'          => 'Jugaad,Custom,Easy,Offer,Coupon,Optin,Email',
		'options'       => array(),
	);

	smile_framework_add_options( 'Convert_Plug_Smile_Modals', 'jugaad', $style_arr );
}
