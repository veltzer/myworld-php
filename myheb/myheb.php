<?php
/*
Plugin Name: MyHeb
Plugin URI: http://veltzer.net/
Description: MyHeb plugin for WordPress
Version: 0.0.1
Author: Mark Veltzer <mark.veltzer@gmail.com> 
Author URI: http://www.veltzer.net 
*/

/*
 * The function that is called when displaying titles of articles
 *
 * Check if the title is hebrew and if so wrap it in the right tags...
 */
function myheb_is_hebrew($string) {
	//return ereg("^[א-ת\s\;\,\.\?\*\:]*$",$string,$regs);
	return ereg("[א-ת]",$string,$regs);
}

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
