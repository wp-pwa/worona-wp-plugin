<?php
/*
Plugin Name: Worona
Plugin URI: http://www.worona.org/
Description: Turn your WordPress site into a native iOS, Android and Windows Phone App.
Version: 0.7.4
Author: Worona Labs SL
Author URI: http://www.worona.org/
License: GPL v3
Copyright: Worona Labs SL
*/



if( !class_exists('worona') ):

class worona
{
	// vars
	var $settings;
	public $rest_api_installed 	= false;
	public $rest_api_active 	= false;
	public $rest_api_working	= false;


	/*
	*  Constructor
	*
	*  This function will construct all the neccessary actions, filters and functions for the Worona plugin to work
	*
	*  @type	function
	*  @date	10/06/14
	*  @since	0.6.0
	*
	*  @param	N/A
	*  @return	N/A
	*/

	function __construct()
	{
		// actions
		add_action('init', array($this, 'init'), 1);
		add_action('admin_menu', array($this, 'worona_admin_actions'));

		add_action('plugins_loaded', array($this,'wp_rest_api_plugin_is_installed'));
		add_action('plugins_loaded', array($this,'wp_rest_api_plugin_is_active'));
		add_action( 'init', array($this,'allow_origin'));


		// filters
		//add_filter( 'json_prepare_post',  array($this, 'add_worona_content_to_api'), 10, 3 );

		//
		//$this->include_wp_api();
	}

	/*
	*  init
	*
	*  This function is called during the 'init' action and will do things such as:
	*  create custom_post_types, register scripts, add actions / filters
	*
	*  @type	action (init)
	*  @date	10/06/14
	*  @since	0.6.0
	*
	*  @param	N/A
	*  @return	N/A
	*/

	function init()
	{
		// requires

	}

	/*
	*  worona_admin_actions
	*
	*  This function is called during the 'admin_menu' action and will do things such as:
	*  add a worona menu page to the Main Menu
	*
	*  @type	action (admin_menu)
	*  @date	18/07/14
	*  @since	0.6.0
	*
	*  @param	N/A
	*  @return	N/A
	*/

	function worona_admin_actions() {

		$icon_url	= trailingslashit(plugin_dir_url( __FILE__ )) . "assets/worona20x20.png";
		$position	= 64.999989; //Right before the "Plugins"

		add_menu_page( 
			'Admin - WORONA', 
			'Worona',
			1,
			'Worona-admin', 
			array($this, 'render_worona_admin'), 
			$icon_url,
			$position 
		);
		add_submenu_page(
			'Worona-admin',// the slug name for the parent menu
			'Admin | Worona',// the title of the page when the browser visits it
			'Admin',// the name of the option in the menu
			'manage_options',// gives the plugin the ability to save settings
			'Worona-admin',// this submenu's slug
			array($this, 'render_worona_admin')// the function that will render the admin page
		);
		add_submenu_page(
			'Worona-admin', // the slug name for the parent menu
			'Help | Worona', // the title of the page when the browser visits it
			'Contact & Help', // the name of the option in the menu
			'manage_options', // gives the plugin the ability to save settings
			'Worona-help', // this submenu's slug
			array( $this, 'render_worona_help' ) // the function that will render the admin page
		);
	}

	/*
	*  render_worona_admin
	*
	*  This function is called by the 'worona_admin_actions' function and will do things such as:
	*  add a worona page to render the admin content
	*
	*  @type	fucntion called by 'worona_admin_actions'
	*  @date	18/07/14
	*  @since	0.6.0
	*
	*  @param	N/A
	*  @return	N/A
	*/

	function render_worona_admin() {
		wp_register_style('worona_plugin_css', plugins_url('/assets/css/worona-plugin.css',__FILE__ ));
		wp_enqueue_style('worona_plugin_css');
	    include( 'admin/worona_admin_page.php');
	}

	/*
	*  render_worona_help
	*
	*  This function is called by the 'worona_admin_actions' function and will do things such as:
	*  add a worona page to render the help content
	*
	*  @type	fucntion called by 'worona_admin_actions'
	*  @date	18/07/14
	*  @since	0.6.0
	*
	*  @param	N/A
	*  @return	N/A
	*/

	function render_worona_help() {
		wp_register_style('worona_plugin_css', plugins_url('/assets/css/worona-plugin.css',__FILE__ ));
		wp_enqueue_style('worona_plugin_css');
	    include('admin/worona_help_page.php');
	}

	/*
	*  add_worona_content_to_api
	*
	*  This function is called during the 'json_prepare_post' filter and will do things such as:
	*  prepare everything to output a new filed (worona_content) with the content the app needs from a post
	*
	*  @type	filter (json_prepare_post)
	*  @date	10/06/14
	*  @since	0.6.0
	*
	*  @param	N/A
	*  @return	N/A
	*/

	function add_worona_content_to_api( $_post, $post, $context ) {

	    // get all the fields of this post from Advanced Custom Fields
	    if( function_exists( "get_fields" ) )
	    {
	    	$fields = get_fields( $post['ID'] );
	    	$_post['worona_content']['acf'] = $fields;
	    }

	    // add the html content of the post to worona_content
	    $html = str_replace( PHP_EOL, '', wpautop( strip_tags( do_shortcode( $post['post_content'] ), '<h1><h2><h3><h4><h5><h6><img><p><ul><li><a><strong>'), false ) );
	    $_post['worona_content']['html'] = apply_filters( "worona_prepare_html", $html );


	    return $_post;
	}


	//Checks if the rest-api plugin is installed	
	public function wp_rest_api_plugin_is_installed() {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$plugins = get_plugins();
		
		$this->rest_api_installed = isset($plugins['rest-api/plugin.php']);
	}

	//Checks if the rest-api plugin is active
	public function wp_rest_api_plugin_is_active() {
		$this->rest_api_active = class_exists( 'WP_REST_Controller' );
	}

	//Generates the url to 'auto-activate' the rest-api plugin
	public function get_activate_wp_rest_api_plugin_url() {
		$plugin = 'rest-api/plugin.php';
		$plugin_escaped = str_replace('/', '%2F', $plugin);

		$activateUrl = sprintf(admin_url('plugins.php?action=activate&plugin=%s&plugin_status=all&paged=1&s'), $plugin_escaped);

	  	// change the plugin request to the plugin to pass the nonce check
	  	$_REQUEST['plugin'] = $plugin;
	  	$activateUrl = wp_nonce_url($activateUrl, 'activate-plugin_' . $plugin);

	  	return $activateUrl;
	}

	//Adds Cross origin * to the header
	function allow_origin() {
	    header("Access-Control-Allow-Origin: *");
	}

	//Checks if the json posts endpoint is responding correctly
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
			// json valid
			// json without error message { code: "rest_no_route" }


		} else {
			return false;
		}
	}

}

/*
*  worona
*
*  The main function responsible for returning the one true worona Instance to functions everywhere.
*  Use this function like you would a global variable, except without needing to declare the global.
*
*  Example: <?php $worona = worona(); ?>
*
*  @type	function
*  @date	11/06/14
*  @since	0.6.0
*
*  @param	N/A
*  @return	(object)
*/

function worona()
{
	global $worona;

	if( !isset($worona) )
	{
		$worona = new worona();
	}

	return $worona;
}


// initialize
worona();


endif; // class_exists check
