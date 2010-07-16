<?php

// url to use this script:
// http://veltzer.net/~mark/php/pages/GetBlob.php?table=TbMsLilypond&id=5&field=pdf&type=application/pdf

require("../setup.php");
my_include("include/utils.php");
my_include("include/db.php");
my_include("include/na.php");

$p_table = $_GET['table'];
$p_id = $_GET['id'];
$p_field = $_GET['field'];
$p_type = $_GET['type'];

# security...
assert($p_table=='TbMsLilypond');
assert($p_field=='source' || $p_field=='pdf' || $p_field=='ps' || $p_field=='midi');

db_connect();
$query=sprintf("SELECT %s FROM %s where id=%s",
	mysql_real_escape_string($p_field),
	mysql_real_escape_string($p_table),
	mysql_real_escape_string($p_id)
);
$result=mysql_query($query);
assert($result);
# we should only get one result...
assert(mysql_num_rows($result)==1);
$fileContent=@mysql_result($result,0,$p_field);
header("Content-type: $p_type");
echo $fileContent;
db_disconnect();

?>
