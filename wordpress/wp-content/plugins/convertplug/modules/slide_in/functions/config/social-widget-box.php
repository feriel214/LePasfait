<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

if ( function_exists( 'smile_framework_add_options' ) ) {
	$style_arr = array(
		'style_name'    => 'Social Widget Box',
		'demo_url'      => CP_PLUGIN_URL . 'modules/slide_in/assets/demos/social_widget_box/social_widget_box.html',
		'demo_dir'      => plugin_dir_path( __FILE__ ) . '../../assets/demos/social_widget_box/social_widget_box.html',
		'img_url'       => CP_PLUGIN_URL . 'modules/slide_in/assets/demos/social_widget_box/social_widget_box.png',
		'customizer_js' => CP_PLUGIN_URL . 'modules/slide_in/assets/demos/social_widget_box/customizer.js',
		'category'      => 'All,widget,Social',
		'tags'          => 'Hangout,Social',
		'options'       => array(

			// field to set ckeditor for middle description.
			array(
				'type'         => 'textarea',
				'class'        => '',
				'name'         => 'modal_middle_desc',
				'opts'         => array(
					'title' => __( 'Middle Description', 'smile' ),
					'value' => __( 'With John Doe', 'smile' ),
				),
				'panel'        => 'Name',
				'dependency'   => array(
					'name'     => 'hidden',
					'operator' => '==',
					'value'    => 'hide',
				),
				'section'      => 'Design',
				'section_icon' => 'connects-icon-disc',
			),
		),
	);
	smile_framework_add_options( 'Convert_Plug_Smile_Slide_Ins', 'social_widget_box', $style_arr );
}
