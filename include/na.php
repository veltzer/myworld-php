<?php

function get_na_string() {
	return "N/A";
}
function val_or_na($val) {
	if($val==NULL) {
		return get_na_string();
	} else {
		return $val;
	}
}

?>
