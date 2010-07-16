<?php

$BASE_PATH = dirname(__FILE__);
ini_set("include_path", ini_get("include_path").":".$BASE_PATH);

function get_root() {
	// this is hardcoded and needs to be changed...
//	return $BASE_PATH."/~mark/php/";
	return "http://veltzer.net/~mark/php/";
}

function my_include($page) {
	include_once($page);
}

function link_to($relative) {
	#return get_root().$relative;
	return $relative;
}

?>
