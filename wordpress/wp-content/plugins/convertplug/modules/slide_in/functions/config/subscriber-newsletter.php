<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

if ( function_exists( 'smile_framework_add_options' ) ) {
	$style_arr = array(
		'style_name'    => 'Subscriber Newsletter',
		'demo_url'      => CP_PLUGIN_URL . 'modules/slide_in/assets/demos/subscriber_newsletter/subscriber_newsletter.html',
		'demo_dir'      => plugin_dir_path( __FILE__ ) . '../../assets/demos/subscriber_newsletter/subscriber_newsletter.html',
		'img_url'       => CP_PLUGIN_URL . 'modules/slide_in/assets/demos/subscriber_newsletter/subscriber_newsletter.png',
		'customizer_js' => CP_PLUGIN_URL . 'modules/slide_in/assets/demos/subscriber_newsletter/customizer.js',
		'category'      => 'All,offers',
		'tags'          => 'offers,default,special,discount,mascot,blank',
		'options'       => array(),
	);

	smile_framework_add_options( 'Convert_Plug_Smile_Slide_Ins', 'subscriber_newsletter', $style_arr );
}
