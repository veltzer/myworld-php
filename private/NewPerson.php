<?php
require('utils.php');
utils_init();
$debug=0;
if($debug) {
	print_r($_POST)."\n";
}

# parameters for this script...
$p_honorificId=my_get_post_or_null('honorificId');
$p_firstname=my_get_post_or_null('firstname');
$p_surname=my_get_post_or_null('surname');
$p_othername=my_get_post_or_null('othername');
$p_ordinal=my_get_post_or_null('ordinal');
$p_remark=my_get_post_or_null('remark');

my_mysql_start_transaction();

$query=sprintf('insert into TbIdPerson (honorificId,firstname,surname,othername,ordinal,remark) values(%s,%s,%s,%s,%s,%s)',
	my_mysql_real_escape_string($p_honorificId),
	my_mysql_real_escape_string($p_firstname),
	my_mysql_real_escape_string($p_surname),
	my_mysql_real_escape_string($p_othername),
	my_mysql_real_escape_string($p_ordinal),
	my_mysql_real_escape_string($p_remark)
);
my_mysql_query($query);
$p_id=mysql_insert_id();
my_mysql_commit();

echo 'new person successfully inserted with id ['.$p_id.']';
?>
