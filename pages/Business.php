<html><head><title>Business info sheet</title></head><body>
<?php

require("../setup.php");
require("frag/business.php");
require("include/db.php");

db_connect();
create_business();
db_disconnect();

?>
</body></html>
