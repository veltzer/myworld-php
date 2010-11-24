<?php
require('utils.php');
utils_init();
$debug=0;
if($debug) {
	print_r($_POST)."\n";
}

# parameters for this script...
$p_name=my_get_post('name');
$p_imdbid=my_get_post('imdbid');
$p_date=javascriptdate_to_mysqldate(my_get_post('date'));
$p_locationId=my_get_post('locationId');
$p_deviceId=my_get_post('deviceId');
$p_rating=my_get_post('rating');
$p_review=my_get_post('review');

// this is a line you can use for debugging...
//error('query not yet implemented');
//
my_mysql_start_transaction();

$p_typeId=my_mysql_query_one('select id from TbWkWorkType where name=\'video movie\'');
$p_viewerId=my_mysql_query_one('select id from TbIdPerson where firstname=\'Mark\' and surname=\'Veltzer\'');
$p_externalId=my_mysql_query_one('select id from TbExternalType where name=\'imdb\'');

// TODO: do all three next queries in a single transaction...

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
	my_mysql_real_escape_string($p_imdbid)
);
my_mysql_query($query);
$p_externalId=mysql_insert_id();
// insert a new view
$query=sprintf('insert into TbWkWorkView (endViewDate,locationId,deviceId,viewerId,workId) values(%s,%s,%s,%s,%s)',
	my_mysql_real_escape_string($p_date),
	my_mysql_real_escape_string($p_locationId),
	my_mysql_real_escape_string($p_deviceId),
	my_mysql_real_escape_string($p_viewerId),
	my_mysql_real_escape_string($p_workId)
);
my_mysql_query($query);
$p_workviewid=mysql_insert_id();
// insert a new review
$query=sprintf('insert into TbWkWorkReview (rating,review,reviewDate,workId,reviewerId) values(%s,%s,%s,%s,%s)',
	my_mysql_real_escape_string($p_rating),
	my_mysql_real_escape_string($p_review),
	my_mysql_real_escape_string($p_date),
	my_mysql_real_escape_string($p_workId),
	my_mysql_real_escape_string($p_viewerId)
);
my_mysql_query($query);
$p_workreviewid=mysql_insert_id();
echo "new work [$p_workId], external [$p_externalId], view [$p_workviewid], review [$p_workreviewid] successfully inserted";

my_mysql_commit();
?>
