<?php
require('utils.php');
utils_init();
$table=$_GET['table'];
if($table=='TbIdPerson') {
	//error("fake error");
	$rows=get_person_data();
	$response=json_encode($rows);
} else {
	$query=sprintf('select id,name as label,name as value from %s',
		mysql_real_escape_string($table)
	);
	$result=my_mysql_query($query);
	$response=my_json_encode($result);
}
echo $response;
utils_finish();
?>
