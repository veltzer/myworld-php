<?php
require("utils.php");
utils_init();
$debug=1;

if($debug) {
	print_r($_POST)."\n";
}

# parameters for this script...
$company=my_get_post('company','must have company name');
$course=my_get_post('course','must have course name');
$start_date=my_get_post('start_date','must have start date');
$end_date=my_get_post('end_date','must have end date');
$start_time=my_get_post('start_time','must have start time');
$end_time=my_get_post('end_time','must have end time');

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
