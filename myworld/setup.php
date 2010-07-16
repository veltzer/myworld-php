<?php

/*
 * This is an include file for "direct pages"-> pages that do not go through wordpress
 */

function my_include($page) {
	include_once($page);
}

function link_to_direct($direct_page) {
	#return get_root().$relative;
	return "../direct/".$direct_page;
}

function link_to_resource($resource) {
	#return get_root().$relative;
	return "../resource/".$resource;
}

?>
