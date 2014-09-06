<?php
require('utils.php');
utils_init();
$debug=0;
if($debug) {
	print_r($_POST)."\n";
}

# parameters for this script...
$p_personId=my_get_post('personId');
$p_name=my_get_post('name');
$p_imdbid=my_get_post('imdbid');
$p_date=javascriptdate_to_mysqldate(my_get_post('date'));
$p_locationId=my_get_post('locationId');
$p_deviceId=my_get_post('deviceId');
$p_remark=my_get_post('remark');
$p_ratingId=my_get_post('ratingId');
$p_review=my_get_post('review');

// this is a line you can use for debugging...
//error('query not yet implemented');
//
my_mysql_start_transaction();

$p_typeId=my_mysql_query_one('select id from TbWkWorkType where name=\'video movie\'');
$p_externalId=my_mysql_query_one('select id from TbExternalType where name=\'imdb_title_id\'');

// insert the actual work
$query=sprintf('insert into TbWkWork (name,typeId) values(%s,%s)',
	my_mysql_real_escape_string($p_name),
	my_mysql_real_escape_string($p_typeId)
);
my_mysql_query($query);
$p_workId=my_mysql_insert_id();
// insert an external imdb id
$query=sprintf('insert into TbWkWorkExternal (workId,externalId,externalCode) values(%s,%s,%s)',
	my_mysql_real_escape_string($p_workId),
	my_mysql_real_escape_string($p_externalId),
	my_mysql_real_escape_string($p_imdbid)
);
my_mysql_query($query);
$p_externalId=my_mysql_insert_id();
// insert a new view
$query=sprintf('insert into TbWkWorkView (endViewDate,locationId,deviceId,workId,remark) values(%s,%s,%s,%s,%s)',
	my_mysql_real_escape_string($p_date),
	my_mysql_real_escape_string($p_locationId),
	my_mysql_real_escape_string($p_deviceId),
	my_mysql_real_escape_string($p_workId),
	my_mysql_real_escape_string($p_remark)
);
my_mysql_query($query);
$p_workviewid=my_mysql_insert_id();
// insert the viewer
$query=sprintf('insert into TbWkWorkViewPerson (viewerId,viewId) values(%s,%s)',
	my_mysql_real_escape_string($p_personId),
	my_mysql_real_escape_string($p_workviewid)
);
my_mysql_query($query);
$p_workviewpersonid=my_mysql_insert_id();
// insert a new review
$query=sprintf('insert into TbWkWorkReview (ratingId,review,reviewDate,workId,reviewerId) values(%s,%s,%s,%s,%s)',
	my_mysql_real_escape_string($p_ratingId),
	my_mysql_real_escape_string($p_review),
	my_mysql_real_escape_string($p_date),
	my_mysql_real_escape_string($p_workId),
	my_mysql_real_escape_string($p_personId)
);
my_mysql_query($query);
$p_workreviewid=my_mysql_insert_id();
my_mysql_commit();

echo "new work [$p_workId], external [$p_externalId], view [$p_workviewid], viewperson[$p_workviewpersonid], review [$p_workreviewid] successfully inserted";
?>
