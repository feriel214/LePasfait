<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

if ( function_exists( 'smile_framework_add_options' ) ) {
	$style_arr = array(
		'style_name'    => 'Special Offer',
		'demo_url'      => CP_PLUGIN_URL . 'modules/modal/assets/demos/special_offer/special_offer.html',
		'demo_dir'      => plugin_dir_path( __FILE__ ) . '../../assets/demos/special_offer/special_offer.html',
		'img_url'       => CP_PLUGIN_URL . 'modules/modal/assets/demos/special_offer/special_offer.png',
		'customizer_js' => CP_PLUGIN_URL . 'modules/modal/assets/demos/special_offer/customizer.js',
		'category'      => 'All,Optins,Offers',
		'tags'          => 'Sale,Offer,Discount,Commerce,Coupon,Optin,Email,Subscribe',
		'options'       => array(),
	);
	smile_framework_add_options( 'Convert_Plug_Smile_Modals', 'special_offer', $style_arr );
}
