<?php
require('utils.php');
utils_init();
$prefix=$_GET['term'];
assert('$prefix!=NULL');
$type=$_GET['type'];
assert('$type=="companies" || $type=="courses"');
db_connect();
if($type=="companies") {
	$query=sprintf('select id,name as label,name as value from TbBsCompanies WHERE name REGEXP "^%s"',
		mysql_real_escape_string($prefix)
	);
}
if($type=="courses") {
	$query=sprintf('select id,name as label,name as value from TbBsCourses WHERE name REGEXP "^%s"',
		mysql_real_escape_string($prefix)
	);
}
$result=mysql_query($query);
assert($result);
$response=my_json_encode($result);
echo $response;
db_disconnect();
?>
