<html><head><title>Business info sheet</title></head><body>
<?php

require("../setup.php");
my_include("include/utils.php");
my_include("include/na.php");
my_include("include/db.php");
my_include("frag/business.php");

db_connect();
create_business();
db_disconnect();

?>
</body></html>
