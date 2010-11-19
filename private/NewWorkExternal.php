<?php
require('utils.php');
utils_init();
$debug=0;
if($debug) {
	print_r($_POST)."\n";
}

# parameters for this script...
$p_workId=my_get_post_or_null('workId');
$p_externalId=my_get_post_or_null('externalId');
$p_externalCode=my_get_post_or_null('externalCode');

$query=sprintf('insert into TbWkWorkExternal (workId,externalId,externalCode) values(%s,%s,%s)',
	my_mysql_real_escape_string($p_workId),
	my_mysql_real_escape_string($p_externalId),
	my_mysql_real_escape_string($p_externalCode)
);
my_mysql_query($query);
$p_workexternalid=mysql_insert_id();
echo "new external successfully inserted with id [$p_workexternalid]";
?>
