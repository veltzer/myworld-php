<?php
header("Content-type: text/html; charset=UTF-8");
?>
<html>
	<head>
		<title>Lilypond music</title>
	</head>
<body>
<?php

require("setup.php");
my_include("include/utils.php");
my_include("include/na.php");
my_include("include/db.php");
my_include("src/lilypond.php");

db_connect();
echo create_lilypond();
db_disconnect();

?>
</body></html>
