<?php

# input is in bytes
function formatSize($size) {
	$units=array('B','KB','MB','GB','TB');
	for ($i = 0; $size > 1024; $i++) { $size /= 1024; }
	return round($size, 2).' '.$units[$i];
}
# input is in seconds
function formatTimeperiod($size) {
	$units=array('secs','mins','hrs','days','months','years');
	$mults=array(60,60,24,30,12);
	$i=0;
	while($size>$mults[$i]) {
		$size/=$mults[$i];
		$i++;
	}
	return round($size, 2).' '.$units[$i];
}

function get_start_table() {
	#return "<table style='empty-cells:show;width:100%;' border='1'>";
	return "<table style='empty-cells:show;width:100%;'>";
}

?>
