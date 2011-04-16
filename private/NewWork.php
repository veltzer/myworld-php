<?php
require('utils.php');
utils_init();
$debug=0;
if($debug) {
	print_r($_POST)."\n";
}

# parameters for this script...
$p_name=my_get_post_or_null('name');
$p_typeId=my_get_post_or_null('typeId');
$p_languageId=my_get_post_or_null('languageId');

my_mysql_start_transaction();

$query=sprintf('insert into TbWkWork (name,typeId,languageId) values(%s,%s,%s)',
	my_mysql_real_escape_string($p_name),
	my_mysql_real_escape_string($p_typeId),
	my_mysql_real_escape_string($p_languageId)
);
my_mysql_query($query);
$p_workid=mysql_insert_id();
my_mysql_commit();

echo "new work successfully inserted with id [$p_workid]";
?>
