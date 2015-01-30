<?php
/*
Plugin Name: Worona
Plugin URI: http://www.worona.org/
Description: Turn your WordPress site into a native iOS, Android and Windows Phone App.
Version: 0.7
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
		add_filter( 'worona_before_prepare_html', array($this, 'prepare_youtube_videos'), 10, 1);
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
		require_once("lib/simple_html_dom.php");

		
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
  
  		//var_dump($post['post_content']);
  		//die();
	    // add the html content of the post to worona_content	    
	    $html  = apply_filters( "worona_before_prepare_html", $post['post_content'] );
	    $html = str_replace( PHP_EOL, '', wpautop( strip_tags( do_shortcode( $html ), '<h1><h2><h3><h4><h5><h6><img><p><ul><iframe><li><a><strong>'), false ) );
	    $_post['worona_content']['html'] = apply_filters( "worona_prepare_html", $html );
	    

	    return $_post;
	}

	function prepare_youtube_videos ( $html ) {

		global $wp_embed;
		$html = $wp_embed->run_shortcode($html);

		// Create a DOM object
		$dom = new simple_html_dom();
		$dom->load( $html );

		// Find all youtube iframes
		foreach($dom->find('iframe') as $iframe) {
			$src = $iframe->src;

			//We recover youtube video id
			preg_match("/embed\/([a-zA-Z0-9]*)(&|#|$|\?)/", $src, $youtube_id);
			$youtube_id = $youtube_id[1];

			//Call Youtube API to obtain video thumbnail
			$youtube_thumbnail_url = "http://img.youtube.com/vi/" . $youtube_id . "/hqdefault.jpg"; 

			//prepare link to Youtube fullscreen web
			$youtube_link = "http://www.youtube.com/embed/" . $youtube_id;

			$youtube_play = trailingslashit(plugin_dir_url( "yotube_play.png" )) . "worona/assets/youtube_play.png";

			//<a href=$youtube_link></a><img src=$youtube_thumbnail_url />
			$iframe->outertext = 
				"<p style='position:relative;'>
	  				<a style=' 	position: absolute;
	   							display: block;
	   							background: url(\"".$youtube_play."\");
	   							height: 85px;
	   							width: 118px;
	   							top: 137px;
	   							left: 181px;' href=\"".$youtube_link."\"></a>
	    			<img src=\"".$youtube_thumbnail_url."\"/>
				</p>";

		}

		return $dom->save();
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
