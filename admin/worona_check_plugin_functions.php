<?php
	//Checks if the rest-api plugin is installed	
	function wp_rest_api_plugin_is_installed() {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$plugins = get_plugins();
		return isset($plugins['rest-api/plugin.php']);
	}

	//Checks if the rest-api plugin is active
	function wp_rest_api_plugin_is_active() {
		return function_exists( 'register_rest_field' );
	}

	//Generates the url to 'auto-activate' the rest-api plugin
	function get_activate_wp_rest_api_plugin_url() {
		$plugin = 'rest-api/plugin.php';
		$plugin_escaped = str_replace('/', '%2F', $plugin);

		$activateUrl = sprintf(admin_url('plugins.php?action=activate&plugin=%s&plugin_status=all&paged=1&s'), $plugin_escaped);

	  	// change the plugin request to the plugin to pass the nonce check
	  	$_REQUEST['plugin'] = $plugin;
	  	$activateUrl = wp_nonce_url($activateUrl, 'activate-plugin_' . $plugin);

	  	return $activateUrl;
	}

	//Checks if the /wp-json/wp/v2/posts endpoint is responding correctly
	function wp_rest_api_endpoint_works() {
		$rest_api_url = get_site_url() . '/wp-json/wp/v2/posts';
		$args = array('timeout' => 10, 'httpversion' => '1.1' );

		$response = wp_remote_get( $rest_api_url, $args );

		if( is_array($response) ) {
			$body = $response['body'];
			$code = $reponse['reponse']['code'];
			$message = $reponse['reponse']['message'];
			$json_reponse = json_decode($body);

			//CHECKS 
			// $code != 200
			// json not valid
			// json with error message { code: "rest_no_route" }


		} else {
			return false;
		}
	}
?>