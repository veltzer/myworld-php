<?php

function assert_callcack($file, $line, $expr) {
	//echo 'assertion failed<br/>';
	//echo 'file is '.$file.'<br/>';
	//echo 'line is '.$line.'<br/>';
	//echo 'message is '.$message.'<br/>';
	print 'Assertion failed in '.$file.' on line '.$line.': '.$expr.'\n';
	//throw new Exception($file.$line.$message);
}

function assert_setup() {
	# call our own assert function
	assert_options(ASSERT_CALLBACK,'assert_callcack');
	# make asserts actually work
	assert_options(ASSERT_ACTIVE,1);
	# make sure that we do not continue execution on failed assertions...
	assert_options(ASSERT_BAIL,1);
	# do not show the standard php assert warning (we will do it on our own...)
	assert_options(ASSERT_WARNING,0);
	# make php be quiet when doing assertions...
	assert_options(ASSERT_QUIET_EVAL,1);

	error_reporting(E_ALL);
	ini_set('display_errors',1);
}

function utils_init() {
	assert_setup();
	db_connect();
}

function utils_finish() {
	db_disconnect();
}

function db_init() {
	global $link;
	$line=NULL;
}

function db_connect() {
	global $link;
	if($link==NULL) {
		$db_host='localhost';
		$db_user='mark';
		$db_pwd='';
		$database='myworld';
		$link=mysql_connect($db_host,$db_user,$db_pwd);
		assert($link);
		assert(mysql_select_db($database));
		# I'm not sure if I need this...
		//assert(mysql_set_charset('utf8',$link));
	}
}

function db_disconnect() {
	// TODO: this code is in remark because it causes wordpress issues
	// fix it!
	/*
	global $link;
	if($link!=NULL) {
		assert(mysql_close($link));
		$link=NULL;
	}
	 */
}

# function that gets an error message and triggers an error for the whole page
function error($msg) {
	header('HTTP/1.1 500 Internal Server Error');
	trigger_error($msg,E_USER_ERROR);
}

function my_mysql_query($query) {
	db_connect();
	$result=mysql_query($query);
	if(!$result) {
		error('mysql error: '.mysql_errno().': '.mysql_error());
	}
	return $result;
}

function my_mysql_query_one($query) {
	db_connect();
	$result=my_mysql_query($query);
	$row=mysql_fetch_array($result,MYSQL_NUM);
	$ret=$row[0];
	assert(mysql_free_result($result));
	return $ret;
}

// do a query expecting one row as result (throw exception if not so). Return only that one
// row...
function my_mysql_query_one_row($query) {
	db_connect();
	$result=my_mysql_query($query);
	$row=mysql_fetch_assoc($result);
	// TODO: throw exception if there are more results...
	assert(mysql_free_result($result));
	return $row;
}

// do a query and return a hash of the results by key...
function my_mysql_query_hash($query,$hash_key) {
	db_connect();
	$result=my_mysql_query($query);
	$ret=array();
	while($row=mysql_fetch_assoc($result)) {
		$ret[$row[$hash_key]]=$row;
	}
	#debug: print the array...
	#print_r($ret);
	assert(mysql_free_result($result));
	return $ret;
}

function phpdate_to_mysqldate($phpdate) {
	return date('Y-m-d H:i:s',$phpdate);
}

function javascriptdate_to_phpdate($javascriptdate) {
	return strtotime($javascriptdate);
}

function javascriptdate_to_mysqldate($javascriptdate) {
	return phpdate_to_mysqldate(javascriptdate_to_phpdate($javascriptdate));
}

function my_assert($val,$reason) {
	if(!$val) {
		error('assert error: '.$reason);
	}
}

function my_get_post($field) {
	if(array_key_exists($field,$_POST)) {
		return $_POST[$field];
	} else {
		error('must have field ['.$field.']');
	}
}

function my_get_post_or_null($field) {
	$val=my_get_post($field);
	if($val=='') {
		$val=NULL;
	}
	return $val;
}

function my_mysql_real_escape_string($str) {
	if($str!=NULL) {
		return '\''.mysql_real_escape_string($str).'\'';
	} else {
		return 'NULL';
	}
}

function my_get_get($field) {
	if(array_key_exists($field,$_GET)) {
		return $_GET[$field];
	} else {
		error('must have field ['.$field.']');
	}
}

function get_na_string() {
	return 'N/A';
}
# function that returns 'N/A' or the string according to whether the string is NULL
function val_or_na($val) {
	if($val==NULL) {
		return get_na_string();
	} else {
		return $val;
	}
}

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
	#return '<table style='empty-cells:show;width:100%;' border='1'>';
	return '<table style=\'empty-cells:show;width:100%;\'>';
}

/* Function to put an audio player for a clip */
// check out: http://wpaudioplayer.com/frequently-asked-questions
function get_audio_player($url,$title,$composer,$poet) {
	$str='[audio:'.$url.'|titles='.$title;
	if($composer!=NULL || $poet!=NULL) {
		if($composer!=NULL) {
			if($poet!=NULL) {
				if($composer!=$poet) {
					$artists=$composer.' - '.$poet;
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
				$artists='';
			}
		}
		$str.='|artists='.$artists;
	}
	$str.=']';
	return $str;
}

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

function calendar() {
	$ret='';
	$ret.='<div id=\'loading\' style=\'display:none\'>loading...</div>';
	$ret.='<div id=\'calendar\'></div>';
	return $ret;
}

/* functions for embedding stuff from youtube */
function youtube_embed($youtube_id,$size_factor) {
	$width=480*$size_factor;
	$height=385*$size_factor;
	# this is taken from going over to youtube, seeing a movie and pussing the 'embed'
	# button which gave me this text...
	return '<object width=\''.$width.'\' height=\''.$height.'\'><param name=\'movie\' value=\'http://www.youtube.com/v/'.$youtube_id.'?fs=1&amp;hl=en_US\'></param><param name=\'allowFullScreen\' value=\'true\'></param><param name=\'allowscriptaccess\' value=\'always\'></param><embed src=\'http://www.youtube.com/v/'.$youtube_id.'?fs=1&amp;hl=en_US\' type=\'application/x-shockwave-flash\' allowscriptaccess=\'always\' allowfullscreen=\'true\' width=\''.$width.'\' height=\''.$height.'\'></embed></object>';
}

function get_external_href($external_name,$external_id) {
	if($external_name=='imdb') {
		return 'http://www.imdb.com/title/tt'.$external_id.'/';
	}
	if($external_name=='TTC') {
		return 'http://www.teach12.com/ttcx/coursedesclong2.aspx?cid='.$external_id;
	}
	if($external_name=='youtube_vid') {
		return 'http://www.youtube.com/v/'.$external_id;
	}
	if($external_name=='amazon') {
		return 'http://www.amazon.com/gp/product/'.$external_id.'/';
	}
	if($external_name=='blog') {
		return $external_id;
	}
	if($external_name=='website') {
		return $external_id;
	}
	if($external_name=='facebook') {
		return 'http://www.facebook.com/'.$external_id;
	}
	if($external_name=='linkedin') {
		return 'http://il.linkedin.com/in/'.$external_id;
	}
	if($external_name=='twitter') {
		return 'http://twitter.com/'.$external_id;
	}
	if($external_name=='google') {
		return 'http://www.google.com/profiles/'.$external_id;
	}
	if($external_name=='picasa') {
		return 'http://picasaweb.google.com/'.$external_id;
	}
	if($external_name=='youtube') {
		return 'http://www.youtube.com/user/'.$external_id;
	}
	if($external_name=='ted') {
		return 'http://www.ted.com/profiles/view/id/'.$external_id;
	}
	if($external_name=='google_reader') {
		return 'http://www.google.com/reader/shared/'.$external_id;
	}
	if($external_name=='scribd') {
		return 'http://www.scribd.com/'.$external_id;
	}
	if($external_name=='hi5') {
		return 'http://www.hi5.com/friend/profile/displayProfile.do?userid='.$external_id;
	}
	error('what external name is ['.$external_name.']');
}

/* function for embedding a ted video */
function ted_embed($tedid) {
	$width=446;
	$height=326;
	#$vid='http://video.ted.com/talks/dynamic/JeffreySkoll_2007-medium.flv';
	$vid='http://video.ted.com/talks/dynamic/JeffreySkoll_2007-small.flv';
	$jpg='http://images.ted.com/images/ted/tedindex/embed-posters/JeffreySkoll-2007.embed_thumbnail.jpg';
	return '<!--copy and paste--><object width=\''.$width.'\' height=\''.$height.'\'><param name=\'movie\' value=\'http://video.ted.com/assets/player/swf/EmbedPlayer.swf\'></param><param name=\'allowFullScreen\' value=\'true\' /><param name=\'allowScriptAccess\' value=\'always\'/><param name=\'wmode\' value=\'transparent\'></param><param name=\'bgColor\' value=\'#ffffff\'></param> <param name=\'flashvars\' value=\''.$vid.'&su='.$jpg.'&vw=432&vh=240&ap=0&ti=170&introDuration=15330&adDuration=4000&postAdDuration=830&adKeys=talk=jeff_skoll_makes_movies_that_make_change;year=2007;theme=media_that_matters;theme=master_storytellers;theme=the_creative_spark;theme=not_business_as_usual;event=TED2007;&preAdTag=tconf.ted/embed;tile=1;sz=512x288;\' /><embed src=\'http://video.ted.com/assets/player/swf/EmbedPlayer.swf\' pluginspace=\'http://www.macromedia.com/go/getflashplayer\' type=\'application/x-shockwave-flash\' wmode=\'transparent\' bgColor=\'#ffffff\' width=\''.$width.'\' height=\''.$height.'\' allowFullScreen=\'true\' allowScriptAccess=\'always\' flashvars=\'vu='.$vid.'&su='.$jpg.'&vw=432&vh=240&ap=0&ti=170&introDuration=15330&adDuration=4000&postAdDuration=830&adKeys=talk=jeff_skoll_makes_movies_that_make_change;year=2007;theme=media_that_matters;theme=master_storytellers;theme=the_creative_spark;theme=not_business_as_usual;event=TED2007;\'></embed></object>';
}

/* function to know whether we are running on the command line */
function isCli() {
	if(php_sapi_name() == 'cli' && empty($_SERVER['REMOTE_ADDR'])) {
		return true;
	} else {
		return false;
	}
}

function printDebug($string) {
	if(isCli()) {
		echo $string."\n";
	} else {
		echo $string.'<br/>';
	}
}
// echo a result set in json style...
function my_json_encode($result) {
	// iterate over every row
	while ($row = mysql_fetch_assoc($result)) {
		// for every field in the result..
		for ($i=0; $i < mysql_num_fields($result); $i++) {
			$info = mysql_fetch_field($result, $i);
			$type = $info->type;
			// cast for real
			if ($type == 'real')
				$row[$info->name] = doubleval($row[$info->name]);
			// cast for int
			if ($type == 'int')
				$row[$info->name] = intval($row[$info->name]);
		}
		$rows[] = $row;
	}
	// JSON-ify all rows together as one big array
	return json_encode($rows);
}

function get_full_name($hash) {
	$firstname=$hash['firstname'];
	$surname=$hash['surname'];
	$othername=$hash['othername'];
	$ordinal=$hash['ordinal'];
	$arr=array();
	if($firstname!=NULL) {
		array_push($arr,$firstname);
	}
	if($othername!=NULL) {
		array_push($arr,$othername);
	}
	if($surname!=NULL) {
		array_push($arr,$surname);
	}
	if($ordinal!=NULL) {
		array_push($arr,$ordinal);
	}
	return join(' ',$arr);
}

function get_person_data() {
	$query=sprintf('select id,firstname,surname,othername,ordinal from TbIdPerson');
	$result=my_mysql_query($query);
	while($row=mysql_fetch_assoc($result)) {
		$row['label']=get_full_name($row);
		$rows[]=$row;
	}
	return $rows;
}

?>
