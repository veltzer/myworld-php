<html><head><title>Works</title></head><body>
<?php

require("../setup.php");
require("frag/works.php");
require("include/db.php");

db_connect();
create_works();
db_disconnect();

?>
</body></html>
