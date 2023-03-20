<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

if ( function_exists( 'smile_framework_add_options' ) ) {
	$style_arr = array(
		'style_name'    => 'Social Media With Form',
		'demo_url'      => CP_PLUGIN_URL . 'modules/modal/assets/demos/social_media_with_form/social_media_with_form.html',
		'demo_dir'      => plugin_dir_path( __FILE__ ) . '../../assets/demos/social_media_with_form/social_media_with_form.html',
		'img_url'       => CP_PLUGIN_URL . 'modules/modal/assets/demos/social_media_with_form/social_media_with_form.png',
		'customizer_js' => CP_PLUGIN_URL . 'modules/modal/assets/demos/social_media_with_form/customizer.js',
		'category'      => 'All,Optins,Updates,Social',
		'tags'          => 'Hangout,Social Media,Update,Training,Optin,Email,Subscribe',
		'options'       => array(

			// field to set ckeditor for middle description.
			array(
				'type'         => 'textarea',
				'class'        => '',
				'name'         => 'modal_middle_desc',
				'opts'         => array(
					'title' => __( 'Middle Description', 'smile' ),
					'value' => __( 'For more details click on the below link.', 'smile' ),
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

	smile_framework_add_options( 'Convert_Plug_Smile_Modals', 'social_media_with_form', $style_arr );
}
