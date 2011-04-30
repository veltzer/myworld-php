<?php

// url to use this script:
// http://veltzer.net/~mark/php/pages/GetBlob.php?table=TbMsLilypond&id=5&field=pdf&type=application/pdf&name_field=filebasename

require('setup.php');
my_include('src/utils.php');

$p_table = $_GET['table'];
$p_sfield = $_GET['sfield'];
$p_id = $_GET['id'];
$p_field = $_GET['field'];
$p_name_field = $_GET['name_field'];
$p_type = $_GET['type'];

$debug=0;

# security...
assert($p_table=='TbMsLilypond' || $p_table=='TbOrganization');
assert($p_sfield=='id' || $p_sfield=='uuid');
#assert($p_field=='ly' || $p_field=='pdf' || $p_field=='ps' || $p_field=='midi');

my_mysql_connect();
$query=sprintf('SELECT %s,%s FROM %s where %s=%s',
	mysql_real_escape_string($p_field),
	mysql_real_escape_string($p_name_field),
	mysql_real_escape_string($p_table),
	mysql_real_escape_string($p_sfield),
	my_mysql_real_escape_string($p_id)
);
if($debug) {
	echo $query.'<br/>';
}
$result=my_mysql_query_one_row($query);
$fileContent=$result[$p_field];
$fileName=$result[$p_name_field];
$fileLength=strlen($fileContent);
# You can see more HTTP headers that may improve stuff in
# http://en.wikipedia.org/wiki/List_of_HTTP_headers
# ideas are: Content-MD5, Content-Length, Last-Modified
# if you want to debug HTTP headers just use wget -S on the
# command line and compared the headers that you are generating
# with the headers that a regular content generates by using
# the web server...
header('Content-type: '.$p_type);
header('Cache-Control: no-cache');
header('Content-Length: '.$fileLength);
header('Content-Disposition: attachment; filename='.$fileName.'.'.$p_field);
echo $fileContent;
my_mysql_disconnect();

?>
