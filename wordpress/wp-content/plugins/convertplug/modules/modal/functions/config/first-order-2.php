<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

if ( function_exists( 'smile_framework_add_options' ) ) {

	$style_arr = array(
		'style_name'    => 'First Order 2',
		'demo_url'      => CP_PLUGIN_URL . 'modules/modal/assets/demos/first_order_2/first_order_2.html',
		'demo_dir'      => plugin_dir_path( __FILE__ ) . '../../assets/demos/first_order_2/first_order_2.html',
		'img_url'       => CP_PLUGIN_URL . 'modules/modal/assets/demos/first_order_2/first_order_2.png',
		'customizer_js' => CP_PLUGIN_URL . 'modules/modal/assets/demos/first_order_2/customizer.js',
		'category'      => 'All,Offers,Exit Intent',
		'tags'          => 'Sale,Offer,Discount,Commerce,Coupon,Day,Button',
		'options'       => array(),
	);

	smile_framework_add_options( 'Convert_Plug_Smile_Modals', 'first_order_2', $style_arr );
}
