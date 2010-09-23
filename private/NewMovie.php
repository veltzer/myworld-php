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
$p_locationid=my_get_post('locationid');
$p_deviceid=my_get_post('deviceid');
$p_rating=my_get_post('rating');
$p_review=my_get_post('review');

// this is a line you can use for debugging...
//error('query not yet implemented');

$p_typeid=my_mysql_query_one('select id from TbWkWorkType where name=\'video movie\'');
$p_viewerid=my_mysql_query_one('select id from TbIdPerson where firstname=\'Mark\' and surname=\'Veltzer\'');
$p_externalid=my_mysql_query_one('select id from TbWkWorkExternal where name=\'imdb\'');

$query=sprintf('insert into TbWkWork (name,externalId,externalCode,typeId) values(\'%s\',\'%s\',\'%s\',\'%s\')',
	mysql_real_escape_string($p_name),
	mysql_real_escape_string($p_externalId),
	mysql_real_escape_string($p_imdbid),
	mysql_real_escape_string($p_typeid)
);
my_mysql_query($query);
$p_workId=mysql_insert_id();
// insert a new view
$query=sprintf('insert into TbWkWorkView (endViewDate,locationId,deviceId,viewerId,workId) values(\'%s\',\'%s\',\'%s\',\'%s\',\'%s\')',
	mysql_real_escape_string($p_date),
	mysql_real_escape_string($p_locationid),
	mysql_real_escape_string($p_deviceid),
	mysql_real_escape_string($p_viewerid),
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
