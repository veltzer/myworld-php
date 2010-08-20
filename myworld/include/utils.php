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
				if($composer!=$poet) {
					$artists=$composer." - ".$poet;
				} else {
					$artists=$composer;
				}
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

/*
 * JQuery helpers start here
 */
function multi_accordion_start() {
	return "<div class=\"myacc\">\n";
}

function multi_accordion_header($header) {
	return "<div class=\"myacc_header\">{$header}</div>";
}

function multi_accordion_body($body) {
	return "<div class=\"myacc_body\">{$body}</div>";
}

function multi_accordion_entry($header,$body) {
	return "<div class=\"myacc_entry\"><div class=\"myacc_header\">{$header}</div>\n<div class=\"myacc_body\">{$body}</div></div>\n";
}

function multi_accordion_end() {
	return "</div>\n";
}

/* functions for embedding stuff from youtube */
function youtube_embed($youtube_id,$size_factor) {
	$width=480*$size_factor;
	$height=385*$size_factor;
	# this is taken from going over to youtube, seeing a movie and pussing the "embed"
	# button which gave me this text...
	return "<object width=\"$width\" height=\"$height\"><param name=\"movie\" value=\"http://www.youtube.com/v/$youtube_id?fs=1&amp;hl=en_US\"></param><param name=\"allowFullScreen\" value=\"true\"></param><param name=\"allowscriptaccess\" value=\"always\"></param><embed src=\"http://www.youtube.com/v/$youtube_id?fs=1&amp;hl=en_US\" type=\"application/x-shockwave-flash\" allowscriptaccess=\"always\" allowfullscreen=\"true\" width=\"$width\" height=\"$height\"></embed></object>";
}

function youtube_id_to_url($youtube_id) {
	return "http://www.youtube.com/v/$youtube_id";
}

?>
