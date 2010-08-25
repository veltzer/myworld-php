<?php

/*
 * This script returns events from my calendar
 */

require("setup.php");
my_include("include/utils.php");

# do you want to debug this script
$debug=0;

//$p_start=$_GET['start'];
//$p_end=$_GET['end'];
$p_start="2009-08-24 01:32:40";
$p_end="2011-08-24 01:32:40";
# connect to the database
db_connect();
$query=sprintf('SELECT id,title,url,start,end FROM TbEvent where start > "%s" and end < "%s"',
	mysql_real_escape_string($p_start),
	mysql_real_escape_string($p_end)
);
if($debug==1) {
	printDebug($query);
}
$result=mysql_query($query);
# make sure we really have a result
assert($result);
if($debug) {
	printDebug($result);
}
// iterate over every row
while ($row = mysql_fetch_assoc($result)) {
	// for every field in the result..
	for ($i=0; $i < mysql_num_fields($result); $i++) {
		$info = mysql_fetch_field($result, $i);
		$type = $info->type;
		// cast for real
		if ($type == 'real')
			$row[$info->name] = doubleval($row[$info->name]);
		// cast for int
		if ($type == 'int')
			$row[$info->name] = intval($row[$info->name]);
	}
	$rows[] = $row;
}
// JSON-ify all rows together as one big array
echo json_encode($rows);
//echo json_encode($result,JSON_FORCE_OBJECT); 
db_disconnect();
?>
