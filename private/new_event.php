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
$title="new event";
$start="2010-08-25 01:42:54";
$end="2010-08-25 01:42:54";
$url="http://foo.com";
$query=sprintf("insert into TbEvent (title,start,end,url) values(%s,%s,%s,%s)",
	mysql_real_escape_string($title),
	mysql_real_escape_string($start),
	mysql_real_escape_string($end),
	mysql_real_escape_string($url)
);
$result=mysql_query($query);
assert($result);
db_disconnect();
echo "all is ok";
?>
