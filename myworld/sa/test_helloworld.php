<?php
header("Content-type: text/html; charset=UTF-8");
?>
<html>
	<head>
		<title>Hello World demo</title>
	</head>
<body>
<?php

require("setup.php");
my_include("src/helloworld.php");
$params=array();
echo create_helloworld($params);

?>
</body></html>
