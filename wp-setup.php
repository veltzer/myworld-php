<?php

/*
 * this file is be included in wordpress files that wish to execute
 * my php code. It makes sure that they can include any of my files
 * and that they can create href links to my files residing in my php
 * folder (like images and the like...). 
 */

// constant which is word press relation to the php code...
define("REL_PATH","/home/mark/public_html/php");
ini_set("include_path", ini_get("include_path").":".REL_PATH);

// this returns the php codes position for file inclusion
function get_file_root() {
	return REL_PATH;
}
// this returns the php codes position for urls
function get_root() {
	return "http://veltzer.net/~mark/php";
}

function debug() {
	echo ini_get("include_path")."<br/>";
	echo __FILE__."<br/>";
	echo dirname(__FILE__)."<br/>";
	echo print_r($_SERVER)."<br/>";
}

?>
