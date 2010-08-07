<?php

function assert_callcack($file, $line, $message) {
	echo "file is {$file}<br/>";
	echo "line is {$line}<br/>";
	echo "message is {$message}<br/>";
	throw new Exception($file.$line.$message);
}

function db_connect() {
	$db_host='localhost';
	$db_user='mark';
	$db_pwd='';
	$database='myworld';

	# call our own assert function
	assert_options(ASSERT_CALLBACK,'assert_callcack');
	# make asserts actually work
	assert_options(ASSERT_ACTIVE,1);
	# make sure that we do not continue execution on failed assertions...
	assert_options(ASSERT_BAIL,1);
	# do not show the standard php assert warning (we will do it on our own...)
	assert_options(ASSERT_WARNING,1);
	#assert_options(ASSERT_QUIET_EVAL,0);


	global $link;
	$link=mysql_connect($db_host,$db_user,$db_pwd);
	assert($link);
	assert(mysql_select_db($database));
	# I'm not sure if I need this...
	//assert(mysql_set_charset('utf8',$link));
}

function db_disconnect() {
	global $link;
	assert(mysql_close($link));
}

?>