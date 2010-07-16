<html><head><title>Works</title></head><body>
<?php

require("../setup.php");
my_include("include/db.php");
my_include("include/utils.php");
my_include("include/na.php");
my_include("frag/works.php");


db_connect();
echo create_works();
db_disconnect();

?>
</body></html>
