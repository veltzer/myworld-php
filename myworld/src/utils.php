<?php

/* assertion */
function assert_callcack($file, $line, $expr) {
	//echo 'assertion failed<br/>';
	//echo 'file is '.$file.'<br/>';
	//echo 'line is '.$line.'<br/>';
	//echo 'message is '.$message.'<br/>';
	print 'Assertion failed in '.$file.' on line '.$line.': '.$expr.'\n';
	//throw new Exception($file.$line.$message);
}

/* assertion */
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

/* function that gets an error message and triggers an error for the whole page */
function error($msg) {
	header('HTTP/1.1 500 Internal Server Error');
	trigger_error($msg,E_USER_ERROR);
}

/* function to know whether we are running on the command line */
function isCli() {
	if(php_sapi_name() == 'cli' && empty($_SERVER['REMOTE_ADDR'])) {
		return true;
	} else {
		return false;
	}
}

/* function to print debug information correctly for cli and non cli mode */
function printDebug($string) {
	if(isCli()) {
		echo $string."\n";
	} else {
		echo $string.'<br/>';
	}
}

/* my own assertion with my own error printing function */
function my_assert($val,$reason) {
	if(!$val) {
		error('assert error: '.$reason);
	}
}

/* initialize the utils system */
function utils_init() {
	assert_setup();
	my_mysql_connect();
}

/* finalize the utils system */
function utils_finish() {
	my_mysql_disconnect();
}

/* wrapper around mysql connect
 * This adds transactino mode and throws an exception if something goes wrong.
 * It also moves the connection to utf8
 */
function my_mysql_connect() {
	global $link;
	if($link==NULL) {
		$db_host='localhost';
		$db_user='mark';
		$db_pwd='';
		$database='myworld';
		$link=mysql_connect($db_host,$db_user,$db_pwd);
		assert($link);
		assert(mysql_select_db($database));
		my_mysql_query('SET AUTOCOMMIT=0');
		# I need this because the default client configuration is for latin1.
		# The thing is that this config is hard to detect since if you turn it
		# off then inserting AND extracting from the db in hebrew will WORK and
		# the data in the command line mysql client will look ok but in fact it is
		# not UTF in the db. USE THIS!!!
		assert(mysql_set_charset('utf8',$link));
	}
}

/* disconnect from mysql */
function my_mysql_disconnect() {
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

/* free a result set from mysql */
function my_mysql_free_result($result) {
	assert(mysql_free_result($result));
}

/* A wrapper for mysql_query */
function my_mysql_query($query) {
	$result=mysql_query($query);
	if(!$result) {
		error('mysql error: '.mysql_errno().': '.mysql_error());
	}
	#built in php function to do logging...
	#error_log('my_mysql_query doing query ['.$query.']',0);
	return $result;
}

/* A query which is assertained to return 1 result */
function my_mysql_query_one($query) {
	$result=my_mysql_query($query);
	# we should only get one result...
	assert(mysql_num_rows($result)==1);
	$row=mysql_fetch_array($result,MYSQL_NUM);
	$ret=$row[0];
	my_mysql_free_result($result);
	return $ret;
}

/* do a query expecting one row as result (throw exception if not so). Return only that one
 * row... */
function my_mysql_query_one_row($query) {
	$result=my_mysql_query($query);
	# we should only get one result...
	assert(mysql_num_rows($result)==1);
	$row=mysql_fetch_assoc($result);
	my_mysql_free_result($result);
	return $row;
}

/* do a query and return a hash of the results by key... */
function my_mysql_query_hash($query,$hash_key) {
	$result=my_mysql_query($query);
	$ret=array();
	while($row=mysql_fetch_assoc($result)) {
		$ret[$row[$hash_key]]=$row;
	}
	#debug: print the array...
	#print_r($ret);
	my_mysql_free_result($result);
	return $ret;
}

/* start transaction */
function my_mysql_start_transaction() {
	my_mysql_query('START TRANSACTION');
}

/* commit a transaction */
function my_mysql_commit() {
	my_mysql_query('COMMIT');
}

/* escape strings in my own style */
function my_mysql_real_escape_string($str) {
	if($str!=NULL) {
		return '\''.mysql_real_escape_string($str).'\'';
	} else {
		return 'NULL';
	}
}

/* convert php dates to mysql */
function phpdate_to_mysqldate($phpdate) {
	return date('Y-m-d H:i:s',$phpdate);
}

/* convert javascript dates to php dates */
function javascriptdate_to_phpdate($javascriptdate) {
	return strtotime($javascriptdate);
}

/* convert javascript dates to mysql dates */
function javascriptdate_to_mysqldate($javascriptdate) {
	return phpdate_to_mysqldate(javascriptdate_to_phpdate($javascriptdate));
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

function my_get_get($field) {
	if(array_key_exists($field,$_GET)) {
		return $_GET[$field];
	} else {
		error('must have field ['.$field.']');
	}
}

/* a string to be printed when there is no data to print */
function get_na_string() {
	return 'N/A';
}

/* function that returns 'N/A' or the string according to whether the string is NULL */
function val_or_na($val) {
	if($val==NULL) {
		return get_na_string();
	} else {
		return $val;
	}
}

/* convert bytes to something which is printable on screen */
function formatSize($size) {
	$units=array('B','KB','MB','GB','TB');
	for ($i = 0; $size > 1024; $i++) { $size /= 1024; }
	return round($size, 2).' '.$units[$i];
}

/* convert seconds to something which is printable on screen */
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

/* echo a result set in json style... */
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

/* functions for embedding stuff from youtube */
function embed_youtube($youtube_id,$size_factor) {
	$width=480*$size_factor;
	$height=385*$size_factor;
	# this is taken from going over to youtube, seeing a movie and pussing the 'embed'
	# button which gave me this text...
	return '<object width=\''.$width.'\' height=\''.$height.'\'><param name=\'movie\' value=\'http://www.youtube.com/v/'.$youtube_id.'?fs=1&amp;hl=en_US\'></param><param name=\'allowFullScreen\' value=\'true\'></param><param name=\'allowscriptaccess\' value=\'always\'></param><embed src=\'http://www.youtube.com/v/'.$youtube_id.'?fs=1&amp;hl=en_US\' type=\'application/x-shockwave-flash\' allowscriptaccess=\'always\' allowfullscreen=\'true\' width=\''.$width.'\' height=\''.$height.'\'></embed></object>';
}

/* function for embedding a ted video */
function embed_ted($tedid) {
	$width=446;
	$height=326;
	#$vid='http://video.ted.com/talks/dynamic/JeffreySkoll_2007-medium.flv';
	$vid='http://video.ted.com/talks/dynamic/JeffreySkoll_2007-small.flv';
	$jpg='http://images.ted.com/images/ted/tedindex/embed-posters/JeffreySkoll-2007.embed_thumbnail.jpg';
	return '<!--copy and paste--><object width=\''.$width.'\' height=\''.$height.'\'><param name=\'movie\' value=\'http://video.ted.com/assets/player/swf/EmbedPlayer.swf\'></param><param name=\'allowFullScreen\' value=\'true\' /><param name=\'allowScriptAccess\' value=\'always\'/><param name=\'wmode\' value=\'transparent\'></param><param name=\'bgColor\' value=\'#ffffff\'></param> <param name=\'flashvars\' value=\''.$vid.'&su='.$jpg.'&vw=432&vh=240&ap=0&ti=170&introDuration=15330&adDuration=4000&postAdDuration=830&adKeys=talk=jeff_skoll_makes_movies_that_make_change;year=2007;theme=media_that_matters;theme=master_storytellers;theme=the_creative_spark;theme=not_business_as_usual;event=TED2007;&preAdTag=tconf.ted/embed;tile=1;sz=512x288;\' /><embed src=\'http://video.ted.com/assets/player/swf/EmbedPlayer.swf\' pluginspace=\'http://www.macromedia.com/go/getflashplayer\' type=\'application/x-shockwave-flash\' wmode=\'transparent\' bgColor=\'#ffffff\' width=\''.$width.'\' height=\''.$height.'\' allowFullScreen=\'true\' allowScriptAccess=\'always\' flashvars=\'vu='.$vid.'&su='.$jpg.'&vw=432&vh=240&ap=0&ti=170&introDuration=15330&adDuration=4000&postAdDuration=830&adKeys=talk=jeff_skoll_makes_movies_that_make_change;year=2007;theme=media_that_matters;theme=master_storytellers;theme=the_creative_spark;theme=not_business_as_usual;event=TED2007;\'></embed></object>';
}

/* function to be call directly from the plugin */
function create_ted($params) {
	return embed_ted($params['id']);
}

function get_external_href($external_name,$external_id) {
	switch($external_name) {
		case 'imdb':
			return 'http://www.imdb.com/title/tt'.$external_id.'/';
			break;
		case 'TTC course':
			return 'http://www.teach12.com/ttcx/coursedesclong2.aspx?cid='.$external_id;
			break;
		case 'TTC professor':
			return 'http://www.teach12.com/tgc/professors/professor_detail.aspx?pid='.$external_id;
			break;
		case 'youtube_vid':
			return 'http://www.youtube.com/v/'.$external_id;
			break;
		case 'amazon':
			return 'http://www.amazon.com/gp/product/'.$external_id.'/';
			break;
		case 'blog':
			return $external_id;
			break;
		case 'website':
			return $external_id;
			break;
		case 'url':
			return $external_id;
			break;
		case 'homepage':
			return $external_id;
			break;
		case 'profile':
			return $external_id;
			break;
		case 'facebook':
			return 'http://www.facebook.com/'.$external_id;
			break;
		case 'linkedin':
			return 'http://il.linkedin.com/in/'.$external_id;
			break;
		case 'twitter':
			return 'http://twitter.com/'.$external_id;
			break;
		case 'google':
			return 'http://www.google.com/profiles/'.$external_id;
			break;
		case 'picasa':
			return 'http://picasaweb.google.com/'.$external_id;
			break;
		case 'youtube':
			return 'http://www.youtube.com/user/'.$external_id;
			break;
		case 'ted':
			return 'http://www.ted.com/profiles/view/id/'.$external_id;
			break;
		case 'ted_speaker':
			return 'http://www.ted.com/speakers/'.$external_id.'.html';
			break;
		case 'google_reader':
			return 'http://www.google.com/reader/shared/'.$external_id;
			break;
		case 'scribd':
			return 'http://www.scribd.com/'.$external_id;
			break;
		case 'hi5':
			return 'http://www.hi5.com/friend/profile/displayProfile.do?userid='.$external_id;
			break;
		case 'digg':
			return 'http://digg.com/'.$external_id;
			break;
		case 'plaxo':
			return 'http://www.plaxo.com/profile/show/'.$external_id;
			break;
		case 'yahoo':
			return 'http://profiles.yahoo.com/'.$external_id;
			break;
		case 'github':
			return 'https://github.com/'.$external_id;
			break;
		case 'sourceforge':
			return 'https://sourceforge.net/users/'.$external_id;
			break;
		case 'imdb_user':
			return 'http://www.imdb.com/user/ur'.$external_id.'/';
			break;
		case 'amazon_user':
			return 'http://www.amazon.com/gp/pdp/profile/'.$external_id;
			break;
		case 'cpan':
			return 'http://search.cpan.org/~'.$external_id.'/';
			break;
		case 'advogato':
			return 'http://advogato.org/person/'.$external_id.'/';
			break;
		case 'about.me':
			return 'http://about.me/'.$external_id.'/';
			break;
		case 'tripit':
			return 'http://www.tripit.com/people/'.$external_id;
			break;
		case 'ibmdw':
			return 'https://www.ibm.com/developerworks/mydeveloperworks/profiles/html/profileView.do?key='.$external_id.'&lang=en';
			break;
		case 'en_wikipedia':
			return 'http://en.wikipedia.org/wiki/'.$external_id;
			break;
		case 'icq':
			return 'http://www.icq.com/people/'.$external_id;
			break;
		case 'ISBN':
			return 'http://isbn.org/find='.$external_id;
			break;
		case 'he_wikipedia':
			return 'http://he.wikipedia.org/wiki/'.$external_id;
			break;
		case 'personal eid':
			return 'http://personal.org/eid/'.$external_id;
			break;
		case 'personal rid':
			return 'http://personal.org/rid/'.$external_id;
			break;
		default:
			error('what external name is ['.$external_name.']');
			break;
	}
}

function get_full_name($hash) {
	$honorific=$hash['honorific'];
	$firstname=$hash['firstname'];
	$surname=$hash['surname'];
	$othername=$hash['othername'];
	$ordinal=$hash['ordinal'];
	$arr=array();
	if($honorific!=NULL) {
		array_push($arr,$honorific);
	}
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
	$query=sprintf('select id,honorific,firstname,surname,othername,ordinal from TbIdPerson order by firstname,surname');
	$result=my_mysql_query($query);
	while($row=mysql_fetch_assoc($result)) {
		$row['label']=get_full_name($row);
		$rows[]=$row;
	}
	return $rows;
}

/* generic function to print a statistics line */
function make_stat($query,$func,$desc) {
	$result=my_mysql_query_one($query);
	if($func!=null) {
		$result=$func($result);
	}
	return '<a title="'.$query.'">'.$desc.' = '.$result.'</a><br/>';
}
/* generic function to print a table */
function make_table($query,$desc) {
	$result=my_mysql_query($query);
	$res='';
	$res.='<a title="'.$query.'">'.$desc.'</a>';
	$res.='<table><tbody>';
	$first=true;
	while($row=mysql_fetch_assoc($result)) {
		if($first) {
			$res.='<tr>';
			# iterate the result and print the headers...
			foreach($row as $k => $v) {
				$res.='<td>'.$k.'</td>';
			}
			$first=false;
			$res.='</tr>';
		}
		$res.='<tr>';
		# iterate the result and print the content
		foreach($row as $k => $v) {
			$res.='<td>'.$v.'</td>';
		}
		$res.='</tr>';
	}
	$res.='</tbody></table>';
	$res.='<br/>';
	my_mysql_free_result($result);
	return $res;
}

?>
