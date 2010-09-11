<?php
require("utils.php");
utils_init();
$debug=1;
if($debug) {
	print_r($_POST)."\n";
}

# parameters for this script...
$imdbid=my_get_post('imdbid');
$locationid=my_get_post('locationid');
$rank=my_get_post('rank');
$review=my_get_post('review');
//$=my_get_post('');

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
?>
