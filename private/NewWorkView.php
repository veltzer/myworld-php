<?php
require('utils.php');
utils_init();
$debug=0;
if($debug) {
	print_r($_POST)."\n";
}

# parameters for this script...
$p_personId=my_get_post('personId');
$p_workId=my_get_post('workId');
$p_date=javascriptdate_to_mysqldate(my_get_post('date'));
$p_locationId=my_get_post('locationId');
$p_deviceId=my_get_post('deviceId');
$p_langId=my_get_post('langId');
$p_ratingId=my_get_post('ratingId');
$p_review=my_get_post('review');

my_mysql_start_transaction();

// insert a new view
$query=sprintf('insert into TbWkWorkView (endViewDate,locationId,deviceId,langId,viewerId,workId) values(%s,%s,%s,%s,%s,%s)',
	my_mysql_real_escape_string($p_date),
	my_mysql_real_escape_string($p_locationId),
	my_mysql_real_escape_string($p_deviceId),
	my_mysql_real_escape_string($p_langId),
	my_mysql_real_escape_string($p_personId),
	my_mysql_real_escape_string($p_workId)
);
my_mysql_query($query);
$p_insertworkviewid=mysql_insert_id();
// insert a new review
$query=sprintf('insert into TbWkWorkReview (reviewerId,ratingId,review,reviewDate,workId) values(%s,%s,%s,%s,%s)',
	my_mysql_real_escape_string($p_personId),
	my_mysql_real_escape_string($p_ratingId),
	my_mysql_real_escape_string($p_review),
	my_mysql_real_escape_string($p_date),
	my_mysql_real_escape_string($p_workId)
);
my_mysql_query($query);
$p_insertworkreviewid=mysql_insert_id();
my_mysql_commit();

echo "view [{$p_insertworkviewid}] and review [{$p_insertworkreviewid}] successfully inserted";
?>
