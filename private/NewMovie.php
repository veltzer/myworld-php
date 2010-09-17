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
$p_date=my_get_post('date');
$p_date=javascriptdate_to_mysqldate($p_date);
$p_locationid=my_get_post('locationid');
$p_deviceid=my_get_post('deviceid');
$p_rating=my_get_post('rating');
$p_review=my_get_post('review');

//error('query not yet implemented');

$p_typeid=my_mysql_query_one('select id from TbWkWorkType where name=\'video movie\'');
// TODO: maybe a query will be better ?!?
$p_viewerid=1;

$query=sprintf('insert into TbWkWork (name,imdbid,endViewDate,locationId,deviceId,rating,review,typeId,viewerId) values(\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\')',
	mysql_real_escape_string($p_name),
	mysql_real_escape_string($p_imdbid),
	mysql_real_escape_string($p_date),
	mysql_real_escape_string($p_locationid),
	mysql_real_escape_string($p_deviceid),
	mysql_real_escape_string($p_rating),
	mysql_real_escape_string($p_review),
	mysql_real_escape_string($p_typeid),
	mysql_real_escape_string($p_viewerid)
);
if($debug) {
	echo 'query is '.$query;
}
my_mysql_query($query);
echo 'new movie successfully inserted';
?>
