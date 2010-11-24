<?php
header("Content-type: text/html; charset=UTF-8");
?>
<html>
	<head>
		<title>Works</title>
	</head>
<body>
<?php

require("setup.php");
my_include("include/utils.php");
my_include("src/works.php");


my_mysql_connect();
echo create_works();
my_mysql_disconnect();

?>
</body></html>
