<?php

function my_include($page) {
	include_once($page);
}

function link_to_direct($direct_page) {
	#return get_root().$relative;
	return "../direct/".$direct_page;
}

?>
