<?php

$db_host='localhost';
$db_user='mark';
$db_pwd='';

$database='myworld';

# TODO:
# Make this script go throught the groups tables to render the creators and viewers.

function assert_callcack($file, $line, $message) {
	echo "file is {$file}<br/>";
	echo "line is {$line}<br/>";
	echo "message is {$message}<br/>";
	throw new Exception($file.$line.$message);
}

# call our own assert function
assert_options(ASSERT_CALLBACK,'assert_callcack');
# make asserts actually work
assert_options(ASSERT_ACTIVE,1);
# make sure that we do not continue execution on failed assertions...
assert_options(ASSERT_BAIL,1);
# do not show the standard php assert warning (we will do it on our own...)
assert_options(ASSERT_WARNING,1);
#assert_options(ASSERT_QUIET_EVAL,0);

$link=mysql_connect($db_host,$db_user,$db_pwd);
assert($link);
assert(mysql_select_db($database));

# this needs to be done after reading the data...
#assert(mysql_close($link));

?>
