<?php
header('Content-type: text/html; charset=UTF-8');
?>
<html>
	<head>
		<title>Lilypond music</title>
	</head>
<body>
<?php

require('setup.php');
my_include('src/utils.php');
my_include('src/lilypond.php');

my_mysql_connect();
echo create_lilypond();
my_mysql_disconnect();

?>
</body></html>
