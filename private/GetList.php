<?php
require('utils.php');
utils_init();
$prefix=$_GET['term'];
assert('$prefix!=NULL');
db_connect();
$query=sprintf('select * from TbBsCompanies WHERE name REGEXP "^%s"',
	mysql_real_escape_string($prefix)
);
$result=mysql_query($query);
assert($result);
$response=$_GET['callback'].'('.my_json_encode($result).')';
echo $response;
db_disconnect();
?>
