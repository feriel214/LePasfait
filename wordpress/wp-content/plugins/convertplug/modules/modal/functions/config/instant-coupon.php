<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

if ( function_exists( 'smile_framework_add_options' ) ) {
	$style_arr = array(
		'style_name'    => 'Instant Coupon',
		'demo_url'      => CP_PLUGIN_URL . 'modules/modal/assets/demos/instant_coupon/instant_coupon.html',
		'demo_dir'      => plugin_dir_path( __FILE__ ) . '../../assets/demos/instant_coupon/instant_coupon.html',
		'img_url'       => CP_PLUGIN_URL . 'modules/modal/assets/demos/instant_coupon/instant_coupon.png',
		'customizer_js' => CP_PLUGIN_URL . 'modules/modal/assets/demos/instant_coupon/customizer.js',
		'category'      => 'All,Optins,Offers,Exit Intent',
		'tags'          => 'Sale,Offer,Discount,Coupon,Optin,Email,Tilt,Bold',
		'options'       => array(),
	);

	smile_framework_add_options( 'Convert_Plug_Smile_Modals', 'instant_coupon', $style_arr );
}
