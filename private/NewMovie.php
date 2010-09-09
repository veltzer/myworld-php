<?php
require("utils.php");
utils_init();
$debug=1;
if($debug) {
	print_r($_POST)."\n";
}

# parameters for this script...
$imdbid=$_POST['imdbid'];
assert('$imdbid!=NULL');
$locationid=$_POST['locationid'];
assert('$locationid!=NULL');
$rank=$_POST['rank'];
assert('$rank!=NULL');
$review=$_POST['review'];
assert('$review!=NULL');

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
