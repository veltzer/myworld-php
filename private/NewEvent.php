<?php
require("utils.php");
utils_init();
$debug=1;

if($debug) {
	print_r($_POST)."\n";
}

# parameters for this script...
$company=$_POST['company'];
assert('$company!=NULL');
$course=$_POST['course'];
assert('$course!=NULL');
$start_date=$_POST['start_date'];
assert('$start_date!=NULL');
$end_date=$_POST['end_date'];
assert('$end_date!=NULL');
$start_time=$_POST['start_time'];
assert('$start_time!=NULL');
$end_time=$_POST['end_time'];
assert('$end_time!=NULL');

$query=sprintf("insert into TbEvent (company,course,start,end) values('%s','%s','%s','%s')",
	mysql_real_escape_string($company),
	mysql_real_escape_string($course),
	mysql_real_escape_string($start),
	mysql_real_escape_string($end)
);
if($debug) {
	echo "query is ".$query;
}
my_mysql_query($query);
echo "new event successfully inserted";
?>
