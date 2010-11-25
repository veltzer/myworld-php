<?php
require('utils.php');
utils_init();
$debug=0;
if($debug) {
	print_r($_POST)."\n";
}

# parameters for this script...
$p_name=my_get_post('name');
$p_typeId=my_get_post_or_null('typeId');
$p_externalId=my_get_post('externalId');
$p_externalCode=my_get_post('externalCode');
$p_start=my_get_post('start');
if($p_start!='') {
	$p_start=javascriptdate_to_mysqldate($p_start);
}
$p_end=javascriptdate_to_mysqldate(my_get_post('end'));
if($p_end!='') {
	$p_end=javascriptdate_to_mysqldate($p_end);
}
$p_personId=my_get_post('personId');
$p_locationId=my_get_post('locationId');
$p_deviceId=my_get_post('deviceId');
$p_ratingId=my_get_post('ratingId');
$p_review=my_get_post('review');

my_mysql_start_transaction();

// insert the actual work
$query=sprintf('insert into TbWkWork (name,typeId) values(%s,%s)',
	my_mysql_real_escape_string($p_name),
	my_mysql_real_escape_string($p_typeId)
);
my_mysql_query($query);
$p_workId=mysql_insert_id();
// insert an external imdb id
$query=sprintf('insert into TbWkWorkExternal (workId,externalId,externalCode) values(%s,%s,%s)',
	my_mysql_real_escape_string($p_workId),
	my_mysql_real_escape_string($p_externalId),
	my_mysql_real_escape_string($p_externalCode)
);
my_mysql_query($query);
$p_externalId=mysql_insert_id();
// insert a new view
$query=sprintf('insert into TbWkWorkView (workId,startViewDate,endViewDate,viewerId,locationId,deviceId) values(%s,%s,%s,%s,%s,%s)',
	my_mysql_real_escape_string($p_workId),
	my_mysql_real_escape_string($p_start),
	my_mysql_real_escape_string($p_end),
	my_mysql_real_escape_string($p_personId),
	my_mysql_real_escape_string($p_locationId),
	my_mysql_real_escape_string($p_deviceId)
);
my_mysql_query($query);
$p_workviewid=mysql_insert_id();
// insert a new review
$query=sprintf('insert into TbWkWorkReview (workId,ratingId,review,reviewDate,reviewerId) values(%s,%s,%s,%s,%s)',
	my_mysql_real_escape_string($p_workId),
	my_mysql_real_escape_string($p_ratingId),
	my_mysql_real_escape_string($p_review),
	my_mysql_real_escape_string($p_end),
	my_mysql_real_escape_string($p_personId)
);
my_mysql_query($query);
$p_workreviewid=mysql_insert_id();
my_mysql_commit();

echo "new work [$p_workId], external [$p_externalId], view [$p_workviewid], review [$p_workreviewid] successfully inserted";
?>
