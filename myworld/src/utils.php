<?php

// assertion callback
function assert_callcack($file, $line, $expr) {
	//echo 'assertion failed<br/>';
	//echo 'file is '.$file.'<br/>';
	//echo 'line is '.$line.'<br/>';
	//echo 'message is '.$message.'<br/>';
	error_log('Assertion failed in '.$file.' on line '.$line.': '.$expr.'\n');
	//throw new Exception($file.$line.$message);
}

// assertion setup for the whole system
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
/* this is client side debugging */
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
	logger_setup(false);
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
	logger_log($query);
	$result=mysql_query($query);
	if(!$result) {
		error('mysql error: '.mysql_errno().': '.mysql_error());
	}
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

// function to do server side logging
function debug_print($string) {
	#built in php function to do logging...
	error_log('log from php ['.$string.']',0);
}

// print a line of =========== to start debug session
function debug_start() {
	debug_print('=====================================================');
}

// print both GET and POST params server side
function debug_params() {
	debug_print(serialize($_GET));
	debug_print(serialize($_POST));
}

// print GET params server side
function debug_get() {
	debug_print(serialize($_GET));
}

// print POST params server side
function debug_post() {
	debug_print(serialize($_POST));
}

function logger_setup($flag) {
	global $debug;
	$debug=$flag;
}

function logger_start() {
	global $debug;
	if($debug) {
		global $handle;
		// TODO: need to handle errors
		$handle=fopen('/tmp/phplog.txt','a+');
	}
}

function logger_log($msg) {
	global $debug;
	if($debug) {
		global $handle;
		// TODO: need to handle errors
		fwrite($handle,$msg);
		fflush($handle);
	}
}

function logger_close() {
	global $debug;
	if($debug) {
		global $handle;
		// TODO: need to handle errors
		fclose($handle);
	}
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
	for ($i=0;$size>1024;$i++) { $size /= 1024; }
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

/* return rows ready for json encoding */
function my_get_rows($result) {
	// iterate over every row
	$rows=array();
	while($row=mysql_fetch_assoc($result)) {
		// for every field in the result..
			for($i=0;$i<mysql_num_fields($result);$i++) {
			$info=mysql_fetch_field($result,$i);
			$type=$info->type;
			$val=$row[$info->name];
			if ($val!=null) {
				if($type=='real') {
					$row[$info->name]=doubleval($row[$info->name]);
				}
				if($type=='int') {
					$row[$info->name]=intval($row[$info->name]);
				}
			}
		}
		$rows[]=$row;
	}
	return $rows;
}

/* echo a result set in json style... */
function my_json_encode($result) {
	// JSON-ify all rows together as one big array
	return json_encode(my_get_rows($result));
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

// this is an example of a simple php string templating solution using the 'str_replace' function...
function template_it($str,$vars) {
	foreach($vars as $k => $v) {
		$str=str_replace("\$$k",$v,$str);
	}
	return $str;
}

function get_external_href($external_name,$external_id) {
	// TODO: this is very bad performance, always going to the database...
	$external_templates=my_mysql_query_hash('SELECT name,template FROM TbExternalType','name');
	$template=$external_templates[$external_name]['template'];
	//debug_print("template is [$template]");
	$vars=array();
	$vars['external_id']=$external_id;
	$result=template_it($template,$vars);
	//deubg_print("result is [$result]");
	return $result;
}

function get_full_name($hash,$honorifics_hash) {
	$honorificId=$hash['honorificId'];
	$firstname=$hash['firstname'];
	$surname=$hash['surname'];
	$othername=$hash['othername'];
	$ordinal=$hash['ordinal'];
	$arr=array();
	if($honorificId!=NULL) {
		array_push($arr,$honorifics_hash[$honorificId]['name']);
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
	$honorifics_hash=my_mysql_query_hash('SELECT * FROM TbIdHonorific','id');
	$query=sprintf('select id,honorificId,firstname,surname,othername,ordinal from TbIdPerson order by firstname,surname');
	$result=my_mysql_query($query);
	while($row=mysql_fetch_assoc($result)) {
		$row['label']=get_full_name($row,$honorifics_hash);
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

/* create an ORDER BY SQL snipplet from a json object coming from an EXT JS 4 control */
function create_order_by($p_sort) {
	if(count($p_sort)>0) {
		$results=array();
		foreach($p_sort as $directive) {
			$results[]=$directive->{'property'}.' '.$directive->{'direction'};
		}
		return 'ORDER BY '.join(', ',$results);
	} else {
		return '';
	}
}

?>
