<?php
require('utils.php');
utils_init();
$table=$_GET['table'];
db_connect();
$query=sprintf('select id,name as label,name as value from %s',
	mysql_real_escape_string($table)
);
$result=mysql_query($query);
assert($result);
$response=my_json_encode($result);
echo $response;
db_disconnect();
?>
