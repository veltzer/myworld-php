<?php
/*
$params=array();
push($params,'from_date');
push($params,'from_time');
push($params,'to_date');
push($params,'to_time');
check_params($params);
 */
require("utils.php");
db_connect();
$company=$_POST['company'];
$course=$_POST['course'];
$start_date=$_POST['start_date'];
$end_date=$_POST['end_date'];
$start_time=$_POST['start_time'];
$end_time=$_POST['end_time'];
$query=sprintf("insert into TbEvent (company,course,start,end) values('%s','%s','%s','%s')",
	mysql_real_escape_string($company),
	mysql_real_escape_string($course),
	mysql_real_escape_string($start),
	mysql_real_escape_string($end)
);
$result=mysql_query($query);
echo "query is ".$query;
assert($result);
db_disconnect();
echo "all is ok";
?>
