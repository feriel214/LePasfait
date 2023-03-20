<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

if ( function_exists( 'smile_framework_add_options' ) ) {
	$style_arr = array(
		'style_name'    => 'Flat Discount',
		'demo_url'      => CP_PLUGIN_URL . 'modules/modal/assets/demos/flat_discount/flat_discount.html',
		'demo_dir'      => plugin_dir_path( __FILE__ ) . '../../assets/demos/flat_discount/flat_discount.html',
		'img_url'       => CP_PLUGIN_URL . 'modules/modal/assets/demos/flat_discount/flat_discount.png',
		'customizer_js' => CP_PLUGIN_URL . 'modules/modal/assets/demos/flat_discount/customizer.js',
		'category'      => 'All,Optins,Offers,Exit Intent',
		'tags'          => 'Sale,Offer,Discount, Commerce,Coupon,Day,Optin,Email',
		'options'       => array(),
	);

	smile_framework_add_options( 'Convert_Plug_Smile_Modals', 'flat_discount', $style_arr );
}
