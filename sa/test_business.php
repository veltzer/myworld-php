<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<title>Business info sheet</title>
	</head>
<body>
<?php

require("setup.php");
my_include("include/utils.php");
my_include("include/na.php");
my_include("include/db.php");
my_include("frag/business.php");

db_connect();
echo create_courses();
echo create_consulting();
echo create_teaching();
echo create_certification();
db_disconnect();

?>
</body></html>
