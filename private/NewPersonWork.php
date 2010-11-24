<?php
require('utils.php');
utils_init();
$debug=0;
if($debug) {
	print_r($_POST)."\n";
}

# parameters for this script...
$p_workId=my_get_post_or_null('workId');
$p_typeId=my_get_post_or_null('typeId');
$p_personId=my_get_post_or_null('personId');
#$p_organizationId=my_get_post_or_null('organizationId');

my_mysql_start_transaction();

/*
$query=sprintf('insert into TbWkWorkContrib (workId,typeId,personId,organizationId) values(%s,%s,%s,%s)',
	my_mysql_real_escape_string($p_workId),
	my_mysql_real_escape_string($p_typeId),
	my_mysql_real_escape_string($p_personId),
	my_mysql_real_escape_string($p_organizationId)
);
*/
$query=sprintf('insert into TbWkWorkContrib (workId,typeId,personId) values(%s,%s,%s)',
	my_mysql_real_escape_string($p_workId),
	my_mysql_real_escape_string($p_typeId),
	my_mysql_real_escape_string($p_personId)
);
my_mysql_query($query);
$p_id=mysql_insert_id();
my_mysql_commit();

echo 'new record successfully inserted with id ['.$p_id.']';
?>
