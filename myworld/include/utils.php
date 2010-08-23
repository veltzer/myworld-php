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
 * Multi according JQuery implementation
 */
function multi_accordion_start() {
	return '<div class="myacc">';
}

function multi_accordion_header($header) {
	return '<div class="myacc_header">'.$header.'</div>';
}

function multi_accordion_body($body) {
	return '<div class="myacc_body">'.$body.'</div>';
}

function multi_accordion_entry($header,$body) {
	return '<div class="myacc_entry"><div class="myacc_header">'.$header.'</div><div class="myacc_body">'.$body.'</div></div>';
}

function multi_accordion_end() {
	return '</div>';
}

function calendar() {
	return '<div id="calendar"></div>';
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

/* function for embedding a ted video */
function ted_embed($tedid) {
	$width=446;
	$height=326;
	#$vid="http://video.ted.com/talks/dynamic/JeffreySkoll_2007-medium.flv";
	$vid="http://video.ted.com/talks/dynamic/JeffreySkoll_2007-small.flv";
	$jpg="http://images.ted.com/images/ted/tedindex/embed-posters/JeffreySkoll-2007.embed_thumbnail.jpg";
	return '<!--copy and paste--><object width="'.$width.'" height="'.$height.'"><param name="movie" value="http://video.ted.com/assets/player/swf/EmbedPlayer.swf"></param><param name="allowFullScreen" value="true" /><param name="allowScriptAccess" value="always"/><param name="wmode" value="transparent"></param><param name="bgColor" value="#ffffff"></param> <param name="flashvars" value="'.$vid.'&su='.$jpg.'&vw=432&vh=240&ap=0&ti=170&introDuration=15330&adDuration=4000&postAdDuration=830&adKeys=talk=jeff_skoll_makes_movies_that_make_change;year=2007;theme=media_that_matters;theme=master_storytellers;theme=the_creative_spark;theme=not_business_as_usual;event=TED2007;&preAdTag=tconf.ted/embed;tile=1;sz=512x288;" /><embed src="http://video.ted.com/assets/player/swf/EmbedPlayer.swf" pluginspace="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" wmode="transparent" bgColor="#ffffff" width="'.$width.'" height="'.$height.'" allowFullScreen="true" allowScriptAccess="always" flashvars="vu='.$vid.'&su='.$jpg.'&vw=432&vh=240&ap=0&ti=170&introDuration=15330&adDuration=4000&postAdDuration=830&adKeys=talk=jeff_skoll_makes_movies_that_make_change;year=2007;theme=media_that_matters;theme=master_storytellers;theme=the_creative_spark;theme=not_business_as_usual;event=TED2007;"></embed></object>';
}

?>
