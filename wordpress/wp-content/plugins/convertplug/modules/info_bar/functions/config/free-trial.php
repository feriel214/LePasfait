<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

if ( function_exists( 'smile_framework_add_options' ) ) {

	smile_framework_add_options(
		'Convert_Plug_Smile_Info_Bars',
		'free_trial',
		array(
			'style_name'    => 'Free Trial',
			'demo_url'      => CP_PLUGIN_URL . 'modules/info_bar/assets/demos/free_trial/free_trial.html',
			'demo_dir'      => plugin_dir_path( __FILE__ ) . '../../assets/demos/free_trial/free_trial.html',
			'img_url'       => CP_PLUGIN_URL . 'modules/info_bar/assets/demos/free_trial/free_trial.png',
			'customizer_js' => CP_PLUGIN_URL . 'modules/info_bar/assets/demos/free_trial/customizer.js',
			'category'      => 'All,Optins',
			'tags'          => 'form',
			'options'       => array(),
		)
	);
}
