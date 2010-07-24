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

function link_to_resource($resource) {
	return WP_PLUGIN_URL."/myworld/resources/".$resource;
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

my_include("include/db.php");
my_include("include/na.php");
my_include("include/utils.php");
my_include("frag/business.php");
my_include("frag/works.php");
my_include("frag/lilypond.php");

/*
 * The function that creates dynamic content
 */
function myworld_create_content($what) {
	db_connect();
	switch($what) {
		case "courses":
			$ret=create_courses();
			break;
		case "consulting":
			$ret=create_consulting();
			break;
		case "teaching":
			$ret=create_teaching();
			break;
		case "certification":
			$ret=create_certification();
			break;
		case "works":
			$ret=create_works();
			break;
		case "lilypond":
			$ret=create_lilypond();
			break;
		default:
			$ret="[$what] is unknown";
			break;
	}
	#$ret="<br/>$ret<br/>";
	db_disconnect();
	return $ret;
}

/*
 * The function that hooks into WP to substitute content
 */
function myworld_the_content($content) {
	$content=myworld_the_title($content);
	$pattern = "/\[myworld:\s*([^\]]+)\s*\]/";
	preg_match_all( $pattern, $content, $tags );
	foreach( $tags[0] as $k=>$cnt ) {
		$content=str_replace($cnt,myworld_create_content($tags[1][$k]),$content);
	}
	return $content;
}
/*
 * The function that is called when displaying titles of articles
 *
 * Check if the title is hebrew and if so wrap it in the right tags...
 */
function isHebrew($string) {
	return ereg("[א-ת]",$string,$regs);
}

function myworld_the_title($title) {
	if (!is_admin() && isHebrew($title)) {
		$title="<div class=hebtitle>".$title."</div>";
	}
	return $title;
}

add_filter('the_content','myworld_the_content');
add_filter('the_title','myworld_the_title');
add_filter('the_excerpt','myworld_the_title');
