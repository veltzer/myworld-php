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
my_include("frag/helloworld.php");
echo create_helloworld();

?>
</body></html>
