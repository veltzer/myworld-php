<?php

// url to use this script:
// http://veltzer.net/~mark/php/pages/GetBlob.php?table=TbMsLilypond&id=5&field=pdf&type=application/pdf&name_field=filebasename

require("setup.php");
my_include("include/utils.php");
my_include("include/db.php");
my_include("include/na.php");

$p_table = $_GET['table'];
$p_sfield = $_GET['sfield'];
$p_id = $_GET['id'];
$p_field = $_GET['field'];
$p_name_field = $_GET['name_field'];
$p_type = $_GET['type'];

$debug=0;

# security...
assert($p_table=='TbMsLilypond');
assert($p_sfield=='id' || $p_sfield=='uuid');
#assert($p_field=='ly' || $p_field=='pdf' || $p_field=='ps' || $p_field=='midi');

db_connect();
$query=sprintf("SELECT %s,%s FROM %s where %s=%s",
	mysql_real_escape_string($p_field),
	mysql_real_escape_string($p_name_field),
	mysql_real_escape_string($p_table),
	mysql_real_escape_string($p_sfield),
	mysql_real_escape_string($p_id)
);
if($debug==1) {
	echo $query."<br/>";
}
$result=mysql_query($query);
# make sure we really have a result
assert($result);
# we should only get one result...
assert(mysql_num_rows($result)==1);
$fileContent=@mysql_result($result,0,$p_field);
$fileName=@mysql_result($result,0,$p_name_field);
# You can see more HTTP headers that may improve stuff in
# http://en.wikipedia.org/wiki/List_of_HTTP_headers
# ideas are: Content-MD5, Content-Length, Last-Modified
# if you want to debug HTTP headers just use wget -S on the
# command line and compared the headers that you are generating
# with the headers that a regular content generates by using
# the web server...
header("Content-type: $p_type");
header("Cache-Control: no-cache");
header("Content-Length: ".strlen($fileContent));
header("Content-Disposition: attachment; filename=$fileName.$p_field");
echo $fileContent;
db_disconnect();

?>
