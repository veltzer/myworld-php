<?php
header("Content-type: text/html; charset=UTF-8");
?>
<html>
	<head>
		<title>Business info sheet</title>
	</head>
<body>
<?php

require("setup.php");
my_include("include/utils.php");
my_include("src/business.php");

my_mysql_connect();
$params=array();
echo create_courses($params);
echo create_consulting($params);
echo create_teaching($params);
echo create_certification($params);
my_mysql_disconnect();

?>
</body></html>
