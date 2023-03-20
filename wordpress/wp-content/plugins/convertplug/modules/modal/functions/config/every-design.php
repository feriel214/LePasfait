<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

if ( function_exists( 'smile_framework_add_options' ) ) {
	$style_arr = array(
		'style_name'    => 'Every Design',
		'demo_url'      => CP_PLUGIN_URL . 'modules/modal/assets/demos/every_design/every_design.html',
		'demo_dir'      => plugin_dir_path( __FILE__ ) . '../../assets/demos/every_design/every_design.html',
		'img_url'       => CP_PLUGIN_URL . 'modules/modal/assets/demos/every_design/every_design.png',
		'customizer_js' => CP_PLUGIN_URL . 'modules/modal/assets/demos/every_design/customizer.js',
		'category'      => 'All,Optins,Exit Intent',
		'tags'          => 'Newsletter,Email,Optin,Subscribe',
		'options'       => array(),
	);

	smile_framework_add_options( 'Convert_Plug_Smile_Modals', 'every_design', $style_arr );
}
