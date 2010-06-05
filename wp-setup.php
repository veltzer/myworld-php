<?php

/*
 * this file is be included in wordpress files that wish to execute
 * my php code. It makes sure that they can include any of my files
 * and that they can create links to my php files
 */

ini_set("include_path", ini_get("include_path").":"."../php");

function get_root() {
	// this is hardcoded and needs to be changed...
//	return $BASE_PATH."/~mark/php/";
	return "http://veltzer.net/~mark/php/";
}

?>
