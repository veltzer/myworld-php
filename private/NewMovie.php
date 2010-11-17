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

$p_typeId=my_mysql_query_one('select id from TbWkWorkType where name=\'video movie\'');
$p_viewerId=my_mysql_query_one('select id from TbIdPerson where firstname=\'Mark\' and surname=\'Veltzer\'');
$p_externalId=my_mysql_query_one('select id from TbWkWorkExternal where name=\'imdb\'');

// TODO: do all three next queries in a single transaction...

$query=sprintf('insert into TbWkWork (name,externalId,externalCode,typeId) values(\'%s\',\'%s\',\'%s\',\'%s\')',
	mysql_real_escape_string($p_name),
	mysql_real_escape_string($p_externalId),
	mysql_real_escape_string($p_imdbid),
	mysql_real_escape_string($p_typeId)
);
my_mysql_query($query);
$p_workId=mysql_insert_id();
// insert a new view
$query=sprintf('insert into TbWkWorkView (endViewDate,locationId,deviceId,viewerId,workId) values(\'%s\',\'%s\',\'%s\',\'%s\',\'%s\')',
	mysql_real_escape_string($p_date),
	mysql_real_escape_string($p_locationId),
	mysql_real_escape_string($p_deviceId),
	mysql_real_escape_string($p_viewerId),
	mysql_real_escape_string($p_workId)
);
my_mysql_query($query);
// insert a new review
$query=sprintf('insert into TbWkWorkReview (rating,review,reviewDate,workId) values(\'%s\',\'%s\',\'%s\',\'%s\')',
	mysql_real_escape_string($p_rating),
	mysql_real_escape_string($p_review),
	mysql_real_escape_string($p_date),
	mysql_real_escape_string($p_workId)
);
my_mysql_query($query);
echo 'new movie, view and review successfully inserted';
?>
