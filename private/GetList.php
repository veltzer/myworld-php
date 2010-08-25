<?php
require('utils.php');
$prefix=$_GET['term'];
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
