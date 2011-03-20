<?php

/*
 * Multi according JQuery implementation
 */
function multi_accordion_start() {
	return '<div class=\'myacc\'>';
}

function multi_accordion_header($header) {
	return '<div class=\'myacc_header\'>'.$header.'</div>';
}

function multi_accordion_body($body) {
	return '<div class=\'myacc_body\'>'.$body.'</div>';
}

function multi_accordion_entry($header,$body) {
	return '<div class=\'myacc_entry\'><div class=\'myacc_header\'>'.$header.'</div><div class=\'myacc_body\'>'.$body.'</div></div>';
}

function multi_accordion_end() {
	return '</div>';
}

?>
