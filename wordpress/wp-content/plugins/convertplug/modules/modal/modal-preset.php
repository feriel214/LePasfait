<?php
/**
 * Prohibit direct script loading.
 *
 * @package Convert_Plus.
 */

defined( 'ABSPATH' ) || die( 'No direct script access allowed!' );

/**
 * Function Name: cp_add_modal_template action to add templates.
 *
 * @param  array  $args   array of settings.
 * @param  string $preset string parameters.
 * @param  string $module string parameters.
 * @return array         array of settings.
 */
function cp_add_modal_template( $args, $preset, $module ) {
	/**
 * Array Format
 * array(
	'locked_content', // theme slug for template
	'International Conference', // template name
	CP_PLUGIN_URL . 'modules/modal/assets/demos/locked_content/locked_content.html', // HTML file for template
	'http://downloads.brainstormforce.com/convertplug/presets/screenshot_international_conf.png', // screen shot url for template
	CP_PLUGIN_URL . 'modules/modal/assets/demos/locked_content/customizer.js', // customizer js for template
	'All,Offers', // categories
	'Shortcode,Canvas,HTML,Custom', // tags
	'international_conference', // template unique slug
	)
 */

	if ( 'modal' === $module ) {

		$modal_temp_array = array(

			'international_conference'      =>
			array(
				'locked_content',
				'International Conference',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/locked_content/locked_content.html',
				'http://downloads.brainstormforce.com/convertplug/presets/screenshot_international_conf.png',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/locked_content/customizer.js',
				'All,Offers',
				'Shortcode,Canvas,HTML,Custom',
				'international_conference',
			),

			'how_to_learn'                  =>
			array(
				'every_design',
				'How To Learn',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/every_design/every_design.html',
				'http://downloads.brainstormforce.com/convertplug/presets/screenshot_how_to_learn.png',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/every_design/customizer.js',
				'All,Offers',
				'Shortcode,Canvas,HTML,Custom',
				'how_to_learn',
			),

			'sharing_is_awesome_do_it'      =>
			array(
				'social_media',
				'Sharing Is Awesome Do It',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/social_media/social_media.html',
				'http://downloads.brainstormforce.com/convertplug/presets/cp_id_8cc49.png',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/social_media/customizer.js',
				'All,Social',
				'Shortcode,Canvas,HTML,Custom,Facebook,Twitter,Google,Blogger,Pinterest,LinkedIn',
				'sharing_is_awesome_do_it',
			),
			'sharing_rounded_icons'         =>
			array(
				'social_media',
				'Sharing Rounded Icons',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/social_media/social_media.html',
				'http://downloads.brainstormforce.com/convertplug/presets/cp_id_ad474.png',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/social_media/customizer.js',
				'All,Social',
				'Shortcode,Canvas,HTML,Custom,Facebook,Twitter,Google,Pinterest',
				'sharing_rounded_icons',
			),
			'sharing_bar_icons'             =>
			array(
				'social_media',
				'Sharing Bar Icons',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/social_media/social_media.html',
				'http://downloads.brainstormforce.com/convertplug/presets/cp_id_5f0a9.png',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/social_media/customizer.js',
				'All,Social',
				'Shortcode,Canvas,HTML,Custom,Facebook,Twitter,Google,Blogger',
				'sharing_bar_icons',
			),
			'first_order_discount'          =>
			array(
				'first_order_2',
				'First Order Discount',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/first_order_2/first_order_2.html',
				'http://downloads.brainstormforce.com/convertplug/presets/cp_id_10442.png',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/first_order_2/customizer.js',
				'All,modal popup, Offers',
				'Shortcode,Canvas,HTML,Custom',
				'first_order_discount',
			),
			'subscribe_to_newsletter'       =>
			array(
				'every_design',
				'Subscribe To Newsletter',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/every_design/every_design.html',
				'http://downloads.brainstormforce.com/convertplug/presets/cp_id_3efd1.png',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/every_design/customizer.js',
				'All,modal popup, full screen',
				'Shortcode,Canvas,HTML,Custom',
				'subscribe_to_newsletter',
			),
			'create_profitable_blog'        =>
			array(
				'every_design',
				'Create Profitable Blog',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/every_design/every_design.html',
				'http://downloads.brainstormforce.com/convertplug/presets/cp_id_f7bc0.png',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/every_design/customizer.js',
				'All,modal popup,full screen',
				'Shortcode,Canvas,HTML,Custom',
				'create_profitable_blog',
			),
			'get_glamorous'                 =>
			array(
				'first_order_2',
				'Get Glamorous',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/first_order_2/first_order_2.html',
				'http://downloads.brainstormforce.com/convertplug/presets/cp_id_fa4a6.png',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/first_order_2/customizer.js',
				'All,modal popup,Offers,Updates,Exit Intent',
				'Shortcode,Canvas,HTML,Custom',
				'get_glamorous',
			),
			'get_latest_freebies'           =>
			array(
				'every_design',
				'Get Latest Freebies',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/every_design/every_design.html',
				'http://downloads.brainstormforce.com/convertplug/presets/cp_id_6dd6c.png',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/every_design/customizer.js',
				'All,modal popup,exit intent,offers,Optins',
				'Shortcode,Canvas,HTML,Custom',
				'get_latest_freebies',
			),
			'burger_hot_deals'              =>
			array(
				'optin_to_win',
				'Burger Hot Deals',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/optin_to_win/optin_to_win.html',
				'http://downloads.brainstormforce.com/convertplug/presets/cp_id_9a260.png',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/optin_to_win/customizer.js',
				'All,modal popup,Optins',
				'Shortcode,Canvas,HTML,Custom',
				'burger_hot_deals',
			),
			'join_greatest_mailing_list'    =>
			array(
				'every_design',
				'Join Greatest Mailing List',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/every_design/every_design.html',
				'http://downloads.brainstormforce.com/convertplug/presets/cp_id_cbf7b.png',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/every_design/customizer.js',
				'All,modal popup,Optins',
				'Shortcode,Canvas,HTML,Custom',
				'join_greatest_mailing_list',
			),
			'pet_care'                      =>
			array(
				'direct_download',
				'Pet Care',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/direct_download/direct_download.html',
				'http://downloads.brainstormforce.com/convertplug/presets/cp_id_e7b09.png',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/direct_download/customizer.js',
				'All,modal popup, Updates',
				'Shortcode,Canvas,HTML,Custom',
				'pet_care',
			),
			'spicy_hot_deal'                =>
			array(
				'direct_download',
				'Spicy Hot Deal',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/direct_download/direct_download.html',
				'http://downloads.brainstormforce.com/convertplug/presets/cp_id_f199e.png',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/direct_download/customizer.js',
				'All,modal popup,Offers',
				'Shortcode,Canvas,HTML,Custom',
				'spicy_hot_deal',
			),

			'blue_social_media_guide'       =>
			array(
				'direct_download',
				'Blue Social Media Guide',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/direct_download/direct_download.html',
				'http://downloads.brainstormforce.com/convertplug/presets/cp_id_e8868.png',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/direct_download/customizer.js',
				'All,Exit Intent,modal popup',
				'Shortcode,Canvas,HTML,Custom',
				'blue_social_media_guide',
			),
			'bricks_popup_subscription_box' =>
			array(
				'every_design',
				'Brics Popup Subscription',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/every_design/every_design.html',
				'http://downloads.brainstormforce.com/convertplug/presets/cp_id_2aa4f.png',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/every_design/customizer.js',
				'All,modal popup,Optins,Exit Intent',
				'Shortcode,Canvas,HTML,Custom',
				'bricks_popup_subscription_box',
			),
			'green_exclusive_blogging_tips' =>
			array(
				'every_design',
				'Green Exclusive Tips',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/every_design/every_design.html',
				'http://downloads.brainstormforce.com/convertplug/presets/cp_id_30739.png',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/every_design/customizer.js',
				'All,modal popup,Optins',
				'Shortcode,Canvas,HTML,Custom',
				'green_exclusive_blogging_tips',
			),
			'get_more_subscribers'          =>
			array(
				'webinar',
				'Get More Subscriber',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/webinar/webinar.html',
				'http://downloads.brainstormforce.com/convertplug/presets/cp_id_8eba4.png',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/webinar/customizer.js',
				'All,modal popup,Optins',
				'Shortcode,Canvas,HTML,Custom',
				'get_more_subscribers',
			),
			'business_blog_optin'           =>
			array(
				'direct_download',
				'Bussiness Blog Optin',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/direct_download/direct_download.html',
				'http://downloads.brainstormforce.com/convertplug/presets/cp_id_fa93d.png',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/direct_download/customizer.js',
				'All,modal popup,Exit Intent',
				'Shortcode,Canvas,HTML,Custom',
				'business_blog_optin',
			),
			'blogging_guide'                =>
			array(
				'direct_download',
				'Blogging Guide',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/direct_download/direct_download.html',
				'http://downloads.brainstormforce.com/convertplug/presets/cp_id_8016c.png',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/direct_download/customizer.js',
				'All,modal popup,Exit Intent',
				'Shortcode,Canvas,HTML,Custom',
				'blogging_guide',
			),
			'black_friday_discount'         =>
			array(
				'first_order',
				'Black Friday Discount',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/first_order/first_order.html',
				'http://downloads.brainstormforce.com/convertplug/presets/cp_id_8c9f9.png',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/first_order/customizer.js',
				'All,modal popup,Updates,Offers',
				'Shortcode,Canvas,HTML,Custom',
				'black_friday_discount',
			),
			'design_kitchen'                =>
			array(
				'locked_content',
				'Design Kitchen',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/locked_content/locked_content.html',
				'http://downloads.brainstormforce.com/convertplug/presets/screenshots_design_kitchen.png',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/locked_content/customizer.js',
				'All,modal popup, Updates',
				'Shortcode,Canvas,HTML,Custom,Kitchen,Delights',
				'design_kitchen',
			),
			'exclusive_tips'                =>
			array(
				'every_design',
				'Exclusive Tips',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/every_design/every_design.html',
				'http://downloads.brainstormforce.com/convertplug/presets/screenshots_excl_tip.png',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/every_design/customizer.js',
				'All,modal popup, Updates',
				'Shortcode,Canvas,HTML,Custom,Tips',
				'exclusive_tips',
			),
			'show_time'                     =>
			array(
				'optin_to_win',
				'Show Time',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/optin_to_win/optin_to_win.html',
				'http://downloads.brainstormforce.com/convertplug/presets/screenshots_show_time.png',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/optin_to_win/customizer.js',
				'All,modal popup, Updates',
				'Shortcode,Canvas,HTML,Custom,Tips,Movie',
				'show_time',
			),
			'design_post'                   =>
			array(
				'every_design',
				'Design Post',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/every_design/every_design.html',
				'http://downloads.brainstormforce.com/convertplug/presets/screenshots_design_post.png',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/every_design/customizer.js',
				'All,modal popup,exit intent,offers,Optins',
				'Shortcode,Canvas,HTML,Custom',
				'design_post',
			),
			'extended_discount'             =>
			array(
				'first_order_2',
				'Extended Discount',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/first_order_2/first_order_2.html',
				'http://downloads.brainstormforce.com/convertplug/presets/screenshot_ext_disc.png',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/first_order_2/customizer.js',
				'All,modal popup,Offers,Updates,Exit Intent',
				'Shortcode,Canvas,HTML,Custom,Discount',
				'extended_discount',
			),
			'design_tigers'                 =>
			array(
				'every_design',
				'Design Tigers',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/every_design/every_design.html',
				'http://downloads.brainstormforce.com/convertplug/presets/screenshots_super_surviver.png',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/every_design/customizer.js',
				'All,modal popup,exit intent,offers,Optins',
				'Shortcode,Canvas,HTML,Custom',
				'design_tigers',
			),
			'free_note'                     =>
			array(
				'first_order',
				'Free Note',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/first_order/first_order.html',
				'http://downloads.brainstormforce.com/convertplug/presets/screenshot_note.png',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/first_order/customizer.js',
				'All,modal popup,Updates,Offers',
				'Shortcode,Canvas,HTML,Custom,Notes,Enterpreneur',
				'free_note',
			),
			'conversion_hack'               =>
			array(
				'direct_download',
				'Conversion Hack',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/direct_download/direct_download.html',
				'http://downloads.brainstormforce.com/convertplug/presets/screenshot_conversion_tips.png',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/direct_download/customizer.js',
				'All,modal popup,Exit Intent',
				'Shortcode,Canvas,HTML,Custom',
				'conversion_hack',
			),
			'ecommerce_growth'              =>
			array(
				'webinar',
				'Ecommerce Growth',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/webinar/webinar.html',
				'http://downloads.brainstormforce.com/convertplug/presets/screenshot_ecommerce.png',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/webinar/customizer.js',
				'All,modal popup,Optins',
				'Shortcode,Canvas,HTML,Custom,Ecommerce',
				'ecommerce_growth',
			),
			'pet_care_fullscreen'           =>
			array(
				'locked_content',
				'Pet Care',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/locked_content/locked_content.html',
				'http://downloads.brainstormforce.com/convertplug/presets/screenshot_full_pet_care.png',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/locked_content/customizer.js',
				'All,modal popup, Updates',
				'Shortcode,Canvas,HTML,Custom,Pet,Care',
				'pet_care_fullscreen',
			),
			'we_care_your_pet'              =>
			array(
				'first_order_2',
				'We Care Your Pet',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/first_order_2/first_order_2.html',
				'http://downloads.brainstormforce.com/convertplug/presets/preset_we_care_ur_pet.png',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/first_order_2/customizer.js',
				'All,modal popup,Offers,Updates,Exit Intent',
				'Shortcode,Canvas,HTML,Custom,Discount,Pet',
				'we_care_your_pet',
			),
			'e_cooking_classes'             =>
			array(
				'direct_download',
				'E Cooking Classes',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/direct_download/direct_download.html',
				'http://downloads.brainstormforce.com/convertplug/presets/screenshot_cookery_classes.png',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/direct_download/customizer.js',
				'All,modal popup,Exit Intent',
				'Shortcode,Canvas,HTML,Custom',
				'e_cooking_classes',
			),
			'huge_discount'                 =>
			array(
				'first_order',
				'Huge Discount',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/first_order/first_order.html',
				'http://downloads.brainstormforce.com/convertplug/presets/screenshot_huge.png',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/first_order/customizer.js',
				'All,modal popup,Updates,Offers',
				'Shortcode,Canvas,HTML,Custom,Notes,Enterpreneur',
				'huge_discount',
			),
			'Yello_pet_food_offer'          =>
			array(
				'special_offer',
				'Yello Pet Food Offer',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/special_offer/special_offer.html',
				'http://downloads.brainstormforce.com/convertplug/presets/screenshot_yellow_food_pet.png',
				CP_PLUGIN_URL . 'modules/modal/assets/demos/special_offer/customizer.js',
				'All,modal popup,Updates,Offers',
				'Shortcode,Canvas,HTML,Custom,Pet,Food',
				'Yello_pet_food_offer',
			),
		);

		if ( '' !== $preset ) {
			$temp_arr                    = $modal_temp_array[ $preset ];
			$modal_temp_array            = array();
			$modal_temp_array[ $preset ] = $temp_arr;
			$args                        = array_merge( $args, $modal_temp_array );
		} else {
			$args = array_merge( $args, $modal_temp_array );
		}
	}

	return $args;
}
