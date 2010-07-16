<?php
/*
Plugin Name: MyWorld
Plugin URI: http://veltzer.net/
Description: MyWorld plugin for WordPress
Version: 0.0.1
Author: Mark Veltzer <mark.veltzer@gmail.com> 
Author URI: http://www.veltzer.net 
*/

/*
 * Handy function to include files once from my own plugin directory
 */
function my_include($file) {
	include_once(plugin_dir_path(__FILE__).$file);
}

function link_to_direct($direct_page) {
	return WP_PLUGIN_URL."/myworld/direct/".$direct_page;
}

/*
 * Plugin initialization function
 */
function myworld_init() {
}
add_action('init', 'myworld_init');

/*
 * Plugin shutdown function
 */
function myworld_admin_init() {
}
add_action('admin_init', 'myworld_admin_init');

/*
 * The function that creates dynamic content
 */
function myworld_create_content($what) {
	switch($what) {
		case "business":
			my_include("include/db.php");
			my_include("include/na.php");
			my_include("include/utils.php");
			my_include("frag/business.php");
			db_connect();
			$ret=create_business();
			db_disconnect();
			return $ret;
		case "works":
			my_include("include/db.php");
			my_include("include/na.php");
			my_include("include/utils.php");
			my_include("frag/works.php");
			db_connect();
			$ret=create_works();
			db_disconnect();
			return $ret;
		case "lilypond":
			my_include("include/db.php");
			my_include("include/na.php");
			my_include("include/utils.php");
			my_include("frag/lilypond.php");
			db_connect();
			$ret=create_lilypond();
			db_disconnect();
			return $ret;
		default:
			return "[$what] is unknown";
	}
}

/*
 * The function that hooks into WP to substitute content
 */
function myworld_the_content( $content ) {
	$pattern = "/\[myworld:\s*([^\]]+)\s*\]/";
	preg_match_all( $pattern, $content, $tags );
	foreach( $tags[0] as $k=>$cnt ) {
		$content=str_replace($cnt,myworld_create_content($tags[1][$k]),$content);
	}
	return $content;
}

add_filter('the_content','myworld_the_content');
