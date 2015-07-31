<?php
/*
Plugin Name: Worona
Plugin URI: http://www.worona.org/
Description: Turn your WordPress site into a native iOS, Android and Windows Phone App.
Version: 0.7.3
Author: Benuit
Author URI: http://www.benuit.com/
License: GPL v3
Copyright: Benuit
*/

if( !class_exists('worona') ):

class worona
{
	// vars
	var $settings;


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
		add_action('wp_head', array($this, 'worona_add_js_detector'), 1);

		// filters
		add_filter( 'json_prepare_post',  array($this, 'add_worona_content_to_api'), 10, 3 );

		//
		$this->include_wp_api();
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
		$page_title = "Worona";
		$menu_title = "Worona";
		$capability = 1;
		$menu_slug  = "worona";
		$function  	= array($this, "worona_admin");
		$icon_url	= trailingslashit(plugin_dir_url( __FILE__ )) . "assets/worona20x20.png";
		$position	= 64.999989; //Right before the "Plugins"

		add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
	}

	/*
	*  worona_admin
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

	function worona_admin() {
		wp_register_style('worona_plugin_css', plugins_url('/assets/css/worona-plugin.css',__FILE__ ));
		wp_enqueue_style('worona_plugin_css');
	    include('admin/worona_admin_page.php');
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

	/*
	*  include_wp_api
	*
	*  This function is called during the __construct() and will do things such as:
	*  check if we can include the WP-API plugin and include it.
	*
	*  @type	method
	*  @date	13/04/15
	*  @since	0.7.2
	*
	*  @param	N/A
	*  @return	N/A
	*/

	function include_wp_api() {

		// check if current_action function exist, because it was introduced in WP 3.9
		if (function_exists('current_action')) {
			// if WP-API is not active, include it
			if ( !in_array( 'json-rest-api/plugin.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
				error_log("json-rest-api/plugin.php");
				include_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'json-rest-api/plugin.php' );
			}
		}
	}

	/*
	*  worona_add_js_detector
	*
	*  This function is called during the wp_head() and will do things such as:
	*  include a js script to detect if it's mobile
	*
	*  @type	method
	*  @date	30/07/15
	*  @since	1.0.0
	*
	*  @param	N/A
	*  @return	N/A
	*/

	function worona_add_js_detector() {
		echo "\n\n<script type='text/javascript' src='//cdnjs.cloudflare.com/ajax/libs/mobile-detect/1.2.1/mobile-detect.min.js'></script>";
		echo "\n<script type='text/javascript' src='" . plugins_url( 'worona/js/worona-detector.js', dirname(__FILE__) ) . "'></script>\n\n";
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
