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

/* Function to start an HTML table for you */
function get_start_table() {
	#return "<table style='empty-cells:show;width:100%;' border='1'>";
	return "<table style='empty-cells:show;width:100%;'>";
}

/* Function to put an audio player for a clip */
// check out: http://wpaudioplayer.com/frequently-asked-questions
function get_audio_player($url,$title,$composer,$poet) {
	$str="[audio:${url}|titles=${title}";
	if($composer!=NULL || $poet!=NULL) {
		if($composer!=NULL) {
			if($poet!=NULL) {
				$artists=join(", ",array($composer,$poet));
			} else {
				$artists=$composer;
			}
		} else {
			if($poet!=NULL) {
				$artists=$poet;
			} else {
				$artists="";
			}
		}
		$str.="|artists=${artists}";
	}
	$str.="]";
	return $str;
}

?>
