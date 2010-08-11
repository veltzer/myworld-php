<?php
/*
Plugin Name: MyHeb
Plugin URI: http://veltzer.net/
Description: MyHeb plugin for WordPress
Version: 0.0.2
Author: Mark Veltzer <mark.veltzer@gmail.com> 
Author URI: http://www.veltzer.net 
*/

/*
 * This is the core detection function...
 */
function myheb_is_hebrew($string) {
	//return ereg("^[א-ת\s\;\,\.\?\*\:]*$",$string,$regs);
	return ereg("[א-ת]",$string,$regs);
}

/*
 * Wrap content in a hebrew div
 */
function myheb_wrap_in_div($content,$class) {
	$content="<div class='".$class."'>".$content."</div>";
	return $content;
}

/*
 * The function that hooks into WP to substitute titles
 */
function myheb_the_title($content) {
	if (!is_admin() && myheb_is_hebrew($content)) {
		$content=myheb_wrap_in_div($content,'hebtitle');
	}
	return $content;
}
add_filter('the_title','myheb_the_title',-10);

/*
 * The function that hooks into WP to substitute content
 */
function myheb_the_content($content) {
	if (!is_admin() && myheb_is_hebrew($content)) {
		$content=myheb_wrap_in_div($content,'hebcontent');
	}
	return $content;
}
add_filter('the_content','myheb_the_content',-10);

/*
 * The function that hooks into WP to substitute excerpts
 */
function myheb_the_excerpt($content) {
	if (!is_admin() && myheb_is_hebrew($content)) {
		$content=myheb_wrap_in_div($content,'hebexcerpt');
	}
	return $content;
}
add_filter('the_excerpt','myheb_the_excerpt',-10);

/*
 * Add our own style sheet at the css part of the document
 */
function myheb_wp_head() {
	$url=plugins_url('css/style.css',__FILE__);
	echo "<link rel='stylesheet' id='myheb-css' href='{$url}' type='text/css' media='screen' />\n";
}
add_action('wp_head','myheb_wp_head');
