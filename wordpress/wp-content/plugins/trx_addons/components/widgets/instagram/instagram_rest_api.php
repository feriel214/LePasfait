<?php
/**
 * Instagram support: REST API callbacks
 *
 * @package WordPress
 * @subpackage ThemeREX Addons
 * @since v1.6.47
 */

// Don't load directly
if ( ! defined( 'TRX_ADDONS_VERSION' ) ) {
	die( '-1' );
}


// Get recent photos
if ( !function_exists( 'trx_addons_widget_instagram_get_recent_photos' ) ) {
	function trx_addons_widget_instagram_get_recent_photos($args) {
		// Check photos in the cache
		$client_id = trx_addons_widget_instagram_get_client_id();
		$cache_data = sprintf('trx_addons_instagram_data_%1$s_%2$s', $client_id, $args['hashtag']);
		$data = get_transient($cache_data);
		// If no photos - request its from Instagram and put to the cache for 4 hours
		if (!is_array($data) || count($data) < $args['count']) {
			$access_token = trx_addons_get_option('api_instagram_access_token');
			if (!empty($access_token)) {
				$count = max(1, $args['count']);
				$url = 'https://api.instagram.com/v1/'
						. (empty($args['hashtag']) ? 'users/self' : "tags/{$args['hashtag']}" )
						.  "/media/recent?access_token={$access_token}&count={$count}";
				$resp = trx_addons_remote_get($url);
				if (substr($resp, 0, 1) == '{') {
					$data = json_decode($resp, true);
					set_transient($cache_data, $data, 4*60*60);
				}
			}
		}
		return $data;
	}
}


//------------------------------------------------
//--  REST API support
//------------------------------------------------

// Register endpoints
if ( !function_exists( 'trx_addons_widget_instagram_rest_register_endpoints' ) ) {
	add_action( 'rest_api_init', 'trx_addons_widget_instagram_rest_register_endpoints');
	function trx_addons_widget_instagram_rest_register_endpoints() {
		// Get access token from Instagram
		register_rest_route( 'trx_addons/v1', '/widget_instagram/get_access', array(
			'methods' => 'GET,POST',
			'callback' => 'trx_addons_widget_instagram_rest_get_access',
			'permission_callback' => '__return_true' // Add this
			));
	}
}


// Return redirect url for Instagram API
if ( !function_exists( 'trx_addons_widget_instagram_rest_get_redirect_uri' ) ) {
	function trx_addons_widget_instagram_rest_get_redirect_uri() {
		$nonce = get_transient('trx_addons_instagram_nonce');
		if (empty($nonce)) {
			$nonce = md5(mt_rand());
			set_transient('trx_addons_instagram_nonce', $nonce, 60*60);
		}
		$url = trailingslashit(home_url()) . "wp-json/trx_addons/v1/widget_instagram/get_access/?nonce={$nonce}";
		if (trx_addons_get_option('api_instagram_client_id') != '') {
			return $url;
		} else {
			return "http://cb.themerex.net/instagram?return_uri={$url}";
		}
	}
}

// Callback: Get authorization code from Instagram
if ( !function_exists( 'trx_addons_widget_instagram_rest_get_access' ) && class_exists( 'WP_REST_Request' ) ) {
	function trx_addons_widget_instagram_rest_get_access(WP_REST_Request $request) {

		// Get response code
		$params = $request->get_params();
		$nonce = get_transient('trx_addons_instagram_nonce');
		if (empty($params['error']) && !empty($params['nonce']) && !empty($nonce) && $params['nonce']==$nonce) {
			
			$code = !empty($params['code']) ? $params['code'] : '';
			$access_token = !empty($params['access_token']) ? $params['access_token'] : '';
			
			// Receive authorization code - request for access token
			if (empty($access_token) && !empty($code)) {
				$client_id = trx_addons_widget_instagram_get_client_id();
				$client_secret = trx_addons_widget_instagram_get_client_secret();
				// Request for access token
				$resp = trx_addons_remote_post('https://api.instagram.com/oauth/access_token',
											array(
													'client_id' => $client_id,
													'client_secret' => $client_secret,
													'grant_type' => 'authorization_code',
													'code' => $code,
													'response_type' => 'code',
													'redirect_uri' => trx_addons_widget_instagram_rest_get_redirect_uri()
												)
										);
				if (substr($resp, 0, 1) == '{') {
					$resp = json_decode($resp, true);
					if (!empty($resp['access_token'])) $access_token = $resp['access_token'];
				}
			}
			
			// Save access token
			if (!empty($access_token) ) {
				$options = get_option('trx_addons_options');
				$options['api_instagram_access_token'] = $access_token;
				update_option('trx_addons_options', $options);
			}
		}		
		
		// Redirect to the options page
		wp_redirect(get_admin_url(null, 'admin.php?page=trx_addons_options#trx_addons_options_section_api_section'));
		die();
	}
}
?>