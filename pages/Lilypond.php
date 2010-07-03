<meta http-equiv=Content-Type content="text/html; charset=utf-8">
<html><head><title>Lilypond music</title></head><body>
<?php

require("../setup.php");
require("frag/lilypond.php");
require("include/db.php");

db_connect();
create_lilypond();
db_disconnect();

?>
</body></html>
