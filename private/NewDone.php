<?php
require('utils.php');
utils_init();
$debug=0;
if($debug) {
	print_r($_POST)."\n";
}

# parameters for this script...
#$p_start=javascriptdate_to_mysqldate(my_get_post('start'));
$p_end=javascriptdate_to_mysqldate(my_get_post('end'));
#$p_personId=my_get_post('personId');
$p_locationId=my_get_post('locationId');
$p_activityId=my_get_post('activityId');
$p_remark=my_get_post('remark');

my_mysql_start_transaction();

# this is instead of getting it via the form above...
$p_personId=my_mysql_query_one('select id from TbIdPerson where firstname=\'Mark\' and surname=\'Veltzer\'');

$query=sprintf('insert into TbTdDone (end,personId,locationId,activityId,remark) values(%s,%s,%s,%s,%s)',
	my_mysql_real_escape_string($p_end),
	my_mysql_real_escape_string($p_personId),
	my_mysql_real_escape_string($p_locationId),
	my_mysql_real_escape_string($p_activityId),
	my_mysql_real_escape_string($p_remark)
);
my_mysql_query($query);
$p_doneid=my_mysql_insert_id();

my_mysql_commit();

echo 'done item inserted with id ['.$p_doneid.']';
?>
