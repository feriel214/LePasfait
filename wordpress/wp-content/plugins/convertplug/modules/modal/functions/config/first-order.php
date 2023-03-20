<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

if ( function_exists( 'smile_framework_add_options' ) ) {
	$style_arr = array(
		'style_name'    => 'First Order',
		'demo_url'      => CP_PLUGIN_URL . 'modules/modal/assets/demos/first_order/first_order.html',
		'demo_dir'      => plugin_dir_path( __FILE__ ) . '../../assets/demos/first_order/first_order.html',
		'img_url'       => CP_PLUGIN_URL . 'modules/modal/assets/demos/first_order/first_order.png',
		'customizer_js' => CP_PLUGIN_URL . 'modules/modal/assets/demos/first_order/customizer.js',
		'category'      => 'All,Offers,Exit Intent',
		'tags'          => 'Sale,Offer,Discount,Commerce,Logo,Coupon,Button',
		'options'       => array(),
	);

	smile_framework_add_options( 'Convert_Plug_Smile_Modals', 'first_order', $style_arr );
}
