<?php
/*
Plugin Name: Worona
Plugin URI: http://www.worona.org/
Description: Turn your WordPress site into a native iOS, Android and Windows Phone App.
Version: 0.6.0
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

		// filters
		add_filter( 'json_prepare_post',  array($this, 'add_worona_content_to_api'), 10, 3 );
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
		require_once("includes/simple_html_dom.php");
		
	}
	
	/*
	*  worona_admin_actions
	*
	*  This function is called during the 'admin_menu' action and will do things such as:
	*  add a worona link to the wordpress settings menu
	*
	*  @type	action (admin_menu)
	*  @date	18/07/14
	*  @since	0.6.0
	*
	*  @param	N/A
	*  @return	N/A
	*/

	function worona_admin_actions() {
	    add_options_page("Worona", "Worona", 1, "Worona", array($this, "worona_admin") );
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
	    $html = str_replace( PHP_EOL, '', wpautop( strip_tags( do_shortcode( $post['post_content'] ), '<h1><h2><h3><img><p><ul><li><a>'), false ) );
	    $_post['worona_content']['html'] = apply_filters( "worona_prepare_html", $html );
	    

	    return $_post;
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