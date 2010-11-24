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
echo create_courses();
echo create_consulting();
echo create_teaching();
echo create_certification();
my_mysql_disconnect();

?>
</body></html>
