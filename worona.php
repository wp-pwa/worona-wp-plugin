<?php
/*
Plugin Name: Worona
Plugin URI: http://www.worona.org/
Description: Turn your WordPress site into a native iOS, Android and Windows Phone App.
Version: 1.0.0
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
	*  @since	1.0.0
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
	*  @since	1.0.0
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
	*  @type	action (init)
	*  @date	18/07/14
	*  @since	1.0.0
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
	*  @since	1.0.0
	*
	*  @param	N/A
	*  @return	N/A
	*/

	function worona_admin() {
	    include('admin/worona_admin_page.php');
	}
	

	private function check_for_images_inside_tag( $child ) {

		// search for images inside blocks (sometimes inside paragraphs or subtitles)
		$child_images = $child->find('img');

		$images_array = array();

		foreach ($child_images as $child_image) {

			preg_match( '|<img.*?class=[\'"](.*?)wp-image-([0-9]{1,6})(.*?)[\'"].*?>|i', $child_image->outertext, $match );

			$attachment_id = intval($match[2]);

			$wp_image = wp_get_attachment_metadata( $attachment_id );

			if ( ( $wp_image !== false ) && ( $wp_image !== "" ) ) {
				$width  = $wp_image["width"];
				$height = $wp_image["height"];
				$url    = wp_get_attachment_url( $attachment_id );
				$sizes  = $wp_image["sizes"];
			} else {
				$image_size = getimagesize( $child_image->src );
				$width      = $image_size[0];
				$height     = $image_size[1];
				$url        = $child_image->src;
				$sizes      = null;
			}

			array_push( $images_array, array( 
				type   => "image",
				url    => $url,
				width  => $width,
				height => $height,
				sizes  => $sizes
				)
			);
		}

		return $images_array;

	}


	/*
	*  extract_tags
	*
	*  This function is called during the 'json_prepare_post' filter and will do things such as:
	*  prepare everything to output a new filed (worona_content) with the content the app needs from a post
	*
	*  @type	filter (json_prepare_post)
	*  @date	10/06/14
	*  @since	1.0.0
	*
	*  @param	N/A
	*  @return	N/A
	*/

	private function extract_tags( $html )
	{
		// start the dom object
		$dom = new simple_html_dom();

		// initializate the final array
		$content_array = array();

		// load the html with an id, to be able to output their children
		$dom->load("<div id='worona_dom'>" . $html . "</div>");

		// get all the first line tags
		$dom_children = $dom->getElementById( '#worona_dom' )->children();

		// traverse the children to find display blocks
		foreach ($dom_children as $child) {

			$size = null;
			$list_array = array();

			switch ( $child->tag ) {
			    case "h1":
			    case "h2":
			    case "h3":
			    case "h4":
			        if ( ( $child->plaintext !== "" ) && ( $child->plaintext !== "&nbsp;" ) && ( $child->plaintext !== " " ) ) {
			        	array_push( $content_array, array( 
			        		type => "subtitle",
			        		size => $child->tag,
			        		text => $child->plaintext
			        	));
			        }
			        break;
			    case "p":
			        if ( ( $child->plaintext !== "" ) && ( $child->plaintext !== "&nbsp;" ) && ( $child->plaintext !== " " ) ) {
			        	array_push( $content_array, array( 
			        		type => "paragraph",
			        		text => $child->plaintext
			        	));
			        	// add images as well
			        	$images = $this->check_for_images_inside_tag( $child );
			        	foreach ($images as $image) {
			        		array_push( $content_array, $image );
			        	}
			        	
			        }
			        break;
			    case "ul":
			    	$list_array = array();
			    	$li_list    = $child->find('li');
			    	foreach ($li_list as $li) {
			    		array_push( $list_array, array( 
			    			type   => "list_item",
			    			text   => $li->plaintext,
			    			images => $this->check_for_images_inside_tag($li)
			    			)
			    		);
			    	}
			    	array_push( $content_array, array( 
			    		type => "list",
			        	text => $list_array
			    	));
			    	break;
			    default:
			    	break;
			}
		}

		return $content_array;
	}


	/*
	*  add_worona_content_to_api
	*
	*  This function is called during the 'json_prepare_post' filter and will do things such as:
	*  prepare everything to output a new filed (worona_content) with the content the app needs from a post
	*
	*  @type	filter (json_prepare_post)
	*  @date	10/06/14
	*  @since	1.0.0
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
*  @since	1.0.0
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