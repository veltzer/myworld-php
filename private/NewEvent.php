<?php
require("utils.php");
utils_init();
$debug=1;

if($debug) {
	print_r($_POST)."\n";
}

# parameters for this script...
$p_name=my_get_post('name');
$p_calendarIy=my_get_post('calendarId');
$p_companIy=my_get_post('companyId');
$p_courseId=my_get_post('courseId');
$p_locationId=my_get_post('locationId');
$p_personId=my_get_post('personId');
$p_remark=my_get_post('remark');

my_mysql_start_transaction();

$query=sprintf("insert into TbEvent (name,calendarId,companyId,courseId,locationId,personId,remark) values(%s,%s,%s,%s,%s,%s,%s)",
	my_mysql_real_escape_string($p_name),
	my_mysql_real_escape_string($p_calendarId),
	my_mysql_real_escape_string($p_companyId),
	my_mysql_real_escape_string($p_courseId),
	my_mysql_real_escape_string($p_locationId),
	my_mysql_real_escape_string($p_personId),
	my_mysql_real_escape_string($p_remark)
);
my_mysql_query($query);
$p_eventid=mysql_insert_id();
my_mysql_commit();

echo 'new event successfully inserted with id ['.$p_eventid.']';
?>
