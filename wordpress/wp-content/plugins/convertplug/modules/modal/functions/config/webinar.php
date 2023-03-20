<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

if ( function_exists( 'smile_framework_add_options' ) ) {
	smile_framework_add_options(
		'Convert_Plug_Smile_Modals',
		'webinar',
		array(
			'style_name'    => 'Webinar',
			'demo_url'      => CP_PLUGIN_URL . 'modules/modal/assets/demos/webinar/webinar.html',
			'demo_dir'      => plugin_dir_path( __FILE__ ) . '../../assets/demos/webinar/webinar.html',
			'img_url'       => CP_PLUGIN_URL . 'modules/modal/assets/demos/webinar/webinar.png',
			'customizer_js' => CP_PLUGIN_URL . 'modules/modal/assets/demos/webinar/customizer.js',
			'category'      => 'All,Optins,Updates',
			'tags'          => 'Hangout,Webinar,Update,Training,Optin,Email,Subscribe',
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
		)
	);
}
