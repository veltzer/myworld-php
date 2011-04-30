<html>
<head>
<!--
	<meta http-equiv="Content-type" content="text/html; charset=utf8"/>
-->
<!--
	<meta http-equiv="Content-type" content="text/html; charset=UTF-8"/>

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
-->
</head>
<body> 

<?php
require('utils.php');
utils_init();
$debug=0;

# this is a test page to explore various issues like UTF

echo 'שלום לכולם';
echo '<br/>';
$query=sprintf('select name from TbWkWork where id=119');
$result=my_mysql_query_one($query);
echo $result;
echo '<br/>';
?>
</body>
</html>
