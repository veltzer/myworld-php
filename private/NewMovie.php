<?php
require("utils.php");
utils_init();
$debug=1;
if($debug) {
	print_r($_POST)."\n";
}

# parameters for this script...
$p_name=my_get_post('name');
$p_imdbid=my_get_post('imdbid');
$p_date=my_get_post('date');
$p_locationid=my_get_post('locationid');
$p_deviceid=my_get_post('deviceid');
$p_rating=my_get_post('rating');
$p_review=my_get_post('review');

error('query not yet implemented');

/*
$query=sprintf("insert into TbWorks () values('%s','%s','%s','%s')",
	mysql_real_escape_string($imdbid),
	mysql_real_escape_string($locationid),
	mysql_real_escape_string($rank),
	mysql_real_escape_string($review)
);
if($debug) {
	echo "query is ".$query;
}
my_mysql_query($query);
echo "new movie successfully inserted";
 */
?>
