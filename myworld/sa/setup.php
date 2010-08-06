<?php

function my_include($page) {
	include_once("../".$page);
}

function link_to_direct($direct_page) {
	return "../direct/".$direct_page;
}

function link_to_resource($resource) {
	return "../resources/".$resource;
}

?>
