<?php
require('utils.php');
utils_init();
$debug=0;
if($debug) {
	print_r($_POST)."\n";
}

# parameters for this script...
$p_name=my_get_post_or_null('name');
$p_typeid=my_get_post_or_null('typeId');
$p_externalid=my_get_post_or_null('externalId');
$p_externalcode=my_get_post_or_null('externalCode');

$query=sprintf('insert into TbWkWork (name,typeId,externalId,externalCode) values(%s,%s,%s,%s)',
	my_mysql_real_escape_string($p_name),
	my_mysql_real_escape_string($p_typeid),
	my_mysql_real_escape_string($p_externalid),
	my_mysql_real_escape_string($p_externalcode)
);
my_mysql_query($query);
$p_id=mysql_insert_id();
echo 'new work successfully inserted with id ['.$p_id.']';
?>
