<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

if ( function_exists( 'smile_framework_add_options' ) ) {
	// You-tube Style.
	$style_arr = array(
		'style_name'    => 'YouTube',
		'demo_url'      => CP_PLUGIN_URL . 'modules/modal/assets/demos/youtube/youtube.html',
		'demo_dir'      => plugin_dir_path( __FILE__ ) . '../../assets/demos/youtube/youtube.html',
		'img_url'       => CP_PLUGIN_URL . 'modules/modal/assets/demos/youtube/youtube.png',
		'customizer_js' => CP_PLUGIN_URL . 'modules/modal/assets/demos/youtube/customizer.js',
		'category'      => 'All,Videos',
		'tags'          => 'Video,YouTube,Play,Media',
		'options'       => array(),
	);
	smile_framework_add_options( 'Convert_Plug_Smile_Modals', 'YouTube', $style_arr );
}
