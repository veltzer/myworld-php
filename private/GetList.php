<?php
require("utils.php");
db_connect();
$query=sprintf("select id,name from TbBsCompanies");
$result=mysql_query($query);
assert($result);
result_echo_json($result);
db_disconnect();
?>
